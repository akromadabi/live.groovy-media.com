@extends('layouts.app')

@section('title', 'Pengguna')

@push('styles')
<style>
    .users-control-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 12px);
        padding: 0.5rem 0.875rem;
        margin-bottom: 0.75rem;
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
    }
    .users-filter-form {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex: 1;
        flex-wrap: wrap;
    }
    .users-search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 180px;
        max-width: 260px;
        flex: 1;
    }
    .users-search-icon {
        position: absolute;
        left: 0.65rem;
        width: 14px;
        height: 14px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .users-search-input {
        height: 34px !important;
        font-size: 0.8125rem !important;
        padding-left: 2rem !important;
        padding-right: 0.65rem !important;
        border-radius: 6px !important;
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        color: var(--text-primary) !important;
        width: 100% !important;
    }
    .users-filter-select {
        height: 34px !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: var(--text-primary) !important;
        background-color: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: 6px !important;
        padding: 0 1.85rem 0 0.65rem !important;
        cursor: pointer;
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.5rem center !important;
        background-size: 13px 13px !important;
        width: auto !important;
        min-width: 120px;
    }
    .users-btn-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        height: 34px;
        padding: 0 0.65rem;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-muted);
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 6px;
        text-decoration: none;
        transition: all var(--transition);
        white-space: nowrap;
    }
    .users-btn-reset:hover {
        color: var(--danger);
        border-color: var(--danger);
        background: rgba(239, 68, 68, 0.05);
    }
    .users-actions {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .users-actions .btn {
        height: 34px;
        font-size: 0.8125rem;
        padding: 0 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 6px;
        white-space: nowrap;
    }

    /* Mobile Cards View */
    .users-mobile-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .user-card-mobile {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        transition: border-color var(--transition);
    }
    .user-card-mobile:hover {
        border-color: var(--primary);
    }
    .user-card-mobile-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }
    .user-card-mobile-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding-top: 0.35rem;
        border-top: 1px dashed var(--border);
        font-size: 0.75rem;
    }
    .user-card-mobile-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding-top: 0.4rem;
        border-top: 1px solid var(--border);
    }
    .user-card-mobile-actions .btn {
        flex: 1;
        height: 32px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        border-radius: 6px;
        padding: 0 0.4rem;
    }

    @media (max-width: 768px) {
        .users-control-bar {
            flex-direction: column;
            align-items: stretch;
            padding: 0.55rem 0.65rem;
            gap: 0.45rem;
        }
        .users-filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem;
            width: 100%;
        }
        .users-search-wrapper {
            grid-column: 1 / -1;
            max-width: none;
            width: 100%;
        }
        .users-filter-select {
            width: 100% !important;
            min-width: 0 !important;
            height: 33px !important;
            font-size: 0.78rem !important;
        }
        .users-btn-reset {
            grid-column: 1 / -1;
            width: 100%;
            justify-content: center;
            height: 30px;
            font-size: 0.75rem;
        }
        .users-actions {
            width: 100%;
        }
        .users-actions .btn {
            width: 100%;
            justify-content: center;
            height: 36px;
        }
    }
</style>
@endpush

