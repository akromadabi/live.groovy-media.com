@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
    <!-- Mobile-Optimized Filter -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 1rem;">
            <form action="{{ route('user.attendances.index') }}" method="GET">
                <div class="mobile-filter-grid">
                    <select name="year" class="form-control form-select">
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="month" class="form-control form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('M') }}
                            </option>
                        @endfor
                    </select>
                    <select name="term" class="form-control form-select">
                        <option value="" {{ request('term') == '' ? 'selected' : '' }}>Semua</option>
                        <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>T1</option>
                        <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>T2</option>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Grid - Mobile Optimized -->
    <div class="stats-grid-mobile mb-4">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Jam</div>
                <div class="stat-number">{{ $totals['total_hours'] }}</div>
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
                <div class="stat-label">Edit</div>
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
                <div class="stat-label">Live</div>
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
                <div class="stat-label">Gaji</div>
                <div class="stat-number" style="color: var(--success); font-size: 0.9rem;">
                    Rp {{ number_format($totals['total_salary'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Breakdown Card - T1/T2 -->
    <div class="term-salary-grid mb-4">
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
    </div>

    <!-- Bonus Card -->
    <div class="bonus-card mb-4 {{ $bonusInfo['target_met'] ? 'bonus-met' : 'bonus-pending' }}">
        <div class="bonus-header">
            @if($bonusInfo['target_met'])
                <span class="bonus-status">✓ Target Tercapai</span>
            @else
                <span class="bonus-status">⏳ Belum Tercapai</span>
            @endif
            <span class="bonus-progress">{{ $bonusInfo['monthly_hours'] }}/{{ $bonusInfo['target_hours'] }} jam</span>
        </div>
        <div class="bonus-body">
            <div class="bonus-label">Bonus Penjualan ({{ $bonusInfo['monthly_sales'] }} pcs)</div>
            <div class="bonus-amount">Rp {{ number_format($bonusInfo['sales_bonus'] ?? 0, 0, ',', '.') }}</div>
            @if(!$bonusInfo['target_met'] && $bonusInfo['sales_bonus'] > 0)
                <div class="bonus-note">*berlaku jika target tercapai</div>
            @endif
        </div>
    </div>

    <!-- Attendance List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Absensi</h3>
            <a href="{{ route('user.attendances.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
        </div>
        <div class="attendance-list">
            @php
                $scheme = auth()->user()->salaryScheme;
            @endphp
            @forelse($attendances as $attendance)
                @php
                    $salary = 0;
                    if ($scheme) {
                        $hours = $attendance->live_duration_minutes / 60;
                        $salary = ($hours * $scheme->hourly_rate)
                            + ($attendance->content_edit_count * $scheme->content_edit_rate)
                            + ($attendance->content_live_count * $scheme->content_live_rate);
                    }
                @endphp
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="date-day">{{ $attendance->attendance_date->format('d') }}</div>
                        <div class="date-month">{{ $attendance->attendance_date->format('M') }}</div>
                    </div>
                    <div class="attendance-info">
                        <div class="attendance-duration">{{ $attendance->formatted_duration }}</div>
                        <div class="attendance-meta">
                            Edit:{{ $attendance->content_edit_count }} Live:{{ $attendance->content_live_count }}
                            Penjualan:{{ $attendance->sales_count }}
                        </div>
                    </div>
                    <div class="attendance-right">
                        <div class="attendance-salary">Rp {{ number_format($salary, 0, ',', '.') }}</div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; justify-content: flex-end;">
                            {{-- TikTok Match Status Icon (based on TOTAL for that date, not per user) --}}
                            @php
                                $dateStr = \Carbon\Carbon::parse($attendance->attendance_date)->toDateString();

                                // Get TOTAL TikTok duration for this date (all users)
                                $totalTiktok = \App\Models\TiktokReportDetail::whereDate('live_date', $dateStr)
                                    ->sum('duration_minutes');

                                // Get TOTAL Attendance duration for this date (all users)
                                $totalAbsen = \App\Models\Attendance::whereDate('attendance_date', $dateStr)
                                    ->sum('live_duration_minutes');

                                // Calculate difference: positive = TikTok more, negative = Absen more
                                $totalDiff = $totalTiktok - $totalAbsen;

                                // Helper function to format duration
                                $formatDuration = function ($minutes) {
                                    $hours = floor($minutes / 60);
                                    $mins = $minutes % 60;

                                    if ($hours > 0 && $mins > 0) {
                                        return $hours . ' j ' . $mins . ' m';
                                    } elseif ($hours > 0) {
                                        return $hours . ' j';
                                    } else {
                                        return $mins . ' m';
                                    }
                                };
                            @endphp
                            @if($totalTiktok > 0)
                                @if(abs($totalDiff) < 10)
                                    {{-- Difference less than 10 minutes = Green checkmark --}}
                                    <span class="badge badge-success badge-sm tooltip-badge"
                                        data-tooltip="Sesuai ({{ $formatDuration(abs($totalDiff)) }})">✓</span>
                                @elseif($totalDiff >= 10)
                                    {{-- TikTok > Absen = Up arrow (blue) --}}
                                    <span class="badge badge-sm tooltip-badge" data-tooltip="TikTok +{{ $formatDuration($totalDiff) }}"
                                        style="background: #3b82f6; color: white;">↑</span>
                                @else
                                    {{-- Absen > TikTok = Down arrow (red) --}}
                                    <span class="badge badge-danger badge-sm tooltip-badge"
                                        data-tooltip="Absen +{{ $formatDuration(abs($totalDiff)) }}">↓</span>
                                @endif
                            @else
                                <span class="badge badge-secondary badge-sm tooltip-badge" data-tooltip="Belum ada data TikTok"
                                    style="opacity: 0.5;">—</span>
                            @endif

                            {{-- Attendance Status Icon --}}
                            @if($attendance->status === 'validated')
                                <span class="badge badge-success badge-sm" title="Tervalidasi">🔒</span>
                            @elseif($attendance->status === 'pending')
                                <span class="badge badge-warning badge-sm">⏳</span>
                            @else
                                <span class="badge badge-danger badge-sm">✕</span>
                            @endif
                            @if($attendance->status === 'pending')
                                <a href="{{ route('user.attendances.edit', $attendance) }}"
                                    class="btn btn-secondary btn-xs">Edit</a>
                                <form action="{{ route('user.attendances.destroy', $attendance) }}" method="POST"
                                    style="display: inline;" onsubmit="return confirm('Hapus absensi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="12" height="12">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted" style="padding: 3rem;">
                    Belum ada data absensi
                </div>
            @endforelse
        </div>
        @if($attendances->hasPages())
            <div class="card-footer">
                {{ $attendances->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection