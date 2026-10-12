<?php

namespace Anggota\Controllers;

use chillerlan\QRCode\{QRCode, QROptions};
use Dompdf\Dompdf;

/**
 * AnggotaPrintController
 *
 * Menangani: cetak kartu anggota, kartu belakang, multiple print,
 * bebas pustaka, dan upload background kartu.
 */
class AnggotaPrintController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    function __construct()
    {
        $this->initAnggotaBase();
    }

    // ----------------------------------------------------------------
    // CETAK KARTU ANGGOTA (SINGLE)
    // ----------------------------------------------------------------

    public function printanggota($id = null)
    {
        $anggota = db_connect()->table('members m')->select('m.*')->where('m.id', $id)->get()->getRow();
        if (!$anggota) {
            throw new \Exception('Data anggota tidak ditemukan');
        }

        return $this->renderKartu([$anggota]);
    }

    // ----------------------------------------------------------------
    // CETAK KARTU BELAKANG
    // ----------------------------------------------------------------

    public function printkartubelakang($id = null)
    {
        $db = db_connect();

        $this->data['perpus_name'] = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? "Perpustakaan Nasional";

        $this->data['lokasi_perpustakaan'] = $db->table('settingparameters')
            ->where('Name', 'NamaLokasiPerpustakaan')->get()->getRow()->Value ?? "Perpustakaan Nasional";

        $logo_setting = $db->table('settingparameters')->where('Name', 'Logo')->get()->getRow();
        $logo_base64  = '';
        if ($logo_setting && $logo_setting->Value) {
            $logo_path = ROOTPATH . 'public/uploads/branch/' . $logo_setting->Value;
            if (file_exists($logo_path)) {
                $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
            }
        }
        $this->data['logo_base64'] = $logo_base64 ?: 'https://placehold.co/80x80/cccccc/666666?text=LOGO';

        echo view('Anggota\Views\pdf\cetak-kartubelakang', $this->data);
    }

    // ----------------------------------------------------------------
    // CETAK KARTU MULTIPLE
    // ----------------------------------------------------------------

    public function multipleprint()
    {
        $member_ids = $this->request->getPost('member_ids');

        if (!$member_ids || !is_array($member_ids)) {
            throw new \Exception('ID anggota tidak valid');
        }

        $members = db_connect()->table('members m')->select('m.*')
            ->whereIn('m.id', $member_ids)->orderBy('m.MemberNo', 'ASC')->get()->getResult();

        return $this->renderKartu($members);
    }

    // ----------------------------------------------------------------
    // KARTU ANGGOTA KEMNAKER (DEPAN + BELAKANG)
    // ----------------------------------------------------------------

    /**
     * Kartu hanya dicetak untuk anggota berstatus Aktif yang sudah punya No. Anggota.
     * Pendaftar yang belum disetujui (PENDING-) dilewati dan disebut di halaman cetak.
     */
    private function renderKartu(array $members)
    {
        $db    = db_connect();
        $aktif = $db->table('status_anggota')->where('Nama', 'Aktif')->get()->getRow();

        $cards   = [];
        $skipped = [];

        foreach ($members as $anggota) {
            $memberNo = (string) $anggota->MemberNo;

            if (strpos($memberNo, 'PENDING-') === 0 || !$aktif || (int) $anggota->StatusAnggota_id !== (int) $aktif->id) {
                $skipped[] = $anggota->Fullname;
                continue;
            }

            // No. Anggota YYMMDD99999: sisi depan menampilkan nomor urutnya saja (001, 002, ...)
            $isUniversal = (bool) preg_match('/^[0-9]{11}$/', $memberNo);

            $cards[] = [
                'nama'        => $anggota->Fullname,
                'member_no'   => $memberNo,
                'nomor_depan' => $isUniversal ? str_pad((string) (int) substr($memberNo, -5), 3, '0', STR_PAD_LEFT) : $memberNo,
                'nomor_urut'  => $isUniversal,
                'berlaku'     => $this->tanggalKartu($anggota->EndDate),
                'qr'          => (new QRCode(new QROptions(['addQuietzone' => false])))->getMatrix($memberNo)->matrix(true),
            ];
        }

        return view('Anggota\Views\pdf\kartu-anggota', [
            'cards'   => $cards,
            'skipped' => $skipped,
            'logo'    => base_url('assets/img/kartu/logo-kemnaker.png'),
        ]);
    }

    private function tanggalKartu($date): string
    {
        $time = $date ? strtotime($date) : false;
        if (!$time || $time <= 0) {
            return '-';
        }

        $bulan = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

        return date('j', $time) . ' ' . $bulan[(int) date('n', $time) - 1] . ' ' . date('Y', $time);
    }

    // ----------------------------------------------------------------
    // BEBAS PUSTAKA
    // ----------------------------------------------------------------

    public function bebaspustaka(int $id = null)
    {
        $db      = db_connect();
        $anggota = $this->anggotaModel->find($id);

        $jenis_perpustakaan = $this->settingModel->where('Name', 'JenisPerpustakaan')->first()->Value ?? 'UMUM';
        $nama_perpustakaan  = $this->settingModel->where('Name', 'NamaPerpustakaan')->first()->Value ?? 'Perpustakaan';

        $kelas_nama    = null;
        $fakultas_nama = null;
        $jurusan_nama  = null;

        if (strtoupper($jenis_perpustakaan) === 'SEKOLAH') {
            if (!empty($anggota->Kelas_id)) {
                $kelas = $db->table('kelas_siswa')->where('id', $anggota->Kelas_id)->get()->getRow();
                $kelas_nama = $kelas->namakelassiswa ?? null;
            }
        } elseif (strtoupper($jenis_perpustakaan) === 'PERGURUAN TINGGI') {
            if (!empty($anggota->Fakultas_id)) {
                $fakultas = $db->table('master_fakultas')->where('id', $anggota->Fakultas_id)->get()->getRow();
                $fakultas_nama = $fakultas->Nama ?? null;
            }
            if (!empty($anggota->Jurusan_id)) {
                $jurusan = $db->table('master_jurusan')->where('id', $anggota->Jurusan_id)->get()->getRow();
                $jurusan_nama = $jurusan->Nama ?? null;
            }
        }

        $this->data['title']              = 'Bebas Pustaka';
        $this->data['anggota']            = $anggota;
        $this->data['jenis_perpustakaan'] = strtoupper($jenis_perpustakaan);
        $this->data['nama_perpustakaan']  = $nama_perpustakaan;
        $this->data['kelas_nama']         = $kelas_nama;
        $this->data['fakultas_nama']      = $fakultas_nama;
        $this->data['jurusan_nama']       = $jurusan_nama;

        $dompdf = new Dompdf();
        $html   = view('Anggota\Views\bebas-pustaka', $this->data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream();
    }

    // ----------------------------------------------------------------
    // UPLOAD BACKGROUND KARTU
    // ----------------------------------------------------------------

    public function uploadBackground()
    {
        if (empty(session()->get('logged_in'))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi login tidak valid.'])->setStatusCode(401);
        }

        $validationRule = [
            'bgImage' => [
                'label' => 'Background Image',
                'rules' => [
                    'uploaded[bgImage]',
                    'is_image[bgImage]',
                    'mime_in[bgImage,image/jpg,image/jpeg,image/png,image/gif]',
                    'max_size[bgImage,10240]',
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $img = $this->request->getFile('bgImage');
        if (!$img || !$img->isValid() || $img->hasMoved()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memproses file yang diunggah.']);
        }

        $newName    = $img->getRandomName();
        $uploadPath = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'master-template' . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        try {
            $img->move($uploadPath, $newName);

            $db      = db_connect();
            $setting = $db->table('settingparameters')->where('Name', 'FileKartuAnggota')->get()->getRow();

            if ($setting) {
                if ($setting->Value && is_file($uploadPath . $setting->Value)) {
                    unlink($uploadPath . $setting->Value);
                }
                $db->table('settingparameters')->where('ID', $setting->ID)->update(['Value' => $newName]);
            } else {
                $db->table('settingparameters')->insert(['Name' => 'FileKartuAnggota', 'Value' => $newName]);
            }

            return $this->response->setJSON([
                'success'  => true,
                'message'  => 'Background kartu berhasil diupdate.',
                'file_url' => base_url('uploads/master-template/' . $newName),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}