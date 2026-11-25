<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryScheme;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SalarySchemeController extends Controller
{
    /**
     * Display a listing of salary schemes
     */
    public function index()
    {
        $users = User::where('role', 'user')
            ->with('salaryScheme')
            ->orderBy('name')
            ->get();

        $activeMarketplaces = Setting::getActiveMarketplaces();
        $marketplaceRateMode = Setting::getValue('marketplace_rate_mode', 'same');

        $globalDefaults = [
            'hourly_rate' => (int) Setting::getValue('default_hourly_rate', 20000),
            'content_edit_rate' => (int) Setting::getValue('content_edit_rate', 10000),
            'content_live_rate' => (int) Setting::getValue('content_live_rate', 5000),
            'monthly_target_hours' => (float) Setting::getValue('monthly_target_hours', 80),
        ];

        return view('admin.salary-schemes.index', compact(
            'users',
            'activeMarketplaces',
            'marketplaceRateMode',
            'globalDefaults'
        ));
    }

    /**
     * Show the form for editing the salary scheme
     */
    public function edit(User $user)
    {
        $scheme = $user->salaryScheme ?? new SalaryScheme();

        // Active marketplaces & rate mode from global settings
        $activeMarketplaces = Setting::getActiveMarketplaces();
        $marketplaceRateMode = Setting::getValue('marketplace_rate_mode', 'same');

        // Global defaults for placeholder hints and fallbacks
        $globalDefaults = [
            'hourly_rate' => (int) Setting::getValue('default_hourly_rate', 20000),
            'content_edit_rate' => (int) Setting::getValue('content_edit_rate', 10000),
            'content_live_rate' => (int) Setting::getValue('content_live_rate', 5000),
            'monthly_target_hours' => (float) Setting::getValue('monthly_target_hours', 80),
            'daily_live_hours' => Setting::getValue('daily_live_hours', 3),
            'monthly_leave_days' => Setting::getValue('monthly_leave_days', 4),
            'bonus_pcs_threshold' => Setting::getValue('bonus_pcs_threshold', 20),
            'bonus_amount' => Setting::getValue('bonus_amount', 10000),
        ];

        return view('admin.salary-schemes.edit', compact(
            'user',
            'scheme',
            'globalDefaults',
            'activeMarketplaces',
            'marketplaceRateMode'
        ));
    }

    /**
     * Update the specified salary scheme
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'hourly_rate' => 'nullable',
            'platform_rates' => 'nullable|array',
            'content_edit_rate' => 'nullable',
            'content_live_rate' => 'nullable',
            'monthly_target_hours' => 'nullable|numeric|min:0|max:744',
            'daily_live_hours' => 'nullable|numeric|min:0.5|max:24',
            'monthly_leave_days' => 'nullable|integer|min:0|max:15',
            'bonus_pcs_threshold' => 'nullable|integer|min:1',
            'bonus_amount' => 'nullable|numeric|min:0',
        ]);

        $rateMode = $request->input('rate_mode', Setting::getValue('marketplace_rate_mode', 'same'));
        $rawRates = $request->input('platform_rates', []);

        $cleanInt = function ($val) {
            if ($val === null || $val === '') {
                return null;
            }
            $clean = preg_replace('/[^\d]/', '', (string) $val);
            return $clean !== '' ? (int) $clean : null;
        };

        $cleanFloat = function ($val) {
            if ($val === null || $val === '') {
                return null;
            }
            return is_numeric($val) ? (float) $val : null;
        };

        $activeMps = Setting::getActiveMarketplaces();

        if ($rateMode === 'same') {
            // Uniform mode: top-level fields only, null if empty
            $validated['hourly_rate'] = $cleanInt($request->input('hourly_rate'));
            $validated['content_edit_rate'] = $cleanInt($request->input('content_edit_rate'));
            $validated['content_live_rate'] = $cleanInt($request->input('content_live_rate'));
            $validated['monthly_target_hours'] = $cleanFloat($request->input('monthly_target_hours'));
            $validated['platform_rates'] = null;
        } else {
            // Custom mode per marketplace
            $cleanRates = [];
            $sumCustomTarget = 0;
            $hasAnyTarget = false;

            if (is_array($rawRates)) {
                foreach ($activeMps as $plat => $mp) {
                    $r = $rawRates[$plat] ?? [];
                    if (is_array($r)) {
                        $h = $cleanInt($r['hourly_rate'] ?? null);
                        $e = $cleanInt($r['content_edit_rate'] ?? null);
                        $l = $cleanInt($r['content_live_rate'] ?? null);
                        $t = $cleanFloat($r['target_hours'] ?? null);

                        if ($t !== null) {
                            $sumCustomTarget += $t;
                            $hasAnyTarget = true;
                        }

                        $cleanRates[$plat] = [
                            'hourly_rate' => $h,
                            'content_edit_rate' => $e,
                            'content_live_rate' => $l,
                            'target_hours' => $t,
                        ];
                    }
                }
            }

            // Sync top-level rates with primary platform (TikTok) or first active
            $primaryPlat = isset($cleanRates['tiktok']) ? 'tiktok' : (array_key_first($cleanRates) ?? null);
            if ($primaryPlat && isset($cleanRates[$primaryPlat])) {
                $validated['hourly_rate'] = $cleanRates[$primaryPlat]['hourly_rate'];
                $validated['content_edit_rate'] = $cleanRates[$primaryPlat]['content_edit_rate'];
                $validated['content_live_rate'] = $cleanRates[$primaryPlat]['content_live_rate'];
            } else {
                $validated['hourly_rate'] = null;
                $validated['content_edit_rate'] = null;
                $validated['content_live_rate'] = null;
            }

            $validated['monthly_target_hours'] = $hasAnyTarget ? $sumCustomTarget : null;
            $validated['platform_rates'] = $cleanRates;
        }

        // Convert empty strings to null for nullable bonus fields
        foreach (['daily_live_hours', 'monthly_leave_days', 'bonus_pcs_threshold', 'bonus_amount'] as $field) {
            if (!isset($validated[$field]) || $validated[$field] === '' || $validated[$field] === null) {
                $validated[$field] = null;
            }
        }

        SalaryScheme::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('admin.salary-schemes.index')
            ->with('success', 'Skema gaji berhasil diperbarui.');
    }

    /**
     * Apply salary scheme to multiple users
     */
    public function applyToAll(Request $request)
    {
        $validated = $request->validate([
            'hourly_rate' => 'nullable|numeric|min:0',
            'content_edit_rate' => 'nullable|numeric|min:0',
            'content_live_rate' => 'nullable|numeric|min:0',
            'sales_bonus_percentage' => 'nullable|numeric|min:0|max:100',
            'sales_bonus_nominal' => 'nullable|numeric|min:0',
        ]);

        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            SalaryScheme::updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );
        }

        return redirect()->route('admin.salary-schemes.index')
            ->with('success', 'Skema gaji berhasil diterapkan ke semua user.');
    }
}
