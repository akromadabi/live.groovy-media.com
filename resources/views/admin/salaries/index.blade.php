@extends('layouts.app')

@section('title', 'Riwayat Gaji')

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 0.75rem 1rem;">
            <form action="{{ route('admin.salaries.index') }}" method="GET" class="flex items-center gap-3"
                style="flex-wrap: wrap;">
                <span class="text-muted" style="font-size: 0.85rem;">Periode:</span>
                <select name="year" class="form-control form-select" style="width: auto; min-width: 90px;">
                    <option value="">Semua</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
                <select name="month" class="form-control form-select" style="width: auto; min-width: 120px;">
                    <option value="">Semua</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="term" class="form-control form-select" style="width: auto; min-width: 100px;">
                    <option value="">Semua</option>
                    <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>T1 (1-15)</option>
                    <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>T2 (16-akhir)</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"
                        height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.salaries.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Generate Gaji
        </a>
    </div>

    <!-- Salary Records Grid -->
    <div class="salary-records-grid">
        @forelse($salaries as $salary)
            <div class="salary-record-card">
                <div class="salary-record-header">
                    <div class="flex items-center gap-3">
                        <div class="avatar">{{ substr($salary->user->name, 0, 1) }}</div>
                        <div>
                            <div class="font-medium">{{ $salary->user->name }}</div>
                            <div class="text-sm text-muted">{{ $salary->user->task ?? 'User' }}</div>
                        </div>
                    </div>
                    <div class="salary-record-status">
                        @if($salary->status === 'paid')
                            <span class="badge badge-success">Dibayar</span>
                        @elseif($salary->status === 'finalized')
                            <span class="badge badge-info">Final</span>
                        @else
                            <span class="badge badge-warning">Draft</span>
                        @endif
                    </div>
                </div>
                <div class="salary-record-body">
                    <div class="salary-record-period">
                        <div class="term-badge {{ $salary->term == 1 ? 't1' : 't2' }}">T{{ $salary->term }}</div>
                        <span>{{ \Carbon\Carbon::parse($salary->period_start)->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="salary-record-amount">
                        Rp {{ number_format($salary->total_salary, 0, ',', '.') }}
                    </div>
                    <div class="salary-record-details">
                        <span>{{ $salary->total_hours }} jam</span>
                        @if($salary->target_met)
                            <span class="badge badge-success badge-sm" style="font-size: 0.6rem;">Target ✓</span>
                        @endif
                    </div>
                </div>
                <div class="salary-record-footer">
                    <a href="{{ route('admin.salaries.show', $salary) }}" class="btn btn-secondary btn-sm btn-block">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                            height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Detail
                    </a>
                    @if($salary->status !== 'paid')
                        <a href="{{ route('admin.salaries.edit', $salary) }}" class="btn btn-secondary btn-sm btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                                height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('admin.salaries.destroy', $salary) }}" method="POST" style="display:inline;"
                            onsubmit="return confirm('Hapus data gaji ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    width="14" height="14">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted" style="grid-column: 1/-1; padding: 3rem;">
                Tidak ada data gaji. <a href="{{ route('admin.salaries.create') }}">Generate gaji sekarang</a>
            </div>
        @endforelse
    </div>

    @if($salaries->hasPages())
        <div class="card-footer mt-4">
            {{ $salaries->withQueryString()->links() }}
        </div>
    @endif
@endsection