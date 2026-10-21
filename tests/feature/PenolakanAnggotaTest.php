<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * Admin menolak pendaftar online lewat tombol "Belum bisa diterima".
 * Memakai database lokal; pendaftar uji dibuat dan dihapus sendiri. Email diganti tiruan.
 *
 * @internal
 */
final class PenolakanAnggotaTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private const TGL_DAFTAR = '2026-10-01 09:00:00';

    private int $memberId = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $db     = db_connect();
        $contoh = $db->table('members')->like('MemberNo', 'PENDING-', 'after')->get()->getRowArray();
        if (! $contoh) {
            $this->markTestSkipped('Butuh minimal satu pendaftar PENDING- di database lokal sebagai contoh baris.');
        }

        unset($contoh['ID']);
        $contoh['MemberNo']   = 'PENDING-UJI' . substr(md5((string) microtime(true)), 0, 5);
        $contoh['Email']      = 'pendaftar.uji@example.com';
        $contoh['PhotoUrl']   = null;
        $contoh['CreateDate'] = self::TGL_DAFTAR;
        $db->table('members')->insert($contoh);
        $this->memberId = (int) $db->insertID();

        Services::injectMock('email', new MockEmail(config('Email')));

        // is_allowed() meloloskan request AJAX dengan membaca $_SERVER langsung, seperti $.post dari browser.
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        if ($this->memberId) {
            db_connect()->table('sumbangan')->where('Member_id', $this->memberId)->where('Keterangan', 'UJI')->delete();
            db_connect()->table('members')->where('ID', $this->memberId)->delete();
        }
        parent::tearDown();
    }

    public function testAdminMenolakPendaftarMenghapusDatanya(): void
    {
        $json = $this->tolak();

        $this->assertFalse($json['error'] ?? true, json_encode($json));
        $this->assertFalse($this->pendaftarMasihAda());
    }

    /**
     * Baris transaksi lama milik anggota yang sudah dihapus bisa menunjuk ke ID
     * yang sekarang dipakai pendaftar baru. Itu bukan riwayat pendaftar ini.
     */
    public function testTransaksiSebelumPendaftaranTidakMenghalangiPenolakan(): void
    {
        $this->tambahSumbangan('2026-01-19 10:32:09');

        $json = $this->tolak();

        $this->assertFalse($json['error'] ?? true, json_encode($json));
        $this->assertFalse($this->pendaftarMasihAda());
    }

    public function testTransaksiSesudahPendaftaranTetapMenghalangiPenolakan(): void
    {
        $this->tambahSumbangan('2026-10-05 09:00:00');

        $json = $this->tolak();

        $this->assertTrue($json['error'] ?? false, json_encode($json));
        $this->assertStringContainsString('riwayat transaksi', $json['message']);
        $this->assertTrue($this->pendaftarMasihAda());
    }

    private function tolak(): array
    {
        $result = $this->withSession(['logged_in' => 1])
            ->post('api/anggota/minta_perbaikan/' . $this->memberId, [
                'alasan'  => ['lainnya'],
                'catatan' => 'Uji otomatis',
            ]);

        return json_decode($result->response()->getBody(), true) ?? [];
    }

    private function tambahSumbangan(string $tanggal): void
    {
        db_connect()->table('sumbangan')->insert([
            'Member_id'  => $this->memberId,
            'Jumlah'     => 0,
            'Keterangan' => 'UJI',
            'CreateDate' => $tanggal,
        ]);
    }

    private function pendaftarMasihAda(): bool
    {
        return db_connect()->table('members')->where('ID', $this->memberId)->countAllResults() > 0;
    }
}
