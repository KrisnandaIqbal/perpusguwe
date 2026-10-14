<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?php
// Sidebar data
$_db = db_connect();
$_perpusNama = $_db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow();
$_perpusAlamat = $_db->table('settingparameters')->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow();
$_perpusPhone = $_db->table('settingparameters')->where('Name', 'Phone')->get()->getRow();
$_perpusEmail = $_db->table('settingparameters')->where('Name', 'EmailPerpustakaan')->get()->getRow();
$_namaPerpus = $_perpusNama ? $_perpusNama->Value : 'Perpustakaan';
$_alamatPerpus = $_perpusAlamat ? $_perpusAlamat->Value : 'Jl. Jendral Gatot Subroto Kav. 51, Jakarta Selatan 12950';
$_phonePerpus = $_perpusPhone ? $_perpusPhone->Value : '';
$_emailPerpus = $_perpusEmail ? $_perpusEmail->Value : '';

// Identify NIP and NIK IDs from master_jenis_identitas
$_nipId = '';
$_nikId = '';
if (!empty($jenis_identitas)) {
    foreach ($jenis_identitas as $ji) {
        if (stripos($ji->Nama, 'NIP') !== false) {
            $_nipId = $ji->id;
        }
        if (stripos($ji->Nama, 'NIK') !== false || !empty($ji->IsNIK)) {
            $_nikId = $ji->id;
        }
    }
}
?>

<?= $this->section('style') ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

