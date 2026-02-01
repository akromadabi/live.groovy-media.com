<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BonusTier;

class BonusTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'min_sales' => 0,
                'max_sales' => 19,
                'bonus_amount' => 0,
                'description' => 'Tidak ada bonus',
            ],
            [
                'min_sales' => 20,
                'max_sales' => 49,
                'bonus_amount' => 10000,
                'description' => 'Bonus Bronze (20-49 pcs)',
            ],
            [
                'min_sales' => 50,
                'max_sales' => 99,
                'bonus_amount' => 30000,
                'description' => 'Bonus Silver (50-99 pcs)',
            ],
            [
                'min_sales' => 100,
                'max_sales' => 199,
                'bonus_amount' => 75000,
                'description' => 'Bonus Gold (100-199 pcs)',
            ],
            [
                'min_sales' => 200,
                'max_sales' => null,
                'bonus_amount' => 150000,
                'description' => 'Bonus Platinum (200+ pcs)',
            ],
        ];

        foreach ($tiers as $tier) {
            BonusTier::create($tier);
        }
    }
}
