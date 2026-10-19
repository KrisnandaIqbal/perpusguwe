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
}
