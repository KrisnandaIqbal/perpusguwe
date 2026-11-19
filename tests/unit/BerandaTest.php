<?php

use CodeIgniter\Test\CIUnitTestCase;
use Rekomendasi\Libraries\Beranda;
use Rekomendasi\Libraries\Evaluasi;
use Rekomendasi\Libraries\Ranker;

/** @internal */
final class BerandaTest extends CIUnitTestCase
{
    public function testBobotMenurutJumlahJudul()
    {
        $this->assertSame(Ranker::BOBOT_COLD_START, Beranda::bobot(0, true));
        $this->assertSame(Ranker::BOBOT_DOMINAN_CONTENT, Beranda::bobot(1, true));
        $this->assertSame(Ranker::BOBOT_DOMINAN_CONTENT, Beranda::bobot(4, true));
        $this->assertSame(Ranker::BOBOT_DOMINAN_KOLAB, Beranda::bobot(5, true));
    }

    public function testTanpaIzinSelaluColdStart()
    {
        $this->assertSame(Ranker::BOBOT_COLD_START, Beranda::bobot(12, false));
    }

    public function testSubjekFavoritSeriMemilihYangLebihBaru()
    {
        $riwayat = [
            ['Subject' => 'Hukum; Investasi'],
            ['Subject' => 'Investasi'],
            ['Subject' => 'Hukum'],
        ];
        $this->assertSame('hukum', Beranda::subjekFavorit($riwayat));
        $this->assertNull(Beranda::subjekFavorit([['Subject' => ';'], ['Subject' => '']]));
        $this->assertNull(Beranda::subjekFavorit([]));
    }

    public function testKelasFavorit()
    {
        $riwayat = [
            ['DeweyNo' => '331.110 26'],
            ['DeweyNo' => '658.4'],
            ['DeweyNo' => '331.2'],
            ['DeweyNo' => ''],
        ];
        $this->assertSame('331', Beranda::kelasFavorit($riwayat));
        // seri: yang lebih dulu ditemui (lebih baru dipinjam) menang
        $this->assertSame('658', Beranda::kelasFavorit([['DeweyNo' => '658.4'], ['DeweyNo' => '331']]));
        $this->assertNull(Beranda::kelasFavorit([['DeweyNo' => 'FIC'], ['DeweyNo' => '']]));
    }

    public function testSplitWaktu()
    {
        $pinjaman = [
            ['member_id' => 1, 'catalog_id' => 10, 'LoanDate' => '2026-01-01 00:00:00'],
            ['member_id' => 1, 'catalog_id' => 10, 'LoanDate' => '2026-01-02 00:00:00'],
            ['member_id' => 1, 'catalog_id' => 20, 'LoanDate' => '2026-06-01 00:00:00'],
            ['member_id' => 2, 'catalog_id' => 30, 'LoanDate' => '2026-06-01 00:00:00'],
        ];
        [$latih, $uji] = Evaluasi::splitWaktu($pinjaman, '2026-03-01 00:00:00');
        $this->assertSame([1 => [10]], $latih);
        $this->assertSame([1 => [20], 2 => [30]], $uji);
    }

    public function testPrecisionDanRecall()
    {
        $rek = [5, 6, 7, 8];
        $this->assertEqualsWithDelta(0.5, Evaluasi::precisionAtK($rek, [6, 8, 99], 4), 0.0001);
        $this->assertEqualsWithDelta(2 / 3, Evaluasi::recallAtK($rek, [6, 8, 99], 4), 0.0001);
        $this->assertEqualsWithDelta(0.0, Evaluasi::precisionAtK($rek, [8], 2), 0.0001);
        $this->assertSame(0.0, Evaluasi::recallAtK($rek, [], 4));
    }

    public function testRekomendasiMengecualikanRiwayat()
    {
        $katalog = [
            1 => ['DeweyNo' => '331.1', 'Subject' => 'kerja', 'Author' => 'a', 'IsOPAC' => 1],
            2 => ['DeweyNo' => '331.12', 'Subject' => 'kerja', 'Author' => 'b', 'IsOPAC' => 1],
            3 => ['DeweyNo' => '005', 'Subject' => 'web', 'Author' => 'c', 'IsOPAC' => 1],
        ];
        $rek = Evaluasi::rekomendasi([1], $katalog, [], [], [1.0, 0.0, 0.0], 2);
        $this->assertSame([2, 3], $rek);
    }
}
