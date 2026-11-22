<?php
/**
 * Profil anggota di dashboard.
 * Variabel: $member (MemberNo, Fullname, Email, EndDate, PhotoUrl, IzinPersonalisasi, nama_unit),
 * $ringkasan (dipinjam, total), $hitung (favorit, keranjang dari dataDashboard).
 */
helper(['dashboard_anggota', 'anggota']);

$izin    = (bool) $member->IzinPersonalisasi;
$nama    = (string) $member->Fullname;
$logo     = base_url('assets/img/kartu/logo-kemnaker.png');
$kartu    = kartuAnggotaData($member);
$ukuranQr = count($kartu['qr']);
$jalurQr  = '';
foreach ($kartu['qr'] as $y => $baris) {
    foreach ($baris as $x => $gelap) {
        $jalurQr .= $gelap ? "M{$x} {$y}h1v1h-1z" : '';
    }
}

$ringkas = [
    ['opac/profil/riwayat', 'katalog', $ringkasan['dipinjam'], 'Sedang dipinjam'],
    ['opac/profil/riwayat', 'riwayat', $ringkasan['total'], 'Total pinjaman'],
    // Favorit dinonaktifkan sementara: peminjaman mandiri belum dibuka untuk umum.
    // ['opac/profil/favorit', 'favorit', (int) ($hitung['favorit'] ?? 0), 'Favorit'],
    ['opac/profil/keranjang', 'keranjang', (int) ($hitung['keranjang'] ?? 0), 'Di keranjang'],
];
$data = [
    ['profil', 'Nama', $nama],
    ['nomor', 'Nomor anggota', (string) $member->MemberNo],
    ['unit', 'Unit kerja', $member->nama_unit ?: '-'],
    ['email', 'Email', $member->Email ?: '-'],
];
?>
<?= $this->extend('Dashboard\Views\anggota_kerangka') ?>

<?= $this->section('isi') ?>
<header class="dm-pf-head">
    <h1 class="dm-judul">Profil</h1>
    <p class="dm-sub">Data keanggotaanmu dan pengaturan saran buku.</p>
</header>

<div class="dm-pf-top">
    <div class="dm-pf-kartu" aria-label="Kartu anggota">
        <figure>
            <figcaption>Depan</figcaption>
            <div class="slot">
                <div class="kt kt-depan">
                    <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">
                    <div class="abs kolom <?= $kartu['foto'] ? '' : 'tanpa-foto' ?>">
                        <img class="logo" src="<?= base_url('assets/img/kartu/logo-kemnaker-teks.webp') ?>" alt="Logo Kemnaker">
                        <?php if ($kartu['foto']) : ?><img class="foto" src="<?= esc($kartu['foto'], 'attr') ?>" alt="Fotomu"><?php endif; ?>
                        <div class="nama <?= count($kartu['nama_baris']) > 1 ? 'is-dua' : '' ?>">
                            <div class="label">Nama</div>
                            <div class="value"><?php foreach ($kartu['nama_baris'] as $baris) : ?><span class="baris"><?= esc($baris) ?></span><?php endforeach; ?></div>
                        </div>
                        <div class="nomor <?= $kartu['nomor_urut'] ? '' : 'is-full' ?>">
                            <div class="label">No. Anggota</div>
                            <div class="value"><?= esc($kartu['nomor_depan']) ?></div>
                        </div>
                    </div>
                    <div class="abs tegak" aria-hidden="true"><?php foreach (str_split('PERPUSTAKAAN') as $huruf) : ?><span><?= $huruf ?></span><?php endforeach; ?></div>
                </div>
            </div>
        </figure>
        <figure>
            <figcaption>Belakang</figcaption>
            <div class="slot">
                <div class="kt kt-belakang">
                    <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">
                    <div class="abs scan">
                        <svg class="qr" viewBox="0 0 <?= $ukuranQr ?> <?= $ukuranQr ?>" shape-rendering="crispEdges" role="img" aria-label="Kode QR No. Anggota <?= esc($kartu['member_no'], 'attr') ?>"><path d="<?= $jalurQr ?>"/></svg>
                        <div class="label">QR Scan</div>
                    </div>
                    <div class="abs info">
                        <div class="logo" style="--logo: url('<?= base_url('assets/img/kartu/logo-kemnaker-teks-putih.png') ?>')"></div>
                        <div class="berlaku"><div class="label">Berlaku s/d</div><div class="value"><?= esc($kartu['berlaku']) ?></div></div>
                        <div class="perpus"><div class="label">Perpustakaan Kemnaker RI</div><div class="value">Jl. Jend. Gatot Subroto Kav. 51<br>Jakarta Selatan &middot; (021) 520-7797</div></div>
                        <div class="nomor"><div class="label">No. Anggota</div><div class="value"><?= esc($kartu['member_no']) ?></div></div>
                    </div>
                </div>
            </div>
        </figure>
        <p class="dm-pf-petunjuk">Sebutkan nomor anggota di meja layanan saat meminjam.</p>
    </div>

    <section class="dm-pf-stats" aria-label="Ringkasan">
        <?php foreach ($ringkas as [$tujuan, $ikon, $jumlah, $label]) : ?>
            <a class="dm-pf-stat" href="<?= base_url($tujuan) ?>">
                <span class="dm-pf-ico"><?= dm_ikon($ikon) ?></span>
                <span><b><?= (int) $jumlah ?></b><?= $label ?></span>
            </a>
        <?php endforeach; ?>
    </section>
</div>

<div class="dm-pf-bottom">
    <section class="dm-pf-panel" aria-labelledby="judul-data">
        <h2 id="judul-data" class="dm-pf-h2">Data diri</h2>
        <dl class="dm-pf-list">
            <?php foreach ($data as [$ikon, $label, $isi]) : ?>
                <div>
                    <span class="dm-pf-ico"><?= dm_ikon($ikon) ?></span>
                    <span><dt><?= $label ?></dt><dd><?= esc($isi) ?></dd></span>
                </div>
            <?php endforeach; ?>
        </dl>
        <p class="dm-pf-note">Ada data yang salah? Minta petugas di meja layanan untuk memperbaikinya.</p>
    </section>

    <section class="dm-pf-panel" aria-labelledby="judul-izin">
        <form method="post" action="<?= base_url('opac/profil/izin') ?>" class="dm-pf-izin">
            <?= csrf_field() ?>
            <input type="hidden" name="izin" value="<?= $izin ? '0' : '1' ?>">
            <div>
                <h2 id="judul-izin" class="dm-pf-h2">Saran dari riwayat pinjam</h2>
                <p class="dm-pf-status<?= $izin ? ' on' : '' ?>"><?= $izin ? 'Menyala' : 'Mati' ?></p>
            </div>
            <button type="submit" class="dm-pf-switch" role="switch" aria-checked="<?= $izin ? 'true' : 'false' ?>" aria-labelledby="judul-izin"><span aria-hidden="true"></span></button>
        </form>
        <p class="dm-pf-isi">Kami memakai riwayat pinjammu untuk menyarankan buku. Hanya kamu yang bisa melihatnya. Kalau dimatikan, kamu tetap mendapat buku populer di unit kerjamu dan buku baru.</p>
        <a class="dm-pf-tautan" href="<?= base_url('opac/profil/dashboard') ?>">
            <span class="dm-pf-ico"><?= dm_ikon('bintang') ?></span>
            <span>Saran muncul di bagian <b>Disarankan untukmu</b> pada Beranda.</span>
            <strong>Lihat saran</strong>
        </a>
    </section>
</div>
<?= $this->endSection() ?>
