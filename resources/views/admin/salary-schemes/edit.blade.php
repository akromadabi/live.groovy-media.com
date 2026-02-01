@extends('layouts.app')

@section('title', 'Edit Skema Gaji')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.salary-schemes.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">Skema Gaji: {{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <div class="flex items-center gap-3 mb-6"
                style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius);">
                <div class="avatar avatar-lg">{{ substr($user->name, 0, 1) }}</div>
                <div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-0">{{ $user->task ?? 'User' }}</p>
                </div>
            </div>

            <form action="{{ route('admin.salary-schemes.update', $user) }}" method="POST" id="salaryForm">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="hourly_rate" class="form-label">Gaji Per Jam (Rp)</label>
                    <input type="text" id="hourly_rate_display" class="form-control currency-input"
                        value="{{ number_format($scheme->hourly_rate ?? 20000, 0, ',', '.') }}" placeholder="Rp 20.000">
                    <input type="hidden" name="hourly_rate" id="hourly_rate" value="{{ $scheme->hourly_rate ?? 20000 }}">
                    @error('hourly_rate')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="content_edit_rate" class="form-label">Gaji Per Konten Edit (Rp)</label>
                        <input type="text" id="content_edit_rate_display" class="form-control currency-input"
                            value="{{ number_format($scheme->content_edit_rate ?? 10000, 0, ',', '.') }}"
                            placeholder="Rp 10.000">
                        <input type="hidden" name="content_edit_rate" id="content_edit_rate"
                            value="{{ $scheme->content_edit_rate ?? 10000 }}">
                    </div>
                    <div class="form-group">
                        <label for="content_live_rate" class="form-label">Gaji Per Konten Live (Rp)</label>
                        <input type="text" id="content_live_rate_display" class="form-control currency-input"
                            value="{{ number_format($scheme->content_live_rate ?? 5000, 0, ',', '.') }}"
                            placeholder="Rp 5.000">
                        <input type="hidden" name="content_live_rate" id="content_live_rate"
                            value="{{ $scheme->content_live_rate ?? 5000 }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="monthly_target_hours" class="form-label">Target Jam Bulanan</label>
                    <input type="number" name="monthly_target_hours" id="monthly_target_hours" class="form-control"
                        value="{{ old('monthly_target_hours', $scheme->monthly_target_hours ?? 80) }}" min="0" max="744"
                        step="0.5" required>
                    <div class="form-hint">Target jam live per bulan untuk mendapatkan bonus</div>
                    @error('monthly_target_hours')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan
                    </button>
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