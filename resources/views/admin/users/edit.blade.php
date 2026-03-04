@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">Edit Pengguna: {{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}"
                        required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" id="password" class="form-control">
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-control form-select" required>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="task" class="form-label">Tugas</label>
                        <select name="task" id="task" class="form-control form-select">
                            <option value="Host Live" {{ old('task', $user->task) == 'Host Live' ? 'selected' : '' }}>Host
                                Live</option>
                            <option value="Editor" {{ old('task', $user->task) == 'Editor' ? 'selected' : '' }}>Editor
                            </option>
                            <option value="Admin Live" {{ old('task', $user->task) == 'Admin Live' ? 'selected' : '' }}>Admin
                                Live</option>
                            <option value="Content Creator" {{ old('task', $user->task) == 'Content Creator' ? 'selected' : '' }}>Content Creator</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor HP (WhatsApp)</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                        value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Status Pengguna</label>
                    <div class="toggle-switch-container">
                        <input type="hidden" name="is_active" value="0">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label" id="statusLabel">{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                    </div>
                </div>

                <hr style="border-color: var(--border); margin: 1.5rem 0;">

                <h4 class="mb-4">📊 Skema Bonus (Per User)</h4>
                <div class="alert alert-info" style="margin-bottom: 1.5rem;">
                    <small>Kosongkan untuk menggunakan aturan global. Jika diisi, nilai ini hanya berlaku untuk user
                        ini.</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="daily_live_hours" class="form-label">Jatah Live Per Hari (Jam)</label>
                        <input type="number" name="daily_live_hours" id="daily_live_hours" class="form-control"
                            value="{{ old('daily_live_hours', $scheme->daily_live_hours) }}" min="0.5" max="24" step="0.5"
                            placeholder="Global: {{ $globalDefaults['daily_live_hours'] }}">
                        <div class="form-hint">Global: {{ $globalDefaults['daily_live_hours'] }} jam</div>
                        @error('daily_live_hours')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="monthly_leave_days" class="form-label">Jatah Libur Per Bulan</label>
                        <input type="number" name="monthly_leave_days" id="monthly_leave_days" class="form-control"
                            value="{{ old('monthly_leave_days', $scheme->monthly_leave_days) }}" min="0" max="15"
                            placeholder="Global: {{ $globalDefaults['monthly_leave_days'] }}">
                        <div class="form-hint">Global: {{ $globalDefaults['monthly_leave_days'] }} hari</div>
                        @error('monthly_leave_days')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="bonus_pcs_threshold" class="form-label">Target Penjualan (pcs)</label>
                        <input type="number" name="bonus_pcs_threshold" id="bonus_pcs_threshold" class="form-control"
                            value="{{ old('bonus_pcs_threshold', $scheme->bonus_pcs_threshold) }}" min="1"
                            placeholder="Global: {{ $globalDefaults['bonus_pcs_threshold'] }}">
                        <div class="form-hint">Global: {{ $globalDefaults['bonus_pcs_threshold'] }} pcs</div>
                        @error('bonus_pcs_threshold')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="bonus_amount" class="form-label">Bonus Per Kelipatan (Rp)</label>
                        <input type="number" name="bonus_amount" id="bonus_amount" class="form-control"
                            value="{{ old('bonus_amount', $scheme->bonus_amount) }}" min="0"
                            placeholder="Global: {{ number_format($globalDefaults['bonus_amount'], 0, ',', '.') }}">
                        <div class="form-hint">Global: Rp {{ number_format($globalDefaults['bonus_amount'], 0, ',', '.') }}
                        </div>
                        @error('bonus_amount')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection