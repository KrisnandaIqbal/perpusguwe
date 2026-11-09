<?php
// Navbar publik "liquid glass" (desain Figma), dipakai beranda dan layout OPAC. CSS: assets/home/navbar.css, JS: assets/home/navbar.js.
// Ikon memakai landing_icon() (ikon garis yang sama dengan isi beranda), jadi tidak butuh Font Awesome maupun Bootstrap.
helper('landing');
$_aset  = base_url('assets/home');
$_path  = trim(current_url(true)->getPath(), '/');
$_depan = in_array($_path, ['', 'home', 'index.php'], true);
$_aktif = static function (bool $cocok): string {
    return $cocok ? ' active' : '';
};
$_ikon = static fn(string $nama): string => landing_icon($nama, 'gn-icon');
$_panah = landing_icon('chevron', 'gn-chev');
?>
<link rel="stylesheet" href="<?= $_aset ?>/navbar.css?v=5">

<header class="gn-bar">
    <a href="<?= base_url('home') ?>" class="gn-brand" aria-label="Perpustakaan Kementerian Ketenagakerjaan RI - Beranda">
        <img class="gn-brand-mark" src="<?= $_aset ?>/logo-kemnaker.png" alt="" width="40" height="40">
        <span class="gn-brand-copy">
            <strong>Perpustakaan</strong>
            <span>Kementerian Ketenagakerjaan RI</span>
        </span>
    </a>

    <nav class="gn-nav" id="gn-nav" aria-label="Navigasi utama">
        <a href="<?= base_url('home') ?>" class="gn-link<?= $_aktif($_depan) ?>"><?= $_ikon('home') ?>Beranda</a>
        <a href="<?= base_url('opac') ?>" class="gn-link<?= $_aktif($_path === 'opac') ?>"><?= $_ikon('search') ?>OPAC</a>
        <a href="<?= base_url('opac/browse') ?>" class="gn-link<?= $_aktif($_path === 'opac/browse') ?>"><?= $_ikon('browse') ?>Browse</a>
        <a href="<?= base_url('buku-tamu') ?>" class="gn-link<?= $_aktif(str_starts_with($_path, 'buku-tamu')) ?>"><?= $_ikon('tamu') ?>Buku Tamu</a>
        <?php if (env('Is_keanggotaan_online') == 1) : ?>
            <a href="<?= base_url('home/pendaftaran-online') ?>" class="gn-link"><?= $_ikon('registrasi') ?>Pendaftaran Anggota</a>
        <?php endif; ?>

        <div class="gn-drop">
            <button type="button" class="gn-link gn-drop-btn" aria-expanded="false">
                <?= $_ikon('grid') ?>Layanan<?= $_panah ?>
            </button>
            <div class="gn-panel">
                <span class="gn-panel-title">Layanan perpustakaan</span>
                <a class="gn-item" href="<?= base_url('home/layanan-isbn') ?>"><span class="gn-item-icon"><?= landing_icon('isbn') ?></span>ISBN</a>
                <a class="gn-item" href="<?= base_url('home/tata-cara-sirkulasi') ?>"><span class="gn-item-icon"><?= landing_icon('sirkulasi') ?></span>Tata Cara Sirkulasi</a>
            </div>
        </div>

        <div class="gn-drop">
            <button type="button" class="gn-link gn-drop-btn" aria-expanded="false">
                <?= $_ikon('statistik') ?>Statistik<?= $_panah ?>
            </button>
            <div class="gn-panel">
                <span class="gn-panel-title">Statistik</span>
                <a class="gn-item" href="<?= base_url('opac/statistics') ?>"><span class="gn-item-icon"><?= landing_icon('katalog') ?></span>Statistik Katalog</a>
                <a class="gn-item" href="<?= base_url('opac/statistics_anggota') ?>"><span class="gn-item-icon"><?= landing_icon('anggota') ?></span>Statistik Anggota</a>
            </div>
        </div>
    </nav>

    <div class="gn-actions">
        <?php if (session()->get('logged_in')) : ?>
            <a class="gn-login" href="<?= base_url('dashboard') ?>">Dashboard</a>
        <?php else : ?>
            <a class="gn-login" href="<?= base_url('login') ?>">Masuk</a>
        <?php endif; ?>
        <button class="gn-burger" id="gn-burger" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="gn-nav">
            <span data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></span>
            <span data-close hidden><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></span>
        </button>
    </div>
</header>
<script src="<?= $_aset ?>/navbar.js?v=3"></script>
