<?php

use App\Libraries\EmailLogger;
use App\Libraries\EmailNotificationLibrary;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockEmail;
use Config\Services;

/**
 * @internal
 */
final class LaporanEmailTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private MockEmail $email;

    protected function setUp(): void
    {
        parent::setUp();

        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        $this->email = new MockEmail(config('Email'));
        Services::injectMock('email', $this->email);
    }

    protected function tearDown(): void
    {
        db_connect()->table('email_logs')->where('recipient_email', 'uji.laporan@example.com')->delete();
        parent::tearDown();
    }

    public function testPengirimanEmailTercatatDiTabelEmailLogs(): void
    {
        $member = (object) [
            'ID'       => 999999,
            'Email'    => 'uji.laporan@example.com',
            'Fullname' => 'Penguji Laporan Email',
            'MemberNo' => 'UJI-001',
        ];

        $lib   = new EmailNotificationLibrary();
        $hasil = $lib->sendRegistrationReceived($member);

        $this->assertTrue($hasil['success']);

        $row = db_connect()->table('email_logs')
            ->where('recipient_email', 'uji.laporan@example.com')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        $this->assertNotNull($row);
        $this->assertSame(EmailLogger::TYPE_REGISTRASI_DITERIMA, $row['email_type']);
        $this->assertSame('success', $row['status']);
        $this->assertSame('Penguji Laporan Email', $row['recipient_name']);
        $this->assertSame('UJI-001', $row['member_no']);
    }

    public function testMenuLaporanPengirimanEmailTerdaftarDiSidebar(): void
    {
        helper(['menu', 'auth']);

        $menuRow = db_connect()->table('c_menus')
            ->where('controller', 'laporan-email')
            ->where('parent', 3)
            ->where('active', 1)
            ->get()
            ->getRowArray();

        $this->assertNotNull($menuRow);
        $this->assertSame('Laporan Pengiriman Email', $menuRow['name']);

        $rendered = display_menu_backend(0, 1, 'admin');
        $this->assertStringContainsString('Laporan Pengiriman Email', $rendered);
        $this->assertStringContainsString('laporan-email', $rendered);
    }

    public function testPreviewEndpointMengembalikanDataDanRingkasan(): void
    {
        $admin = db_connect()->table('users')->select('id')->where('username', 'admin')->get()->getRow();
        $this->assertNotNull($admin);

        EmailLogger::log([
            'recipient_email' => 'uji.laporan@example.com',
            'recipient_name'  => 'Penguji Preview',
            'member_no'       => 'UJI-002',
            'email_type'      => EmailLogger::TYPE_STRUK_PINJAM,
            'subject'         => 'Struk Peminjaman Uji',
            'status'          => 'success',
        ]);

        $adminId = (int) $admin->id;
        $session = [
            'logged_in' => $adminId,
            'user'      => (object) [
                'id'         => $adminId,
                'username'   => 'admin',
                'first_name' => 'Admin',
                'category'   => 'admin',
            ],
        ];

        $pageResponse = $this->withSession($session)->get('laporan-email');
        $pageResponse->assertOK();
        $pageResponse->assertSee('Laporan Pengiriman Email');

        $response = $this->withSession($session)
            ->post('laporan-email/preview', [
                'columns'    => ['sent_at', 'recipient_email', 'recipient_name', 'email_type', 'status'],
                'email_type' => EmailLogger::TYPE_STRUK_PINJAM,
                'keyword'    => 'uji.laporan@example.com',
            ]);

        $response->assertOK();
        $json = json_decode($response->response()->getBody(), true);

        $this->assertIsArray($json);
        $this->assertSame('success', $json['status']);
        $this->assertGreaterThanOrEqual(1, $json['total']);
        $this->assertSame('Struk Peminjaman', $json['data'][0]['email_type']);
        $this->assertSame('Berhasil', $json['data'][0]['status']);
    }
}
