@extends('layouts.app')

@section('title', 'Kontrol Gaji')

@section('content')
    <div class="card">
        <div class="card-header flex items-center justify-between" style="padding: 0.65rem 0.85rem;">
            <div>
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 600;">Skema & Tarif Gaji Pengguna</h3>
                <p class="text-xs text-muted mb-0">Atur tarif per jam, target live, dan komisi konten untuk masing-masing host/staf</p>
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
                        <th class="hide-on-mobile">Target Live</th>
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
                                @if(($marketplaceRateMode ?? 'same') === 'different' && isset($activeMarketplaces) && count($activeMarketplaces) > 1)
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        @foreach($activeMarketplaces as $mpKey => $mp)
                                            <div style="font-size: 0.78rem; display: flex; align-items: center; gap: 6px;">
                                                <x-platform-logo :platform="$mpKey" size="xs" />
                                                <span class="text-muted" style="font-size: 0.72rem; font-weight: 500;">{{ $mp['short_name'] }}:</span>
                                                <span style="font-weight: 700; color: var(--text-primary);">
                                                    Rp {{ number_format($user->salaryScheme?->getHourlyRateForPlatform($mpKey) ?? $mp['default_hourly_rate'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="font-semibold text-sm" style="color: var(--primary);">
                                        Rp {{ number_format($user->salaryScheme?->getHourlyRateForPlatform() ?? ($globalDefaults['hourly_rate'] ?? 20000), 0, ',', '.') }} <span class="text-xs text-muted font-normal">/ jam</span>
                                    </span>
                                @endif
                            </td>
                            <td class="hide-on-mobile">
                                @if(isset($activeMarketplaces) && count($activeMarketplaces) > 1 && is_array($user->salaryScheme?->platform_rates))
                                    <div style="display: flex; flex-direction: column; gap: 3px;">
                                        @foreach($activeMarketplaces as $mpKey => $mp)
                                            <div style="font-size: 0.78rem; display: flex; align-items: center; gap: 6px;">
                                                <x-platform-logo :platform="$mpKey" size="xs" />
                                                <span class="text-muted" style="font-size: 0.72rem; font-weight: 500;">{{ $mp['short_name'] }}:</span>
                                                <span style="font-weight: 700; color: var(--text-primary);">
                                                    {{ $user->salaryScheme?->getTargetHoursForPlatform($mpKey) ?? 40 }}j
                                                </span>
                                            </div>
                                        @endforeach
                                        <div style="font-size: 0.7rem; color: var(--text-muted); border-top: 1px dashed var(--border); padding-top: 2px;">
                                            Total: <strong>{{ $user->salaryScheme?->getTargetHoursForPlatform() ?? ($globalDefaults['monthly_target_hours'] ?? 80) }} jam</strong>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold">{{ $user->salaryScheme?->getTargetHoursForPlatform() ?? ($globalDefaults['monthly_target_hours'] ?? 80) }} jam</span>
                                @endif
                            </td>
                            <td class="hide-on-mobile">
                                <span class="text-sm">Rp {{ number_format($user->salaryScheme?->getContentEditRateForPlatform() ?? ($globalDefaults['content_edit_rate'] ?? 10000), 0, ',', '.') }}</span>
                            </td>
                            <td class="hide-on-mobile">
                                <span class="text-sm">Rp {{ number_format($user->salaryScheme?->getContentLiveRateForPlatform() ?? ($globalDefaults['content_live_rate'] ?? 5000), 0, ',', '.') }}</span>
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
                            <td colspan="7" class="text-center text-muted" style="padding: 2.5rem;">
                                Tidak ada data pengguna
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
