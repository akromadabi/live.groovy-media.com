@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="settings-container">
        <!-- ========================================================
             1. PENGATURAN FORMULIR & KEBIJAKAN ABSENSI
             ======================================================== -->
        <div class="settings-card mb-4">
            <div class="settings-card-header">
                <div class="flex items-center gap-2.5">
                    <div class="settings-header-icon icon-indigo">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Pengaturan Formulir & Kebijakan Absensi</h3>
                        <p class="settings-card-subtitle">Atur fitur dinamis formulir absensi host dan batasan waktu pengisian</p>
                    </div>
                </div>

                <button type="button" id="btn-toggle-preview" class="settings-btn-preview" title="Lihat simulasi formulir di layar host">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Preview Form Host</span>
                </button>
            </div>

            <div class="settings-card-body">
                <form action="{{ route('admin.settings.update-system') }}" method="POST" id="attendance-policy-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="setting_group" value="attendance">

                    <!-- Fitur 1: Bukti Screenshot -->
                    <div class="settings-item">
                        <div class="settings-item-main">
                            <div class="flex items-center gap-3">
                                <div class="settings-item-icon icon-indigo">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="settings-item-title">Bukti Screenshot (Proof of Work)</span>
                                    <p class="settings-item-desc">Foto live otomatis dikompresi WebP/HD sebelum diupload (hemat storage & kuota).</p>
                                </div>
                            </div>
                            <label class="settings-switch" title="Aktifkan untuk menampilkan kolom upload screenshot di form host">
                                <input type="checkbox" name="attendance_proof_enabled" id="attendance_proof_enabled" value="1"
                                    {{ ($settings['attendance_proof_enabled'] ?? true) ? 'checked' : '' }}>
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Sub-option: Hanya muncul jika fitur diaktifkan -->
                        <div class="settings-sub-panel" id="proof-sub-panel" style="display: {{ ($settings['attendance_proof_enabled'] ?? true) ? 'block' : 'none' }};">
                            <label class="settings-sub-checkbox">
                                <input type="checkbox" name="attendance_proof_required" id="attendance_proof_required" value="1"
                                    {{ ($settings['attendance_proof_required'] ?? false) ? 'checked' : '' }}>
                                <span>
                                    <strong>Wajib diisi oleh host</strong>
                                    <span class="text-muted">(Host tidak dapat submit absensi jika foto screenshot kosong)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Fitur 2: Link Video TikTok (VT) -->
                    <div class="settings-item">
                        <div class="settings-item-main">
                            <div class="flex items-center gap-3">
                                <div class="settings-item-icon icon-sky">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="settings-item-title">Link Video TikTok (VT)</span>
                                    <p class="settings-item-desc">Host melampirkan tautan URL video TikTok hasil live streaming atau rekaman editan.</p>
                                </div>
                            </div>
                            <label class="settings-switch switch-sky" title="Aktifkan untuk menampilkan kolom input link TikTok di form host">
                                <input type="checkbox" name="attendance_vt_link_enabled" id="attendance_vt_link_enabled" value="1"
                                    {{ ($settings['attendance_vt_link_enabled'] ?? true) ? 'checked' : '' }}>
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Sub-option: Hanya muncul jika fitur diaktifkan -->
                        <div class="settings-sub-panel" id="vt-sub-panel" style="display: {{ ($settings['attendance_vt_link_enabled'] ?? true) ? 'block' : 'none' }};">
                            <label class="settings-sub-checkbox">
                                <input type="checkbox" name="attendance_vt_link_required" id="attendance_vt_link_required" value="1"
                                    {{ ($settings['attendance_vt_link_required'] ?? false) ? 'checked' : '' }}>
                                <span>
                                    <strong>Wajib diisi oleh host</strong>
                                    <span class="text-muted">(Host tidak dapat submit absensi jika link video kosong)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Fitur 3: Batas Waktu Pengisian (Deadline Locking) -->
                    <div class="settings-item">
                        <div class="settings-item-main">
                            <div class="flex items-center gap-3">
                                <div class="settings-item-icon icon-amber">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="settings-item-title">Batas Waktu Pengisian (Deadline Locking)</span>
                                    <p class="settings-item-desc">Mencegah host mengisi atau merevisi absensi untuk tanggal yang sudah lama terlewat.</p>
                                </div>
                            </div>
                            <label class="settings-switch switch-amber" title="Aktifkan batasan cut-off tanggal absensi">
                                <input type="checkbox" name="attendance_deadline_enabled" id="attendance_deadline_enabled" value="1"
                                    {{ ($settings['attendance_deadline_enabled'] ?? false) ? 'checked' : '' }}>
                                <span class="settings-switch-slider"></span>
                            </label>
                        </div>

                        <!-- Sub-option: Hanya muncul jika fitur diaktifkan -->
                        <div class="settings-sub-panel" id="deadline-sub-panel" style="display: {{ ($settings['attendance_deadline_enabled'] ?? false) ? 'block' : 'none' }};">
                            <div class="grid grid-2 gap-3">
                                <div>
                                    <label for="attendance_deadline_days" class="form-label" style="font-size: 0.76rem; font-weight: 600; margin-bottom: 0.25rem;">
                                        Toleransi Hari Mundur
                                    </label>
                                    <select name="attendance_deadline_days" id="attendance_deadline_days" class="settings-form-control">
                                        <option value="0" {{ ($settings['attendance_deadline_days'] ?? 1) == 0 ? 'selected' : '' }}>Hanya Hari H (Maksimal hari yang sama)</option>
                                        <option value="1" {{ ($settings['attendance_deadline_days'] ?? 1) == 1 ? 'selected' : '' }}>Maksimal H+1 (Besoknya - Standar)</option>
                                        <option value="2" {{ ($settings['attendance_deadline_days'] ?? 1) == 2 ? 'selected' : '' }}>Maksimal H+2 (2 hari setelah live)</option>
                                        <option value="3" {{ ($settings['attendance_deadline_days'] ?? 1) == 3 ? 'selected' : '' }}>Maksimal H+3 (3 hari setelah live)</option>
                                        <option value="7" {{ ($settings['attendance_deadline_days'] ?? 1) == 7 ? 'selected' : '' }}>Maksimal 1 Minggu (7 hari)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="attendance_deadline_time" class="form-label mb-0" style="font-size: 0.76rem; font-weight: 600; margin-bottom: 0.25rem;">
                                        Jam Cut-off Terakhir (WIB)
                                    </label>
                                    <input type="time" name="attendance_deadline_time" id="attendance_deadline_time" class="settings-form-control"
                                        value="{{ $settings['attendance_deadline_time'] ?? '23:59' }}">
                                </div>
                            </div>

                            <p class="text-xs text-muted mb-0 mt-2" id="deadline-sim-text" style="font-size: 0.74rem;">
                                Contoh: Live streaming hari <strong>Senin</strong> dapat diisi maksimal hingga <strong>Selasa pukul 23:59 WIB</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Collapsible Host Preview Drawer -->
                    <div id="policy-preview-wrapper" style="display: none;" class="mt-3">
                        <div class="settings-preview-box">
                            <div class="settings-preview-header">
                                <span style="font-weight: 700; font-size: 0.78rem; color: var(--text-primary);">📱 Simulasi Tampilan di Formulir Absensi Host:</span>
                                <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: #4f46e5; font-size: 0.68rem;">Live Preview</span>
                            </div>
                            <div class="settings-preview-body">
                                <div id="mock-deadline-alert" class="settings-mock-alert mb-2" style="display: none;">
                                    ℹ️ Batas input absensi berlaku: maksimal <span id="mock-deadline-text">H+1</span>.
                                </div>
                                <div class="grid grid-2 gap-2 mb-2">
                                    <div class="settings-mock-input">
                                        <span class="settings-mock-label">Tanggal Live *</span>
                                        <span style="font-size: 0.74rem;">{{ date('d M Y') }}</span>
                                    </div>
                                    <div class="settings-mock-input">
                                        <span class="settings-mock-label">Sesi *</span>
                                        <span style="font-size: 0.74rem;">Sesi 1 (Pagi) ▾</span>
                                    </div>
                                </div>
                                <div id="mock-proof-field" class="settings-mock-input mb-2">
                                    <div class="flex items-center justify-between">
                                        <span class="settings-mock-label mb-0">📸 Bukti Screenshot <span id="mock-proof-badge" style="color: #64748b;">(Opsional)</span></span>
                                        <span style="font-size: 0.65rem; color: #10b981; font-weight: 600;">⚡ HD Compress</span>
                                    </div>
                                    <div style="border: 1px dashed var(--border); border-radius: 4px; padding: 0.4rem; text-align: center; margin-top: 4px; font-size: 0.7rem; color: var(--text-muted);">
                                        Pilih Foto Screenshot Live
                                    </div>
                                </div>
                                <div id="mock-vt-field" class="settings-mock-input">
                                    <span class="settings-mock-label">🔗 Link Video TikTok <span id="mock-vt-badge" style="color: #64748b;">(Opsional)</span></span>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">https://vt.tiktok.com/...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between flex-wrap gap-2 mt-3 pt-2">
                        <button type="submit" class="settings-btn-primary" id="btn-save-attendance-policy">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Kebijakan Absensi</span>
                        </button>
                        <span class="text-muted" style="font-size: 0.73rem;">
                            Kebijakan langsung berlaku untuk seluruh akun host.
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================
             2. PENGATURAN NOTIFIKASI WHATSAPP
             ======================================================== -->
        <div class="settings-card mb-4">
            <div class="settings-card-header">
                <div class="flex items-center gap-2.5">
                    <div class="settings-header-icon icon-emerald">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Pengaturan Notifikasi WhatsApp</h3>
                        <p class="settings-card-subtitle">Kirim pengingat absensi otomatis dan laporan harian ke grup WhatsApp</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-muted font-medium">Status Gateway:</span>
                    <label class="settings-switch switch-emerald" title="Aktifkan atau matikan seluruh notifikasi WhatsApp otomatis">
                        <input type="checkbox" form="whatsapp-settings-form" name="whatsapp_enabled" id="whatsapp_enabled" value="1"
                            {{ ($settings['whatsapp_enabled'] ?? false) ? 'checked' : '' }}>
                        <span class="settings-switch-slider"></span>
                    </label>
                </div>
            </div>

            <div class="settings-card-body">
                <form action="{{ route('admin.settings.update-system') }}" method="POST" id="whatsapp-settings-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="setting_group" value="whatsapp">

                    <!-- Section: Gateway Credentials -->
                    <div class="mb-3">
                        <span class="text-muted" style="font-size: 0.72rem; font-weight: 700; display: block; margin-bottom: 0.6rem; letter-spacing: 0.03em;">
                            KREDENSIAL API GATEWAY:
                        </span>
                        <div class="grid grid-2 gap-3 mb-3">
                            <div>
                                <label for="whatsapp_api_url" class="form-label" style="font-size: 0.76rem; font-weight: 600;">API URL</label>
                                <input type="text" name="whatsapp_api_url" id="whatsapp_api_url" class="settings-form-control"
                                    value="{{ $settings['whatsapp_api_url'] ?? '' }}"
                                    placeholder="http://serverwa.hello-inv.com/send-message">
                            </div>
                            <div>
                                <label for="whatsapp_api_key" class="form-label" style="font-size: 0.76rem; font-weight: 600;">API Key</label>
                                <input type="text" name="whatsapp_api_key" id="whatsapp_api_key" class="settings-form-control"
                                    value="{{ $settings['whatsapp_api_key'] ?? '' }}" placeholder="API Key token gateway">
                            </div>
                        </div>

                        <div class="grid grid-2 gap-3 mb-3">
                            <div>
                                <label for="whatsapp_sender_number" class="form-label" style="font-size: 0.76rem; font-weight: 600;">Nomor Pengirim (Sender)</label>
                                <input type="text" name="whatsapp_sender_number" id="whatsapp_sender_number" class="settings-form-control"
                                    value="{{ $settings['whatsapp_sender_number'] ?? '' }}" placeholder="628xxxxxxxxxx">
                                <small class="text-muted" style="font-size: 0.7rem;">Nomor server/bot WA (format: 628xxx)</small>
                            </div>
                            <div>
                                <label for="whatsapp_admin_phone" class="form-label" style="font-size: 0.76rem; font-weight: 600;">Nomor Admin (Tujuan Test)</label>
                                <input type="text" name="whatsapp_admin_phone" id="whatsapp_admin_phone" class="settings-form-control"
                                    value="{{ $settings['whatsapp_admin_phone'] ?? '' }}" placeholder="628xxxxxxxxxx">
                                <small class="text-muted" style="font-size: 0.7rem;">Nomor HP admin penerima uji coba koneksi</small>
                            </div>
                        </div>

                        <div>
                            <label for="whatsapp_group_id" class="form-label" style="font-size: 0.76rem; font-weight: 600;">ID Grup WhatsApp (Rekap Laporan)</label>
                            <input type="text" name="whatsapp_group_id" id="whatsapp_group_id" class="settings-form-control"
                                value="{{ $settings['whatsapp_group_id'] ?? '' }}" placeholder="120363164010378827@g.us">
                            <small class="text-muted" style="font-size: 0.7rem;">ID Grup tujuan kirim rekap performa harian (format: xxx@g.us)</small>
                        </div>
                    </div>

                    <div style="border-top: 1px dashed var(--border); margin: 1.1rem 0;"></div>

                    <!-- Section: Notification Schedule -->
                    <div class="mb-3">
                        <span class="text-muted" style="font-size: 0.72rem; font-weight: 700; display: block; margin-bottom: 0.6rem; letter-spacing: 0.03em;">
                            JADWAL PENGIRIMAN OTOMATIS:
                        </span>
                        <div class="grid grid-2 gap-3">
                            <div>
                                <label for="wa_reminder_time" class="form-label" style="font-size: 0.76rem; font-weight: 600;">Jam Pengingat Absen</label>
                                <input type="time" name="wa_reminder_time" id="wa_reminder_time" class="settings-form-control"
                                    value="{{ $settings['wa_reminder_time'] ?? '18:00' }}">
                                <small class="text-muted" style="font-size: 0.7rem;">Kirim ke host yang belum mengisi absensi hari ini</small>
                            </div>
                            <div>
                                <label for="wa_report_time" class="form-label" style="font-size: 0.76rem; font-weight: 600;">Jam Laporan Harian</label>
                                <input type="time" name="wa_report_time" id="wa_report_time" class="settings-form-control"
                                    value="{{ $settings['wa_report_time'] ?? '21:00' }}">
                                <small class="text-muted" style="font-size: 0.7rem;">Kirim rekap total jam live dan omset ke grup</small>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-2 mt-4 pt-1">
                        <button type="submit" class="settings-btn-primary" style="background: #059669;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Pengaturan WhatsApp</span>
                        </button>
                    </div>
                </form>

                <div style="border-top: 1px solid var(--border); margin: 1.15rem 0;"></div>

                <!-- Test Connection & Scheduler Info -->
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <form action="{{ route('admin.settings.test-whatsapp') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="height: 34px; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" style="color: #25D366;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Test Kirim WA ke Admin</span>
                        </button>
                    </form>
                    <span class="text-muted" style="font-size: 0.74rem;">Uji coba pengiriman pesan langsung ke nomor HP admin</span>
                </div>

                <div class="settings-info-alert mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" style="color: #0284c7; flex-shrink: 0; margin-top: 1px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div style="font-size: 0.74rem; color: var(--text-secondary); line-height: 1.45;">
                        <strong>Informasi Scheduler Server:</strong> Pastikan cron/scheduler aktif agar notifikasi otomatis berjalan:
                        <code>* * * * * php artisan schedule:run</code>. Di lokal: <code>php artisan schedule:work</code>.
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================
             3. PENGATURAN IDENTITAS APLIKASI
             ======================================================== -->
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="flex items-center gap-2.5">
                    <div class="settings-header-icon icon-slate">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="settings-card-title">Identitas & Konfigurasi Aplikasi</h3>
                        <p class="settings-card-subtitle">Ubah nama sistem yang tampil pada header dan title browser</p>
                    </div>
                </div>
            </div>

            <div class="settings-card-body">
                <form action="{{ route('admin.settings.update-system') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="setting_group" value="app">

                    <div class="form-group mb-3">
                        <label for="app_name" class="form-label" style="font-size: 0.76rem; font-weight: 600;">Nama Aplikasi</label>
                        <input type="text" name="app_name" id="app_name" class="settings-form-control"
                            value="{{ $settings['app_name'] ?? 'TikTok Live Manager' }}" placeholder="TikTok Live Manager">
                    </div>

                    <button type="submit" class="settings-btn-primary" style="background: #475569;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Nama Aplikasi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* ==========================================================
       UNIFIED MODERN SETTINGS STYLING
       ========================================================== */
    .settings-container {
        max-width: 860px;
        margin: 0 auto;
    }

    /* Unified Settings Card */
    .settings-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.25rem;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .settings-card:hover {
        border-color: rgba(99, 102, 241, 0.35);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
    }

    .settings-card-header {
        padding: 0.85rem 1.15rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg-primary);
        flex-wrap: wrap;
    }

    .settings-header-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .icon-indigo { background: rgba(99, 102, 241, 0.12); color: #4f46e5; }
    .icon-sky { background: rgba(14, 165, 233, 0.12); color: #0284c7; }
    .icon-amber { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .icon-emerald { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .icon-slate { background: rgba(100, 116, 139, 0.12); color: #475569; }

    .settings-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.25;
        margin: 0;
    }
    .settings-card-subtitle {
        font-size: 0.74rem;
        color: var(--text-muted);
        margin: 2px 0 0 0;
    }

    .settings-card-body {
        padding: 1.15rem;
    }

    /* Settings Item Box */
    .settings-item {
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.85rem;
        background: var(--surface);
        transition: all 0.2s ease;
    }
    .settings-item:last-child {
        margin-bottom: 0;
    }
    .settings-item:hover {
        border-color: rgba(99, 102, 241, 0.35);
    }

    .settings-item-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .settings-item-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .settings-item-title {
        font-size: 0.86rem;
        font-weight: 600;
        color: var(--text-primary);
        display: block;
        line-height: 1.3;
    }
    .settings-item-desc {
        font-size: 0.73rem;
        color: var(--text-secondary);
        line-height: 1.4;
        margin: 2px 0 0 0;
    }

    /* Sub Panel (Conditionally Shown) */
    .settings-sub-panel {
        margin-top: 0.7rem;
        padding-top: 0.7rem;
        border-top: 1px dashed var(--border);
        animation: fadeInPanel 0.2s ease;
    }
    @keyframes fadeInPanel {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .settings-sub-checkbox {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        cursor: pointer;
        user-select: none;
        font-size: 0.78rem;
        color: var(--text-primary);
        margin: 0;
    }
    .settings-sub-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #4f46e5;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* Modern Minimalist iOS Switch */
    .settings-switch {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        margin: 0;
        flex-shrink: 0;
    }
    .settings-switch input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        margin: 0;
    }
    .settings-switch-slider {
        position: relative;
        width: 42px;
        height: 23px;
        background-color: #cbd5e1;
        border-radius: 23px;
        transition: background-color 0.2s ease;
        display: inline-block;
    }
    [data-theme="dark"] .settings-switch-slider {
        background-color: #475569;
    }
    .settings-switch-slider::before {
        content: '';
        position: absolute;
        width: 17px;
        height: 17px;
        left: 3px;
        top: 3px;
        background-color: #ffffff;
        border-radius: 50%;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }
    .settings-switch input:checked + .settings-switch-slider {
        background-color: #4f46e5;
    }
    .settings-switch input:checked + .settings-switch-slider::before {
        transform: translateX(19px);
    }
    .switch-sky input:checked + .settings-switch-slider {
        background-color: #0284c7;
    }
    .switch-amber input:checked + .settings-switch-slider {
        background-color: #f59e0b;
    }
    .switch-emerald input:checked + .settings-switch-slider {
        background-color: #10b981;
    }

    /* Form Controls */
    .settings-form-control {
        width: 100%;
        padding: 0.48rem 0.7rem;
        font-size: 0.8rem;
        border: 1px solid var(--border);
        border-radius: 7px;
        background: var(--surface);
        color: var(--text-primary);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .settings-form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .settings-btn-primary {
        height: 36px;
        padding: 0 1.25rem;
        font-size: 0.82rem;
        font-weight: 600;
        border-radius: 7px;
        background: #4f46e5;
        color: #ffffff;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .settings-btn-primary:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    .settings-btn-preview {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        height: 28px;
        padding: 0 0.65rem;
        font-size: 0.74rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .settings-btn-preview:hover,
    .settings-btn-preview.active {
        border-color: #4f46e5;
        color: #4f46e5;
        background: rgba(99, 102, 241, 0.08);
    }

    .settings-preview-box {
        background: var(--bg-primary);
        border: 1px dashed var(--border);
        border-radius: 9px;
        padding: 0.85rem;
    }
    .settings-preview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.6rem;
    }
    .settings-mock-alert {
        background: rgba(14, 165, 233, 0.1);
        border: 1px solid rgba(14, 165, 233, 0.2);
        border-radius: 6px;
        padding: 0.4rem 0.65rem;
        font-size: 0.72rem;
        color: #0369a1;
    }
    [data-theme="dark"] .settings-mock-alert { color: #38bdf8; }
    .settings-mock-input {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 0.4rem 0.6rem;
    }
    .settings-mock-label {
        font-size: 0.68rem;
        font-weight: 600;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 2px;
    }

    .settings-info-alert {
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Proof Screenshot
    const proofEnabled = document.getElementById('attendance_proof_enabled');
    const proofRequired = document.getElementById('attendance_proof_required');
    const proofSubPanel = document.getElementById('proof-sub-panel');
    const mockProofField = document.getElementById('mock-proof-field');
    const mockProofBadge = document.getElementById('mock-proof-badge');

    function syncProof() {
        const isEnabled = proofEnabled.checked;
        const isReq = proofRequired.checked;

        // Sub-option ONLY appears if feature is enabled
        proofSubPanel.style.display = isEnabled ? 'block' : 'none';
        if (mockProofField) mockProofField.style.display = isEnabled ? 'block' : 'none';
        if (mockProofBadge) {
            mockProofBadge.textContent = isReq ? '* Wajib' : '(Opsional)';
            mockProofBadge.style.color = isReq ? '#dc2626' : '#64748b';
            mockProofBadge.style.fontWeight = isReq ? '700' : 'normal';
        }
    }
    if (proofEnabled) proofEnabled.addEventListener('change', syncProof);
    if (proofRequired) proofRequired.addEventListener('change', syncProof);

    // 2. Video Link
    const vtEnabled = document.getElementById('attendance_vt_link_enabled');
    const vtRequired = document.getElementById('attendance_vt_link_required');
    const vtSubPanel = document.getElementById('vt-sub-panel');
    const mockVtField = document.getElementById('mock-vt-field');
    const mockVtBadge = document.getElementById('mock-vt-badge');

    function syncVt() {
        const isEnabled = vtEnabled.checked;
        const isReq = vtRequired.checked;

        // Sub-option ONLY appears if feature is enabled
        vtSubPanel.style.display = isEnabled ? 'block' : 'none';
        if (mockVtField) mockVtField.style.display = isEnabled ? 'block' : 'none';
        if (mockVtBadge) {
            mockVtBadge.textContent = isReq ? '* Wajib' : '(Opsional)';
            mockVtBadge.style.color = isReq ? '#dc2626' : '#64748b';
            mockVtBadge.style.fontWeight = isReq ? '700' : 'normal';
        }
    }
    if (vtEnabled) vtEnabled.addEventListener('change', syncVt);
    if (vtRequired) vtRequired.addEventListener('change', syncVt);

    // 3. Deadline Locking
    const deadlineEnabled = document.getElementById('attendance_deadline_enabled');
    const deadlineDays = document.getElementById('attendance_deadline_days');
    const deadlineTime = document.getElementById('attendance_deadline_time');
    const deadlineSubPanel = document.getElementById('deadline-sub-panel');
    const deadlineSimText = document.getElementById('deadline-sim-text');
    const mockDeadlineAlert = document.getElementById('mock-deadline-alert');
    const mockDeadlineText = document.getElementById('mock-deadline-text');

    function syncDeadline() {
        const isEnabled = deadlineEnabled.checked;
        const days = parseInt(deadlineDays.value, 10);
        const time = deadlineTime.value || '23:59';

        // Sub-option ONLY appears if feature is enabled
        deadlineSubPanel.style.display = isEnabled ? 'block' : 'none';
        if (mockDeadlineAlert) mockDeadlineAlert.style.display = isEnabled ? 'block' : 'none';

        let sim = '';
        if (days === 0) {
            sim = `Contoh: Host <strong>hanya dapat mengisi di hari yang sama</strong> dengan live (cut-off pukul ${time} WIB).`;
            if (mockDeadlineText) mockDeadlineText.textContent = `Hanya Hari H (cut-off ${time} WIB)`;
        } else if (days === 1) {
            sim = `Contoh: Live streaming hari <strong>Senin</strong> dapat diisi maksimal hingga <strong>Selasa pukul ${time} WIB</strong>.`;
            if (mockDeadlineText) mockDeadlineText.textContent = `Maksimal H+1 (cut-off ${time} WIB)`;
        } else if (days === 7) {
            sim = `Contoh: Live streaming hari <strong>Senin</strong> memiliki batas waktu 1 minggu hingga <strong>Senin depan pukul ${time} WIB</strong>.`;
            if (mockDeadlineText) mockDeadlineText.textContent = `Maksimal 1 Minggu`;
        } else {
            sim = `Contoh: Host dapat mengisi absensi maksimal <strong>H+${days} hari</strong> setelah live, hingga pukul ${time} WIB.`;
            if (mockDeadlineText) mockDeadlineText.textContent = `Maksimal H+${days} (cut-off ${time} WIB)`;
        }

        if (deadlineSimText) deadlineSimText.innerHTML = sim;
    }

    if (deadlineEnabled) deadlineEnabled.addEventListener('change', syncDeadline);
    if (deadlineDays) deadlineDays.addEventListener('change', syncDeadline);
    if (deadlineTime) {
        deadlineTime.addEventListener('input', syncDeadline);
        deadlineTime.addEventListener('change', syncDeadline);
    }

    // 4. Preview Toggle Button
    const btnTogglePreview = document.getElementById('btn-toggle-preview');
    const previewWrapper = document.getElementById('policy-preview-wrapper');
    if (btnTogglePreview && previewWrapper) {
        btnTogglePreview.addEventListener('click', function() {
            const isShown = previewWrapper.style.display !== 'none';
            previewWrapper.style.display = isShown ? 'none' : 'block';
            btnTogglePreview.classList.toggle('active', !isShown);
        });
    }

    // Run initial sync
    syncProof();
    syncVt();
    syncDeadline();
});
</script>
@endpush
