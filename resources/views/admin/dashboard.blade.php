@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Grid - Mobile Optimized -->
    <div class="stats-grid-mobile mb-4">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total User</div>
                <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
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
                <div class="stat-number">{{ number_format($stats['total_live']) }}</div>
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
                <div class="stat-number">{{ number_format($stats['total_hours'], 1) }}</div>
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
                <div class="stat-number">Rp {{ number_format($stats['total_salary'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 0.75rem 1rem;">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-3"
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
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"
                        height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                @if($selectedMonth || $selectedYear)
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-2 mb-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Jam Live Per User</h3>
                <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
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
                <div class="chart-container">
                    <canvas id="salesPerUserChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Chart -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Aktivitas Harian</h3>
            <span class="text-muted" style="font-size: 0.75rem;">{{ $filterLabel }}</span>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 200px;">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Attendances -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Absensi Terbaru</h3>
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
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
                                    <span>{{ $attendance->user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td>{{ $attendance->formatted_duration }}</td>
                            <td>{{ $attendance->content_edit_count + $attendance->content_live_count }}</td>
                            <td>{{ $attendance->sales_count }}</td>
                            <td>
                                @if($attendance->status === 'validated')
                                    <span class="badge badge-success">Tervalidasi</span>
                                @elseif($attendance->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
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
                        borderRadius: 8,
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
                        borderRadius: 8,
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
                        tension: 0.4,
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