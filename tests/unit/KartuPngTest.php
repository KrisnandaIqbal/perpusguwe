<?php

use App\Libraries\KartuPng;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Kartu anggota sebagai gambar PNG untuk lampiran email: 3x ukuran desain, pojok transparan.
 *
 * @internal
 */
final class KartuPngTest extends CIUnitTestCase
{
    private array $card;

    protected function setUp(): void
    {
        parent::setUp();
        helper('anggota');

        $this->card = kartuAnggotaData((object) [
            'Fullname'  => 'krisnanda muhammad iqbal',
            'MemberNo'  => '26101200371',
            'PhotoUrl'  => '',
            'EndDate'   => '2027-10-12',
        ]);
    }

    public function sisiProvider(): array
    {
        return ['depan' => ['depan'], 'belakang' => ['belakang']];
    }

    /** @dataProvider sisiProvider */
    public function testUkuranTigaKaliDesain(string $sisi)
    {
        $img = imagecreatefromstring((new KartuPng())->$sisi($this->card));

        $this->assertSame(1026, imagesx($img));
        $this->assertSame(648, imagesy($img));
    }

    /** @dataProvider sisiProvider */
    public function testPojokTransparanIsiTidak(string $sisi)
    {
        $img = imagecreatefromstring((new KartuPng())->$sisi($this->card));
        $alpha = fn (int $x, int $y) => (imagecolorat($img, $x, $y) >> 24) & 0x7F;

        // 127 = transparan penuh, 0 = tidak tembus
        foreach ([[0, 0], [1025, 0], [0, 647], [1025, 647]] as [$x, $y]) {
            $this->assertSame(127, $alpha($x, $y), "pojok ($x, $y) harus transparan");
        }

        $this->assertSame(0, $alpha(513, 0), 'tepi atas di tengah harus tidak tembus');
        $this->assertSame(0, $alpha(513, 324), 'tengah kartu harus tidak tembus');
    }
}
