<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class SendAttendanceReminder extends Command
{
    protected $signature = 'wa:send-reminder';
    protected $description = 'Send WhatsApp reminder to users who have not submitted attendance today';

    public function handle()
    {
        $waService = new WhatsAppService();

        if (!$waService->isEnabled()) {
            $this->warn('WhatsApp not enabled or not configured');
            return 0;
        }

        $today = Carbon::today();
        $loginUrl = url('/login');

        // Get all active users
        $users = User::where('role', 'user')
            ->where('is_active', true)
            ->whereNotNull('phone')
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Check if user has attendance today
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->exists();

            if (!$hasAttendance) {
                $result = $waService->sendAttendanceReminder($user, $loginUrl);
                if ($result) {
                    $sent++;
                    $this->info("Reminder sent to {$user->name}");
                } else {
                    $this->warn("Failed to send to {$user->name}");
                }
            } else {
                $skipped++;
            }
        }

        $this->info("Sent: {$sent}, Skipped (already submitted): {$skipped}");
        return 0;
    }
}
