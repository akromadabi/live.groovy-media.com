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
    .gamification-card {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.08) 100%);
        border: 1px solid rgba(99, 102, 241, 0.25);
        border-radius: var(--radius-lg, 10px);
        padding: 0.75rem 1rem;
    }
    .proof-thumb {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
        cursor: pointer;
        border: 1px solid var(--border);
        transition: transform 0.15s ease;
    }
    .proof-thumb:hover {
        transform: scale(1.1);
    }
    .image-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .image-modal-overlay.active {
        display: flex;
    }
    .image-modal-content {
        max-width: 90vw;
        max-height: 90vh;
        border-radius: 8px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }
    .image-modal-content img {
        display: block;
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
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
            <select name="platform" class="filter-select" onchange="this.form.submit()">
                <option value="" {{ request('platform') == '' ? 'selected' : '' }}>Semua Platform</option>
                @foreach($allMarketplaces as $mpKey => $mp)
                    @if(!empty($mp['enabled']) || request('platform') === $mpKey)
                        <option value="{{ $mpKey }}" {{ request('platform') == $mpKey ? 'selected' : '' }}>
                            {{ $mp['name'] }}
                        </option>
                    @endif
                @endforeach
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

    @if(!empty($platformBreakdown) && count($platformBreakdown) > 0)
        <div class="mb-3" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md, 8px); padding: 0.6rem 0.85rem;">
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Platform:
            </div>
            @foreach($platformBreakdown as $pKey => $pData)
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 500; background: {{ $pData['info']['badge_bg'] }}; color: {{ $pData['info']['badge_color'] }};">
                    <x-platform-logo :platform="$pKey" size="2xs" />
                    <strong>{{ $pData['info']['short_name'] }}</strong>
                    <span style="opacity: 0.85;">{{ number_format($pData['hours'], 1) }} jam</span>
                    @if($pData['rate'] > 0)
                        <span style="font-weight: 600;">(Rp {{ number_format($pData['salary'], 0, ',', '.') }})</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

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
                <span class="bonus-status">{{ $bonusInfo['target_met'] ? 'Target Tercapai' : 'Target: ' . $bonusInfo['monthly_hours'] . '/' . $bonusInfo['target_hours'] . ' j' }}</span>
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

    <!-- Gamification & Bonus Progress Bar (Real-Time) -->
    <div class="gamification-card mb-3">
        <div class="flex items-center justify-between gap-2 mb-2">
            <div class="flex items-center gap-2">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 8px; background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
                <div>
                    <h4 style="margin: 0; font-size: 0.85rem; font-weight: 700; color: var(--text);">Progres Menuju Bonus Berikutnya</h4>
                    <span style="font-size: 0.72rem; color: var(--text-muted);">
                        Tiap kelipatan <strong>{{ $bonusInfo['threshold'] ?? 20 }} pcs</strong> penjualan berhak bonus <strong>Rp {{ number_format($bonusInfo['bonus_amount_per_tier'] ?? 10000, 0, ',', '.') }}</strong>
                    </span>
                </div>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 0.85rem; font-weight: 700; color: #6366f1;">
                    {{ $bonusInfo['monthly_sales'] ?? 0 }} / {{ $bonusInfo['next_tier_target'] ?? 20 }} pcs
                </span>
                <div style="font-size: 0.72rem; color: #10b981; font-weight: 600;">
                    Tinggal {{ $bonusInfo['remaining_to_next'] ?? 0 }} pcs lagi!
                </div>
            </div>
        </div>
        <!-- Progress Bar -->
        <div style="background: rgba(0,0,0,0.08); border-radius: 999px; height: 10px; overflow: hidden; position: relative;">
            <div style="background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%); height: 100%; border-radius: 999px; width: {{ $bonusInfo['tier_progress_percent'] ?? 0 }}%; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);"></div>
        </div>
        <div class="flex items-center justify-between mt-2" style="font-size: 0.72rem; color: var(--text-muted);">
            <span>Level Bonus Saat Ini: <strong>{{ $bonusInfo['current_multiplier'] ?? 0 }}x (Rp {{ number_format(($bonusInfo['current_multiplier'] ?? 0) * ($bonusInfo['bonus_amount_per_tier'] ?? 10000), 0, ',', '.') }})</strong></span>
            <span>Target Berikutnya: <strong>Total Rp {{ number_format($bonusInfo['next_bonus_total'] ?? 0, 0, ',', '.') }}</strong></span>
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
                        <th style="width: 130px;">Tanggal & Sesi</th>
                        <th>Durasi Live</th>
                        <th>Konten</th>
                        <th>Penjualan</th>
                        <th>Gaji Sesi</th>
                        <th>Bukti & VT</th>
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
                                $rate = $scheme->getHourlyRateForPlatform($attendance->platform ?? 'tiktok');
                                $salary = ($hours * $rate)
                                    + ($attendance->content_edit_count * $scheme->content_edit_rate)
                                    + ($attendance->content_live_count * $scheme->content_live_rate);
                            }
                            $dateStr = \Carbon\Carbon::parse($attendance->attendance_date)->toDateString();
                            $totalTiktok = $tiktokTotalsByDate[$dateStr] ?? 0;
                            $totalAbsen = $attendanceTotalsByDate[$dateStr] ?? 0;
                            $totalDiff = $totalTiktok - $totalAbsen;
                            $pInfo = $attendance->platform_info;
                        @endphp
                        <tr>
                            <td>
                                <div class="font-medium text-sm">{{ $attendance->attendance_date->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-muted flex items-center gap-1 mt-0.5 flex-wrap">
                                    <span class="badge" style="background: {{ $pInfo['bg_badge'] ?? '#0f172a' }}; color: {{ $pInfo['text_badge'] ?? '#ffffff' }}; font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <x-platform-logo :platform="$attendance->platform ?? 'tiktok'" size="2xs" />
                                        <span>{{ $pInfo['short_name'] ?? 'Live' }}</span>
                                    </span>
                                    <span class="badge badge-secondary badge-xs" style="font-size: 0.68rem; padding: 1px 5px;">{{ $attendance->session_name ?? 'Sesi 1' }}</span>
                                    <span>{{ $attendance->attendance_date->translatedFormat('D') }}</span>
                                </div>
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
                                <div class="flex items-center gap-2">
                                    @if($attendance->proof_image)
                                        <img src="{{ $attendance->proof_image_url }}" alt="Bukti" class="proof-thumb" onclick="openImageModal('{{ $attendance->proof_image_url }}')" title="Klik untuk lihat bukti screenshot">
                                    @else
                                        <span class="text-muted text-xs">—</span>
                                    @endif

                                    @if($attendance->vt_link)
                                        <a href="{{ $attendance->vt_link }}" target="_blank" rel="noopener noreferrer" class="badge badge-sm" style="background: #111; color: #fff; text-decoration: none;" title="Buka video VT TikTok">
                                            VT ↗
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($totalTiktok > 0)
                                    @if(abs($totalDiff) < 10)
                                        <span class="badge badge-success badge-sm" title="Sesuai ({{ $formatDuration(abs($totalDiff)) }})">✓ Sesuai</span>
                                    @elseif($totalDiff >= 10)
                                        <span class="badge badge-sm" style="background: #3b82f6; color: white;" title="TikTok +{{ $formatDuration($totalDiff) }}">↑ TT +{{ $formatDuration($totalDiff) }}</span>
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
                                    <span class="badge badge-success badge-xs" title="Tervalidasi" style="display: inline-flex; align-items: center; gap: 3px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="11" height="11">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Valid</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted" style="padding: 2.5rem;">
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
                        $rate = $scheme->getHourlyRateForPlatform($attendance->platform ?? 'tiktok');
                        $salary = ($hours * $rate)
                            + ($attendance->content_edit_count * $scheme->content_edit_rate)
                            + ($attendance->content_live_count * $scheme->content_live_rate);
                    }
                    $dateStr = \Carbon\Carbon::parse($attendance->attendance_date)->toDateString();
                    $totalTiktok = $tiktokTotalsByDate[$dateStr] ?? 0;
                    $totalAbsen = $attendanceTotalsByDate[$dateStr] ?? 0;
                    $totalDiff = $totalTiktok - $totalAbsen;
                    $pInfo = $attendance->platform_info;
                @endphp
                <div class="attendance-item">
                    <div class="attendance-date">
                        <div class="date-day">{{ $attendance->attendance_date->format('d') }}</div>
                        <div class="date-month">{{ $attendance->attendance_date->translatedFormat('M') }}</div>
                    </div>
                    <div class="attendance-info">
                        <div class="flex items-center gap-1 mb-0.5 flex-wrap">
                            <span class="attendance-duration">{{ $attendance->formatted_duration }}</span>
                            <span class="badge" style="background: {{ $pInfo['bg_badge'] ?? '#0f172a' }}; color: {{ $pInfo['text_badge'] ?? '#ffffff' }}; font-size: 0.62rem; padding: 2px 5px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px;">
                                <x-platform-logo :platform="$attendance->platform ?? 'tiktok'" size="2xs" />
                                <span>{{ $pInfo['short_name'] ?? 'Live' }}</span>
                            </span>
                            <span class="badge badge-secondary badge-xs" style="font-size: 0.65rem;">{{ $attendance->session_name ?? 'Sesi 1' }}</span>
                        </div>
                        <div class="attendance-meta">
                            Edit: {{ $attendance->content_edit_count }} • Live: {{ $attendance->content_live_count }} • Jual: {{ $attendance->sales_count }}
                        </div>
                        @if($attendance->proof_image || $attendance->vt_link)
                            <div class="flex items-center gap-2 mt-1">
                                @if($attendance->proof_image)
                                    <span onclick="openImageModal('{{ $attendance->proof_image_url }}')" style="font-size: 0.7rem; color: var(--primary); cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 3px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Bukti</span>
                                    </span>
                                @endif
                                @if($attendance->vt_link)
                                    <a href="{{ $attendance->vt_link }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.7rem; color: #6366f1;">
                                        VT ↗
                                    </a>
                                @endif
                            </div>
                        @endif
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
                                <span class="badge badge-success badge-sm" title="Tervalidasi">Valid</span>
                            @elseif($attendance->status === 'pending')
                                <span class="badge badge-warning badge-sm">Pending</span>
                                <a href="{{ route('user.attendances.edit', $attendance) }}" class="btn btn-secondary btn-xs" style="padding: 2px 6px; font-size: 0.7rem;">Edit</a>
                            @else
                                <span class="badge badge-danger badge-sm">Ditolak</span>
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

    <!-- Image Modal Lightbox -->
    <div id="imageModal" class="image-modal-overlay" onclick="closeImageModal()">
        <div class="image-modal-content" onclick="event.stopPropagation()">
            <div style="padding: 8px 12px; background: #1e293b; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.8rem; font-weight: 600;">Bukti Screenshot Live</span>
                <button type="button" onclick="closeImageModal()" style="background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; line-height: 1;">&times;</button>
            </div>
            <img id="modalImg" src="" alt="Bukti Live">
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openImageModal(url) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImg');
        img.src = url;
        modal.classList.add('active');
    }
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('active');
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeImageModal();
    });
</script>
@endpush
