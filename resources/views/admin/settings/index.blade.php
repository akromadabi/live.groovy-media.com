@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <!-- WhatsApp Notification Settings -->
    <div class="card" style="max-width: 900px;">
        <div class="card-header">
            <h3 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    style="width: 24px; height: 24px; color: #25D366;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Pengaturan WhatsApp Notifikasi
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update-system') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-2 gap-4 mb-4">
                    <div class="form-group">
                        <label for="whatsapp_api_url" class="form-label">API URL</label>
                        <input type="text" name="whatsapp_api_url" id="whatsapp_api_url" class="form-control"
                            value="{{ $settings['whatsapp_api_url'] ?? '' }}"
                            placeholder="http://serverwa.hello-inv.com/send-message">
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_api_key" class="form-label">API Key</label>
                        <input type="text" name="whatsapp_api_key" id="whatsapp_api_key" class="form-control"
                            value="{{ $settings['whatsapp_api_key'] ?? '' }}" placeholder="VbM1epmqMKqrztVrWpd1YquAboWWFa">
                    </div>
                </div>

                <div class="grid grid-2 gap-4 mb-4">
                    <div class="form-group">
                        <label for="whatsapp_sender_number" class="form-label">Nomor Pengirim</label>
                        <input type="text" name="whatsapp_sender_number" id="whatsapp_sender_number" class="form-control"
                            value="{{ $settings['whatsapp_sender_number'] ?? '' }}" placeholder="6282135863451">
                        <small class="text-muted">Format: 62xxxxxxxxxx</small>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_admin_phone" class="form-label">Nomor Admin (Test & Pengingat)</label>
                        <input type="text" name="whatsapp_admin_phone" id="whatsapp_admin_phone" class="form-control"
                            value="{{ $settings['whatsapp_admin_phone'] ?? '' }}" placeholder="6281234567890">
                        <small class="text-muted">Nomor HP admin untuk test koneksi</small>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="whatsapp_group_id" class="form-label">ID Grup WhatsApp (Penerima Laporan)</label>
                    <input type="text" name="whatsapp_group_id" id="whatsapp_group_id" class="form-control"
                        value="{{ $settings['whatsapp_group_id'] ?? '' }}" placeholder="120363164010378827@g.us">
                    <small class="text-muted">ID Grup untuk menerima laporan harian (format: xxx@g.us)</small>
                </div>

                <hr style="border-color: var(--border); margin: 1.5rem 0;">
                <h4 class="mb-4">⏰ Jadwal Notifikasi</h4>

                <div class="grid grid-2 gap-4 mb-4">
                    <div class="form-group">
                        <label for="wa_reminder_time" class="form-label">Jam Pengingat Absen</label>
                        <input type="time" name="wa_reminder_time" id="wa_reminder_time" class="form-control"
                            value="{{ $settings['wa_reminder_time'] ?? '18:00' }}">
                        <small class="text-muted">Kirim pengingat ke user yang belum absen</small>
                    </div>

                    <div class="form-group">
                        <label for="wa_report_time" class="form-label">Jam Laporan Harian</label>
                        <input type="time" name="wa_report_time" id="wa_report_time" class="form-control"
                            value="{{ $settings['wa_report_time'] ?? '21:00' }}">
                        <small class="text-muted">Kirim rekap harian ke grup</small>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="flex items-center gap-2" style="cursor: pointer;">
                        <input type="checkbox" name="whatsapp_enabled" value="1"
                            {{ $settings['whatsapp_enabled'] ?? false ? 'checked' : '' }}
                            style="width: 18px; height: 18px;">
                        <span style="font-weight: 500;">Aktifkan notifikasi WhatsApp</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

            <hr style="border-color: var(--border); margin: 1.5rem 0;">

            <div class="flex gap-3 items-center" style="flex-wrap: wrap;">
                <form action="{{ route('admin.settings.test-whatsapp') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Test Kirim WA
                    </button>
                </form>
                <span class="text-muted" style="font-size: 0.85rem;">Test akan mengirim pesan ke nomor admin</span>
            </div>

            <div style="background: var(--card-bg); padding: 1rem; border-radius: 0.5rem; margin-top: 1rem;">
                <strong style="display: block; margin-bottom: 0.5rem;">📋 Informasi Penting:</strong>
                <ul style="margin: 0; padding-left: 1.5rem; color: var(--text-secondary); font-size: 0.9rem;">
                    <li>Pengingat absen dikirim ke user yang belum mengisi absen hari itu</li>
                    <li>Laporan harian berisi rekap jam live semua user aktif</li>
                    <li>Pastikan scheduler Laravel berjalan: <code>php artisan schedule:work</code></li>
                    <li>Untuk production, setup cron job: <code>* * * * * php artisan schedule:run</code></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- System Settings -->
    <div class="card mt-6" style="max-width: 900px;">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Lainnya</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update-system') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="app_name" class="form-label">Nama Aplikasi</label>
                    <input type="text" name="app_name" id="app_name" class="form-control"
                        value="{{ $settings['app_name'] ?? 'TikTok Live Manager' }}">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>
@endsection