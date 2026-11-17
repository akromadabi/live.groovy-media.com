<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PerformanceController extends Controller
{
    /**
     * Compute performance leaderboard and metrics data based on request filters
     */
    public function getPerformanceData(Request $request): array
    {
        $period = $request->input('period');
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');
        $status = $request->input('status', 'all');
        $category = $request->input('category', 'overall');
        $search = $request->input('search', '');

        // Determine default period if not specified
        if (!$period && !$selectedMonth && !$selectedYear) {
            $hasDataThisMonth = Attendance::whereBetween('attendance_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])->exists();

            if ($hasDataThisMonth) {
                $period = 'this_month';
            } else {
                $period = 'all';
            }
        }

        // Determine date range and label
        $startDate = null;
        $endDate = null;
        $filterLabel = 'Seluruh Waktu';

        if ($selectedMonth && $selectedYear) {
            $period = 'custom';
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->toDateString();
            $filterLabel = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F Y');
        } elseif ($selectedYear) {
            $period = 'custom';
            $startDate = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear()->toDateString();
            $endDate = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear()->toDateString();
            $filterLabel = 'Tahun ' . $selectedYear;
        } elseif ($period === 'this_month') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
            $filterLabel = 'Bulan Ini (' . now()->translatedFormat('F Y') . ')';
        } elseif ($period === 'last_month') {
            $lastMonth = now()->subMonth();
            $startDate = $lastMonth->startOfMonth()->toDateString();
            $endDate = $lastMonth->endOfMonth()->toDateString();
            $filterLabel = 'Bulan Lalu (' . $lastMonth->translatedFormat('F Y') . ')';
        } elseif ($period === 'last_30_days') {
            $startDate = now()->subDays(30)->toDateString();
            $endDate = now()->toDateString();
            $filterLabel = '30 Hari Terakhir';
        } elseif ($period === 'last_7_days') {
            $startDate = now()->subDays(7)->toDateString();
            $endDate = now()->toDateString();
            $filterLabel = '7 Hari Terakhir';
        } else {
            $period = 'all';
            $filterLabel = 'Seluruh Waktu';
        }

        // Query active users with role 'user'
        $usersQuery = User::where('role', 'user')->where('is_active', true);
        if (!empty($search)) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }
        $users = $usersQuery->get();

        // Optimized: Single SQL aggregation query instead of N+1 queries in loop
        $statsQuery = Attendance::query()
            ->selectRaw('
                user_id,
                COUNT(*) as sessions_count,
                COALESCE(SUM(live_duration_minutes), 0) as total_minutes,
                COALESCE(SUM(sales_count), 0) as total_sales,
                COALESCE(SUM(content_edit_count), 0) as total_content_edit,
                COALESCE(SUM(content_live_count), 0) as total_content_live,
                COALESCE(MAX(sales_count), 0) as max_single_sales,
                COALESCE(MAX(live_duration_minutes), 0) as max_single_duration
            ');

        if ($startDate && $endDate) {
            $statsQuery->whereBetween('attendance_date', [$startDate, $endDate]);
        }

        if ($status === 'validated') {
            $statsQuery->where('status', 'validated');
        } elseif ($status === 'pending') {
            $statsQuery->where('status', 'pending');
        }

        $userStats = $statsQuery->groupBy('user_id')->get()->keyBy('user_id');

        $performances = collect();

        foreach ($users as $user) {
            $stat = $userStats->get($user->id);

            $sessionsCount = $stat ? (int) $stat->sessions_count : 0;
            $totalMinutes = $stat ? (int) $stat->total_minutes : 0;
            $totalSales = $stat ? (int) $stat->total_sales : 0;
            $totalContentEdit = $stat ? (int) $stat->total_content_edit : 0;
            $totalContentLive = $stat ? (int) $stat->total_content_live : 0;
            $totalContent = $totalContentEdit + $totalContentLive;
            $maxSingleSales = $stat ? (int) $stat->max_single_sales : 0;
            $maxSingleDuration = $stat ? (int) $stat->max_single_duration : 0;

            $totalHours = round($totalMinutes / 60, 1);
            $avgDurationMinutes = $sessionsCount > 0 ? round($totalMinutes / $sessionsCount) : 0;
            $avgSales = $sessionsCount > 0 ? round($totalSales / $sessionsCount, 1) : 0;
            $avgContent = $sessionsCount > 0 ? round($totalContent / $sessionsCount, 1) : 0;

            $performances->push([
                'user' => $user,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'task' => $user->task ?? 'Host Live',
                'sessions_count' => $sessionsCount,
                'total_minutes' => $totalMinutes,
                'total_hours' => $totalHours,
                'avg_duration_minutes' => $avgDurationMinutes,
                'max_single_duration' => $maxSingleDuration,
                'total_sales' => $totalSales,
                'avg_sales' => $avgSales,
                'max_single_sales' => $maxSingleSales,
                'total_content_edit' => $totalContentEdit,
                'total_content_live' => $totalContentLive,
                'total_content' => $totalContent,
                'avg_content' => $avgContent,
            ]);
        }

        $maxMinutes = $performances->max('total_minutes') ?: 1;
        $maxSales = $performances->max('total_sales') ?: 1;
        $maxContent = $performances->max('total_content') ?: 1;

        $rankedPerformances = $performances->map(function ($item) use ($maxMinutes, $maxSales, $maxContent) {
            $durationScore = $maxMinutes > 0 ? round(($item['total_minutes'] / $maxMinutes) * 100, 1) : 0;
            $salesScore = $maxSales > 0 ? round(($item['total_sales'] / $maxSales) * 100, 1) : 0;
            $contentScore = $maxContent > 0 ? round(($item['total_content'] / $maxContent) * 100, 1) : 0;

            $overallScore = round(($durationScore * 0.35) + ($salesScore * 0.40) + ($contentScore * 0.25), 1);

            if ($overallScore >= 85) {
                $tier = 'Diamond Host';
                $tierBadge = 'badge-diamond';
            } elseif ($overallScore >= 70) {
                $tier = 'Gold Star';
                $tierBadge = 'badge-gold';
            } elseif ($overallScore >= 50) {
                $tier = 'Silver Pro';
                $tierBadge = 'badge-silver';
            } elseif ($overallScore >= 25) {
                $tier = 'Bronze Host';
                $tierBadge = 'badge-bronze';
            } else {
                $tier = 'Rising Star';
                $tierBadge = 'badge-secondary';
            }

            $item['duration_score'] = $durationScore;
            $item['sales_score'] = $salesScore;
            $item['content_score'] = $contentScore;
            $item['overall_score'] = $overallScore;
            $item['tier'] = $tier;
            $item['tier_badge'] = $tierBadge;

            return $item;
        });

        // Sort based on category
        if ($category === 'duration') {
            $sorted = $rankedPerformances->sort(function ($a, $b) {
                if ($a['total_minutes'] === $b['total_minutes']) {
                    return $b['total_sales'] <=> $a['total_sales'];
                }
                return $b['total_minutes'] <=> $a['total_minutes'];
            });
        } elseif ($category === 'sales') {
            $sorted = $rankedPerformances->sort(function ($a, $b) {
                if ($a['total_sales'] === $b['total_sales']) {
                    return $b['total_minutes'] <=> $a['total_minutes'];
                }
                return $b['total_sales'] <=> $a['total_sales'];
            });
        } elseif ($category === 'content') {
            $sorted = $rankedPerformances->sort(function ($a, $b) {
                if ($a['total_content'] === $b['total_content']) {
                    return $b['total_sales'] <=> $a['total_sales'];
                }
                return $b['total_content'] <=> $a['total_content'];
            });
        } else {
            $sorted = $rankedPerformances->sort(function ($a, $b) {
                if ($a['overall_score'] === $b['overall_score']) {
                    return $b['total_sales'] <=> $a['total_sales'];
                }
                return $b['overall_score'] <=> $a['overall_score'];
            });
        }

        $rank = 1;
        $leaderboard = $sorted->values()->map(function ($item) use (&$rank) {
            $item['rank'] = $rank++;
            return $item;
        });

        $topFirst = $leaderboard->get(0);
        $topSecond = $leaderboard->get(1);
        $topThird = $leaderboard->get(2);

        $mvpLeader = $rankedPerformances->sortByDesc('overall_score')->first();
        $durationLeader = $rankedPerformances->sortByDesc('total_minutes')->first();
        $salesLeader = $rankedPerformances->sortByDesc('total_sales')->first();
        $contentLeader = $rankedPerformances->sortByDesc('total_content')->first();

        $teamSummary = [
            'total_users' => $performances->count(),
            'active_users' => $performances->where('sessions_count', '>', 0)->count(),
            'total_hours' => round($performances->sum('total_minutes') / 60, 1),
            'total_minutes' => $performances->sum('total_minutes'),
            'total_sales' => $performances->sum('total_sales'),
            'total_content' => $performances->sum('total_content'),
            'total_content_edit' => $performances->sum('total_content_edit'),
            'total_content_live' => $performances->sum('total_content_live'),
            'total_sessions' => $performances->sum('sessions_count'),
            'avg_sales_per_session' => $performances->sum('sessions_count') > 0
                ? round($performances->sum('total_sales') / $performances->sum('sessions_count'), 1)
                : 0,
            'avg_hours_per_session' => $performances->sum('sessions_count') > 0
                ? round(($performances->sum('total_minutes') / 60) / $performances->sum('sessions_count'), 1)
                : 0,
            'avg_score' => $leaderboard->count() > 0 ? round($leaderboard->avg('overall_score'), 1) : 0,
        ];

        $chartUsers = $leaderboard->take(7);
        $chartData = [
            'labels' => $chartUsers->pluck('name')->toArray(),
            'hours' => $chartUsers->pluck('total_hours')->toArray(),
            'sales' => $chartUsers->pluck('total_sales')->toArray(),
            'content' => $chartUsers->pluck('total_content')->toArray(),
            'scores' => $chartUsers->pluck('overall_score')->toArray(),
        ];

        return compact(
            'leaderboard',
            'topFirst',
            'topSecond',
            'topThird',
            'mvpLeader',
            'durationLeader',
            'salesLeader',
            'contentLeader',
            'teamSummary',
            'chartData',
            'period',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'filterLabel',
            'status',
            'category',
            'search'
        );
    }

    /**
     * Display top performance leaderboard
     */
    public function index(Request $request)
    {
        $data = $this->getPerformanceData($request);
        return view('admin.performance.index', $data);
    }

    /**
     * Display formal print/PDF view of the report
     */
    public function printReport(Request $request)
    {
        $data = $this->getPerformanceData($request);
        return view('admin.performance.print', $data);
    }

    /**
     * Get user attendance details for performance drilldown modal
     */
    public function userDetail(User $user, Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status', 'all');

        $query = Attendance::where('user_id', $user->id);

        if ($startDate && $endDate) {
            $query->whereBetween('attendance_date', [$startDate, $endDate]);
        }

        if ($status === 'validated') {
            $query->where('status', 'validated');
        } elseif ($status === 'pending') {
            $query->where('status', 'pending');
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        $totalMinutes = (int) $attendances->sum('live_duration_minutes');
        $totalSales = (int) $attendances->sum('sales_count');
        $totalContentEdit = (int) $attendances->sum('content_edit_count');
        $totalContentLive = (int) $attendances->sum('content_live_count');
        $sessionsCount = $attendances->count();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '-',
                'task' => $user->task ?? 'Host Live',
            ],
            'summary' => [
                'sessions_count' => $sessionsCount,
                'total_hours' => round($totalMinutes / 60, 1),
                'total_minutes' => $totalMinutes,
                'avg_duration_minutes' => $sessionsCount > 0 ? round($totalMinutes / $sessionsCount) : 0,
                'total_sales' => $totalSales,
                'avg_sales' => $sessionsCount > 0 ? round($totalSales / $sessionsCount, 1) : 0,
                'max_single_sales' => (int) $attendances->max('sales_count'),
                'total_content_edit' => $totalContentEdit,
                'total_content_live' => $totalContentLive,
                'total_content' => $totalContentEdit + $totalContentLive,
            ],
            'attendances' => $attendances->map(function ($a) {
                $hours = floor($a->live_duration_minutes / 60);
                $mins = $a->live_duration_minutes % 60;
                return [
                    'id' => $a->id,
                    'date' => Carbon::parse($a->attendance_date)->translatedFormat('d M Y'),
                    'raw_date' => $a->attendance_date->format('Y-m-d'),
                    'duration_formatted' => sprintf('%d jam %02d mnt', $hours, $mins),
                    'duration_minutes' => $a->live_duration_minutes,
                    'sales_count' => $a->sales_count,
                    'content_edit_count' => $a->content_edit_count,
                    'content_live_count' => $a->content_live_count,
                    'total_content' => $a->content_edit_count + $a->content_live_count,
                    'status' => $a->status,
                    'notes' => $a->notes ?? '-',
                ];
            }),
        ]);
    }

    /**
     * Export performance leaderboard to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $data = $this->getPerformanceData($request);
        $sorted = $data['leaderboard'];
        $filterLabel = $data['filterLabel'];

        $fileName = 'Top-Performa-Host_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($sorted) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Peringkat',
                'Nama Host',
                'Role / Tugas',
                'Total Sesi Live',
                'Total Jam Live',
                'Total Menit Live',
                'Total Penjualan (Pcs)',
                'Rata-rata Penjualan / Sesi',
                'Konten Edit',
                'Konten Live',
                'Total Konten',
                'Skor Durasi (0-100)',
                'Skor Penjualan (0-100)',
                'Skor Konten (0-100)',
                'Skor Keseluruhan (0-100)',
                'Predikat',
            ]);

            foreach ($sorted as $row) {
                fputcsv($handle, [
                    $row['rank'],
                    $row['name'],
                    $row['task'],
                    $row['sessions_count'],
                    $row['total_hours'],
                    $row['total_minutes'],
                    $row['total_sales'],
                    $row['avg_sales'],
                    $row['total_content_edit'],
                    $row['total_content_live'],
                    $row['total_content'],
                    $row['duration_score'],
                    $row['sales_score'],
                    $row['content_score'],
                    $row['overall_score'],
                    $row['tier'],
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
