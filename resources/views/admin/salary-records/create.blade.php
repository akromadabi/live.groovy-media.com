@extends('layouts.app')

@section('title', 'Tambah Rekap Gaji')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3 class="card-title">Tambah Rekap Gaji</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.salary-records.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">User <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-control form-select" required>
                        <option value="">Pilih User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                data-hourly-rate="{{ $user->salaryScheme->hourly_rate ?? 0 }}"
                                data-content-edit-rate="{{ $user->salaryScheme->content_edit_rate ?? 0 }}"
                                data-content-live-rate="{{ $user->salaryScheme->content_live_rate ?? 0 }}"
                                data-sales-bonus="{{ $user->salaryScheme->sales_bonus_nominal ?? 0 }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->task }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="text-danger" style="font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <select name="year" id="year" class="form-control form-select" required>
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bulan <span class="text-danger">*</span></label>
                        <select name="month" id="month" class="form-control form-select" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Termin <span class="text-danger">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="term" id="term1" value="1" {{ old('term', '1') == '1' ? 'checked' : '' }} required>
                            <span>T1 (Tanggal 1-15)</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="term" id="term2" value="2" {{ old('term') == '2' ? 'checked' : '' }}>
                            <span>T2 (Tanggal 16-akhir)</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Gaji <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}"
                        placeholder="Contoh: 1500000" min="0" step="1000" required>
                    <small class="text-muted" id="salary-hint">Pilih user untuk menghitung gaji otomatis</small>
                    @error('amount')
                        <span class="text-danger" style="font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2"
                        placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control form-select" required>
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('admin.salary-records.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance data for salary calculation -->
    <script>
        const attendanceData = @json($attendanceData ?? []);
    </script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user_id');
    const yearSelect = document.getElementById('year');
    const monthSelect = document.getElementById('month');
    const term1Radio = document.getElementById('term1');
    const term2Radio = document.getElementById('term2');
    const amountInput = document.getElementById('amount');
    const salaryHint = document.getElementById('salary-hint');

    function calculateSalary() {
        const userId = userSelect.value;
        const year = yearSelect.value;
        const month = monthSelect.value;
        const term = term1Radio.checked ? '1' : (term2Radio.checked ? '2' : '');

        if (!userId || !year || !month || !term) {
            return;
        }

        // Build key for lookup
        const key = `${userId}-${year}-${month}-${term}`;
        const data = attendanceData[key];

        if (data) {
            amountInput.value = Math.round(data.salary);
            salaryHint.textContent = `Jam: ${data.hours} | Edit: ${data.content_edit} | Live: ${data.content_live} | Sales: ${data.sales}`;
        } else {
            salaryHint.textContent = 'Tidak ada data absensi untuk periode ini';
        }
    }

    userSelect.addEventListener('change', calculateSalary);
    yearSelect.addEventListener('change', calculateSalary);
    monthSelect.addEventListener('change', calculateSalary);
    term1Radio.addEventListener('change', calculateSalary);
    term2Radio.addEventListener('change', calculateSalary);

    // Calculate on load if user is pre-selected
    if (userSelect.value) {
        calculateSalary();
    }
});
</script>
@endpush