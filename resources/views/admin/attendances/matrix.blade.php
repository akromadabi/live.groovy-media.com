@extends('layouts.app')

@section('title', 'Matriks Kalender Kehadiran')

@push('styles')
<style>
    /* ========================================================
       TOP CONTROL BAR
       ======================================================== */
    .matrix-topbar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 10px);
        padding: 0.65rem 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 0.75rem;
    }

    .topbar-row-1 {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .topbar-row-2 {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        flex-wrap: wrap;
        padding-top: 0.4rem;
        border-top: 1px dashed var(--border);
    }

    /* Month Stepper Pill */
    .month-stepper {
        display: inline-flex;
        align-items: center;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        height: 32px;
    }

    .stepper-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        transition: background 0.15s ease;
    }

    .stepper-btn:hover {
        background: var(--border);
        color: var(--primary);
    }

    .stepper-label {
        padding: 0 0.65rem;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
    }

    /* Select Inputs */
    .matrix-select-group {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0;
    }

    .matrix-select {
        height: 32px;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-primary);
        background-color: var(--surface);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 0 1.5rem 0 0.55rem;
        cursor: pointer;
    }

    /* View Switcher Pill */
    .view-switcher-pill {
        display: inline-flex;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 2px;
        gap: 2px;
        height: 32px;
        box-sizing: border-box;
    }

    .view-switch-btn {
        border: none;
        background: transparent;
        color: var(--text-secondary);
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0 9px;
        height: 100%;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    .view-switch-btn.active {
        background: var(--surface);
        color: var(--primary);
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    /* Scroll Hint Bar */
    .scroll-hint-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 10px;
        background: rgba(99, 102, 241, 0.08);
        border: 1px dashed rgba(99, 102, 241, 0.25);
        border-radius: 8px;
        font-size: 0.72rem;
        color: #4f46e5;
        margin-bottom: 0.65rem;
    }

    /* ========================================================
       MATRIX TABLE (SOLID STICKY, ZERO SEE-THROUGH)
       ======================================================== */
    .matrix-container {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg, 10px);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .matrix-table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
        max-width: 100%;
    }

    .matrix-table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.74rem;
        width: max-content;
        min-width: 1350px;
        table-layout: fixed;
    }

    .matrix-table th,
    .matrix-table td {
        padding: 5px 2px;
        text-align: center;
        border-bottom: 1px solid var(--border);
        border-right: 1px solid var(--border);
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Table Header: SOLID OPAQUE BACKGROUND (Prevents See-Through) */
    .matrix-table thead th {
        background-color: var(--bg-secondary) !important;
        color: var(--text-primary);
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 2px solid var(--border) !important;
    }

    /* Top-Left Corner: Sticky Top + Sticky Left */
    .matrix-table thead th.sticky-col-user {
        position: sticky !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 30 !important; /* Top priority */
        background-color: var(--bg-secondary) !important;
        border-right: 2px solid var(--border) !important;
        border-bottom: 2px solid var(--border) !important;
    }

    /* Body Left Column: Sticky Left (Host Name) */
    .matrix-table tbody td.sticky-col-user {
        position: sticky !important;
        left: 0 !important;
        z-index: 20 !important;
        background-color: var(--surface) !important; /* Pure solid surface color */
        border-right: 2px solid var(--border) !important;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
    }

    /* Footer Row: Sticky Bottom */
    .matrix-table tfoot td {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background-color: var(--bg-secondary) !important;
        border-top: 2px solid var(--border) !important;
        font-weight: 700;
    }

    /* Bottom-Left Corner: Sticky Bottom + Sticky Left */
    .matrix-table tfoot td.sticky-col-user {
        position: sticky !important;
        bottom: 0 !important;
        left: 0 !important;
        z-index: 30 !important;
        background-color: var(--bg-secondary) !important;
        border-right: 2px solid var(--border) !important;
        border-top: 2px solid var(--border) !important;
    }

    /* User Column Width */
    .sticky-col-user {
        width: 145px;
        min-width: 145px;
        max-width: 145px;
        text-align: left !important;
        padding-left: 8px !important;
        padding-right: 6px !important;
        overflow: hidden;
    }

    /* Day Columns */
    .col-day {
        width: 32px;
        min-width: 32px;
        max-width: 34px;
    }

    .col-day-num {
        font-size: 0.74rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .col-day-name {
        font-size: 0.6rem;
        line-height: 1;
        margin-top: 2px;
        color: var(--text-muted);
    }

    .weekend-col {
        background-color: rgba(239, 68, 68, 0.035);
    }

    /* Summary Columns (Scrollable at far right, NOT sticky to prevent overlap) */
    .col-summary-start {
        border-left: 2px solid var(--border) !important;
    }
    .col-summary {
        background-color: var(--bg-secondary);
        font-weight: 600;
    }
    .col-summary-hdr {
        background-color: var(--border) !important;
        font-weight: 700 !important;
    }

    /* Cell Badges */
    .cell-badge {
        display: inline-block;
        padding: 2px 3px;
        border-radius: 4px;
        font-size: 0.67rem;
        font-weight: 600;
        line-height: 1;
        min-width: 24px;
        text-align: center;
    }
    .cell-success {
        background: #D1FAE5;
        color: #065F46;
    }
    [data-theme="dark"] .cell-success {
        background: rgba(16, 185, 129, 0.2);
        color: #34D399;
    }
    .cell-warning {
        background: #FEF3C7;
        color: #92400E;
    }
    [data-theme="dark"] .cell-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #FBBF24;
    }
    .cell-pending {
        background: #E0E7FF;
        color: #3730A3;
        border: 1px dashed #6366F1;
    }
    [data-theme="dark"] .cell-pending {
        background: rgba(99, 102, 241, 0.2);
        color: #A5B4FC;
    }
    .cell-absent {
        color: var(--text-muted);
        opacity: 0.35;
        font-size: 0.72rem;
    }

    /* ========================================================
       MODE KARTU (MOBILE APP-STYLE VIEW)
       ======================================================== */
    .host-cards-container {
        display: none;
        flex-direction: column;
        gap: 0.65rem;
    }

    .host-matrix-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.75rem;
        box-shadow: var(--shadow-sm);
    }

    .host-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.55rem;
    }

    .host-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.35rem;
        margin-bottom: 0.65rem;
        text-align: center;
    }

    .host-stat-pill {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 5px 3px;
    }

    .host-stat-val {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-primary);
        display: block;
        line-height: 1.1;
    }

    .host-stat-lbl {
        font-size: 0.62rem;
        color: var(--text-secondary);
        display: block;
        margin-top: 2px;
    }

    .mini-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 3px;
        background: var(--bg-secondary);
        padding: 6px;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .mini-cal-day {
        text-align: center;
        padding: 3px 1px;
        background: var(--surface);
        border-radius: 4px;
        font-size: 0.62rem;
        border: 1px solid var(--border);
    }

    /* ========================================================
       COMPACT LEGEND CARD (FIXED PROPORTIONS)
       ======================================================== */
    .matrix-legend-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
        box-shadow: var(--shadow-sm);
    }

    .legend-title {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.74rem;
    }

    .legend-chips-wrap {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--text-secondary);
        font-size: 0.72rem;
        white-space: nowrap;
    }

    /* Print Button Screen Styling */
    .matrix-print-btn {
        height: 32px;
        padding: 0 0.85rem;
        font-size: 0.78rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        border-radius: 6px;
    }
    @media (max-width: 576px) {
        .matrix-print-btn-text {
            display: none;
        }
        .matrix-print-btn {
            padding: 0 0.5rem;
        }
    }

    /* Hidden by default on screen */
    .print-document-header,
    .print-document-footer {
        display: none;
    }

    /* ========================================================
       EXECUTIVE-GRADE PRINT STYLING (A4 LANDSCAPE EPSON/PDF)
       ======================================================== */
    @media print {
        @page {
            size: landscape;
            margin: 7mm 8mm 7mm 8mm;
        }

        /* Force crisp color printing on color & mono printers */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box !important;
        }

        html, body {
            background: #ffffff !important;
            color: #0f172a !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            font-size: 7pt !important;
            line-height: 1.2 !important;
        }

        /* Neutralize parent flex/sidebar/grid wrappers */
        .app-container,
        .main-content,
        .page-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            display: block !important;
            background: transparent !important;
        }

        /* Hide all screen-only UI components */
        .sidebar,
        .sidebar-overlay,
        .header,
        .app-header,
        .matrix-topbar,
        .scroll-hint-bar,
        .view-switcher-pill,
        .bottom-nav,
        .impersonate-bar,
        .matrix-legend-card,
        nav,
        header,
        footer {
            display: none !important;
        }

        /* ====================================================
           1. EXECUTIVE PRINT HEADER (Kop & Metadata)
           ==================================================== */
        .print-document-header {
            display: block !important;
            margin-bottom: 6px !important;
            border-bottom: 2px solid #0f172a !important;
            padding-bottom: 5px !important;
        }

        .print-brand-row {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 5px !important;
        }

        .print-brand-left {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .print-logo-box {
            width: 28px !important;
            height: 28px !important;
            border-radius: 6px !important;
            background: #4f46e5 !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .print-logo-box svg {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        .print-org-name {
            font-size: 11pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            line-height: 1.15 !important;
            margin: 0 !important;
            letter-spacing: -0.01em !important;
        }

        .print-doc-heading {
            font-size: 7pt !important;
            color: #475569 !important;
            font-weight: 700 !important;
            margin: 1px 0 0 0 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        .print-doc-meta {
            text-align: right !important;
        }

        .print-period-badge {
            display: inline-block !important;
            background: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            font-size: 8pt !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        .print-meta-sub {
            font-size: 6pt !important;
            color: #64748b !important;
            margin-top: 2px !important;
        }

        /* Executive Summary KPI Row */
        .print-summary-bar {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 5px !important;
            margin-top: 5px !important;
        }

        .print-summary-col {
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 4px !important;
            padding: 3px 5px !important;
            text-align: center !important;
        }

        .print-summary-lbl {
            font-size: 5.5pt !important;
            font-weight: 700 !important;
            color: #64748b !important;
            display: block !important;
            text-transform: uppercase !important;
            line-height: 1 !important;
        }

        .print-summary-num {
            font-size: 8.5pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            line-height: 1.2 !important;
            margin-top: 1px !important;
            display: block !important;
        }
        .print-summary-num small {
            font-size: 6pt !important;
            font-weight: 600 !important;
            color: #64748b !important;
        }

        /* ====================================================
           2. PROFESSIONAL PRINT MATRIX TABLE (ZERO SCROLLBARS)
           ==================================================== */
        .matrix-container {
            border: 1.5px solid #64748b !important;
            border-radius: 4px !important;
            overflow: visible !important;
            box-shadow: none !important;
            margin: 0 0 6px 0 !important;
            width: 100% !important;
            background: #ffffff !important;
        }

        .matrix-table-wrapper {
            overflow: visible !important;
            overflow-x: visible !important;
            position: static !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .matrix-table {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
            font-size: 6.5pt !important;
        }

        /* Strip sticky styles to avoid Chromium print engine offsets */
        .matrix-table thead th,
        .matrix-table thead th.sticky-col-user,
        .matrix-table tbody td.sticky-col-user,
        .matrix-table tfoot td,
        .matrix-table tfoot td.sticky-col-user {
            position: static !important;
            top: auto !important;
            left: auto !important;
            bottom: auto !important;
            z-index: auto !important;
            box-shadow: none !important;
        }

        .matrix-table th,
        .matrix-table td {
            padding: 2.5px 1px !important;
            border: 1px solid #cbd5e1 !important;
            text-align: center !important;
            line-height: 1.15 !important;
        }

        .matrix-table thead th {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            border-bottom: 1.5px solid #64748b !important;
        }

        /* Host Column (Visible, Crisp, No Scrollbar) */
        .sticky-col-user {
            width: 110px !important;
            min-width: 110px !important;
            max-width: 110px !important;
            text-align: left !important;
            padding-left: 5px !important;
            padding-right: 3px !important;
            font-weight: 700 !important;
            background: #f8fafc !important;
            border-right: 1.5px solid #64748b !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        .sticky-col-user .avatar {
            display: none !important;
        }
        .sticky-col-user .text-truncate {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            font-size: 7pt !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        /* Zebra Striping */
        .matrix-table tbody tr:nth-child(even) td {
            background-color: #f8fafc !important;
        }
        .matrix-table tbody tr:nth-child(even) td.sticky-col-user {
            background-color: #f1f5f9 !important;
        }

        /* Day columns */
        .matrix-table th.col-day {
            width: auto !important;
            min-width: 0 !important;
        }
        .col-day-num {
            font-size: 6.8pt !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }
        .col-day-name {
            font-size: 5pt !important;
            font-weight: 600 !important;
            margin-top: 1px !important;
            text-transform: uppercase !important;
            color: #64748b !important;
        }

        .weekend-col {
            background: #fff1f2 !important;
        }
        .weekend-col .col-day-num {
            color: #dc2626 !important;
        }
        .weekend-col .col-day-name {
            color: #ef4444 !important;
        }

        /* Summary columns at far right */
        .col-summary-start {
            border-left: 1.5px solid #64748b !important;
        }
        .col-summary-hdr {
            width: 28px !important;
            min-width: 28px !important;
            background: #e2e8f0 !important;
            color: #0f172a !important;
            font-weight: 800 !important;
            font-size: 6.2pt !important;
        }
        .col-summary {
            width: 28px !important;
            min-width: 28px !important;
            background: #f8fafc !important;
            font-weight: 700 !important;
            font-size: 6.5pt !important;
            color: #0f172a !important;
        }
        .col-summary small {
            display: none !important;
        }

        /* Footer totals */
        .matrix-table tfoot td {
            background: #e2e8f0 !important;
            font-weight: 800 !important;
            border-top: 1.5px solid #64748b !important;
        }
        .matrix-table tfoot td.sticky-col-user {
            font-weight: 800 !important;
            font-size: 7pt !important;
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .matrix-table tfoot td.col-day div {
            font-size: 5pt !important;
            line-height: 1.1 !important;
        }

        /* Cell badges */
        .cell-badge {
            display: inline-block !important;
            font-size: 5.8pt !important;
            font-weight: 800 !important;
            padding: 1px 1.5px !important;
            border-radius: 2px !important;
            min-width: 16px !important;
            line-height: 1 !important;
        }
        .cell-success {
            background: #d1fae5 !important;
            color: #065f46 !important;
            border: 1px solid #6ee7b7 !important;
        }
        .cell-warning {
            background: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fcd34d !important;
        }
        .cell-pending {
            background: #e0e7ff !important;
            color: #3730a3 !important;
            border: 1px dashed #818cf8 !important;
        }
        .cell-absent {
            color: #94a3b8 !important;
            font-size: 5.5pt !important;
            opacity: 0.5 !important;
        }

        /* ====================================================
           3. PROFESSIONAL PRINT CARD MODE (2-COLUMN GRID)
           ==================================================== */
        .host-cards-container {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 8px !important;
            width: 100% !important;
        }

        .host-matrix-card {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            background: #ffffff !important;
            border: 1px solid #94a3b8 !important;
            border-radius: 5px !important;
            padding: 6px 8px !important;
            box-shadow: none !important;
        }

        .host-card-header {
            margin-bottom: 4px !important;
        }
        .host-stat-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 3px !important;
            margin-bottom: 5px !important;
        }
        .host-stat-pill {
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 3px !important;
            padding: 2px !important;
            text-align: center !important;
        }
        .host-stat-val {
            font-size: 7.5pt !important;
            font-weight: 700 !important;
        }
        .host-stat-lbl {
            font-size: 5pt !important;
        }

        .mini-calendar-grid {
            display: grid !important;
            grid-template-columns: repeat(7, 1fr) !important;
            gap: 2px !important;
            background: #f1f5f9 !important;
            padding: 3px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 3px !important;
        }
        .mini-cal-day {
            font-size: 5pt !important;
            padding: 1.5px 1px !important;
            border-radius: 2px !important;
        }

        /* ====================================================
           4. PROFESSIONAL PRINT FOOTER & APPROVAL SIGNATURES
           ==================================================== */
        .print-document-footer {
            display: block !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            margin-top: 8px !important;
            padding-top: 6px !important;
            border-top: 1px solid #cbd5e1 !important;
        }

        .print-legend-row {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 6.5pt !important;
            margin-bottom: 14px !important;
            color: #475569 !important;
        }
        .print-legend-title {
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        .print-approval-row {
            display: flex !important;
            align-items: flex-end !important;
            justify-content: space-between !important;
            padding: 0 45px !important;
        }

        .print-sign-box {
            text-align: center !important;
            width: 200px !important;
        }

        .print-sign-label {
            font-size: 7pt !important;
            color: #475569 !important;
            margin-bottom: 35px !important;
        }

        .print-sign-line {
            display: none !important;
        }

        .print-sign-name {
            font-size: 8pt !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            border-bottom: 1px solid #0f172a !important;
            padding-bottom: 2px !important;
            margin: 0 !important;
        }

        .print-sign-sub {
            font-size: 6.5pt !important;
            color: #64748b !important;
            margin-top: 2px !important;
        }
    }
</style>
@endpush

@section('content')
    <!-- Top Control Bar (Clean Symmetrical 2-Tier Layout) -->
    <div class="matrix-topbar">
        <!-- Row 1: Back + Quick Stepper Month Navigator + Print -->
        <div class="topbar-row-1">
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary btn-sm" style="height: 32px; padding: 0 0.65rem; font-size: 0.78rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>

            <!-- Month Stepper Navigator -->
            <div class="month-stepper">
                @php
                    $prevMonth = $month == 1 ? 12 : $month - 1;
                    $prevYear = $month == 1 ? $year - 1 : $year;
                    $nextMonth = $month == 12 ? 1 : $month + 1;
                    $nextYear = $month == 12 ? $year + 1 : $year;
                @endphp
                <a href="{{ route('admin.attendances.matrix', ['year' => $prevYear, 'month' => $prevMonth]) }}" class="stepper-btn" title="Bulan Sebelumnya">‹</a>
                <div class="stepper-label">
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
                </div>
                <a href="{{ route('admin.attendances.matrix', ['year' => $nextYear, 'month' => $nextMonth]) }}" class="stepper-btn" title="Bulan Berikutnya">›</a>
            </div>

            <!-- Print / PDF Button -->
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm matrix-print-btn" title="Cetak Dokumen / Simpan PDF (Landscape)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span class="matrix-print-btn-text">Cetak Dokumen</span>
            </button>
        </div>

        <!-- Row 2: Month & Year Selectors + Mode Switcher -->
        <div class="topbar-row-2">
            <form action="{{ route('admin.attendances.matrix') }}" method="GET" class="matrix-select-group">
                <select name="month" class="matrix-select" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>

                <select name="year" class="matrix-select" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>

            <!-- Mode Switcher -->
            <div class="view-switcher-pill">
                <button type="button" class="view-switch-btn active" id="btn-view-table" onclick="switchView('table')" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Tabel</span>
                </button>
                <button type="button" class="view-switch-btn" id="btn-view-cards" onclick="switchView('cards')" style="display: inline-flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Kartu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Scroll Hint Bar for Mobile Table Mode -->
    <div class="scroll-hint-bar" id="scroll-hint">
        <span class="flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
            <span>Geser tabel ke samping untuk melihat tanggal 1 - {{ $daysInMonth }} & rekap</span>
        </span>
        <button type="button" onclick="switchView('cards')" style="background: none; border: none; color: #4f46e5; font-weight: 700; font-size: 0.72rem; cursor: pointer; text-decoration: underline;">
            Mode Kartu
        </button>
    </div>

    <!-- ========================================================
         EXECUTIVE PRINT DOCUMENT HEADER (Kop & Ringkasan KPI)
         ======================================================== -->
    <div class="print-document-header">
        <div class="print-brand-row">
            <div class="print-brand-left">
                <div class="print-logo-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="print-org-name">{{ config('app.name', 'TikTok Live Manager') }}</h1>
                    <p class="print-doc-heading">REKAPITULASI MATRIKS KEHADIRAN & JAM LIVE STREAMING HOST</p>
                </div>
            </div>

            <div class="print-doc-meta">
                <div class="print-period-badge">
                    Periode: <strong>{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</strong>
                </div>
                <div class="print-meta-sub">
                    Dicetak: {{ now()->translatedFormat('d M Y, H:i') }} WIB &bull; Oleh: {{ auth()->user()->name ?? 'Administrator' }}
                </div>
            </div>
        </div>

        <!-- Executive KPI Summary Bar -->
        <div class="print-summary-bar">
            <div class="print-summary-col">
                <span class="print-summary-lbl">TOTAL HOST</span>
                <span class="print-summary-num">{{ count($users) }} <small>Orang</small></span>
            </div>
            <div class="print-summary-col">
                <span class="print-summary-lbl">TOTAL JAM LIVE</span>
                <span class="print-summary-num">{{ number_format(collect($userSummaries)->sum('total_hours'), 1) }} <small>Jam</small></span>
            </div>
            <div class="print-summary-col">
                <span class="print-summary-lbl">TOTAL HARI MASUK</span>
                <span class="print-summary-num">{{ collect($userSummaries)->sum('present_days') }} <small>Hari</small></span>
            </div>
            <div class="print-summary-col">
                <span class="print-summary-lbl">TOTAL SALES TERJUAL</span>
                <span class="print-summary-num">{{ number_format(collect($userSummaries)->sum('total_sales')) }} <small>Pcs</small></span>
            </div>
            <div class="print-summary-col">
                <span class="print-summary-lbl">RATA-RATA / HOST</span>
                <span class="print-summary-num">{{ count($users) > 0 ? round(collect($userSummaries)->sum('total_hours') / count($users), 1) : 0 }} <small>Jam/Host</small></span>
            </div>
        </div>
    </div>

    <!-- MODE 1: Table Matrix View -->
    <div class="matrix-container mb-3" id="matrix-table-container">
        <div class="matrix-table-wrapper">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col-user">
                            Host ({{ count($users) }})
                        </th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dt = \Carbon\Carbon::createFromDate($year, $month, $d);
                                $isWeekend = $dt->isWeekend();
                            @endphp
                            <th class="col-day {{ $isWeekend ? 'weekend-col' : '' }}">
                                <div class="col-day-num" style="color: {{ $isWeekend ? 'var(--danger)' : 'inherit' }};">{{ $d }}</div>
                                <div class="col-day-name" style="color: {{ $isWeekend ? 'var(--danger)' : 'var(--text-muted)' }};">
                                    {{ substr($dt->translatedFormat('D'), 0, 3) }}
                                </div>
                            </th>
                        @endfor
                        <!-- Rekap columns (normal table headers, NOT sticky to prevent overlapping) -->
                        <th class="col-summary-hdr col-summary-start" style="width: 54px; min-width: 54px;">Hadir</th>
                        <th class="col-summary-hdr" style="width: 64px; min-width: 64px;">Jam</th>
                        <th class="col-summary-hdr" style="width: 58px; min-width: 58px;">Sales</th>
                        <th class="col-summary-hdr" style="width: 58px; min-width: 58px;">Rata²</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $summary = $userSummaries[$user->id] ?? [
                                'present_days' => 0,
                                'total_hours' => 0,
                                'total_sales' => 0,
                                'avg_daily_hours' => 0
                            ];
                        @endphp
                        <tr>
                            <!-- Solid Sticky Left Column -->
                            <td class="sticky-col-user">
                                <div class="flex items-center gap-1.5" style="min-width: 0;">
                                    <div class="avatar avatar-xs" style="width: 22px; height: 22px; font-size: 0.65rem; flex-shrink: 0;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div style="min-width: 0; overflow: hidden;">
                                        <div style="font-weight: 600; font-size: 0.74rem;" class="text-truncate" title="{{ $user->name }}">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 1 - daysInMonth cells -->
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cell = $matrixData[$user->id][$d] ?? ['present' => false, 'hours' => 0, 'sales' => 0, 'status' => 'absent'];
                                    $dt = \Carbon\Carbon::createFromDate($year, $month, $d);
                                    $isWeekend = $dt->isWeekend();
                                @endphp
                                <td class="col-day {{ $isWeekend ? 'weekend-col' : '' }}">
                                    @if($cell['present'])
                                        @php
                                            $badgeClass = 'cell-warning';
                                            if ($cell['hours'] >= 3) {
                                                $badgeClass = 'cell-success';
                                            }
                                            if ($cell['status'] === 'pending') {
                                                $badgeClass = 'cell-pending';
                                            }
                                        @endphp
                                        <span class="cell-badge {{ $badgeClass }}" title="Tgl {{ $d }}: {{ $cell['hours'] }}j ({{ $cell['sales'] }} pcs) • {{ $cell['sessions_count'] }} sesi">
                                            {{ $cell['hours'] }}
                                        </span>
                                    @else
                                        <span class="cell-absent">—</span>
                                    @endif
                                </td>
                            @endfor

                            <!-- Summary Columns (Normal scrollable table cells) -->
                            <td class="col-summary col-summary-start" style="color: var(--primary);">
                                {{ $summary['present_days'] }}<small style="font-size: 0.6rem; font-weight: normal;">h</small>
                            </td>
                            <td class="col-summary">
                                {{ $summary['total_hours'] }}<small style="font-size: 0.6rem; font-weight: normal;">j</small>
                            </td>
                            <td class="col-summary" style="color: var(--success);">
                                {{ $summary['total_sales'] }}
                            </td>
                            <td class="col-summary" style="color: var(--text-secondary);">
                                {{ $summary['avg_daily_hours'] }}j
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 5 }}" class="text-center text-muted" style="padding: 2rem;">
                                Tidak ada data host aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <!-- Solid Bottom-Left Corner -->
                        <td class="sticky-col-user">
                            Total / Hari
                        </td>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dTotal = $dailyTotals[$d] ?? ['present_count' => 0, 'total_hours' => 0];
                                $dt = \Carbon\Carbon::createFromDate($year, $month, $d);
                                $isWeekend = $dt->isWeekend();
                            @endphp
                            <td class="col-day {{ $isWeekend ? 'weekend-col' : '' }}">
                                <div style="font-size: 0.65rem; font-weight: 700;">{{ $dTotal['present_count'] }}h</div>
                                <div style="color: var(--primary); font-size: 0.62rem; font-weight: 600;">{{ $dTotal['total_hours'] }}j</div>
                            </td>
                        @endfor
                        <td class="col-summary col-summary-start" colspan="4" style="text-align: center; font-size: 0.7rem; color: var(--text-secondary);">
                            Rekap Bulanan
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- MODE 2: Mobile Host Cards -->
    <div class="host-cards-container mb-3" id="matrix-cards-container">
        @forelse($users as $user)
            @php
                $summary = $userSummaries[$user->id] ?? [
                    'present_days' => 0,
                    'total_hours' => 0,
                    'total_sales' => 0,
                    'avg_daily_hours' => 0
                ];
            @endphp
            <div class="host-matrix-card">
                <div class="host-card-header">
                    <div class="flex items-center gap-2" style="min-width: 0;">
                        <div class="avatar avatar-sm" style="width: 32px; height: 32px; font-size: 0.8rem; flex-shrink: 0;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.85rem;" class="text-truncate">{{ $user->name }}</div>
                            <div class="text-xs text-muted">{{ $user->task ?? 'Host Live' }}</div>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-success badge-sm">{{ $summary['total_hours'] }} jam live</span>
                    </div>
                </div>

                <!-- 4 Quick Stat Pills -->
                <div class="host-stat-grid">
                    <div class="host-stat-pill">
                        <span class="host-stat-val" style="color: var(--primary);">{{ $summary['present_days'] }}</span>
                        <span class="host-stat-lbl">Hari Masuk</span>
                    </div>
                    <div class="host-stat-pill">
                        <span class="host-stat-val">{{ $summary['total_hours'] }}j</span>
                        <span class="host-stat-lbl">Total Jam</span>
                    </div>
                    <div class="host-stat-pill">
                        <span class="host-stat-val" style="color: var(--success);">{{ $summary['total_sales'] }}</span>
                        <span class="host-stat-lbl">Sales (pcs)</span>
                    </div>
                    <div class="host-stat-pill">
                        <span class="host-stat-val">{{ $summary['avg_daily_hours'] }}j</span>
                        <span class="host-stat-lbl">Rata²/Hari</span>
                    </div>
                </div>

                <!-- Mini Calendar Grid -->
                <div>
                    <div style="font-size: 0.7rem; font-weight: 600; margin-bottom: 4px; color: var(--text-secondary);">
                        Kehadiran Tanggal 1 - {{ $daysInMonth }}:
                    </div>
                    <div class="mini-calendar-grid">
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $cell = $matrixData[$user->id][$d] ?? ['present' => false, 'hours' => 0];
                                $dt = \Carbon\Carbon::createFromDate($year, $month, $d);
                                $isWeekend = $dt->isWeekend();
                            @endphp
                            <div class="mini-cal-day" style="{{ $cell['present'] ? ($cell['hours'] >= 3 ? 'background: #D1FAE5; color: #065F46; font-weight: 700; border-color: #34D399;' : 'background: #FEF3C7; color: #92400E; font-weight: 600; border-color: #FBBF24;') : ($isWeekend ? 'background: rgba(239,68,68,0.06); color: #DC2626;' : 'opacity: 0.35;') }}">
                                <div style="font-size: 0.58rem;">{{ $d }}</div>
                                <div style="font-size: 0.62rem;">{{ $cell['present'] ? $cell['hours'] . 'j' : '—' }}</div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-3 text-center text-muted">
                Tidak ada data host.
            </div>
        @endforelse
    </div>

    <!-- ========================================================
         PROFESSIONAL PRINT DOCUMENT FOOTER (Legenda & Pengesahan)
         ======================================================== -->
    <div class="print-document-footer">
        <div class="print-legend-row">
            <span class="print-legend-title">Keterangan:</span>
            <span><span class="cell-badge cell-success">3.0</span> &ge; 3 Jam (Target Tercapai)</span>
            <span><span class="cell-badge cell-warning">1.5</span> &lt; 3 Jam (Di Bawah Target)</span>
            <span><span class="cell-badge cell-pending">2.0</span> Pending Verifikasi</span>
            <span><span class="cell-badge cell-absent">&mdash;</span> Libur / Tidak Live</span>
        </div>

        <div class="print-approval-row">
            <div class="print-sign-box">
                <p class="print-sign-label">Dibuat &amp; Diverifikasi Oleh:</p>
                <div class="print-sign-line"></div>
                <p class="print-sign-name">{{ auth()->user()->name ?? 'Administrator' }}</p>
                <p class="print-sign-sub">Admin / PIC Operasional Live</p>
            </div>
            <div class="print-sign-box">
                <p class="print-sign-label">Mengetahui &amp; Menyetujui:</p>
                <div class="print-sign-line"></div>
                <p class="print-sign-name">( ___________________________ )</p>
                <p class="print-sign-sub">Operational Manager / Head of Agency</p>
            </div>
        </div>
    </div>

    <!-- Compact Legend Card (Symmetrical & Clean) -->
    <div class="matrix-legend-card">
        <span class="legend-title">Keterangan:</span>
        <div class="legend-chips-wrap">
            <span class="legend-chip">
                <span class="cell-badge cell-success">3.0</span> &ge; 3 Jam (Target)
            </span>
            <span class="legend-chip">
                <span class="cell-badge cell-warning">1.5</span> &lt; 3 Jam
            </span>
            <span class="legend-chip">
                <span class="cell-badge cell-pending">2.0</span> Pending
            </span>
            <span class="legend-chip">
                <span class="cell-badge cell-absent">—</span> Libur / Kosong
            </span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function switchView(mode) {
        const tableContainer = document.getElementById('matrix-table-container');
        const cardsContainer = document.getElementById('matrix-cards-container');
        const btnTable = document.getElementById('btn-view-table');
        const btnCards = document.getElementById('btn-view-cards');
        const scrollHint = document.getElementById('scroll-hint');

        if (mode === 'cards') {
            tableContainer.style.display = 'none';
            cardsContainer.style.display = 'flex';
            btnTable.classList.remove('active');
            btnCards.classList.add('active');
            if (scrollHint) scrollHint.style.display = 'none';
        } else {
            tableContainer.style.display = 'block';
            cardsContainer.style.display = 'none';
            btnTable.classList.add('active');
            btnCards.classList.remove('active');
            if (scrollHint) scrollHint.style.display = 'flex';
        }

        try {
            localStorage.setItem('matrix_view_mode', mode);
        } catch (e) {}
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const savedMode = localStorage.getItem('matrix_view_mode');
            if (savedMode) {
                switchView(savedMode);
            } else if (window.innerWidth <= 768) {
                switchView('cards');
            }
        } catch (e) {}
    });
</script>
@endpush
