<?php

namespace App\Libraries;

/**
 * Kartu anggota sebagai gambar PNG untuk lampiran email, digambar dengan GD.
 *
 * Posisi, ukuran, dan warna mengikuti kartu-anggota-pdf.php (desain 342 x 216 px, kartu putih berfoto ruang baca), digambar 3x
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

    /** Sisi depan: kepala, foto ruang baca berlengkung, foto anggota, nama, nomor, dan barcode. */
    public function depan(array $card): string
    {
        $img = $this->kanvas('#FFFFFF');

        // Lingkaran besar (pusat 425,165, jari-jari 260) berisi foto ruang baca, dengan cincin biru dan emas di luarnya
        $this->bulatan($img, 425, 165, 267, '#2E7BC4');
        $this->bulatan($img, 425, 165, 263.5, '#D4A437');
        $this->bulatan($img, 425, 165, 260, null, $this->fotoRuang(177, 216), 165);

        $this->kepala($img);

        if (!empty($card['foto_path']) && is_file($card['foto_path'])) {
            // Foto 3:4 dipotong dari tengah atas
            $this->foto($img, $card['foto_path'], 20, 56, 54, 72, 4);
        } else {
            $this->kotakBulat($img, 20, 56, 54, 72, 4, '#E8EEF4');
        }

        // Nama (maksimal dua baris, diatur namaKartuBaris) lalu nomor, rata kiri
        $baris = $card['nama_baris'] ?? [$card['nama']];
        $y     = 56 + 72 + 8;
        foreach ($baris as $isi) {
            $this->teks($img, $isi, 'ExtraBold', 12, '#0D1C2E', 20, $y, 14.4, 0, 'kiri', 132);
            $y += 14.4;
        }
        $this->teks($img, $card['member_no'], 'Bold', 9, '#5B6878', 20, $y + 4, 11.7, 0.9);

        $this->barcode($img, $card['barcode'], 20, 188, 14, '#0D1C2E');

        return $this->png($img);
    }

    /** Sisi belakang: kepala, kode QR di tengah, masa berlaku, dan petunjuk memindai. */
    public function belakang(array $card): string
    {
        $img = $this->kanvas('#FFFFFF');

        $this->kepala($img);

        $this->qr($img, $card['qr'], (self::W - 88) / 2, 61, 88, '#0D1C2E');
        $this->teks($img, $card['member_no'], 'Bold', 6, '#0D1C2E', 0, 153, 8.4, 1.2, 'tengah', self::W);

        $this->teks($img, 'BERLAKU S/D', 'Bold', 5.5, '#5B6878', 20, 167.5, 7.7, 0.66);
        $this->teks($img, $card['berlaku'], 'Bold', 6.5, '#17406B', 20, 176.2, 9.1);
        $this->teks($img, 'Pindai kode QR di meja layanan', 'Medium', 5.5, '#5B6878', 20, 190.3, 7.7);

        return $this->png($img);
    }

    /** Kepala dua sisi: logo, nama perpustakaan, dan garis tiga warna Kemnaker. */
    private function kepala(\GdImage $img): void
    {
        $this->tempel($img, 'img/kartu/logo-kemnaker.png', 20, 18, 24, 24);
        $this->teks($img, 'PERPUSTAKAAN', 'ExtraBold', 7.5, '#17406B', 50, 21.4, 9, 0.6);
        $this->teks($img, 'Kementerian Ketenagakerjaan RI', 'Medium', 5.5, '#4A5A6B', 50, 31.4, 7.15);

        $this->kotakBulat($img, 20, 49, 32, 2, 1, '#17406B');
        $this->kotakBulat($img, 54, 49, 20, 2, 1, '#2E7BC4');
        $this->kotakBulat($img, 76, 49, 12, 2, 1, '#D4A437');
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

    /** Foto ruang baca dipotong memenuhi kotak $w x $h px desain; bagian yang dipertahankan condong ke kanan (72%). */
    private function fotoRuang(float $w, float $h): \GdImage
    {
        $src = imagecreatefromstring((string) file_get_contents($this->aset . 'home/hero/01-ruang-baca.jpg'));
        $tw  = (int) round($w * self::S);
        $th  = (int) round($h * self::S);
        $sh  = imagesy($src);
        $cw  = (int) round($sh * $tw / $th);
        $cx  = (int) round((imagesx($src) - $cw) * 0.72);

        $tmp = $this->kosong($tw, $th);
        imagecopyresampled($tmp, $src, 0, 0, $cx, 0, $tw, $th, $cw, $sh);

        return $tmp;
    }

    /**
     * Lingkaran berpusat di ($cx, $cy) dengan jari-jari $r (px desain), warna rata $hex atau isi gambar $isi
     * yang tepi kirinya di $ix px desain. Hanya busur kiri yang berada di dalam kartu, jadi tiap baris
     * diisi dari titik potong lengkung sampai tepi kanan; piksel di lengkung diberi transparansi sebagian.
     */
    private function bulatan(\GdImage $img, float $cx, float $cy, float $r, ?string $hex, ?\GdImage $isi = null, float $ix = 0): void
    {
        $w  = imagesx($img);
        $h  = imagesy($img);
        $cx *= self::S;
        $cy *= self::S;
        $r  *= self::S;
        $ix = (int) round($ix * self::S);

        for ($y = 0; $y < $h; $y++) {
            $dy = $y + 0.5 - $cy;
            if (abs($dy) >= $r) {
                continue;
            }

            $kiri  = $cx - sqrt($r * $r - $dy * $dy);
            $mulai = (int) ceil($kiri) + 1;

            for ($x = max(0, (int) floor($kiri) - 1); $x < min($w, $mulai); $x++) {
                $cakupan = max(0.0, min(1.0, $r - hypot($x + 0.5 - $cx, $dy) + 0.5));
                if ($cakupan <= 0) {
                    continue;
                }

                $alpha = (int) round(127 - $cakupan * 127);
                imagesetpixel($img, $x, $y, $isi ? ((imagecolorat($isi, $x - $ix, $y) & 0xFFFFFF) | ($alpha << 24)) : $this->warna($img, $hex, $alpha));
            }

            if ($mulai < $w) {
                $isi
                    ? imagecopy($img, $isi, $mulai, $y, $mulai - $ix, $y, $w - $mulai, 1)
                    : imagefilledrectangle($img, $mulai, $y, $w - 1, $y, $this->warna($img, $hex));
            }
        }
    }

    /** Barcode Code 128 setinggi $h px, satu modul selebar 1 px desain (mengecil kalau lebih lebar dari kolom 132 px). */
    private function barcode(\GdImage $img, array $data, float $x, float $y, float $h, string $hex): void
    {
        $modul = min(1.0, 132 / $data['lebar']);
        $gelap = $this->warna($img, $hex);

        foreach ($data['bar'] as [$bx, $bw]) {
            imagefilledrectangle(
                $img,
                (int) round(($x + $bx * $modul) * self::S),
                (int) round($y * self::S),
                (int) round(($x + ($bx + $bw) * $modul) * self::S) - 1,
                (int) round(($y + $h) * self::S) - 1,
                $gelap
            );
        }
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
