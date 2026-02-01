@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="grid grid-2" style="max-width: 900px;">
        <!-- Profile Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Profil</h3>
            </div>
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4"
                    style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius);">
                    <div class="avatar avatar-lg">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div>
                        <h4 class="mb-0">{{ auth()->user()->name }}</h4>
                        <p class="text-muted mb-0">{{ auth()->user()->task ?? 'User' }}</p>
                    </div>
                </div>

                <form action="{{ route('user.settings.update-profile') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor HP (WhatsApp)</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                            value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xxxx">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Profil
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Section -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ubah Password</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('user.settings.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                        @error('current_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Appearance Settings -->
    <div class="card mt-6" style="max-width: 450px;">
        <div class="card-header">
            <h3 class="card-title">Tampilan</h3>
        </div>
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="mb-1">Mode Malam</h4>
                    <p class="text-muted mb-0 text-sm">Aktifkan tema gelap untuk kenyamanan mata</p>
                </div>
                <button type="button" class="theme-toggle" onclick="toggleTheme()" style="width: 48px; height: 48px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection