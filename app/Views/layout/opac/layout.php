<?php
$db = db_connect();
$nama_perpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?: "Perpustakaan Mitra";
$alamat = $db->table('settingparameters')->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value ?: "Jl.Perpustakaan Mitra";
$tentang_kami = $db->table('settingparameters')->where('Name', 'TentangKami')->get()->getRow()->Value ?: "Perpustakaan Mitra";
$logo = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow()->Value;
$phone = $db->table('settingparameters')->where('Name', 'Phone')->get()->getRow()->Value;
$email = $db->table('settingparameters')->where('Name', 'EmailPerpustakaan')->get()->getRow()->Value;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'OPAC - ' . $nama_perpustakaan; ?></title>

    <link rel="icon" href="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/logo-inlislite-icon.webp') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css') ?>/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-50: #eff6ff;
            --brand-100: #dbeafe;
            --brand-500: #3b82f6;
            --brand-600: #1b3878;
            --brand-700: #1d4ed8;
            --brand-900: #1e3a8a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-500: #64748b;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        body, input, select, textarea, button {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--slate-50);
            color: var(--slate-800);
        }

        /* ─── Global Utility ─── */
        .text-brand {
            color: var(--brand-600);
        }

        .bg-brand {
            background-color: var(--brand-600) !important;
            color: white;
        }

        .btn-brand {
            background-color: var(--brand-600);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-brand:hover {
            background-color: var(--brand-700);
            color: white;
            transform: translateY(-2px);
        }

        .rounded-xl {
            border-radius: 1rem !important;
        }

        .rounded-2xl {
            border-radius: 1.5rem !important;
        }

        .shadow-soft {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, .05),
                0 8px 10px -6px rgba(0, 0, 0, .01);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ─── Navbar ─── */
        .navbar-custom {
            background-color: var(--brand-600);
            border-bottom: none;
            padding: 12px 0;
            transition: all 0.3s;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon-custom {
            color: #ffffff !important;
            font-size: 1.5rem;
        }

        /* ✅ Brand area */
        .navbar-brand {
            max-width: 320px;
            flex-shrink: 1;
            min-width: 0;
            /* wajib agar clamp bekerja di flexbox */
        }

        .navbar-brand img {
            width: auto;
            /* ✅ lebar menyesuaikan */
            height: 52px;
            /* ✅ sesuaikan dengan tinggi 2 baris h1 + p */
            max-width: 52px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }

        /* ✅ Wrapper teks brand */
        .navbar-brand .brand-text {
            min-width: 0;
        }

        /* ✅ Nama perpustakaan: maksimal 2 baris, tidak dipotong dengan ellipsis */
        .navbar-brand h1 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: #ffffff;
            white-space: normal;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .navbar-brand p {
            font-size: 0.65rem;
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
            color: #e2e8f0;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        /* ✅ Nav link */
        /* ✅ Nav link — lebih besar */
        .nav-link {
            color: #f8fafc;
            font-weight: 500;
            font-size: 1rem;
            /* ✅ naik dari 0.875rem */
            margin: 0 6px;
            /* ✅ sedikit lebih lega */
            transition: 0.3s;
            white-space: nowrap;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #93c5fd;
        }

        /* ✅ Responsif */
        @media (max-width: 1200px) {
            .navbar-brand {
                max-width: 260px;
            }

            .navbar-brand h1 {
                font-size: 0.875rem;
            }

            .nav-link {
                font-size: 0.95rem;
                margin: 0 4px;
            }
        }

        @media (max-width: 992px) {
            .navbar-brand {
                max-width: 200px;
            }

            .navbar-brand h1 {
                font-size: 0.8rem;
            }

            .nav-link {
                font-size: 0.9rem;
                margin: 0 2px;
            }
        }

        /* ─── Footer ─── */
        .footer-custom {
            background-color: var(--slate-900);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
            border-top: 1px solid var(--slate-800);
        }

        .footer-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: var(--brand-500);
            padding-left: 5px;
        }

        .footer-contact li {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .footer-contact i {
            color: var(--brand-500);
            margin-top: 4px;
        }

        /* ─── Pagination Custom ─── */
        .pagination .page-link {
            color: #1b3878;
            border: 1.5px solid #dbeafe;
            background-color: #f8fafc;
            font-size: 0.9rem;
            min-width: 40px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: #1b3878;
            border-color: #1b3878;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(27, 56, 120, 0.25);
        }

        .pagination .page-item.active .page-link {
            background-color: #1b3878;
            border-color: #1b3878;
            color: white;
            box-shadow: 0 4px 12px rgba(27, 56, 120, 0.35);
        }

        .pagination .page-item.disabled .page-link {
            color: #cbd5e1;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        /* ─── SweetAlert2 Fix ─── */
        .swal2-icon.swal2-warning {
            border-color: #f8bb86 !important;
            color: #f8bb86 !important;
        }

        .swal2-styled.swal2-confirm {
            background-color: #3085d6 !important;
            color: #fff !important;
            border: none !important;
        }

        .swal2-styled.swal2-cancel {
            background-color: #d33 !important;
            color: #fff !important;
            border: none !important;
        }

        /* ─── Modern Book Card (OPAC / Browse / Home) ─── */
        .opac-modern-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px 18px 16px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }

        .opac-modern-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .opac-cover-stage {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 4px 10px 14px;
        }

        .opac-book-3d {
            position: relative;
            width: 100%;
            max-width: 215px;
            aspect-ratio: 3 / 4.15;
            border-radius: 4px 8px 8px 4px;
            box-shadow:
                -4px 0 8px rgba(0, 0, 0, 0.15),
                0 12px 24px -4px rgba(15, 23, 42, 0.35),
                inset 4px 0 8px rgba(255, 255, 255, 0.12);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .opac-modern-card:hover .opac-book-3d {
            transform: translateY(-3px) scale(1.015);
            box-shadow:
                -5px 2px 10px rgba(0, 0, 0, 0.18),
                0 16px 28px -4px rgba(15, 23, 42, 0.42),
                inset 4px 0 8px rgba(255, 255, 255, 0.15);
        }

        .opac-real-cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .opac-generated-cover {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 16px 15px 14px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
            z-index: 1;
            overflow: hidden;
        }

        /* Lipatan punggung buku (Book Spine Crease) */
        .opac-book-spine {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 14px;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0.18) 0%,
                rgba(0, 0, 0, 0.35) 45%,
                rgba(255, 255, 255, 0.12) 75%,
                transparent 100%
            );
            z-index: 2;
            pointer-events: none;
        }

        /* Ornamen garis geometris halus pada sampul */
        .opac-cover-geo-circle {
            position: absolute;
            top: -18px;
            right: -32px;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .opac-cover-geo-rect {
            position: absolute;
            bottom: 42px;
            left: 22px;
            width: 56px;
            height: 56px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .opac-cover-top {
            position: relative;
            z-index: 3;
            gap: 6px;
        }

        .opac-cover-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.56rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            padding: 3px 7px;
            border-radius: 4px;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 135px;
        }

        .opac-cover-year {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            flex-shrink: 0;
        }

        .opac-cover-middle {
            position: relative;
            z-index: 3;
            padding: 8px 0;
        }

        .opac-cover-title {
            font-family: 'Libre Baskerville', Georgia, serif;
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.35;
            color: #ffffff;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .opac-cover-author {
            font-size: 0.66rem;
            line-height: 1.35;
            color: rgba(255, 255, 255, 0.72);
            font-weight: 400;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .opac-cover-bottom {
            position: relative;
            z-index: 3;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 8px;
            gap: 6px;
        }

        .opac-cover-call {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem;
            color: rgba(255, 255, 255, 0.65);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .opac-cover-edition {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.6rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: right;
        }

        /* Status & Sirkulasi di bawah cover */
        .opac-status-pill {
            display: inline-flex;
            align-items: center;
            font-size: 0.74rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .opac-status-pill.status-available {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .opac-status-pill.status-borrowed {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .opac-eks-count {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.74rem;
            color: #64748b;
            font-weight: 500;
        }

        .opac-call-row {
            font-size: 0.76rem;
            color: #64748b;
        }

        .opac-call-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.73rem;
            font-weight: 600;
            color: #1e40af;
            background-color: #eff6ff;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .opac-shelf-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.73rem;
            color: #64748b;
            margin-left: 3px;
        }

        .opac-book-heading {
            font-family: 'Libre Baskerville', Georgia, serif;
            font-size: 0.96rem;
            font-weight: 700;
            line-height: 1.42;
            margin-top: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.72rem;
        }

        .opac-book-heading a {
            color: #0f172a;
            transition: color 0.2s ease;
        }

        .opac-book-heading a:hover {
            color: #1b3878;
        }

        .opac-book-author {
            font-size: 0.8rem;
            color: #475569;
            font-weight: 500;
        }

        .opac-book-submeta {
            font-size: 0.74rem;
            color: #94a3b8;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.05rem;
        }

        .opac-btn-detail {
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e293b;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.2s ease;
        }

        .opac-btn-detail:hover {
            background-color: #1b3878;
            border-color: #1b3878;
            color: #ffffff;
        }

        /* ─── Pop-Up Modal Deskripsi Bibliografis OPAC ─── */
        .opac-meta-modal .modal-dialog {
            max-width: 720px;
        }

        .opac-meta-modal .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.38);
            padding: 30px 34px 26px;
            background: #ffffff;
        }

        .opac-modal-kicker {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .opac-modal-title {
            font-family: 'Libre Baskerville', Georgia, serif;
            font-size: 1.32rem;
            font-weight: 700;
            line-height: 1.38;
            color: #0f172a;
            margin-bottom: 0;
        }

        .opac-modal-close-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .opac-modal-close-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .opac-modal-ddc-pill {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e293b;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .opac-modal-field-label {
            font-size: 0.74rem;
            color: #94a3b8;
            margin-bottom: 3px;
            font-weight: 500;
        }

        .opac-modal-field-value {
            font-size: 0.84rem;
            color: #1e293b;
            font-weight: 600;
            line-height: 1.4;
        }

        .opac-modal-field-value.shelf-highlight {
            font-family: 'JetBrains Mono', monospace;
            color: #1b3878;
            font-size: 0.8rem;
        }

        .opac-modal-section-heading {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .opac-modal-abstract {
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.62;
            margin-bottom: 12px;
        }

        .opac-modal-subjects {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.5;
        }

        .opac-modal-subjects a {
            color: #475569;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .opac-modal-subjects a:hover {
            color: #1b3878;
            text-decoration: underline;
        }

        .opac-modal-input {
            font-size: 0.84rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 14px;
            color: #1e293b;
            background-color: #f8fafc;
        }

        .opac-modal-input:focus {
            background-color: #ffffff;
            border-color: #1b3878;
            box-shadow: 0 0 0 3px rgba(27, 56, 120, 0.12);
            outline: none;
        }

        .opac-modal-btn-pinjam {
            background-color: #1b3878;
            color: #ffffff;
            font-size: 0.84rem;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 18px;
            border: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .opac-modal-btn-pinjam:hover {
            background-color: #152c5e;
            color: #ffffff;
            transform: translateY(-1px);
        }

        @media (max-width: 576px) {
            .opac-meta-modal .modal-content {
                padding: 20px;
            }
            .opac-modal-title {
                font-size: 1.1rem;
            }
        }
    </style>

    <?= $this->renderSection('style') ?>
</head>

<body>
    <?= $this->include('App\Views\layout\partial\loader'); ?>

    <!-- ─── Navbar ─── -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-custom shadow-sm">
        <div class="container">

            <!-- ✅ Brand: gap-2 lebih rapat, title tooltip untuk nama panjang -->
            <a class="navbar-brand d-flex align-items-center gap-2"
                href="<?= base_url('home') ?>"
                title="<?= esc($nama_perpustakaan) ?>">
                <img src="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/default-perpus.png') ?>"
                    alt="Logo" class="bg-white p-1 shadow-sm">
                <div class="brand-text">
                    <h1><?= esc($nama_perpustakaan) ?></h1>
                    <p>INLISLIte 3.3</p>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars text-white navbar-toggler-icon-custom"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('home') ?>">
                            <i class="fa-solid fa-home me-1"></i>Beranda
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('opac') ?>">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>OPAC
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= (url_is('opac/browse*') || url_is('opac/subjek*') || url_is('subjek*')) ? 'active' : '' ?>" href="<?= base_url('opac/browse') ?>">
                            <i class="fa-solid fa-compass me-1"></i>Browse
                        </a>
                    </li>
 
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('buku-tamu') ?>">
                            <i class="fa-solid fa-address-book me-1"></i>Buku Tamu
                        </a>
                    </li>

                    <?php if (env('Is_keanggotaan_online') == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('home/pendaftaran-online') ?>">
                            <i class="fa-solid fa-id-card me-1"></i>Pendaftaran Anggota
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"
                            id="navbarDropdownLayanan" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-desktop me-1"></i>Layanan
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownLayanan">
                            <!--<li>
                                <a class="dropdown-item" href="<?= base_url('baca-ditempat') ?>">
                                    <i class="fa-solid fa-book-reader fa-fw text-secondary me-2"></i>Baca Ditempat
                                </a>
                            </li>-->
                            <!-- Menu Peminjaman & Pengembalian Mandiri disembunyikan sesuai butir 5 (fitur tetap aktif via direct URL) -->
                            <li style="display: none;">
                                <a class="dropdown-item" href="<?= base_url('peminjaman-mandiri') ?>">
                                    <i class="fa-solid fa-hand-holding-hand fa-fw text-secondary me-2"></i>Peminjaman Mandiri
                                </a>
                            </li>
                            <li style="display: none;">
                                <a class="dropdown-item" href="<?= base_url('pengembalian-mandiri') ?>">
                                    <i class="fa-solid fa-rotate-left fa-fw text-secondary me-2"></i>Pengembalian Mandiri
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('home/layanan-isbn') ?>">
                                    <i class="fa-solid fa-file-pdf fa-fw text-secondary me-2"></i>ISBN
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('home/tata-cara-sirkulasi') ?>">
                                    <i class="fa-solid fa-file-pdf fa-fw text-secondary me-2"></i>Tata Cara Sirkulasi
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#"
                            id="navbarDropdownStatistik" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-chart-pie me-1"></i>Statistik
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="navbarDropdownStatistik">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('opac/statistics') ?>">
                                    <i class="fa-solid fa-book-bookmark fa-fw text-secondary me-2"></i>Statistik Katalog
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('opac/statistics_anggota') ?>">
                                    <i class="fa-solid fa-users fa-fw text-secondary me-2"></i>Statistik Anggota
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>

               <!-- Tombol Login dihapus dari navbar; admin login lewat URL /login. Tombol Dashboard hanya tampil kalau sudah login. -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <?php if (session()->get('logged_in')): ?>
                        <a href="<?= base_url('dashboard') ?>"
                            class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold shadow-sm"
                            style="font-size: 0.9rem; white-space: nowrap;">
                            <i class="fa-solid fa-gauge me-2"></i>Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </nav>

    <!-- ─── Main Content ─── -->
    <main style="min-height: 80vh;">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- ─── Footer ─── -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row mb-5">

                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="<?=  base_url('assets/img/Logo-Inlislite.webp') ?>"
                            alt="Logo" class="rounded bg-white p-1" style="width:150px;height:70px;object-fit:cover;">
                        <h4 class="m-0 fw-bold"><?= esc($nama_perpustakaan) ?></h4>
                    </div>
                    <p class="text-secondary" style="font-size:0.9rem;line-height:1.8;">
                        <?= esc($tentang_kami) ?>
                    </p>
                </div>

                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 offset-lg-1">
                    <h5 class="footer-title">Layanan</h5>
                    <ul class="footer-links">
                        <?php if (env('Is_keanggotaan_online') == 1): ?><li><a href="<?= base_url('home/pendaftaran-online') ?>">Pendaftaran Anggota</a></li><?php endif; ?>
                        <!-- Disembunyikan sesuai butir 5 -->
                        <li style="display: none;"><a href="<?= base_url('peminjaman-mandiri') ?>">Peminjaman Mandiri</a></li>
                        <li style="display: none;"><a href="<?= base_url('pengembalian-mandiri') ?>">Pengembalian Mandiri</a></li>
                        <li><a href="<?= base_url('opac') ?>">OPAC</a></li>
                        <li><a href="<?= base_url('opac/browse') ?>">Browse Katalog</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-title">Kontak & Lokasi</h5>
                    <ul class="footer-links footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i> <span><?= esc($alamat) ?></span></li>
                        <li><i class="fa-solid fa-phone"></i> <span><?= esc($phone)  ?></span></li>
                        <li><i class="fa-solid fa-envelope"></i> <span><?= esc($email)  ?></span></li>
                    </ul>
                </div>

            </div>
            <div class="border-top border-secondary pt-4 mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-secondary mb-0" style="font-size:0.8rem;">
                    &copy; <?= date('Y') ?> <?= esc($nama_perpustakaan) ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- ─── Modal Pop-Up Deskripsi Bibliografis OPAC ─── -->
    <div class="modal fade opac-meta-modal" id="opacBookMetaModal" tabindex="-1" aria-labelledby="opacModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <div class="opac-modal-kicker">DESKRIPSI BIBLIOGRAFIS OPAC KEMNAKER</div>
                        <h3 class="opac-modal-title" id="opacModalTitle">-</h3>
                    </div>
                    <button type="button" class="opac-modal-close-btn" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Body: 2 Kolom (Sampul di Kiri, Detail di Kanan) -->
                <div class="row g-4 align-items-start">
                    <!-- Kolom Kiri: Sampul Buku 3D -->
                    <div class="col-sm-4 d-flex flex-column align-items-center">
                        <div class="opac-book-3d w-100" id="opacModalBook3d" style="max-width: 195px;">
                            <img src="" alt="" id="opacModalCoverImg" class="opac-real-cover-img" style="display: none;"
                                 onerror="this.style.display='none'; document.getElementById('opacModalGenCover').style.display='flex';">
                            <div class="opac-generated-cover" id="opacModalGenCover" style="display: flex;">
                                <div class="opac-book-spine"></div>
                                <div class="opac-cover-geo-circle"></div>
                                <div class="opac-cover-geo-rect"></div>

                                <div class="opac-cover-top d-flex justify-content-between align-items-center">
                                    <span class="opac-cover-tag" id="opacModalCoverTag">-</span>
                                    <span class="opac-cover-year" id="opacModalCoverYear"></span>
                                </div>

                                <div class="opac-cover-middle my-auto">
                                    <div class="opac-cover-title" id="opacModalCoverTitle">-</div>
                                    <div class="opac-cover-author" id="opacModalCoverAuthor">-</div>
                                </div>

                                <div class="opac-cover-bottom d-flex justify-content-between align-items-end">
                                    <span class="opac-cover-call" id="opacModalCoverCall">-</span>
                                    <span class="opac-cover-edition" id="opacModalCoverEdition">-</span>
                                </div>
                            </div>
                        </div>

                        <a href="#" id="opacModalFullDetailLink" class="btn btn-link btn-sm text-decoration-none mt-3 fw-semibold" style="color: #1b3878; font-size: 0.76rem;">
                            <i class="fa-solid fa-up-right-from-square me-1"></i>Halaman Detail &amp; MARC
                        </a>
                    </div>

                    <!-- Kolom Kanan: Metadata & Sirkulasi -->
                    <div class="col-sm-8">
                        <!-- Baris Badge Status & DDC -->
                        <div class="d-flex flex-wrap align-items-center gap-2 pb-3 mb-3 border-bottom">
                            <span id="opacModalStatusBadge" class="opac-status-pill status-available">
                                <i class="fa-regular fa-circle-check me-1"></i>Tersedia di Rak
                            </span>
                            <span class="opac-modal-ddc-pill" id="opacModalDdcBadge">DDC: -</span>
                        </div>

                        <!-- Grid 2x2 Informasi Utama -->
                        <div class="row g-3 pb-3 mb-3 border-bottom">
                            <div class="col-6">
                                <div class="opac-modal-field-label">Pengarang / Penyusun</div>
                                <div class="opac-modal-field-value" id="opacModalAuthor">-</div>
                            </div>
                            <div class="col-6">
                                <div class="opac-modal-field-label">Lokasi Rak Fisik</div>
                                <div class="opac-modal-field-value shelf-highlight">
                                    <i class="fa-solid fa-location-dot me-1"></i><span id="opacModalShelf">-</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="opac-modal-field-label">Penerbit &amp; Tahun</div>
                                <div class="opac-modal-field-value fw-normal" id="opacModalPubYear">-</div>
                            </div>
                            <div class="col-6">
                                <div class="opac-modal-field-label">ISBN &amp; Edisi</div>
                                <div class="opac-modal-field-value fw-normal" id="opacModalIsbnEdition" style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">-</div>
                            </div>
                        </div>

                        <!-- Abstrak / Ringkasan Koleksi -->
                        <div>
                            <div class="opac-modal-section-heading">ABSTRAK / RINGKASAN KOLEKSI</div>
                            <p class="opac-modal-abstract" id="opacModalAbstract">-</p>
                            <div class="opac-modal-subjects">
                                <strong class="text-dark">Tajuk Subjek:</strong>
                                <span id="opacModalSubjects">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js'); ?>/jquery-4.0.0.min.js"></script>
    <script src="<?= base_url('assets/js'); ?>/bootstrap.min.js"></script>
    <script src="<?= base_url('assets/js'); ?>/sweetalert2@8.js"></script>
    <script>
        // SweetAlert2 versi 8 memakai opsi `type`; halaman yang menulis `icon` (nama di versi 9 ke atas) tetap dapat ikonnya
        (function () {
            const fire = Swal.fire.bind(Swal);
            Swal.fire = function (opsi, ...lainnya) {
                if (opsi && typeof opsi === 'object' && opsi.icon && !opsi.type) {
                    opsi = Object.assign({}, opsi, { type: opsi.icon });
                }
                return fire(opsi, ...lainnya);
            };
        })();

        // Handler Pop-Up Deskripsi Bibliografis OPAC
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('opacBookMetaModal');
            if (!modalEl) return;

            let currentBookData = null;

            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('.js-open-book-modal');
                if (!trigger) return;

                const card = trigger.closest('.opac-modern-card');
                if (!card || !card.dataset.bookMeta) return;

                e.preventDefault();

                try {
                    const data = JSON.parse(card.dataset.bookMeta);
                    currentBookData = data;
                    populateAndShowBookModal(data);
                } catch (err) {
                    console.error('Gagal memuat metadata buku:', err);
                    window.location.href = trigger.getAttribute('href');
                }
            });

            function populateAndShowBookModal(data) {
                document.getElementById('opacModalTitle').textContent = data.title || 'Tanpa Judul';
                document.getElementById('opacModalAuthor').textContent = data.author || '-';
                document.getElementById('opacModalShelf').textContent = data.shelfLoc || 'Rak Koleksi Umum';
                document.getElementById('opacModalPubYear').textContent = data.pubAndYear || '-';
                document.getElementById('opacModalIsbnEdition').textContent = data.isbnAndEdition || '-';
                document.getElementById('opacModalDdcBadge').textContent = 'DDC: ' + (data.callNumber || '-');
                document.getElementById('opacModalAbstract').textContent = data.abstract || '-';
                document.getElementById('opacModalFullDetailLink').setAttribute('href', data.detailUrl || '#');

                // Status ketersediaan
                const statusBadge = document.getElementById('opacModalStatusBadge');
                if (data.isAvailable) {
                    statusBadge.className = 'opac-status-pill status-available';
                    statusBadge.innerHTML = '<i class="fa-regular fa-circle-check me-1"></i>' + (data.statusModalText || 'Tersedia di Rak');
                } else {
                    statusBadge.className = 'opac-status-pill status-borrowed';
                    statusBadge.innerHTML = '<i class="fa-regular fa-clock me-1"></i>' + (data.statusModalText || 'Dipinjam');
                }

                // Tajuk Subjek
                const subjContainer = document.getElementById('opacModalSubjects');
                subjContainer.innerHTML = '';
                const subjects = Array.isArray(data.subjects) ? data.subjects : ['Koleksi Umum'];
                subjects.forEach(function (subj, idx) {
                    const a = document.createElement('a');
                    a.href = '<?= base_url('opac') ?>?search=' + encodeURIComponent(subj) + '&search_by=Subject';
                    a.textContent = subj;
                    subjContainer.appendChild(a);
                    if (idx < subjects.length - 1) {
                        subjContainer.appendChild(document.createTextNode(' · '));
                    }
                });

                // Sampul Buku 3D di dalam Modal
                const book3d = document.getElementById('opacModalBook3d');
                const coverImg = document.getElementById('opacModalCoverImg');
                const genCover = document.getElementById('opacModalGenCover');

                book3d.style.background = data.paletteBg || 'linear-gradient(145deg, #10254c 0%, #0a1833 100%)';

                const coverTag = document.getElementById('opacModalCoverTag');
                coverTag.textContent = data.topLabel || 'KOLEKSI';
                coverTag.style.background = data.palettePillBg || 'rgba(245, 158, 11, 0.16)';
                coverTag.style.color = data.paletteAccent || '#f59e0b';

                document.getElementById('opacModalCoverYear').textContent = data.year || '';
                document.getElementById('opacModalCoverTitle').textContent = data.title || '';
                document.getElementById('opacModalCoverAuthor').textContent = data.author || '';
                document.getElementById('opacModalCoverCall').textContent = data.callNumber || '';

                const coverEd = document.getElementById('opacModalCoverEdition');
                coverEd.textContent = data.footerRight || '';
                coverEd.style.color = data.paletteAccent || '#f59e0b';

                if (data.hasCover && data.coverImgSrc) {
                    coverImg.src = data.coverImgSrc;
                    coverImg.alt = data.title || '';
                    coverImg.style.display = 'block';
                    genCover.style.display = 'none';
                } else {
                    coverImg.style.display = 'none';
                    genCover.style.display = 'flex';
                }

                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            }
        });
    </script>

    <?= $this->renderSection('script') ?>
</body>

</html>