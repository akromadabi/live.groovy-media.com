@extends('layouts.app')

@section('title', 'Detail Report TikTok')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.tiktok-reports.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Summary -->
    <div class="card mb-6">
        <div class="card-body">
            <div class="flex flex-col gap-4"
                style="flex-direction: row; align-items: center; justify-content: space-between;">
                <div>
                    <h3 class="mb-1">{{ $report->original_filename }}</h3>
                    <p class="text-muted mb-0">Diupload: {{ $report->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="flex gap-4">
                    <div class="text-center">
                        <div class="font-bold" style="font-size: 1.5rem; color: var(--success);">
                            {{ $report->details->where('match_status', 'matched')->count() }}
                        </div>
                        <div class="text-xs text-muted">Cocok</div>
                    </div>
                    <div class="text-center">
                        <div class="font-bold" style="font-size: 1.5rem; color: var(--danger);">
                            {{ $report->details->where('match_status', 'not_matched')->count() }}
                        </div>
                        <div class="text-xs text-muted">Tidak Cocok</div>
                    </div>
                    <div class="text-center">
                        <div class="font-bold" style="font-size: 1.5rem; color: var(--warning);">
                            {{ $report->details->where('match_status', 'needs_verification')->count() }}
                        </div>
                        <div class="text-xs text-muted">Verifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Perbandingan</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Tanggal</th>
                        <th>Durasi Report</th>
                        <th>Durasi Absensi</th>
                        <th>Selisih</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report->details as $detail)
                        <tr>
                            <td>
                                @if($detail->user)
                                    <div class="flex items-center gap-2">
                                        <div class="avatar avatar-sm">{{ substr($detail->user->name, 0, 1) }}</div>
                                        <span>{{ $detail->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">{{ $detail->tiktok_username ?? '-' }}</span>
                                @endif
                            </td>
                            <td>{{ $detail->live_date ? $detail->live_date->format('d M Y') : '-' }}</td>
                            <td>{{ floor($detail->report_duration_minutes / 60) }}j {{ $detail->report_duration_minutes % 60 }}m
                            </td>
                            <td>
                                @if($detail->attendance_duration_minutes !== null)
                                    {{ floor($detail->attendance_duration_minutes / 60) }}j
                                    {{ $detail->attendance_duration_minutes % 60 }}m
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tiktokMinutes = $detail->duration_minutes ?? 0;
                                    $absenMinutes = $detail->attendance_duration_minutes ?? 0;
                                    $diff = $tiktokMinutes - $absenMinutes;
                                    $absDiffHours = round(abs($diff) / 60, 1);
                                @endphp
                                @if($absenMinutes > 0)
                                    @if($diff > 0)
                                        {{-- TikTok lebih banyak = biru --}}
                                        <span style="font-size: 0.85rem; font-weight: 600; color: #3B82F6;">
                                            TikTok +{{ $absDiffHours }} jam
                                        </span>
                                    @elseif($diff < 0)
                                        {{-- Absen lebih banyak = merah --}}
                                        <span style="font-size: 0.85rem; font-weight: 600; color: #EF4444;">
                                            Absen +{{ $absDiffHours }} jam
                                        </span>
                                    @else
                                        <span style="font-size: 0.85rem; color: #10B981;">Sama</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($detail->match_status === 'matched')
                                    <span class="badge badge-success">Cocok</span>
                                @elseif($detail->match_status === 'not_matched')
                                    <span class="badge badge-danger">Tidak Cocok</span>
                                @else
                                    <span class="badge badge-warning">Verifikasi</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-secondary btn-sm dropdown-trigger">
                                        Ubah Status
                                    </button>
                                    <div class="dropdown-menu">
                                        <form action="{{ route('admin.tiktok-reports.update-match-status', $detail) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="match_status" value="matched">
                                            <button type="submit" class="dropdown-item">✓ Cocok</button>
                                        </form>
                                        <form action="{{ route('admin.tiktok-reports.update-match-status', $detail) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="match_status" value="not_matched">
                                            <button type="submit" class="dropdown-item">✗ Tidak Cocok</button>
                                        </form>
                                        <form action="{{ route('admin.tiktok-reports.update-match-status', $detail) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="match_status" value="needs_verification">
                                            <button type="submit" class="dropdown-item">? Perlu Verifikasi</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 3rem;">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection