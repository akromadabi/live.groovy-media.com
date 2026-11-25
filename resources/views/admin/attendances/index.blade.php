@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@php
    function sortUrl($column) {
        $currentSort = request('sort', 'attendance_date');
        $currentDirection = request('direction', 'desc');
        
        $direction = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
        
        return request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $direction
        ]);
    }
    
    function sortIcon($column) {
        $currentSort = request('sort', 'attendance_date');
        $currentDirection = request('direction', 'desc');
        
        if ($currentSort !== $column) {
            return '<span style="opacity: 0.3; font-size: 0.8rem;">↕</span>';
        }
        
        return $currentDirection === 'asc' 
            ? '<span style="color: var(--primary); font-size: 0.8rem;">▲</span>' 
            : '<span style="color: var(--primary); font-size: 0.8rem;">▼</span>';
    }
@endphp

@push('styles')
    <style>
        .sortable-link {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: color var(--transition);
        }
        .sortable-link:hover {
            color: var(--primary);
        }

        /* Top Control Bar */
        .attendance-control-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg, 10px);
            padding: 0.5rem 0.875rem;
            margin-bottom: 0.625rem;
            box-shadow: var(--shadow-sm);
        }

        .attendance-filter-form {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
            flex: 1;
        }

        .attendance-filter-form .filter-select {
            height: 32px;
            font-size: 0.8rem;
            padding: 0.2rem 1.8rem 0.2rem 0.6rem;
            width: auto;
            border-radius: 6px;
            background-color: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-primary);
        }

        .attendance-filter-form .filter-user { min-width: 130px; }
        .attendance-filter-form .filter-year { min-width: 85px; }
        .attendance-filter-form .filter-month { min-width: 105px; }
        .attendance-filter-form .filter-term { min-width: 110px; }
        .attendance-filter-form .filter-status { min-width: 115px; }

        .attendance-btn-reset {
            height: 32px;
            padding: 0.2rem 0.65rem;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .attendance-action-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-shrink: 0;
        }

        .attendance-action-btn {
            height: 32px;
            padding: 0.25rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 6px;
        }

        /* 6-Card Stats Grid Desktop */
        .attendance-stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .attendance-stats-grid .stat-card-compact {
            padding: 0.45rem 0.6rem;
            min-height: 48px;
            border-radius: var(--radius-lg, 10px);
            background: var(--surface);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.55rem;
            box-shadow: var(--shadow-sm);
        }

        .attendance-stats-grid .stat-number {
            font-size: 0.95rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Mobile View Cards */
        .attendance-mobile-container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .mobile-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        .mobile-select-all-label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 0;
            user-select: none;
            color: var(--text-primary);
        }

        .attendance-card-mobile {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.65rem 0.75rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            transition: border-color var(--transition);
        }

        .attendance-card-mobile:hover {
            border-color: var(--primary);
        }

        .card-mobile-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .card-mobile-user {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .card-mobile-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            line-height: 1.2;
        }

        .card-mobile-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
            padding-top: 0.3rem;
            border-top: 1px dashed var(--border);
        }

        .card-mobile-meta-left {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .card-mobile-badge-duration {
            font-weight: 700;
            color: var(--text-primary);
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            padding: 0.15rem 0.4rem;
            border-radius: 5px;
            font-size: 0.72rem;
            white-space: nowrap;
        }

        .card-mobile-stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr;
            gap: 0.35rem;
            background: var(--bg-secondary);
            padding: 0.4rem 0.5rem;
            border-radius: 6px;
        }

        .card-stat-box {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .card-stat-lbl {
            font-size: 0.65rem;
            color: var(--text-muted);
            line-height: 1.1;
        }

        .card-stat-val {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-mobile-actions {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding-top: 0.35rem;
            border-top: 1px solid var(--border);
        }

        .card-mobile-actions .btn {
            flex: 1;
            height: 28px;
            padding: 0 0.35rem;
            font-size: 0.72rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            border-radius: 6px;
            white-space: nowrap;
        }

        .card-mobile-actions .btn svg {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }

        /* Responsive Breakpoint (<= 768px) */
        @media (max-width: 768px) {
            .attendance-control-bar {
                flex-direction: column;
                align-items: stretch;
                padding: 0.55rem;
                gap: 0.45rem;
            }

            .attendance-action-group {
                width: 100%;
                order: -1;
            }

            .attendance-action-btn {
                width: 100%;
                height: 36px;
                justify-content: center;
                font-size: 0.8125rem;
            }

            .attendance-filter-form {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.35rem;
                width: 100%;
            }

            .attendance-filter-form .filter-select {
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                height: 33px;
                font-size: 0.75rem;
                padding: 0.15rem 1.6rem 0.15rem 0.45rem;
            }

            .attendance-filter-form .filter-user {
                grid-column: 1 / -1;
            }

            .attendance-filter-form .attendance-btn-reset {
                grid-column: 1 / -1;
                width: 100%;
                justify-content: center;
                height: 30px;
                font-size: 0.75rem;
            }

            /* 2-Column Stats Grid on Mobile */
            .attendance-stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.35rem !important;
                margin-bottom: 0.5rem;
            }

            .attendance-stats-grid .stat-card-compact {
                padding: 0.35rem 0.45rem;
                min-height: 44px;
                gap: 0.4rem;
            }

            .attendance-stats-grid .stat-icon-sm {
                width: 30px;
                height: 30px;
                flex-shrink: 0;
            }

            .attendance-stats-grid .stat-label {
                font-size: 0.62rem !important;
                line-height: 1.1;
            }

            .attendance-stats-grid .stat-number {
                font-size: 0.82rem !important;
                line-height: 1.15;
                margin-top: 1px;
            }

            /* Term Salary Grid on Mobile */
            .term-salary-grid {
                gap: 0.35rem !important;
            }
            .term-card {
                padding: 0.65rem 0.5rem !important;
            }
            .term-header {
                margin-bottom: 0.4rem !important;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Unified Top Control Bar (Filter Left + Action Right) -->
    <div class="attendance-control-bar">
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="attendance-filter-form">
            <select name="user_id" class="form-control form-select filter-select filter-user" onchange="this.form.submit()">
                <option value="">Semua User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="year" class="form-control form-select filter-select filter-year" onchange="this.form.submit()">
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="form-control form-select filter-select filter-month" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="term" class="form-control form-select filter-select filter-term" onchange="this.form.submit()">
                <option value="" {{ request('term') == '' ? 'selected' : '' }}>Semua Termin</option>
                <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>T1 (1-15)</option>
                <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>T2 (16-Akhir)</option>
            </select>
            <select name="status" class="form-control form-select filter-select filter-status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Tervalidasi</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="platform" class="form-control form-select filter-select" style="min-width: 120px;" onchange="this.form.submit()">
                <option value="">Semua Platform</option>
                @foreach($allMarketplaces as $mpKey => $mp)
                    @if(!empty($mp['enabled']) || request('platform') === $mpKey)
                        <option value="{{ $mpKey }}" {{ request('platform') == $mpKey ? 'selected' : '' }}>
                            {{ $mp['name'] }}
                        </option>
                    @endif
                @endforeach
            </select>
            @if(request()->hasAny(['user_id', 'term', 'status', 'platform']))
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-sm attendance-btn-reset">
                    Reset
                </a>
            @endif
        </form>
        <div class="attendance-action-group">
            <a href="{{ route('admin.attendances.matrix', ['year' => request('year', now()->year), 'month' => request('month', now()->month)]) }}" class="btn btn-secondary attendance-action-btn" style="border-color: #6366f1; color: #6366f1;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Matriks Kalender
            </a>
            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary attendance-action-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Absensi
            </a>
        </div>
    </div>

    <!-- Unified 6-Card Stats Grid -->
    <div class="attendance-stats-grid">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label" style="font-size: 0.68rem;">Total Live</div>
                <div class="stat-number">{{ number_format($totals['total_live']) }}</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label" style="font-size: 0.68rem;">Total Jam</div>
                <div class="stat-number">{{ number_format($totals['total_hours'], 1) }}j</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label" style="font-size: 0.68rem;">Penjualan</div>
                <div class="stat-number">{{ number_format($totals['total_sales']) }}</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label" style="font-size: 0.68rem;">Gaji</div>
                <div class="stat-number">
                    @if($bonusInfo)
                        Rp {{ number_format(($bonusInfo['total_salary'] ?? 0) + ($bonusInfo['target_met'] ? $bonusInfo['sales_bonus'] : 0), 0, ',', '.') }}
                    @else
                        Rp -
                    @endif
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
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label" style="font-size: 0.68rem;">Konten Edit</div>
                <div class="stat-number">{{ number_format($totals['total_content_edit']) }}</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label" style="font-size: 0.68rem;">Konten Live</div>
                <div class="stat-number">{{ number_format($totals['total_content_live']) }}</div>
            </div>
        </div>
    </div>

    <!-- Salary Summary (only shown when specific user is selected) -->
    @if($bonusInfo)
        <div class="salary-summary-container mb-4">
            <!-- T1/T2 Salary Grid -->
            <div class="term-salary-grid mb-3">
                <div class="term-card">
                    <div class="term-header">
                        <span class="term-badge t1">T1</span>
                        <span class="term-period">1 - 15</span>
                    </div>
                    <div class="term-body">
                        <div class="term-hours">{{ $bonusInfo['termData']['t1']['hours'] }} <small>jam</small></div>
                        <div class="term-salary">Rp {{ number_format($bonusInfo['termData']['t1']['salary'], 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="term-card">
                    <div class="term-header">
                        <span class="term-badge t2">T2</span>
                        <span class="term-period">16 - Akhir</span>
                    </div>
                    <div class="term-body">
                        <div class="term-hours">{{ $bonusInfo['termData']['t2']['hours'] }} <small>jam</small></div>
                        <div class="term-salary">Rp {{ number_format($bonusInfo['termData']['t2']['salary'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Target Achievement Card -->
            <div class="card mb-3">
                <div class="card-body">
                    @php
                        $targetPercent = $bonusInfo['target_hours'] > 0 ? min(100, round(($bonusInfo['monthly_hours'] / $bonusInfo['target_hours']) * 100)) : 0;
                        $selectedYear = request('year', now()->year);
                        $selectedMonth = request('month', now()->month);
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <h3 style="font-size: 0.95rem; font-weight: 600; margin: 0;">Target {{ $bonusInfo['user_name'] }} - Bulan {{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F') }}</h3>
                        <span style="font-size: 0.8rem; font-weight: 600; color: {{ $bonusInfo['target_met'] ? 'var(--success)' : 'var(--danger, #ef4444)' }};">
                            {{ $bonusInfo['monthly_hours'] }}/{{ $bonusInfo['target_hours'] }} jam
                        </span>
                    </div>
                    <!-- Progress Bar -->
                    <div style="background: var(--border, #e5e7eb); border-radius: 10px; height: 12px; overflow: hidden; margin-bottom: 8px;">
                        <div style="background: {{ $bonusInfo['target_met'] ? 'linear-gradient(90deg, #10b981, #34d399)' : 'linear-gradient(90deg, #6366f1, #818cf8)' }}; height: 100%; border-radius: 10px; width: {{ $targetPercent }}%; transition: width 0.5s ease;">
                        </div>
                    </div>
                    <div class="flex items-center justify-between" style="font-size: 0.8rem;">
                        <span style="color: var(--text-muted, #6b7280);">{{ $targetPercent }}%</span>
                        @if($bonusInfo['target_met'])
                            <span style="color: var(--success, #10b981); font-weight: 600;">Target Tercapai</span>
                        @else
                            <span style="color: var(--text-muted, #6b7280);">Kurang {{ max(0, $bonusInfo['target_hours'] - $bonusInfo['monthly_hours']) }} jam lagi</span>
                        @endif
                    </div>

                    @if($bonusInfo['target_hours'] > 0)
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border, #e5e7eb); display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.85rem; color: var(--text-muted, #6b7280);">Bonus Penjualan:</span>
                                <div>
                                    @if($bonusInfo['target_met'])
                                        <span style="font-size: 1rem; font-weight: 700; color: var(--success, #10b981);">Rp {{ number_format($bonusInfo['sales_bonus'], 0, ',', '.') }}</span>
                                    @else
                                        <span style="font-size: 1rem; font-weight: 700; color: var(--text-muted, #6b7280);">Rp {{ number_format($bonusInfo['sales_bonus'], 0, ',', '.') }}</span>
                                        <span style="font-size: 0.75rem; color: var(--text-muted, #6b7280);">(target belum tercapai)</span>
                                    @endif
                                </div>
                            </div>
                            
                            @php
                                $t1 = $bonusInfo['termData']['t1']['salary'] ?? 0;
                                $t2 = $bonusInfo['termData']['t2']['salary'] ?? 0;
                                $bonus = $bonusInfo['target_met'] ? ($bonusInfo['sales_bonus'] ?? 0) : 0;
                                $totalSalary = $t1 + $t2 + $bonus;
                            @endphp
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed var(--border, #e5e7eb); padding-top: 8px; margin-top: 4px;">
                                <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-color, #1e293b);">Estimasi Total Gaji (T1 + T2 + Bonus):</span>
                                <span style="font-size: 1.1rem; font-weight: 800; color: var(--primary-color, #4f46e5);">Rp {{ number_format($totalSalary, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        @php
                            $t1 = $bonusInfo['termData']['t1']['salary'] ?? 0;
                            $t2 = $bonusInfo['termData']['t2']['salary'] ?? 0;
                            $totalSalary = $t1 + $t2;
                        @endphp
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border, #e5e7eb); display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-color, #1e293b);">Estimasi Total Gaji (T1 + T2):</span>
                            <span style="font-size: 1.1rem; font-weight: 800; color: var(--primary-color, #4f46e5);">Rp {{ number_format($totalSalary, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Action Bar (hidden by default) -->
    <div id="bulk-action-bar" class="card mb-3" style="display: none;">
        <div class="card-body" style="padding: 0.75rem 1rem;">
            <div class="flex items-center justify-between" style="flex-wrap: wrap; gap: 0.5rem;">
                <span id="selected-count" style="font-weight: 600; font-size: 0.85rem;">0 item dipilih</span>
                <div class="flex gap-2">
                    <button type="button" onclick="bulkValidate()" class="btn btn-success btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Validasi Masal
                    </button>
                    <button type="button" onclick="bulkDelete()" class="btn btn-danger btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Masal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms for Bulk Actions -->
    <form id="bulk-validate-form" action="{{ route('admin.attendances.bulk-validate') }}" method="POST" style="display:none;">
        @csrf
    </form>
    <form id="bulk-delete-form" action="{{ route('admin.attendances.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <!-- Desktop View Table (Hidden on Mobile) -->
    <div class="card hide-on-mobile mb-3">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)">
                        </th>
                        <th>
                            <a href="{{ sortUrl('user') }}" class="sortable-link">
                                User {!! sortIcon('user') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('attendance_date') }}" class="sortable-link">
                                Tanggal & Sesi {!! sortIcon('attendance_date') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('created_at') }}" class="sortable-link">
                                Waktu Input {!! sortIcon('created_at') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('live_duration_minutes') }}" class="sortable-link">
                                Durasi {!! sortIcon('live_duration_minutes') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('content_edit_count') }}" class="sortable-link">
                                Konten Edit {!! sortIcon('content_edit_count') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('content_live_count') }}" class="sortable-link">
                                Konten Live {!! sortIcon('content_live_count') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('sales_count') }}" class="sortable-link">
                                Penjualan {!! sortIcon('sales_count') !!}
                            </a>
                        </th>
                        <th>Gaji</th>
                        <th>Bukti & VT</th>
                        <th>Cek TikTok (Discrepancy)</th>
                        <th>
                            <a href="{{ sortUrl('status') }}" class="sortable-link">
                                Status {!! sortIcon('status') !!}
                            </a>
                        </th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            $scheme = $attendance->user->salaryScheme;
                            $salary = 0;
                            if ($scheme) {
                                $hours = $attendance->live_duration_minutes / 60;
                                $rate = $scheme->getHourlyRateForPlatform($attendance->platform ?? 'tiktok');
                                $salary =
                                    $hours * $rate +
                                    $attendance->content_edit_count * $scheme->content_edit_rate +
                                    $attendance->content_live_count * $scheme->content_live_rate +
                                    \App\Models\BonusTier::getBonusForSales($attendance->sales_count);
                            }
                            $key = $attendance->user_id . '_' . $attendance->attendance_date->toDateString();
                            $tt = $tiktokTotals[$key] ?? null;
                            $pInfo = $attendance->platform_info;
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" class="desktop-row-checkbox" value="{{ $attendance->id }}"
                                    onchange="updateSelection()">
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar avatar-sm">{{ substr($attendance->user->name, 0, 1) }}</div>
                                    <div>
                                        <div class="font-medium">{{ $attendance->user->name }}</div>
                                        <div class="text-xs text-muted">{{ $attendance->user->task }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $attendance->attendance_date->format('d M Y') }}</div>
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
                                <div style="font-weight: 500;">
                                    {{ $attendance->created_at ? $attendance->created_at->format('d M Y') : '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">
                                    {{ $attendance->created_at ? $attendance->created_at->format('H:i') : '' }}
                                </div>
                            </td>
                            <td>{{ $attendance->formatted_duration }}</td>
                            <td>{{ $attendance->content_edit_count }}</td>
                            <td>{{ $attendance->content_live_count }}</td>
                            <td>{{ $attendance->sales_count }}</td>
                            <td class="font-medium" style="color: var(--success);">
                                Rp {{ number_format($salary, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($attendance->proof_image)
                                        <img src="{{ $attendance->proof_image_url }}" alt="Bukti" class="proof-thumb" onclick="openImageModal('{{ $attendance->proof_image_url }}')" title="Lihat bukti screenshot" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; cursor: pointer; border: 1px solid var(--border);">
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
                                @if($tt)
                                    @php
                                        $diff = $attendance->live_duration_minutes - $tt['total_duration'];
                                    @endphp
                                    @if(abs($diff) <= 15)
                                        <span class="badge badge-success badge-xs" title="Durasi cocok dengan data TikTok (selisih {{ $diff >= 0 ? '+' : '' }}{{ $diff }}m)">
                                            Cocok
                                        </span>
                                    @elseif($diff > 15)
                                        <span class="badge badge-danger badge-xs" style="background: #ef4444; color: #fff; font-weight: 600;" title="Klaim host melebihi data TikTok! (Klaim: {{ $attendance->formatted_duration }} vs TikTok: {{ round($tt['total_duration']/60, 1) }} jam)">
                                            Selisih +{{ round($diff / 60, 1) }}j
                                        </span>
                                    @else
                                        <span class="badge badge-info badge-xs" style="background: #3b82f6; color: #fff;" title="TikTok mencatat durasi lebih banyak (TikTok: {{ round($tt['total_duration']/60, 1) }} jam)">
                                            ↑ TT +{{ round(abs($diff) / 60, 1) }}j
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-muted" style="opacity: 0.6;">Belum ada TT</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status === 'validated')
                                    <span class="badge badge-success">Tervalidasi</span>
                                @elseif($attendance->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns" style="justify-content: center;">
                                    @if($attendance->status === 'pending')
                                        <form action="{{ route('admin.attendances.validate', $attendance) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm btn-icon" title="Validasi">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.reject', $attendance) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Tolak">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.attendances.edit', $attendance) }}"
                                        class="btn btn-secondary btn-sm btn-icon" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.attendances.destroy', $attendance) }}" method="POST"
                                        style="display:inline;" id="delete-{{ $attendance->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-{{ $attendance->id }}')"
                                            class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" width="16" height="16">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted" style="padding: 2.5rem;">Tidak ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View Cards (Hidden on Desktop) -->
    <div class="attendance-mobile-container hide-on-desktop mb-3">
        @if($attendances->count() > 0)
            <div class="mobile-list-header">
                <label class="mobile-select-all-label">
                    <input type="checkbox" id="select-all-mobile" onchange="toggleSelectAll(this)">
                    <span>Pilih Semua ({{ $attendances->total() }})</span>
                </label>
                <span class="text-xs text-muted">{{ $attendances->firstItem() }}-{{ $attendances->lastItem() }} dari {{ $attendances->total() }}</span>
            </div>

            @foreach($attendances as $attendance)
                @php
                    $scheme = $attendance->user->salaryScheme;
                    $salary = 0;
                    if ($scheme) {
                        $hours = $attendance->live_duration_minutes / 60;
                        $rate = $scheme->getHourlyRateForPlatform($attendance->platform ?? 'tiktok');
                        $salary =
                            $hours * $rate +
                            $attendance->content_edit_count * $scheme->content_edit_rate +
                            $attendance->content_live_count * $scheme->content_live_rate +
                            \App\Models\BonusTier::getBonusForSales($attendance->sales_count);
                    }
                    $key = $attendance->user_id . '_' . $attendance->attendance_date->toDateString();
                    $tt = $tiktokTotals[$key] ?? null;
                    $pInfo = $attendance->platform_info;
                @endphp
                <div class="attendance-card-mobile">
                    <!-- Top Row: Checkbox, Avatar, User Info, Status Badge -->
                    <div class="card-mobile-top">
                        <div class="flex items-center gap-2" style="min-width: 0;">
                            <input type="checkbox" class="mobile-row-checkbox" value="{{ $attendance->id }}" onchange="updateSelection()">
                            <div class="avatar avatar-sm" style="flex-shrink: 0; width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($attendance->user->name, 0, 1) }}
                            </div>
                            <div style="min-width: 0;">
                                <div class="card-mobile-user text-truncate" title="{{ $attendance->user->name }}">
                                    {{ $attendance->user->name }}
                                </div>
                                <div class="card-mobile-sub text-truncate">
                                    {{ $attendance->user->task }}
                                </div>
                            </div>
                        </div>
                        <div style="flex-shrink: 0;">
                            @if($attendance->status === 'validated')
                                <span class="badge badge-success badge-sm">Tervalidasi</span>
                            @elseif($attendance->status === 'pending')
                                <span class="badge badge-warning badge-sm">Pending</span>
                            @else
                                <span class="badge badge-danger badge-sm">Ditolak</span>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Row: Date, Input Time, Duration -->
                    <div class="card-mobile-meta-row">
                        <div class="card-mobile-meta-left flex items-center gap-1 flex-wrap">
                            <span class="badge" style="background: {{ $pInfo['bg_badge'] ?? '#0f172a' }}; color: {{ $pInfo['text_badge'] ?? '#ffffff' }}; font-size: 0.62rem; padding: 2px 5px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px;">
                                <x-platform-logo :platform="$attendance->platform ?? 'tiktok'" size="2xs" />
                                <span>{{ $pInfo['short_name'] ?? 'Live' }}</span>
                            </span>
                            <span style="font-weight: 600; color: var(--text-primary);">
                                {{ $attendance->attendance_date->translatedFormat('d M Y') }}
                            </span>
                            <span class="badge badge-secondary badge-xs" style="font-size: 0.65rem;">{{ $attendance->session_name ?? 'Sesi 1' }}</span>
                            @if($attendance->created_at)
                                <span class="text-muted" style="font-size: 0.7rem;">
                                    ({{ $attendance->created_at->format('H:i') }})
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="card-mobile-badge-duration">⏱ {{ $attendance->formatted_duration }}</span>
                        </div>
                    </div>

                    <!-- Discrepancy & Proof Badges Row -->
                    <div class="flex items-center justify-between gap-2" style="font-size: 0.72rem; padding: 2px 0;">
                        <div>
                            @if($tt)
                                @php
                                    $diff = $attendance->live_duration_minutes - $tt['total_duration'];
                                @endphp
                                @if(abs($diff) <= 15)
                                    <span class="badge badge-success badge-xs">Sesuai TT</span>
                                @elseif($diff > 15)
                                    <span class="badge badge-danger badge-xs" style="background: #ef4444; color: #fff; font-weight: 600;">
                                        Selisih +{{ round($diff / 60, 1) }}j
                                    </span>
                                @else
                                    <span class="badge badge-info badge-xs" style="background: #3b82f6; color: #fff;">
                                        ↑ TT +{{ round(abs($diff) / 60, 1) }}j
                                    </span>
                                @endif
                            @else
                                <span class="text-muted" style="opacity: 0.6;">Belum ada TT</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($attendance->proof_image)
                                <span onclick="openImageModal('{{ $attendance->proof_image_url }}')" style="color: var(--primary); font-weight: 600; cursor: pointer; text-decoration: underline; display: inline-flex; align-items: center; gap: 3px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Bukti</span>
                                </span>
                            @endif
                            @if($attendance->vt_link)
                                <a href="{{ $attendance->vt_link }}" target="_blank" rel="noopener noreferrer" style="color: #6366f1; font-weight: 600;">
                                    VT ↗
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Stats Row: Edit/Live, Penjualan, Gaji -->
                    <div class="card-mobile-stats-row">
                        <div class="card-stat-box">
                            <span class="card-stat-lbl">Edit / Live</span>
                            <span class="card-stat-val">{{ $attendance->content_edit_count }} / {{ $attendance->content_live_count }}</span>
                        </div>
                        <div class="card-stat-box">
                            <span class="card-stat-lbl">Penjualan</span>
                            <span class="card-stat-val">{{ $attendance->sales_count }} pcs</span>
                        </div>
                        <div class="card-stat-box" style="text-align: right;">
                            <span class="card-stat-lbl">Gaji</span>
                            <span class="card-stat-val" style="color: var(--success); font-weight: 700;">
                                Rp {{ number_format($salary, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="card-mobile-actions">
                        @if($attendance->status === 'pending')
                            <form action="{{ route('admin.attendances.validate', $attendance) }}" method="POST" style="flex: 1; display: flex;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100" title="Validasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Validasi
                                </button>
                            </form>
                            <form action="{{ route('admin.attendances.reject', $attendance) }}" method="POST" style="flex: 1; display: flex;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm w-100" title="Tolak">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.attendances.edit', $attendance) }}" class="btn btn-secondary btn-sm" style="flex: 1;" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.attendances.destroy', $attendance) }}" method="POST" style="flex: 1; display: flex;" id="delete-mobile-{{ $attendance->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-mobile-{{ $attendance->id }}')" class="btn btn-danger btn-sm w-100" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card p-4 text-center text-muted" style="border-radius: 10px; margin-bottom: 0.75rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="36" height="36" style="opacity: 0.35; margin: 0 auto 0.5rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <div style="font-size: 0.85rem; font-weight: 500;">Tidak ada data absensi.</div>
            </div>
        @endif
    </div>

    <!-- Pagination for both Desktop and Mobile -->
    @if($attendances->hasPages())
        <div class="card-footer" style="margin-top: 0.75rem; border-top: none; background: transparent; padding: 0.25rem 0;">
            {{ $attendances->withQueryString()->links() }}
        </div>
    @endif

    <!-- Image Modal Lightbox -->
    <div id="imageModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;" onclick="closeImageModal()">
        <div style="max-width: 90vw; max-height: 90vh; border-radius: 8px; overflow: hidden; background: #000; box-shadow: 0 10px 25px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">
            <div style="padding: 8px 12px; background: #1e293b; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.8rem; font-weight: 600;">Bukti Screenshot Host</span>
                <button type="button" onclick="closeImageModal()" style="background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; line-height: 1;">&times;</button>
            </div>
            <img id="modalImg" src="" alt="Bukti Live" style="display: block; max-width: 100%; max-height: 85vh; object-fit: contain;">
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openImageModal(url) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImg');
            img.src = url;
            modal.style.display = 'flex';
        }
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeImageModal();
        });

        function toggleSelectAll(source) {
            const isMobile = window.innerWidth <= 768;
            const targetSelector = isMobile ? '.mobile-row-checkbox' : '.desktop-row-checkbox';
            const checkboxes = document.querySelectorAll(targetSelector);
            checkboxes.forEach(cb => cb.checked = source.checked);
            
            // Sync the other selectAll checkbox
            const desktopSelectAll = document.getElementById('select-all');
            const mobileSelectAll = document.getElementById('select-all-mobile');
            if (desktopSelectAll) desktopSelectAll.checked = source.checked;
            if (mobileSelectAll) mobileSelectAll.checked = source.checked;

            updateSelection();
        }

        function updateSelection() {
            const isMobile = window.innerWidth <= 768;
            const selector = isMobile ? '.mobile-row-checkbox:checked' : '.desktop-row-checkbox:checked';
            const checkboxes = document.querySelectorAll(selector);
            const uniqueIds = Array.from(new Set(Array.from(checkboxes).map(cb => cb.value)));
            const count = uniqueIds.length;
            const bulkBar = document.getElementById('bulk-action-bar');
            const countSpan = document.getElementById('selected-count');

            if (count > 0) {
                bulkBar.style.display = 'block';
                countSpan.textContent = count + ' item dipilih';
            } else {
                bulkBar.style.display = 'none';
            }
        }

        function getSelectedIds() {
            const isMobile = window.innerWidth <= 768;
            const selector = isMobile ? '.mobile-row-checkbox:checked' : '.desktop-row-checkbox:checked';
            const checkboxes = document.querySelectorAll(selector);
            return Array.from(new Set(Array.from(checkboxes).map(cb => cb.value)));
        }

        function bulkValidate() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (confirm('Validasi ' + ids.length + ' absensi yang dipilih?')) {
                const form = document.getElementById('bulk-validate-form');
                form.innerHTML = '@csrf';
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                form.submit();
            }
        }

        function bulkDelete() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (confirm('Hapus ' + ids.length + ' absensi yang dipilih? Tindakan ini tidak dapat dibatalkan!')) {
                const form = document.getElementById('bulk-delete-form');
                form.innerHTML = '@csrf';
                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                form.submit();
            }
        }

        function confirmDelete(formId) {
            if (confirm('Hapus data absensi ini?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
@endpush
