<?php

use CodeIgniter\Test\CIUnitTestCase;
use Rekomendasi\Libraries\UsulanOtomatis;

/** @internal */
final class UsulanOtomatisTest extends CIUnitTestCase
{
    /** @dataProvider normalProvider */
    public function testNormal(string $query, string $harapan)
    {
        $this->assertSame($harapan, UsulanOtomatis::normal($query));
    }

    public function normalProvider(): array
    {
        return [
            'huruf kecil'        => ['Laskar PELANGI', 'laskar pelangi'],
            'rapikan spasi'      => ["  laskar \t  pelangi  ", 'laskar pelangi'],
            'tanda baca ujung'   => ['laskar pelangi?!', 'laskar pelangi'],
            'titik di ujung'     => ['Manajemen SDM.', 'manajemen sdm'],
            'tanda baca tengah'  => ['a.b, c', 'a.b, c'],
            'tanda baca + spasi' => ['laskar pelangi ... ', 'laskar pelangi'],
            'unicode'            => ['ÉTUDE  Ünï', 'étude ünï'],
            'kosong'             => ['   ', ''],
            'hanya tanda baca'   => ['???', ''],
        ];
    }

    /** @dataProvider ambangProvider */
    public function testTampil(int $dicari, int $klik, bool $harapan)
    {
        $this->assertSame($harapan, UsulanOtomatis::tampil($dicari, $klik));
    }

    public function ambangProvider(): array
    {
        return [
            'dicari 2 kali tidak muncul' => [2, 0, false],
            'dicari 3 kali muncul'       => [3, 0, true],
            'dicari 1 kali'              => [1, 0, false],
            'onesearch 1 klik'           => [0, 1, false],
            'onesearch 2 klik muncul'    => [0, 2, true],
            'dicari 2 + onesearch 1'     => [2, 1, false],
            'baru dibuat'                => [0, 0, false],
        ];
    }
}
