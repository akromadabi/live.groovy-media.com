<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        // Filter by user
        $selectedUser = null;
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
            $selectedUser = User::find($request->user_id);
        }

        // Get filter parameters with defaults
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month); // default ke bulan saat ini
        $term = $request->input('term'); // null = full month, 1 = 1-15, 2 = 16-end

        // Calculate date range based on year, month, term (use toDateString for DATE column)
        $startDate = Carbon::createFromDate($year, $month, 1)->toDateString();
        if ($term === '1') {
            $endDate = Carbon::createFromDate($year, $month, 15)->toDateString();
        } elseif ($term === '2') {
            $startDate = Carbon::createFromDate($year, $month, 16)->toDateString();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        } else {
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        }
        $query->whereBetween('attendance_date', [$startDate, $endDate]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderByDesc('attendance_date')->paginate(20);
        $users = User::where('role', 'user')->orderBy('name')->get();

        // Calculate totals for filtered result
        $totalsQuery = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);
        if ($request->filled('user_id')) {
            $totalsQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('status')) {
            $totalsQuery->where('status', $request->status);
        }

        $totalMinutes = (clone $totalsQuery)->sum('live_duration_minutes');
        $totalContentEdit = (clone $totalsQuery)->sum('content_edit_count');
        $totalContentLive = (clone $totalsQuery)->sum('content_live_count');
        $totalSales = (clone $totalsQuery)->sum('sales_count');

        $totals = [
            'total_hours' => round($totalMinutes / 60, 1),
            'total_content_edit' => $totalContentEdit,
            'total_content_live' => $totalContentLive,
            'total_sales' => $totalSales,
        ];

        // Calculate bonus info if specific user selected
        $bonusInfo = null;
        if ($selectedUser) {
            $scheme = $selectedUser->salaryScheme;
            $monthStart = Carbon::createFromDate($year, $month, 1)->toDateString();
            $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
            $daysInMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;

            $monthlyMinutes = $selectedUser->attendances()
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->sum('live_duration_minutes');
            $monthlyHours = round($monthlyMinutes / 60, 1);
            $monthlySales = $selectedUser->attendances()
                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                ->sum('sales_count');

            // Target calculation: (days in month - 4 days off) × 3 hours/day
            $daysOff = 4;
            $dailyTargetHours = 3;
            $targetHours = ($daysInMonth - $daysOff) * $dailyTargetHours;

            $targetMet = $monthlyHours >= $targetHours;
            $salesBonus = $monthlySales > 0 ? \App\Models\BonusTier::getBonusForSales($monthlySales) : 0;

            // Calculate total salary
            $totalSalary = 0;
            if ($scheme) {
                $totalSalary = ($monthlyMinutes / 60 * $scheme->hourly_rate)
                    + ($totalContentEdit * $scheme->content_edit_rate)
                    + ($totalContentLive * $scheme->content_live_rate);
            }

            $bonusInfo = [
                'user_name' => $selectedUser->name,
                'monthly_hours' => $monthlyHours,
                'target_hours' => $targetHours,
                'target_met' => $targetMet,
                'monthly_sales' => $monthlySales,
                'sales_bonus' => $salesBonus,
                'total_salary' => $totalSalary,
            ];
        }

        return view('admin.attendances.index', compact('attendances', 'users', 'totals', 'bonusInfo'));
    }

    /**
     * Show the form for creating a new attendance
     */
    public function create()
    {
        $users = User::where('role', 'user')->where('is_active', true)->orderBy('name')->get();
        return view('admin.attendances.create', compact('users'));
    }

    /**
     * Store a newly created attendance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'live_duration_hours' => 'required|numeric|min:0.5|max:24',
            'content_edit_count' => 'nullable|integer|min:0|max:10',
            'content_live_count' => 'nullable|integer|min:0|max:10',
            'sales_count' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Convert decimal hours to minutes
        $totalMinutes = (int) ($validated['live_duration_hours'] * 60);

        Attendance::create([
            'user_id' => $validated['user_id'],
            'attendance_date' => $validated['attendance_date'],
            'live_duration_minutes' => $totalMinutes,
            'content_edit_count' => $validated['content_edit_count'],
            'content_live_count' => $validated['content_live_count'],
            'sales_count' => $validated['sales_count'],
            'status' => 'validated',
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Absensi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the attendance
     */
    public function edit(Attendance $attendance)
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.attendances.edit', compact('attendance', 'users'));
    }

    /**
     * Update the specified attendance
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'live_duration_hours' => 'required|numeric|min:0.5|max:24',
            'content_edit_count' => 'nullable|integer|min:0|max:10',
            'content_live_count' => 'nullable|integer|min:0|max:10',
            'sales_count' => 'required|integer|min:0',
            'status' => 'required|in:pending,validated,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Convert decimal hours to minutes
        $totalMinutes = (int) ($validated['live_duration_hours'] * 60);

        $attendance->update([
            'user_id' => $validated['user_id'],
            'attendance_date' => $validated['attendance_date'],
            'live_duration_minutes' => $totalMinutes,
            'content_edit_count' => $validated['content_edit_count'],
            'content_live_count' => $validated['content_live_count'],
            'sales_count' => $validated['sales_count'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    /**
     * Remove the specified attendance
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Absensi berhasil dihapus.');
    }

    /**
     * Validate attendance
     */
    public function validate_attendance(Attendance $attendance)
    {
        $attendance->update(['status' => 'validated']);

        return back()->with('success', 'Absensi berhasil divalidasi.');
    }

    /**
     * Reject attendance
     */
    public function reject(Attendance $attendance)
    {
        $attendance->update(['status' => 'rejected']);

        return back()->with('success', 'Absensi ditolak.');
    }

    /**
     * Bulk validate selected attendances
     */
    public function bulkValidate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attendances,id',
        ]);

        Attendance::whereIn('id', $validated['ids'])
            ->where('status', 'pending')
            ->update(['status' => 'validated']);

        $count = count($validated['ids']);
        return back()->with('success', "{$count} absensi berhasil divalidasi.");
    }

    /**
     * Bulk delete selected attendances
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attendances,id',
        ]);

        Attendance::whereIn('id', $validated['ids'])->delete();

        $count = count($validated['ids']);
        return back()->with('success', "{$count} absensi berhasil dihapus.");
    }

    /**
     * Import attendance from Excel file
     * Supports columns: User, Tanggal, Durasi, Konten Edit, Konten Live, Penjualan
     */
    public function import(Request $request)
    {
        $request->validate([
            'attendance_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('attendance_file');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            // Indonesian month names mapping
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
                'desember' => 12
            ];

            // Find header row
            $headerRowIndex = 0;
            $header = null;

            for ($i = 0; $i < min(10, count($data)); $i++) {
                $rowLower = array_map('strtolower', array_map('trim', array_map('strval', $data[$i])));
                $rowString = implode(' ', $rowLower);

                // Check for either 'user' or 'nama' column
                if (
                    (strpos($rowString, 'user') !== false || strpos($rowString, 'nama') !== false) &&
                    (strpos($rowString, 'durasi') !== false || strpos($rowString, 'tanggal') !== false)
                ) {
                    $headerRowIndex = $i;
                    $header = $data[$i];
                    break;
                }
            }

            if ($header === null) {
                return back()->with('error', 'Header tidak ditemukan. Pastikan file memiliki kolom User/Nama, Tanggal, dan Durasi.');
            }

            // Get data rows
            $dataRows = array_slice($data, $headerRowIndex + 1);
            $headerLower = array_map('strtolower', array_map('trim', array_map('strval', $header)));

            // Find column indices
            $userIndex = null;
            $dateIndex = null;
            $durationIndex = null;
            $contentEditIndex = null;
            $contentLiveIndex = null;
            $salesIndex = null;

            foreach ($headerLower as $index => $col) {
                if ($userIndex === null && (strpos($col, 'user') !== false || strpos($col, 'nama') !== false)) {
                    $userIndex = $index;
                }
                if ($dateIndex === null && strpos($col, 'tanggal') !== false) {
                    $dateIndex = $index;
                }
                if ($durationIndex === null && strpos($col, 'durasi') !== false) {
                    $durationIndex = $index;
                }
                if ($contentEditIndex === null && strpos($col, 'konten edit') !== false) {
                    $contentEditIndex = $index;
                }
                if ($contentLiveIndex === null && strpos($col, 'konten live') !== false) {
                    $contentLiveIndex = $index;
                }
                if ($salesIndex === null && (strpos($col, 'penjualan') !== false || strpos($col, 'sales') !== false)) {
                    $salesIndex = $index;
                }
            }

            if ($userIndex === null || $dateIndex === null) {
                return back()->with('error', 'Kolom User/Nama atau Tanggal tidak ditemukan.');
            }

            // Cache users for faster lookup
            $users = User::where('role', 'user')->get()->keyBy(function ($user) {
                return strtoupper(trim($user->name));
            });

            $importedCount = 0;
            $skippedCount = 0;
            $skippedNames = [];

            foreach ($dataRows as $row) {
                // Skip empty rows
                if (!isset($row[$userIndex]) || empty(trim(strval($row[$userIndex])))) {
                    continue;
                }
                if (!isset($row[$dateIndex]) || empty(trim(strval($row[$dateIndex])))) {
                    continue;
                }

                $nameValue = strtoupper(trim(strval($row[$userIndex])));
                $dateValue = trim(strval($row[$dateIndex]));

                // Find user
                $user = $users->get($nameValue);
                if (!$user) {
                    // Try partial match
                    $user = $users->first(function ($u) use ($nameValue) {
                        return strtoupper(trim($u->name)) === $nameValue;
                    });
                }

                if (!$user) {
                    $skippedCount++;
                    if (!in_array($nameValue, $skippedNames)) {
                        $skippedNames[] = $nameValue;
                    }
                    continue;
                }

                // Parse date
                $attendanceDate = null;

                // Try Indonesian format: "16 Januari 2025"
                if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{4})/', $dateValue, $matches)) {
                    $day = (int) $matches[1];
                    $monthName = strtolower($matches[2]);
                    $year = (int) $matches[3];

                    if (isset($indonesianMonths[$monthName])) {
                        $month = $indonesianMonths[$monthName];
                        $attendanceDate = Carbon::createFromDate($year, $month, $day)->toDateString();
                    }
                }

                // Try standard format: "2025-01-16" or "16/01/2025"
                if (!$attendanceDate) {
                    try {
                        $attendanceDate = Carbon::parse($dateValue)->toDateString();
                    } catch (\Exception $e) {
                        // Skip if date cannot be parsed
                    }
                }

                if (!$attendanceDate) {
                    $skippedCount++;
                    continue;
                }

                // Parse duration (handle comma as decimal: 2,5 = 2.5 hours)
                $durationMinutes = 0;
                if ($durationIndex !== null && isset($row[$durationIndex])) {
                    $durationStr = strval($row[$durationIndex]);
                    $durationStr = str_replace(',', '.', $durationStr);
                    $durationStr = preg_replace('/[^0-9.]/', '', $durationStr);
                    $durationHours = is_numeric($durationStr) ? (float) $durationStr : 0;
                    $durationMinutes = (int) round($durationHours * 60);
                }

                // Parse optional fields
                $contentEdit = 0;
                if ($contentEditIndex !== null && isset($row[$contentEditIndex])) {
                    $contentEdit = (int) preg_replace('/[^0-9]/', '', strval($row[$contentEditIndex]));
                }

                $contentLive = 0;
                if ($contentLiveIndex !== null && isset($row[$contentLiveIndex])) {
                    $contentLive = (int) preg_replace('/[^0-9]/', '', strval($row[$contentLiveIndex]));
                }

                $sales = 0;
                if ($salesIndex !== null && isset($row[$salesIndex])) {
                    $sales = (int) preg_replace('/[^0-9]/', '', strval($row[$salesIndex]));
                }

                // Check if attendance already exists for this user and date
                $existingAttendance = Attendance::where('user_id', $user->id)
                    ->where('attendance_date', $attendanceDate)
                    ->first();

                if ($existingAttendance) {
                    // Update existing attendance (add values)
                    $existingAttendance->live_duration_minutes += $durationMinutes;
                    $existingAttendance->content_edit_count += $contentEdit;
                    $existingAttendance->content_live_count += $contentLive;
                    $existingAttendance->sales_count += $sales;
                    $existingAttendance->save();
                } else {
                    // Create new attendance
                    Attendance::create([
                        'user_id' => $user->id,
                        'attendance_date' => $attendanceDate,
                        'live_duration_minutes' => $durationMinutes,
                        'content_edit_count' => $contentEdit,
                        'content_live_count' => $contentLive,
                        'sales_count' => $sales,
                        'status' => 'validated',
                    ]);
                }

                $importedCount++;
            }

            $message = "Import berhasil! {$importedCount} data diimport.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} data dilewati.";
                if (count($skippedNames) > 0 && count($skippedNames) <= 5) {
                    $message .= " Nama tidak ditemukan: " . implode(', ', $skippedNames);
                }
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for attendance import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['User', 'Tanggal', 'Durasi', 'Konten Edit', 'Konten Live', 'Penjualan'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            ['NAMA USER', '16 Januari 2025', '3', '2', '1', '5'],
            ['NAMA USER 2', '17 Januari 2025', '2,5', '3', '2', '10'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add notes
        $sheet->setCellValue('A5', 'Catatan:');
        $sheet->setCellValue('A6', '- User: Nama harus persis sama dengan nama di sistem');
        $sheet->setCellValue('A7', '- Tanggal: Format "16 Januari 2025"');
        $sheet->setCellValue('A8', '- Durasi: Dalam jam (gunakan koma untuk desimal, contoh: 2,5)');
        $sheet->setCellValue('A9', '- Konten Edit, Konten Live, Penjualan: Angka bulat');

        $sheet->getStyle('A5:A9')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));

        // Set sheet title
        $sheet->setTitle('Template Absensi');

        // Create response
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'template_import_absensi.xlsx';
        $tempFile = storage_path('app/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
