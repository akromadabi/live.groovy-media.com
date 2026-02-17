@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Welcome Card -->
    <div class="card mb-6">
        <div class="card-body">
            <div class="flex items-center gap-4">
                <div class="avatar avatar-lg">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <h2 class="mb-1">Halo, {{ auth()->user()->name }}!</h2>
                    <p class="text-muted mb-0">{{ auth()->user()->task ?? 'User' }} •
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 0.75rem 1rem;">
            <form action="{{ route('user.dashboard') }}" method="GET" class="flex items-center gap-3"
                style="flex-wrap: wrap;">
                <span class="text-muted" style="font-size: 0.85rem;">Periode:</span>
                <select name="month" class="form-control form-select" style="width: auto; min-width: 120px;">
                    <option value="">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-control form-select" style="width: auto; min-width: 90px;">
                    <option value="">Semua Tahun</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if($selectedMonth || $selectedYear)
                    <a href="{{ route('user.dashboard') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Stats Grid - Mobile Optimized -->
    <div class="stats-grid-mobile mb-4">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Live</div>
                <div class="stat-number">{{ number_format($stats['total_live']) }}</div>
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
                <div class="stat-number">{{ number_format($stats['this_month_sales']) }}</div>
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
                <div class="stat-number">Rp {{ number_format($stats['estimated_salary'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Content Stats - berjajar seperti box atas -->
    <div class="stats-grid-mobile mb-4" style="grid-template-columns: repeat(2, 1fr);">
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

    <!-- T1/T2 Salary Grid -->
    <div class="term-salary-grid mb-4">
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
                <span class="term-period">16 - Akhir</span>
            </div>
            <div class="term-body">
                <div class="term-hours">{{ $termData['t2_hours'] ?? 0 }} <small>jam</small></div>
                <div class="term-salary">Rp {{ number_format($termData['t2_salary'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Personal Charts Grid -->
    <div class="grid grid-2 mb-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Jam Live Harian Saya</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 180px;">
                    <canvas id="hoursChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Penjualan Harian Saya</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 180px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- All Users Charts Grid -->
    <div class="grid grid-2 mb-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Jam Live Per User</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 220px;">
                    <canvas id="hoursPerUserChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Penjualan Per User</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 220px;">
                    <canvas id="salesPerUserChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent -->
    <div class="grid grid-2 mb-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('user.attendances.create') }}" class="btn btn-primary btn-block mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Input Absensi Baru
                </a>
                <a href="{{ route('user.attendances.index') }}" class="btn btn-secondary btn-block">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Lihat Riwayat Absensi
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Absensi Terbaru</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @forelse($recentAttendances as $attendance)
                    <div class="flex items-center justify-between"
                        style="padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border);">
                        <div>
                            <div class="font-medium">{{ $attendance->attendance_date->format('d M Y') }}</div>
                            <div class="text-sm text-muted">{{ $attendance->formatted_duration }}</div>
                        </div>
                        @if($attendance->status === 'validated')
                            <span class="badge badge-success" title="Locked by Admin" style="padding: 0.25rem 0.5rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                        @elseif($attendance->status === 'pending')
                            <span class="badge badge-warning" title="Open (Pending Validation)" style="padding: 0.25rem 0.5rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                            </span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted" style="padding: 2rem;">
                        Belum ada data absensi
                    </div>
                @endforelse
            </div>
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