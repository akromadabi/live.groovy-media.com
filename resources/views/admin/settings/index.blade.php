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
                    <input type="hidden" name="setting_group" value="whatsapp">

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
                    <input type="hidden" name="setting_group" value="app">

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

@push('styles')
<style>
    /* ==========================================================
       MODERN POLICY SETTINGS STYLING
       ========================================================== */
    .policy-main-card {
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .policy-header-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .policy-preview-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        height: 32px;
        padding: 0 0.85rem;
        font-size: 0.78rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid rgba(99, 102, 241, 0.35);
        color: #4f46e5;
        background: rgba(99, 102, 241, 0.06);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .policy-preview-toggle-btn:hover,
    .policy-preview-toggle-btn.active {
        background: #4f46e5;
        color: #ffffff;
        border-color: #4f46e5;
    }
    .policy-preview-toggle-btn.active .policy-indicator-dot {
        background: #ffffff;
    }
    .policy-indicator-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        animation: pulseDot 2s infinite;
    }
    @keyframes pulseDot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.85); }
    }

    /* Policy Card Container */
    .policy-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.15rem 1.25rem;
        background: var(--surface);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .policy-card:hover {
        border-color: rgba(99, 102, 241, 0.45);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .policy-card.is-disabled {
        background: var(--bg-secondary);
        opacity: 0.85;
    }

    /* Policy Icons */
    .policy-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .policy-card:hover .policy-icon-box {
        transform: scale(1.05);
    }
    .policy-icon-indigo {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(79, 70, 229, 0.08));
        color: #4f46e5;
    }
    .policy-icon-sky {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(2, 132, 199, 0.08));
        color: #0284c7;
    }
    .policy-icon-amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.08));
        color: #d97706;
    }

    .policy-title {
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--text-primary);
        line-height: 1.3;
    }
    .policy-desc {
        font-size: 0.77rem;
        color: var(--text-secondary);
        line-height: 1.45;
        margin-top: 3px;
        max-width: 620px;
    }

    /* Chips */
    .policy-chip-sm {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.15rem 0.5rem;
        font-size: 0.68rem;
        font-weight: 600;
        border-radius: 9999px;
        letter-spacing: 0.01em;
    }
    .chip-emerald {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }
    .chip-sky {
        background: rgba(14, 165, 233, 0.12);
        color: #0284c7;
    }
    .chip-amber {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
    }
    .chip-indigo {
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
    }

    /* iOS-Style Modern Toggle Switch */
    .policy-switch {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        margin-bottom: 0;
    }
    .policy-switch input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        margin: 0;
    }
    .policy-switch-slider {
        position: relative;
        width: 46px;
        height: 25px;
        background-color: #cbd5e1;
        border-radius: 25px;
        transition: background-color 0.25s ease, box-shadow 0.25s ease;
        flex-shrink: 0;
        display: inline-block;
    }
    [data-theme="dark"] .policy-switch-slider {
        background-color: #475569;
    }
    .policy-switch-slider::before {
        content: '';
        position: absolute;
        width: 19px;
        height: 19px;
        left: 3px;
        top: 3px;
        background-color: #ffffff;
        border-radius: 50%;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .policy-switch input:checked + .policy-switch-slider::before {
        transform: translateX(21px);
    }
    .policy-switch-indigo input:checked + .policy-switch-slider {
        background-color: #4f46e5;
    }
    .policy-switch-sky input:checked + .policy-switch-slider {
        background-color: #0284c7;
    }
    .policy-switch-amber input:checked + .policy-switch-slider {
        background-color: #f59e0b;
    }
    .policy-switch input:focus-visible + .policy-switch-slider {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.35);
    }

    /* Status Badges */
    .policy-badge {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        letter-spacing: 0.01em;
        transition: all 0.2s ease;
    }
    .policy-badge-active {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }
    .policy-badge-inactive {
        background: rgba(148, 163, 184, 0.16);
        color: var(--text-muted);
    }
    .policy-badge-required {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .policy-badge-optional {
        background: rgba(99, 102, 241, 0.09);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    /* Sub-panels */
    .policy-sub-panel {
        margin-top: 0.9rem;
        padding-top: 0.85rem;
        border-top: 1px dashed var(--border);
        transition: all 0.25s ease;
    }
    .policy-sub-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-primary);
        display: block;
    }
    .policy-sub-desc {
        font-size: 0.73rem;
        color: var(--text-secondary);
        margin-top: 1px;
    }
    .policy-disabled-notice {
        margin-top: 0.75rem;
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        font-size: 0.75rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Preset Chip Buttons */
    .policy-chip-btn {
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.35rem 0.75rem;
        font-size: 0.74rem;
        font-weight: 500;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .policy-chip-btn:hover {
        border-color: #4f46e5;
        color: #4f46e5;
        background: rgba(99, 102, 241, 0.05);
    }
    .policy-chip-btn.active {
        border-color: #4f46e5;
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        font-weight: 700;
        box-shadow: 0 0 0 1px #4f46e5;
    }

    /* Mini Time Quick Buttons */
    .policy-mini-time-btn {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: 0.12rem 0.45rem;
        font-size: 0.68rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .policy-mini-time-btn:hover {
        border-color: #4f46e5;
        color: #4f46e5;
        background: rgba(99, 102, 241, 0.08);
    }

    /* Real-Time Simulation Card */
    .policy-simulation-card {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.06), rgba(245, 158, 11, 0.06));
        border: 1px solid rgba(99, 102, 241, 0.22);
        border-radius: 10px;
        padding: 0.85rem 1rem;
    }
    [data-theme="dark"] .policy-simulation-card {
        background: rgba(30, 41, 59, 0.7);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .policy-sim-icon {
        color: #4f46e5;
        margin-top: 1px;
        flex-shrink: 0;
    }
    .policy-sim-heading {
        font-size: 0.78rem;
        font-weight: 700;
        color: #4f46e5;
        display: block;
        margin-bottom: 2px;
    }
    .policy-sim-text {
        font-size: 0.75rem;
        color: var(--text-primary);
        line-height: 1.5;
    }

    /* Live Host Preview Mockup Section */
    .policy-preview-section {
        background: var(--bg-secondary);
        border: 1.5px dashed rgba(99, 102, 241, 0.4);
        border-radius: 14px;
        padding: 1.15rem;
        animation: fadeInDown 0.25s ease;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .policy-preview-box {
        max-width: 480px;
        margin: 0 auto;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }
    .policy-preview-header {
        background: var(--bg-primary);
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
    }
    .policy-mobile-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .policy-mockup-body {
        padding: 1rem;
    }
    .policy-mock-alert {
        background: rgba(14, 165, 233, 0.1);
        border: 1px solid rgba(14, 165, 233, 0.25);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.72rem;
        color: #0369a1;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    [data-theme="dark"] .policy-mock-alert {
        color: #38bdf8;
    }
    .policy-mock-input {
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 0.4rem 0.65rem;
    }
    .policy-mock-label {
        font-size: 0.68rem;
        font-weight: 600;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 2px;
    }
    .policy-mock-field {
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--text-primary);
    }
    .policy-mock-field-card {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.65rem 0.75rem;
        background: var(--surface);
    }
    .policy-mock-tag {
        font-size: 0.65rem;
        font-weight: 600;
        padding: 0.1rem 0.4rem;
        border-radius: 4px;
    }
    .tag-required {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
    }
    .tag-optional {
        background: rgba(148, 163, 184, 0.15);
        color: var(--text-muted);
    }
    .policy-mock-dropzone {
        border: 1.5px dashed var(--border);
        border-radius: 6px;
        padding: 0.65rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--bg-primary);
    }
    .policy-mock-submit {
        background: #4f46e5;
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 600;
        text-align: center;
        padding: 0.5rem;
        border-radius: 6px;
        opacity: 0.9;
    }

    /* Save Button */
    .policy-save-btn {
        height: 38px;
        padding: 0 1.35rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }
    .policy-save-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Proof Screenshot Elements
    const proofEnabled = document.getElementById('attendance_proof_enabled');
    const proofRequired = document.getElementById('attendance_proof_required');
    const proofCard = document.getElementById('proof-policy-card');
    const proofSubPanel = document.getElementById('proof-sub-panel');
    const proofDisabledNotice = document.getElementById('proof-disabled-notice');
    const proofStatusBadge = document.getElementById('proof-status-badge');
    const proofRequiredBadge = document.getElementById('proof-required-badge');
    const mockProofField = document.getElementById('mock-proof-field');
    const mockProofBadge = document.getElementById('mock-proof-badge');

    // 2. Video Link Elements
    const vtEnabled = document.getElementById('attendance_vt_link_enabled');
    const vtRequired = document.getElementById('attendance_vt_link_required');
    const vtCard = document.getElementById('vt-policy-card');
    const vtSubPanel = document.getElementById('vt-sub-panel');
    const vtDisabledNotice = document.getElementById('vt-disabled-notice');
    const vtStatusBadge = document.getElementById('vt-status-badge');
    const vtRequiredBadge = document.getElementById('vt-required-badge');
    const mockVtField = document.getElementById('mock-vt-field');
    const mockVtBadge = document.getElementById('mock-vt-badge');

    // 3. Deadline Locking Elements
    const deadlineEnabled = document.getElementById('attendance_deadline_enabled');
    const deadlineDays = document.getElementById('attendance_deadline_days');
    const deadlineTime = document.getElementById('attendance_deadline_time');
    const deadlineCard = document.getElementById('deadline-policy-card');
    const deadlineSubPanel = document.getElementById('deadline-sub-panel');
    const deadlineDisabledNotice = document.getElementById('deadline-disabled-notice');
    const deadlineStatusBadge = document.getElementById('deadline-status-badge');
    const deadlineSimText = document.getElementById('deadline-sim-text');
    const mockDeadlineAlert = document.getElementById('mock-deadline-alert');
    const mockDeadlineText = document.getElementById('mock-deadline-text');
    const chipBtns = document.querySelectorAll('.policy-chip-btn');
    const timeBtns = document.querySelectorAll('.policy-mini-time-btn');

    // 4. Preview Drawer Toggle
    const btnTogglePreview = document.getElementById('btn-toggle-preview');
    const previewWrapper = document.getElementById('policy-preview-wrapper');

    // ========================================================
    // BUKTI SCREENSHOT LOGIC
    // ========================================================
    function updateProofUI() {
        const isEnabled = proofEnabled.checked;
        const isReq = proofRequired.checked;

        if (isEnabled) {
            proofCard.classList.remove('is-disabled');
            proofStatusBadge.textContent = 'Tampil di Form';
            proofStatusBadge.className = 'policy-badge policy-badge-active';
            proofSubPanel.style.display = 'block';
            proofDisabledNotice.style.display = 'none';
            proofRequired.disabled = false;
            if (mockProofField) mockProofField.style.display = 'block';
        } else {
            proofCard.classList.add('is-disabled');
            proofStatusBadge.textContent = 'Disembunyikan';
            proofStatusBadge.className = 'policy-badge policy-badge-inactive';
            proofSubPanel.style.display = 'none';
            proofDisabledNotice.style.display = 'flex';
            proofRequired.disabled = true;
            if (mockProofField) mockProofField.style.display = 'none';
        }

        if (isReq) {
            proofRequiredBadge.textContent = '🔒 Wajib Diisi';
            proofRequiredBadge.className = 'policy-badge policy-badge-required';
            if (mockProofBadge) {
                mockProofBadge.textContent = '* Wajib';
                mockProofBadge.className = 'policy-mock-tag tag-required';
            }
        } else {
            proofRequiredBadge.textContent = '⚪ Bersifat Opsional';
            proofRequiredBadge.className = 'policy-badge policy-badge-optional';
            if (mockProofBadge) {
                mockProofBadge.textContent = '(Opsional)';
                mockProofBadge.className = 'policy-mock-tag tag-optional';
            }
        }
    }

    if (proofEnabled) proofEnabled.addEventListener('change', updateProofUI);
    if (proofRequired) proofRequired.addEventListener('change', updateProofUI);

    // ========================================================
    // LINK VT LOGIC
    // ========================================================
    function updateVtUI() {
        const isEnabled = vtEnabled.checked;
        const isReq = vtRequired.checked;

        if (isEnabled) {
            vtCard.classList.remove('is-disabled');
            vtStatusBadge.textContent = 'Tampil di Form';
            vtStatusBadge.className = 'policy-badge policy-badge-active';
            vtSubPanel.style.display = 'block';
            vtDisabledNotice.style.display = 'none';
            vtRequired.disabled = false;
            if (mockVtField) mockVtField.style.display = 'block';
        } else {
            vtCard.classList.add('is-disabled');
            vtStatusBadge.textContent = 'Disembunyikan';
            vtStatusBadge.className = 'policy-badge policy-badge-inactive';
            vtSubPanel.style.display = 'none';
            vtDisabledNotice.style.display = 'flex';
            vtRequired.disabled = true;
            if (mockVtField) mockVtField.style.display = 'none';
        }

        if (isReq) {
            vtRequiredBadge.textContent = '🔒 Wajib Diisi';
            vtRequiredBadge.className = 'policy-badge policy-badge-required';
            if (mockVtBadge) {
                mockVtBadge.textContent = '* Wajib';
                mockVtBadge.className = 'policy-mock-tag tag-required';
            }
        } else {
            vtRequiredBadge.textContent = '⚪ Bersifat Opsional';
            vtRequiredBadge.className = 'policy-badge policy-badge-optional';
            if (mockVtBadge) {
                mockVtBadge.textContent = '(Opsional)';
                mockVtBadge.className = 'policy-mock-tag tag-optional';
            }
        }
    }

    if (vtEnabled) vtEnabled.addEventListener('change', updateVtUI);
    if (vtRequired) vtRequired.addEventListener('change', updateVtUI);

    // ========================================================
    // DEADLINE LOCKING LOGIC & SIMULATION
    // ========================================================
    function updateDeadlineUI() {
        const isEnabled = deadlineEnabled.checked;
        const days = parseInt(deadlineDays.value, 10);
        const time = deadlineTime.value || '23:59';

        if (isEnabled) {
            deadlineCard.classList.remove('is-disabled');
            deadlineStatusBadge.textContent = 'Batas Waktu Aktif';
            deadlineStatusBadge.className = 'policy-badge policy-badge-active';
            deadlineSubPanel.style.display = 'block';
            deadlineDisabledNotice.style.display = 'none';
            if (mockDeadlineAlert) mockDeadlineAlert.style.display = 'flex';
        } else {
            deadlineCard.classList.add('is-disabled');
            deadlineStatusBadge.textContent = 'Pengisian Bebas';
            deadlineStatusBadge.className = 'policy-badge policy-badge-inactive';
            deadlineSubPanel.style.display = 'none';
            deadlineDisabledNotice.style.display = 'flex';
            if (mockDeadlineAlert) mockDeadlineAlert.style.display = 'none';
        }

        // Highlight chip matching current days
        chipBtns.forEach(btn => {
            if (parseInt(btn.getAttribute('data-days'), 10) === days) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Build human-friendly explanation
        let explanation = '';
        if (days === 0) {
            explanation = `Host <strong>hanya dapat mengisi absensi pada hari yang sama</strong> dengan tanggal live stream (cut-off pukul <strong>${time} WIB</strong>). Tanggal kemarin dan seterusnya langsung terkunci.`;
            if (mockDeadlineText) {
                mockDeadlineText.innerHTML = `Batas pengisian: <strong>Hanya hari H live</strong> (cut-off ${time} WIB).`;
            }
        } else if (days === 1) {
            explanation = `Jika live streaming berlangsung hari <strong>Senin</strong>, host memiliki waktu hingga hari <strong>Selasa pukul ${time} WIB</strong> untuk submit absensi. Lewat dari jam ini, form tanggal Senin terkunci otomatis.`;
            if (mockDeadlineText) {
                mockDeadlineText.innerHTML = `Batas input berlaku: <strong>Maksimal H+1</strong> (cut-off ${time} WIB).`;
            }
        } else if (days === 2) {
            explanation = `Jika live streaming berlangsung hari <strong>Senin</strong>, host memiliki toleransi 2 hari hingga hari <strong>Rabu pukul ${time} WIB</strong> untuk submit absensi.`;
            if (mockDeadlineText) {
                mockDeadlineText.innerHTML = `Batas input berlaku: <strong>Maksimal H+2</strong> (cut-off ${time} WIB).`;
            }
        } else if (days === 3) {
            explanation = `Jika live streaming berlangsung hari <strong>Senin</strong>, batas waktu pengisian absensi adalah hingga hari <strong>Kamis pukul ${time} WIB</strong>.`;
            if (mockDeadlineText) {
                mockDeadlineText.innerHTML = `Batas input berlaku: <strong>Maksimal H+3</strong> (cut-off ${time} WIB).`;
            }
        } else if (days === 7) {
            explanation = `Host diberikan toleransi 1 minggu penuh. Jika live berlangsung hari <strong>Senin</strong>, batas akhir submit adalah <strong>Senin pekan berikutnya pukul ${time} WIB</strong>.`;
            if (mockDeadlineText) {
                mockDeadlineText.innerHTML = `Batas input berlaku: <strong>Maksimal 1 Minggu / 7 Hari</strong>.`;
            }
        } else {
            explanation = `Host dapat mengisi absensi maksimal <strong>${days} hari mundur</strong> setelah tanggal live, hingga pukul <strong>${time} WIB</strong>.`;
            if (mockDeadlineText) {
                mockDeadlineText.innerHTML = `Batas input berlaku: <strong>Maksimal H+${days}</strong> (cut-off ${time} WIB).`;
            }
        }

        if (deadlineSimText) {
            deadlineSimText.innerHTML = explanation;
        }
    }

    if (deadlineEnabled) deadlineEnabled.addEventListener('change', updateDeadlineUI);
    if (deadlineDays) deadlineDays.addEventListener('change', updateDeadlineUI);
    if (deadlineTime) {
        deadlineTime.addEventListener('input', updateDeadlineUI);
        deadlineTime.addEventListener('change', updateDeadlineUI);
    }

    // Chip Preset Clicks
    chipBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const daysVal = this.getAttribute('data-days');
            deadlineDays.value = daysVal;
            updateDeadlineUI();
        });
    });

    // Mini Time Preset Clicks
    timeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const timeVal = this.getAttribute('data-time');
            deadlineTime.value = timeVal;
            updateDeadlineUI();
        });
    });

    // ========================================================
    // TOGGLE PREVIEW DRAWER
    // ========================================================
    if (btnTogglePreview && previewWrapper) {
        btnTogglePreview.addEventListener('click', function() {
            const isVisible = previewWrapper.style.display !== 'none';
            if (isVisible) {
                previewWrapper.style.display = 'none';
                btnTogglePreview.classList.remove('active');
            } else {
                previewWrapper.style.display = 'block';
                btnTogglePreview.classList.add('active');
                previewWrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    // Initialize all states on page load
    updateProofUI();
    updateVtUI();
    updateDeadlineUI();

    // Feedback on Save button submit
    const form = document.getElementById('attendance-policy-form');
    const saveBtn = document.getElementById('btn-save-attendance-policy');
    if (form && saveBtn) {
        form.addEventListener('submit', function() {
            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="16" height="16">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyimpan Kebijakan...</span>
            `;
        });
    }
});
</script>
@endpush
