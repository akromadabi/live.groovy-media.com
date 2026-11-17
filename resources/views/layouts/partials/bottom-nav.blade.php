<!-- Bottom Navigation (Mobile) - Modern Pixel-Perfect Style -->
<style>
/* Pixel-Perfect Bottom Navigation */
.bottom-nav {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 106 !important;
    height: auto !important;
    min-height: 58px !important;
    background: var(--surface) !important;
    border-top: 1px solid var(--border) !important;
    box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.05) !important;
    padding-bottom: env(safe-area-inset-bottom, 0px) !important;
    transition: background-color 0.2s, border-color 0.2s, box-shadow 0.2s;
}

[data-theme="dark"] .bottom-nav {
    background: var(--surface) !important;
    border-top: 1px solid var(--border) !important;
    box-shadow: 0 -2px 14px rgba(0, 0, 0, 0.3) !important;
}

.bottom-nav-container {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    height: 58px !important;
    padding: 0 0.25rem !important;
    position: relative !important;
    max-width: 500px !important;
    margin: 0 auto !important;
}

/* Symmetrical Grid: Each item takes equal flex: 1 */
.bottom-nav-item {
    flex: 1 1 0% !important;
    min-width: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 3px !important;
    padding: 4px 2px 7px 2px !important;
    color: var(--text-muted) !important;
    text-decoration: none !important;
    position: relative !important;
    user-select: none !important;
    -webkit-tap-highlight-color: transparent !important;
    transition: color 0.15s ease, opacity 0.2s ease !important;
    overflow: visible !important;
}

/* Identical Icon Geometry & Baseline */
.bottom-nav-item svg {
    width: 22px !important;
    height: 22px !important;
    stroke-width: 1.8 !important;
    flex-shrink: 0 !important;
    transition: color 0.15s ease, stroke-width 0.15s ease !important;
    transform: none !important;
    filter: none !important;
}

/* Label text: crisp, identical baseline across all items */
.bottom-nav-item span {
    font-size: 0.68rem !important;
    font-weight: 500 !important;
    line-height: 1.1 !important;
    letter-spacing: 0.01em !important;
    white-space: nowrap !important;
    transform: none !important;
    transition: color 0.15s ease, font-weight 0.15s ease !important;
}

/* Active State */
.bottom-nav-item.active {
    color: var(--primary) !important;
}

.bottom-nav-item.active svg {
    color: var(--primary) !important;
    stroke-width: 2.2 !important;
    transform: none !important;
}

.bottom-nav-item.active span {
    font-weight: 600 !important;
    color: var(--primary) !important;
    transform: none !important;
}

/* Precision Active Pill Indicator */
.bottom-nav-item::after {
    content: '' !important;
    position: absolute !important;
    bottom: 3px !important;
    left: 50% !important;
    transform: translateX(-50%) scaleX(0) !important;
    width: 14px !important;
    height: 2.5px !important;
    background: var(--primary) !important;
    border-radius: 99px !important;
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.bottom-nav-item.active::after {
    transform: translateX(-50%) scaleX(1) !important;
}

.bottom-nav-item:active {
    opacity: 0.7;
}

/* Center FAB Button: Perfectly Centered */
.bottom-nav-fab-wrapper {
    flex: 0 0 58px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 100% !important;
    position: relative !important;
    z-index: 108 !important;
    margin-top: 0 !important;
}

.fab-button {
    width: 50px !important;
    height: 50px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.45) !important;
    border: 3.5px solid var(--surface) !important;
    margin-top: -20px !important;
    cursor: pointer !important;
    transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s ease !important;
    text-decoration: none !important;
    -webkit-tap-highlight-color: transparent !important;
    z-index: 108 !important;
}

[data-theme="dark"] .fab-button {
    border-color: var(--surface) !important;
    box-shadow: 0 4px 16px rgba(129, 140, 248, 0.5) !important;
}

.fab-button svg {
    width: 24px !important;
    height: 24px !important;
    color: #ffffff !important;
    transition: transform 0.25s ease !important;
}

.fab-button:active {
    transform: scale(0.92) !important;
}

/* ============================================================
   SEAMLESS BOTTOM INTEGRATION WITH FAB VISIBLE
   ============================================================ */

/* Backdrop overlay */
.fab-overlay {
    position: fixed !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.5) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    z-index: 104 !important;
    display: none;
    opacity: 0;
    transition: opacity 0.25s ease;
}

.fab-overlay.active {
    display: block !important;
    opacity: 1 !important;
}

