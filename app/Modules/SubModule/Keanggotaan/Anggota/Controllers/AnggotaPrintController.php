<?php

namespace Anggota\Controllers;

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

    /** Kartu depan dan belakang sudah satu tampilan; rute lama diteruskan ke sana. */
    public function printkartubelakang($id = null)
    {
        return $this->printanggota($id);
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

        helper('anggota');

        $cards   = [];
        $skipped = [];

        foreach ($members as $anggota) {
            if (strpos((string) $anggota->MemberNo, 'PENDING-') === 0 || !$aktif || (int) $anggota->StatusAnggota_id !== (int) $aktif->id) {
                $skipped[] = $anggota->Fullname;
                continue;
            }

            $cards[] = kartuAnggotaData($anggota);
        }

        return view('Anggota\Views\pdf\kartu-anggota', [
            'cards'   => $cards,
            'skipped' => $skipped,
            'logo'    => base_url('assets/img/kartu/logo-kemnaker.png'),
        ]);
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