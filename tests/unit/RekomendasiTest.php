<?php

use CodeIgniter\Test\CIUnitTestCase;
use Rekomendasi\Libraries\ContentScore;
use Rekomendasi\Libraries\Kolab;
use Rekomendasi\Libraries\Ranker;

/** @internal */
final class RekomendasiTest extends CIUnitTestCase
{
    /** @dataProvider ddcProvider */
    public function testDdc(string $a, string $b, float $harapan)
    {
        $this->assertEqualsWithDelta($harapan, ContentScore::ddcSimilarity($a, $b), 0.0001);
    }

    public function ddcProvider(): array
    {
        return [
            '5 digit atau lebih' => ['658.40', '658.401 2', 1.0],
            '4 digit'            => ['658.4', '658.41', 0.8],
            '3 digit'            => ['658.3', '658.4', 0.6],
            '2 digit'            => ['658', '651', 0.3],
            '1 digit'            => ['658', '600', 0.1],
            '0 digit'            => ['658', '331', 0.0],
            'kosong'             => ['', '658', 0.0],
        ];
    }

    public function testJaccardSubjek()
    {
        $this->assertEqualsWithDelta(1 / 3, ContentScore::subjekSimilarity('Manajemen; Bisnis', 'bisnis; Pemasaran'), 0.0001);
        $this->assertSame(0.0, ContentScore::subjekSimilarity('', ''));
        $this->assertSame(0.0, ContentScore::subjekSimilarity('A', 'B'));
    }

    public function testPengarangSamaDanDataBerantakan()
    {
        $this->assertSame(1.0, ContentScore::pengarangSimilarity('Budi; Ani', 'ani'));
        $this->assertSame(0.0, ContentScore::pengarangSimilarity('Budi', 'Citra'));
        $this->assertSame(0.0, ContentScore::pengarangSimilarity(';', ';'));
        $this->assertSame(0.0, ContentScore::pengarangSimilarity(null, ' ; ; '));
    }

    public function testScoreGabungan()
    {
        $a = ['DeweyNo' => '658.4', 'Subject' => 'X; Y', 'Author' => 'Budi'];
        $b = ['DeweyNo' => '658.41', 'Subject' => 'X; Y', 'Author' => 'Budi'];
        $this->assertEqualsWithDelta(0.5 * 0.8 + 0.35 + 0.15, ContentScore::score($a, $b), 0.0001);
    }

    private function k(int $id, float $content, string $author = '', string $ddc = '658', array $extra = []): array
    {
        return array_merge(['id' => $id, 'content' => $content, 'Author' => $author, 'DeweyNo' => $ddc, 'IsOPAC' => 1], $extra);
    }

    public function testRumusSkor()
    {
        $k = ['content' => 0.5, 'kolab' => 0.4, 'keyakinan' => 0.5, 'populer' => 1.0, 'tersedia' => 1];
        $this->assertEqualsWithDelta(0.7 * 0.5 + 0.3 * 0.4 * 0.5 + 0.1, Ranker::skor($k, Ranker::BOBOT_DETAIL), 0.0001);
    }

    public function testUrutanDanFilterDipinjamDanIsOpac()
    {
        $hasil = Ranker::baris([
            $this->k(1, 0.2), $this->k(2, 0.9), $this->k(3, 0.8),
            $this->k(4, 0.7, '', '658', ['IsOPAC' => 0]),
        ], Ranker::BOBOT_DETAIL, [3]);
        $this->assertSame([2, 1], array_column($hasil, 'id'));
    }

    public function testMaksDuaPerPengarangTermasukDataBerantakan()
    {
        $hasil = Ranker::baris([
            $this->k(1, 0.9, 'Budi'), $this->k(2, 0.8, 'budi; Ani'), $this->k(3, 0.7, 'Budi'),
            $this->k(4, 0.6, ';'), $this->k(5, 0.5, ';'), $this->k(6, 0.4, ';'),
        ], Ranker::BOBOT_DETAIL);
        $this->assertSame([1, 2, 4, 5, 6], array_column($hasil, 'id'));
    }

    public function testMaksSatuMajalahKecualiSumberMajalah()
    {
        $kandidat = [$this->k(1, 0.9, '', '050'), $this->k(2, 0.8, '', '051.2'), $this->k(3, 0.7)];
        $this->assertSame([1, 3], array_column(Ranker::baris($kandidat, Ranker::BOBOT_DETAIL), 'id'));
        $this->assertSame([1, 2, 3], array_column(Ranker::baris($kandidat, Ranker::BOBOT_DETAIL, [], true), 'id'));
    }

    public function testHalamanDedupAntarBarisDanMinimumEmpat()
    {
        $baris = fn (array $ids) => array_map(fn ($i) => $this->k($i, 0.5), $ids);
        $hasil = Ranker::halaman([
            'a' => $baris([1, 2, 3, 4, 5]),
            'b' => $baris([4, 5, 6, 7, 8, 9]),  // 4 dan 5 sudah di 'a'
            'c' => $baris([1, 2, 10, 11, 12]),  // sisa 3 judul, dibuang
        ]);
        $this->assertSame(['a', 'b'], array_keys($hasil));
        $this->assertSame([6, 7, 8, 9], array_column($hasil['b'], 'id'));
    }

    public function testKolabDanKeyakinan()
    {
        $this->assertEqualsWithDelta(0.5, Kolab::kolab(2, 4, 4), 0.0001);
        $this->assertSame(0.0, Kolab::kolab(2, 0, 4));
        $this->assertEqualsWithDelta(2 / 3, Kolab::keyakinan(2), 0.0001);
        $this->assertSame(1.0, Kolab::keyakinan(5));
    }

    public function testTetanggaMengabaikanCKurangDariDua()
    {
        $p = [];
        foreach ([[1, 10], [2, 10], [3, 10], [1, 20], [2, 20], [3, 30], [1, 20]] as [$m, $c]) {
            $p[] = ['member_id' => $m, 'catalog_id' => $c];
        }
        $t = Kolab::tetangga($p, 10);
        $this->assertSame(3, $t['nX']);
        $this->assertSame([20 => 2], $t['c']); // 30 hanya c = 1; pinjaman ganda anggota 1 tidak dihitung dua kali

        $h = Kolab::hitung($t, [20 => 8]);
        $this->assertEqualsWithDelta(2 / sqrt(24), $h[20]['kolab'], 0.0001);
        $this->assertEqualsWithDelta(2 / 3, $h[20]['keyakinan'], 0.0001);
    }
}
