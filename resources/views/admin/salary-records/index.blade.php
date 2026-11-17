@extends('layouts.app')

@section('title', 'Gaji')

@push('styles')
<style>
    /* ==========================================================
       Salary Records - Modern Compact Mobile & Desktop Layout
       ========================================================== */

    /* Unified Desktop Control Bar (Filter + Actions in 1 line) */
    .salary-control-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 10px);
        padding: 0.5rem 0.875rem;
        margin-bottom: 0.625rem;
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
    }
    .salary-filter-form {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: nowrap;
    }
    .salary-filter-form .filter-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
        margin-right: 0.25rem;
    }
    .salary-filter-form .filter-select {
        width: auto !important;
        min-width: 105px;
        max-width: 145px;
        height: 32px !important;
        padding: 0.2rem 1.75rem 0.2rem 0.55rem !important;
        font-size: 0.78rem !important;
        border-radius: 6px !important;
        color: var(--text-primary);
        background-color: var(--surface);
        border: 1px solid var(--border);
    }
    .salary-filter-form .filter-select:focus {
        border-color: var(--primary);
        outline: none;
    }
    .salary-filter-form .btn-reset {
        height: 32px;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        white-space: nowrap;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        text-decoration: none;
    }
    .salary-filter-form .btn-reset:hover {
        background: var(--border);
        color: var(--text-primary);
    }

    /* Action Buttons in Control Bar */
    .salary-actions-bar {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: nowrap;
    }
    .salary-actions-bar .btn {
        height: 32px;
        padding: 0.25rem 0.65rem;
        font-size: 0.78rem;
        font-weight: 500;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }
    .salary-actions-bar .btn svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    /* Compact Stats Grid */
    .salary-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.625rem;
        margin-bottom: 0.625rem;
    }
    .salary-stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 10px);
        padding: 0.45rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        box-shadow: var(--shadow-sm);
        min-height: 48px;
    }
    .salary-stat-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .salary-stat-icon svg {
        width: 16px;
        height: 16px;
    }
    .salary-stat-card.primary { border-left: 3px solid var(--primary); }
    .salary-stat-card.primary .salary-stat-icon {
        background: rgba(99, 102, 241, 0.12);
        color: var(--primary);
    }
    .salary-stat-card.warning { border-left: 3px solid var(--warning); }
    .salary-stat-card.warning .salary-stat-icon {
        background: rgba(245, 158, 11, 0.12);
        color: var(--warning);
    }
    .salary-stat-card.success { border-left: 3px solid var(--success); }
    .salary-stat-card.success .salary-stat-icon {
        background: rgba(16, 185, 129, 0.12);
        color: var(--success);
    }
    .salary-stat-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        font-weight: 500;
        line-height: 1.1;
    }
    .salary-stat-value {
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.2;
        margin-top: 1px;
    }
    .salary-stat-value.primary { color: var(--primary); }
    .salary-stat-value.warning { color: var(--warning); }
    .salary-stat-value.success { color: var(--success); }

    /* Compact Table Card */
    .salary-table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 10px);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .salary-table {
        width: 100%;
        border-collapse: collapse;
    }
    .salary-table th {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--text-muted);
        padding: 0.45rem 0.6rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg-secondary);
        white-space: nowrap;
    }
    .salary-table td {
        padding: 0.42rem 0.6rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        font-size: 0.8125rem;
    }
    .salary-table tbody tr:hover {
        background: rgba(99, 102, 241, 0.025);
    }

    /* Column Widths on Desktop */
    .salary-table .col-checkbox {
        width: 34px;
        text-align: center;
        padding-left: 0.6rem;
    }
    .salary-table .col-user {
        min-width: 160px;
    }
    .salary-table .col-periode {
        width: 90px;
        text-align: center;
    }
    .salary-table .col-detail {
        min-width: 250px;
    }
    .salary-table .col-gaji {
        width: 120px;
        text-align: right;
    }
    .salary-table .col-status {
        width: 95px;
        text-align: center;
    }
    .salary-table .col-aksi {
        width: 125px;
        text-align: center;
    }

    /* User Cell Desktop */
    .user-compact-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 0;
    }
    .user-avatar-mini {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(99, 102, 241, 0.12);
        color: var(--primary);
        font-weight: 700;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .user-name-mini {
        font-weight: 600;
        font-size: 0.8125rem;
        color: var(--text-primary);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .user-meta-mini {
        font-size: 0.68rem;
        color: var(--text-muted);
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Periode Cell */
    .periode-compact-cell {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }
    .periode-month {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
    }
    .term-pill {
        font-size: 0.62rem;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 3px;
        line-height: 1.2;
    }
    .term-pill.t1 { background: #e0e7ff; color: #4338ca; }
    .term-pill.t2 { background: #fef3c7; color: #b45309; }
    [data-theme="dark"] .term-pill.t1 { background: #312e81; color: #c7d2fe; }
    [data-theme="dark"] .term-pill.t2 { background: #78350f; color: #fde68a; }

    /* Detail Cell Desktop: Micro Chips */
    .desktop-detail-cell {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .detail-chips-row {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .detail-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.72rem;
        background: var(--bg-secondary);
        padding: 0.12rem 0.4rem;
        border-radius: 4px;
        color: var(--text-secondary);
        white-space: nowrap;
    }
    .detail-chip svg {
        width: 12px;
        height: 12px;
        flex-shrink: 0;
    }
    .detail-chip.duration svg { color: var(--primary); }
    .detail-chip.content svg { color: var(--warning); }
    .detail-chip.sales svg { color: var(--danger); }
    .detail-chip.duration { color: var(--text-primary); font-weight: 600; }

    .target-badge {
        font-size: 0.68rem;
        padding: 0.1rem 0.4rem;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        width: fit-content;
    }
    .target-badge.achieved {
        background: rgba(16, 185, 129, 0.12);
        color: var(--success);
        font-weight: 600;
    }
    .target-badge.pending {
        background: var(--bg-secondary);
        color: var(--text-muted);
        font-size: 0.68rem;
    }

    /* Gaji Cell Desktop */
    .gaji-compact-cell {
        font-size: 0.825rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        text-align: right;
    }

    /* Desktop Action Buttons: Compact Icon Buttons */
    .desktop-action-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    .btn-action-icon {
        width: 27px;
        height: 27px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .btn-action-icon:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    .btn-action-icon.view:hover {
        color: var(--primary);
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.08);
    }
    .btn-action-icon.edit:hover {
        color: var(--accent);
        border-color: var(--accent);
        background: rgba(245, 158, 11, 0.08);
    }
    .btn-action-icon.pay:hover {
        color: var(--success);
        border-color: var(--success);
        background: rgba(16, 185, 129, 0.08);
    }
    .btn-action-icon.delete:hover {
        color: var(--danger);
        border-color: var(--danger);
        background: rgba(239, 68, 68, 0.08);
    }
    .btn-action-icon svg {
        width: 13px;
        height: 13px;
    }

    /* Hide mobile actions on desktop */
    .mobile-action-group {
        display: none;
    }
    .mobile-status-indicator {
        display: none;
    }

    /* Native Action Bottom Sheet */
    .salary-sheet-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(4px);
        z-index: 1050;
        display: none;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .salary-sheet-overlay.active {
        display: flex;
        opacity: 1;
    }
    .salary-sheet {
        background: var(--surface);
        border: 1px solid var(--border);
        border-bottom: none;
        width: 100%;
        max-width: 480px;
        border-radius: 20px 20px 0 0;
        padding: 1rem 1.25rem calc(1.25rem + env(safe-area-inset-bottom));
        box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.25);
        transform: translateY(100%);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        max-height: 85vh;
        overflow-y: auto;
    }
    .salary-sheet-overlay.active .salary-sheet {
        transform: translateY(0);
    }
    .sheet-drag-handle {
        width: 36px;
        height: 4px;
        background: var(--border);
        border-radius: 2px;
        margin: 0 auto 0.875rem;
    }
    .sheet-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.875rem;
    }
    .sheet-user {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        min-width: 0;
    }
    .sheet-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sheet-subtitle {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .sheet-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-muted);
        cursor: pointer;
        line-height: 1;
        padding: 0.25rem;
    }
    .sheet-amount-banner {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.25);
        border-radius: 10px;
        padding: 0.6rem 0.85rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .sheet-amount-label {
        font-size: 0.78rem;
        color: var(--text-muted);
        font-weight: 500;
    }
    .sheet-amount-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--success);
    }
    .sheet-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.4rem;
        margin-bottom: 0.875rem;
    }
    .sheet-stat-card {
        background: var(--bg-secondary);
        border-radius: 8px;
        padding: 0.45rem 0.25rem;
        text-align: center;
    }
    .sheet-stat-label {
        font-size: 0.62rem;
        color: var(--text-muted);
    }
    .sheet-stat-value {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-top: 2px;
    }
    .sheet-actions {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }
    .sheet-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        border-radius: 9px;
    }

    /* ==========================================================
       MOBILE RESPONSIVE OVERRIDES (<= 768px)
       ========================================================== */
    @media (max-width: 768px) {
        .salary-control-bar {
            flex-direction: column;
            align-items: stretch;
            padding: 0.45rem 0.5rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            gap: 0.4rem;
        }
        .salary-filter-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.35rem;
            width: 100%;
        }
        .salary-filter-form .filter-label {
            display: none;
        }
        .salary-filter-form .filter-select {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            height: 31px !important;
            padding: 0.15rem 0.35rem !important;
            font-size: 0.72rem !important;
            border-radius: 6px !important;
        }
        .salary-filter-form .btn-reset {
            grid-column: 1 / -1;
            height: 26px;
            padding: 0.15rem 0.5rem;
            font-size: 0.7rem;
            justify-content: center;
            border-radius: 6px;
            width: 100%;
        }

        /* Stats Grid Mobile (3 Equal Columns, ~48px height) */
        .salary-stats-grid {
            gap: 0.35rem;
            margin-bottom: 0.5rem;
        }
        .salary-stat-card {
            padding: 0.45rem 0.35rem;
            border-radius: 8px;
            flex-direction: column;
            align-items: flex-start;
            gap: 1px;
            box-shadow: none;
            min-height: auto;
        }
        .salary-stat-icon {
            display: none;
        }
        .salary-stat-label {
            font-size: 0.62rem;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .salary-stat-value {
            font-size: 0.75rem;
            word-break: break-all;
            line-height: 1.15;
            margin-top: 1px;
        }

        /* Actions Bar Mobile */
        .salary-actions-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem;
            margin-bottom: 0;
            width: 100%;
        }
        .salary-actions-bar .btn {
            width: 100%;
            justify-content: center;
            padding: 0.38rem 0.5rem;
            font-size: 0.75rem;
            height: 32px;
            border-radius: 8px;
        }
        .salary-actions-bar .btn svg {
            width: 13px;
            height: 13px;
        }
        .salary-actions-bar #downloadSelectedBtn {
            grid-column: 1 / -1;
            order: -1;
        }

        /* Table Mobile Optimization: 100% Fit */
        .salary-table-card {
            border-radius: 10px;
        }
        .salary-table-card .table-container {
            overflow-x: hidden !important;
            width: 100% !important;
            margin-bottom: 0;
        }
        .salary-table {
            width: 100% !important;
            min-width: 100% !important;
            table-layout: fixed !important;
            font-size: 0.75rem;
        }
        .salary-table th {
            padding: 0.45rem 0.2rem !important;
            font-size: 0.65rem !important;
            background: var(--bg-secondary);
        }
        .salary-table td {
            padding: 0.45rem 0.2rem !important;
            border-bottom: 1px solid var(--border);
        }

        /* Column Specific Mobile Widths */
        .salary-table .col-checkbox {
            width: 26px !important;
            min-width: 26px !important;
            max-width: 26px !important;
            text-align: center;
            padding-left: 0.3rem !important;
        }
        .salary-table .col-user {
            width: auto !important;
            min-width: 0 !important;
            padding-left: 0.3rem !important;
        }
        .salary-table .col-periode {
            width: 58px !important;
            min-width: 58px !important;
            max-width: 58px !important;
            text-align: center;
        }
        .salary-table .col-gaji {
            width: 76px !important;
            min-width: 76px !important;
            max-width: 76px !important;
            text-align: right;
        }
        .salary-table .col-aksi {
            width: 48px !important;
            min-width: 48px !important;
            max-width: 48px !important;
            text-align: center;
            padding-right: 0.3rem !important;
        }

        /* Hide detail & status columns on mobile */
        .hide-on-mobile {
            display: none !important;
        }

        /* Mobile Action Buttons: Eye + 3-Dots */
        .desktop-action-group {
            display: none !important;
        }
        .mobile-action-group {
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }
        .btn-icon-mini {
            width: 22px;
            height: 22px;
            padding: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            flex-shrink: 0;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-secondary);
        }
        .btn-icon-mini:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
        .btn-icon-mini.primary-icon {
            color: var(--primary);
        }
        .btn-icon-mini svg {
            width: 12px;
            height: 12px;
        }

        /* Status Dot on Mobile */
        .mobile-status-indicator {
            display: inline-flex !important;
            align-items: center;
            gap: 3px;
        }
        .mobile-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .mobile-status-dot.paid { background: var(--success); }
        .mobile-status-dot.pending { background: var(--warning); }
        .mobile-status-text.paid { color: var(--success); font-weight: 600; font-size: 0.68rem; }
        .mobile-status-text.pending { color: var(--warning); font-weight: 500; font-size: 0.68rem; }
    }
