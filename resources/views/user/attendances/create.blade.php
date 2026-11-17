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

    <div class="card" style="max-width: 560px;">
        <div class="card-header" style="padding: 0.65rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Input Absensi Harian</h3>
            <p class="text-xs text-muted mb-0">Laporkan sesi live stream dan pembuatan konten Anda</p>
        </div>
        <div class="card-body" style="padding: 1rem;">
            <form action="{{ route('user.attendances.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="attendance_date" class="form-label" style="font-size: 0.8rem;">Tanggal</label>
                    <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                        value="{{ old('attendance_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                    @error('attendance_date')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="live_duration_hours" class="form-label" style="font-size: 0.8rem;">Durasi Live (Jam)</label>
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
                    <label for="sales_count" class="form-label" style="font-size: 0.8rem;">Jumlah Penjualan (pcs)</label>
                    <input type="number" name="sales_count" id="sales_count" class="form-control"
                        value="{{ old('sales_count', 0) }}" min="0" required>
                    @error('sales_count')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="notes" class="form-label" style="font-size: 0.8rem;">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"
                        placeholder="Catatan tambahan bila ada...">{{ old('notes') }}</textarea>
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
