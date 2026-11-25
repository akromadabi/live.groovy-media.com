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

    <div class="card" style="max-width: 680px;">
        <div class="card-header" style="padding: 0.75rem 1rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 700;">Skema Gaji: {{ $user->name }}</h3>
        </div>
        <div class="card-body" style="padding: 1.15rem;">
            <!-- User Profile Box -->
            <div class="flex items-center gap-3 mb-3"
                style="padding: 0.75rem; background: var(--bg-secondary); border-radius: 8px;">
                <div class="avatar avatar-md">{{ substr($user->name, 0, 1) }}</div>
                <div>
                    <h4 class="mb-0" style="font-size: 0.95rem; font-weight: 700;">{{ $user->name }}</h4>
                    <p class="text-muted mb-0 text-xs">{{ $user->task ?? 'User' }} &bull; {{ $user->email }}</p>
                </div>
            </div>

            <form action="{{ route('admin.salary-schemes.update', $user) }}" method="POST" id="salaryForm">
                @csrf
                @method('PUT')

                @php
                    $isDifferentMode = ($marketplaceRateMode ?? 'same') === 'different' && count($activeMarketplaces) > 1;
                    $globalTargetPerMp = round(($globalDefaults['monthly_target_hours'] ?? 80) / max(1, count($activeMarketplaces)), 1);
                @endphp

                @if(!$isDifferentMode)
                    <!-- ==============================================
                         MODEL UPAH SERAGAM (SAMA SEMUA MARKETPLACE)
                         ============================================== -->
                    <input type="hidden" name="rate_mode" value="same">

                    <div class="flex items-center justify-between mb-3 p-2.5 rounded-lg" style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.18);">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" style="color: #4f46e5; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);">
                                <strong>Model Upah Seragam:</strong> Tarif ini otomatis berlaku untuk seluruh marketplace aktif ({{ implode(', ', array_column($activeMarketplaces, 'short_name')) }}).
                            </span>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="text-xs text-primary" style="font-size: 0.72rem; text-decoration: underline; white-space: nowrap;">Ubah di Pengaturan</a>
                    </div>

                    <div class="alert alert-info mb-3" style="padding: 0.5rem 0.75rem; font-size: 0.78rem; border-radius: 6px;">
                        Kosongkan field jika ingin mengikuti nilai dari <strong>Pengaturan Sistem</strong>. Nilai yang diisi hanya berlaku khusus untuk host ini.
                    </div>

                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 0.85rem;" class="mb-3">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.65rem;">
                            <!-- Gaji Live / Jam -->
                            <div>
                                <label for="hourly_rate_display" class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Gaji Live Per Jam (Rp)</label>
                                <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                    <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border-right: 1px solid var(--border);">Rp</span>
                                    <input type="text" id="hourly_rate_display" class="form-control currency-input"
                                        value="{{ $scheme->hourly_rate !== null ? number_format($scheme->hourly_rate, 0, ',', '.') : '' }}"
                                        placeholder="Global: {{ number_format($globalDefaults['hourly_rate'], 0, ',', '.') }}"
                                        style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                </div>
                                <input type="hidden" name="hourly_rate" id="hourly_rate" value="{{ $scheme->hourly_rate }}">
                                <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: Rp {{ number_format($globalDefaults['hourly_rate'], 0, ',', '.') }}/jam</div>
                            </div>

                            <!-- Target Live Bulanan -->
                            <div>
                                <label for="monthly_target_hours" class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Target Live Bulanan</label>
                                <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                    <input type="number" name="monthly_target_hours" id="monthly_target_hours" class="form-control"
                                        value="{{ old('monthly_target_hours', $scheme->monthly_target_hours) }}" min="0" max="744" step="0.5"
                                        placeholder="Global: {{ $globalDefaults['monthly_target_hours'] }}"
                                        style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                    <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 600; color: var(--text-secondary); background: var(--bg-secondary); border-left: 1px solid var(--border);">Jam</span>
                                </div>
                                <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: {{ $globalDefaults['monthly_target_hours'] }} jam</div>
                            </div>

                            <!-- Konten Edit -->
                            <div>
                                <label for="content_edit_rate_display" class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Gaji Per Konten Edit (Rp)</label>
                                <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                    <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border-right: 1px solid var(--border);">Rp</span>
                                    <input type="text" id="content_edit_rate_display" class="form-control currency-input"
                                        value="{{ $scheme->content_edit_rate !== null ? number_format($scheme->content_edit_rate, 0, ',', '.') : '' }}"
                                        placeholder="Global: {{ number_format($globalDefaults['content_edit_rate'], 0, ',', '.') }}"
                                        style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                </div>
                                <input type="hidden" name="content_edit_rate" id="content_edit_rate" value="{{ $scheme->content_edit_rate }}">
                                <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: Rp {{ number_format($globalDefaults['content_edit_rate'], 0, ',', '.') }}</div>
                            </div>

                            <!-- Konten Live -->
                            <div>
                                <label for="content_live_rate_display" class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Gaji Per Konten Live (Rp)</label>
                                <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                    <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border-right: 1px solid var(--border);">Rp</span>
                                    <input type="text" id="content_live_rate_display" class="form-control currency-input"
                                        value="{{ $scheme->content_live_rate !== null ? number_format($scheme->content_live_rate, 0, ',', '.') : '' }}"
                                        placeholder="Global: {{ number_format($globalDefaults['content_live_rate'], 0, ',', '.') }}"
                                        style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                </div>
                                <input type="hidden" name="content_live_rate" id="content_live_rate" value="{{ $scheme->content_live_rate }}">
                                <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: Rp {{ number_format($globalDefaults['content_live_rate'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- ==============================================
                         MODEL UPAH BEDA TIAP MARKETPLACE
                         ============================================== -->
                    <input type="hidden" name="rate_mode" value="different">

                    <div class="flex items-center justify-between mb-3 p-2.5 rounded-lg flex-wrap gap-2" style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.18);">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" style="color: #4f46e5; flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);">
                                <strong>Model Upah Beda Marketplace:</strong> Sesuaikan tarif gaji & target live masing-masing platform.
                            </span>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="text-xs text-primary" style="font-size: 0.72rem; text-decoration: underline; white-space: nowrap;">Ubah di Pengaturan</a>
                    </div>

                    <div class="alert alert-info mb-3" style="padding: 0.5rem 0.75rem; font-size: 0.78rem; border-radius: 6px;">
                        Kosongkan field jika ingin mengikuti nilai dari <strong>Pengaturan Sistem</strong>. Nilai yang diisi hanya berlaku khusus untuk host ini.
                    </div>

                    <!-- Marketplace Custom Rate Cards -->
                    <div class="grid grid-1 gap-2.5 mb-3">
                        @foreach($activeMarketplaces as $mpKey => $mp)
                            @php
                                $valH = (is_array($scheme->platform_rates) && isset($scheme->platform_rates[$mpKey]['hourly_rate']))
                                    ? $scheme->platform_rates[$mpKey]['hourly_rate']
                                    : ($scheme->hourly_rate);
                                $valT = (is_array($scheme->platform_rates) && isset($scheme->platform_rates[$mpKey]['target_hours']))
                                    ? $scheme->platform_rates[$mpKey]['target_hours']
                                    : null;
                                $valE = (is_array($scheme->platform_rates) && isset($scheme->platform_rates[$mpKey]['content_edit_rate']))
                                    ? $scheme->platform_rates[$mpKey]['content_edit_rate']
                                    : ($scheme->content_edit_rate);
                                $valL = (is_array($scheme->platform_rates) && isset($scheme->platform_rates[$mpKey]['content_live_rate']))
                                    ? $scheme->platform_rates[$mpKey]['content_live_rate']
                                    : ($scheme->content_live_rate);

                                $globalHourlyForMp = $mp['default_hourly_rate'] ?? $globalDefaults['hourly_rate'];
                            @endphp
                            <div class="mp-custom-card" id="card-mp-{{ $mpKey }}" style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 0.85rem; box-shadow: var(--shadow-sm);">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <x-platform-logo :platform="$mpKey" size="sm" />
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); line-height: 1.1;">
                                                {{ $mp['name'] }}
                                            </div>
                                            <span class="text-muted" style="font-size: 0.68rem;">Default Sistem: Rp {{ number_format($globalHourlyForMp, 0, ',', '.') }}/jam</span>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.65rem;">
                                    <!-- Gaji Live / Jam -->
                                    <div>
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Live / Jam</label>
                                        <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                            <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border-right: 1px solid var(--border);">Rp</span>
                                            <input type="text" id="rate_{{ $mpKey }}_hourly_display" class="form-control currency-input mp-input-hourly"
                                                value="{{ $valH !== null ? number_format($valH, 0, ',', '.') : '' }}"
                                                placeholder="Global: {{ number_format($globalHourlyForMp, 0, ',', '.') }}"
                                                style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                        </div>
                                        <input type="hidden" name="platform_rates[{{ $mpKey }}][hourly_rate]" id="rate_{{ $mpKey }}_hourly" value="{{ $valH }}">
                                        <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: Rp {{ number_format($globalHourlyForMp, 0, ',', '.') }}</div>
                                    </div>

                                    <!-- Target Jam Bulanan -->
                                    <div>
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Target Live Bulanan</label>
                                        <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                            <input type="number" step="0.5" min="0" max="744" name="platform_rates[{{ $mpKey }}][target_hours]" id="rate_{{ $mpKey }}_target"
                                                class="form-control mp-input-target"
                                                value="{{ $valT }}"
                                                placeholder="Global: {{ $globalTargetPerMp }}"
                                                style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                            <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 600; color: var(--text-secondary); background: var(--bg-secondary); border-left: 1px solid var(--border);">Jam</span>
                                        </div>
                                        <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: {{ $globalTargetPerMp }} jam</div>
                                    </div>

                                    <!-- Konten Edit -->
                                    <div>
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Konten Edit</label>
                                        <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                            <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border-right: 1px solid var(--border);">Rp</span>
                                            <input type="text" id="rate_{{ $mpKey }}_edit_display" class="form-control currency-input mp-input-edit"
                                                value="{{ $valE !== null ? number_format($valE, 0, ',', '.') : '' }}"
                                                placeholder="Global: {{ number_format($globalDefaults['content_edit_rate'], 0, ',', '.') }}"
                                                style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                        </div>
                                        <input type="hidden" name="platform_rates[{{ $mpKey }}][content_edit_rate]" id="rate_{{ $mpKey }}_edit" value="{{ $valE }}">
                                        <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: Rp {{ number_format($globalDefaults['content_edit_rate'], 0, ',', '.') }}</div>
                                    </div>

                                    <!-- Konten Live -->
                                    <div>
                                        <label class="form-label mb-1" style="font-size: 0.72rem; font-weight: 600;">Konten Live</label>
                                        <div class="input-group" style="display: flex; align-items: stretch; border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: var(--bg-primary);">
                                            <span style="display: flex; align-items: center; padding: 0 0.55rem; font-size: 0.72rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border-right: 1px solid var(--border);">Rp</span>
                                            <input type="text" id="rate_{{ $mpKey }}_live_display" class="form-control currency-input mp-input-live"
                                                value="{{ $valL !== null ? number_format($valL, 0, ',', '.') : '' }}"
                                                placeholder="Global: {{ number_format($globalDefaults['content_live_rate'], 0, ',', '.') }}"
                                                style="border: none; border-radius: 0; font-size: 0.82rem; font-weight: 700; padding: 0.4rem 0.55rem;">
                                        </div>
                                        <input type="hidden" name="platform_rates[{{ $mpKey }}][content_live_rate]" id="rate_{{ $mpKey }}_live" value="{{ $valL }}">
                                        <div class="form-hint" style="font-size: 0.68rem; margin-top: 2px;">Default: Rp {{ number_format($globalDefaults['content_live_rate'], 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Total Target Summary Indicator -->
                    <div class="flex items-center justify-between mt-2.5 mb-3" style="background: var(--bg-secondary); border: 1px dashed var(--border); border-radius: 8px; padding: 0.5rem 0.75rem;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Total Target Semua Platform:</span>
                        <span id="custom-total-target-display" style="font-size: 0.82rem; font-weight: 700; color: #4f46e5;">0 Jam / Bulan</span>
                    </div>
                @endif

                <hr style="border-color: var(--border); margin: 1rem 0;">

                <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.35rem;">📊 Skema Bonus Khusus (Opsional)</h4>
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
            window.formatCurrency = function(value) {
                if (value === null || value === '' || isNaN(value)) return '';
                const num = parseInt(value, 10) || 0;
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            // Parse currency to number
            window.parseCurrency = function(value) {
                const cleaned = String(value).replace(/[^0-9]/g, '');
                return cleaned !== '' ? parseInt(cleaned, 10) : '';
            };

            // Setup currency inputs
            function initCurrencyInputs() {
                const currencyInputs = document.querySelectorAll('.currency-input');
                currencyInputs.forEach(function (input) {
                    const hiddenId = input.id.replace('_display', '');
                    const hiddenInput = document.getElementById(hiddenId);

                    if (!hiddenInput) return;

                    // Format on load
                    if (hiddenInput.value && hiddenInput.value !== '') {
                        input.value = formatCurrency(hiddenInput.value);
                    } else {
                        input.value = '';
                    }

                    // Format on input
                    input.addEventListener('input', function (e) {
                        const raw = e.target.value.replace(/[^0-9]/g, '');
                        if (raw === '') {
                            hiddenInput.value = '';
                            e.target.value = '';
                        } else {
                            const numValue = parseInt(raw, 10);
                            hiddenInput.value = numValue;
                            e.target.value = formatCurrency(numValue);
                        }
                    });

                    // Select all on focus
                    input.addEventListener('focus', function (e) {
                        setTimeout(() => e.target.select(), 0);
                    });
                });
            }

            initCurrencyInputs();

            // Calculate and display total target for custom mode
            const defaultPerMp = {{ (float) $globalTargetPerMp }};
            window.updateTotalTarget = function() {
                let total = 0;
                const targetInputs = document.querySelectorAll('.mp-input-target');
                if (targetInputs.length === 0) return;

                targetInputs.forEach(function(inp) {
                    const val = parseFloat(inp.value);
                    total += !isNaN(val) ? val : defaultPerMp;
                });

                const el = document.getElementById('custom-total-target-display');
                if (el) {
                    el.textContent = (Math.round(total * 10) / 10) + ' Jam / Bulan';
                }
            };

            document.querySelectorAll('.mp-input-target').forEach(function(inp) {
                inp.addEventListener('input', updateTotalTarget);
            });
            updateTotalTarget();
        });
    </script>
@endpush
