@extends('layouts.app')

@section('title', 'Generate Gaji')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 700px;">
        <div class="card-header">
            <h3 class="card-title">Generate Rekap Gaji</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <strong>📌 Info:</strong> Sistem akan menghitung gaji berdasarkan absensi yang sudah tervalidasi pada
                periode yang dipilih. Bonus penjualan hanya dihitung jika target durasi bulanan tercapai.
            </div>

            <form action="{{ route('admin.salaries.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="year" class="form-label">Tahun</label>
                        <select name="year" id="year" class="form-control form-select" required>
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="month" class="form-label">Bulan</label>
                        <select name="month" id="month" class="form-control form-select" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="term" class="form-label">Termin</label>
                        <select name="term" id="term" class="form-control form-select" required>
                            <option value="1" {{ $currentTerm == 1 ? 'selected' : '' }}>Termin 1 (Tanggal 1-15)</option>
                            <option value="2" {{ $currentTerm == 2 ? 'selected' : '' }}>Termin 2 (Tanggal 16-Akhir)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Pilih User (Opsional)</label>
                    <p class="text-muted text-sm mb-2">Kosongkan untuk generate gaji semua user aktif</p>
                    <div class="checkbox-group"
                        style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem;">
                        @foreach($users as $user)
                            <label class="form-check" style="margin-bottom: 0.5rem;">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                                <span>{{ $user->name }} <span class="text-muted">({{ $user->task ?? 'User' }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Generate Gaji
                    </button>
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection