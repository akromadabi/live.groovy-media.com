<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Only register scheduled tasks if settings table exists (avoids migration errors)
if (Schema::hasTable('settings')) {
    // WhatsApp Attendance Reminder - runs at configured time
    Schedule::command('wa:send-reminder')->dailyAt(
        Setting::getValue('wa_reminder_time', '18:00')
    )->when(function () {
        return Setting::getValue('whatsapp_enabled', false);
    });

    // WhatsApp Daily Report - runs at configured time
    Schedule::command('wa:send-daily-report')->dailyAt(
        Setting::getValue('wa_report_time', '21:00')
    )->when(function () {
        return Setting::getValue('whatsapp_enabled', false);
    });
}
