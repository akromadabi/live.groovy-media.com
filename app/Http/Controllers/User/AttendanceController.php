<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        $attendances = $query->orderByDesc('attendance_date')
            ->orderBy('session_name')
            ->paginate(31);

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

        // Gamification: Next Tier Calculation
        $threshold = $scheme?->bonus_pcs_threshold ?: Setting::getValue('bonus_pcs_threshold', 20);
        $threshold = max(1, (int) $threshold);
        $bonusAmt = $scheme && $scheme->bonus_amount !== null ? (float) $scheme->bonus_amount : (float) Setting::getValue('bonus_amount', 10000);

        $currentMultiplier = floor($monthlySales / $threshold);
        $nextTierTarget = ($currentMultiplier + 1) * $threshold;
        $remainingToNext = max(0, $nextTierTarget - $monthlySales);
        $tierProgressPercent = min(100, round((($monthlySales % $threshold) / $threshold) * 100));
        $nextBonusTotal = ($currentMultiplier + 1) * $bonusAmt;

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
            'threshold' => $threshold,
            'bonus_amount_per_tier' => $bonusAmt,
            'current_multiplier' => $currentMultiplier,
            'next_tier_target' => $nextTierTarget,
            'remaining_to_next' => $remainingToNext,
            'tier_progress_percent' => $tierProgressPercent,
            'next_bonus_total' => $nextBonusTotal,
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
        $settings = [
            'proof_enabled' => Setting::getValue('attendance_proof_enabled', true),
            'proof_required' => Setting::getValue('attendance_proof_required', false),
            'vt_link_enabled' => Setting::getValue('attendance_vt_link_enabled', true),
            'vt_link_required' => Setting::getValue('attendance_vt_link_required', false),
            'deadline_enabled' => Setting::getValue('attendance_deadline_enabled', false),
            'deadline_days' => (int) Setting::getValue('attendance_deadline_days', 1),
            'deadline_time' => Setting::getValue('attendance_deadline_time', '23:59'),
        ];

        $minDate = null;
        if ($settings['deadline_enabled']) {
            $minDate = Carbon::today()->subDays($settings['deadline_days'])->toDateString();
        }

        return view('user.attendances.create', compact('settings', 'minDate'));
    }

    /**
     * Store a newly created attendance
     */
    public function store(Request $request)
    {
        $settings = [
            'proof_enabled' => Setting::getValue('attendance_proof_enabled', true),
            'proof_required' => Setting::getValue('attendance_proof_required', false),
            'vt_link_enabled' => Setting::getValue('attendance_vt_link_enabled', true),
            'vt_link_required' => Setting::getValue('attendance_vt_link_required', false),
            'deadline_enabled' => Setting::getValue('attendance_deadline_enabled', false),
            'deadline_days' => (int) Setting::getValue('attendance_deadline_days', 1),
            'deadline_time' => Setting::getValue('attendance_deadline_time', '23:59'),
        ];

        // Check deadline locking
        if ($settings['deadline_enabled']) {
            $minDate = Carbon::today()->subDays($settings['deadline_days'])->toDateString();
            if ($request->input('attendance_date') < $minDate) {
                return back()->withInput()->with('error', "Batas pengisian absensi telah terlewat (Maksimal H+{$settings['deadline_days']}). Silakan hubungi admin untuk izin pengisian susulan.");
            }
        }

        $rules = [
            'attendance_date' => 'required|date|before_or_equal:today',
            'session_name' => 'required|string|max:50',
            'live_duration_hours' => 'required|numeric|min:0.5|max:24',
            'content_edit_count' => 'nullable|integer|min:0|max:10',
            'content_live_count' => 'nullable|integer|min:0|max:10',
            'sales_count' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ];

        if ($settings['proof_enabled']) {
            $rules['proof_image'] = ($settings['proof_required'] ? 'required' : 'nullable') . '|image|mimes:jpeg,jpg,png,webp|max:10240';
        }

        if ($settings['vt_link_enabled']) {
            $rules['vt_link'] = ($settings['vt_link_required'] ? 'required' : 'nullable') . '|url|max:500';
        }

        $validated = $request->validate($rules);

        // Convert decimal hours to minutes (e.g., 1.5 hours = 90 minutes)
        $totalMinutes = (int) ($validated['live_duration_hours'] * 60);

        // Check if attendance already exists for this date and session
        $existing = Auth::user()->attendances()
            ->where('attendance_date', $validated['attendance_date'])
            ->where('session_name', $validated['session_name'])
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', "Absensi untuk {$validated['session_name']} pada tanggal ini sudah ada. Silakan pilih sesi lain (misal: Sesi 2) atau edit absensi yang sudah ada.");
        }

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('attendance_proofs', 'public');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'attendance_date' => $validated['attendance_date'],
            'session_name' => $validated['session_name'],
            'live_duration_minutes' => $totalMinutes,
            'content_edit_count' => $validated['content_edit_count'] ?? 0,
            'content_live_count' => $validated['content_live_count'] ?? 0,
            'sales_count' => $validated['sales_count'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'proof_image' => $proofPath,
            'vt_link' => $validated['vt_link'] ?? null,
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

        $settings = [
            'proof_enabled' => Setting::getValue('attendance_proof_enabled', true),
            'proof_required' => Setting::getValue('attendance_proof_required', false),
            'vt_link_enabled' => Setting::getValue('attendance_vt_link_enabled', true),
            'vt_link_required' => Setting::getValue('attendance_vt_link_required', false),
            'deadline_enabled' => Setting::getValue('attendance_deadline_enabled', false),
            'deadline_days' => (int) Setting::getValue('attendance_deadline_days', 1),
        ];

        $minDate = null;
        if ($settings['deadline_enabled']) {
            $minDate = Carbon::today()->subDays($settings['deadline_days'])->toDateString();
        }

        return view('user.attendances.edit', compact('attendance', 'settings', 'minDate'));
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

        $settings = [
            'proof_enabled' => Setting::getValue('attendance_proof_enabled', true),
            'proof_required' => Setting::getValue('attendance_proof_required', false),
            'vt_link_enabled' => Setting::getValue('attendance_vt_link_enabled', true),
            'vt_link_required' => Setting::getValue('attendance_vt_link_required', false),
            'deadline_enabled' => Setting::getValue('attendance_deadline_enabled', false),
            'deadline_days' => (int) Setting::getValue('attendance_deadline_days', 1),
        ];

        $rules = [
            'attendance_date' => 'required|date|before_or_equal:today',
            'session_name' => 'required|string|max:50',
            'live_duration_hours' => 'required|numeric|min:0.5|max:24',
            'content_edit_count' => 'nullable|integer|min:0|max:10',
            'content_live_count' => 'nullable|integer|min:0|max:10',
            'sales_count' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ];

        if ($settings['proof_enabled']) {
            $isProofRequired = $settings['proof_required'] && empty($attendance->proof_image);
            $rules['proof_image'] = ($isProofRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,jpg,png,webp|max:10240';
        }

        if ($settings['vt_link_enabled']) {
            $rules['vt_link'] = ($settings['vt_link_required'] ? 'required' : 'nullable') . '|url|max:500';
        }

        $validated = $request->validate($rules);

        // Check if session conflicts with another entry
        $existing = Auth::user()->attendances()
            ->where('attendance_date', $validated['attendance_date'])
            ->where('session_name', $validated['session_name'])
            ->where('id', '!=', $attendance->id)
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', "Absensi untuk {$validated['session_name']} pada tanggal ini sudah ada.");
        }

        // Convert decimal hours to minutes
        $totalMinutes = (int) ($validated['live_duration_hours'] * 60);

        $updateData = [
            'attendance_date' => $validated['attendance_date'],
            'session_name' => $validated['session_name'],
            'live_duration_minutes' => $totalMinutes,
            'content_edit_count' => $validated['content_edit_count'] ?? 0,
            'content_live_count' => $validated['content_live_count'] ?? 0,
            'sales_count' => $validated['sales_count'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($settings['vt_link_enabled']) {
            $updateData['vt_link'] = $validated['vt_link'] ?? null;
        }

        if ($request->hasFile('proof_image')) {
            if ($attendance->proof_image && Storage::disk('public')->exists($attendance->proof_image)) {
                Storage::disk('public')->delete($attendance->proof_image);
            }
            $updateData['proof_image'] = $request->file('proof_image')->store('attendance_proofs', 'public');
        }

        $attendance->update($updateData);

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

        if ($attendance->proof_image && Storage::disk('public')->exists($attendance->proof_image)) {
            Storage::disk('public')->delete($attendance->proof_image);
        }

        $attendance->delete();

        return redirect()->route('user.attendances.index')
            ->with('success', 'Absensi berhasil dihapus.');
    }
}
