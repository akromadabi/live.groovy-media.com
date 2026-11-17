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
</style>
@endpush

@section('content')
    <!-- Dashboard Period & Control Bar -->
    <div class="dashboard-period-bar">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="period-filter-wrapper">
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
                <a href="{{ route('admin.dashboard') }}" class="period-reset-btn" title="Reset Periode">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset
                </a>
            @endif
        </form>

        <div class="dashboard-action-group">
            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary dashboard-action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Absensi
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-mobile mb-3">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total User</div>
                <div class="stat-number">{{ number_format($stats['total_users']) }} user</div>
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
                <div class="stat-label">Total Live</div>
                <div class="stat-number">{{ number_format($stats['total_live']) }} sesi</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Jam</div>
                <div class="stat-number">{{ number_format($stats['total_hours'], 1) }} jam</div>
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
                <div class="stat-label">Total Gaji</div>
                <div class="stat-number" style="color: var(--success); font-size: 0.95rem;">Rp {{ number_format($stats['total_salary'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-2 mb-3">
        <div class="card">
            <div class="card-header" style="padding: 0.5rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.875rem;">Jam Live Per User</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body" style="padding: 0.65rem 0.85rem;">
                <div class="chart-container" style="height: 200px;">
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
                <div class="chart-container" style="height: 200px;">
                    <canvas id="salesPerUserChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Chart -->
    <div class="card mb-3">
        <div class="card-header" style="padding: 0.5rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.875rem;">Aktivitas Harian</h3>
            <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
        </div>
        <div class="card-body" style="padding: 0.65rem 0.85rem;">
            <div class="chart-container" style="height: 180px;">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Attendances -->
    <div class="card">
        <div class="card-header flex items-center justify-between" style="padding: 0.5rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.875rem;">Absensi Terbaru</h3>
            <a href="{{ route('admin.attendances.index') }}" class="text-xs text-primary" style="font-weight: 500;">Lihat Semua &rarr;</a>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Konten</th>
                        <th>Penjualan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $attendance)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar avatar-sm">{{ substr($attendance->user->name, 0, 1) }}</div>
                                    <span class="font-medium text-sm">{{ $attendance->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">{{ $attendance->attendance_date->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-muted">{{ $attendance->attendance_date->translatedFormat('l') }}</div>
                            </td>
                            <td>
                                <span class="badge badge-secondary" style="font-weight: 600;">{{ $attendance->formatted_duration }}</span>
                            </td>
                            <td>
                                <span class="text-xs">{{ $attendance->content_edit_count }} edit / {{ $attendance->content_live_count }} live</span>
                            </td>
                            <td>
                                <span class="font-medium text-sm">{{ $attendance->sales_count }} pcs</span>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 2rem;">
                                Belum ada data absensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hours Per User Chart
            const hoursData = @json($hoursPerUser);
            new Chart(document.getElementById('hoursPerUserChart'), {
                type: 'bar',
                data: {
                    labels: hoursData.map(item => item.name),
                    datasets: [{
                        label: 'Jam Live',
                        data: hoursData.map(item => item.hours),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });

            // Sales Per User Chart
            const salesData = @json($salesPerUser);
            new Chart(document.getElementById('salesPerUserChart'), {
                type: 'bar',
                data: {
                    labels: salesData.map(item => item.name),
                    datasets: [{
                        label: 'Penjualan',
                        data: salesData.map(item => item.sales),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });

            // Daily Activity Chart
            const dailyData = @json($dailyAttendances);
            new Chart(document.getElementById('dailyActivityChart'), {
                type: 'line',
                data: {
                    labels: dailyData.map(item => {
                        const date = new Date(item.date);
                        return date.getDate();
                    }),
                    datasets: [{
                        label: 'Jam Live',
                        data: dailyData.map(item => Math.round(item.total_minutes / 60 * 10) / 10),
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
