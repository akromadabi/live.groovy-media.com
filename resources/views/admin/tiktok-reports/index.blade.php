@extends('layouts.app')

@section('title', 'Report TikTok')

@section('content')
    <!-- Filter Bar -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 1rem;">
            <form action="{{ route('admin.tiktok-reports.index') }}" method="GET" class="flex items-center gap-3 flex-wrap">
                <select name="year" class="form-control form-select" style="width: auto; min-width: 100px;">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <select name="month" class="form-control form-select" style="width: auto; min-width: 130px;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button type="button" data-modal="upload-modal" class="btn btn-success" style="margin-left: auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Report
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid-mobile mb-4">
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $dailyData->count() }}</div>
                <div class="stat-label">Hari Tercatat</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalAbsenHours, 1) }} jam</div>
                <div class="stat-label">Total Absen</div>
            </div>
        </div>
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($totalTiktokHours, 1) }} jam</div>
                <div class="stat-label">Total TikTok</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Perbandingan Harian</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Absen</th>
                        <th>Report TikTok</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyData as $data)
                        <tr>
                            <td>
                                <div class="font-medium">{{ \Carbon\Carbon::parse($data->date)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($data->date)->translatedFormat('l') }}
                                </div>
                            </td>
                            <td>
                                @if($data->absen_minutes > 0)
                                    <span class="font-medium">{{ number_format($data->absen_minutes / 60, 1) }} jam</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($data->tiktok_minutes > 0)
                                    <span class="font-medium">{{ number_format($data->tiktok_minutes / 60, 1) }} jam</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $diff = $data->absen_minutes - $data->tiktok_minutes;
                                    $diffHours = abs($diff) / 60;
                                @endphp
                                @if($data->tiktok_minutes == 0 && $data->absen_minutes > 0)
                                    <span class="badge badge-warning">Belum ada data TikTok</span>
                                @elseif($data->absen_minutes == 0 && $data->tiktok_minutes > 0)
                                    <span class="badge badge-danger">Tidak ada absen</span>
                                @elseif($diff == 0)
                                    <span class="badge badge-success">Sesuai</span>
                                @elseif($diff > 0)
                                    <span class="badge badge-info">Absen +{{ number_format($diffHours, 1) }} jam</span>
                                @else
                                    <span class="badge badge-danger">TikTok +{{ number_format($diffHours, 1) }} jam</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding: 3rem;">
                                Tidak ada data untuk periode ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Uploaded Files List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">File Report yang Diupload</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Tanggal Upload</th>
                        <th>Total Data</th>
                        <th>Total Durasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uploadedFiles as $file)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $file->original_filename ?? $file->filename }}</div>
                                <div class="text-xs text-muted">oleh {{ $file->uploader->name ?? 'Unknown' }}</div>
                            </td>
                            <td>{{ $file->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $file->total_records ?? $file->details()->count() }} data</td>
                            <td>{{ number_format(($file->total_duration_minutes ?? $file->details()->sum('duration_minutes')) / 60, 1) }}
                                jam</td>
                            <td>
                                <form action="{{ route('admin.tiktok-reports.destroy', $file) }}" method="POST"
                                    style="display:inline;" id="delete-file-{{ $file->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-file-{{ $file->id }}')"
                                        class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted" style="padding: 2rem;">
                                Belum ada file diupload
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal-overlay" id="upload-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Upload Report TikTok</h3>
                <button type="button" class="modal-close" data-modal-close>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20"
                        height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.tiktok-reports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">File Excel (.xlsx)</label>
                        <input type="file" name="report_file" class="form-control" accept=".xlsx,.xls" required>
                        <div class="form-hint">Upload file report dari TikTok dalam format Excel</div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <strong>📌 Format File:</strong><br>
                        File harus memiliki kolom:<br>
                        • <strong>Start time</strong> - Format: 2026-01-29 19:16<br>
                        • <strong>Duration</strong> - Nilai dalam detik
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload & Proses
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection