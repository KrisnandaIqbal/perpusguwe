<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Buku tamu anggota dan bukan anggota: nama, unit asal, dan nomor WhatsApp tersimpan.
 * Memakai database lokal; baris uji ditandai nama "Uji Otomatis" dan dihapus lagi.
 *
 * @internal
 */
final class BukuTamuTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $lokasi = db_connect()->table('locations')->select('ID')->get()->getRow();
        if (! $lokasi) {
            $this->markTestSkipped('Butuh minimal satu lokasi ruang di database lokal.');
        }

        // Kiosk buku tamu menyimpan lokasi ruang di cookie Location_id
        service('superglobals')->setCookie('Location_id', (string) $lokasi->ID);
    }

    /** ID anggota uji yang dibuat tes ini, dihapus di tearDown. */
    private array $anggotaUji = [];

    /** Anggota uji disalin dari anggota yang ada, dengan nama, unit, dan No. HP yang ditentukan tes. */
    private function anggotaUji(string $nama, ?int $unitKerjaId, string $hp): object
    {
        $db     = db_connect();
        $contoh = $db->table('members')->notLike('MemberNo', 'PENDING-', 'after')->get()->getRowArray();

        unset($contoh['ID']);
        $contoh['MemberNo']     = '98' . substr((string) hrtime(true), -9);
        $contoh['Fullname']     = $nama;
        $contoh['UnitKerja_id'] = $unitKerjaId;
        $contoh['NoHp']         = $hp;
        $contoh['Phone']        = $hp;
        $db->table('members')->insert($contoh);
        $this->anggotaUji[] = (int) $db->insertID();

        return (object) $contoh;
    }

    protected function tearDown(): void
    {
        if ($this->anggotaUji) {
            db_connect()->table('members')->whereIn('ID', $this->anggotaUji)->delete();
        }
        service('superglobals')->unsetCookie('Location_id');
        db_connect()->table('memberguesses')->like('Nama', 'Uji Otomatis')->delete();
        db_connect()->table('groupguesses')->like('NamaKetua', 'Uji Otomatis')->delete();
        parent::tearDown();
    }

    private function tamu(string $nama): ?object
    {
        return db_connect()->table('memberguesses')->where('Nama', $nama)->get()->getRow();
    }

    public function testBukanAnggotaTersimpanDenganUnitDanWhatsApp(): void
    {
        $result = $this->post('buku-tamu/non_anggota', [
            'Nama'            => 'Tamu Uji Otomatis',
            'UnitKerja_id'    => '2',
            'HP'              => '+62 812 0000 1111',
        ]);

        $result->assertRedirectTo('/buku-tamu/non_anggota');
        $tamu = $this->tamu('Tamu Uji Otomatis');
        $this->assertNotNull($tamu);
        $this->assertNull($tamu->NoAnggota);
        $this->assertSame('081200001111', $tamu->HP);
        $this->assertSame('2', (string) $tamu->UnitKerja_id);
        $this->assertNull($tamu->SubUnitKerja_id);
    }

    public function testBukanAnggotaDariLuarKemnaker(): void
    {
        $this->post('buku-tamu/non_anggota', [
            'Nama'         => 'Tamu Luar Uji Otomatis',
            'UnitKerja_id' => 'lainnya',
            'UnitLainnya'  => 'Universitas Indonesia',
            'HP'           => '081200002222',
        ]);

        $tamu = $this->tamu('Tamu Luar Uji Otomatis');
        $this->assertNotNull($tamu);
        $this->assertNull($tamu->UnitKerja_id);
        $this->assertSame('Universitas Indonesia', $tamu->UnitLainnya);
    }

    public function testIsianSalahTidakTersimpanDanPesannyaTampil(): void
    {
        $result = $this->post('buku-tamu/non_anggota', [
            'Nama'            => 'Tamu Salah Uji Otomatis',
            'UnitKerja_id'    => '2',
            'HP'              => '12345',
        ]);

        $result->assertOK();
        $result->assertSee('Nomor WhatsApp diawali 08 atau 62');
        $this->assertStringContainsString('value="Tamu Salah Uji Otomatis"', $result->getBody());
        $this->assertNull($this->tamu('Tamu Salah Uji Otomatis'));
    }

    public function testTanpaLokasiRuangKunjunganTetapTersimpan(): void
    {
        service('superglobals')->unsetCookie('Location_id');

        $this->post('buku-tamu/non_anggota', [
            'Nama'         => 'Tamu Tanpa Ruang Uji Otomatis',
            'UnitKerja_id' => 'lainnya',
            'UnitLainnya'  => 'Universitas Indonesia',
            'HP'           => '081200005555',
        ])->assertRedirectTo('/buku-tamu/non_anggota');

        $tamu = $this->tamu('Tamu Tanpa Ruang Uji Otomatis');
        $this->assertNotNull($tamu);
        $this->assertNull($tamu->Location_Id);
    }

    public function testFormBukanAnggotaTidakLagiMenanyakanIsianLama(): void
    {
        // Tanpa lokasi ruang pun form langsung tampil, tidak dialihkan ke halaman Atur Lokasi
        service('superglobals')->unsetCookie('Location_id');
        $result = $this->get('buku-tamu/non_anggota');

        $result->assertOK();
        $this->assertStringContainsString('name="HP"', $result->getBody());
        $result->assertDontSee('name="Profesi_id"');
        $result->assertDontSee('name="PendidikanTerakhir_id"');
        $result->assertDontSee('name="JenisKelamin_id"');
        $result->assertDontSee('name="Alamat"');
        $result->assertDontSee('>Kementerian Ketenagakerjaan Republik Indonesia<');
        $result->assertDontSee('name="TujuanKunjungan_id"');
        $result->assertDontSee('Total Kunjungan Hari ini');
    }

    public function testAnggotaTersimpanDenganNomorAnggota(): void
    {
        $anggota = db_connect()->table('members')->notLike('MemberNo', 'PENDING-', 'after')->get()->getRow();

        $this->post('buku-tamu/store_anggota', [
            'member_no'       => $anggota->MemberNo,
            'Nama'            => 'Anggota Uji Otomatis',
            'UnitKerja_id'    => '2',
            'HP'              => '081200003333',
        ]);

        $tamu = $this->tamu('Anggota Uji Otomatis');
        $this->assertNotNull($tamu);
        $this->assertSame($anggota->MemberNo, $tamu->NoAnggota);
        $this->assertSame('081200003333', $tamu->HP);
    }

    public function testAnggotaDenganIsianSalahDikembalikanKeFormnya(): void
    {
        $anggota = db_connect()->table('members')->notLike('MemberNo', 'PENDING-', 'after')->get()->getRow();

        $result = $this->post('buku-tamu/store_anggota', [
            'member_no'    => $anggota->MemberNo,
            'Nama'         => 'Anggota Salah Uji Otomatis',
            'UnitKerja_id' => '',
            'HP'           => '081200004444',
        ]);

        $result->assertRedirect();
        $this->assertSame(base_url('buku-tamu') . '?member_no=' . rawurlencode($anggota->MemberNo), $result->response()->getHeaderLine('Location'));
        $this->assertNull($this->tamu('Anggota Salah Uji Otomatis'));
    }

    public function testAnggotaDenganDataLengkapLangsungTercatat(): void
    {
        $anggota = $this->anggotaUji('Anggota Lengkap Uji Otomatis', 2, '081200005555');

        $result = $this->get('buku-tamu?member_no=' . rawurlencode($anggota->MemberNo));

        $result->assertRedirectTo(base_url('buku-tamu'));
        $tamu = $this->tamu('Anggota Lengkap Uji Otomatis');
        $this->assertNotNull($tamu);
        $this->assertSame($anggota->MemberNo, $tamu->NoAnggota);
        $this->assertSame('081200005555', $tamu->HP);
    }

    public function testDataAnggotaMengisiFormOtomatis(): void
    {
        // Tanpa satuan kerja, data belum lengkap: form tampil terisi dan kunjungan belum tercatat
        $anggota = $this->anggotaUji('Anggota Belum Lengkap Uji Otomatis', null, '081200006666');

        $result = $this->get('buku-tamu?member_no=' . rawurlencode($anggota->MemberNo));

        $result->assertOK();
        $this->assertNull($this->tamu('Anggota Belum Lengkap Uji Otomatis'));
        $this->assertStringContainsString('value="081200006666"', $result->getBody());
        $result->assertSee('Simpan Kunjungan');
        $this->assertStringNotContainsString('name="SubUnitKerja_id"', $result->getBody());
        $this->assertStringNotContainsString('name="TujuanKunjungan_id"', $result->getBody());
    }

    private function rombongan(string $nama): ?object
    {
        return db_connect()->table('groupguesses')->where('NamaKetua', $nama)->get()->getRow();
    }

    public function testRombonganKemnakerTersimpanDenganUnit(): void
    {
        $this->post('buku-tamu/rombongan', [
            'Jenis'           => 'kemnaker',
            'Nama'            => 'Ketua Kemnaker Uji Otomatis',
            'UnitKerja_id'    => '2',
            'HP'              => '+62 812 0000 6666',
            'CountPersonel'   => '12',
        ])->assertRedirectTo('/buku-tamu/rombongan');

        $r = $this->rombongan('Ketua Kemnaker Uji Otomatis');
        $this->assertNotNull($r);
        $this->assertSame('081200006666', $r->NomerTelponKetua);
        $this->assertSame('2', (string) $r->UnitKerja_id);
        $this->assertSame('Sekretariat Jenderal', $r->AsalInstansi);
        $this->assertSame('12', (string) $r->CountPersonel);
    }

    public function testRombonganLainnyaTersimpanDenganNamaInstansi(): void
    {
        $this->post('buku-tamu/rombongan', [
            'Jenis'         => 'lainnya',
            'Nama'          => 'Ketua Lainnya Uji Otomatis',
            'UnitLainnya'   => 'SMA Negeri 1 Jakarta',
            'HP'            => '081200007777',
            'CountPersonel' => '30',
        ]);

        $r = $this->rombongan('Ketua Lainnya Uji Otomatis');
        $this->assertNotNull($r);
        $this->assertNull($r->UnitKerja_id);
        $this->assertSame('SMA Negeri 1 Jakarta', $r->AsalInstansi);
    }

    public function testRombonganKurangDariEnamOrangDitolak(): void
    {
        $result = $this->post('buku-tamu/rombongan', [
            'Jenis'         => 'lainnya',
            'Nama'          => 'Ketua Kecil Uji Otomatis',
            'UnitLainnya'   => 'SMA Negeri 1 Jakarta',
            'HP'            => '081200008888',
            'CountPersonel' => '5',
        ]);

        $result->assertOK();
        $result->assertSee('Rombongan paling sedikit 6 orang');
        $this->assertNull($this->rombongan('Ketua Kecil Uji Otomatis'));
    }

    public function testFormRombonganHanyaMenanyakanIsianBaru(): void
    {
        $result = $this->get('buku-tamu/rombongan');

        $result->assertOK();
        $body = $result->getBody();
        $this->assertStringContainsString('value="kemnaker"', $body);
        $this->assertStringContainsString('name="CountPersonel"', $body);
        foreach (['EmailInstansi', 'AlamatInstansi', 'CountLaki', 'CountPNS', 'CountSD', 'TujuanKunjungan_ID'] as $lama) {
            $this->assertStringNotContainsString('name="' . $lama . '"', $body);
        }
        $result->assertDontSee('Total Kunjungan Hari ini');
    }
}
