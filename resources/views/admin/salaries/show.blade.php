@extends('layouts.app')

@section('title', 'Detail Gaji')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-2" style="gap: 1.5rem;">
        <!-- Summary Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Gaji</h3>
                <div>
                    @if($salary->status === 'paid')
                        <span class="badge badge-success">Dibayar</span>
                    @elseif($salary->status === 'finalized')
                        <span class="badge badge-info">Final</span>
                    @else
                        <span class="badge badge-warning">Draft</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4"
                    style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius);">
                    <div class="avatar avatar-lg">{{ substr($salary->user->name, 0, 1) }}</div>
                    <div>
                        <h4 class="mb-1">{{ $salary->user->name }}</h4>
                        <p class="text-muted mb-0">{{ $salary->user->task ?? 'User' }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-muted text-sm">Periode</div>
                    <div class="font-medium">{{ $salary->period_label }}</div>
                    <div class="text-sm">{{ $salary->date_range_label }}</div>
                </div>

                <table class="table" style="margin-bottom: 1rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted">Total Jam</td>
                            <td class="text-right">{{ $salary->total_hours }} jam
                                @if($salary->target_met)
                                    <span class="badge badge-success" style="margin-left: 4px;">Target ✓</span>
                                @else
                                    <span class="badge badge-warning" style="margin-left: 4px;">Belum Target</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Gaji Live</td>
                            <td class="text-right">Rp {{ number_format($salary->live_salary, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bonus Konten Edit</td>
                            <td class="text-right">Rp {{ number_format($salary->content_edit_bonus, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bonus Konten Live</td>
                            <td class="text-right">Rp {{ number_format($salary->content_live_bonus, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Penjualan</td>
                            <td class="text-right">{{ $salary->total_sales }} pcs</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bonus Penjualan</td>
                            <td class="text-right" style="color: var(--success);">
                                Rp {{ number_format($salary->sales_bonus, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($salary->deduction > 0)
                            <tr>
                                <td class="text-muted">Potongan</td>
                                <td class="text-right" style="color: var(--danger);">
                                    -Rp {{ number_format($salary->deduction, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="border-top: 2px solid var(--border);">
                            <td class="font-bold">TOTAL</td>
                            <td class="text-right font-bold" style="color: var(--success); font-size: 1.25rem;">
                                {{ $salary->formatted_total }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                @if($salary->deduction_notes)
                    <div class="mb-4" style="padding: 0.75rem; background: var(--bg-secondary); border-radius: var(--radius);">
                        <div class="text-muted text-sm mb-1">Catatan Potongan:</div>
                        <div>{{ $salary->deduction_notes }}</div>
                    </div>
                @endif

                @if($salary->paid_at)
                    <div class="text-sm text-muted">
                        Dibayar pada: {{ $salary->paid_at->format('d M Y H:i') }}
                    </div>
                @endif

                @if($salary->status !== 'paid')
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('admin.salaries.edit', $salary) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('admin.salaries.recalculate', $salary) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">Hitung Ulang</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Attendance Details -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Absensi</h3>
                <span class="text-muted">{{ $attendances->count() }} record</span>
            </div>
            <div class="table-container" style="max-height: 500px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Durasi</th>
                            <th>Edit</th>
                            <th>Live</th>
                            <th>Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->attendance_date->format('d M') }}</td>
                                <td>{{ $attendance->formatted_duration }}</td>
                                <td>{{ $attendance->content_edit_count }}</td>
                                <td>{{ $attendance->content_live_count }}</td>
                                <td>{{ $attendance->sales_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Tidak ada data absensi tervalidasi pada periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection