<?php
/**
 * Kartu anggota Perpustakaan Kemnaker, sisi depan dan belakang.
 * Ukuran dan posisi mengikuti desain Figma (kartu 342 x 216 px).
 *
 * @var array  $cards   nama, member_no, nomor_depan, nomor_urut, berlaku, qr (matriks boolean)
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
        .card { position: relative; width: 342px; height: 216px; border-radius: 12px; overflow: hidden;
            box-shadow: 0 12px 48px 0 rgba(27, 58, 92, 0.20), 0 2px 8px 0 rgba(0, 0, 0, 0.08); }
        .card img { display: block; object-fit: contain; }
        .abs { position: absolute; }
        .label { font-weight: 700; text-transform: uppercase; white-space: nowrap; }

        /* --- Sisi depan --- */
        .front { background: #F0EBE1; color: #0D1C2E; }
        /* 210 px dengan jarak 3 px di atas dan bawah kartu (216 px) */
        .front .motif { left: 168px; top: 3px; width: 210px; height: 210px; opacity: 0.85; }
        /* Logo, foto, nama, dan nomor ditumpuk di kiri dengan jarak 10 px yang sama */
        /* Bawah kolom di 200 px, 16 px dari tepi bawah kartu */
        .front .kolom { left: 18px; top: 14px; width: 150px; height: 186px; display: flex; flex-direction: column; align-items: flex-start; gap: 10px; }
        /* Tinggi 36 px membuat simbolnya selebar 24 px, sama dengan logo sisi belakang */
        .front .logo { width: auto; height: 36px; }

        /* Foto persegi mengisi sisa tinggi kolom */
        .front .foto { flex: 1 1 0; min-height: 0; aspect-ratio: 1; border-radius: 3px; object-fit: cover; object-position: center top; }
        /* Tanpa foto: nama dan nomor tetap di bawah, ruang di bawah logo dibiarkan kosong */
        .front .tanpa-foto .nama { margin-top: auto; }

        /* Berhenti sebelum motif besar yang mulai di 168 px */
        .front .nama { max-width: 150px; }
        .front .nama .label { font-size: 7px; line-height: 10.5px; letter-spacing: 1.12px; opacity: 0.4; }
        .front .nama .value { padding-top: 3px; font-size: 16px; font-weight: 800; line-height: 19.2px; letter-spacing: -0.16px; }
        /* Pemecahan baris dan singkatan diatur namaKartuBaris(); elipsis hanya jaga-jaga kalau huruf lebih lebar dari perkiraan */
        /* Padding bawah dibalas margin negatif: batas potong overflow turun 4 px supaya ekor g, j, y tidak terpotong, posisi baris tetap */
        .front .nama .baris { display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-bottom: 4px; margin-bottom: -4px; }
        /* Nama dua baris: jarak baris 18 px supaya tetap terbaca sebagai satu nama */
        .front .nama.is-dua .value { line-height: 18px; }

        .front .kolom > .nomor { margin-left: 1px; }
        .front .nomor { display: inline-flex; flex-direction: column; align-items: flex-start; }
        .front .nomor .label { align-self: flex-start; font-size: 7px; line-height: 7px; letter-spacing: 1.26px; opacity: 0.45; }
        .front .nomor .value { padding-top: 4px; font-size: 18px; font-weight: 800; line-height: 18px; letter-spacing: -0.18px; }
        .front .nomor.is-full .value { padding-top: 8px; font-size: 13px; line-height: 18px; letter-spacing: 0; }

        .front .tegak { right: 13.5px; top: 18px; display: flex; flex-direction: column; align-items: flex-end; gap: 1px;
            font-size: 5.5px; font-weight: 700; line-height: 6px; opacity: 0.35; }

        /* --- Sisi belakang --- */
        .back { background: #0D1C2E; color: #F0EBE1; }
        .back .motif { left: 71px; top: 8px; width: 200px; height: 200px; opacity: 0.14; }

        /* 80 px, lebih besar dari desain (70 px), dengan titik tengah yang sama */
        .back .scan { left: 45px; top: 60px; width: 80px; }
        /* Gelap di atas terang dengan tepi kosong: versi krem di atas navy tidak terbaca alat pemindai */
        .back .qr { display: block; width: 80px; height: 80px; padding: 4px; border-radius: 6px; background: #F0EBE1; }
        .back .qr path { fill: #0D1C2E; }
        .back .scan .label { padding-top: 6px; font-size: 7px; line-height: 10.5px; letter-spacing: 0.98px; text-align: center; opacity: 0.7; }

        .back .info { left: 193.8px; top: 24px; width: 132px; height: 168px; display: flex; flex-direction: column;
            justify-content: space-between; align-items: flex-end; text-align: right; }
        /* Logo dicetak krem lewat mask supaya sisi belakang hanya memakai navy, krem, dan emas */
        /* Ukuran sama dengan logo sisi depan (tinggi 36 px, rasio gambar 1152:1556) */
        .back .info .logo { height: 36px; aspect-ratio: 1152 / 1556; background: #F0EBE1;
            -webkit-mask: var(--logo) center / contain no-repeat; mask: var(--logo) center / contain no-repeat; }
        /* Label diredupkan sedikit supaya nilainya yang terbaca lebih dulu */
        .back .info .label { font-size: 6px; line-height: 9px; letter-spacing: 0.96px; opacity: 0.7; }
        .back .berlaku .value { padding-top: 2px; font-size: 11px; font-weight: 800; line-height: 16.5px; letter-spacing: 0.22px; color: #B8943F; }
        .back .perpus .label { letter-spacing: 0.84px; }
        .back .perpus .value { padding-top: 2px; font-size: 6.5px; font-weight: 500; line-height: 10.4px; opacity: 0.85; white-space: nowrap; }
        .back .nomor .value { padding-top: 2px; font-size: 15px; font-weight: 800; line-height: 20px; letter-spacing: 0.3px; white-space: nowrap; }

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
                        <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">
                        <div class="abs kolom <?= $card['foto'] ? '' : 'tanpa-foto' ?>">
                            <img class="logo" src="<?= base_url('assets/img/kartu/logo-kemnaker-teks.webp') ?>" alt="Logo Kemnaker">

                            <?php if ($card['foto']): ?>
                                <img class="foto" src="<?= esc($card['foto'], 'attr') ?>" alt="Foto <?= esc($card['nama'], 'attr') ?>">
                            <?php endif; ?>

                            <div class="nama <?= count($card['nama_baris']) > 1 ? 'is-dua' : '' ?>">
                                <div class="label">Nama</div>
                                <div class="value"><?php foreach ($card['nama_baris'] as $baris): ?><span class="baris"><?= esc($baris) ?></span><?php endforeach; ?></div>
                            </div>

                            <div class="nomor <?= $card['nomor_urut'] ? '' : 'is-full' ?>">
                                <div class="label">No. Anggota</div>
                                <div class="value"><?= esc($card['nomor_depan']) ?></div>
                            </div>
                        </div>

                        <div class="abs tegak" aria-hidden="true">
                            <?php foreach (str_split('PERPUSTAKAAN') as $huruf): ?><span><?= $huruf ?></span><?php endforeach; ?>
                        </div>
                    </div>
                </div>
                </figure>

                <figure class="side">
                <figcaption>Belakang</figcaption>
                <div class="slot">
                    <div class="card back">
                        <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">

                        <div class="abs scan">
                            <svg class="qr" viewBox="0 0 <?= $size ?> <?= $size ?>" shape-rendering="crispEdges" role="img" aria-label="Kode QR No. Anggota <?= esc($card['member_no'], 'attr') ?>">
                                <path d="<?= $path ?>"/>
                            </svg>
                            <div class="label">QR Scan</div>
                        </div>

                        <div class="abs info">
                            <div class="logo" style="--logo: url('<?= base_url('assets/img/kartu/logo-kemnaker-teks-putih.png') ?>')"></div>

                            <div class="berlaku">
                                <div class="label">Berlaku s/d</div>
                                <div class="value"><?= esc($card['berlaku']) ?></div>
                            </div>

                            <div class="perpus">
                                <div class="label">Perpustakaan Kemnaker RI</div>
                                <div class="value">Jl. Jend. Gatot Subroto Kav. 51<br>Jakarta Selatan &middot; (021) 520-7797</div>
                            </div>

                            <div class="nomor">
                                <div class="label">No. Anggota</div>
                                <div class="value"><?= esc($card['member_no']) ?></div>
                            </div>
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
