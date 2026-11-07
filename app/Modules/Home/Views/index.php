<?php
// Beranda (landing page). File ini hanya kerangka halaman; tiap bagian ada di Views/landing/.
//   Data      : disiapkan Home::index() (Controllers/Home.php)
//   Fungsi    : Helpers/landing_helper.php (ikon, layanan, sampul buku, foto hero)
//   Gaya      : public/assets/home/landing.css + landing-extra.css, navbar di navbar.css
//   Perilaku  : public/assets/home/js/*.js (daftarnya di Views/landing/scripts.php)
//   Navbar    : layout/partial/navbar_publik.php (dipakai juga oleh halaman OPAC)
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Beranda') ?> | <?= esc($library_name) ?></title>
    <link rel="icon" href="<?= base_url('assets/img/logo-inlislite-icon.webp') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Serif+4:opsz,wght@8..60,600&display=swap">
    <link rel="stylesheet" href="<?= landing_asset('landing.css') ?>">
    <link rel="stylesheet" href="<?= landing_asset('landing-extra.css') ?>">
</head>

<body>
    <?= $this->include('App\Views\layout\partial\loader') ?>
    <main class="page-shell">
        <div class="site-frame">
            <?= $this->include('App\Views\layout\partial\navbar_publik') ?>
            <?= $this->include('Home\Views\landing\hero') ?>
            <?= $this->include('Home\Views\landing\shortcuts') ?>
            <?= $this->include('Home\Views\landing\showcase') ?>
            <?= $this->include('Home\Views\landing\catalog') ?>
            <?= $this->include('Home\Views\landing\footer') ?>
        </div>
    </main>

    <?= $this->include('Home\Views\landing\scripts') ?>
</body>

</html>
