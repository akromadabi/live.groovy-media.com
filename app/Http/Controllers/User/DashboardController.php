<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\Setting;
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

        // Single aggregate query for all-time user stats
        $allTimeStats = $user->attendances()
            ->selectRaw('
                COUNT(*) as total_live,
                COALESCE(SUM(live_duration_minutes), 0) as total_minutes,
                COALESCE(SUM(sales_count), 0) as total_sales,
                COALESCE(SUM(content_edit_count), 0) as total_content_edit,
                COALESCE(SUM(content_live_count), 0) as total_content_live
            ')
            ->first();

        if ($startDate && $endDate) {
            $filteredStats = $user->attendances()
                ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw('
                    COALESCE(SUM(live_duration_minutes), 0) as total_minutes,
                    COALESCE(SUM(sales_count), 0) as total_sales,
                    COALESCE(SUM(content_edit_count), 0) as total_content_edit,
                    COALESCE(SUM(content_live_count), 0) as total_content_live
                ')
                ->first();
        } else {
            $filteredStats = $allTimeStats;
        }

        $stats = [
            'total_live' => (int) ($allTimeStats->total_live ?? 0),
            'total_hours' => round(($allTimeStats->total_minutes ?? 0) / 60, 1),
            'estimated_salary' => $this->calculatePendingSalary($user),
            'this_month_hours' => round(($filteredStats->total_minutes ?? 0) / 60, 1),
            'this_month_sales' => (int) ($filteredStats->total_sales ?? 0),
            'this_month_content_edit' => (int) ($filteredStats->total_content_edit ?? 0),
            'this_month_content_live' => (int) ($filteredStats->total_content_live ?? 0),
        ];

        // Calculate T1/T2 and target achievement for selected month (use current month if no filter)
        $termYear = $selectedYear ?: now()->year;
        $termMonth = $selectedMonth ?: now()->month;
        $scheme = $user->salaryScheme;

        $targetMonthStart = Carbon::createFromDate($termYear, $termMonth, 1)->startOfDay();
        $targetMonthEnd = $targetMonthStart->copy()->endOfMonth();

        // Single query for the target month attendances (used for T1, T2, targets, platforms, and daily bonus)
        $targetMonthAttendances = $user->attendances()
            ->whereBetween('attendance_date', [$targetMonthStart->toDateString(), $targetMonthEnd->toDateString()])
            ->get();

        $termData = $this->calculateTermDataFromCollection($targetMonthAttendances, $scheme, $termYear, $termMonth);

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

        // Optimized: Calculate hours & sales per user in a single aggregated query
        $userMetrics = User::where('role', 'user')
            ->where('is_active', true)
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

        // Calculate target achievement data for the selected/current month from $targetMonthAttendances
        $targetMonth = $termMonth;
        $targetYear = $termYear;
        $endDay = $targetMonthEnd->day;

        $validTargetAttendances = $targetMonthAttendances->whereIn('status', ['pending', 'validated']);
        $targetMonthMinutes = $validTargetAttendances->sum('live_duration_minutes');
        $monthlyHours = round($targetMonthMinutes / 60, 1);

        // Active marketplaces & dynamic per-platform target calculations
        $activeMarketplaces = Setting::getActiveMarketplaces();
        $platformTargets = [];
        $hasCustomPlatformTargets = false;

        // Check if scheme has custom targets configured in platform_rates
        if ($scheme && is_array($scheme->platform_rates) && count($activeMarketplaces) > 1) {
            foreach ($activeMarketplaces as $platKey => $platInfo) {
                if (isset($scheme->platform_rates[$platKey]) && is_array($scheme->platform_rates[$platKey])) {
                    $pTarget = isset($scheme->platform_rates[$platKey]['target_hours'])
                        ? (float) $scheme->platform_rates[$platKey]['target_hours']
                        : 0;
                    if ($pTarget > 0) {
                        $hasCustomPlatformTargets = true;
                    }
                }
            }
        }

        // Group monthly attendances by platform in memory
        $monthAttendancesByPlatform = [];
        foreach ($validTargetAttendances as $vAtt) {
            $pKey = $vAtt->platform ?? 'tiktok';
            $monthAttendancesByPlatform[$pKey] = ($monthAttendancesByPlatform[$pKey] ?? 0) + $vAtt->live_duration_minutes;
        }

        if ($hasCustomPlatformTargets) {
            $totalPlatformTarget = 0;
            foreach ($activeMarketplaces as $platKey => $platInfo) {
                $pTarget = $scheme->getTargetHoursForPlatform($platKey);
                $pMinutes = (int) ($monthAttendancesByPlatform[$platKey] ?? 0);
                $pHours = round($pMinutes / 60, 1);
                $pPercent = $pTarget > 0 ? min(100, round(($pHours / $pTarget) * 100)) : 0;
                $pMet = $pTarget > 0 && $pHours >= $pTarget;

                $platformTargets[$platKey] = [
                    'key' => $platKey,
                    'name' => $platInfo['name'],
                    'short_name' => $platInfo['short_name'],
                    'target_hours' => $pTarget,
                    'current_hours' => $pHours,
                    'progress_percent' => $pPercent,
                    'target_met' => $pMet,
                    'remaining_hours' => max(0, round($pTarget - $pHours, 1)),
                ];
                $totalPlatformTarget += $pTarget;
            }

            $monthlyTarget = $totalPlatformTarget > 0 ? $totalPlatformTarget : ($scheme ? $scheme->calculateMonthlyTarget($targetYear, $targetMonth) : 0);
        } else {
            $monthlyTarget = $scheme ? $scheme->calculateMonthlyTarget($targetYear, $targetMonth) : 0;
        }

        // Calculate bonus in memory (always calculate for display, regardless of target met)
        $monthlyBonus = 0;
        if ($scheme) {
            $dailySalesMap = [];
            foreach ($validTargetAttendances as $vAtt) {
                $dStr = $vAtt->attendance_date->toDateString();
                $dailySalesMap[$dStr] = ($dailySalesMap[$dStr] ?? 0) + (int) $vAtt->sales_count;
            }

            $userThreshold = $scheme->bonus_pcs_threshold;
            $userBonusAmt = $scheme->bonus_amount !== null ? (float) $scheme->bonus_amount : null;
            foreach ($dailySalesMap as $totalSalesForDay) {
                $monthlyBonus += \App\Models\BonusTier::getBonusForSales($totalSalesForDay, $userThreshold, $userBonusAmt);
            }
        }

        $targetMet = $monthlyHours >= $monthlyTarget;
        $targetPercent = $monthlyTarget > 0 ? min(100, round(($monthlyHours / $monthlyTarget) * 100)) : 0;

        // Gamification calculations for next bonus tier
        $userThreshold = $scheme ? $scheme->bonus_pcs_threshold : \App\Models\Setting::getValue('bonus_pcs_threshold', 20);
        $userThreshold = max(1, (int) ($userThreshold ?: 20));
        $userBonusAmt = $scheme && $scheme->bonus_amount !== null ? (float) $scheme->bonus_amount : (float) \App\Models\Setting::getValue('bonus_amount', 10000);

        $monthSales = $stats['this_month_sales'];
        $currentMultiplier = floor($monthSales / $userThreshold);
        $nextTierTarget = ($currentMultiplier + 1) * $userThreshold;
        $remainingToNext = max(0, $nextTierTarget - $monthSales);
        $tierProgressPercent = min(100, round((($monthSales % $userThreshold) / $userThreshold) * 100));
        $nextBonusTotal = ($currentMultiplier + 1) * $userBonusAmt;

        $bonusGamification = [
            'threshold' => $userThreshold,
            'bonus_per_tier' => $userBonusAmt,
            'current_sales' => $monthSales,
            'current_multiplier' => $currentMultiplier,
            'next_target' => $nextTierTarget,
            'remaining_to_next' => $remainingToNext,
            'remaining_sales' => $remainingToNext,
            'progress_percent' => $tierProgressPercent,
            'next_bonus_total' => $nextBonusTotal,
        ];

        return view('user.dashboard', compact(
            'stats',
            'recentAttendances',
            'monthlyAttendance',
            'termData',
            'selectedMonth',
            'selectedYear',
            'filterLabel',
            'hoursPerUser',
            'salesPerUser',
            'endDay',
            'monthlyTarget',
            'monthlyHours',
            'monthlyBonus',
            'targetMet',
            'targetPercent',
            'bonusGamification',
            'platformTargets'
        ));
    }

    /**
     * Calculate T1 and T2 data in memory from already fetched month attendances
     */
    private function calculateTermDataFromCollection($monthAttendances, $scheme, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $t1Attendances = $monthAttendances->filter(fn($a) => (int) $a->attendance_date->day <= 15);
        $t2Attendances = $monthAttendances->filter(fn($a) => (int) $a->attendance_date->day >= 16);

        $t1Hours = round($t1Attendances->sum('live_duration_minutes') / 60, 1);
        $t2Hours = round($t2Attendances->sum('live_duration_minutes') / 60, 1);

        $t1Salary = 0;
        $t2Salary = 0;

        if ($scheme) {
            foreach ($t1Attendances as $a) {
                $hours = $a->live_duration_minutes / 60;
                $plat = $a->platform ?? 'tiktok';
                $t1Salary += $hours * $scheme->getHourlyRateForPlatform($plat);
                $t1Salary += ($a->content_edit_count ?? 0) * $scheme->getContentEditRateForPlatform($plat);
                $t1Salary += ($a->content_live_count ?? 0) * $scheme->getContentLiveRateForPlatform($plat);
            }

            foreach ($t2Attendances as $a) {
                $hours = $a->live_duration_minutes / 60;
                $plat = $a->platform ?? 'tiktok';
                $t2Salary += $hours * $scheme->getHourlyRateForPlatform($plat);
                $t2Salary += ($a->content_edit_count ?? 0) * $scheme->getContentEditRateForPlatform($plat);
                $t2Salary += ($a->content_live_count ?? 0) * $scheme->getContentLiveRateForPlatform($plat);
            }

            $fullMonthHours = $t1Hours + $t2Hours;
            $monthlyTarget = $scheme->calculateMonthlyTarget($year, $month);
            $targetMet = $fullMonthHours >= $monthlyTarget;

            if ($targetMet) {
                $dailySales = [];
                foreach ($monthAttendances as $a) {
                    $date = $a->attendance_date->toDateString();
                    $dailySales[$date] = ($dailySales[$date] ?? 0) + $a->sales_count;
                }
                $userThreshold = $scheme->bonus_pcs_threshold;
                $userBonusAmount = $scheme->bonus_amount !== null ? (float) $scheme->bonus_amount : null;
                foreach ($dailySales as $salesCount) {
                    $t2Salary += \App\Models\BonusTier::getBonusForSales($salesCount, $userThreshold, $userBonusAmount);
                }
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
        if (!$scheme) {
            return 0;
        }

        // Get last salary end date
        $lastSalary = $user->salaries()->orderByDesc('period_end')->first();
        $startDate = $lastSalary ? $lastSalary->period_end->addDay() : Carbon::now()->startOfMonth();

        $attendances = $user->attendances()
            ->whereIn('status', ['pending', 'validated'])
            ->where('attendance_date', '>=', $startDate)
            ->get();

        $total = 0;
        $totalHours = 0;
        $dailySales = [];

        foreach ($attendances as $attendance) {
            $hours = $attendance->live_duration_minutes / 60;
            $totalHours += $hours;
            $plat = $attendance->platform ?? 'tiktok';
            $total += $hours * $scheme->getHourlyRateForPlatform($plat);
            $total += ($attendance->content_edit_count ?? 0) * $scheme->getContentEditRateForPlatform($plat);
            $total += ($attendance->content_live_count ?? 0) * $scheme->getContentLiveRateForPlatform($plat);

            // Group daily sales for bonus calculation
            $date = $attendance->attendance_date->toDateString();
            if (!isset($dailySales[$date])) {
                $dailySales[$date] = 0;
            }
            $dailySales[$date] += $attendance->sales_count;
        }

        // Only add sales bonus if target hours are met
        // Use dynamic monthly target calculation
        $monthlyTarget = $scheme->calculateMonthlyTarget();
        $targetMet = $totalHours >= $monthlyTarget;
        if ($targetMet) {
            $userThreshold = $scheme->bonus_pcs_threshold;
            $userBonusAmount = $scheme->bonus_amount !== null ? (float) $scheme->bonus_amount : null;
            foreach ($dailySales as $salesCount) {
                $total += \App\Models\BonusTier::getBonusForSales($salesCount, $userThreshold, $userBonusAmount);
            }
        }

        return round($total, 0);
    }
}
