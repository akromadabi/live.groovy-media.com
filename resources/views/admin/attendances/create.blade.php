@extends('layouts.app')

@section('title', 'Tambah Absensi')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">Tambah Absensi Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label for="user_id" class="form-label">User / Host</label>
                    <select name="user_id" id="user_id" class="form-control form-select" required>
                        <option value="">Pilih User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->task }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="platform" class="form-label">Platform Live</label>
                    <select name="platform" id="platform" class="form-control form-select" required>
                        @foreach($activeMarketplaces as $mpKey => $mp)
                            <option value="{{ $mpKey }}" {{ old('platform', 'tiktok') == $mpKey ? 'selected' : '' }}>
                                {{ $mp['name'] }} (Rp {{ number_format($mp['default_hourly_rate'], 0, ',', '.') }}/jam)
                            </option>
                        @endforeach
                    </select>
                    @error('platform')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row mb-3">
                    <div class="form-group mb-0" style="flex: 1.2;">
                        <label for="attendance_date" class="form-label">Tanggal</label>
                        <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                            value="{{ old('attendance_date', date('Y-m-d')) }}" required>
                        @error('attendance_date')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0" style="flex: 1;">
                        <label for="session_name" class="form-label">Sesi / Shift</label>
                        <select name="session_name" id="session_name" class="form-control form-select">
                            <option value="Sesi 1" {{ old('session_name', 'Sesi 1') == 'Sesi 1' ? 'selected' : '' }}>Sesi 1 (Pagi)</option>
                            <option value="Sesi 2" {{ old('session_name') == 'Sesi 2' ? 'selected' : '' }}>Sesi 2 (Siang/Sore)</option>
                            <option value="Sesi 3" {{ old('session_name') == 'Sesi 3' ? 'selected' : '' }}>Sesi 3 (Malam)</option>
                            <option value="Sesi 4" {{ old('session_name') == 'Sesi 4' ? 'selected' : '' }}>Sesi 4 (Subuh/Lembur)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="live_duration_hours" class="form-label">Durasi Live (Jam)</label>
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
                        <label for="content_edit_count" class="form-label">Jumlah Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count') == $count ? 'selected' : '' }}>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="content_live_count" class="form-label">Jumlah Konten Live</label>
                        <select name="content_live_count" id="content_live_count" class="form-control form-select">
                            <option value="0" {{ old('content_live_count', 0) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_live_count') == $count ? 'selected' : '' }}>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="sales_count" class="form-label">Jumlah Penjualan (pcs)</label>
                    <input type="number" name="sales_count" id="sales_count" class="form-control"
                        value="{{ old('sales_count', 0) }}" min="0" required>
                </div>

                <div class="form-group mb-3">
                    <label for="proof_image" class="form-label flex items-center justify-between">
                        <span>Bukti Screenshot (Opsional)</span>
                        <span class="text-xs text-muted" style="color: #10b981; font-weight: 600;">Auto-Compress HD</span>
                    </label>
                    <input type="file" name="proof_image" id="proof_image" class="form-control" accept="image/*">
                    <div id="proof-preview"></div>
                </div>

                <div class="form-group mb-3">
                    <label for="vt_link" class="form-label">Link Video TikTok / VT (Opsional)</label>
                    <input type="url" name="vt_link" id="vt_link" class="form-control"
                        placeholder="https://vt.tiktok.com/..." value="{{ old('vt_link') }}">
                </div>

                <div class="form-group mb-3">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"
                        placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">Batal</a>
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
