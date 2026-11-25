@extends('layouts.app')

@section('title', 'Input Absensi Live')

@push('styles')
<style>
    .attendance-form-page {
        max-width: 580px;
    }

    .attendance-form-page .btn-back-attendance {
        height: 30px !important;
        padding: 0 0.65rem !important;
        font-size: 0.76rem !important;
        font-weight: 600 !important;
        border-radius: 7px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.3rem !important;
        flex-shrink: 0;
    }

    .attendance-form-page .attendance-form-card {
        border-radius: 12px !important;
    }

    .attendance-form-page .attendance-form-card > .card-header {
        padding: 0.75rem 1rem !important;
    }

    .attendance-form-page .attendance-form-card .card-title {
        font-size: 0.98rem !important;
        font-weight: 700 !important;
        line-height: 1.25 !important;
    }

    .attendance-form-page .attendance-form-subtitle {
        font-size: 0.76rem !important;
        margin-top: 1px;
    }

    .attendance-form-page .badge-quick-mode {
        background: rgba(16, 185, 129, 0.12) !important;
        color: #059669 !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        padding: 0.2rem 0.55rem !important;
        border-radius: 999px !important;
        flex-shrink: 0;
    }

    .attendance-form-page .attendance-form-card > .card-body {
        padding: 0.9rem 1rem !important;
    }

    .attendance-form-page .form-group {
        margin-bottom: 0.75rem !important;
    }

    .attendance-form-page .form-label {
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        margin-bottom: 0.3rem !important;
        color: var(--text-primary);
    }

    .attendance-form-page .form-control,
    .attendance-form-page .form-select {
        height: 38px !important;
        padding: 0.4rem 0.75rem !important;
        font-size: 0.85rem !important;
        border-radius: 8px !important;
    }

    .attendance-form-page .form-select {
        padding-right: 2rem !important;
    }

    .attendance-form-page textarea.form-control {
        height: auto !important;
        min-height: 64px !important;
        padding: 0.5rem 0.75rem !important;
    }

    .attendance-form-page input[type="file"].form-control {
        height: auto !important;
        min-height: 38px !important;
        padding: 0.4rem 0.65rem !important;
    }

    /* Platform Selector (2 columns side-by-side, no text wrap) */
    .platform-selector {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .platform-chip {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.55rem 0.65rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--surface);
        transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        min-width: 0;
    }

    .platform-chip:hover {
        border-color: rgba(99, 102, 241, 0.4);
    }

    .platform-chip.active {
        border-color: #4f46e5;
        background: rgba(99, 102, 241, 0.05);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.1);
    }

    .platform-chip[data-platform="tiktok"].active {
        border-color: #0f172a;
        background: rgba(15, 23, 42, 0.04);
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.1);
    }

    .platform-chip[data-platform="shopee"].active {
        border-color: #ea580c;
        background: rgba(234, 88, 12, 0.05);
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.12);
    }

    .platform-chip[data-platform="tokopedia"].active {
        border-color: #16a34a;
        background: rgba(22, 163, 74, 0.05);
    }

    .platform-chip[data-platform="lazada"].active {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }

    .platform-chip-info {
        flex: 1;
        min-width: 0;
    }

    .platform-chip-name {
        font-size: 0.825rem;
        font-weight: 700;
        color: var(--text-primary);
        display: block;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .platform-chip-rate {
        font-size: 0.7rem;
        color: var(--text-muted);
        display: block;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .platform-chip-check {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.18s ease;
        flex-shrink: 0;
    }

    .platform-chip.active .platform-chip-check {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
    }

    .platform-chip[data-platform="tiktok"].active .platform-chip-check {
        background: #0f172a;
        border-color: #0f172a;
    }

    .platform-chip[data-platform="shopee"].active .platform-chip-check {
        background: #ea580c;
        border-color: #ea580c;
    }

    .platform-chip[data-platform="tokopedia"].active .platform-chip-check {
        background: #16a34a;
        border-color: #16a34a;
    }

    .platform-chip[data-platform="lazada"].active .platform-chip-check {
        background: #2563eb;
        border-color: #2563eb;
    }

    /* Sales & Content Compact 3-Column Row */
    .sales-content-grid {
        display: grid;
        grid-template-columns: 1.25fr 0.9fr 0.9fr;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .sales-content-grid .form-group {
        margin-bottom: 0 !important;
    }

    /* Submit Action Buttons */
    .attendance-submit-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .attendance-submit-actions .btn {
        height: 38px !important;
        padding: 0 1rem !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
    }
</style>
@endpush

@section('content')
<div class="attendance-form-page">
    <div class="card attendance-form-card">
        <div class="card-header">
            <div class="flex items-center justify-between gap-2">
                <div style="min-width: 0;">
                    <h3 class="card-title">Input Absensi Live Streaming</h3>
                    <p class="text-muted mb-0 attendance-form-subtitle">Laporkan sesi live per platform/akun Anda</p>
                </div>
                <span class="badge badge-quick-mode">
                    Quick Mode
                </span>
            </div>
        </div>
        <div class="card-body">
            @if(!empty($settings['deadline_enabled']) && !empty($minDate))
                <div class="alert alert-info mb-3" style="padding: 0.5rem 0.75rem; font-size: 0.78rem; border-radius: 7px;">
                    Batas input absensi berlaku: maksimal tanggal <strong>{{ \Carbon\Carbon::parse($minDate)->translatedFormat('d M Y') }}</strong> (H+{{ $settings['deadline_days'] }}).
                </div>
            @endif

            <form action="{{ route('user.attendances.store') }}" method="POST" enctype="multipart/form-data" id="attendanceForm">
                @csrf

                <!-- 1. Pilihan Platform / Marketplace (MP) -->
                <div class="form-group">
                    <label class="form-label">
                        Pilih Platform Live <span class="text-danger">*</span>
                    </label>

                    <input type="hidden" name="platform" id="input_platform" value="{{ old('platform', $selectedPlatform) }}">

                    <div class="platform-selector">
                        @foreach($activeMarketplaces as $mpKey => $mp)
                            <div class="platform-chip {{ old('platform', $selectedPlatform) === $mpKey ? 'active' : '' }}"
                                data-platform="{{ $mpKey }}"
                                data-name="{{ $mp['name'] }}"
                                data-label="{{ $mp['link_label'] ?? 'Link Video / Live' }}"
                                data-placeholder="{{ $mp['link_placeholder'] ?? 'https://...' }}"
                                onclick="selectPlatform('{{ $mpKey }}')">
                                <x-platform-logo :platform="$mpKey" size="sm" />
                                <div class="platform-chip-info">
                                    <span class="platform-chip-name">{{ $mp['name'] }}</span>
                                    <span class="platform-chip-rate">Rp {{ number_format($mp['default_hourly_rate'], 0, ',', '.') }}/jam</span>
                                </div>
                                <div class="platform-chip-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="11" height="11">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('platform')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 2. Tanggal Live -->
                <div class="form-group">
                    <label for="attendance_date" class="form-label">Tanggal Live <span class="text-danger">*</span></label>
                    <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                        value="{{ old('attendance_date', $selectedDate) }}"
                        @if(!empty($minDate)) min="{{ $minDate }}" @endif
                        max="{{ date('Y-m-d') }}" required>
                    @error('attendance_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 3. Durasi Live (Jam) -->
                <div class="form-group">
                    <label for="live_duration_hours" class="form-label">
                        Durasi Live (Jam) <span class="text-danger">*</span>
                    </label>
                    <select name="live_duration_hours" id="live_duration_hours" class="form-control form-select" required>
                        <option value="">Pilih Durasi...</option>
                        @foreach([0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8] as $duration)
                            <option value="{{ $duration }}" {{ old('live_duration_hours', '2') == $duration ? 'selected' : '' }}>
                                {{ $duration }} Jam
                            </option>
                        @endforeach
                    </select>
                    @error('live_duration_hours')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 4. Penjualan & Konten (3 Kolom Sejajar Compact) -->
                <div class="sales-content-grid">
                    <div class="form-group">
                        <label for="sales_count" class="form-label">
                            Penjualan (pcs) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="sales_count" id="sales_count" class="form-control"
                            value="{{ old('sales_count', 0) }}" min="0" required placeholder="0">
                        @error('sales_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content_edit_count" class="form-label">Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count') == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="content_live_count" class="form-label">Konten Live</label>
                        <select name="content_live_count" id="content_live_count" class="form-control form-select">
                            <option value="0" {{ old('content_live_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_live_count') == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 5. Bukti Screenshot -->
                @if(!empty($settings['proof_enabled']))
                    <div class="form-group">
                        <label for="proof_image" class="form-label flex items-center justify-between">
                            <span>
                                Bukti Screenshot Live
                                @if(!empty($settings['proof_required']))
                                    <span class="text-danger">*</span>
                                @else
                                    <span class="text-muted">(Opsional)</span>
                                @endif
                            </span>
                            <span class="text-muted" style="color: #10b981; font-size: 0.72rem; font-weight: 600;">Auto-Compress HD</span>
                        </label>
                        <input type="file" name="proof_image" id="proof_image" class="form-control" accept="image/*"
                            {{ !empty($settings['proof_required']) ? 'required' : '' }}>
                        <div id="proof-preview"></div>
                        @error('proof_image')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <!-- 6. Link Live / Video VT -->
                @if(!empty($settings['vt_link_enabled']))
                    <div class="form-group">
                        <label for="vt_link" class="form-label" id="vt_link_label">
                            Link Live / Video
                            @if(!empty($settings['vt_link_required']))
                                <span class="text-danger">*</span>
                            @else
                                <span class="text-muted">(Opsional)</span>
                            @endif
                        </label>
                        <input type="url" name="vt_link" id="vt_link" class="form-control"
                            placeholder="https://..."
                            value="{{ old('vt_link') }}" {{ !empty($settings['vt_link_required']) ? 'required' : '' }}>
                        @error('vt_link')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <!-- 7. Catatan Opsional -->
                <div class="form-group">
                    <label for="notes" class="form-label">Catatan Kendala / Info (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"
                        placeholder="Catatan kendala sinyal, target, atau info live...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 8. Submit Action Buttons -->
                <input type="hidden" name="action" id="form_action" value="save">

                <div class="attendance-submit-actions">
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('form_action').value='save'" style="flex: 1; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Absensi</span>
                    </button>

                    @if(count($activeMarketplaces) > 1)
                        <button type="submit" class="btn btn-secondary" onclick="document.getElementById('form_action').value='save_and_next'"
                            style="flex: 1.15; justify-content: center; border-color: #ea580c; color: #ea580c; background: rgba(234, 88, 12, 0.05); display: inline-flex; align-items: center; gap: 5px;"
                            title="Simpan absensi ini dan langsung buka form untuk platform lainnya">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Simpan &amp; Input Platform Lain</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/image-compressor.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const proofInput = document.getElementById('proof_image');
        const proofPreview = document.getElementById('proof-preview');
        if (proofInput && window.ImageCompressor) {
            window.ImageCompressor.attachToFileInput(proofInput, proofPreview);
        }

        // Initialize platform labels
        const activeChip = document.querySelector('.platform-chip.active');
        if (activeChip) {
            updatePlatformContext(activeChip);
        }
    });

    function selectPlatform(platformKey) {
        document.getElementById('input_platform').value = platformKey;
        document.querySelectorAll('.platform-chip').forEach(chip => {
            chip.classList.toggle('active', chip.dataset.platform === platformKey);
            if (chip.dataset.platform === platformKey) {
                updatePlatformContext(chip);
            }
        });
    }

    function updatePlatformContext(chip) {
        const labelEl = document.getElementById('vt_link_label');
        const inputEl = document.getElementById('vt_link');
        if (labelEl && chip.dataset.label) {
            labelEl.innerHTML = `${chip.dataset.label} <span class="text-muted">(Opsional)</span>`;
        }
        if (inputEl && chip.dataset.placeholder) {
            inputEl.placeholder = chip.dataset.placeholder;
        }
    }
</script>
@endpush
