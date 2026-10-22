<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Export buku tamu: satu file Excel dengan sheet Anggota, Bukan Anggota, dan Rombongan, dibatasi rentang tanggal.
 * Memakai database lokal; baris uji ditandai nama "Uji Otomatis" dan dihapus lagi.
 *
 * @internal
 */
final class ExportBukuTamuTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private int $adminId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        // Membaca XLSX di tes butuh ekstensi zip; jalankan dengan: php -d extension=zip vendor/bin/phpunit
        if (! class_exists(ZipArchive::class)) {
            $this->markTestSkipped('Ekstensi PHP zip tidak aktif.');
        }

        $admin = db_connect()->table('users')->select('id')->get()->getRow();
        if (! $admin) {
            $this->markTestSkipped('Butuh minimal satu akun admin di database lokal.');
        }
        $this->adminId = (int) $admin->id;

        $db = db_connect();
        $db->table('memberguesses')->insertBatch([
            ['Nama' => 'Anggota Ekspor Uji Otomatis', 'NoAnggota' => '990000111', 'UnitKerja_id' => 2, 'UnitLainnya' => null, 'HP' => '081200001111', 'CreateDate' => '2026-09-10 09:00:00'],
            ['Nama' => 'Tamu Ekspor Uji Otomatis', 'NoAnggota' => null, 'UnitKerja_id' => null, 'UnitLainnya' => 'Universitas Contoh', 'HP' => '081200002222', 'CreateDate' => '2026-09-11 10:00:00'],
            ['Nama' => 'Anggota Lama Uji Otomatis', 'NoAnggota' => '990000222', 'UnitKerja_id' => 2, 'UnitLainnya' => null, 'HP' => '081200003333', 'CreateDate' => '2026-08-01 09:00:00'],
        ]);
        $db->table('groupguesses')->insert([
            'NamaKetua' => 'Ketua Ekspor Uji Otomatis', 'NomerTelponKetua' => '081200004444', 'AsalInstansi' => 'SMA Negeri 1 Contoh',
            'AlamatInstansi' => '', 'CountPersonel' => 9, 'CountTk' => 0, 'CreateDate' => '2026-09-12 13:00:00',
        ]);
    }

    protected function tearDown(): void
    {
        db_connect()->table('memberguesses')->like('Nama', 'Uji Otomatis')->delete();
        db_connect()->table('groupguesses')->like('NamaKetua', 'Uji Otomatis')->delete();
        parent::tearDown();
    }

    private function unduh(string $query): Spreadsheet
    {
        $result = $this->withSession(['logged_in' => $this->adminId])->get('bukutamu/export?' . $query);
        $result->assertOK();
        $this->assertStringContainsString('spreadsheetml', $result->response()->getHeaderLine('Content-Type'));

        $berkas = tempnam(sys_get_temp_dir(), 'bukutamu') . '.xlsx';
        file_put_contents($berkas, $result->response()->getBody());
        $excel = IOFactory::load($berkas);
        unlink($berkas);

        return $excel;
    }

    /** Semua isi sel satu sheet digabung jadi teks, untuk dicari. */
    private static function isi(Spreadsheet $excel, string $sheet): string
    {
        return implode("\n", array_map(static fn ($baris) => implode('|', $baris), $excel->getSheetByName($sheet)->toArray()));
    }

    public function testSatuFileTigaSheetDanBarisMasukSheetYangBenar(): void
    {
        $excel = $this->unduh('dari=2026-09-01&sampai=2026-09-30');

        $this->assertSame(['Anggota', 'Bukan Anggota', 'Rombongan'], $excel->getSheetNames());

        $anggota = self::isi($excel, 'Anggota');
        $this->assertStringContainsString('Anggota Ekspor Uji Otomatis', $anggota);
        $this->assertStringContainsString('990000111', $anggota);
        $this->assertStringContainsString('Sekretariat Jenderal', $anggota);
        $this->assertStringNotContainsString('Tamu Ekspor Uji Otomatis', $anggota);

        $bukanAnggota = self::isi($excel, 'Bukan Anggota');
        $this->assertStringContainsString('Tamu Ekspor Uji Otomatis', $bukanAnggota);
        $this->assertStringContainsString('Universitas Contoh', $bukanAnggota);

        $rombongan = self::isi($excel, 'Rombongan');
        $this->assertStringContainsString('Ketua Ekspor Uji Otomatis', $rombongan);
        $this->assertStringContainsString('SMA Negeri 1 Contoh', $rombongan);
        $this->assertStringContainsString('|9|', $rombongan);
    }

    public function testBarisDiLuarRentangTanggalTidakIkut(): void
    {
        $excel = $this->unduh('dari=2026-09-01&sampai=2026-09-30');

        $this->assertStringNotContainsString('Anggota Lama Uji Otomatis', self::isi($excel, 'Anggota'));
    }

    public function testTanpaTanggalSemuaDataIkut(): void
    {
        $excel = $this->unduh('');

        $this->assertStringContainsString('Anggota Lama Uji Otomatis', self::isi($excel, 'Anggota'));
        $this->assertStringContainsString('Anggota Ekspor Uji Otomatis', self::isi($excel, 'Anggota'));
    }
}
