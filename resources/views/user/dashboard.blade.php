@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Premium Dashboard Period Bar */
    .dashboard-period-bar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 12px);
        padding: 0.55rem 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 0.85rem;
    }

    .period-filter-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .period-badge-label {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--bg-secondary);
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        white-space: nowrap;
        border: 1px solid var(--border);
    }

    .period-badge-label svg {
        width: 14px;
        height: 14px;
        color: var(--primary);
    }

    .period-select-custom {
        height: 34px !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: var(--text-primary) !important;
        background-color: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: 6px !important;
        padding: 0 1.85rem 0 0.65rem !important;
        cursor: pointer;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.5rem center !important;
        background-size: 14px 14px !important;
        transition: border-color var(--transition), box-shadow var(--transition);
        width: auto !important;
        min-width: 125px;
    }

    .period-select-custom:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .period-reset-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        height: 34px;
        padding: 0 0.65rem;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-muted);
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 6px;
        text-decoration: none;
        transition: all var(--transition);
    }

    .period-reset-btn:hover {
        color: var(--danger);
        border-color: var(--danger);
        background: rgba(239, 68, 68, 0.05);
    }

    .dashboard-action-group {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        margin-left: auto;
    }

    .dashboard-action-btn {
        height: 34px !important;
        padding: 0 0.85rem !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
        border-radius: 6px !important;
        white-space: nowrap !important;
        box-shadow: 0 1px 2px rgba(99, 102, 241, 0.2);
    }

    @media (max-width: 768px) {
        .dashboard-period-bar {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 0.55rem 0.65rem !important;
            gap: 0.5rem !important;
        }

        .period-filter-wrapper {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.35rem !important;
            width: 100% !important;
        }

        .period-badge-label {
            grid-column: 1 / -1 !important;
            justify-content: center !important;
            font-size: 0.75rem !important;
            padding: 0.3rem 0.5rem !important;
        }

        .period-select-custom {
            width: 100% !important;
            min-width: 0 !important;
            font-size: 0.78rem !important;
        }

        .period-reset-btn {
            grid-column: 1 / -1 !important;
            justify-content: center !important;
            height: 30px !important;
        }

        .dashboard-action-group {
            width: 100% !important;
            margin-left: 0 !important;
        }

        .dashboard-action-btn {
            width: 100% !important;
            justify-content: center !important;
            height: 36px !important;
        }
    }
    .user-stats-grid-6 {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.5rem;
    }
    .user-target-row {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 0.65rem;
    }
    @media (max-width: 1024px) {
        .user-stats-grid-6 {
            grid-template-columns: repeat(3, 1fr);
        }
        .user-target-row {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 768px) {
        .user-stats-grid-6 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
    <!-- Dashboard Period & Control Bar -->
    <div class="dashboard-period-bar">
        <form action="{{ route('user.dashboard') }}" method="GET" class="period-filter-wrapper">
            <div class="period-badge-label">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Periode: <strong>{{ $filterLabel }}</strong></span>
            </div>

            <select name="month" class="period-select-custom" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <select name="year" class="period-select-custom" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            @if($selectedMonth || $selectedYear)
                <a href="{{ route('user.dashboard') }}" class="period-reset-btn" title="Reset Periode">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset
                </a>
            @endif
        </form>

        <div class="dashboard-action-group">
            <a href="{{ route('user.attendances.create') }}" class="btn btn-primary dashboard-action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Absensi
            </a>
        </div>
    </div>

    <!-- 6 Compact Stats Row -->
    <div class="user-stats-grid-6 mb-3">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Live</div>
                <div class="stat-number">{{ number_format($stats['total_live']) }}x</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">{{ $filterLabel }}</div>
                <div class="stat-number">{{ number_format($stats['this_month_hours'], 1) }} jam</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Penjualan</div>
                <div class="stat-number">{{ number_format($stats['this_month_sales']) }} pcs</div>
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
                    Rp {{ number_format($stats['estimated_salary'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Konten Edit</div>
                <div class="stat-number">{{ number_format($stats['this_month_content_edit']) }}</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Konten Live</div>
                <div class="stat-number">{{ number_format($stats['this_month_content_live']) }}</div>
            </div>
        </div>
    </div>

    <!-- T1/T2 & Target Achievement Row -->
    <div class="user-target-row mb-3">
        <!-- T1 & T2 Cards -->
        <div class="term-salary-grid" style="margin: 0;">
            <div class="term-card">
                <div class="term-header">
                    <span class="term-badge t1">T1</span>
                    <span class="term-period">1 - 15</span>
                </div>
                <div class="term-body">
                    <div class="term-hours">{{ $termData['t1_hours'] ?? 0 }} <small>jam</small></div>
                    <div class="term-salary">Rp {{ number_format($termData['t1_salary'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="term-card">
                <div class="term-header">
                    <span class="term-badge t2">T2</span>
                    <span class="term-period">16 - {{ $endDay }}</span>
                </div>
                <div class="term-body">
                    <div class="term-hours">{{ $termData['t2_hours'] ?? 0 }} <small>jam</small></div>
                    <div class="term-salary">Rp {{ number_format($termData['t2_salary'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Target Achievement Card -->
        <div class="card" style="margin: 0;">
            <div class="card-body" style="padding: 0.85rem 1rem;">
                <div class="flex items-center justify-between mb-2">
                    <h3 style="font-size: 0.875rem; font-weight: 600; margin: 0;">🎯 Target Bulan {{ \Carbon\Carbon::createFromDate($selectedYear ?: now()->year, $selectedMonth ?: now()->month, 1)->translatedFormat('F') }}</h3>
                    <span style="font-size: 0.8rem; font-weight: 600; color: {{ $targetMet ? 'var(--success)' : 'var(--danger, #ef4444)' }};">
                        {{ $monthlyHours }}/{{ $monthlyTarget }} jam ({{ $targetPercent }}%)
                    </span>
                </div>
                <!-- Progress Bar -->
                <div style="background: var(--border, #e5e7eb); border-radius: 8px; height: 10px; overflow: hidden; margin-bottom: 6px;">
                    <div style="background: {{ $targetMet ? 'linear-gradient(90deg, #10b981, #34d399)' : 'linear-gradient(90deg, #6366f1, #818cf8)' }}; height: 100%; border-radius: 8px; width: {{ min(100, $targetPercent) }}%; transition: width 0.5s ease;"></div>
                </div>
                <div class="flex items-center justify-between" style="font-size: 0.75rem;">
                    @if($targetMet)
                        <span style="color: var(--success, #10b981); font-weight: 600;">✅ Target Tercapai!</span>
                    @else
                        <span style="color: var(--text-muted, #6b7280);">Kurang {{ max(0, $monthlyTarget - $monthlyHours) }} jam lagi untuk klaim bonus</span>
                    @endif
                    <span style="color: var(--text-muted, #6b7280);">Bonus Penjualan: <strong style="color: {{ $targetMet ? 'var(--success)' : 'var(--text-muted)' }};">Rp {{ number_format($monthlyBonus, 0, ',', '.') }}</strong></span>
                </div>

                @php
                    $t1 = $termData['t1_salary'] ?? 0;
                    $t2 = $termData['t2_salary'] ?? 0;
                    $bonus = $targetMet ? ($monthlyBonus ?? 0) : 0;
                    $totalSalary = $t1 + $t2 + $bonus;
                @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed var(--border, #e5e7eb); padding-top: 6px; margin-top: 8px;">
                    <span style="font-size: 0.8rem; font-weight: 600;">Estimasi Total Payout (T1 + T2 + Bonus):</span>
                    <span style="font-size: 1rem; font-weight: 800; color: var(--primary);">Rp {{ number_format($totalSalary, 0, ',', '.') }}</span>
                </div>

                @if(!empty($bonusGamification))
                    <!-- Gamification Bonus Tier Progress Bar -->
                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed var(--border, #e5e7eb);">
                        <div class="flex items-center justify-between mb-1" style="font-size: 0.75rem;">
                            <span style="font-weight: 600; color: #6366f1;">
                                🚀 Progres Bonus Tier ({{ $bonusGamification['current_sales'] }}/{{ $bonusGamification['next_target'] }} pcs)
                            </span>
                            <span style="font-weight: 700; color: #10b981;">
                                Kurang {{ $bonusGamification['remaining_sales'] }} pcs lagi!
                            </span>
                        </div>
                        <div style="background: rgba(0,0,0,0.06); border-radius: 999px; height: 8px; overflow: hidden; margin-bottom: 4px;">
                            <div style="background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%); height: 100%; border-radius: 999px; width: {{ $bonusGamification['progress_percent'] }}%; transition: width 0.6s ease;"></div>
                        </div>
                        <div class="flex items-center justify-between" style="font-size: 0.7rem; color: var(--text-muted);">
                            <span>Tiap {{ $bonusGamification['threshold'] }} pcs = +Rp {{ number_format($bonusGamification['bonus_per_tier'], 0, ',', '.') }}</span>
                            <span>Target Level {{ $bonusGamification['current_multiplier'] + 1 }}: Total Bonus Rp {{ number_format($bonusGamification['next_bonus_total'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Personal Charts Grid -->
    <div class="grid grid-2 mb-3">
        <div class="card">
            <div class="card-header" style="padding: 0.5rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.875rem;">Jam Live Harian Saya</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body" style="padding: 0.65rem 0.85rem;">
                <div class="chart-container" style="height: 170px;">
                    <canvas id="hoursChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding: 0.5rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.875rem;">Penjualan Harian Saya</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body" style="padding: 0.65rem 0.85rem;">
                <div class="chart-container" style="height: 170px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- All Users Charts Grid -->
    <div class="grid grid-2 mb-3">
        <div class="card">
            <div class="card-header" style="padding: 0.5rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.875rem;">Jam Live Per User</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body" style="padding: 0.65rem 0.85rem;">
                <div class="chart-container" style="height: 190px;">
                    <canvas id="hoursPerUserChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="padding: 0.5rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.875rem;">Penjualan Per User</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body" style="padding: 0.65rem 0.85rem;">
                <div class="chart-container" style="height: 190px;">
                    <canvas id="salesPerUserChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendances -->
    <div class="card mb-3">
        <div class="card-header flex items-center justify-between" style="padding: 0.5rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.875rem;">Absensi Terbaru</h3>
            <a href="{{ route('user.attendances.index') }}" class="text-xs text-primary" style="font-weight: 500;">Lihat Semua &rarr;</a>
        </div>
        <div class="card-body" style="padding: 0;">
            @forelse($recentAttendances as $attendance)
                <div class="flex items-center justify-between"
                    style="padding: 0.5rem 0.85rem; border-bottom: 1px solid var(--border);">
                    <div>
                        <div class="font-medium text-sm">{{ $attendance->attendance_date->translatedFormat('d M Y') }}</div>
                        <div class="text-xs text-muted">{{ $attendance->formatted_duration }} • Jual: {{ $attendance->sales_count }}</div>
                    </div>
                    <div>
                        @if($attendance->status === 'validated')
                            <span class="badge badge-success badge-sm" title="Tervalidasi">🔒 Tervalidasi</span>
                        @elseif($attendance->status === 'pending')
                            <span class="badge badge-warning badge-sm" title="Pending">⏳ Pending</span>
                        @else
                            <span class="badge badge-danger badge-sm">✕ Ditolak</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted" style="padding: 1.5rem; font-size: 0.85rem;">
                    Belum ada data absensi
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthlyData = @json($monthlyAttendance);
            const labels = monthlyData.map(item => new Date(item.date).getDate());

            // My Hours Chart
            new Chart(document.getElementById('hoursChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jam',
                        data: monthlyData.map(item => Math.round(item.total_minutes / 60 * 10) / 10),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderRadius: 4,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // My Sales Chart
            new Chart(document.getElementById('salesChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Penjualan',
                        data: monthlyData.map(item => item.total_sales || 0),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 4,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // Hours Per User Chart
            const hoursPerUserData = @json($hoursPerUser);
            new Chart(document.getElementById('hoursPerUserChart'), {
                type: 'bar',
                data: {
                    labels: hoursPerUserData.map(item => item.name),
                    datasets: [{
                        label: 'Jam',
                        data: hoursPerUserData.map(item => item.hours),
                        backgroundColor: hoursPerUserData.map((item, idx) => {
                            const colors = ['rgba(99, 102, 241, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(251, 191, 36, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)'];
                            return colors[idx % colors.length];
                        }),
                        borderRadius: 4,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });

            // Sales Per User Chart
            const salesPerUserData = @json($salesPerUser);
            new Chart(document.getElementById('salesPerUserChart'), {
                type: 'bar',
                data: {
                    labels: salesPerUserData.map(item => item.name),
                    datasets: [{
                        label: 'Penjualan',
                        data: salesPerUserData.map(item => item.sales),
                        backgroundColor: salesPerUserData.map((item, idx) => {
                            const colors = ['rgba(16, 185, 129, 0.8)', 'rgba(99, 102, 241, 0.8)', 'rgba(251, 191, 36, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)'];
                            return colors[idx % colors.length];
                        }),
                        borderRadius: 4,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });
        });
    </script>
@endpush
