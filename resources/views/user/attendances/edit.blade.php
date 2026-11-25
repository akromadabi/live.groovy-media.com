@extends('layouts.app')

@section('title', 'Edit Absensi')

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
        transition: all 0.18s ease;
        user-select: none;
        min-width: 0;
    }

    .platform-chip.active {
        border-color: #4f46e5;
        background: rgba(99, 102, 241, 0.05);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.1);
    }

    .platform-chip[data-platform="tiktok"].active {
        border-color: #0f172a;
        background: rgba(15, 23, 42, 0.04);
    }

    .platform-chip[data-platform="shopee"].active {
        border-color: #ea580c;
        background: rgba(234, 88, 12, 0.05);
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
            <h3 class="card-title">Edit Absensi</h3>
            <p class="text-muted mb-0 attendance-form-subtitle">Ubah data absensi yang masih berstatus pending</p>
        </div>
        <div class="card-body">
            @php
                $currentHours = $attendance->live_duration_minutes / 60;
                $currentHours = round($currentHours * 2) / 2;
                $currentPlatform = old('platform', $attendance->platform ?? 'tiktok');
            @endphp

            <form action="{{ route('user.attendances.update', $attendance) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Platform Selection -->
                <div class="form-group">
                    <label class="form-label">Platform Live <span class="text-danger">*</span></label>
                    <input type="hidden" name="platform" id="input_platform" value="{{ $currentPlatform }}">
                    <div class="platform-selector">
                        @foreach($activeMarketplaces as $mpKey => $mp)
                            <div class="platform-chip {{ $currentPlatform === $mpKey ? 'active' : '' }}"
                                data-platform="{{ $mpKey }}"
                                data-label="{{ $mp['link_label'] ?? 'Link Video / Live' }}"
                                onclick="selectPlatform('{{ $mpKey }}')">
                                <x-platform-logo :platform="$mpKey" size="sm" />
                                <div class="platform-chip-info">
                                    <span class="platform-chip-name">{{ $mp['name'] }}</span>
                                </div>
                                <div class="platform-chip-check">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="11" height="11">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label for="attendance_date" class="form-label">Tanggal Live <span class="text-danger">*</span></label>
                    <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                        value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}"
                        @if(!empty($minDate)) min="{{ $minDate }}" @endif
                        max="{{ date('Y-m-d') }}" required>
                    @error('attendance_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="live_duration_hours" class="form-label">Durasi Live (Jam) <span class="text-danger">*</span></label>
                    <select name="live_duration_hours" id="live_duration_hours" class="form-control form-select" required>
                        <option value="">Pilih Durasi</option>
                        @foreach([0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8] as $duration)
                            <option value="{{ $duration }}" {{ old('live_duration_hours', $currentHours) == $duration ? 'selected' : '' }}>
                                {{ $duration }} Jam
                            </option>
                        @endforeach
                    </select>
                    @error('live_duration_hours')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="sales-content-grid">
                    <div class="form-group">
                        <label for="sales_count" class="form-label">Penjualan (pcs) <span class="text-danger">*</span></label>
                        <input type="number" name="sales_count" id="sales_count" class="form-control"
                            value="{{ old('sales_count', $attendance->sales_count) }}" min="0" required>
                        @error('sales_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="content_edit_count" class="form-label">Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', $attendance->content_edit_count) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count', $attendance->content_edit_count) == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="content_live_count" class="form-label">Konten Live</label>
                        <select name="content_live_count" id="content_live_count" class="form-control form-select">
                            <option value="0" {{ old('content_live_count', $attendance->content_live_count) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_live_count', $attendance->content_live_count) == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(!empty($settings['proof_enabled']))
                    <div class="form-group">
                        <label for="proof_image" class="form-label flex items-center justify-between">
                            <span>
                                Bukti Screenshot (Proof of Work)
                                @if(!empty($settings['proof_required']) && empty($attendance->proof_image))
                                    <span class="text-danger">*</span>
                                @else
                                    <span class="text-muted">(Opsional)</span>
                                @endif
                            </span>
                            <span class="text-muted" style="color: #10b981; font-size: 0.72rem; font-weight: 600;">Auto-Compress HD</span>
                        </label>

                        @if($attendance->proof_image)
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; padding: 6px 10px; background: var(--bg-hover); border-radius: 6px;">
                                <img src="{{ $attendance->proof_image_url }}" alt="Bukti" style="width: 36px; height: 36px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Foto bukti tersimpan saat ini. Pilih file baru jika ingin mengganti.</span>
                            </div>
                        @endif

                        <input type="file" name="proof_image" id="proof_image" class="form-control" accept="image/*"
                            {{ (!empty($settings['proof_required']) && empty($attendance->proof_image)) ? 'required' : '' }}>
                        <div id="proof-preview"></div>
                        @error('proof_image')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if(!empty($settings['vt_link_enabled']))
                    <div class="form-group">
                        <label for="vt_link" class="form-label" id="vt_link_label">
                            Link Video / Live
                            @if(!empty($settings['vt_link_required']))
                                <span class="text-danger">*</span>
                            @else
                                <span class="text-muted">(Opsional)</span>
                            @endif
                        </label>
                        <input type="url" name="vt_link" id="vt_link" class="form-control"
                            placeholder="https://..."
                            value="{{ old('vt_link', $attendance->vt_link) }}" {{ !empty($settings['vt_link_required']) ? 'required' : '' }}>
                        @error('vt_link')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes', $attendance->notes) }}</textarea>
                </div>

                <div class="attendance-submit-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
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
    });

    function selectPlatform(platformKey) {
        document.getElementById('input_platform').value = platformKey;
        document.querySelectorAll('.platform-chip').forEach(chip => {
            chip.classList.toggle('active', chip.dataset.platform === platformKey);
        });
    }
</script>
@endpush
