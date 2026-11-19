<?php

namespace Rekomendasi\Libraries;

/**
 * Pilihan bobot dan favorit untuk beranda anggota (rencana 2b dan 2d). Fungsi murni, tanpa akses database.
 *
 * Riwayat: baris ['id', 'Subject', 'DeweyNo'], judul yang pernah dipinjam anggota, terbaru di atas, tanpa duplikat.
 */
class Beranda
{
    /** Bobot menurut jumlah judul berbeda yang pernah dipinjam. Tanpa izin atau tanpa riwayat: cold start. */
    public static function bobot(int $jumlahJudul, bool $izin): array
    {
        if (!$izin || $jumlahJudul === 0) {
            return Ranker::BOBOT_COLD_START;
        }
        return $jumlahJudul >= 5 ? Ranker::BOBOT_DOMINAN_KOLAB : Ranker::BOBOT_DOMINAN_CONTENT;
    }

    /** Subjek yang paling sering muncul di riwayat. Seri dimenangkan subjek yang lebih dulu ditemui (lebih baru dipinjam). */
    public static function subjekFavorit(array $riwayat): ?string
    {
        $hitung = [];
        foreach ($riwayat as $r) {
            foreach (ContentScore::daftar($r['Subject'] ?? '') as $s) {
                $hitung[$s] = ($hitung[$s] ?? 0) + 1;
            }
        }
        return $hitung ? self::terbanyak($hitung) : null;
    }

    /** Kelas DDC 3 digit (mis. '331') yang paling sering muncul di riwayat. Seri dimenangkan yang lebih baru dipinjam. */
    public static function kelasFavorit(array $riwayat): ?string
    {
        $hitung = [];
        foreach ($riwayat as $r) {
            $ddc = preg_replace('/[\s.]/', '', (string) ($r['DeweyNo'] ?? ''));
            if (strlen($ddc) >= 3 && ctype_digit(substr($ddc, 0, 3))) {
                $kelas = substr($ddc, 0, 3);
                $hitung[$kelas] = ($hitung[$kelas] ?? 0) + 1;
            }
        }
        return $hitung ? self::terbanyak($hitung) : null;
    }

    private static function terbanyak(array $hitung): string
    {
        $maks = max($hitung);
        return (string) array_search($maks, $hitung, true);
    }
}
