<?php

namespace App\Libraries;

/**
 * Kartu anggota sebagai gambar PNG untuk lampiran email, digambar dengan GD.
 *
 * Posisi, ukuran, dan warna mengikuti kartu-anggota-pdf.php (desain 342 x 216 px), digambar 3x
 * supaya tajam (sekitar 300 dpi pada lebar 85,6 mm). Pojok kartu transparan dengan radius 12 px,
 * hal yang tidak bisa dilakukan PDF karena penampil PDF selalu menggambar kertas putih.
 *
 * Desain kartu ada di tiga tempat: kartu-anggota.php (cetak), kartu-anggota-pdf.php (PDF), dan kelas ini.
 * Kalau salah satunya diubah, ubah juga dua lainnya.
 */
class KartuPng
{
    /** Skala gambar terhadap desain 342 x 216 px */
    private const S = 3;
    private const W = 342;
    private const H = 216;

    /** Metrik Plus Jakarta Sans (hhea, per em) untuk menaruh garis dasar teks seperti CSS */
    private const ASCENT  = 1.038;
    private const DESCENT = 0.222;

    private string $aset;

    public function __construct(?string $aset = null)
    {
        $this->aset = rtrim($aset ?? FCPATH . 'assets', '/\\') . '/';
    }

    /** Sisi depan: logo, foto, nama, dan nomor urut. */
    public function depan(array $card): string
    {
        $img = $this->kanvas('#F0EBE1');

        $this->tempel($img, 'img/kartu/logo-kemnaker.png', 168, 3, 210, 210, 0.85);
        $this->tempel($img, 'img/kartu/logo-kemnaker-teks.png', 18, 14, 26.65, 36);

        // Ukuran dan posisi nama sama dengan kartu-anggota-pdf.php
        $baris    = $card['nama_baris'] ?? [$card['nama']];
        $duaBaris = count($baris) > 1;
        $urut     = !empty($card['nomor_urut']);
        $namaLh   = $duaBaris ? 18 : 19.2;
        // Blok nama (label 10,5 + jarak 3 + baris) berakhir 10 px di atas nomor (171)
        $namaTop  = 171 - 10 - (10.5 + 3 + count($baris) * $namaLh);

        if (!empty($card['foto_path']) && is_file($card['foto_path'])) {
            // Foto 2:3 seperti pas foto di pendaftaran, berhenti 10 px di atas nama, jadi lebih kecil kalau nama dua baris
            $fotoH = $namaTop - 10 - 60;
            // Rata kiri dengan logo, nama, dan nomor
            $this->foto($img, $card['foto_path'], 18, 60, $fotoH * 2 / 3, $fotoH, 3);
        }

        $this->teks($img, 'NAMA', 'Bold', 7, '#8A8B8A', 18, $namaTop, 10.5, 1.12);
        foreach ($baris as $i => $isi) {
            $this->teks($img, $isi, 'ExtraBold', 16, '#0D1C2E', 18, $namaTop + 13.5 + $i * $namaLh, $namaLh, -0.16, 'kiri', 145);
        }

        $this->teks($img, 'NO. ANGGOTA', 'Bold', 7, '#7F8286', 19, 171, 7, 1.26);
        $this->teks($img, $card['nomor_depan'], 'ExtraBold', $urut ? 18 : 13, '#0D1C2E', 19, 171 + 7 + ($urut ? 4 : 8), 18, $urut ? -0.18 : 0);

        // Tulisan tegak: satu huruf per baris 6 px, di tengah kolom selebar 6 px
        foreach (str_split('PERPUSTAKAAN') as $i => $huruf) {
            $this->teks($img, $huruf, 'Bold', 5.5, '#A09F9E', self::W - 13.5 - 6, 18 + $i * 6, 6, 0, 'tengah', 6);
        }

        return $this->png($img);
    }

    /** Sisi belakang: kode QR, masa berlaku, alamat, dan No. Anggota lengkap. */
    public function belakang(array $card): string
    {
        $img = $this->kanvas('#0D1C2E');

        $this->tempel($img, 'img/kartu/logo-kemnaker.png', 71, 8, 200, 200, 0.14);

        // QR gelap di atas kotak krem: versi krem di atas navy tidak terbaca alat pemindai
        $this->kotakBulat($img, 45, 60, 80, 80, 6, '#F0EBE1');
        $this->qr($img, $card['qr'], 49, 64, 72, '#0D1C2E');
        $this->teks($img, 'QR SCAN', 'Bold', 7, '#ACABAB', 45, 146, 10.5, 0.98, 'tengah', 80);

        $kanan = self::W - 16.2;
        $this->tempel($img, 'img/kartu/logo-kemnaker-teks-putih.png', $kanan - 26.65, 24, 26.65, 36);

        $this->teks($img, 'BERLAKU S/D', 'Bold', 6, '#ACABAB', $kanan, 70, 9, 0.96, 'kanan');
        $this->teks($img, $card['berlaku'], 'ExtraBold', 11, '#B8943F', $kanan, 81, 16.5, 0.22, 'kanan');

        $this->teks($img, 'PERPUSTAKAAN KEMNAKER RI', 'Bold', 6, '#ACABAB', $kanan, 115, 9, 0.84, 'kanan');
        $this->teks($img, 'Jl. Jend. Gatot Subroto Kav. 51', 'Medium', 6.5, '#CEC9C3', $kanan, 124, 10.4, 0, 'kanan');
        $this->teks($img, 'Jakarta Selatan · (021) 520-7797', 'Medium', 6.5, '#CEC9C3', $kanan, 134.4, 10.4, 0, 'kanan');

        $this->teks($img, 'NO. ANGGOTA', 'Bold', 6, '#ACABAB', $kanan, 162, 9, 0.96, 'kanan');
        $this->teks($img, $card['member_no'], 'ExtraBold', 15, '#F0EBE1', $kanan, 173, 20, 0.3, 'kanan');

        return $this->png($img);
    }

