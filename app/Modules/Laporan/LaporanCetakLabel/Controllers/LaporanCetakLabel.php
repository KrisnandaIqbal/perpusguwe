<?php

namespace LaporanCetakLabel\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanCetakLabel extends \Base\Controllers\BaseController
{
    private const NAMA = "COALESCE(NULLIF(TRIM(CONCAT_WS(' ', u.first_name, u.last_name)), ''), l.username)";
    private const PER_PAGE = [100, 250, 500, 1000];

    private function query()
    {
        $db   = \Config\Database::connect();
        $from = $this->request->getGet('dari');
        $to   = $this->request->getGet('sampai');
        $uid  = $this->request->getGet('user_id');
        $mode = $this->request->getGet('mode') ?: 'asli';
        $nama = self::NAMA;

        $b = $db->table('item_label_print_log l')->join('users u', 'u.id = l.user_id', 'left');

        if ($mode === 'unik') {
            $b->select("MAX(l.print_date) AS print_date, MAX($nama) AS nama_user,
                        l.item_code, MAX(l.title) AS title,
                        MAX(l.author) AS author, MAX(l.call_number) AS call_number", false)
              ->groupBy('l.item_code, l.user_id');
        } else {
            $b->select("l.print_date, $nama AS nama_user,
                        l.item_code, l.title, l.author, l.call_number", false);
        }

        if ($from) $b->where('l.print_date >=', $from . ' 00:00:00');
        if ($to)   $b->where('l.print_date <=', $to . ' 23:59:59');
        if ($uid)  $b->where('l.user_id', (int) $uid);

        return $b;
    }

    public function index()
    {
        $db   = \Config\Database::connect();
        $nama = self::NAMA;

        $per = (int) $this->request->getGet('per');
        if (!in_array($per, self::PER_PAGE, true)) $per = 500;

        $b     = $this->query();
        $total = $b->countAllResults(false);
        $pages = max(1, (int) ceil($total / $per));
        $page  = min(max(1, (int) $this->request->getGet('page')), $pages);

        $rows = $b->orderBy('print_date', 'DESC')->limit($per, ($page - 1) * $per)->get()->getResultArray();

        $data = [
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'pages'   => $pages,
            'per'     => $per,
            'perOpts' => self::PER_PAGE,
            'users'   => $db->query("SELECT DISTINCT l.user_id, $nama AS nama
                                     FROM item_label_print_log l
                                     LEFT JOIN users u ON u.id = l.user_id
                                     ORDER BY nama")->getResultArray(),
            'dari'    => $this->request->getGet('dari'),
            'sampai'  => $this->request->getGet('sampai'),
            'user_id' => $this->request->getGet('user_id'),
            'mode'    => $this->request->getGet('mode') ?: 'asli',
        ];
        return view('LaporanCetakLabel\Views\index', $data);
    }

    public function export()
    {
        $rows = $this->query()->orderBy('print_date', 'DESC')->get()->getResultArray();

        $ss = new Spreadsheet();
        $sh = $ss->getActiveSheet();
        $sh->setTitle('Cetak Label');
        $sh->fromArray(['Waktu Cetak', 'Nama User', 'Kode Buku', 'Judul', 'Pengarang', 'Call Number'], null, 'A1');
        $sh->getStyle('A1:F1')->getFont()->setBold(true);

        $r = 2;
        foreach ($rows as $row) {
            $sh->setCellValueExplicit("A$r", (string) $row['print_date'], 's');
            $sh->setCellValueExplicit("B$r", (string) $row['nama_user'], 's');
            $sh->setCellValueExplicit("C$r", (string) $row['item_code'], 's');
            $sh->setCellValueExplicit("D$r", (string) $row['title'], 's');
            $sh->setCellValueExplicit("E$r", (string) $row['author'], 's');
            $sh->setCellValueExplicit("F$r", (string) $row['call_number'], 's');
            $r++;
        }
        foreach (range('A', 'F') as $c) $sh->getColumnDimension($c)->setAutoSize(true);

        $name = 'laporan_cetak_label_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$name\"");
        (new Xlsx($ss))->save('php://output');
        exit;
    }
}
