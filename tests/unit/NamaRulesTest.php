<?php

use App\Validation\NamaRules;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Nama anggota hanya boleh huruf A-Z dan tanda baca yang lazim di nama.
 *
 * @internal
 */
final class NamaRulesTest extends CIUnitTestCase
{
    /** @dataProvider namaProvider */
    public function testNamaOrang(string $nama, bool $harapan)
    {
        $this->assertSame($harapan, (new NamaRules())->nama_orang($nama));
    }

    public function namaProvider(): array
    {
        return [
            'nama biasa'                    => ['Krisnanda Muhammad Iqbal', true],
            'huruf kecil semua'             => ['rizky ramadhan', true],
            'apostrof'                      => ["Ma'ruf Amin", true],
            'apostrof dari keyboard ponsel' => ['Ma’ruf Amin', true],
            'tanda hubung'                  => ['Siti-Nur Aisyah', true],
            'singkatan bertitik'            => ['M. Iqbal', true],
            'gelar dengan koma'             => ['Andi, S.Kom', true],
            'spasi di ujung diabaikan'      => ['  Andra Wijaya ', true],
            'simbol hias'                   => ['꧁☠︎₭iℒℒℰℛ☠︎꧂', false],
            'huruf hias Unicode'            => ['ℒucky', false],
            'angka'                         => ['Iqbal123', false],
            'emoji'                         => ['Iqbal 😀', false],
            'tag HTML'                      => ['<b>Iqbal</b>', false],
            'diawali tanda baca'            => ['.Iqbal', false],
            'huruf beraksen'                => ['Élodie', false],
            'kosong'                        => ['', false],
            'spasi saja'                    => ['   ', false],
        ];
    }
}
