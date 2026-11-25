@extends('layouts.app')

@section('title', 'Edit Absensi')

@push('styles')
<style>
    .platform-selector {
        display: flex;
        gap: 0.6rem;
        flex-wrap: wrap;
        margin-bottom: 0.85rem;
    }
    .platform-chip {
        flex: 1;
        min-width: 130px;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 0.65rem 0.85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        background: var(--surface);
        transition: all 0.2s ease;
        user-select: none;
    }
    .platform-chip.active {
        border-color: #4f46e5;
        background: rgba(99, 102, 241, 0.05);
    }
    .platform-chip[data-platform="tiktok"].active {
        border-color: #0f172a;
        background: rgba(15, 23, 42, 0.05);
    }
    .platform-chip[data-platform="shopee"].active {
        border-color: #ea580c;
        background: rgba(234, 88, 12, 0.06);
    }
    .platform-chip-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .platform-chip-info {
        flex: 1;
        overflow: hidden;
    }
    .platform-chip-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-primary);
        display: block;
    }
    .platform-chip-check {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid var(--border);
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
</style>
@endpush

@section('content')
    <div class="mb-3">
        <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary btn-sm" style="height: 32px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header" style="padding: 0.75rem 1rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 700;">Edit Absensi</h3>
            <p class="text-xs text-muted mb-0">Ubah data absensi yang masih berstatus pending</p>
        </div>
        <div class="card-body" style="padding: 1.15rem;">
            @php
                $currentHours = $attendance->live_duration_minutes / 60;
                $currentHours = round($currentHours * 2) / 2;
                $currentPlatform = old('platform', $attendance->platform ?? 'tiktok');
            @endphp

            <form action="{{ route('user.attendances.update', $attendance) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Platform Selection -->
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600;">Platform Live <span class="text-danger">*</span></label>
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

                <div class="form-row mb-3">
                    <div class="form-group mb-0" style="flex: 1.2;">
                        <label for="attendance_date" class="form-label" style="font-size: 0.8rem; font-weight: 600;">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                            value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}"
                            @if(!empty($minDate)) min="{{ $minDate }}" @endif
                            max="{{ date('Y-m-d') }}" required>
                        @error('attendance_date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="session_name" class="form-label" style="font-size: 0.8rem; font-weight: 600;">Sesi / Shift <span class="text-danger">*</span></label>
                        <select name="session_name" id="session_name" class="form-control form-select" required>
                            <option value="Sesi 1" {{ old('session_name', $attendance->session_name ?? 'Sesi 1') == 'Sesi 1' ? 'selected' : '' }}>Sesi 1 (Pagi)</option>
                            <option value="Sesi 2" {{ old('session_name', $attendance->session_name) == 'Sesi 2' ? 'selected' : '' }}>Sesi 2 (Siang/Sore)</option>
                            <option value="Sesi 3" {{ old('session_name', $attendance->session_name) == 'Sesi 3' ? 'selected' : '' }}>Sesi 3 (Malam)</option>
                            <option value="Sesi 4" {{ old('session_name', $attendance->session_name) == 'Sesi 4' ? 'selected' : '' }}>Sesi 4 (Subuh/Lembur)</option>
                        </select>
                        @error('session_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="live_duration_hours" class="form-label" style="font-size: 0.8rem; font-weight: 600;">Durasi Live (Jam) <span class="text-danger">*</span></label>
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

                <div class="form-row mb-3">
                    <div class="form-group mb-0" style="flex: 1.2;">
                        <label for="sales_count" class="form-label" style="font-size: 0.8rem; font-weight: 600;">Jumlah Penjualan (pcs) <span class="text-danger">*</span></label>
                        <input type="number" name="sales_count" id="sales_count" class="form-control"
                            value="{{ old('sales_count', $attendance->sales_count) }}" min="0" required>
                        @error('sales_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="content_edit_count" class="form-label" style="font-size: 0.8rem;">Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', $attendance->content_edit_count) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count', $attendance->content_edit_count) == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="content_live_count" class="form-label" style="font-size: 0.8rem;">Konten Live</label>
                        <select name="content_live_count" id="content_live_count" class="form-control form-select">
                            <option value="0" {{ old('content_live_count', $attendance->content_live_count) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_live_count', $attendance->content_live_count) == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(!empty($settings['proof_enabled']))
                    <div class="form-group mb-3">
                        <label for="proof_image" class="form-label flex items-center justify-between" style="font-size: 0.8rem; font-weight: 600;">
                            <span>
                                Bukti Screenshot (Proof of Work)
                                @if(!empty($settings['proof_required']) && empty($attendance->proof_image))
                                    <span class="text-danger">*</span>
                                @else
                                    <span class="text-muted">(Opsional)</span>
                                @endif
                            </span>
                            <span class="text-xs text-muted" style="color: #10b981; font-weight: 600;">Auto-Compress HD</span>
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
                    <div class="form-group mb-3">
                        <label for="vt_link" class="form-label" id="vt_link_label" style="font-size: 0.8rem; font-weight: 600;">
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

                <div class="form-group mb-3">
                    <label for="notes" class="form-label" style="font-size: 0.8rem;">Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes', $attendance->notes) }}</textarea>
                </div>

                <div class="flex items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 1.25rem; font-size: 0.85rem; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary" style="height: 38px; padding: 0 1rem; font-size: 0.85rem;">Batal</a>
                </div>
            </form>
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
