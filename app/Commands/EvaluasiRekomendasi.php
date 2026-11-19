<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Rekomendasi\Libraries\Beranda;
use Rekomendasi\Libraries\Evaluasi;
use Rekomendasi\Libraries\Ranker;

/**
 * Evaluasi offline rekomendasi (T10): precision@k dan recall@k dari data pinjam, split berdasarkan waktu.
 * Hanya membaca database. Contoh: php spark rekomendasi:evaluasi --hari 90 --k 10 --grid
 */
class EvaluasiRekomendasi extends BaseCommand
{
    protected $group       = 'Rekomendasi';
    protected $name        = 'rekomendasi:evaluasi';
    protected $description = 'Hitung precision@k dan recall@k rekomendasi dari data pinjam, lalu bandingkan bobot.';
    protected $usage       = 'rekomendasi:evaluasi [--hari N] [--k N] [--maks N] [--grid]';
    protected $options     = [
        '--hari' => 'Pinjaman dalam N hari terakhir jadi data uji, sisanya data latih (bawaan 90).',
        '--k'    => 'Panjang daftar rekomendasi (bawaan 10).',
        '--maks' => 'Evaluasi paling banyak N anggota, diambil acak (bawaan 300).',
        '--grid' => 'Cari bobot terbaik: semua kombinasi wc, wk, wp kelipatan 0,1 yang jumlahnya 1.',
    ];

    public function run(array $params)
    {
        $hari = (int) (CLI::getOption('hari') ?? 90);
        $k    = (int) (CLI::getOption('k') ?? 10);
        $maks = (int) (CLI::getOption('maks') ?? 300);

        $db = Database::connect('data');
        $pinjaman = $db->query('SELECT DISTINCT li.member_id, c.Catalog_id AS catalog_id, li.LoanDate FROM collectionloanitems li JOIN collections c ON c.ID = li.Collection_id')->getResultArray();
        $batas = date('Y-m-d 00:00:00', strtotime("-{$hari} days"));
        [$latih, $uji] = Evaluasi::splitWaktu($pinjaman, $batas);

        $katalog = [];
        foreach ($db->query('SELECT ID, DeweyNo, Subject, Author, IsOPAC FROM catalogs WHERE IsOPAC = 1')->getResultArray() as $r) {
            $katalog[(int) $r['ID']] = $r;
        }

        // anggota yang punya riwayat latih dan meminjam judul baru di masa uji
        $anggota = [];
        foreach ($uji as $m => $judul) {
            $baru = array_values(array_diff($judul, $latih[$m] ?? []));
            if (!empty($latih[$m]) && $baru) {
                $anggota[$m] = $baru;
            }
        }
        CLI::write(sprintf('Pinjaman: %d baris. Latih: %d anggota. Uji: %d anggota. Layak dievaluasi: %d.', count($pinjaman), count($latih), count($uji), count($anggota)));
        if (!$anggota) {
            CLI::write('Belum ada anggota yang layak. Butuh pinjaman sebelum dan sesudah ' . $batas . '.', 'yellow');
            return;
        }
        if (count($anggota) > $maks) {
            $anggota = array_intersect_key($anggota, array_flip((array) array_rand($anggota, $maks)));
        }

        $peminjamPerJudul = [];
        foreach ($latih as $m => $judul) {
            foreach ($judul as $c) {
                $peminjamPerJudul[$c][] = $m;
            }
        }
        $maksPeminjam = max(array_map('count', $peminjamPerJudul));
        $populer = array_map(fn ($p) => count($p) / $maksPeminjam, $peminjamPerJudul);

        $pasangan = [];
        foreach ($anggota as $m => $_) {
            foreach ($latih[$m] as $x) {
                $pasangan[$x] ??= Evaluasi::pasanganKolab($latih, $peminjamPerJudul, $x);
            }
        }

        $set = [
            'bobot sekarang (dinamis)' => null,
            'content saja'             => [1.0, 0.0, 0.0],
            'kolab saja'               => [0.0, 1.0, 0.0],
            'populer saja'             => Ranker::BOBOT_COLD_START,
            'dominan content'          => Ranker::BOBOT_DOMINAN_CONTENT,
            'dominan kolab'            => Ranker::BOBOT_DOMINAN_KOLAB,
        ];
        if (CLI::getOption('grid')) {
            for ($c = 0; $c <= 10; $c++) {
                for ($kb = 0; $kb <= 10 - $c; $kb++) {
                    $set[sprintf('wc=%.1f wk=%.1f wp=%.1f', $c / 10, $kb / 10, (10 - $c - $kb) / 10)] = [$c / 10, $kb / 10, (10 - $c - $kb) / 10];
                }
            }
        }

        $hasil = [];
        foreach ($set as $nama => $bobot) {
            $p = $r = 0.0;
            foreach ($anggota as $m => $benar) {
                $b   = $bobot ?? Beranda::bobot(count($latih[$m]), true);
                $rek = Evaluasi::rekomendasi($latih[$m], $katalog, $populer, $pasangan, $b, $k);
                $p  += Evaluasi::precisionAtK($rek, $benar, $k);
                $r  += Evaluasi::recallAtK($rek, $benar, $k);
            }
            $hasil[] = [$nama, round($p / count($anggota), 4), round($r / count($anggota), 4)];
        }

        usort($hasil, fn ($a, $b) => $b[1] <=> $a[1]);
        CLI::write(sprintf('k = %d, anggota = %d, data uji sejak %s', $k, count($anggota), $batas), 'green');
        CLI::table(array_slice($hasil, 0, 12), ['Bobot', "precision@{$k}", "recall@{$k}"]);
    }
}
