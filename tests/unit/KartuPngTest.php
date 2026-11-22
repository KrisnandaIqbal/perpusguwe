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

    /** Foto anggota di sisi depan berbentuk 3:4 (54 x 72 px desain, mulai di 20, 56) dan memenuhi kotaknya. */
    public function testFotoDepanBerasio3Banding4()
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

        $this->assertTrue($merah((20 + 27) * 3, (56 + 36) * 3), 'di dalam kotak foto harus terisi foto');
        $this->assertTrue($merah((20 + 50) * 3, (56 + 68) * 3), 'foto memenuhi kotak sampai pojok bawah');
        $this->assertFalse($merah((20 + 60) * 3, (56 + 36) * 3), 'di kanan kotak tidak boleh terisi foto');
        $this->assertFalse($merah((20 + 27) * 3, (56 + 76) * 3), 'di bawah kotak tidak boleh terisi foto');
    }

    /** Lengkung foto ruang baca di kanan depan terisi dan pojok kiri atas kartu tetap putih. */
    public function testLengkungFotoDiKananDepan()
    {
        $img    = imagecreatefromstring((new KartuPng())->depan($this->card));
        $putih  = fn (int $x, int $y) => (imagecolorat($img, $x, $y) & 0xFFFFFF) === 0xFFFFFF;

        $this->assertTrue($putih(100 * 3, 5 * 3), 'kolom kiri berlatar putih');
        $this->assertFalse($putih(300 * 3, 100 * 3), 'kanan kartu berisi foto, bukan putih');
    }

    /** Barcode di depan tergambar: ada piksel gelap di baris tengahnya dan bagian kanan barcode kosong. */
    public function testBarcodeDepanTergambar()
    {
        $img   = imagecreatefromstring((new KartuPng())->depan($this->card));
        $gelap = 0;
        for ($x = 20 * 3; $x < 132 * 3; $x++) {
            $gelap += (imagecolorat($img, $x, 190 * 3) & 0xFF) < 80 ? 1 : 0;
        }

        $this->assertGreaterThan(100, $gelap, 'barcode harus punya garis gelap');
        $this->assertSame(0xFFFFFF, imagecolorat($img, 140 * 3, 190 * 3) & 0xFFFFFF, 'di kanan barcode harus putih');
    }
}
