<?php
/**
 * Kartu anggota untuk lampiran email, dirender dompdf: halaman 1 sisi depan, halaman 2 sisi belakang.
 * Tata letak dan desain mengikuti kartu-anggota.php (Figma terbaru), termasuk foto anggota.
 *
 * @var array  $card            nama, foto, foto_path, member_no, nomor_depan, nomor_urut, berlaku, qr (matriks boolean)
 * @var string $logo            path file logo Kemnaker (simbol)
 * @var string $logo_krem       path file logo versi krem untuk sisi belakang
 * @var string $logo_teks       path file logo Kemnaker dengan teks (depan)
 * @var string $logo_teks_putih path file logo Kemnaker teks versi putih/krem (belakang)
 * @var string $fonts           folder file TTF Plus Jakarta Sans
 */

// Desain 342 x 216 px dicetak selebar 85,6 mm (CR80)
$pt = static fn (float $px): string => round($px * 242.65 / 342, 2) . 'pt';

// dompdf mengalikan line-height dengan tinggi font (1,26 em untuk Plus Jakarta Sans), jadi nilainya dibagi dulu
$lh = static fn (float $px): string => $pt($px / 1.26);

$size = count($card['qr']);
$path = '';
foreach ($card['qr'] as $y => $row) {
    foreach ($row as $x => $dark) {
        if ($dark) {
            $path .= "M{$x} {$y}h1v1h-1z";
        }
    }
}
$qr = 'data:image/svg+xml;base64,' . base64_encode(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" shape-rendering="crispEdges"><path fill="#0D1C2E" d="' . $path . '"/></svg>'
);

// Foto diubah menjadi base64 agar dompdf dapat memuat gambar secara langsung dan stabil
$fotoSrc = null;
if (!empty($card['foto_path']) && is_file($card['foto_path'])) {
    $mime = mime_content_type($card['foto_path']) ?: 'image/jpeg';
    $fotoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($card['foto_path']));
} elseif (!empty($card['foto']) && is_string($card['foto'])) {
    if (str_starts_with($card['foto'], 'data:image')) {
        $fotoSrc = $card['foto'];
    } elseif (is_file($card['foto'])) {
        $mime = mime_content_type($card['foto']) ?: 'image/jpeg';
        $fotoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($card['foto']));
    }
}

