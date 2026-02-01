@extends('layouts.app')

@section('title', 'Edit Rekap Gaji')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-2" style="gap: 1.5rem;">
        <!-- Summary Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ringkasan Gaji</h3>
            </div>
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4"
                    style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius);">
                    <div class="avatar avatar-lg">{{ substr($salary->user->name, 0, 1) }}</div>
                    <div>
                        <h4 class="mb-1">{{ $salary->user->name }}</h4>
                        <p class="text-muted mb-0">{{ $salary->user->task ?? 'User' }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-muted text-sm">Periode</div>
                    <div class="font-medium">{{ $salary->period_label }}</div>
                    <div class="text-sm">{{ $salary->date_range_label }}</div>
                </div>

                <table class="table" style="margin-bottom: 0;">
                    <tbody>
                        <tr>
                            <td class="text-muted">Total Jam</td>
                            <td class="text-right">{{ $salary->total_hours }} jam
                                @if($salary->target_met)
                                    <span class="badge badge-success" style="margin-left: 4px;">Target ✓</span>
                                @else
                                    <span class="badge badge-warning" style="margin-left: 4px;">Belum Target</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Gaji Live</td>
                            <td class="text-right">Rp {{ number_format($salary->live_salary, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bonus Konten Edit</td>
                            <td class="text-right">Rp {{ number_format($salary->content_edit_bonus, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bonus Konten Live</td>
                            <td class="text-right">Rp {{ number_format($salary->content_live_bonus, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Penjualan</td>
                            <td class="text-right">{{ $salary->total_sales }} pcs</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bonus Penjualan</td>
                            <td class="text-right" style="color: var(--success);">
                                Rp {{ number_format($salary->sales_bonus, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Potongan & Status</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.salaries.update', $salary) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="deduction" class="form-label">Potongan (Rp)</label>
                        <input type="number" name="deduction" id="deduction" class="form-control"
                            value="{{ old('deduction', $salary->deduction) }}" min="0">
                        @error('deduction')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="deduction_notes" class="form-label">Catatan Potongan</label>
                        <textarea name="deduction_notes" id="deduction_notes" class="form-control" rows="3"
                            placeholder="Keterangan potongan...">{{ old('deduction_notes', $salary->deduction_notes) }}</textarea>
                        @error('deduction_notes')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control form-select" required>
                            <option value="draft" {{ $salary->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="finalized" {{ $salary->status === 'finalized' ? 'selected' : '' }}>Final</option>
                            <option value="paid" {{ $salary->status === 'paid' ? 'selected' : '' }}>Dibayar</option>
                        </select>
                    </div>

                    <hr style="border-color: var(--border); margin: 1.5rem 0;">

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-lg">Total Gaji:</span>
                        <span class="text-xl font-bold" style="color: var(--success);" id="totalSalary">
                            {{ $salary->formatted_total }}
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>

                <hr style="border-color: var(--border); margin: 1.5rem 0;">

                <form action="{{ route('admin.salaries.recalculate', $salary) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-block">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Hitung Ulang dari Absensi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('deduction').addEventListener('input', function () {
            const baseSalary = {{ (float) $salary->live_salary + (float) $salary->content_edit_bonus + (float) $salary->content_live_bonus + (float) $salary->sales_bonus }};
            const deduction = parseFloat(this.value) || 0;
            const total = Math.max(0, baseSalary - deduction);
            document.getElementById('totalSalary').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    </script>
@endsection