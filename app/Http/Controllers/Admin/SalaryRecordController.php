<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryRecordController extends Controller
{
    /**
     * Display a listing of salary records
     */
    public function index(Request $request)
    {
        $query = SalaryRecord::with('user');

        // Filter by year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Calculate totals for filtered query (before pagination)
        $totals = [
            'total_amount' => (clone $query)->sum('amount'),
            'total_pending' => (clone $query)->where('status', 'pending')->sum('amount'),
            'total_paid' => (clone $query)->where('status', 'paid')->sum('amount'),
            'count_pending' => (clone $query)->where('status', 'pending')->count(),
            'count_paid' => (clone $query)->where('status', 'paid')->count(),
        ];

        $records = $query->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('term')
            ->paginate(20);

        $users = User::where('role', 'user')->orderBy('name')->get();
        $years = range(now()->year, 2020);

        return view('admin.salary-records.index', compact('records', 'users', 'years', 'totals'));
    }

    /**
     * Show the form for creating a new salary record
     */
    public function create()
    {
        $users = User::where('role', 'user')->with('salaryScheme')->orderBy('name')->get();

        // Build attendance data for all users and periods
        $attendanceData = [];

        // Get last 12 months of data
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($users as $user) {
            $scheme = $user->salaryScheme;
            if (!$scheme)
                continue;

            // Get all attendances for this user in the date range
            $attendances = $user->attendances()
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'validated')
                ->get();

            // Group by year-month-term
            foreach ($attendances as $attendance) {
                $date = Carbon::parse($attendance->attendance_date);
                $year = $date->year;
                $month = $date->month;
                $day = $date->day;
                $term = $day <= 15 ? '1' : '2';
                $key = "{$user->id}-{$year}-{$month}-{$term}";

                if (!isset($attendanceData[$key])) {
                    $attendanceData[$key] = [
                        'hours' => 0,
                        'content_edit' => 0,
                        'content_live' => 0,
                        'sales' => 0,
                        'salary' => 0,
                    ];
                }

                $hours = $attendance->live_duration_minutes / 60;
                $attendanceData[$key]['hours'] += round($hours, 1);
                $attendanceData[$key]['content_edit'] += $attendance->content_edit_count;
                $attendanceData[$key]['content_live'] += $attendance->content_live_count;
                $attendanceData[$key]['sales'] += $attendance->sales_count;

                // Calculate base salary (without sales bonus)
                $attendanceData[$key]['salary'] +=
                    ($hours * $scheme->hourly_rate) +
                    ($attendance->content_edit_count * $scheme->content_edit_rate) +
                    ($attendance->content_live_count * $scheme->content_live_rate);

                // Track daily sales for bonus calculation (grouped by date)
                if (!isset($attendanceData[$key]['daily_sales'])) {
                    $attendanceData[$key]['daily_sales'] = [];
                }
                $dateKey = $attendance->attendance_date->toDateString();
                if (!isset($attendanceData[$key]['daily_sales'][$dateKey])) {
                    $attendanceData[$key]['daily_sales'][$dateKey] = 0;
                }
                $attendanceData[$key]['daily_sales'][$dateKey] += $attendance->sales_count;
            }
        }

        // Calculate sales bonus for each period (only T2, using full month hours)
        foreach ($attendanceData as $key => $data) {
            // Parse the key: {user_id}-{year}-{month}-{term}
            $parts = explode('-', $key);
            $userId = $parts[0];
            $term = $parts[3] ?? '1';
            $year = $parts[1] ?? null;
            $month = $parts[2] ?? null;
            $foundUser = $users->firstWhere('id', $userId);
            $scheme = $foundUser ? $foundUser->salaryScheme : null;

            if (isset($data['daily_sales']) && $scheme && $term === '2' && $year && $month) {
                // Get T1 hours for same user+month
                $t1Key = "{$userId}-{$year}-{$month}-1";
                $t1Hours = isset($attendanceData[$t1Key]) ? $attendanceData[$t1Key]['hours'] : 0;
                $fullMonthHours = $t1Hours + $data['hours'];
                $targetMet = $fullMonthHours >= (float) $scheme->monthly_target_hours;

                if ($targetMet) {
                    // Add bonus from T2 daily sales
                    foreach ($data['daily_sales'] as $salesCount) {
                        $attendanceData[$key]['salary'] += \App\Models\BonusTier::getBonusForSales($salesCount);
                    }
                    // Also add bonus from T1 daily sales (bonus for whole month goes to T2)
                    if (isset($attendanceData[$t1Key]['daily_sales'])) {
                        foreach ($attendanceData[$t1Key]['daily_sales'] as $salesCount) {
                            $attendanceData[$key]['salary'] += \App\Models\BonusTier::getBonusForSales($salesCount);
                        }
                    }
                }
                unset($attendanceData[$key]['daily_sales']);
            } elseif (isset($data['daily_sales'])) {
                // T1 or no scheme: keep daily_sales for T2 to reference, but don't add bonus
                // Only clean up if this is T1 (T2 would have been handled above)
                if ($term === '1') {
                    // Don't unset yet - T2 might need to reference T1's daily_sales
                } else {
                    unset($attendanceData[$key]['daily_sales']);
                }
            }
        }

        // Clean up remaining daily_sales from T1 entries
        foreach ($attendanceData as $key => $data) {
            if (isset($data['daily_sales'])) {
                unset($attendanceData[$key]['daily_sales']);
            }
        }

        return view('admin.salary-records.create', compact('users', 'attendanceData'));
    }

    /**
     * Store a newly created salary record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'term' => 'required|in:1,2',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,paid',
        ]);

        // Check if record already exists
        $exists = SalaryRecord::where('user_id', $validated['user_id'])
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('term', $validated['term'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Data gaji untuk user, periode, dan termin ini sudah ada.');
        }

        if ($validated['status'] === 'paid') {
            $validated['paid_at'] = now();
        }

        SalaryRecord::create($validated);

        return redirect()->route('admin.salary-records.index')
            ->with('success', 'Data gaji berhasil disimpan.');
    }

    /**
     * Display the specified salary record
     */
    public function show(SalaryRecord $salary_record)
    {
        $salary_record->load('user');
        $salaryScheme = \App\Models\SalaryScheme::where('user_id', $salary_record->user_id)->first();
        $potentialBonus = \App\Models\BonusTier::getBonusForSales((int) $salary_record->total_sales);
        return view('admin.salary-records.show', [
            'salaryRecord' => $salary_record,
            'salaryScheme' => $salaryScheme,
            'potentialBonus' => $potentialBonus,
        ]);
    }

    /**
     * Show the form for editing the salary record
     */
    public function edit(SalaryRecord $salary_record)
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.salary-records.edit', ['salaryRecord' => $salary_record, 'users' => $users]);
    }

    /**
     * Update the specified salary record
     */
    public function update(Request $request, SalaryRecord $salary_record)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'term' => 'required|in:1,2',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,paid',
        ]);

        // Check if record already exists (excluding current)
        $exists = SalaryRecord::where('user_id', $validated['user_id'])
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('term', $validated['term'])
            ->where('id', '!=', $salary_record->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Data gaji untuk user, periode, dan termin ini sudah ada.');
        }

        if ($validated['status'] === 'paid' && $salary_record->status !== 'paid') {
            $validated['paid_at'] = now();
        }

        $salary_record->update($validated);

        return redirect()->route('admin.salary-records.index')
            ->with('success', 'Data gaji berhasil diperbarui.');
    }

    /**
     * Remove the specified salary record
     */
    public function destroy(SalaryRecord $salary_record)
    {
        $salary_record->delete();

        return redirect()->route('admin.salary-records.index')
            ->with('success', 'Data gaji berhasil dihapus.');
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(SalaryRecord $salary_record)
    {
        $salary_record->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Status berhasil diubah menjadi Dibayar.');
    }

    /**
     * Download salary slip as image
     */
    public function downloadSlip($id)
    {
        $salaryRecord = SalaryRecord::with('user')->findOrFail($id);
        $salaryScheme = \App\Models\SalaryScheme::where('user_id', $salaryRecord->user_id)->first();
        $potentialBonus = \App\Models\BonusTier::getBonusForSales((int) $salaryRecord->total_sales);
        return view('admin.salary-records.slip', compact('salaryRecord', 'salaryScheme', 'potentialBonus'));
    }

    /**
     * Bulk download salary slips (POST)
     * Currently used to handle the route, but JS performs individual downloads
     */
    public function bulkDownload(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal satu rekap gaji');
        }

        // JS handles the actual popups for these IDs
        return back()->with('success', 'Sedang memproses download untuk ' . count($ids) . ' record.');
    }

    /**
     * Generate salary records from existing attendance data
     */
    public function generateAll(Request $request)
    {
        $filterYear = $request->input('year');
        $filterMonth = $request->input('month');
        $filterTerm = $request->input('term');
        $filterUserId = $request->input('user_id');

        $usersQuery = User::where('role', 'user')->with('salaryScheme');
        if ($filterUserId) {
            $usersQuery->where('id', $filterUserId);
        }
        $users = $usersQuery->get();

        $generated = 0;
        $skipped = 0;

        // Determine date range
        if ($filterYear && $filterMonth) {
            // Specific month
            $startDate = Carbon::create($filterYear, $filterMonth, 1)->startOfMonth();
            $endDate = Carbon::create($filterYear, $filterMonth, 1)->endOfMonth();
        } elseif ($filterYear) {
            // Whole year
            $startDate = Carbon::create($filterYear, 1, 1)->startOfYear()->startOfDay();
            $endDate = Carbon::create($filterYear, 12, 31)->endOfYear()->endOfDay();
        } else {
            // Get the earliest attendance date
            $earliestAttendance = \App\Models\Attendance::where('status', 'validated')
                ->orderBy('attendance_date')
                ->first();

            if (!$earliestAttendance) {
                return back()->with('error', 'Tidak ada data absen tervalidasi.');
            }

            $startDate = Carbon::parse($earliestAttendance->attendance_date)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $year = $currentDate->year;
            $month = $currentDate->month;

            // Skip if filtering by specific month and this is not that month
            if ($filterMonth && $month != $filterMonth) {
                $currentDate->addMonth();
                continue;
            }

            /** @var User $user */
            foreach ($users as $user) {
                // Determine which terms to process
                $termsToProcess = $filterTerm ? [$filterTerm] : ['1', '2'];

                foreach ($termsToProcess as $term) {
                    // Check if record already exists
                    $exists = SalaryRecord::where('user_id', $user->id)
                        ->where('year', $year)
                        ->where('month', $month)
                        ->where('term', $term)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    // Calculate date range for this term
                    if ($term === '1') {
                        $termStart = Carbon::create($year, $month, 1)->startOfDay();
                        $termEnd = Carbon::create($year, $month, 15)->endOfDay();
                    } else {
                        $termStart = Carbon::create($year, $month, 16)->startOfDay();
                        $termEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
                    }

                    // Get attendances for this period (pending and validated)
                    $attendances = $user->attendances()
                        ->whereBetween('attendance_date', [$termStart, $termEnd])
                        ->whereIn('status', ['pending', 'validated'])
                        ->get();

                    if ($attendances->isEmpty()) {
                        continue;
                    }

                    // Calculate totals
                    $totalMinutes = $attendances->sum('live_duration_minutes');
                    $totalHours = round($totalMinutes / 60, 2);
                    $totalLiveCount = $attendances->count();
                    $totalSales = $attendances->sum('sales_count');
                    $totalContentEdit = $attendances->sum('content_edit_count');
                    $totalContentLive = $attendances->sum('content_live_count');

                    // Calculate amount based on salary scheme
                    $baseSalary = 0;
                    $bonusAmount = 0;
                    $targetMet = false;
                    $scheme = $user->salaryScheme;
                    if ($scheme) {
                        // Base salary: hours + content bonuses
                        $baseSalary += $totalHours * $scheme->hourly_rate;
                        $baseSalary += $totalContentEdit * $scheme->content_edit_rate;
                        $baseSalary += $totalContentLive * $scheme->content_live_rate;

                        // Bonus only applies on T2 (end of month evaluation)
                        if ($term === '2') {
                            // Get T1 hours for this user in the same month
                            $t1Start = Carbon::create($year, $month, 1)->startOfDay();
                            $t1End = Carbon::create($year, $month, 15)->endOfDay();
                            $t1Hours = $user->attendances()
                                ->whereBetween('attendance_date', [$t1Start, $t1End])
                                ->whereIn('status', ['pending', 'validated'])
                                ->sum('live_duration_minutes') / 60;

                            // Full month hours = T1 + T2
                            $fullMonthHours = $t1Hours + $totalHours;
                            $targetMet = $fullMonthHours >= (float) $scheme->monthly_target_hours;

                            // Only apply sales bonus if full month target is met
                            if ($targetMet) {
                                // Get ALL month sales (T1 + T2) for bonus calculation
                                $allMonthAttendances = $user->attendances()
                                    ->whereBetween('attendance_date', [$t1Start, $termEnd])
                                    ->whereIn('status', ['pending', 'validated'])
                                    ->get();

                                $dailySales = [];
                                foreach ($allMonthAttendances as $a) {
                                    $date = $a->attendance_date->toDateString();
                                    if (!isset($dailySales[$date])) {
                                        $dailySales[$date] = 0;
                                    }
                                    $dailySales[$date] += $a->sales_count;
                                }

                                foreach ($dailySales as $salesCount) {
                                    $bonusAmount += \App\Models\BonusTier::getBonusForSales($salesCount);
                                }
                            }
                        }
                        // T1: no bonus evaluation, targetMet stays false
                    }

                    $amount = round($baseSalary + $bonusAmount);

                    // Create the salary record
                    SalaryRecord::create([
                        'user_id' => $user->id,
                        'year' => $year,
                        'month' => $month,
                        'term' => $term,
                        'amount' => $amount,
                        'base_salary' => round($baseSalary),
                        'bonus_amount' => round($bonusAmount),
                        'target_met' => $targetMet,
                        'total_hours' => $totalHours,
                        'total_live_count' => $totalLiveCount,
                        'total_sales' => $totalSales,
                        'total_content_edit' => $totalContentEdit,
                        'total_content_live' => $totalContentLive,
                        'status' => 'pending',
                        'notes' => 'Generated from attendance data',
                    ]);

                    $generated++;
                }
            }

            $currentDate->addMonth();
        }

        $noSchemeCount = $users->filter(fn($u) => !$u->salaryScheme)->count();
        $message = "Berhasil generate {$generated} record gaji untuk " . ($users->count()) . " user.";

        if ($skipped > 0) {
            $message .= " ({$skipped} data sudah ada).";
        }

        if ($noSchemeCount > 0) {
            $message .= " Perhatian: {$noSchemeCount} user tidak memiliki skema gaji (nilai gaji akan 0).";
        }

        // Count unvalidated attendances to inform user
        $unvalidatedCount = \App\Models\Attendance::where('status', '!=', 'validated')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->count();

        if ($unvalidatedCount > 0) {
            $message .= " Perhatian: Ada {$unvalidatedCount} data absen yang belum divalidasi dan tidak ikut dalam rekap.";
        }

        return back()->with('success', $message);
    }
}
