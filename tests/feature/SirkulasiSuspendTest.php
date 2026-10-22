<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Pengujian aturan baru sanksi keterlambatan pengembalian buku:
 * 1. Terlambat N hari menghasilkan suspend N hari, dihitung dari tanggal pengembalian. Tanpa denda uang.
 * 2. Tepat waktu tidak menghasilkan suspend.
 * 3. Anggota yang sedang disuspend ditolak saat meminjam di Sirkulasi Admin.
 * 4. Anggota yang sedang disuspend ditolak saat meminjam di Pinjam Mandiri (SelfLoan).
 * 5. Anggota yang masa suspend-nya sudah lewat diperbolehkan meminjam kembali.
 *
 * @internal
 */
final class SirkulasiSuspendTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private int $memberId = 0;
    private string $memberNo = '';

    protected function setUp(): void
    {
        parent::setUp();

        // Database tests memakai database yang sama dengan default
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $db = db_connect();

        // Buat data anggota uji aktif (StatusAnggota_id = 3, JenisAnggota_id = 1)
        $this->memberNo = 'UJI-SUSPEND-' . substr(md5((string) microtime(true)), 0, 6);
        $db->table('members')->insert([
            'MemberNo'         => $this->memberNo,
            'Fullname'         => 'Anggota Uji Suspend',
            'Email'            => 'uji.suspend@example.com',
            'Phone'            => '081234567890',
            'StatusAnggota_id' => 3, // Aktif
            'JenisAnggota_id'  => 1,
            'EndDate'          => date('Y-m-d H:i:s', strtotime('+1 year')),
            'active'           => 1,
            'CreateDate'       => date('Y-m-d H:i:s'),
        ]);

        $this->memberId = (int) $db->insertID();

        helper(['sirkulasi', 'selfloan']);
    }

    protected function tearDown(): void
    {
        if ($this->memberId > 0) {
            $db = db_connect();
            $db->table('pelanggaran')->where('Member_id', $this->memberId)->delete();
            $db->table('members')->where('ID', $this->memberId)->delete();
        }

        parent::tearDown();
    }

    /**
     * Uji: terlambat 4 hari menghasilkan suspend 4 hari dan jumlah denda 0 (tanpa denda uang).
     */
    public function testTerlambat4HariMenghasilkanSuspend4HariDanTanpaDendaUang(): void
    {
        $db = db_connect();

        // Simulasikan pencatatan pelanggaran akibat keterlambatan 4 hari
        $lateDays = 4;
        $returnDate = date('Y-m-d H:i:s');

        $db->table('pelanggaran')->insert([
            'CollectionLoan_id'     => 1,
            'CollectionLoanItem_id' => 1,
            'JenisPelanggaran_id'   => 1, // Keterlambatan
            'JenisDenda_id'         => 5, // Suspend (Skorsing)
            'JumlahDenda'           => 0, // Tanpa denda uang
            'JumlahSuspend'         => $lateDays,
            'Paid'                  => 1,
            'Member_id'             => $this->memberId,
            'Collection_id'         => 1,
            'active'                => 1,
            'CreateDate'            => $returnDate,
        ]);

        $pelanggaranId = (int) $db->insertID();
        $row = $db->table('pelanggaran')->where('ID', $pelanggaranId)->get()->getRow();

        $this->assertNotNull($row);
        $this->assertSame(4, (int) $row->JumlahSuspend, 'Jumlah suspend harus 4 hari sesuai hari keterlambatan.');
        $this->assertSame(0.0, (float) $row->JumlahDenda, 'Jumlah denda harus 0 (tanpa denda uang).');
        $this->assertSame(1, (int) $row->Paid, 'Status Paid harus 1 karena denda uang ditiadakan.');

        // Verifikasi melalui helper sirkulasi cek_suspend_anggota
        $suspendStatus = cek_suspend_anggota($this->memberId);
        $this->assertTrue($suspendStatus['is_suspended']);
        $this->assertSame(4, $suspendStatus['suspend_days']);
        $this->assertStringContainsString('Anggota sedang disuspend', $suspendStatus['message']);
    }

    /**
     * Uji: tepat waktu tidak menghasilkan suspend.
     */
    public function testTepatWaktuTidakMenghasilkanSuspend(): void
    {
        // Anggota tanpa pelanggaran keterlambatan
        $suspendStatus = cek_suspend_anggota($this->memberId);

        $this->assertFalse($suspendStatus['is_suspended'], 'Anggota yang tepat waktu tidak boleh disuspend.');
        $this->assertSame(0, $suspendStatus['suspend_days']);
        $this->assertEmpty($suspendStatus['message']);
    }

    /**
     * Uji: anggota yang sedang disuspend ditolak saat meminjam di Sirkulasi Admin.
     */
    public function testAnggotaSedangDisuspendDitolakDiSirkulasiAdmin(): void
    {
        $db = db_connect();

        // Buat pelanggaran aktif (terlambat 4 hari, dibuat hari ini)
        $db->table('pelanggaran')->insert([
            'CollectionLoan_id'     => 1,
            'CollectionLoanItem_id' => 1,
            'JenisPelanggaran_id'   => 1,
            'JenisDenda_id'         => 5,
            'JumlahDenda'           => 0,
            'JumlahSuspend'         => 4,
            'Paid'                  => 1,
            'Member_id'             => $this->memberId,
            'Collection_id'         => 1,
            'active'                => 1,
            'CreateDate'            => date('Y-m-d H:i:s'),
        ]);

        // Panggil validasi member peminjaman admin
        $controller = new \Peminjaman\Controllers\Peminjaman();
        $controller->initController(
            service('request'),
            service('response'),
            service('logger')
        );

        $reflector = new \ReflectionMethod($controller, 'validateMember');
        $reflector->setAccessible(true);
        $result = $reflector->invoke($controller, $this->memberNo);

        $this->assertFalse($result['success'], 'Anggota yang sedang disuspend harus ditolak saat meminjam di admin.');
        $this->assertStringContainsString('suspend', strtolower($result['message']));
    }

    /**
     * Uji: anggota yang sedang disuspend ditolak saat pinjam mandiri (SelfLoan).
     */
    public function testAnggotaSedangDisuspendDitolakDiPinjamMandiri(): void
    {
        $db = db_connect();

        // Buat pelanggaran aktif (terlambat 4 hari)
        $db->table('pelanggaran')->insert([
            'CollectionLoan_id'     => 1,
            'CollectionLoanItem_id' => 1,
            'JenisPelanggaran_id'   => 1,
            'JenisDenda_id'         => 5,
            'JumlahDenda'           => 0,
            'JumlahSuspend'         => 4,
            'Paid'                  => 1,
            'Member_id'             => $this->memberId,
            'Collection_id'         => 1,
            'active'                => 1,
            'CreateDate'            => date('Y-m-d H:i:s'),
        ]);

        // Cek via helper check_member_loan_eligibility (selfloan_helper.php)
        $eligibility = check_member_loan_eligibility($this->memberId);

        $this->assertFalse($eligibility['eligible'], 'Anggota yang sedang disuspend harus ditolak saat pinjam mandiri.');
        $this->assertStringContainsString('suspend', strtolower($eligibility['message']));
    }

    /**
     * Uji: anggota yang masa suspend-nya sudah selesai diperbolehkan meminjam kembali.
     */
    public function testAnggotaSuspendSudahKadaluarsaBolehMeminjam(): void
    {
        $db = db_connect();

        // Buat pelanggaran yang dibuat 10 hari lalu dengan masa suspend 4 hari (sudah berakhir 6 hari lalu)
        $pastDate = date('Y-m-d H:i:s', strtotime('-10 days'));

        $db->table('pelanggaran')->insert([
            'CollectionLoan_id'     => 1,
            'CollectionLoanItem_id' => 1,
            'JenisPelanggaran_id'   => 1,
            'JenisDenda_id'         => 5,
            'JumlahDenda'           => 0,
            'JumlahSuspend'         => 4,
            'Paid'                  => 1,
            'Member_id'             => $this->memberId,
            'Collection_id'         => 1,
            'active'                => 1,
            'CreateDate'            => $pastDate,
        ]);

        // Suspend check harus menghasilkan is_suspended = false
        $suspendStatus = cek_suspend_anggota($this->memberId);
        $this->assertFalse($suspendStatus['is_suspended'], 'Suspend yang telah lewat durasinya tidak boleh lagi memblokir peminjaman.');

        // Self loan eligibility harus lolos (eligible = true)
        $eligibility = check_member_loan_eligibility($this->memberId);
        $this->assertTrue($eligibility['eligible'], 'Anggota dengan suspend kadaluarsa harus memenuhi syarat pinjam mandiri.');
    }
}
