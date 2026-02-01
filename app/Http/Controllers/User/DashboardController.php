<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters (empty = all)
        $selectedMonth = $request->input('month', '');
        $selectedYear = $request->input('year', '');

        // Build date range based on filters
        $startDate = null;
        $endDate = null;
        $filterLabel = 'Seluruh Waktu';

        if ($selectedYear && $selectedMonth) {
            $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            $filterLabel = Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        } elseif ($selectedYear) {
            $startDate = Carbon::create($selectedYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($selectedYear, 12, 31)->endOfYear();
            $filterLabel = 'Tahun ' . $selectedYear;
        } elseif ($selectedMonth) {
            $selectedYear = now()->year;
            $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            $filterLabel = Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        }

        // Get filtered statistics
        $filteredQuery = $user->attendances();
        if ($startDate && $endDate) {
            $filteredQuery = $filteredQuery->whereBetween('attendance_date', [$startDate, $endDate]);
        }
        $filteredAttendances = $filteredQuery->get();

        $stats = [
            'total_live' => $user->attendances()->count(),
            'total_hours' => round($user->attendances()->sum('live_duration_minutes') / 60, 1),
            'estimated_salary' => $this->calculatePendingSalary($user),
            'this_month_hours' => round($filteredAttendances->sum('live_duration_minutes') / 60, 1),
            'this_month_sales' => $filteredAttendances->sum('sales_count'),
            'this_month_content_edit' => $filteredAttendances->sum('content_edit_count'),
            'this_month_content_live' => $filteredAttendances->sum('content_live_count'),
        ];

        // Calculate T1/T2 for selected month (use current month if no filter)
        $termYear = $selectedYear ?: now()->year;
        $termMonth = $selectedMonth ?: now()->month;
        $scheme = $user->salaryScheme;
        $termData = $this->calculateTermData($user, $scheme, $termYear, $termMonth);

        // Recent attendances
        $recentAttendances = $user->attendances()
            ->orderByDesc('attendance_date')
            ->limit(5)
            ->get();

        // For charts, if no filter use all data for per-user charts
        $chartStartDate = $startDate;
        $chartEndDate = $endDate;

        // Monthly attendance for chart (daily data)
        $monthlyAttendance = $user->attendances()
            ->whereBetween('attendance_date', [$chartStartDate, $chartEndDate])
            ->selectRaw('DATE(attendance_date) as date, SUM(live_duration_minutes) as total_minutes, SUM(sales_count) as total_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get hours per user for all active users (for comparison chart)
        $hoursPerUserQuery = User::where('role', 'user')
            ->where('is_active', true);

        if ($chartStartDate && $chartEndDate) {
            $hoursPerUserQuery->with([
                'attendances' => function ($query) use ($chartStartDate, $chartEndDate) {
                    $query->whereBetween('attendance_date', [$chartStartDate, $chartEndDate]);
                }
            ]);
        } else {
            $hoursPerUserQuery->with('attendances');
        }

        $hoursPerUser = $hoursPerUserQuery->get()
            ->map(function ($u) {
                $totalMinutes = $u->attendances->sum('live_duration_minutes');
                return [
                    'name' => $u->name,
                    'hours' => round($totalMinutes / 60, 1),
                ];
            })
            ->sortByDesc('hours')
            ->values();

        // Get sales per user for all active users
        $salesPerUserQuery = User::where('role', 'user')
            ->where('is_active', true);

        if ($chartStartDate && $chartEndDate) {
            $salesPerUserQuery->with([
                'attendances' => function ($query) use ($chartStartDate, $chartEndDate) {
                    $query->whereBetween('attendance_date', [$chartStartDate, $chartEndDate]);
                }
            ]);
        } else {
            $salesPerUserQuery->with('attendances');
        }

        $salesPerUser = $salesPerUserQuery->get()
            ->map(function ($u) {
                $totalSales = $u->attendances->sum('sales_count');
                return [
                    'name' => $u->name,
                    'sales' => $totalSales,
                ];
            })
            ->sortByDesc('sales')
            ->values();

        return view('user.dashboard', compact(
            'stats',
            'recentAttendances',
            'monthlyAttendance',
            'termData',
            'selectedMonth',
            'selectedYear',
            'filterLabel',
            'hoursPerUser',
            'salesPerUser'
        ));
    }

    /**
     * Calculate T1 and T2 data for specified month
     */
    private function calculateTermData($user, $scheme, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        // T1: 1-15
        $t1Start = Carbon::create($year, $month, 1);
        $t1End = Carbon::create($year, $month, 15);

        // T2: 16-end of month
        $t2Start = Carbon::create($year, $month, 16);
        $t2End = Carbon::create($year, $month, 1)->endOfMonth();

        $t1Attendances = $user->attendances()
            ->whereBetween('attendance_date', [$t1Start, $t1End])
            ->get();

        $t2Attendances = $user->attendances()
            ->whereBetween('attendance_date', [$t2Start, $t2End])
            ->get();

        $t1Hours = round($t1Attendances->sum('live_duration_minutes') / 60, 1);
        $t2Hours = round($t2Attendances->sum('live_duration_minutes') / 60, 1);

        $t1Salary = 0;
        $t2Salary = 0;

        if ($scheme) {
            foreach ($t1Attendances as $a) {
                $hours = $a->live_duration_minutes / 60;
                $t1Salary += $hours * $scheme->hourly_rate;
                $t1Salary += $a->content_edit_count * $scheme->content_edit_rate;
                $t1Salary += $a->content_live_count * $scheme->content_live_rate;
            }

            foreach ($t2Attendances as $a) {
                $hours = $a->live_duration_minutes / 60;
                $t2Salary += $hours * $scheme->hourly_rate;
                $t2Salary += $a->content_edit_count * $scheme->content_edit_rate;
                $t2Salary += $a->content_live_count * $scheme->content_live_rate;
            }
        }

        return [
            't1_hours' => $t1Hours,
            't1_salary' => round($t1Salary),
            't2_hours' => $t2Hours,
            't2_salary' => round($t2Salary),
        ];
    }

    /**
     * Calculate pending salary from unprocessed attendances
     */
    private function calculatePendingSalary($user)
    {
        $scheme = $user->salaryScheme;
        if (!$scheme)
            return 0;

        // Get last salary end date
        $lastSalary = $user->salaries()->orderByDesc('period_end')->first();
        $startDate = $lastSalary ? $lastSalary->period_end->addDay() : Carbon::now()->startOfMonth();

        $attendances = $user->attendances()
            ->where('status', 'validated')
            ->where('attendance_date', '>=', $startDate)
            ->get();

        $total = 0;
        foreach ($attendances as $attendance) {
            $hours = $attendance->live_duration_minutes / 60;
            $total += $hours * $scheme->hourly_rate;
            $total += $attendance->content_edit_count * $scheme->content_edit_rate;
            $total += $attendance->content_live_count * $scheme->content_live_rate;
            $total += $attendance->sales_count * $scheme->sales_bonus_nominal;
        }

        return round($total, 0);
    }
}
