@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@push('styles')
<style>
    .user-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.65rem;
    }
    @media (max-width: 768px) {
        .user-summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .user-summary-grid .bonus-summary-card {
            grid-column: span 2;
        }
    }
</style>
@endpush

@section('content')
    <!-- Control Bar -->
    <div class="salary-control-bar mb-3">
        <form action="{{ route('user.attendances.index') }}" method="GET" class="salary-filter-form">
            <select name="year" class="filter-select" onchange="this.form.submit()">
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="filter-select" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="term" class="filter-select" onchange="this.form.submit()">
                <option value="" {{ request('term') == '' ? 'selected' : '' }}>Semua Termin</option>
                <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>T1 (1-15)</option>
                <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>T2 (16-Akhir)</option>
            </select>
        </form>
        <div class="salary-btn-group">
            <a href="{{ route('user.attendances.create') }}" class="btn btn-primary btn-sm salary-action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Absensi
            </a>
        </div>
    </div>

    <!-- 4 Stats Compact Cards -->
    <div class="stats-grid-mobile mb-3">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Jam</div>
                <div class="stat-number">{{ $totals['total_hours'] }} jam</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Konten Edit</div>
                <div class="stat-number">{{ $totals['total_content_edit'] }}</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Konten Live</div>
                <div class="stat-number">{{ $totals['total_content_live'] }}</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Estimasi Gaji</div>
                <div class="stat-number" style="color: var(--success); font-size: 0.92rem;">
                    Rp {{ number_format($totals['total_salary'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- T1, T2 & Bonus Summary Cards -->
    <div class="user-summary-grid mb-3">
        <div class="term-card">
            <div class="term-header">
                <span class="term-badge t1">T1</span>
                <span class="term-period">1 - 15</span>
            </div>
            <div class="term-body">
                <div class="term-hours">{{ $totals['t1_hours'] ?? 0 }} <small>jam</small></div>
                <div class="term-salary">Rp {{ number_format($totals['t1_salary'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="term-card">
            <div class="term-header">
                <span class="term-badge t2">T2</span>
                <span class="term-period">16 - Akhir</span>
            </div>
            <div class="term-body">
                <div class="term-hours">{{ $totals['t2_hours'] ?? 0 }} <small>jam</small></div>
                <div class="term-salary">Rp {{ number_format($totals['t2_salary'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="bonus-card bonus-summary-card {{ $bonusInfo['target_met'] ? 'bonus-met' : 'bonus-pending' }}" style="margin: 0;">
            <div class="bonus-header">
                <span class="bonus-status">{{ $bonusInfo['target_met'] ? '✓ Target Tercapai' : '⏳ Target: ' . $bonusInfo['monthly_hours'] . '/' . $bonusInfo['target_hours'] . ' j' }}</span>
                <span class="bonus-progress">{{ $bonusInfo['monthly_sales'] }} pcs</span>
            </div>
            <div class="bonus-body">
                <div class="bonus-label">Bonus Penjualan</div>
                <div class="bonus-amount">Rp {{ number_format($bonusInfo['sales_bonus'] ?? 0, 0, ',', '.') }}</div>
                @if(!$bonusInfo['target_met'] && $bonusInfo['sales_bonus'] > 0)
                    <div class="bonus-note">*cair saat target terpenuhi</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Table (Desktop) / Cards (Mobile) -->
    <div class="card">
        <div class="card-header flex items-center justify-between" style="padding: 0.65rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Daftar Absensi</h3>
            <span class="text-xs text-muted">{{ $attendances->total() }} catatan</span>
        </div>

        @php
            $scheme = auth()->user()->salaryScheme;
            $formatDuration = function ($minutes) {
                $hours = floor($minutes / 60);
                $mins = $minutes % 60;
                if ($hours > 0 && $mins > 0) return $hours . ' j ' . $mins . ' m';
                if ($hours > 0) return $hours . ' j';
                return $mins . ' m';
            };
        @endphp

        <!-- Desktop View Table -->
        <div class="table-container hide-on-mobile">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Tanggal</th>
                        <th>Durasi Live</th>
                        <th>Konten</th>
                        <th>Penjualan</th>
                        <th>Gaji Sesi</th>
                        <th>Status TikTok</th>
                        <th>Status</th>
                        <th style="width: 80px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            $salary = 0;
                            if ($scheme) {
                                $hours = $attendance->live_duration_minutes / 60;
                                $salary = ($hours * $scheme->hourly_rate)
                                    + ($attendance->content_edit_count * $scheme->content_edit_rate)
                                    + ($attendance->content_live_count * $scheme->content_live_rate);
                            }
                            $dateStr = \Carbon\Carbon::parse($attendance->attendance_date)->toDateString();
                            $totalTiktok = $tiktokTotalsByDate[$dateStr] ?? 0;
                            $totalAbsen = $attendanceTotalsByDate[$dateStr] ?? 0;
                            $totalDiff = $totalTiktok - $totalAbsen;
                        @endphp
                        <tr>
                            <td>
                                <div class="font-medium text-sm">{{ $attendance->attendance_date->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-muted">{{ $attendance->attendance_date->translatedFormat('l') }}</div>
                            </td>
                            <td>
                                <span class="badge badge-secondary" style="font-weight: 600;">{{ $attendance->formatted_duration }}</span>
                            </td>
                            <td>
                                <div class="text-xs">
                                    <span title="Edit">{{ $attendance->content_edit_count }} edit</span> / 
                                    <span title="Live">{{ $attendance->content_live_count }} live</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-medium text-sm">{{ $attendance->sales_count }} pcs</span>
                            </td>
                            <td>
                                <span class="font-medium text-sm" style="color: var(--success);">Rp {{ number_format($salary, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if($totalTiktok > 0)
                                    @if(abs($totalDiff) < 10)
                                        <span class="badge badge-success badge-sm" title="Sesuai ({{ $formatDuration(abs($totalDiff)) }})">✓ Sesuai</span>
                                    @elseif($totalDiff >= 10)
                                        <span class="badge badge-sm" style="background: #3b82f6; color: white;" title="TikTok +{{ $formatDuration($totalDiff) }}">↑ TikTok +{{ $formatDuration($totalDiff) }}</span>
                                    @else
                                        <span class="badge badge-danger badge-sm" title="Absen +{{ $formatDuration(abs($totalDiff)) }}">↓ Absen +{{ $formatDuration(abs($totalDiff)) }}</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary badge-sm" style="opacity: 0.6;">Belum ada TT</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status === 'validated')
                                    <span class="badge badge-success badge-sm">Tervalidasi</span>
                                @elseif($attendance->status === 'pending')
                                    <span class="badge badge-warning badge-sm">Pending</span>
                                @else
                                    <span class="badge badge-danger badge-sm">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($attendance->status === 'pending')
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('user.attendances.edit', $attendance) }}" class="btn btn-secondary btn-icon" style="width: 26px; height: 26px; padding: 0;" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('user.attendances.destroy', $attendance) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus absensi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-icon" style="width: 26px; height: 26px; padding: 0;" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-muted" title="Tervalidasi">🔒</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding: 2.5rem;">
                                Belum ada data absensi untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Compact List) -->
        <div class="attendance-list hide-on-desktop">
            @forelse($attendances as $attendance)
                @php
                    $salary = 0;
                    if ($scheme) {
                        $hours = $attendance->live_duration_minutes / 60;
                        $salary = ($hours * $scheme->hourly_rate)
                            + ($attendance->content_edit_count * $scheme->content_edit_rate)
                            + ($attendance->content_live_count * $scheme->content_live_rate);
                    }
                    $dateStr = \Carbon\Carbon::parse($attendance->attendance_date)->toDateString();
                    $totalTiktok = $tiktokTotalsByDate[$dateStr] ?? 0;
                    $totalAbsen = $attendanceTotalsByDate[$dateStr] ?? 0;
                    $totalDiff = $totalTiktok - $totalAbsen;
                @endphp
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="date-day">{{ $attendance->attendance_date->format('d') }}</div>
                        <div class="date-month">{{ $attendance->attendance_date->translatedFormat('M') }}</div>
                    </div>
                    <div class="attendance-info">
                        <div class="attendance-duration">{{ $attendance->formatted_duration }}</div>
                        <div class="attendance-meta">
                            Edit: {{ $attendance->content_edit_count }} • Live: {{ $attendance->content_live_count }} • Jual: {{ $attendance->sales_count }}
                        </div>
                    </div>
                    <div class="attendance-right">
                        <div class="attendance-salary">Rp {{ number_format($salary, 0, ',', '.') }}</div>
                        <div style="display: flex; align-items: center; gap: 0.35rem; justify-content: flex-end;">
                            @if($totalTiktok > 0)
                                @if(abs($totalDiff) < 10)
                                    <span class="badge badge-success badge-sm" title="Sesuai">✓</span>
                                @elseif($totalDiff >= 10)
                                    <span class="badge badge-sm" data-tooltip="TikTok +{{ $formatDuration($totalDiff) }}" style="background: #3b82f6; color: white;">↑</span>
                                @else
                                    <span class="badge badge-danger badge-sm" data-tooltip="Absen +{{ $formatDuration(abs($totalDiff)) }}">↓</span>
                                @endif
                            @else
                                <span class="badge badge-secondary badge-sm" style="opacity: 0.5;">—</span>
                            @endif

                            @if($attendance->status === 'validated')
                                <span class="badge badge-success badge-sm" title="Tervalidasi">🔒</span>
                            @elseif($attendance->status === 'pending')
                                <span class="badge badge-warning badge-sm">⏳</span>
                                <a href="{{ route('user.attendances.edit', $attendance) }}" class="btn btn-secondary btn-xs" style="padding: 2px 6px; font-size: 0.7rem;">Edit</a>
                            @else
                                <span class="badge badge-danger badge-sm">✕</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted" style="padding: 2.5rem;">
                    Belum ada data absensi
                </div>
            @endforelse
        </div>

        @if($attendances->hasPages())
            <div class="card-footer" style="padding: 0.65rem 0.85rem;">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
