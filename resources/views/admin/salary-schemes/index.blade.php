@extends('layouts.app')

@section('title', 'Kontrol Gaji')

@section('content')
    <div class="card">
        <div class="card-header flex items-center justify-between" style="padding: 0.65rem 0.85rem;">
            <div>
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Skema & Tarif Gaji Pengguna</h3>
                <p class="text-xs text-muted mb-0">Atur tarif per jam dan komisi konten untuk masing-masing host/staf</p>
            </div>
            <span class="text-xs text-muted font-medium">{{ $users->count() }} Pengguna</span>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Tugas</th>
                        <th>Gaji / Jam</th>
                        <th class="hide-on-mobile">Konten Edit</th>
                        <th class="hide-on-mobile">Konten Live</th>
                        <th style="width: 80px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar avatar-sm">{{ substr($user->name, 0, 1) }}</div>
                                    <div>
                                        <div class="font-medium text-sm">{{ $user->name }}</div>
                                        <div class="text-xs text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $user->task ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-sm" style="color: var(--primary);">Rp {{ number_format($user->salaryScheme->hourly_rate ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td class="hide-on-mobile">
                                <span class="text-sm">Rp {{ number_format($user->salaryScheme->content_edit_rate ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td class="hide-on-mobile">
                                <span class="text-sm">Rp {{ number_format($user->salaryScheme->content_live_rate ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.salary-schemes.edit', $user) }}" class="btn btn-secondary btn-sm" style="height: 28px; padding: 0 0.65rem; font-size: 0.75rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" width="14" height="14">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                                Tidak ada data pengguna
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
