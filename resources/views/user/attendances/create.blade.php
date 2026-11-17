@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
    <div class="mb-3">
        <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary btn-sm" style="height: 32px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 580px;">
        <div class="card-header" style="padding: 0.65rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Input Absensi Live & Konten</h3>
            <p class="text-xs text-muted mb-0">Laporkan sesi live stream dan pembuatan konten Anda</p>
        </div>
        <div class="card-body" style="padding: 1rem;">
            @if(!empty($settings['deadline_enabled']) && !empty($minDate))
                <div class="alert alert-info mb-3" style="padding: 0.55rem 0.75rem; font-size: 0.76rem; border-radius: 6px;">
                    ℹ️ Batas input absensi berlaku: maksimal tanggal <strong>{{ \Carbon\Carbon::parse($minDate)->translatedFormat('d M Y') }}</strong> (H+{{ $settings['deadline_days'] }}).
                </div>
            @endif

            <form action="{{ route('user.attendances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mb-3">
                    <div class="form-group mb-0" style="flex: 1.2;">
                        <label for="attendance_date" class="form-label" style="font-size: 0.8rem;">Tanggal Live <span class="text-danger">*</span></label>
                        <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                            value="{{ old('attendance_date', date('Y-m-d')) }}"
                            @if(!empty($minDate)) min="{{ $minDate }}" @endif
                            max="{{ date('Y-m-d') }}" required>
                        @error('attendance_date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="session_name" class="form-label" style="font-size: 0.8rem;">Sesi / Shift <span class="text-danger">*</span></label>
                        <select name="session_name" id="session_name" class="form-control form-select" required>
                            <option value="Sesi 1" {{ old('session_name', 'Sesi 1') == 'Sesi 1' ? 'selected' : '' }}>Sesi 1 (Pagi)</option>
                            <option value="Sesi 2" {{ old('session_name') == 'Sesi 2' ? 'selected' : '' }}>Sesi 2 (Siang/Sore)</option>
                            <option value="Sesi 3" {{ old('session_name') == 'Sesi 3' ? 'selected' : '' }}>Sesi 3 (Malam)</option>
                            <option value="Sesi 4" {{ old('session_name') == 'Sesi 4' ? 'selected' : '' }}>Sesi 4 (Subuh/Lembur)</option>
                        </select>
                        @error('session_name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="live_duration_hours" class="form-label" style="font-size: 0.8rem;">Durasi Live (Jam) <span class="text-danger">*</span></label>
                    <select name="live_duration_hours" id="live_duration_hours" class="form-control form-select" required>
                        <option value="">Pilih Durasi</option>
                        @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8] as $duration)
                            <option value="{{ $duration }}" {{ old('live_duration_hours') == $duration ? 'selected' : '' }}>
                                {{ $duration }} Jam
                            </option>
                        @endforeach
                    </select>
                    @error('live_duration_hours')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row mb-3">
                    <div class="form-group mb-0">
                        <label for="content_edit_count" class="form-label" style="font-size: 0.8rem;">Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count') == $count ? 'selected' : '' }}>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                        @error('content_edit_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-0">
                        <label for="content_live_count" class="form-label" style="font-size: 0.8rem;">Konten Live</label>
                        <select name="content_live_count" id="content_live_count" class="form-control form-select">
                            <option value="0" {{ old('content_live_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_live_count') == $count ? 'selected' : '' }}>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                        @error('content_live_count')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="sales_count" class="form-label" style="font-size: 0.8rem;">Jumlah Penjualan (pcs) <span class="text-danger">*</span></label>
                    <input type="number" name="sales_count" id="sales_count" class="form-control"
                        value="{{ old('sales_count', 0) }}" min="0" required>
                    @error('sales_count')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                @if(!empty($settings['proof_enabled']))
                    <div class="form-group mb-3">
                        <label for="proof_image" class="form-label flex items-center justify-between" style="font-size: 0.8rem;">
                            <span>
                                📸 Bukti Screenshot (Proof of Work)
                                @if(!empty($settings['proof_required']))
                                    <span class="text-danger">*</span>
                                @else
                                    <span class="text-muted">(Opsional)</span>
                                @endif
                            </span>
                            <span class="text-xs text-muted" style="color: #10b981;">⚡ Auto-Compress HD</span>
                        </label>
                        <input type="file" name="proof_image" id="proof_image" class="form-control" accept="image/*"
                            {{ !empty($settings['proof_required']) ? 'required' : '' }}>
                        <div id="proof-preview"></div>
                        @error('proof_image')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                @if(!empty($settings['vt_link_enabled']))
                    <div class="form-group mb-3">
                        <label for="vt_link" class="form-label" style="font-size: 0.8rem;">
                            🔗 Link Video TikTok / VT
                            @if(!empty($settings['vt_link_required']))
                                <span class="text-danger">*</span>
                            @else
                                <span class="text-muted">(Opsional)</span>
                            @endif
                        </label>
                        <input type="url" name="vt_link" id="vt_link" class="form-control"
                            placeholder="https://vt.tiktok.com/..."
                            value="{{ old('vt_link') }}" {{ !empty($settings['vt_link_required']) ? 'required' : '' }}>
                        @error('vt_link')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="form-group mb-3">
                    <label for="notes" class="form-label" style="font-size: 0.8rem;">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"
                        placeholder="Catatan kendala, target, atau info live...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Absensi
                    </button>
                    <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary" style="height: 36px; padding: 0 1rem; font-size: 0.85rem;">Batal</a>
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
</script>
@endpush
