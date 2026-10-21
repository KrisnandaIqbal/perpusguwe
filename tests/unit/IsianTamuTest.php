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
    private array $unitIds = [2, 9];

    private function periksa(array $post): array
    {
        return IsianTamu::periksa($post + [
            'Nama'            => 'Budi Santoso',
            'UnitKerja_id'    => '2',
            'UnitLainnya'     => '',
            'HP'              => '0812 3456 7890',
        ], $this->unitIds);
    }

    public function testIsianLengkapDisimpanDenganNomorDirapikan(): void
    {
        $hasil = $this->periksa([]);

        $this->assertSame([], $hasil['errors']);
        $this->assertSame([
            'Nama'         => 'Budi Santoso',
            'UnitKerja_id' => 2,
            'UnitLainnya'  => null,
            'HP'           => '081234567890',
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

    public function testCukupSatuanKerjaTanpaBiroAtauUnit(): void
    {
        $hasil = $this->periksa(['UnitKerja_id' => '9']);

        $this->assertSame([], $hasil['errors']);
        $this->assertArrayNotHasKey('SubUnitKerja_id', $hasil['data']);
    }

    public function testSatuanKerjaTidakDikenalDitolak(): void
    {
        $this->assertArrayHasKey('UnitKerja_id', $this->periksa(['UnitKerja_id' => '77'])['errors']);
    }

    public function testLainnyaMemintaNamaInstansi(): void
    {
        $this->assertArrayHasKey('UnitLainnya', $this->periksa(['UnitKerja_id' => IsianTamu::LAINNYA])['errors']);

        $hasil = $this->periksa(['UnitKerja_id' => IsianTamu::LAINNYA, 'UnitLainnya' => ' Universitas Indonesia ']);

        $this->assertSame([], $hasil['errors']);
        $this->assertNull($hasil['data']['UnitKerja_id']);
        $this->assertSame('Universitas Indonesia', $hasil['data']['UnitLainnya']);
    }

    private function periksaRombongan(array $post): array
    {
        return IsianTamu::periksaRombongan($post + [
            'Jenis'           => 'kemnaker',
            'Nama'            => 'Budi Santoso',
            'UnitKerja_id'    => '2',
            'UnitLainnya'     => '',
            'HP'              => '081234567890',
            'CountPersonel'   => '12',
        ], $this->unitIds);
    }

    public function testRombonganKemnakerMemakaiUnit(): void
    {
        $hasil = $this->periksaRombongan(['UnitLainnya' => 'Diabaikan']);

        $this->assertSame([], $hasil['errors']);
        $this->assertSame(2, $hasil['data']['UnitKerja_id']);
        $this->assertNull($hasil['data']['UnitLainnya']);
        $this->assertSame(12, $hasil['data']['CountPersonel']);
    }

    public function testRombonganLainnyaMemakaiNamaInstansi(): void
    {
        $hasil = $this->periksaRombongan(['Jenis' => 'lainnya', 'UnitLainnya' => 'SMA Negeri 1 Jakarta']);

        $this->assertSame([], $hasil['errors']);
        $this->assertNull($hasil['data']['UnitKerja_id']);
        $this->assertSame('SMA Negeri 1 Jakarta', $hasil['data']['UnitLainnya']);

        $this->assertArrayHasKey('UnitLainnya', $this->periksaRombongan(['Jenis' => 'lainnya'])['errors']);
    }

    public function testRombonganKemnakerTidakBolehMemilihLainnya(): void
    {
        $this->assertArrayHasKey('UnitKerja_id', $this->periksaRombongan(['UnitKerja_id' => IsianTamu::LAINNYA])['errors']);
    }

    public function testRombonganWajibMemilihJenis(): void
    {
        $hasil = $this->periksaRombongan(['Jenis' => '']);

        $this->assertSame(['Jenis'], array_keys($hasil['errors']));
    }

    public function testRombonganPalingSedikitEnamOrang(): void
    {
        $this->assertArrayHasKey('CountPersonel', $this->periksaRombongan(['CountPersonel' => '5'])['errors']);
        $this->assertArrayHasKey('CountPersonel', $this->periksaRombongan(['CountPersonel' => ''])['errors']);
        $this->assertArrayHasKey('CountPersonel', $this->periksaRombongan(['CountPersonel' => '6.5'])['errors']);
        $this->assertArrayNotHasKey('CountPersonel', $this->periksaRombongan(['CountPersonel' => '6'])['errors']);
    }

    public function testTeksUnitUntukDitampilkan(): void
    {
        helper('anggota');

        $this->assertSame('Sekretariat Jenderal - Biro Hukum', IsianTamu::teksUnit('Sekretariat Jenderal', 'Biro Hukum', null));
        $this->assertSame('Universitas Indonesia', IsianTamu::teksUnit(null, null, 'Universitas Indonesia'));
        $this->assertSame('-', IsianTamu::teksUnit(null, null, null));
    }
}
