<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Isi lembar data anggota (PDF dari tombol Ekspor PDF di Daftar Anggota).
 *
 * @internal
 */
final class LembarDataAnggotaPdfTest extends CIUnitTestCase
{
    private function render(array $member): string
    {
        helper('anggota');

        return view('Anggota\Views\export_pdf', [
            'namaPerpustakaan'   => 'PERPUSTAKAAN KEMENTERIAN KETENAGAKERJAAN',
            'alamatPerpustakaan' => '',
            'petugasNama'        => 'Petugas',
            'tglCetak'           => '21/10/2026',
            'membersData'        => [[
                'member'    => (object) array_merge([
                    'Fullname'       => 'Budi Santoso',
                    'MemberNo'       => '26102100001',
                    'StatusAnggota'  => 'Aktif',
                    'JenisAnggota'   => 'ASN',
                    'JenisIdentitas' => 'NIP',
                    'IdentityNo'     => '199001012020121001',
                    'UnitKerja'      => null,
                    'SubUnitKerja'   => null,
                    'Email'          => 'budi@example.com',
                    'Phone'          => '081234567890',
                    'AddressNow'     => 'Jakarta',
                    'RegisterDate'   => '2026-10-21',
                    'EndDate'        => '2027-10-21',
                    'status_non_asn' => null,
                ], $member),
                'photoPath' => null,
            ]],
        ], ['debug' => false]);
    }

    public function testLabelTanpaKeteranganTambahan(): void
    {
        $html = $this->render([]);

        foreach ([
            'Data Terverifikasi Berdasarkan Formulir Pendaftaran Online Pemustaka',
            '(Pendaftaran Online)',
            '(Tanpa Gelar)',
            'Alamat Email Aktif',
            'HP Aktif',
            'Nomor Induk Pegawai (NIP)',
            '(NIP)',
            'Kementerian Ketenagakerjaan RI',
            'Halaman 1 dari',
            '3 &times; 4',
        ] as $dihapus) {
            $this->assertStringNotContainsString($dihapus, $html);
        }

        $this->assertStringContainsString('Nomor Induk Pegawai', $html);
        $this->assertStringContainsString('Dicetak dari Sistem Perpustakaan Kemnaker RI pada:', $html);
        $this->assertStringContainsString('Pasfoto<br>2 &times; 3', $html);
    }

    public function testNikTanpaKeteranganDalamKurung(): void
    {
        $html = $this->render(['JenisAnggota' => 'Non ASN', 'JenisIdentitas' => 'NIK', 'status_non_asn' => 'staf']);

        $this->assertStringContainsString('Nomor Induk Kependudukan', $html);
        $this->assertStringNotContainsString('(NIK)', $html);
    }
}
