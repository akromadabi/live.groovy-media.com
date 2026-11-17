<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Performa Host - {{ $filterLabel }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 24px 16px;
            font-size: 13px;
            line-height: 1.45;
        }

        /* Screen Action Bar (Hidden on Print) */
        .screen-toolbar {
            max-width: 1020px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-secondary:hover {
            background: #f1f5f9;
        }

        /* Main Document Sheet */
        .report-sheet {
            max-width: 1020px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 34px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }

        /* Top decorative gradient bar */
        .report-sheet::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #06b6d4, #10b981, #f59e0b);
        }

        /* Executive Hero Banner Card */
        .report-hero-card {
            background: linear-gradient(135deg, #090d16 0%, #15182a 45%, #1e1e38 100%);
            background-image: 
                radial-gradient(circle at 92% 15%, rgba(99, 102, 241, 0.35) 0%, transparent 45%),
                radial-gradient(circle at 10% 85%, rgba(6, 182, 212, 0.22) 0%, transparent 40%),
                linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            border-radius: 16px;
            padding: 22px 26px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25), 0 8px 10px -6px rgba(15, 23, 42, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .report-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #6366f1 0%, #06b6d4 35%, #10b981 70%, #f59e0b 100%);
        }

        .hero-watermark-rings {
            position: absolute;
            right: -35px;
            bottom: -45px;
            color: rgba(255, 255, 255, 0.05);
            pointer-events: none;
            z-index: 1;
        }

        .hero-left {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            z-index: 2;
            flex: 1;
            min-width: 0;
        }

        .hero-logo-box {
            width: 58px;
            height: 58px;
            background: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 7px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
            border: 2px solid rgba(255, 255, 255, 0.9);
            flex-shrink: 0;
        }

        .hero-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-fallback {
            font-size: 20px;
            font-weight: 900;
            color: #4f46e5;
            letter-spacing: -0.03em;
        }

        .hero-brand-text {
            min-width: 0;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 3px 10px;
            background: rgba(99, 102, 241, 0.22);
            border: 1px solid rgba(165, 180, 252, 0.35);
            border-radius: 999px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #c7d2fe;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .live-pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981, 0 0 4px #34d399;
            display: inline-block;
        }

        .hero-title {
            font-size: 23px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.25);
        }

        .hero-desc {
            font-size: 11.5px;
            color: #cbd5e1;
            margin-top: 4px;
            line-height: 1.45;
            max-width: 520px;
        }

        .hero-right {
            position: relative;
            z-index: 2;
            flex-shrink: 0;
        }

        .hero-meta-panel {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(10px);
            border-radius: 13px;
            padding: 12px 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 250px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .meta-panel-header {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .meta-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
        }

        .meta-period-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
        }

        .meta-chips-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            padding-top: 7px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .chip-status {
            background: rgba(16, 185, 129, 0.22);
            border: 1px solid rgba(52, 211, 153, 0.45);
            color: #6ee7b7;
        }

        .chip-time {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #e2e8f0;
        }

        /* Light Theme Alternate (Ink-Saver / Clean White) */
        .report-hero-card.theme-light {
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 50%, #eef2ff 100%) !important;
            border: 1.5px solid #c7d2fe !important;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.08) !important;
            color: #0f172a !important;
        }

        .report-hero-card.theme-light .hero-watermark-rings {
            color: rgba(79, 70, 229, 0.05) !important;
        }

        .report-hero-card.theme-light .hero-title {
            color: #0f172a !important;
            text-shadow: none !important;
        }

        .report-hero-card.theme-light .hero-desc {
            color: #475569 !important;
        }

        .report-hero-card.theme-light .hero-eyebrow {
            background: #eef2ff !important;
            border-color: #c7d2fe !important;
            color: #4338ca !important;
        }

        .report-hero-card.theme-light .hero-logo-box {
            background: #ffffff !important;
            border: 2px solid #e0e7ff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12) !important;
        }

        .report-hero-card.theme-light .hero-meta-panel {
            background: #ffffff !important;
            border: 1.5px solid #c7d2fe !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        .report-hero-card.theme-light .meta-label {
            color: #64748b !important;
        }

        .report-hero-card.theme-light .meta-period-badge {
            color: #1e1b4b !important;
        }

        .report-hero-card.theme-light .meta-chips-row {
            border-top: 1px solid #e2e8f0 !important;
        }

        .report-hero-card.theme-light .chip-status {
            background: #ecfdf5 !important;
            border-color: #a7f3d0 !important;
            color: #047857 !important;
        }

        .report-hero-card.theme-light .chip-time {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }

        /* Attractive KPI Summary Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .kpi-card.hours { background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%); border-top: 3px solid #6366f1; }
        .kpi-card.sales { background: linear-gradient(180deg, #f6fdf9 0%, #ffffff 100%); border-top: 3px solid #10b981; }
        .kpi-card.content { background: linear-gradient(180deg, #fefaf6 0%, #ffffff 100%); border-top: 3px solid #f59e0b; }
        .kpi-card.sessions { background: linear-gradient(180deg, #faf5ff 0%, #ffffff 100%); border-top: 3px solid #8b5cf6; }

        .kpi-lbl {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }

        .kpi-val {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
            margin-top: 3px;
        }

        .kpi-card.hours .kpi-val { color: #4338ca; }
        .kpi-card.sales .kpi-val { color: #047857; }
        .kpi-card.content .kpi-val { color: #b45309; }
        .kpi-card.sessions .kpi-val { color: #6d28d9; }

        .kpi-sub {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Top 3 Honor Roll Cards */
        .top3-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .podium-mini-card {
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .podium-mini-card.rank-1 {
            border-color: #fde68a;
            background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.08);
        }

        .podium-mini-card.rank-2 {
            border-color: #cbd5e1;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .podium-mini-card.rank-3 {
            border-color: #fed7aa;
            background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);
        }

        .podium-avatar-badge {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            flex-shrink: 0;
            position: relative;
        }

        .podium-mini-card.rank-1 .podium-avatar-badge { background: #fef3c7; color: #b45309; border: 2px solid #f59e0b; }
        .podium-mini-card.rank-2 .podium-avatar-badge { background: #f1f5f9; color: #475569; border: 2px solid #94a3b8; }
        .podium-mini-card.rank-3 .podium-avatar-badge { background: #ffedd5; color: #9a3412; border: 2px solid #ea580c; }

        .podium-pos-pill {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
        }

        .podium-mini-card.rank-1 .podium-pos-pill { background: #f59e0b; }
        .podium-mini-card.rank-2 .podium-pos-pill { background: #64748b; }
        .podium-mini-card.rank-3 .podium-pos-pill { background: #d97706; }

        .podium-body {
            min-width: 0;
            flex: 1;
        }

        .podium-header-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 4px;
        }

        .podium-tag-text {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .podium-mini-card.rank-1 .podium-tag-text { color: #b45309; }
        .podium-mini-card.rank-2 .podium-tag-text { color: #64748b; }
        .podium-mini-card.rank-3 .podium-tag-text { color: #c2410c; }

        .podium-score {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
        }

        .podium-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 1px;
        }

        .podium-stats-sub {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Leaderboard Table (Attractive, Spacious, High-End SaaS Look) */
        .table-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .lead-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .lead-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 10px 12px;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
        }

        .lead-table td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #1e293b;
        }

        .lead-table tbody tr:nth-child(even) {
            background: #fafcff;
        }

        .lead-table tbody tr.row-rank-1 {
            background: #fffdf5;
        }

        .lead-table tfoot td {
            background: #f8fafc;
            font-weight: 800;
            font-size: 11.5px;
            border-top: 2px solid #cbd5e1;
            padding: 10px 12px;
            color: #0f172a;
        }

        /* Rank badges */
        .rank-circle {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 11.5px;
        }

        .rank-circle.rank-1 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .rank-circle.rank-2 { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .rank-circle.rank-3 { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .rank-circle.rank-other { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

        /* Host Column (Generous Width, No Wrap!) */
        .host-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .host-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #e0e7ff;
        }

        .host-name-box {
            min-width: 0;
        }

        .host-full-name {
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            white-space: nowrap;
        }

        .host-task-pill {
            font-size: 10px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Metric Cells */
        .metric-primary {
            font-weight: 700;
            font-size: 12.5px;
            color: #0f172a;
        }

        .metric-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Tier Pill Badges (Vibrant, Sleek) */
        .tier-badge-print {
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            text-align: center;
            white-space: nowrap;
        }

        .tier-badge-print.diamond { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
        .tier-badge-print.gold { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .tier-badge-print.silver { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .tier-badge-print.bronze { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .tier-badge-print.rising { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

        /* Score Mini Progress Bar */
        .score-box {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .score-val {
            font-size: 12.5px;
            font-weight: 800;
            color: #0f172a;
        }

        .score-track {
            width: 48px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            background: #4f46e5;
            border-radius: 999px;
        }

        /* Footer / Signatures Area */
        .doc-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .footer-watermark {
            max-width: 60%;
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
        }

        .sig-block {
            text-align: center;
            min-width: 200px;
        }

        .sig-date-text {
            font-size: 11px;
            color: #475569;
            margin-bottom: 2px;
        }

        .sig-title-text {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 52px;
        }

        .sig-line-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 160px;
        }

        .sig-sub-title {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Print Media Overrides */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .screen-toolbar {
                display: none !important;
            }

            .report-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 12mm 12mm 12mm;
            }

            .table-card {
                box-shadow: none !important;
                page-break-inside: auto !important;
            }

            .lead-table tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            .lead-table thead {
                display: table-header-group !important;
            }

            .doc-footer {
                page-break-inside: avoid !important;
            }

            .report-hero-card {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Control Bar -->
    <div class="screen-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('admin.performance.index', request()->all()) }}" class="btn btn-secondary">
                &larr; Kembali ke Dashboard
            </a>
            <span style="font-size: 12.5px; font-weight: 600; color: #475569;">
                Pratinjau Hasil Cetak Laporan (Format A4)
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" onclick="toggleHeaderTheme()" class="btn btn-secondary" id="themeToggleBtn" title="Ganti gaya header (Midnight Gelap / Light Terang)">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <span id="themeBtnText">Gaya: Midnight</span>
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan ke PDF
            </button>
        </div>
    </div>

    <!-- Official Document Sheet -->
    <div class="report-sheet">
        
        <!-- Modern Executive Hero Banner Card -->
        <div class="report-hero-card" id="reportHeroCard">
            <!-- Decorative SVG rings background watermark -->
            <div class="hero-watermark-rings">
                <svg width="220" height="220" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="2" stroke-dasharray="6 6"/>
                    <circle cx="100" cy="100" r="65" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="100" cy="100" r="40" stroke="currentColor" stroke-width="1"/>
                </svg>
            </div>

            <div class="hero-left">
                <div class="hero-logo-box">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="hero-logo-img" onerror="this.parentElement.innerHTML='<div class=\'logo-fallback\'>TLM</div>'">
                </div>
                <div class="hero-brand-text">
                    <div class="hero-eyebrow">
                        <span class="live-pulse-dot"></span>
                        <span>TIKTOK LIVE MANAGEMENT &bull; OFFICIAL PERFORMANCE REPORT</span>
                    </div>
                    <h1 class="hero-title">TOP PERFORMA HOST</h1>
                    <p class="hero-desc">
                        Rekapitulasi evaluasi komposit kinerja host live streaming: durasi siaran, penjualan produk, dan produksi konten.
                    </p>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-meta-panel">
                    <div class="meta-panel-header">
                        <span class="meta-label">PERIODE EVALUASI</span>
                        <div class="meta-period-badge">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $filterLabel }}</span>
                        </div>
                    </div>
                    
                    <div class="meta-chips-row">
                        <div class="meta-chip chip-status">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ $status === 'validated' ? 'Hanya Tervalidasi' : 'Semua Absensi' }}</span>
                        </div>
                        <div class="meta-chip chip-time">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ now()->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Vibrant Executive KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card hours">
                <div class="kpi-lbl">Total Durasi Live</div>
                <div class="kpi-val">{{ number_format($teamSummary['total_hours'], 1) }} Jam</div>
                <div class="kpi-sub">{{ number_format($teamSummary['total_minutes']) }} menit &bull; avg {{ $teamSummary['avg_hours_per_session'] }}j/sesi</div>
            </div>

            <div class="kpi-card sales">
                <div class="kpi-lbl">Total Penjualan</div>
                <div class="kpi-val">{{ number_format($teamSummary['total_sales']) }} Pcs</div>
                <div class="kpi-sub">Rata-rata {{ $teamSummary['avg_sales_per_session'] }} pcs/sesi</div>
            </div>

            <div class="kpi-card content">
                <div class="kpi-lbl">Produksi Konten</div>
                <div class="kpi-val">{{ number_format($teamSummary['total_content']) }} Video</div>
                <div class="kpi-sub">{{ $teamSummary['total_content_edit'] }} edit &bull; {{ $teamSummary['total_content_live'] }} live</div>
            </div>

            <div class="kpi-card sessions">
                <div class="kpi-lbl">Aktivitas Tim</div>
                <div class="kpi-val">{{ $teamSummary['total_sessions'] }} Sesi</div>
                <div class="kpi-sub">{{ $teamSummary['active_users'] }} host aktif &bull; Skor rata-rata {{ $teamSummary['avg_score'] }}</div>
            </div>
        </div>

        <!-- Sleek Top 3 Podium Strip -->
        @if($topFirst)
        <div class="top3-row">
            <!-- Rank 1: Gold -->
            <div class="podium-mini-card rank-1">
                <div class="podium-avatar-badge">
                    {{ substr($topFirst['name'], 0, 1) }}
                    <span class="podium-pos-pill">1</span>
                </div>
                <div class="podium-body">
                    <div class="podium-header-line">
                        <span class="podium-tag-text">Juara 1 &bull; MVP</span>
                        <span class="podium-score">{{ $topFirst['overall_score'] }}/100</span>
                    </div>
                    <div class="podium-name">{{ $topFirst['name'] }}</div>
                    <div class="podium-stats-sub">
                        <strong>{{ $topFirst['total_hours'] }}j</strong> live &bull; <strong>{{ number_format($topFirst['total_sales']) }}</strong> pcs &bull; <strong>{{ $topFirst['total_content'] }}</strong> konten
                    </div>
                </div>
            </div>

            <!-- Rank 2: Silver -->
            @if($topSecond)
            <div class="podium-mini-card rank-2">
                <div class="podium-avatar-badge">
                    {{ substr($topSecond['name'], 0, 1) }}
                    <span class="podium-pos-pill">2</span>
                </div>
                <div class="podium-body">
                    <div class="podium-header-line">
                        <span class="podium-tag-text">Juara 2</span>
                        <span class="podium-score">{{ $topSecond['overall_score'] }}/100</span>
                    </div>
                    <div class="podium-name">{{ $topSecond['name'] }}</div>
                    <div class="podium-stats-sub">
                        <strong>{{ $topSecond['total_hours'] }}j</strong> live &bull; <strong>{{ number_format($topSecond['total_sales']) }}</strong> pcs &bull; <strong>{{ $topSecond['total_content'] }}</strong> konten
                    </div>
                </div>
            </div>
            @endif

            <!-- Rank 3: Bronze -->
            @if($topThird)
            <div class="podium-mini-card rank-3">
                <div class="podium-avatar-badge">
                    {{ substr($topThird['name'], 0, 1) }}
                    <span class="podium-pos-pill">3</span>
                </div>
                <div class="podium-body">
                    <div class="podium-header-line">
                        <span class="podium-tag-text">Juara 3</span>
                        <span class="podium-score">{{ $topThird['overall_score'] }}/100</span>
                    </div>
                    <div class="podium-name">{{ $topThird['name'] }}</div>
                    <div class="podium-stats-sub">
                        <strong>{{ $topThird['total_hours'] }}j</strong> live &bull; <strong>{{ number_format($topThird['total_sales']) }}</strong> pcs &bull; <strong>{{ $topThird['total_content'] }}</strong> konten
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Spacious, Modern Leaderboard Table (8 Columns, Broad Name Column!) -->
        <div class="table-card">
            <table class="lead-table">
                <thead>
                    <tr>
                        <th style="width: 44px; text-align: center;">#</th>
                        <th style="width: 27%;">Host / Talent</th>
                        <th style="width: 58px; text-align: center;">Sesi</th>
                        <th style="width: 17%;">Durasi Live</th>
                        <th style="width: 17%;">Penjualan</th>
                        <th style="width: 15%;">Konten</th>
                        <th style="width: 75px; text-align: right;">Skor</th>
                        <th style="width: 105px; text-align: center;">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $item)
                        <tr class="{{ $item['rank'] === 1 ? 'row-rank-1' : '' }}">
                            <!-- Rank Circle Badge -->
                            <td style="text-align: center;">
                                <span class="rank-circle {{ $item['rank'] <= 3 ? 'rank-' . $item['rank'] : 'rank-other' }}">
                                    {{ $item['rank'] }}
                                </span>
                            </td>

                            <!-- Host Column (Broad, Never Wrapping Words!) -->
                            <td>
                                <div class="host-cell">
                                    <div class="host-avatar">
                                        {{ substr($item['name'], 0, 1) }}
                                    </div>
                                    <div class="host-name-box">
                                        <div class="host-full-name">{{ $item['name'] }}</div>
                                        <div class="host-task-pill">{{ $item['task'] }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Sessions Count -->
                            <td style="text-align: center; font-weight: 600;">
                                {{ $item['sessions_count'] }}
                            </td>

                            <!-- Live Duration (Combined Jam + Menit) -->
                            <td>
                                <div class="metric-primary" style="color: #4338ca;">{{ number_format($item['total_hours'], 1) }} Jam</div>
                                <div class="metric-sub">{{ number_format($item['total_minutes']) }} mnt &bull; avg {{ $item['avg_duration_minutes'] }} mnt</div>
                            </td>

                            <!-- Sales (Combined Pcs + Avg) -->
                            <td>
                                <div class="metric-primary" style="color: #047857;">{{ number_format($item['total_sales']) }} pcs</div>
                                <div class="metric-sub">avg {{ $item['avg_sales'] }} &bull; rekor {{ $item['max_single_sales'] }} pcs</div>
                            </td>

                            <!-- Content (Combined Total + Edit/Live) -->
                            <td>
                                <div class="metric-primary">{{ $item['total_content'] }} Video</div>
                                <div class="metric-sub">{{ $item['total_content_edit'] }} edit + {{ $item['total_content_live'] }} live</div>
                            </td>

                            <!-- Performance Score with mini bar -->
                            <td style="text-align: right;">
                                <div class="score-box" style="align-items: flex-end;">
                                    <span class="score-val">{{ $item['overall_score'] }}</span>
                                    <div class="score-track">
                                        <div class="score-fill" style="width: {{ min(100, max(5, $item['overall_score'])) }}%;"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Tier Pill Badge -->
                            <td style="text-align: center;">
                                @php
                                    $tClass = 'rising';
                                    if ($item['overall_score'] >= 85) $tClass = 'diamond';
                                    elseif ($item['overall_score'] >= 70) $tClass = 'gold';
                                    elseif ($item['overall_score'] >= 50) $tClass = 'silver';
                                    elseif ($item['overall_score'] >= 25) $tClass = 'bronze';
                                @endphp
                                <span class="tier-badge-print {{ $tClass }}">{{ $item['tier'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 24px; color: #64748b;">
                                Tidak ada data performa untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: left; padding-left: 16px;">TOTAL & RATA-RATA TIM</td>
                        <td style="text-align: center;">{{ $teamSummary['total_sessions'] }}</td>
                        <td>{{ number_format($teamSummary['total_hours'], 1) }} Jam ({{ number_format($teamSummary['total_minutes']) }} mnt)</td>
                        <td style="color: #047857;">{{ number_format($teamSummary['total_sales']) }} pcs (avg {{ $teamSummary['avg_sales_per_session'] }})</td>
                        <td>{{ number_format($teamSummary['total_content']) }} Video ({{ $teamSummary['total_content_edit'] }}e + {{ $teamSummary['total_content_live'] }}l)</td>
                        <td style="text-align: right;">{{ $teamSummary['avg_score'] }}</td>
                        <td style="text-align: center;">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Modern Footer & Signatures Block -->
        <div class="doc-footer">
            <div class="footer-watermark">
                <p><strong>TikTok Live Manager &bull; Operational Report</strong></p>
                <p>Laporan performa ini diterbitkan otomatis berdasarkan data absensi harian yang tercatat.</p>
                <p>Pembobotan skor: <strong>Durasi (35%)</strong>, <strong>Penjualan (40%)</strong>, dan <strong>Konten (25%)</strong>.</p>
                <p>Dicetak oleh: <strong>{{ auth()->user()->name }}</strong> pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.</p>
            </div>

            <div class="sig-block">
                <div class="sig-date-text">{{ now()->translatedFormat('d F Y') }}</div>
                <div class="sig-title-text">Mengetahui / Penanggung Jawab,</div>
                <div class="sig-line-name">{{ auth()->user()->name }}</div>
                <div class="sig-sub-title">Administrator / Ops Manager</div>
            </div>
        </div>

    </div>

    <!-- Auto Print Script if ?auto=1 & Header Theme Switcher -->
    <script>
        function toggleHeaderTheme() {
            const card = document.getElementById('reportHeroCard');
            const btnText = document.getElementById('themeBtnText');
            if (!card) return;
            const isLight = card.classList.toggle('theme-light');
            try {
                localStorage.setItem('tlm_perf_print_theme', isLight ? 'light' : 'midnight');
            } catch(e) {}
            if (btnText) {
                btnText.textContent = isLight ? 'Gaya: Light' : 'Gaya: Midnight';
            }
        }

        // Restore preferred header theme if set previously
        (function() {
            try {
                const savedTheme = localStorage.getItem('tlm_perf_print_theme');
                if (savedTheme === 'light') {
                    const card = document.getElementById('reportHeroCard');
                    const btnText = document.getElementById('themeBtnText');
                    if (card) card.classList.add('theme-light');
                    if (btnText) btnText.textContent = 'Gaya: Light';
                }
            } catch(e) {}
        })();

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto') === '1') {
            window.addEventListener('load', function () {
                setTimeout(function () {
                    window.print();
                }, 350);
            });
        }
    </script>
</body>
</html>
