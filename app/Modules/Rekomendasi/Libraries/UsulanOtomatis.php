<?php

namespace Rekomendasi\Libraries;

/** Aturan usulan pengadaan otomatis dari pencarian (rencana 3e). Fungsi murni, tanpa akses database. */
class UsulanOtomatis
{
    public const MIN_PENCARIAN = 3; // request_count: pencarian 0 hasil dari session berbeda
    public const MIN_ONESEARCH = 2; // onesearch_click_count: klik tombol OneSearch dari session berbeda

    /** Huruf kecil, spasi dirapikan, tanda baca di ujung dibuang. Kunci pengelompokan query yang sama. */
    public static function normal(string $query): string
    {
        $q = mb_strtolower(preg_replace('/\s+/u', ' ', trim($query)));
        return trim(preg_replace('/\p{P}+$/u', '', $q));
    }

    /** Baris sumber 'pencarian' baru masuk daftar admin kalau salah satu hitungan mencapai ambang. */
    public static function tampil(int $requestCount, int $onesearchClickCount): bool
    {
        return $requestCount >= self::MIN_PENCARIAN || $onesearchClickCount >= self::MIN_ONESEARCH;
    }
}
