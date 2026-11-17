@extends('layouts.app')

@section('title', 'Edit Skema Gaji')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.salary-schemes.index') }}" class="btn btn-secondary btn-sm" style="height: 32px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 620px;">
        <div class="card-header" style="padding: 0.65rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Skema Gaji: {{ $user->name }}</h3>
        </div>
        <div class="card-body" style="padding: 1rem;">
            <div class="flex items-center gap-3 mb-3"
                style="padding: 0.65rem; background: var(--bg-secondary); border-radius: 8px;">
                <div class="avatar avatar-md">{{ substr($user->name, 0, 1) }}</div>
                <div>
                    <h4 class="mb-0" style="font-size: 0.95rem; font-weight: 600;">{{ $user->name }}</h4>
                    <p class="text-muted mb-0 text-xs">{{ $user->task ?? 'User' }} &bull; {{ $user->email }}</p>
                </div>
            </div>

            <form action="{{ route('admin.salary-schemes.update', $user) }}" method="POST" id="salaryForm">
                @csrf
                @method('PUT')

                <div class="form-group mb-2">
                    <label for="hourly_rate" class="form-label" style="font-size: 0.8rem;">Gaji Per Jam (Rp)</label>
                    <input type="text" id="hourly_rate_display" class="form-control currency-input"
                        value="{{ number_format($scheme->hourly_rate ?? 20000, 0, ',', '.') }}" placeholder="Rp 20.000">
                    <input type="hidden" name="hourly_rate" id="hourly_rate" value="{{ $scheme->hourly_rate ?? 20000 }}">
                    @error('hourly_rate')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row mb-2">
                    <div class="form-group mb-0">
                        <label for="content_edit_rate" class="form-label" style="font-size: 0.8rem;">Gaji Per Konten Edit (Rp)</label>
                        <input type="text" id="content_edit_rate_display" class="form-control currency-input"
                            value="{{ number_format($scheme->content_edit_rate ?? 10000, 0, ',', '.') }}"
                            placeholder="Rp 10.000">
                        <input type="hidden" name="content_edit_rate" id="content_edit_rate"
                            value="{{ $scheme->content_edit_rate ?? 10000 }}">
                    </div>
                    <div class="form-group mb-0">
                        <label for="content_live_rate" class="form-label" style="font-size: 0.8rem;">Gaji Per Konten Live (Rp)</label>
                        <input type="text" id="content_live_rate_display" class="form-control currency-input"
                            value="{{ number_format($scheme->content_live_rate ?? 5000, 0, ',', '.') }}"
                            placeholder="Rp 5.000">
                        <input type="hidden" name="content_live_rate" id="content_live_rate"
                            value="{{ $scheme->content_live_rate ?? 5000 }}">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="monthly_target_hours" class="form-label" style="font-size: 0.8rem;">Target Jam Bulanan</label>
                    <input type="number" name="monthly_target_hours" id="monthly_target_hours" class="form-control"
                        value="{{ old('monthly_target_hours', $scheme->monthly_target_hours ?? 80) }}" min="0" max="744"
                        step="0.5" required>
                    <div class="form-hint" style="font-size: 0.72rem;">Target jam live per bulan untuk mendapatkan bonus</div>
                    @error('monthly_target_hours')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <hr style="border-color: var(--border); margin: 1rem 0;">

                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">📊 Skema Bonus Khusus (Opsional)</h4>
                <div class="alert alert-info mb-3" style="padding: 0.5rem 0.75rem; font-size: 0.78rem; border-radius: 6px;">
                    Kosongkan field jika ingin mengikuti aturan global. Nilai yang diisi hanya berlaku untuk pengguna ini.
                </div>

                <div class="form-row mb-2">
                    <div class="form-group mb-0">
                        <label for="daily_live_hours" class="form-label" style="font-size: 0.8rem;">Jatah Live / Hari (Jam)</label>
                        <input type="number" name="daily_live_hours" id="daily_live_hours" class="form-control"
                            value="{{ old('daily_live_hours', $scheme->daily_live_hours) }}" min="0.5" max="24" step="0.5"
                            placeholder="Global: {{ $globalDefaults['daily_live_hours'] }}">
                        <div class="form-hint" style="font-size: 0.72rem;">Default: {{ $globalDefaults['daily_live_hours'] }} jam</div>
                        @error('daily_live_hours')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-0">
                        <label for="monthly_leave_days" class="form-label" style="font-size: 0.8rem;">Jatah Libur / Bulan</label>
                        <input type="number" name="monthly_leave_days" id="monthly_leave_days" class="form-control"
                            value="{{ old('monthly_leave_days', $scheme->monthly_leave_days) }}" min="0" max="15"
                            placeholder="Global: {{ $globalDefaults['monthly_leave_days'] }}">
                        <div class="form-hint" style="font-size: 0.72rem;">Default: {{ $globalDefaults['monthly_leave_days'] }} hari</div>
                        @error('monthly_leave_days')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row mb-3">
                    <div class="form-group mb-0">
                        <label for="bonus_pcs_threshold" class="form-label" style="font-size: 0.8rem;">Target Penjualan (pcs)</label>
                        <input type="number" name="bonus_pcs_threshold" id="bonus_pcs_threshold" class="form-control"
                            value="{{ old('bonus_pcs_threshold', $scheme->bonus_pcs_threshold) }}" min="1"
                            placeholder="Global: {{ $globalDefaults['bonus_pcs_threshold'] }}">
                        <div class="form-hint" style="font-size: 0.72rem;">Default: {{ $globalDefaults['bonus_pcs_threshold'] }} pcs</div>
                        @error('bonus_pcs_threshold')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-0">
                        <label for="bonus_amount" class="form-label" style="font-size: 0.8rem;">Bonus Per Kelipatan (Rp)</label>
                        <input type="number" name="bonus_amount" id="bonus_amount" class="form-control"
                            value="{{ old('bonus_amount', $scheme->bonus_amount) }}" min="0"
                            placeholder="Global: {{ number_format($globalDefaults['bonus_amount'], 0, ',', '.') }}">
                        <div class="form-hint" style="font-size: 0.72rem;">Default: Rp {{ number_format($globalDefaults['bonus_amount'], 0, ',', '.') }}</div>
                        @error('bonus_amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Skema
                    </button>
                    <a href="{{ route('admin.salary-schemes.index') }}" class="btn btn-secondary" style="height: 36px; padding: 0 1rem; font-size: 0.85rem;">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Format currency for display
            function formatCurrency(value) {
                const num = parseInt(value) || 0;
                return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Parse currency to number
            function parseCurrency(value) {
                return parseInt(value.replace(/[^0-9]/g, '')) || 0;
            }

            // Setup currency inputs
            const currencyInputs = document.querySelectorAll('.currency-input');
            currencyInputs.forEach(function (input) {
                const hiddenId = input.id.replace('_display', '');
                const hiddenInput = document.getElementById(hiddenId);

                // Format on load
                input.value = formatCurrency(hiddenInput.value);

                // Format on input
                input.addEventListener('input', function (e) {
                    const numValue = parseCurrency(e.target.value);
                    hiddenInput.value = numValue;

                    // Update display with cursor position preserved
                    const cursorPos = e.target.selectionStart;
                    e.target.value = formatCurrency(numValue);
                });

                // Select all on focus
                input.addEventListener('focus', function (e) {
                    setTimeout(() => e.target.select(), 0);
                });
            });
        });
    </script>
@endpush
