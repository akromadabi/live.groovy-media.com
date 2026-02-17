@extends('layouts.app')

@section('title', 'Gaji')

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 0.75rem 1rem;">
            <form action="{{ route('admin.salary-records.index') }}" method="GET" class="flex items-center gap-3"
                style="flex-wrap: wrap;">
                <span class="text-muted" style="font-size: 0.85rem;">Filter:</span>
                <select name="year" class="form-control form-select" style="width: auto; min-width: 90px;">
                    <option value="">Tahun</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <select name="month" class="form-control form-select" style="width: auto; min-width: 120px;">
                    <option value="">Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="term" class="form-control form-select" style="width: auto; min-width: 100px;">
                    <option value="">Termin</option>
                    <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>T1</option>
                    <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>T2</option>
                </select>
                <select name="status" class="form-control form-select" style="width: auto; min-width: 100px;">
                    <option value="">Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if(request()->hasAny(['year', 'month', 'term', 'status']))
                    <a href="{{ route('admin.salary-records.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Total Stats Box -->
    <div class="stats-grid mb-4"
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div class="stat-card"
            style="background: var(--card-bg); border-radius: 12px; padding: 1rem 1.25rem; border: 1px solid var(--border);">
            <div class="flex items-center gap-3">
                <div class="stat-icon-sm primary"
                    style="background: rgba(var(--primary-rgb), 0.15); padding: 0.75rem; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="width: 24px; height: 24px; color: var(--primary);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 0.8rem;">Total Gaji</div>
                    <div class="font-bold" style="font-size: 1.2rem; color: var(--primary);">Rp
                        {{ number_format($totals['total_amount'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card"
            style="background: var(--card-bg); border-radius: 12px; padding: 1rem 1.25rem; border: 1px solid var(--border);">
            <div class="flex items-center gap-3">
                <div class="stat-icon-sm warning"
                    style="background: rgba(245, 158, 11, 0.15); padding: 0.75rem; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="width: 24px; height: 24px; color: var(--warning);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 0.8rem;">Pending ({{ $totals['count_pending'] }})</div>
                    <div class="font-bold" style="font-size: 1.2rem; color: var(--warning);">Rp
                        {{ number_format($totals['total_pending'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card"
            style="background: var(--card-bg); border-radius: 12px; padding: 1rem 1.25rem; border: 1px solid var(--border);">
            <div class="flex items-center gap-3">
                <div class="stat-icon-sm success"
                    style="background: rgba(16, 185, 129, 0.15); padding: 0.75rem; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="width: 24px; height: 24px; color: var(--success);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 0.8rem;">Dibayar ({{ $totals['count_paid'] }})</div>
                    <div class="font-bold" style="font-size: 1.2rem; color: var(--success);">Rp
                        {{ number_format($totals['total_paid'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3 justify-end mb-4" style="flex-wrap: wrap;">
        <button type="button" class="btn btn-success" id="downloadSelectedBtn" style="display: none;"
            onclick="downloadSelected()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download Terpilih (<span id="selectedCount">0</span>)
        </button>
        <button type="button" class="btn btn-secondary"
            onclick="document.getElementById('generateModal').classList.add('active')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Generate Gaji
        </button>
        <a href="{{ route('admin.salary-records.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Data Gaji
        </a>
    </div>

    <!-- Generate Modal -->
    <div class="modal-overlay" id="generateModal">
        <div class="modal" style="max-width: 500px;">
            <div class="modal-header">
                <h3 class="modal-title">Generate Gaji</h3>
                <button type="button" class="modal-close"
                    onclick="document.getElementById('generateModal').classList.remove('active')">&times;</button>
            </div>
            <form action="{{ route('admin.salary-records.generate-all') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-4">Generate gaji otomatis berdasarkan data absensi tervalidasi.</p>

                    <div class="form-group">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-control form-select">
                            <option value="">Semua Tahun</option>
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bulan</label>
                        <select name="month" class="form-control form-select">
                            <option value="">Semua Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Termin</label>
                        <select name="term" class="form-control form-select">
                            <option value="">Semua Termin</option>
                            <option value="1">T1 (1-15)</option>
                            <option value="2">T2 (16-Akhir)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-control form-select">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('generateModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>User</th>
                        <th>Periode</th>
                        <th class="hide-on-mobile">Detail</th>
                        <th>Gaji</th>
                        <th class="hide-on-mobile">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>
                                <input type="checkbox" class="record-checkbox" value="{{ $record->id }}"
                                    onchange="updateSelectedCount()">
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar avatar-sm">{{ substr($record->user->name, 0, 1) }}</div>
                                    <div>
                                        <div class="font-medium">{{ $record->user->name }}</div>
                                        <div class="text-xs text-muted">{{ $record->user->task }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $record->period_label }}</div>
                                <span class="term-badge {{ $record->term == '1' ? 't1' : 't2' }}"
                                    style="font-size: 0.65rem;">T{{ $record->term }}</span>
                            </td>
                            <td class="hide-on-mobile">
                                <div class="text-xs" style="line-height: 1.6;">
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" style="width: 12px; height: 12px; color: var(--primary);">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <strong>{{ number_format($record->total_hours, 1) }}</strong> jam
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" style="width: 12px; height: 12px; color: var(--warning);">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        {{ $record->total_content_live }} live
                                        <span style="margin: 0 0.25rem;">•</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" style="width: 12px; height: 12px; color: var(--success);">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        {{ $record->total_content_edit }} edit
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" style="width: 12px; height: 12px; color: var(--danger);">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        {{ $record->total_sales }} penjualan
                                    </div>
                                    @php
                                        // Check if bonus was achieved (need to check monthly target)
                                        $user = $record->user;
                                        $scheme = $user->salaryScheme;
                                        $bonusAchieved = false;

                                        if ($scheme) {
                                            $monthStart = \Carbon\Carbon::createFromDate($record->year, $record->month, 1)->startOfDay();
                                            $monthEnd = $monthStart->copy()->endOfMonth();

                                            $monthlyMinutes = \App\Models\Attendance::where('user_id', $user->id)
                                                ->whereIn('status', ['pending', 'validated'])
                                                ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                                                ->sum('live_duration_minutes');

                                            $monthlyHours = $monthlyMinutes / 60;
                                            $bonusAchieved = $monthlyHours >= $scheme->monthly_target_hours;
                                        }
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;">
                                        @if($bonusAchieved)
                                            <span style="color: var(--success); font-weight: 600;">✓ Bonus tercapai</span>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.75rem;">Target belum tercapai</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="font-medium" style="color: var(--success);">
                                {{ $record->formatted_amount }}
                            </td>
                            <td class="hide-on-mobile">
                                @if($record->status === 'paid')
                                    <span class="badge badge-success">Dibayar</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.salary-records.show', $record) }}"
                                        class="btn btn-secondary btn-sm btn-icon" title="Detail & Download">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.salary-records.edit', $record) }}"
                                        class="btn btn-secondary btn-sm btn-icon" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if($record->status !== 'paid')
                                        <form action="{{ route('admin.salary-records.mark-paid', $record) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm btn-icon" title="Tandai Dibayar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" width="16" height="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.salary-records.destroy', $record) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">
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
                            <td colspan="7" class="text-center text-muted" style="padding: 3rem;">
                                Tidak ada data rekap gaji. <a href="{{ route('admin.salary-records.create') }}">Tambah
                                    sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="card-footer">
                {{ $records->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.record-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.record-checkbox:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('downloadSelectedBtn').style.display = count > 0 ? 'inline-flex' : 'none';
        }

        function downloadSelected() {
            const checkboxes = document.querySelectorAll('.record-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih minimal satu rekap gaji');
                return;
            }

            // Open each slip in new tab with auto-download enabled
            ids.forEach((id, index) => {
                setTimeout(() => {
                    const url = "{{ route('admin.salary-records.slip', ['id' => '__ID__']) }}".replace('__ID__', id);
                    window.open(url + '?auto=1', '_blank');
                }, index * 1500); // Increased delay for stable downloads
            });
        }
    </script>
@endpush