$logoFront = !empty($logo_teks) && file_exists($logo_teks) ? $logo_teks : $logo;
$logoBack  = !empty($logo_teks_putih) && file_exists($logo_teks_putih) ? $logo_teks_putih : (!empty($logo_krem) ? $logo_krem : $logo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Anggota <?= esc($card['member_no']) ?></title>
    <style>
        @page { size: 242.65pt 153.25pt; margin: 0; }
        /* dompdf hanya mengenal tebal normal dan bold, jadi tiap ketebalan didaftarkan sebagai keluarga font sendiri */
        @font-face { font-family: "PJS Medium"; font-weight: normal; src: url("<?= $fonts ?>PlusJakartaSans-Medium.ttf") format("truetype"); }
        @font-face { font-family: "PJS Bold"; font-weight: normal; src: url("<?= $fonts ?>PlusJakartaSans-Bold.ttf") format("truetype"); }
        @font-face { font-family: "PJS ExtraBold"; font-weight: normal; src: url("<?= $fonts ?>PlusJakartaSans-ExtraBold.ttf") format("truetype"); }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "PJS Bold", Helvetica, sans-serif; font-weight: normal; }
        .value { font-family: "PJS ExtraBold", Helvetica, sans-serif; }

        .card { position: relative; width: <?= $pt(342) ?>; height: <?= $pt(216) ?>; overflow: hidden; }
        .abs { position: absolute; }
        .label { text-transform: uppercase; white-space: nowrap; font-family: "PJS Bold", Helvetica, sans-serif; }

        /* --- Sisi Depan --- */
        .front { background: #F0EBE1; color: #0D1C2E; page-break-after: always; }
        .front .motif { left: <?= $pt(168) ?>; top: <?= $pt(3) ?>; width: <?= $pt(210) ?>; height: <?= $pt(210) ?>; opacity: 0.85; }
        .front .logo { left: <?= $pt(18) ?>; top: <?= $pt(14) ?>; width: <?= $pt(26.65) ?>; height: <?= $pt(36) ?>; }

        /* Foto anggota dengan rasio 2:3 */
        .front .foto-box { left: <?= $pt(31.5) ?>; top: <?= $pt(60) ?>; width: <?= $pt(42.67) ?>; height: <?= $pt(64) ?>; border-radius: <?= $pt(3) ?>; overflow: hidden; }
        .front .foto-box img { width: 100%; height: 100%; display: block; }

        <?php
        // Nama satu kata dibesarkan (tetap di bawah nomor 18 px); naik 4 px supaya jaraknya ke nomor tetap lega
        $pendek  = !empty($card['nama_pendek']);
        $namaPx  = $pendek ? ($card['nomor_urut'] ? 16 : 13) : 11;
        ?>
        .front .nama { left: <?= $pt(18) ?>; top: <?= $pt($pendek ? 130 : 134) ?>; width: <?= $pt(145) ?>; }
        .front .nama .label { font-size: <?= $pt(7) ?>; line-height: <?= $lh(10.5) ?>; letter-spacing: <?= $pt(1.12) ?>; color: #8A8B8A; }
        .front .nama .value { padding-top: <?= $pt(3) ?>; font-size: <?= $pt($namaPx) ?>; line-height: <?= $lh($namaPx * 1.2) ?>; white-space: nowrap; overflow: hidden; color: #0D1C2E; }

        .front .nomor { left: <?= $pt(19) ?>; top: <?= $pt(171) ?>; }
        .front .nomor .label { font-size: <?= $pt(7) ?>; line-height: <?= $lh(7) ?>; letter-spacing: <?= $pt(1.26) ?>; color: #7F8286; }
        .front .nomor .value { padding-top: <?= $pt($card['nomor_urut'] ? 4 : 8) ?>; font-size: <?= $pt($card['nomor_urut'] ? 18 : 13) ?>;
            line-height: <?= $lh($card['nomor_urut'] ? 18 : 18) ?>; letter-spacing: <?= $pt($card['nomor_urut'] ? -0.18 : 0) ?>; color: #0D1C2E; }

        .front .tegak { right: <?= $pt(13.5) ?>; top: <?= $pt(18) ?>; width: <?= $pt(6) ?>; font-size: <?= $pt(5.5) ?>; line-height: <?= $lh(6) ?>;
            text-align: center; color: #A09F9E; }

        /* --- Sisi Belakang --- */
        .back { background: #0D1C2E; color: #F0EBE1; }
        .back .motif { left: <?= $pt(71) ?>; top: <?= $pt(8) ?>; width: <?= $pt(200) ?>; height: <?= $pt(200) ?>; opacity: 0.14; }
        .back .scan { left: <?= $pt(45) ?>; top: <?= $pt(60) ?>; width: <?= $pt(80) ?>; }
        .back .qr-box { width: <?= $pt(80) ?>; height: <?= $pt(80) ?>; padding: <?= $pt(4) ?>; border-radius: <?= $pt(6) ?>; background: #F0EBE1; }
        .back .qr-box img { width: <?= $pt(72) ?>; height: <?= $pt(72) ?>; display: block; }
        .back .scan .label { padding-top: <?= $pt(6) ?>; font-size: <?= $pt(7) ?>; line-height: <?= $lh(10.5) ?>; letter-spacing: <?= $pt(0.98) ?>;
            text-align: center; color: #ACABAB; }

        .back .logo { right: <?= $pt(16.2) ?>; top: <?= $pt(24) ?>; width: <?= $pt(26.65) ?>; height: <?= $pt(36) ?>; }

        .back .info { right: <?= $pt(16.2) ?>; text-align: right; }
        .back .info .label { font-size: <?= $pt(6) ?>; line-height: <?= $lh(9) ?>; letter-spacing: <?= $pt(0.96) ?>; color: #ACABAB; }
        .back .berlaku { top: <?= $pt(70) ?>; width: <?= $pt(132) ?>; }
        .back .berlaku .value { padding-top: <?= $pt(2) ?>; font-size: <?= $pt(11) ?>; line-height: <?= $lh(16.5) ?>; letter-spacing: <?= $pt(0.22) ?>; color: #B8943F; }

        .back .perpus { top: <?= $pt(115) ?>; width: <?= $pt(140) ?>; }
        .back .perpus .label { letter-spacing: <?= $pt(0.84) ?>; }
        .back .perpus .value { font-family: "PJS Medium", Helvetica, sans-serif; font-size: <?= $pt(6.5) ?>; line-height: <?= $lh(10.4) ?>; color: #CEC9C3; white-space: nowrap; }

        .back .nomor { top: <?= $pt(162) ?>; width: <?= $pt(140) ?>; }
        .back .nomor .value { padding-top: <?= $pt(2) ?>; font-size: <?= $pt(15) ?>; line-height: <?= $lh(20) ?>; letter-spacing: <?= $pt(0.3) ?>; color: #F0EBE1; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="card front">
        <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">
        <img class="abs logo" src="<?= esc($logoFront, 'attr') ?>" alt="Logo Kemnaker">

        <?php if ($fotoSrc): ?>
            <div class="abs foto-box">
                <img src="<?= $fotoSrc ?>" alt="Foto <?= esc($card['nama'], 'attr') ?>">
            </div>
        <?php endif; ?>

        <div class="abs nama">
            <div class="label">Nama</div>
            <div class="value"><?= esc($card['nama']) ?></div>
        </div>

        <div class="abs nomor">
            <div class="label">No. Anggota</div>
            <div class="value"><?= esc($card['nomor_depan']) ?></div>
        </div>

        <div class="abs tegak"><?= implode('<br>', str_split('PERPUSTAKAAN')) ?></div>
    </div>

    <div class="card back">
        <img class="abs motif" src="<?= esc($logo, 'attr') ?>" alt="">

        <div class="abs scan">
            <div class="qr-box"><img src="<?= $qr ?>" alt="Kode QR No. Anggota <?= esc($card['member_no'], 'attr') ?>"></div>
            <div class="label">QR Scan</div>
        </div>

        <img class="abs logo" src="<?= esc($logoBack, 'attr') ?>" alt="">

        <div class="abs info berlaku">
            <div class="label">Berlaku s/d</div>
            <div class="value"><?= esc($card['berlaku']) ?></div>
        </div>

        <div class="abs info perpus">
            <div class="label">Perpustakaan Kemnaker RI</div>
            <div class="value">Jl. Jend. Gatot Subroto Kav. 51<br>Jakarta Selatan &middot; (021) 520-7797</div>
        </div>

        <div class="abs info nomor">
            <div class="label">No. Anggota</div>
            <div class="value"><?= esc($card['member_no']) ?></div>
        </div>
    </div>
</body>
</html>
