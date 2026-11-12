<?php

namespace Rekomendasi\Libraries;

/**
 * Urutan akhir kandidat rekomendasi. Fungsi murni: input skor, output urutan.
 *
 * Kandidat: ['id', 'DeweyNo', 'Author', 'IsOPAC', 'content', 'kolab', 'keyakinan', 'populer', 'tersedia'].
 */
class Ranker
{
    // Titik awal (rencana 2b), disetel ulang setelah evaluasi. Urutan: [wc, wk, wp].
    public const BOBOT_DETAIL          = [0.7, 0.3, 0.0];
    public const BOBOT_DETAIL_CONTENT  = [0.7, 0.0, 0.0];   // sampai co-borrow (T6) dipasang
    public const BOBOT_COLD_START      = [0.0, 0.0, 1.0];   // 0 pinjaman atau IzinPersonalisasi = 0
    public const BOBOT_DOMINAN_CONTENT = [0.6, 0.2, 0.2];   // 1-4 pinjaman
    public const BOBOT_DOMINAN_KOLAB   = [0.35, 0.45, 0.2]; // >= 5 pinjaman

    public const MIN_PER_BARIS      = 4;
    public const MAKS_PER_PENGARANG = 2;

    public static function skor(array $k, array $bobot): float
    {
        [$wc, $wk, $wp] = $bobot;
        return $wc * ($k['content'] ?? 0)
            + $wk * ($k['kolab'] ?? 0) * ($k['keyakinan'] ?? 0)
            + $wp * ($k['populer'] ?? 0)
            + 0.1 * ($k['tersedia'] ?? 0);
    }

    /**
     * Satu baris: filter, urutkan, lalu terapkan batas pengarang dan majalah.
     *
     * @param array $dipinjam      id judul yang sudah pernah dipinjam anggota
     * @param bool  $sumberMajalah true kalau judul sumbernya majalah (DDC 05x)
     */
    public static function baris(array $kandidat, array $bobot, array $dipinjam = [], bool $sumberMajalah = false, int $batas = 20): array
    {
        $kandidat = array_filter($kandidat, fn ($k) => ($k['IsOPAC'] ?? 1) && !in_array($k['id'], $dipinjam));
        usort($kandidat, fn ($a, $b) => self::skor($b, $bobot) <=> self::skor($a, $bobot));

        $hasil = [];
        $perPengarang = [];
        $majalah = 0;
        foreach ($kandidat as $k) {
            $pengarang = ContentScore::daftar($k['Author'] ?? '');
            if (array_filter($pengarang, fn ($p) => ($perPengarang[$p] ?? 0) >= self::MAKS_PER_PENGARANG)) {
                continue;
            }
            if (self::majalah($k)) {
                if (!$sumberMajalah && $majalah >= 1) {
                    continue;
                }
                $majalah++;
            }
            foreach ($pengarang as $p) {
                $perPengarang[$p] = ($perPengarang[$p] ?? 0) + 1;
            }
            $hasil[] = $k;
            if (count($hasil) >= $batas) {
                break;
            }
        }
        return $hasil;
    }

    /**
     * Satu halaman: judul tidak muncul di dua baris (baris lebih atas menang),
     * baris dengan kurang dari MIN_PER_BARIS judul dibuang. Kunci baris dipertahankan.
     */
    public static function halaman(array $barisBaris): array
    {
        $terpakai = [];
        $hasil = [];
        foreach ($barisBaris as $nama => $baris) {
            $baris = array_values(array_filter($baris, fn ($k) => !isset($terpakai[$k['id']])));
            if (count($baris) < self::MIN_PER_BARIS) {
                continue;
            }
            foreach ($baris as $k) {
                $terpakai[$k['id']] = true;
            }
            $hasil[$nama] = $baris;
        }
        return $hasil;
    }

    public static function majalah(array $k): bool
    {
        return str_starts_with(preg_replace('/[\s.]/', '', (string) ($k['DeweyNo'] ?? '')), '05');
    }
}
