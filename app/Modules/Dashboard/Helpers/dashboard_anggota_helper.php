<?php

if (! function_exists('dm_ikon')) {
    /** Ikon garis untuk sidebar dan tombol card (viewBox 24). */
    function dm_ikon(string $nama): string
    {
        $isi = [
            'beranda'  => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
            'katalog'  => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
            'keranjang' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>',
            'favorit'  => '<path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>',
            'riwayat'  => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>',
            'profil'   => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>',
            'nomor'    => '<rect width="18" height="14" x="3" y="5" rx="2"/><path d="M7 10h4M7 14h7"/>',
            'unit'     => '<path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 10h1M14 10h1M9 14h1M14 14h1"/>',
            'email'    => '<rect width="18" height="14" x="3" y="5" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'bintang'  => '<path d="m12 3 2.6 5.6 6.1.7-4.5 4.2 1.2 6L12 16.6 6.6 19.5l1.2-6L3.3 9.3l6.1-.7z"/>',
            'keluar'   =>'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>',
        ][$nama] ?? '';

        return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $isi . '</svg>';
    }
}

if (! function_exists('dm_cover')) {
    /** Cover: gambar asli bila ada, selain itu cover buatan (landing_cover_style) seperti di beranda. */
    function dm_cover(array $b): string
    {
        $url = trim((string) ($b['CoverURL'] ?? ''));
        if ($url !== '' && ! filter_var($url, FILTER_VALIDATE_URL)) {
            $url = is_file(FCPATH . 'uploads/katalog/' . $url) ? base_url('uploads/katalog/' . $url) : '';
        }
        if ($url !== '' && ! in_array(basename($url), ['default-cover.jpg', 'default-cover.png'], true)) {
            return '<img src="' . esc($url, 'attr') . '" alt="" loading="lazy">';
        }
        $gaya     = landing_cover_style((object) $b);
        $penulis  = trim((string) ($b['Author'] ?? ''), " ;,\t\n");
        $kategori = trim(explode(';', (string) ($b['Subject'] ?? ''))[0], " ,\t\n");

        return '<div class="book-cover book-cover-md' . ($gaya['terang'] ? ' book-cover-light' : '') . '" style="background:' . $gaya['warna'][0] . ';color:' . $gaya['warna'][1] . '" aria-hidden="true">'
            . '<span class="book-cover-spine"></span>'
            . '<span class="book-cover-category">' . esc(mb_strimwidth($kategori ?: 'Koleksi', 0, 26, '…')) . '</span>'
            . '<strong class="book-cover-title">' . esc($b['Title']) . '</strong>'
            . '<span class="book-cover-author">' . esc(mb_strimwidth($penulis ?: 'Anonim', 0, 34, '…')) . '</span>'
            . '<span class="book-cover-shape" style="background:' . $gaya['warna'][1] . '"></span></div>';
    }
}

if (! function_exists('dm_kartu')) {
    /**
     * Card buku. $urutan: nomor peringkat (opsional). $favorit/$keranjang: id katalog milik anggota ini,
     * tombolnya menyimpan atau menghapus lewat POST dan kembali ke halaman yang sama.
     */
    function dm_kartu(array $b, ?int $urutan = null, array $favorit = [], array $keranjang = []): string
    {
        $id       = (int) $b['ID'];
        $tersedia = (int) ($b['AvailEks'] ?? 0);
        $total    = (int) ($b['TotalEks'] ?? 0);
        $penulis  = trim((string) ($b['Author'] ?? ''), " ;,\t\n");
        $subjek   = trim(explode(';', (string) ($b['Subject'] ?? ''))[0]);
        $aksi     = static function (string $tujuan, bool $aktif, string $ikon, string $labelAktif, string $labelBelum) use ($id): string {
            $label = $aktif ? $labelAktif : $labelBelum;

            return '<form method="post" action="' . base_url('opac/profil/' . $tujuan . '/ubah') . '">' . csrf_field()
                . '<input type="hidden" name="catalog_id" value="' . $id . '">'
                . '<button type="submit" class="dm-icon-btn' . ($aktif ? ' aktif' : '') . '" aria-pressed="' . ($aktif ? 'true' : 'false') . '" aria-label="' . esc($label, 'attr') . '" title="' . esc($label, 'attr') . '">' . dm_ikon($ikon) . '</button></form>';
        };

        ob_start(); ?>
        <article class="dm-book">
            <div class="dm-book-top">
                <span class="dm-chip<?= $tersedia > 0 ? '' : ' habis' ?>"><?= $tersedia > 0 ? 'Tersedia' : 'Dipinjam' ?> <?= $tersedia ?>/<?= $total ?></span>
                <?php if ($urutan !== null) : ?><span class="dm-rank">#<?= $urutan ?></span><?php endif; ?>
                <?= dm_cover($b) ?>
            </div>
            <div class="dm-info">
                <div class="dm-meta"><span><?= esc($subjek ?: 'Koleksi') ?></span><span><?= esc(trim((string) ($b['PublishYear'] ?? ''))) ?></span></div>
                <h3><?= esc($b['Title']) ?></h3>
                <p><?= esc($penulis ?: 'Anonim') ?></p>
                <div class="dm-actions">
                    <a class="dm-btn" href="<?= base_url('opac/detail/' . $id) ?>">Lihat</a>
                    <?php /* Favorit dinonaktifkan sementara: peminjaman mandiri belum dibuka untuk umum.
                    echo $aksi('favorit', in_array($id, $favorit, true), 'favorit', 'Hapus dari Favorit', 'Simpan ke Favorit'); */ ?>
                    <?= $aksi('keranjang', in_array($id, $keranjang, true), 'keranjang', 'Keluarkan dari Keranjang', 'Masukkan ke Keranjang') ?>
                </div>
            </div>
        </article>
        <?php

        return ob_get_clean();
    }
}
