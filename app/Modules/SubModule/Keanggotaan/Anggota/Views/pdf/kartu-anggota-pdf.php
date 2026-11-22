<?php
/**
 * Kartu anggota untuk lampiran email, dirender dompdf: halaman 1 sisi depan, halaman 2 sisi belakang.
 * Tata letak dan desain mengikuti kartu-anggota.php: kartu putih, foto ruang baca berlengkung di kanan depan,
 * kode QR di tengah belakang, dan barcode di depan.
 *
 * @var array  $card  nama, foto, foto_path, member_no, berlaku, qr (matriks boolean), barcode
 * @var string $logo  path file logo Kemnaker (simbol)
 * @var string $ruang path file foto ruang baca untuk lengkung di sisi depan
 * @var string $fonts folder file TTF Plus Jakarta Sans
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

// Barcode: satu modul selebar 1 px desain (mengecil kalau lebih lebar dari kolom 132 px), tinggi 14 px
$modul   = min(1.0, 132 / $card['barcode']['lebar']);
$barcode = '';
foreach ($card['barcode']['bar'] as [$bx, $bw]) {
    $barcode .= "M{$bx} 0h{$bw}v14h-{$bw}z";
}
$barcodeSrc = 'data:image/svg+xml;base64,' . base64_encode(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $card['barcode']['lebar'] . ' 14" shape-rendering="crispEdges"><path fill="#0D1C2E" d="' . $barcode . '"/></svg>'
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

// dompdf tidak mengenal object-fit: ukuran asli gambar dipakai untuk memotongnya manual agar memenuhi kotak.
// Foto anggota 54 x 72 dipotong dari tengah atas; foto ruang baca 177 x 216 condong ke kanan (72%).
$potong = static function (?array $ukuran, float $w, float $h, float $geser): array {
    if (!$ukuran || $ukuran[0] <= 0 || $ukuran[1] <= 0) {
        return [$w, $h, 0.0];
    }
    $skala = max($w / $ukuran[0], $h / $ukuran[1]);

    return [$ukuran[0] * $skala, $ukuran[1] * $skala, ($w - $ukuran[0] * $skala) * $geser];
};
$fotoUkuran = $fotoSrc ? @getimagesizefromstring(base64_decode(substr($fotoSrc, strpos($fotoSrc, ',') + 1))) : false;
[$fotoW, $fotoT, $fotoX] = $potong($fotoUkuran ?: null, 54, 72, 0.5);
[$ruangW, $ruangT, $ruangX] = $potong(@getimagesize($ruang) ?: null, 177, 216, 0.72);

$baris = $card['nama_baris'] ?? [$card['nama']];
// Nama (14,4 px per baris) mulai di 136 px, 8 px di bawah foto; nomor 4 px di bawah baris terakhir
$nomorTop = 136 + count($baris) * 14.4 + 4;
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

        /* Sudut membulat 12 px seperti halaman cetak, supaya terlihat seperti kartu fisik; isi ikut terpotong melengkung */
        .card { position: relative; width: <?= $pt(342) ?>; height: <?= $pt(216) ?>; overflow: hidden; border-radius: <?= $pt(12) ?>; background: #FFFFFF; color: #0D1C2E; }
        .front { page-break-after: always; }
        .abs { position: absolute; }
        .nowrap { white-space: nowrap; }

        /* Kepala dua sisi: logo, nama perpustakaan, garis tiga warna Kemnaker */
        .kepala-logo { left: <?= $pt(20) ?>; top: <?= $pt(18) ?>; width: <?= $pt(24) ?>; height: <?= $pt(24) ?>; }
        .judul { left: <?= $pt(50) ?>; top: <?= $pt(21.4) ?>; font-family: "PJS ExtraBold", Helvetica, sans-serif; font-size: <?= $pt(7.5) ?>; line-height: <?= $lh(9) ?>; letter-spacing: <?= $pt(0.6) ?>; color: #17406B; }
        .sub { left: <?= $pt(50) ?>; top: <?= $pt(31.4) ?>; font-family: "PJS Medium", Helvetica, sans-serif; font-size: <?= $pt(5.5) ?>; line-height: <?= $lh(7.15) ?>; color: #4A5A6B; }
        .garis { top: <?= $pt(49) ?>; height: <?= $pt(2) ?>; border-radius: <?= $pt(1) ?>; }

        /* --- Sisi Depan --- */
        /* Lingkaran besar berpusat di (425, 165): cincin biru dan emas dari tiga lingkaran bersusun, foto di yang paling dalam */
        .cincin-biru { left: <?= $pt(158) ?>; top: <?= $pt(-102) ?>; width: <?= $pt(534) ?>; height: <?= $pt(534) ?>; border-radius: <?= $pt(267) ?>; background: #2E7BC4; }
        .cincin-emas { left: <?= $pt(161.5) ?>; top: <?= $pt(-98.5) ?>; width: <?= $pt(527) ?>; height: <?= $pt(527) ?>; border-radius: <?= $pt(263.5) ?>; background: #D4A437; }
        .lengkung { left: <?= $pt(165) ?>; top: <?= $pt(-95) ?>; width: <?= $pt(520) ?>; height: <?= $pt(520) ?>; border-radius: <?= $pt(260) ?>; overflow: hidden; background: #17406B; }
        .lengkung img { position: absolute; left: <?= $pt($ruangX) ?>; top: <?= $pt(95) ?>; width: <?= $pt($ruangW) ?>; height: <?= $pt($ruangT) ?>; }

        .foto-box { left: <?= $pt(20) ?>; top: <?= $pt(56) ?>; width: <?= $pt(54) ?>; height: <?= $pt(72) ?>; border-radius: <?= $pt(4) ?>; overflow: hidden; background: #E8EEF4; }
        .foto-box img { position: absolute; left: <?= $pt($fotoX) ?>; top: 0; width: <?= $pt($fotoW) ?>; height: <?= $pt($fotoT) ?>; }

        .nama { left: <?= $pt(20) ?>; top: <?= $pt(136) ?>; width: <?= $pt(132) ?>; }
        .nama div { font-family: "PJS ExtraBold", Helvetica, sans-serif; font-size: <?= $pt(12) ?>; line-height: <?= $lh(14.4) ?>; white-space: nowrap; overflow: hidden; color: #0D1C2E; }
        .nomor { left: <?= $pt(20) ?>; top: <?= $pt($nomorTop) ?>; font-size: <?= $pt(9) ?>; line-height: <?= $lh(11.7) ?>; letter-spacing: <?= $pt(0.9) ?>; color: #5B6878; }
        .barcode { left: <?= $pt(20) ?>; top: <?= $pt(188) ?>; width: <?= $pt($card['barcode']['lebar'] * $modul) ?>; height: <?= $pt(14 * $modul) ?>; }

        /* --- Sisi Belakang --- */
        .qr { left: <?= $pt((342 - 88) / 2) ?>; top: <?= $pt(61) ?>; width: <?= $pt(88) ?>; height: <?= $pt(88) ?>; }
        .no { left: 0; top: <?= $pt(153) ?>; width: <?= $pt(342) ?>; text-align: center; font-size: <?= $pt(6) ?>; line-height: <?= $lh(8.4) ?>; letter-spacing: <?= $pt(1.2) ?>; color: #0D1C2E; }
        .berlaku-label { left: <?= $pt(20) ?>; top: <?= $pt(167.5) ?>; font-size: <?= $pt(5.5) ?>; line-height: <?= $lh(7.7) ?>; letter-spacing: <?= $pt(0.66) ?>; color: #5B6878; }
        .berlaku-isi { left: <?= $pt(20) ?>; top: <?= $pt(176.2) ?>; font-size: <?= $pt(6.5) ?>; line-height: <?= $lh(9.1) ?>; color: #17406B; }
        .petunjuk { left: <?= $pt(20) ?>; top: <?= $pt(190.3) ?>; font-family: "PJS Medium", Helvetica, sans-serif; font-size: <?= $pt(5.5) ?>; line-height: <?= $lh(7.7) ?>; color: #5B6878; }
    </style>
</head>
<body>
    <div class="card front">
        <div class="abs cincin-biru"></div>
        <div class="abs cincin-emas"></div>
        <div class="abs lengkung"><img src="<?= esc($ruang, 'attr') ?>" alt=""></div>

        <img class="abs kepala-logo" src="<?= esc($logo, 'attr') ?>" alt="Logo Kemnaker">
        <div class="abs nowrap judul">PERPUSTAKAAN</div>
        <div class="abs nowrap sub">Kementerian Ketenagakerjaan RI</div>
        <div class="abs garis" style="left: <?= $pt(20) ?>; width: <?= $pt(32) ?>; background: #17406B"></div>
        <div class="abs garis" style="left: <?= $pt(54) ?>; width: <?= $pt(20) ?>; background: #2E7BC4"></div>
        <div class="abs garis" style="left: <?= $pt(76) ?>; width: <?= $pt(12) ?>; background: #D4A437"></div>

        <div class="abs foto-box">
            <?php if ($fotoSrc): ?><img src="<?= $fotoSrc ?>" alt="Foto <?= esc($card['nama'], 'attr') ?>"><?php endif; ?>
        </div>

        <div class="abs nama">
            <?php foreach ($baris as $isi): ?><div><?= esc($isi) ?></div><?php endforeach; ?>
        </div>
        <div class="abs nowrap nomor"><?= esc($card['member_no']) ?></div>

        <img class="abs barcode" src="<?= $barcodeSrc ?>" alt="Barcode No. Anggota <?= esc($card['member_no'], 'attr') ?>">
    </div>

    <div class="card back">
        <img class="abs kepala-logo" src="<?= esc($logo, 'attr') ?>" alt="Logo Kemnaker">
        <div class="abs nowrap judul">PERPUSTAKAAN</div>
        <div class="abs nowrap sub">Kementerian Ketenagakerjaan RI</div>
        <div class="abs garis" style="left: <?= $pt(20) ?>; width: <?= $pt(32) ?>; background: #17406B"></div>
        <div class="abs garis" style="left: <?= $pt(54) ?>; width: <?= $pt(20) ?>; background: #2E7BC4"></div>
        <div class="abs garis" style="left: <?= $pt(76) ?>; width: <?= $pt(12) ?>; background: #D4A437"></div>

        <img class="abs qr" src="<?= $qr ?>" alt="Kode QR No. Anggota <?= esc($card['member_no'], 'attr') ?>">
        <div class="abs nowrap no"><?= esc($card['member_no']) ?></div>

        <div class="abs nowrap berlaku-label">BERLAKU S/D</div>
        <div class="abs nowrap berlaku-isi"><?= esc($card['berlaku']) ?></div>
        <div class="abs nowrap petunjuk">Pindai kode QR di meja layanan</div>
    </div>
</body>
</html>
