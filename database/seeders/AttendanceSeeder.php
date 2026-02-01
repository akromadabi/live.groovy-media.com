<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $durationOptions = [60, 90, 120, 150, 180, 210, 240, 270]; // in minutes (1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5 hours)
        $contentOptions = [0, 1, 2, 3, 4];
        $statusOptions = ['validated', 'validated', 'validated', 'pending']; // 75% validated

        foreach ($users as $user) {
            // Create 15 attendance records for each user
            for ($i = 0; $i < 15; $i++) {
                $date = Carbon::now()->subDays($i);

                Attendance::create([
                    'user_id' => $user->id,
                    'attendance_date' => $date->format('Y-m-d'),
                    'live_duration_minutes' => $durationOptions[array_rand($durationOptions)],
                    'content_edit_count' => $contentOptions[array_rand($contentOptions)],
                    'content_live_count' => $contentOptions[array_rand($contentOptions)],
                    'sales_count' => rand(0, 20),
                    'status' => $statusOptions[array_rand($statusOptions)],
                    'notes' => $i % 5 == 0 ? 'Catatan absensi contoh' : null,
                ]);
            }
        }
    }
}
