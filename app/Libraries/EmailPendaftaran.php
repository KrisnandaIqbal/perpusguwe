<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Pemberitahuan email ke pendaftar anggota: pendaftaran diterima, disetujui, dan perlu perbaikan.
 * Hasil pengiriman terakhir dicatat di members (NotifJenis, NotifTerkirim, NotifDate) supaya bisa dikirim ulang.
 */
class EmailPendaftaran
{
    public const DITERIMA  = 'diterima';
    public const DISETUJUI = 'disetujui';
    public const PERBAIKAN = 'perbaikan';

    public const NAMA_PERPUSTAKAAN = 'Perpustakaan Kemnaker RI';

    /**
     * @return array{terkirim: bool, pesan: string}
     */
    public function kirim(string $jenis, $memberId): array
    {
        $db     = db_connect();
        $member = $db->table('members as a')
            ->select('a.*, jenis_anggota.jenisanggota as JenisAnggota, master_unit.nama_unit as UnitKerja')
            ->join('jenis_anggota', 'jenis_anggota.id = a.JenisAnggota_id', 'left')
            ->join('master_unit', 'master_unit.id = a.UnitKerja_id', 'left')
            ->where('a.ID', $memberId)
            ->get()->getRow();

        if (!$member) {
            return ['terkirim' => false, 'pesan' => 'Data anggota tidak ditemukan.'];
        }

        try {
            $hasil = $this->kirimKe($jenis, $member);
        } catch (\Throwable $e) {
            log_message('error', '[EmailPendaftaran] ' . $jenis . ' ke anggota ' . $member->ID . ': ' . $e->getMessage());
            $hasil = ['terkirim' => false, 'pesan' => 'Email gagal dibuat atau dikirim.'];
        }

        $db->table('members')->where('ID', $member->ID)->update([
            'NotifJenis'    => $jenis,
            'NotifTerkirim' => $hasil['terkirim'] ? 1 : 0,
            'NotifDate'     => date('Y-m-d H:i:s'),
        ]);

        return $hasil;
    }

    /** Mengirim lagi pemberitahuan yang sesuai dengan status anggota saat ini. */
    public function kirimUlang($memberId): array
    {
        $row   = db_connect()->table('members')->select('MemberNo, StatusAnggota_id')->where('ID', $memberId)->get()->getRow();
        $jenis = $row ? self::jenisUntuk((string) $row->MemberNo, $row->StatusAnggota_id) : null;
        if (!$jenis) {
            return ['terkirim' => false, 'pesan' => 'Tidak ada pemberitahuan pendaftaran untuk status anggota ini.'];
        }

        return $this->kirim($jenis, $memberId);
    }

    /** Pemberitahuan yang berlaku untuk seorang anggota, atau null kalau statusnya di luar alur pendaftaran. */
    public static function jenisUntuk(string $memberNo, $statusId): ?string
    {
        helper('anggota');
        static $status = null;
        $status ??= [
            'menunggu'  => statusAnggotaId('Menunggu'),
            'perbaikan' => statusAnggotaId('Perlu Perbaikan'),
            'aktif'     => statusAnggotaId('Aktif'),
        ];

        $pending  = strpos($memberNo, 'PENDING-') === 0;
        $statusId = (int) $statusId;

        if ($pending && $statusId === $status['perbaikan']) {
            return self::PERBAIKAN;
        }
        if ($pending && $statusId === $status['menunggu']) {
            return self::DITERIMA;
        }
        if (!$pending && $statusId === $status['aktif']) {
            return self::DISETUJUI;
        }

        return null;
    }