    // ----------------------------------------------------------------
    // Alat gambar. Semua koordinat dalam px desain (342 x 216), dikali S di sini.
    // ----------------------------------------------------------------

    private function kanvas(string $hex): \GdImage
    {
        $img = imagecreatetruecolor(self::W * self::S, self::H * self::S);
        imagefill($img, 0, 0, $this->warna($img, $hex));

        return $img;
    }

    private function png(\GdImage $img): string
    {
        $this->pojok($img, 12 * self::S);
        imagesavealpha($img, true);

        ob_start();
        imagepng($img, null, 6);

        return ob_get_clean();
    }

    private function warna(\GdImage $img, string $hex, int $alpha = 0): int
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return imagecolorallocatealpha($img, $r, $g, $b, $alpha);
    }

    /**
     * Membuat keempat pojok transparan dengan radius $r px (ukuran gambar sebenarnya).
     * Piksel di garis lengkung diberi transparansi sebagian supaya tepinya halus.
     */
    private function pojok(\GdImage $img, float $r): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        $n = (int) ceil($r);

        imagealphablending($img, false);

        for ($y = 0; $y < $n; $y++) {
            for ($x = 0; $x < $n; $x++) {
                $cakupan = max(0.0, min(1.0, $r - hypot($r - ($x + 0.5), $r - ($y + 0.5)) + 0.5));
                if ($cakupan >= 1.0) {
                    continue;
                }

                foreach ([[$x, $y], [$w - 1 - $x, $y], [$x, $h - 1 - $y], [$w - 1 - $x, $h - 1 - $y]] as [$px, $py]) {
                    $c     = imagecolorat($img, $px, $py);
                    $tampak = (127 - (($c >> 24) & 0x7F)) / 127 * $cakupan;
                    imagesetpixel($img, $px, $py, ($c & 0xFFFFFF) | ((int) round(127 - $tampak * 127) << 24));
                }
            }
        }

        imagealphablending($img, true);
    }

    /** Gambar PNG dari folder aset, diskalakan ke kotak tujuan, dengan opasitas opsional. */
    private function tempel(\GdImage $img, string $file, float $x, float $y, float $w, float $h, float $opasitas = 1.0): void
    {
        $src = imagecreatefrompng($this->aset . $file);
        $tw  = (int) round($w * self::S);
        $th  = (int) round($h * self::S);

        $tmp = $this->kosong($tw, $th);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, imagesx($src), imagesy($src));

        if ($opasitas < 1.0) {
            imagealphablending($tmp, false);
            for ($py = 0; $py < $th; $py++) {
                for ($px = 0; $px < $tw; $px++) {
                    $c = imagecolorat($tmp, $px, $py);
                    $a = 127 - (int) round((127 - (($c >> 24) & 0x7F)) * $opasitas);
                    imagesetpixel($tmp, $px, $py, ($c & 0xFFFFFF) | ($a << 24));
                }
            }
        }

        imagecopy($img, $tmp, (int) round($x * self::S), (int) round($y * self::S), 0, 0, $tw, $th);
    }

    /** Foto anggota dipotong memenuhi kotak (bagian atas foto dipertahankan), pojok membulat. */
    private function foto(\GdImage $img, string $path, float $x, float $y, float $w, float $h, float $r): void
    {
        $src = @imagecreatefromstring((string) file_get_contents($path));
        if (!$src) {
            return;
        }

        $tw = (int) round($w * self::S);
        $th = (int) round($h * self::S);
        $sw = imagesx($src);
        $sh = imagesy($src);

        // Ambil bagian foto dengan rasio kotak tujuan: lebar dipotong di tengah, tinggi dipotong dari bawah
        if ($sw / $sh > $tw / $th) {
            $cw = (int) round($sh * $tw / $th);
            $cx = (int) round(($sw - $cw) / 2);
            $ch = $sh;
        } else {
            $cw = $sw;
            $cx = 0;
            $ch = (int) round($sw * $th / $tw);
        }

        $tmp = $this->kosong($tw, $th);
        imagecopyresampled($tmp, $src, 0, 0, $cx, 0, $tw, $th, $cw, $ch);
        $this->pojok($tmp, $r * self::S);

        imagecopy($img, $tmp, (int) round($x * self::S), (int) round($y * self::S), 0, 0, $tw, $th);
    }

    private function kotakBulat(\GdImage $img, float $x, float $y, float $w, float $h, float $r, string $hex): void
    {
        $tw  = (int) round($w * self::S);
        $th  = (int) round($h * self::S);
        $tmp = $this->kosong($tw, $th);
        imagefilledrectangle($tmp, 0, 0, $tw - 1, $th - 1, $this->warna($tmp, $hex));
        $this->pojok($tmp, $r * self::S);

        imagecopy($img, $tmp, (int) round($x * self::S), (int) round($y * self::S), 0, 0, $tw, $th);
    }

    /** Matriks QR (baris x kolom, true = gelap) digambar memenuhi kotak $ukuran px. */
    private function qr(\GdImage $img, array $matriks, float $x, float $y, float $ukuran, string $hex): void
    {
        $n     = count($matriks);
        $sel   = $ukuran * self::S / $n;
        $x0    = $x * self::S;
        $y0    = $y * self::S;
        $gelap = $this->warna($img, $hex);

        // Batas tiap modul dibulatkan dari posisi kumulatif supaya tidak ada celah antarmodul
        foreach ($matriks as $baris => $kolom) {
            foreach ($kolom as $k => $isi) {
                if ($isi) {
                    imagefilledrectangle(
                        $img,
                        (int) round($x0 + $k * $sel),
                        (int) round($y0 + $baris * $sel),
                        (int) round($x0 + ($k + 1) * $sel) - 1,
                        (int) round($y0 + ($baris + 1) * $sel) - 1,
                        $gelap
                    );
                }
            }
        }
    }

    /**
     * Satu baris teks dalam kotak setinggi $lh px, garis dasarnya dihitung seperti CSS.
     * $x adalah tepi kiri (kiri), tepi kanan (kanan), atau tepi kiri kotak selebar $lebar (tengah).
     * Untuk rata kiri, $lebar adalah lebar maksimal; teks yang lebih panjang dipotong dengan elipsis.
     */
    private function teks(\GdImage $img, string $isi, string $tebal, float $px, string $hex, float $x, float $top, float $lh, float $spasi = 0, string $rata = 'kiri', float $lebar = 0): void
    {
        $font = $this->aset . 'fonts/plus-jakarta-sans/PlusJakartaSans-' . $tebal . '.ttf';
        $pt   = $px * self::S * 0.75; // GD memakai 96 dpi: 1 pt = 4/3 px

        if ($rata === 'kiri' && $lebar > 0 && $this->lebar($font, $pt, $isi, $spasi) > $lebar * self::S) {
            while (mb_strlen($isi) > 1 && $this->lebar($font, $pt, $isi . '…', $spasi) > $lebar * self::S) {
                $isi = mb_substr($isi, 0, -1);
            }
            $isi = rtrim($isi) . '…';
        }

        $w = $this->lebar($font, $pt, $isi, $spasi);
        $kiri = match ($rata) {
            'kanan'  => $x * self::S - $w,
            'tengah' => ($x + $lebar / 2) * self::S - $w / 2,
            default  => $x * self::S,
        };

        $em       = $px * self::S;
        $dasar    = (int) round($top * self::S + ($lh * self::S - (self::ASCENT + self::DESCENT) * $em) / 2 + self::ASCENT * $em);
        $warna    = $this->warna($img, $hex);

        if ($spasi == 0) {
            imagettftext($img, $pt, 0, (int) round($kiri), $dasar, $warna, $font, $isi);

            return;
        }

        // Jarak antarhuruf: tiap huruf ditaruh di posisi maju awalannya ditambah spasi
        $huruf = mb_str_split($isi);
        foreach ($huruf as $i => $h) {
            $maju = $this->maju($font, $pt, implode('', array_slice($huruf, 0, $i)));
            imagettftext($img, $pt, 0, (int) round($kiri + $maju + $i * $spasi * self::S), $dasar, $warna, $font, $h);
        }
    }

    /** Lebar teks termasuk jarak antarhuruf, dalam px gambar. */
    private function lebar(string $font, float $pt, string $isi, float $spasi): float
    {
        return $this->maju($font, $pt, $isi) + max(0, mb_strlen($isi) - 1) * $spasi * self::S;
    }

    /** Lebar maju sebuah teks; "H" penutup membuat spasi di akhir ikut terhitung. */
    private function maju(string $font, float $pt, string $isi): float
    {
        if ($isi === '') {
            return 0.0;
        }

        return imagettfbbox($pt, 0, $font, $isi . 'H')[2] - imagettfbbox($pt, 0, $font, 'H')[2];
    }

    private function kosong(int $w, int $h): \GdImage
    {
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, false);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
        imagesavealpha($img, true);

        return $img;
    }
}
