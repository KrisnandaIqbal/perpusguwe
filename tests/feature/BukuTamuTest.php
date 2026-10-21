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

    private int $subUnitId;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $lokasi = db_connect()->table('locations')->select('ID')->get()->getRow();
        $sub    = db_connect()->table('master_subunit')->where('unit_id', 2)->where('active', 1)->get()->getRow();
        if (! $lokasi || ! $sub) {
            $this->markTestSkipped('Butuh lokasi ruang dan unit Sekretariat Jenderal di database lokal.');
        }

        // Kiosk buku tamu menyimpan lokasi ruang di cookie Location_id
        service('superglobals')->setCookie('Location_id', (string) $lokasi->ID);
        $this->subUnitId = (int) $sub->id;
    }

    protected function tearDown(): void
    {
        service('superglobals')->unsetCookie('Location_id');
        db_connect()->table('memberguesses')->like('Nama', 'Uji Otomatis')->delete();
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
            'SubUnitKerja_id' => (string) $this->subUnitId,
            'HP'              => '+62 812 0000 1111',
        ]);

        $result->assertRedirectTo('/buku-tamu/non_anggota');
        $tamu = $this->tamu('Tamu Uji Otomatis');
        $this->assertNotNull($tamu);
        $this->assertNull($tamu->NoAnggota);
        $this->assertSame('081200001111', $tamu->HP);
        $this->assertSame('2', (string) $tamu->UnitKerja_id);
        $this->assertSame((string) $this->subUnitId, (string) $tamu->SubUnitKerja_id);
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
            'SubUnitKerja_id' => (string) $this->subUnitId,
            'HP'              => '12345',
        ]);

        $result->assertOK();
        $result->assertSee('Nomor WhatsApp diawali 08 atau 62');
        $this->assertStringContainsString('value="Tamu Salah Uji Otomatis"', $result->getBody());
        $this->assertNull($this->tamu('Tamu Salah Uji Otomatis'));
    }

    public function testFormBukanAnggotaTidakLagiMenanyakanIsianLama(): void
    {
        $result = $this->get('buku-tamu/non_anggota');

        $result->assertOK();
        $this->assertStringContainsString('name="HP"', $result->getBody());
        $result->assertDontSee('name="Profesi_id"');
        $result->assertDontSee('name="PendidikanTerakhir_id"');
        $result->assertDontSee('name="JenisKelamin_id"');
        $result->assertDontSee('name="Alamat"');
        $result->assertDontSee('>Kementerian Ketenagakerjaan Republik Indonesia<');
    }

    public function testAnggotaTersimpanDenganNomorAnggota(): void
    {
        $anggota = db_connect()->table('members')->notLike('MemberNo', 'PENDING-', 'after')->get()->getRow();

        $this->post('buku-tamu/store_anggota', [
            'member_no'       => $anggota->MemberNo,
            'Nama'            => 'Anggota Uji Otomatis',
            'UnitKerja_id'    => '2',
            'SubUnitKerja_id' => (string) $this->subUnitId,
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

    public function testDataAnggotaMengisiFormOtomatis(): void
    {
        $anggota = db_connect()->table('members')
            ->notLike('MemberNo', 'PENDING-', 'after')
            ->where("COALESCE(NULLIF(NoHp, ''), Phone) IS NOT NULL", null, false)
            ->get()->getRow();

        $result = $this->get('buku-tamu?member_no=' . rawurlencode($anggota->MemberNo));

        $result->assertOK();
        $this->assertStringContainsString('value="' . ($anggota->NoHp ?: $anggota->Phone) . '"', $result->getBody());
        $result->assertSee('Simpan Kunjungan');
    }
}
