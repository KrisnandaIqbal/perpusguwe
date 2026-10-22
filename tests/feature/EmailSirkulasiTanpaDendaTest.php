<?php

use App\Libraries\EmailNotificationLibrary;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * Perpustakaan tidak memakai denda, jadi email sirkulasi ke anggota tidak menyebutnya.
 * Nama perpustakaan dibaca dari database lokal; email diganti tiruan.
 *
 * @internal
 */
final class EmailSirkulasiTanpaDendaTest extends CIUnitTestCase
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

    private static function buku(): object
    {
        return (object) [
            'ID' => 1, 'CollectionLoan_id' => 1, 'CreateDate' => '2026-10-01 09:00:00',
            'Email' => 'anggota.uji@example.com', 'Fullname' => 'Anggota Uji', 'MemberNo' => '990001',
            'Title' => 'Buku Uji', 'Publisher' => 'Penerbit', 'NomorBarcode' => 'B1', 'CallNumber' => '000',
            'DueDate' => '2026-10-08', 'DueDateExtend' => '2026-10-15', 'LateDays' => 4,
        ];
    }

    public static function pengirim(): array
    {
        return [
            'peringatan keterlambatan' => [static fn ($lib) => $lib->sendOverdueNotification(self::buku(), 4)],
            'peringatan massal'        => [static fn ($lib) => ['success' => $lib->sendBulkOverdueNotification(['anggota.uji@example.com' => [self::buku()]])['sent'] === 1, 'message' => 'massal']],
            'struk peminjaman'         => [static fn ($lib) => $lib->sendStrukEmail(self::buku(), [self::buku()])],
            'struk perpanjangan'       => [static fn ($lib) => $lib->sendStrukPerpanjanganEmail(self::buku(), [self::buku()])],
        ];
    }

    /**
     * @dataProvider pengirim
     */
    public function testEmailTidakMenyebutDenda(callable $kirim): void
    {
        $hasil = $kirim(new EmailNotificationLibrary());
        $this->assertTrue($hasil['success'], $hasil['message']);

        $this->assertStringNotContainsStringIgnoringCase('denda', $this->email->archive['body']);
    }
}
