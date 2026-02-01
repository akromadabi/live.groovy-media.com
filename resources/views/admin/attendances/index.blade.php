@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-6">
        <div class="card-body">
            <form action="{{ route('admin.attendances.index') }}" method="GET" class="filter-bar">
                <div class="form-group">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-control form-select">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-control form-select">
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-control form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Periode</label>
                    <select name="term" class="form-control form-select">
                        <option value="" {{ request('term') == '' ? 'selected' : '' }}>Satu Bulan</option>
                        <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>Termin 1 (1-15)</option>
                        <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>Termin 2 (16-Akhir)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control form-select">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Tervalidasi
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
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

    <!-- Stats Grid -->
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
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <div class="stat-label">Total Sales</div>
                <div class="stat-number">{{ $totals['total_sales'] }}</div>
            </div>
        </div>
    </div>

    <!-- Bonus Card (only shown when specific user is selected) -->
    @if($bonusInfo)
        <div class="bonus-card mb-4 {{ $bonusInfo['target_met'] ? 'bonus-met' : 'bonus-pending' }}">
            <div class="bonus-header">
                <span class="bonus-status">
                    {{ $bonusInfo['user_name'] }}
                    @if($bonusInfo['target_met'])
                        • ✓ Target Tercapai
                    @else
                        • ⏳ Belum Tercapai
                    @endif
                </span>
                <span class="bonus-progress">{{ $bonusInfo['monthly_hours'] }}/{{ $bonusInfo['target_hours'] }} jam</span>
            </div>
            <div class="bonus-body">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span class="bonus-label">Gaji (tanpa bonus)</span>
                    <span style="font-weight: 600;">Rp {{ number_format($bonusInfo['total_salary'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span class="bonus-label">Bonus Penjualan ({{ $bonusInfo['monthly_sales'] }} pcs)</span>
                    <span class="bonus-amount">Rp {{ number_format($bonusInfo['sales_bonus'] ?? 0, 0, ',', '.') }}</span>
                </div>
                @if(!$bonusInfo['target_met'] && $bonusInfo['sales_bonus'] > 0)
                    <div class="bonus-note">*bonus berlaku jika target tercapai</div>
                @endif
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
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Konten Edit</th>
                        <th>Konten Live</th>
                        <th>Penjualan</th>
                        <th>Gaji</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        @php
                            $scheme = $attendance->user->salaryScheme;
                            // Gaji harian = durasi + konten (tanpa penjualan, penjualan masuk bonus)
                            $salary = 0;
                            if ($scheme) {
                                $hours = $attendance->live_duration_minutes / 60;
                                $salary = ($hours * $scheme->hourly_rate)
                                    + ($attendance->content_edit_count * $scheme->content_edit_rate)
                                    + ($attendance->content_live_count * $scheme->content_live_rate);
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
                            <td>{{ $attendance->formatted_duration }}</td>
                            <td>{{ $attendance->content_edit_count }}</td>
                            <td>{{ $attendance->content_live_count }}</td>
                            <td>{{ $attendance->sales_count }}</td>
                            <td class="font-medium" style="color: var(--success);">
                                Rp {{ number_format($salary, 0, ',', '.') }}
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
                            <td colspan="10" class="text-center text-muted" style="padding: 3rem;">
                                Tidak ada data absensi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
    </script>
@endpush