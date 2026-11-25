@extends('layouts.app')

@section('title', 'Skema Bonus')

@section('content')
    <div class="card" style="max-width: 650px;">
        <div class="card-header" style="padding: 0.65rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Aturan Bonus Global</h3>
            <p class="text-xs text-muted mb-0">Pengaturan ini berlaku sebagai patokan default untuk seluruh host live</p>
        </div>
        <div class="card-body" style="padding: 1rem;">
            <form action="{{ route('admin.bonus-scheme.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.65rem;" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" style="color: var(--primary);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Target Durasi Kerja</span>
                    </h4>
                    <div class="form-row">
                        <div class="form-group mb-2">
                            <label for="daily_live_hours" class="form-label" style="font-size: 0.8rem;">Jam Live / Hari</label>
                            <input type="number" name="daily_live_hours" id="daily_live_hours" class="form-control"
                                value="{{ $settings['daily_live_hours'] ?? 3 }}" min="1" max="24" step="0.5" placeholder="3">
                            <div class="form-hint" style="font-size: 0.72rem;">Target jam live per hari</div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="monthly_leave_days" class="form-label" style="font-size: 0.8rem;">Jatah Libur / Bulan</label>
                            <input type="number" name="monthly_leave_days" id="monthly_leave_days" class="form-control"
                                value="{{ $settings['monthly_leave_days'] ?? 4 }}" min="0" max="15" placeholder="4">
                            <div class="form-hint" style="font-size: 0.72rem;">Hari libur dalam sebulan</div>
                        </div>
                    </div>

                    @php
                        $dailyHours = $settings['daily_live_hours'] ?? 3;
                        $leaveDays = $settings['monthly_leave_days'] ?? 4;
                        $daysInMonth = now()->daysInMonth;
                        $workDays = $daysInMonth - $leaveDays;
                        $targetHours = $workDays * $dailyHours;
                    @endphp
                    <div class="alert alert-info mt-2" style="padding: 0.5rem 0.75rem; font-size: 0.78rem; border-radius: 6px;">
                        <strong>Simulasi Bulan Ini ({{ now()->translatedFormat('F Y') }}):</strong><br>
                        {{ $daysInMonth }} hari - {{ $leaveDays }} libur = {{ $workDays }} hari kerja &bull; 
                        <strong>Target: {{ $workDays }} &times; {{ $dailyHours }} = {{ $targetHours }} jam</strong>
                    </div>
                </div>

                <hr style="border-color: var(--border); margin: 1rem 0;">

                <div class="mb-3">
                    <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.65rem;" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" style="color: #059669;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Bonus Penjualan</span>
                    </h4>
                    <div class="form-row">
                        <div class="form-group mb-2">
                            <label for="bonus_pcs_threshold" class="form-label" style="font-size: 0.8rem;">Threshold Penjualan (pcs)</label>
                            <input type="number" name="bonus_pcs_threshold" id="bonus_pcs_threshold" class="form-control"
                                value="{{ $settings['bonus_pcs_threshold'] ?? 20 }}" min="1" placeholder="20">
                            <div class="form-hint" style="font-size: 0.72rem;">Kelipatan pcs untuk bonus</div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="bonus_amount" class="form-label" style="font-size: 0.8rem;">Bonus Per Kelipatan (Rp)</label>
                            <input type="number" name="bonus_amount" id="bonus_amount" class="form-control"
                                value="{{ $settings['bonus_amount'] ?? 10000 }}" min="0" placeholder="10000">
                            <div class="form-hint" style="font-size: 0.72rem;">Nominal bonus per kelipatan</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
