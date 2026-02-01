<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SalaryScheme;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@tiktok.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'task' => 'Administrator',
                'is_active' => true,
                'phone' => '081234567890',
            ]
        );

        // Create users from the list
        $users = [
            [
                'name' => 'Tahsya Ilfina',
                'email' => 'tahsya@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6285720188221',
            ],
            [
                'name' => 'Naili Nimatul Maula',
                'email' => 'naili@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6285700130249',
            ],
            [
                'name' => 'Khoiril Septian',
                'email' => 'khoiril@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '62895324587351',
            ],
            [
                'name' => 'Faiska',
                'email' => 'faiska@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6285601525370',
            ],
            [
                'name' => 'Vera Atika Sari',
                'email' => 'vera@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6285226113936',
            ],
            [
                'name' => 'Elly',
                'email' => 'elly@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6281328587022',
            ],
            [
                'name' => 'Triana',
                'email' => 'triana@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6287819318975',
            ],
            [
                'name' => 'Dina Istifada',
                'email' => 'dina@tiktok.local',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'task' => 'Host Live',
                'is_active' => true,
                'phone' => '6289501164371',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Create default salary scheme for each user if not exists
            SalaryScheme::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'hourly_rate' => 25000,
                    'content_edit_rate' => 15000,
                    'content_live_rate' => 10000,
                    'sales_bonus_percentage' => 0,
                    'sales_bonus_nominal' => 0,
                ]
            );
        }
    }
}
