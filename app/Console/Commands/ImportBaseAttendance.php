<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportBaseAttendance extends Command
{
    protected $signature = 'import:base-attendance {file=BASE.xlsx : Path to Excel file}';
    protected $description = 'Import attendance data from BASE.xlsx file';

    private array $indonesianMonths = [
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
        'desember' => 12
    ];

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }

        $this->info("📊 Loading Excel file: {$file}");

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $this->info("📝 Found " . ($highestRow - 1) . " rows to import");

        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $usersCreated = 0;
        $userCache = [];

        $bar = $this->output->createProgressBar($highestRow - 1);
        $bar->start();

        for ($row = 2; $row <= $highestRow; $row++) {
            try {
                $userName = trim($sheet->getCell('A' . $row)->getValue() ?? '');
                $dateValue = $sheet->getCell('B' . $row)->getValue();
                $duration = $sheet->getCell('C' . $row)->getValue();
                $contentEdit = $sheet->getCell('D' . $row)->getValue();
                $contentLive = $sheet->getCell('E' . $row)->getValue();
                $sales = $sheet->getCell('F' . $row)->getValue();

                // Skip empty rows
                if (empty($userName)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Get or create user
                $userKey = strtolower($userName);
                if (!isset($userCache[$userKey])) {
                    $user = User::whereRaw('LOWER(name) = ?', [$userKey])->first();

                    if (!$user) {
                        $user = User::create([
                            'name' => $userName,
                            'email' => $this->generateEmail($userName),
                            'password' => bcrypt('password123'),
                            'role' => 'user',
                            'is_active' => true,
                        ]);
                        $usersCreated++;
                    }
                    $userCache[$userKey] = $user->id;
                }
                $userId = $userCache[$userKey];

                // Parse date
                $attendanceDate = $this->parseDate($dateValue);
                if (!$attendanceDate) {
                    $this->newLine();
                    $this->warn("Row {$row}: Invalid date format: {$dateValue}");
                    $errors++;
                    $bar->advance();
                    continue;
                }

                // Check if attendance already exists
                $exists = Attendance::where('user_id', $userId)
                    ->where('attendance_date', $attendanceDate)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Create attendance
                Attendance::create([
                    'user_id' => $userId,
                    'attendance_date' => $attendanceDate,
                    'live_duration_minutes' => (int) (floatval(str_replace(',', '.', $duration)) * 60),
                    'content_edit_count' => (int) ($contentEdit ?? 0),
                    'content_live_count' => (int) ($contentLive ?? 0),
                    'sales_count' => (int) ($sales ?? 0),
                    'status' => 'validated',
                    'notes' => 'Imported from BASE.xlsx',
                ]);

                $imported++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Row {$row}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Import completed!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Imported', $imported],
                ['Skipped (duplicate/empty)', $skipped],
                ['Errors', $errors],
                ['New Users Created', $usersCreated],
            ]
        );

        return Command::SUCCESS;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If it's a numeric Excel date
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        // Parse Indonesian date format: "16 Januari 2025"
        $value = strtolower(trim($value));
        if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{4})/', $value, $matches)) {
            $day = (int) $matches[1];
            $monthName = $matches[2];
            $year = (int) $matches[3];

            if (isset($this->indonesianMonths[$monthName])) {
                $month = $this->indonesianMonths[$monthName];
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return null;
    }

    private function generateEmail(string $name): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '.', trim($name)));
        $slug = trim($slug, '.');
        return $slug . '@tiktok-live.local';
    }
}
