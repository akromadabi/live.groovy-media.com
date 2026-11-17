@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="grid grid-2 gap-3" style="max-width: 860px;">
        <!-- Profile Section -->
        <div class="card">
            <div class="card-header" style="padding: 0.65rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Edit Profil</h3>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <div class="flex items-center gap-3 mb-3"
                    style="padding: 0.65rem; background: var(--bg-secondary); border-radius: 8px;">
                    <div class="avatar avatar-md">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div>
                        <div class="font-semibold text-sm">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-muted">{{ auth()->user()->task ?? 'User' }} &bull; {{ auth()->user()->email }}</div>
                    </div>
                </div>

                <form action="{{ route('user.settings.update-profile') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-2">
                        <label for="name" class="form-label" style="font-size: 0.8rem;">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label for="email" class="form-label" style="font-size: 0.8rem;">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone" class="form-label" style="font-size: 0.8rem;">Nomor HP (WhatsApp)</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                            value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xxxx">
                    </div>

                    <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Profil
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Section -->
        <div class="card">
            <div class="card-header" style="padding: 0.65rem 0.85rem;">
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Ubah Password</h3>
            </div>
            <div class="card-body" style="padding: 1rem;">
                <form action="{{ route('user.settings.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-2">
                        <label for="current_password" class="form-label" style="font-size: 0.8rem;">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                        @error('current_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-2">
                        <label for="password" class="form-label" style="font-size: 0.8rem;">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label" style="font-size: 0.8rem;">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="height: 36px; padding: 0 1.25rem; font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Appearance Settings -->
    <div class="card mt-3" style="max-width: 420px;">
        <div class="card-header" style="padding: 0.65rem 0.85rem;">
            <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Tampilan</h3>
        </div>
        <div class="card-body" style="padding: 0.85rem 1rem;">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="mb-0" style="font-size: 0.875rem;">Mode Malam</h4>
                    <p class="text-muted mb-0 text-xs">Aktifkan tema gelap untuk kenyamanan mata</p>
                </div>
                <button type="button" class="theme-toggle" onclick="toggleTheme()" style="width: 36px; height: 36px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection
