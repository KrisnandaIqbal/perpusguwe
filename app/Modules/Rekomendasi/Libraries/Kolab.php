<?php

namespace Rekomendasi\Libraries;

/**
 * "Yang meminjam X juga meminjam Y". Hitungan murni, tanpa akses database.
 *
 * kolab = c / sqrt(nX * nY), keyakinan = min(1, c / 3). Pasangan dengan c < MIN_C diabaikan.
 */
class Kolab
{
    public const MIN_C = 2;

    public static function kolab(int $c, int $nX, int $nY): float
    {
        return ($nX > 0 && $nY > 0) ? $c / sqrt($nX * $nY) : 0.0;
    }

    public static function keyakinan(int $c): float
    {
        return min(1.0, $c / 3);
    }

    /**
     * Dari semua pinjaman para peminjam X, hitung nX dan c per judul lain yang c-nya cukup.
     *
     * @param array $pinjaman baris ['member_id', 'catalog_id'] milik peminjam X, termasuk pinjaman X sendiri
     * @return array ['nX' => int, 'c' => [idJudulY => c]]
     */
    public static function tetangga(array $pinjaman, int $idX): array
    {
        $anggotaX = [];
        foreach ($pinjaman as $p) {
            if ((int) $p['catalog_id'] === $idX) {
                $anggotaX[(int) $p['member_id']] = true;
            }
        }

        $perJudul = [];
        foreach ($pinjaman as $p) {
            $y = (int) $p['catalog_id'];
            if ($y !== $idX && isset($anggotaX[(int) $p['member_id']])) {
                $perJudul[$y][(int) $p['member_id']] = true;
            }
        }

        $c = [];
        foreach ($perJudul as $y => $anggota) {
            if (count($anggota) >= self::MIN_C) {
                $c[$y] = count($anggota);
            }
        }
        return ['nX' => count($anggotaX), 'c' => $c];
    }

    /**
     * @param array $tetangga hasil tetangga()
     * @param array $nY       [idJudulY => jumlah anggota berbeda yang meminjam Y]
     * @return array [idJudulY => ['kolab' => float, 'keyakinan' => float]]
     */
    public static function hitung(array $tetangga, array $nY): array
    {
        $hasil = [];
        foreach ($tetangga['c'] as $y => $c) {
            $hasil[$y] = [
                'kolab'     => self::kolab($c, $tetangga['nX'], $nY[$y] ?? 0),
                'keyakinan' => self::keyakinan($c),
            ];
        }
        return $hasil;
    }
}
