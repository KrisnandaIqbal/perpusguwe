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
        
        // Load data from master tables
        // NIK and NIP from master_jenis_identitas
        $data['jenis_identitas'] = $this->db->table('master_jenis_identitas')
                                            ->where('active', 1)
                                            ->get()->getResult();
        
        $data['jenis_anggota'] = $this->db->table('jenis_anggota')
                                          ->where('active', 1)
                                          ->get()->getResult();

        $data['unit_kerja'] = $this->db->table('master_unit')
                                       ->where('active', 1)
                                       ->get()->getResult();

        return view('Registrasi\Views\index', $data);
    }

    public function submit()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(base_url('home/pendaftaran-online'));
        }

        $jenisAnggotaId = $this->request->getPost('JenisAnggota_id');
        $fullname = $this->request->getPost('Fullname');
        $identityTypeId = $this->request->getPost('IdentityType_id');
        $identityNo = $this->request->getPost('IdentityNo');
        $unitKerjaId = $this->request->getPost('UnitKerja_id');
        $address = $this->request->getPost('AddressNow');
        $email = $this->request->getPost('Email');
        $phone = $this->request->getPost('Phone');
        $persetujuan = $this->request->getPost('Persetujuan');

        // Validation Rules
        $rules = [
            'JenisAnggota_id' => 'required',
            'Fullname' => 'required|min_length[3]',
            'UnitKerja_id' => 'required',
            'AddressNow' => 'required',
            'Email' => 'required|valid_email',
            'Phone' => 'required',
            'Persetujuan' => 'required',
        ];

        // Conditional Rules
        // Check if Magang / Non ASN
        $jenisAnggota = $this->db->table('jenis_anggota')->where('id', $jenisAnggotaId)->get()->getRow();
        $isMagang = false;
        if ($jenisAnggota && (stripos($jenisAnggota->jenisanggota, 'Magang') !== false || stripos($jenisAnggota->jenisanggota, 'Non ASN') !== false)) {
            $isMagang = true;
        }

        $rules['IdentityNo'] = 'required';

        if ($isMagang) {
            // Non-ASN / Magang uses NIK
            $nikType = $this->db->table('master_jenis_identitas')->where('IsNIK', 1)->get()->getRow();
            if ($nikType) {
                $identityTypeId = $nikType->id;
            }
        } else {
            // ASN strictly uses NIP
            $nipType = $this->db->table('master_jenis_identitas')->like('Nama', 'NIP')->get()->getRow();
            if ($nipType) {
                $identityTypeId = $nipType->id;
            } else {
                $rules['IdentityType_id'] = 'required';
            }
        }

        if (!$this->validate($rules)) {
            return $this->respond([
                'success' => false,
                'message' => 'Validasi gagal, mohon lengkapi semua field yang wajib.',
                'errors' => $this->validator->getErrors()
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

        if ($isMagang) {
            if (!IdentityValidator::isValidNIK($identityNo)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Nomor Identitas untuk Non-ASN / Magang (NIK) harus 16 digit angka.'
                ]);
            }
        } else {
            // ASN is strictly NIP (18 digits)
            if (!IdentityValidator::isValidNIP($identityNo)) {
                return $this->respond([
                    'success' => false,
                    'message' => 'Format NIP untuk ASN tidak valid. Harus 18 digit angka resmi BKN.'
                ]);
            }
        }

        // Duplicate Check
        $checkIdentity = $this->db->table('members')->where('IdentityNo', $identityNo)->countAllResults();
        if ($checkIdentity > 0) {
            return $this->respond([
                'success' => false,
                'message' => 'Nomor Identitas sudah terdaftar sebelumnya.'
            ]);
        }

        $checkEmail = $this->db->table('members')->where('Email', $email)->countAllResults();
        if ($checkEmail > 0) {
            return $this->respond([
                'success' => false,
                'message' => 'Email sudah terdaftar sebelumnya.'
            ]);
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
            'IdentityType_id' => $identityTypeId,
            'IdentityNo' => $identityNo,
            'UnitKerja_id' => $unitKerjaId,
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
            $this->db->table('members')->insert($dataInsert);
            $newId = $this->db->insertID();

            // Hook for sending email
            $this->sendNotificationEmail($email, $fullname);

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

    private function sendNotificationEmail($email, $fullname)
    {
        // TODO: Implement email sending logic here
        // Email library can be loaded and used to send a confirmation email
    }
}

