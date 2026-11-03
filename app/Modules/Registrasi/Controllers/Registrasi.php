<?php

namespace Registrasi\Controllers;

use \Base\Controllers\BaseController;
use Registrasi\Libraries\IdentityValidator;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Registrasi extends BaseController
{
    use ResponseTrait;
    protected $db;
    protected $modulePath;

    public function __construct()
    {
        $this->db = \Config\Database::connect('data');
        $this->modulePath = ROOTPATH . 'public/uploads/anggota/';

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath, 0777, true);
        }
    }

    public function index()
    {
        $data['title'] = 'Pendaftaran Anggota';
        
        // Master ASN & NIP default
        $asnRow = $this->db->table('jenis_anggota')
                           ->where('active', 1)
                           ->like('jenisanggota', 'ASN')
                           ->notLike('jenisanggota', 'Non')
                           ->get()->getRow();
        $nipRow = $this->db->table('master_jenis_identitas')
                           ->where('active', 1)
                           ->like('Nama', 'NIP')
                           ->get()->getRow();

        $data['asn_id'] = $asnRow ? (int) $asnRow->id : 1;
        $data['nip_id'] = $nipRow ? (int) $nipRow->id : 1;

        // R2: Saring unit induk Kementerian Ketenagakerjaan (ID: 1) khusus form registrasi online
        $data['unit_kerja'] = $this->db->table('master_unit')
                                       ->where('active', 1)
                                       ->where('id !=', 1)
                                       ->get()->getResult();

        $subUnitModel = new \App\Models\SubUnitModel();
        $data['sub_units_grouped'] = $subUnitModel->getAllActiveGrouped();

        $data['jenis_kelamin'] = $this->db->table('jenis_kelamin')
                                           ->where('active', 1)
                                           ->orderBy('ID', 'ASC')
                                           ->get()->getResult();

        return view('Registrasi\Views\index', $data);
    }

    public function sub_units($unitId = null)
    {
        $subUnitModel = new \App\Models\SubUnitModel();
        $list = $subUnitModel->getActiveByUnitId((int) $unitId);
        return $this->response->setJSON($list);
    }

    public function check_duplicate()
    {
        $field = (string) $this->request->getPost('field');
        $value = trim((string) $this->request->getPost('value'));

        if (!in_array($field, ['Phone', 'Email', 'IdentityNo'], true) || empty($value)) {
            return $this->respond(['registered' => false]);
        }

        helper('anggota');
        $perbaikanId = statusAnggotaId('Perlu Perbaikan');

        $builder = $this->db->table('members')
            ->select('ID, IdentityNo, Email, Phone, NoHp, MemberNo, StatusAnggota_id');

        if ($field === 'Phone') {
            $normalized = IdentityValidator::normalizePhone($value);
            $builder->groupStart()
                ->where('Phone', $normalized)
                ->orWhere('NoHp', $normalized)
            ->groupEnd();
        } elseif ($field === 'Email') {
            $builder->where('Email', $value);
        } elseif ($field === 'IdentityNo') {
            $builder->where('IdentityNo', $value);
        }

        $rows = $builder->get()->getResult();

        foreach ($rows as $row) {
            $bolehUlang = $perbaikanId
                && (int) $row->StatusAnggota_id === $perbaikanId
                && strpos((string) $row->MemberNo, 'PENDING-') === 0;

            if (!$bolehUlang) {
                $messages = [
                    'Phone'      => 'Nomor handphone ini sudah terdaftar.',
                    'Email'      => 'Email sudah terdaftar sebelumnya.',
                    'IdentityNo' => 'NIP sudah terdaftar sebelumnya.',
                ];
                return $this->respond([
                    'registered' => true,
                    'message'    => $messages[$field],
                ]);
            }
        }

        return $this->respond(['registered' => false]);
    }

    public function submit()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(base_url('home/pendaftaran-online'));
        }

        $asnRow = $this->db->table('jenis_anggota')
                           ->where('active', 1)
                           ->like('jenisanggota', 'ASN')
                           ->notLike('jenisanggota', 'Non')
                           ->get()->getRow();
        $nipRow = $this->db->table('master_jenis_identitas')
                           ->where('active', 1)
                           ->like('Nama', 'NIP')
                           ->get()->getRow();

        $jenisAnggotaId = $asnRow ? (int) $asnRow->id : ((int) $this->request->getPost('JenisAnggota_id') ?: 1);
        $identityTypeId = $nipRow ? (int) $nipRow->id : ((int) $this->request->getPost('IdentityType_id') ?: 1);

        $fullname = $this->request->getPost('Fullname');
        $sexId = $this->request->getPost('Sex_id');
        $identityNo = trim((string) $this->request->getPost('IdentityNo'));
        $unitKerjaId = $this->request->getPost('UnitKerja_id');
        $subUnitKerjaId = $this->request->getPost('SubUnitKerja_id');
        $statusNonAsn = null; // Magang & Non-ASN dihapus dari pendaftaran baru
        $address = $this->request->getPost('AddressNow');
        $email = $this->request->getPost('Email');
        $phone = $this->request->getPost('Phone');
        $persetujuan = $this->request->getPost('Persetujuan');

        // Validation Rules
        $rules = [
            'IdentityNo' => 'required',
            'Fullname' => 'required|min_length[3]|nama_orang',
            'Sex_id' => 'required',
            'UnitKerja_id' => 'required',
            'AddressNow' => 'required',
            'Email' => 'required|valid_email',
            'Phone' => 'required',
            'Persetujuan' => 'required',
        ];

        $messages = [
            'Fullname' => [
                'nama_orang' => 'Nama hanya boleh berisi huruf A-Z, spasi, titik, koma, apostrof, dan tanda hubung.'
            ],
            'Sex_id' => [
                'required' => 'Jenis kelamin wajib dipilih.'
            ]
        ];

        // Validasi di SERVER: sub-unit harus benar-benar milik unit yang dipilih
        helper('anggota');
        $unitCheck = is_valid_unit_subunit($unitKerjaId, $subUnitKerjaId);
        if (!$unitCheck['valid']) {
            return $this->respond([
                'success' => false,
                'message' => $unitCheck['message']
            ]);
        }

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            return $this->respond([
                'success' => false,
                // Nama berisi simbol bukan soal field kosong, jadi pesannya disebut langsung
                'message' => $errors['Fullname'] ?? 'Validasi gagal, mohon lengkapi semua field yang wajib.',
                'errors' => $errors
            ]);
        }

        // Validasi Jenis Kelamin ke tabel master (active = 1)
        $validSex = $this->db->table('jenis_kelamin')
            ->where('ID', (int) $sexId)
            ->where('active', 1)
            ->get()->getRow();
        if (!$validSex) {
            return $this->respond([
                'success' => false,
                'message' => 'Pilihan jenis kelamin tidak valid.',
                'errors' => ['Sex_id' => 'Pilihan jenis kelamin tidak valid.']
            ]);
        }

        // Custom Validation via Library
        $phone = IdentityValidator::normalizePhone($phone);
        if (!IdentityValidator::isValidPhone($phone)) {
            return $this->respond([
                'success' => false,
                'message' => 'Format nomor telepon tidak valid. Gunakan awalan 08 (10-15 digit).'
            ]);
        }

        // Khusus ASN: Wajib NIP 18 digit resmi BKN
        if (!IdentityValidator::isValidNIP($identityNo)) {
            return $this->respond([
                'success' => false,
                'message' => 'Format NIP tidak valid. Harus 18 digit angka resmi BKN.'
            ]);
        }

        // Duplicate Check
        // Pendaftaran berstatus Perlu Perbaikan boleh diulang dengan nomor identitas dan email yang sama
        helper('anggota');
        $perbaikanId = statusAnggotaId('Perlu Perbaikan');
        $ulang = null;

        $sama = $this->db->table('members')
            ->select('ID, IdentityNo, Email, Phone, NoHp, MemberNo, StatusAnggota_id, PhotoUrl')
            ->groupStart()
                ->where('IdentityNo', $identityNo)
                ->orWhere('Email', $email)
                ->orWhere('Phone', $phone)
                ->orWhere('NoHp', $phone)
            ->groupEnd()
            ->get()->getResult();

        foreach ($sama as $row) {
            $bolehUlang = $perbaikanId
                && (int) $row->StatusAnggota_id === $perbaikanId
                && strpos((string) $row->MemberNo, 'PENDING-') === 0;

            if (!$bolehUlang) {
                if ($row->IdentityNo === $identityNo) {
                    $field = 'IdentityNo';
                    $msg   = 'NIP sudah terdaftar sebelumnya.';
                } elseif ($row->Email === $email) {
                    $field = 'Email';
                    $msg   = 'Email sudah terdaftar sebelumnya.';
                } else {
                    $field = 'Phone';
                    $msg   = 'Nomor handphone ini sudah terdaftar.';
                }

                return $this->respond([
                    'success' => false,
                    'message' => $msg,
                    'errors'  => [$field => $msg]
                ]);
            }

            if ($ulang === null || $row->IdentityNo === $identityNo) {
                $ulang = $row;
            }
        }

        // Handle photo upload (optional)
        $photoFileName = null;
        $photoFile = $this->request->getFile('PhotoFile');
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png'];
            $maxSize = 2 * 1024 * 1024; // 2 MB

            if (!in_array($photoFile->getMimeType(), $allowedTypes)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Format foto harus JPG atau PNG.'
                ]);
            }

            if ($photoFile->getSize() > $maxSize) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Ukuran foto maksimal 2 MB.'
                ]);
            }

            $photoFileName = $photoFile->getRandomName();
            $photoFile->move($this->modulePath, $photoFileName);
        }

        // Insert Data
        $dataInsert = [
            'JenisAnggota_id' => $jenisAnggotaId,
            'Fullname' => $fullname,
            'Sex_id' => (int) $sexId,
            'IdentityType_id' => $identityTypeId,
            'IdentityNo' => $identityNo,
            'UnitKerja_id' => $unitKerjaId,
            'SubUnitKerja_id' => !empty($subUnitKerjaId) ? (int) $subUnitKerjaId : null,
            'status_non_asn' => $statusNonAsn,
            'AddressNow' => $address,
            'Email' => $email,
            'NoHp' => $phone,
            'Phone' => $phone,
            'StatusAnggota_id' => 2, // Menunggu verifikasi
            'RegisterDate' => date('Y-m-d H:i:s'),
            'TglPersetujuan' => null,
            'MemberNo' => 'PENDING-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'EndDate' => null,
            'CreateDate' => date('Y-m-d H:i:s'),
            'active' => 1
        ];

        if ($photoFileName) {
            $dataInsert['PhotoUrl'] = $photoFileName;
        }

        try {
            if ($ulang) {
                // Baris lama dipakai lagi; alasan perbaikannya tetap tersimpan untuk dibaca admin
                unset($dataInsert['MemberNo'], $dataInsert['CreateDate']);
                $dataInsert['UpdateDate'] = date('Y-m-d H:i:s');
                $this->db->table('members')->where('ID', $ulang->ID)->update($dataInsert);
                $newId = $ulang->ID;

                if ($photoFileName && $ulang->PhotoUrl && is_file($this->modulePath . $ulang->PhotoUrl)) {
                    unlink($this->modulePath . $ulang->PhotoUrl);
                }
            } else {
                $this->db->table('members')->insert($dataInsert);
                $newId = $this->db->insertID();
            }

            // Pendaftaran tetap diterima walau email gagal; hasilnya tercatat di kolom NotifTerkirim
            (new \App\Libraries\EmailPendaftaran())->kirim(\App\Libraries\EmailPendaftaran::DITERIMA, $newId);

            return $this->respond([
                'success' => true,
                'message' => 'Pendaftaran diterima, menunggu verifikasi petugas.',
                'redirect' => base_url('registrasi/success')
            ]);
        } catch (\Exception $e) {
            // Cleanup uploaded photo on failure
            if ($photoFileName && file_exists($this->modulePath . $photoFileName)) {
                unlink($this->modulePath . $photoFileName);
            }

            return $this->respond([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function success()
    {
        $data['title'] = 'Pendaftaran Berhasil';
        return view('Registrasi\Views\success', $data);
    }
}

