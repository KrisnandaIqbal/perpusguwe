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

    /** Foto di sisi depan berbentuk 2:3 seperti pas foto di pendaftaran, bukan persegi. */
    public function testFotoDepanBerasio2Banding3()
    {
        $foto = tempnam(sys_get_temp_dir(), 'foto') . '.png';
        $src  = imagecreatetruecolor(200, 300);
        imagefill($src, 0, 0, imagecolorallocate($src, 255, 0, 0));
        imagepng($src, $foto);

        $card = ['foto_path' => $foto] + $this->card;
        $card['nama_baris'] = ['Budi Santoso'];
        $img  = imagecreatefromstring((new KartuPng())->depan($card));
        unlink($foto);

        $merah = fn (int $x, int $y) => (imagecolorat($img, $x, $y) >> 16 & 0xFF) > 200 && (imagecolorat($img, $x, $y) >> 8 & 0xFF) < 60;

        // Nama satu baris: kotak foto mulai (18, 60), tinggi sekitar 58 px, jadi lebarnya sekitar 39 px (skala 3)
        $tengahY = (60 + 29) * 3;
        $this->assertTrue($merah((18 + 30) * 3, $tengahY), 'di dalam kotak foto harus terisi foto');
        $this->assertFalse($merah((18 + 45) * 3, $tengahY), 'di luar lebar 2:3 tidak boleh terisi foto');
        $this->assertTrue($merah((18 + 20) * 3, (60 + 55) * 3), 'bagian bawah foto tidak boleh terpotong');
    }
}
