@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
    <div class="mb-4">
        <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card mobile-card">
        <div class="card-header">
            <h3 class="card-title">Edit Absensi</h3>
        </div>
        <div class="card-body">
            @php
                // Calculate decimal hours from minutes
                $currentHours = $attendance->live_duration_minutes / 60;
                // Round to nearest 0.5
                $currentHours = round($currentHours * 2) / 2;
            @endphp
            
            <form action="{{ route('user.attendances.update', $attendance) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="attendance_date" class="form-label">Tanggal</label>
                    <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                        value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="live_duration_hours" class="form-label">Durasi Live (Jam)</label>
                    <select name="live_duration_hours" id="live_duration_hours" class="form-control form-select" required>
                        <option value="">Pilih Durasi</option>
                        @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5] as $duration)
                            <option value="{{ $duration }}" {{ old('live_duration_hours', $currentHours) == $duration ? 'selected' : '' }}>
                                {{ $duration }} Jam
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="content_edit_count" class="form-label">Jumlah Konten Edit</label>
                        <select name="content_edit_count" id="content_edit_count" class="form-control form-select">
                            <option value="0" {{ old('content_edit_count', $attendance->content_edit_count) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_edit_count', $attendance->content_edit_count) == $count ? 'selected' : '' }}>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="content_live_count" class="form-label">Jumlah Konten Live</label>
                        <select name="content_live_count" id="content_live_count" class="form-control form-select">
                            <option value="0" {{ old('content_live_count', $attendance->content_live_count) == 0 ? 'selected' : '' }}>0</option>
                            @foreach([1, 2, 3, 4] as $count)
                                <option value="{{ $count }}" {{ old('content_live_count', $attendance->content_live_count) == $count ? 'selected' : '' }}>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sales_count" class="form-label">Jumlah Penjualan</label>
                    <input type="number" name="sales_count" id="sales_count" class="form-control"
                        value="{{ old('sales_count', $attendance->sales_count) }}" min="0" required>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan</label>
                    <textarea name="notes" id="notes" class="form-control"
                        rows="2">{{ old('notes', $attendance->notes) }}</textarea>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary flex-1">Simpan Perubahan</button>
                    <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection