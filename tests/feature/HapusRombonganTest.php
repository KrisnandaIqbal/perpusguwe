<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Tombol hapus di tabel Rombongan admin menghapus baris rombongan itu, bukan kunjungan perorangan yang ID-nya sama.
 * Memakai database lokal; baris uji ditandai "Uji Otomatis" dan dihapus lagi.
 *
 * @internal
 */
final class HapusRombonganTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private int $adminId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $admin = db_connect()->table('users')->select('id')->get()->getRow();
        if (! $admin) {
            $this->markTestSkipped('Butuh minimal satu akun admin di database lokal.');
        }
        $this->adminId = (int) $admin->id;
    }

    protected function tearDown(): void
    {
        db_connect()->table('memberguesses')->like('Nama', 'Uji Otomatis')->delete();
        db_connect()->table('groupguesses')->like('NamaKetua', 'Uji Otomatis')->delete();
        parent::tearDown();
    }

    public function testTombolHapusRombonganMenghapusRombonganItu(): void
    {
        $db = db_connect();
        $db->table('groupguesses')->insert([
            'NamaKetua' => 'Ketua Hapus Uji Otomatis', 'AsalInstansi' => 'SMA Contoh', 'AlamatInstansi' => '',
            'CountPersonel' => 7, 'CountTk' => 0, 'CreateDate' => date('Y-m-d H:i:s'),
        ]);
        $idRombongan = (int) $db->insertID();

        // Kunjungan perorangan dengan ID yang sama, yang tidak boleh ikut terhapus
        if ($db->table('memberguesses')->where('ID', $idRombongan)->countAllResults() === 0) {
            $db->table('memberguesses')->insert(['ID' => $idRombongan, 'Nama' => 'Tamu Sama ID Uji Otomatis', 'CreateDate' => date('Y-m-d H:i:s')]);
        }
        $perorangan = $db->table('memberguesses')->where('ID', $idRombongan)->get()->getRow();

        // Tabel admin memuat data lewat AJAX (DataTables); is_ajax() membaca $_SERVER langsung
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        try {
            $tabel = json_decode($this->withSession(['logged_in' => $this->adminId])->get('api/bukutamu/rombongan_datatable?' . http_build_query([
                'draw'    => 1, 'start' => 0, 'length' => 10,
                'columns' => [['data' => 'id'], ['data' => 'action'], ['data' => 'Group_chief', 'searchable' => 'true']],
                'search'  => ['value' => 'Ketua Hapus Uji Otomatis'],
            ]))->response()->getBody(), true);
        } finally {
            unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        }
        $baris = current(array_filter($tabel['data'], static fn ($b) => (int) $b['id'] === $idRombongan));
        $this->assertNotFalse($baris, 'Rombongan uji harus tampil di tabel admin.');
        $this->assertSame(1, preg_match('#data-href="([^"]+)"#', $baris['action'], $m));

        $this->withSession(['logged_in' => $this->adminId])->get(str_replace(base_url(), '', html_entity_decode($m[1])));

        $this->assertSame(0, $db->table('groupguesses')->where('ID', $idRombongan)->countAllResults(), 'Rombongan harus terhapus.');
        $this->assertSame(1, $db->table('memberguesses')->where('ID', $perorangan->ID)->countAllResults(), 'Kunjungan perorangan dengan ID sama tidak boleh ikut terhapus.');
    }
}
