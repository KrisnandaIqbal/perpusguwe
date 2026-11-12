<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    :root {
        --browse-primary: #1b3878;
        --browse-primary-dark: #122550;
        --browse-accent: #3b82f6;
        --browse-bg-soft: #f8fafc;
        --browse-border: #e2e8f0;
    }

    /* Hero Banner */
    .browse-hero {
        background: linear-gradient(135deg, #1b3878 0%, #2563eb 100%);
        color: #ffffff;
        border-radius: 24px;
        padding: 40px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(27, 56, 120, 0.15);
    }
    .browse-hero::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Category Switcher Tabs */
    .browse-category-tabs {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        padding: 5px;
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        margin-bottom: 24px;
        gap: 4px;
        max-width: 100%;
        overflow-x: auto;
    }
    .browse-cat-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        border-radius: 50px;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.92rem;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .browse-cat-btn:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        transform: translateY(-1px);
    }
    .browse-cat-btn.active {
        background: #ffffff;
        color: #1b3878;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }
    .browse-cat-btn .badge-pill-count {
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 20px;
        font-weight: 700;
    }
    .browse-cat-btn.active .badge-pill-count {
        background: #eff6ff;
        color: #1d4ed8;
    }
    .browse-cat-btn:not(.active) .badge-pill-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Live Search Input */
    .browse-search-box {
        position: relative;
        max-width: 680px;
        margin: 0 auto;
    }
    .browse-search-box form {
        position: relative;
        width: 100%;
        margin: 0;
        display: flex;
        align-items: center;
    }
    .browse-search-input {
        width: 100%;
        height: 56px;
        border-radius: 50px;
        padding-left: 58px;
        padding-right: 50px;
        font-size: 1rem;
        line-height: 1.5;
        border: 2px solid #ffffff;
        background: #ffffff;
        color: #1e293b;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transition: all 0.25s ease;
    }
    .browse-search-input:focus {
        outline: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border-color: #93c5fd;
    }
    .browse-search-icon-wrapper {
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        height: 100%;
        width: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 5;
    }
    .browse-search-icon {
        width: 1em;
        height: 1em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #64748b;
        font-size: 1.15rem;
        line-height: 1;
        margin: 0;
        padding: 0;
        transform-origin: 50% 50%;
        box-sizing: border-box;
    }
    .browse-search-clear {
        position: absolute;
        right: 18px;
        top: 0;
        bottom: 0;
        margin: auto 0;
        width: 32px;
        height: 32px;
        display: none;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.1rem;
        border-radius: 50%;
        transition: all 0.2s ease;
        z-index: 5;
    }
    .browse-search-clear:hover {
        color: #ef4444;
        background: rgba(0, 0, 0, 0.05);
    }

    /* Blok Blur Highlight Kata Kunci Pencarian (Biru Transparan & Blur Elegan, Bukan Kuning) */
    mark.browse-highlight-blur,
    .browse-highlight-blur {
        background: rgba(37, 99, 235, 0.12) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(59, 130, 246, 0.28) !important;
        padding: 1px 7px !important;
        border-radius: 6px !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08) !important;
        display: inline-block !important;
        line-height: 1.25 !important;
        vertical-align: baseline !important;
        margin: 0 1px !important;
    }
    /* Versi di atas cover buku gelap (frosted blur putih transparan) */
    .opac-modern-card .opac-cover-stage mark.browse-highlight-blur,
    .opac-modern-card .opac-cover-title mark.browse-highlight-blur,
    .opac-modern-card .opac-cover-author mark.browse-highlight-blur,
    .opac-cover-container mark.browse-highlight-blur {
        background: rgba(255, 255, 255, 0.24) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.45) !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35) !important;
    }

    /* Popular Chips */
    .populer-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 0.88rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .populer-chip:hover {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(27, 56, 120, 0.2);
    }
    .populer-chip .badge-count {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 9999px;
        transition: all 0.2s ease;
    }
    .populer-chip:hover .badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Sticky Alphabet Navigation Bar */
    .alphabet-nav-container {
        position: sticky;
        top: 80px;
        z-index: 95;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 10px 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
    }
    .alphabet-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        justify-content: center;
    }
    .alphabet-btn {
        min-width: 38px;
        height: 38px;
        padding: 0 8px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
        position: relative;
    }
    .alphabet-btn:hover:not(.disabled) {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(27, 56, 120, 0.2);
    }
    .alphabet-btn.active {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
        box-shadow: 0 4px 10px rgba(27, 56, 120, 0.3);
    }
    .alphabet-btn.all-btn {
        min-width: 68px;
        font-weight: 600;
    }
    .alphabet-btn.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        background: #f8fafc;
        border-color: #f1f5f9;
        color: #94a3b8;
    }

    /* Letter Header (Subject & Results) */
    .letter-group-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 36px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e2e8f0;
    }
    .letter-badge-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1b3878 0%, #2563eb 100%);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(27, 56, 120, 0.25);
    }
    .letter-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    /* Subject Card Item */
    .subject-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        position: relative;
    }
    .subject-card:hover {
        background: #ffffff;
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.1);
        color: inherit;
    }
    .subject-card .subject-name {
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.4;
        display: block;
        transition: color 0.15s ease;
    }
    .subject-card:hover .subject-name {
        color: #1b3878;
    }
    .subject-sub-heading {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 400;
        margin-top: 3px;
    }
    .subject-card .card-footer-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 6px;
        border-top: 1px dashed #f1f5f9;
    }
    .subject-count-badge {
        font-size: 0.74rem;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
        padding: 2px 7px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .subject-action-arrow {
        color: #94a3b8;
        font-size: 0.8rem;
        transition: transform 0.2s ease, color 0.2s ease;
    }
    .subject-card:hover .subject-action-arrow {
        transform: translateX(3px);
        color: #1b3878;
    }

    /* DDC Directory Cards & Elements */
    .ddc-class-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 36px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }
    .ddc-class-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ddc-class-badge {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        font-weight: 800;
        font-family: var(--bs-font-monospace, monospace);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
    }
    .ddc-class-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .ddc-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none !important;
        color: inherit !important;
        transition: all 0.22s ease;
        position: relative;
    }
    .ddc-card:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(27, 56, 120, 0.08);
    }
    .ddc-card.is-empty {
        background: #fbfcfe;
        border-color: #f1f5f9;
        opacity: 0.72;
    }
    .ddc-card.is-empty:hover {
        opacity: 0.95;
        border-color: #cbd5e1;
    }
    .ddc-card-top {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }
    .ddc-code-pill {
        font-family: var(--bs-font-monospace, monospace);
        font-weight: 800;
        font-size: 0.92rem;
        padding: 4px 10px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        line-height: 1.3;
        transition: all 0.2s ease;
    }
    .ddc-card:hover .ddc-code-pill {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
    }
    .ddc-name {
        font-size: 0.93rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.4;
        display: block;
        transition: color 0.15s ease;
    }
    .ddc-card:hover .ddc-name {
        color: #1b3878;
    }
    .ddc-sections-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 6px;
    }
    .ddc-sec-tag {
        font-size: 0.72rem;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 1px 6px;
        border-radius: 5px;
        font-family: var(--bs-font-monospace, monospace);
        font-weight: 600;
    }
    .ddc-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px dashed #f1f5f9;
    }
    .ddc-count-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .ddc-count-badge.has-items {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }
    .ddc-count-badge.no-items {
        background: #f8fafc;
        color: #94a3b8;
        border: 1px solid #f1f5f9;
    }
    .ddc-arrow {
        color: #94a3b8;
        font-size: 0.8rem;
        transition: transform 0.2s ease, color 0.2s ease;
    }
    .ddc-card:hover .ddc-arrow {
        transform: translateX(3px);
        color: #1b3878;
    }

    /* List View Card for Books */
    .catalog-list-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        position: relative;
    }
    .catalog-list-card:hover {
        border-color: #93c5fd;
        box-shadow: 0 10px 28px rgba(27, 56, 120, 0.08);
        transform: translateY(-2px);
    }
    .catalog-list-cover-link {
        display: block;
        text-decoration: none !important;
        flex-shrink: 0;
    }
    .catalog-list-thumb {
        width: 86px;
        height: 122px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
        background: #f1f5f9;
        display: block;
        transition: transform 0.2s ease;
    }
    .catalog-list-thumb-cover {
        width: 86px;
        height: 122px;
        min-width: 86px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 8px 7px 7px 11px;
        color: #ffffff;
        text-decoration: none !important;
        transition: transform 0.2s ease;
    }
    .catalog-list-cover-link:hover .catalog-list-thumb-cover,
    .catalog-list-cover-link:hover .catalog-list-thumb {
        transform: scale(1.03);
    }
    .catalog-thumb-spine {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: rgba(255, 255, 255, 0.22);
        box-shadow: inset -1px 0 2px rgba(0, 0, 0, 0.25);
    }
    .catalog-thumb-tag {
        font-size: 0.58rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1;
    }
    .catalog-thumb-title {
        font-size: 0.68rem;
        font-weight: 700;
        line-height: 1.25;
        color: #ffffff;
        margin-top: auto;
        margin-bottom: auto;
        padding: 4px 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
    }
    .catalog-thumb-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.55rem;
        color: rgba(255, 255, 255, 0.75);
        line-height: 1;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        padding-top: 4px;
    }
    .catalog-thumb-call {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 50px;
    }
    .catalog-year-pill {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        line-height: 1.3;
    }
    .badge-control-no {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .catalog-list-heading {
        font-size: 1.08rem;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 6px;
    }
    .catalog-list-heading a {
        color: #0f172a;
        transition: color 0.15s ease;
    }
    .catalog-list-heading a:hover {
        color: #2563eb;
    }
    .catalog-list-meta-row {
        gap: 8px 18px;
    }
    .catalog-list-meta-row .meta-item {
        display: inline-flex;
        align-items: center;
        font-size: 0.84rem;
    }
    .subject-badge-item {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 500;
        font-size: 0.78rem;
        padding: 3px 8px;
        border-radius: 6px;
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
        text-decoration: none !important;
    }
    .subject-badge-item:hover {
        background-color: #1b3878 !important;
        color: #ffffff !important;
        border-color: #1b3878 !important;
    }

    /* View Mode Switcher Button Group */
    .view-mode-btn {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 6px 14px;
        transition: all 0.2s ease;
    }
    .view-mode-btn:hover {
        background: #f8fafc;
        color: #1b3878;
    }
    .view-mode-btn.active {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
    }

    /* Back to Top Button */
    .btn-back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #1b3878;
        color: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 6px 18px rgba(27, 56, 120, 0.35);
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 99;
    }
    .btn-back-to-top.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .btn-back-to-top:hover {
        background: #2563eb;
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.4);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-4" style="padding-top: 100px !important;">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="<?= base_url('home') ?>" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-home me-1"></i>Beranda
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('opac') ?>" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>OPAC
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('opac/browse') ?>" class="text-decoration-none text-muted">
                    Browse Katalog
                </a>
            </li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
                <?php if ($browse_type === 'author'): ?>
                    <i class="fa-solid fa-user-pen me-1"></i>Pengarang
                <?php elseif ($browse_type === 'title'): ?>
                    <i class="fa-solid fa-book me-1"></i>Judul
                <?php else: ?>
                    <i class="fa-solid fa-tags me-1"></i>Subjek
                <?php endif; ?>
            </li>
        </ol>
    </nav>

    <!-- Hero Banner Terpadu -->
    <div class="browse-hero mb-4">
        <div class="row align-items-center">
            <div class="col-lg-10 mx-auto text-center">

                <!-- Category Switcher Pill Tabs (3 Tab: Judul, Pengarang, Subjek) -->
                <div class="browse-category-tabs">
                    <a href="<?= base_url('opac/browse?type=title&letter=' . ($browse_type === 'title' ? $letter : 'A')) ?>"
                       class="browse-cat-btn <?= $browse_type === 'title' ? 'active' : '' ?>"
                       title="Browse berdasarkan abjad Judul Buku">
                        <i class="fa-solid fa-book"></i>
                        <span>Judul</span>
                        <span class="badge-pill-count"><?= number_format($total_titles ?? 0) ?></span>
                    </a>
                    <a href="<?= base_url('opac/browse?type=author&letter=' . ($browse_type === 'author' ? $letter : 'A')) ?>"
                       class="browse-cat-btn <?= $browse_type === 'author' ? 'active' : '' ?>"
                       title="Browse berdasarkan nama Pengarang / Penulis">
                        <i class="fa-solid fa-user-pen"></i>
                        <span>Pengarang</span>
                        <span class="badge-pill-count"><?= number_format($total_authors ?? 0) ?></span>
                    </a>
                    <a href="<?= base_url('opac/browse?type=subject') ?>"
                       class="browse-cat-btn <?= $browse_type === 'subject' ? 'active' : '' ?>"
                       title="Browse berdasarkan klasifikasi Subjek (000 - 900)">
                        <i class="fa-solid fa-tags"></i>
                        <span>Subjek</span>
                        <span class="badge-pill-count"><?= number_format($total_subjects ?? 0) ?></span>
                    </a>
                </div>

                <!-- Judul & Keterangan Dinamis -->
                <?php if ($browse_type === 'author'): ?>
                    <h1 class="display-6 fw-bold mb-2">Jelajah Koleksi Lewat Pengarang</h1>
                    <p class="lead mb-4 text-white-50" style="font-size: 1.05rem;">
                        Telusuri koleksi buku berdasarkan nama penulis, penyunting, penerjemah, maupun lembaga pengarang secara teratur menurut abjad.
                    </p>
                <?php elseif ($browse_type === 'title'): ?>
                    <h1 class="display-6 fw-bold mb-2">Jelajah Koleksi Lewat Judul</h1>
                    <p class="lead mb-4 text-white-50" style="font-size: 1.05rem;">
                        Telusuri katalog perpustakaan Kementerian Ketenagakerjaan berdasarkan abjad judul buku secara teratur, cepat, dan akurat.
                    </p>
                <?php else: ?>
                    <h1 class="display-6 fw-bold mb-2">Jelajah Koleksi Lewat Subjek</h1>
                    <p class="lead mb-4 text-white-50" style="font-size: 1.05rem;">
                        Telusuri ribuan koleksi perpustakaan Kementerian Ketenagakerjaan melalui klasifikasi standar subjek (000 - 900) secara teratur, berurutan, dan terstruktur.
                    </p>
                <?php endif; ?>

                <!-- Search Input Box -->
                <div class="browse-search-box">
                    <form action="<?= base_url('opac/browse') ?>" method="GET" id="browseSearchForm">
                        <input type="hidden" name="type" value="<?= esc($browse_type) ?>">
                        <input type="hidden" name="letter" value="<?= esc($letter) ?>">
                        <div class="browse-search-icon-wrapper">
                            <i class="fa-solid fa-magnifying-glass browse-search-icon" id="browseSearchIcon"></i>
                        </div>
                        <input type="text"
                               id="browseSearchInput"
                               name="q"
                               class="form-control browse-search-input"
                               placeholder="<?= $browse_type === 'subject' ? 'Ketik nomor subjek/DDC (misal: 010, 020, 330) atau nama bidang keilmuan...' : ($browse_type === 'author' ? 'Cari nama pengarang atau penulis...' : 'Cari judul buku atau kata kunci judul...') ?>"
                               value="<?= esc($search_query ?? '') ?>"
                               autocomplete="off">
                        <button type="button" id="browseSearchClear" class="browse-search-clear" title="Bersihkan pencarian">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </form>
                </div>

                <!-- Ringkasan Statistik -->
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 mt-3 text-white-50 small">
                    <span>
                        <i class="fa-solid fa-book me-1 text-warning"></i>
                        <strong class="text-white"><?= number_format($total_titles ?? 0) ?></strong> Total Judul
                    </span>
                    <span>&bull;</span>
                    <span>
                        <i class="fa-solid fa-user-pen me-1 text-info"></i>
                        <strong class="text-white"><?= number_format($total_authors ?? 0) ?></strong> Pengarang
                    </span>
                    <span>&bull;</span>
                    <span>
                        <i class="fa-solid fa-tags me-1 text-success"></i>
                        <strong class="text-white"><?= number_format($total_subjects ?? 0) ?></strong> Subjek (000-900)
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- Pintasan Populer (Subjek atau Pengarang) -->
    <?php if ($browse_type === 'subject' && !empty($ddc_top_divisions)): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-fire text-danger me-2"></i>Subjek Terpopuler
                    </h5>
                    <small class="text-muted">Klasifikasi subjek dengan jumlah koleksi terbanyak</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($ddc_top_divisions as $top): ?>
                        <a href="<?= base_url('opac?DeweyNo=' . $top['code']) ?>"
                           class="populer-chip"
                           title="Lihat <?= number_format($top['count']) ?> koleksi di subjek <?= esc($top['code']) ?> - <?= esc($top['name']) ?>">
                            <span class="badge bg-primary text-white fw-bold me-1" style="font-family: monospace; font-size: 0.76rem;"><?= esc($top['code']) ?></span>
                            <span><?= esc($top['name']) ?></span>
                            <span class="badge-count"><?= number_format($top['count']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php elseif ($browse_type === 'author' && !empty($top_authors)): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-award text-warning me-2"></i>Pengarang Terpopuler
                    </h5>
                    <small class="text-muted">Penulis dengan koleksi terbanyak di perpustakaan</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($top_authors as $top): ?>
                        <a href="<?= base_url('opac?search=' . urlencode($top['name']) . '&search_by=Author') ?>"
                           class="populer-chip"
                           title="Lihat <?= $top['count'] ?> buku karya <?= esc($top['name']) ?>">
                            <i class="fa-solid fa-user-pen text-primary opacity-75"></i>
                            <span><?= esc($top['name']) ?></span>
                            <span class="badge-count"><?= number_format($top['count']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php elseif ($browse_type === 'title' && !empty($top_subjects)): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-compass text-info me-2"></i>Topik & Kategori Pilihan
                    </h5>
                    <small class="text-muted">Pintasan tema koleksi pilihan pembaca</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach (array_slice($top_subjects, 0, 10) as $top): ?>
                        <a href="<?= base_url('opac?DeweyNo=' . $top['code']) ?>"
                           class="populer-chip"
                           title="Telusuri koleksi bertema <?= esc($top['name']) ?>">
                            <span class="badge bg-primary text-white fw-bold me-1" style="font-family: monospace; font-size: 0.76rem;"><?= esc($top['code']) ?></span>
                            <span><?= esc($top['name']) ?></span>
                            <span class="badge-count"><?= number_format($top['count']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sticky Navigation Bar (Subjek Kelas 000-900 / Alfabet Judul & Pengarang) -->
    <div class="alphabet-nav-container" id="alphabetNav">
        <div class="alphabet-nav">
            <?php if ($browse_type === 'subject'): ?>
                <!-- Subjek: Navigasi 10 Kelas Utama (000 - 900) -->
                <a href="javascript:void(0);"
                   class="alphabet-btn all-btn active"
                   data-ddc-class="all"
                   id="ddcAllBtn"
                   title="Tampilkan seluruh kelas subjek 000 - 900">
                    <i class="fa-solid fa-shapes me-1 small"></i> Semua Kelas
                </a>

                <?php foreach ($ddc_hierarchy ?? [] as $mCode => $mInfo): ?>
                    <a href="javascript:void(0);"
                       class="alphabet-btn ddc-class-nav-btn"
                       data-ddc-class="<?= esc($mCode) ?>"
                       title="Kelas <?= esc($mCode) ?>: <?= esc($mInfo['name']) ?> (<?= number_format($mInfo['count']) ?> koleksi)">
                        <span style="font-family: monospace; font-weight: 800;"><?= esc($mCode) ?></span>
                        <span class="badge bg-secondary-subtle text-secondary ms-1 d-none d-lg-inline" style="font-size: 0.7rem;"><?= number_format($mInfo['count']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Judul / Pengarang: Navigasi Huruf A-Z & 0-9 -->
                <?php foreach ($alphabet as $char): ?>
                    <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $char) ?>"
                       class="alphabet-btn <?= (empty($search_query) && ($letter ?? 'A') === $char) ? 'active' : '' ?>"
                       data-letter="<?= $char ?>"
                       title="Browse <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?> berawalan <?= $char ?>">
                        <?= $char ?>
                    </a>
                <?php endforeach; ?>

                <div class="d-none d-md-flex align-items-center gap-1 ms-1 ps-2 border-start">
                    <?php for ($i = 0; $i <= 9; $i++): ?>
                        <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $i) ?>"
                           class="alphabet-btn <?= (empty($search_query) && ($letter ?? '') == (string)$i) ? 'active' : '' ?>"
                           data-letter="<?= $i ?>"
                           title="Angka <?= $i ?>"
                           style="min-width: 32px; height: 38px; font-size: 0.82rem;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- KONTEN: JIKA TIPE BROWSE = SUBJEK (DIREKTORI SUBJEK 000-900)  -->
    <!-- ============================================================= -->
    <?php if ($browse_type === 'subject'): ?>

        <!-- Live Filter Counter Info untuk Subjek -->
        <div id="filterResultInfoDdc" class="d-flex align-items-center justify-content-between mb-3 text-muted small px-1" style="display: none !important;">
            <div>
                <i class="fa-solid fa-filter text-primary me-1"></i>
                Menampilkan <strong id="filterMatchCountDdc" class="text-dark">0</strong> divisi subjek yang cocok
            </div>
            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="resetFilterBtnDdc">
                <i class="fa-solid fa-rotate-left me-1"></i>Reset Tampilan
            </button>
        </div>

        <!-- DDC / Subject Directory Container (000 - 900) -->
        <div id="ddcDirectory">
            <?php
            $totalDivisionsRendered = 0;
            foreach ($ddc_hierarchy ?? [] as $mCode => $mClass):
                $totalDivisionsRendered += count($mClass['divisions']);
            ?>
                <div class="ddc-class-section mb-5" data-class-group="<?= esc($mCode) ?>" id="ddc-class-<?= esc($mCode) ?>">
                    <div class="ddc-class-header">
                        <div class="ddc-class-title-wrap">
                            <div class="ddc-class-badge" style="background: <?= esc($mClass['color']) ?>;">
                                <?= esc($mCode) ?>
                            </div>
                            <div>
                                <h2 class="ddc-class-title">
                                    <i class="fa-solid <?= esc($mClass['icon']) ?> me-2" style="color: <?= esc($mClass['color']) ?>;"></i>
                                    Kelas <?= esc($mCode) ?>: <?= esc($mClass['name']) ?>
                                </h2>
                                <span class="text-muted small class-summary-text">
                                    <?= number_format($mClass['count']) ?> koleksi perpustakaan &bull; 10 Divisi (<?= esc($mCode) ?> - <?= esc(substr($mCode, 0, 1) . '90') ?>)
                                </span>
                            </div>
                        </div>
                        <a href="<?= base_url('opac?DeweyNo=' . substr($mCode, 0, 1)) ?>" 
                           class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-none d-md-inline-flex align-items-center gap-1"
                           title="Lihat seluruh koleksi di kelas <?= esc($mCode) ?>">
                            <span>Semua Kelas <?= esc($mCode) ?></span>
                            <i class="fa-solid fa-arrow-right small"></i>
                        </a>
                    </div>

                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                        <?php foreach ($mClass['divisions'] as $div): ?>
                            <?php $hasBooks = ($div['count'] > 0); ?>
                            <div class="col ddc-division-item" 
                                 data-code="<?= esc($div['code']) ?>" 
                                 data-name="<?= esc(mb_strtolower($div['name'])) ?>"
                                 data-has-books="<?= $hasBooks ? '1' : '0' ?>">
                                <a href="<?= base_url('opac?DeweyNo=' . $div['code']) ?>"
                                   class="ddc-card shadow-sm <?= $hasBooks ? '' : 'is-empty' ?>"
                                   title="Lihat koleksi subjek <?= esc($div['code']) ?> - <?= esc($div['name']) ?>">
                                    <div>
                                        <div class="ddc-card-top">
                                            <span class="ddc-code-pill" style="border-left: 3px solid <?= esc($mClass['color']) ?>;">
                                                <?= esc($div['code']) ?>
                                            </span>
                                            <div class="flex-grow-1">
                                                <span class="ddc-name"><?= esc($div['name']) ?></span>
                                                <?php if (!empty($div['sections'])): ?>
                                                    <div class="ddc-sections-preview">
                                                        <?php foreach ($div['sections'] as $sec): ?>
                                                            <span class="ddc-sec-tag" title="<?= number_format($sec['count']) ?> koleksi">
                                                                <?= esc($sec['code']) ?> <span class="text-muted">(<?= number_format($sec['count']) ?>)</span>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ddc-card-footer">
                                        <span class="ddc-count-badge <?= $hasBooks ? 'has-items' : 'no-items' ?>">
                                            <i class="fa-solid fa-book me-1 small"></i>
                                            <?= number_format($div['count']) ?> koleksi
                                        </span>
                                        <span class="ddc-arrow">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Empty State Subjek -->
            <div id="emptySearchStateDdc" class="text-center py-5 my-4 bg-white rounded-4 border border-light shadow-sm" style="display: none;">
                <div class="mb-3 text-secondary opacity-50">
                    <i class="fa-solid fa-shapes fa-4x"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Subjek Tidak Ditemukan</h4>
                <p class="text-muted mb-4" id="emptyStateMessageDdc">
                    Tidak ada nomor atau nama divisi subjek yang cocok dengan kata kunci pencarian Anda.
                </p>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="emptyResetBtnDdc">
                    <i class="fa-solid fa-rotate-left me-1"></i> Tampilkan Seluruh Kelas Subjek
                </button>
            </div>
        </div>

    <!-- ============================================================= -->
    <!-- KONTEN: JIKA TIPE BROWSE = JUDUL ATAU PENGARANG                -->
    <!-- ============================================================= -->
    <?php else: ?>

        <div id="resultsWrapper">
            <?= view('Opac\Views\browse_results', get_defined_vars()) ?>
        </div>

    <?php endif; ?>

</div>

<!-- Floating Back to Top Button -->
<button type="button" class="btn-back-to-top" id="backToTopBtn" title="Kembali ke atas" aria-label="Kembali ke atas">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
// --- Tampilan Switcher (Grid / List / Table) ---
function switchView(viewType) {
    document.querySelectorAll('.view-container').forEach(el => el.style.display = 'none');
    const target = document.getElementById(viewType + 'View');
    if (target) target.style.display = 'block';

    // Update active button state
    document.querySelectorAll('.view-mode-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById('btnView' + viewType.charAt(0).toUpperCase() + viewType.slice(1));
    if (activeBtn) activeBtn.classList.add('active');

    try {
        localStorage.setItem('opac_browse_view_mode', viewType);
    } catch (e) {}
}

function exportBrowseResults(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    params.set('export', 'browse_results');
    window.open('<?= base_url('opac/export') ?>?' + params.toString(), '_blank');
}

// Global reset helper untuk judul / pengarang
function resetBrowseSearch() {
    const searchInput = document.getElementById('browseSearchInput');
    const searchClear = document.getElementById('browseSearchClear');
    if (searchInput) {
        searchInput.value = '';
    }
    if (searchClear) {
        searchClear.style.display = 'none';
    }
    if (typeof window.performBrowseLiveSearch === 'function') {
        window.performBrowseLiveSearch('');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Restore View Mode preference
    const savedMode = localStorage.getItem('opac_browse_view_mode') || 'grid';
    switchView(savedMode);

    // --- Search Box Logic ---
    const searchInput   = document.getElementById('browseSearchInput');
    const searchClear   = document.getElementById('browseSearchClear');
    const searchForm    = document.getElementById('browseSearchForm');
    const browseType    = '<?= esc($browse_type) ?>';
    const currentLetter = '<?= esc($letter ?? 'A') ?>';

    function toggleClearBtn() {
        if (searchInput && searchClear) {
            searchClear.style.display = searchInput.value.trim().length > 0 ? 'block' : 'none';
        }
    }

    // --- LIVE AJAX SEARCH UNTUK JUDUL & PENGARANG ---
    let liveSearchDebounce;
    let liveSearchAbortController = null;

    function resetBrowseSearch() {
        if (searchInput) {
            searchInput.value = '';
            toggleClearBtn();
        }
        if (browseType === 'subject') {
            applySubjectFilter();
        } else if (browseType === 'ddc') {
            applyDdcFilter();
        } else {
            performBrowseLiveSearch('');
        }
    }
    window.resetBrowseSearch = resetBrowseSearch;

    // --- FUNGSI HIGHLIGHT BLUR ELEGAN (FROSTED GLASS BLUR BIRU/PUTIH) ---
    function removeBrowseHighlight() {
        const marks = document.querySelectorAll('mark.browse-highlight-blur');
        marks.forEach(mark => {
            const parent = mark.parentNode;
            if (parent) {
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            }
        });
    }

    function highlightNode(node, regex) {
        if (node.nodeType === Node.TEXT_NODE) {
            const text = node.nodeValue;
            if (regex.test(text)) {
                regex.lastIndex = 0;
                const fragment = document.createDocumentFragment();
                let lastIdx = 0;
                let match;
                while ((match = regex.exec(text)) !== null) {
                    if (match.index > lastIdx) {
                        fragment.appendChild(document.createTextNode(text.substring(lastIdx, match.index)));
                    }
                    const mark = document.createElement('mark');
                    mark.className = 'browse-highlight-blur';
                    mark.textContent = match[0];
                    fragment.appendChild(mark);
                    lastIdx = regex.lastIndex;
                }
                if (lastIdx < text.length) {
                    fragment.appendChild(document.createTextNode(text.substring(lastIdx)));
                }
                node.parentNode.replaceChild(fragment, node);
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            if (node.tagName.toLowerCase() === 'mark' || node.classList.contains('browse-highlight-blur')) {
                return;
            }
            const children = Array.from(node.childNodes);
            children.forEach(child => highlightNode(child, regex));
        }
    }

    function applyBrowseHighlight(query) {
        removeBrowseHighlight();
        if (!query) return;
        const cleanQuery = query.trim();
        if (!cleanQuery) return;

        const tokens = cleanQuery.split(/\s+/).filter(t => t.length > 0);
        if (tokens.length === 0) return;

        const escaped = tokens.map(t => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
        const pattern = new RegExp('(' + escaped.join('|') + ')', 'gi');

        const selectors = [
            '.opac-cover-title',
            '.opac-cover-author',
            '.opac-book-heading a',
            '.opac-book-author',
            '.catalog-list-title',
            '.catalog-list-author',
            '.catalog-table-title',
            '.catalog-table-author',
            '.subject-name',
            '.ddc-name',
            '.ddc-code-pill'
        ];

        const elements = document.querySelectorAll(selectors.join(', '));
        elements.forEach(el => {
            highlightNode(el, pattern);
        });
    }

    window.applyBrowseHighlight = applyBrowseHighlight;
    window.removeBrowseHighlight = removeBrowseHighlight;

    function performBrowseLiveSearch(query) {
        if (browseType === 'subject') {
            if (typeof applyDdcFilter === 'function') {
                applyDdcFilter();
            }
            return;
        }

        const searchIcon = document.getElementById('browseSearchIcon') || document.querySelector('.browse-search-icon');
        if (searchIcon) {
            searchIcon.className = 'fa-solid fa-circle-notch fa-spin browse-search-icon';
        }

        if (liveSearchAbortController) {
            liveSearchAbortController.abort();
        }
        liveSearchAbortController = new AbortController();

        const params = new URLSearchParams();
        params.set('type', browseType);
        params.set('letter', currentLetter);
        if (query.length > 0) {
            params.set('q', query);
        }
        params.set('ajax', '1');

        fetch('<?= base_url('opac/browse') ?>?' + params.toString(), {
            signal: liveSearchAbortController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (searchIcon) {
                searchIcon.className = 'fa-solid fa-magnifying-glass browse-search-icon';
            }
            if (data.status === 'success') {
                const resultsWrapper = document.getElementById('resultsWrapper');
                if (resultsWrapper) {
                    resultsWrapper.innerHTML = data.html;
                }

                // Update status aktif tombol huruf alfabet
                const alphabetButtons = document.querySelectorAll('.alphabet-btn');
                if (query.length > 0) {
                    // Ketika mencari kata kunci (misal: "tere"), hilangkan status aktif huruf awal (misal: "A")
                    alphabetButtons.forEach(btn => btn.classList.remove('active'));
                } else {
                    // Ketika pencarian dibersihkan, kembalikan status aktif huruf saat ini
                    alphabetButtons.forEach(btn => {
                        const btnLetter = btn.getAttribute('data-letter') || btn.textContent.trim();
                        if (btnLetter === currentLetter) {
                            btn.classList.add('active');
                        } else {
                            btn.classList.remove('active');
                        }
                    });
                }

                // Terapkan highlight blur biru/putih pada judul & pengarang yang cocok
                applyBrowseHighlight(query);

                // Terapkan kembali view mode (grid/list/table) yang sedang dipilih
                const currentViewMode = localStorage.getItem('opac_browse_view_mode') || 'grid';
                switchView(currentViewMode);
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                if (searchIcon) {
                    searchIcon.className = 'fa-solid fa-magnifying-glass browse-search-icon';
                }
            }
        });
    }

    window.performBrowseLiveSearch = performBrowseLiveSearch;

    if (searchInput) {
        toggleClearBtn();
        searchInput.addEventListener('input', function () {
            toggleClearBtn();
            if (browseType === 'subject') {
                clearTimeout(liveSearchDebounce);
                liveSearchDebounce = setTimeout(applyDdcFilter, 60);
            } else {
                clearTimeout(liveSearchDebounce);
                liveSearchDebounce = setTimeout(function () {
                    performBrowseLiveSearch(searchInput.value.trim());
                }, 220);
            }
        });
    }

    if (searchClear) {
        searchClear.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
                toggleClearBtn();
                if (browseType === 'subject') {
                    applyDdcFilter();
                } else {
                    performBrowseLiveSearch('');
                }
                searchInput.focus();
            }
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (browseType === 'subject') {
                applyDdcFilter();
            } else {
                performBrowseLiveSearch(searchInput ? searchInput.value.trim() : '');
            }
        });
    }

    // --- Logika Khusus Subjek (Klasifikasi Standar 000 - 900: Client-side Instant Filter & Navigasi Kelas) ---
    if (browseType === 'subject') {
        const ddcNavButtons       = document.querySelectorAll('.ddc-class-nav-btn, #ddcAllBtn');
        const ddcClassSections    = document.querySelectorAll('.ddc-class-section');
        const emptyStateDdc       = document.getElementById('emptySearchStateDdc');
        const filterResultInfoDdc = document.getElementById('filterResultInfoDdc');
        const filterMatchCountDdc = document.getElementById('filterMatchCountDdc');
        const resetFilterBtnDdc   = document.getElementById('resetFilterBtnDdc');
        const emptyResetBtnDdc    = document.getElementById('emptyResetBtnDdc');

        let currentDdcClassFilter = 'all';

        function applyDdcFilter() {
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let totalVisible = 0;

            ddcClassSections.forEach(section => {
                const sectionClass = section.getAttribute('data-class-group');
                const items        = section.querySelectorAll('.ddc-division-item');
                let sectionVisibleCount = 0;

                // Filter tombol kelas utama (000, 100, 200, ... 900)
                if (currentDdcClassFilter !== 'all' && sectionClass !== currentDdcClassFilter) {
                    section.style.display = 'none';
                    return;
                }

                items.forEach(item => {
                    const code = (item.getAttribute('data-code') || '').toLowerCase();
                    const name = (item.getAttribute('data-name') || '').toLowerCase();
                    let matches = true;

                    if (query.length > 0) {
                        // Cocokkan nomor kode (misal: 020 atau 02) atau nama divisi (misal: perpustakaan, hukum)
                        if (!code.includes(query) && !name.includes(query)) {
                            matches = false;
                        }
                    }

                    if (matches) {
                        item.style.display = '';
                        sectionVisibleCount++;
                        totalVisible++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (sectionVisibleCount > 0) {
                    section.style.display = '';
                    const summaryText = section.querySelector('.class-summary-text');
                    if (summaryText && query.length > 0) {
                        summaryText.textContent = `${sectionVisibleCount} divisi cocok dengan pencarian`;
                    }
                } else {
                    section.style.display = 'none';
                }
            });

            if (filterResultInfoDdc && filterMatchCountDdc) {
                if (query.length > 0 || currentDdcClassFilter !== 'all') {
                    filterResultInfoDdc.style.setProperty('display', 'flex', 'important');
                    filterMatchCountDdc.textContent = totalVisible.toLocaleString('id-ID');
                } else {
                    filterResultInfoDdc.style.setProperty('display', 'none', 'important');
                }
            }

            if (emptyStateDdc) {
                emptyStateDdc.style.display = totalVisible === 0 ? 'block' : 'none';
            }

            // Highlight nomor dan nama divisi DDC yang cocok
            applyBrowseHighlight(query);
        }

        window.applyDdcFilter = applyDdcFilter;

        ddcNavButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                ddcNavButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const targetClass = this.getAttribute('data-ddc-class');
                currentDdcClassFilter = targetClass;

                applyDdcFilter();

                if (targetClass !== 'all') {
                    const targetEl = document.getElementById(`ddc-class-${targetClass}`);
                    if (targetEl && targetEl.style.display !== 'none') {
                        const navHeight = 160;
                        const elPos = targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight;
                        window.scrollTo({ top: elPos, behavior: 'smooth' });
                    }
                }
            });
        });

        function resetDdcAll() {
            if (searchInput) searchInput.value = '';
            currentDdcClassFilter = 'all';
            ddcNavButtons.forEach(b => {
                if (b.getAttribute('data-ddc-class') === 'all') {
                    b.classList.add('active');
                } else {
                    b.classList.remove('active');
                }
            });
            toggleClearBtn();
            applyDdcFilter();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        if (resetFilterBtnDdc) resetFilterBtnDdc.addEventListener('click', resetDdcAll);
        if (emptyResetBtnDdc)  emptyResetBtnDdc.addEventListener('click', resetDdcAll);

        if (searchInput && searchInput.value.trim().length > 0) {
            applyDdcFilter();
        }
    }

    // Terapkan highlight awal jika ada query di URL / input (Judul / Pengarang / Subjek)
    if (searchInput && searchInput.value.trim().length > 0) {
        applyBrowseHighlight(searchInput.value.trim());
    }

    // --- Floating Back to Top Button ---
    const backToTopBtn = document.getElementById('backToTopBtn');
    if (backToTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 400) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
</script>
<?= $this->endSection() ?>
