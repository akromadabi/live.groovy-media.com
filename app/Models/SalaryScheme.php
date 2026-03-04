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
        'hourly_rate' => 20000,
        'content_edit_rate' => 10000,
        'content_live_rate' => 5000,
        'monthly_target_hours' => 80,
        'sales_bonus_percentage' => 0,
        'sales_bonus_nominal' => 0,
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'integer',
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
     * Calculate base salary (without sales bonus)
     */
    public function calculateBaseSalary(float $totalHours, int $contentEdit, int $contentLive): float
    {
        $liveSalary = $totalHours * $this->hourly_rate;
        $contentBonus = ($contentEdit * $this->content_edit_rate)
            + ($contentLive * $this->content_live_rate);

        return $liveSalary + $contentBonus;
    }

    /**
     * Calculate monthly salary with bonus consideration
     * Bonus only applies if monthly target is met
     */
    public function calculateMonthlySalary(float $totalHours, int $contentEdit, int $contentLive, int $totalSales): array
    {
        $baseSalary = $this->calculateBaseSalary($totalHours, $contentEdit, $contentLive);
        $targetMet = $totalHours >= $this->monthly_target_hours;

        // Get sales bonus from tier if target is met
        $salesBonus = 0;
        if ($targetMet && $totalSales > 0) {
            $salesBonus = BonusTier::getBonusForSales($totalSales);
        }

        return [
            'base_salary' => $baseSalary,
            'live_salary' => $totalHours * $this->hourly_rate,
            'content_bonus' => ($contentEdit * $this->content_edit_rate) + ($contentLive * $this->content_live_rate),
            'sales_bonus' => $salesBonus,
            'total' => $baseSalary + $salesBonus,
            'target_hours' => (float) $this->monthly_target_hours,
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
     * Formula: (days_in_month - leave_days) * daily_hours
     */
    public function calculateMonthlyTarget($year = null, $month = null): float
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $dailyHours = (float) $this->getEffectiveValue('daily_live_hours');
        $leaveDays = (int) $this->getEffectiveValue('monthly_leave_days');
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $workDays = $daysInMonth - $leaveDays;
        return $workDays * $dailyHours;
    }
}
