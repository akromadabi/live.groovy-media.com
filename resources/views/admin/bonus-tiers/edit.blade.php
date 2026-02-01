@extends('layouts.app')

@section('title', 'Edit Tier Bonus')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.bonus-tiers.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">Edit Tier Bonus</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bonus-tiers.update', $bonusTier) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="min_sales" class="form-label">Minimum Penjualan</label>
                        <input type="number" name="min_sales" id="min_sales" class="form-control"
                            value="{{ old('min_sales', $bonusTier->min_sales) }}" min="0" required>
                        <div class="form-hint">Jumlah penjualan minimum untuk tier ini</div>
                        @error('min_sales')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="max_sales" class="form-label">Maksimum Penjualan</label>
                        <input type="number" name="max_sales" id="max_sales" class="form-control"
                            value="{{ old('max_sales', $bonusTier->max_sales) }}" min="0">
                        <div class="form-hint">Kosongkan jika tidak ada batas</div>
                        @error('max_sales')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="bonus_amount" class="form-label">Jumlah Bonus (Rp)</label>
                    <input type="number" name="bonus_amount" id="bonus_amount" class="form-control"
                        value="{{ old('bonus_amount', $bonusTier->bonus_amount) }}" min="0" required>
                    @error('bonus_amount')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                    <input type="text" name="description" id="description" class="form-control"
                        value="{{ old('description', $bonusTier->description) }}"
                        placeholder="Contoh: Bonus Bronze (20-49 pcs)">
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bonusTier->is_active) ? 'checked' : '' }}>
                        <span>Aktif</span>
                    </label>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.bonus-tiers.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection