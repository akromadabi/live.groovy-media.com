<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\SalaryScheme;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User data from BASE.xlsx - mapping uppercase names to user info
        $users = [
            'NAILI NIMATUL MAULA' => [
                'name' => 'Naili Nimatul Maula',
                'email' => 'naili@tiktok.local',
                'phone' => '6285700130249',
            ],
            'TAHSYA ILFINA' => [
                'name' => 'Tahsya Ilfina',
                'email' => 'tahsya@tiktok.local',
                'phone' => '6285720188221',
            ],
            'KHOIRIL SEPTIAN' => [
                'name' => 'Khoiril Septian',
                'email' => 'khoiril@tiktok.local',
                'phone' => '62895324587351',
            ],
            'FAISKA' => [
                'name' => 'Faiska',
                'email' => 'faiska@tiktok.local',
                'phone' => '6285601525370',
            ],
            'VERA ATIKA SARI' => [
                'name' => 'Vera Atika Sari',
                'email' => 'vera@tiktok.local',
                'phone' => '6285226113936',
            ],
            'DINA ISTIFADA' => [
                'name' => 'Dina Istifada',
                'email' => 'dina@tiktok.local',
                'phone' => '6289501164371',
            ],
            'TRIANA' => [
                'name' => 'Triana',
                'email' => 'triana@tiktok.local',
                'phone' => '6287819318975',
            ],
            'ELLY' => [
                'name' => 'Elly',
                'email' => 'elly@tiktok.local',
                'phone' => '6281328587022',
            ],
        ];

        // Create admin first
        User::firstOrCreate(
            ['email' => 'admin@tiktok.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'task' => 'Administrator',
                'is_active' => true,
                'phone' => '081234567890',
            ]
        );

        // Create users and their salary schemes
        $userMap = [];
        foreach ($users as $excelName => $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('user123'),
                    'role' => 'user',
                    'task' => 'Host Live',
                    'is_active' => true,
                    'phone' => $userData['phone'],
                ]
            );

            // Map Excel name (uppercase) to user id
            $userMap[$excelName] = $user->id;

            // Create default salary scheme
            SalaryScheme::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'hourly_rate' => 25000,
                    'content_edit_rate' => 15000,
                    'content_live_rate' => 10000,
                    'sales_bonus_percentage' => 0,
                    'sales_bonus_nominal' => 0,
                ]
            );
        }

        $this->command->info('Users created successfully!');

        // Import attendances from Excel
        $this->importAttendances($userMap);
    }

    private function importAttendances(array $userMap): void
    {
        $filePath = base_path('BASE.xlsx');

        if (!file_exists($filePath)) {
            $this->command->warn('BASE.xlsx not found, skipping attendance import.');
            return;
        }

        $this->command->info('Loading BASE.xlsx...');

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $this->command->info('Total rows in Excel: ' . count($data));

        // Indonesian month mapping
        $indonesianMonths = [
            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12,
        ];

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($data as $rowNum => $row) {
            if ($rowNum <= 1)
                continue; // Skip header

            $name = strtoupper(trim($row['A'] ?? ''));
            $dateStr = trim($row['B'] ?? '');
            $durationStr = str_replace(',', '.', trim($row['C'] ?? '0'));
            $contentLive = intval($row['D'] ?? 0);
            $contentEdit = intval($row['E'] ?? 0);
            $salesCount = intval($row['F'] ?? 0);

            if (empty($name) || empty($dateStr)) {
                $skipped++;
                continue;
            }

            // Find user
            if (!isset($userMap[$name])) {
                if (!isset($errors[$name])) {
                    $errors[$name] = 0;
                }
                $errors[$name]++;
                $skipped++;
                continue;
            }

            // Parse Indonesian date (e.g., "16 Januari 2025")
            $dateParts = preg_split('/\s+/', $dateStr);
            if (count($dateParts) !== 3) {
                $skipped++;
                continue;
            }

            $day = intval($dateParts[0]);
            $month = $indonesianMonths[strtolower($dateParts[1])] ?? null;
            $year = intval($dateParts[2]);

            if (!$month) {
                $skipped++;
                continue;
            }

            try {
                $date = Carbon::createFromDate($year, $month, $day);
                $duration = floatval($durationStr);
                $durationMinutes = (int) ($duration * 60); // Convert hours to minutes

                // Check if attendance already exists
                $exists = Attendance::where('user_id', $userMap[$name])
                    ->whereDate('attendance_date', $date)
                    ->exists();

                if (!$exists) {
                    Attendance::create([
                        'user_id' => $userMap[$name],
                        'attendance_date' => $date,
                        'live_duration_minutes' => $durationMinutes,
                        'content_edit_count' => $contentEdit,
                        'content_live_count' => $contentLive,
                        'sales_count' => $salesCount,
                        'status' => 'validated',
                        'notes' => 'Imported from BASE.xlsx',
                    ]);
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $this->command->info("Imported: {$imported} attendances");

        if ($skipped > 0) {
            $this->command->info("Skipped: {$skipped} rows");
        }

        if (!empty($errors)) {
            $this->command->warn("Users not found in mapping:");
            foreach ($errors as $name => $count) {
                $this->command->warn("  - {$name}: {$count} records");
            }
        }
    }
}
