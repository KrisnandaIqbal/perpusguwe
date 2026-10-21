<?php

use CodeIgniter\Test\CIUnitTestCase;
use GuestBook\Libraries\IsianTamu;

/**
 * Isian buku tamu: nama, unit asal, dan nomor WhatsApp.
 *
 * @internal
 */
final class IsianTamuTest extends CIUnitTestCase
{
    /** Satuan kerja 2 punya dua unit, satuan kerja 9 tidak punya unit. */
    private array $unitIds = [2, 9];
    private array $subUnits = [2 => [['id' => 21], ['id' => 22]]];

    private function periksa(array $post): array
    {
        return IsianTamu::periksa($post + [
            'Nama'            => 'Budi Santoso',
            'UnitKerja_id'    => '2',
            'SubUnitKerja_id' => '21',
            'UnitLainnya'     => '',
            'HP'              => '0812 3456 7890',
        ], $this->unitIds, $this->subUnits);
    }

    public function testIsianLengkapDisimpanDenganNomorDirapikan(): void
    {
        $hasil = $this->periksa([]);

        $this->assertSame([], $hasil['errors']);
        $this->assertSame([
            'Nama'            => 'Budi Santoso',
            'UnitKerja_id'    => 2,
            'SubUnitKerja_id' => 21,
            'UnitLainnya'     => null,
            'HP'              => '081234567890',
        ], $hasil['data']);
    }

    public function testNomor62DiubahJadi08(): void
    {
        $this->assertSame('081234567890', $this->periksa(['HP' => '+62 812-3456-7890'])['data']['HP']);
    }

    public function testIsianKosongDitolakPerKolom(): void
    {
        $hasil = $this->periksa(['Nama' => '  ', 'UnitKerja_id' => '', 'HP' => '']);

        $this->assertArrayHasKey('Nama', $hasil['errors']);
        $this->assertArrayHasKey('UnitKerja_id', $hasil['errors']);
        $this->assertArrayHasKey('HP', $hasil['errors']);
    }

    public function testNomorBukanSelulerDitolak(): void
    {
        $this->assertArrayHasKey('HP', $this->periksa(['HP' => '021123456'])['errors']);
    }

    public function testUnitWajibKalauSatuanKerjaPunyaUnit(): void
    {
        $this->assertArrayHasKey('SubUnitKerja_id', $this->periksa(['SubUnitKerja_id' => ''])['errors']);
        $this->assertArrayHasKey('SubUnitKerja_id', $this->periksa(['SubUnitKerja_id' => '99'])['errors']);
    }

    public function testSatuanKerjaTanpaUnitTidakMemintaUnit(): void
    {
        $hasil = $this->periksa(['UnitKerja_id' => '9', 'SubUnitKerja_id' => '']);

        $this->assertSame([], $hasil['errors']);
        $this->assertNull($hasil['data']['SubUnitKerja_id']);
    }

    public function testSatuanKerjaTidakDikenalDitolak(): void
    {
        $this->assertArrayHasKey('UnitKerja_id', $this->periksa(['UnitKerja_id' => '77'])['errors']);
    }

    public function testLainnyaMemintaNamaInstansi(): void
    {
        $this->assertArrayHasKey('UnitLainnya', $this->periksa(['UnitKerja_id' => IsianTamu::LAINNYA])['errors']);

        $hasil = $this->periksa(['UnitKerja_id' => IsianTamu::LAINNYA, 'SubUnitKerja_id' => '21', 'UnitLainnya' => ' Universitas Indonesia ']);

        $this->assertSame([], $hasil['errors']);
        $this->assertNull($hasil['data']['UnitKerja_id']);
        $this->assertNull($hasil['data']['SubUnitKerja_id']);
        $this->assertSame('Universitas Indonesia', $hasil['data']['UnitLainnya']);
    }

    public function testTeksUnitUntukDitampilkan(): void
    {
        helper('anggota');

        $this->assertSame('Sekretariat Jenderal - Biro Hukum', IsianTamu::teksUnit('Sekretariat Jenderal', 'Biro Hukum', null));
        $this->assertSame('Universitas Indonesia', IsianTamu::teksUnit(null, null, 'Universitas Indonesia'));
        $this->assertSame('-', IsianTamu::teksUnit(null, null, null));
    }
}
