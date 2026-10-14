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

        /* --- Bilah atas, hanya di layar --- */
        .toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; padding: 14px 20px; background: #0D1C2E; color: #F0EBE1; font-size: 14px; }
        .toolbar button { min-height: 44px; padding: 0 20px; border: 0; border-radius: 6px; background: #F0EBE1; color: #0D1C2E; font: inherit; font-weight: 700; cursor: pointer; }
        .toolbar button:focus-visible { outline: 3px solid #B8943F; outline-offset: 2px; }
        .notice { margin: 16px 20px 0; padding: 12px 16px; border-radius: 6px; background: #fff; font-size: 14px; line-height: 1.5; }

        .sheet { display: flex; flex-direction: column; gap: 24px; padding: 24px 20px 48px; }
        .pair { display: flex; flex-wrap: wrap; gap: 24px; }

        /* Kartu digambar pada 342 x 216 px lalu diskalakan lewat zoom */
        .slot { zoom: 1.5; }
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
        /* Foto mulai dari tengah tulisan KEMNAKER pada logo */
        .front .kolom > .foto { margin-left: 13.5px; }

        /* Foto mengisi sisa tinggi kolom, lebarnya mengikuti rasio pas foto 2:3 */
        .front .foto { flex: 1 1 0; min-height: 0; aspect-ratio: 2 / 3; border-radius: 3px; object-fit: cover; object-position: center top; }
        /* Tanpa foto: nama dan nomor tetap di bawah, ruang di bawah logo dibiarkan kosong */
        .front .tanpa-foto .nama { margin-top: auto; }

        /* Berhenti sebelum motif besar yang mulai di 168 px */
        .front .nama { max-width: 150px; }
        .front .nama .label { font-size: 7px; line-height: 10.5px; letter-spacing: 1.12px; opacity: 0.4; }
        .front .nama .value { padding-top: 3px; font-size: 11px; font-weight: 800; line-height: 13.2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .front .kolom > .nomor { margin-left: 1px; }
        .front .nomor { display: inline-flex; flex-direction: column; align-items: center; }
        .front .nomor .label { align-self: flex-start; font-size: 7px; line-height: 7px; letter-spacing: 1.26px; opacity: 0.45; }
        .front .nomor .value { padding-top: 4px; font-size: 18px; font-weight: 800; line-height: 18px; letter-spacing: -0.18px; }
        .front .nomor.is-full { align-items: flex-start; }
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
            .toolbar, .notice { display: none; }
            .sheet { gap: 6mm; padding: 0; }
            .pair { flex-wrap: nowrap; gap: 6mm; break-inside: avoid; }
            /* 342 px menjadi 85,6 mm (ukuran kartu standar CR80) */
            .slot { zoom: 0.946; }
            .card { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()" <?= empty($cards) ? 'disabled' : '' ?>>Cetak <?= count($cards) ?> kartu</button>
        <span>Di dialog cetak, pilih skala 100% dan aktifkan "Background graphics". Kartu tercetak 85,6 x 54 mm.</span>
    </div>

    <?php if (!empty($skipped)): ?>
        <p class="notice">
            Tidak dicetak karena belum berstatus Aktif atau belum punya No. Anggota:
            <strong><?= esc(implode(', ', $skipped)) ?></strong>.
            Setujui dulu anggotanya di Daftar Anggota.
        </p>
    <?php endif; ?>

    <?php $tanpaFoto = array_column(array_filter($cards, fn ($card) => !$card['foto']), 'nama'); ?>
    <?php if (!empty($tanpaFoto)): ?>
        <p class="notice">
            Dicetak tanpa foto karena belum mengunggah foto:
            <strong><?= esc(implode(', ', $tanpaFoto)) ?></strong>.
            Kalau ingin kartunya berfoto, unggah fotonya dulu di data anggota lalu cetak ulang.
        </p>
    <?php endif; ?>

    <?php if (empty($cards)): ?>
        <p class="notice">Tidak ada kartu yang bisa dicetak dari anggota yang dipilih.</p>
    <?php endif; ?>

    <main class="sheet">
        <?php foreach ($cards as $card): ?>
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
            <section class="pair">
                <div class="slot">
                    <div class="card front">
                        <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">
                        <div class="abs kolom <?= $card['foto'] ? '' : 'tanpa-foto' ?>">
                            <img class="logo" src="<?= base_url('assets/img/kartu/logo-kemnaker-teks.webp') ?>" alt="Logo Kemnaker">

                            <?php if ($card['foto']): ?>
                                <img class="foto" src="<?= esc($card['foto'], 'attr') ?>" alt="Foto <?= esc($card['nama'], 'attr') ?>">
                            <?php endif; ?>

                            <div class="nama">
                                <div class="label">Nama</div>
                                <div class="value"><?= esc($card['nama']) ?></div>
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
            </section>
        <?php endforeach; ?>
    </main>
</body>
</html>
