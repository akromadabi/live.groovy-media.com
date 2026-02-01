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

                // Calculate salary
                $attendanceData[$key]['salary'] +=
                    ($hours * $scheme->hourly_rate) +
                    ($attendance->content_edit_count * $scheme->content_edit_rate) +
                    ($attendance->content_live_count * $scheme->content_live_rate) +
                    ($attendance->sales_count * $scheme->sales_bonus_nominal);
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
        return view('admin.salary-records.show', ['salaryRecord' => $salary_record]);
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
    public function downloadSlip(SalaryRecord $salary_record)
    {
        $salary_record->load('user');
        return view('admin.salary-records.slip', ['salaryRecord' => $salary_record]);
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
            $startDate = Carbon::create($filterYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($filterYear, 12, 31)->endOfYear();
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
                        $termStart = Carbon::create($year, $month, 1);
                        $termEnd = Carbon::create($year, $month, 15);
                    } else {
                        $termStart = Carbon::create($year, $month, 16);
                        $termEnd = Carbon::create($year, $month, 1)->endOfMonth();
                    }

                    // Get attendances for this period
                    $attendances = $user->attendances()
                        ->whereBetween('attendance_date', [$termStart, $termEnd])
                        ->where('status', 'validated')
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
                    $amount = 0;
                    $scheme = $user->salaryScheme;
                    if ($scheme) {
                        $amount += $totalHours * $scheme->hourly_rate;
                        $amount += $totalContentEdit * $scheme->content_edit_rate;
                        $amount += $totalContentLive * $scheme->content_live_rate;
                        $amount += $totalSales * $scheme->sales_bonus_nominal;
                    }

                    // Create the salary record
                    SalaryRecord::create([
                        'user_id' => $user->id,
                        'year' => $year,
                        'month' => $month,
                        'term' => $term,
                        'amount' => round($amount),
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

        return back()->with('success', "Berhasil generate {$generated} rekap gaji. {$skipped} data sudah ada.");
    }
}
