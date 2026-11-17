@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div style="max-width: 860px;">
        <!-- WhatsApp Notification Settings -->
        <div class="card mb-3">
            <div class="card-header" style="padding: 0.65rem 0.85rem;">
                <h3 class="card-title flex items-center gap-2" style="font-size: 0.95rem; font-weight: 600;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="width: 20px; height: 20px; color: #25D366;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Pengaturan Notifikasi WhatsApp
                </h3>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <form action="{{ route('admin.settings.update-system') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-2 gap-3 mb-3">
                        <div class="form-group mb-0">
                            <label for="whatsapp_api_url" class="form-label" style="font-size: 0.8rem;">API URL</label>
                            <input type="text" name="whatsapp_api_url" id="whatsapp_api_url" class="form-control"
                                value="{{ $settings['whatsapp_api_url'] ?? '' }}"
                                placeholder="http://serverwa.hello-inv.com/send-message">
                        </div>

                        <div class="form-group mb-0">
                            <label for="whatsapp_api_key" class="form-label" style="font-size: 0.8rem;">API Key</label>
                            <input type="text" name="whatsapp_api_key" id="whatsapp_api_key" class="form-control"
                                value="{{ $settings['whatsapp_api_key'] ?? '' }}" placeholder="API Key token">
                        </div>
                    </div>

                    <div class="grid grid-2 gap-3 mb-3">
                        <div class="form-group mb-0">
                            <label for="whatsapp_sender_number" class="form-label" style="font-size: 0.8rem;">Nomor Pengirim</label>
                            <input type="text" name="whatsapp_sender_number" id="whatsapp_sender_number" class="form-control"
                                value="{{ $settings['whatsapp_sender_number'] ?? '' }}" placeholder="628xxxxxxxxxx">
                            <small class="text-muted" style="font-size: 0.72rem;">Format: 62xxxxxxxxxx</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="whatsapp_admin_phone" class="form-label" style="font-size: 0.8rem;">Nomor Admin (Test & Pengingat)</label>
                            <input type="text" name="whatsapp_admin_phone" id="whatsapp_admin_phone" class="form-control"
                                value="{{ $settings['whatsapp_admin_phone'] ?? '' }}" placeholder="628xxxxxxxxxx">
                            <small class="text-muted" style="font-size: 0.72rem;">Nomor HP admin tujuan test koneksi</small>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="whatsapp_group_id" class="form-label" style="font-size: 0.8rem;">ID Grup WhatsApp (Penerima Laporan)</label>
                        <input type="text" name="whatsapp_group_id" id="whatsapp_group_id" class="form-control"
                            value="{{ $settings['whatsapp_group_id'] ?? '' }}" placeholder="120363164010378827@g.us">
                        <small class="text-muted" style="font-size: 0.72rem;">ID Grup untuk menerima laporan harian (format: xxx@g.us)</small>
                    </div>

                    <hr style="border-color: var(--border); margin: 1rem 0;">
                    <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.65rem;">⏰ Jadwal Notifikasi</h4>

                    <div class="grid grid-2 gap-3 mb-3">
                        <div class="form-group mb-0">
                            <label for="wa_reminder_time" class="form-label" style="font-size: 0.8rem;">Jam Pengingat Absen</label>
                            <input type="time" name="wa_reminder_time" id="wa_reminder_time" class="form-control"
                                value="{{ $settings['wa_reminder_time'] ?? '18:00' }}">
                            <small class="text-muted" style="font-size: 0.72rem;">Kirim ke user yang belum isi absen</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="wa_report_time" class="form-label" style="font-size: 0.8rem;">Jam Laporan Harian</label>
                            <input type="time" name="wa_report_time" id="wa_report_time" class="form-control"
                                value="{{ $settings['wa_report_time'] ?? '21:00' }}">
                            <small class="text-muted" style="font-size: 0.72rem;">Kirim rekap harian ke grup</small>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="flex items-center gap-2" style="cursor: pointer;">
                            <input type="checkbox" name="whatsapp_enabled" value="1"
                                {{ ($settings['whatsapp_enabled'] ?? false) ? 'checked' : '' }}
                                style="width: 16px; height: 16px;">
                            <span style="font-size: 0.85rem; font-weight: 500;">Aktifkan notifikasi otomatis WhatsApp</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>

                <hr style="border-color: var(--border); margin: 1rem 0;">

                <div class="flex gap-3 items-center flex-wrap">
                    <form action="{{ route('admin.settings.test-whatsapp') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="height: 32px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Test Kirim WA ke Admin
                        </button>
                    </form>
                    <span class="text-muted" style="font-size: 0.78rem;">Menguji koneksi WhatsApp gateway ke nomor admin</span>
                </div>

                <div class="alert alert-info mt-3" style="padding: 0.65rem 0.85rem; font-size: 0.78rem; border-radius: 6px;">
                    <strong style="display: block; margin-bottom: 0.35rem;">📋 Informasi Scheduler:</strong>
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        <li>Pastikan cron/scheduler aktif di server: <code>* * * * * php artisan schedule:run</code></li>
                        <li>Mode lokal (Laragon/Terminal): jalankan <code>php artisan schedule:work</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="card">
            <div class="card-header" style="padding: 0.65rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Pengaturan Aplikasi</h3>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <form action="{{ route('admin.settings.update-system') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="app_name" class="form-label" style="font-size: 0.8rem;">Nama Aplikasi</label>
                        <input type="text" name="app_name" id="app_name" class="form-control"
                            value="{{ $settings['app_name'] ?? 'TikTok Live Manager' }}">
                    </div>

                    <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Nama Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
