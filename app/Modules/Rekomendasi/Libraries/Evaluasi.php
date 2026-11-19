<?php

namespace Rekomendasi\Libraries;

/**
 * Evaluasi offline (T10): bagi pinjaman menurut waktu, rekomendasikan dari bagian lama,
 * ukur apakah anggota benar-benar meminjam judul itu di bagian baru. Fungsi murni.
 */
class Evaluasi
{
    /**
     * @param array  $pinjaman baris ['member_id', 'catalog_id', 'LoanDate']
     * @param string $batas    'Y-m-d H:i:s'; sebelum batas = latih, mulai batas = uji
     * @return array [[memberId => [idJudul]], [memberId => [idJudul]]], judul unik per anggota
     */
    public static function splitWaktu(array $pinjaman, string $batas): array
    {
        $latih = $uji = [];
        foreach ($pinjaman as $p) {
            $m = (int) $p['member_id'];
            $c = (int) $p['catalog_id'];
            if ($p['LoanDate'] < $batas) {
                $latih[$m][$c] = $c;
            } else {
                $uji[$m][$c] = $c;
            }
        }
        return [array_map('array_values', $latih), array_map('array_values', $uji)];
    }

    public static function precisionAtK(array $rekomendasi, array $benar, int $k): float
    {
        $hit = count(array_intersect(array_slice($rekomendasi, 0, $k), $benar));
        return $k > 0 ? $hit / $k : 0.0;
    }

    public static function recallAtK(array $rekomendasi, array $benar, int $k): float
    {
        $hit = count(array_intersect(array_slice($rekomendasi, 0, $k), $benar));
        return $benar ? $hit / count($benar) : 0.0;
    }

    /** Kolab satu judul X dari data latih: [idY => ['kolab', 'keyakinan']]. */
    public static function pasanganKolab(array $latih, array $peminjamPerJudul, int $x): array
    {
        $pinjaman = [];
        foreach ($peminjamPerJudul[$x] ?? [] as $m) {
            foreach ($latih[$m] as $c) {
                $pinjaman[] = ['member_id' => $m, 'catalog_id' => $c];
            }
        }
        $nY = array_map('count', $peminjamPerJudul);
        return Kolab::hitung(Kolab::tetangga($pinjaman, $x), $nY);
    }

    /**
     * Top-$k judul untuk satu anggota dari riwayat latihnya.
     *
     * @param array $riwayat  id judul yang dipinjam anggota (bagian latih)
     * @param array $katalog  [id => ['DeweyNo','Subject','Author','IsOPAC']]
     * @param array $populer  [id => 0-1]
     * @param array $pasangan [idX => hasil pasanganKolab()]
     */
    public static function rekomendasi(array $riwayat, array $katalog, array $populer, array $pasangan, array $bobot, int $k): array
    {
        $kandidat = [];
        foreach ($katalog as $id => $y) {
            $content = 0.0;
            foreach ($riwayat as $x) {
                $content = max($content, ContentScore::score($katalog[$x] ?? [], $y));
            }
            $kolab = $keyakinan = 0.0;
            foreach ($riwayat as $x) {
                $pair = $pasangan[$x][$id] ?? null;
                if ($pair && $pair['kolab'] * $pair['keyakinan'] > $kolab * $keyakinan) {
                    [$kolab, $keyakinan] = [$pair['kolab'], $pair['keyakinan']];
                }
            }
            $kandidat[] = $y + ['id' => $id, 'content' => $content, 'kolab' => $kolab, 'keyakinan' => $keyakinan, 'populer' => $populer[$id] ?? 0.0];
        }
        return array_column(Ranker::baris($kandidat, $bobot, $riwayat, false, $k), 'id');
    }
}
