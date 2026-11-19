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
<link rel="stylesheet" href="<?= base_url('assets/home/navbar.css') . '?v=' . time() ?>">

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
        <a href="<?= base_url('buku-tamu') ?>" class="gn-link<?= $_aktif(str_starts_with($_path, 'buku-tamu')) ?>"><?= $_ikon('tamu') ?>Buku Tamu</a>

        <div class="gn-drop">
            <button type="button" class="gn-link gn-drop-btn" aria-expanded="false">
                <?= $_ikon('grid') ?>Layanan<?= $_panah ?>
            </button>
            <div class="gn-panel">
                <span class="gn-panel-title">Layanan perpustakaan</span>
                <a class="gn-item" href="<?= base_url('home/layanan-isbn') ?>"><span class="gn-item-icon"><?= landing_icon('isbn') ?></span>ISBN</a>
                <a class="gn-item" href="<?= base_url('home/tata-cara-sirkulasi') ?>"><span class="gn-item-icon"><?= landing_icon('sirkulasi') ?></span>Tata Cara Sirkulasi</a>
                <?php if (env('Is_keanggotaan_online') == 1) : ?>
                    <a class="gn-item" href="<?= base_url('home/pendaftaran-online') ?>"><span class="gn-item-icon"><?= landing_icon('registrasi') ?></span>Pendaftaran Anggota</a>
                <?php endif; ?>
            </div>
        </div>
        <?php /* Statistik disembunyikan sementara (halaman opac/statistics dan opac/statistics_anggota tetap ada). */ ?>
    </nav>

    <div class="gn-actions">
        <?php 
            $currentUser = function_exists('user') ? user() : null;
            $isAnggota = false;
            if ($currentUser) {
                $isAnggota = (
                    ($currentUser->category ?? '') === 'anggota' ||
                    !empty($currentUser->anggota) ||
                    (function_exists('is_member') && (is_member('anggota') || is_member('keanggotaan')))
                );
            }
        ?>
        <?php if (session()->get('logged_in')) : ?>
            <a class="gn-login" href="<?= $isAnggota ? base_url('portal') : base_url('dashboard') ?>" aria-label="<?= $isAnggota ? 'Portal Anggota' : 'Dashboard' ?>">
                <span class="gn-login-full"><?= $isAnggota ? 'Portal Anggota' : 'Dashboard' ?></span>
                <span class="gn-login-short"><?= $isAnggota ? 'Portal' : 'Akun' ?></span>
            </a>
        <?php elseif (session()->get('session_member')) : ?>
            <a class="gn-login" href="<?= base_url('opac/profil/dashboard') ?>" aria-label="Dashboard Anggota">
                <span class="gn-login-full">Dashboard</span>
                <span class="gn-login-short">Dashboard</span>
            </a>
        <?php else : ?>
            <a class="gn-login" href="<?= base_url('login') ?>">Masuk</a>
        <?php endif; ?>
        <button class="gn-burger" id="gn-burger" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="gn-nav">
            <span data-open><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></span>
            <span data-close hidden><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></span>
        </button>
    </div>
</header>

<!-- Navigasi Bawah Mobile (< 768px): floating liquid glass dock -->
<div class="gn-bnav-sheet" id="gn-bnav-layanan-sheet" hidden>
    <span class="gn-panel-title">Layanan perpustakaan</span>
    <a class="gn-item" href="<?= base_url('home/layanan-isbn') ?>"><span class="gn-item-icon"><?= landing_icon('isbn') ?></span>ISBN</a>
    <a class="gn-item" href="<?= base_url('home/tata-cara-sirkulasi') ?>"><span class="gn-item-icon"><?= landing_icon('sirkulasi') ?></span>Tata Cara Sirkulasi</a>
    <?php if (env('Is_keanggotaan_online') == 1) : ?>
        <a class="gn-item" href="<?= base_url('home/pendaftaran-online') ?>"><span class="gn-item-icon"><?= landing_icon('registrasi') ?></span>Pendaftaran Anggota</a>
    <?php endif; ?>
