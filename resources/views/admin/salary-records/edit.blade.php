@extends('layouts.app')

@section('title', 'Edit Rekap Gaji')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3 class="card-title">Edit Rekap Gaji</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.salary-records.update', $salaryRecord) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="form-label">User <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-control form-select" required>
                        <option value="">Pilih User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $salaryRecord->user_id) == $user->id ? 'selected' : '' }}>
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
                        <select name="year" class="form-control form-select" required>
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('year', $salaryRecord->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bulan <span class="text-danger">*</span></label>
                        <select name="month" class="form-control form-select" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', $salaryRecord->month) == $m ? 'selected' : '' }}>
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
                            <input type="radio" name="term" value="1" {{ old('term', $salaryRecord->term) == '1' ? 'checked' : '' }} required>
                            <span>T1 (Tanggal 1-15)</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="term" value="2" {{ old('term', $salaryRecord->term) == '2' ? 'checked' : '' }}>
                            <span>T2 (Tanggal 16-akhir)</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Gaji <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" value="{{ old('amount', $salaryRecord->amount) }}" 
                        placeholder="Contoh: 1500000" min="0" step="1000" required>
                    @error('amount')
                        <span class="text-danger" style="font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)">{{ old('notes', $salaryRecord->notes) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control form-select" required>
                        <option value="pending" {{ old('status', $salaryRecord->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status', $salaryRecord->status) == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update
                    </button>
                    <a href="{{ route('admin.salary-records.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
