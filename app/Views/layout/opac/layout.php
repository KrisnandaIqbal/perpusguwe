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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

    <?= $this->renderSection('script') ?>
</body>

</html>