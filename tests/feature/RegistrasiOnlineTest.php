<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * Pengujian form registrasi online dan submit pendaftaran:
 * - Default ke ASN & NIP 18 digit
 * - Kategori dan Magang telah dihilangkan
 * - Teks Hak Akses "10 hari kerja"
 * - Alur verifikasi NIP
 *
 * @internal
 */
final class RegistrasiOnlineTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private int $createdMemberId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $dbConfig = config('Database');
        $dbConfig->tests = $dbConfig->default;

        Services::injectMock('email', new MockEmail(config('Email')));
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        if ($this->createdMemberId > 0) {
            db_connect()->table('members')->where('ID', $this->createdMemberId)->delete();
        }
        parent::tearDown();
    }

    public function testHalamanPendaftaranMemuatTeksYangSesuai(): void
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $result = $this->get('registrasi');
        $result->assertStatus(200);

        $body = $result->response()->getBody();

        // 1. Teks NIP ada, Kategori radio cards dan Magang tidak ada di DOM HTML
        $this->assertStringContainsString('Nomor Induk Pegawai (NIP)', $body);
        $this->assertStringNotContainsString('id="jenisAnggotaCards"', $body);
        $this->assertStringNotContainsString('name="status_non_asn"', $body);
        $this->assertStringNotContainsString('Magang Nasional', $body);
        $this->assertStringNotContainsString('Magang Reguler', $body);

        // 2. Hak akses 10 hari kerja (bukan 14 hari kalender)
        $this->assertStringContainsString('10 hari kerja', $body);
        $this->assertStringNotContainsString('14 hari kalender', $body);

        // 3. Alur verifikasi NIP
        $this->assertStringContainsString('Pustakawan memvalidasi NIP dalam 1&times;24 jam kerja.', $body);
        $this->assertStringNotContainsString('NIP/NIK', $body);

        // 4. WhatsApp di form dialihkan ke email / nomor handphone
        $this->assertStringContainsString('Nomor Handphone Aktif', $body);
        $this->assertStringNotContainsString('Nomor WhatsApp Aktif', $body);
    }

    public function testSubmitValidMenyimpanSebagaiAsnDanNip(): void
    {
        $db = db_connect();

        // Cari UnitKerja valid yang memiliki SubUnit
        $subUnit = $db->table('master_subunit')->where('active', 1)->get()->getRow();
        if (!$subUnit) {
            $this->markTestSkipped('Butuh data master_subunit di database.');
        }

        $sex = $db->table('jenis_kelamin')->where('active', 1)->get()->getRow();
        $testNip = '199001012020121' . rand(100, 999);
        $testEmail = 'uji.' . rand(1000, 9999) . '@example.com';
        $testPhone = '0812' . rand(10000000, 99999999);

        $postData = [
            'IdentityNo'     => $testNip,
            'Fullname'       => 'Pendaftar Uji Otomatis',
            'Sex_id'         => $sex->ID,
            'UnitKerja_id'   => $subUnit->unit_id,
            'SubUnitKerja_id'=> $subUnit->id,
            'AddressNow'     => 'Jl. Pengujian No. 123, Jakarta',
            'Email'          => $testEmail,
            'Phone'          => $testPhone,
            'Persetujuan'    => '1',
        ];

        $result = $this->post('registrasi/submit', $postData);
        $json = json_decode($result->response()->getBody(), true);

        $this->assertTrue($json['success'] ?? false, 'Submit gagal: ' . ($json['message'] ?? ''));

        // Cek data di database
        $member = $db->table('members')->where('IdentityNo', $testNip)->get()->getRow();
        $this->assertNotNull($member);
        $this->createdMemberId = (int) $member->ID;

        // Jenis Anggota harus ASN dan status_non_asn null
        $asnRow = $db->table('jenis_anggota')->where('id', $member->JenisAnggota_id)->get()->getRow();
        $this->assertStringContainsString('ASN', $asnRow->jenisanggota);
        $this->assertNull($member->status_non_asn);
    }

    public function testSubmitFormatNipSalahDitolak(): void
    {
        $db = db_connect();
        $subUnit = $db->table('master_subunit')->where('active', 1)->get()->getRow();
        if (!$subUnit) {
            $this->markTestSkipped('Butuh data master_subunit di database.');
        }

        $postData = [
            'IdentityNo'     => '3171010101900001', // 16 digit NIK
            'Fullname'       => 'Pendaftar NIK Salah',
            'Sex_id'         => 1,
            'UnitKerja_id'   => $subUnit->unit_id,
            'SubUnitKerja_id'=> $subUnit->id,
            'AddressNow'     => 'Jl. Uji',
            'Email'          => 'uji.nik@example.com',
            'Phone'          => '081299998888',
            'Persetujuan'    => '1',
        ];

        $result = $this->post('registrasi/submit', $postData);
        $json = json_decode($result->response()->getBody(), true);

        $this->assertFalse($json['success'] ?? true);
        $this->assertStringContainsString('18 digit angka resmi BKN', $json['message'] ?? '');
    }
}
