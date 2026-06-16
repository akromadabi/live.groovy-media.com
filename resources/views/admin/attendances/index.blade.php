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
    </style>
@endpush

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form action="{{ route('admin.attendances.index') }}" method="GET" class="filter-bar-grid"
                style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <select name="user_id" class="form-control form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <select name="year" class="form-control form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Tahun --</option>
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <select name="month" class="form-control form-select" onchange="this.form.submit()">
                        <option value="">-- Pilih Bulan --</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <select name="term" class="form-control form-select" onchange="this.form.submit()">
                        <option value="" {{ request('term') == '' ? 'selected' : '' }}>-- Satu Bulan --</option>
                        <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>Termin 1 (1-15)</option>
                        <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>Termin 2 (16-Akhir)</option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="status" class="form-control form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Tervalidasi
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Grid - Mobile Optimized (6 boxes matches user dashboard layout) -->
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
            <div>
                <div class="stat-label">Total Jam</div>
                <div class="stat-number">{{ number_format($totals['total_hours'], 1) }} jam</div>
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
            <div>
                <div class="stat-label">Gaji</div>
                <div class="stat-number">
                    @if($bonusInfo)
                        Rp {{ number_format(($bonusInfo['total_salary'] ?? 0) + ($bonusInfo['target_met'] ? $bonusInfo['sales_bonus'] : 0), 0, ',', '.') }}
                    @else
                        Rp -
                    @endif
                </div>
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
            <div>
                <div class="stat-label">Konten Live</div>
                <div class="stat-number">{{ number_format($totals['total_content_live']) }}</div>
            </div>
        </div>
    </div>

    <!-- Salary Summary (only shown when specific user is selected) -->
    @if($bonusInfo)
        <div class="salary-summary-container mb-6">
            <!-- T1/T2 Salary Grid -->
            <div class="term-salary-grid mb-4">
                <div class="term-card">
                    <div class="term-header">
                        <span class="term-badge t1">T1</span>
                        <span class="term-period">1 - 15</span>
                    </div>
                    <div class="term-body">
                        <div class="term-hours">{{ $bonusInfo['termData']['t1']['hours'] }} <small>jam</small></div>
                        <div class="term-salary">Rp {{ number_format($bonusInfo['termData']['t1']['salary'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="term-card">
                    <div class="term-header">
                        <span class="term-badge t2">T2</span>
                        <span class="term-period">16 - Akhir</span>
                    </div>
                    <div class="term-body">
                        <div class="term-hours">{{ $bonusInfo['termData']['t2']['hours'] }} <small>jam</small></div>
                        <div class="term-salary">Rp {{ number_format($bonusInfo['termData']['t2']['salary'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Achievement Card -->
            <div class="card mb-4">
                <div class="card-body">
                    @php
                        $targetPercent = $bonusInfo['target_hours'] > 0 ? min(100, round(($bonusInfo['monthly_hours'] / $bonusInfo['target_hours']) * 100)) : 0;
                        $selectedYear = request('year', now()->year);
                        $selectedMonth = request('month', now()->month);
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <h3 style="font-size: 0.95rem; font-weight: 600; margin: 0;">🎯 Target {{ $bonusInfo['user_name'] }} - Bulan {{ \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->translatedFormat('F') }}</h3>
                        <span
                            style="font-size: 0.8rem; font-weight: 600; color: {{ $bonusInfo['target_met'] ? 'var(--success)' : 'var(--danger, #ef4444)' }};">
                            {{ $bonusInfo['monthly_hours'] }}/{{ $bonusInfo['target_hours'] }} jam
                        </span>
                    </div>
                    <!-- Progress Bar -->
                    <div
                        style="background: var(--border, #e5e7eb); border-radius: 10px; height: 12px; overflow: hidden; margin-bottom: 8px;">
                        <div
                            style="background: {{ $bonusInfo['target_met'] ? 'linear-gradient(90deg, #10b981, #34d399)' : 'linear-gradient(90deg, #6366f1, #818cf8)' }}; height: 100%; border-radius: 10px; width: {{ $targetPercent }}%; transition: width 0.5s ease;">
                        </div>
                    </div>
                    <div class="flex items-center justify-between" style="font-size: 0.8rem;">
                        <span style="color: var(--text-muted, #6b7280);">{{ $targetPercent }}%</span>
                        @if($bonusInfo['target_met'])
                            <span style="color: var(--success, #10b981); font-weight: 600;">✅ Target Tercapai!</span>
                        @else
                            <span style="color: var(--text-muted, #6b7280);">Kurang {{ max(0, $bonusInfo['target_hours'] - $bonusInfo['monthly_hours']) }} jam lagi</span>
                        @endif
                    </div>

                    @if($bonusInfo['target_hours'] > 0)
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border, #e5e7eb); display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 0.85rem; color: var(--text-muted, #6b7280);">💰 Bonus Penjualan:</span>
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

    <!-- Actions -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Absensi
        </a>
    </div>

    <!-- Bulk Action Bar (hidden by default) -->
    <div id="bulk-action-bar" class="card mb-4" style="display: none;">
        <div class="card-body" style="padding: 0.75rem 1rem;">
            <div class="flex items-center justify-between">
                <span id="selected-count">0 item dipilih</span>
                <div class="flex gap-2">
                    <button type="button" onclick="bulkValidate()" class="btn btn-success btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Validasi Masal
                    </button>
                    <button type="button" onclick="bulkDelete()" class="btn btn-danger btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Masal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms for Bulk Actions -->
    <form id="bulk-validate-form" action="{{ route('admin.attendances.bulk-validate') }}" method="POST"
        style="display:none;">
        @csrf
    </form>
    <form id="bulk-delete-form" action="{{ route('admin.attendances.bulk-delete') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                        </th>
                        <th>
                            <a href="{{ sortUrl('user') }}" class="sortable-link">
                                User {!! sortIcon('user') !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ sortUrl('attendance_date') }}" class="sortable-link">
                                Tanggal {!! sortIcon('attendance_date') !!}
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
                        <th class="hide-on-mobile">
                            <a href="{{ sortUrl('content_edit_count') }}" class="sortable-link">
                                Konten Edit {!! sortIcon('content_edit_count') !!}
                            </a>
                        </th>
                        <th class="hide-on-mobile">
                            <a href="{{ sortUrl('content_live_count') }}" class="sortable-link">
                                Konten Live {!! sortIcon('content_live_count') !!}
                            </a>
                        </th>
                        <th class="hide-on-mobile">
                            <a href="{{ sortUrl('sales_count') }}" class="sortable-link">
                                Penjualan {!! sortIcon('sales_count') !!}
                            </a>
                        </th>
                        <th class="hide-on-mobile">Gaji</th>
                        <th class="hide-on-mobile">
                            <a href="{{ sortUrl('status') }}" class="sortable-link">
                                Status {!! sortIcon('status') !!}
                            </a>
                        </th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            $scheme = $attendance->user->salaryScheme;
                            $salary = 0;
                            if ($scheme) {
                                $hours = $attendance->live_duration_minutes / 60;
                                $salary =
                                    $hours * $scheme->hourly_rate +
                                    $attendance->content_edit_count * $scheme->content_edit_rate +
                                    $attendance->content_live_count * $scheme->content_live_rate +
                                    \App\Models\BonusTier::getBonusForSales($attendance->sales_count);
                            }
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $attendance->id }}"
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
                            <td>{{ $attendance->attendance_date->format('d M Y') }}</td>
                            <td>
                                <div style="font-weight: 500;">
                                    {{ $attendance->created_at ? $attendance->created_at->format('d M Y') : '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px;">
                                    {{ $attendance->created_at ? $attendance->created_at->format('H:i') : '' }}
                                </div>
                            </td>
                            <td>{{ $attendance->formatted_duration }}</td>
                            <td class="hide-on-mobile">{{ $attendance->content_edit_count }}</td>
                            <td class="hide-on-mobile">{{ $attendance->content_live_count }}</td>
                            <td class="hide-on-mobile">{{ $attendance->sales_count }}</td>
                            <td class="hide-on-mobile font-medium" style="color: var(--success);">
                                Rp {{ number_format($salary, 0, ',', '.') }}
                            </td>
                            <td class="hide-on-mobile">
                                @if($attendance->status === 'validated')
                                    <span class="badge badge-success">Tervalidasi</span>
                                @elseif($attendance->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
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
                            <td colspan="11" class="text-center text-muted">Tidak ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
        <div class="card-footer">
            {{ $attendances->withQueryString()->links() }}
        </div>
    @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelection();
        }

        function updateSelection() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const count = checkboxes.length;
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
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function bulkValidate() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (confirm('Validasi ' + ids.length + ' absensi yang dipilih?')) {
                const form = document.getElementById('bulk-validate-form');
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

        function toggleRowDetails(id) {
            const detailRow = document.getElementById('detail-' + id);
            if (detailRow.style.display === 'none') {
                detailRow.style.display = 'table-row';
            } else {
                detailRow.style.display = 'none';
            }
        }

        function toggleActionMenu(id) {
            const menu = document.getElementById('action-menu-' + id);
            const allMenus = document.querySelectorAll('.action-menu');

            // Close all other menus
            allMenus.forEach(m => {
                if (m.id !== 'action-menu-' + id) {
                    m.style.display = 'none';
                }
            });

            // Toggle current menu
            if (menu.style.display === 'none') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Close menus when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.dropdown-action')) {
                document.querySelectorAll('.action-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });

        function confirmDelete(formId) {
            if (confirm('Hapus data absensi ini?')) {
                document.getElementById(formId).submit();
            }
        }
    </script>
@endpush