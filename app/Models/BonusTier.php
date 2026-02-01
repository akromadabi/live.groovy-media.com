<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusTier extends Model
{
    protected $fillable = [
        'min_sales',
        'max_sales',
        'bonus_amount',
        'description',
        'is_active',
    ];

    protected $casts = [
        'bonus_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the applicable bonus for a given sales count using multiplier logic
     * 
     * Example: threshold = 20, bonus_amount = 10000
     * - 19 sales = 0 (0 * 10000)
     * - 20 sales = 10000 (1 * 10000)
     * - 39 sales = 10000 (1 * 10000)
     * - 40 sales = 20000 (2 * 10000)
     * - 80 sales = 40000 (4 * 10000)
     */
    public static function getBonusForSales(int $salesCount): float
    {
        $threshold = (int) \App\Models\Setting::getValue('bonus_pcs_threshold', 20);
        $bonusAmount = (float) \App\Models\Setting::getValue('bonus_amount', 10000);

        if ($threshold <= 0 || $salesCount < $threshold) {
            return 0;
        }

        // Calculate multiplier (floor division)
        $multiplier = floor($salesCount / $threshold);

        return $multiplier * $bonusAmount;
    }

    /**
     * Get all active tiers ordered by min_sales
     */
    public static function getActiveTiers()
    {
        return self::where('is_active', true)
            ->orderBy('min_sales')
            ->get();
    }
}
