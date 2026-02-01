@extends('layouts.app')

@section('title', 'Skema Bonus')

@section('content')
    <div class="card" style="max-width: 500px;">
        <div class="card-body">
            <form action="{{ route('admin.bonus-scheme.update') }}" method="POST">
                @csrf
                @method('PUT')

                <h4 class="mb-4">📊 Target Durasi</h4>

                <div class="form-group">
                    <label for="daily_live_hours" class="form-label">Jam Live Per Hari</label>
                    <input type="number" name="daily_live_hours" id="daily_live_hours" class="form-control"
                        value="{{ $settings['daily_live_hours'] ?? 3 }}" min="1" max="24" step="0.5" placeholder="3">
                    <div class="form-hint">Target durasi live per hari (jam)</div>
                </div>

                <div class="form-group">
                    <label for="monthly_leave_days" class="form-label">Jatah Libur Per Bulan</label>
                    <input type="number" name="monthly_leave_days" id="monthly_leave_days" class="form-control"
                        value="{{ $settings['monthly_leave_days'] ?? 4 }}" min="0" max="15" placeholder="4">
                    <div class="form-hint">Jumlah hari libur dalam sebulan</div>
                </div>

                @php
                    $dailyHours = $settings['daily_live_hours'] ?? 3;
                    $leaveDays = $settings['monthly_leave_days'] ?? 4;
                    $daysInMonth = now()->daysInMonth;
                    $workDays = $daysInMonth - $leaveDays;
                    $targetHours = $workDays * $dailyHours;
                @endphp
                <div class="alert alert-info">
                    <strong>📅 Contoh Bulan Ini:</strong><br>
                    {{ $daysInMonth }} hari - {{ $leaveDays }} libur = {{ $workDays }} hari kerja<br>
                    <strong>Target: {{ $workDays }} × {{ $dailyHours }} = {{ $targetHours }} jam</strong>
                </div>

                <hr style="border-color: var(--border); margin: 1.5rem 0;">

                <h4 class="mb-4">💰 Bonus Penjualan</h4>

                <div class="form-group">
                    <label for="bonus_pcs_threshold" class="form-label">Threshold Penjualan (pcs)</label>
                    <input type="number" name="bonus_pcs_threshold" id="bonus_pcs_threshold" class="form-control"
                        value="{{ $settings['bonus_pcs_threshold'] ?? 20 }}" min="1" placeholder="20">
                    <div class="form-hint">Kelipatan pcs untuk mendapatkan bonus</div>
                </div>

                <div class="form-group">
                    <label for="bonus_amount" class="form-label">Bonus Per Kelipatan (Rp)</label>
                    <input type="number" name="bonus_amount" id="bonus_amount" class="form-control"
                        value="{{ $settings['bonus_amount'] ?? 10000 }}" min="0" placeholder="10000">
                    <div class="form-hint">Jumlah bonus per kelipatan threshold</div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
                </button>
            </form>
        </div>
    </div>
@endsection