<style>
    /* ─────────────────────────────────────────────────────────────
       DESIGN SYSTEM TOKENS — Modern Editorial Minimal ("Digital Library")
       Linear / Stripe / Vercel inspired aesthetic
       ───────────────────────────────────────────────────────────── */
    :root {
        /* Palette: Deep obsidian navy brand, single calm electric royal blue accent */
        --navy-950: #0a0f1d;
        --navy-900: #0f172a;
        --navy-850: #131c31;
        --navy-800: #1e293b;
        --navy-700: #334155;

        --accent-50:  #eff6ff;
        --accent-100: #dbeafe;
        --accent-200: #bfdbfe;
        --accent-500: #3b82f6;
        --accent-600: #2563eb;
        --accent-700: #1d4ed8;

        /* Warm editorial off-white surface, crisp white cards */
        --bg-page: #fbfbfa;
        --bg-card: #ffffff;
        --bg-card-hover: #fafafa;
        --bg-input: #ffffff;

        /* Ultra-fine borders (1px hairline) */
        --border-subtle: #e6e8eb;
        --border-card: #e5e7eb;
        --border-input: #d1d5db;
        --border-focus: #2563eb;

        /* Typography colors */
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #64748b;
        --text-subtle: #94a3b8;

        /* Status colors */
        --emerald-600: #059669;
        --emerald-50:  #ecfdf5;
        --emerald-200: #a7f3d0;
        --rose-600:    #dc2626;
        --rose-50:     #fef2f2;
        --rose-200:    #fecaca;

        /* Typography */
        --font-heading: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;

        /* Shadows: Flat editorial, sharp 1px hairline border + delicate depth */
        --shadow-subtle: 0 1px 2px rgba(15, 23, 42, 0.03);
        --shadow-card:   0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
        --shadow-float:  0 10px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        --ring-focus:    0 0 0 3px rgba(37, 99, 235, 0.14);

        /* Transitions */
        --transition-fast: 160ms cubic-bezier(0.16, 1, 0.3, 1);
        --transition-normal: 220ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* ─── Global Reset & Typography in Context ─── */
    body {
        font-family: var(--font-body);
        background-color: var(--bg-page);
        color: var(--text-primary);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ─── Page Container & Layout ─── */
    .rf-wrap {
        background-color: var(--bg-page);
        min-height: calc(100vh - 65px);
        padding-top: 100px;
        padding-bottom: 72px;
        overflow-x: hidden;
    }

    .rf-wrap .container {
        max-width: 1120px;
    }

    /* ─── Breadcrumb ─── */
    .rf-breadcrumb {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    .rf-breadcrumb a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color var(--transition-fast);
    }

    .rf-breadcrumb a:hover {
        color: var(--accent-600);
    }

    .rf-breadcrumb .sep {
        color: var(--text-subtle);
        font-size: 0.72rem;
    }

    .rf-breadcrumb .current {
        color: var(--navy-800);
        font-weight: 500;
    }

    /* ─── Title Section ─── */
    .rf-title-section {
        margin-bottom: 24px;
    }

    .rf-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 24px;
    }

    .rf-title-main {
        flex: 1;
        min-width: 0;
    }

    .rf-title {
        font-family: var(--font-heading);
        font-size: 1.85rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: var(--navy-950);
        line-height: 1.25;
        margin: 0 0 8px;
    }

    .rf-lead {
        font-size: 0.94rem;
        color: var(--text-secondary);
        line-height: 1.55;
        margin: 0;
        max-width: 620px;
    }

    /* Refined PDP Badge */
    .rf-pdp-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: 10px;
        box-shadow: var(--shadow-subtle);
        flex-shrink: 0;
    }

    .rf-pdp-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: var(--emerald-50);
        color: var(--emerald-600);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .rf-pdp-icon svg {
        width: 15px;
        height: 15px;
    }

    .rf-pdp-meta {
        display: flex;
        flex-direction: column;
    }

    .rf-pdp-meta strong {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--navy-900);
        line-height: 1.2;
    }

    .rf-pdp-meta span {
        font-size: 0.7rem;
        color: var(--text-muted);
        line-height: 1.2;
        margin-top: 2px;
    }

    /* ─── Progress Stepper (Connected Timeline) ─── */
    .rf-stepper {
        display: flex;
        align-items: center;
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 28px;
        box-shadow: var(--shadow-subtle);
    }

    .rf-step {
        display: flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        border: none;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        text-align: left;
        transition: background var(--transition-fast);
        color: inherit;
        text-decoration: none;
        flex: 1;
        min-width: 0;
    }

    .rf-step:hover {
        background: var(--bg-card-hover);
    }

    .rf-step:focus-visible {
        outline: none;
        box-shadow: var(--ring-focus);
    }

    .rf-step-num {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
        transition: all var(--transition-fast);
        background: var(--bg-page);
        color: var(--text-muted);
        border: 1px solid var(--border-subtle);
    }

    .rf-step-content {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .rf-step-label {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--text-muted);
        line-height: 1;
    }

    .rf-step-title {
        font-family: var(--font-heading);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--navy-900);
        line-height: 1.3;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .rf-title-desktop {
        display: inline;
    }

    .rf-title-mobile {
        display: none;
    }

    /* Active Step State */
    .rf-step.active .rf-step-num {
        background: var(--navy-950);
        color: #ffffff;
        border-color: var(--navy-950);
        box-shadow: 0 1px 3px rgba(10, 15, 29, 0.2);
    }

    .rf-step.active .rf-step-label {
        color: var(--accent-600);
    }

    .rf-step.active .rf-step-title {
        color: var(--navy-950);
        font-weight: 700;
    }

    /* Stepper Error State (Red Alert) */
    .rf-step.has-error .rf-step-num {
        background: #fee2e2 !important;
        color: #dc2626 !important;
        border-color: #f87171 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    .rf-step.has-error .rf-step-label,
    .rf-step.has-error .rf-step-title {
        color: #dc2626 !important;
    }

    /* Stepper Connector Line */
    .rf-step-line {
        height: 1px;
        flex: 0 0 28px;
        background: var(--border-subtle);
        margin: 0 4px;
    }

    /* ─── Main Two-Column Layout ─── */
    .rf-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 32px;
        align-items: start;
    }

    /* ─── Form Column ─── */
    .rf-form-col {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ─── Section Card ─── */
    .rf-section {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: 14px;
        padding: 28px;
        box-shadow: var(--shadow-card);
        transition: border-color var(--transition-fast);
        scroll-margin-top: 80px;
    }

    .rf-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 6px;
    }

    .rf-section-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rf-section-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 7px;
        background: var(--navy-950);
        color: #ffffff;
        font-family: var(--font-heading);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        flex-shrink: 0;
    }

    .rf-section-title {
        font-family: var(--font-heading);
        font-size: 1.08rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--navy-950);
        margin: 0;
    }

    .rf-required-tag {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        background: var(--bg-page);
        border: 1px solid var(--border-subtle);
        padding: 3px 8px;
        border-radius: 6px;
    }

    .rf-section-desc {
        font-size: 0.84rem;
        color: var(--text-muted);
        margin: 0 0 22px;
        padding-left: 38px;
        line-height: 1.5;
    }

    /* ─── Selectable Radio Cards (ASN / Non-ASN) ─── */
    .rf-radio-cards {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .rf-radio-card {
        position: relative;
    }

    .rf-radio-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .rf-card-choice {
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 18px 20px;
        background: #ffffff;
        border: 2px solid var(--border-card);
        border-radius: 14px;
        cursor: pointer;
        transition: all var(--transition-fast);
        user-select: none;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .rf-card-choice:hover {
        border-color: #94a3b8;
        background: #fafbfe;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -4px rgba(15, 23, 42, 0.08);
    }

    .rf-card-choice-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        gap: 10px;
    }

    .rf-card-icon-group {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .rf-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--bg-page);
        border: 1px solid var(--border-subtle);
        color: var(--navy-700);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all var(--transition-fast);
    }

    .rf-card-icon.is-asn {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .rf-card-icon.is-nonasn {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #047857;
    }

    .rf-card-icon svg {
        width: 20px;
        height: 20px;
    }

    .rf-card-chip {
        display: inline-flex;
        align-items: center;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .rf-card-chip.is-asn {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .rf-card-chip.is-nonasn {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .rf-card-radio-mark {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid var(--border-input);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast);
        background: #ffffff;
        flex-shrink: 0;
        color: transparent;
    }

    .rf-card-radio-mark svg {
        width: 12px;
        height: 12px;
        stroke-width: 3;
        transition: transform var(--transition-fast);
        transform: scale(0.6);
    }

    .rf-card-choice-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .rf-card-title-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }

    .rf-card-title {
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--navy-900);
        line-height: 1.3;
    }

    .rf-card-subtitle {
        font-size: 0.73rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .rf-card-desc {
        font-size: 0.79rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-top: 2px;
    }

    .rf-card-footer {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--border-subtle);
        font-size: 0.72rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .rf-card-footer svg {
        width: 13px;
        height: 13px;
        color: var(--accent-600);
    }

    /* Selected State (High clarity, glowing border, vibrant check) */
    .rf-radio-card input[type="radio"]:checked + .rf-card-choice {
        border-color: var(--accent-600);
        background-color: #f8faff;
        box-shadow: 0 0 0 1px var(--accent-600), 0 8px 24px -4px rgba(37, 99, 235, 0.16);
    }

    .rf-radio-card input[type="radio"]:checked + .rf-card-choice .rf-card-radio-mark {
        border-color: var(--accent-600);
        background: var(--accent-600);
        color: #ffffff;
    }

    .rf-radio-card input[type="radio"]:checked + .rf-card-choice .rf-card-radio-mark svg {
        transform: scale(1);
    }

    .rf-radio-card input[type="radio"]:checked + .rf-card-choice .rf-card-title {
        color: var(--accent-700);
    }

    .rf-radio-card input[type="radio"]:focus-visible + .rf-card-choice {
        box-shadow: var(--ring-focus);
    }

    /* ─── Form Fields & Controls ─── */
    .rf-field {
        margin-bottom: 20px;
    }

    .rf-field:last-child {
        margin-bottom: 0;
    }

    .rf-field-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 7px;
    }

    .rf-label {
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--navy-900);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 7px;
    }

    .rf-field-header .rf-label {
        margin-bottom: 0;
    }

    .rf-label .req {
        color: var(--rose-600);
        font-weight: 700;
    }

    .rf-sublabel {
        font-size: 0.74rem;
        color: var(--text-muted);
        font-weight: 400;
        margin-left: auto;
    }

    /* Live Digit Badge */
    .rf-digit-badge {
        font-family: var(--font-mono);
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
        background: var(--bg-page);
        color: var(--text-muted);
        border: 1px solid var(--border-subtle);
        transition: all var(--transition-fast);
        letter-spacing: -0.02em;
    }

    .rf-digit-badge.is-valid {
        background: var(--emerald-50) !important;
        color: var(--emerald-600) !important;
        border-color: var(--emerald-200) !important;
    }

    .rf-digit-badge.is-error {
        background: #fee2e2 !important;
        color: #dc2626 !important;
        border-color: #fca5a5 !important;
    }

    /* Field Status Badge (Valid / Error in label header) */
    .rf-badge-status {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-page);
        color: var(--text-muted);
        transition: all var(--transition-fast);
        display: none;
        align-items: center;
        gap: 4px;
        letter-spacing: -0.01em;
    }

    .rf-badge-status.is-error {
        display: inline-flex;
        background: #fee2e2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    .rf-badge-status.is-valid {
        display: inline-flex;
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }

    /* Inputs, Select, Textarea (Strict 48px height, 10px radius) */
    .rf-input,
    .rf-select {
        width: 100%;
        height: 48px;
        padding: 0 14px;
        font-family: inherit;
        font-size: 0.9rem;
        color: var(--navy-950);
        background: var(--bg-input);
        border: 1px solid var(--border-input);
        border-radius: 10px;
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        box-sizing: border-box;
    }

    textarea.rf-input {
        height: auto;
        min-height: 96px;
        padding: 12px 14px;
        line-height: 1.5;
        resize: vertical;
    }

    .rf-input::placeholder {
        color: var(--text-subtle);
        font-size: 0.88rem;
    }

    .rf-input:hover,
    .rf-select:hover {
        border-color: #94a3b8;
    }

    .rf-input:focus,
    .rf-select:focus {
        outline: none;
        border-color: var(--accent-600);
        box-shadow: var(--ring-focus);
    }

    /* Red Error State for Inputs */
    .rf-input.has-error,
    .rf-select.has-error,
    .rf-select-trigger.has-error {
        border-color: #ef4444 !important;
        background-color: #fff8f8 !important;
    }

    .rf-input.has-error:focus,
    .rf-select.has-error:focus,
    .rf-select-trigger.has-error:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
    }

    .rf-input.is-valid,
    .rf-select-trigger.is-valid {
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }

    .rf-input.is-valid:focus,
    .rf-select-trigger.is-valid:focus {
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
    }

    /* Floating input status icons */
    .rf-input-wrap {
        position: relative;
        width: 100%;
    }

    .rf-input-wrap .rf-input {
        padding-right: 40px;
    }

    .rf-phone-group.rf-input-wrap .rf-input {
        padding-right: 40px;
    }

    .rf-input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        pointer-events: none;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .rf-input-icon svg {
        width: 18px;
        height: 18px;
    }

    .rf-input.has-error ~ .rf-input-icon.is-error,
    .rf-select-trigger.has-error .rf-input-icon.is-error {
        display: flex;
        color: #ef4444;
    }

    .rf-input.is-valid ~ .rf-input-icon.is-valid {
        display: flex;
        color: #10b981;
    }

    /* Field Feedback Message below inputs */
    .rf-field-feedback {
        display: none;
        align-items: flex-start;
        gap: 6px;
        font-size: 0.77rem;
        line-height: 1.45;
        margin-top: 6px;
    }

    .rf-field-feedback.is-error {
        display: flex !important;
        color: #dc2626 !important;
        font-weight: 500;
    }

    .rf-field-feedback.is-valid {
        display: flex !important;
        color: #059669 !important;
        font-weight: 500;
    }

    .rf-field-feedback svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ─── Top Error Summary Banner (Red Editorial Box) ─── */
    .rf-error-summary {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.08);
        animation: rfSlideDown 200ms ease-out;
    }

    @keyframes rfSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .rf-error-summary-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .rf-error-summary-title-wrap {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .rf-error-summary-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .rf-error-summary-icon svg {
        width: 18px;
        height: 18px;
    }

    .rf-error-summary-title {
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 700;
        color: #991b1b;
        margin: 0 0 4px;
        line-height: 1.3;
    }

    .rf-error-summary-desc {
        font-size: 0.82rem;
        color: #b91c1c;
        margin: 0;
        line-height: 1.5;
    }

    .rf-error-summary-badge {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #b91c1c;
        background: #fee2e2;
        border: 1px solid #fca5a5;
        padding: 4px 10px;
        border-radius: 6px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .rf-error-summary-list {
        margin: 14px 0 0;
        padding-left: 44px;
        list-style-type: disc;
    }

    .rf-error-summary-list li {
        font-size: 0.82rem;
        color: #7f1d1d;
        line-height: 1.6;
        margin-bottom: 4px;
    }

    .rf-error-summary-list li strong {
        color: #991b1b;
        font-weight: 600;
    }

    .rf-agree-box.has-error {
        border-color: #ef4444 !important;
        background-color: #fff8f8 !important;
        box-shadow: 0 0 0 1px #fca5a5;
    }

    /* Custom Select Chevron */
    .rf-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px;
        padding-right: 38px;
        cursor: pointer;
    }

    /* ─── Custom Responsive Select Dropdown (Strict 100% Width, Zero Overflow) ─── */
    .rf-custom-select {
        position: relative;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .rf-select-native {
        position: absolute !important;
        opacity: 0 !important;
        width: 1px !important;
        height: 1px !important;
        pointer-events: none !important;
        clip: rect(0,0,0,0) !important;
        margin: -1px !important;
    }

    .rf-select-trigger {
        width: 100%;
        max-width: 100%;
        height: 48px;
        padding: 0 14px;
        font-family: inherit;
        font-size: 0.9rem;
        color: var(--navy-950);
        background: var(--bg-input);
        border: 1px solid var(--border-input);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
        text-align: left;
        box-sizing: border-box;
        transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
        user-select: none;
    }

    @media (max-width: 768px) {
        .rf-select-trigger {
            font-size: 16px !important;
            height: 48px;
        }
    }

    .rf-select-trigger:hover {
        border-color: #94a3b8;
    }

    .rf-select-trigger:focus,
    .rf-custom-select.is-open .rf-select-trigger {
        outline: none;
        border-color: var(--accent-600);
        box-shadow: var(--ring-focus);
    }

    .rf-select-trigger.has-error {
        border-color: var(--rose-600) !important;
        background-color: #fff9f9;
    }

    .rf-select-value {
        flex: 1;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--navy-950);
        font-weight: 500;
    }

    .rf-select-value.is-placeholder {
        color: var(--text-subtle);
        font-weight: 400;
    }

    .rf-select-arrow {
        width: 16px;
        height: 16px;
        color: var(--text-muted);
        flex-shrink: 0;
        transition: transform var(--transition-fast);
    }

    .rf-custom-select.is-open .rf-select-arrow {
        transform: rotate(180deg);
        color: var(--accent-600);
    }

    .rf-select-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        width: 100%;
        max-width: 100%;
        background: #ffffff;
        border: 1px solid var(--border-card);
        border-radius: 12px;
        box-shadow: var(--shadow-float);
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 100;
        padding: 5px;
        box-sizing: border-box;
        animation: rfSelectFade 120ms ease-out;
    }

    @keyframes rfSelectFade {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .rf-custom-select.is-open .rf-select-dropdown {
        display: block;
    }

    .rf-select-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 0.86rem;
        color: var(--navy-900);
        cursor: pointer;
        line-height: 1.4;
        transition: background var(--transition-fast);
        box-sizing: border-box;
        width: 100%;
    }

    .rf-select-option .rf-opt-text {
        flex: 1;
        min-width: 0;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .rf-select-option:hover {
        background: var(--bg-page);
    }

    .rf-select-option.is-selected {
        background: var(--accent-50);
        color: var(--accent-600);
        font-weight: 600;
    }

    .rf-opt-check {
        width: 14px;
        height: 14px;
        color: var(--accent-600);
        flex-shrink: 0;
        display: none;
    }

    .rf-select-option.is-selected .rf-opt-check {
        display: block;
    }

    .rf-select-option.is-placeholder {
        color: var(--text-muted);
        font-size: 0.8rem;
        border-bottom: 1px solid var(--border-subtle);
        margin-bottom: 4px;
        border-radius: 6px 6px 0 0;
    }

    /* Phone Group (+62 Prefix) */
    .rf-phone-group {
        display: flex;
        align-items: stretch;
    }

    .rf-phone-prefix {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--navy-800);
        background: var(--bg-page);
        border: 1px solid var(--border-input);
        border-right: none;
        border-radius: 10px 0 0 10px;
        user-select: none;
    }

    .rf-phone-group .rf-input {
        border-radius: 0 10px 10px 0;
    }

    .rf-phone-group .rf-input:focus {
        position: relative;
        z-index: 1;
    }

    /* Helper Hints & Error Messages */
    .rf-hint {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 0.77rem;
        color: var(--text-muted);
        margin-top: 6px;
        line-height: 1.4;
    }

    .rf-hint svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        margin-top: 1px;
        color: var(--text-subtle);
    }

    .rf-error-msg {
        display: none;
        font-size: 0.76rem;
        font-weight: 500;
        color: var(--rose-600);
        margin-top: 5px;
    }

    .rf-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    /* ─── Photo Upload Section (Clean Minimal Segmented) ─── */
    .rf-photo-toggle {
        display: inline-flex;
        background: var(--bg-page);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        padding: 3px;
        gap: 2px;
        margin-bottom: 12px;
    }

    .rf-photo-toggle label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-muted);
        padding: 5px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all var(--transition-fast);
        user-select: none;
    }

    .rf-photo-toggle input[type="radio"] {
        display: none;
    }

    .rf-photo-toggle label.is-checked {
        background: #ffffff;
        color: var(--navy-950);
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .rf-dropzone {
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 22px 18px;
        text-align: center;
        cursor: pointer;
        background: var(--bg-page);
        transition: all var(--transition-fast);
    }

    .rf-dropzone:hover {
        border-color: var(--accent-600);
        background: var(--accent-50);
    }

    .rf-dropzone-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid var(--border-subtle);
        color: var(--accent-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }

    .rf-dropzone-icon svg {
        width: 18px;
        height: 18px;
    }

    .rf-dropzone-text {
        font-size: 0.84rem;
        font-weight: 600;
        color: var(--navy-900);
    }

    .rf-dropzone-sub {
        font-size: 0.74rem;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .rf-photo-preview {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        background: var(--bg-page);
        border: 1px solid var(--border-card);
        border-radius: 10px;
    }

    .rf-photo-preview img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--border-subtle);
    }

    .rf-photo-remove {
        background: transparent;
        border: 1px solid var(--border-subtle);
        color: var(--rose-600);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .rf-photo-remove:hover {
        background: var(--rose-50);
        border-color: var(--rose-200);
    }

    /* ─── PDP Agreement Box ─── */
    .rf-agree-box {
        background: var(--bg-page);
        border: 1px solid var(--border-card);
        border-radius: 10px;
        padding: 16px;
        margin-top: 24px;
        margin-bottom: 24px;
    }

    .rf-agree-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .rf-agree-row input[type="checkbox"] {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        border: 1.5px solid var(--border-input);
        margin-top: 2px;
        accent-color: var(--accent-600);
        cursor: pointer;
        flex-shrink: 0;
    }

    .rf-agree-row label {
        font-size: 0.82rem;
        color: var(--text-secondary);
        line-height: 1.5;
        cursor: pointer;
        margin: 0;
    }

    .rf-agree-row label strong {
        color: var(--navy-900);
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .rf-agree-row a {
        color: var(--accent-600);
        text-decoration: underline;
        font-weight: 500;
    }

    .rf-agree-error {
        display: none;
        font-size: 0.76rem;
        color: var(--rose-600);
        margin-top: 8px;
        padding-left: 30px;
    }

    /* ─── Action Buttons ─── */
    .rf-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 18px;
        border-top: 1px solid var(--border-subtle);
    }

    .rf-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 46px;
        padding: 0 22px;
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all var(--transition-fast);
        border: none;
        user-select: none;
        text-decoration: none;
    }

    .rf-btn-ghost {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border-card);
    }

    .rf-btn-ghost:hover {
        background: var(--bg-page);
        color: var(--navy-950);
        border-color: #cbd5e1;
    }

    .rf-btn-primary {
        background: var(--navy-950);
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(10, 15, 29, 0.15);
    }

    .rf-btn-primary:hover:not(:disabled) {
        background: var(--navy-800);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(10, 15, 29, 0.15);
    }

    .rf-btn-primary:active:not(:disabled) {
        transform: translateY(0);
    }

    .rf-btn-primary:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
    }

    .rf-btn svg {
        width: 16px;
        height: 16px;
    }

    /* ─── Alert Box ─── */
    .rf-alert {
        display: none;
        padding: 14px 16px;
        border-radius: 10px;
        font-size: 0.84rem;
        line-height: 1.45;
        margin-top: 20px;
    }

    .rf-alert.error {
        background: var(--rose-50);
        border: 1px solid var(--rose-200);
        color: var(--rose-600);
    }

    .rf-alert.success {
        background: var(--emerald-50);
        border: 1px solid var(--emerald-200);
        color: var(--emerald-600);
    }

    /* ─── Sidebar ─── */
    .rf-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 80px;
    }

    .rf-side-card {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: 14px;
        padding: 22px;
        box-shadow: var(--shadow-card);
    }

    .rf-side-header {
        margin-bottom: 14px;
    }

    .rf-side-title {
        font-family: var(--font-heading);
        font-size: 0.94rem;
        font-weight: 700;
        letter-spacing: -0.015em;
        color: var(--navy-950);
        margin: 0 0 3px;
    }

    .rf-side-sub {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* Benefits List */
    .rf-benefits-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .rf-benefits-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.82rem;
        color: var(--text-secondary);
        line-height: 1.45;
    }

    .rf-benefit-icon {
        width: 18px;
        height: 18px;
        border-radius: 5px;
        background: var(--emerald-50);
        color: var(--emerald-600);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .rf-benefit-icon svg {
        width: 12px;
        height: 12px;
    }

    .rf-benefits-list strong {
        color: var(--navy-900);
        font-weight: 600;
    }

    /* Timeline Stepper in Sidebar */
    .rf-timeline {
        position: relative;
        padding-left: 20px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .rf-timeline::before {
        content: "";
        position: absolute;
        top: 6px;
        bottom: 6px;
        left: 6px;
        width: 1.5px;
        background: var(--border-subtle);
    }

    .rf-timeline-item {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .rf-timeline-node {
        position: absolute;
        left: -20px;
        top: 4px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: #ffffff;
        border: 2.5px solid var(--accent-600);
    }

    .rf-timeline-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--navy-900);
        line-height: 1.3;
    }

    .rf-timeline-desc {
        font-size: 0.74rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    /* Editorial Dark Help Card (Linear / Vercel style) */
    .rf-side-card.dark {
        background: var(--navy-950);
        border-color: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        box-shadow: 0 4px 20px rgba(10, 15, 29, 0.25);
    }

    .rf-side-card.dark .rf-side-title {
        color: #ffffff;
    }

    .rf-side-card.dark .rf-side-sub {
        color: #94a3b8;
    }

    .rf-dark-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 14px;
    }

    .rf-dark-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        font-size: 0.78rem;
        line-height: 1.4;
    }

    .rf-dark-item:first-child {
        padding-top: 0;
        border-top: none;
    }

    .rf-dark-icon {
        width: 16px;
        height: 16px;
        color: #38bdf8;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .rf-dark-content strong {
        display: block;
        color: #f1f5f9;
        font-size: 0.76rem;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .rf-dark-content span,
    .rf-dark-content a {
        color: #94a3b8;
        font-size: 0.76rem;
        text-decoration: none;
    }

    .rf-dark-content a:hover {
        color: #38bdf8;
        text-decoration: underline;
    }

    /* ─── Modal Dialog (Syarat & Ketentuan) ─── */
    .rf-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 15, 29, 0.6);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        padding: 20px;
    }

    .rf-modal {
        background: #ffffff;
        border: 1px solid var(--border-card);
        border-radius: 14px;
        max-width: 540px;
        width: 100%;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-float);
        animation: rfModalPop 180ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes rfModalPop {
        from {
            opacity: 0;
            transform: scale(0.97) translateY(8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .rf-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .rf-modal-head h3 {
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 0;
    }

    .rf-modal-close {
        background: transparent;
        border: none;
        font-size: 1.25rem;
        line-height: 1;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        transition: color var(--transition-fast);
    }

    .rf-modal-close:hover {
        color: var(--navy-950);
    }

    .rf-modal-body {
        padding: 20px 22px;
        overflow-y: auto;
        font-size: 0.84rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .rf-modal-body ol {
        padding-left: 20px;
        margin: 12px 0;
    }

    .rf-modal-body li {
        margin-bottom: 8px;
    }

    .rf-modal-body li strong {
        color: var(--navy-900);
    }

    .rf-scroll-note {
        font-size: 0.76rem;
        color: var(--text-muted);
        background: var(--bg-page);
        border: 1px solid var(--border-subtle);
        padding: 8px 12px;
        border-radius: 6px;
        margin-top: 14px;
    }

    .rf-modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 22px;
        border-top: 1px solid var(--border-subtle);
        background: var(--bg-page);
        border-radius: 0 0 14px 14px;
    }

    /* ─── Responsive Adjustments ─── */
    @media (max-width: 992px) {
        .rf-layout {
            grid-template-columns: 1fr;
            gap: 28px;
        }

        .rf-sidebar {
            position: static;
        }

        .rf-title-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .rf-pdp-pill {
            width: 100%;
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .rf-wrap {
            padding-top: 80px;
            padding-bottom: 48px;
        }

        .rf-title {
            font-size: 1.45rem;
            line-height: 1.25;
            margin-bottom: 6px;
        }

        .rf-lead {
            font-size: 0.88rem;
            line-height: 1.5;
        }

        /* Prevent auto-zoom on iOS Safari & Android */
        .rf-input,
        .rf-select {
            font-size: 16px !important;
            height: 48px;
        }

        .rf-phone-prefix {
            font-size: 15px;
            padding: 0 12px;
        }

        .rf-section {
            padding: 22px 18px;
        }

        .rf-section-title {
            font-size: 1.02rem;
        }
    }

    @media (max-width: 640px) {
        .rf-radio-cards {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .rf-card-choice {
            padding: 16px;
        }

        .rf-card-title {
            font-size: 1rem;
        }

        .rf-card-icon {
            width: 36px;
            height: 36px;
        }

        .rf-card-icon svg {
            width: 18px;
            height: 18px;
        }

        .rf-card-chip {
            font-size: 0.68rem;
            padding: 3px 6px;
        }

        .rf-grid-2 {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .rf-section {
            padding: 20px 16px;
            border-radius: 12px;
        }

        .rf-section-head {
            flex-wrap: wrap;
            gap: 8px;
        }

        .rf-section-desc {
            padding-left: 0;
            font-size: 0.8rem;
            margin-bottom: 18px;
        }

        .rf-field-header {
            flex-wrap: wrap;
            gap: 6px;
        }

        /* Mobile Stepper */
        .rf-stepper {
            padding: 8px 10px;
            gap: 4px;
            justify-content: space-between;
        }

        .rf-step {
            padding: 6px 4px;
            gap: 6px;
            flex: 1;
            justify-content: center;
        }

        .rf-step-num {
            width: 22px;
            height: 22px;
            font-size: 0.72rem;
            border-radius: 6px;
        }

        .rf-step-label {
            display: none !important;
        }

        .rf-step-title {
            display: block !important;
            font-size: 0.76rem;
            margin-top: 0;
            line-height: 1.2;
        }

        .rf-title-desktop {
            display: none !important;
        }

        .rf-title-mobile {
            display: inline !important;
        }

        .rf-step-line {
            flex: 0 0 12px;
            margin: 0 2px;
        }

        /* Actions Button Mobile Stacking */
        .rf-actions {
            flex-direction: column-reverse;
            gap: 10px;
            padding-top: 16px;
        }

        .rf-actions .rf-btn {
            width: 100%;
            height: 48px;
        }

        /* Prevent Text & Link Overflow */
        .rf-dark-content a,
        .rf-dark-content span {
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .rf-agree-box {
            padding: 14px;
        }

        .rf-agree-row {
            gap: 10px;
        }

        /* Modal Dialog Mobile */
        .rf-overlay {
            padding: 12px;
        }

        .rf-modal {
            max-height: 90vh;
            border-radius: 12px;
        }

        .rf-modal-head {
            padding: 14px 16px;
        }

        .rf-modal-body {
            padding: 16px;
        }

        .rf-modal-foot {
            padding: 12px 16px;
            flex-direction: column-reverse;
            gap: 8px;
        }

        .rf-modal-foot .rf-btn {
            width: 100%;
            height: 44px;
        }
    }

    @media (max-width: 380px) {
        .rf-wrap {
            padding-top: 76px;
            padding-bottom: 36px;
        }

        .rf-title {
            font-size: 1.28rem;
        }

        .rf-section {
            padding: 16px 12px;
        }

        .rf-card-choice {
            padding: 12px 12px;
        }

        .rf-step {
            padding: 4px 2px;
            gap: 4px;
        }

        .rf-step-num {
            width: 20px;
            height: 20px;
            font-size: 0.68rem;
            border-radius: 5px;
        }

        .rf-step-title {
            font-size: 0.7rem;
        }

        .rf-step-line {
            flex: 0 0 6px;
            margin: 0 1px;
        }

        .rf-pdp-pill {
            padding: 8px 10px;
            gap: 8px;
        }
    }

    /* Prefers Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        *, ::before, ::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="rf-wrap">
    <div class="container">

        <!-- Breadcrumb -->
        <nav class="rf-breadcrumb" aria-label="Breadcrumb">
            <a href="<?= base_url('opac') ?>">Beranda</a>
            <span class="sep">/</span>
            <a href="<?= base_url('opac') ?>">Layanan Pemustaka</a>
            <span class="sep">/</span>
            <span class="current">Registrasi Keanggotaan</span>
        </nav>

        <!-- Title Section -->
        <div class="rf-title-section">
            <div class="rf-title-row">
                <div class="rf-title-main">
                    <h1 class="rf-title">Registrasi Keanggotaan Perpustakaan</h1>
                    <p class="rf-lead">Akses koleksi cetak, repositori ilmiah, dan ruang baca digital terpadu melalui Kartu Anggota resmi.</p>
                </div>

                <div class="rf-pdp-pill" title="Kepatuhan UU Perlindungan Data Pribadi No. 27/2022">
                    <div class="rf-pdp-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="rf-pdp-meta">
                        <strong>Perlindungan Data UU PDP</strong>
                        <span>UU No. 27/2022 &bull; Verifikasi 1&times;24 Jam</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Stepper (Connected Timeline) -->
        <div class="rf-stepper" role="tablist" aria-label="Alur Pendaftaran">
            <button type="button" class="rf-step active" data-target="section-1" role="tab" aria-selected="true">
                <span class="rf-step-num">1</span>
                <div class="rf-step-content">
                    <span class="rf-step-label">Langkah 01</span>
                    <span class="rf-step-title">
                        <span class="rf-title-desktop">Kategori & Identitas</span>
                        <span class="rf-title-mobile">Kategori</span>
                    </span>
                </div>
            </button>
            
            <div class="rf-step-line" aria-hidden="true"></div>

            <button type="button" class="rf-step" data-target="section-2" role="tab" aria-selected="false">
                <span class="rf-step-num">2</span>
                <div class="rf-step-content">
                    <span class="rf-step-label">Langkah 02</span>
                    <span class="rf-step-title">
                        <span class="rf-title-desktop">Data Diri & Satuan Kerja</span>
                        <span class="rf-title-mobile">Data Diri</span>
                    </span>
                </div>
            </button>

            <div class="rf-step-line" aria-hidden="true"></div>

            <button type="button" class="rf-step" data-target="section-3" role="tab" aria-selected="false">
                <span class="rf-step-num">3</span>
                <div class="rf-step-content">
                    <span class="rf-step-label">Langkah 03</span>
                    <span class="rf-step-title">
                        <span class="rf-title-desktop">Kontak & Aktivasi</span>
                        <span class="rf-title-mobile">Kontak</span>
                    </span>
                </div>
            </button>
        </div>

        <!-- Main Two-Column Grid -->
        <div class="rf-layout">

            <!-- Form Column -->
            <div class="rf-form-col">
                <form id="formRegistrasi" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>

                    <!-- Error Summary Banner (Red Alert Box) -->
                    <div id="errorSummaryBox" class="rf-error-summary" style="display: none;">
                        <div class="rf-error-summary-head">
                            <div class="rf-error-summary-title-wrap">
                                <div class="rf-error-summary-icon">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="rf-error-summary-title" id="errorSummaryTitle">Pendaftaran Tidak Dapat Diproses</h3>
                                    <p class="rf-error-summary-desc">Sistem INLISLite mendeteksi data yang belum memenuhi standardisasi arsip kepegawaian. Harap tinjau dan perbaiki poin-poin berikut sebelum mengirimkan kembali:</p>
                                </div>
                            </div>
                            <span class="rf-error-summary-badge">Koreksi Diperlukan</span>
                        </div>
                        <ul class="rf-error-summary-list" id="errorSummaryList"></ul>
                    </div>

                    <!-- Section 1: Kategori Keanggotaan -->
                    <div class="rf-section" id="section-1">
                        <div class="rf-section-head">
                            <div class="rf-section-title-wrap">
                                <span class="rf-section-num">01</span>
                                <h2 class="rf-section-title">Kategori Keanggotaan</h2>
                            </div>
                            <span class="rf-required-tag">Wajib Dipilih</span>
                        </div>
                        <p class="rf-section-desc">Pilih status kepegawaian Anda. Sistem otomatis memvalidasi jenis identitas sesuai kategori.</p>

                        <!-- Selectable Cards for Jenis Anggota -->
                        <div class="rf-radio-cards" id="jenisAnggotaCards">
                            <?php foreach ($jenis_anggota as $idx => $ja): 
                                $jaNameLower = strtolower(trim($ja->jenisanggota));
                                $isAsn = stripos($jaNameLower, 'asn') !== false && stripos($jaNameLower, 'non') === false;
                            ?>
                            <div class="rf-radio-card">
                                <input type="radio" name="JenisAnggota_id" id="ja_<?= $ja->id ?>"
                                       value="<?= $ja->id ?>"
                                       data-name="<?= esc($jaNameLower) ?>"
                                       required>
                                <label for="ja_<?= $ja->id ?>" class="rf-card-choice">
                                    <div class="rf-card-choice-top">
                                        <div class="rf-card-icon-group">
                                            <div class="rf-card-icon <?= $isAsn ? 'is-asn' : 'is-nonasn' ?>">
                                                <?php if ($isAsn): ?>
                                                <!-- Official Government Landmark SVG -->
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 21h18M3 7v14M21 7v14M6 7V4a1 1 0 011-1h10a1 1 0 011 1v3M9 11h6M9 15h6" />
                                                </svg>
                                                <?php else: ?>
                                                <!-- Non-ASN / ID Badge SVG -->
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
                                                    <circle cx="9" cy="7" r="4" />
                                                    <path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                                                </svg>
                                                <?php endif; ?>
                                            </div>
                                            <span class="rf-card-chip <?= $isAsn ? 'is-asn' : 'is-nonasn' ?>">
                                                <?= $isAsn ? 'Wajib NIP 18 Digit' : 'Wajib NIK 16 Digit' ?>
                                            </span>
                                        </div>
                                        <div class="rf-card-radio-mark" aria-hidden="true">
                                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3.5 8.5l3 3 6-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="rf-card-choice-body">
                                        <div class="rf-card-title-row">
                                            <div class="rf-card-title"><?= esc($ja->jenisanggota) ?></div>
                                            <span class="rf-card-subtitle"><?= $isAsn ? 'PNS & PPPK' : 'PPNPN & Honorer' ?></span>
                                        </div>
                                        <div class="rf-card-desc">
                                            <?php if ($isAsn): ?>
                                            Pegawai ASN Kementerian Ketenagakerjaan. Menggunakan NIP 18 digit resmi BKN tanpa spasi.
                                            <?php else: ?>
                                            Tenaga Non-ASN, Honorer, Magang & Riset. Menggunakan NIK KTP resmi 16 digit.
                                            <?php endif; ?>
                                        </div>
                                        <div class="rf-card-footer">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 16A8 8 0 108 0a8 8 0 000 16zm.93-9.412l-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                            <span>Format identitas divalidasi otomatis oleh sistem</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="rf-field-feedback is-error" id="jenisAnggotaError" style="display:none;"></div>

                        <!-- Identity Input Section -->
                        <div class="rf-field" id="identityBlock" style="margin-top: 10px;">
                            <div class="rf-field-header">
                                <label class="rf-label" id="identityLabel" for="IdentityNo">
                                    Nomor Induk Pegawai (NIP) <span class="req">*</span>
                                </label>
                                <span class="rf-digit-badge" id="digitCount">0 / 18 digit</span>
                            </div>
                            <input type="hidden" id="IdentityType_id" name="IdentityType_id" value="">

                            <div class="rf-input-wrap">
                                <input type="text" class="rf-input" id="IdentityNo" name="IdentityNo"
                                       placeholder="Masukkan 18 digit NIP resmi BKN" inputmode="numeric" autocomplete="off" maxlength="18">
                                <span class="rf-input-icon is-error">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <span class="rf-input-icon is-valid">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="rf-field-feedback" id="feedback_IdentityNo"></div>
                            <div class="rf-hint" id="identityHint">
                                <svg viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                                </svg>
                                <span>Nomor Induk Pegawai 18 digit resmi BKN tanpa spasi atau tanda baca.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Diri & Domisili -->
                    <div class="rf-section" id="section-2">
                        <div class="rf-section-head">
                            <div class="rf-section-title-wrap">
                                <span class="rf-section-num">02</span>
                                <h2 class="rf-section-title">Data Diri & Satuan Kerja</h2>
                            </div>
                        </div>
                        <p class="rf-section-desc">Lengkapi identitas diri pemustaka sesuai kartu tanda pengenal kedinasan atau KTP resmi.</p>

                        <div class="rf-field">
                            <div class="rf-field-header">
                                <label class="rf-label" for="Fullname">
                                    Nama Lengkap (Tanpa Gelar) <span class="req">*</span>
                                </label>
                                <span class="rf-badge-status" id="badge_Fullname"></span>
                            </div>
                            <div class="rf-input-wrap">
                                <input type="text" class="rf-input" id="Fullname" name="Fullname"
                                       placeholder="Contoh: Raden Satria Pamungkas" required autocomplete="name">
                                <span class="rf-input-icon is-error">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <span class="rf-input-icon is-valid">
                                    <svg viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="rf-field-feedback" id="feedback_Fullname"></div>
                        </div>

                        <div class="rf-field">
                            <div class="rf-field-header">
                                <label class="rf-label" for="UnitKerja_id">
                                    Unit Kerja / Satuan Kerja Penempatan <span class="req">*</span>
                                </label>
                                <span class="rf-badge-status" id="badge_UnitKerja"></span>
                            </div>
                            <div class="rf-custom-select" id="customUnitKerja">
                                <!-- Real select for form payload & validation -->
                                <select class="rf-select-native" id="UnitKerja_id" name="UnitKerja_id" required tabindex="-1" aria-hidden="true">
                                    <option value="">Pilih Satuan Kerja / Unit Eselon I</option>
                                    <?php foreach ($unit_kerja as $uk): ?>
                                        <option value="<?= $uk->id ?>"><?= esc($uk->nama_unit) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <!-- Visual Trigger (Strictly 100% width) -->
                                <button type="button" class="rf-select-trigger" id="unitKerjaTrigger" aria-haspopup="listbox" aria-expanded="false">
                                    <span class="rf-select-value is-placeholder" id="unitKerjaValue">Pilih Satuan Kerja / Unit Eselon I</span>
                                    <svg class="rf-select-arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 8l4 4 4-4"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu (Strictly 100% width, never overflows) -->
                                <div class="rf-select-dropdown" id="unitKerjaDropdown" role="listbox" tabindex="-1">
                                    <div class="rf-select-option is-placeholder" data-value="" role="option">
                                        <span class="rf-opt-text">Pilih Satuan Kerja / Unit Eselon I</span>
                                    </div>
                                    <?php foreach ($unit_kerja as $uk): ?>
                                        <div class="rf-select-option" data-value="<?= $uk->id ?>" role="option">
                                            <span class="rf-opt-text"><?= esc($uk->nama_unit) ?></span>
                                            <svg class="rf-opt-check" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3.5 8.5l3 3 6-7"/>
                                            </svg>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="rf-field-feedback" id="feedback_UnitKerja"></div>
                        </div>

                        <div class="rf-field">
                            <div class="rf-field-header">
                                <label class="rf-label" for="AddressNow">
                                    Alamat Domisili Saat Ini <span class="req">*</span>
                                </label>
                                <span class="rf-badge-status" id="badge_AddressNow"></span>
                            </div>
                            <textarea class="rf-input" id="AddressNow" name="AddressNow"
                                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, dan kota/kabupaten..."
                                      required></textarea>
                            <div class="rf-field-feedback" id="feedback_AddressNow"></div>
                        </div>

                        <!-- Photo Upload (Optional) -->
                        <div class="rf-field">
                            <div class="rf-field-header">
                                <label class="rf-label">Foto Profil Pemustaka</label>
                                <span class="rf-sublabel">Opsional (Maks. 2 MB)</span>
                            </div>
                            
                            <div class="rf-photo-toggle">
                                <label id="labelPhotoNo" class="is-checked">
                                    <input type="radio" name="want_photo" id="wantPhotoNo" value="0" checked>
                                    <span>Lewati dulu</span>
                                </label>
                                <label id="labelPhotoYes">
                                    <input type="radio" name="want_photo" id="wantPhotoYes" value="1">
                                    <span>Unggah foto</span>
                                </label>
                            </div>

                            <input type="file" id="PhotoFile" name="PhotoFile" accept="image/jpeg,image/png" style="display:none;">

                            <div id="photoUploadContainer" style="display:none; margin-top: 8px;">
                                <div class="rf-dropzone" id="photoDropzone">
                                    <div class="rf-dropzone-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                                            <circle cx="12" cy="13" r="4"/>
                                        </svg>
                                    </div>
                                    <div class="rf-dropzone-text">Pilih foto pasfoto formal</div>
                                    <div class="rf-dropzone-sub">Format: JPG atau PNG &bull; Maksimal ukuran file 2 MB</div>
                                </div>
                                
                                <div class="rf-photo-preview" id="photoPreviewContainer" style="display:none;">
                                    <img id="photoPreview" src="" alt="Preview Foto">
                                    <div style="flex:1;">
                                        <div style="font-size:0.8rem;font-weight:600;color:var(--navy-900);">Foto terpasang</div>
                                        <div style="font-size:0.74rem;color:var(--text-muted);">Foto akan dicetak pada kartu anggota digital.</div>
                                    </div>
                                    <button type="button" class="rf-photo-remove" id="photoRemoveBtn">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Kontak & Aktivasi -->
                    <div class="rf-section" id="section-3">
                        <div class="rf-section-head">
                            <div class="rf-section-title-wrap">
                                <span class="rf-section-num">03</span>
                                <h2 class="rf-section-title">Kontak & Notifikasi Akun</h2>
                            </div>
                        </div>
                        <p class="rf-section-desc">Alamat korespondensi untuk pengiriman barcode kartu digital dan notifikasi sirkulasi buku.</p>

                        <div class="rf-grid-2">
                            <div class="rf-field">
                                <div class="rf-field-header">
                                    <label class="rf-label" for="Email">
                                        Alamat Email Aktif <span class="req">*</span>
                                    </label>
                                    <span class="rf-badge-status" id="badge_Email"></span>
                                </div>
                                <div class="rf-input-wrap">
                                    <input type="email" class="rf-input" id="Email" name="Email"
                                           placeholder="nama@kemnaker.go.id" required autocomplete="email">
                                    <span class="rf-input-icon is-error">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                    <span class="rf-input-icon is-valid">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="rf-field-feedback" id="feedback_Email"></div>
                                <div class="rf-hint">
                                    <svg viewBox="0 0 16 16" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Bukti registrasi & barcode kartu dikirim ke alamat surel ini.</span>
                                </div>
                            </div>

                            <div class="rf-field">
                                <div class="rf-field-header">
                                    <label class="rf-label" for="Phone">
                                        Nomor WhatsApp Aktif <span class="req">*</span>
                                    </label>
                                    <span class="rf-badge-status" id="badge_Phone"></span>
                                </div>
                                <div class="rf-phone-group rf-input-wrap">
                                    <span class="rf-phone-prefix">+62</span>
                                    <input type="tel" class="rf-input" id="Phone" name="Phone"
                                           placeholder="81234567890" required autocomplete="tel-national">
                                    <span class="rf-input-icon is-error">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                    <span class="rf-input-icon is-valid">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                </div>
                                <div class="rf-field-feedback" id="feedback_Phone"></div>
                                <div class="rf-hint">
                                    <svg viewBox="0 0 16 16" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Pemberitahuan batas peminjaman & reservasi koleksi.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agreement Box -->
                        <div class="rf-agree-box" id="agreeBox">
                            <div class="rf-agree-row">
                                <input type="checkbox" id="Persetujuan" name="Persetujuan" value="1" disabled required>
                                <label for="Persetujuan">
                                    <strong>Pernyataan Pemustaka & Kepatuhan UU PDP:</strong>
                                    Saya menyatakan data yang diisi adalah benar dan sah. Saya bersedia menaati tata tertib Perpustakaan serta menyetujui pemrosesan data pribadi untuk administrasi sirkulasi sesuai
                                    <a href="javascript:void(0)" id="linkSyarat">Syarat & Ketentuan Keanggotaan</a>.
                                </label>
                            </div>
                            <div class="rf-field-feedback" id="persetujuanFeedback" style="margin-top: 10px; padding-left: 30px;"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="rf-actions">
                            <button type="reset" class="rf-btn rf-btn-ghost" id="btnReset">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                </svg>
                                <span>Bersihkan Formulir</span>
                            </button>
                            <button type="button" class="rf-btn rf-btn-primary" id="btnSubmitRegistrasi" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                                </svg>
                                <span>Kirim Formulir Registrasi</span>
                            </button>
                        </div>

                        <div id="alertMessage" class="rf-alert"></div>
                    </div>

                </form>
            </div>

            <!-- Sidebar Info Column -->
            <aside class="rf-sidebar">

                <!-- Benefits Card -->
                <div class="rf-side-card">
                    <div class="rf-side-header">
                        <h3 class="rf-side-title">Hak Akses Anggota</h3>
                        <p class="rf-side-sub">Fasilitas pemustaka terdaftar</p>
                    </div>
                    <ul class="rf-benefits-list">
                        <li>
                            <span class="rf-benefit-icon">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3.5 8.5l3 3 6-7"/>
                                </svg>
                            </span>
                            <div>Peminjaman hingga <strong>2 eksemplar</strong> buku fisik selama 14 hari kalender.</div>
                        </li>
                        <li>
                            <span class="rf-benefit-icon">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3.5 8.5l3 3 6-7"/>
                                </svg>
                            </span>
                            <div>Akses pangkalan data jurnal & <strong>e-Resources Kemnaker RI</strong>.</div>
                        </li>
                        <li>
                            <span class="rf-benefit-icon">
                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3.5 8.5l3 3 6-7"/>
                                </svg>
                            </span>
                            <div>Akses ruang perpustakaan & <strong>fasilitas penelusuran mandiri</strong>.</div>
                        </li>
                    </ul>
                </div>

                <!-- Verification Flow (Connected Timeline) -->
                <div class="rf-side-card">
                    <div class="rf-side-header">
                        <h3 class="rf-side-title">Alur Verifikasi Pustakawan</h3>
                        <p class="rf-side-sub">Proses validasi kartu anggota digital</p>
                    </div>
                    <div class="rf-timeline">
                        <div class="rf-timeline-item">
                            <span class="rf-timeline-node"></span>
                            <div class="rf-timeline-title">Pengiriman Formulir Online</div>
                            <div class="rf-timeline-desc">Tanda terima & nomor registrasi langsung dikirim ke email Anda.</div>
                        </div>
                        <div class="rf-timeline-item">
                            <span class="rf-timeline-node"></span>
                            <div class="rf-timeline-title">Pengecekan Data Identitas</div>
                            <div class="rf-timeline-desc">Pustakawan memvalidasi NIP/NIK dalam 1&times;24 jam kerja.</div>
                        </div>
                        <div class="rf-timeline-item">
                            <span class="rf-timeline-node"></span>
                            <div class="rf-timeline-title">Aktivasi Barcode Digital</div>
                            <div class="rf-timeline-desc">Kartu digital diterbitkan & langsung siap digunakan di sirkulasi.</div>
                        </div>
                    </div>
                </div>

                <!-- Help & Contact Card (Editorial Dark) -->
                <div class="rf-side-card dark">
                    <div class="rf-side-header">
                        <h3 class="rf-side-title">Bantuan & Layanan Fisik</h3>
                        <p class="rf-side-sub">Meja Layanan Pemustaka</p>
                    </div>
                    <div class="rf-dark-list">
                        <div class="rf-dark-item">
                            <svg class="rf-dark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <div class="rf-dark-content">
                                <strong>Jam Operasional:</strong>
                                <span>Senin – Jumat: 08.00–16.00 WIB<br>Istirahat: 12.00–13.00 WIB</span>
                            </div>
                        </div>

                        <?php if (!empty($_alamatPerpus)): ?>
                        <div class="rf-dark-item">
                            <svg class="rf-dark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div class="rf-dark-content">
                                <strong>Alamat Perpustakaan:</strong>
                                <span> Jl.Jenderal Gatot Subroto Kav. 51, RT.5/RW.4, Kuningan Timur, Kecamatan Setiabudi, Kota Jakarta Selatan, DKI Jakarta 12950. Gedung B</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($_emailPerpus)): ?>
                        <div class="rf-dark-item">
                            <svg class="rf-dark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <div class="rf-dark-content">
                                <strong>Surel Layanan:</strong>
                                <a href="mailto:perpustakaan@kemnaker.go.id">perpustakaan@kemnaker.go.id</a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($_phonePerpus)): ?>
                        <div class="rf-dark-item">
                            <svg class="rf-dark-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                            </svg>
                            <div class="rf-dark-content">
                                <strong>Telepon Layanan:</strong>
                                <span>08119521151</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </aside>
        </div>

    </div>
</div>

<!-- Modal Syarat & Ketentuan Keanggotaan -->
<div id="modalSyarat" class="rf-overlay" role="dialog" aria-modal="true" aria-labelledby="rfModalTitle">
    <div class="rf-modal">
        <div class="rf-modal-head">
            <h3 id="rfModalTitle">Syarat dan Ketentuan Keanggotaan</h3>
            <button type="button" class="rf-modal-close" id="closeSyarat" aria-label="Tutup">&times;</button>
        </div>
        <div class="rf-modal-body" id="syaratScrollbox">
            <p>Selamat datang di layanan Pendaftaran Anggota Perpustakaan.</p>
            <p>Dengan mendaftar sebagai anggota, Anda menyetujui seluruh ketentuan dan kebijakan yang berlaku sebagai berikut:</p>
            <ol>
                <li><strong>Kebenaran Data:</strong> Seluruh data yang Anda masukkan dalam formulir ini adalah benar, akurat, dan dapat dipertanggungjawabkan sesuai identitas asli (KTP/SK Kedinasan resmi).</li>
                <li><strong>Penggunaan Kartu Anggota:</strong> Kartu anggota tidak dapat dipindahtangankan dan sepenuhnya menjadi tanggung jawab pemegang kartu yang terdaftar.</li>
                <li><strong>Peminjaman Koleksi:</strong> Anggota berhak meminjam koleksi perpustakaan sesuai kuota dan batas waktu yang ditentukan oleh peraturan perpustakaan.</li>
                <li><strong>Sanksi Keterlambatan:</strong> Keterlambatan pengembalian koleksi akan dikenakan denda atau penangguhan hak pinjam sesuai ketentuan yang berlaku.</li>
                <li><strong>Tanggung Jawab Kerusakan/Kehilangan:</strong> Apabila koleksi yang dipinjam mengalami kerusakan atau hilang, anggota wajib mengganti dengan koleksi yang sama atau membayar biaya penggantian resmi.</li>
                <li><strong>Ketertiban Ruang Baca:</strong> Anggota wajib menjaga ketertiban, keamanan, dan kebersihan lingkungan perpustakaan.</li>
                <li><strong>Privasi Data (Kepatuhan UU PDP):</strong> Data pribadi yang Anda berikan dikelola dengan aman untuk keperluan administrasi perpustakaan dan tidak akan disebarluaskan ke pihak ketiga tanpa izin sah.</li>
            </ol>
            <div class="rf-scroll-note">Gulir kotak teks ini hingga paling bawah untuk mengaktifkan tombol persetujuan.</div>
            <div style="height: 60px;"></div>
            <p style="font-size:0.78rem;color:var(--text-muted);">Terima kasih atas kepatuhan Anda demi kenyamanan seluruh pemustaka.</p>
        </div>
        <div class="rf-modal-foot">
            <button type="button" class="rf-btn rf-btn-ghost" id="btnTutupSyarat">Tutup</button>
            <button type="button" class="rf-btn rf-btn-primary" id="btnSetujuSyarat" disabled>Saya Setuju</button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formRegistrasi');
    const cbPersetujuan = document.getElementById('Persetujuan');
    const btnSubmit = document.getElementById('btnSubmitRegistrasi');
    const alertEl = document.getElementById('alertMessage');

    const identityTypeInput = document.getElementById('IdentityType_id');
    const identityBlock = document.getElementById('identityBlock');
    const identityLabel = document.getElementById('identityLabel');
    const inputIdentityNo = document.getElementById('IdentityNo');
    const feedbackIdentityNo = document.getElementById('feedback_IdentityNo');
    const identityHint = document.getElementById('identityHint');
    const digitCount = document.getElementById('digitCount');

    const nipId = '<?= $_nipId ?>';
    const nikId = '<?= $_nikId ?>';

    const photoDropzone = document.getElementById('photoDropzone');
    const photoFileInput = document.getElementById('PhotoFile');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');
    const photoPreview = document.getElementById('photoPreview');
    const photoRemoveBtn = document.getElementById('photoRemoveBtn');
    const photoUploadContainer = document.getElementById('photoUploadContainer');
    const wantPhotoNo = document.getElementById('wantPhotoNo');
    const wantPhotoYes = document.getElementById('wantPhotoYes');
    const labelPhotoNo = document.getElementById('labelPhotoNo');
    const labelPhotoYes = document.getElementById('labelPhotoYes');

    const linkSyarat = document.getElementById('linkSyarat');
    const modalSyarat = document.getElementById('modalSyarat');
    const closeSyarat = document.getElementById('closeSyarat');
    const btnTutupSyarat = document.getElementById('btnTutupSyarat');
    const btnSetujuSyarat = document.getElementById('btnSetujuSyarat');
    const syaratScrollbox = document.getElementById('syaratScrollbox');

    let selectedIdDigits = 18;
    let isNonAsn = false;

    // ─── Stepper Navigation (Click to Scroll) ───
    const stepButtons = document.querySelectorAll('.rf-step');
    const sections = document.querySelectorAll('.rf-section');

    stepButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Sync Active Stepper on Scroll
    function updateActiveStep() {
        let currentId = 'section-1';
        sections.forEach(sec => {
            const rect = sec.getBoundingClientRect();
            if (rect.top <= 140) {
                currentId = sec.id;
            }
        });

        stepButtons.forEach(step => {
            const isActive = step.dataset.target === currentId;
            step.classList.toggle('active', isActive);
            step.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    }

    window.addEventListener('scroll', updateActiveStep, { passive: true });

    // ─── Jenis Anggota Selection & Identity Mode ───
    const jaRadios = document.querySelectorAll('input[name="JenisAnggota_id"]');

    function applyJenisAnggota(radio) {
        if (!radio) return;
        const name = (radio.dataset.name || '').trim().toLowerCase();
        const isAsn = name.includes('asn') && !name.includes('non');
        isNonAsn = !isAsn;
        document.getElementById('jenisAnggotaError').style.display = 'none';

        if (isNonAsn) {
            // Non-ASN: Langsung NIK (16 digit)
            identityTypeInput.value = nikId;
            selectedIdDigits = 16;
            inputIdentityNo.maxLength = 16;
            inputIdentityNo.setAttribute('maxlength', '16');
            if (identityLabel) identityLabel.innerHTML = 'Nomor Induk Kependudukan (NIK) <span class="req">*</span>';
            inputIdentityNo.placeholder = 'Masukkan 16 digit NIK KTP';
            identityHint.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>Nomor Induk Kependudukan 16 digit resmi tanpa spasi.</span>
            `;
            feedbackIdentityNo.textContent = 'NIK wajib diisi dengan 16 digit angka.';
        } else {
            // ASN: Wajib NIP (18 digit)
            identityTypeInput.value = nipId;
            selectedIdDigits = 18;
            inputIdentityNo.maxLength = 18;
            inputIdentityNo.setAttribute('maxlength', '18');
            if (identityLabel) identityLabel.innerHTML = 'Nomor Induk Pegawai (NIP) <span class="req">*</span>';
            inputIdentityNo.placeholder = 'Masukkan 18 digit NIP resmi BKN';
            identityHint.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>Nomor Induk Pegawai 18 digit resmi BKN tanpa spasi atau tanda baca.</span>
            `;
            feedbackIdentityNo.textContent = 'NIP wajib diisi dengan 18 digit angka resmi BKN.';
        }

        if (inputIdentityNo.value.length > selectedIdDigits) {
            inputIdentityNo.value = inputIdentityNo.value.slice(0, selectedIdDigits);
        }

        updateDigitCount();
    }

    jaRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            applyJenisAnggota(this);
        });
    });

    // Inisialisasi awal (default ke opsi ASN jika ada)
    const activeRadio = document.querySelector('input[name="JenisAnggota_id"]:checked');
    if (activeRadio) {
        applyJenisAnggota(activeRadio);
    } else if (jaRadios.length > 0) {
        jaRadios[0].checked = true;
        applyJenisAnggota(jaRadios[0]);
    }

    // ─── Live Digit Counter & Formatting (Strict Limit) ───
    inputIdentityNo.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, selectedIdDigits);
        updateDigitCount();
        if (this.classList.contains('has-error')) this.classList.remove('has-error');
    });

    function updateDigitCount() {
        const len = inputIdentityNo.value.length;
        digitCount.textContent = len + ' / ' + selectedIdDigits + ' digit';
        if (len === selectedIdDigits) {
            digitCount.classList.add('is-valid');
        } else {
            digitCount.classList.remove('is-valid');
        }
    }

    // ─── Phone Formatting ───
    const phoneInput = document.getElementById('Phone');
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        if (this.classList.contains('has-error')) this.classList.remove('has-error');
    });

    // ─── Custom Dropdown Unit Kerja (100% Responsive / No Overflow) ───
    const customUnitKerja = document.getElementById('customUnitKerja');
    const unitKerjaTrigger = document.getElementById('unitKerjaTrigger');
    const unitKerjaValue = document.getElementById('unitKerjaValue');
    const unitKerjaOptions = document.querySelectorAll('.rf-select-option');
    const nativeUnitSelect = document.getElementById('UnitKerja_id');

    if (customUnitKerja && unitKerjaTrigger && nativeUnitSelect) {
        unitKerjaTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = customUnitKerja.classList.contains('is-open');
            customUnitKerja.classList.toggle('is-open', !isOpen);
            unitKerjaTrigger.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        });

        unitKerjaOptions.forEach(opt => {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                const val = this.dataset.value;
                const text = this.querySelector('.rf-opt-text') ? this.querySelector('.rf-opt-text').textContent.trim() : this.textContent.trim();
                
                nativeUnitSelect.value = val;
                nativeUnitSelect.dispatchEvent(new Event('change', { bubbles: true }));

                unitKerjaOptions.forEach(o => o.classList.remove('is-selected'));
                if (val) {
                    this.classList.add('is-selected');
                    unitKerjaValue.textContent = text;
                    unitKerjaValue.classList.remove('is-placeholder');
                } else {
                    unitKerjaValue.textContent = 'Pilih Satuan Kerja / Unit Eselon I';
                    unitKerjaValue.classList.add('is-placeholder');
                }

                unitKerjaTrigger.classList.remove('has-error');
                nativeUnitSelect.classList.remove('has-error');
                customUnitKerja.classList.remove('is-open');
                unitKerjaTrigger.setAttribute('aria-expanded', 'false');
            });
        });

        document.addEventListener('click', function(e) {
            if (!customUnitKerja.contains(e.target)) {
                customUnitKerja.classList.remove('is-open');
                unitKerjaTrigger.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && customUnitKerja.classList.contains('is-open')) {
                customUnitKerja.classList.remove('is-open');
                unitKerjaTrigger.setAttribute('aria-expanded', 'false');
                unitKerjaTrigger.focus();
            }
        });
    }

    const btnResetForm = document.getElementById('btnReset');
    if (btnResetForm) {
        btnResetForm.addEventListener('click', function() {
            setTimeout(() => {
                if (unitKerjaValue) {
                    unitKerjaValue.textContent = 'Pilih Satuan Kerja / Unit Eselon I';
                    unitKerjaValue.classList.add('is-placeholder');
                }
                unitKerjaOptions.forEach(o => o.classList.remove('is-selected'));
                if (unitKerjaTrigger) unitKerjaTrigger.classList.remove('has-error');
            }, 50);
        });
    }

    // ─── Photo Upload Interactions ───
    function syncPhotoToggle() {
        if (wantPhotoYes.checked) {
            labelPhotoYes.classList.add('is-checked');
            labelPhotoNo.classList.remove('is-checked');
            photoUploadContainer.style.display = 'block';
        } else {
            labelPhotoNo.classList.add('is-checked');
            labelPhotoYes.classList.remove('is-checked');
            photoUploadContainer.style.display = 'none';
            photoFileInput.value = '';
            photoPreview.src = '';
            photoPreviewContainer.style.display = 'none';
            photoDropzone.style.display = 'block';
        }
    }

    wantPhotoYes.addEventListener('change', syncPhotoToggle);
    wantPhotoNo.addEventListener('change', syncPhotoToggle);

    photoDropzone.addEventListener('click', () => photoFileInput.click());

    photoDropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--accent-600)';
        this.style.background = 'var(--accent-50)';
    });

    photoDropzone.addEventListener('dragleave', function() {
        this.style.borderColor = '#cbd5e1';
        this.style.background = 'var(--bg-page)';
    });

    photoDropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#cbd5e1';
        this.style.background = 'var(--bg-page)';
        if (e.dataTransfer.files.length > 0) {
            handlePhotoFile(e.dataTransfer.files[0]);
        }
    });

    photoFileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            handlePhotoFile(this.files[0]);
        }
    });

    function handlePhotoFile(file) {
        const allowed = ['image/jpeg', 'image/png'];
        if (!allowed.includes(file.type)) {
            alert('Format foto harus berupa JPG atau PNG.');
            photoFileInput.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file foto maksimal 2 MB.');
            photoFileInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            photoPreview.src = e.target.result;
            photoDropzone.style.display = 'none';
            photoPreviewContainer.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }

    photoRemoveBtn.addEventListener('click', function() {
        photoFileInput.value = '';
        photoPreview.src = '';
        photoPreviewContainer.style.display = 'none';
        photoDropzone.style.display = 'block';
    });

    // ─── Syarat & Ketentuan Modal ───
    linkSyarat.addEventListener('click', function(e) {
        e.preventDefault();
        modalSyarat.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(checkModalScroll, 100);
    });

    function closeModal() {
        modalSyarat.style.display = 'none';
        document.body.style.overflow = '';
    }

    closeSyarat.addEventListener('click', closeModal);
    btnTutupSyarat.addEventListener('click', closeModal);
    modalSyarat.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalSyarat.style.display === 'flex') closeModal();
    });

    function checkModalScroll() {
        if (syaratScrollbox.scrollHeight <= syaratScrollbox.clientHeight) {
            btnSetujuSyarat.disabled = false;
        } else if (syaratScrollbox.scrollTop + syaratScrollbox.clientHeight >= syaratScrollbox.scrollHeight - 10) {
            btnSetujuSyarat.disabled = false;
        }
    }

    syaratScrollbox.addEventListener('scroll', checkModalScroll);

    btnSetujuSyarat.addEventListener('click', function() {
        cbPersetujuan.disabled = false;
        cbPersetujuan.checked = true;
        btnSubmit.disabled = false;
        closeModal();
        validatePersetujuan();
    });

    cbPersetujuan.addEventListener('change', function() {
        btnSubmit.disabled = !this.checked;
    });

    // ─── Form Reset Handler ───
    document.getElementById('btnReset').addEventListener('click', function() {
        setTimeout(function() {
            if (jaRadios.length > 0) {
                jaRadios[0].checked = true;
                applyJenisAnggota(jaRadios[0]);
            }
            inputIdentityNo.value = '';
            updateDigitCount();
            cbPersetujuan.checked = false;
            cbPersetujuan.disabled = true;
            btnSubmit.disabled = true;
            wantPhotoNo.checked = true;
            syncPhotoToggle();
            form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            alertEl.className = 'rf-alert';
            alertEl.style.display = 'none';
        }, 10);
    });

    // Elements for Validation
    const fullnameInput = document.getElementById('Fullname');
    const badgeFullname = document.getElementById('badge_Fullname');
    const feedbackFullname = document.getElementById('feedback_Fullname');

    const badgeUnitKerja = document.getElementById('badge_UnitKerja');
    const feedbackUnitKerja = document.getElementById('feedback_UnitKerja');

    const addressInput = document.getElementById('AddressNow');
    const badgeAddress = document.getElementById('badge_AddressNow');
    const feedbackAddress = document.getElementById('feedback_AddressNow');

    const emailInput = document.getElementById('Email');
    const badgeEmail = document.getElementById('badge_Email');
    const feedbackEmail = document.getElementById('feedback_Email');

    const badgePhone = document.getElementById('badge_Phone');
    const feedbackPhone = document.getElementById('feedback_Phone');

    const agreeBox = document.getElementById('agreeBox');
    const persetujuanFeedback = document.getElementById('persetujuanFeedback');

    const errorSummaryBox = document.getElementById('errorSummaryBox');
    const errorSummaryTitle = document.getElementById('errorSummaryTitle');
    const errorSummaryList = document.getElementById('errorSummaryList');

    // Validation Helper Functions
    function setFieldError(field, badge, feedback, badgeText, errorMsg) {
        if (field) {
            field.classList.add('has-error');
            field.classList.remove('is-valid');
        }
        if (badge) {
            badge.className = 'rf-badge-status is-error';
            badge.textContent = badgeText;
            badge.style.display = 'inline-flex';
        }
        if (feedback) {
            feedback.className = 'rf-field-feedback is-error';
            feedback.style.display = 'flex';
            feedback.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>${errorMsg}</span>
            `;
        }
    }

    function setFieldValid(field, badge, feedback, validMsg) {
        if (field) {
            field.classList.remove('has-error');
            field.classList.add('is-valid');
        }
        if (badge) {
            badge.className = 'rf-badge-status is-valid';
            badge.textContent = '✓ Valid';
            badge.style.display = 'inline-flex';
        }
        if (feedback) {
            feedback.className = 'rf-field-feedback is-valid';
            feedback.style.display = 'flex';
            feedback.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zm-3.97-3.03a.75.75 0 00-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 00-1.06 1.06L6.97 11.03a.75.75 0 001.079-.02l3.992-4.99a.75.75 0 00-.01-1.05z" clip-rule="evenodd"/>
                </svg>
                <span>${validMsg || 'Data valid.'}</span>
            `;
        }
    }

    function validateIdentityField() {
        const val = inputIdentityNo.value.trim();
        const len = val.length;
        const typeName = isNonAsn ? 'NIK' : 'NIP';
        const needed = selectedIdDigits;

        if (len === 0) {
            digitCount.className = 'rf-digit-badge is-error';
            digitCount.textContent = 'Wajib Diisi';
            inputIdentityNo.classList.add('has-error');
            inputIdentityNo.classList.remove('is-valid');
            feedbackIdentityNo.className = 'rf-field-feedback is-error';
            feedbackIdentityNo.style.display = 'flex';
            feedbackIdentityNo.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>Nomor ${typeName} wajib diisi sesuai kartu identitas resmi Anda.</span>
            `;
            return `<strong>Nomor Identitas (${typeName}):</strong> Nomor ${typeName} wajib diisi.`;
        } else if (len < needed) {
            const kurang = needed - len;
            digitCount.className = 'rf-digit-badge is-error';
            digitCount.textContent = `${len} / ${needed} Digit (Kurang ${kurang} digit)`;
            inputIdentityNo.classList.add('has-error');
            inputIdentityNo.classList.remove('is-valid');
            feedbackIdentityNo.className = 'rf-field-feedback is-error';
            feedbackIdentityNo.style.display = 'flex';
            feedbackIdentityNo.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>Nomor ${typeName} tidak lengkap. Wajib ${needed} digit angka (terdeteksi ${len} digit: ${val}). Kurang ${kurang} digit.</span>
            `;
            return `<strong>Nomor Identitas (${typeName}):</strong> Harus berjumlah tepat ${needed} digit angka (saat ini berisi ${len} digit).`;
        } else {
            digitCount.className = 'rf-digit-badge is-valid';
            digitCount.textContent = `${needed} / ${needed} digit ✓`;
            inputIdentityNo.classList.remove('has-error');
            inputIdentityNo.classList.add('is-valid');
            feedbackIdentityNo.className = 'rf-field-feedback is-valid';
            feedbackIdentityNo.style.display = 'flex';
            feedbackIdentityNo.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zm-3.97-3.03a.75.75 0 00-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 00-1.06 1.06L6.97 11.03a.75.75 0 001.079-.02l3.992-4.99a.75.75 0 00-.01-1.05z" clip-rule="evenodd"/>
                </svg>
                <span>Nomor ${typeName} memenuhi ketentuan ${needed} digit.</span>
            `;
            return null;
        }
    }

    function validateFullname() {
        const val = fullnameInput.value.trim();
        if (val.length < 3) {
            setFieldError(fullnameInput, badgeFullname, feedbackFullname, 'Wajib Diisi', 'Nama lengkap wajib diisi minimal 3 karakter sesuai KTP / SK Kedinasan.');
            return '<strong>Nama Lengkap:</strong> Wajib diisi minimal 3 karakter sesuai identitas.';
        } else {
            setFieldValid(fullnameInput, badgeFullname, feedbackFullname, 'Sesuai format nama sipil resmi.');
            return null;
        }
    }

    function validateUnitKerja() {
        if (!nativeUnitSelect.value) {
            setFieldError(unitKerjaTrigger, badgeUnitKerja, feedbackUnitKerja, 'Belum Dipilih', 'Satuan kerja penempatan wajib dipilih.');
            return '<strong>Unit Kerja:</strong> Satuan kerja / direktorat penempatan belum dipilih.';
        } else {
            setFieldValid(unitKerjaTrigger, badgeUnitKerja, feedbackUnitKerja, 'Unit kerja terdaftar.');
            return null;
        }
    }

    function validateAddress() {
        const val = addressInput.value.trim();
        if (val.length < 5) {
            setFieldError(addressInput, badgeAddress, feedbackAddress, 'Wajib Diisi', 'Alamat domisili saat ini wajib diisi lengkap untuk pengiriman berkas atau kartu anggota.');
            return '<strong>Alamat Domisili:</strong> Wajib diisi alamat domisili lengkap.';
        } else {
            setFieldValid(addressInput, badgeAddress, feedbackAddress, 'Alamat domisili tercatat.');
            return null;
        }
    }

    function validateEmail() {
        const val = emailInput.value.trim();
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!val || !re.test(val)) {
            setFieldError(emailInput, badgeEmail, feedbackEmail, 'Email Tidak Valid', 'Format email tidak valid. Masukkan surel aktif (contoh: nama@kemnaker.go.id).');
            return '<strong>Alamat Email:</strong> Format surel tidak valid (harus email aktif).';
        } else {
            setFieldValid(emailInput, badgeEmail, feedbackEmail, 'Format email valid untuk aktivasi akun.');
            return null;
        }
    }

    function validatePhone() {
        const raw = phoneInput.value.replace(/\D/g, '');
        if (raw.length < 9 || raw.length > 13) {
            setFieldError(phoneInput, badgePhone, feedbackPhone, 'Format Nomor Tidak Valid', 'Nomor harus nomor seluler / WhatsApp yang valid (diawali 08 atau 62, 9–13 digit).');
            return '<strong>Kontak Pemohon:</strong> Format nomor Telepon / WhatsApp tidak valid (harus nomor seluler diawali 08 atau 62).';
        } else {
            setFieldValid(phoneInput, badgePhone, feedbackPhone, 'Nomor WhatsApp terverifikasi.');
            return null;
        }
    }

    function validatePersetujuan() {
        if (!cbPersetujuan.checked) {
            agreeBox.classList.add('has-error');
            persetujuanFeedback.className = 'rf-field-feedback is-error';
            persetujuanFeedback.style.display = 'flex';
            persetujuanFeedback.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>Wajib menyetujui pernyataan kebenaran data dan privasi perpustakaan.</span>
            `;
            return '<strong>Lembar Pernyataan:</strong> Centang persetujuan syarat dan ketentuan wajib disetujui sebelum mengirim formulir.';
        } else {
            agreeBox.classList.remove('has-error');
            persetujuanFeedback.className = 'rf-field-feedback is-valid';
            persetujuanFeedback.style.display = 'flex';
            persetujuanFeedback.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zm-3.97-3.03a.75.75 0 00-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 00-1.06 1.06L6.97 11.03a.75.75 0 001.079-.02l3.992-4.99a.75.75 0 00-.01-1.05z" clip-rule="evenodd"/>
                </svg>
                <span>Persetujuan syarat & ketentuan telah dikonfirmasi.</span>
            `;
            return null;
        }
    }

    // Real-time Input Listeners
    fullnameInput.addEventListener('input', function() {
        if (this.value.trim().length >= 3) validateFullname();
    });

    emailInput.addEventListener('input', function() {
        if (this.value.includes('@')) validateEmail();
    });

    phoneInput.addEventListener('input', function() {
        if (this.value.length >= 9) validatePhone();
    });

    addressInput.addEventListener('input', function() {
        if (this.value.trim().length >= 5) validateAddress();
    });

    cbPersetujuan.addEventListener('change', function() {
        validatePersetujuan();
    });

    // ─── Submit Handler (AJAX) ───
    btnSubmit.addEventListener('click', function() {
        const errorItems = [];

        // 1. Jenis Anggota
        const selectedJa = document.querySelector('input[name="JenisAnggota_id"]:checked');
        if (!selectedJa) {
            errorItems.push({ sec: 'section-1', step: 'section-1', msg: '<strong>Kategori Keanggotaan:</strong> Pilih status kepegawaian Anda (ASN atau Non-ASN).' });
            const jaErr = document.getElementById('jenisAnggotaError');
            if (jaErr) {
                jaErr.style.display = 'flex';
                jaErr.textContent = 'Pilih salah satu kategori keanggotaan.';
            }
        }

        // 2. Identity
        const errId = validateIdentityField();
        if (errId) errorItems.push({ sec: 'section-1', step: 'section-1', msg: errId });

        // 3. Fullname
        const errName = validateFullname();
        if (errName) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errName });

        // 4. Unit Kerja
        const errUnit = validateUnitKerja();
        if (errUnit) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errUnit });

        // 5. Alamat Domisili
        const errAddr = validateAddress();
        if (errAddr) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errAddr });

        // 6. Email
        const errEmail = validateEmail();
        if (errEmail) errorItems.push({ sec: 'section-3', step: 'section-3', msg: errEmail });

        // 7. Phone
        const errPhone = validatePhone();
        if (errPhone) errorItems.push({ sec: 'section-3', step: 'section-3', msg: errPhone });

        // 8. Persetujuan
        const errAgree = validatePersetujuan();
        if (errAgree) errorItems.push({ sec: 'section-3', step: 'section-3', msg: errAgree });

        // Stepper Error Update
        stepButtons.forEach(btn => btn.classList.remove('has-error'));

        if (errorItems.length > 0) {
            const errorSteps = new Set(errorItems.map(e => e.step));
            stepButtons.forEach(btn => {
                if (errorSteps.has(btn.dataset.target)) {
                    btn.classList.add('has-error');
                }
            });

            // Populate Error Summary Banner
            errorSummaryTitle.textContent = `Pendaftaran Tidak Dapat Diproses (Terdapat ${errorItems.length} Kesalahan)`;
            errorSummaryList.innerHTML = errorItems.map(item => `<li>${item.msg}</li>`).join('');
            errorSummaryBox.style.display = 'block';

            // Smooth Scroll to Top Summary Box
            errorSummaryBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        } else {
            errorSummaryBox.style.display = 'none';
        }

        // Normalisasi nomor telepon ke format 08xx untuk submit
        const rawPhone = phoneInput.value.replace(/\D/g, '');
        const phoneForSubmit = '0' + rawPhone;

        // Button Loading State
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `
            <svg class="rf-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;animation:rfSpin 0.7s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                <path d="M12 2a10 10 0 0110 10" stroke-linecap="round"/>
            </svg>
            <span>Memproses Pendaftaran...</span>
        `;

        const formData = new FormData(form);
        formData.set('Phone', phoneForSubmit);

        if (photoFileInput.files.length > 0) {
            formData.set('PhotoFile', photoFileInput.files[0]);
        }

        axios.post('<?= base_url('registrasi/submit') ?>', formData, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            const res = response.data;
            if (res && res.success) {
                window.location.href = res.redirect;
            } else {
                showAlert(res && res.message ? res.message : 'Terjadi kesalahan, silakan periksa formulir.', 'error');
                if (res && res.errors) {
                    for (const [key, value] of Object.entries(res.errors)) {
                        const field = document.getElementById(key);
                        if (field) field.classList.add('has-error');
                        if (key === 'UnitKerja_id') {
                            const ut = document.getElementById('unitKerjaTrigger');
                            if (ut) ut.classList.add('has-error');
                        }
                    }
                }
                resetSubmitBtn();
            }
        })
        .catch(error => {
            const msg = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : 'Terjadi kendala jaringan. Mohon coba beberapa saat lagi.';
            showAlert(msg, 'error');
            resetSubmitBtn();
        });
    });

    function resetSubmitBtn() {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
            <span>Kirim Formulir Registrasi</span>
        `;
    }

    function showAlert(msg, type) {
        alertEl.className = 'rf-alert ' + type;
        alertEl.innerHTML = msg;
        alertEl.style.display = 'block';
        alertEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
<style>
@keyframes rfSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
<?= $this->endSection() ?>