</div>

<nav class="gn-bottom-nav" aria-label="Navigasi bawah mobile">
    <a href="<?= base_url('home') ?>" class="gn-bnav-item<?= $_aktif($_depan) ?>" aria-label="Halaman Beranda"<?= $_depan ? ' aria-current="page"' : '' ?>>
        <?= $_ikon('home') ?>
        <span>Beranda</span>
    </a>
    <a href="<?= base_url('opac') ?>" class="gn-bnav-item<?= $_aktif($_path === 'opac') ?>" aria-label="Katalog OPAC"<?= $_path === 'opac' ? ' aria-current="page"' : '' ?>>
        <?= $_ikon('search') ?>
        <span>OPAC</span>
    </a>
    <a href="<?= base_url('buku-tamu') ?>" class="gn-bnav-item<?= $_aktif(str_starts_with($_path, 'buku-tamu')) ?>" aria-label="Buku Tamu"<?= str_starts_with($_path, 'buku-tamu') ? ' aria-current="page"' : '' ?>>
        <?= $_ikon('tamu') ?>
        <span>Buku Tamu</span>
    </a>
    <?php $_layananAktif = str_starts_with($_path, 'home/layanan') || str_starts_with($_path, 'home/tata-cara') || str_starts_with($_path, 'home/pendaftaran'); ?>
    <button type="button" id="gn-bnav-layanan-btn" class="gn-bnav-item<?= $_aktif($_layananAktif) ?>" aria-label="Layanan Perpustakaan" aria-expanded="false" aria-controls="gn-bnav-layanan-sheet">
        <?= $_ikon('grid') ?>
        <span>Layanan</span>
    </button>
    <?php if (session()->get('logged_in')) : ?>
        <a href="<?= $isAnggota ? base_url('portal') : base_url('dashboard') ?>" class="gn-bnav-item<?= $_aktif(str_starts_with($_path, 'portal') || str_starts_with($_path, 'dashboard')) ?>" aria-label="<?= $isAnggota ? 'Portal Anggota' : 'Dashboard' ?>"<?= (str_starts_with($_path, 'portal') || str_starts_with($_path, 'dashboard')) ? ' aria-current="page"' : '' ?>>
            <?= $_ikon('anggota') ?>
            <span><?= $isAnggota ? 'Portal' : 'Akun' ?></span>
        </a>
    <?php elseif (session()->get('session_member')) : ?>
        <a href="<?= base_url('opac/profil') ?>" class="gn-bnav-item<?= $_aktif(str_starts_with($_path, 'opac/profil')) ?>" aria-label="Profil Anggota"<?= str_starts_with($_path, 'opac/profil') ? ' aria-current="page"' : '' ?>>
            <?= $_ikon('anggota') ?>
            <span>Profil</span>
        </a>
    <?php else : ?>
        <a href="<?= base_url('login') ?>" class="gn-bnav-item<?= $_aktif($_path === 'login') ?>" aria-label="Masuk ke Akun"<?= $_path === 'login' ? ' aria-current="page"' : '' ?>>
            <?= $_ikon('anggota') ?>
            <span>Masuk</span>
        </a>
    <?php endif; ?>
</nav>

<script src="<?= $_aset ?>/navbar.js?v=4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('gn-bnav-layanan-btn');
    var sheet = document.getElementById('gn-bnav-layanan-sheet');
    if (!btn || !sheet) return;
    function setOpen(open) {
        sheet.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.classList.toggle('is-open', open);
    }
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        setOpen(sheet.hidden);
    });
    document.addEventListener('click', function (e) {
        if (!sheet.hidden && !sheet.contains(e.target) && !btn.contains(e.target)) {
            setOpen(false);
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !sheet.hidden) setOpen(false);
    });
});
</script>
