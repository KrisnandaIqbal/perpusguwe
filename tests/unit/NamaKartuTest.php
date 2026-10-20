<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Nama di kartu anggota: huruf besar di awal kata, tanpa merusak nama yang sengaja diketik.
 *
 * @internal
 */
final class NamaKartuTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('anggota');
    }

    /** @dataProvider namaProvider */
    public function testNamaKartu(string $masukan, string $harapan)
    {
        $this->assertSame($harapan, namaKartu($masukan));
    }

    public function namaProvider(): array
    {
        return [
            'huruf kecil semua'         => ['rizky ramadhan', 'Rizky Ramadhan'],
            'kapital semua'             => ['RIZKY RAMADHAN', 'Rizky Ramadhan'],
            'satu kata'                 => ['andra', 'Andra'],
            'sudah benar'               => ['Rizky Ramadhan', 'Rizky Ramadhan'],
            'campuran dibiarkan'        => ['Muhammad McDonald', 'Muhammad McDonald'],
            'per kata'                  => ['rizky Ramadhan', 'Rizky Ramadhan'],
            'tanda hubung'              => ['siti-nur aisyah', 'Siti-Nur Aisyah'],
            'gelar bertitik'            => ['andi s.kom', 'Andi S.Kom'],
            'apostrof tidak dikapital'  => ["ma'ruf amin", "Ma'ruf Amin"],
            'huruf beraksen'            => ['ÉLODIE', 'Élodie'],
            'spasi berlebih dirapikan'  => ['  andra   wijaya ', 'Andra Wijaya'],
        ];
    }

    /** @dataProvider barisProvider */
    public function testNamaKartuBaris(string $nama, array $harapan)
    {
        $this->assertSame($harapan, namaKartuBaris($nama));
    }

    public function barisProvider(): array
    {
        return [
            'satu kata satu baris'                    => ['Andra', ['Andra']],
            'dua kata satu baris'                     => ['Rizky Ramadhan', ['Rizky Ramadhan']],
            'tepat 15 huruf satu baris'               => ['Krisnanda Iqbal', ['Krisnanda Iqbal']],
            'dua kata lebih dari 15 huruf dua baris'  => ['Bambang Sutrisno', ['Bambang', 'Sutrisno']],
            'tiga kata dua baris, panjang seimbang'   => ['Krisnanda Muhammad Iqbal', ['Krisnanda', 'Muhammad Iqbal']],
            'empat kata yang muat tidak disingkat'    => ['Siti Nur Aisyah Putri', ['Siti Nur', 'Aisyah Putri']],
            'empat kata, kata terakhir disingkat'     => ['Muhammad Rizky Ramadhan Saputra', ['Muhammad Rizky', 'Ramadhan S.']],
            'kepanjangan: kata terakhir disingkat'    => ['Muhammad Rizky Ramadhan Saputra Wijayakusuma', ['Muhammad Rizky', 'Ramadhan S. W.']],
            'masih kepanjangan: singkat kata sebelum' => ['Muhammad Rizky Ramadhan Saputrawijaya Kusumaningrat', ['Muhammad Rizky', 'Ramadhan S. K.']],
            'satu kata kepanjangan dibiarkan'         => ['Krisnandamuhammadiqbalsaputra', ['Krisnandamuhammadiqbalsaputra']],
            'dua kata kepanjangan'                    => ['Krisnandamuhammad Iqbalsaputrawijaya', ['Krisnandamuhammad', 'I.']],
        ];
    }
}
