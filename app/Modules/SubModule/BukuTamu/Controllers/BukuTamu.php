<?php

namespace BukuTamu\Controllers;

use \CodeIgniter\Files\File;

class BukuTamu extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $memberguestModel;
    public $groupguestModel;
    public $uploadPath;
    public $modulePath;

    function __construct()
    {
        $this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
        $this->groupguestModel = new \BukuTamu\Models\GroupGuestModel();
        $this->uploadPath = ROOTPATH . 'public/uploads/';
        $this->modulePath = ROOTPATH . 'public/uploads/bukutamu/';

        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath);
        }

        if (!file_exists($this->modulePath)) {
            mkdir($this->modulePath);
        }
    }

    public function index()
    {
        $this->data['title'] = 'Buku Tamu - Anggota';
        $this->data['hari_ini'] = $this->kunjunganHariIni();
        echo view('BukuTamu\Views\list', $this->data);
    }

    public function non_anggota()
    {
        $this->data['title'] = 'Buku Tamu - Non Anggota';
        $this->data['hari_ini'] = $this->kunjunganHariIni();
        echo view('BukuTamu\Views\list_non_anggota', $this->data);
    }

    public function rombongan()
    {
        $this->data['title'] = 'Buku Tamu - Rombongan';
        $this->data['hari_ini'] = $this->kunjunganHariIni();
        echo view('BukuTamu\Views\list_rombongan', $this->data);
    }

    /** Kunjungan hari ini untuk admin; formulir buku tamu publik tidak lagi menampilkannya. */
    private function kunjunganHariIni(): array
    {
        $db      = db_connect();
        $hariIni = date('Y-m-d');

        $anggota     = $db->table('memberguesses')->where('DATE(CreateDate)', $hariIni)->where('NoAnggota IS NOT NULL')->countAllResults();
        $bukanAnggota = $db->table('memberguesses')->where('DATE(CreateDate)', $hariIni)->where('NoAnggota IS NULL')->countAllResults();
        $rombongan   = $db->table('groupguesses')->selectSum('CountPersonel', 'orang')->selectCount('ID', 'kelompok')
            ->where('DATE(CreateDate)', $hariIni)->get()->getRow();

        return [
            'anggota'            => $anggota,
            'bukan_anggota'      => $bukanAnggota,
            'rombongan'          => (int) ($rombongan->kelompok ?? 0),
            'rombongan_orang'    => (int) ($rombongan->orang ?? 0),
            'total'              => $anggota + $bukanAnggota + (int) ($rombongan->orang ?? 0),
        ];
    }

    public function delete(int $id = 0)
    {
        if (!$id) {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Parameter ID tidak ditemukan');
            return redirect()->to('bukutamu');
        }

        $slug = $this->request->getGet('slug') ?? 'anggota';
        if ($slug == 'rombongan') {
            $bukutamuDelete = $this->groupguestModel->delete($id);
        } else {
            $bukutamuDelete = $this->memberguestModel->delete($id);
        }

        if ($bukutamuDelete) {
            session()->setFlashdata('swal_icon', 'success');
            session()->setFlashdata('swal_title', 'Berhasil');
            session()->setFlashdata('swal_text', 'Buku Tamu berhasil dihapus');
            return redirect()->to('bukutamu?slug=' . $slug);
        } else {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Buku Tamu gagal dihapus');
            return redirect()->to('bukutamu?slug=' . $slug);
        }
    }
}
