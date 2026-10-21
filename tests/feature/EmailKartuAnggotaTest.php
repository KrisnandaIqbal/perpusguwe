<?php

use App\Libraries\EmailPendaftaran;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * Email persetujuan membawa kartu anggota sebagai satu file PDF (sisi depan dan belakang).
 * Memakai database lokal; anggota uji disalin dari anggota aktif dan dihapus lagi. Email diganti tiruan.
 *
 * @internal
 */
final class EmailKartuAnggotaTest extends CIUnitTestCase
{
    private int $memberId = 0;
    private MockEmail $email;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $db     = db_connect();
        $contoh = $db->table('members')->notLike('MemberNo', 'PENDING-', 'after')->where('StatusAnggota_id', 3)->get()->getRowArray();
        if (! $contoh) {
            $this->markTestSkipped('Butuh minimal satu anggota aktif di database lokal sebagai contoh baris.');
        }

        unset($contoh['ID']);
        $contoh['MemberNo'] = '99' . substr((string) hrtime(true), -9);
        $contoh['Email']    = 'anggota.uji@example.com';
        $db->table('members')->insert($contoh);
        $this->memberId = (int) $db->insertID();

        putenv('email.SMTPUser=uji');
        putenv('email.SMTPPass=uji');
        $this->email = new MockEmail(config('Email'));
        Services::injectMock('email', $this->email);
    }

    protected function tearDown(): void
    {
        if ($this->memberId) {
            db_connect()->table('members')->where('ID', $this->memberId)->delete();
        }
        parent::tearDown();
    }

    public function testEmailPersetujuanMelampirkanSatuPdfDuaHalaman(): void
    {
        $hasil = (new EmailPendaftaran())->kirim(EmailPendaftaran::DISETUJUI, $this->memberId);

        $this->assertTrue($hasil['terkirim'], $hasil['pesan']);
        $lampiran = $this->email->archive['attachments'];
        $this->assertCount(1, $lampiran);
        $this->assertSame('application/pdf', $lampiran[0]['type']);

        $pdf = base64_decode($lampiran[0]['content'], true);
        $this->assertSame(2, preg_match_all('#/Type\s*/Page[^s]#', $pdf), 'PDF harus berisi sisi depan dan sisi belakang.');
    }
}
