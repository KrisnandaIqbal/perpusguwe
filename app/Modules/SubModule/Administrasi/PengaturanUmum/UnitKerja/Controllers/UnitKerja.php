<?php

namespace UnitKerja\Controllers;

use UnitKerja\Models\UnitKerjaModel;
use App\Models\SubUnitModel;

class UnitKerja extends \Base\Controllers\BaseController
{
    public $unitkerjaModel;
    public $subunitModel;

    public function __construct()
    {
        $this->unitkerjaModel = new UnitKerjaModel();
        $this->subunitModel   = new SubUnitModel();
        helper(['url', 'form', 'app', 'anggota']);
    }

    public function index()
    {
        $db = db_connect();
        $this->data['title'] = 'Pengaturan Unit Kerja & Sub-Unit';
        $this->data['units'] = $db->table('master_unit')->orderBy('id', 'ASC')->get()->getResult();

        return view('UnitKerja\Views\list', $this->data);
    }

    public function apply_status($id)
    {
        $field = $this->request->getGet('field') ?? 'active';
        $value = (int) $this->request->getGet('value');

        $updated = $this->unitkerjaModel->update($id, [$field => $value]);
        if ($updated) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Unit Kerja berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Unit Kerja gagal diubah');
        }

        return redirect()->to('master-unit-kerja');
    }

    public function apply_status_subunit($id)
    {
        $value = (int) $this->request->getGet('value');
        $updated = $this->subunitModel->update($id, ['active' => $value]);

        if ($updated) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Status Sub-Unit Kerja berhasil diubah');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Status Sub-Unit Kerja gagal diubah');
        }

        return redirect()->to('master-unit-kerja');
    }

    public function delete_subunit(int $id = 0)
    {
        if (!$id) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Error');
            $this->session->setFlashdata('swal_text', 'ID Sub-Unit tidak valid');
            return redirect()->to('master-unit-kerja');
        }

        // ATURAN KERAS: Yang sudah dipakai anggota TIDAK boleh dihapus, hanya dinonaktifkan.
        if ($this->subunitModel->isUsedByMembers($id)) {
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Dilarang Menghapus');
            $this->session->setFlashdata('swal_text', 'Sub-Unit Kerja ini sudah dipakai oleh anggota perpustakaan. Sub-unit tidak boleh dihapus demi menjaga riwayat data, silakan nonaktifkan saja.');
            return redirect()->to('master-unit-kerja');
        }

        $deleted = $this->subunitModel->delete($id);
        if ($deleted) {
            $this->session->setFlashdata('swal_icon', 'success');
            $this->session->setFlashdata('swal_title', 'Berhasil');
            $this->session->setFlashdata('swal_text', 'Sub-Unit Kerja berhasil dihapus');
        } else {
            $this->session->setFlashdata('swal_icon', 'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_text', 'Sub-Unit Kerja gagal dihapus');
        }

        return redirect()->to('master-unit-kerja');
    }
}
