<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestEmailNotif extends BaseCommand
{
    protected $group       = 'Test';
    protected $name        = 'test:emailnotif';
    protected $description = 'Uji kirim 3 notifikasi keanggotaan (sementara, hapus setelah uji).';
    protected $usage       = 'test:emailnotif alamat@email.com';

    public function run(array $params)
    {
        $to = $params[0] ?? null;
        if (!$to) {
            CLI::error('Isi alamat email tujuan.');
            return;
        }

        $lib = new \App\Libraries\EmailNotificationLibrary();
        $member = (object) [
            'Email'    => $to,
            'Fullname' => 'Anggota Uji Coba',
            'MemberNo' => '2026100800001',
            'EndDate'  => '2027-10-08',
        ];
        $px = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

        $r1 = $lib->sendRegistrationReceived($member);
        CLI::write('1. Diterima : ' . ($r1['success'] ? 'OK' : 'GAGAL') . ' - ' . $r1['message']);

        $r2 = $lib->sendMembershipApproved($member, $px, '', 'Perpustakaan Kemnaker', $px, 'Pegawai');
        CLI::write('2. Disetujui: ' . ($r2['success'] ? 'OK' : 'GAGAL') . ' - ' . $r2['message']);

        $r3 = $lib->sendRegistrationRejected($member, 'Data identitas tidak sesuai dengan ID card.');
        CLI::write('3. Ditolak  : ' . ($r3['success'] ? 'OK' : 'GAGAL') . ' - ' . $r3['message']);
    }
}