/* Bottom Sheet Menu: Extends to bottom: 0 behind the FAB button */
.fab-dropup {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-width: 480px !important;
    margin: 0 auto !important;
    background: var(--surface) !important;
    border: 1px solid var(--border) !important;
    border-bottom: none !important;
    border-radius: 24px 24px 0 0 !important;
    box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.25) !important;
    padding: 0.85rem 1.25rem calc(72px + env(safe-area-inset-bottom, 0px)) !important;
    display: none;
    z-index: 105 !important;
    transform: translateY(100%);
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
    max-height: 85vh !important;
    overflow-y: auto !important;
}

.fab-dropup.active {
    display: block !important;
    transform: translateY(0) !important;
    animation: slideUpSheet 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

@keyframes slideUpSheet {
    from {
        opacity: 0.5;
        transform: translateY(100%);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Override legacy CSS from app.css */
.fab-dropup::before { display: none !important; }
.fab-dropup-item::after { display: none !important; }

/* When menu is OPEN: Bottom Nav seamlessly integrates */
body.fab-menu-open .bottom-nav {
    background: transparent !important;
    border-top-color: transparent !important;
    box-shadow: none !important;
    pointer-events: none !important;
}
body.fab-menu-open .bottom-nav-item {
    opacity: 0.25 !important;
    pointer-events: none !important;
}
body.fab-menu-open .bottom-nav-fab-wrapper {
    pointer-events: auto !important;
}
body.fab-menu-open .fab-button {
    transform: rotate(45deg) scale(1.05) !important;
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.65) !important;
    border-color: var(--surface) !important;
}

/* Drag Handle & Header */
.sheet-drag-handle {
    width: 38px;
    height: 4px;
    background: var(--border);
    border-radius: 99px;
    margin: 0 auto 0.75rem;
}

.sheet-menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
}

.sheet-menu-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
}

.sheet-menu-subtitle {
    font-size: 0.72rem;
    color: var(--text-muted);
}

