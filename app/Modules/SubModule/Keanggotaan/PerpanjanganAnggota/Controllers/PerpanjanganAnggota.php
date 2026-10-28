<?php

namespace PerpanjanganAnggota\Controllers;

use \CodeIgniter\Files\File;

class PerpanjanganAnggota extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $anggotaModel;
    public $perpanjanganModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->perpanjanganModel = new \PerpanjanganAnggota\Models\PerpanjanganAnggotaModel();
        $this->anggotaModel = new \Anggota\Models\AnggotaModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/perpanjangan-anggota/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
        helper('adminigniter');
        helper('reference');
        helper('anggota');
        helper('sirkulasi');
        helper('tgl_indo');
        helper('url');
        helper('thumbnail');
    }

    public function index()
    {
        $query = $this->perpanjanganModel
            ->select('member_perpanjangan.*, members.Fullname as nama, members.MemberNo as MembersNo')
            ->join('members', 'members.ID = member_perpanjangan.Member_id', 'left')
            ->orderBy('member_perpanjangan.ID', 'DESC');

        $perpanjangans = $query->findAll();

        $this->data['title'] = 'Daftar Perpanjangan Anggota';
        $this->data['perpanjangans'] = $perpanjangans;

        return view('PerpanjanganAnggota\Views\list', $this->data);
    }

    public function create()
    {
        $this->data['title'] = 'Tambah Perpanjangan Anggota';

        $unitModel = new \UnitKerja\Models\UnitKerjaModel();
        $subUnitModel = new \App\Models\SubUnitModel();
        $this->data['unit_kerja'] = $unitModel->where('active', 1)->orderBy('id', 'ASC')->findAll();
        $this->data['sub_units_grouped'] = $subUnitModel->getAllActiveGrouped();

        $db = \Config\Database::connect();
        $this->data['jenis_anggota_list'] = $db->table('jenis_anggota')
            ->where('active', 1)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $this->validation->setRules([
            'Member_id'       => ['label' => 'Anggota', 'rules' => 'required'],
            'EndDate'         => ['label' => 'Tanggal Berakhir', 'rules' => 'required'],
            'Jenisanggota_id' => ['label' => 'Kategori Anggota', 'rules' => 'required'],
        ]);

        if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
            $member_ids     = (array) $this->request->getPost('Member_id');
            $biaya          = $this->request->getPost('biaya');
            $keterangan     = $this->request->getPost('Keterangan');
            $endDate        = $this->request->getPost('EndDate');
            $jenisId        = $this->request->getPost('Jenisanggota_id');
            $unitKerjaId    = $this->request->getPost('UnitKerja_id');
            $subUnitKerjaId = $this->request->getPost('SubUnitKerja_id');
            $statusNonAsn   = $this->request->getPost('status_non_asn');
            $isLunas        = $this->request->getPost('is_lunas') ? 1 : 0;

            $isAsn = function_exists('isJenisAnggotaAsn') ? isJenisAnggotaAsn($jenisId) : ($jenisId == 22);

            $db->transStart();

            foreach ($member_ids as $id_anggota) {
                $save_data = [
                    'Member_id'  => $id_anggota,
                    'Tanggal'    => $endDate,
                    'biaya'      => $biaya,
                    'Keterangan' => $keterangan,
                    'IsLunas'    => $isLunas,
                    'Branch_id'  => branch_id(),
                    'UpdateBy'   => user_id(),
                ];

                $newPerpanjanganId = $this->perpanjanganModel->insert($save_data, true);

                if ($newPerpanjanganId !== false && $newPerpanjanganId > 0) {
                    $data = [
                        'EndDate'          => $endDate,
                        'JenisAnggota_id'  => $jenisId,
                        'StatusAnggota_id' => 3, // Set ke Aktif jika sebelumnya Kadaluarsa/Suspend
                    ];

                    if (!empty($unitKerjaId)) {
                        $data['UnitKerja_id'] = $unitKerjaId;
                    }

                    if ($subUnitKerjaId !== null && $subUnitKerjaId !== '') {
                        $data['SubUnitKerja_id'] = !empty($subUnitKerjaId) ? $subUnitKerjaId : null;
                    }

                    if ($isAsn) {
                        $data['status_non_asn'] = null;
                    } else {
                        if (!empty($statusNonAsn)) {
                            $data['status_non_asn'] = $statusNonAsn;
                        }
                    }

                    $this->anggotaModel->update($id_anggota, $data);

                    // Sinkronkan pelanggaran suspend jika anggota diperpanjang
                    if (function_exists('sinkronkan_status_suspend_anggota')) {
                        sinkronkan_status_suspend_anggota($id_anggota, 3);
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() !== false) {
                session()->setFlashdata('swal_icon', 'success');
                session()->setFlashdata('swal_title', 'Berhasil');
                session()->setFlashdata('swal_text', 'Perpanjangan anggota berhasil disimpan');
                return redirect()->to('/perpanjangan-anggota');
            } else {
                session()->setFlashdata('swal_icon', 'error');
                session()->setFlashdata('swal_title', 'Gagal');
                session()->setFlashdata('swal_text', 'Perpanjangan anggota gagal disimpan');
                return view('PerpanjanganAnggota\Views\add', $this->data);
            }
        } else {
            $this->data['redirect'] = base_url('perpanjangan-anggota/create');
            set_message('message', $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message'));
            return view('PerpanjanganAnggota\Views\add', $this->data);
        }
    }

    public function edit(int $id = null)
    {
        $this->data['title'] = 'Ubah PerpanjanganAnggota';
        $perpanjangananggota = $this->anggotaModel->find($id);
        $this->data['perpanjangananggota'] = $perpanjangananggota;

        $this->validation->setRule('name', 'Nama', 'required');
        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                $slug = url_title($this->request->getPost('name'), '-', TRUE);
                $update_data = [
                    'name' => $this->request->getPost('name'),
                    'slug' => $slug,
                    'sort' => $this->request->getPost('sort'),
                    'description' => $this->request->getPost('description'),
                    'updated_by' => user_id(),
                ];

                $perpanjanganUpdate = $this->anggotaModel->update($id, $update_data);

                if ($perpanjanganUpdate) {
                    session()->setFlashdata('swal_icon', 'success');
                    session()->setFlashdata('swal_title', 'Berhasil');
                    session()->setFlashdata('swal_text', 'Perpanjangan anggota berhasil diubah');
                    return redirect()->to('/perpanjangan-anggota');
                } else {
                    session()->setFlashdata('swal_icon', 'error');
                    session()->setFlashdata('swal_title', 'Gagal');
                    session()->setFlashdata('swal_text', 'Perpanjangan anggota gagal diubah');
                    return redirect()->to('/perpanjangan-anggota/edit/' . $id);
                }
            }
        }

        $this->data['redirect'] = base_url('perpanjangan-anggota/edit/' . $id);
        return view('PerpanjanganAnggota\Views\update', $this->data);
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'ID tidak ditemukan');
            return redirect()->to('/perpanjangan-anggota');
        }
        $perpanjanganDelete = $this->perpanjanganModel->delete($id);
        if ($perpanjanganDelete) {
            session()->setFlashdata('swal_icon', 'success');
            session()->setFlashdata('swal_title', 'Berhasil');
            session()->setFlashdata('swal_text', 'Perpanjangan anggota berhasil dihapus');
        } else {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Perpanjangan anggota gagal dihapus');
        }
        return redirect()->to('/perpanjangan-anggota');
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field');
        $value = $this->request->getGet('value');

        $perpanjanganUpdate = $this->anggotaModel->update($id, array($field => $value));

        if ($perpanjanganUpdate) {
            set_message('toastr_msg', ' PerpanjanganAnggota berhasil diubah');
            set_message('toastr_type', 'success');
        } else {
            set_message('toastr_msg', ' PerpanjanganAnggota gagal diubah');
            set_message('toastr_type', 'warning');
        }
        return redirect()->to('/perpanjangan-anggota');
    }
}
