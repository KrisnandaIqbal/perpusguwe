<?php

namespace Member\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class MemberPortal extends \Base\Controllers\BaseResourceController
{
    use ResponseTrait;

    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect('data');
        helper(['app', 'url', 'text', 'date_id']);
    }

    /**
     * Mengambil identifier member dari param atau session/header
     */
    protected function getMemberIdentifier()
    {
        $memberId = $this->request->getVar('member_id');
        $memberNo = $this->request->getVar('member_no');

        if (!$memberId && !$memberNo) {
            // Cek session
            $sessUser = session()->get('user');
            if ($sessUser && isset($sessUser['id'])) {
                $memberId = $sessUser['id'];
            }
        }

        return ['id' => $memberId, 'no' => $memberNo];
    }

    /**
     * Mendapatkan record member aktif
     */
    protected function findMember()
    {
        $ident = $this->getMemberIdentifier();
        $builder = $this->db->table('members');

        if (!empty($ident['id'])) {
            $builder->where('ID', $ident['id']);
        } elseif (!empty($ident['no'])) {
            $builder->where('MemberNo', $ident['no']);
        } else {
            // Fallback default member pertama jika belum ada parameter (untuk preview dashboard)
            $first = $builder->orderBy('ID', 'ASC')->limit(1)->get()->getRow();
            return $first;
        }

        return $builder->get()->getRow();
    }

    /**
     * GET /api/member/portal/profile
     * Informasi profil lengkap anggota dan ringkasan metrik
     */
    public function profile()
    {
        $member = $this->findMember();

        if (!$member) {
            return $this->respond([
                'success' => false,
                'message' => 'Data anggota tidak ditemukan'
            ], 404);
        }

        // Hitung total pinjaman aktif
        $activeLoansCount = $this->db->table('collectionloanitems')
            ->where('member_id', $member->ID)
            ->where('LoanStatus', 'Loan')
            ->countAllResults();

        // Hitung total pinjaman terlambat
        $overdueLoansCount = $this->db->table('collectionloanitems')
            ->where('member_id', $member->ID)
            ->where('LoanStatus', 'Loan')
            ->where('DueDate <', date('Y-m-d H:i:s'))
            ->countAllResults();

        // Hitung denda belum lunas
        $unpaidFines = $this->db->table('pelanggaran')
            ->selectSum('JumlahDenda', 'total')
            ->where('Member_id', $member->ID)
            ->where('Paid', 0)
            ->get()->getRow()->total ?? 0;

        // Total riwayat peminjaman yang pernah dilakukan
        $totalHistory = $this->db->table('collectionloanitems')
            ->where('member_id', $member->ID)
            ->countAllResults();

        $photoUrl = null;
        if (!empty($member->PhotoUrl)) {
            $photoUrl = base_url('uploads/anggota/' . $member->PhotoUrl);
        }

        $result = [
            'id' => (string)$member->ID,
            'memberNo' => $member->MemberNo,
            'name' => $member->Fullname,
            'email' => $member->Email ?? '-',
            'phone' => $member->NoHp ?? $member->Phone ?? '-',
            'address' => $member->AddressNow ?? $member->Address ?? '-',
            'registerDate' => $member->RegisterDate ? date('Y-m-d', strtotime($member->RegisterDate)) : '-',
            'expiryDate' => $member->EndDate ? date('Y-m-d', strtotime($member->EndDate)) : '-',
            'institution' => $member->InstitutionName ?? 'Umum',
            'status' => ((int)$member->active === 1) ? 'Active' : 'Inactive',
            'photoUrl' => $photoUrl,
            'metrics' => [
                'activeLoans' => (int)$activeLoansCount,
                'overdueLoans' => (int)$overdueLoansCount,
                'unpaidFines' => (float)$unpaidFines,
                'totalHistory' => (int)$totalHistory
            ]
        ];

        return $this->respond([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * GET /api/member/portal/loans
     * Daftar buku yang sedang dipinjam oleh anggota (peminjaman aktif)
     */
    public function loans()
    {
        $member = $this->findMember();
        if (!$member) {
            return $this->respond(['success' => false, 'message' => 'Anggota tidak ditemukan'], 404);
        }

        $builder = $this->db->table('collectionloanitems cli')
            ->select('cli.ID as loan_item_id, cli.CollectionLoan_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LoanStatus, cli.LateDays, c.NomorBarcode, cat.ID as catalog_id, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.CoverURL, cat.CallNumber')
            ->join('collections c', 'c.ID = cli.Collection_id', 'left')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->where('cli.member_id', $member->ID)
            ->where('cli.LoanStatus', 'Loan')
            ->orderBy('cli.DueDate', 'ASC');

        $rows = $builder->get()->getResult();
        $loans = [];

        foreach ($rows as $row) {
            $isOverdue = strtotime($row->DueDate) < time();
            $daysLeft = ceil((strtotime($row->DueDate) - time()) / 86400);

            $loans[] = [
                'id' => (string)$row->loan_item_id,
                'loanId' => (string)$row->CollectionLoan_id,
                'bookId' => (string)($row->catalog_id ?? $row->NomorBarcode),
                'barcode' => $row->NomorBarcode ?? '-',
                'title' => $row->Title ?? 'Koleksi Tanpa Judul',
                'author' => $row->Author ?? 'Anonim',
                'publisher' => $row->Publisher ?? '-',
                'publishYear' => $row->PublishYear ?? '-',
                'callNumber' => $row->CallNumber ?? '-',
                'coverUrl' => !empty($row->CoverURL) ? base_url('uploaded_files/sampul_koleksi/original/Catalog/' . $row->CoverURL) : null,
                'borrowDate' => $row->LoanDate ? date('Y-m-d', strtotime($row->LoanDate)) : '-',
                'dueDate' => $row->DueDate ? date('Y-m-d', strtotime($row->DueDate)) : '-',
                'status' => $isOverdue ? 'Overdue' : 'Active',
                'daysRemaining' => (int)$daysLeft
            ];
        }

        return $this->respond([
            'success' => true,
            'data' => $loans
        ]);
    }

    /**
     * GET /api/member/portal/history
     * Riwayat pengembalian dan transaksi denda anggota
     */
    public function history()
    {
        $member = $this->findMember();
        if (!$member) {
            return $this->respond(['success' => false, 'message' => 'Anggota tidak ditemukan'], 404);
        }

        // Ambil riwayat pinjam yang sudah dikembalikan (Return)
        $builder = $this->db->table('collectionloanitems cli')
            ->select('cli.ID as loan_item_id, cli.LoanDate, cli.DueDate, cli.ActualReturn, cli.LoanStatus, cli.LateDays, cat.Title, cat.Author, c.NomorBarcode')
            ->join('collections c', 'c.ID = cli.Collection_id', 'left')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->where('cli.member_id', $member->ID)
            ->orderBy('cli.ActualReturn', 'DESC')
            ->limit(50);

        $loanHistory = $builder->get()->getResult();

        // Ambil riwayat denda/pelanggaran
        $fineBuilder = $this->db->table('pelanggaran p')
            ->select('p.ID as fine_id, p.JumlahDenda, p.Paid, p.CreateDate, cat.Title, jp.JenisPelanggaran')
            ->join('collections c', 'c.ID = p.Collection_id', 'left')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->join('jenis_pelanggaran jp', 'jp.ID = p.JenisPelanggaran_id', 'left')
            ->where('p.Member_id', $member->ID)
            ->orderBy('p.CreateDate', 'DESC');

        $fines = $fineBuilder->get()->getResult();

        return $this->respond([
            'success' => true,
            'data' => [
                'loans' => $loanHistory,
                'fines' => $fines
            ]
        ]);
    }

    /**
     * GET /api/member/portal/catalogs
     * OPAC Katalog Koleksi Riil dari database
     */
    public function catalogs()
    {
        $keyword = $this->request->getVar('q');
        $limit = (int)($this->request->getVar('limit') ?? 20);
        $offset = (int)($this->request->getVar('offset') ?? 0);

        $builder = $this->db->table('catalogs cat')
            ->select('cat.ID, cat.Title, cat.Author, cat.Publisher, cat.PublishYear, cat.ISBN, cat.CallNumber, cat.CoverURL, cat.Subject, cat.Note, COUNT(c.ID) as total_copies, SUM(CASE WHEN c.Status_id = 1 THEN 1 ELSE 0 END) as available_copies')
            ->join('collections c', 'c.Catalog_id = cat.ID', 'left')
            ->where('cat.IsOPAC', 1)
            ->where('cat.active', 1)
            ->groupBy('cat.ID')
            ->orderBy('cat.ID', 'DESC')
            ->limit($limit, $offset);

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('cat.Title', $keyword)
                ->orLike('cat.Author', $keyword)
                ->orLike('cat.ISBN', $keyword)
                ->orLike('cat.Subject', $keyword)
                ->groupEnd();
        }

        $rows = $builder->get()->getResult();
        $catalogs = [];

        foreach ($rows as $row) {
            $catalogs[] = [
                'id' => (string)$row->ID,
                'title' => $row->Title ?? 'Koleksi Tanpa Judul',
                'author' => $row->Author ?? 'Anonim',
                'publisher' => $row->Publisher ?? '-',
                'publishYear' => $row->PublishYear ?? '-',
                'isbn' => $row->ISBN ?? '-',
                'callNumber' => $row->CallNumber ?? '-',
                'subject' => $row->Subject ?? '-',
                'synopsis' => !empty($row->Note) ? strip_tags($row->Note) : 'Belum ada sinopsis untuk koleksi ini.',
                'coverUrl' => !empty($row->CoverURL) ? base_url('uploaded_files/sampul_koleksi/original/Catalog/' . $row->CoverURL) : null,
                'totalCopies' => (int)$row->total_copies,
                'availableCopies' => (int)$row->available_copies,
                'status' => ((int)$row->available_copies > 0) ? 'Available' : 'Borrowed'
            ];
        }

        return $this->respond([
            'success' => true,
            'data' => $catalogs
        ]);
    }

    /**
     * POST /api/member/portal/extend
     * Permintaan perpanjangan buku mandiri oleh anggota
     */
    public function extend()
    {
        $member = $this->findMember();
        if (!$member) {
            return $this->respond(['success' => false, 'message' => 'Anggota tidak ditemukan'], 404);
        }

        $loanItemId = $this->request->getVar('loan_item_id');
        if (!$loanItemId) {
            return $this->respond(['success' => false, 'message' => 'Item pinjaman tidak valid'], 400);
        }

        $loanItem = $this->db->table('collectionloanitems')
            ->where('ID', $loanItemId)
            ->where('member_id', $member->ID)
            ->where('LoanStatus', 'Loan')
            ->get()->getRow();

        if (!$loanItem) {
            return $this->respond(['success' => false, 'message' => 'Peminjaman aktif tidak ditemukan'], 404);
        }

        // Tambah 7 hari ke DueDate
        $currentDue = strtotime($loanItem->DueDate);
        $newDueDate = date('Y-m-d H:i:s', strtotime('+7 days', $currentDue));

        $this->db->table('collectionloanitems')
            ->where('ID', $loanItemId)
            ->update([
                'DueDate' => $newDueDate,
                'UpdateDate' => date('Y-m-d H:i:s')
            ]);

        // Catat di collectionloanextends
        $this->db->table('collectionloanextends')->insert([
            'CollectionLoan_id' => $loanItem->CollectionLoan_id,
            'CollectionLoanItem_id' => $loanItemId,
            'Collection_id' => $loanItem->Collection_id,
            'Member_id' => $member->ID,
            'DateExtend' => date('Y-m-d H:i:s'),
            'DueDate' => $newDueDate,
            'CreateDate' => date('Y-m-d H:i:s'),
            'active' => 1
        ]);

        return $this->respond([
            'success' => true,
            'message' => 'Buku berhasil diperpanjang hingga ' . date('d M Y', strtotime($newDueDate)),
            'newDueDate' => date('Y-m-d', strtotime($newDueDate))
        ]);
    }
}
