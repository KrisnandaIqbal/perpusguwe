<?php
/** Daftar buku milik anggota: dipakai Keranjang dan Favorit. Variabel: $judulHalaman, $keterangan, $buku, $kosong, $tersedia (false bila tabel belum dibuat). */
helper(['landing', 'dashboard_anggota']);
?>
<?= $this->extend('Dashboard\Views\anggota_kerangka') ?>

<?= $this->section('isi') ?>
<section class="dm-sec" aria-labelledby="judul-halaman">
    <div class="dm-sec-head">
        <h1 id="judul-halaman" class="dm-judul"><?= esc($judulHalaman) ?></h1>
        <a href="<?= base_url('opac') ?>">Cari buku di katalog</a>
    </div>
    <p class="dm-sub"><?= esc($keterangan) ?></p>
    <?php if (!$tersedia) : ?>
        <p class="dm-empty">Fitur ini belum aktif. Pustakawan perlu menjalankan pembaruan database terlebih dahulu.</p>
    <?php elseif (!$buku) : ?>
        <p class="dm-empty"><?= esc($kosong) ?></p>
    <?php else : ?>
        <div class="dm-grid"><?php foreach ($buku as $b) { echo dm_kartu($b, null, $favorit_ids, $keranjang_ids); } ?></div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
