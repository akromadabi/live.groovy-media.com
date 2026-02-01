@extends('layouts.app')

@section('title', 'Kontrol Gaji')

@section('content')
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Tugas</th>
                        <th>Gaji/Jam</th>
                        <th>Gaji/Konten Edit</th>
                        <th>Gaji/Konten Live</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar avatar-sm">{{ substr($user->name, 0, 1) }}</div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->task ?? '-' }}</td>
                            <td>Rp {{ number_format($user->salaryScheme->hourly_rate ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($user->salaryScheme->content_edit_rate ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($user->salaryScheme->content_live_rate ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('admin.salary-schemes.edit', $user) }}" class="btn btn-secondary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" width="16" height="16">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 3rem;">
                                Tidak ada pengguna
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection