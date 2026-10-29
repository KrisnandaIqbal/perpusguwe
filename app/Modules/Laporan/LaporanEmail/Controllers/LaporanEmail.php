<?php

namespace LaporanEmail\Controllers;

use App\Libraries\EmailLogger;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanEmail extends \Base\Controllers\BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $summary = $this->computeSummary();

        $data = [
            'title'       => 'Laporan Pengiriman Email',
            'summary'     => $summary,
            'emailTypes'  => EmailLogger::typeLabels(),
            'columnNames' => $this->columnNames(),
        ];

        return view('LaporanEmail\Views\index', $data);
    }

    public function preview()
    {
        $columns = $this->request->getPost('columns') ?? [];
        if (empty($columns)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Pilih minimal satu kolom untuk ditampilkan.',
            ]);
        }

        $filters = $this->collectFilters();
        $summary = $this->computeSummary($filters);

        $builder = $this->buildQuery($filters);
        $builder->limit(100);
        $rows = $builder->get()->getResultArray();

        $formatted = $this->formatRows($rows, $columns);

        return $this->response->setJSON([
            'status'  => 'success',
            'data'    => $formatted,
            'columns' => $columns,
            'total'   => count($formatted),
            'summary' => $summary,
        ]);
    }

    public function export()
    {
        $columns = $this->request->getPost('columns') ?? [];
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kolom untuk diekspor.');
        }

        $filters     = $this->collectFilters();
        $rows        = $this->buildQuery($filters)->get()->getResultArray();
        $formatted   = $this->formatRows($rows, $columns);
        $columnNames = $this->columnNames();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengiriman Email');

        $colLetter = 'A';
        foreach ($columns as $colKey) {
            $sheet->setCellValue($colLetter . '1', $columnNames[$colKey] ?? $colKey);
            $sheet->getStyle($colLetter . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            $colLetter++;
        }

        $rowNum = 2;
        foreach ($formatted as $item) {
            $colLetter = 'A';
            foreach ($columns as $colKey) {
                $sheet->setCellValue($colLetter . $rowNum, $item[$colKey] ?? '-');
                $colLetter++;
            }
            $rowNum++;
        }

        $filename = 'Laporan_Pengiriman_Email_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPdf()
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $columns = $this->request->getPost('columns') ?? [];
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Pilih minimal satu kolom untuk diekspor.');
        }

        $filters = $this->collectFilters();
        $builder = $this->buildQuery($filters);

        $totalRecords = (clone $builder)->countAllResults(false);
        $maxRecords   = 5000;
        if ($totalRecords > $maxRecords) {
            return redirect()->back()->with(
                'error',
                "Jumlah data terlalu besar ({$totalRecords} baris). Maksimum ekspor PDF adalah {$maxRecords} baris. Gunakan filter tanggal atau ekspor ke Excel."
            );
        }

        $rows        = $builder->get()->getResultArray();
        $formatted   = $this->formatRows($rows, $columns);
        $columnNames = $this->columnNames();

        $logokop          = $this->db->table('settingparameters')->where('Name', 'LogoKop')->get()->getRow('Value') ?? '';
        $namaPerpustakaan = $this->db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow('Value') ?? 'Perpustakaan';

        $logoBase64 = '';
        if ($logokop) {
            $logoPath = ROOTPATH . 'public/uploads/branch/' . $logokop;
            if (file_exists($logoPath)) {
                $ext        = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime       = in_array($ext, ['jpg', 'jpeg'], true) ? 'image/jpeg' : 'image/' . $ext;
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 8px; margin: 0; color: #1f2937; }
            .kop { display: flex; align-items: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
            .kop img { max-height: 60px; max-width: 120px; margin-right: 12px; }
            .kop-text { flex: 1; }
            .kop-text h2 { margin: 0; font-size: 13px; }
            .kop-text p { margin: 2px 0; font-size: 8px; color: #555; }
            h3.report-title { text-align: center; font-size: 11px; margin: 6px 0 10px 0; }
            table { width: 100%; border-collapse: collapse; font-size: 7.5px; }
            th { background-color: #3e5c8b; color: #fff; padding: 5px; text-align: left; border: 1px solid #ccc; }
            td { padding: 4px 5px; border: 1px solid #ddd; vertical-align: top; }
            tr:nth-child(even) td { background-color: #f8fafc; }
            .footer { margin-top: 8px; font-size: 7.5px; color: #555; text-align: right; }
        </style></head><body>';

        $html .= '<div class="kop">';
        if ($logoBase64) {
            $html .= '<img src="' . $logoBase64 . '" alt="Logo">';
        }
        $html .= '<div class="kop-text"><h2>' . esc($namaPerpustakaan) . '</h2>'
               . '<p>Laporan Pengiriman Email | Dicetak: ' . date('d-m-Y H:i') . '</p></div></div>';
        $html .= '<h3 class="report-title">LAPORAN RIWAYAT PENGIRIMAN EMAIL</h3>';

        $html .= '<table><thead><tr><th>No</th>';
        foreach ($columns as $col) {
            $html .= '<th>' . esc($columnNames[$col] ?? $col) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $no = 1;
        foreach ($formatted as $item) {
            $html .= '<tr><td>' . $no++ . '</td>';
            foreach ($columns as $col) {
                $html .= '<td>' . esc($item[$col] ?? '-') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '<div class="footer">Total: ' . ($no - 1) . ' pengiriman</div>';
        $html .= '</body></html>';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', count($columns) > 5 ? 'landscape' : 'portrait');
        $dompdf->render();

        $fileName = 'Laporan_Pengiriman_Email_' . date('d-m-Y_His') . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit;
    }

    private function columnNames(): array
    {
        return [
            'sent_at'         => 'Waktu Kirim',
            'recipient_email' => 'Email Tujuan',
            'recipient_name'  => 'Nama Penerima',
            'member_no'       => 'Nomor Anggota',
            'email_type'      => 'Jenis Notifikasi',
            'subject'         => 'Subjek Email',
            'status'          => 'Status Pengiriman',
            'petugas'         => 'Petugas / Pengirim',
            'error_message'   => 'Keterangan / Error',
        ];
    }

    private function collectFilters(): array
    {
        return [
            'start_date' => trim((string) $this->request->getPost('start_date')),
            'end_date'   => trim((string) $this->request->getPost('end_date')),
            'email_type' => trim((string) $this->request->getPost('email_type')),
            'status'     => trim((string) $this->request->getPost('status')),
            'keyword'    => trim((string) $this->request->getPost('keyword')),
        ];
    }

    private function buildQuery(array $filters = [])
    {
        $builder = $this->db->table('email_logs el');
        $builder->select('
            el.id,
            el.sent_at,
            el.recipient_email,
            COALESCE(NULLIF(el.recipient_name, \'\'), m.Fullname, \'-\') as recipient_name,
            COALESCE(NULLIF(el.member_no, \'\'), m.MemberNo, \'-\') as member_no,
            el.email_type,
            el.subject,
            el.status,
            COALESCE(u.first_name, u.username, \'Sistem Otomatis\') as petugas,
            COALESCE(NULLIF(el.error_message, \'\'), \'-\') as error_message
        ', false);
        $builder->join('members m', 'm.ID = el.member_id', 'left');
        $builder->join('users u', 'u.id = el.created_by', 'left');

        $this->applyFilters($builder, $filters);
        $builder->orderBy('el.sent_at', 'DESC');
        $builder->orderBy('el.id', 'DESC');

        return $builder;
    }

    private function applyFilters($builder, array $filters): void
    {
        if (!empty($filters['start_date'])) {
            $builder->where('DATE(el.sent_at) >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('DATE(el.sent_at) <=', $filters['end_date']);
        }
        if (!empty($filters['email_type'])) {
            $builder->where('el.email_type', $filters['email_type']);
        }
        if (!empty($filters['status'])) {
            $builder->where('el.status', $filters['status']);
        }
        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $builder->groupStart()
                ->like('el.recipient_email', $kw)
                ->orLike('el.recipient_name', $kw)
                ->orLike('el.member_no', $kw)
                ->orLike('el.subject', $kw)
                ->orLike('m.Fullname', $kw)
                ->orLike('m.MemberNo', $kw)
                ->groupEnd();
        }
    }

    private function computeSummary(array $filters = []): array
    {
        if (!$this->db->tableExists('email_logs')) {
            return ['total' => 0, 'success' => 0, 'failed' => 0, 'rate' => 0];
        }

        $builder = $this->db->table('email_logs el');
        $builder->select('
            COUNT(*) as total,
            SUM(CASE WHEN el.status = "success" THEN 1 ELSE 0 END) as success_count,
            SUM(CASE WHEN el.status = "failed" THEN 1 ELSE 0 END) as failed_count
        ', false);
        $builder->join('members m', 'm.ID = el.member_id', 'left');
        $this->applyFilters($builder, $filters);

        $row     = $builder->get()->getRowArray();
        $total   = (int) ($row['total'] ?? 0);
        $success = (int) ($row['success_count'] ?? 0);
        $failed  = (int) ($row['failed_count'] ?? 0);
        $rate    = $total > 0 ? round(($success / $total) * 100, 1) : 0;

        return [
            'total'   => $total,
            'success' => $success,
            'failed'  => $failed,
            'rate'    => $rate,
        ];
    }

    private function formatRows(array $rows, array $columns): array
    {
        $result = [];
        foreach ($rows as $row) {
            $row['sent_at']    = !empty($row['sent_at']) ? date('d-m-Y H:i:s', strtotime($row['sent_at'])) : '-';
            $row['email_type'] = EmailLogger::labelFor((string) ($row['email_type'] ?? ''));
            $row['status']     = ($row['status'] ?? '') === 'success' ? 'Berhasil' : 'Gagal';

            if (!empty($row['error_message']) && $row['error_message'] !== '-') {
                $cleanError           = trim(preg_replace('/\s+/', ' ', strip_tags((string) $row['error_message'])));
                $row['error_message'] = mb_substr($cleanError, 0, 160);
            }

            $filtered = [];
            foreach ($columns as $col) {
                $filtered[$col] = $row[$col] ?? '-';
            }
            $result[] = $filtered;
        }

        return $result;
    }
}
