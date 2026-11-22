<?php
/**
 * Beranda dashboard anggota. Data buku datang dari OPAC (Opac::getBarisAnggota, lewat Anggota::dashboard); modul OPAC tidak diubah.
 * Dua bagian saja: "Disarankan untukmu" (baris personal pertama) dan baris populer.
 */
helper(['landing', 'dashboard_anggota']);

// Baris populer: "Paling banyak dipinjam" atau "Populer di <unit>". Baris personal pertama yang tersisa jadi "Disarankan untukmu".
$baris        = $baris_rekomendasi ?? [];
$kunciPopuler = null;
foreach (array_keys($baris) as $kunci) {
    if ($kunci === 'Paling banyak dipinjam' || str_starts_with($kunci, 'Populer di ')) {
        $kunciPopuler = $kunci;
        break;
    }
}
$populer    = $kunciPopuler !== null ? $baris[$kunciPopuler] : [];
$sisa       = array_diff_key($baris, $kunciPopuler !== null ? [$kunciPopuler => 1] : []);
$kunciSaran = array_key_first($sisa);
$saran      = $kunciSaran !== null ? $sisa[$kunciSaran] : [];
?>
<?= $this->extend('Dashboard\Views\anggota_kerangka') ?>

<?= $this->section('isi') ?>
<header class="dm-head">
    <h1>Halo, <?= esc(session('session_member')['nama'] ?? '') ?></h1>
    <form class="dm-search" role="search" action="<?= base_url('opac') ?>" method="get">
        <label class="sr-only" for="cari-koleksi">Cari koleksi</label>
        <div class="dm-search-box">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input id="cari-koleksi" type="search" name="search" placeholder="Cari judul, pengarang, atau subjek">
            <button type="submit" aria-label="Cari di katalog"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></button>
        </div>
    </form>
</header>

<?php if ($saran) : ?>
<section class="dm-sec" aria-labelledby="judul-saran">
    <div class="dm-sec-head">
        <h2 id="judul-saran">Disarankan untukmu</h2>
        <a href="<?= base_url('opac/profil') ?>">Atur saran di Profil</a>
    </div>
    <p class="dm-sub"><?= esc($kunciSaran) ?></p>
    <div class="dm-grid"><?php foreach ($saran as $b) { echo dm_kartu($b, null, $favorit_ids, $keranjang_ids); } ?></div>
</section>
<?php endif; ?>

<?php if ($populer) : ?>
<section class="dm-sec" aria-labelledby="judul-populer">
    <div class="dm-sec-head">
        <h2 id="judul-populer"><?= esc($kunciPopuler) ?></h2>
        <a href="<?= base_url('opac') ?>">Buka katalog lengkap</a>
    </div>
    <div class="dm-grid"><?php foreach ($populer as $i => $b) { echo dm_kartu($b, $i + 1, $favorit_ids, $keranjang_ids); } ?></div>
</section>
<?php endif; ?>
<?= $this->endSection() ?>