    private function kirimKe(string $jenis, object $member): array
    {
        if (empty($member->Email)) {
            return ['terkirim' => false, 'pesan' => 'Pendaftar tidak punya alamat email.'];
        }

        $smtpUser = getenv('email.SMTPUser') ?: '';
        $smtpPass = getenv('email.SMTPPass') ?: '';
        if ($smtpUser === '' || $smtpPass === '') {
            return ['terkirim' => false, 'pesan' => 'SMTP belum diatur: isi email.SMTPUser dan email.SMTPPass di file .env.'];
        }

        helper('anggota');

        $data = [
            'jenis'       => $jenis,
            'member'      => $member,
            'nama_perpus' => self::NAMA_PERPUSTAKAAN,
            'url_daftar'  => base_url('registrasi'),
        ];

        switch ($jenis) {
            case self::DITERIMA:
                $subject = 'Pendaftaran anggota Anda sudah kami terima';
                break;

            case self::DISETUJUI:
                $subject = 'Pendaftaran disetujui, No. Anggota ' . $member->MemberNo;
                $data['berlaku'] = !empty($member->EndDate) ? tgl_indonesia(substr($member->EndDate, 0, 10)) : '-';
                break;

            case self::PERBAIKAN:
                $subject = 'Pendaftaran Anda belum bisa diterima';
                $daftar  = alasanPerbaikan();
                $data['alasan']  = $daftar[$member->AlasanPerbaikan] ?? $daftar['lainnya'];
                $data['lainnya'] = !isset($daftar[$member->AlasanPerbaikan]) || $member->AlasanPerbaikan === 'lainnya';
                $data['catatan'] = (string) $member->CatatanPerbaikan;
                break;

            default:
                return ['terkirim' => false, 'pesan' => 'Jenis pemberitahuan tidak dikenal.'];
        }

        $email = \Config\Services::email();
        $email->initialize([
            'protocol'   => 'smtp',
            'SMTPHost'   => getenv('email.SMTPHost') ?: 'smtp.gmail.com',
            'SMTPUser'   => $smtpUser,
            'SMTPPass'   => $smtpPass,
            'SMTPPort'   => (int) (getenv('email.SMTPPort') ?: 587),
            'SMTPCrypto' => env('email.SMTPCrypto', 'tls'),
            'mailType'   => 'html',
            'charset'    => 'utf-8',
            'newline'    => "\r\n",
        ]);
        $email->clear(true);
        $email->setFrom(getenv('email.fromEmail') ?: $smtpUser, getenv('email.fromName') ?: self::NAMA_PERPUSTAKAAN);
        $email->setTo($member->Email);
        $email->setSubject($subject);
        $email->setMessage(view('Anggota\Views\email\pendaftaran', $data, ['debug' => false]));

        if ($jenis === self::DISETUJUI) {
            $email->attach($this->kartuPdf($member), 'attachment', 'kartu-anggota-' . $member->MemberNo . '.pdf', 'application/pdf');
        }

        if ($email->send()) {
            log_message('info', '[EmailPendaftaran] ' . $jenis . ' terkirim ke ' . $member->Email);
            return ['terkirim' => true, 'pesan' => 'Email terkirim ke ' . $member->Email . '.'];
        }

        log_message('error', '[EmailPendaftaran] ' . $jenis . ' gagal ke ' . $member->Email . ': ' . $email->printDebugger(['headers']));
        return ['terkirim' => false, 'pesan' => 'Email ke ' . $member->Email . ' gagal terkirim.'];
    }

    /** Kartu anggota dua halaman (depan, belakang) sebagai isi file PDF. */
    public function kartuPdf(object $member): string
    {
        helper('anggota');

        // dompdf menyimpan metrik font hasil olahannya; folder vendor belum tentu bisa ditulisi di server
        $fontCache = WRITEPATH . 'cache/dompdf';
        if (!is_dir($fontCache)) {
            mkdir($fontCache, 0775, true);
        }

        $options = new Options();
        $options->setChroot(FCPATH);
        $options->setFontDir($fontCache);
        $options->setFontCache($fontCache);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('Anggota\Views\pdf\kartu-anggota-pdf', [
            'card'      => kartuAnggotaData($member),
            'logo'      => FCPATH . 'assets/img/kartu/logo-kemnaker.png',
            'logo_krem' => FCPATH . 'assets/img/kartu/logo-kemnaker-krem.png',
            // Garis miring terbalik di path Windows dibaca CSS sebagai karakter escape
            'fonts'     => str_replace('\\', '/', FCPATH) . 'assets/fonts/plus-jakarta-sans/',
        ], ['debug' => false]));
        $dompdf->render();

        return $dompdf->output();
    }
}
