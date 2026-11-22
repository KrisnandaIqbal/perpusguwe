<?php
/**
 * Kartu anggota Perpustakaan Kemnaker, sisi depan dan belakang.
 * Ukuran dan posisi mengikuti desain Figma (kartu 342 x 216 px).
 *
 * @var array  $cards   nama, member_no, berlaku, qr (matriks boolean)
 * @var array  $skipped nama anggota yang tidak dicetak
 * @var string $logo    URL logo Kemnaker
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kartu Anggota</title>
    <style>
        <?php foreach ([500 => 'Medium', 700 => 'Bold', 800 => 'ExtraBold'] as $tebal => $nama): ?>
        @font-face { font-family: "Plus Jakarta Sans"; font-weight: <?= $tebal ?>; font-display: swap;
            src: url("<?= base_url('assets/fonts/plus-jakarta-sans/PlusJakartaSans-' . $nama . '.ttf') ?>") format("truetype"); }
        <?php endforeach; ?>

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "Plus Jakarta Sans", "Segoe UI", Arial, sans-serif;
            background: #E5DFD5;
            color: #0D1C2E;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        a { color: inherit; }
        :focus-visible { outline: 3px solid #B8943F; outline-offset: 2px; }

        /* --- Bilah atas, hanya di layar --- */
        .toolbar { position: sticky; top: 0; z-index: 10; background: #0D1C2E; color: #F0EBE1; }
        .toolbar-inner { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 20px; max-width: 1100px; margin: 0 auto; padding: 12px 20px; }
        .back-link { display: inline-flex; align-items: center; min-height: 44px; font-size: 14px; font-weight: 700; text-decoration: none; opacity: 0.75; }
        .back-link:hover { opacity: 1; }
        .heading { flex: 1 1 auto; min-width: 0; padding-left: 20px; border-left: 1px solid rgba(240, 235, 225, 0.2); }
        .heading h1 { font-size: 17px; font-weight: 800; line-height: 1.3; }
        .heading p { font-size: 13px; opacity: 0.65; }
        .actions { display: flex; align-items: center; gap: 8px; }
        .btn { display: inline-flex; align-items: center; min-height: 44px; padding: 0 20px; border: 0; border-radius: 6px;
            background: #F0EBE1; color: #0D1C2E; font: inherit; font-size: 14px; font-weight: 700; cursor: pointer; }
        .btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .btn-ghost { background: transparent; color: #F0EBE1; box-shadow: inset 0 0 0 1px rgba(240, 235, 225, 0.35); }

        /* Tips cetak dibuka sebagai panel kecil di bawah tombolnya */
        .tips { position: relative; }
        .tips summary { list-style: none; }
        .tips summary::-webkit-details-marker { display: none; }
        .tips[open] summary { background: rgba(240, 235, 225, 0.12); }
        /* Tanda tanya membedakan tombol bantuan ini dari tombol Cetak di sebelahnya */
        .tips-icon { display: inline-grid; place-items: center; width: 20px; height: 20px; margin-right: 8px; border: 1.5px solid currentColor;
            border-radius: 50%; font-size: 12px; font-weight: 800; line-height: 1; }
        .tips-panel { position: absolute; right: 0; top: calc(100% + 8px); width: 300px; padding: 16px 18px; border-radius: 8px;
            background: #fff; color: #0D1C2E; font-size: 13px; line-height: 1.5; box-shadow: 0 12px 32px rgba(13, 28, 46, 0.25); }
        .tips-panel ol { padding-left: 18px; }
        .tips-panel li + li { margin-top: 6px; }
        .tips-panel p { margin-top: 10px; opacity: 0.65; }

        .notices { display: flex; flex-direction: column; gap: 12px; max-width: 1100px; margin: 24px auto 0; padding: 0 20px; }
        .notice { padding: 12px 16px; border-left: 4px solid; border-radius: 6px; font-size: 14px; line-height: 1.5; }
        .notice-warn { border-color: #B8943F; background: #FBF4E2; }
        .notice-error { border-color: #A33A2B; background: #F8E6E2; }
        .notice strong { font-weight: 800; }
        .notice ul { display: flex; flex-wrap: wrap; gap: 6px 16px; margin-top: 4px; list-style: none; }
        .notice ul a { font-weight: 700; }

        .empty { max-width: 480px; margin: 64px auto; padding: 0 20px; text-align: center; font-size: 15px; line-height: 1.6; }
        .empty a { font-weight: 700; }

        .sheet { display: flex; flex-direction: column; gap: 24px; max-width: 1100px; margin: 0 auto; padding: 24px 20px 64px; }

        /* Satu anggota: judul, lalu sisi depan dan belakang berdampingan */
        .entry { padding: 20px 24px 24px; border-radius: 12px; background: rgba(255, 255, 255, 0.45); }
        .entry-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin-bottom: 16px; font-size: 14px; }
        /* Teks pendamping memakai warna redup tetap (kontras sekitar 6:1), bukan opasitas */
        .entry-head .urut { font-weight: 800; color: #4D5A6A; font-variant-numeric: tabular-nums; }
        .entry-head .nama { font-size: 16px; font-weight: 800; }
        .entry-head .no { color: #4D5A6A; font-variant-numeric: tabular-nums; }
        .entry-head .badge { padding: 2px 8px; border-radius: 999px; background: #FBF4E2; color: #7A5F1F; font-size: 12px; font-weight: 700; }
        .entry-head .edit { margin-left: auto; font-size: 13px; font-weight: 700; }

        .pair { display: flex; flex-wrap: wrap; gap: 24px; }
        .side { margin: 0; }
        .side figcaption { margin-bottom: 8px; font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #4D5A6A; }

        /* Kartu digambar pada 342 x 216 px lalu diskalakan lewat zoom */
        .slot { zoom: 1.4; }
        @media (max-width: 1100px) { .slot { zoom: 1.15; } }
        @media (max-width: 900px) { .heading { flex-basis: 100%; order: 3; padding-left: 0; border-left: 0; } .actions { margin-left: auto; } }
        /* Di HP bilah atas ikut tergulir supaya tidak memakan layar, dan tombol mengisi satu baris */
        @media (max-width: 600px) {
            .toolbar { position: static; }
            .toolbar-inner { gap: 8px 12px; }
            .back-link { min-height: 36px; }
            .heading h1 { font-size: 15px; }
            .actions { flex-basis: 100%; margin-left: 0; order: 4; }
            .actions > .btn { flex: 1 1 auto; justify-content: center; }
            /* Tombol Tips ada di tepi kiri, jadi panelnya diratakan ke kiri agar tetap di dalam layar */
            .tips-panel { left: 0; right: auto; width: min(300px, calc(100vw - 32px)); }
        }
        @media (max-width: 480px) {
            .slot { zoom: 0.92; }
            .toolbar-inner, .notices, .sheet { padding-left: 16px; padding-right: 16px; }
            .entry { padding: 16px; }
        }
        .card { position: relative; width: 342px; height: 216px; border-radius: 12px; overflow: hidden; background: #fff;
            box-shadow: 0 12px 48px 0 rgba(27, 58, 92, 0.20), 0 2px 8px 0 rgba(0, 0, 0, 0.08); }
        .card img { display: block; }
        .abs { position: absolute; }
        .label { font-weight: 700; text-transform: uppercase; white-space: nowrap; }

        /* Dua sisi putih berkepala sama: logo, nama perpustakaan, dan garis tiga warna Kemnaker */
        .kepala { left: 20px; top: 18px; display: flex; flex-direction: column; gap: 7px; color: #17406B; }
        .kepala .baris-logo { display: flex; align-items: center; gap: 6px; }
        .kepala .logo { width: 24px; height: 24px; object-fit: contain; }
        .kepala .judul { font-size: 7.5px; font-weight: 800; line-height: 1.2; letter-spacing: 0.08em; }
        .kepala .sub { margin-top: 1px; font-size: 5.5px; font-weight: 500; line-height: 1.3; color: #4A5A6B; }
        .kepala .garis { display: flex; gap: 2px; }
        .kepala .garis i { display: block; height: 2px; border-radius: 1px; }
        .kepala .garis i:nth-child(1) { width: 32px; background: #17406B; }
        .kepala .garis i:nth-child(2) { width: 20px; background: #2E7BC4; }
        .kepala .garis i:nth-child(3) { width: 12px; background: #D4A437; }

        /* --- Sisi depan --- */
        /* Foto ruang baca di kanan, dipotong lingkaran besar; dua cincin emas dan biru dibuat dari box-shadow */
        .front .lengkung { left: 165px; top: -95px; width: 520px; height: 520px; border-radius: 50%; overflow: hidden; background: #17406B;
            box-shadow: 0 0 0 3.5px #D4A437, 0 0 0 7px #2E7BC4; }
        .front .lengkung img { position: absolute; left: 0; top: 95px; width: 177px; height: 216px; object-fit: cover; object-position: 72% 50%; }

        /* Foto, nama, dan nomor ditumpuk rata kiri; jarak foto ke nama 8 px, nama ke nomor 4 px; tinggi kolom ikut jumlah baris nama */
        .front .kolom { left: 20px; top: 56px; width: 132px; display: flex; flex-direction: column; align-items: flex-start; gap: 8px; }
        .front .foto { width: 54px; height: 72px; flex: none; border-radius: 4px; object-fit: cover; object-position: center top; }
        .front .foto.kosong { background: #E8EEF4; box-shadow: inset 0 0 0 0.5px #C9D4E0; }
        .front .nama { width: 100%; }
        /* Pemecahan baris dan singkatan diatur namaKartuBaris(); elipsis hanya jaga-jaga kalau huruf lebih lebar dari perkiraan */
        /* Padding bawah dibalas margin negatif: batas potong overflow turun 2 px supaya ekor g, j, y tidak terpotong, posisi baris tetap */
        .front .nama .baris { display: block; font-size: 12px; font-weight: 800; line-height: 1.2; color: #0D1C2E;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-bottom: 2px; margin-bottom: -2px; }
        .front .nomor { margin-top: -4px; font-size: 9px; font-weight: 700; line-height: 1.3; letter-spacing: 0.1em; color: #5B6878; }
        /* Satu modul selebar 1 px, jadi lebarnya ikut isi nomor; lebar diatur di markup, mengecil kalau lebih dari kolom 132 px */
        .front .barcode { left: 20px; bottom: 14px; height: 14px; }

        /* --- Sisi belakang --- */
        .back .scan { left: 0; right: 0; top: 61px; display: flex; flex-direction: column; align-items: center; gap: 4px; }
        /* Putih polos tanpa bingkai: tepi kosong kartu sudah cukup lebar untuk alat pemindai */
        .back .qr { width: 88px; height: 88px; }
        .back .qr path { fill: #0D1C2E; }
        .back .scan .no { font-size: 6px; font-weight: 700; line-height: 1.4; letter-spacing: 0.2em; color: #0D1C2E; }
        .back .berlaku { left: 20px; bottom: 18px; width: 100px; display: flex; flex-direction: column; gap: 5px; }
        .back .berlaku .label { font-size: 5.5px; line-height: 1.4; letter-spacing: 0.12em; color: #5B6878; }
        .back .berlaku .value { margin-top: 1px; font-size: 6.5px; font-weight: 700; line-height: 1.4; color: #17406B; }
        .back .berlaku .petunjuk { font-size: 5.5px; font-weight: 500; line-height: 1.4; color: #5B6878; }

        @page { size: A4 portrait; margin: 10mm; }

        @media print {
            body { background: #fff; }
            .toolbar, .notices, .entry-head, .side figcaption { display: none; }
            .sheet { gap: 6mm; max-width: none; padding: 0; }
            .entry { padding: 0; background: none; }
            .pair { flex-wrap: nowrap; gap: 6mm; break-inside: avoid; }
            /* 342 px menjadi 85,6 mm (ukuran kartu standar CR80) */
            .slot { zoom: 0.946; }
            .card { box-shadow: none; }
        }
    </style>
</head>
<body>
    <?php
    $jumlah    = count($cards);
    $tanpaFoto = array_filter($cards, fn ($card) => !$card['foto']);
    $editUrl   = fn ($card) => $card['id'] ? base_url('anggota/edit/' . $card['id']) : null;
    ?>
    <header class="toolbar">
        <div class="toolbar-inner">
            <a class="back-link" href="<?= base_url('anggota') ?>">&larr; Daftar Anggota</a>
            <div class="heading">
                <h1>Cetak Kartu Anggota</h1>
                <?php if ($jumlah): ?><p><?= $jumlah ?> kartu &middot; depan dan belakang &middot; 85,6 &times; 54 mm</p><?php endif; ?>
            </div>
            <?php // Tanpa kartu tidak ada yang bisa dicetak, jadi tombol cetak dan tipsnya tidak ditampilkan ?>
            <?php if ($jumlah): ?>
            <div class="actions">
                <details class="tips">
                    <summary class="btn btn-ghost"><span class="tips-icon" aria-hidden="true">?</span>Tips cetak</summary>
                    <div class="tips-panel">
                        <ol>
                            <li>Pilih skala <strong>100%</strong> (bukan "Fit to page").</li>
                            <li>Centang <strong>Background graphics</strong> supaya warna kartu ikut tercetak.</li>
                            <li>Margin biarkan <strong>Default</strong>.</li>
                        </ol>
                        <p>Kartu tercetak seukuran kartu ATM (85,6 &times; 54 mm), depan dan belakang berdampingan.</p>
                    </div>
                </details>
                <button type="button" class="btn" onclick="window.print()">Cetak <?= $jumlah ?> kartu</button>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <?php if (!empty($skipped) || !empty($tanpaFoto)): ?>
        <div class="notices">
            <?php if (!empty($skipped)): ?>
                <div class="notice notice-error" role="status">
                    <strong>Ada <?= count($skipped) ?> anggota yang tidak dicetak</strong> karena belum berstatus Aktif atau belum punya No. Anggota.
                    Setujui dulu di Daftar Anggota.
                    <ul><?php foreach ($skipped as $nama): ?><li><?= esc($nama) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($tanpaFoto)): ?>
                <div class="notice notice-warn" role="status">
                    <strong>Ada <?= count($tanpaFoto) ?> kartu yang dicetak tanpa foto.</strong>
                    Unggah fotonya di data anggota, lalu muat ulang halaman ini.
                    <ul>
                        <?php foreach ($tanpaFoto as $card): ?>
                            <li><?php if ($editUrl($card)): ?><a href="<?= $editUrl($card) ?>"><?= esc($card['nama']) ?></a><?php else: ?><?= esc($card['nama']) ?><?php endif; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!$jumlah): ?>
        <div class="empty">
            <p><?= esc($pesan ?? 'Tidak ada kartu yang bisa dicetak dari anggota yang dipilih.') ?></p>
            <p><a href="<?= base_url('anggota') ?>">Kembali ke Daftar Anggota</a></p>
        </div>
    <?php endif; ?>

    <main class="sheet">
        <?php foreach ($cards as $i => $card): ?>
            <?php
            $size = count($card['qr']);
            $path = '';
            foreach ($card['qr'] as $y => $row) {
                foreach ($row as $x => $dark) {
                    if ($dark) {
                        $path .= "M{$x} {$y}h1v1h-1z";
                    }
                }
            }
            ?>
            <?php
            // Garis barcode digambar sebagai satu path SVG, seperti kode QR
            $barcode = '';
            foreach ($card['barcode']['bar'] as [$bx, $bw]) {
                $barcode .= "M{$bx} 0h{$bw}v1h-{$bw}z";
            }

            // Kepala yang sama di dua sisi kartu
            $kepala = '<div class="abs kepala">'
                . '<div class="baris-logo"><img class="logo" src="' . esc($logo, 'attr') . '" alt="Logo Kemnaker">'
                . '<div><div class="judul">PERPUSTAKAAN</div><div class="sub">Kementerian Ketenagakerjaan RI</div></div></div>'
                . '<div class="garis" aria-hidden="true"><i></i><i></i><i></i></div></div>';
            ?>
            <section class="entry" aria-label="Kartu <?= esc($card['nama'], 'attr') ?>">
            <div class="entry-head">
                <span class="urut"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <span class="nama"><?= esc($card['nama']) ?></span>
                <span class="no"><?= esc($card['member_no']) ?></span>
                <?php if (!$card['foto']): ?><span class="badge">Tanpa foto</span><?php endif; ?>
                <?php if ($editUrl($card)): ?><a class="edit" href="<?= $editUrl($card) ?>">Lihat data</a><?php endif; ?>
            </div>
            <div class="pair">
                <figure class="side">
                <figcaption>Depan</figcaption>
                <div class="slot">
                    <div class="card front">
                        <div class="abs lengkung"><img src="<?= base_url('assets/home/hero/01-ruang-baca.jpg') ?>" alt=""></div>

                        <?= $kepala ?>

                        <div class="abs kolom">
                            <?php if ($card['foto']): ?>
                                <img class="foto" src="<?= esc($card['foto'], 'attr') ?>" alt="Foto <?= esc($card['nama'], 'attr') ?>">
                            <?php else: ?>
                                <div class="foto kosong"></div>
                            <?php endif; ?>

                            <div class="nama"><?php foreach ($card['nama_baris'] as $baris): ?><span class="baris"><?= esc($baris) ?></span><?php endforeach; ?></div>
                            <div class="nomor"><?= esc($card['member_no']) ?></div>
                        </div>

                        <svg class="abs barcode" style="width: <?= min(132, $card['barcode']['lebar']) ?>px" viewBox="0 0 <?= $card['barcode']['lebar'] ?> 1" preserveAspectRatio="none" shape-rendering="crispEdges" role="img" aria-label="Barcode No. Anggota <?= esc($card['member_no'], 'attr') ?>">
                            <path fill="#0D1C2E" d="<?= $barcode ?>"/>
                        </svg>
                    </div>
                </div>
                </figure>

                <figure class="side">
                <figcaption>Belakang</figcaption>
                <div class="slot">
                    <div class="card back">
                        <?= $kepala ?>

                        <div class="abs scan">
                            <svg class="qr" viewBox="0 0 <?= $size ?> <?= $size ?>" shape-rendering="crispEdges" role="img" aria-label="Kode QR No. Anggota <?= esc($card['member_no'], 'attr') ?>">
                                <path d="<?= $path ?>"/>
                            </svg>
                            <div class="no"><?= esc($card['member_no']) ?></div>
                        </div>

                        <div class="abs berlaku">
                            <div>
                                <div class="label">Berlaku s/d</div>
                                <div class="value"><?= esc($card['berlaku']) ?></div>
                            </div>
                            <div class="petunjuk">Pindai kode QR di meja layanan</div>
                        </div>
                    </div>
                </div>
                </figure>
            </div>
            </section>
        <?php endforeach; ?>
    </main>

    <script>
        // Panel Tips cetak tertutup dengan Escape atau klik di luar panel
        (function () {
            var tips = document.querySelector('.tips');
            if (!tips) return;

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && tips.open) {
                    tips.open = false;
                    tips.querySelector('summary').focus();
                }
            });

            document.addEventListener('click', function (e) {
                if (tips.open && !tips.contains(e.target)) tips.open = false;
            });
        })();
    </script>
</body>
</html>