</style>
@endpush

@section('content')
    <!-- Unified Control Bar: Filter (Left) + Actions (Right) -->
    <div class="salary-control-bar">
        <!-- Filter Form -->
        <form action="{{ route('admin.salary-records.index') }}" method="GET" class="salary-filter-form">
            <span class="filter-label">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter:
            </span>
            
            <select name="year" class="form-control form-select filter-select" onchange="this.form.submit()">
                <option value="">Tahun</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>

            <select name="month" class="form-control form-select filter-select" onchange="this.form.submit()">
                <option value="">Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <select name="term" class="form-control form-select filter-select" onchange="this.form.submit()">
                <option value="">Termin</option>
                <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>T1</option>
                <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>T2</option>
            </select>

            <select name="status" class="form-control form-select filter-select" onchange="this.form.submit()">
                <option value="">Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
            </select>

            @if(request()->hasAny(['year', 'month', 'term', 'status']))
                <a href="{{ route('admin.salary-records.index') }}" class="btn btn-secondary btn-sm btn-reset" title="Reset Filter">
                    âœ• Reset
                </a>
            @endif
        </form>

        <!-- Action Buttons -->
        <div class="salary-actions-bar">
            <button type="button" class="btn btn-success" id="downloadSelectedBtn" style="display: none;"
                onclick="downloadSelected()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download (<span id="selectedCount">0</span>)
            </button>
            <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('generateModal').classList.add('active')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Generate Gaji
            </button>
            <a href="{{ route('admin.salary-records.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Data Gaji
            </a>
        </div>
    </div>

    <!-- Compact Total Stats Grid -->
    <div class="salary-stats-grid">
        <div class="salary-stat-card primary">
            <div class="salary-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="salary-stat-label">Total Gaji</div>
                <div class="salary-stat-value primary">
                    Rp {{ number_format($totals['total_amount'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="salary-stat-card warning">
            <div class="salary-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="salary-stat-label">Pending ({{ $totals['count_pending'] }})</div>
                <div class="salary-stat-value warning">
                    Rp {{ number_format($totals['total_pending'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="salary-stat-card success">
            <div class="salary-stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="salary-stat-label">Dibayar ({{ $totals['count_paid'] }})</div>
                <div class="salary-stat-value success">
                    Rp {{ number_format($totals['total_paid'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal-overlay" id="generateModal">
        <div class="modal" style="max-width: 500px;">
            <div class="modal-header">
                <h3 class="modal-title">Generate Gaji</h3>
                <button type="button" class="modal-close"
                    onclick="document.getElementById('generateModal').classList.remove('active')">&times;</button>
            </div>
            <form action="{{ route('admin.salary-records.generate-all') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-4">Generate gaji otomatis berdasarkan data absensi tervalidasi.</p>

                    <div class="form-group">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-control form-select">
                            <option value="">Semua Tahun</option>
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Bulan</label>
                        <select name="month" class="form-control form-select">
                            <option value="">Semua Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Termin</label>
                        <select name="term" class="form-control form-select">
                            <option value="">Semua Termin</option>
                            <option value="1">T1 (1-15)</option>
                            <option value="2">T2 (16-Akhir)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-control form-select">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('generateModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="salary-table-card">
        <div class="table-container">
            <table class="salary-table table">
                <thead>
                    <tr>
                        <th class="col-checkbox">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th class="col-user">User</th>
                        <th class="col-periode">Periode</th>
                        <th class="col-detail hide-on-mobile">Detail</th>
                        <th class="col-gaji">Gaji</th>
                        <th class="col-status hide-on-mobile">Status</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        @php
                            $userName = $record->user->name ?? 'User';
                            $userTask = $record->user->task ?? 'Host Live';
                            $isPaid = $record->status === 'paid';
                            $periodShort = \Carbon\Carbon::createFromDate($record->year, $record->month, 1)->translatedFormat('M y');
                            
                            // Check bonus for detail view
                            $user = $record->user;
                            $scheme = $user ? $user->salaryScheme : null;
                            $bonusAchieved = false;

                            if ($scheme) {
                                $monthStart = \Carbon\Carbon::createFromDate($record->year, $record->month, 1)->startOfDay();
                                $monthEnd = $monthStart->copy()->endOfMonth();

                                $monthlyMinutes = \App\Models\Attendance::where('user_id', $user->id)
                                    ->whereIn('status', ['pending', 'validated'])
                                    ->whereBetween('attendance_date', [$monthStart, $monthEnd])
                                    ->sum('live_duration_minutes');

                                $monthlyHours = $monthlyMinutes / 60;
                                $monthlyTarget = $scheme->calculateMonthlyTarget($record->year, $record->month);
                                $bonusAchieved = $monthlyHours >= $monthlyTarget;
                            }
                        @endphp
                        <tr>
                            <td class="col-checkbox">
                                <input type="checkbox" class="record-checkbox" value="{{ $record->id }}"
                                    onchange="updateSelectedCount()">
                            </td>
                            <td class="col-user">
                                <div class="user-compact-cell">
                                    <div class="user-avatar-mini">{{ substr($userName, 0, 1) }}</div>
                                    <div class="user-info-mini">
                                        <div class="user-name-mini" title="{{ $userName }}">{{ $userName }}</div>
                                        <div class="user-meta-mini">
                                            <span>{{ $userTask }}</span>
                                            <span class="mobile-status-indicator">
                                                <span>â€¢</span>
                                                <span class="mobile-status-dot {{ $isPaid ? 'paid' : 'pending' }}"></span>
                                                <span class="mobile-status-text {{ $isPaid ? 'paid' : 'pending' }}">
                                                    {{ $isPaid ? 'Dibayar' : 'Pending' }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="col-periode">
                                <div class="periode-compact-cell">
                                    <span class="periode-month">{{ $periodShort }}</span>
                                    <span class="term-pill {{ $record->term == '1' ? 't1' : 't2' }}">T{{ $record->term }}</span>
                                </div>
                            </td>
                            <td class="col-detail hide-on-mobile">
                                <div class="desktop-detail-cell">
                                    <div class="detail-chips-row">
                                        <span class="detail-chip duration" title="Durasi Live Streaming">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <strong>{{ number_format($record->total_hours, 1) }}</strong>j
                                        </span>
                                        <span class="detail-chip content" title="Total Konten">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            {{ $record->total_content_live }} live â€¢ {{ $record->total_content_edit }} edit
                                        </span>
                                        <span class="detail-chip sales" title="Total Penjualan">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            {{ $record->total_sales }} sales
                                        </span>
                                    </div>
                                    <div style="margin-top: 1px;">
                                        @if($bonusAchieved)
                                            <span class="target-badge achieved">âœ“ Target tercapai</span>
                                        @else
                                            <span class="target-badge pending">Target belum tercapai</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="col-gaji">
                                <div class="gaji-compact-cell">
                                    {{ $record->formatted_amount }}
                                </div>
                            </td>
                            <td class="col-status hide-on-mobile" style="text-align: center;">
                                @if($isPaid)
                                    <span class="badge badge-success badge-sm" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">Dibayar</span>
                                @else
                                    <span class="badge badge-warning badge-sm" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">Pending</span>
                                @endif
                            </td>
                            <td class="col-aksi">
                                <!-- Desktop Action Buttons: Compact Micro Icons -->
                                <div class="desktop-action-group">
                                    <a href="{{ route('admin.salary-records.show', $record) }}"
                                        class="btn-action-icon view" title="Lihat Slip & Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.salary-records.edit', $record) }}"
                                        class="btn-action-icon edit" title="Edit Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if(!$isPaid)
                                        <form action="{{ route('admin.salary-records.mark-paid', $record) }}" method="POST"
                                            style="display:inline; margin: 0;">
                                            @csrf
                                            <button type="submit" class="btn-action-icon pay" title="Tandai Sudah Dibayar">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.salary-records.destroy', $record) }}" method="POST"
                                        style="display:inline; margin: 0;" onsubmit="return confirm('Hapus rekap gaji ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-icon delete" title="Hapus Data">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Mobile Action Buttons: Eye + 3-Dots -->
                                <div class="mobile-action-group">
                                    <a href="{{ route('admin.salary-records.show', $record) }}"
                                        class="btn-icon-mini primary-icon" title="Lihat Slip">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <button type="button" class="btn-icon-mini"
                                        onclick="openActionSheet({
                                            id: {{ $record->id }},
                                            name: '{{ addslashes($userName) }}',
                                            period: '{{ addslashes($record->period_label) }}',
                                            term: '{{ $record->term }}',
                                            status: '{{ $record->status }}',
                                            amount: '{{ $record->formatted_amount }}',
                                            hours: '{{ number_format($record->total_hours, 1) }}',
                                            live: '{{ $record->total_content_live }}',
                                            edit: '{{ $record->total_content_edit }}',
                                            sales: '{{ $record->total_sales }}',
                                            showUrl: '{{ route('admin.salary-records.show', $record) }}',
                                            editUrl: '{{ route('admin.salary-records.edit', $record) }}',
                                            paidUrl: '{{ route('admin.salary-records.mark-paid', $record) }}',
                                            deleteUrl: '{{ route('admin.salary-records.destroy', $record) }}'
                                        })"
                                        title="Opsi Lainnya">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 2.5rem 1rem;">
                                Tidak ada data rekap gaji. <a href="{{ route('admin.salary-records.create') }}">Tambah sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="card-footer" style="padding: 0.6rem 0.875rem;">
                {{ $records->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Mobile Action Bottom Sheet -->
    <div class="salary-sheet-overlay" id="actionSheetBackdrop" onclick="closeActionSheet()">
        <div class="salary-sheet" onclick="event.stopPropagation()">
            <div class="sheet-drag-handle"></div>
            
            <div class="sheet-header">
                <div class="sheet-user">
                    <div class="user-avatar-mini" id="sheetAvatar" style="width: 28px; height: 28px; font-size: 0.8rem;">U</div>
                    <div>
                        <h3 id="sheetUserName" class="sheet-title">User Name</h3>
                        <div class="sheet-subtitle">
                            <span id="sheetPeriod">November 2026</span>
                            <span class="badge" id="sheetStatusBadge">Pending</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="sheet-close" onclick="closeActionSheet()">&times;</button>
            </div>

            <div class="sheet-amount-banner">
                <div class="sheet-amount-label">Total Gaji</div>
                <div class="sheet-amount-value" id="sheetAmount">Rp 0</div>
            </div>

            <div class="sheet-stats-grid">
                <div class="sheet-stat-card">
                    <div class="sheet-stat-label">Durasi</div>
                    <div class="sheet-stat-value" id="sheetHours">0 jam</div>
                </div>
                <div class="sheet-stat-card">
                    <div class="sheet-stat-label">Live</div>
                    <div class="sheet-stat-value" id="sheetLive">0 live</div>
                </div>
                <div class="sheet-stat-card">
                    <div class="sheet-stat-label">Edit</div>
                    <div class="sheet-stat-value" id="sheetEdit">0 edit</div>
                </div>
                <div class="sheet-stat-card">
                    <div class="sheet-stat-label">Penjualan</div>
                    <div class="sheet-stat-value" id="sheetSales">0 sales</div>
                </div>
            </div>

            <div class="sheet-actions">
                <a href="#" id="sheetDetailBtn" class="btn btn-secondary sheet-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Lihat Detail & Unduh Slip
                </a>
                
                <a href="#" id="sheetEditBtn" class="btn btn-secondary sheet-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Rekap Gaji
                </a>

                <form id="sheetPaidForm" action="" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-success sheet-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tandai Sudah Dibayar
                    </button>
                </form>

                <form id="sheetDeleteForm" action="" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus rekap gaji ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger sheet-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Rekap Gaji
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.record-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.record-checkbox:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('downloadSelectedBtn').style.display = count > 0 ? 'inline-flex' : 'none';
        }

        function downloadSelected() {
            const checkboxes = document.querySelectorAll('.record-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                alert('Pilih minimal satu rekap gaji');
                return;
            }

            // Open each slip in new tab with auto-download enabled
            ids.forEach((id, index) => {
                setTimeout(() => {
                    const url = "{{ route('admin.salary-records.slip', ['id' => '__ID__']) }}".replace('__ID__', id);
                    window.open(url + '?auto=1', '_blank');
                }, index * 1500);
            });
        }

        // Mobile Action Bottom Sheet Functions
        function openActionSheet(data) {
            document.getElementById('sheetAvatar').textContent = data.name.charAt(0);
            document.getElementById('sheetUserName').textContent = data.name;
            document.getElementById('sheetPeriod').textContent = data.period + ' (T' + data.term + ')';

            const badge = document.getElementById('sheetStatusBadge');
            badge.textContent = data.status === 'paid' ? 'Dibayar' : 'Pending';
            badge.className = 'badge ' + (data.status === 'paid' ? 'badge-success' : 'badge-warning');

            document.getElementById('sheetAmount').textContent = data.amount;
            document.getElementById('sheetHours').textContent = data.hours + ' jam';
            document.getElementById('sheetLive').textContent = data.live + ' live';
            document.getElementById('sheetEdit').textContent = data.edit + ' edit';
            document.getElementById('sheetSales').textContent = data.sales + ' sales';

            document.getElementById('sheetDetailBtn').href = data.showUrl;
            document.getElementById('sheetEditBtn').href = data.editUrl;

            const paidForm = document.getElementById('sheetPaidForm');
            if (data.status === 'paid') {
                paidForm.style.display = 'none';
            } else {
                paidForm.style.display = 'block';
                paidForm.action = data.paidUrl;
            }

            const deleteForm = document.getElementById('sheetDeleteForm');
            deleteForm.action = data.deleteUrl;

            document.getElementById('actionSheetBackdrop').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeActionSheet() {
            document.getElementById('actionSheetBackdrop').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeActionSheet();
            }
        });
    </script>
@endpush
