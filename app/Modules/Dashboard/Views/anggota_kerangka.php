<?php
/**
 * Kerangka dashboard anggota: sidebar + isi. Halaman memakai $this->extend() dan mengisi section 'isi'.
 * Variabel: $aktif (kunci menu yang menyala), $hitung (jumlah keranjang dan favorit untuk lencana menu).
 */
helper(['landing', 'dashboard_anggota']);

$anggota = session('session_member');
$nama    = (string) ($anggota['nama'] ?? '');
$nomor   = (string) ($anggota['member_no'] ?? '');
$logo    = landing_asset('logo-kemnaker.png');

$namaPerpustakaan = db_connect('data')->table('settingparameters')->select('Value')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? '';
$namaPerpustakaan = $namaPerpustakaan ?: 'Perpustakaan';

$menu = [
    'beranda'   => ['Beranda', 'opac/profil/dashboard'],
    'keranjang' => ['Keranjang', 'opac/profil/keranjang'],
    // Favorit dinonaktifkan sementara: peminjaman mandiri belum dibuka untuk umum.
    // 'favorit'   => ['Favorit', 'opac/profil/favorit'],
    'riwayat'   => ['Riwayat Peminjaman', 'opac/profil/riwayat'],
    'katalog'   => ['Katalog', 'opac'],
    'profil'    => ['Profil', 'opac/profil'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($judul ?? 'Dashboard Anggota') ?></title>
    <link rel="icon" href="<?= esc($logo, 'attr') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard-app/anggota.css') . '?v=' . filemtime(FCPATH . 'assets/dashboard-app/anggota.css') ?>">
</head>
<body>
<div class="dm-shell">
    <aside class="dm-side" aria-label="Menu anggota">
        <a class="dm-brand" href="<?= base_url('home') ?>">
            <img src="<?= esc($logo, 'attr') ?>" alt="">
            <strong><?= esc($namaPerpustakaan) ?></strong>
        </a>
        <div class="dm-user">
            <span class="dm-avatar" aria-hidden="true"><?= esc(mb_strtoupper(mb_substr($nama, 0, 2))) ?></span>
            <span><b><?= esc($nama) ?></b><small><?= esc($nomor) ?></small></span>
        </div>
        <nav class="dm-nav" aria-label="Akses cepat">
            <p class="dm-label">AKSES CEPAT</p>
            <?php foreach ($menu as $kunci => [$label, $tujuan]) : ?>
                <a class="dm-link<?= $aktif === $kunci ? ' on' : '' ?>" href="<?= base_url($tujuan) ?>"<?= $aktif === $kunci ? ' aria-current="page"' : '' ?>>
                    <?= dm_ikon($kunci) ?><?= $label ?>
                    <?php if (!empty($hitung[$kunci])) : ?><span class="dm-count"><?= (int) $hitung[$kunci] ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="dm-foot">
            <a class="dm-link" href="<?= base_url('opac/keluar') ?>"><?= dm_ikon('keluar') ?>Keluar</a>
        </div>
    </aside>

    <main class="dm-main">
        <?php if (session('pesan')) : ?><div class="dm-flash" role="status"><?= esc(session('pesan')) ?></div><?php endif; ?>
        <?= $this->renderSection('isi') ?>
    </main>
</div>
</body>
</html>
