<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Only register scheduled tasks if settings table exists (avoids migration/database connection errors during bootstrap)
try {
    if (Schema::hasTable('settings')) {
        // WhatsApp Attendance Reminder - runs at configured time
        Schedule::command('wa:send-reminder')->dailyAt(
            Setting::getValue('wa_reminder_time', '18:00')
        )->when(function () {
            try {
                return Setting::getValue('whatsapp_enabled', false);
            } catch (\Throwable $e) {
                return false;
            }
        });

        // WhatsApp Daily Report - runs at configured time
        Schedule::command('wa:send-daily-report')->dailyAt(
            Setting::getValue('wa_report_time', '21:00')
        )->when(function () {
            try {
                return Setting::getValue('whatsapp_enabled', false);
            } catch (\Throwable $e) {
                return false;
            }
        });
    }
} catch (\Throwable $e) {
    // Prevent app from crashing during bootstrap if database is unreachable or migrating
}
