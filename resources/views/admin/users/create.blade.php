@extends('layouts.app')

@section('title', 'Tambah Pengguna')

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
            <h3 class="card-title">Tambah Pengguna Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}"
                        required>
                    @error('username')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-control form-select" required>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="task" class="form-label">Tugas</label>
                        <select name="task" id="task" class="form-control form-select">
                            <option value="Host Live" {{ old('task') == 'Host Live' ? 'selected' : '' }}>Host Live</option>
                            <option value="Editor" {{ old('task') == 'Editor' ? 'selected' : '' }}>Editor</option>
                            <option value="Admin Live" {{ old('task') == 'Admin Live' ? 'selected' : '' }}>Admin Live</option>
                            <option value="Content Creator" {{ old('task') == 'Content Creator' ? 'selected' : '' }}>Content
                                Creator</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Nomor HP (WhatsApp)</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}"
                        placeholder="08xxxx">
                    @error('phone')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="flex items-center gap-2" style="cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            style="width: 18px; height: 18px;">
                        <span>Aktifkan pengguna</span>
                    </label>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection