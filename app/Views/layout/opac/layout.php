<?php
$nama_perpustakaan = $nama_perpustakaan ?? 'Perpustakaan Mitra';
$alamat = $alamat_perpustakaan ?? 'Jl.Perpustakaan Mitra';
$tentang_kami = $tentang_kami ?? 'Perpustakaan Mitra';
$logo = $logo ?? '';
$phone = $phone ?? '';
$email = $email ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'OPAC - ' . $nama_perpustakaan; ?></title>

    <link rel="icon" href="<?= !empty($logo) ? base_url('uploads/branch/' . $logo) : base_url('assets/img/logo-inlislite-icon.webp') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css') ?>/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/opac_beranda.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/opac_beranda.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/opac_modal.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/opac_modal.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400;1,8..60,600&display=swap" rel="stylesheet">

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
            aspect-ratio: 2 / 3;
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

        /* Pill Rekomendasi (Populer / Baru) */
        .opac-recom-pill {
            display: inline-flex;
            align-items: center;
            font-size: 0.73rem;
            font-weight: 600;
            padding: 3.5px 10px;
            border-radius: 999px;
            line-height: 1.25;
            letter-spacing: 0.01em;
        }

        .opac-recom-pill.recom-popular {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .opac-recom-pill.recom-baru {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        /* Subtle Utilities Fallback untuk Bootstrap 5.0.2 */
        .bg-danger-subtle {
            background-color: #fee2e2 !important;
        }
        .text-danger-emphasis {
            color: #991b1b !important;
        }
        .border-danger-subtle {
            border-color: #fecaca !important;
        }

        .bg-warning-subtle {
            background-color: #fef3c7 !important;
        }
        .text-warning-emphasis {
            color: #92400e !important;
        }
        .border-warning-subtle {
            border-color: #fde68a !important;
        }

        .bg-success-subtle {
            background-color: #ecfdf5 !important;
        }
        .text-success-emphasis {
            color: #065f46 !important;
        }
        .border-success-subtle {
            border-color: #a7f3d0 !important;
        }

        .bg-primary-subtle {
            background-color: #eff6ff !important;
        }
        .text-primary-emphasis {
            color: #1e40af !important;
        }
        .border-primary-subtle {
            border-color: #bfdbfe !important;
        }

        /* Custom Sleek Scrollbar untuk Carousel Rekomendasi */
        .opac-carousel-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 12px;
        }

        .opac-carousel-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .opac-carousel-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 999px;
        }

        .opac-carousel-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
            transition: background 0.2s ease;
        }

        .opac-carousel-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

    </style>

    <?= $this->renderSection('style') ?>
</head>

<body>
    <?= $this->include('App\Views\layout\partial\loader'); ?>

    <!-- ─── Navbar ─── -->
    <?= $this->include('App\Views\layout\partial\navbar_publik') ?>

    <!-- ─── Main Content ─── -->
    <main style="min-height: 80vh;">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- ─── Footer ─── -->
    <?= $this->include('App\Views\layout\partial\footer_publik') ?>

    <!-- ─── Modal Pop-Up Deskripsi Bibliografis OPAC ─── -->
    <div class="modal fade om-modal" id="opacBookMetaModal" tabindex="-1" aria-labelledby="omJudul" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="om-tutup" data-bs-dismiss="modal" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
                <div class="om-grid">
                    <div class="om-sampul">
                        <img class="ob-cover-img" id="omSampulFoto" alt="" hidden>
                        <div class="ob-cover" id="omSampulBuatan" aria-hidden="true">
                            <span class="ob-cover-punggung"></span>
                            <span class="ob-cover-kategori" id="omSampulKategori"></span>
                            <strong class="ob-cover-judul" id="omSampulJudul"></strong>
                            <span class="ob-cover-penulis" id="omSampulPenulis"></span>
                            <span class="ob-cover-bulat" id="omSampulBulat"></span>
                        </div>
                    </div>
                    <div class="om-isi">
                        <div>
                            <h2 class="om-judul" id="omJudul"></h2>
                            <p class="om-penulis" id="omPenulis"></p>
                            <span class="om-stok" id="omStok"></span>
                        </div>
                        <dl class="om-info">
                            <div><dt>Nomor panggil</dt><dd id="omPanggil"></dd></div>
                            <div><dt>Lokasi rak</dt><dd id="omRak"></dd></div>
                            <div><dt>Penerbit dan tahun</dt><dd id="omPenerbit"></dd></div>
                            <div><dt>ISBN dan edisi</dt><dd id="omIsbn"></dd></div>
                        </dl>
                        <div class="om-ringkas" id="omRingkas">
                            <h3>Ringkasan</h3>
                            <p id="omRingkasan"></p>
                        </div>
                        <div class="om-subjek" id="omSubjekWrap">
                            <h3>Subjek</h3>
                            <ul id="omSubjek"></ul>
                        </div>
                        <div class="om-aksi">
                            <a class="om-btn om-btn-utama" id="omDetail" href="#">Lihat detail</a>
                            <button type="button" class="om-btn om-btn-lain" data-bs-dismiss="modal">Tutup</button>
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

    </script>
    <script>window.OPAC_URL = <?= json_encode(base_url('opac')) ?>;</script>
    <script src="<?= base_url('assets/js/opac_modal.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/opac_modal.js') ?>" defer></script>

    <?= $this->renderSection('script') ?>
</body>

</html>
