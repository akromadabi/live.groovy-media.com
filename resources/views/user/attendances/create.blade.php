@extends('layouts.app')

@section('title', 'Input Absensi Live')

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
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }
    .platform-chip:hover {
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-1px);
    }
    .platform-chip.active {
        border-color: #4f46e5;
        background: rgba(99, 102, 241, 0.05);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
    }
    .platform-chip[data-platform="tiktok"].active {
        border-color: #0f172a;
        background: rgba(15, 23, 42, 0.05);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.12);
    }
    .platform-chip[data-platform="shopee"].active {
        border-color: #ea580c;
        background: rgba(234, 88, 12, 0.06);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.15);
    }
    .platform-chip[data-platform="tokopedia"].active {
        border-color: #16a34a;
        background: rgba(22, 163, 74, 0.06);
    }
    .platform-chip[data-platform="lazada"].active {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.06);
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
        line-height: 1.2;
    }
    .platform-chip-rate {
        font-size: 0.68rem;
        color: var(--text-muted);
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
        transition: all 0.2s ease;
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

    /* Duration Quick Pills */
    .duration-pills {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        margin-top: 0.4rem;
    }
    .duration-pill {
        border: 1px solid var(--border);
        background: var(--surface);
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        color: var(--text-secondary);
        user-select: none;
    }
    .duration-pill:hover {
        border-color: #4f46e5;
        color: #4f46e5;
    }
    .duration-pill.active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
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
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title" style="font-size: 0.95rem; font-weight: 700;">Input Absensi Live Streaming</h3>
                    <p class="text-xs text-muted mb-0">Laporkan sesi live per platform/akun Anda</p>
                </div>
                <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 0.68rem; font-weight: 600;">
                    Quick Mode
                </span>
            </div>
        </div>
        <div class="card-body" style="padding: 1.15rem;">
            @if(!empty($settings['deadline_enabled']) && !empty($minDate))
                <div class="alert alert-info mb-3" style="padding: 0.55rem 0.75rem; font-size: 0.76rem; border-radius: 6px;">
                    Batas input absensi berlaku: maksimal tanggal <strong>{{ \Carbon\Carbon::parse($minDate)->translatedFormat('d M Y') }}</strong> (H+{{ $settings['deadline_days'] }}).
                </div>
            @endif

            <form action="{{ route('user.attendances.store') }}" method="POST" enctype="multipart/form-data" id="attendanceForm">
                @csrf

                <!-- 1. Pilihan Platform / Marketplace (MP) -->
                <div class="form-group mb-3">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.35rem;">
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
                                <x-platform-logo :platform="$mpKey" size="md" />
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

                <!-- 2. Tanggal & Sesi -->
                <div class="form-row mb-3">
                    <div class="form-group mb-0" style="flex: 1.2;">
                        <label for="attendance_date" class="form-label" style="font-size: 0.8rem; font-weight: 600;">Tanggal Live <span class="text-danger">*</span></label>
                        <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                            value="{{ old('attendance_date', $selectedDate) }}"
                            @if(!empty($minDate)) min="{{ $minDate }}" @endif
                            max="{{ date('Y-m-d') }}" required>
                        @error('attendance_date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="session_name" class="form-label" style="font-size: 0.8rem; font-weight: 600;">
                            Sesi / Shift <span class="text-danger">*</span>
                        </label>
                        <select name="session_name" id="session_name" class="form-control form-select" required>
                            <option value="Sesi 1" {{ old('session_name', $selectedSession) == 'Sesi 1' ? 'selected' : '' }}>Sesi 1 (Pagi)</option>
                            <option value="Sesi 2" {{ old('session_name', $selectedSession) == 'Sesi 2' ? 'selected' : '' }}>Sesi 2 (Siang/Sore)</option>
                            <option value="Sesi 3" {{ old('session_name', $selectedSession) == 'Sesi 3' ? 'selected' : '' }}>Sesi 3 (Malam)</option>
                            <option value="Sesi 4" {{ old('session_name', $selectedSession) == 'Sesi 4' ? 'selected' : '' }}>Sesi 4 (Subuh/Lembur)</option>
                        </select>
                        @error('session_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- 3. Durasi Live (Quick Pills + Dropdown) -->
                <div class="form-group mb-3">
                    <div class="flex items-center justify-between">
                        <label for="live_duration_hours" class="form-label mb-0" style="font-size: 0.8rem; font-weight: 600;">
                            Durasi Live (Jam) <span class="text-danger">*</span>
                        </label>
                        <span class="text-xs text-muted" style="font-size: 0.72rem;">Tap tombol cepat di bawah</span>
                    </div>

                    <div class="duration-pills mb-2">
                        @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 5] as $dur)
                            <div class="duration-pill {{ old('live_duration_hours', '2') == $dur ? 'active' : '' }}"
                                onclick="setDuration({{ $dur }})">
                                {{ $dur }} Jam
                            </div>
                        @endforeach
                    </div>

                    <select name="live_duration_hours" id="live_duration_hours" class="form-control form-select" required onchange="syncDurationPills(this.value)">
                        <option value="">Pilih Durasi Lainnya...</option>
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

                <!-- 4. Penjualan & Konten -->
                <div class="form-row mb-3">
                    <div class="form-group mb-0" style="flex: 1.2;">
                        <label for="sales_count" class="form-label" style="font-size: 0.8rem; font-weight: 600;">
                            Jumlah Penjualan (pcs) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="sales_count" id="sales_count" class="form-control"
                            value="{{ old('sales_count', 0) }}" min="0" required placeholder="0">
                        @error('sales_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="content_edit_count" class="form-label" style="font-size: 0.8rem;">Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count') == $count ? 'selected' : '' }}>{{ $count }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="content_live_count" class="form-label" style="font-size: 0.8rem;">Konten Live</label>
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
                    <div class="form-group mb-3">
                        <label for="proof_image" class="form-label flex items-center justify-between" style="font-size: 0.8rem; font-weight: 600;">
                            <span>
                                Bukti Screenshot Live
                                @if(!empty($settings['proof_required']))
                                    <span class="text-danger">*</span>
                                @else
                                    <span class="text-muted">(Opsional)</span>
                                @endif
                            </span>
                            <span class="text-xs text-muted" style="color: #10b981; font-weight: 600;">Auto-Compress HD</span>
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
                    <div class="form-group mb-3">
                        <label for="vt_link" class="form-label" id="vt_link_label" style="font-size: 0.8rem; font-weight: 600;">
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
                <div class="form-group mb-3">
                    <label for="notes" class="form-label" style="font-size: 0.8rem;">Catatan Kendala / Info (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"
                        placeholder="Catatan kendala sinyal, target, atau info live...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 8. Submit Action Buttons -->
                <input type="hidden" name="action" id="form_action" value="save">

                <div class="flex items-center gap-2 mt-4 flex-wrap">
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('form_action').value='save'" style="height: 38px; padding: 0 1.25rem; font-size: 0.85rem; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Absensi
                    </button>

                    @if(count($activeMarketplaces) > 1)
                        <button type="submit" class="btn btn-secondary" onclick="document.getElementById('form_action').value='save_and_next'"
                            style="height: 38px; padding: 0 1rem; font-size: 0.82rem; font-weight: 600; border-color: #ea580c; color: #ea580c; background: rgba(234, 88, 12, 0.04); display: inline-flex; align-items: center; gap: 5px;"
                            title="Simpan absensi ini dan langsung buka form untuk platform lainnya">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Simpan &amp; Input Platform Lain</span>
                        </button>
                    @endif

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

    function setDuration(dur) {
        const select = document.getElementById('live_duration_hours');
        if (select) {
            select.value = dur;
            syncDurationPills(dur);
        }
    }

    function syncDurationPills(val) {
        document.querySelectorAll('.duration-pill').forEach(pill => {
            const pillVal = parseFloat(pill.textContent);
            pill.classList.toggle('active', pillVal === parseFloat(val));
        });
    }
</script>
@endpush
