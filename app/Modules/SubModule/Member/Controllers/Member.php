<?php

namespace Member\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Member extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $uploadPath;
    public $modulePath;
    public $db;

    public function __construct()
    {
        $this->language = \Config\Services::language();
        $this->language->setLocale('id');

        $this->memberModel = new \Member\Models\MemberModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/member/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }


        helper('adminigniter');
        helper('reference');
        helper('date_id');
        helper('member');
    }

    public function activate($hash = '')
    {
        $result = member_activate($hash);
        if ($result) {
            // Kirim kartu anggota digital otomatis setelah aktivasi berhasil
            try {
                $db = \Config\Database::connect();
                $perpusName = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? 'Perpustakaan Nasional';
                $logoSetting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
                $logoBase64 = '';
                if ($logoSetting && $logoSetting->Value) {
                    $logoPath = ROOTPATH . 'public/uploads/branch/' . $logoSetting->Value;
                    if (file_exists($logoPath)) {
                        $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                        $mime = $ext === 'webp' ? 'image/webp' : ($ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : 'image/png');
                        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                }

                $photoSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="250" height="280"><rect width="100%" height="100%" fill="#cccccc"/><text x="50%" y="50%" font-family="Arial" font-size="28" fill="#666666" text-anchor="middle" dominant-baseline="middle">FOTO</text></svg>';
                $photoSrc = 'data:image/svg+xml;base64,' . base64_encode($photoSvg);
                if (!empty($result->PhotoUrl)) {
                    $photoPath = ROOTPATH . 'public/uploads/anggota/' . $result->PhotoUrl;
                    if (file_exists($photoPath)) {
                        $photoSrc = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($photoPath));
                    }
                }

                $options = new \chillerlan\QRCode\QROptions(['scale' => 7, 'imageBase64' => true]);
                $qrImage = (new \chillerlan\QRCode\QRCode($options))->render($result->MemberNo);

                $jenisRow = $db->table('jenis_anggota')->select('jenisanggota')->where('id', $result->JenisAnggota_id)->get()->getRow();
                $jenisAnggota = $jenisRow ? $jenisRow->jenisanggota : 'UMUM';

                $emailLib = new \App\Libraries\EmailNotificationLibrary();
                $emailLib->sendMemberCard($result, $qrImage, $photoSrc, $perpusName, $logoBase64, $jenisAnggota);
            } catch (\Exception $e) {
                log_message('error', '[Activate] Gagal kirim kartu otomatis: ' . $e->getMessage());
            }

            return redirect()->to(base_url('login'))->with('success', 'Akun berhasil diaktifkan, silakan login.');
        }
        return redirect()->to(base_url('login'))->with('error', 'Link aktivasi tidak valid atau sudah kedaluwarsa.');
    }
}
