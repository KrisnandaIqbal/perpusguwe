<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/opac_style.css') ?>?v=<?= time() ?>">
<noscript><style>.ob-panel[hidden] { display: block; }</style></noscript>
<style>
    /* Inisialisasi variabel dan base styling tema baru (diselaraskan dengan brand Kemnaker) */
    :root {
        --opac-primary: #0f3fb4;
        --opac-primary-dark: #0a2f8c;
        --opac-primary-light: #2563eb;
        --opac-primary-subtle: #eff6ff;
        --opac-card-bg: rgba(255, 255, 255, 0.85);
        --opac-card-bg-hover: #ffffff;
        --opac-card-border: #e2e8f0;
        --opac-callout-bg: rgba(255, 255, 255, 0.85);
        --opac-callout-border: #e2e8f0;
        --opac-text-primary: #1e293b;
        --opac-text-secondary: #64748b;
        --opac-text-muted: #8c96a3;
        --opac-border-subtle: #e2e8f0;
    }

    html, body {
        overflow-x: hidden;
        max-width: 100vw;
    }

    body {
        background-color: #fafaf9;
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--opac-text-primary);
    }

    /* ─── Top Hero / Header OPAC (Sesuai Desain Editorial) ─── */
    .opac-hero-section {
        background: #ffffff;
        border-bottom: 1px solid var(--opac-border-subtle);
        padding-top: 96px; /* Clearance navbar fixed */
        padding-bottom: 40px;
        position: relative;
        z-index: 20;
        width: 100%;
        overflow-x: clip;
    }

    .opac-hero-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: clamp(24px, 4vw, 54px);
        align-items: center;
        min-width: 0;
    }

    .opac-hero-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
        max-width: 100%;
    }

    .opac-hero-kicker {
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: var(--opac-primary);
        margin-bottom: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .opac-hero-title {
        font-family: 'Source Serif 4', Georgia, serif;
        font-size: clamp(34px, 3.8vw, 54px);
        line-height: 1.12;
        font-weight: 700;
        color: #111827;
        margin-bottom: 18px;
        letter-spacing: -0.02em;
    }

    .opac-hero-desc {
        font-size: 1.02rem;
        line-height: 1.65;
        color: #4b5563;
        margin-bottom: 28px;
        max-width: 580px;
    }

    /* Search Bar Besar Form */
    .opac-search-hero-box {
        position: relative;
        width: 100%;
        max-width: 580px;
        margin-bottom: 24px;
    }

    .opac-search-input-wrap {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid #d1d5db;
        border-radius: 14px;
        padding: 5px 6px 5px 18px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
    }

    .opac-search-input-wrap:focus-within {
        border-color: var(--opac-primary);
        box-shadow: 0 0 0 4px rgba(15, 63, 180, 0.12), 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .opac-search-input-wrap i.search-icon {
        color: #9ca3af;
        font-size: 1.05rem;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .opac-search-input-wrap input[name="search"] {
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.98rem;
        color: var(--opac-text-primary);
        width: 100%;
        padding: 9px 4px;
    }

    .opac-search-input-wrap input[name="search"]::placeholder {
        color: #9ca3af;
    }

    .opac-search-btn-submit {
        background-color: var(--opac-primary);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 10px 24px;
        font-size: 0.92rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.18s ease, transform 0.15s ease;
        white-space: nowrap;
        cursor: pointer;
        flex-shrink: 0;
    }

    .opac-search-btn-submit:hover {
        background-color: var(--opac-primary-dark);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* Mood / Nuansa Filter Row */
    .opac-mood-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .opac-mood-label {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 500;
    }

    .opac-random-link {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--opac-primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s ease, transform 0.15s ease;
    }

    .opac-random-link:hover {
        color: var(--opac-primary-dark);
        text-decoration: underline;
        transform: translateY(-1px);
    }

    /* Category Pills Navigation */
    .opac-category-track {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        padding: 2px 2px 4px;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        min-width: 0;
    }

    .opac-category-track::-webkit-scrollbar {
        display: none;
    }

    .opac-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.86rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #4b5563;
        cursor: pointer;
    }

    .opac-pill-btn:hover {
        border-color: #cbd5e1;
        color: #111827;
        background: #ffffff;
    }

    .opac-pill-btn.active {
        background-color: #111827;
        color: #ffffff;
        border-color: #111827;
        box-shadow: 0 3px 8px rgba(17, 24, 39, 0.2);
    }

    /* Card Foto Suasana Perpustakaan (Kanan Hero) */
    .opac-hero-media {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 380px;
        display: flex;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .opac-hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .opac-hero-media:hover img {
        transform: scale(1.025);
    }


    .opac-pill-dropdown .dropdown-toggle::after {
        margin-left: 6px;
    }

    .opac-dropdown-menu {
        border-radius: 12px;
        border: 1px solid var(--opac-border-subtle);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        padding: 8px;
        min-width: 200px;
    }

    .opac-dropdown-menu .dropdown-item {
        font-size: 0.85rem;
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: 500;
        color: #334155;
    }

    .opac-dropdown-menu .dropdown-item:hover,
    .opac-dropdown-menu .dropdown-item.active {
        background-color: var(--opac-primary-subtle);
        color: var(--opac-primary);
        font-weight: 600;
    }

    /* ─── Main Content Layout ─── */
    .opac-main-container {
        padding-top: 16px;
        padding-bottom: 48px;
    }

    /* Sidebar Filter (Desktop) */
    .opac-sidebar-wrap {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--opac-border-subtle);
        padding: 20px 18px;
    }

    @media (min-width: 992px) {
        .opac-sidebar-drawer {
            position: static !important;
            transform: none !important;
            visibility: visible !important;
            opacity: 1 !important;
            bottom: auto !important;
            left: auto !important;
            right: auto !important;
            max-height: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            z-index: auto !important;
            overflow: visible !important;
            background: transparent !important;
        }

        .opac-sidebar-wrap {
            position: sticky;
            top: 84px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .opac-sidebar-wrap::-webkit-scrollbar {
            width: 5px;
        }

        .opac-sidebar-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .opac-sidebar-wrap::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 999px;
        }

        .opac-sidebar-wrap::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    }

    .opac-filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--opac-border-subtle);
        margin-bottom: 16px;
    }

    .opac-filter-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .opac-filter-reset {
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .opac-filter-reset:hover {
        color: var(--opac-primary);
        text-decoration: underline;
    }

    .opac-filter-group {
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .opac-filter-group:last-of-type {
        border-bottom: none;
        margin-bottom: 12px;
        padding-bottom: 0;
    }

    .opac-filter-toggle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        background: transparent;
        border: none;
        padding: 0;
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        cursor: pointer;
        text-align: left;
        margin-bottom: 10px;
    }

    .opac-filter-toggle i {
        font-size: 0.72rem;
        color: #94a3b8;
        transition: transform 0.2s ease;
    }

    .opac-filter-toggle.collapsed i {
        transform: rotate(-90deg);
    }

    .opac-checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 0.84rem;
        color: #334155;
        cursor: pointer;
        user-select: none;
    }

    .opac-checkbox-item:last-child {
        margin-bottom: 0;
    }

    .opac-checkbox-item input[type="radio"],
    .opac-checkbox-item input[type="checkbox"] {
        accent-color: var(--opac-primary);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .opac-year-range-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .opac-year-input {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 0.84rem;
        color: #1e293b;
        outline: none;
        background: #ffffff;
    }

    .opac-year-input:focus {
        border-color: var(--opac-primary);
    }

    .opac-btn-apply-year {
        background-color: transparent;
        border: 1px solid var(--opac-primary);
        color: var(--opac-primary);
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        border-radius: 999px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .opac-btn-apply-year:hover {
        background-color: var(--opac-primary);
        color: #ffffff;
    }

    .opac-mobile-filter-btn {
        background-color: #ffffff;
        border: 1px solid var(--opac-border-subtle);
        color: var(--opac-text-primary);
        font-size: 0.82rem;
        border-radius: 999px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .opac-mobile-filter-btn:hover,
    .opac-mobile-filter-btn:focus {
        border-color: var(--opac-primary);
        color: var(--opac-primary);
    }

    /* Usulkan Buku Callout Card */
    .opac-callout-card {
        background: var(--opac-callout-bg);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid var(--opac-callout-border);
        border-radius: 4px;
        padding: 16px 14px;
        margin-top: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .opac-callout-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
        line-height: 1.35;
    }

    .opac-callout-desc {
        font-size: 0.76rem;
        color: #64748b;
        margin-bottom: 10px;
    }

    .opac-callout-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        text-decoration: none;
        transition: gap 0.2s ease, color 0.2s ease;
    }

    .opac-callout-link:hover {
        color: var(--opac-primary);
        gap: 9px;
    }

    /* ─── Right Content: Header & Catalog Grid ─── */
    .opac-catalog-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--opac-border-subtle);
    }

    .opac-section-heading {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
        margin-bottom: 2px;
    }

    .opac-section-subheading {
        font-size: 0.84rem;
        color: var(--opac-text-secondary);
        margin: 0;
    }

    .opac-header-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        max-width: 100%;
    }

    .opac-sort-label {
        font-size: 0.82rem;
        color: var(--opac-text-secondary);
        margin-right: 4px;
        white-space: nowrap;
    }

    .opac-sort-select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.82rem;
        font-weight: 500;
        color: #334155;
        background: #ffffff;
        cursor: pointer;
        outline: none;
    }

    .opac-sort-select:focus {
        border-color: var(--opac-primary);
    }

    .opac-view-switch-btn {
        width: 34px;
        height: 34px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        border-radius: 8px;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .opac-view-switch-btn:hover {
        background: #f8fafc;
        color: #1e293b;
    }

    .opac-view-switch-btn.active {
        background: #ffffff;
        color: var(--opac-primary);
        border-color: var(--opac-primary);
    }

    /* ─── Book Card (Sesuai Desain Screenshot) ─── */
    .opac-warm-card {
        background-color: var(--opac-card-bg);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 4px;
        border: 1px solid var(--opac-card-border);
        padding: 12px;
        transition: transform 0.22s ease, box-shadow 0.22s ease, background-color 0.22s ease, border-color 0.22s ease;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .opac-warm-card:hover {
        transform: translateY(-3px);
        background-color: var(--opac-card-bg-hover);
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .opac-cover-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .opac-cover-stage {
        width: 100%;
        display: block;
    }

    .opac-book-3d {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1.45;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        transition: transform 0.22s ease;
    }

    .opac-warm-card:hover .opac-book-3d {
        transform: scale(1.015);
    }

    .opac-real-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 8px;
    }

    /* Kicker Kategori Uppercase */
    .opac-book-kicker {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        color: var(--opac-text-muted);
        text-transform: uppercase;
        line-height: 1.2;
    }

    /* Judul Buku */
    .opac-book-heading {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--opac-text-primary);
        line-height: 1.35;
        margin-top: 2px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.45rem;
    }

    .opac-book-heading a {
        color: var(--opac-text-primary);
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .opac-book-heading a:hover {
        color: var(--opac-primary);
    }

    /* Pengarang */
    .opac-book-author {
        font-size: 0.8rem;
        color: var(--opac-text-secondary);
        font-weight: 500;
        line-height: 1.3;
    }

    /* Status Badge */
    .opac-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 600;
        line-height: 1.25;
        white-space: nowrap;
    }

    .opac-status-badge.badge-available {
        background-color: #e6f4ea;
        color: #137333;
    }

    .opac-status-badge.badge-borrowed {
        background-color: #fef3c7;
        color: #b45309;
    }

    /* ─── Mode Daftar (List View) ─── */
    .opac-catalog-grid.view-list > .opac-col-book {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }

    .opac-catalog-grid.view-list .opac-warm-card {
        flex-direction: row !important;
        align-items: flex-start;
        gap: 16px;
        padding: 14px;
    }

    .opac-catalog-grid.view-list .opac-cover-wrapper {
        width: 105px !important;
        max-width: 105px !important;
        flex-shrink: 0;
    }

    .opac-catalog-grid.view-list .opac-book-3d {
        aspect-ratio: 1 / 1.45;
    }

    /* ─── Pagination Desain Baru ─── */
    .opac-pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 36px;
        padding-top: 20px;
        border-top: 1px solid var(--opac-border-subtle);
    }

    .opac-page-info {
        font-size: 0.84rem;
        color: var(--opac-text-secondary);
        margin: 0;
    }

    .opac-custom-pagination .page-item .page-link {
        border: none;
        background: transparent;
        color: #475569;
        font-size: 0.88rem;
        font-weight: 600;
        min-width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.18s ease;
    }

    .opac-custom-pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }

    .opac-custom-pagination .page-item.active .page-link {
        background-color: var(--opac-primary);
        color: #ffffff;
        box-shadow: 0 3px 8px rgba(15, 63, 180, 0.25);
    }

    .opac-custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        cursor: not-allowed;
    }

    /* ─── Mode Responsive: Tablet & HP ─── */
    @media (max-width: 991.98px) {
        .opac-hero-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 28px;
            min-width: 0;
        }

        .opac-hero-content {
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }

        .opac-search-hero-box {
            max-width: 100%;
        }

        .opac-hero-media {
            display: none;
        }
    }

    @media (max-width: 767.98px) {
        .opac-hero-section {
            padding-top: calc(16px + env(safe-area-inset-top, 0px)) !important;
            padding-bottom: 14px !important;
        }

        .opac-hero-grid {
            grid-template-columns: minmax(0, 1fr);
            min-width: 0;
        }

        .opac-hero-content {
            min-width: 0;
            max-width: 100%;
        }

        .opac-hero-kicker {
            font-size: 0.72rem;
            margin-bottom: 4px;
            word-break: normal;
        }

        .opac-hero-title {
            font-size: clamp(1.32rem, 1.05rem + 1.5vw, 1.65rem);
            line-height: 1.18;
            margin-bottom: 12px;
            word-break: normal;
            overflow-wrap: break-word;
            text-wrap: balance;
        }

        .opac-hero-desc {
            display: none;
        }

        .opac-search-hero-box {
            width: 100%;
            max-width: 100%;
            margin-bottom: 12px;
        }

        .opac-search-input-wrap {
            width: 100%;
            max-width: 100%;
            padding: 4px 4px 4px 12px;
            border-radius: 12px;
            min-height: 44px;
        }

        .opac-search-input-wrap i.search-icon {
            font-size: 0.9rem;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .opac-search-input-wrap input[name="search"] {
            font-size: 0.94rem;
            padding: 6px 4px;
            min-width: 0;
            flex: 1 1 auto;
        }

        .opac-year-input {
            font-size: 1rem;
        }

        .opac-search-btn-submit {
            padding: 8px 14px;
            min-height: 36px;
            font-size: 0.82rem;
            border-radius: 9px;
            flex-shrink: 0;
        }

        .opac-mood-row {
            margin-bottom: 6px;
            font-size: 0.75rem;
            width: 100%;
            max-width: 100%;
        }

        .opac-category-track {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            gap: 6px;
        }

        .opac-mood-label {
            font-size: 0.75rem;
        }

        .opac-random-link {
            font-size: 0.75rem;
        }

        .opac-pill-btn {
            padding: 6px 12px;
            font-size: 0.76rem;
            border-radius: 9px;
        }

        .opac-hero-media {
            display: none; /* Sembunyikan foto di HP agar fokus langsung ke katalog buku */
        }

        .opac-main-container {
            padding-top: 12px;
            padding-bottom: 70px; /* Ruang untuk bottom nav */
        }

        .opac-catalog-header {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            margin-bottom: 14px;
            padding-bottom: 10px;
        }

        .opac-section-heading {
            font-size: 1.05rem;
            margin-bottom: 0;
        }

        .opac-section-subheading {
            display: none;
        }

        .opac-header-actions {
            flex-wrap: nowrap;
            gap: 6px;
            width: 100%;
        }

        .opac-sort-wrap {
            flex: 1 1 auto;
            min-width: 0;
        }

        .opac-sort-select {
            width: 100%;
            min-width: 0;
            font-size: 0.8rem;
            padding: 6px 10px;
            min-height: 36px;
        }

        .opac-view-switch-btn {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
        }

        /* Mode HP: Kartu Buku Diperkecil */
        .opac-catalog-grid > .opac-col-book {
            padding-left: 4px !important;
            padding-right: 4px !important;
            margin-bottom: 8px;
        }

        .opac-warm-card {
            padding: 7px 6px 8px;
            border-radius: 10px;
        }

        .opac-book-kicker {
            font-size: 0.58rem;
            letter-spacing: 0.04em;
            margin-bottom: 2px;
        }

        .opac-book-heading {
            font-size: 0.74rem !important;
            line-height: 1.22 !important;
            min-height: 1.82rem;
            margin-bottom: 2px;
        }

        .opac-book-author {
            font-size: 0.65rem;
            margin-bottom: 4px;
        }

        .opac-status-badge {
            font-size: 0.62rem;
            padding: 2px 6px;
        }

        .opac-pagination-container {
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding-top: 14px;
        }

        .opac-custom-pagination .page-item .page-link {
            min-width: 30px;
            height: 30px;
            font-size: 0.78rem;
        }

    }

    /* Bottom Sheet Drawer Filter Mobile & Tablet (< 992px) */
    @media (max-width: 991.98px) {
        .opac-sidebar-drawer {
            position: fixed;
            bottom: -100%;
            left: 0;
            right: 0;
            width: 100%;
            max-height: 82vh;
            background: #ffffff;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.2);
            z-index: 1060;
            overflow-y: auto;
            transition: bottom 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 0;
        }

        .opac-sidebar-drawer.is-open {
            bottom: 0;
        }

        .opac-drawer-header {
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 10;
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .opac-drawer-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1050;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .opac-drawer-backdrop.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        body.opac-sheet-open {
            overflow: hidden;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// Evaluasi parameter aktif
$activeCategory     = (string) ($category ?? request()->getVar('category') ?? '');
$activeAvailability = (string) ($availability ?? request()->getVar('availability') ?? 'semua');
$activeFormat       = (string) ($format ?? request()->getVar('format') ?? '');
$activeLanguage     = (string) ($language ?? request()->getVar('language') ?? '');
$activeYearStart    = (string) ($year_start ?? request()->getVar('year_start') ?? '');
$activeYearEnd      = (string) ($year_end ?? request()->getVar('year_end') ?? '');
$activeSort         = (string) ($sort ?? request()->getVar('sort') ?? 'terbaru');
$rawSearch          = (string) (request()->getVar('search') ?? '');
$activeSearchBy     = (string) ($search_by ?? request()->getVar('search_by') ?? '');
$rawLetter          = (string) (request()->getVar('letter') ?? '');
$activeLetter       = preg_match('/^[A-Za-z0-9]$/', $rawLetter) ? strtoupper($rawLetter) : '';
$activeAuthor       = (string) (request()->getVar('Author') ?? '');
$activeSubjectParam = (string) (request()->getVar('Subject') ?? '');
$activeDeweyParam   = (string) (request()->getVar('DeweyNo') ?? '');
$activePub          = (string) (request()->getVar('Publisher') ?? '');
$activeIsbn         = (string) (request()->getVar('ISBN') ?? '');
$activeYear         = (string) (request()->getVar('PublishYear') ?? '');
$activeLocation     = (string) (request()->getVar('PublishLocation') ?? '');

$activeSubject = ($activeSearchBy === 'Subject' && $rawSearch !== '') ? $rawSearch : $activeSubjectParam;
$activeDewey   = ($activeSearchBy === 'DeweyNo' && $rawSearch !== '') ? $rawSearch : $activeDeweyParam;

$hasActiveQuery = $rawSearch !== '' || $activeLetter !== '' || $activeSubject !== '' || $activeDewey !== '' ||
    $activeAuthor !== '' || $activePub !== '' || $activeIsbn !== '' || $activeYear !== '' || $activeLocation !== '' ||
    ($activeCategory !== '' && $activeCategory !== 'semua') ||
    ($activeAvailability !== '' && $activeAvailability !== 'semua') || $activeFormat !== '' ||
    $activeLanguage !== '' || $activeYearStart !== '' || $activeYearEnd !== '';

// Beranda (rak buku) kalau tidak ada pencarian atau filter; selain itu daftar hasil. Penentunya ada di Opac::berandaPolos().
$modeBeranda = !empty($mode_beranda);

// Angka di tombol Filter: berapa isian panel yang sedang aktif.
$jumlahFilter = count(array_filter([
    $activeAuthor, $activePub, $activeIsbn, $activeDeweyParam,
    ($activeAvailability !== 'semua' ? $activeAvailability : ''), $activeFormat, $activeLanguage, $activeYearStart, $activeYearEnd,
], static fn ($v) => $v !== ''));
$jelajahDdc       = $jelajah['ddc'] ?? [];
$jelajahPengarang = $jelajah['pengarang'] ?? [];
?>

<?php
// Parameter yang tidak punya kolom di panel Filter ikut terkirim lewat input tersembunyi, supaya pencarian baru tidak menghapusnya.
$bawaan = array_filter([
    'category'        => $activeCategory !== 'semua' ? $activeCategory : '',
    'sort'            => $activeSort !== 'terbaru' ? $activeSort : '',
    'search_by'       => $activeSearchBy,
    'letter'          => $activeLetter,
    'Subject'         => $activeSubjectParam,
    'PublishYear'     => $activeYear,
    'PublishLocation' => $activeLocation,
], static fn ($v) => $v !== '');

$huruf      = array_merge(range('A', 'Z'), range(0, 9));
$modeAbjad  = ($activeLetter !== '' && $activeSearchBy === 'Author') ? 'Author' : 'Title';
$kodeDdc    = array_map(static fn ($k) => (string) substr((string) $k, 0, 1), array_keys($jelajahDdc));
$ddcLainnya = $activeDeweyParam !== '' && !in_array($activeDeweyParam, $kodeDdc, true);
?>

<!-- ─── Hero: judul, kotak cari, panel Filter dan Abjad ─── -->
<section class="ob-hero" aria-labelledby="opacHeroTitle">
    <form class="ob-shell" id="opacSearchForm" method="get" action="<?= base_url('opac') ?>">
        <?php foreach ($bawaan as $nama => $nilai): ?>
            <input type="hidden" name="<?= esc($nama, 'attr') ?>" value="<?= esc($nilai, 'attr') ?>">
        <?php endforeach; ?>

        <div class="ob-hero-grid">
            <div class="ob-hero-copy">
                <h1 class="ob-hero-title" id="opacHeroTitle"><span>OPAC</span> <em>Online Public Access Catalogue</em></h1>
                <p class="ob-hero-desc">Telusuri koleksi tercetak perpustakaan. Cek ketersediaan, letak rak, dan status buku sebelum datang.</p>

                <div class="ob-search" role="search">
                    <div class="ob-pill">
                        <svg class="ob-pill-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <label for="opacSearchInput" class="visually-hidden">Kata kunci</label>
                        <input type="search" id="opacSearchInput" name="search" value="<?= esc($rawSearch, 'attr') ?>" placeholder="Cari judul atau topik" autocomplete="off">
                        <button type="button" class="ob-icon-btn" data-panel-toggle aria-controls="panelAbjad" aria-expanded="false" aria-label="Telusuri menurut abjad" title="Telusuri menurut abjad">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4.6 10.5 7.7 3.2l3.1 7.3"/><path d="M5.6 8.3h4.2"/><path d="M5 13.5h5.2L5 20.3h5.2"/><path d="M16.8 3.5v16.8"/><path d="m13.2 16.8 3.6 3.6 3.6-3.6"/></svg>
                        </button>
                        <button type="submit" class="ob-go">Cari</button>
                    </div>
                    <button type="button" class="ob-filter-btn" data-panel-toggle aria-controls="panelFilter" aria-expanded="false">
                        <span class="ob-filter-ico" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h10"/><path d="M18 6h2"/><circle cx="16" cy="6" r="2"/><path d="M4 12h4"/><path d="M12 12h8"/><circle cx="10" cy="12" r="2"/><path d="M4 18h12"/><circle cx="18" cy="18" r="2"/></svg></span>
                        Filter
                        <?php if ($jumlahFilter > 0): ?>
                            <span class="ob-filter-badge"><?= $jumlahFilter ?><span class="visually-hidden"> filter aktif</span></span>
                        <?php endif; ?>
                    </button>
                </div>

                <span class="ob-jumlah"><strong><?= number_format($total_records ?? 0) ?></strong> <?= $hasActiveQuery ? 'judul ditemukan' : 'judul di katalog' ?></span>
            </div>

            <div class="ob-hero-mark" aria-hidden="true">
                <div class="ob-ring"><div class="ob-ring-in"><img src="<?= base_url('assets/home/logo-kemnaker.png') ?>" alt="" width="256" height="256"></div></div>
            </div>

            <!-- Panel Filter: semua isian ikut terkirim bersama kata kunci -->
            <div class="ob-panel" id="panelFilter" hidden>
                <div class="ob-panel-kepala">
                    <h2>Filter pencarian</h2>
                    <button type="button" class="ob-tutup" data-panel-close="panelFilter" aria-label="Tutup filter"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                </div>
                <div class="ob-panel-isi">
                    <div class="ob-fields">
                        <div class="ob-field">
                            <label for="fAuthor">Pengarang</label>
                            <input type="text" id="fAuthor" name="Author" value="<?= esc($activeAuthor, 'attr') ?>" placeholder="Nama pengarang" autocomplete="off">
                        </div>
                        <div class="ob-field">
                            <label for="fPublisher">Penerbit</label>
                            <input type="text" id="fPublisher" name="Publisher" value="<?= esc($activePub, 'attr') ?>" placeholder="Nama penerbit" autocomplete="off">
                        </div>
                        <div class="ob-field">
                            <label for="fIsbn">ISBN</label>
                            <input type="text" id="fIsbn" name="ISBN" value="<?= esc($activeIsbn, 'attr') ?>" placeholder="978-..." autocomplete="off">
                        </div>
                        <div class="ob-field">
                            <label for="fTersedia">Ketersediaan</label>
                            <select id="fTersedia" name="availability">
                                <option value="semua" <?= ($activeAvailability === 'semua' || $activeAvailability === '') ? 'selected' : '' ?>>Semua buku</option>
                                <option value="tersedia" <?= $activeAvailability === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                <option value="dipinjam" <?= $activeAvailability === 'dipinjam' ? 'selected' : '' ?>>Sedang dipinjam</option>
                            </select>
                        </div>
                        <div class="ob-field">
                            <label for="fFormat">Format koleksi</label>
                            <select id="fFormat" name="format">
                                <option value="" <?= $activeFormat === '' ? 'selected' : '' ?>>Semua format</option>
                                <option value="cetak" <?= $activeFormat === 'cetak' ? 'selected' : '' ?>>Buku cetak</option>
                                <option value="ebook" <?= $activeFormat === 'ebook' ? 'selected' : '' ?>>E-book</option>
                                <option value="audio" <?= $activeFormat === 'audio' ? 'selected' : '' ?>>Buku audio</option>
                            </select>
                        </div>
                        <div class="ob-field">
                            <label for="fBahasa">Bahasa</label>
                            <select id="fBahasa" name="language">
                                <option value="" <?= $activeLanguage === '' ? 'selected' : '' ?>>Semua bahasa</option>
                                <option value="ind" <?= ($activeLanguage === 'ind' || $activeLanguage === 'id') ? 'selected' : '' ?>>Indonesia</option>
                                <option value="eng" <?= ($activeLanguage === 'eng' || $activeLanguage === 'en') ? 'selected' : '' ?>>Inggris</option>
                            </select>
                        </div>
                        <fieldset class="ob-field">
                            <legend>Tahun terbit</legend>
                            <div class="ob-rentang">
                                <input type="number" name="year_start" value="<?= esc($activeYearStart, 'attr') ?>" min="1900" max="2099" placeholder="Dari" aria-label="Tahun terbit dari">
                                <span aria-hidden="true">-</span>
                                <input type="number" name="year_end" value="<?= esc($activeYearEnd, 'attr') ?>" min="1900" max="2099" placeholder="Sampai" aria-label="Tahun terbit sampai">
                            </div>
                        </fieldset>
                    </div>

                    <?php if (!empty($jelajahDdc)): ?>
                        <fieldset class="ob-grup">
                            <legend class="ob-sub">Subjek (klasifikasi DDC)</legend>
                            <div class="ob-ddc">
                                <label><input type="radio" name="DeweyNo" value="" <?= $activeDeweyParam === '' ? 'checked' : '' ?>><span class="nama">Semua kelas</span></label>
                                <?php if ($ddcLainnya): ?>
                                    <label><input type="radio" name="DeweyNo" value="<?= esc($activeDeweyParam, 'attr') ?>" checked><span class="kode"><?= esc($activeDeweyParam) ?></span><span class="nama">Kelas yang sedang dipilih</span></label>
                                <?php endif; ?>
                                <?php foreach ($jelajahDdc as $kode => $kelas): $nilai = (string) substr((string) $kode, 0, 1); ?>
                                    <label>
                                        <input type="radio" name="DeweyNo" value="<?= esc($nilai, 'attr') ?>" <?= $activeDeweyParam === $nilai ? 'checked' : '' ?>>
                                        <span class="kode"><?= esc($kode) ?></span>
                                        <span class="nama"><?= esc($kelas['name']) ?></span>
                                        <span class="jumlah"><?= number_format($kelas['count']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    <?php endif; ?>

                    <?php if (!empty($jelajahPengarang)): ?>
                        <div class="ob-grup">
                            <span class="ob-sub">Pengarang dengan koleksi terbanyak</span>
                            <div class="ob-chips">
                                <?php foreach ($jelajahPengarang as $p): ?>
                                    <a class="ob-chip" href="<?= base_url('opac?' . http_build_query(['search' => $p['name'], 'search_by' => 'Author'])) ?>"><?= esc($p['name']) ?> <small><?= number_format($p['count']) ?></small></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="ob-panel-kaki">
                    <a class="ob-semua ob-usul" href="<?= base_url('home/pendaftaran-online') ?>">Belum menemukan bukunya? Usulkan buku</a>
                    <a class="ob-btn" href="<?= base_url('opac') ?>">Reset</a>
                    <button type="submit" class="ob-btn ob-btn-isi">Terapkan filter</button>
                </div>
            </div>

            <!-- Panel Abjad: tautan langsung ke daftar buku berawalan huruf itu -->
            <div class="ob-panel" id="panelAbjad" hidden>
                <div class="ob-panel-kepala">
                    <h2>Telusuri menurut abjad</h2>
                    <div class="ob-kepala-alat">
                        <div class="ob-abjad-mode" role="group" aria-label="Abjad berdasarkan">
                            <button type="button" data-abjad-mode="Title" aria-pressed="<?= $modeAbjad === 'Title' ? 'true' : 'false' ?>">Judul</button>
                            <button type="button" data-abjad-mode="Author" aria-pressed="<?= $modeAbjad === 'Author' ? 'true' : 'false' ?>">Pengarang</button>
                        </div>
                        <button type="button" class="ob-tutup" data-panel-close="panelAbjad" aria-label="Tutup abjad"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                    </div>
                </div>
                <div class="ob-panel-isi">
                    <?php foreach (['Title' => 'judul', 'Author' => 'pengarang'] as $kriteria => $nama): ?>
                        <nav class="ob-huruf" data-abjad-grup="<?= $kriteria ?>" aria-label="Abjad <?= $nama ?>" <?= $modeAbjad === $kriteria ? '' : 'hidden' ?>>
                            <?php foreach ($huruf as $h): ?>
                                <a href="<?= base_url('opac?' . http_build_query(['search_by' => $kriteria, 'letter' => $h])) ?>" <?= ($activeLetter === (string) $h && $modeAbjad === $kriteria) ? 'aria-current="true"' : '' ?>><?= $h ?></a>
                            <?php endforeach; ?>
                        </nav>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </form>

    <div class="ob-wave" aria-hidden="true">
        <svg class="ob-wave-belakang" viewBox="0 0 2880 80" preserveAspectRatio="none" focusable="false"><path d="M0 30C240 60 480 60 720 30S1200 0 1440 30 1920 60 2160 30 2640 0 2880 30V80H0Z"/></svg>
        <svg class="ob-wave-depan" viewBox="0 0 2880 80" preserveAspectRatio="none" focusable="false"><path d="M0 44C240 16 480 16 720 44S1200 72 1440 44 1920 16 2160 44 2640 72 2880 44V80H0Z"/></svg>
    </div>
</section>

<?php helper('opac_beranda'); ?>

<!-- ─── Isi halaman: rak buku (beranda) atau hasil pencarian ─── -->
<div class="ob-shell ob-main" id="opacMain">
            <?php if (session()->getFlashdata('notif_izin')): ?>
                <div class="alert alert-info opac-notif-izin mb-4" role="status">
                    Kami memakai riwayat pinjammu untuk menyarankan buku. Hanya kamu yang bisa melihatnya.
                    Kamu bisa mematikannya di <a href="<?= base_url('opac/profil') ?>" class="alert-link">Profil</a>.
                </div>
            <?php endif; ?>

            <?php if ($modeBeranda): ?>
                <?php
                // Rak pribadi anggota (hanya muncul kalau sudah login dan punya riwayat), lalu rak untuk semua pengunjung.
                foreach (($baris_rekomendasi ?? []) as $judulBaris => $isiBaris) {
                    if (!empty($isiBaris)) {
                        echo beranda_rak('rak-' . substr(md5($judulBaris), 0, 8), $judulBaris, '', $isiBaris);
                    }
                }

                // Chip topik: tautan biasa (muat ulang halaman) supaya tetap jalan tanpa JavaScript.
                ob_start(); ?>
                <nav class="ob-chips" aria-label="Pilih topik">
                    <?php foreach ($topik_daftar as $kunci => $labelTopik): ?>
                        <a class="ob-chip" href="<?= base_url('opac?' . http_build_query(['topik' => $kunci])) ?>#rak-topik" <?= $kunci === $topik_aktif ? 'aria-current="true"' : '' ?>><?= esc($labelTopik) ?></a>
                    <?php endforeach; ?>
                </nav>
                <?php $chipTopik = ob_get_clean(); ?>

                <?= beranda_rak('rak-topik', 'Jelajah per topik', 'Pilih topik untuk mengganti rak.', $rak_beranda['topik'] ?? [], [
                    'semua'  => base_url('opac?' . http_build_query(['category' => $topik_kategori])),
                    'antara' => $chipTopik,
                    'kosong' => 'Belum ada buku untuk topik ' . ($topik_daftar[$topik_aktif] ?? '') . '.',
                ]) ?>
                <?= beranda_rak('rak-populer', 'Paling sering dipinjam', '12 bulan terakhir, dari semua anggota', $rak_beranda['populer'] ?? [], [
                    'jenis'  => 'populer',
                    'semua'  => base_url('opac?sort=populer'),
                    'kosong' => 'Belum cukup data peminjaman untuk menampilkan rak ini.',
                ]) ?>
                <?= beranda_rak('rak-baru', 'Baru masuk rak', 'Koleksi terbaru yang ditambahkan ke katalog', $rak_beranda['baru'] ?? [], [
                    'semua'  => base_url('opac?semua=1'),
                    'kosong' => 'Belum ada koleksi baru.',
                ]) ?>
            <?php else: ?>

            <!-- Header Hasil: judul, jumlah buku, urutkan, tampilan kisi atau daftar -->
            <div class="opac-catalog-header">
                <div>
                    <h2 class="opac-section-heading">
                        <?php if ($activeSubject !== ''): ?>
                            Subjek: <span class="text-primary"><?= esc($activeSubject) ?></span>
                        <?php elseif ($activeDewey !== ''): ?>
                            Klasifikasi DDC: <span class="text-primary"><?= esc($activeDewey) ?></span>
                        <?php elseif ($rawSearch !== ''): ?>
                            Hasil Pencarian: <span class="text-primary"><?= esc($rawSearch) ?></span>
                        <?php elseif ($activeLetter !== ''): ?>
                            <?= $activeSearchBy === 'Author' ? 'Pengarang' : 'Judul' ?> berawalan <span class="text-primary"><?= esc($activeLetter) ?></span>
                        <?php elseif ($activeAuthor !== ''): ?>
                            Pengarang: <span class="text-primary"><?= esc($activeAuthor) ?></span>
                        <?php elseif ($activePub !== ''): ?>
                            Penerbit: <span class="text-primary"><?= esc($activePub) ?></span>
                        <?php elseif ($activeYear !== ''): ?>
                            Tahun Terbit: <span class="text-primary"><?= esc($activeYear) ?></span>
                        <?php elseif ($activeLocation !== ''): ?>
                            Lokasi Terbit: <span class="text-primary"><?= esc($activeLocation) ?></span>
                        <?php else: ?>
                            Semua koleksi
                        <?php endif; ?>
                    </h2>
                    <p class="opac-section-subheading">
                        <?= number_format($total_records ?? 0) ?> buku untuk menemani rasa ingin tahu Anda
                    </p>
                </div>

                <div class="opac-header-actions">
                    <a href="<?= base_url('opac') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="min-height: 44px; display: inline-flex; align-items: center;">
                        <i class="fas fa-times me-1"></i><?= $hasActiveQuery ? 'Reset' : 'Kembali ke beranda' ?>
                    </a>

                    <!-- Dropdown Urutkan -->
                    <div class="d-flex align-items-center opac-sort-wrap">
                        <span class="opac-sort-label d-none d-sm-inline">Urutkan:</span>
                        <select name="sort" class="opac-sort-select" onchange="window.location.href=this.value" aria-label="Urutkan Koleksi">
                            <option value="<?= esc(buildOpacUrl(['sort' => 'terbaru'])) ?>" <?= $activeSort === 'terbaru' ? 'selected' : '' ?>>Terbaru ditambahkan</option>
                            <option value="<?= esc(buildOpacUrl(['sort' => 'populer'])) ?>" <?= $activeSort === 'populer' ? 'selected' : '' ?>>Paling sering dipinjam</option>
                            <option value="<?= esc(buildOpacUrl(['sort' => 'title_asc'])) ?>" <?= $activeSort === 'title_asc' ? 'selected' : '' ?>>Judul (A-Z)</option>
                            <option value="<?= esc(buildOpacUrl(['sort' => 'title_desc'])) ?>" <?= $activeSort === 'title_desc' ? 'selected' : '' ?>>Judul (Z-A)</option>
                            <option value="<?= esc(buildOpacUrl(['sort' => 'year_desc'])) ?>" <?= $activeSort === 'year_desc' ? 'selected' : '' ?>>Tahun (Terbaru)</option>
                            <option value="<?= esc(buildOpacUrl(['sort' => 'year_asc'])) ?>" <?= $activeSort === 'year_asc' ? 'selected' : '' ?>>Tahun (Terlama)</option>
                        </select>
                    </div>

                    <!-- View Switcher (Grid / List) -->
                    <div class="d-flex align-items-center gap-1 ms-1">
                        <button type="button" class="opac-view-switch-btn active" id="btnViewGrid" title="Tampilan Kisi" aria-label="Tampilan Kisi">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="opac-view-switch-btn" id="btnViewList" title="Tampilan Daftar" aria-label="Tampilan Daftar">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ─── Grid Kartu Buku (Desktop 4 kolom, Mobile 2 kolom compact) ─── -->
            <div class="row g-2 g-md-3 opac-catalog-grid" id="opacCatalogGrid">
                <?php
                $displayCatalogs = $catalogs ?? [];
                ?>

                <?php if (!empty($displayCatalogs)): ?>
                    <?php foreach ($displayCatalogs as $catalog): ?>
                        <?= renderModernBookCard($catalog, 'opac-col-book col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-6') ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-3 shadow-sm border" style="border-color: #e2e8f0 !important;">
                            <div class="mb-3">
                                <i class="fas fa-book-open fa-3x text-muted opacity-50"></i>
                            </div>
                            <h4 class="text-dark fw-bold mb-2">
                                <?= $hasActiveQuery ? 'Koleksi tidak ditemukan' : 'Belum ada koleksi yang ditampilkan' ?>
                            </h4>
                            <p class="text-muted mb-4 px-3" style="font-size: 0.9rem;">
                                <?= $hasActiveQuery ? 'Coba ubah kata kunci atau bersihkan filter yang sedang aktif.' : 'Katalog perpustakaan siap dijelajahi.' ?>
                            </p>
                            <?php if (!empty($onesearch_url)): ?>
                                <p class="text-dark fw-semibold mb-2">Buku ini belum ada di perpustakaan kami.</p>
                                <a href="<?= esc($onesearch_url, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-dark rounded-pill px-4 mb-3 js-onesearch">
                                    <i class="fas fa-external-link-alt me-2"></i>Cari di Indonesia OneSearch
                                </a><br>
                            <?php endif; ?>
                            <a href="<?= base_url('opac') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-undo me-2"></i>Reset Semua Filter
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rekomendasi Mirip Jika Ada -->
            <?php if (!empty($catalogs_mirip)): ?>
                <h5 class="fw-bold text-dark mt-4 mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Mungkin kamu tertarik</h5>
                <div class="row g-2 g-md-3 opac-catalog-grid">
                    <?php foreach ($catalogs_mirip as $catalog): ?>
                        <?= renderModernBookCard($catalog, 'opac-col-book col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-6') ?>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!empty($onesearch_url) && !empty($displayCatalogs)): ?>
                <p class="text-muted small mt-4 mb-0">
                    Hasilnya sedikit?
                    <a href="<?= esc($onesearch_url, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="js-onesearch">Cari juga di Indonesia OneSearch</a>
                </p>
            <?php endif; ?>

            <!-- ─── Pagination Bawah (Sesuai Screenshot: Menampilkan X-Y dari Z buku & Page Nav) ─── -->
            <?php if (!empty($displayCatalogs)): ?>
                <?php
                $currP = (int) ($currentPage ?? 1);
                $pSize = (int) ($perPage ?? 16);
                $totalRec = (int) ($total_records ?? count($displayCatalogs));
                $startItem = ($currP - 1) * $pSize + 1;
                $endItem   = min($currP * $pSize, $totalRec);
                ?>
                <div class="opac-pagination-container">
                    <p class="opac-page-info">
                        Menampilkan <?= $startItem ?>–<?= $endItem ?> dari <?= number_format($totalRec) ?> buku
                    </p>

                    <div>
                        <?php if (!empty($pager)): ?>
                            <?php
                            if (is_string($pager)) {
                                echo $pager;
                            } else {
                                echo $pager->links('default', 'opac_pagination');
                            }
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php endif; // akhir mode hasil pencarian ?>
</div>

<script src="<?= base_url('assets/js/opac_beranda.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/opac_beranda.js') ?>" defer></script>
<script>
    // Autofocus input pencarian jika kosong
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && !searchInput.value && window.innerWidth >= 1024) {
            searchInput.focus();
        }

        // Toggle Grid vs List View
        const grid = document.getElementById('opacCatalogGrid');
        const btnGrid = document.getElementById('btnViewGrid');
        const btnList = document.getElementById('btnViewList');

        const savedView = localStorage.getItem('opac_view_mode') || 'grid';
        if (savedView === 'list' && grid && btnList && btnGrid) {
            grid.classList.add('view-list');
            btnList.classList.add('active');
            btnGrid.classList.remove('active');
        }

        if (btnGrid && btnList && grid) {
            btnGrid.addEventListener('click', function() {
                grid.classList.remove('view-list');
                btnGrid.classList.add('active');
                btnList.classList.remove('active');
                localStorage.setItem('opac_view_mode', 'grid');
            });
            btnList.addEventListener('click', function() {
                grid.classList.add('view-list');
                btnList.classList.add('active');
                btnGrid.classList.remove('active');
                localStorage.setItem('opac_view_mode', 'list');
            });
        }
    });

    // Logging Klik Katalog
    (function () {
        const searchLogId = <?= (int) ($search_log_id ?? 0) ?>;
        const clickUrl = <?= json_encode(base_url('api/Opac/click')) ?>;
        document.addEventListener('click', function (e) {
            const os = e.target.closest('a.js-onesearch');
            if (os) {
                const b = new FormData();
                b.append('target', 'onesearch');
                if (searchLogId) b.append('search_log_id', searchLogId);
                navigator.sendBeacon(clickUrl, b);
                return;
            }
            const link = e.target.closest('.opac-modern-card a.js-open-book-modal');
            if (!link) return;
            const card = link.closest('.opac-modern-card');
            const cards = Array.from(document.querySelectorAll('.opac-modern-card'));
            const body = new FormData();
            body.append('target', 'katalog');
            body.append('catalog_id', card.dataset.catalogId || '');
            body.append('position', cards.indexOf(card) + 1);
            if (searchLogId) body.append('search_log_id', searchLogId);
            navigator.sendBeacon(clickUrl, body);
        }, true);
    })();
</script>
<?= $this->endSection() ?>
