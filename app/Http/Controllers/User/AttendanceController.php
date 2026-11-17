<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of user's attendances
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->attendances();

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

        $attendances = $query->orderByDesc('attendance_date')->paginate(31);

        // Get salary scheme for calculation
        $scheme = $user->salaryScheme;

        // Calculate totals for the filtered result using 1 query
        $filterTotals = $user->attendances()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->selectRaw('
                COALESCE(SUM(live_duration_minutes), 0) as total_minutes,
                COALESCE(SUM(content_edit_count), 0) as total_content_edit,
                COALESCE(SUM(content_live_count), 0) as total_content_live,
                COALESCE(SUM(sales_count), 0) as total_sales
            ')
            ->first();

        $totalMinutes = $filterTotals->total_minutes ?? 0;
        $totalContentEdit = $filterTotals->total_content_edit ?? 0;
        $totalContentLive = $filterTotals->total_content_live ?? 0;
        $totalSales = $filterTotals->total_sales ?? 0;

        // Calculate total salary (tanpa penjualan, penjualan masuk bonus)
        $totalSalary = 0;
        if ($scheme) {
            $totalHours = $totalMinutes / 60;
            $totalSalary = ($totalHours * $scheme->hourly_rate)
                + ($totalContentEdit * $scheme->content_edit_rate)
                + ($totalContentLive * $scheme->content_live_rate);
        }

        // Calculate T1, T2, and full month totals in 1 single aggregate query
        $monthStart = Carbon::createFromDate($year, $month, 1)->toDateString();
        $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;

        $t1Start = $monthStart;
        $t1End = Carbon::createFromDate($year, $month, 15)->toDateString();
        $t2Start = Carbon::createFromDate($year, $month, 16)->toDateString();
        $t2End = $monthEnd;

        $monthAggregates = $user->attendances()
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->selectRaw("
                COALESCE(SUM(CASE WHEN attendance_date BETWEEN '{$t1Start}' AND '{$t1End}' THEN live_duration_minutes ELSE 0 END), 0) as t1_minutes,
                COALESCE(SUM(CASE WHEN attendance_date BETWEEN '{$t1Start}' AND '{$t1End}' THEN content_edit_count ELSE 0 END), 0) as t1_content_edit,
                COALESCE(SUM(CASE WHEN attendance_date BETWEEN '{$t1Start}' AND '{$t1End}' THEN content_live_count ELSE 0 END), 0) as t1_content_live,
                COALESCE(SUM(CASE WHEN attendance_date BETWEEN '{$t2Start}' AND '{$t2End}' THEN live_duration_minutes ELSE 0 END), 0) as t2_minutes,
                COALESCE(SUM(CASE WHEN attendance_date BETWEEN '{$t2Start}' AND '{$t2End}' THEN content_edit_count ELSE 0 END), 0) as t2_content_edit,
                COALESCE(SUM(CASE WHEN attendance_date BETWEEN '{$t2Start}' AND '{$t2End}' THEN content_live_count ELSE 0 END), 0) as t2_content_live,
                COALESCE(SUM(live_duration_minutes), 0) as monthly_minutes,
                COALESCE(SUM(sales_count), 0) as monthly_sales
            ")
            ->first();

        $t1Minutes = $monthAggregates->t1_minutes ?? 0;
        $t1ContentEdit = $monthAggregates->t1_content_edit ?? 0;
        $t1ContentLive = $monthAggregates->t1_content_live ?? 0;
        $t1Hours = round($t1Minutes / 60, 1);
        $t1Salary = $scheme ? (($t1Minutes / 60 * $scheme->hourly_rate) + ($t1ContentEdit * $scheme->content_edit_rate) + ($t1ContentLive * $scheme->content_live_rate)) : 0;

        $t2Minutes = $monthAggregates->t2_minutes ?? 0;
        $t2ContentEdit = $monthAggregates->t2_content_edit ?? 0;
        $t2ContentLive = $monthAggregates->t2_content_live ?? 0;
        $t2Hours = round($t2Minutes / 60, 1);
        $t2Salary = $scheme ? (($t2Minutes / 60 * $scheme->hourly_rate) + ($t2ContentEdit * $scheme->content_edit_rate) + ($t2ContentLive * $scheme->content_live_rate)) : 0;

        $monthlyMinutes = $monthAggregates->monthly_minutes ?? 0;
        $monthlyHours = round($monthlyMinutes / 60, 1);
        $monthlySales = $monthAggregates->monthly_sales ?? 0;

        // Target calculation: (days in month - 4 days off) * 3 hours/day
        $daysOff = 4;
        $dailyTargetHours = 3;
        $targetHours = ($daysInMonth - $daysOff) * $dailyTargetHours;

        $targetMet = $monthlyHours >= $targetHours;
        $salesBonus = $monthlySales > 0 ? \App\Models\BonusTier::getBonusForSales($monthlySales) : 0;

        $totals = [
            'total_hours' => round($totalMinutes / 60, 1),
            'total_content_edit' => $totalContentEdit,
            'total_content_live' => $totalContentLive,
            'total_sales' => $totalSales,
            'total_salary' => $totalSalary,
            't1_salary' => $t1Salary,
            't2_salary' => $t2Salary,
            't1_hours' => $t1Hours,
            't2_hours' => $t2Hours,
        ];

        $bonusInfo = [
            'monthly_hours' => $monthlyHours,
            'target_hours' => $targetHours,
            'target_met' => $targetMet,
            'monthly_sales' => $monthlySales,
            'sales_bonus' => $salesBonus,
        ];

        // Pre-aggregate TikTok and Attendance daily totals for the paginated items to eliminate N+1 queries in view
        $dates = $attendances->pluck('attendance_date')->map(fn($d) => Carbon::parse($d)->toDateString())->unique()->values()->all();
        $tiktokTotalsByDate = [];
        $attendanceTotalsByDate = [];

        if (!empty($dates)) {
            $tiktokTotalsByDate = \App\Models\TiktokReportDetail::whereIn('live_date', $dates)
                ->groupBy('live_date')
                ->selectRaw('live_date, SUM(duration_minutes) as total')
                ->pluck('total', 'live_date')
                ->toArray();

            $attendanceTotalsByDate = Attendance::whereIn('attendance_date', $dates)
                ->groupBy('attendance_date')
                ->selectRaw('attendance_date, SUM(live_duration_minutes) as total')
                ->pluck('total', 'attendance_date')
                ->toArray();
        }

        return view('user.attendances.index', compact('attendances', 'totals', 'bonusInfo', 'tiktokTotalsByDate', 'attendanceTotalsByDate'));
    }

    /**
     * Show the form for creating a new attendance
     */
    public function create()
    {
        return view('user.attendances.create');
    }

    /**
     * Store a newly created attendance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_date' => 'required|date|before_or_equal:today',
            'live_duration_hours' => 'required|numeric|min:0.5|max:24',
            'content_edit_count' => 'nullable|integer|min:0|max:10',
            'content_live_count' => 'nullable|integer|min:0|max:10',
            'sales_count' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Convert decimal hours to minutes (e.g., 1.5 hours = 90 minutes)
        $totalMinutes = (int) ($validated['live_duration_hours'] * 60);

        // Check if attendance already exists for this date
        $existing = Auth::user()->attendances()
            ->where('attendance_date', $validated['attendance_date'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Absensi untuk tanggal ini sudah ada. Silakan edit yang sudah ada.');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'attendance_date' => $validated['attendance_date'],
            'live_duration_minutes' => $totalMinutes,
            'content_edit_count' => $validated['content_edit_count'],
            'content_live_count' => $validated['content_live_count'],
            'sales_count' => $validated['sales_count'],
            'status' => 'pending',
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('user.attendances.index')
            ->with('success', 'Absensi berhasil disimpan. Menunggu validasi admin.');
    }

    /**
     * Show the form for editing the attendance
     */
    public function edit(Attendance $attendance)
    {
        // Ensure user can only edit their own attendance
        if ((int) $attendance->user_id !== (int) Auth::id()) {
            abort(403);
        }

        // Only allow editing pending attendances
        if ($attendance->status !== 'pending') {
            return back()->with('error', 'Hanya absensi dengan status pending yang dapat diedit.');
        }

        return view('user.attendances.edit', compact('attendance'));
    }

    /**
     * Update the specified attendance
     */
    public function update(Request $request, Attendance $attendance)
    {
        // Ensure user can only edit their own attendance
        if ((int) $attendance->user_id !== (int) Auth::id()) {
            abort(403);
        }

        // Only allow editing pending attendances
        if ($attendance->status !== 'pending') {
            return back()->with('error', 'Hanya absensi dengan status pending yang dapat diedit.');
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date|before_or_equal:today',
            'live_duration_hours' => 'required|numeric|min:0.5|max:24',
            'content_edit_count' => 'nullable|integer|min:0|max:10',
            'content_live_count' => 'nullable|integer|min:0|max:10',
            'sales_count' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Convert decimal hours to minutes (e.g., 1.5 hours = 90 minutes)
        $totalMinutes = (int) ($validated['live_duration_hours'] * 60);

        $attendance->update([
            'attendance_date' => $validated['attendance_date'],
            'live_duration_minutes' => $totalMinutes,
            'content_edit_count' => $validated['content_edit_count'],
            'content_live_count' => $validated['content_live_count'],
            'sales_count' => $validated['sales_count'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('user.attendances.index')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    /**
     * Delete the specified attendance
     */
    public function destroy(Attendance $attendance)
    {
        // Ensure user can only delete their own attendance
        if ((int) $attendance->user_id !== (int) Auth::id()) {
            abort(403);
        }

        // Only allow deleting pending attendances
        if ($attendance->status !== 'pending') {
            return back()->with('error', 'Hanya absensi dengan status pending yang dapat dihapus.');
        }

        $attendance->delete();

        return redirect()->route('user.attendances.index')
            ->with('success', 'Absensi berhasil dihapus.');
    }
}
