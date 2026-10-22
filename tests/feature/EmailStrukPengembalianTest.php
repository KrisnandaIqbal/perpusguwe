<?php

use App\Libraries\EmailNotificationLibrary;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * Isi email struk pengembalian mengikuti keterlambatan buku yang dikembalikan.
 * Nama perpustakaan dibaca dari database lokal; email diganti tiruan.
 *
 * @internal
 */
final class EmailStrukPengembalianTest extends CIUnitTestCase
{
    private MockEmail $email;

    protected function setUp(): void
    {
        parent::setUp();

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $this->email = new MockEmail(config('Email'));
        Services::injectMock('email', $this->email);
    }

    private function kirim(array $hariTerlambat): string
    {
        $anggota = (object) ['Email' => 'anggota.uji@example.com', 'Fullname' => 'Anggota Uji', 'MemberNo' => '990001'];
        $buku    = [];
        foreach ($hariTerlambat as $i => $hari) {
            $buku[] = (object) ['Title' => 'Buku ' . ($i + 1), 'NomorBarcode' => 'B' . $i, 'DueDate' => '2026-10-01', 'LateDays' => $hari];
        }

        $hasil = (new EmailNotificationLibrary())->sendStrukPengembalianEmail($anggota, $buku);
        $this->assertTrue($hasil['success'], $hasil['message']);

        return $this->email->archive['body'];
    }

    public function testBukuTerlambatTidakDisebutTepatWaktu(): void
    {
        $isi = $this->kirim([4]);

        $this->assertStringNotContainsString('tepat waktu', $isi);
        $this->assertStringContainsString('Terima kasih telah mengembalikan buku. Buku dikembalikan melewati jatuh tempo.', $isi);
    }

    public function testSebagianBukuTerlambat(): void
    {
        $isi = $this->kirim([0, 4]);

        $this->assertStringContainsString('Terima kasih telah mengembalikan buku. Sebagian buku dikembalikan melewati jatuh tempo.', $isi);
    }

    public function testSemuaTepatWaktu(): void
    {
        $isi = $this->kirim([0, 0]);

        $this->assertStringContainsString('Terima kasih telah mengembalikan buku tepat waktu.', $isi);
    }

    public function testTidakLagiMenyebutDenda(): void
    {
        $isi = $this->kirim([4]);

        $this->assertStringNotContainsStringIgnoringCase('denda', $isi);
    }
}