@section('content')
    <!-- Unified Control Bar: Filter (Left) Tambah Pengguna (Right) -->
    <div class="users-control-bar">
        <form action="{{ route('admin.users.index') }}" method="GET" class="users-filter-form">
            <div class="users-search-wrapper">
                <svg class="users-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" class="form-control users-search-input" placeholder="Cari nama pengguna..."
                    value="{{ request('search') }}" onchange="this.form.submit()">
            </div>
            
            <select name="status" class="form-control form-select users-filter-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            @if(isset($tasks) && $tasks->count() > 0)
                <select name="task" class="form-control form-select users-filter-select" onchange="this.form.submit()">
                    <option value="">Semua Tugas</option>
                    @foreach($tasks as $t)
                        <option value="{{ $t }}" {{ request('task') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            @endif

            @if(request()->hasAny(['search', 'status', 'task']))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm users-btn-reset">
                    Reset
                </a>
            @endif
        </form>

        <div class="users-actions">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="card hide-on-mobile mb-4">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Tugas</th>
                        <th>Role</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2" style="min-width: 140px;">
                                    <div class="avatar avatar-sm" style="width: 28px; height: 28px; font-size: 0.75rem; flex-shrink: 0;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="font-medium" style="font-size: 0.8125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->name }}</div>
                                        <div class="text-xs text-muted" style="font-size: 0.68rem;">{{ '@' . ($user->username ?? strtolower(explode(' ', $user->name)[0])) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: 0.78rem;">{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-secondary" style="font-size: 0.7rem; font-weight: 500;">
                                    {{ $user->task ?? 'Host Live' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}" style="font-size: 0.7rem;">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                    class="toggle-form" style="display: inline-block; margin: 0;">
                                    @csrf
                                    <label class="toggle-switch" style="transform: scale(0.85); margin: 0;">
                                        <input type="checkbox" {{ $user->is_active ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <div class="action-btns" style="justify-content: center; gap: 3px;">
                                    @if($user->id !== auth()->id() && $user->is_active)
                                        <form action="{{ route('admin.users.impersonate', $user) }}" method="POST"
                                            style="display:inline; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm btn-icon" style="width: 27px; height: 27px; padding: 0;" title="Login Bypass Cepat">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" width="13" height="13">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm btn-icon"
                                        style="width: 27px; height: 27px; padding: 0;" title="Edit Pengguna">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" width="13" height="13">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            style="display:inline; margin: 0;" id="delete-user-{{ $user->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-user-{{ $user->id }}')"
                                                class="btn btn-danger btn-sm btn-icon" style="width: 27px; height: 27px; padding: 0;" title="Hapus Pengguna">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" width="13" height="13">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                                Tidak ada data pengguna. <a href="{{ route('admin.users.create') }}">Tambah sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-footer" style="padding: 0.6rem 0.875rem;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Mobile Card View -->
    <div class="users-mobile-container hide-on-desktop mb-4">
        @forelse($users as $user)
            <div class="user-card-mobile">
                <div class="user-card-mobile-top">
                    <div class="flex items-center gap-2" style="min-width: 0; flex: 1;">
                        <div class="avatar avatar-sm" style="width: 32px; height: 32px; font-size: 0.8rem; flex-shrink: 0;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <div class="font-semibold text-sm" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-primary); line-height: 1.2;">
                                {{ $user->name }}
                            </div>
                            <div class="text-xs text-muted flex items-center gap-1" style="font-size: 0.7rem; line-height: 1.2; margin-top: 2px;">
                                <span>{{ '@' . ($user->username ?? strtolower(explode(' ', $user->name)[0])) }}</span>
                                <span style="opacity: 0.4;">•</span>
                                <span class="badge {{ $user->role === 'admin' ? 'badge-primary' : 'badge-secondary' }}" style="font-size: 0.62rem; padding: 0.08rem 0.35rem;">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Status Toggle -->
                    <div style="flex-shrink: 0;">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="margin: 0;">
                            @csrf
                            <label class="toggle-switch" style="transform: scale(0.85); margin: 0;">
                                <input type="checkbox" {{ $user->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="toggle-slider"></span>
                            </label>
                        </form>
                    </div>
                </div>

                <div class="user-card-mobile-meta">
                    <div class="text-xs text-muted flex items-center gap-1" style="min-width: 0; flex: 1; overflow: hidden; text-overflow: ellipsis; font-size: 0.72rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12" style="flex-shrink: 0; opacity: 0.7;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="badge badge-secondary" style="font-size: 0.68rem; font-weight: 500; padding: 0.15rem 0.45rem;">
                            {{ $user->task ?? 'Host Live' }}
                        </span>
                    </div>
                </div>

                <div class="user-card-mobile-actions">
                    @if($user->id !== auth()->id() && $user->is_active)
                        <form action="{{ route('admin.users.impersonate', $user) }}" method="POST" style="flex: 1; margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" style="width: 100%; height: 32px; font-size: 0.75rem; justify-content: center; gap: 4px;" title="Login Bypass Cepat">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Bypass
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm" style="flex: 1; height: 32px; font-size: 0.75rem; justify-content: center; gap: 4px;" title="Edit Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="flex: 1; margin: 0;" id="delete-user-m-{{ $user->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-user-m-{{ $user->id }}')" class="btn btn-danger btn-sm" style="width: 100%; height: 32px; font-size: 0.75rem; justify-content: center; gap: 4px;" title="Hapus Pengguna">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="card text-center text-muted" style="padding: 2.5rem 1rem;">
                Tidak ada data pengguna. <a href="{{ route('admin.users.create') }}">Tambah sekarang</a>
            </div>
        @endforelse

        @if($users->hasPages())
            <div style="margin-top: 0.75rem;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
