<?php

// Rak buku di beranda OPAC: kartu bergaya landing (Home/Views/landing/showcase.php) dan carousel.
// CSS: public/assets/css/opac_beranda.css, JS: public/assets/js/opac_beranda.js.

if (! function_exists('beranda_kartu_buku')) {
    /**
     * Satu kartu buku. $jenis 'populer' menambah nomor urut dan jumlah pinjam; selain itu menampilkan tahun terbit.
     * $b adalah baris katalog (array) yang sudah lewat enrichCatalogsWithCirculation().
     */
    function beranda_kartu_buku(array $b, string $jenis = '', int $urutan = 0): string
    {
        helper(['landing', 'image']);

        $id      = (int) ($b['ID'] ?? $b['id'] ?? 0);
        $judul   = trim((string) ($b['Title'] ?? '')) ?: 'Tanpa judul';
        $penulis = trim((string) ($b['Author'] ?? ''), " ;,\t\n") ?: 'Anonim';
        $buku    = (object) ($b + ['ID' => $id]);

        $total  = (int) ($b['TotalEks'] ?? 0);
        $tersedia = (int) ($b['AvailEks'] ?? 0);
        $dipinjam = (int) ($b['total_dipinjam'] ?? 0);
        $tahun    = trim((string) ($b['PublishYear'] ?? ''));
        $tahun    = in_array($tahun, ['', '0000', '-'], true) ? 'Koleksi baru' : $tahun;

        helper('opac');

        ob_start(); ?>
        <article class="ob-card" data-book-meta="<?= opacBookMetaAttr($b) ?>">
            <div class="ob-card-cover">
                <?php if ($jenis === 'populer' && $urutan > 0) : ?><span class="ob-rank">#<?= $urutan ?></span><?php endif; ?>
                <?php if (landing_has_cover($buku)) : ?>
                    <img class="ob-cover-img" src="<?= get_catalog_thumb_url($buku->CoverURL, 300, 450) ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='<?= base_url('assets/img/default-cover.webp') ?>'">
                <?php else :
                    $gaya     = landing_cover_style($buku);
                    $kategori = trim(explode(';', (string) ($b['Subject'] ?? ''))[0], " ,\t\n");
                ?>
                    <div class="ob-cover<?= $gaya['terang'] ? ' ob-cover-terang' : '' ?>" style="background:<?= $gaya['warna'][0] ?>;color:<?= $gaya['warna'][1] ?>" aria-hidden="true">
                        <span class="ob-cover-punggung"></span>
                        <span class="ob-cover-kategori"><?= esc(mb_strimwidth($kategori ?: 'Koleksi', 0, 26, '…')) ?></span>
                        <strong class="ob-cover-judul"><?= esc($judul) ?></strong>
                        <span class="ob-cover-penulis"><?= esc(mb_strimwidth($penulis, 0, 34, '…')) ?></span>
                        <span class="ob-cover-bulat" style="background:<?= $gaya['warna'][1] ?>"></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ob-card-info">
                <span class="ob-card-meta">
                    <?php if ($jenis === 'populer' && $dipinjam > 0) : ?>
                        <?= landing_icon('katalog') ?><span><?= $dipinjam ?> kali dipinjam</span>
                    <?php else : ?>
                        <span><?= esc($tahun) ?></span>
                    <?php endif; ?>
                </span>
                <h3><?= esc($judul) ?></h3>
                <p><?= esc($penulis) ?></p>
                <?php $keranjang = opacTombolKeranjang($id, 'ob-keranjang', false, $judul); ?>
                <div class="ob-card-aksi<?= $keranjang !== '' ? ' ob-card-aksi-anggota' : '' ?>">
                    <?php if ($total > 0) : ?>
                        <span class="ob-stok <?= $tersedia > 0 ? 'ob-stok-ada' : 'ob-stok-habis' ?>"><?= $tersedia > 0 ? 'Tersedia' : 'Sedang dipinjam' ?></span>
                    <?php endif; ?>
                    <a class="ob-lihat js-open-book-modal" href="<?= base_url('opac/detail/' . $id) ?>" aria-label="Lihat detail: <?= esc($judul, 'attr') ?>">Lihat</a>
                    <?= $keranjang ?>
                </div>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }
}

if (! function_exists('beranda_rak')) {
    /**
     * Satu rak: judul, tombol geser, lalu deretan kartu yang bisa digeser (carousel).
     *
     * $opsi: jenis ('populer' memberi nomor urut), semua (URL "Lihat semua"), antara (HTML di antara judul dan rak,
     * dipakai chip topik), kosong (teks bila rak tidak punya buku).
     */
    function beranda_rak(string $id, string $judul, string $sub, array $buku, array $opsi = []): string
    {
        $jenis  = $opsi['jenis'] ?? '';
        $semua  = $opsi['semua'] ?? null;
        $antara = $opsi['antara'] ?? '';
        $kosong = $opsi['kosong'] ?? 'Belum ada buku untuk ditampilkan.';

        ob_start(); ?>
        <section class="ob-rak" id="<?= esc($id, 'attr') ?>" aria-labelledby="<?= esc($id, 'attr') ?>-h" data-carousel>
            <div class="ob-rak-kepala">
                <div class="ob-rak-judul">
                    <h2 id="<?= esc($id, 'attr') ?>-h"><?= esc($judul) ?></h2>
                    <?php if ($sub !== '') : ?><p><?= esc($sub) ?></p><?php endif; ?>
                </div>
                <div class="ob-rak-alat">
                    <?php if ($semua) : ?><a class="ob-semua" href="<?= esc($semua, 'attr') ?>">Lihat semua</a><?php endif; ?>
                    <?php if ($buku) : ?>
                        <span class="ob-hitung" data-carousel-count aria-live="polite"></span>
                        <button type="button" class="ob-nav" data-carousel-prev aria-label="Buku sebelumnya: <?= esc($judul, 'attr') ?>" disabled>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
                        </button>
                        <button type="button" class="ob-nav" data-carousel-next aria-label="Buku berikutnya: <?= esc($judul, 'attr') ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?= $antara ?>
            <?php if ($buku) : ?>
                <div class="ob-track" data-carousel-track tabindex="0" role="group" aria-label="Daftar buku: <?= esc($judul, 'attr') ?>">
                    <?php foreach (array_values($buku) as $i => $b) : ?>
                        <?= beranda_kartu_buku($b, $jenis, $i + 1) ?>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="ob-kosong"><?= esc($kosong) ?></p>
            <?php endif; ?>
        </section>
        <?php
        return ob_get_clean();
    }
}
