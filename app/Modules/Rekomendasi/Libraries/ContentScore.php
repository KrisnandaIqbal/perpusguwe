<?php

namespace Rekomendasi\Libraries;

/** Kemiripan isi dua judul, 0-1: 0.50 DDC + 0.35 subjek + 0.15 pengarang. */
class ContentScore
{
    // Indeks = panjang awalan DDC yang sama (digit).
    private const DDC_SKOR = [0, 0.1, 0.3, 0.6, 0.8, 1.0];

    public static function score(array $a, array $b): float
    {
        return 0.50 * self::ddcSimilarity($a['DeweyNo'] ?? '', $b['DeweyNo'] ?? '')
            + 0.35 * self::subjekSimilarity($a['Subject'] ?? '', $b['Subject'] ?? '')
            + 0.15 * self::pengarangSimilarity($a['Author'] ?? '', $b['Author'] ?? '');
    }

    public static function ddcSimilarity(?string $a, ?string $b): float
    {
        $a = preg_replace('/[\s.]/', '', (string) $a);
        $b = preg_replace('/[\s.]/', '', (string) $b);
        $n = 0;
        $maks = min(strlen($a), strlen($b), 5);
        while ($n < $maks && $a[$n] === $b[$n]) {
            $n++;
        }
        return self::DDC_SKOR[$n];
    }

    public static function subjekSimilarity(?string $a, ?string $b): float
    {
        $a = self::daftar($a);
        $b = self::daftar($b);
        $gabungan = count(array_unique(array_merge($a, $b)));
        return $gabungan === 0 ? 0.0 : count(array_intersect($a, $b)) / $gabungan;
    }

    public static function pengarangSimilarity(?string $a, ?string $b): float
    {
        return array_intersect(self::daftar($a), self::daftar($b)) ? 1.0 : 0.0;
    }

    /** Pecah di ';', trim, huruf kecil, buang kosong dan duplikat. Data lokal kadang hanya berisi ";". */
    public static function daftar(?string $teks): array
    {
        $hasil = array_map(fn ($s) => mb_strtolower(trim($s)), explode(';', (string) $teks));
        return array_values(array_unique(array_filter($hasil, fn ($s) => $s !== '')));
    }
}
