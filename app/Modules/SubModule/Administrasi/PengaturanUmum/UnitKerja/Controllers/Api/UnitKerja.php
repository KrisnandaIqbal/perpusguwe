<?php

namespace UnitKerja\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\DataTable;
use App\Models\SubUnitModel;
use UnitKerja\Models\UnitKerjaModel;

class UnitKerja extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;

    protected $unitkerjaModel;
    protected $subunitModel;
    protected $session;

    public function __construct()
    {
        helper(['url', 'form', 'auth', 'app', 'anggota']);
        $this->unitkerjaModel = new UnitKerjaModel();
        $this->subunitModel   = new SubUnitModel();
        $this->session        = session();
    }

    /**
     * Datatable untuk Sub-Unit Kerja (mendukung filter per unit_id)
     */
    public function datatable_subunit()
    {
        $db = db_connect();
        $builder = $db->table('master_subunit as su')
            ->select('su.id, su.id as action, su.unit_id, su.nama_subunit, su.sort, su.active, su.CreateDate, mu.nama_unit as UnitKerja')
            ->select('(SELECT COUNT(m.ID) FROM members m WHERE m.SubUnitKerja_id = su.id) as MemberCount')
            ->join('master_unit mu', 'mu.id = su.unit_id', 'left');

        $unitId = $this->request->getGet('unit_id');
        if (!empty($unitId)) {
            $builder->where('su.unit_id', (int) $unitId);
        }

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('nama_subunit', function ($row) {
                return '<strong>' . esc($row->nama_subunit) . '</strong>';
            })
            ->edit('UnitKerja', function ($row) {
                return '<span class="badge badge-light border text-dark font-weight-normal px-2 py-1">' . esc($row->UnitKerja ?? '-') . '</span>';
            })
            ->edit('sort', function ($row) {
                return '<span class="badge badge-secondary">' . (int) $row->sort . '</span>';
            })
            ->edit('MemberCount', function ($row) {
                if ($row->MemberCount > 0) {
                    return '<span class="badge badge-info">' . (int) $row->MemberCount . ' anggota</span>';
                }
                return '<span class="text-muted small">0 anggota</span>';
            })
            ->edit('active', function ($row) {
                $status = (int) $row->active === 1 ? 'Aktif' : 'Nonaktif';
                $class  = (int) $row->active === 1 ? 'success' : 'danger';
                return '<span class="badge badge-' . $class . '">' . $status . '</span>';
            })
            ->edit('action', function ($row) {
                $edit = '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-primary btn-edit-subunit" title="Ubah Nama & Urutan"><i class="fa fa-edit"></i></button>';
                
                if ((int) $row->active === 1) {
                    $toggle = '<a href="' . base_url('master-unit-kerja/apply_status_subunit/' . $row->id . '?value=0') . '" class="btn btn-sm btn-warning" title="Nonaktifkan"><i class="fa fa-times"></i></a>';
                } else {
                    $toggle = '<a href="' . base_url('master-unit-kerja/apply_status_subunit/' . $row->id . '?value=1') . '" class="btn btn-sm btn-success" title="Aktifkan"><i class="fa fa-check"></i></a>';
                }

                if ((int) $row->MemberCount > 0) {
                    $del = '<button type="button" class="btn btn-sm btn-secondary" title="Tidak dapat dihapus karena sudah dipakai anggota" onclick="alertSudahDipakai(' . (int) $row->MemberCount . ')"><i class="fa fa-trash"></i></button>';
                } else {
                    $del = '<a href="' . base_url('master-unit-kerja/delete_subunit/' . $row->id) . '" onclick="return confirm(\'Yakin ingin menghapus sub-unit ' . esc($row->nama_subunit, 'js') . '?\')" class="btn btn-sm btn-danger" title="Hapus"><i class="fa fa-trash"></i></a>';
                }

                return '<div class="btn-group" role="group">' . $edit . ' ' . $toggle . ' ' . $del . '</div>';
            })
            ->toJson();

        return $dataTable;
    }

    /**
     * Endpoint detail sub-unit (untuk modal edit)
     */
    public function detail_subunit($id = null)
    {
        $data = $this->subunitModel->find($id);
        if ($data) {
            $data->member_count = $this->subunitModel->isUsedByMembers((int) $id) ? 1 : 0;
            return $this->respond($data);
        }
        return $this->failNotFound('Sub-Unit Kerja tidak ditemukan');
    }

    /**
     * Endpoint simpan sub-unit baru
     */
    public function create_subunit()
    {
        $unitId = (int) $this->request->getPost('unit_id');
        $nama   = trim((string) $this->request->getPost('nama_subunit'));
        $sort   = (int) $this->request->getPost('sort');
        $active = (int) ($this->request->getPost('active') ?? 1);

        if ($unitId <= 0 || $nama === '') {
            return $this->fail('Unit kerja induk dan nama sub-unit wajib diisi.');
        }

        $id = $this->subunitModel->insert([
            'unit_id'      => $unitId,
            'nama_subunit' => $nama,
            'sort'         => $sort,
            'active'       => $active,
        ]);

        if ($id) {
            return $this->respondCreated([
                'success' => true,
                'message' => 'Sub-Unit Kerja berhasil ditambahkan',
                'id'      => $id
            ]);
        }

        return $this->fail('Gagal menyimpan Sub-Unit Kerja');
    }

    /**
     * Endpoint edit sub-unit
     */
    public function edit_subunit($id = null)
    {
        $unitId = (int) $this->request->getPost('unit_id');
        $nama   = trim((string) $this->request->getPost('nama_subunit'));
        $sort   = (int) $this->request->getPost('sort');
        $active = (int) ($this->request->getPost('active') ?? 1);

        if (!$id || $nama === '') {
            return $this->fail('Data tidak valid.');
        }

        $dataUpdate = [
            'nama_subunit' => $nama,
            'sort'         => $sort,
            'active'       => $active,
        ];

        if ($unitId > 0) {
            $dataUpdate['unit_id'] = $unitId;
        }

        $ok = $this->subunitModel->update($id, $dataUpdate);
        if ($ok) {
            return $this->respond([
                'success' => true,
                'message' => 'Sub-Unit Kerja berhasil diperbarui'
            ]);
        }

        return $this->fail('Gagal memperbarui Sub-Unit Kerja');
    }

    /**
     * Endpoint hapus sub-unit (dengan proteksi ketat)
     */
    public function delete_subunit($id = null)
    {
        if (!$id) {
            return $this->fail('ID Sub-Unit tidak valid');
        }

        if ($this->subunitModel->isUsedByMembers((int) $id)) {
            return $this->fail('Sub-Unit Kerja ini sudah dipakai oleh anggota dan tidak boleh dihapus. Silakan nonaktifkan saja.', 400);
        }

        $ok = $this->subunitModel->delete($id);
        if ($ok) {
            return $this->respondDeleted([
                'success' => true,
                'message' => 'Sub-Unit Kerja berhasil dihapus'
            ]);
        }

        return $this->fail('Gagal menghapus Sub-Unit Kerja');
    }

    /**
     * Endpoint publik ringan untuk dropdown berantai
     * Mengembalikan list sub-unit aktif milik unit tertentu
     */
    public function sub_units($unitId = null)
    {
        $list = $this->subunitModel->getActiveByUnitId((int) $unitId);
        return $this->respond($list, 200);
    }

    /**
     * Datatable untuk Unit Kerja Induk (master_unit)
     */
    public function datatable()
    {
        $db = db_connect();
        $builder = $db->table('master_unit as mu')
            ->select('mu.id, mu.id as action, mu.nama_unit, mu.active, mu.CreateDate')
            ->select('(SELECT COUNT(su.id) FROM master_subunit su WHERE su.unit_id = mu.id) as SubUnitCount')
            ->select('(SELECT COUNT(m.ID) FROM members m WHERE m.UnitKerja_id = mu.id) as MemberCount');

        $dataTable = DataTable::of($builder)
            ->addNumbering('no')
            ->edit('nama_unit', function ($row) {
                return '<strong>' . esc($row->nama_unit) . '</strong>';
            })
            ->edit('SubUnitCount', function ($row) {
                return '<span class="badge badge-info">' . (int) $row->SubUnitCount . ' Sub-Unit</span>';
            })
            ->edit('MemberCount', function ($row) {
                if ($row->MemberCount > 0) {
                    return '<span class="badge badge-primary">' . (int) $row->MemberCount . ' anggota</span>';
                }
                return '<span class="text-muted small">0 anggota</span>';
            })
            ->edit('active', function ($row) {
                $status = (int) $row->active === 1 ? 'Aktif' : 'Nonaktif';
                $class  = (int) $row->active === 1 ? 'success' : 'danger';
                return '<span class="badge badge-' . $class . '">' . $status . '</span>';
            })
            ->edit('action', function ($row) {
                $edit = '<button type="button" data-id="' . $row->id . '" class="btn btn-sm btn-primary btn-edit-unit" title="Ubah Nama Unit"><i class="fa fa-edit"></i></button>';
                
                if ((int) $row->active === 1) {
                    $toggle = '<a href="' . base_url('master-unit-kerja/apply_status/' . $row->id . '?value=0') . '" class="btn btn-sm btn-warning" title="Nonaktifkan"><i class="fa fa-times"></i></a>';
                } else {
                    $toggle = '<a href="' . base_url('master-unit-kerja/apply_status/' . $row->id . '?value=1') . '" class="btn btn-sm btn-success" title="Aktifkan"><i class="fa fa-check"></i></a>';
                }

                return '<div class="btn-group" role="group">' . $edit . ' ' . $toggle . '</div>';
            })
            ->toJson();

        return $dataTable;
    }

    /**
     * Endpoint detail Unit Kerja Induk
     */
    public function detail($id = null)
    {
        $data = $this->unitkerjaModel->find($id);
        if ($data) {
            return $this->respond($data);
        }
        return $this->failNotFound('Unit Kerja tidak ditemukan');
    }

    /**
     * Endpoint edit nama Unit Kerja Induk
     */
    public function edit($id = null)
    {
        $nama   = trim((string) $this->request->getPost('nama_unit'));
        $active = (int) ($this->request->getPost('active') ?? 1);

        if (!$id || $nama === '') {
            return $this->fail('Nama unit kerja wajib diisi.');
        }

        $ok = $this->unitkerjaModel->update($id, [
            'nama_unit' => $nama,
            'active'    => $active,
        ]);

        if ($ok) {
            return $this->respond([
                'success' => true,
                'message' => 'Unit Kerja berhasil diperbarui'
            ]);
        }

        return $this->fail('Gagal memperbarui Unit Kerja');
    }
}
