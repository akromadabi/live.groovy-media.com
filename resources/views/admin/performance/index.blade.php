@extends('layouts.app')

@section('title', 'Top Performa Host')

@push('styles')
<style>
    /* Compact Top Performa Layout */
    .perf-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .perf-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.2;
    }

    .perf-subtitle {
        color: var(--text-secondary);
        font-size: 0.8rem;
        margin-top: 0.15rem;
    }

    /* Filter Card & Segmented Control */
    .perf-filter-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .perf-segment-nav {
        display: inline-flex;
        background: var(--bg-secondary);
        padding: 3px;
        border-radius: var(--radius);
        gap: 3px;
        overflow-x: auto;
        max-width: 100%;
    }

    .perf-segment-item {
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        border-radius: calc(var(--radius) - 2px);
        text-decoration: none;
        transition: all var(--transition);
        white-space: nowrap;
    }

    .perf-segment-item:hover {
        color: var(--text-primary);
    }

    .perf-segment-item.active {
        background: var(--surface);
        color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    /* Top 3 Compact Cards */
    .top3-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
        margin-bottom: 1rem;
    }

    .top3-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 0.875rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.6rem;
        box-shadow: var(--shadow-sm);
        transition: border-color var(--transition);
    }

    .top3-card.rank-1 {
        border-color: rgba(245, 158, 11, 0.45);
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.05) 0%, var(--surface) 100%);
    }

    .top3-card.rank-2 {
        border-color: rgba(148, 163, 184, 0.45);
    }

    .top3-card.rank-3 {
        border-color: rgba(217, 119, 6, 0.35);
    }

    .rank-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.725rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
    }

    .rank-pill.rank-1 { background: #FEF3C7; color: #B45309; }
    .rank-pill.rank-2 { background: #F1F5F9; color: #475569; }
    .rank-pill.rank-3 { background: #FFEDD5; color: #9A3412; }

    [data-theme="dark"] .rank-pill.rank-1 { background: rgba(245, 158, 11, 0.2); color: #FCD34D; }
    [data-theme="dark"] .rank-pill.rank-2 { background: rgba(148, 163, 184, 0.2); color: #E2E8F0; }
    [data-theme="dark"] .rank-pill.rank-3 { background: rgba(217, 119, 6, 0.2); color: #FDBA74; }

    .top3-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--text-secondary);
        padding-top: 0.45rem;
        border-top: 1px dashed var(--border);
    }

    /* Rank number badges in Table */
    .rank-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        font-size: 0.775rem;
        font-weight: 700;
        color: var(--text-secondary);
        background: var(--bg-secondary);
    }

    .rank-num.rank-1 { background: #FEF3C7; color: #B45309; }
    .rank-num.rank-2 { background: #F1F5F9; color: #475569; }
    .rank-num.rank-3 { background: #FFEDD5; color: #9A3412; }

    [data-theme="dark"] .rank-num.rank-1 { background: rgba(245, 158, 11, 0.2); color: #FCD34D; }
    [data-theme="dark"] .rank-num.rank-2 { background: rgba(148, 163, 184, 0.2); color: #E2E8F0; }
    [data-theme="dark"] .rank-num.rank-3 { background: rgba(217, 119, 6, 0.2); color: #FDBA74; }

    /* Score Progress Bar */
    .score-bar-wrapper {
        width: 100%;
        height: 5px;
        background: var(--bg-secondary);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 4px;
    }

    .score-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: var(--primary);
    }

    /* Tier Badges */
    .badge-diamond { background: var(--primary-light); color: var(--primary); }
    .badge-gold { background: #FEF3C7; color: #B45309; }
    .badge-silver { background: #F1F5F9; color: #475569; }
    .badge-bronze { background: #FFEDD5; color: #C2410C; }

    [data-theme="dark"] .badge-diamond { background: rgba(99, 102, 241, 0.2); color: #A5B4FC; }
    [data-theme="dark"] .badge-gold { background: rgba(245, 158, 11, 0.2); color: #FCD34D; }
    [data-theme="dark"] .badge-silver { background: rgba(148, 163, 184, 0.2); color: #CBD5E1; }
    [data-theme="dark"] .badge-bronze { background: rgba(217, 119, 6, 0.2); color: #FDBA74; }

    /* Modal Styling */
    .modal-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .modal-stat-box {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.6rem 0.75rem;
        text-align: center;
    }

    .modal-stat-val {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .modal-stat-lbl {
        font-size: 0.675rem;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 2px;
    }

    /* Print-Only Elements (Hidden on screen) */
    .print-only-block {
        display: none;
    }

    /* Default (Desktop) Layout Rules */
    .perf-mvp-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
    }

    .perf-toolbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .perf-filter-form {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
        margin: 0;
    }

    .perf-search-wrap {
        position: relative;
    }

    .perf-search-input {
        font-size: 0.8rem;
        padding: 0.35rem 0.6rem;
        min-width: 130px;
    }

    .perf-selects-row {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .perf-select {
        width: auto;
        font-size: 0.8rem;
        padding: 0.35rem 1.7rem 0.35rem 0.6rem;
    }

    .perf-select-period { min-width: 125px; }
    .perf-select-month  { min-width: 105px; }
    .perf-select-year   { min-width: 85px; }
    .perf-select-status { min-width: 115px; }

    .perf-btn-reset {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        height: 32px;
        display: inline-flex;
        align-items: center;
        border-radius: 6px;
    }

    /* Mobile Host Card Items */
    .perf-mobile-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        box-shadow: var(--shadow-sm);
        transition: border-color var(--transition);
    }

    .perf-mobile-card:hover {
        border-color: var(--primary);
    }

    .perf-mobile-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .perf-mobile-stats-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0.35rem;
        background: var(--bg-secondary);
        padding: 0.4rem 0.5rem;
        border-radius: 6px;
    }

    .perf-stat-col {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .perf-stat-lbl {
        font-size: 0.62rem;
        color: var(--text-muted);
        line-height: 1.1;
    }

    .perf-stat-val {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .perf-stat-sub {
        font-size: 0.62rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Responsive Overrides (<= 768px) */
    @media (max-width: 768px) {
        .perf-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .perf-header-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem;
            width: 100%;
        }

        .perf-header-actions .btn {
            width: 100%;
            justify-content: center;
            height: 34px;
            font-size: 0.78rem;
        }

        /* 4 MVP Cards (2 columns x 2 rows on mobile) */
        .perf-mvp-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.35rem !important;
            margin-bottom: 0.65rem !important;
        }

        .perf-mvp-grid .stat-card-compact {
            padding: 0.35rem 0.5rem !important;
            min-height: 44px !important;
            gap: 0.4rem !important;
        }

        .perf-mvp-grid .stat-icon-sm {
            width: 30px !important;
            height: 30px !important;
            flex-shrink: 0;
        }

        .perf-mvp-grid .stat-label {
            font-size: 0.62rem !important;
            line-height: 1.1;
        }

        .perf-mvp-grid .stat-number {
            font-size: 0.82rem !important;
            line-height: 1.15;
            margin-top: 1px;
        }

        /* Filter Toolbar Card on Mobile */
        .perf-filter-card {
            padding: 0.55rem 0.65rem !important;
            margin-bottom: 0.75rem !important;
            border-radius: 10px !important;
        }

        .perf-toolbar-container {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .perf-segment-nav {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            width: 100% !important;
            gap: 2px !important;
            padding: 2px !important;
        }

        .perf-segment-item {
            padding: 0.35rem 0.15rem !important;
            font-size: 0.7rem !important;
            text-align: center !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .perf-filter-form {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.35rem !important;
            width: 100% !important;
        }

        .perf-search-wrap {
            width: 100% !important;
        }

        .perf-search-input {
            width: 100% !important;
            min-width: 0 !important;
            height: 34px !important;
            font-size: 0.78rem !important;
            border-radius: 6px !important;
        }

        .perf-selects-row {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.35rem !important;
            width: 100% !important;
        }

        .perf-select {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            height: 34px !important;
            font-size: 0.75rem !important;
            padding: 0.15rem 1.6rem 0.15rem 0.45rem !important;
            border-radius: 6px !important;
        }

        .perf-btn-reset {
            width: 100% !important;
            height: 30px !important;
            justify-content: center !important;
            font-size: 0.75rem !important;
            display: flex !important;
            align-items: center !important;
            border-radius: 6px !important;
        }

        /* Top 3 Cards Mobile */
        .top3-grid {
            grid-template-columns: 1fr !important;
            gap: 0.5rem !important;
            margin-bottom: 0.75rem !important;
        }

        .top3-card {
            padding: 0.65rem 0.75rem !important;
            border-radius: 10px !important;
        }

        .top3-meta-row {
            font-size: 0.72rem !important;
            flex-wrap: wrap !important;
            gap: 0.35rem !important;
        }

        .modal-stat-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    /* ========================================================
       Professional Executive Print Stylesheet
       ======================================================== */
    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }

        body {
            background: #ffffff !important;
            color: #0f172a !important;
            font-size: 9pt !important;
        }

        /* Hide Web Clutter */
        .sidebar,
        .bottom-nav,
        .header,
        .perf-header,
        .stats-grid-mobile,
        .perf-filter-card,
        .top3-grid,
        .chart-card,
        .modal-overlay,
        .btn-print,
        .btn-export,
        td:last-child,
        th:last-child,
        .score-bar-wrapper {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .page-content {
            padding: 0 !important;
        }

        /* Show Print-Only Header & Footer */
        .print-only-block {
            display: block !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
        }

        .card-header {
            display: none !important;
        }

        /* Professional Printable Table */
        .table-container {
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 8.5pt !important;
            page-break-inside: auto !important;
        }

        .table thead {
            display: table-header-group !important;
        }

        .table tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }

        .table th {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            font-size: 8pt !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            padding: 5px 6px !important;
            border: 1px solid #cbd5e1 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table td {
            padding: 5px 6px !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            font-size: 8.5pt !important;
            vertical-align: middle !important;
        }

        .table tbody tr:nth-child(even) {
            background: #fafafa !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .table tfoot {
            display: table-footer-group !important;
        }

        .table tfoot td {
            font-weight: 700 !important;
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            padding: 5px 6px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endpush

@section('content')
<div class="page-container">

    <!-- ==============================================
         PRINT-ONLY KOP & EXECUTIVE HEADER (Only on Print)
         ============================================== -->
    <div class="print-only-block" style="margin-bottom: 14px;">
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #312e81 100%); border-radius: 12px; padding: 14px 20px; color: #ffffff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border: 1px solid rgba(255,255,255,0.12); -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; gap: 14px; position: relative; z-index: 2;">
                <div style="width: 46px; height: 46px; background: #ffffff; border-radius: 11px; display: flex; align-items: center; justify-content: center; padding: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 1.5px solid rgba(255,255,255,0.85); flex-shrink: 0;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.parentElement.innerHTML='<b style=\'color:#4f46e5;font-size:14px;\'>TLM</b>'">
                </div>
                <div>
                    <div style="font-size: 8pt; font-weight: 700; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.08em; display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                        TIKTOK LIVE MANAGEMENT &bull; OFFICIAL PERFORMANCE REPORT
                    </div>
                    <div style="font-size: 15pt; font-weight: 800; color: #ffffff; letter-spacing: -0.02em; line-height: 1.2;">TOP PERFORMA HOST</div>
                    <div style="font-size: 8pt; color: #cbd5e1; margin-top: 2px;">Rekapitulasi evaluasi komposit kinerja: durasi live, penjualan, dan konten</div>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.16); border-radius: 10px; padding: 8px 14px; text-align: right; min-width: 220px; position: relative; z-index: 2;">
                <div style="font-size: 7.5pt; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em;">Periode Evaluasi</div>
                <div style="font-size: 11.5pt; font-weight: 800; color: #ffffff; margin-top: 1px;">{{ $filterLabel }}</div>
                <div style="font-size: 7.5pt; color: #e2e8f0; margin-top: 4px; display: flex; align-items: center; justify-content: flex-end; gap: 5px;">
                    <span style="background: rgba(16,185,129,0.22); color: #6ee7b7; border: 1px solid rgba(52,211,153,0.35); padding: 2px 6px; border-radius: 999px; font-weight: 600;">Semua Data</span>
                    <span>&bull; {{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
                </div>
            </div>
        </div>

        <!-- Executive Summary 4 Boxes on Print -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 10px;">
            <div style="border: 1px solid #cbd5e1; border-left: 3px solid #4f46e5; border-radius: 5px; padding: 6px 8px; background: #ffffff;">
                <div style="font-size: 7.5pt; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Jam Live</div>
                <div style="font-size: 11pt; font-weight: 800; color: #0f172a;">{{ number_format($teamSummary['total_hours'], 1) }} Jam</div>
                <div style="font-size: 7.5pt; color: #64748b;">{{ number_format($teamSummary['total_minutes']) }} Menit</div>
            </div>
            <div style="border: 1px solid #cbd5e1; border-left: 3px solid #10b981; border-radius: 5px; padding: 6px 8px; background: #ffffff;">
                <div style="font-size: 7.5pt; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Penjualan</div>
                <div style="font-size: 11pt; font-weight: 800; color: #0f172a;">{{ number_format($teamSummary['total_sales']) }} Pcs</div>
                <div style="font-size: 7.5pt; color: #64748b;">Avg {{ $teamSummary['avg_sales_per_session'] }} pcs/sesi</div>
            </div>
            <div style="border: 1px solid #cbd5e1; border-left: 3px solid #f59e0b; border-radius: 5px; padding: 6px 8px; background: #ffffff;">
                <div style="font-size: 7.5pt; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Konten</div>
                <div style="font-size: 11pt; font-weight: 800; color: #0f172a;">{{ number_format($teamSummary['total_content']) }} Video</div>
                <div style="font-size: 7.5pt; color: #64748b;">{{ $teamSummary['total_content_edit'] }} Edit + {{ $teamSummary['total_content_live'] }} Live</div>
            </div>
            <div style="border: 1px solid #cbd5e1; border-left: 3px solid #64748b; border-radius: 5px; padding: 6px 8px; background: #ffffff;">
                <div style="font-size: 7.5pt; font-weight: 600; color: #64748b; text-transform: uppercase;">Aktivitas Tim</div>
                <div style="font-size: 11pt; font-weight: 800; color: #0f172a;">{{ $teamSummary['total_sessions'] }} Sesi</div>
                <div style="font-size: 7.5pt; color: #64748b;">{{ $teamSummary['active_users'] }} Host Aktif</div>
            </div>
        </div>

        <!-- Top 3 Performer Mini Strip on Print -->
        @if($topFirst)
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 5px; padding: 6px 10px; margin-bottom: 12px; font-size: 8pt;">
            <div>
                <strong>[1] {{ $topFirst['name'] }}</strong> (MVP &bull; Skor {{ $topFirst['overall_score'] }})
                <div style="color: #64748b;">{{ $topFirst['total_hours'] }}j live &bull; {{ number_format($topFirst['total_sales']) }} sales &bull; {{ $topFirst['total_content'] }} konten</div>
            </div>
            @if($topSecond)
            <div>
                <strong>[2] {{ $topSecond['name'] }}</strong> (Skor {{ $topSecond['overall_score'] }})
                <div style="color: #64748b;">{{ $topSecond['total_hours'] }}j live &bull; {{ number_format($topSecond['total_sales']) }} sales &bull; {{ $topSecond['total_content'] }} konten</div>
            </div>
            @endif
            @if($topThird)
            <div>
                <strong>[3] {{ $topThird['name'] }}</strong> (Skor {{ $topThird['overall_score'] }})
                <div style="color: #64748b;">{{ $topThird['total_hours'] }}j live &bull; {{ number_format($topThird['total_sales']) }} sales &bull; {{ $topThird['total_content'] }} konten</div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- ==============================================
         ON-SCREEN HEADER ROW
         ============================================== -->
    <div class="perf-header">
        <div>
            <h2 class="perf-title">Top Performa Host</h2>
            <div class="perf-subtitle">Leaderboard live streaming dinilai dari durasi, penjualan produk, dan produksi konten.</div>
        </div>

        <div class="flex items-center gap-2 perf-header-actions">
            <a href="{{ route('admin.performance.export', request()->all()) }}" class="btn btn-secondary btn-sm btn-export" title="Unduh CSV">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.performance.print', array_merge(request()->all(), ['auto' => 1])) }}" target="_blank" class="btn btn-secondary btn-sm btn-print" title="Buka Pratinjau Cetak / PDF">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak
            </a>
        </div>
    </div>

    <!-- 4 Compact Stat Cards (Senada dengan Dashboard) -->
    <div class="stats-grid-mobile perf-mvp-grid mb-4">
        <!-- Top Performer -->
        <div class="stat-card-compact">
            <div class="stat-icon-sm warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label">MVP (Skor {{ $mvpLeader['overall_score'] ?? 0 }})</div>
                <div class="stat-number truncate" style="font-size: 1rem;">{{ $mvpLeader['name'] ?? '-' }}</div>
            </div>
        </div>

        <!-- Top Durasi -->
        <div class="stat-card-compact">
            <div class="stat-icon-sm primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label">Top Jam ({{ $durationLeader['total_hours'] ?? 0 }}j)</div>
                <div class="stat-number truncate" style="font-size: 1rem;">{{ $durationLeader['name'] ?? '-' }}</div>
            </div>
        </div>

        <!-- Top Penjualan -->
        <div class="stat-card-compact">
            <div class="stat-icon-sm success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label">Top Sales ({{ number_format($salesLeader['total_sales'] ?? 0) }} pcs)</div>
                <div class="stat-number truncate" style="font-size: 1rem;">{{ $salesLeader['name'] ?? '-' }}</div>
            </div>
        </div>

        <!-- Top Konten -->
        <div class="stat-card-compact">
            <div class="stat-icon-sm danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="stat-label">Top Konten ({{ $contentLeader['total_content'] ?? 0 }})</div>
                <div class="stat-number truncate" style="font-size: 1rem;">{{ $contentLeader['name'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Compact Toolbar: Category Tabs + Period Filters -->
    <div class="perf-filter-card">
        <div class="perf-toolbar-container">
            <!-- Category Segmented Control -->
            <div class="perf-segment-nav">
                <a href="{{ route('admin.performance.index', array_merge(request()->all(), ['category' => 'overall'])) }}"
                   class="perf-segment-item {{ $category === 'overall' ? 'active' : '' }}">
                    Skor Keseluruhan
                </a>
                <a href="{{ route('admin.performance.index', array_merge(request()->all(), ['category' => 'duration'])) }}"
                   class="perf-segment-item {{ $category === 'duration' ? 'active' : '' }}">
                    Durasi Live
                </a>
                <a href="{{ route('admin.performance.index', array_merge(request()->all(), ['category' => 'sales'])) }}"
                   class="perf-segment-item {{ $category === 'sales' ? 'active' : '' }}">
                    Penjualan
                </a>
                <a href="{{ route('admin.performance.index', array_merge(request()->all(), ['category' => 'content'])) }}"
                   class="perf-segment-item {{ $category === 'content' ? 'active' : '' }}">
                    Konten
                </a>
            </div>

            <!-- Single Line Filters (Desktop) / Symmetrical Grid (Mobile) -->
            <form action="{{ route('admin.performance.index') }}" method="GET" class="perf-filter-form">
                <input type="hidden" name="category" value="{{ $category }}">

                <!-- Search Input -->
                <div class="perf-search-wrap">
                    <input type="text" id="hostTableSearch" class="form-control perf-search-input" placeholder="Cari host...">
                </div>

                <div class="perf-selects-row">
                    <!-- Periode Presets Dropdown -->
                    <select name="period" class="form-control form-select perf-select perf-select-period" onchange="this.form.submit()">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                        <option value="last_30_days" {{ $period === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="last_7_days" {{ $period === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    </select>

                    <!-- Month / Year Selector -->
                    <select name="month" class="form-control form-select perf-select perf-select-month" onchange="this.form.submit()">
                        <option value="">Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="year" class="form-control form-select perf-select perf-select-year" onchange="this.form.submit()">
                        <option value="">Tahun</option>
                        @for($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                @if($selectedMonth || $selectedYear || $period !== 'all')
                    <a href="{{ route('admin.performance.index') }}" class="btn btn-secondary btn-sm perf-btn-reset">
                        ✕ Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Top 3 Compact Cards (Minimalist Podium) -->
    @if($leaderboard->isNotEmpty())
    <div class="top3-grid">
        <!-- Rank 1: Gold -->
        @if($topFirst)
        <div class="top3-card rank-1">
            <div class="flex items-center justify-between">
                <span class="rank-pill rank-1">Juara 1</span>
                <span class="text-xs font-semibold" style="color: #B45309;">Skor {{ $topFirst['overall_score'] }}/100</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="avatar" style="width: 38px; height: 38px; font-size: 0.9rem; background: #FEF3C7; color: #B45309;">
                    {{ substr($topFirst['name'], 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="font-bold text-sm truncate">{{ $topFirst['name'] }}</div>
                    <div class="text-xs text-muted">{{ $topFirst['task'] }} &bull; {{ $topFirst['sessions_count'] }} sesi</div>
                </div>
            </div>
            <div class="top3-meta-row">
                <span><strong>{{ $topFirst['total_hours'] }}j</strong> live</span>
                <span><strong>{{ number_format($topFirst['total_sales']) }}</strong> sales</span>
                <span><strong>{{ $topFirst['total_content'] }}</strong> konten</span>
                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.725rem; padding: 0.2rem 0.5rem;" onclick="showUserDetail({{ $topFirst['user_id'] }})">
                    Detail
                </button>
            </div>
        </div>
        @endif

        <!-- Rank 2: Silver -->
        @if($topSecond)
        <div class="top3-card rank-2">
            <div class="flex items-center justify-between">
                <span class="rank-pill rank-2">Juara 2</span>
                <span class="text-xs font-semibold text-muted">Skor {{ $topSecond['overall_score'] }}/100</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="avatar" style="width: 38px; height: 38px; font-size: 0.9rem; background: #F1F5F9; color: #475569;">
                    {{ substr($topSecond['name'], 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="font-bold text-sm truncate">{{ $topSecond['name'] }}</div>
                    <div class="text-xs text-muted">{{ $topSecond['task'] }} &bull; {{ $topSecond['sessions_count'] }} sesi</div>
                </div>
            </div>
            <div class="top3-meta-row">
                <span><strong>{{ $topSecond['total_hours'] }}j</strong> live</span>
                <span><strong>{{ number_format($topSecond['total_sales']) }}</strong> sales</span>
                <span><strong>{{ $topSecond['total_content'] }}</strong> konten</span>
                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.725rem; padding: 0.2rem 0.5rem;" onclick="showUserDetail({{ $topSecond['user_id'] }})">
                    Detail
                </button>
            </div>
        </div>
        @endif

        <!-- Rank 3: Bronze -->
        @if($topThird)
        <div class="top3-card rank-3">
            <div class="flex items-center justify-between">
                <span class="rank-pill rank-3">Juara 3</span>
                <span class="text-xs font-semibold text-muted">Skor {{ $topThird['overall_score'] }}/100</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="avatar" style="width: 38px; height: 38px; font-size: 0.9rem; background: #FFEDD5; color: #9A3412;">
                    {{ substr($topThird['name'], 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="font-bold text-sm truncate">{{ $topThird['name'] }}</div>
                    <div class="text-xs text-muted">{{ $topThird['task'] }} &bull; {{ $topThird['sessions_count'] }} sesi</div>
                </div>
            </div>
            <div class="top3-meta-row">
                <span><strong>{{ $topThird['total_hours'] }}j</strong> live</span>
                <span><strong>{{ number_format($topThird['total_sales']) }}</strong> sales</span>
                <span><strong>{{ $topThird['total_content'] }}</strong> konten</span>
                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.725rem; padding: 0.2rem 0.5rem;" onclick="showUserDetail({{ $topThird['user_id'] }})">
                    Detail
                </button>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Leaderboard Table Card (Centerpiece) -->
    <div class="card mb-4">
        <div class="card-header flex justify-between items-center" style="padding: 0.75rem 1rem;">
            <div>
                <h3 class="card-title" style="font-size: 0.95rem; margin: 0;">Peringkat Host</h3>
                <span class="text-xs text-muted">{{ $filterLabel }} &bull; Urutan: {{ ucfirst($category) }}</span>
            </div>
            <span class="badge badge-secondary" style="font-size: 0.75rem;">{{ $leaderboard->count() }} Host</span>
        </div>

        <div class="table-container">
            <table class="table" id="hostPerformanceTable">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>Host</th>
                        <th>Durasi Live</th>
                        <th>Penjualan</th>
                        <th>Konten</th>
                        <th>Skor Performa</th>
                        <th style="width: 80px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $item)
                        <tr class="host-row" data-name="{{ strtolower($item['name']) }}">
                            <!-- Rank Number -->
                            <td style="text-align: center;">
                                <span class="rank-num {{ $item['rank'] <= 3 ? 'rank-' . $item['rank'] : '' }}">
                                    {{ $item['rank'] }}
                                </span>
                            </td>

                            <!-- User Info -->
                            <td>
                                <div class="flex items-center gap-2 perf-header-actions">
                                    <div class="avatar avatar-sm">
                                        {{ substr($item['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">{{ $item['name'] }}</div>
                                        <div class="text-xs text-muted">{{ $item['task'] }} &bull; {{ $item['sessions_count'] }} sesi</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Duration -->
                            <td>
                                <div class="font-semibold text-sm">{{ $item['total_hours'] }} Jam</div>
                                <div class="text-xs text-muted">{{ number_format($item['total_minutes']) }} mnt &bull; avg {{ $item['avg_duration_minutes'] }} mnt</div>
                            </td>

                            <!-- Sales -->
                            <td>
                                <div class="font-semibold text-sm" style="color: #059669;">
                                    {{ number_format($item['total_sales']) }} pcs
                                </div>
                                <div class="text-xs text-muted">avg {{ $item['avg_sales'] }} &bull; max {{ $item['max_single_sales'] }}</div>
                            </td>

                            <!-- Content -->
                            <td>
                                <div class="font-semibold text-sm">{{ $item['total_content'] }}</div>
                                <div class="text-xs text-muted">{{ $item['total_content_edit'] }} edit &bull; {{ $item['total_content_live'] }} live</div>
                            </td>

                            <!-- Score & Tier -->
                            <td style="min-width: 150px;">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="badge {{ $item['tier_badge'] }}" style="font-size: 0.7rem; padding: 0.15rem 0.4rem;">
                                        {{ $item['tier'] }}
                                    </span>
                                    <span class="font-bold">{{ $item['overall_score'] }}</span>
                                </div>
                                <div class="score-bar-wrapper">
                                    <div class="score-bar-fill" style="width: {{ min(100, max(5, $item['overall_score'])) }}%;"></div>
                                </div>
                            </td>

                            <!-- Action -->
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;" onclick="showUserDetail({{ $item['user_id'] }})">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 2rem 1rem;">
                                Tidak ada data performa untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <!-- Print-Only Table Footer -->
                <tfoot class="print-only-block">
                    <tr>
                        <td colspan="2" style="text-align: center;">TOTAL & RATA-RATA TIM</td>
                        <td>{{ number_format($teamSummary['total_hours'], 1) }} Jam ({{ number_format($teamSummary['total_minutes']) }} mnt)</td>
                        <td>{{ number_format($teamSummary['total_sales']) }} pcs (avg {{ $teamSummary['avg_sales_per_session'] }})</td>
                        <td>{{ number_format($teamSummary['total_content']) }} ({{ $teamSummary['total_content_edit'] }} edit + {{ $teamSummary['total_content_live'] }} live)</td>
                        <td>Rata-rata: {{ $teamSummary['avg_score'] }}</td>
                        <td>-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Mobile Leaderboard Host Cards (Hidden on Desktop) -->
    <div class="card mb-4 hide-on-desktop">
        <div class="card-header flex justify-between items-center" style="padding: 0.65rem 0.75rem;">
            <div>
                <h3 class="card-title" style="font-size: 0.9rem; margin: 0;">Peringkat Host</h3>
                <span class="text-xs text-muted">{{ $filterLabel }} &bull; {{ ucfirst($category) }}</span>
            </div>
            <span class="badge badge-secondary" style="font-size: 0.72rem;">{{ $leaderboard->count() }} Host</span>
        </div>

        <div style="padding: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
            @forelse($leaderboard as $item)
                <div class="perf-mobile-card host-row" data-name="{{ strtolower($item['name']) }}">
                    <div class="perf-mobile-card-top">
                        <div class="flex items-center gap-2" style="min-width: 0;">
                            <span class="rank-num {{ $item['rank'] <= 3 ? 'rank-' . $item['rank'] : '' }}" style="flex-shrink: 0;">
                                {{ $item['rank'] }}
                            </span>
                            <div class="avatar avatar-sm" style="flex-shrink: 0; width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ substr($item['name'], 0, 1) }}
                            </div>
                            <div style="min-width: 0;">
                                <div class="font-bold text-sm text-truncate">{{ $item['name'] }}</div>
                                <div class="text-xs text-muted text-truncate">{{ $item['task'] }} &bull; {{ $item['sessions_count'] }} sesi</div>
                            </div>
                        </div>
                        <div style="text-align: right; flex-shrink: 0;">
                            <div class="flex items-center gap-1 justify-end">
                                <span class="badge {{ $item['tier_badge'] }}" style="font-size: 0.68rem; padding: 0.15rem 0.4rem;">
                                    {{ $item['tier'] }}
                                </span>
                                <span class="font-extrabold text-sm" style="color: var(--primary);">{{ $item['overall_score'] }}</span>
                            </div>
                            <div class="score-bar-wrapper" style="width: 70px; margin-top: 3px; margin-left: auto;">
                                <div class="score-bar-fill" style="width: {{ min(100, max(5, $item['overall_score'])) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="perf-mobile-stats-row">
                        <div class="perf-stat-col">
                            <span class="perf-stat-lbl">Durasi Live</span>
                            <span class="perf-stat-val">{{ $item['total_hours'] }} Jam</span>
                            <span class="perf-stat-sub">{{ $item['avg_duration_minutes'] }}m/sesi</span>
                        </div>
                        <div class="perf-stat-col">
                            <span class="perf-stat-lbl">Penjualan</span>
                            <span class="perf-stat-val" style="color: #059669;">{{ number_format($item['total_sales']) }} pcs</span>
                            <span class="perf-stat-sub">avg {{ $item['avg_sales'] }}</span>
                        </div>
                        <div class="perf-stat-col">
                            <span class="perf-stat-lbl">Konten</span>
                            <span class="perf-stat-val">{{ $item['total_content'] }}</span>
                            <span class="perf-stat-sub">{{ $item['total_content_edit'] }}e / {{ $item['total_content_live'] }}l</span>
                        </div>
                    </div>

                    <div>
                        <button type="button" class="btn btn-secondary btn-sm w-100" style="height: 30px; font-size: 0.78rem; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;" onclick="showUserDetail({{ $item['user_id'] }})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat Detail Performa
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted p-4">
                    Tidak ada data performa untuk filter yang dipilih.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Comparison Chart (Collapsible/Clean) - Marked as .chart-card to hide cleanly on print -->
    <div class="card mb-4 chart-card">
        <div class="card-header flex justify-between items-center" style="padding: 0.75rem 1rem;">
            <div>
                <h3 class="card-title" style="font-size: 0.95rem; margin: 0;">Grafik Perbandingan Top Host</h3>
                <span class="text-xs text-muted">Komparasi metrik jam live, penjualan (pcs), dan konten</span>
            </div>
            <span class="badge badge-secondary" style="font-size: 0.75rem;">Chart</span>
        </div>
        <div class="card-body" style="padding: 1rem;">
            <div class="chart-container" style="position: relative; height: 220px;">
                <canvas id="topPerformersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Print-Only Signatures Block -->
    <div class="print-only-block" style="margin-top: 24px; padding-top: 12px; border-top: 1px solid #cbd5e1; page-break-inside: avoid;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="font-size: 8pt; color: #64748b; max-width: 60%;">
                <p><strong>Catatan Dokumen:</strong></p>
                <p>&bull; Laporan ini diterbitkan secara otomatis melalui sistem <em>TikTok Live Manager</em>.</p>
                <p>&bull; Pembobotan performa: Durasi (35%), Penjualan (40%), dan Konten (25%).</p>
                <p>&bull; Dicetak oleh: <strong>{{ auth()->user()->name }}</strong> pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.</p>
            </div>
            <div style="text-align: center; min-width: 180px;">
                <div style="font-size: 8.5pt; color: #334155; margin-bottom: 2px;">{{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-size: 8.5pt; font-weight: 700; color: #0f172a; margin-bottom: 45px;">Mengetahui / Penanggung Jawab,</div>
                <div style="font-size: 9pt; font-weight: 700; color: #0f172a; border-bottom: 1px solid #0f172a; display: inline-block; min-width: 150px; padding-bottom: 1px;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">Administrator / Ops Manager</div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Drilldown Detail Host -->
<div class="modal-overlay" id="hostDetailModal">
    <div class="modal" style="max-width: 680px; width: 95%;">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="modalHostName" style="font-size: 1.1rem;">Rincian Host</h3>
                <div class="text-xs text-muted" id="modalHostMeta">Memuat data...</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal(document.getElementById('hostDetailModal'))">&times;</button>
        </div>

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <!-- Loading Indicator -->
            <div id="modalLoading" class="text-center" style="padding: 2rem 0;">
                <div class="text-sm text-muted">Memuat data sesi live...</div>
            </div>

            <!-- Content Area -->
            <div id="modalContent" style="display: none;">
                <!-- Summary Stat Boxes -->
                <div class="modal-stat-grid">
                    <div class="modal-stat-box">
                        <div class="modal-stat-val" id="modalTotalHours" style="color: var(--primary);">0 Jam</div>
                        <div class="modal-stat-lbl">Durasi Live</div>
                    </div>
                    <div class="modal-stat-box">
                        <div class="modal-stat-val" id="modalTotalSales" style="color: #059669;">0 Pcs</div>
                        <div class="modal-stat-lbl">Penjualan</div>
                    </div>
                    <div class="modal-stat-box">
                        <div class="modal-stat-val" id="modalTotalContent">0</div>
                        <div class="modal-stat-lbl">Total Konten</div>
                    </div>
                    <div class="modal-stat-box">
                        <div class="modal-stat-val" id="modalSessionsCount">0</div>
                        <div class="modal-stat-lbl">Sesi Live</div>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-xs text-muted uppercase">Riwayat Sesi Live</span>
                    <span class="text-xs text-muted" id="modalSessionNote"></span>
                </div>

                <!-- Session Attendance Table -->
                <div class="table-container" style="max-height: 240px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius);">
                    <table class="table" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Durasi</th>
                                <th>Sales</th>
                                <th>Edit</th>
                                <th>Live</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="modalAttendanceTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer flex justify-between items-center">
            <span class="text-xs text-muted">Data berdasarkan absensi yang tercatat</span>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal(document.getElementById('hostDetailModal'))">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Real-time Search in Leaderboard Table
    document.getElementById('hostTableSearch')?.addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.host-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            row.style.display = name.includes(query) ? '' : 'none';
        });
    });

    // Chart.js Configuration
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);
        const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';

        const textColor = isDarkMode ? '#94A3B8' : '#64748B';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.06)';

        const ctxPerformer = document.getElementById('topPerformersChart');
        if (ctxPerformer && chartData.labels.length > 0) {
            new Chart(ctxPerformer, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Jam Live',
                            data: chartData.hours,
                            backgroundColor: 'rgba(99, 102, 241, 0.85)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Penjualan (Pcs)',
                            data: chartData.sales,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderRadius: 4,
                        },
                        {
                            label: 'Total Konten',
                            data: chartData.content,
                            backgroundColor: 'rgba(245, 158, 11, 0.85)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: textColor,
                                font: { family: 'Inter', size: 11 },
                                boxWidth: 10,
                                usePointStyle: true,
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: textColor,
                                font: { family: 'Inter', size: 11 }
                            }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                font: { family: 'Inter', size: 11 }
                            }
                        }
                    }
                }
            });
        }
    });

    // AJAX Modal Detail Host Function
    function showUserDetail(userId) {
        const modal = document.getElementById('hostDetailModal');
        const loading = document.getElementById('modalLoading');
        const content = document.getElementById('modalContent');
        const title = document.getElementById('modalHostName');
        const meta = document.getElementById('modalHostMeta');

        title.textContent = 'Memuat Data Host...';
        meta.textContent = 'Mengambil riwayat absensi...';
        loading.style.display = 'block';
        content.style.display = 'none';

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        const startDate = @json($startDate);
        const endDate = @json($endDate);
        const status = @json($status);

        let url = `/admin/performance/user/${userId}?status=${encodeURIComponent(status)}`;
        if (startDate && endDate) {
            url += `&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    title.textContent = data.user.name;
                    meta.textContent = `${data.user.task} ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¢ ${data.user.email} ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¢ Telp: ${data.user.phone}`;

                    document.getElementById('modalTotalHours').textContent = `${data.summary.total_hours} Jam`;
                    document.getElementById('modalTotalSales').textContent = `${Number(data.summary.total_sales).toLocaleString()} Pcs`;
                    document.getElementById('modalTotalContent').textContent = data.summary.total_content;
                    document.getElementById('modalSessionsCount').textContent = `${data.summary.sessions_count} Sesi`;
                    document.getElementById('modalSessionNote').textContent = `${data.summary.sessions_count} sesi`;

                    const tbody = document.getElementById('modalAttendanceTableBody');
                    tbody.innerHTML = '';

                    if (data.attendances.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted" style="padding: 1.5rem;">Tidak ada catatan live pada periode ini.</td></tr>`;
                    } else {
                        data.attendances.forEach(item => {
                            const statusBadge = item.status === 'validated'
                                ? '<span class="badge badge-success" style="font-size: 0.675rem;">Valid</span>'
                                : '<span class="badge badge-warning" style="font-size: 0.675rem;">Pending</span>';

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>${item.date}</strong></td>
                                <td>${item.duration_formatted}</td>
                                <td style="color: #059669; font-weight: 600;">${Number(item.sales_count).toLocaleString()} pcs</td>
                                <td>${item.content_edit_count}</td>
                                <td>${item.content_live_count}</td>
                                <td>${statusBadge}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }

                    loading.style.display = 'none';
                    content.style.display = 'block';
                } else {
                    title.textContent = 'Gagal Memuat';
                    meta.textContent = 'Terjadi kesalahan saat memuat data.';
                    loading.style.display = 'none';
                }
            })
            .catch(err => {
                console.error(err);
                title.textContent = 'Gagal Memuat';
                meta.textContent = 'Koneksi ke server gagal.';
                loading.style.display = 'none';
            });
    }
</script>
@endpush
