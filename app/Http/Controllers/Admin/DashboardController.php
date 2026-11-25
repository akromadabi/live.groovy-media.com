<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index(Request $request)
    {
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
            // Month but no year - use current year
            $selectedYear = now()->year;
            $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
            $endDate = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();
            $filterLabel = Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        }

        // Get statistics based on filter
        $attendanceQuery = Attendance::query();
        $salaryQuery = Salary::query();

        if ($startDate && $endDate) {
            $attendanceQuery->whereBetween('attendance_date', [$startDate, $endDate]);
            $salaryQuery->whereBetween('period_start', [$startDate, $endDate]);
        }

        $attAgg = $attendanceQuery->selectRaw('COUNT(*) as total_live, COALESCE(SUM(live_duration_minutes), 0) as total_minutes')->first();

        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_live' => (int) ($attAgg->total_live ?? 0),
            'total_hours' => round(($attAgg->total_minutes ?? 0) / 60, 1),
            'total_salary' => $salaryQuery->sum('total_salary'),
        ];

        // For charts, if no filter use all data for per-user charts
        $chartStartDate = $startDate;
        $chartEndDate = $endDate;

        // Get daily attendances for chart - use current month when no filter for daily view
        $dailyChartStart = $chartStartDate ?: Carbon::now()->startOfMonth();
        $dailyChartEnd = $chartEndDate ?: Carbon::now()->endOfMonth();

        $dailyAttendances = Attendance::whereBetween('attendance_date', [$dailyChartStart, $dailyChartEnd])
            ->selectRaw('DATE(attendance_date) as date, COUNT(*) as count, SUM(live_duration_minutes) as total_minutes')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Optimized: Calculate hours & sales per user in a single aggregated query
        $userMetrics = User::where('role', 'user')
            ->leftJoin('attendances', function ($join) use ($chartStartDate, $chartEndDate) {
                $join->on('users.id', '=', 'attendances.user_id');
                if ($chartStartDate && $chartEndDate) {
                    $join->whereBetween('attendances.attendance_date', [$chartStartDate, $chartEndDate]);
                }
            })
            ->selectRaw('users.name, COALESCE(SUM(attendances.live_duration_minutes), 0) as total_minutes, COALESCE(SUM(attendances.sales_count), 0) as total_sales')
            ->groupBy('users.id', 'users.name')
            ->get();

        $hoursPerUser = $userMetrics
            ->map(function ($row) {
                return [
                    'name' => $row->name,
                    'hours' => round(((int) $row->total_minutes) / 60, 1),
                ];
            })
            ->sortByDesc('hours')
            ->values();

        $salesPerUser = $userMetrics
            ->map(function ($row) {
                return [
                    'name' => $row->name,
                    'sales' => (int) $row->total_sales,
                ];
            })
            ->sortByDesc('sales')
            ->values();

        // Get salary per period (last 6 months)
        $salaryPerMonth = Salary::where('period_start', '>=', Carbon::now()->subMonths(6))
            ->selectRaw('MONTH(period_start) as month, YEAR(period_start) as year, SUM(total_salary) as total')
            ->groupByRaw('YEAR(period_start), MONTH(period_start)')
            ->orderByRaw('YEAR(period_start), MONTH(period_start)')
            ->get();

        // Recent attendances
        $recentAttendances = Attendance::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'dailyAttendances',
            'hoursPerUser',
            'salesPerUser',
            'salaryPerMonth',
            'recentAttendances',
            'selectedMonth',
            'selectedYear',
            'filterLabel'
        ));
    }
}
