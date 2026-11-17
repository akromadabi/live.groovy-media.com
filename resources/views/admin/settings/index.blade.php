@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div style="max-width: 860px;">
        <!-- Attendance Form & Policy Settings -->
        <div class="card mb-3 policy-main-card">
            <div class="card-header d-flex items-center justify-between flex-wrap gap-2" style="padding: 0.85rem 1.15rem;">
                <div class="flex items-center gap-2.5">
                    <div class="policy-header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title mb-0" style="font-size: 1rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;">
                            Pengaturan Formulir & Kebijakan Absensi
                        </h3>
                        <p class="text-xs text-muted mb-0" style="margin-top: 2px;">
                            Atur fitur dinamis formulir absensi host serta batasan waktu (cut-off) pengisian
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="btn-toggle-preview" class="btn btn-outline-primary btn-sm policy-preview-toggle-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Preview Form Host</span>
                        <span class="policy-indicator-dot"></span>
                    </button>
                </div>
            </div>

            <div class="card-body" style="padding: 1.15rem;">
                <form action="{{ route('admin.settings.update-system') }}" method="POST" id="attendance-policy-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="setting_group" value="attendance">

                    <!-- ========================================================
                         1. BUKTI SCREENSHOT (PROOF OF WORK)
                         ======================================================== -->
                    <div class="policy-card mb-3" id="proof-policy-card">
                        <div class="policy-card-top flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="policy-icon-box policy-icon-indigo">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="policy-title">Bukti Screenshot (Proof of Work)</span>
                                        <span class="policy-chip-sm chip-emerald">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Auto-Compress HD
                                        </span>
                                    </div>
                                    <p class="policy-desc mb-0">
                                        Foto bukti sesi live otomatis dikompresi WebP/JPEG tanpa pecah sebelum diupload (hemat storage server & kuota host).
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="policy-badge" id="proof-status-badge"></span>
                                <label class="policy-switch policy-switch-indigo" title="Aktifkan atau sembunyikan kolom upload screenshot di form host">
                                    <input type="checkbox" name="attendance_proof_enabled" id="attendance_proof_enabled" value="1"
                                        {{ ($settings['attendance_proof_enabled'] ?? true) ? 'checked' : '' }}>
                                    <span class="policy-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Sub-option: Wajib vs Opsional -->
                        <div class="policy-sub-panel" id="proof-sub-panel">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div>
                                    <span class="policy-sub-label">Sifat Pengisian Field Bukti:</span>
                                    <p class="policy-sub-desc mb-0">
                                        Tentukan apakah host dapat menyimpan absensi tanpa menyertakan foto screenshot.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="policy-badge" id="proof-required-badge"></span>
                                    <label class="policy-switch policy-switch-amber" title="Centang jika wajib diisi">
                                        <input type="checkbox" name="attendance_proof_required" id="attendance_proof_required" value="1"
                                            {{ ($settings['attendance_proof_required'] ?? false) ? 'checked' : '' }}>
                                        <span class="policy-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="policy-disabled-notice" id="proof-disabled-notice" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                            <span>Field screenshot disembunyikan sepenuhnya dari formulir absensi host.</span>
                        </div>
                    </div>

                    <!-- ========================================================
                         2. LINK VIDEO TIKTOK (VT)
                         ======================================================== -->
                    <div class="policy-card mb-3" id="vt-policy-card">
                        <div class="policy-card-top flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="policy-icon-box policy-icon-sky">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="policy-title">Link Video TikTok (VT)</span>
                                        <span class="policy-chip-sm chip-sky">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Verifikasi Konten
                                        </span>
                                    </div>
                                    <p class="policy-desc mb-0">
                                        Host melampirkan tautan URL video TikTok hasil editan atau live stream untuk rekapitulasi performa.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="policy-badge" id="vt-status-badge"></span>
                                <label class="policy-switch policy-switch-sky" title="Aktifkan atau sembunyikan kolom input link video TikTok di form host">
                                    <input type="checkbox" name="attendance_vt_link_enabled" id="attendance_vt_link_enabled" value="1"
                                        {{ ($settings['attendance_vt_link_enabled'] ?? true) ? 'checked' : '' }}>
                                    <span class="policy-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Sub-option: Wajib vs Opsional -->
                        <div class="policy-sub-panel" id="vt-sub-panel">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div>
                                    <span class="policy-sub-label">Sifat Pengisian Field Link Video:</span>
                                    <p class="policy-sub-desc mb-0">
                                        Tentukan apakah host wajib menyertakan link VT sebelum absensi dapat disimpan.
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="policy-badge" id="vt-required-badge"></span>
                                    <label class="policy-switch policy-switch-amber" title="Centang jika wajib diisi">
                                        <input type="checkbox" name="attendance_vt_link_required" id="attendance_vt_link_required" value="1"
                                            {{ ($settings['attendance_vt_link_required'] ?? false) ? 'checked' : '' }}>
                                        <span class="policy-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="policy-disabled-notice" id="vt-disabled-notice" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                            <span>Field link TikTok disembunyikan sepenuhnya dari formulir absensi host.</span>
                        </div>
                    </div>

                    <!-- ========================================================
                         3. BATAS WAKTU PENGISIAN (DEADLINE LOCKING)
                         ======================================================== -->
                    <div class="policy-card mb-3" id="deadline-policy-card">
                        <div class="policy-card-top flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="policy-icon-box policy-icon-amber">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="22" height="22">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="policy-title">Batas Waktu Pengisian (Deadline Locking)</span>
                                        <span class="policy-chip-sm chip-amber">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Kunci Otomatis
                                        </span>
                                    </div>
                                    <p class="policy-desc mb-0">
                                        Mencegah host mengisi atau mengubah absensi tanggal lampau yang sudah melewati batas toleransi tanpa izin admin.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="policy-badge" id="deadline-status-badge"></span>
                                <label class="policy-switch policy-switch-amber" title="Aktifkan atau nonaktifkan batasan waktu pengisian absensi">
                                    <input type="checkbox" name="attendance_deadline_enabled" id="attendance_deadline_enabled" value="1"
                                        {{ ($settings['attendance_deadline_enabled'] ?? false) ? 'checked' : '' }}>
                                    <span class="policy-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Active State Configuration -->
                        <div class="policy-sub-panel" id="deadline-sub-panel">
                            <!-- Quick Preset Chips -->
                            <div class="mb-3">
                                <label class="form-label" style="font-size: 0.78rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.35rem; display: block;">
                                    ⚡ Pilihan Cepat Toleransi Deadline:
                                </label>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" class="policy-chip-btn" data-days="0">
                                        Hari H Saja (0 hari)
                                    </button>
                                    <button type="button" class="policy-chip-btn" data-days="1">
                                        ⭐ Maks H+1 (Standar)
                                    </button>
                                    <button type="button" class="policy-chip-btn" data-days="2">
                                        Maks H+2 (2 hari)
                                    </button>
                                    <button type="button" class="policy-chip-btn" data-days="3">
                                        Maks H+3 (3 hari)
                                    </button>
                                    <button type="button" class="policy-chip-btn" data-days="7">
                                        1 Minggu (7 hari)
                                    </button>
                                </div>
                            </div>

                            <!-- Inputs Grid -->
                            <div class="grid grid-2 gap-3">
                                <div class="form-group mb-0">
                                    <label for="attendance_deadline_days" class="form-label" style="font-size: 0.78rem; font-weight: 600;">
                                        Toleransi Hari Mundur
                                    </label>
                                    <select name="attendance_deadline_days" id="attendance_deadline_days" class="form-control form-select">
                                        <option value="0" {{ ($settings['attendance_deadline_days'] ?? 1) == 0 ? 'selected' : '' }}>
                                            Hanya Hari H (Maksimal hari yang sama)
                                        </option>
                                        <option value="1" {{ ($settings['attendance_deadline_days'] ?? 1) == 1 ? 'selected' : '' }}>
                                            Maksimal H+1 (Contoh: Live Senin, batas Selasa)
                                        </option>
                                        <option value="2" {{ ($settings['attendance_deadline_days'] ?? 1) == 2 ? 'selected' : '' }}>
                                            Maksimal H+2 (Batas 2 hari berikutnya)
                                        </option>
                                        <option value="3" {{ ($settings['attendance_deadline_days'] ?? 1) == 3 ? 'selected' : '' }}>
                                            Maksimal H+3 (Batas 3 hari berikutnya)
                                        </option>
                                        <option value="7" {{ ($settings['attendance_deadline_days'] ?? 1) == 7 ? 'selected' : '' }}>
                                            Maksimal 1 Minggu (7 Hari)
                                        </option>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.72rem;">Jumlah hari maksimal mundur dari tanggal live.</small>
                                </div>

                                <div class="form-group mb-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <label for="attendance_deadline_time" class="form-label mb-0" style="font-size: 0.78rem; font-weight: 600;">
                                            Jam Cut-off Terakhir
                                        </label>
                                        <div class="flex items-center gap-1">
                                            <button type="button" class="policy-mini-time-btn" data-time="23:59">23:59</button>
                                            <button type="button" class="policy-mini-time-btn" data-time="12:00">12:00</button>
                                        </div>
                                    </div>
                                    <input type="time" name="attendance_deadline_time" id="attendance_deadline_time" class="form-control"
                                        value="{{ $settings['attendance_deadline_time'] ?? '23:59' }}">
                                    <small class="text-muted" style="font-size: 0.72rem;">Form terkunci tepat setelah jam cut-off ini.</small>
                                </div>
                            </div>

                            <!-- Interactive Live Simulation Box -->
                            <div class="policy-simulation-card mt-3">
                                <div class="flex items-start gap-2.5">
                                    <div class="policy-sim-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div style="flex: 1;">
                                        <span class="policy-sim-heading">Simulasi Nyata Kebijakan Ini:</span>
                                        <p class="policy-sim-text mb-0" id="deadline-sim-text">
                                            Memuat simulasi...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inactive State Notice -->
                        <div class="policy-disabled-notice" id="deadline-disabled-notice" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15" style="color: #10b981;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Mode Pengisian Bebas Aktif:</strong> Host bebas mengisi atau mengedit absensi untuk tanggal kapan pun tanpa batasan cut-off waktu.</span>
                        </div>
                    </div>

                    <!-- ========================================================
                         LIVE HOST FORM PREVIEW (DRAWER / COLLAPSIBLE MOCKUP)
                         ======================================================== -->
                    <div class="policy-preview-section mb-3" id="policy-preview-wrapper" style="display: none;">
                        <div class="policy-preview-box">
                            <div class="policy-preview-header flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="policy-mobile-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary); display: block;">
                                            Simulasi Tampilan Formulir di Akun Host
                                        </span>
                                        <span class="text-muted" style="font-size: 0.72rem;">
                                            Tampilan preview interaktif di bawah merespons langsung opsi yang Anda atur di atas
                                        </span>
                                    </div>
                                </div>
                                <span class="policy-chip-sm chip-indigo">Live Mockup</span>
                            </div>

                            <div class="policy-mockup-body">
                                <!-- Mockup Alert (Deadline Warning) -->
                                <div class="policy-mock-alert mb-3" id="mock-deadline-alert" style="display: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" style="flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span id="mock-deadline-text">Batas input absensi berlaku: maksimal tanggal ...</span>
                                </div>

                                <div class="grid grid-2 gap-2 mb-2">
                                    <div class="policy-mock-input">
                                        <span class="policy-mock-label">Tanggal Live *</span>
                                        <div class="policy-mock-field">{{ date('d M Y') }}</div>
                                    </div>
                                    <div class="policy-mock-input">
                                        <span class="policy-mock-label">Sesi / Shift *</span>
                                        <div class="policy-mock-field">Sesi 1 (Pagi) ▾</div>
                                    </div>
                                </div>

                                <div class="grid grid-2 gap-2 mb-2">
                                    <div class="policy-mock-input">
                                        <span class="policy-mock-label">Durasi Live *</span>
                                        <div class="policy-mock-field">3.0 Jam ▾</div>
                                    </div>
                                    <div class="policy-mock-input">
                                        <span class="policy-mock-label">Penjualan (pcs) *</span>
                                        <div class="policy-mock-field">0</div>
                                    </div>
                                </div>

                                <!-- Mockup Bukti Screenshot -->
                                <div class="policy-mock-field-card mb-2" id="mock-proof-field">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="policy-mock-label mb-0 flex items-center gap-1.5">
                                            <span>📸 Bukti Screenshot (Proof of Work)</span>
                                            <span id="mock-proof-badge" class="policy-mock-tag"></span>
                                        </span>
                                        <span class="text-xs" style="color: #10b981; font-size: 0.68rem; font-weight: 600;">⚡ Auto-Compress HD</span>
                                    </div>
                                    <div class="policy-mock-dropzone">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20" style="color: var(--text-muted); margin-bottom: 2px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span style="font-size: 0.72rem; color: var(--text-secondary);">Klik untuk pilih foto screenshot live</span>
                                    </div>
                                </div>

                                <!-- Mockup Link Video TikTok -->
                                <div class="policy-mock-field-card mb-2" id="mock-vt-field">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="policy-mock-label mb-0 flex items-center gap-1.5">
                                            <span>🔗 Link Video TikTok (VT)</span>
                                            <span id="mock-vt-badge" class="policy-mock-tag"></span>
                                        </span>
                                    </div>
                                    <div class="policy-mock-field text-muted" style="font-size: 0.72rem;">
                                        https://vt.tiktok.com/ZS...
                                    </div>
                                </div>

                                <!-- Mockup Submit Button -->
                                <div class="policy-mock-btn-row mt-3">
                                    <div class="policy-mock-submit">
                                        Simpan Absensi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========================================================
                         ACTION BUTTONS
                         ======================================================== -->
                    <div class="policy-action-bar flex items-center justify-between flex-wrap gap-2 pt-2">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn btn-primary policy-save-btn" id="btn-save-attendance-policy">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="17" height="17">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Kebijakan Absensi</span>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.reload();" style="height: 38px; font-size: 0.85rem;">
                                Batal
                            </button>
                        </div>
                        <span class="text-muted" style="font-size: 0.75rem;">
                            💡 Kebijakan langsung diterapkan ke seluruh host saat membuka formulir absensi.
                        </span>
                    </div>
                </form>
            </div>
        </div>

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
                            Simpan Pengaturan WhatsApp
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
