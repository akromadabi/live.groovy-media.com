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

        // Calculate totals for the filtered result
        $baseQuery = $user->attendances()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status));

        $totalMinutes = (clone $baseQuery)->sum('live_duration_minutes');
        $totalContentEdit = (clone $baseQuery)->sum('content_edit_count');
        $totalContentLive = (clone $baseQuery)->sum('content_live_count');
        $totalSales = (clone $baseQuery)->sum('sales_count');

        // Calculate total salary (tanpa penjualan, penjualan masuk bonus)
        $totalSalary = 0;
        if ($scheme) {
            $totalHours = $totalMinutes / 60;
            $totalSalary = ($totalHours * $scheme->hourly_rate)
                + ($totalContentEdit * $scheme->content_edit_rate)
                + ($totalContentLive * $scheme->content_live_rate);
        }

        // Calculate T1 and T2 for selected month (ALL attendance, not just validated)
        $t1Salary = 0;
        $t2Salary = 0;
        $t1Hours = 0;
        $t2Hours = 0;
        if ($scheme) {
            // Termin 1: 1-15 (use toDateString for DATE column)
            $t1Start = Carbon::createFromDate($year, $month, 1)->toDateString();
            $t1End = Carbon::createFromDate($year, $month, 15)->toDateString();
            $t1Minutes = $user->attendances()
                ->whereBetween('attendance_date', [$t1Start, $t1End])
                ->sum('live_duration_minutes');
            $t1ContentEdit = $user->attendances()
                ->whereBetween('attendance_date', [$t1Start, $t1End])
                ->sum('content_edit_count');
            $t1ContentLive = $user->attendances()
                ->whereBetween('attendance_date', [$t1Start, $t1End])
                ->sum('content_live_count');
            $t1Hours = round($t1Minutes / 60, 1);
            $t1Salary = ($t1Minutes / 60 * $scheme->hourly_rate)
                + ($t1ContentEdit * $scheme->content_edit_rate)
                + ($t1ContentLive * $scheme->content_live_rate);

            // Termin 2: 16-end
            $t2Start = Carbon::createFromDate($year, $month, 16)->toDateString();
            $t2End = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
            $t2Minutes = $user->attendances()
                ->whereBetween('attendance_date', [$t2Start, $t2End])
                ->sum('live_duration_minutes');
            $t2ContentEdit = $user->attendances()
                ->whereBetween('attendance_date', [$t2Start, $t2End])
                ->sum('content_edit_count');
            $t2ContentLive = $user->attendances()
                ->whereBetween('attendance_date', [$t2Start, $t2End])
                ->sum('content_live_count');
            $t2Hours = round($t2Minutes / 60, 1);
            $t2Salary = ($t2Minutes / 60 * $scheme->hourly_rate)
                + ($t2ContentEdit * $scheme->content_edit_rate)
                + ($t2ContentLive * $scheme->content_live_rate);
        }

        // Calculate monthly totals for bonus check (ALL attendance in full month)
        $monthStart = Carbon::createFromDate($year, $month, 1)->toDateString();
        $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;

        $monthlyQuery = $user->attendances()
            ->whereBetween('attendance_date', [$monthStart, $monthEnd]);
        $monthlyMinutes = (clone $monthlyQuery)->sum('live_duration_minutes');
        $monthlyHours = round($monthlyMinutes / 60, 1);
        $monthlySales = (clone $monthlyQuery)->sum('sales_count');

        // Target calculation: (days in month - 4 days off) × 3 hours/day
        $daysOff = 4;
        $dailyTargetHours = 3;
        $targetHours = ($daysInMonth - $daysOff) * $dailyTargetHours;

        $targetMet = $monthlyHours >= $targetHours;
        // Always calculate bonus based on sales (show potential bonus even if target not met)
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

        return view('user.attendances.index', compact('attendances', 'totals', 'bonusInfo'));
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
