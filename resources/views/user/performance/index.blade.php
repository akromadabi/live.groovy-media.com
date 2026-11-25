@extends('layouts.app')

@section('title', 'Top Performa Host')

@push('styles')
<style>
    /* ========================================================
       TOP PERFORMA HOST - CREATIVE, ELEGANT & PROFESSIONAL
       ======================================================== */

    /* Header Section */
    .perf-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-bottom: 1.15rem;
    }

    .perf-title-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .perf-hub-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .perf-title {
        font-size: 1.38rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .perf-subtitle {
        color: var(--text-secondary);
        font-size: 0.8125rem;
        margin-top: 0.25rem;
        line-height: 1.4;
    }

    .btn-scoring-rule {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0.28rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--primary);
        background: var(--surface);
        border: 1px solid rgba(99, 102, 241, 0.25);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        line-height: 1.2;
        text-decoration: none;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(99, 102, 241, 0.08);
    }

    .btn-scoring-rule:hover {
        background: rgba(99, 102, 241, 0.12);
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(99, 102, 241, 0.18);
    }

    [data-theme="dark"] .btn-scoring-rule {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(129, 140, 248, 0.35);
        color: #a5b4fc;
    }

    [data-theme="dark"] .btn-scoring-rule:hover {
        background: rgba(99, 102, 241, 0.25);
        border-color: #818cf8;
    }

    /* ========================================================
       HERO SHOWCASE CARD ("PERFORMA ANDA")
       ======================================================== */
    .my-perf-card {
        position: relative;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.07) 0%, rgba(139, 92, 246, 0.04) 45%, rgba(244, 63, 94, 0.03) 100%);
        border: 1px solid rgba(99, 102, 241, 0.22);
        border-radius: 18px;
        padding: 1.25rem 1.35rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 20px -3px rgba(99, 102, 241, 0.1), 0 1px 3px 0 rgba(0, 0, 0, 0.03);
        overflow: hidden;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    [data-theme="dark"] .my-perf-card {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.14) 0%, rgba(139, 92, 246, 0.08) 50%, rgba(15, 23, 42, 0.7) 100%);
        border-color: rgba(129, 140, 248, 0.28);
        box-shadow: 0 4px 25px -4px rgba(0, 0, 0, 0.4), 0 0 15px rgba(99, 102, 241, 0.12);
    }

    .my-perf-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
        background: linear-gradient(90deg, #6366F1 0%, #8B5CF6 35%, #EC4899 70%, #F59E0B 100%);
    }

    .my-perf-card::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 190px;
        height: 190px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(139, 92, 246, 0.08) 50%, transparent 75%);
        pointer-events: none;
        z-index: 0;
    }

    .my-perf-inner {
        position: relative;
        z-index: 1;
    }

    .my-perf-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.85rem;
        margin-bottom: 0.95rem;
    }

    .my-perf-user-group {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .my-avatar-container {
        position: relative;
        flex-shrink: 0;
    }

    .my-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        font-size: 1.15rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);
        border: 2px solid var(--surface);
    }

    .my-avatar.top-1 {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        box-shadow: 0 3px 12px rgba(245, 158, 11, 0.45);
    }

    .my-avatar.top-2 {
        background: linear-gradient(135deg, #94A3B8 0%, #475569 100%);
        box-shadow: 0 3px 10px rgba(100, 116, 139, 0.35);
    }

    .my-avatar.top-3 {
        background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%);
        box-shadow: 0 3px 10px rgba(234, 88, 12, 0.35);
    }

    .my-avatar-crown-badge {
        position: absolute;
        bottom: -5px;
        right: -6px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        border: 1.5px solid #ffffff;
        font-size: 0.72rem;
    }

    [data-theme="dark"] .my-avatar-crown-badge {
        background: #1E293B;
        border-color: #334155;
    }

    .my-user-info {
        display: flex;
        flex-direction: column;
    }

    .my-user-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.45rem;
        line-height: 1.25;
    }

    .me-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.12rem 0.5rem;
        border-radius: 999px;
        box-shadow: 0 1px 4px rgba(79, 70, 229, 0.3);
        letter-spacing: 0.02em;
    }

    .my-user-role {
        font-size: 0.76rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 2px;
    }

    .my-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
    }

    .my-perf-badge-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .my-rank-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        background: var(--surface);
        color: var(--text-primary);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s ease;
    }

    .my-rank-badge.top-1 {
        background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
        color: #B45309;
        border-color: rgba(245, 158, 11, 0.4);
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);
    }

    [data-theme="dark"] .my-rank-badge.top-1 {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(217, 119, 6, 0.2) 100%);
        color: #FCD34D;
        border-color: rgba(245, 158, 11, 0.5);
    }

    .my-rank-badge.top-2 {
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        color: #475569;
        border-color: rgba(148, 163, 184, 0.4);
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
    }

    [data-theme="dark"] .my-rank-badge.top-2 {
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.2) 0%, rgba(100, 116, 139, 0.15) 100%);
        color: #CBD5E1;
        border-color: rgba(148, 163, 184, 0.45);
    }

    .my-rank-badge.top-3 {
        background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);
        color: #C2410C;
        border-color: rgba(234, 88, 12, 0.4);
        box-shadow: 0 2px 8px rgba(234, 88, 12, 0.2);
    }

    [data-theme="dark"] .my-rank-badge.top-3 {
        background: linear-gradient(135deg, rgba(234, 88, 12, 0.25) 0%, rgba(194, 65, 12, 0.18) 100%);
        color: #FDBA74;
        border-color: rgba(234, 88, 12, 0.45);
    }

    .my-tier-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        box-shadow: var(--shadow-sm);
    }

    .badge-diamond {
        background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
        color: #4F46E5;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    .badge-gold {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        color: #B45309;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .badge-silver {
        background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
        color: #475569;
        border: 1px solid rgba(148, 163, 184, 0.35);
    }
    .badge-bronze {
        background: linear-gradient(135deg, #FFEDD5 0%, #FED7AA 100%);
        color: #9A3412;
        border: 1px solid rgba(234, 88, 12, 0.35);
    }

    [data-theme="dark"] .badge-diamond {
        background: rgba(99, 102, 241, 0.2);
        color: #A5B4FC;
        border-color: rgba(129, 140, 248, 0.4);
    }
    [data-theme="dark"] .badge-gold {
        background: rgba(245, 158, 11, 0.2);
        color: #FCD34D;
        border-color: rgba(245, 158, 11, 0.4);
    }
    [data-theme="dark"] .badge-silver {
        background: rgba(148, 163, 184, 0.2);
        color: #CBD5E1;
        border-color: rgba(148, 163, 184, 0.4);
    }
    [data-theme="dark"] .badge-bronze {
        background: rgba(217, 119, 6, 0.2);
        color: #FDBA74;
        border-color: rgba(217, 119, 6, 0.4);
    }

    /* Score Progress Bar & Tier Gauge */
    .my-meter-section {
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(99, 102, 241, 0.15);
        border-radius: 12px;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.95rem;
    }

    [data-theme="dark"] .my-meter-section {
        background: rgba(15, 23, 42, 0.5);
        border-color: rgba(129, 140, 248, 0.18);
    }

    .my-meter-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.76rem;
        margin-bottom: 0.4rem;
    }

    .my-meter-label {
        font-weight: 600;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .my-meter-score {
        font-weight: 800;
        color: var(--text-primary);
    }

    .my-meter-score span {
        color: var(--primary);
        font-size: 0.95rem;
    }

    .my-meter-track {
        width: 100%;
        height: 8px;
        background: rgba(99, 102, 241, 0.12);
        border-radius: 999px;
        overflow: hidden;
        position: relative;
    }

    [data-theme="dark"] .my-meter-track {
        background: rgba(255, 255, 255, 0.08);
    }

    .my-meter-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #6366F1 0%, #8B5CF6 50%, #EC4899 100%);
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
    }

    .my-meter-hint {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.68rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
    }

    /* 4 Glass Stat Tiles */
    .my-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.65rem;
        margin-top: 0.5rem;
    }

    .my-stat-box {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.75rem 0.85rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .my-stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.06);
        border-color: rgba(99, 102, 241, 0.35);
    }

    [data-theme="dark"] .my-stat-box {
        background: rgba(30, 41, 59, 0.65);
        border-color: rgba(255, 255, 255, 0.08);
    }

    [data-theme="dark"] .my-stat-box:hover {
        border-color: rgba(129, 140, 248, 0.4);
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.3);
    }

    .my-stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    .my-stat-icon-wrap {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }

    .my-stat-icon-wrap.indigo { background: rgba(99, 102, 241, 0.12); color: #6366F1; }
    .my-stat-icon-wrap.emerald { background: rgba(16, 185, 129, 0.12); color: #10B981; }
    .my-stat-icon-wrap.amber { background: rgba(245, 158, 11, 0.12); color: #F59E0B; }
    .my-stat-icon-wrap.sky { background: rgba(14, 165, 233, 0.12); color: #0EA5E9; }

    [data-theme="dark"] .my-stat-icon-wrap.indigo { background: rgba(99, 102, 241, 0.22); color: #A5B4FC; }
    [data-theme="dark"] .my-stat-icon-wrap.emerald { background: rgba(16, 185, 129, 0.22); color: #6EE7B7; }
    [data-theme="dark"] .my-stat-icon-wrap.amber { background: rgba(245, 158, 11, 0.22); color: #FCD34D; }
    [data-theme="dark"] .my-stat-icon-wrap.sky { background: rgba(14, 165, 233, 0.22); color: #7DD3FC; }

    .my-stat-box .lbl {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
    }

    .my-stat-box .val {
        font-size: 1.18rem;
        font-weight: 800;
        line-height: 1.2;
        margin-top: 0.15rem;
        letter-spacing: -0.01em;
    }

    .my-stat-box .sub {
        font-size: 0.68rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Action bar at card bottom */
    .my-perf-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.95rem;
        padding-top: 0.75rem;
        border-top: 1px dashed rgba(99, 102, 241, 0.2);
    }

    .my-perf-note {
        font-size: 0.72rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-detail-perf {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.4rem 0.95rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        background: var(--surface);
        color: var(--primary);
        border: 1px solid rgba(99, 102, 241, 0.25);
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .btn-detail-perf:hover {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(99, 102, 241, 0.3);
    }

    [data-theme="dark"] .btn-detail-perf {
        background: rgba(30, 41, 59, 0.85);
        border-color: rgba(129, 140, 248, 0.35);
        color: #A5B4FC;
    }

    [data-theme="dark"] .btn-detail-perf:hover {
        background: #6366F1;
        color: #ffffff;
    }

    /* ========================================================
       HALL OF FAME (4 SPOTLIGHT CARDS)
       ======================================================== */
    .perf-mvp-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.15rem;
    }

    .spotlight-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 0.8rem 0.95rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .spotlight-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
    }

    .spotlight-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 3.5px;
    }

    .spotlight-card.mvp::before { background: linear-gradient(180deg, #F59E0B, #D97706); }
    .spotlight-card.duration::before { background: linear-gradient(180deg, #6366F1, #4F46E5); }
    .spotlight-card.sales::before { background: linear-gradient(180deg, #10B981, #059669); }
    .spotlight-card.content::before { background: linear-gradient(180deg, #F43F5E, #E11D48); }

    .spotlight-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.4rem;
    }

    .spotlight-category-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .spotlight-category-pill.mvp { color: #D97706; }
    .spotlight-category-pill.duration { color: #4F46E5; }
    .spotlight-category-pill.sales { color: #059669; }
    .spotlight-category-pill.content { color: #E11D48; }

    [data-theme="dark"] .spotlight-category-pill.mvp { color: #FBBF24; }
    [data-theme="dark"] .spotlight-category-pill.duration { color: #818CF8; }
    [data-theme="dark"] .spotlight-category-pill.sales { color: #34D399; }
    [data-theme="dark"] .spotlight-category-pill.content { color: #FB7185; }

    .spotlight-metric-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .spotlight-leader-row {
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }

    .spotlight-avatar {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        font-size: 0.82rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #ffffff;
    }

    .spotlight-avatar.mvp { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .spotlight-avatar.duration { background: linear-gradient(135deg, #6366F1, #4F46E5); }
    .spotlight-avatar.sales { background: linear-gradient(135deg, #10B981, #059669); }
    .spotlight-avatar.content { background: linear-gradient(135deg, #F43F5E, #E11D48); }

    .spotlight-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ========================================================
       TOOLBAR: SEGMENTED TABS & ADVANCED FILTERS
       ======================================================== */
    .perf-filter-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.15rem;
        box-shadow: var(--shadow-sm);
    }

    .perf-toolbar-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .perf-segment-nav {
        display: inline-flex;
        background: var(--bg-secondary);
        padding: 3px;
        border-radius: 10px;
        gap: 3px;
        overflow-x: auto;
        max-width: 100%;
        border: 1px solid var(--border);
    }

    .perf-segment-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0.4rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .perf-segment-item svg {
        width: 14px;
        height: 14px;
        opacity: 0.75;
    }

    .perf-segment-item:hover {
        color: var(--text-primary);
    }

    .perf-segment-item.active {
        background: var(--surface);
        color: var(--primary);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .perf-segment-item.active svg {
        opacity: 1;
        color: var(--primary);
    }

    [data-theme="dark"] .perf-segment-item.active {
        background: #334155;
        color: #A5B4FC;
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

    .perf-search-icon {
        position: absolute;
        left: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 14px;
        color: var(--text-muted);
        pointer-events: none;
    }

    .perf-search-input {
        font-size: 0.8rem;
        padding: 0.4rem 0.65rem 0.4rem 2rem;
        min-width: 150px;
        height: 34px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--surface);
        color: var(--text-primary);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .perf-search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .perf-selects-row {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .perf-select {
        height: 34px;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0 1.8rem 0 0.65rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        background-color: var(--surface);
        color: var(--text-primary);
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 13px 13px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .perf-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .perf-select-period { min-width: 125px; }
    .perf-select-month  { min-width: 105px; }
    .perf-select-year   { min-width: 85px; }

    .perf-btn-reset {
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0 0.75rem;
        height: 34px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 8px;
        color: var(--text-muted);
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .perf-btn-reset:hover {
        color: var(--danger);
        border-color: var(--danger);
        background: rgba(239, 68, 68, 0.08);
    }

    /* ========================================================
       PODIUM 3 BESAR (OLYMPIC CREATOR STAGE 2 - 1 - 3)
       Subtle, prestigious, perfectly proportioned
       ======================================================== */
    .podium-stage-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 1.15rem 1.25rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 18px -2px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    [data-theme="dark"] .podium-stage-card {
        background: linear-gradient(180deg, rgba(30, 41, 59, 0.85) 0%, rgba(15, 23, 42, 0.95) 100%);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 22px rgba(0, 0, 0, 0.4);
    }

    .podium-stage-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid var(--border);
    }

    .podium-title-group {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .podium-title-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        color: #B45309;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 2px 6px rgba(245, 158, 11, 0.25);
    }

    [data-theme="dark"] .podium-title-icon-box {
        background: rgba(245, 158, 11, 0.25);
        color: #FCD34D;
    }

    .podium-title-text {
        font-size: 0.98rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .podium-subtitle-text {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .podium-badge-indicator {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.22rem 0.65rem;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.1);
        color: var(--primary);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    /* Stage Layout (Olympic: 2 - 1 - 3) */
    .podium-stage {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 0.85rem;
        padding: 1rem 0.5rem 0.25rem;
    }

    .podium-column {
        flex: 1;
        max-width: 230px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 14px;
        padding: 0.65rem 0.45rem 0.35rem;
        position: relative;
    }

    .podium-column:hover {
        transform: translateY(-4px);
    }

    .podium-column.rank-1 {
        order: 2;
        z-index: 2;
    }

    .podium-column.rank-2 {
        order: 1;
        z-index: 1;
    }

    .podium-column.rank-3 {
        order: 3;
        z-index: 1;
    }

    .podium-avatar-wrapper {
        position: relative;
        margin-bottom: 0.45rem;
    }

    .podium-avatar {
        border-radius: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transition: transform 0.2s ease;
    }

    .podium-column.rank-1 .podium-avatar {
        width: 58px;
        height: 58px;
        font-size: 1.35rem;
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.45);
        border: 3px solid #FEF3C7;
    }

    .podium-column.rank-2 .podium-avatar {
        width: 48px;
        height: 48px;
        font-size: 1.1rem;
        background: linear-gradient(135deg, #94A3B8 0%, #64748B 100%);
        box-shadow: 0 3px 12px rgba(100, 116, 139, 0.35);
        border: 2.5px solid #F1F5F9;
    }

    .podium-column.rank-3 .podium-avatar {
        width: 46px;
        height: 46px;
        font-size: 1.05rem;
        background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%);
        box-shadow: 0 3px 12px rgba(234, 88, 12, 0.3);
        border: 2.5px solid #FFEDD5;
    }

    .podium-crown {
        position: absolute;
        top: -16px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 1.35rem;
        filter: drop-shadow(0 2px 4px rgba(245, 158, 11, 0.5));
        animation: crownFloat 2.5s ease-in-out infinite alternate;
    }

    @keyframes crownFloat {
        0% { transform: translateX(-50%) translateY(0); }
        100% { transform: translateX(-50%) translateY(-3px); }
    }

    .podium-medal {
        position: absolute;
        bottom: -5px;
        right: -6px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        border: 1.5px solid #ffffff;
    }

    [data-theme="dark"] .podium-medal {
        background: #1E293B;
        border-color: #334155;
    }

    .podium-name {
        font-size: 0.84rem;
        font-weight: 700;
        color: var(--text-primary);
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.25;
    }

    .podium-column.rank-1 .podium-name {
        font-size: 0.92rem;
    }

    .me-pill-wrap {
        min-height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2px 0;
    }

    .podium-score-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 0.18rem 0.55rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        margin-top: 0.15rem;
    }

    .podium-column.rank-1 .podium-score-pill {
        background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
        color: #B45309;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }

    .podium-column.rank-2 .podium-score-pill {
        background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
        color: #475569;
        border: 1px solid rgba(148, 163, 184, 0.35);
    }

    .podium-column.rank-3 .podium-score-pill {
        background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);
        color: #C2410C;
        border: 1px solid rgba(234, 88, 12, 0.35);
    }

    [data-theme="dark"] .podium-column.rank-1 .podium-score-pill {
        background: rgba(245, 158, 11, 0.2);
        color: #FCD34D;
    }
    [data-theme="dark"] .podium-column.rank-2 .podium-score-pill {
        background: rgba(148, 163, 184, 0.2);
        color: #E2E8F0;
    }
    [data-theme="dark"] .podium-column.rank-3 .podium-score-pill {
        background: rgba(234, 88, 12, 0.2);
        color: #FDBA74;
    }

    /* Pedestal Block Base */
    .podium-pedestal {
        width: 100%;
        border-radius: 12px;
        padding: 0.6rem 0.45rem;
        margin-top: 0.45rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .podium-column.rank-1 .podium-pedestal {
        min-height: 86px;
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.14) 0%, rgba(245, 158, 11, 0.04) 100%);
        border: 1.5px solid rgba(245, 158, 11, 0.4);
    }

    .podium-column.rank-2 .podium-pedestal {
        min-height: 72px;
        background: linear-gradient(180deg, rgba(148, 163, 184, 0.12) 0%, rgba(148, 163, 184, 0.03) 100%);
        border: 1.5px solid rgba(148, 163, 184, 0.35);
    }

    .podium-column.rank-3 .podium-pedestal {
        min-height: 62px;
        background: linear-gradient(180deg, rgba(234, 88, 12, 0.12) 0%, rgba(234, 88, 12, 0.03) 100%);
        border: 1.5px solid rgba(234, 88, 12, 0.35);
    }

    [data-theme="dark"] .podium-column.rank-1 .podium-pedestal {
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.22) 0%, rgba(15, 23, 42, 0.6) 100%);
        border-color: rgba(245, 158, 11, 0.45);
    }
    [data-theme="dark"] .podium-column.rank-2 .podium-pedestal {
        background: linear-gradient(180deg, rgba(148, 163, 184, 0.18) 0%, rgba(15, 23, 42, 0.6) 100%);
        border-color: rgba(148, 163, 184, 0.4);
    }
    [data-theme="dark"] .podium-column.rank-3 .podium-pedestal {
        background: linear-gradient(180deg, rgba(234, 88, 12, 0.18) 0%, rgba(15, 23, 42, 0.6) 100%);
        border-color: rgba(234, 88, 12, 0.4);
    }

    .podium-step-badge {
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .podium-column.rank-1 .podium-step-badge { color: #B45309; }
    .podium-column.rank-2 .podium-step-badge { color: #475569; }
    .podium-column.rank-3 .podium-step-badge { color: #9A3412; }

    [data-theme="dark"] .podium-column.rank-1 .podium-step-badge { color: #FCD34D; }
    [data-theme="dark"] .podium-column.rank-2 .podium-step-badge { color: #CBD5E1; }
    [data-theme="dark"] .podium-column.rank-3 .podium-step-badge { color: #FDBA74; }

    .podium-stats-sub {
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-top: 0.2rem;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .podium-view-btn {
        margin-top: 0.35rem;
        font-size: 0.66rem;
        font-weight: 700;
        padding: 0.15rem 0.55rem;
        border-radius: 999px;
        background: var(--surface);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .podium-column:hover .podium-view-btn {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    /* User Highlight on Podium */
    .podium-column.is-me {
        background: rgba(99, 102, 241, 0.05);
        border-radius: 16px;
        box-shadow: 0 0 0 2px var(--primary);
    }

    [data-theme="dark"] .podium-column.is-me {
        background: rgba(99, 102, 241, 0.15);
    }

    /* Rank number badges */
    .rank-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 800;
        color: var(--text-secondary);
        background: var(--bg-secondary);
    }

    .rank-num.rank-1 { background: #FEF3C7; color: #B45309; }
    .rank-num.rank-2 { background: #F1F5F9; color: #475569; }
    .rank-num.rank-3 { background: #FFEDD5; color: #9A3412; }

    [data-theme="dark"] .rank-num.rank-1 { background: rgba(245, 158, 11, 0.25); color: #FCD34D; }
    [data-theme="dark"] .rank-num.rank-2 { background: rgba(148, 163, 184, 0.25); color: #E2E8F0; }
    [data-theme="dark"] .rank-num.rank-3 { background: rgba(217, 119, 6, 0.25); color: #FDBA74; }

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
        background: linear-gradient(90deg, #6366F1, #8B5CF6);
    }

    /* ========================================================
       LEADERBOARD - MOBILE CARDS & DESKTOP TABLE
       ======================================================== */
    tr.host-row.is-me {
        background-color: rgba(99, 102, 241, 0.06) !important;
        outline: 1.5px solid var(--primary);
    }

    [data-theme="dark"] tr.host-row.is-me {
        background-color: rgba(99, 102, 241, 0.14) !important;
    }

    .perf-mobile-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.75rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .perf-mobile-card.is-me {
        border-color: var(--primary) !important;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.06) 0%, var(--surface) 100%);
        box-shadow: 0 0 0 1.5px rgba(99, 102, 241, 0.35);
    }

    [data-theme="dark"] .perf-mobile-card.is-me {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.16) 0%, var(--surface) 100%);
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
        gap: 0.4rem;
        background: var(--bg-secondary);
        padding: 0.5rem 0.6rem;
        border-radius: 8px;
    }

    .perf-stat-col {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .perf-stat-lbl {
        font-size: 0.64rem;
        font-weight: 600;
        color: var(--text-muted);
        line-height: 1.1;
    }

    .perf-stat-val {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 2px;
    }

    .perf-stat-sub {
        font-size: 0.62rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Modal Styling */
    .modal-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.55rem;
        margin-bottom: 1rem;
    }

    .modal-stat-box {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
        text-align: center;
    }

    .modal-stat-val {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .modal-stat-lbl {
        font-size: 0.68rem;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 3px;
    }

    /* ========================================================
       RESPONSIVE BREAKPOINTS (<= 768px Mobile)
       ======================================================== */
    @media (max-width: 768px) {
        .perf-header {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            margin-bottom: 0.85rem;
        }

        .my-perf-card {
            padding: 1rem;
            border-radius: 14px;
            margin-bottom: 0.95rem;
        }

        .my-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.5rem;
        }

        .my-stat-box {
            padding: 0.65rem 0.75rem;
        }

        .my-stat-box .val {
            font-size: 1.05rem;
        }

        .perf-mvp-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 0.5rem !important;
            margin-bottom: 0.85rem !important;
        }

        .spotlight-card {
            padding: 0.65rem 0.75rem !important;
            border-radius: 10px !important;
        }

        .spotlight-avatar {
            width: 28px !important;
            height: 28px !important;
            font-size: 0.75rem !important;
        }

        .spotlight-name {
            font-size: 0.82rem !important;
        }

        .perf-filter-card {
            padding: 0.6rem 0.75rem !important;
            margin-bottom: 0.85rem !important;
            border-radius: 12px !important;
        }

        .perf-toolbar-container {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.55rem !important;
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
            padding: 0.4rem 0.2rem !important;
            font-size: 0.72rem !important;
            text-align: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .perf-segment-item svg {
            display: none !important;
        }

        .perf-filter-form {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.4rem !important;
            width: 100% !important;
        }

        .perf-search-wrap {
            width: 100% !important;
        }

        .perf-search-input {
            width: 100% !important;
            min-width: 0 !important;
            height: 34px !important;
            font-size: 0.8rem !important;
        }

        .perf-selects-row {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 0.4rem !important;
            width: 100% !important;
        }

        .perf-select {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            height: 34px !important;
            font-size: 0.78rem !important;
        }

        .perf-btn-reset {
            width: 100% !important;
            height: 32px !important;
            justify-content: center !important;
            font-size: 0.78rem !important;
        }

        /* Responsive Podium on Mobile: 3 pedestals side by side */
        .podium-stage-card {
            padding: 0.85rem 0.75rem 0.85rem !important;
            border-radius: 14px !important;
            margin-bottom: 0.95rem !important;
        }

        .podium-stage {
            gap: 0.35rem !important;
            padding: 0.75rem 0.15rem 0.15rem !important;
        }

        .podium-column {
            padding: 0.35rem 0.15rem !important;
        }

        .podium-column.rank-1 .podium-avatar {
            width: 48px !important;
            height: 48px !important;
            font-size: 1.15rem !important;
        }

        .podium-column.rank-2 .podium-avatar {
            width: 40px !important;
            height: 40px !important;
            font-size: 0.95rem !important;
        }

        .podium-column.rank-3 .podium-avatar {
            width: 38px !important;
            height: 38px !important;
            font-size: 0.9rem !important;
        }

        .podium-crown {
            font-size: 1.15rem !important;
            top: -14px !important;
        }

        .podium-medal {
            width: 18px !important;
            height: 18px !important;
            font-size: 0.65rem !important;
            bottom: -3px !important;
            right: -3px !important;
        }

        .podium-name {
            font-size: 0.76rem !important;
            max-width: 95px !important;
        }

        .podium-column.rank-1 .podium-name {
            font-size: 0.82rem !important;
        }

        .podium-score-pill {
            font-size: 0.65rem !important;
            padding: 0.12rem 0.4rem !important;
        }

        .podium-pedestal {
            padding: 0.45rem 0.25rem !important;
        }

        .podium-column.rank-1 .podium-pedestal {
            min-height: 72px !important;
        }

        .podium-column.rank-2 .podium-pedestal {
            min-height: 58px !important;
        }

        .podium-column.rank-3 .podium-pedestal {
            min-height: 50px !important;
        }

        .podium-step-badge {
            font-size: 0.62rem !important;
        }

        .podium-stats-sub {
            font-size: 0.58rem !important;
        }

        .podium-view-btn {
            font-size: 0.6rem !important;
            padding: 0.1rem 0.45rem !important;
            margin-top: 0.25rem !important;
        }

        .modal-stat-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
@endpush

@section('content')
<div class="page-container">

    <!-- Header Row -->
    <div class="perf-header">
        <div>
            <div class="perf-title-row">
                <span class="perf-hub-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Live Performance
                </span>
                <h2 class="perf-title">Top Performa Host</h2>
                <button type="button" class="btn-scoring-rule" onclick="openModal('scoringRulesModal')" data-modal="scoringRulesModal" title="Aturan Penilaian Skor Host">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Aturan Skor</span>
                </button>
            </div>
            <div class="perf-subtitle">Leaderboard live streaming dinilai dari durasi live (35%), penjualan produk (40%), dan produksi konten (25%).</div>
        </div>
    </div>

    <!-- Personal Performance Card ("Performa Saya") -->
    <div class="my-perf-card">
        <div class="my-perf-inner">
            <!-- Top Profile & Badges -->
            <div class="my-perf-top">
                <div class="my-perf-user-group">
                    <div class="my-avatar-container">
                        <div class="my-avatar {{ $myRank == 1 ? 'top-1' : ($myRank == 2 ? 'top-2' : ($myRank == 3 ? 'top-3' : '')) }}">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        @if($myRank == 1)
                            <div class="my-avatar-crown-badge" title="Juara 1 MVP">👑</div>
                        @elseif($myRank == 2)
                            <div class="my-avatar-crown-badge" title="Juara 2">🥈</div>
                        @elseif($myRank == 3)
                            <div class="my-avatar-crown-badge" title="Juara 3">🥉</div>
                        @endif
                    </div>
                    <div class="my-user-info">
                        <div class="my-user-name">
                            {{ auth()->user()->name }}
                            <span class="me-pill">Anda</span>
                        </div>
                        <div class="my-user-role">
                            <span class="my-status-dot"></span>
                            <span>{{ auth()->user()->task ?? 'Host Live Streaming' }}</span>
                        </div>
                    </div>
                </div>

                <div class="my-perf-badge-row">
                    @if($myPerformance)
                        <span class="my-rank-badge {{ $myRank == 1 ? 'top-1' : ($myRank == 2 ? 'top-2' : ($myRank == 3 ? 'top-3' : '')) }}">
                            @if($myRank == 1)
                                <span>🏆 Juara 1 (MVP)</span>
                            @elseif($myRank == 2)
                                <span>🥈 Juara 2</span>
                            @elseif($myRank == 3)
                                <span>🥉 Juara 3</span>
                            @else
                                <span>🏅 Peringkat #{{ $myRank }} dari {{ $totalHosts }} Host</span>
                            @endif
                        </span>
                        <span class="my-tier-badge {{ $myPerformance['tier_badge'] }}">
                            @if($myPerformance['tier'] === 'Diamond Host') 💎
                            @elseif($myPerformance['tier'] === 'Gold Star') ⭐
                            @elseif($myPerformance['tier'] === 'Silver Pro') 🛡️
                            @elseif($myPerformance['tier'] === 'Bronze Host') 🥉
                            @else ✨
                            @endif
                            {{ $myPerformance['tier'] }} &bull; Skor {{ $myPerformance['overall_score'] }}
                        </span>
                    @else
                        <span class="badge badge-secondary" style="font-size: 0.75rem; padding: 0.3rem 0.6rem;">Belum ada sesi tercatat</span>
                    @endif
                </div>
            </div>

            @if($myPerformance)
                <!-- Score Meter & Milestone Target -->
                <div class="my-meter-section">
                    <div class="my-meter-meta">
                        <span class="my-meter-label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Skor Indeks Performa
                        </span>
                        <div class="my-meter-score">
                            <span>{{ $myPerformance['overall_score'] }}</span> / 100
                        </div>
                    </div>
                    <div class="my-meter-track">
                        <div class="my-meter-fill" style="width: {{ min(100, max(5, $myPerformance['overall_score'])) }}%;"></div>
                    </div>
                    <div class="my-meter-hint">
                        <span>Tier: {{ $myPerformance['tier'] }}</span>
                        @if($myPerformance['overall_score'] < 25)
                            <span>{{ 25 - round($myPerformance['overall_score']) }} poin lagi menuju Bronze Host (25)</span>
                        @elseif($myPerformance['overall_score'] < 50)
                            <span>{{ 50 - round($myPerformance['overall_score']) }} poin lagi menuju Silver Pro (50)</span>
                        @elseif($myPerformance['overall_score'] < 70)
                            <span>{{ 70 - round($myPerformance['overall_score']) }} poin lagi menuju Gold Star (70)</span>
                        @elseif($myPerformance['overall_score'] < 85)
                            <span>{{ 85 - round($myPerformance['overall_score']) }} poin lagi menuju Diamond Host (85)</span>
                        @else
                            <span style="color: #6366F1; font-weight: 700;">⭐ Performa Maksimal di Diamond Host!</span>
                        @endif
                    </div>
                </div>

                <!-- 4 KPI Stat Tiles -->
                <div class="my-stats-grid">
                    <!-- Durasi Live -->
                    <div class="my-stat-box">
                        <div class="my-stat-header">
                            <span class="lbl">Durasi Live</span>
                            <div class="my-stat-icon-wrap indigo">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="val" style="color: var(--primary);">{{ $myPerformance['total_hours'] }} Jam</div>
                        <div class="sub">{{ number_format($myPerformance['total_minutes']) }} menit tervalidasi</div>
                    </div>

                    <!-- Penjualan -->
                    <div class="my-stat-box">
                        <div class="my-stat-header">
                            <span class="lbl">Penjualan</span>
                            <div class="my-stat-icon-wrap emerald">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                        </div>
                        <div class="val" style="color: #10B981;">{{ number_format($myPerformance['total_sales']) }} pcs</div>
                        <div class="sub">Avg. {{ $myPerformance['avg_sales'] }} pcs / sesi</div>
                    </div>

                    <!-- Total Konten -->
                    <div class="my-stat-box">
                        <div class="my-stat-header">
                            <span class="lbl">Total Konten</span>
                            <div class="my-stat-icon-wrap amber">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="val" style="color: #F59E0B;">{{ $myPerformance['total_content'] }}</div>
                        <div class="sub">{{ $myPerformance['total_content_edit'] }} edit &bull; {{ $myPerformance['total_content_live'] }} live</div>
                    </div>

                    <!-- Sesi Live -->
                    <div class="my-stat-box">
                        <div class="my-stat-header">
                            <span class="lbl">Sesi Live</span>
                            <div class="my-stat-icon-wrap sky">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="15" height="15">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                                </svg>
                            </div>
                        </div>
                        <div class="val" style="color: #0EA5E9;">{{ $myPerformance['sessions_count'] }}</div>
                        <div class="sub">Avg. {{ $myPerformance['avg_duration_minutes'] }} mnt / sesi</div>
                    </div>
                </div>

                <!-- Footer Action -->
                <div class="my-perf-footer">
                    <div class="my-perf-note">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13" style="color: #10B981;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Data dihitung dari sesi live tervalidasi pada periode terpilih
                    </div>
                    <button type="button" class="btn-detail-perf" onclick="showUserDetail({{ auth()->id() }})">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Lihat Rincian Sesi Saya</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="12" height="12">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            @else
                <div class="text-xs text-muted" style="margin-top: 0.5rem; padding: 0.75rem; background: var(--bg-secondary); border-radius: 8px;">
                    Belum ada data absensi valid untuk Anda pada periode ini. Mulai live streaming dan submit absensi untuk masuk peringkat leaderboard!
                </div>
            @endif
        </div>
    </div>

    <!-- Spotlight Leaders (4 Hall of Fame Cards) -->
    <div class="perf-mvp-grid">
        <!-- MVP Overall -->
        <div class="spotlight-card mvp">
            <div class="spotlight-top">
                <span class="spotlight-category-pill mvp">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    MVP Leader
                </span>
                <span class="spotlight-metric-badge" style="color: #B45309;">
                    Skor {{ $mvpLeader['overall_score'] ?? 0 }}
                </span>
            </div>
            <div class="spotlight-leader-row">
                <div class="spotlight-avatar mvp">
                    {{ substr($mvpLeader['name'] ?? '?', 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="spotlight-name">
                        <span class="truncate">{{ $mvpLeader['name'] ?? '-' }}</span>
                        @if(isset($mvpLeader['user_id']) && $mvpLeader['user_id'] === auth()->id())
                            <span class="me-pill">Anda</span>
                        @endif
                    </div>
                    <div class="text-xs text-muted truncate">{{ $mvpLeader['task'] ?? 'Host Live' }}</div>
                </div>
            </div>
        </div>

        <!-- Top Durasi -->
        <div class="spotlight-card duration">
            <div class="spotlight-top">
                <span class="spotlight-category-pill duration">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Top Jam Live
                </span>
                <span class="spotlight-metric-badge" style="color: var(--primary);">
                    {{ $durationLeader['total_hours'] ?? 0 }} Jam
                </span>
            </div>
            <div class="spotlight-leader-row">
                <div class="spotlight-avatar duration">
                    {{ substr($durationLeader['name'] ?? '?', 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="spotlight-name">
                        <span class="truncate">{{ $durationLeader['name'] ?? '-' }}</span>
                        @if(isset($durationLeader['user_id']) && $durationLeader['user_id'] === auth()->id())
                            <span class="me-pill">Anda</span>
                        @endif
                    </div>
                    <div class="text-xs text-muted truncate">{{ $durationLeader['sessions_count'] ?? 0 }} sesi live</div>
                </div>
            </div>
        </div>

        <!-- Top Penjualan -->
        <div class="spotlight-card sales">
            <div class="spotlight-top">
                <span class="spotlight-category-pill sales">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Top Penjualan
                </span>
                <span class="spotlight-metric-badge" style="color: #059669;">
                    {{ number_format($salesLeader['total_sales'] ?? 0) }} pcs
                </span>
            </div>
            <div class="spotlight-leader-row">
                <div class="spotlight-avatar sales">
                    {{ substr($salesLeader['name'] ?? '?', 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="spotlight-name">
                        <span class="truncate">{{ $salesLeader['name'] ?? '-' }}</span>
                        @if(isset($salesLeader['user_id']) && $salesLeader['user_id'] === auth()->id())
                            <span class="me-pill">Anda</span>
                        @endif
                    </div>
                    <div class="text-xs text-muted truncate">Produk Terjual</div>
                </div>
            </div>
        </div>

        <!-- Top Konten -->
        <div class="spotlight-card content">
            <div class="spotlight-top">
                <span class="spotlight-category-pill content">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Top Konten
                </span>
                <span class="spotlight-metric-badge" style="color: #E11D48;">
                    {{ $contentLeader['total_content'] ?? 0 }} Video
                </span>
            </div>
            <div class="spotlight-leader-row">
                <div class="spotlight-avatar content">
                    {{ substr($contentLeader['name'] ?? '?', 0, 1) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="spotlight-name">
                        <span class="truncate">{{ $contentLeader['name'] ?? '-' }}</span>
                        @if(isset($contentLeader['user_id']) && $contentLeader['user_id'] === auth()->id())
                            <span class="me-pill">Anda</span>
                        @endif
                    </div>
                    <div class="text-xs text-muted truncate">Produksi Konten</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar: Category Tabs + Period Filters -->
    <div class="perf-filter-card">
        <div class="perf-toolbar-container">
            <!-- Category Segmented Control with Icons -->
            <div class="perf-segment-nav">
                <a href="{{ route('user.performance.index', array_merge(request()->all(), ['category' => 'overall'])) }}"
                   data-category="overall" onclick="return switchPerfCategory('overall', event)"
                   class="perf-segment-item {{ $category === 'overall' ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span>Skor Keseluruhan</span>
                </a>
                <a href="{{ route('user.performance.index', array_merge(request()->all(), ['category' => 'duration'])) }}"
                   data-category="duration" onclick="return switchPerfCategory('duration', event)"
                   class="perf-segment-item {{ $category === 'duration' ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Durasi Live</span>
                </a>
                <a href="{{ route('user.performance.index', array_merge(request()->all(), ['category' => 'sales'])) }}"
                   data-category="sales" onclick="return switchPerfCategory('sales', event)"
                   class="perf-segment-item {{ $category === 'sales' ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Penjualan</span>
                </a>
                <a href="{{ route('user.performance.index', array_merge(request()->all(), ['category' => 'content'])) }}"
                   data-category="content" onclick="return switchPerfCategory('content', event)"
                   class="perf-segment-item {{ $category === 'content' ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Konten</span>
                </a>
            </div>

            <!-- Search & Filter Controls -->
            <form action="{{ route('user.performance.index') }}" method="GET" class="perf-filter-form" id="perfFilterForm">
                <input type="hidden" name="category" id="perfCategoryInput" value="{{ $category }}">

                <!-- Search Input -->
                <div class="perf-search-wrap">
                    <svg class="perf-search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="hostTableSearch" class="perf-search-input" placeholder="Cari host...">
                </div>

                <div class="perf-selects-row">
                    <!-- Periode Presets Dropdown -->
                    <select name="period" class="perf-select perf-select-period" onchange="onPerfPeriodChange(this)">
                        @if($period === 'custom')
                            <option value="custom" selected disabled>Filter Bulan/Tahun</option>
                        @endif
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="this_month" {{ $period === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="last_month" {{ $period === 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                        <option value="last_30_days" {{ $period === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="last_7_days" {{ $period === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    </select>

                    <!-- Month / Year Selector -->
                    <select name="month" class="perf-select perf-select-month" onchange="onPerfMonthYearChange(this)">
                        <option value="">Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="year" class="perf-select perf-select-year" onchange="onPerfMonthYearChange(this)">
                        <option value="">Tahun</option>
                        @for($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                @if($selectedMonth || $selectedYear || $period !== 'all')
                    <a href="{{ route('user.performance.index') }}" class="perf-btn-reset" title="Reset Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="13" height="13">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Top 3 Podium Stage (Olympic Creator Stage: Juara 2 | Juara 1 | Juara 3) -->
    @if($leaderboard->isNotEmpty())
    <div class="podium-stage-card">
        <div class="podium-stage-header">
            <div class="podium-title-group">
                <div class="podium-title-icon-box">🏆</div>
                <div>
                    <div class="podium-title-text">Podium 3 Besar</div>
                    <div class="podium-subtitle-text">Peringkat teratas berdasarkan <strong class="perf-cat-label" style="color: var(--primary);">{{ ucfirst($category) }}</strong></div>
                </div>
            </div>
            <span class="podium-badge-indicator">
                <span class="my-status-dot"></span>
                <span>Top 3 Showcase</span>
            </span>
        </div>

        <div class="podium-stage">
            <!-- Juara 2 (Perak) - Left -->
            @if($topSecond)
            <div class="podium-column rank-2 {{ $topSecond['user_id'] === auth()->id() ? 'is-me' : '' }}"
                 data-podium-rank="2"
                 onclick="showUserDetail({{ $topSecond['user_id'] }})"
                 title="Klik untuk melihat detail performa {{ $topSecond['name'] }}">
                <div class="podium-avatar-wrapper">
                    <div class="podium-avatar">
                        <span class="podium-avatar-text">{{ substr($topSecond['name'], 0, 1) }}</span>
                    </div>
                    <div class="podium-medal">🥈</div>
                </div>
                <div class="podium-name">
                    <span class="podium-name-text">{{ $topSecond['name'] }}</span>
                </div>
                <div class="me-pill-wrap">
                    @if($topSecond['user_id'] === auth()->id())
                        <span class="me-pill">Anda</span>
                    @endif
                </div>
                <span class="podium-score-pill">
                    <span class="podium-score-val">
                        @if($category === 'duration') {{ $topSecond['total_hours'] }} Jam
                        @elseif($category === 'sales') {{ number_format($topSecond['total_sales']) }} pcs
                        @elseif($category === 'content') {{ $topSecond['total_content'] }} Konten
                        @else Skor {{ $topSecond['overall_score'] }}
                        @endif
                    </span>
                </span>
                <div class="podium-pedestal">
                    <span class="podium-step-badge">Juara 2</span>
                    <div class="podium-stats-sub">
                        <span>{{ $topSecond['total_hours'] }}j</span> &bull; <span>{{ number_format($topSecond['total_sales']) }} pcs</span>
                    </div>
                    <button type="button" class="podium-view-btn">Detail &rarr;</button>
                </div>
            </div>
            @endif

            <!-- Juara 1 (Emas / MVP) - Center Raised -->
            @if($topFirst)
            <div class="podium-column rank-1 {{ $topFirst['user_id'] === auth()->id() ? 'is-me' : '' }}"
                 data-podium-rank="1"
                 onclick="showUserDetail({{ $topFirst['user_id'] }})"
                 title="Klik untuk melihat detail performa {{ $topFirst['name'] }}">
                <div class="podium-avatar-wrapper">
                    <div class="podium-crown">👑</div>
                    <div class="podium-avatar">
                        <span class="podium-avatar-text">{{ substr($topFirst['name'], 0, 1) }}</span>
                    </div>
                    <div class="podium-medal">🥇</div>
                </div>
                <div class="podium-name">
                    <span class="podium-name-text">{{ $topFirst['name'] }}</span>
                </div>
                <div class="me-pill-wrap">
                    @if($topFirst['user_id'] === auth()->id())
                        <span class="me-pill">Anda</span>
                    @endif
                </div>
                <span class="podium-score-pill">
                    <span class="podium-score-val">
                        @if($category === 'duration') {{ $topFirst['total_hours'] }} Jam
                        @elseif($category === 'sales') {{ number_format($topFirst['total_sales']) }} pcs
                        @elseif($category === 'content') {{ $topFirst['total_content'] }} Konten
                        @else Skor {{ $topFirst['overall_score'] }}
                        @endif
                    </span>
                </span>
                <div class="podium-pedestal">
                    <span class="podium-step-badge">Juara 1 (MVP)</span>
                    <div class="podium-stats-sub">
                        <span>{{ $topFirst['total_hours'] }}j</span> &bull; <span>{{ number_format($topFirst['total_sales']) }} pcs</span>
                    </div>
                    <button type="button" class="podium-view-btn">Detail &rarr;</button>
                </div>
            </div>
            @endif

            <!-- Juara 3 (Perunggu) - Right -->
            @if($topThird)
            <div class="podium-column rank-3 {{ $topThird['user_id'] === auth()->id() ? 'is-me' : '' }}"
                 data-podium-rank="3"
                 onclick="showUserDetail({{ $topThird['user_id'] }})"
                 title="Klik untuk melihat detail performa {{ $topThird['name'] }}">
                <div class="podium-avatar-wrapper">
                    <div class="podium-avatar">
                        <span class="podium-avatar-text">{{ substr($topThird['name'], 0, 1) }}</span>
                    </div>
                    <div class="podium-medal">🥉</div>
                </div>
                <div class="podium-name">
                    <span class="podium-name-text">{{ $topThird['name'] }}</span>
                </div>
                <div class="me-pill-wrap">
                    @if($topThird['user_id'] === auth()->id())
                        <span class="me-pill">Anda</span>
                    @endif
                </div>
                <span class="podium-score-pill">
                    <span class="podium-score-val">
                        @if($category === 'duration') {{ $topThird['total_hours'] }} Jam
                        @elseif($category === 'sales') {{ number_format($topThird['total_sales']) }} pcs
                        @elseif($category === 'content') {{ $topThird['total_content'] }} Konten
                        @else Skor {{ $topThird['overall_score'] }}
                        @endif
                    </span>
                </span>
                <div class="podium-pedestal">
                    <span class="podium-step-badge">Juara 3</span>
                    <div class="podium-stats-sub">
                        <span>{{ $topThird['total_hours'] }}j</span> &bull; <span>{{ number_format($topThird['total_sales']) }} pcs</span>
                    </div>
                    <button type="button" class="podium-view-btn">Detail &rarr;</button>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Leaderboard Table (Desktop View) -->
    <div class="card mb-4 hide-on-mobile" style="border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <div class="card-header flex justify-between items-center" style="padding: 0.85rem 1.15rem; background: var(--surface);">
            <div>
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 700; margin: 0;">Peringkat Host</h3>
                <span class="text-xs text-muted">{{ $filterLabel }} &bull; Diurutkan berdasarkan: <strong class="perf-cat-label" style="color: var(--primary);">{{ ucfirst($category) }}</strong></span>
            </div>
            <span class="badge badge-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 6px;">{{ $leaderboard->count() }} Host Aktif</span>
        </div>

        <div class="table-container">
            <table class="table" id="hostPerformanceTable">
                <thead>
                    <tr>
                        <th style="width: 55px; text-align: center;">#</th>
                        <th>Host</th>
                        <th>Durasi Live</th>
                        <th>Penjualan</th>
                        <th>Konten</th>
                        <th>Skor Performa</th>
                        <th style="width: 80px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="desktopLeaderboardBody">
                    @forelse($leaderboard as $item)
                        <tr class="host-row {{ $item['user_id'] === auth()->id() ? 'is-me' : '' }}"
                            data-user-id="{{ $item['user_id'] }}"
                            data-name="{{ strtolower($item['name']) }}"
                            data-overall="{{ $item['overall_score'] }}"
                            data-minutes="{{ $item['total_minutes'] }}"
                            data-sales="{{ $item['total_sales'] }}"
                            data-content="{{ $item['total_content'] }}">
                            <!-- Rank Number -->
                            <td style="text-align: center;">
                                <span class="rank-num {{ $item['rank'] <= 3 ? 'rank-' . $item['rank'] : '' }}">
                                    {{ $item['rank'] }}
                                </span>
                            </td>

                            <!-- User Info -->
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar avatar-sm" style="font-weight: 700;">
                                        {{ substr($item['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">
                                            {{ $item['name'] }}
                                            @if($item['user_id'] === auth()->id())
                                                <span class="me-pill">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-muted">{{ $item['task'] }} &bull; {{ $item['sessions_count'] }} sesi</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Duration -->
                            <td>
                                <div class="font-semibold text-sm" style="color: var(--primary);">{{ $item['total_hours'] }} Jam</div>
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
                                <div class="font-semibold text-sm" style="color: #F59E0B;">{{ $item['total_content'] }}</div>
                                <div class="text-xs text-muted">{{ $item['total_content_edit'] }} edit &bull; {{ $item['total_content_live'] }} live</div>
                            </td>

                            <!-- Score & Tier -->
                            <td style="min-width: 150px;">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="my-tier-badge {{ $item['tier_badge'] }}" style="font-size: 0.68rem; padding: 0.15rem 0.45rem;">
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
                                <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 6px;" onclick="showUserDetail({{ $item['user_id'] }})">
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
            </table>
        </div>
    </div>

    <!-- Mobile Leaderboard Host Cards -->
    <div class="card mb-4 hide-on-desktop" style="border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <div class="card-header flex justify-between items-center" style="padding: 0.75rem 0.85rem; background: var(--surface);">
            <div>
                <h3 class="card-title" style="font-size: 0.9rem; font-weight: 700; margin: 0;">Peringkat Host</h3>
                <span class="text-xs text-muted">{{ $filterLabel }} &bull; <strong class="perf-cat-label" style="color: var(--primary);">{{ ucfirst($category) }}</strong></span>
            </div>
            <span class="badge badge-secondary" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; border-radius: 6px;">{{ $leaderboard->count() }} Host</span>
        </div>

        <div id="mobileLeaderboardBody" style="padding: 0.6rem; display: flex; flex-direction: column; gap: 0.6rem;">
            @forelse($leaderboard as $item)
                <div class="perf-mobile-card host-row {{ $item['user_id'] === auth()->id() ? 'is-me' : '' }}"
                     data-user-id="{{ $item['user_id'] }}"
                     data-name="{{ strtolower($item['name']) }}"
                     data-overall="{{ $item['overall_score'] }}"
                     data-minutes="{{ $item['total_minutes'] }}"
                     data-sales="{{ $item['total_sales'] }}"
                     data-content="{{ $item['total_content'] }}">
                    <div class="perf-mobile-card-top">
                        <div class="flex items-center gap-2" style="min-width: 0;">
                            <span class="rank-num {{ $item['rank'] <= 3 ? 'rank-' . $item['rank'] : '' }}" style="flex-shrink: 0;">
                                {{ $item['rank'] }}
                            </span>
                            <div class="avatar avatar-sm" style="flex-shrink: 0; width: 34px; height: 34px; font-size: 0.82rem; font-weight: 700;">
                                {{ substr($item['name'], 0, 1) }}
                            </div>
                            <div style="min-width: 0;">
                                <div class="font-bold text-sm text-truncate">
                                    {{ $item['name'] }}
                                    @if($item['user_id'] === auth()->id())
                                        <span class="me-pill">Anda</span>
                                    @endif
                                </div>
                                <div class="text-xs text-muted text-truncate">{{ $item['task'] }} &bull; {{ $item['sessions_count'] }} sesi</div>
                            </div>
                        </div>
                        <div style="text-align: right; flex-shrink: 0;">
                            <div class="flex items-center gap-1 justify-end">
                                <span class="my-tier-badge {{ $item['tier_badge'] }}" style="font-size: 0.65rem; padding: 0.12rem 0.4rem;">
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
                            <span class="perf-stat-val" style="color: var(--primary);">{{ $item['total_hours'] }} Jam</span>
                            <span class="perf-stat-sub">{{ $item['avg_duration_minutes'] }}m/sesi</span>
                        </div>
                        <div class="perf-stat-col">
                            <span class="perf-stat-lbl">Penjualan</span>
                            <span class="perf-stat-val" style="color: #059669;">{{ number_format($item['total_sales']) }} pcs</span>
                            <span class="perf-stat-sub">avg {{ $item['avg_sales'] }}</span>
                        </div>
                        <div class="perf-stat-col">
                            <span class="perf-stat-lbl">Konten</span>
                            <span class="perf-stat-val" style="color: #F59E0B;">{{ $item['total_content'] }}</span>
                            <span class="perf-stat-sub">{{ $item['total_content_edit'] }}e / {{ $item['total_content_live'] }}l</span>
                        </div>
                    </div>

                    <div>
                        <button type="button" class="btn btn-secondary btn-sm w-100" style="height: 32px; font-size: 0.78rem; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;" onclick="showUserDetail({{ $item['user_id'] }})">
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

    <!-- Comparison Chart -->
    <div class="card mb-4 chart-card" style="border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <div class="card-header flex justify-between items-center" style="padding: 0.85rem 1.15rem; background: var(--surface);">
            <div>
                <h3 class="card-title" style="font-size: 0.95rem; font-weight: 700; margin: 0;">Grafik Perbandingan Top Host</h3>
                <span class="text-xs text-muted">Komparasi metrik jam live, penjualan (pcs), dan produksi konten</span>
            </div>
            <span class="badge badge-secondary" style="font-size: 0.72rem; padding: 0.2rem 0.5rem; border-radius: 6px;">Chart</span>
        </div>
        <div class="card-body" style="padding: 1.15rem;">
            <div class="chart-container" style="position: relative; height: 230px;">
                <canvas id="topPerformersChart"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- Modal Drilldown Detail Host -->
<div class="modal-overlay" id="hostDetailModal">
    <div class="modal" style="max-width: 680px; width: 95%; border-radius: 14px;">
        <div class="modal-header" style="border-bottom: 1px solid var(--border); padding: 0.85rem 1.15rem;">
            <div>
                <h3 class="modal-title" id="modalHostName" style="font-size: 1.1rem; font-weight: 700;">Rincian Host</h3>
                <div class="text-xs text-muted" id="modalHostMeta">Memuat data...</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal(document.getElementById('hostDetailModal'))">&times;</button>
        </div>

        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding: 1rem 1.15rem;">
            <!-- Loading Indicator -->
            <div id="modalLoading" class="text-center" style="padding: 2.5rem 0;">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem; border-width: 3px; display: inline-block; margin-bottom: 0.5rem;"></div>
                <div class="text-sm text-muted">Mengambil data sesi live &amp; performa...</div>
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
                        <div class="modal-stat-val" id="modalTotalContent" style="color: #F59E0B;">0</div>
                        <div class="modal-stat-lbl">Total Konten</div>
                    </div>
                    <div class="modal-stat-box">
                        <div class="modal-stat-val" id="modalSessionsCount" style="color: #0EA5E9;">0</div>
                        <div class="modal-stat-lbl">Sesi Live</div>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-2">
                    <span class="font-bold text-xs text-muted uppercase" style="letter-spacing: 0.03em;">Riwayat Sesi Live</span>
                    <span class="text-xs text-muted" id="modalSessionNote"></span>
                </div>

                <!-- Session Attendance Table -->
                <div class="table-container" style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border); border-radius: 10px;">
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

        <div class="modal-footer flex justify-between items-center" style="border-top: 1px solid var(--border); padding: 0.75rem 1.15rem;">
            <span class="text-xs text-muted">Data berdasarkan absensi yang tercatat</span>
            <button type="button" class="btn btn-secondary btn-sm" style="border-radius: 6px; padding: 0.35rem 0.85rem;" onclick="closeModal(document.getElementById('hostDetailModal'))">Tutup</button>
        </div>
    </div>
</div>

@include('partials.scoring-rules-modal')
@endsection

@push('scripts')
<script>
    // Leaderboard data in memory for 0ms instant category switching
    window.__perfHostsData = @json($leaderboard->map(fn($item) => collect($item)->except(['user', 'email', 'phone'])->all())->values());
    window.__perfCurrentUserId = {{ (int) auth()->id() }};

    function onPerfPeriodChange(selectEl) {
        const form = selectEl.form;
        if (!form) return;
        const monthEl = form.querySelector('[name="month"]');
        const yearEl = form.querySelector('[name="year"]');
        if (monthEl) monthEl.value = '';
        if (yearEl) yearEl.value = '';
        if (window.navigateFilterForm) {
            window.navigateFilterForm(form);
        } else {
            form.submit();
        }
    }

    function onPerfMonthYearChange(selectEl) {
        const form = selectEl.form;
        if (!form) return;
        const monthEl = form.querySelector('[name="month"]');
        const yearEl = form.querySelector('[name="year"]');
        if (monthEl && monthEl.value && yearEl && !yearEl.value) {
            yearEl.value = '{{ now()->year }}';
        }
        if (window.navigateFilterForm) {
            window.navigateFilterForm(form);
        } else {
            form.submit();
        }
    }

    function compareHostsByCategory(a, b, category) {
        if (category === 'duration') {
            if (b.total_minutes !== a.total_minutes) return b.total_minutes - a.total_minutes;
            return b.total_sales - a.total_sales;
        } else if (category === 'sales') {
            if (b.total_sales !== a.total_sales) return b.total_sales - a.total_sales;
            return b.total_minutes - a.total_minutes;
        } else if (category === 'content') {
            if (b.total_content !== a.total_content) return b.total_content - a.total_content;
            return b.total_sales - a.total_sales;
        } else {
            if (b.overall_score !== a.overall_score) return b.overall_score - a.overall_score;
            return b.total_sales - a.total_sales;
        }
    }

    function switchPerfCategory(category, event) {
        if (event) {
            event.preventDefault();
        }

        // 1. Update active tab pill immediately
        document.querySelectorAll('.perf-segment-item').forEach(tab => {
            tab.classList.toggle('active', tab.getAttribute('data-category') === category);
        });

        // 2. Update hidden form input & category label
        const catInput = document.getElementById('perfCategoryInput');
        if (catInput) catInput.value = category;

        const catLabel = category.charAt(0).toUpperCase() + category.slice(1);
        document.querySelectorAll('.perf-cat-label').forEach(el => {
            el.textContent = catLabel;
        });

        // 3. Sort desktop table rows & re-assign rank badges
        const desktopBody = document.getElementById('desktopLeaderboardBody');
        if (desktopBody) {
            const rows = Array.from(desktopBody.querySelectorAll('tr.host-row'));
            rows.sort((rA, rB) => {
                const a = {
                    overall_score: parseFloat(rA.dataset.overall || 0),
                    total_minutes: parseInt(rA.dataset.minutes || 0, 10),
                    total_sales: parseInt(rA.dataset.sales || 0, 10),
                    total_content: parseInt(rA.dataset.content || 0, 10),
                };
                const b = {
                    overall_score: parseFloat(rB.dataset.overall || 0),
                    total_minutes: parseInt(rB.dataset.minutes || 0, 10),
                    total_sales: parseInt(rB.dataset.sales || 0, 10),
                    total_content: parseInt(rB.dataset.content || 0, 10),
                };
                return compareHostsByCategory(a, b, category);
            });
            rows.forEach((row, idx) => {
                const rank = idx + 1;
                const badge = row.querySelector('.rank-num');
                if (badge) {
                    badge.textContent = rank;
                    badge.className = 'rank-num' + (rank <= 3 ? ' rank-' + rank : '');
                }
                desktopBody.appendChild(row);
            });
        }

        // 4. Sort mobile cards & re-assign rank badges
        const mobileBody = document.getElementById('mobileLeaderboardBody');
        if (mobileBody) {
            const cards = Array.from(mobileBody.querySelectorAll('.perf-mobile-card.host-row'));
            cards.sort((cA, cB) => {
                const a = {
                    overall_score: parseFloat(cA.dataset.overall || 0),
                    total_minutes: parseInt(cA.dataset.minutes || 0, 10),
                    total_sales: parseInt(cA.dataset.sales || 0, 10),
                    total_content: parseInt(cA.dataset.content || 0, 10),
                };
                const b = {
                    overall_score: parseFloat(cB.dataset.overall || 0),
                    total_minutes: parseInt(cB.dataset.minutes || 0, 10),
                    total_sales: parseInt(cB.dataset.sales || 0, 10),
                    total_content: parseInt(cB.dataset.content || 0, 10),
                };
                return compareHostsByCategory(a, b, category);
            });
            cards.forEach((card, idx) => {
                const rank = idx + 1;
                const badge = card.querySelector('.rank-num');
                if (badge) {
                    badge.textContent = rank;
                    badge.className = 'rank-num' + (rank <= 3 ? ' rank-' + rank : '');
                }
                mobileBody.appendChild(card);
            });
        }

        // 5. Update Podium 3 Besar & Chart from sorted data
        if (Array.isArray(window.__perfHostsData) && window.__perfHostsData.length > 0) {
            const sorted = [...window.__perfHostsData].sort((a, b) => compareHostsByCategory(a, b, category));

            // Update Podium Columns
            const podiumCols = document.querySelectorAll('.podium-column');
            podiumCols.forEach(col => {
                const rank = parseInt(col.getAttribute('data-podium-rank'), 10);
                const host = sorted[rank - 1];
                if (!host) {
                    col.style.display = 'none';
                    return;
                }
                col.style.display = '';

                col.classList.toggle('is-me', host.user_id === window.__perfCurrentUserId);
                col.setAttribute('onclick', `showUserDetail(${host.user_id})`);
                col.setAttribute('title', `Klik untuk melihat detail performa ${host.name}`);

                const avatarEl = col.querySelector('.podium-avatar-text');
                if (avatarEl) avatarEl.textContent = (host.name || '?').substring(0, 1);

                const nameEl = col.querySelector('.podium-name-text');
                if (nameEl) nameEl.textContent = host.name;

                const mePill = col.querySelector('.me-pill-wrap');
                if (mePill) {
                    mePill.innerHTML = host.user_id === window.__perfCurrentUserId ? '<span class="me-pill">Anda</span>' : '';
                }

                const scoreEl = col.querySelector('.podium-score-val');
                if (scoreEl) {
                    if (category === 'duration') {
                        scoreEl.textContent = `${host.total_hours} Jam`;
                    } else if (category === 'sales') {
                        scoreEl.textContent = `${Number(host.total_sales).toLocaleString()} pcs`;
                    } else if (category === 'content') {
                        scoreEl.textContent = `${host.total_content} Konten`;
                    } else {
                        scoreEl.textContent = `Skor ${host.overall_score}`;
                    }
                }

                const statsEl = col.querySelector('.podium-stats-sub');
                if (statsEl) {
                    statsEl.innerHTML = `<span>${host.total_hours}j</span> &bull; <span>${Number(host.total_sales).toLocaleString()} pcs</span>`;
                }
            });

            // Update Chart top 7
            const top7 = sorted.slice(0, 7);
            const chartInstance = window.Chart && document.getElementById('topPerformersChart')
                ? Chart.getChart(document.getElementById('topPerformersChart'))
                : null;
            if (chartInstance) {
                chartInstance.data.labels = top7.map(h => h.name);
                chartInstance.data.datasets[0].data = top7.map(h => h.total_hours);
                chartInstance.data.datasets[1].data = top7.map(h => h.total_sales);
                chartInstance.data.datasets[2].data = top7.map(h => h.total_content);
                chartInstance.update('none');
            }
        }

        // 6. Update URL query parameter without reloading
        const url = new URL(window.location.href);
        url.searchParams.set('category', category);
        window.history.replaceState({}, '', url.toString());

        return false;
    }

    // Real-time Search in Leaderboard
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
        if (ctxPerformer && chartData && chartData.labels && chartData.labels.length > 0 && window.Chart) {
            Chart.getChart(ctxPerformer)?.destroy();
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

        let url = `/user/performance/user/${userId}?status=${encodeURIComponent(status)}`;
        if (startDate && endDate) {
            url += `&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    title.textContent = data.user.name;
                    meta.textContent = `${data.user.task}`;

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
