<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hourly_rate',
        'platform_rates',
        'content_edit_rate',
        'content_live_rate',
        'monthly_target_hours',
        'sales_bonus_percentage',
        'sales_bonus_nominal',
        'daily_live_hours',
        'monthly_leave_days',
        'bonus_pcs_threshold',
        'bonus_amount',
    ];

    protected $attributes = [
        'sales_bonus_percentage' => 0,
        'sales_bonus_nominal' => 0,
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'integer',
            'platform_rates' => 'array',
            'content_edit_rate' => 'integer',
            'content_live_rate' => 'integer',
            'monthly_target_hours' => 'decimal:1',
            'sales_bonus_percentage' => 'decimal:2',
            'sales_bonus_nominal' => 'integer',
            'daily_live_hours' => 'decimal:1',
            'monthly_leave_days' => 'integer',
            'bonus_pcs_threshold' => 'integer',
            'bonus_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns this salary scheme
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get hourly rate for a specific platform/marketplace
     * Follows custom value if set, otherwise falls back to global setting.
     */
    public function getHourlyRateForPlatform(?string $platform = null): int
    {
        $platform = $platform ?: 'tiktok';

        // 1. Check if user has a custom rate in platform_rates
        if (is_array($this->platform_rates) && isset($this->platform_rates[$platform])) {
            $item = $this->platform_rates[$platform];
            if (is_array($item) && isset($item['hourly_rate']) && $item['hourly_rate'] !== null && $item['hourly_rate'] !== '') {
                return (int) $item['hourly_rate'];
            }
            if (is_numeric($item)) {
                return (int) $item;
            }
        }

        // 2. If platform is tiktok and user has top-level hourly_rate set
        if ($this->hourly_rate !== null && $this->hourly_rate !== '') {
            return (int) $this->hourly_rate;
        }

        // 3. Fallback based on global marketplace rate mode
        $mode = Setting::getValue('marketplace_rate_mode', 'same');
        if ($mode === 'different') {
            $platformInfo = Setting::getPlatformInfo($platform);
            if (isset($platformInfo['default_hourly_rate']) && $platformInfo['default_hourly_rate'] !== null) {
                return (int) $platformInfo['default_hourly_rate'];
            }
        }

        // 4. Fallback to global uniform hourly rate setting
        $globalRate = Setting::getValue('default_hourly_rate');
        if ($globalRate !== null && $globalRate !== '') {
            return (int) $globalRate;
        }

        $platformInfo = Setting::getPlatformInfo($platform);
        return (int) ($platformInfo['default_hourly_rate'] ?? 20000);
    }

    /**
     * Get content edit rate for a specific platform/marketplace
     * Follows custom value if set, otherwise falls back to global setting.
     */
    public function getContentEditRateForPlatform(?string $platform = null): int
    {
        $platform = $platform ?: 'tiktok';

        // 1. Check custom rate in platform_rates
        if (is_array($this->platform_rates) && isset($this->platform_rates[$platform])) {
            $item = $this->platform_rates[$platform];
            if (is_array($item) && isset($item['content_edit_rate']) && $item['content_edit_rate'] !== null && $item['content_edit_rate'] !== '') {
                return (int) $item['content_edit_rate'];
            }
        }

        // 2. Check top-level content_edit_rate
        if ($this->content_edit_rate !== null && $this->content_edit_rate !== '') {
            return (int) $this->content_edit_rate;
        }

        // 3. Fallback to global setting in Pengaturan
        return (int) Setting::getValue('content_edit_rate', 10000);
    }

    /**
     * Get content live rate for a specific platform/marketplace
     * Follows custom value if set, otherwise falls back to global setting.
     */
    public function getContentLiveRateForPlatform(?string $platform = null): int
    {
        $platform = $platform ?: 'tiktok';

        // 1. Check custom rate in platform_rates
        if (is_array($this->platform_rates) && isset($this->platform_rates[$platform])) {
            $item = $this->platform_rates[$platform];
            if (is_array($item) && isset($item['content_live_rate']) && $item['content_live_rate'] !== null && $item['content_live_rate'] !== '') {
                return (int) $item['content_live_rate'];
            }
        }

        // 2. Check top-level content_live_rate
        if ($this->content_live_rate !== null && $this->content_live_rate !== '') {
            return (int) $this->content_live_rate;
        }

        // 3. Fallback to global setting in Pengaturan
        return (int) Setting::getValue('content_live_rate', 5000);
    }

    /**
     * Get target live hours for a specific platform/marketplace
     * Follows custom value if set, otherwise falls back to global setting.
     */
    public function getTargetHoursForPlatform(?string $platform = null): float
    {
        $platform = $platform ?: 'tiktok';

        // 1. Check custom target in platform_rates
        if (is_array($this->platform_rates) && isset($this->platform_rates[$platform])) {
            $item = $this->platform_rates[$platform];
            if (is_array($item) && isset($item['target_hours']) && $item['target_hours'] !== null && $item['target_hours'] !== '') {
                return (float) $item['target_hours'];
            }
        }

        // 2. Check top-level monthly_target_hours
        if ($this->monthly_target_hours !== null && $this->monthly_target_hours !== '') {
            return (float) $this->monthly_target_hours;
        }

        // 3. Fallback to global setting in Pengaturan
        return (float) Setting::getValue('monthly_target_hours', 80);
    }

    /**
     * Get structured breakdown of hours, rates, and salaries per platform
     */
    public function getPlatformBreakdown($attendances): array
    {
        $breakdown = [];

        foreach ($attendances as $att) {
            $plat = $att->platform ?? 'tiktok';
            if (!isset($breakdown[$plat])) {
                $hourlyRate = $this->getHourlyRateForPlatform($plat);
                $editRate = $this->getContentEditRateForPlatform($plat);
                $liveRate = $this->getContentLiveRateForPlatform($plat);
                $targetHours = $this->getTargetHoursForPlatform($plat);

                $breakdown[$plat] = [
                    'platform' => $plat,
                    'info' => Setting::getPlatformInfo($plat),
                    'minutes' => 0,
                    'hours' => 0,
                    'sales' => 0,
                    'content_edit_count' => 0,
                    'content_live_count' => 0,
                    'count' => 0,
                    'rate' => $hourlyRate,
                    'hourly_rate' => $hourlyRate,
                    'content_edit_rate' => $editRate,
                    'content_live_rate' => $liveRate,
                    'target_hours' => $targetHours,
                    'salary' => 0, // live salary for backward compatibility
                    'live_salary' => 0,
                    'content_edit_salary' => 0,
                    'content_live_salary' => 0,
                    'total_salary' => 0,
                ];
            }
            $breakdown[$plat]['minutes'] += $att->live_duration_minutes;
            $breakdown[$plat]['sales'] += $att->sales_count;
            $breakdown[$plat]['content_edit_count'] += ($att->content_edit_count ?? 0);
            $breakdown[$plat]['content_live_count'] += ($att->content_live_count ?? 0);
            $breakdown[$plat]['count'] += 1;
        }

        foreach ($breakdown as $plat => &$data) {
            $data['hours'] = round($data['minutes'] / 60, 2);
            $data['live_salary'] = ($data['minutes'] / 60) * $data['hourly_rate'];
            $data['salary'] = $data['live_salary'];
            $data['content_edit_salary'] = $data['content_edit_count'] * $data['content_edit_rate'];
            $data['content_live_salary'] = $data['content_live_count'] * $data['content_live_rate'];
            $data['total_salary'] = $data['live_salary'] + $data['content_edit_salary'] + $data['content_live_salary'];
            $data['target_met'] = $data['target_hours'] > 0 && $data['hours'] >= $data['target_hours'];
            $data['target_progress'] = $data['target_hours'] > 0 ? min(100, round(($data['hours'] / $data['target_hours']) * 100, 1)) : 0;
        }

        return $breakdown;
    }

    /**
     * Calculate base salary (without sales bonus)
     */
    public function calculateBaseSalary(float $totalHours, int $contentEdit, int $contentLive): float
    {
        $hRate = $this->getHourlyRateForPlatform('tiktok');
        $editRate = $this->getContentEditRateForPlatform('tiktok');
        $liveRate = $this->getContentLiveRateForPlatform('tiktok');

        $liveSalary = $totalHours * $hRate;
        $contentBonus = ($contentEdit * $editRate) + ($contentLive * $liveRate);

        return $liveSalary + $contentBonus;
    }

    /**
     * Calculate monthly salary with bonus consideration
     * Bonus only applies if monthly target is met
     */
    public function calculateMonthlySalary(float $totalHours, int $contentEdit, int $contentLive, int $totalSales): array
    {
        $targetHours = $this->monthly_target_hours !== null && $this->monthly_target_hours !== ''
            ? (float) $this->monthly_target_hours
            : (float) Setting::getValue('monthly_target_hours', 80);

        $baseSalary = $this->calculateBaseSalary($totalHours, $contentEdit, $contentLive);
        $targetMet = $totalHours >= $targetHours;

        // Get sales bonus from tier if target is met
        $salesBonus = 0;
        if ($targetMet && $totalSales > 0) {
            $salesBonus = BonusTier::getBonusForSales($totalSales);
        }

        $hRate = $this->getHourlyRateForPlatform('tiktok');
        $editRate = $this->getContentEditRateForPlatform('tiktok');
        $liveRate = $this->getContentLiveRateForPlatform('tiktok');

        return [
            'base_salary' => $baseSalary,
            'live_salary' => $totalHours * $hRate,
            'content_bonus' => ($contentEdit * $editRate) + ($contentLive * $liveRate),
            'sales_bonus' => $salesBonus,
            'total' => $baseSalary + $salesBonus,
            'target_hours' => $targetHours,
            'current_hours' => $totalHours,
            'target_met' => $targetMet,
        ];
    }

    /**
     * Get effective bonus setting value (per-user or global fallback)
     */
    public function getEffectiveValue(string $field, $globalDefault = null)
    {
        // If user has a custom value set, use it
        if ($this->{$field} !== null) {
            return $this->{$field};
        }

        // Fallback to global setting
        $defaults = [
            'daily_live_hours' => 3,
            'monthly_leave_days' => 4,
            'bonus_pcs_threshold' => 20,
            'bonus_amount' => 10000,
        ];

        return Setting::getValue($field, $globalDefault ?? ($defaults[$field] ?? null));
    }

    /**
     * Calculate monthly target hours dynamically
     * Formula: (days_in_month - leave_days) * daily_hours, or scheme's monthly_target_hours if set
     */
    public function calculateMonthlyTarget($year = null, $month = null): float
    {
        // 1. If user has an explicit monthly target configured, use it
        if ($this->monthly_target_hours !== null && (float) $this->monthly_target_hours > 0) {
            return (float) $this->monthly_target_hours;
        }

        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $dailyHours = (float) $this->getEffectiveValue('daily_live_hours');
        $leaveDays = (int) $this->getEffectiveValue('monthly_leave_days');
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $workDays = $daysInMonth - $leaveDays;
        return $workDays * $dailyHours;
    }
}
