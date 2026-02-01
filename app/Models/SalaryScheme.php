<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
