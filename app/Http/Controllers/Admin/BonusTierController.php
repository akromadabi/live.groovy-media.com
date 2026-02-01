<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class BonusTierController extends Controller
{
    /**
     * Display bonus scheme settings
     */
    public function index()
    {
        $settings = [
            'daily_live_hours' => Setting::getValue('daily_live_hours', 3),
            'monthly_leave_days' => Setting::getValue('monthly_leave_days', 4),
            'bonus_pcs_threshold' => Setting::getValue('bonus_pcs_threshold', 20),
            'bonus_amount' => Setting::getValue('bonus_amount', 10000),
        ];

        return view('admin.bonus-tiers.index', compact('settings'));
    }

    /**
     * Update bonus scheme settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'daily_live_hours' => 'required|numeric|min:0.5|max:24',
            'monthly_leave_days' => 'required|integer|min:0|max:15',
            'bonus_pcs_threshold' => 'required|integer|min:1',
            'bonus_amount' => 'required|numeric|min:0',
        ]);

        Setting::setValue('daily_live_hours', $validated['daily_live_hours']);
        Setting::setValue('monthly_leave_days', $validated['monthly_leave_days']);
        Setting::setValue('bonus_pcs_threshold', $validated['bonus_pcs_threshold']);
        Setting::setValue('bonus_amount', $validated['bonus_amount']);

        return back()->with('success', 'Skema bonus berhasil diperbarui.');
    }

    /**
     * Calculate monthly target hours for a given month/year
     */
    public static function calculateMonthlyTarget($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $dailyHours = Setting::getValue('daily_live_hours', 3);
        $leaveDays = Setting::getValue('monthly_leave_days', 4);
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $workDays = $daysInMonth - $leaveDays;
        return $workDays * $dailyHours;
    }
}
