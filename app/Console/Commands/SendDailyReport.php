<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class SendDailyReport extends Command
{
    protected $signature = 'wa:send-daily-report';
    protected $description = 'Send daily attendance report via WhatsApp to admin';

    public function handle()
    {
        $waService = new WhatsAppService();

        if (!$waService->isEnabled()) {
            $this->warn('WhatsApp not enabled or not configured');
            return 0;
        }

        $adminPhone = Setting::getValue('whatsapp_admin_phone', '');

        // Support group ID format (ends with @g.us)
        $recipientPhone = Setting::getValue('whatsapp_group_id', '');
        if (empty($recipientPhone)) {
            $recipientPhone = $adminPhone;
        }

        if (empty($recipientPhone)) {
            $this->warn('Admin phone/group ID not configured');
            return 0;
        }

        $today = Carbon::today();
        $dayName = $today->translatedFormat('l');
        $dateFormatted = $today->format('d F Y');

        // Get all active users
        $users = User::where('role', 'user')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build report message
        $message = "LAPORAN LIVE\n";
        $message .= "{$dayName}, {$dateFormatted}\n\n";

        foreach ($users as $user) {
            // Get today's attendance for user
            $attendances = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $today)
                ->where('status', 'validated')
                ->get();

            $totalHours = round($attendances->sum('live_duration_minutes') / 60, 0);
            $videoEdit = $attendances->sum('content_edit_count');
            $contentLive = $attendances->sum('content_live_count');
            $sales = $attendances->sum('sales_count');

            $message .= strtoupper($user->name) . "\n";
            $message .= "LIVE: {$totalHours} JAM\n";

            if ($videoEdit > 0) {
                $message .= "VIDEO EDIT: {$videoEdit}\n";
            }
            if ($contentLive > 0) {
                $message .= "KONTEN LIVE: {$contentLive}\n";
            }
            if ($sales > 0) {
                $message .= "PENJUALAN: {$sales}\n";
            }

            $message .= "\n";
        }

        $result = $waService->sendDailyReport($recipientPhone, trim($message));

        if ($result) {
            $this->info('Daily report sent successfully');
        } else {
            $this->error('Failed to send daily report');
        }

        return 0;
    }
}
