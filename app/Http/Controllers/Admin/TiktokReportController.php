<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TiktokReport;
use App\Models\TiktokReportDetail;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TiktokReportController extends Controller
{
    // Indonesian month names mapping
    private $indonesianMonths = [
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

    /**
     * Display daily comparison data with filter and file list
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Get attendance data grouped by date
        $attendanceData = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->select('attendance_date', DB::raw('SUM(live_duration_minutes) as total_minutes'))
            ->groupBy('attendance_date')
            ->pluck('total_minutes', 'attendance_date')
            ->toArray();

        // Get TikTok report data grouped by date
        $tiktokData = TiktokReportDetail::whereBetween('live_date', [$startDate, $endDate])
            ->select('live_date', DB::raw('SUM(duration_minutes) as total_minutes'))
            ->groupBy('live_date')
            ->pluck('total_minutes', 'live_date')
            ->toArray();

        // Merge all dates
        $allDates = array_unique(array_merge(
            array_keys($attendanceData),
            array_keys($tiktokData)
        ));
        sort($allDates);

        // Create daily comparison array
        $dailyData = collect($allDates)->map(function ($date) use ($attendanceData, $tiktokData) {
            return (object) [
                'date' => $date,
                'absen_minutes' => $attendanceData[$date] ?? 0,
                'tiktok_minutes' => $tiktokData[$date] ?? 0,
            ];
        });

        // Calculate totals
        $totalAbsenHours = $dailyData->sum('absen_minutes') / 60;
        $totalTiktokHours = $dailyData->sum('tiktok_minutes') / 60;

        // Get uploaded files list
        $uploadedFiles = TiktokReport::with('uploader')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.tiktok-reports.index', compact(
            'dailyData',
            'totalAbsenHours',
            'totalTiktokHours',
            'year',
            'month',
            'uploadedFiles'
        ));
    }

    /**
     * Store a newly created report
     * Supports format: NAMA, DURASI (JAM), TANGGAL (Indonesian date)
     * Supports multiple file upload
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_files' => 'required|array|min:1',
            'report_files.*' => 'file|mimes:xlsx,xls|max:10240',
        ]);

        $files = $request->file('report_files');
        $successMessages = [];
        $errorMessages = [];

        // Pre-load all users for name matching (case-insensitive) - once for all files
        $allUsers = User::where('role', 'user')->get()->keyBy(function ($user) {
            return strtolower(trim($user->name));
        });

        foreach ($files as $fileIndex => $file) {
            $originalFilename = $file->getClientOriginalName();
            $storedFilename = time() . '_' . $fileIndex . '_' . $originalFilename;

            try {
                $result = $this->processExcelFile($file, $storedFilename, $originalFilename, $allUsers);
                $successMessages[] = $result;
            } catch (\Exception $e) {
                $errorMessages[] = "❌ {$originalFilename}: " . $e->getMessage();
            }
        }

        // Build combined message
        $flashMessages = [];

        if (!empty($successMessages)) {
            $totalFiles = count($successMessages);
            $flash = "✅ {$totalFiles} file berhasil diupload:\n" . implode("\n", $successMessages);
            $flashMessages['success'] = $flash;
        }

        if (!empty($errorMessages)) {
            $flashMessages['error'] = implode("\n", $errorMessages);
        }

        if (empty($successMessages) && !empty($errorMessages)) {
            return back()->with('error', implode("\n", $errorMessages));
        }

        return redirect()->route('admin.tiktok-reports.index')
            ->with($flashMessages);
    }

    /**
     * Process a single Excel file and create report + details
     */
    private function processExcelFile($file, $storedFilename, $originalFilename, $allUsers): string
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Find header row - search for row containing keywords from either English or Indonesian format
        $headerRowIndex = 0;
        $header = null;

        for ($i = 0; $i < min(10, count($data)); $i++) {
            $rowLower = array_map('strtolower', array_map('trim', array_map('strval', $data[$i])));
            $rowString = implode(' ', $rowLower);

            if (
                strpos($rowString, 'durasi') !== false ||
                strpos($rowString, 'duration') !== false ||
                strpos($rowString, 'tanggal') !== false ||
                strpos($rowString, 'start time') !== false ||
                strpos($rowString, 'waktu mulai') !== false
            ) {
                $headerRowIndex = $i;
                $header = $data[$i];
                break;
            }
        }

        if ($header === null) {
            throw new \Exception('Header tidak ditemukan. Pastikan file memiliki kolom "Duration/Durasi" dan "Start time/Waktu mulai".');
        }

        // Get data rows (after header)
        $dataRows = array_slice($data, $headerRowIndex + 1);

        // Convert header to lowercase for matching
        $headerLower = array_map('strtolower', array_map('trim', array_map('strval', $header)));

        // Find column indices - support both Indonesian and English formats
        $nameIndex = null;
        $durationIndex = null;
        $dateIndex = null;

        foreach ($headerLower as $index => $col) {
            // Name column: "nama", "user", "name", "livestream" (EN), "streaming langsung" (ID)
            if (
                $nameIndex === null && (
                    strpos($col, 'nama') !== false ||
                    strpos($col, 'user') !== false ||
                    $col === 'name' ||
                    $col === 'livestream' ||
                    strpos($col, 'streaming') !== false
                )
            ) {
                $nameIndex = $index;
            }
            // Duration column: "durasi" (Indonesian) or "duration" (English)
            if ($durationIndex === null && (strpos($col, 'durasi') !== false || strpos($col, 'duration') !== false)) {
                $durationIndex = $index;
            }
            // Date column: "tanggal" (ID), "waktu mulai" (ID), "start time" (EN), "date" (EN)
            if (
                $dateIndex === null && (
                    strpos($col, 'tanggal') !== false ||
                    strpos($col, 'waktu') !== false ||
                    strpos($col, 'start') !== false ||
                    $col === 'date'
                )
            ) {
                $dateIndex = $index;
            }
        }

        if ($durationIndex === null) {
            throw new \Exception('Kolom "Duration" / "Durasi" tidak ditemukan di file.');
        }
        if ($dateIndex === null) {
            throw new \Exception('Kolom "Start time" / "Waktu mulai" / "Tanggal" tidak ditemukan di file.');
        }

        // Check if duration is in hours (JAM) or seconds
        // "Durasi (Jam)" or column containing "jam"/"hour" = hours format
        // "Duration" or "Durasi" alone = seconds format (raw TikTok data)
        $durationHeader = $headerLower[$durationIndex];
        $isHoursFormat = strpos($durationHeader, 'jam') !== false ||
            strpos($durationHeader, 'hour') !== false;

        $report = TiktokReport::create([
            'filename' => $storedFilename,
            'original_filename' => $originalFilename,
            'report_date' => now(),
            'uploaded_by' => auth()->id(),
            'total_records' => count($dataRows),
            'total_duration_minutes' => 0,
        ]);

        $totalDurationMinutes = 0;
        $importedCount = 0;
        $matchedCount = 0;

        // Process each data row
        foreach ($dataRows as $row) {
            // Skip empty rows or rows without date
            if (!isset($row[$dateIndex]) || empty(trim(strval($row[$dateIndex])))) {
                continue;
            }

            // Parse date
            $dateValue = trim(strval($row[$dateIndex]));
            $liveDate = $this->parseIndonesianDate($dateValue);

            if (!$liveDate) {
                continue; // Skip rows with unparseable dates
            }

            // Parse duration
            $durationValue = isset($row[$durationIndex]) ? $row[$durationIndex] : 0;
            $durationMinutes = $this->parseDuration($durationValue, $isHoursFormat);

            // Parse name and match user
            $userId = null;
            $matchedAttendanceId = null;
            $matchStatus = 'needs_verification';
            $attendanceDurationMinutes = null;

            if ($nameIndex !== null && isset($row[$nameIndex]) && !empty(trim(strval($row[$nameIndex])))) {
                $userName = strtolower(trim(strval($row[$nameIndex])));

                // Try exact match first
                if (isset($allUsers[$userName])) {
                    $userId = $allUsers[$userName]->id;
                } else {
                    // Try partial match (contains)
                    foreach ($allUsers as $nameLower => $user) {
                        if (strpos($nameLower, $userName) !== false || strpos($userName, $nameLower) !== false) {
                            $userId = $user->id;
                            break;
                        }
                    }
                }

                // If user found, try to match with attendance
                if ($userId) {
                    $attendance = Attendance::where('user_id', $userId)
                        ->whereDate('attendance_date', $liveDate)
                        ->first();

                    if ($attendance) {
                        $matchedAttendanceId = $attendance->id;
                        $attendanceDurationMinutes = $attendance->live_duration_minutes;

                        // Auto-determine match status based on duration difference
                        $diff = abs($durationMinutes - $attendanceDurationMinutes);
                        $tolerance = 30; // 30 minutes tolerance

                        if ($diff <= $tolerance) {
                            $matchStatus = 'matched';
                            $matchedCount++;
                        } else {
                            $matchStatus = 'needs_verification';
                        }
                    }
                }
            }

            if ($durationMinutes > 0) {
                TiktokReportDetail::create([
                    'tiktok_report_id' => $report->id,
                    'user_id' => $userId,
                    'live_date' => $liveDate,
                    'duration_minutes' => $durationMinutes,
                    'match_status' => $matchStatus,
                    'matched_attendance_id' => $matchedAttendanceId,
                    'attendance_duration_minutes' => $attendanceDurationMinutes,
                ]);

                $totalDurationMinutes += $durationMinutes;
                $importedCount++;
            }
        }

        // Update total duration
        $report->update([
            'total_duration_minutes' => $totalDurationMinutes,
            'total_records' => $importedCount,
        ]);

        $totalRows = count($dataRows);
        $message = "📄 {$originalFilename}: {$importedCount}/{$totalRows} baris diimport (" . number_format($totalDurationMinutes / 60, 1) . " jam)";
        if ($matchedCount > 0) {
            $message .= " — {$matchedCount} cocok dengan absensi";
        }

        return $message;
    }

    /**
     * Parse Indonesian date format like "16 Januari 2025"
     * Also supports: "2026-01-29 19:16" and Excel serial dates
     */
    private function parseIndonesianDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim(strval($value));

        try {
            // Try Indonesian format: "16 Januari 2025"
            if (preg_match('/(\d{1,2})\s+(\w+)\s+(\d{4})/', $value, $matches)) {
                $day = (int) $matches[1];
                $monthName = strtolower($matches[2]);
                $year = (int) $matches[3];

                if (isset($this->indonesianMonths[$monthName])) {
                    $month = $this->indonesianMonths[$monthName];
                    return Carbon::createFromDate($year, $month, $day)->toDateString();
                }
            }

            // Try standard date format: "2026-01-29" or "2026-01-29 19:16"
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return Carbon::parse($value)->toDateString();
            }

            // Try Excel serial date (numeric)
            if (is_numeric($value) && $value > 30000) {
                return Carbon::createFromFormat('Y-m-d', '1899-12-30')
                    ->addDays((int) $value)
                    ->toDateString();
            }

            // Try other common formats
            return Carbon::parse($value)->toDateString();

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse duration value - supports hours (2,5 or 2.5) and seconds
     */
    private function parseDuration($value, bool $isHours = true): int
    {
        if (empty($value)) {
            return 0;
        }

        // Convert to string and handle Indonesian decimal format (comma instead of dot)
        $valueStr = strval($value);
        $valueStr = str_replace(',', '.', $valueStr);
        $valueStr = preg_replace('/[^0-9.]/', '', $valueStr);

        if (!is_numeric($valueStr) || empty($valueStr)) {
            return 0;
        }

        $numericValue = (float) $valueStr;

        if ($isHours) {
            // Value is in hours, convert to minutes
            return (int) round($numericValue * 60);
        } else {
            // Value is in seconds, convert to minutes
            return (int) round($numericValue / 60);
        }
    }

    /**
     * Display the specified report
     */
    public function show(TiktokReport $tiktokReport)
    {
        $tiktokReport->load(['details.user', 'details.matchedAttendance']);

        $stats = [
            'total' => $tiktokReport->details->count(),
            'matched' => $tiktokReport->details->where('match_status', 'matched')->count(),
            'unmatched' => $tiktokReport->details->where('match_status', 'unmatched')->count(),
            'needs_verification' => $tiktokReport->details->where('match_status', 'needs_verification')->count(),
        ];

        return view('admin.tiktok-reports.show', compact('tiktokReport', 'stats'));
    }

    /**
     * Remove the specified report
     */
    public function destroy(TiktokReport $tiktokReport)
    {
        // Delete related details first
        $tiktokReport->details()->delete();
        $tiktokReport->delete();

        return redirect()->route('admin.tiktok-reports.index')
            ->with('success', 'Report berhasil dihapus.');
    }

    /**
     * Update match status
     */
    public function updateMatchStatus(Request $request, TiktokReportDetail $detail)
    {
        $validated = $request->validate([
            'match_status' => 'required|in:matched,unmatched,needs_verification',
        ]);

        $detail->update(['match_status' => $validated['match_status']]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }
}
