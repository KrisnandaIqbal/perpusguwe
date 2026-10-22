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
            return redirect()->to($slug == 'rombongan' ? 'bukutamu/rombongan' : 'bukutamu');
        } else {
            session()->setFlashdata('swal_icon', 'error');
            session()->setFlashdata('swal_title', 'Gagal');
            session()->setFlashdata('swal_text', 'Buku Tamu gagal dihapus');
            return redirect()->to($slug == 'rombongan' ? 'bukutamu/rombongan' : 'bukutamu');
        }
    }

    /**
     * Satu file Excel berisi tiga sheet: Anggota, Bukan Anggota, dan Rombongan.
     * Rentang tanggal kunjungan lewat ?dari=YYYY-MM-DD&sampai=YYYY-MM-DD; kosong berarti semua data.
     */
    public function export()
    {
        $dari   = $this->tanggalExport($this->request->getGet('dari'));
        $sampai = $this->tanggalExport($this->request->getGet('sampai'));

        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $excel->removeSheetByIndex(0);

        $this->sheetExport($excel, 'Anggota',
            ['No', 'Tgl. Kunjungan', 'Nama Anggota', 'Nomor Anggota', 'Unit Asal', 'WhatsApp', 'Lokasi Ruang'],
            array_map(static fn ($r) => [$r->CreateDate, $r->Nama, $r->NoAnggota, \GuestBook\Libraries\IsianTamu::teksUnit($r->Unit_name, $r->SubUnit_name, $r->UnitLainnya), $r->HP, $r->Location_name],
                $this->tamuExport(true, $dari, $sampai)));

        $this->sheetExport($excel, 'Bukan Anggota',
            ['No', 'Tgl. Kunjungan', 'Nama Pengunjung', 'Unit Asal', 'WhatsApp', 'Lokasi Ruang'],
            array_map(static fn ($r) => [$r->CreateDate, $r->Nama, \GuestBook\Libraries\IsianTamu::teksUnit($r->Unit_name, $r->SubUnit_name, $r->UnitLainnya), $r->HP, $r->Location_name],
                $this->tamuExport(false, $dari, $sampai)));

        $rombongan = db_connect()->table('groupguesses as a')
            ->select('a.CreateDate, a.NamaKetua, a.NomerTelponKetua, a.AsalInstansi, a.CountPersonel, l.Name as Location_name')
            ->join('locations l', 'l.ID = a.Location_ID', 'left');
        $this->filterTanggal($rombongan, $dari, $sampai);
        $this->sheetExport($excel, 'Rombongan',
            ['No', 'Tgl. Kunjungan', 'Penanggung Jawab', 'WhatsApp', 'Asal Instansi', 'Jumlah Orang', 'Lokasi Ruang'],
            array_map(static fn ($r) => [$r->CreateDate, $r->NamaKetua, $r->NomerTelponKetua, $r->AsalInstansi, (int) $r->CountPersonel, $r->Location_name],
                $rombongan->orderBy('a.CreateDate')->get()->getResult()));

        $excel->setActiveSheetIndex(0);

        ob_start();
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel))->save('php://output');
        $isi = ob_get_clean();

        $nama = 'buku-tamu' . ($dari ? '-' . $dari : '') . ($sampai ? '-sd-' . $sampai : '') . '.xlsx';

        return $this->response
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $nama . '"')
            ->setBody($isi);
    }

    /** Tanggal Y-m-d yang valid, atau null kalau kosong atau tidak valid. */
    private function tanggalExport($nilai): ?string
    {
        $tanggal = \DateTime::createFromFormat('!Y-m-d', (string) $nilai);

        return $tanggal && $tanggal->format('Y-m-d') === $nilai ? $nilai : null;
    }

    private function filterTanggal($builder, ?string $dari, ?string $sampai): void
    {
        if ($dari) {
            $builder->where('a.CreateDate >=', $dari . ' 00:00:00');
        }
        if ($sampai) {
            $builder->where('a.CreateDate <=', $sampai . ' 23:59:59');
        }
    }

    /** Kunjungan perorangan: anggota (punya NoAnggota) atau bukan anggota. */
    private function tamuExport(bool $anggota, ?string $dari, ?string $sampai): array
    {
        $builder = db_connect()->table('memberguesses as a')
            ->select('a.CreateDate, a.Nama, a.NoAnggota, a.HP, a.UnitLainnya, mu.nama_unit as Unit_name, ms.nama_subunit as SubUnit_name, l.Name as Location_name')
            ->join('master_unit mu', 'mu.id = a.UnitKerja_id', 'left')
            ->join('master_subunit ms', 'ms.id = a.SubUnitKerja_id', 'left')
            ->join('locations l', 'l.ID = a.Location_id', 'left')
            ->where($anggota ? 'a.NoAnggota IS NOT NULL' : 'a.NoAnggota IS NULL');
        $this->filterTanggal($builder, $dari, $sampai);

        return $builder->orderBy('a.CreateDate')->get()->getResult();
    }

    /** Satu sheet: judul kolom tebal, nomor urut otomatis, lebar kolom menyesuaikan isi. */
    private function sheetExport(\PhpOffice\PhpSpreadsheet\Spreadsheet $excel, string $judul, array $kolom, array $baris): void
    {
        $sheet = $excel->createSheet();
        $sheet->setTitle($judul);
        $sheet->fromArray($kolom, null, 'A1');
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);

        foreach ($baris as $i => $isi) {
            $sheet->fromArray(array_merge([$i + 1], $isi), null, 'A' . ($i + 2));
        }

        foreach ($sheet->getColumnIterator() as $k) {
            $sheet->getColumnDimension($k->getColumnIndex())->setAutoSize(true);
        }
        $sheet->freezePane('A2');
    }
}