/* Menu List Items - Crisp, Colorful, Sharp */
.menu-nav-list {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.menu-nav-item {
    display: flex !important;
    align-items: center !important;
    gap: 0.875rem !important;
    padding: 0.625rem 0.75rem !important;
    color: var(--text-primary) !important;
    text-decoration: none !important;
    border-radius: 12px !important;
    transition: background 0.15s ease, transform 0.15s ease !important;
    background: transparent !important;
    border: none !important;
}

.menu-nav-item:hover,
.menu-nav-item:active {
    background: var(--bg-secondary) !important;
    transform: translateX(3px);
}

/* Distinct Colorful Icon Boxes */
.menu-icon-box {
    width: 40px !important;
    height: 40px !important;
    border-radius: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
}

.menu-icon-box svg {
    width: 22px !important;
    height: 22px !important;
    background: transparent !important;
    padding: 0 !important;
    border-radius: 0 !important;
    stroke-width: 2 !important;
}

/* Top Performa: Gold / Amber */
.menu-icon-box.performa {
    background: #FEF3C7 !important;
    color: #D97706 !important;
}
[data-theme="dark"] .menu-icon-box.performa {
    background: rgba(245, 158, 11, 0.2) !important;
    color: #FBBF24 !important;
}

/* Skema Gaji: Indigo */
.menu-icon-box.skema {
    background: #E0E7FF !important;
    color: #4F46E5 !important;
}
[data-theme="dark"] .menu-icon-box.skema {
    background: rgba(99, 102, 241, 0.2) !important;
    color: #818CF8 !important;
}

/* Skema Bonus: Purple */
.menu-icon-box.bonus {
    background: #F3E8FF !important;
    color: #9333EA !important;
}
[data-theme="dark"] .menu-icon-box.bonus {
    background: rgba(168, 85, 247, 0.2) !important;
    color: #C084FC !important;
}

/* Gaji: Emerald Green */
.menu-icon-box.gaji {
    background: #D1FAE5 !important;
    color: #059669 !important;
}
[data-theme="dark"] .menu-icon-box.gaji {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #34D399 !important;
}

/* Report TikTok: Rose / Red */
.menu-icon-box.tiktok {
    background: #FFE4E6 !important;
    color: #E11D48 !important;
}
[data-theme="dark"] .menu-icon-box.tiktok {
    background: rgba(244, 63, 94, 0.2) !important;
    color: #FB7185 !important;
}

/* Typography in menu item */
.menu-text-box {
    display: flex !important;
    flex-direction: column !important;
    gap: 1px !important;
    flex: 1 !important;
    min-width: 0 !important;
}

.menu-title {
    font-size: 0.92rem !important;
    font-weight: 600 !important;
    color: var(--text-primary) !important;
    line-height: 1.2 !important;
}

.menu-desc {
    font-size: 0.74rem !important;
    color: var(--text-muted) !important;
    line-height: 1.1 !important;
}

/* Clean Chevron Right */
.menu-chevron {
    width: 18px !important;
    height: 18px !important;
    color: var(--text-muted) !important;
    margin-left: auto !important;
    flex-shrink: 0 !important;
    background: transparent !important;
    padding: 0 !important;
    border-radius: 0 !important;
}

/* Remove fake home indicator bar */
.home-indicator {
    display: none !important;
}
</style>

<nav class="bottom-nav">
    <div class="bottom-nav-container">
        @if(auth()->user()->isAdmin())
            <!-- Admin Bottom Navigation -->
            <a href="{{ route('admin.dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="{{ request()->routeIs('admin.dashboard') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Home</span>
            </a>
            <a href="{{ route('admin.attendances.index') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Absensi</span>
            </a>

            <!-- FAB Center Button (Always Prominently Visible at Bottom) -->
            <div class="bottom-nav-fab-wrapper">
                <div class="fab-button" id="admin-fab-trigger" onclick="toggleAdminMenu()" title="Buka/Tutup Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
            </div>

            <a href="{{ route('admin.users.index') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>User</span>
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Setting</span>
            </a>
        @else
            <!-- User Bottom Navigation -->
            <a href="{{ route('user.dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="{{ request()->routeIs('user.dashboard') ? 'currentColor' : 'none' }}" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Home</span>
            </a>

            <!-- FAB Center Button -->
            <div class="bottom-nav-fab-wrapper">
                <a href="{{ route('user.attendances.create') }}"
                    class="fab-button {{ request()->routeIs('user.attendances.create') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </a>
            </div>

            <a href="{{ route('user.attendances.index') }}"
                class="bottom-nav-item {{ request()->routeIs('user.attendances.index') || request()->routeIs('user.attendances.edit') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('user.settings.index') }}"
                class="bottom-nav-item {{ request()->routeIs('user.settings.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Profil</span>
            </a>
        @endif
    </div>
</nav>

<!-- Admin Bottom Sheet Menu: Extends to bottom: 0 with FAB centered above -->
@if(auth()->user()->isAdmin())
    <div class="fab-dropup" id="admin-fab-menu">
        <div class="sheet-drag-handle"></div>

        <div class="sheet-menu-header">
            <div>
                <div class="sheet-menu-title">Menu Pintasan</div>
                <div class="sheet-menu-subtitle">Pilih menu cepat untuk navigasi</div>
            </div>
        </div>

        <div class="menu-nav-list">
            <a href="{{ route('admin.performance.index') }}" class="menu-nav-item">
                <div class="menu-icon-box performa">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <div class="menu-text-box">
                    <span class="menu-title">Top Performa</span>
                    <span class="menu-desc">Peringkat & KPI Host Live</span>
                </div>
                <svg class="menu-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('admin.salary-schemes.index') }}" class="menu-nav-item">
                <div class="menu-icon-box skema">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="menu-text-box">
                    <span class="menu-title">Skema Gaji</span>
                    <span class="menu-desc">Aturan tarif per jam & fee konten</span>
                </div>
                <svg class="menu-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('admin.bonus-tiers.index') }}" class="menu-nav-item">
                <div class="menu-icon-box bonus">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                </div>
                <div class="menu-text-box">
                    <span class="menu-title">Skema Bonus</span>
                    <span class="menu-desc">Target jam & bonus penjualan</span>
                </div>
                <svg class="menu-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('admin.salary-records.index') }}" class="menu-nav-item">
                <div class="menu-icon-box gaji">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="menu-text-box">
                    <span class="menu-title">Gaji</span>
                    <span class="menu-desc">Rekap data & unduh slip gaji</span>
                </div>
                <svg class="menu-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('admin.tiktok-reports.index') }}" class="menu-nav-item">
                <div class="menu-icon-box tiktok">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="menu-text-box">
                    <span class="menu-title">Report TikTok</span>
                    <span class="menu-desc">Laporan performa TikTok Live</span>
                </div>
                <svg class="menu-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
@endif

<!-- Overlay for FAB menu -->
<div class="fab-overlay" id="fab-overlay" onclick="closeAdminMenu()"></div>

<script>
    function toggleAdminMenu() {
        const menu = document.getElementById('admin-fab-menu');
        const overlay = document.getElementById('fab-overlay');

        if (!menu) return;

        if (menu.classList.contains('active')) {
            closeAdminMenu();
        } else {
            menu.classList.add('active');
            if (overlay) overlay.classList.add('active');
            document.body.classList.add('fab-menu-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAdminMenu() {
        const menu = document.getElementById('admin-fab-menu');
        const overlay = document.getElementById('fab-overlay');

        if (menu) menu.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('fab-menu-open');
        document.body.style.overflow = '';
    }

    // Close on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAdminMenu();
        }
    });
</script>
