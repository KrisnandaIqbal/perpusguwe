<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?php
helper('landing');


$_nipId = $nip_id ?? '';
$_asnId = $asn_id ?? '';
?>

<?= $this->section('style') ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">

<style>
    /* Token desain mengikuti DESIGN.md (satu warna identitas: biru institusi).
       Nama variabel lama dipertahankan supaya aturan di bawah ikut berganti warna. */
    :root {
        --navy-950: #151922;
        --navy-900: #151922;
        --navy-850: #1b2130;
        --navy-800: #2a3142;
        --navy-700: #5b6272;

        --accent-50:  #eef2fc;
        --accent-100: #dce5f8;
        --accent-200: #b8c9ef;
        --accent-500: #0f3fb4;
        --accent-600: #0f3fb4;
        --accent-700: #0b3290;

        --bg-page: #f1f4f8;
        --bg-card: #ffffff;
        --bg-card-hover: #f9fbfc;
        --bg-input: #ffffff;
        --bg-panel: #eef1f6;

        --border-subtle: #eef0f4;
        --border-card: #e3e7ee;
        --border-input: #d3d9e3;
        --border-focus: #0f3fb4;

        /* muted 6,11:1 di atas putih; placeholder 4,97:1 */
        --text-primary: #151922;
        --text-secondary: #5b6272;
        --text-muted: #5b6272;
        --text-subtle: #687081;

        --emerald-600: #0f7a52;
        --emerald-50:  #e3f6ed;
        --emerald-200: #a9dfc5;
        --rose-600:    #c0392b;
        --rose-50:     #fdebe5;
        --rose-200:    #f2b8ab;

        --font-heading: 'Plus Jakarta Sans', sans-serif;
        --font-body: 'Plus Jakarta Sans', sans-serif;
        --font-serif: 'Source Serif 4', Georgia, 'Times New Roman', serif;
        --font-mono: 'Plus Jakarta Sans', sans-serif;

        --shadow-subtle: 0 1px 2px rgba(22, 33, 51, 0.04);
        --shadow-card:   0 1px 2px rgba(22, 33, 51, 0.04), 0 8px 24px -12px rgba(22, 33, 51, 0.08);
        --shadow-float:  0 26px 50px rgba(22, 33, 51, 0.16);
        --ring-focus:    0 0 0 2px #ffffff, 0 0 0 4px #0f3fb4;

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
        background: #fdebe5 !important;
        color: #c0392b !important;
        border-color: #f87171 !important;
        box-shadow: 0 0 0 2px rgba(192, 57, 43, 0.2);
    }

    .rf-step.has-error .rf-step-label,
    .rf-step.has-error .rf-step-title {
        color: #c0392b !important;
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
        grid-template-columns: minmax(0, 1fr);
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
        scroll-margin-top: 100px;
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
        background: #eef2fc;
        border-color: #b8c9ef;
        color: #0b3290;
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
        border: 1px solid #b8c9ef;
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
        background-color: #eef2fc;
        box-shadow: 0 0 0 1px var(--accent-600), 0 8px 24px -4px rgba(15, 63, 180, 0.16);
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

    /* ─── Gender Radio Cards ─── */
    .rf-gender-group {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        outline: none;
    }

    .rf-gender-card {
        position: relative;
        cursor: pointer;
        margin-bottom: 0;
        user-select: none;
    }

    .rf-gender-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .rf-gender-card-inner {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #ffffff;
        border: 1.5px solid var(--border-card);
        border-radius: 12px;
        transition: all var(--transition-fast);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        min-height: 58px;
    }

    .rf-gender-card:hover .rf-gender-card-inner {
        border-color: #94a3b8;
        background: #fafbfe;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.06);
    }

    .rf-gender-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all var(--transition-fast);
    }

    .rf-gender-icon-male {
        background: #eef2fc;
        border: 1px solid #b8c9ef;
        color: #0b3290;
    }

    .rf-gender-icon-female {
        background: #fdf2f8;
        border: 1px solid #fbcfe8;
        color: #be185d;
    }

    .rf-gender-icon svg {
        width: 18px;
        height: 18px;
    }

    .rf-gender-content {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .rf-gender-title {
        font-family: var(--font-heading);
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--navy-900);
        line-height: 1.25;
    }

    .rf-gender-subtitle {
        font-size: 0.72rem;
        color: var(--text-muted);
        line-height: 1.2;
        margin-top: 2px;
    }

    .rf-gender-radio-mark {
        width: 20px;
        height: 20px;
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

    .rf-gender-radio-mark svg {
        width: 10px;
        height: 10px;
        stroke-width: 3;
        transition: transform var(--transition-fast);
        transform: scale(0.6);
    }

    /* Selected state */
    .rf-gender-card input[type="radio"]:checked + .rf-gender-card-inner {
        border-color: var(--accent-600);
        background-color: #eef2fc;
        box-shadow: 0 0 0 1px var(--accent-600), 0 4px 16px -2px rgba(15, 63, 180, 0.14);
    }

    .rf-gender-card input[type="radio"]:checked + .rf-gender-card-inner .rf-gender-radio-mark {
        border-color: var(--accent-600);
        background: var(--accent-600);
        color: #ffffff;
    }

    .rf-gender-card input[type="radio"]:checked + .rf-gender-card-inner .rf-gender-radio-mark svg {
        transform: scale(1);
    }

    .rf-gender-card input[type="radio"]:checked + .rf-gender-card-inner .rf-gender-title {
        color: var(--accent-700);
    }

    .rf-gender-card input[type="radio"]:focus-visible + .rf-gender-card-inner {
        box-shadow: var(--ring-focus);
    }

    /* Error state for group */
    .rf-gender-group.has-error .rf-gender-card-inner {
        border-color: var(--rose-600);
        background: #fffafa;
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
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .rf-digit-badge.is-valid {
        background: var(--emerald-50) !important;
        color: #0f7a52 !important;
        border-color: #a9dfc5 !important;
    }

    .rf-digit-badge.is-error {
        background: #fdebe5 !important;
        color: #c0392b !important;
        border-color: #f2b8ab !important;
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
        background: #fdebe5;
        color: #c0392b;
        border-color: #f2b8ab;
    }

    .rf-badge-status.is-valid {
        display: inline-flex;
        background: #e3f6ed;
        color: #0f7a52;
        border-color: #a9dfc5;
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
        border-color: #c0392b !important;
        background-color: #fff8f8 !important;
    }

    .rf-input.has-error:focus,
    .rf-select.has-error:focus,
    .rf-select-trigger.has-error:focus {
        box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.15) !important;
    }

    .rf-input.is-valid,
    .rf-select-trigger.is-valid {
        border-color: #0f7a52 !important;
        background-color: #f0fdf4 !important;
    }

    .rf-input.is-valid:focus,
    .rf-select-trigger.is-valid:focus {
        box-shadow: 0 0 0 3px rgba(15, 122, 82, 0.15) !important;
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
        color: #c0392b;
    }

    .rf-input.is-valid ~ .rf-input-icon.is-valid {
        display: flex;
        color: #0f7a52;
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
        color: #c0392b !important;
        font-weight: 500;
    }

    .rf-field-feedback.is-valid {
        display: flex !important;
        color: #0f7a52 !important;
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
        background: #fdebe5;
        border: 1.5px solid #f2b8ab;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(192, 57, 43, 0.08);
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
        background: #fdebe5;
        color: #c0392b;
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
        background: #fdebe5;
        border: 1px solid #f2b8ab;
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
        border-color: #c0392b !important;
        background-color: #fff8f8 !important;
        box-shadow: 0 0 0 1px #f2b8ab;
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

    .rf-select-trigger:disabled,
    .rf-select-trigger.is-disabled {
        background-color: #f8fafc !important;
        color: var(--text-muted) !important;
        cursor: not-allowed !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
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

    #wrapperStatusNonAsn {
        transition: opacity 220ms ease, transform 220ms ease;
    }
    #wrapperStatusNonAsn.rf-anim-show {
        animation: rfSlideFadeIn 220ms ease-out forwards;
    }
    @keyframes rfSlideFadeIn {
        from {
            opacity: 0;
            transform: translateY(-6px);
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

    /* Helper Hints (Teks petunjuk informatif berwarna biru sesuai butir 2) */
    .rf-hint {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 0.77rem;
        color: #0f3fb4;
        margin-top: 6px;
        line-height: 1.4;
    }

    .rf-hint svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        margin-top: 1px;
        color: #0f3fb4;
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

    /* Rasio 2:3, sama dengan bingkai foto di kartu anggota */
    .rf-photo-preview img {
        width: 40px;
        height: 60px;
        border-radius: 6px;
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

    .rf-photo-actions {
        display: flex;
        gap: 6px;
    }

    .rf-photo-recrop {
        background: transparent;
        border: 1px solid var(--border-subtle);
        color: var(--navy-900);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.76rem;
        font-weight: 500;
        cursor: pointer;
    }

    .rf-photo-recrop:hover {
        background: var(--bg-page);
    }

    .rf-photo-recrop:focus-visible,
    .rf-photo-remove:focus-visible {
        outline: 2px solid var(--accent-600);
        outline-offset: 2px;
    }

    /* Area crop: tinggi dibatasi supaya tombol tetap terlihat di layar HP */
    .rf-crop-stage {
        height: min(60vh, 420px);
        background: var(--bg-page);
        border-radius: 8px;
        overflow: hidden;
    }

    .rf-crop-stage img {
        display: block;
        max-width: 100%;
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

    /* Kartu bantuan gelap */
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
        color: #8fa7e6;
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
        color: #8fa7e6;
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
        max-width: 680px;
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
        padding: 22px 24px;
        overflow-y: auto;
        font-size: 0.84rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .rf-modal-body ol {
        padding-left: 20px;
        margin: 10px 0;
    }

    .rf-modal-body li {
        margin-bottom: 8px;
    }

    .rf-modal-body li strong {
        color: var(--navy-900);
    }

    /* Styling Dokumen Tata Tertib */
    .rf-doc-header {
        text-align: center;
        border-bottom: 2px solid var(--border-subtle);
        padding-bottom: 16px;
        margin-bottom: 18px;
    }

    .rf-doc-title {
        font-family: var(--font-heading);
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 0 0 8px 0;
        line-height: 1.4;
        text-transform: uppercase;
        letter-spacing: 0.01em;
    }

    .rf-doc-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 10px;
        background: var(--bg-page);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        padding: 8px 14px;
        display: inline-block;
        text-align: left;
    }

    .rf-doc-chapter {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 20px 0 10px 0;
        padding-bottom: 4px;
        border-bottom: 1px dashed var(--border-subtle);
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .rf-doc-article {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--navy-900);
        margin: 14px 0 6px 0;
    }

    .rf-doc-list {
        padding-left: 22px;
        margin: 8px 0 12px 0;
    }

    .rf-doc-list li {
        margin-bottom: 6px;
    }

    .rf-doc-list-alpha {
        list-style-type: lower-alpha;
        padding-left: 22px;
        margin: 6px 0 8px 0;
    }

    .rf-doc-list-alpha li {
        margin-bottom: 4px;
    }

    .rf-doc-highlight {
        background-color: #fefce8;
        border-left: 3px solid #eab308;
        padding: 6px 10px;
        border-radius: 4px;
        margin: 6px 0;
    }

    .rf-doc-sign {
        margin: 16px 0;
        padding: 12px 16px;
        background: var(--bg-page);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .rf-doc-annex {
        margin-top: 24px;
        padding-top: 18px;
        border-top: 2px dashed var(--border-subtle);
    }

    .rf-scroll-note {
        font-size: 0.78rem;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        padding: 10px 14px;
        border-radius: 8px;
        margin-top: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
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
            grid-template-columns: minmax(0,1fr);
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
            padding-top: 100px;
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
        .rf-radio-cards,
        .rf-gender-group {
            grid-template-columns: 1fr;
            gap: 10px;
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
            padding-top: 100px;
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

    /* ─── Halaman registrasi: hero, stepper, kartu, sidebar (desain Figma "Proto 1" + DESIGN.md) ─── */
    .rf-wrap { padding: 138px 0 96px; }
    .rf-wrap .container { max-width: 1360px; padding-inline: clamp(24px, 5vw, 76px); }

    .rf-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
        gap: 48px;
        align-items: center;
        margin-bottom: 44px;
    }
    .rf-hero-copy { min-width: 0; }
    .rf-title {
        font-family: var(--font-serif);
        font-weight: 600;
        font-size: clamp(32px, 3.8vw, 54px);
        line-height: 1.08;
        letter-spacing: -0.01em;
        font-variation-settings: "opsz" 24;
        color: var(--navy-950);
        margin: 0 0 22px;
    }
    .rf-title-mark { position: relative; display: inline-block; }
    @media (min-width: 769px) {
        .rf-title-line { white-space: nowrap; }
        .rf-title-mark { display: block; width: fit-content; }
    }
    .rf-lead { font-size: 15px; line-height: 1.8; max-width: 540px; color: var(--text-secondary); margin: 0 0 26px; }
    .rf-chips { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 10px; }
    .rf-chips li {
        display: inline-flex; align-items: center; gap: 8px; min-height: 36px; padding: 0 14px;
        background: #fff; border: 1px solid var(--border-card); border-radius: 999px;
        font-size: 13px; font-weight: 700; color: var(--navy-950);
    }
    .rf-chips svg { width: 16px; height: 16px; color: var(--accent-600); flex: none; }

    /* Pratinjau kartu: replika kartu cetak 342 x 216. Semua ukuran dalam --u (1 px desain) supaya ikut mengecil. */
    .rf-card-stage { position: relative; width: 100%; max-width: 480px; justify-self: center; padding: 0 30px 40px 0; }
    .rf-card-tilt { position: relative; }
    .rf-mcard {
        --u: calc(100cqw / 342);
        container-type: inline-size;
        position: relative; width: 100%; aspect-ratio: 342 / 216; overflow: hidden;
        border-radius: 3.5088% / 5.5556%;
        box-shadow: 0 12px 48px rgba(27, 58, 92, 0.2), 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }
    /* Bayangan depan dibuat tipis: bayangan lebar jatuh ke kartu belakang dan membuatnya tampak abu-abu */
    .rf-mcard-front { z-index: 1; background: #fff; color: #0d1c2e; transform: rotate(-3deg); box-shadow: 0 1px 3px rgba(13, 28, 46, 0.12), 0 0 0 1px rgba(13, 28, 46, 0.04); }
    .rf-mcard-back { position: absolute; inset: 0; z-index: 0; background: #fff; transform: translate(24px, 22px) rotate(3deg); }
    /* Lingkaran besar berisi foto ruang baca di kanan depan; cincin emas dan biru dari box-shadow */
    .rf-mc-lengkung {
        position: absolute; left: calc(165 * var(--u)); top: calc(-95 * var(--u)); width: calc(520 * var(--u)); height: calc(520 * var(--u));
        border-radius: 50%; overflow: hidden; background: #17406b;
        box-shadow: 0 0 0 calc(3.5 * var(--u)) #d4a437, 0 0 0 calc(7 * var(--u)) #2e7bc4;
    }
    .rf-mc-lengkung img { position: absolute; left: 0; top: calc(95 * var(--u)); width: calc(177 * var(--u)); height: calc(216 * var(--u)); object-fit: cover; object-position: 72% 50%; }
    .rf-mc-kepala { position: absolute; left: calc(20 * var(--u)); top: calc(18 * var(--u)); display: flex; flex-direction: column; gap: calc(7 * var(--u)); color: #17406b; }
    .rf-mc-baris-logo { display: flex; align-items: center; gap: calc(6 * var(--u)); }
    .rf-mc-logo { display: block; width: calc(24 * var(--u)); height: calc(24 * var(--u)); object-fit: contain; }
    .rf-mc-judul { font-size: calc(7.5 * var(--u)); font-weight: 800; line-height: 1.2; letter-spacing: 0.08em; }
    .rf-mc-sub { margin-top: calc(1 * var(--u)); font-size: calc(5.5 * var(--u)); font-weight: 500; line-height: 1.3; color: #4a5a6b; }
    .rf-mc-garis { display: flex; gap: calc(2 * var(--u)); }
    .rf-mc-garis i { display: block; height: calc(2 * var(--u)); border-radius: calc(1 * var(--u)); }
    .rf-mc-garis i:nth-child(1) { width: calc(32 * var(--u)); background: #17406b; }
    .rf-mc-garis i:nth-child(2) { width: calc(20 * var(--u)); background: #2e7bc4; }
    .rf-mc-garis i:nth-child(3) { width: calc(12 * var(--u)); background: #d4a437; }
    .rf-mc-kolom {
        position: absolute; left: calc(20 * var(--u)); top: calc(56 * var(--u)); width: calc(132 * var(--u));
        display: flex; flex-direction: column; align-items: flex-start; gap: calc(8 * var(--u));
    }
    .rf-mc-foto {
        position: relative; flex: none; width: calc(54 * var(--u)); height: calc(72 * var(--u)); overflow: hidden;
        border-radius: calc(4 * var(--u)); background: #e8eef4;
        display: grid; place-items: center;
        font-size: calc(14 * var(--u)); font-weight: 800; color: rgba(13, 28, 46, 0.5);
    }
    .rf-mc-foto img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; }
    .rf-mc-nama { width: 100%; }
    .rf-mc-baris { display: block; font-size: calc(12 * var(--u)); font-weight: 800; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-bottom: calc(2 * var(--u)); margin-bottom: calc(-2 * var(--u)); }
    .rf-mc-barcode { position: absolute; left: calc(20 * var(--u)); bottom: calc(14 * var(--u)); height: calc(14 * var(--u)); }
    /* Kartu belakang sesuai desain: kepala, QR di tengah, masa berlaku dan petunjuk di kiri bawah */
    .rf-mc-qr { position: absolute; left: calc(127 * var(--u)); top: calc(61 * var(--u)); width: calc(88 * var(--u)); height: calc(88 * var(--u)); }
    .rf-mc-qr path { fill: #0d1c2e; }
    .rf-mc-no { position: absolute; left: 0; right: 0; top: calc(153 * var(--u)); text-align: center; font-size: calc(6 * var(--u)); font-weight: 700; line-height: 1.4; letter-spacing: 0.2em; color: #0d1c2e; }
    .rf-mc-berlaku { position: absolute; left: calc(20 * var(--u)); bottom: calc(18 * var(--u)); display: flex; flex-direction: column; gap: calc(5 * var(--u)); white-space: nowrap; }
    .rf-mc-berlaku b { display: block; font-size: calc(5.5 * var(--u)); font-weight: 700; line-height: 1.4; letter-spacing: 0.12em; color: #5b6878; }
    .rf-mc-berlaku span { display: block; margin-top: calc(1 * var(--u)); font-size: calc(6.5 * var(--u)); font-weight: 700; line-height: 1.4; color: #17406b; }
    .rf-mc-berlaku small { font-size: calc(5.5 * var(--u)); font-weight: 500; line-height: 1.4; color: #5b6878; }
    .rf-mc-nomor { margin-top: calc(-4 * var(--u)); font-size: calc(9 * var(--u)); font-weight: 700; line-height: 1.3; letter-spacing: 0.1em; color: #5b6878; white-space: nowrap; }
    /* Kursor di atas kartu: kartu depan meluruskan diri, kartu belakang mendekat */
    @media (hover: hover) {
        .rf-card-stage:hover .rf-mcard-front { transform: rotate(0deg); }
        .rf-card-stage:hover .rf-mcard-back { transform: translate(16px, 14px) rotate(1.5deg); }
    }


    /* Stepper */
    .rf-stepper { padding: 14px 22px; border-radius: 20px; margin-bottom: 32px; gap: 14px; box-shadow: var(--shadow-card); }
    .rf-step { flex: 0 0 auto; gap: 12px; padding: 6px 8px; border-radius: 14px; }
    .rf-step-num { width: 36px; height: 36px; border-radius: 10px; font-size: 14px; background: #fff; border-color: var(--border-card); }
    .rf-step-label { font-size: 11px; font-weight: 800; letter-spacing: 0.1em; line-height: 1.2; }
    .rf-step-title { font-size: 14px; font-weight: 700; margin-top: 3px; }
    .rf-step-line { flex: 1 1 24px; margin: 0; background: var(--border-card); }
    .rf-step.active .rf-step-num { background: var(--navy-950); border-color: var(--navy-950); }

    /* Kolom form dan kartu section */
    #formRegistrasi { display: flex; flex-direction: column; gap: 24px; }
    .rf-sidebar { top: 118px; gap: 24px; }
    .rf-section { margin: 0; border-radius: 24px; scroll-margin-top: 120px; }
    .rf-section-num { width: 32px; height: 32px; border-radius: 9px; font-size: 12px; }
    .rf-section-title { font-size: 18px; letter-spacing: -0.01em; }
    .rf-section-desc { font-size: 14px; line-height: 1.6; }
    .rf-required-tag {
        font-size: 11px; font-weight: 800; letter-spacing: 0.08em; white-space: nowrap;
        color: var(--text-muted); background: var(--bg-panel); border: 0; padding: 5px 9px; border-radius: 6px;
    }

    /* Kontrol */
    .rf-label { font-size: 14px; font-weight: 700; }
    .rf-input, .rf-select, .rf-select-trigger { height: 52px; border-radius: 12px; font-size: 14px; }
    textarea.rf-input { height: auto; min-height: 120px; }
    .rf-input:hover, .rf-select:hover, .rf-select-trigger:hover { border-color: var(--text-subtle); }
    .rf-input:focus, .rf-select:focus, .rf-select-trigger:focus,
    .rf-custom-select.is-open .rf-select-trigger { border-color: var(--accent-600); box-shadow: var(--ring-focus); }
    .rf-select-trigger:disabled, .rf-select-trigger.is-disabled { background-color: var(--bg-panel) !important; border-color: var(--border-card) !important; }
    .rf-digit-badge { background: var(--bg-panel); border: 0; font-size: 12px; font-weight: 700; padding: 3px 9px; border-radius: 999px; font-variant-numeric: tabular-nums; }
    .rf-gender-card-inner { min-height: 60px; border-radius: 14px; box-shadow: none; }
    .rf-gender-card:hover .rf-gender-card-inner { background: #fff; border-color: var(--text-subtle); transform: none; box-shadow: none; }
    .rf-gender-card input[type="radio"]:checked + .rf-gender-card-inner { background: var(--accent-50); box-shadow: none; }
    .rf-hint { font-size: 13px; }

    /* Toggle foto: radio tetap bisa difokus keyboard */
    .rf-photo-toggle { background: var(--bg-panel); border: 0; border-radius: 12px; padding: 4px; }
    .rf-photo-toggle label { position: relative; min-height: 44px; padding: 0 16px; border-radius: 9px; font-size: 14px; font-weight: 600; }
    .rf-photo-toggle input[type="radio"] { display: block; position: absolute; inset: 0; margin: 0; opacity: 0; cursor: pointer; }
    .rf-photo-toggle label:focus-within { outline: 2px solid var(--accent-600); outline-offset: 2px; }
    .rf-photo-toggle label.is-checked { background: #fff; color: var(--navy-950); font-weight: 700; box-shadow: 0 1px 3px rgba(22, 33, 51, 0.1); }

    .rf-agree-box { background: var(--bg-page); border-radius: 16px; padding: 18px 20px; }
    .rf-agree-row input[type="checkbox"] { width: 20px; height: 20px; }

    /* Aksi */
    .rf-actions { flex-wrap: wrap; justify-content: space-between; gap: 14px; padding-top: 20px; }
    .rf-progress { display: flex; align-items: center; gap: 8px; margin: 0 auto 0 0; font-size: 13px; color: var(--text-muted); }
    .rf-progress svg { width: 16px; height: 16px; flex: none; }
    .rf-progress strong { color: var(--navy-950); font-weight: 800; }
    .rf-progress.is-done, .rf-progress.is-done strong { color: var(--emerald-600); }
    .rf-btn { height: 48px; border-radius: 12px; font-size: 14px; font-weight: 700; }
    .rf-btn:focus-visible { outline: none; box-shadow: var(--ring-focus); }
    .rf-btn-primary { background: var(--accent-600); box-shadow: none; }
    .rf-btn-primary:hover:not(:disabled) { background: var(--navy-950); transform: none; box-shadow: none; }
    .rf-btn-primary:disabled { background: var(--border-card); color: var(--text-muted); }
    .rf-btn-ghost { background: #fff; border-color: var(--border-input); color: var(--navy-950); }
    .rf-btn-ghost:hover { background: #fff; border-color: var(--accent-600); color: var(--accent-600); }

    /* Sidebar */
    .rf-side-card { padding: 26px; border-radius: 24px; }
    .rf-side-title { font-size: 16px; }
    .rf-side-sub { font-size: 13px; }
    .rf-benefits-list li { font-size: 14px; }
    .rf-timeline-title { font-size: 14px; }
    .rf-timeline-desc { font-size: 13px; }
    .rf-timeline-node { border-color: var(--border-input); }
    .rf-timeline-item:first-child .rf-timeline-node { border-color: var(--accent-600); }
    .rf-timeline::before { background: var(--border-card); }
    .rf-side-card.dark { background: #11161f; border-color: #11161f; box-shadow: none; }
    .rf-side-card.dark .rf-side-sub { color: #a3abba; }
    .rf-dark-content strong { color: #fff; font-size: 13px; }
    .rf-dark-content span, .rf-dark-content a { display: block; font-size: 13px; line-height: 1.5; color: #a3abba; }
    .rf-dark-content .rf-dark-sub { font-size: 12px; margin-bottom: 4px; }
    .rf-dark-content a:hover { color: #fff; }

    @media (min-width: 769px) {
        .rf-section { padding: 32px; }
        .rf-section-desc { padding-left: 44px; }
    }
    @media (max-width: 992px) {
        .rf-hero { grid-template-columns: minmax(0, 1fr); gap: 36px; }
        .rf-card-stage { justify-self: start; }
    }
    @media (max-width: 800px) {
        .rf-wrap { padding-top: 112px; }
    }
    @media (max-width: 640px) {
        .rf-wrap { padding-bottom: 64px; }
        .rf-section { border-radius: 18px; }
        .rf-stepper { padding: 8px 10px; gap: 4px; border-radius: 16px; }
        .rf-step { flex: 1 1 0; justify-content: center; padding: 6px 4px; gap: 6px; min-height: 44px; }
        .rf-step-num { width: 26px; height: 26px; font-size: 12px; border-radius: 8px; }
        .rf-step-line { flex: 0 0 12px; }
        .rf-mcard-front { transform: rotate(-2deg); }
        .rf-mcard-back { transform: translate(16px, 16px) rotate(2deg); }
        .rf-card-stage { padding: 0 20px 40px 0; }
        .rf-actions { flex-direction: column; align-items: stretch; }
        .rf-progress { order: 0; margin: 0; }
        #btnSubmitRegistrasi { order: 1; }
        #btnReset { order: 2; }
    }
    @media (max-width: 380px) {
        .rf-step { padding: 4px 2px; gap: 4px; }
        .rf-step-num { width: 22px; height: 22px; font-size: 11px; }
        .rf-step-line { flex-basis: 6px; }
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

        <section class="rf-hero" aria-labelledby="rfJudul">
            <div class="rf-hero-copy">
                <h1 class="rf-title" id="rfJudul"><span class="rf-title-line">Registrasi Keanggotaan</span> <span class="rf-title-mark">Perpustakaan</span></h1>
                <p class="rf-lead">Akses koleksi cetak, repositori ilmiah, dan ruang baca digital terpadu melalui Kartu Anggota resmi Perpustakaan Kementerian Ketenagakerjaan.</p>

                <ul class="rf-chips">
                    <li><?= landing_icon('jam') ?>± 5 menit pengisian</li>
                    <li><?= landing_icon('perisai') ?>Verifikasi 1×24 jam kerja</li>
                    <li><?= landing_icon('surat') ?>Kartu digital via email</li>
                </ul>
            </div>

            <div class="rf-card-stage">
                <?php /* Replika sisi depan dan belakang kartu cetak (Anggota/Views/pdf/kartu-anggota.php, 342 x 216 px). Ukuran memakai satuan --u supaya ikut mengecil di layar sempit. */
                $_kepala = '<div class="rf-mc-kepala"><div class="rf-mc-baris-logo"><img class="rf-mc-logo" src="' . base_url('assets/img/kartu/logo-kemnaker.png') . '" alt="">'
                    . '<div><div class="rf-mc-judul">PERPUSTAKAAN</div><div class="rf-mc-sub">Kementerian Ketenagakerjaan RI</div></div></div>'
                    . '<div class="rf-mc-garis" aria-hidden="true"><i></i><i></i><i></i></div></div>';

                // Barcode dan QR contoh dari nomor palsu, supaya pratinjau terlihat seperti kartu jadi
                helper('anggota');
                $_contoh   = '26012600001';
                $_barcode  = kartuBarcode($_contoh);
                $_jalurBc  = '';
                foreach ($_barcode['bar'] as [$_bx, $_bw]) {
                    $_jalurBc .= "M{$_bx} 0h{$_bw}v1h-{$_bw}z";
                }
                $_qr       = (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->getMatrix($_contoh)->matrix(true);
                $_ukuranQr = count($_qr);
                $_jalurQr  = '';
                foreach ($_qr as $_y => $_baris) {
                    foreach ($_baris as $_x => $_gelap) {
                        $_jalurQr .= $_gelap ? "M{$_x} {$_y}h1v1h-1z" : '';
                    }
                } ?>
                <div class="rf-card-tilt">
                    <div class="rf-mcard rf-mcard-back" aria-hidden="true">
                        <?= $_kepala ?>
                        <svg class="rf-mc-qr" viewBox="0 0 <?= $_ukuranQr ?> <?= $_ukuranQr ?>" shape-rendering="crispEdges"><path d="<?= $_jalurQr ?>"/></svg>
                        <div class="rf-mc-no"><?= $_contoh ?></div>
                        <div class="rf-mc-berlaku"><div><b>BERLAKU S/D</b><span>Setelah verifikasi</span></div><small>Pindai kode QR di meja layanan</small></div>
                    </div>
                    <div class="rf-mcard rf-mcard-front" role="img" aria-label="Pratinjau sisi depan Kartu Anggota, terisi sesuai data formulir">
                        <div class="rf-mc-lengkung"><img src="<?= base_url('assets/home/hero/01-ruang-baca.jpg') ?>" alt=""></div>
                        <?= $_kepala ?>
                        <div class="rf-mc-kolom">
                            <div class="rf-mc-foto">
                                <span id="mcPhotoQ">?</span>
                                <img id="mcPhotoImg" alt="" hidden>
                            </div>
                            <div class="rf-mc-nama" id="mcNama">
                                <div id="mcName"><span class="rf-mc-baris">Nama Lengkap</span></div>
                            </div>
                            <div class="rf-mc-nomor">Nomor anggota</div>
                        </div>
                        <svg class="rf-mc-barcode" style="width: calc(<?= min(132, $_barcode['lebar']) ?> * var(--u))" viewBox="0 0 <?= $_barcode['lebar'] ?> 1" preserveAspectRatio="none" shape-rendering="crispEdges"><path fill="#0d1c2e" d="<?= $_jalurBc ?>"/></svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Progress Stepper (Connected Timeline) -->
        <div class="rf-stepper" role="tablist" aria-label="Alur Pendaftaran">
            <button type="button" class="rf-step active" data-target="section-1" role="tab" aria-selected="true">
                <span class="rf-step-num">1</span>
                <div class="rf-step-content">
                    <span class="rf-step-label">Langkah 01</span>
                    <span class="rf-step-title">
                        <span class="rf-title-desktop">Nomor Induk Pegawai</span>
                        <span class="rf-title-mobile">NIP</span>
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

                    <!-- Section 1: Nomor Induk Pegawai (NIP) -->
                    <div class="rf-section" id="section-1">
                        <div class="rf-section-head">
                            <div class="rf-section-title-wrap">
                                <span class="rf-section-num">01</span>
                                <h2 class="rf-section-title">Nomor Induk Pegawai (NIP)</h2>
                            </div>
                            <span class="rf-required-tag">Wajib Diisi</span>
                        </div>
                        <p class="rf-section-desc">Pendaftaran keanggotaan perpustakaan diperuntukkan bagi Pegawai ASN Kementerian Ketenagakerjaan menggunakan NIP 18 digit resmi BKN.</p>

                        <input type="hidden" id="JenisAnggota_id" name="JenisAnggota_id" value="<?= esc($_asnId) ?>">
                        <input type="hidden" id="IdentityType_id" name="IdentityType_id" value="<?= esc($_nipId) ?>">

                        <!-- Identity Input Section -->
                        <div class="rf-field" id="identityBlock" style="margin-top: 10px;">
                            <div class="rf-field-header">
                                <label class="rf-label" id="identityLabel" for="IdentityNo">
                                    Nomor Induk Pegawai (NIP) <span class="req">*</span>
                                </label>
                                <span class="rf-digit-badge" id="digitCount">0 / 18 digit</span>
                            </div>

                            <!-- Petunjuk kolom NIP dipindahkan ke atas kolom isian berwarna biru -->
                            <div class="rf-hint" id="identityHint" style="margin-top: 2px; margin-bottom: 8px;">
                                <svg viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                                </svg>
                                <span>Masukkan 18 digit NIP tanpa spasi atau tanda baca.</span>
                            </div>

                            <div class="rf-input-wrap">
                                <input type="text" class="rf-input" id="IdentityNo" name="IdentityNo"
                                       placeholder="Masukkan 18 digit NIP tanpa spasi atau tanda baca" inputmode="numeric" autocomplete="off" maxlength="18">
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
                        </div>
                    </div>

                    <!-- Section 2: Data Diri & Domisili -->
                    <div class="rf-section" id="section-2">
                        <div class="rf-section-head">
                            <div class="rf-section-title-wrap">
                                <span class="rf-section-num">02</span>
                                <h2 class="rf-section-title">Data Diri & Satuan Kerja</h2>
                            </div>
                            <span class="rf-required-tag">Wajib Diisi</span>
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
                                       placeholder="Contoh: Raden Satria Pamungkas" required autocomplete="name" pattern="<?= esc(\App\Validation\NamaRules::POLA, 'attr') ?>" title="Hanya huruf A-Z, spasi, titik, koma, apostrof, dan tanda hubung">
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

                        <!-- Field Jenis Kelamin -->
                        <div class="rf-field" id="fieldSex">
                            <div class="rf-field-header">
                                <label class="rf-label">
                                    Jenis Kelamin <span class="req">*</span>
                                </label>
                                <span class="rf-badge-status" id="badge_Sex"></span>
                            </div>
                            <div class="rf-gender-group" id="sexRadioGroup" tabindex="-1">
                                <?php if (!empty($jenis_kelamin)): ?>
                                    <?php foreach ($jenis_kelamin as $jk): ?>
                                    <label class="rf-gender-card" for="sex_<?= $jk->ID ?>">
                                        <input type="radio" name="Sex_id" id="sex_<?= $jk->ID ?>" value="<?= $jk->ID ?>" data-label="<?= esc($jk->Name) ?>" required>
                                        <div class="rf-gender-card-inner">
                                            <div class="rf-gender-icon rf-gender-icon-<?= $jk->ID == 1 ? 'male' : 'female' ?>">
                                                <?php if ($jk->ID == 1): ?>
                                                <!-- Mars / Male Icon -->
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="10" cy="14" r="5"></circle>
                                                    <line x1="19" y1="5" x2="13.6" y2="10.4"></line>
                                                    <line x1="19" y1="5" x2="14" y2="5"></line>
                                                    <line x1="19" y1="5" x2="19" y2="10"></line>
                                                </svg>
                                                <?php else: ?>
                                                <!-- Venus / Female Icon -->
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="9" r="5"></circle>
                                                    <line x1="12" y1="14" x2="12" y2="21"></line>
                                                    <line x1="9" y1="18" x2="15" y2="18"></line>
                                                </svg>
                                                <?php endif; ?>
                                            </div>
                                            <div class="rf-gender-content">
                                                <span class="rf-gender-title"><?= esc($jk->Name) ?></span>
                                            </div>
                                            <div class="rf-gender-radio-mark" aria-hidden="true">
                                                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3.5 8.5l3 3 6-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="rf-field-feedback" id="feedback_Sex"></div>
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

                        <!-- Dropdown Berantai: Sub-Unit Kerja -->
                        <div class="rf-field" id="wrapperSubUnitKerja">
                            <div class="rf-field-header">
                                <label class="rf-label" for="SubUnitKerja_id">
                                    Biro / Direktorat / Unit (Eselon II) <span class="req" id="reqSubUnitKerja">*</span>
                                </label>
                                <span class="rf-badge-status" id="badge_SubUnitKerja"></span>
                            </div>
                            <div class="rf-custom-select is-disabled" id="customSubUnitKerja">
                                <!-- Real select for form payload & validation -->
                                <select class="rf-select-native" id="SubUnitKerja_id" name="SubUnitKerja_id" tabindex="-1" aria-hidden="true" disabled>
                                    <option value="">Pilih unit kerja dahulu</option>
                                </select>

                                <!-- Visual Trigger -->
                                <button type="button" class="rf-select-trigger is-disabled" id="subUnitKerjaTrigger" aria-haspopup="listbox" aria-expanded="false" disabled>
                                    <span class="rf-select-value is-placeholder" id="subUnitKerjaValue">Pilih unit kerja dahulu</span>
                                    <svg class="rf-select-arrow" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 8l4 4 4-4"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div class="rf-select-dropdown" id="subUnitKerjaDropdown" role="listbox" tabindex="-1">
                                    <div class="rf-select-option is-placeholder" data-value="" role="option">
                                        <span class="rf-opt-text">Pilih unit kerja dahulu</span>
                                    </div>
                                </div>
                            </div>
                            <div class="rf-field-feedback" id="feedback_SubUnitKerja"></div>
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
                                <span class="rf-sublabel">Opsional</span>
                            </div>
                            <div class="rf-hint" style="margin-top:0;margin-bottom:8px;">Gunakan pas foto formal dengan latar polos. Format JPG atau PNG, maksimal 2 MB.</div>

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
                                    <div class="rf-dropzone-text">Pilih pas foto</div>
                                    <div class="rf-dropzone-sub">Klik atau seret foto ke sini. Setelah dipilih, Anda bisa mengatur bagian yang dipakai.</div>
                                </div>

                                <div class="rf-photo-preview" id="photoPreviewContainer" style="display:none;">
                                    <img id="photoPreview" src="" alt="Pratinjau foto untuk kartu anggota">
                                    <div style="flex:1;">
                                        <div style="font-size:0.8rem;font-weight:600;color:var(--navy-900);">Foto terpasang</div>
                                        <div style="font-size:0.74rem;color:var(--text-muted);">Bagian ini yang tampil di kartu anggota digital.</div>
                                    </div>
                                    <div class="rf-photo-actions">
                                        <button type="button" class="rf-photo-recrop" id="photoRecropBtn">Atur ulang</button>
                                        <button type="button" class="rf-photo-remove" id="photoRemoveBtn">Hapus</button>
                                    </div>
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
                            <span class="rf-required-tag">Wajib Diisi</span>
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
                                    <span>Bukti registrasi, status verifikasi, dan notifikasi sirkulasi dikirim ke alamat surel ini.</span>
                                </div>
                            </div>

                            <div class="rf-field">
                                <div class="rf-field-header">
                                    <label class="rf-label" for="Phone">
                                        Nomor Handphone Aktif <span class="req">*</span>
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
                                    <span>Nomor handphone pemustaka yang aktif.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agreement Box -->
                        <div class="rf-agree-box" id="agreeBox">
                            <div class="rf-agree-row">
                                <input type="checkbox" id="Persetujuan" name="Persetujuan" value="1" required>
                                <label for="Persetujuan">
                                    <strong>Pernyataan Pemustaka:</strong>
                                    Saya menyatakan data yang diisi adalah benar dan sah. Saya bersedia menaati tata tertib Perpustakaan serta menyetujui pemrosesan data pribadi untuk administrasi sirkulasi sesuai
                                    <a href="javascript:void(0)" id="linkSyarat">Syarat & Ketentuan Keanggotaan</a>.
                                </label>
                            </div>
                            <div class="rf-field-feedback" id="persetujuanFeedback" style="margin-top: 10px; padding-left: 30px;"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="rf-actions">
                            <p class="rf-progress" id="rfProgress" role="status">
                                <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/></svg>
                                <span id="rfProgressText"><strong>9</strong> isian wajib belum lengkap</span>
                            </p>
                            <button type="reset" class="rf-btn rf-btn-ghost" id="btnReset">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 109-9 9.75 9.75 0 00-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                </svg>
                                <span>Bersihkan Formulir</span>
                            </button>
                            <button type="button" class="rf-btn rf-btn-primary" id="btnSubmitRegistrasi" disabled>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                                <span>Kirim Formulir Registrasi</span>
                            </button>
                        </div>

                        <div id="alertMessage" class="rf-alert"></div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

<!-- Modal Atur Foto (crop 2:3 untuk kartu anggota) -->
<div id="modalCrop" class="rf-overlay" role="dialog" aria-modal="true" aria-labelledby="rfCropTitle">
    <div class="rf-modal">
        <div class="rf-modal-head">
            <h3 id="rfCropTitle">Atur Foto</h3>
            <button type="button" class="rf-modal-close" id="closeCrop" aria-label="Batal">&times;</button>
        </div>
        <div class="rf-modal-body">
            <p style="margin:0 0 12px;">Geser dan perbesar foto sampai kepala dan bahu masuk ke dalam bingkai.</p>
            <div class="rf-crop-stage">
                <img id="cropImage" src="" alt="Foto yang sedang diatur">
            </div>
        </div>
        <div class="rf-modal-foot">
            <button type="button" class="rf-btn rf-btn-ghost" id="btnBatalCrop">Batal</button>
            <button type="button" class="rf-btn rf-btn-primary" id="btnPakaiCrop">Pakai foto ini</button>
        </div>
    </div>
</div>

<!-- Modal Syarat & Ketentuan Keanggotaan -->
<div id="modalSyarat" class="rf-overlay" role="dialog" aria-modal="true" aria-labelledby="rfModalTitle">
    <div class="rf-modal">
        <div class="rf-modal-head">
            <h3 id="rfModalTitle">Tata Tertib Keanggotaan & Layanan Sirkulasi</h3>
            <button type="button" class="rf-modal-close" id="closeSyarat" aria-label="Tutup">&times;</button>
        </div>
        <div class="rf-modal-body" id="syaratScrollbox">
            <div class="rf-doc-header">
                <h4 class="rf-doc-title">TATA TERTIB KEANGGOTAAN DAN LAYANAN SIRKULASI<br>PERPUSTAKAAN KEMENTERIAN KETENAGAKERJAAN</h4>
            </div>

            <div class="rf-doc-chapter">BAB I. KETENTUAN UMUM</div>
            <div class="rf-doc-article">Pasal 1</div>
            <p>Dalam tata tertib ini yang dimaksud dengan:</p>
            <ol class="rf-doc-list-alpha">
                <li><strong>Perpustakaan</strong> adalah Perpustakaan Kementerian Ketenagakerjaan di lingkungan Biro Hubungan Masyarakat, Sekretariat Jenderal.</li>
                <li><strong>Anggota</strong> adalah pegawai yang terdaftar dan memiliki kartu anggota Perpustakaan.</li>
                <li><strong>Pengelola</strong> adalah pustakawan dan petugas pada Tim Kerja Informasi Publik dan Perpustakaan yang melaksanakan layanan Perpustakaan.</li>
                <li><strong>Sirkulasi</strong> adalah layanan peminjaman, pengembalian, dan perpanjangan koleksi.</li>
                <li><strong>Koleksi</strong> adalah seluruh bahan perpustakaan yang dikelola Perpustakaan.</li>
            </ol>

            <div class="rf-doc-chapter">BAB II. KEANGGOTAAN</div>
            <div class="rf-doc-article">Pasal 2 (Jenis Anggota)</div>
            <p>Anggota terdiri atas:</p>
            <ol class="rf-doc-list-alpha">
                <li><strong>Anggota Pegawai</strong>, yaitu pegawai Kementerian Ketenagakerjaan berstatus ASN.</li>
            </ol>

            <div class="rf-doc-article">Pasal 3 (Syarat dan Data Pendaftaran)</div>
            <ol class="rf-doc-list">
                <li>Pendaftar menyerahkan data yang benar dan lengkap, yaitu:
                    <ol class="rf-doc-list-alpha">
                        <li>nama lengkap;</li>
                        <li>NIP;</li>
                        <li>unit kerja atau unit penempatan;</li>
                        <li>alamat domisili;</li>
                        <li>alamat email dan nomor telepon yang aktif;</li>
                        <li>pilihan mencantumkan atau tidak mencantumkan pasfoto pada kartu anggota.</li>
                    </ol>
                </li>
                <li>Pendaftar menyatakan telah membaca dan menyetujui tata tertib ini, serta menyetujui pemrosesan data pribadinya, NIP, oleh Perpustakaan untuk verifikasi identitas, pengelolaan keanggotaan, dan layanan sirkulasi sebagaimana diatur dalam Pasal 20.</li>
            </ol>

            <div class="rf-doc-article">Pasal 4 (Tata Cara Pendaftaran)</div>
            <ol class="rf-doc-list">
                <li>Pendaftaran dilakukan melalui formulir pendaftaran yang disediakan Perpustakaan.</li>
                <li>Pengelola memverifikasi status pendaftar dengan memeriksa kartu identitas pegawai (langsung atau melalui foto/pindaian), serta mencatat hasil verifikasi, nama petugas, dan tanggal verifikasi, sebelum keanggotaan diaktifkan.</li>
                <li>Pendaftaran yang datanya belum sesuai atau tidak memenuhi syarat ditolak. Pendaftar diberi tahu melalui email beserta alasannya dan dapat mengirim ulang pendaftaran melalui formulir pendaftaran.</li>
                <li>Setelah diaktifkan, Anggota menerima nomor anggota dan kartu anggota digital melalui email.</li>
                <li>Formulir pendaftaran, pernyataan persetujuan, catatan verifikasi, dan catatan pengiriman kartu disimpan sebagai bukti pendaftaran, dengan memperhatikan Pasal 20 ayat (7).</li>
                <li>Setiap Anggota diberi nomor anggota Perpustakaan yang berbeda dari NIP, dengan format tahun-bulan-tanggal pengesahan keanggotaan diikuti nomor urut lima digit. Nomor urut berjalan terus dan tidak diulang setiap hari. Nomor anggota tidak berubah selama Anggota terdaftar, termasuk saat perpanjangan.</li>
            </ol>

            <div class="rf-doc-article">Pasal 5 (Masa Berlaku)</div>
            <ol class="rf-doc-list">
                <li>Keanggotaan Pegawai berlaku 1 (satu) tahun sejak tanggal keanggotaan diaktifkan dan dapat diperpanjang.</li>
                <li>Perpanjangan keanggotaan dilakukan dengan konfirmasi data kepada Pengelola sebelum masa berlaku berakhir.</li>
            </ol>

            <div class="rf-doc-article">Pasal 6 (Kartu Anggota)</div>
            <ol class="rf-doc-list">
                <li>Kartu anggota berbentuk digital dan/atau cetak. Kartu digital dikirim melalui email setelah keanggotaan diaktifkan dan berlaku sebagai bukti keanggotaan sejak diterbitkan. Kartu cetak diterbitkan menyusul sesuai ketersediaan.</li>
                <li>Pasfoto Anggota dicantumkan pada kartu anggota hanya apabila Anggota memilihnya pada formulir pendaftaran.</li>
                <li>Kartu anggota bersifat pribadi dan tidak boleh dipindahtangankan.</li>
                <li>Kehilangan atau kerusakan kartu anggota dilaporkan kepada Pengelola untuk diterbitkan ulang dengan nomor anggota yang sama. Kartu yang telah diterbitkan ulang menggantikan kartu sebelumnya, dan kartu sebelumnya tidak berlaku lagi.</li>
            </ol>

            <div class="rf-doc-article">Pasal 7 (Berakhirnya Keanggotaan)</div>
            <ol class="rf-doc-list">
                <li>Keanggotaan berakhir apabila:
                    <ol class="rf-doc-list-alpha">
                        <li>masa berlaku berakhir dan tidak diperpanjang;</li>
                        <li>Anggota mengundurkan diri;</li>
                        <li>Hak tarik persetujuan data pribadi.</li>
                    </ol>
                </li>
                <li>Keanggotaan Anggota Pegawai yang pindah tugas ke luar Kementerian, pensiun, atau berhenti sebagai pegawai berakhir pada saat masa berlaku keanggotaannya berakhir tanpa perpanjangan.</li>
                <li>Anggota yang mengundurkan diri dapat mendaftar kembali setelah periode 1 (satu) bulan sejak pengunduran diri.</li>
                <li>Sebelum keanggotaan berakhir, Anggota wajib mengembalikan seluruh koleksi yang dipinjam.</li>
            </ol>

            <div class="rf-doc-chapter">BAB III. HAK DAN KEWAJIBAN ANGGOTA</div>
            <div class="rf-doc-article">Pasal 8 (Hak)</div>
            <p>Anggota berhak:</p>
            <ol class="rf-doc-list-alpha">
                <li>memperoleh layanan dan fasilitas Perpustakaan;</li>
                <li>memperoleh informasi tentang koleksi dan layanan;</li>
                <li>meminjam koleksi sesuai ketentuan;</li>
                <li>menyampaikan saran dan pengaduan kepada Pengelola.</li>
            </ol>

            <div class="rf-doc-article">Pasal 9 (Kewajiban)</div>
            <ol class="rf-doc-list">
                <li>Anggota wajib:
                    <ol class="rf-doc-list-alpha">
                        <li>mematuhi tata tertib ini;</li>
                        <li>memberikan data yang benar dan memperbarui data apabila berubah;</li>
                        <li>menjaga kartu anggota;</li>
                        <li>menjaga keutuhan dan kebersihan koleksi serta fasilitas;</li>
                        <li>mengembalikan koleksi tepat waktu;</li>
                        <li>mengganti koleksi yang hilang atau rusak;</li>
                        <li>menjaga ketertiban di ruang Perpustakaan sesuai Pasal 15.</li>
                    </ol>
                </li>
                <li>Perubahan data Anggota, antara lain alamat email, nomor telepon, atau unit kerja, disampaikan secara tertulis, termasuk melalui email, kepada Pengelola. Pengelola memverifikasi dan memperbarui data pada sistem Perpustakaan. Nomor anggota tidak berubah.</li>
            </ol>

            <div class="rf-doc-chapter">BAB IV. SIRKULASI</div>
            <div class="rf-doc-article">Pasal 10 (Peminjaman)</div>
            <ol class="rf-doc-list">
                <li>Peminjaman dilakukan dengan menunjukkan kartu anggota atau menyebutkan nomor anggota, dengan syarat keanggotaan masih berlaku.</li>
                <li>Setiap Anggota dapat meminjam paling banyak 2 (dua) eksemplar pada waktu yang sama.</li>
                <li>Lama peminjaman adalah 10 (sepuluh) hari kerja untuk setiap eksemplar, dihitung sejak tanggal peminjaman eksemplar tersebut (akhir pekan, cuti bersama, dan libur nasional tidak dihitung). Eksemplar yang dipinjam pada waktu berbeda memiliki tanggal jatuh tempo masing-masing. Koleksi dikembalikan paling lambat pada hari jatuh tempo.</li>
                <li>Peminjaman dicatat melalui sistem Perpustakaan.</li>
            </ol>

            <div class="rf-doc-article">Pasal 11 (Perpanjangan)</div>
            <ol class="rf-doc-list">
                <li>Peminjaman dapat diperpanjang 1 (satu) kali untuk 10 (sepuluh) hari kerja, dihitung sejak tanggal perpanjangan (akhir pekan, cuti bersama, dan libur nasional tidak dihitung).</li>
                <li>Perpanjangan diajukan paling lambat pada tanggal jatuh tempo.</li>
            </ol>

            <div class="rf-doc-article">Pasal 12 (Koleksi yang Tidak Dipinjamkan)</div>
            <ol class="rf-doc-list">
                <li>Koleksi tertentu hanya dapat dibaca di tempat dan tidak dipinjamkan, meliputi koleksi referensi, koleksi bernilai historis atau eksemplar tunggal, serta koleksi yang rapuh.</li>
                <li>Koleksi pada ayat (1) ditandai pada sistem Perpustakaan oleh Pengelola.</li>
                <li>Terbitan berkala (majalah, jurnal, dan laporan) serta bahan non-buku (CD, DVD, dan sejenisnya) hanya dapat dibaca di tempat dan tidak dipinjamkan, kecuali ditentukan lain oleh Pengelola.</li>
            </ol>

            <div class="rf-doc-article">Pasal 13 (Penutupan Sementara dan Pengembalian Serentak)</div>
            <ol class="rf-doc-list">
                <li>Untuk keperluan penataan, inventarisasi, atau stock opname koleksi, atau menjelang akhir tahun anggaran, Pengelola dapat menutup layanan sirkulasi sementara.</li>
                <li>Penutupan diumumkan kepada Anggota paling lambat 7 (tujuh) hari kalender sebelum penutupan.</li>
                <li>Anggota wajib mengembalikan seluruh koleksi yang dipinjam paling lambat pada tanggal yang ditetapkan dalam pengumuman, meskipun belum jatuh tempo.</li>
                <li>Selama penutupan, tidak ada peminjaman dan perpanjangan. Hari penutupan tidak dihitung sebagai hari keterlambatan.</li>
            </ol>

            <div class="rf-doc-chapter">BAB V. JAM LAYANAN DAN TATA TERTIB RUANG</div>
            <div class="rf-doc-article">Pasal 14 (Jam Layanan)</div>
            <ol class="rf-doc-list">
                <li>Perpustakaan melayani pada hari kerja, Senin sampai dengan Jumat, dengan jam layanan sebagai berikut:
                    <ol class="rf-doc-list-alpha">
                        <li>Senin sampai dengan Kamis: pukul 07.30 sampai dengan 16.00, dengan waktu istirahat pukul 12.00 sampai dengan 13.00;</li>
                        <li>Jumat: pukul 07.30 sampai dengan 16.30, dengan waktu istirahat pukul 12.00 sampai dengan 13.30.</li>
                    </ol>
                </li>
                <li>Perubahan jam layanan atau penutupan sementara karena kegiatan dan hari libur diumumkan oleh Pengelola kepada Anggota.</li>
            </ol>

            <div class="rf-doc-article">Pasal 15 (Tata Tertib Ruang)</div>
            <ol class="rf-doc-list">
                <li>Setiap orang yang berada di ruang Perpustakaan wajib:
                    <ol class="rf-doc-list-alpha">
                        <li>menjaga ketenangan dan mengatur ponsel dalam mode senyap;</li>
                        <li>menjaga kebersihan, keutuhan, dan kerapian koleksi, fasilitas, serta ruangan;</li>
                        <li>mengembalikan koleksi yang telah dibaca ke tempat yang ditentukan Pengelola, tanpa menatanya kembali di rak;</li>
                        <li>mencatatkan kepada Pengelola setiap koleksi yang dibawa keluar ruangan;</li>
                        <li>mengikuti arahan Pengelola.</li>
                    </ol>
                </li>
                <li>Di ruang Perpustakaan dilarang:
                    <ol class="rf-doc-list-alpha">
                        <li>makan dan minum;</li>
                        <li>merokok, termasuk rokok elektrik;</li>
                        <li>mencoret, melipat, menyobek, atau merusak koleksi;</li>
                        <li>membawa koleksi keluar ruangan tanpa pencatatan peminjaman.</li>
                    </ol>
                </li>
                <li>Pengelola dapat menegur dan meminta keluar ruangan orang yang melanggar. Pelanggaran yang diulangi setelah ditegur diperlakukan sebagai pelanggaran berat sebagaimana dimaksud dalam Pasal 18.</li>
            </ol>

            <div class="rf-doc-chapter">BAB VI. SANKSI</div>
            <div class="rf-doc-article">Pasal 16 (Keterlambatan)</div>
            <ol class="rf-doc-list">
                <li>Anggota yang terlambat mengembalikan koleksi dikenai penangguhan hak pinjam selama jumlah hari kerja keterlambatan, dihitung sejak koleksi dikembalikan.</li>
                <li>Koleksi yang belum dikembalikan lebih dari 30 (tiga puluh) hari kalender setelah tanggal jatuh tempo diperlakukan sebagai koleksi hilang dan berlaku Pasal 17. Batas waktu penggantian pada Pasal 17 ayat (3) dihitung sejak tanggal koleksi diperlakukan sebagai hilang.</li>
            </ol>

            <div class="rf-doc-article">Pasal 17 (Kehilangan dan Kerusakan)</div>
            <ol class="rf-doc-list">
                <li>Anggota yang menghilangkan koleksi wajib menggantinya dengan koleksi yang sama atau cetakan/edisi yang lebih baru dari koleksi yang hilang.</li>
                <li>Apabila koleksi sebagaimana dimaksud pada ayat (1) tidak tersedia, penggantian dilakukan dengan koleksi lain yang setara subjek dan nilainya, dengan persetujuan Pengelola.</li>
                <li>Penggantian dilakukan paling lambat 30 (tiga puluh) hari kalender sejak kehilangan dilaporkan.</li>
                <li>Koleksi yang mengalami rusak berat karena Anggota, sesuai kriteria dalam Lampiran II (Keterangan Kerusakan), diperlakukan sebagai koleksi hilang.</li>
                <li>Selama penggantian belum dilakukan, hak pinjam Anggota ditangguhkan.</li>
            </ol>

            <div class="rf-doc-article">Pasal 18 (Pelanggaran Berat dan Pembekuan Keanggotaan)</div>
            <ol class="rf-doc-list">
                <li>Pelanggaran berat meliputi:
                    <ol class="rf-doc-list-alpha">
                        <li>memindahtangankan kartu anggota;</li>
                        <li>merusak koleksi dengan sengaja atau mengambil koleksi tanpa pencatatan peminjaman;</li>
                        <li>tidak mengganti koleksi hilang atau rusak berat dalam batas waktu sebagaimana dimaksud dalam Pasal 17 ayat (3);</li>
                        <li>memberikan data palsu pada saat pendaftaran;</li>
                        <li>mengulangi pelanggaran tata tertib ruang setelah ditegur.</li>
                    </ol>
                </li>
                <li>Terhadap pelanggaran berat, Ketua Tim Kerja Informasi Publik dan Perpustakaan, atas usul Pengelola, dapat membekukan keanggotaan selama 30 (tiga puluh) hari kalender atau mencabut keanggotaan, dan Anggota diberi tahu melalui email.</li>
                <li>Pembekuan atau pencabutan keanggotaan tidak menghapus kewajiban Anggota untuk mengembalikan atau mengganti koleksi.</li>
                <li>Ketentuan ini tidak mengurangi penerapan peraturan perundang-undangan lain.</li>
            </ol>

            <div class="rf-doc-article">Pasal 19 (Keberatan)</div>
            <ol class="rf-doc-list">
                <li>Anggota yang dikenai sanksi dapat mengajukan keberatan secara tertulis, termasuk melalui email, kepada Ketua Tim Kerja Informasi Publik dan Perpustakaan paling lambat 5 (lima) hari kerja sejak sanksi diberitahukan.</li>
                <li>Ketua Tim memberikan tanggapan paling lambat 5 (lima) hari kerja sejak keberatan diterima.</li>
                <li>Selama keberatan diproses, sanksi tetap berlaku kecuali Ketua Tim menentukan lain.</li>
            </ol>

            <div class="rf-doc-chapter">BAB VII. PELINDUNGAN DATA PRIBADI</div>
            <div class="rf-doc-article">Pasal 20</div>
            <ol class="rf-doc-list">
                <li>Perpustakaan mengumpulkan data pribadi Anggota, termasuk NIP, hanya untuk verifikasi identitas, pengelolaan keanggotaan, dan layanan sirkulasi.</li>
                <li>Data pribadi hanya dapat diakses oleh Pengelola yang berwenang dan tidak dibagikan kepada pihak lain di luar tujuan pada ayat (1). Data pribadi tidak ditampilkan pada katalog daring (OPAC) maupun laporan, dan nomor identitas disamarkan pada laporan. NIP tidak dicantumkan pada kartu anggota.</li>
                <li>Foto atau pindaian kartu identitas hanya digunakan untuk verifikasi dan tidak disimpan setelah verifikasi selesai.</li>
                <li>Pasfoto Anggota hanya digunakan untuk penerbitan kartu anggota sesuai pilihan Anggota, tidak ditampilkan pada OPAC maupun laporan, dan diperlakukan sebagai data pribadi yang disimpan sesuai ayat (6).</li>
                <li>Anggota berhak mengetahui data yang disimpan, memperbaiki data yang tidak benar, dan meminta penghapusan data setelah keanggotaan berakhir dan seluruh kewajibannya diselesaikan.</li>
                <li>Data pribadi Anggota disimpan selama keanggotaan berlaku dan paling lama 1 (satu) bulan setelah keanggotaan berakhir serta seluruh kewajiban Anggota diselesaikan, kemudian dihapus atau dianonimkan.</li>
                <li>Bukti pendaftaran sebagaimana dimaksud dalam Pasal 4 ayat (5) tetap disimpan setelah masa simpan pada ayat (6) berakhir tanpa NIP dan data kontak pribadi, sehingga hanya memuat nomor anggota, tanggal pengesahan, jenis anggota, dan hasil verifikasi, untuk keperluan pelaporan dan audit.</li>
                <li>Pengelola menjaga keamanan data, termasuk membatasi akses sistem dan mengamankan cadangan (backup) data.</li>
            </ol>

            <div class="rf-doc-chapter">BAB VIII. PENANGGUNG JAWAB</div>
            <div class="rf-doc-article">Pasal 21</div>
            <ol class="rf-doc-list">
                <li>Pengelola bertanggung jawab atas pendaftaran, verifikasi, sirkulasi, pencatatan, dan penyimpanan bukti keanggotaan.</li>
                <li>Pengelola menyusun rekapitulasi keanggotaan dan sirkulasi secara berkala sesuai ketentuan SOP Perpustakaan yang berlaku, dan melaporkannya kepada pimpinan.</li>
                <li>Ketua Tim Kerja Informasi Publik dan Perpustakaan mengawasi pelaksanaan tata tertib ini.</li>
            </ol>

            <div class="rf-doc-chapter">BAB IX. PENUTUP</div>
            <div class="rf-doc-article">Pasal 22</div>
            <ol class="rf-doc-list">
                <li>Hal yang belum diatur dalam tata tertib ini diatur lebih lanjut oleh Pengelola dengan persetujuan Ketua Tim Kerja Informasi Publik dan Perpustakaan.</li>
                <li>Tata tertib ini berlaku sejak tanggal ditetapkan.</li>
            </ol>

            <!-- LAMPIRAN -->
            <div class="rf-doc-annex">
                <div class="rf-doc-chapter" style="border-bottom: 2px solid var(--navy-900);">LAMPIRAN I: ALUR PELAKSANAAN</div>

                <div class="rf-doc-article">A. Pendaftaran</div>
                <ol class="rf-doc-list">
                    <li>Pendaftar mengisi formulir online, termasuk pilihan mencantumkan atau tidak mencantumkan pasfoto pada kartu anggota, dan mencentang persetujuan tata tertib.</li>
                    <li>Pengelola memeriksa kartu identitas pendaftar (ID card pegawai) secara langsung atau lewat foto/pindaian, lalu mencocokkan dengan data yang diisi. Foto atau pindaian kartu identitas tidak disimpan setelah dicocokkan.</li>
                    <li>Jika data sesuai, Pengelola mengonfirmasi keanggotaan di sistem. Nomor anggota terbit otomatis saat konfirmasi. Pengelola memilih jenis anggota, mengisi tanggal kedaluwarsa, dan mencatat hasil verifikasi (petugas dan tanggal). Jika data belum sesuai atau pendaftar tidak memenuhi syarat, Pengelola menolak pendaftaran dengan memilih alasan penolakan pada sistem, dan pemberitahuan penolakan beserta alasannya dikirim lewat email. Pendaftar dapat mengirim ulang pendaftaran.</li>
                    <li>Kartu digital dikirim lewat email. Kartu memuat pasfoto hanya jika Anggota memilihnya pada formulir.</li>
                    <li>Kartu cetak, jika sudah jadi, diserahkan dengan daftar serah terima bertanda tangan.</li>
                    <li>Tanda terima kartu: satu daftar per batch, kolom nama, no anggota, tanda tangan.</li>
                    <li>Pengelola menyimpan formulir, catatan verifikasi, dan catatan pengiriman email sebagai bukti pendaftaran.</li>
                </ol>

                <div class="rf-doc-article">B. Peminjaman</div>
                <ol class="rf-doc-list">
                    <li>Anggota menunjukkan kartu atau menyebutkan nomor anggota.</li>
                    <li>Pengelola memeriksa di sistem: keanggotaan masih aktif, pinjaman belum 2 buku, dan hak pinjam tidak sedang ditangguhkan.</li>
                    <li>Pengelola mencatat peminjaman. Jatuh tempo setiap eksemplar adalah 10 hari kerja dari hari peminjaman eksemplar tersebut.</li>
                </ol>

                <div class="rf-doc-article">C. Pengembalian dan Perpanjangan</div>
                <ol class="rf-doc-list">
                    <li>Saat buku kembali, Pengelola mencatat pengembaliannya di sistem.</li>
                    <li>Jika terlambat, hak pinjam ditangguhkan selama jumlah hari kerja keterlambatan, terhitung sejak koleksi dikembalikan.</li>
                    <li>Perpanjangan hanya bisa 1 kali, paling lambat pada tanggal jatuh tempo, untuk 10 hari kerja sejak tanggal perpanjangan.</li>
                </ol>

                <div class="rf-doc-article">D. Perpanjangan Keanggotaan (Pegawai, Tiap Tahun)</div>
                <ol class="rf-doc-list">
                    <li>Sekitar 30 hari sebelum kedaluwarsa, Pengelola mengirim pengingat lewat email.</li>
                    <li>Anggota mengonfirmasi bahwa datanya masih benar (masih pegawai, kontak masih aktif).</li>
                    <li>Pengelola memperbarui tanggal kedaluwarsa. Nomor dan kartu tetap sama.</li>
                </ol>

                <div class="rf-doc-article">E. Berakhirnya Keanggotaan</div>
                <ol class="rf-doc-list">
                    <li>Anggota Pegawai yang pindah atau pensiun: keanggotaan berakhir saat tidak melakukan perpanjangan tahunan.</li>
                    <li>Anggota yang belum mengembalikan buku tetap harus menyelesaikannya meski keanggotaannya sudah berakhir.</li>
                </ol>

                <div class="rf-doc-article">F. Laporan</div>
                <ol class="rf-doc-list">
                    <li>Tiap bulan, Pengelola mengekspor rekap dari sistem sesuai ketentuan SOP Perpustakaan yang berlaku: anggota baru, jumlah per jenis, anggota aktif, dan yang kedaluwarsa. Rekap ini dilaporkan ke pimpinan sebagai bukti pendaftaran berjalan.</li>
                </ol>

            <div class="rf-scroll-note">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Gulir kotak teks ini hingga paling bawah untuk mengaktifkan tombol persetujuan di bawah.</span>
            </div>
            <div style="height: 40px;"></div>
            <p style="font-size:0.78rem;color:var(--text-muted);text-align:center;">Terima kasih atas kepatuhan Anda demi kenyamanan seluruh pemustaka Perpustakaan Kementerian Ketenagakerjaan.</p>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
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

    const selectedIdDigits = 18;

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

    // ─── Live Digit Counter & Formatting (Strict Limit) ───
    inputIdentityNo.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, selectedIdDigits);
        updateDigitCount();
        if (this.classList.contains('has-error')) this.classList.remove('has-error');
        if (this.value.length === 0) {
            this.classList.remove('has-error', 'is-valid');
            digitCount.className = 'rf-digit-badge';
            digitCount.textContent = '0 / ' + selectedIdDigits + ' digit';
            feedbackIdentityNo.style.display = 'none';
        } else if (this.value.length === selectedIdDigits) {
            validateIdentityField();
        } else {
            this.classList.remove('is-valid');
            feedbackIdentityNo.style.display = 'none';
        }
    });

    function updateDigitCount() {
        const len = inputIdentityNo.value.length;
        if (len === 0) {
            digitCount.className = 'rf-digit-badge';
            digitCount.textContent = '0 / ' + selectedIdDigits + ' digit';
        } else if (len === selectedIdDigits) {
            digitCount.className = 'rf-digit-badge is-valid';
            digitCount.textContent = selectedIdDigits + ' / ' + selectedIdDigits + ' digit ✓';
        } else {
            digitCount.className = 'rf-digit-badge is-error';
            digitCount.textContent = len + ' / ' + selectedIdDigits + ' digit ✕';
        }
    }

    // ─── Phone Formatting ───
    const phoneInput = document.getElementById('Phone');
    // ─── Custom Dropdown Unit Kerja (100% Responsive / No Overflow) ───
    // ─── Preloaded Sub-Units Data from Server ───
    const subUnitsData = <?= json_encode($sub_units_grouped ?? []) ?>;

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ─── Custom Dropdown Unit Kerja (100% Responsive / No Overflow) ───
    const customUnitKerja = document.getElementById('customUnitKerja');
    const unitKerjaTrigger = document.getElementById('unitKerjaTrigger');
    const unitKerjaValue = document.getElementById('unitKerjaValue');
    const unitKerjaOptions = document.querySelectorAll('#unitKerjaDropdown .rf-select-option');
    const nativeUnitSelect = document.getElementById('UnitKerja_id');
    const badgeUnitKerja = document.getElementById('badge_UnitKerja');
    const feedbackUnitKerja = document.getElementById('feedback_UnitKerja');

    // ─── Custom Dropdown Sub-Unit Kerja ───
    const wrapperSubUnitKerja = document.getElementById('wrapperSubUnitKerja');
    const customSubUnitKerja = document.getElementById('customSubUnitKerja');
    const subUnitKerjaTrigger = document.getElementById('subUnitKerjaTrigger');
    const subUnitKerjaValue = document.getElementById('subUnitKerjaValue');
    const subUnitKerjaDropdown = document.getElementById('subUnitKerjaDropdown');
    const nativeSubUnitSelect = document.getElementById('SubUnitKerja_id');
    const badgeSubUnitKerja = document.getElementById('badge_SubUnitKerja');
    const feedbackSubUnitKerja = document.getElementById('feedback_SubUnitKerja');

    function resetSubUnitSelection() {
        if (!nativeSubUnitSelect) return;
        nativeSubUnitSelect.value = '';
        nativeSubUnitSelect.innerHTML = '<option value="">Pilih Biro / Direktorat / Unit</option>';
        if (subUnitKerjaValue) {
            subUnitKerjaValue.textContent = 'Pilih Biro / Direktorat / Unit';
            subUnitKerjaValue.classList.add('is-placeholder');
        }
        if (subUnitKerjaTrigger) {
            subUnitKerjaTrigger.classList.remove('has-error', 'is-valid');
        }
        if (typeof clearFieldStatus === 'function') {
            clearFieldStatus(subUnitKerjaTrigger, badgeSubUnitKerja, feedbackSubUnitKerja);
        }
    }

    function updateSubUnitsForUnit(unitId) {
        resetSubUnitSelection();

        if (!unitId) {
            // Unit belum dipilih: dropdown bawah nonaktif, teks "Pilih unit kerja dahulu"
            if (wrapperSubUnitKerja) wrapperSubUnitKerja.style.display = 'block';
            if (customSubUnitKerja) customSubUnitKerja.classList.add('is-disabled');
            if (subUnitKerjaTrigger) {
                subUnitKerjaTrigger.classList.add('is-disabled');
                subUnitKerjaTrigger.disabled = true;
            }
            if (nativeSubUnitSelect) {
                nativeSubUnitSelect.disabled = true;
                nativeSubUnitSelect.required = false;
            }
            if (subUnitKerjaValue) {
                subUnitKerjaValue.textContent = 'Pilih unit kerja dahulu';
                subUnitKerjaValue.classList.add('is-placeholder');
            }
            if (subUnitKerjaDropdown) {
                subUnitKerjaDropdown.innerHTML = '<div class="rf-select-option is-placeholder" data-value="" role="option"><span class="rf-opt-text">Pilih unit kerja dahulu</span></div>';
            }
            return;
        }

        const subs = subUnitsData[unitId] || [];
        if (subs.length === 0) {
            // Unit tanpa sub-unit (nomor 1): disembunyikan dan tidak wajib
            if (wrapperSubUnitKerja) wrapperSubUnitKerja.style.display = 'none';
            if (customSubUnitKerja) customSubUnitKerja.classList.add('is-disabled');
            if (subUnitKerjaTrigger) {
                subUnitKerjaTrigger.classList.add('is-disabled');
                subUnitKerjaTrigger.disabled = true;
            }
            if (nativeSubUnitSelect) {
                nativeSubUnitSelect.disabled = true;
                nativeSubUnitSelect.required = false;
                nativeSubUnitSelect.value = '';
            }
        } else {
            // Unit yang punya sub-unit: ditampilkan, aktif, dan WAJIB dipilih
            if (wrapperSubUnitKerja) wrapperSubUnitKerja.style.display = 'block';
            if (customSubUnitKerja) customSubUnitKerja.classList.remove('is-disabled');
            if (subUnitKerjaTrigger) {
                subUnitKerjaTrigger.classList.remove('is-disabled');
                subUnitKerjaTrigger.disabled = false;
            }
            if (nativeSubUnitSelect) {
                nativeSubUnitSelect.disabled = false;
                nativeSubUnitSelect.required = true;
            }

            // Build options
            let optionsHtml = '<div class="rf-select-option is-placeholder" data-value="" role="option"><span class="rf-opt-text">Pilih Biro / Direktorat / Unit</span></div>';
            let selectHtml = '<option value="">Pilih Biro / Direktorat / Unit</option>';

            subs.forEach(item => {
                optionsHtml += `
                    <div class="rf-select-option" data-value="${item.id}" role="option">
                        <span class="rf-opt-text">${escapeHtml(item.nama_subunit)}</span>
                        <svg class="rf-opt-check" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3.5 8.5l3 3 6-7"/>
                        </svg>
                    </div>
                `;
                selectHtml += `<option value="${item.id}">${escapeHtml(item.nama_subunit)}</option>`;
            });

            if (subUnitKerjaDropdown) subUnitKerjaDropdown.innerHTML = optionsHtml;
            if (nativeSubUnitSelect) nativeSubUnitSelect.innerHTML = selectHtml;

            // Bind click events on sub-unit options
            const subOptions = subUnitKerjaDropdown.querySelectorAll('.rf-select-option');
            subOptions.forEach(opt => {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const val = this.dataset.value;
                    const text = this.querySelector('.rf-opt-text') ? this.querySelector('.rf-opt-text').textContent.trim() : this.textContent.trim();

                    nativeSubUnitSelect.value = val;
                    nativeSubUnitSelect.dispatchEvent(new Event('change', { bubbles: true }));

                    subOptions.forEach(o => o.classList.remove('is-selected'));
                    if (val) {
                        this.classList.add('is-selected');
                        subUnitKerjaValue.textContent = text;
                        subUnitKerjaValue.classList.remove('is-placeholder');
                        validateSubUnitKerja();
                    } else {
                        subUnitKerjaValue.textContent = 'Pilih Biro / Direktorat / Unit';
                        subUnitKerjaValue.classList.add('is-placeholder');
                        clearFieldStatus(subUnitKerjaTrigger, badgeSubUnitKerja, feedbackSubUnitKerja);
                    }

                    subUnitKerjaTrigger.classList.remove('has-error');
                    customSubUnitKerja.classList.remove('is-open');
                    subUnitKerjaTrigger.setAttribute('aria-expanded', 'false');
                });
            });
        }
    }

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

                // Chaining: Update and reset sub-units
                updateSubUnitsForUnit(val);
                validateUnitKerja();
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

    if (customSubUnitKerja && subUnitKerjaTrigger) {
        subUnitKerjaTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (subUnitKerjaTrigger.disabled || customSubUnitKerja.classList.contains('is-disabled')) {
                return;
            }
            const isOpen = customSubUnitKerja.classList.contains('is-open');
            customSubUnitKerja.classList.toggle('is-open', !isOpen);
            subUnitKerjaTrigger.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function(e) {
            if (!customSubUnitKerja.contains(e.target)) {
                customSubUnitKerja.classList.remove('is-open');
                subUnitKerjaTrigger.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && customSubUnitKerja.classList.contains('is-open')) {
                customSubUnitKerja.classList.remove('is-open');
                subUnitKerjaTrigger.setAttribute('aria-expanded', 'false');
                subUnitKerjaTrigger.focus();
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
                updateSubUnitsForUnit('');
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
            resetPhoto();
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

    // ─── Crop Foto ───
    // Yang dikirim ke server hanya hasil crop (croppedPhoto), bukan file asli dari input
    const modalCrop = document.getElementById('modalCrop');
    const cropImage = document.getElementById('cropImage');
    const btnPakaiCrop = document.getElementById('btnPakaiCrop');
    const photoRecropBtn = document.getElementById('photoRecropBtn');
    let cropper = null;
    let originalPhotoUrl = null;
    let croppedPhoto = null;

    function handlePhotoFile(file) {
        // File asli tidak ikut terkirim lewat FormData(form)
        photoFileInput.value = '';

        const allowed = ['image/jpeg', 'image/png'];
        if (!allowed.includes(file.type)) {
            alert('Format foto harus berupa JPG atau PNG.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file foto maksimal 2 MB.');
            return;
        }

        if (originalPhotoUrl) URL.revokeObjectURL(originalPhotoUrl);
        originalPhotoUrl = URL.createObjectURL(file);
        openCrop();
    }

    function openCrop() {
        cropImage.src = originalPhotoUrl;
        modalCrop.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImage, {
            aspectRatio: 2 / 3,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.9,
            background: false,
        });
        btnPakaiCrop.focus();
    }

    function closeCrop() {
        modalCrop.style.display = 'none';
        document.body.style.overflow = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    // 400 x 600 px cukup untuk kartu digital maupun cetak 300 dpi
    btnPakaiCrop.addEventListener('click', function() {
        const canvas = cropper.getCroppedCanvas({ width: 400, height: 600, fillColor: '#ffffff', imageSmoothingQuality: 'high' });
        canvas.toBlob(function(blob) {
            croppedPhoto = blob;
            if (photoPreview.src.startsWith('blob:')) URL.revokeObjectURL(photoPreview.src);
            photoPreview.src = URL.createObjectURL(blob);
            photoDropzone.style.display = 'none';
            photoPreviewContainer.style.display = 'flex';
            closeCrop();
        }, 'image/jpeg', 0.85);
    });

    // Batal tanpa foto sebelumnya berarti tidak ada foto; kalau sudah ada, foto lama tetap dipakai
    function cancelCrop() {
        closeCrop();
        if (!croppedPhoto) resetPhoto();
    }

    document.getElementById('btnBatalCrop').addEventListener('click', cancelCrop);
    document.getElementById('closeCrop').addEventListener('click', cancelCrop);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalCrop.style.display === 'flex') cancelCrop();
    });

    photoRecropBtn.addEventListener('click', openCrop);

    function resetPhoto() {
        photoFileInput.value = '';
        croppedPhoto = null;
        if (photoPreview.src.startsWith('blob:')) URL.revokeObjectURL(photoPreview.src);
        photoPreview.removeAttribute('src');
        photoPreviewContainer.style.display = 'none';
        photoDropzone.style.display = 'block';
    }

    photoRemoveBtn.addEventListener('click', resetPhoto);

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
        updateSubmitButtonState();
        closeModal();
        validatePersetujuan();
    });

    cbPersetujuan.addEventListener('change', function() {
        updateSubmitButtonState();
    });

    // Elements for Validation
    const fullnameInput = document.getElementById('Fullname');
    const badgeFullname = document.getElementById('badge_Fullname');
    const feedbackFullname = document.getElementById('feedback_Fullname');

    const sexRadios = document.querySelectorAll('input[name="Sex_id"]');
    const sexGroup = document.getElementById('sexRadioGroup');
    const badgeSex = document.getElementById('badge_Sex');
    const feedbackSex = document.getElementById('feedback_Sex');

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
            // Sesuai butir 2: Hilangkan warna hijau pada petunjuk di bawah kolom setelah diisi dengan benar.
            // Warna hijau dan centang di bagian atas kolom tetap.
            feedback.className = 'rf-field-feedback';
            feedback.style.display = 'none';
            feedback.innerHTML = '';
        }
    }

    // Menghapus seluruh status hijau/merah jika input kosong atau dibersihkan
    function clearFieldStatus(field, badge, feedback) {
        if (field) {
            field.classList.remove('has-error', 'is-valid');
        }
        if (badge) {
            badge.className = 'rf-badge-status';
            badge.style.display = 'none';
            badge.textContent = '';
        }
        if (feedback) {
            feedback.className = 'rf-field-feedback';
            feedback.style.display = 'none';
            feedback.innerHTML = '';
        }
    }

    let isPhoneDuplicate = false;
    let isEmailDuplicate = false;

    function updateSubmitButtonState() {
        if (!cbPersetujuan || !cbPersetujuan.checked || isPhoneDuplicate || isEmailDuplicate) {
            btnSubmit.disabled = true;
        } else {
            btnSubmit.disabled = false;
        }
    }

    // ─── Form Reset Handler (Bersihkan Formulir Secara Menyeluruh) ───
    const resetBtnEl = document.getElementById('btnReset');
    if (resetBtnEl) {
        resetBtnEl.addEventListener('click', function() {
            setTimeout(function() {

                inputIdentityNo.value = '';
                inputIdentityNo.classList.remove('has-error', 'is-valid');
                digitCount.className = 'rf-digit-badge';
                digitCount.textContent = '0 / ' + selectedIdDigits + ' digit';
                feedbackIdentityNo.className = 'rf-field-feedback';
                feedbackIdentityNo.style.display = 'none';
                feedbackIdentityNo.innerHTML = '';

                if (unitKerjaValue) {
                    unitKerjaValue.textContent = 'Pilih Satuan Kerja / Unit Eselon I';
                    unitKerjaValue.classList.add('is-placeholder');
                }
                unitKerjaOptions.forEach(o => o.classList.remove('is-selected'));
                if (unitKerjaTrigger) unitKerjaTrigger.classList.remove('has-error', 'is-valid');

                clearFieldStatus(fullnameInput, badgeFullname, feedbackFullname);
                sexRadios.forEach(r => { r.checked = false; });
                clearFieldStatus(sexGroup, badgeSex, feedbackSex);
                clearFieldStatus(addressInput, badgeAddress, feedbackAddress);
                clearFieldStatus(emailInput, badgeEmail, feedbackEmail);
                clearFieldStatus(phoneInput, badgePhone, feedbackPhone);
                clearFieldStatus(unitKerjaTrigger, badgeUnitKerja, feedbackUnitKerja);

                cbPersetujuan.checked = false;
                cbPersetujuan.disabled = false;
                agreeBox.classList.remove('has-error', 'is-valid');
                persetujuanFeedback.className = 'rf-field-feedback';
                persetujuanFeedback.style.display = 'none';
                persetujuanFeedback.innerHTML = '';


                isPhoneDuplicate = false;
                isEmailDuplicate = false;
                btnSubmit.disabled = true;
                wantPhotoNo.checked = true;
                syncPhotoToggle();

                form.querySelectorAll('.has-error, .is-valid').forEach(el => el.classList.remove('has-error', 'is-valid'));
                if (errorSummaryBox) errorSummaryBox.style.display = 'none';
                alertEl.className = 'rf-alert';
                alertEl.style.display = 'none';
                stepButtons.forEach(btn => btn.classList.remove('has-error'));
            }, 30);
        });
    }

    function validateIdentityField() {
        const val = inputIdentityNo.value.trim();
        const len = val.length;
        const needed = 18;

        if (len === 0) {
            digitCount.className = 'rf-digit-badge is-error';
            digitCount.textContent = '0 / ' + needed + ' digit ✕';
            inputIdentityNo.classList.add('has-error');
            inputIdentityNo.classList.remove('is-valid');
            feedbackIdentityNo.className = 'rf-field-feedback is-error';
            feedbackIdentityNo.style.display = 'flex';
            feedbackIdentityNo.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>NIP wajib diisi 18 digit resmi BKN.</span>
            `;
            return `<strong>NIP:</strong> NIP wajib diisi.`;
        } else if (len < needed) {
            const kurang = needed - len;
            digitCount.className = 'rf-digit-badge is-error';
            digitCount.textContent = `${len} / ${needed} digit ✕`;
            inputIdentityNo.classList.add('has-error');
            inputIdentityNo.classList.remove('is-valid');
            feedbackIdentityNo.className = 'rf-field-feedback is-error';
            feedbackIdentityNo.style.display = 'flex';
            feedbackIdentityNo.innerHTML = `
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                </svg>
                <span>NIP tidak lengkap. Wajib ${needed} digit angka (terdeteksi ${len} digit). Kurang ${kurang} digit.</span>
            `;
            return `<strong>NIP:</strong> Harus berjumlah tepat ${needed} digit angka (saat ini berisi ${len} digit).`;
        } else {
            digitCount.className = 'rf-digit-badge is-valid';
            digitCount.textContent = `${needed} / ${needed} digit ✓`;
            inputIdentityNo.classList.remove('has-error');
            inputIdentityNo.classList.add('is-valid');
            // Butir 2: Hilangkan warna hijau pada petunjuk di bawah kolom setelah diisi dengan benar
            feedbackIdentityNo.className = 'rf-field-feedback';
            feedbackIdentityNo.style.display = 'none';
            feedbackIdentityNo.innerHTML = '';
            return null;
        }
    }

    // Pola sama dengan aturan nama_orang di server (App\Validation\NamaRules)
    const polaNama = new RegExp('^(?:' + <?= json_encode(\App\Validation\NamaRules::POLA) ?> + ')$', 'u');

    function validateFullname() {
        const val = fullnameInput.value.trim();
        if (val.length === 0) {
            clearFieldStatus(fullnameInput, badgeFullname, feedbackFullname);
            setFieldError(fullnameInput, badgeFullname, feedbackFullname, 'Wajib Diisi', 'Nama lengkap wajib diisi minimal 3 karakter sesuai KTP / SK Kedinasan.');
            return '<strong>Nama Lengkap:</strong> Wajib diisi minimal 3 karakter sesuai identitas.';
        } else if (val.length < 3) {
            setFieldError(fullnameInput, badgeFullname, feedbackFullname, 'Minimal 3 Karakter', 'Nama lengkap minimal 3 karakter sesuai identitas.');
            return '<strong>Nama Lengkap:</strong> Wajib diisi minimal 3 karakter sesuai identitas.';
        } else if (!polaNama.test(val)) {
            setFieldError(fullnameInput, badgeFullname, feedbackFullname, 'Format Tidak Valid', 'Nama hanya boleh berisi huruf A-Z, spasi, titik, koma, apostrof, dan tanda hubung.');
            return '<strong>Nama Lengkap:</strong> Hanya boleh berisi huruf A-Z, spasi, titik, koma, apostrof, dan tanda hubung.';
        } else {
            setFieldValid(fullnameInput, badgeFullname, feedbackFullname, 'Sesuai format nama sipil resmi.');
            return null;
        }
    }

    function validateJenisKelamin() {
        const checked = document.querySelector('input[name="Sex_id"]:checked');
        if (!checked) {
            setFieldError(sexGroup, badgeSex, feedbackSex, 'Belum Dipilih', 'Jenis kelamin wajib dipilih.');
            return '<strong>Jenis Kelamin:</strong> Jenis kelamin wajib dipilih.';
        } else {
            const label = checked.getAttribute('data-label') || 'Terpilih';
            setFieldValid(sexGroup, badgeSex, feedbackSex, 'Jenis kelamin: ' + label + '.');
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

    function validateSubUnitKerja() {
        if (!wrapperSubUnitKerja || wrapperSubUnitKerja.style.display === 'none' || (nativeSubUnitSelect && nativeSubUnitSelect.disabled)) {
            return null; // Tidak wajib untuk Unit 1 / unit tanpa sub-unit
        }
        if (!nativeSubUnitSelect || !nativeSubUnitSelect.value) {
            setFieldError(subUnitKerjaTrigger, badgeSubUnitKerja, feedbackSubUnitKerja, 'Belum Dipilih', 'Biro / Direktorat / Unit (Eselon II) wajib dipilih.');
            return '<strong>Biro / Direktorat / Unit (Eselon II):</strong> Satuan kerja penempatan spesifik wajib dipilih.';
        } else {
            setFieldValid(subUnitKerjaTrigger, badgeSubUnitKerja, feedbackSubUnitKerja, 'Biro / Direktorat terdaftar.');
            return null;
        }
    }

    function validateAddress() {
        const val = addressInput.value.trim();
        if (val.length === 0) {
            clearFieldStatus(addressInput, badgeAddress, feedbackAddress);
            setFieldError(addressInput, badgeAddress, feedbackAddress, 'Wajib Diisi', 'Alamat domisili saat ini wajib diisi lengkap untuk pengiriman berkas atau kartu anggota.');
            return '<strong>Alamat Domisili:</strong> Wajib diisi alamat domisili lengkap.';
        } else if (val.length < 5) {
            setFieldError(addressInput, badgeAddress, feedbackAddress, 'Kurang Lengkap', 'Alamat domisili saat ini wajib diisi lengkap.');
            return '<strong>Alamat Domisili:</strong> Wajib diisi alamat domisili lengkap.';
        } else {
            setFieldValid(addressInput, badgeAddress, feedbackAddress, 'Alamat domisili tercatat.');
            return null;
        }
    }

    function validateEmail() {
        const val = emailInput.value.trim();
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (val.length === 0) {
            clearFieldStatus(emailInput, badgeEmail, feedbackEmail);
            setFieldError(emailInput, badgeEmail, feedbackEmail, 'Wajib Diisi', 'Format email tidak valid. Masukkan surel aktif (contoh: nama@kemnaker.go.id).');
            return '<strong>Alamat Email:</strong> Wajib diisi surel aktif.';
        } else if (!re.test(val)) {
            setFieldError(emailInput, badgeEmail, feedbackEmail, 'Email Tidak Valid', 'Format email tidak valid. Masukkan surel aktif (contoh: nama@kemnaker.go.id).');
            return '<strong>Alamat Email:</strong> Format surel tidak valid (harus email aktif).';
        } else {
            setFieldValid(emailInput, badgeEmail, feedbackEmail, 'Format email valid untuk aktivasi akun.');
            return null;
        }
    }

    function validatePhone() {
        const raw = phoneInput.value.replace(/\D/g, '');
        if (raw.length === 0) {
            clearFieldStatus(phoneInput, badgePhone, feedbackPhone);
            setFieldError(phoneInput, badgePhone, feedbackPhone, 'Wajib Diisi', 'Nomor handphone wajib diisi.');
            return '<strong>Kontak Pemohon:</strong> Nomor handphone wajib diisi.';
        } else if (raw.length < 9 || raw.length > 13) {
            setFieldError(phoneInput, badgePhone, feedbackPhone, 'Format Nomor Tidak Valid', 'Nomor harus nomor handphone yang valid (diawali 08 atau 62, 9–13 digit).');
            return '<strong>Kontak Pemohon:</strong> Format nomor handphone tidak valid (harus diawali 08 atau 62).';
        } else {
            setFieldValid(phoneInput, badgePhone, feedbackPhone, 'Format nomor handphone valid.');
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

    // ─── Real-time Input Listeners ───
    // Bila pengguna mengosongkan / menghapus isi input, status hijau (is-valid) seketika dihilangkan
    fullnameInput.addEventListener('input', function() {
        const val = this.value.trim();
        if (val.length === 0) {
            clearFieldStatus(fullnameInput, badgeFullname, feedbackFullname);
        } else if (val.length >= 3) {
            validateFullname();
        } else {
            clearFieldStatus(fullnameInput, badgeFullname, feedbackFullname);
        }
    });

    sexRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            validateJenisKelamin();
        });
    });

    addressInput.addEventListener('input', function() {
        const val = this.value.trim();
        if (val.length === 0) {
            clearFieldStatus(addressInput, badgeAddress, feedbackAddress);
        } else if (val.length >= 5) {
            validateAddress();
        } else {
            clearFieldStatus(addressInput, badgeAddress, feedbackAddress);
        }
    });

    emailInput.addEventListener('input', function() {
        const val = this.value.trim();
        isEmailDuplicate = false;
        if (val.length === 0) {
            clearFieldStatus(emailInput, badgeEmail, feedbackEmail);
        } else {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (re.test(val)) {
                validateEmail();
            } else {
                clearFieldStatus(emailInput, badgeEmail, feedbackEmail);
            }
        }
        updateSubmitButtonState();
    });

    emailInput.addEventListener('blur', function() {
        const val = this.value.trim();
        if (val.length === 0) return;
        const err = validateEmail();
        if (err) return;

        const formData = new FormData();
        formData.append('field', 'Email');
        formData.append('value', val);

        axios.post('<?= base_url('registrasi/check-duplicate') ?>', formData, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            const res = response.data;
            if (res && res.registered) {
                isEmailDuplicate = true;
                setFieldError(emailInput, badgeEmail, feedbackEmail, 'Sudah Terdaftar', res.message || 'Email sudah terdaftar sebelumnya.');
                updateSubmitButtonState();
            } else {
                isEmailDuplicate = false;
                setFieldValid(emailInput, badgeEmail, feedbackEmail, 'Format email valid untuk aktivasi akun.');
                updateSubmitButtonState();
            }
        })
        .catch(() => {});
    });

    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
        if (this.classList.contains('has-error')) this.classList.remove('has-error');
        isPhoneDuplicate = false;
        const raw = this.value;
        if (raw.length === 0) {
            clearFieldStatus(phoneInput, badgePhone, feedbackPhone);
        } else if (raw.length >= 9 && raw.length <= 13) {
            validatePhone();
        } else {
            clearFieldStatus(phoneInput, badgePhone, feedbackPhone);
        }
        updateSubmitButtonState();
    });

    phoneInput.addEventListener('blur', function() {
        const raw = this.value.replace(/\D/g, '');
        if (raw.length === 0) return;
        const err = validatePhone();
        if (err) return;

        let checkVal = raw;
        if (checkVal.startsWith('62')) {
            checkVal = checkVal.slice(2);
        }
        if (checkVal.startsWith('0')) {
            checkVal = checkVal.slice(1);
        }
        checkVal = '0' + checkVal;

        const formData = new FormData();
        formData.append('field', 'Phone');
        formData.append('value', checkVal);

        axios.post('<?= base_url('registrasi/check-duplicate') ?>', formData, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            const res = response.data;
            if (res && res.registered) {
                isPhoneDuplicate = true;
                setFieldError(phoneInput, badgePhone, feedbackPhone, 'Sudah Terdaftar', res.message || 'Nomor handphone ini sudah terdaftar.');
                updateSubmitButtonState();
            } else {
                isPhoneDuplicate = false;
                setFieldValid(phoneInput, badgePhone, feedbackPhone, 'Format nomor handphone valid.');
                updateSubmitButtonState();
            }
        })
        .catch(() => {});
    });

    cbPersetujuan.addEventListener('change', function() {
        validatePersetujuan();
    });

    // ─── Submit Handler (AJAX) ───
    btnSubmit.addEventListener('click', function() {
        const errorItems = [];

        // 1. Identity (NIP)
        const errId = validateIdentityField();
        if (errId) errorItems.push({ sec: 'section-1', step: 'section-1', msg: errId });

        // 2. Fullname
        const errName = validateFullname();
        if (errName) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errName });

        // 2b. Jenis Kelamin
        const errSex = validateJenisKelamin();
        if (errSex) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errSex });

        // 3. Unit Kerja
        const errUnit = validateUnitKerja();
        if (errUnit) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errUnit });

        // 3b. Sub-Unit Kerja
        const errSubUnit = validateSubUnitKerja();
        if (errSubUnit) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errSubUnit });

        // 4. Alamat Domisili
        const errAddr = validateAddress();
        if (errAddr) errorItems.push({ sec: 'section-2', step: 'section-2', msg: errAddr });

        // 5. Email
        const errEmail = validateEmail();
        if (errEmail) {
            errorItems.push({ sec: 'section-3', step: 'section-3', msg: errEmail });
        } else if (isEmailDuplicate) {
            errorItems.push({ sec: 'section-3', step: 'section-3', msg: '<strong>Alamat Email:</strong> Email sudah terdaftar sebelumnya.' });
        }

        // 6. Phone
        const errPhone = validatePhone();
        if (errPhone) {
            errorItems.push({ sec: 'section-3', step: 'section-3', msg: errPhone });
        } else if (isPhoneDuplicate) {
            errorItems.push({ sec: 'section-3', step: 'section-3', msg: '<strong>Kontak Pemohon:</strong> Nomor handphone ini sudah terdaftar.' });
        }

        // 7. Persetujuan
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
        let rawPhone = phoneInput.value.replace(/\D/g, '');
        if (rawPhone.startsWith('62')) rawPhone = rawPhone.slice(2);
        if (rawPhone.startsWith('0')) rawPhone = rawPhone.slice(1);
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

        if (croppedPhoto) {
            formData.set('PhotoFile', croppedPhoto, 'foto.jpg');
        } else {
            formData.delete('PhotoFile');
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
                let firstErrorEl = null;

                if (res && res.errors) {
                    for (const [key, value] of Object.entries(res.errors)) {
                        const field = document.getElementById(key);
                        if (field) {
                            field.classList.add('has-error');
                            field.classList.remove('is-valid');
                            if (!firstErrorEl) firstErrorEl = field;
                        }
                        if (key === 'Phone') {
                            isPhoneDuplicate = true;
                            setFieldError(phoneInput, badgePhone, feedbackPhone, 'Sudah Terdaftar', value);
                            if (!firstErrorEl) firstErrorEl = phoneInput;
                        }
                        if (key === 'Email') {
                            isEmailDuplicate = true;
                            setFieldError(emailInput, badgeEmail, feedbackEmail, 'Sudah Terdaftar', value);
                            if (!firstErrorEl) firstErrorEl = emailInput;
                        }
                        if (key === 'IdentityNo') {
                            digitCount.className = 'rf-digit-badge is-error';
                            digitCount.textContent = 'Sudah Terdaftar';
                            inputIdentityNo.classList.add('has-error');
                            inputIdentityNo.classList.remove('is-valid');
                            feedbackIdentityNo.className = 'rf-field-feedback is-error';
                            feedbackIdentityNo.style.display = 'flex';
                            feedbackIdentityNo.innerHTML = `
                                <svg viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                                </svg>
                                <span>${value}</span>
                            `;
                            if (!firstErrorEl) firstErrorEl = inputIdentityNo;
                        }
                        if (key === 'UnitKerja_id') {
                            const ut = document.getElementById('unitKerjaTrigger');
                            if (ut) {
                                ut.classList.add('has-error');
                                if (!firstErrorEl) firstErrorEl = ut;
                            }
                        }
                        if (key === 'SubUnitKerja_id') {
                            const sut = document.getElementById('subUnitKerjaTrigger');
                            if (sut) {
                                sut.classList.add('has-error');
                                if (!firstErrorEl) firstErrorEl = sut;
                            }
                        }
                        if (key === 'Sex_id') {
                            if (sexGroup) {
                                sexGroup.classList.add('has-error');
                                if (!firstErrorEl) firstErrorEl = sexGroup;
                            }
                            setFieldError(sexGroup, badgeSex, feedbackSex, 'Belum Dipilih', value);
                        }
                    }
                } else if (res && res.message) {
                    if (res.message.includes('WhatsApp') || res.message.includes('telepon') || res.message.includes('handphone')) {
                        isPhoneDuplicate = true;
                        setFieldError(phoneInput, badgePhone, feedbackPhone, 'Sudah Terdaftar', res.message);
                        firstErrorEl = phoneInput;
                    } else if (res.message.includes('Email')) {
                        isEmailDuplicate = true;
                        setFieldError(emailInput, badgeEmail, feedbackEmail, 'Sudah Terdaftar', res.message);
                        firstErrorEl = emailInput;
                    } else if (res.message.includes('Identitas') || res.message.includes('NIP')) {
                        digitCount.className = 'rf-digit-badge is-error';
                        digitCount.textContent = 'Sudah Terdaftar';
                        inputIdentityNo.classList.add('has-error');
                        inputIdentityNo.classList.remove('is-valid');
                        feedbackIdentityNo.className = 'rf-field-feedback is-error';
                        feedbackIdentityNo.style.display = 'flex';
                        feedbackIdentityNo.innerHTML = `
                            <svg viewBox="0 0 16 16" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 15A7 7 0 108 1a7 7 0 000 14zm.75-10.25a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5zm0 2.5a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0V7.25z" clip-rule="evenodd"/>
                            </svg>
                            <span>${res.message}</span>
                        `;
                        firstErrorEl = inputIdentityNo;
                    }
                }

                if (firstErrorEl) {
                    firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (firstErrorEl === sexGroup && sexRadios.length > 0) {
                        sexRadios[0].focus();
                    } else if (typeof firstErrorEl.focus === 'function') {
                        firstErrorEl.focus();
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRegistrasi');
    if (!form) return;

    const el = (id) => document.getElementById(id);
    const nip = el('IdentityNo'), nama = el('Fullname'), unit = el('UnitKerja_id'), sub = el('SubUnitKerja_id');
    const alamat = el('AddressNow'), email = el('Email'), hp = el('Phone'), setuju = el('Persetujuan');
    const wrapSub = el('wrapperSubUnitKerja');
    const progress = el('rfProgress'), progressText = el('rfProgressText');
    const mcName = el('mcName'), mcNama = el('mcNama'), mcPhotoImg = el('mcPhotoImg'), mcPhotoQ = el('mcPhotoQ');
    const photoPreview = el('photoPreview');
    const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function sisaIsian() {
        const subLengkap = unit.value
            ? (wrapSub.style.display === 'none' || sub.disabled || !!sub.value)
            : false;
        const hpDigit = hp.value.replace(/\D/g, '').length;
        const lengkap = [
            nip.value.length === 18,
            nama.value.trim().length >= 3,
            !!form.querySelector('input[name="Sex_id"]:checked'),
            !!unit.value,
            subLengkap,
            alamat.value.trim().length >= 5,
            reEmail.test(email.value.trim()),
            hpDigit >= 9 && hpDigit <= 13,
            setuju.checked,
        ];
        return lengkap.filter((ok) => !ok).length;
    }

    // Port namaKartuBaris() di app/Helpers/anggota_helper.php: maksimal 15 huruf per baris, 2 baris seimbang, lalu inisial dari kata terakhir.
    const BATAS_NAMA = 15;
    function pecahSeimbang(kata) {
        let terbaik = null;
        for (let k = 1; k < kata.length; k++) {
            const baris = [kata.slice(0, k).join(' '), kata.slice(k).join(' ')];
            const panjang = Math.max(baris[0].length, baris[1].length);
            if (terbaik === null || panjang < terbaik[0]) terbaik = [panjang, baris];
        }
        return terbaik[1];
    }
    function namaBaris(nama) {
        const kata = nama.trim().split(/\s+/).filter(Boolean);
        if (kata.length <= 1) return kata;
        for (let i = kata.length - 1; ; i--) {
            const satu = kata.join(' ');
            const baris = satu.length <= BATAS_NAMA ? [satu] : pecahSeimbang(kata);
            const muat = Math.max(...baris.map((b) => b.length)) <= BATAS_NAMA;
            if (muat || i < 1) return baris;
            kata[i] = kata[i].charAt(0) + '.';
        }
    }

    function perbarui() {
        const sisa = sisaIsian();
        progress.classList.toggle('is-done', sisa === 0);
        progressText.innerHTML = sisa === 0
            ? 'Semua isian wajib sudah lengkap'
            : '<strong>' + sisa + '</strong> isian wajib belum lengkap';

        const baris = namaBaris(nama.value);
        mcName.replaceChildren(...(baris.length ? baris : ['Nama Lengkap']).map((teks) => {
            const span = document.createElement('span');
            span.className = 'rf-mc-baris';
            span.textContent = teks;
            return span;
        }));
        mcNama.classList.toggle('is-dua', baris.length > 1);
    }

    function jadwalkan() {
        perbarui();
        setTimeout(perbarui, 0);
    }

    function sinkronFoto() {
        const src = photoPreview.getAttribute('src');
        if (src) {
            mcPhotoImg.src = src;
            mcPhotoImg.hidden = false;
            mcPhotoQ.hidden = true;
        } else {
            mcPhotoImg.removeAttribute('src');
            mcPhotoImg.hidden = true;
            mcPhotoQ.hidden = false;
        }
    }

    form.addEventListener('input', jadwalkan);
    form.addEventListener('change', jadwalkan);
    form.addEventListener('reset', function () { setTimeout(perbarui, 120); });
    el('btnReset').addEventListener('click', function () { setTimeout(perbarui, 120); });
    new MutationObserver(sinkronFoto).observe(photoPreview, { attributes: true, attributeFilter: ['src'] });

    perbarui();
    sinkronFoto();
});
</script>
<style>
@keyframes rfSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
<?= $this->endSection() ?>
