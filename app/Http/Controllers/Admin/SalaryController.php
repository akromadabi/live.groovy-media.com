<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BonusTier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Display a listing of salaries
     */
    public function index(Request $request)
    {
        $query = Salary::with('user');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

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

        $salaries = $query->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('term')
            ->paginate(20);

        $users = User::where('role', 'user')->orderBy('name')->get();

        // Get available years for filter
        $years = Salary::distinct()->pluck('year')->sortDesc()->toArray();
        if (empty($years)) {
            $years = [Carbon::now()->year];
        }

        return view('admin.salaries.index', compact('salaries', 'users', 'years'));
    }

    /**
     * Show the form for creating/generating salary records
     */
    public function create()
    {
        $users = User::where('role', 'user')->where('is_active', true)->orderBy('name')->get();

        // Default to current period
        $now = Carbon::now();
        $currentTerm = $now->day <= 15 ? 1 : 2;

        return view('admin.salaries.create', compact('users', 'currentTerm'));
    }

    /**
     * Generate salary records for all users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'term' => 'required|integer|in:1,2',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $dates = Salary::getTermDates($validated['year'], $validated['month'], $validated['term']);

        // If no specific users selected, generate for all active users
        if (empty($validated['user_ids'])) {
            $users = User::where('role', 'user')->where('is_active', true)->with('salaryScheme')->get();
        } else {
            $users = User::whereIn('id', $validated['user_ids'])->with('salaryScheme')->get();
        }

        $generated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Check if salary already exists
            $exists = Salary::where('user_id', $user->id)
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->where('term', $validated['term'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            if (!$user->salaryScheme) {
                $skipped++;
                continue;
            }

            // Calculate salary
            $salaryData = $this->calculateSalaryForUser($user, $dates['start'], $dates['end']);

            Salary::create([
                'user_id' => $user->id,
                'year' => $validated['year'],
                'month' => $validated['month'],
                'term' => $validated['term'],
                'period_start' => $dates['start'],
                'period_end' => $dates['end'],
                ...$salaryData,
                'status' => 'draft',
            ]);

            $generated++;
        }

        return redirect()->route('admin.salaries.index')
            ->with('success', "Rekap gaji berhasil dibuat untuk {$generated} user. {$skipped} dilewati.");
    }

    /**
     * Calculate salary for a specific user
     */
    private function calculateSalaryForUser(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $scheme = $user->salaryScheme;

        // Get validated attendances in period
        $attendances = Attendance::where('user_id', $user->id)
            ->where('status', 'validated')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $totalMinutes = $attendances->sum('live_duration_minutes');
        $totalHours = $totalMinutes / 60;
        $totalContentEdit = $attendances->sum('content_edit_count');
        $totalContentLive = $attendances->sum('content_live_count');
        $totalSales = $attendances->sum('sales_count');

        // Calculate base salary components
        $liveSalary = $totalHours * $scheme->hourly_rate;
        $contentEditBonus = $totalContentEdit * $scheme->content_edit_rate;
        $contentLiveBonus = $totalContentLive * $scheme->content_live_rate;

        // Check if monthly target is met (untuk bonus penjualan)
        // Target dihitung per bulan, jadi kita perlu total jam bulan ini
        $monthStart = Carbon::createFromDate($startDate->year, $startDate->month, 1);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $monthlyMinutes = Attendance::where('user_id', $user->id)
            ->where('status', 'validated')
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->sum('live_duration_minutes');

        $monthlyHours = $monthlyMinutes / 60;
        $targetMet = $monthlyHours >= $scheme->monthly_target_hours;

        // Get sales bonus from tier (only if target met)
        $salesBonus = 0;
        if ($targetMet && $totalSales > 0) {
            $salesBonus = BonusTier::getBonusForSales($totalSales);
        }

        // Calculate total
        $totalSalary = $liveSalary + $contentEditBonus + $contentLiveBonus + $salesBonus;

        return [
            'total_hours' => round($totalHours, 2),
            'live_salary' => round($liveSalary, 0),
            'content_edit_bonus' => round($contentEditBonus, 0),
            'content_live_bonus' => round($contentLiveBonus, 0),
            'total_sales' => $totalSales,
            'sales_bonus' => round($salesBonus, 0),
            'target_met' => $targetMet,
            'deduction' => 0,
            'deduction_notes' => null,
            'total_salary' => round($totalSalary, 0),
        ];
    }

    /**
     * Show the salary record details
     */
    public function show(Salary $salary)
    {
        $salary->load('user.salaryScheme');

        // Get attendances for this period
        $attendances = Attendance::where('user_id', $salary->user_id)
            ->where('status', 'validated')
            ->whereBetween('attendance_date', [$salary->period_start, $salary->period_end])
            ->orderBy('attendance_date')
            ->get();

        return view('admin.salaries.show', compact('salary', 'attendances'));
    }

    /**
     * Show the form for editing the salary record
     */
    public function edit(Salary $salary)
    {
        return view('admin.salaries.edit', compact('salary'));
    }

    /**
     * Update the specified salary record
     */
    public function update(Request $request, Salary $salary)
    {
        $validated = $request->validate([
            'deduction' => 'required|numeric|min:0',
            'deduction_notes' => 'nullable|string|max:1000',
            'status' => 'required|in:draft,finalized,paid',
        ]);

        // Recalculate total with deduction
        $totalSalary = (float) $salary->live_salary
            + (float) $salary->content_edit_bonus
            + (float) $salary->content_live_bonus
            + (float) $salary->sales_bonus
            - $validated['deduction'];

        $updateData = [
            'deduction' => $validated['deduction'],
            'deduction_notes' => $validated['deduction_notes'],
            'total_salary' => max(0, $totalSalary),
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'paid' && !$salary->paid_at) {
            $updateData['paid_at'] = now();
        }

        $salary->update($updateData);

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Rekap gaji berhasil diperbarui.');
    }

    /**
     * Remove the specified salary record
     */
    public function destroy(Salary $salary)
    {
        if ($salary->status === 'paid') {
            return back()->with('error', 'Tidak dapat menghapus rekap gaji yang sudah dibayar.');
        }

        $salary->delete();

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Rekap gaji berhasil dihapus.');
    }

    /**
     * Recalculate salary for a record
     */
    public function recalculate(Salary $salary)
    {
        if ($salary->status === 'paid') {
            return back()->with('error', 'Tidak dapat menghitung ulang rekap gaji yang sudah dibayar.');
        }

        $user = $salary->user->load('salaryScheme');

        if (!$user->salaryScheme) {
            return back()->with('error', 'User tidak memiliki skema gaji.');
        }

        $salaryData = $this->calculateSalaryForUser(
            $user,
            $salary->period_start,
            $salary->period_end
        );

        // Keep existing deduction
        $salaryData['deduction'] = $salary->deduction;
        $salaryData['deduction_notes'] = $salary->deduction_notes;
        $salaryData['total_salary'] = $salaryData['total_salary'] - $salary->deduction;

        $salary->update($salaryData);

        return back()->with('success', 'Rekap gaji berhasil dihitung ulang.');
    }
}
