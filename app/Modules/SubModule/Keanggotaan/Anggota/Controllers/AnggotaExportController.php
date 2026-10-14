<?php

namespace Anggota\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * AnggotaExportController
 *
 * Menangani:
 * - Ekspor semua data anggota ke format XLSX (batching 500 baris, teks identitas, styling).
 * - Ekspor data anggota terpilih ke format PDF (maks 50 anggota, 1 anggota per halaman A4 portrait).
 */
class AnggotaExportController extends \Base\Controllers\BaseController
{
    use AnggotaBase;

    public function __construct()
    {
        $this->initAnggotaBase();
    }

    /**
     * Sanitasi teks untuk mencegah Formula Injection di Excel (=, +, -, @)
     */
    private function sanitizeSpreadsheetText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }
        $firstChar = substr($text, 0, 1);
        if (in_array($firstChar, ['=', '+', '-', '@'], true)) {
            return ' ' . $text;
        }
        return $text;
    }

    /**
     * Ekspor seluruh anggota ke file XLSX (Spreadsheet)
     * FR-10, FR-11, FR-12, FR-13, FR-14
     */
    public function exportXlsx()
    {
        // Pengecekan hak akses sesuai izin modul Anggota
        $userId = session()->get('logged_in') ?? login_id();
        if (!$userId) {
            return redirect()->to('/login');
        }

        if (!is_allowed('anggota', $userId) && !is_member('admin')) {
            session()->setFlashdata('swal_permission', [
                'icon'  => 'error',
                'title' => 'Akses Ditolak',
                'text'  => lang('App.permission.not.have'),
            ]);
            return redirect()->to('/dashboard/index');
        }

        $db = db_connect();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Anggota');

        // Header Kolom
        $headers = [
            'A1' => 'No',
            'B1' => 'No. Anggota / Barcode',
            'C1' => 'Status Anggota',
            'D1' => 'Jenis Anggota',
            'E1' => 'Tgl. Registrasi',
            'F1' => 'Masa Berlaku',
            'G1' => 'Nama Lengkap',
            'H1' => 'Jenis Identitas',
            'I1' => 'Nomor Identitas (NIK)',
            'J1' => 'Tempat Lahir',
            'K1' => 'Tanggal Lahir',
            'L1' => 'Jenis Kelamin',
            'M1' => 'Status Perkawinan',
            'N1' => 'Agama',
            'O1' => 'Pekerjaan',
            'P1' => 'Nama Institusi',
            'Q1' => 'Email',
            'R1' => 'Nomor WhatsApp / HP',
            'S1' => 'Alamat KTP',
            'T1' => 'RT / RW KTP',
            'U1' => 'Kelurahan KTP',
            'V1' => 'Kecamatan KTP',
            'W1' => 'Kota KTP',
            'X1' => 'Provinsi KTP',
            'Y1' => 'Alamat Domisili',
            'Z1' => 'RT / RW Domisili',
            'AA1' => 'Kelurahan Domisili',
            'AB1' => 'Kecamatan Domisili',
            'AC1' => 'Kota Domisili',
            'AD1' => 'Provinsi Domisili',
            'AE1' => 'Tanggal Dibuat',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style Header: Tebal, latar belakang abu-abu terang, teks tengah
        $headerRange = 'A1:AE1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE9ECEF');
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Bekukan baris header (Freeze pane A2) - FR-13
        $sheet->freezePane('A2');

        // Query bertahap (batch 500 baris) - FR-14
        $batchSize = 500;
        $offset = 0;
        $rowNum = 2;
        $nomorUrut = 1;

        while (true) {
            $builder = $db->table('members m')
                ->select([
                    'm.ID',
                    'm.MemberNo',
                    'm.Fullname',
                    'm.IdentityNo',
                    'm.PlaceOfBirth',
                    'm.DateOfBirth',
                    'm.Address',
                    'm.AddressNow',
                    'm.Phone',
                    'm.Email',
                    'm.InstitutionName',
                    'm.RT',
                    'm.RW',
                    'm.RTNow',
                    'm.RWNow',
                    'm.Kelurahan',
                    'm.Kecamatan',
                    'm.City',
                    'm.Province',
                    'm.KelurahanNow',
                    'm.KecamatanNow',
                    'm.CityNow',
                    'm.ProvinceNow',
                    'm.RegisterDate',
                    'm.EndDate',
                    'm.CreateDate',
                    'sa.Nama as StatusAnggota',
                    'ja.jenisanggota as JenisAnggota',
                    'mji.Nama as JenisIdentitas',
                    'jk.Name as JenisKelamin',
                    'msp.Nama as StatusPerkawinan',
                    'ag.Name as Agama',
                    'mp.Pekerjaan as Pekerjaan',
                ])
                ->join('status_anggota sa', 'sa.id = m.StatusAnggota_id', 'left')
                ->join('jenis_anggota ja', 'ja.id = m.JenisAnggota_id', 'left')
                ->join('master_jenis_identitas mji', 'mji.id = m.IdentityType_id', 'left')
                ->join('jenis_kelamin jk', 'jk.ID = m.Sex_id', 'left')
                ->join('master_status_perkawinan msp', 'msp.id = m.MaritalStatus_id', 'left')
                ->join('agama ag', 'ag.ID = m.Agama_id', 'left')
                ->join('master_pekerjaan mp', 'mp.id = m.Job_id', 'left')
                ->where('m.IsKeranjang', 0)
                ->orderBy('m.ID', 'ASC')
                ->limit($batchSize, $offset);

            $rows = $builder->get()->getResult();

            if (empty($rows)) {
                break;
            }

            foreach ($rows as $item) {
                // Kolom nomor urut
                $sheet->setCellValue('A' . $rowNum, $nomorUrut);

                // Kolom identitas teks murni (FR-12: NIK, nomor anggota, nomor HP)
                $sheet->getCell('B' . $rowNum)->setValueExplicit($this->sanitizeSpreadsheetText($item->MemberNo ?? '-'), DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowNum, $item->StatusAnggota ?? '-');
                $sheet->setCellValue('D' . $rowNum, $item->JenisAnggota ?? '-');

                // Tanggal Registrasi & Berakhir
                $regDate = !empty($item->RegisterDate) ? substr($item->RegisterDate, 0, 10) : '-';
                $endDate = !empty($item->EndDate) ? substr($item->EndDate, 0, 10) : '-';
                $sheet->setCellValue('E' . $rowNum, $regDate);
                $sheet->setCellValue('F' . $rowNum, $endDate);

                $sheet->setCellValue('G' . $rowNum, $this->sanitizeSpreadsheetText($item->Fullname ?? '-'));
                $sheet->setCellValue('H' . $rowNum, $item->JenisIdentitas ?? '-');

                // NIK explicit string agar leading zero tidak hilang
                $sheet->getCell('I' . $rowNum)->setValueExplicit($this->sanitizeSpreadsheetText($item->IdentityNo ?? '-'), DataType::TYPE_STRING);

                $sheet->setCellValue('J' . $rowNum, $this->sanitizeSpreadsheetText($item->PlaceOfBirth ?? '-'));
                $tglLahir = !empty($item->DateOfBirth) ? substr($item->DateOfBirth, 0, 10) : '-';
                $sheet->setCellValue('K' . $rowNum, $tglLahir);

                $sheet->setCellValue('L' . $rowNum, $item->JenisKelamin ?? '-');
                $sheet->setCellValue('M' . $rowNum, $item->StatusPerkawinan ?? '-');
                $sheet->setCellValue('N' . $rowNum, $item->Agama ?? '-');
                $sheet->setCellValue('O' . $rowNum, $item->Pekerjaan ?? '-');
                $sheet->setCellValue('P' . $rowNum, $this->sanitizeSpreadsheetText($item->InstitutionName ?? '-'));
                $sheet->setCellValue('Q' . $rowNum, $this->sanitizeSpreadsheetText($item->Email ?? '-'));

                // Phone explicit string agar leading zero (08...) tidak hilang
                $sheet->getCell('R' . $rowNum)->setValueExplicit($this->sanitizeSpreadsheetText($item->Phone ?? '-'), DataType::TYPE_STRING);

                $sheet->setCellValue('S' . $rowNum, $this->sanitizeSpreadsheetText($item->Address ?? '-'));
                $rtRw = (!empty($item->RT) || !empty($item->RW)) ? ($item->RT ?? '') . ' / ' . ($item->RW ?? '') : '-';
                $sheet->setCellValue('T' . $rowNum, $rtRw);
                $sheet->setCellValue('U' . $rowNum, $this->sanitizeSpreadsheetText($item->Kelurahan ?? '-'));
                $sheet->setCellValue('V' . $rowNum, $this->sanitizeSpreadsheetText($item->Kecamatan ?? '-'));
                $sheet->setCellValue('W' . $rowNum, $this->sanitizeSpreadsheetText($item->City ?? '-'));
                $sheet->setCellValue('X' . $rowNum, $this->sanitizeSpreadsheetText($item->Province ?? '-'));

                $sheet->setCellValue('Y' . $rowNum, $this->sanitizeSpreadsheetText($item->AddressNow ?? '-'));
                $rtRwNow = (!empty($item->RTNow) || !empty($item->RWNow)) ? ($item->RTNow ?? '') . ' / ' . ($item->RWNow ?? '') : '-';
                $sheet->setCellValue('Z' . $rowNum, $rtRwNow);
                $sheet->setCellValue('AA' . $rowNum, $this->sanitizeSpreadsheetText($item->KelurahanNow ?? '-'));
                $sheet->setCellValue('AB' . $rowNum, $this->sanitizeSpreadsheetText($item->KecamatanNow ?? '-'));
                $sheet->setCellValue('AC' . $rowNum, $this->sanitizeSpreadsheetText($item->CityNow ?? '-'));
                $sheet->setCellValue('AD' . $rowNum, $this->sanitizeSpreadsheetText($item->ProvinceNow ?? '-'));

                $createDate = !empty($item->CreateDate) ? substr($item->CreateDate, 0, 10) : '-';
                $sheet->setCellValue('AE' . $rowNum, $createDate);

                $rowNum++;
                $nomorUrut++;
            }

            $offset += $batchSize;
        }

        // Aktifkan auto-filter (FR-13)
        $lastRow = max($rowNum - 1, 1);
        $sheet->setAutoFilter('A1:AE' . $lastRow);

        // Auto-width kolom (FR-13)
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['AA', 'AB', 'AC', 'AD', 'AE'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download langsung tanpa simpan file permanen (FR-12, FR-16)
        $filename = 'anggota_' . date('Y-m-d') . '.xlsx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Ekspor atau Pratinjau data anggota terpilih ke format PDF (A4 Portrait, 1 anggota per halaman)
     * Format diselaraskan dengan data pendaftaran online Kemnaker RI.
     */
    public function exportPdf()
    {
        log_message('error', '=== EXPORT_PDF CALLED === POST: ' . json_encode($this->request->getPost()) . ' | GET: ' . json_encode($this->request->getGet()));
        
        // Pengecekan hak akses sesuai izin modul Anggota
        $userId = session()->get('logged_in') ?? login_id();
        if (!$userId) {
            log_message('error', '=== EXPORT_PDF: NOT LOGGED IN ===');
            return redirect()->to('/login');
        }

        $isPreview = (bool) ($this->request->getPost('preview') ?? $this->request->getGet('preview') ?? false);
        $memberIds = $this->request->getPost('member_ids') ?? $this->request->getPost('member_ids[]') ?? $this->request->getGet('member_ids') ?? $this->request->getGet('member_ids[]');

        log_message('error', '=== EXPORT_PDF: isPreview=' . ($isPreview ? '1' : '0') . ' memberIds=' . json_encode($memberIds));

        if (is_string($memberIds)) {
            if (strpos($memberIds, ',') !== false) {
                $memberIds = explode(',', $memberIds);
            } else {
                $memberIds = [$memberIds];
            }
        }

        if (empty($memberIds) || !is_array($memberIds)) {
            if ($isPreview || $this->request->isAJAX()) {
                if (ob_get_length()) ob_end_clean();
                header('Content-Type: text/html; charset=UTF-8', true, 400);
                echo '<div class="alert alert-warning m-4 text-center">Silakan pilih minimal 1 anggota untuk melihat pratinjau.</div>';
                exit;
            }
            return redirect()->back()->with('swal_icon', 'warning')
                ->with('swal_title', 'Perhatian')
                ->with('swal_text', 'Silakan pilih minimal satu anggota.');
        }

        // Filter hanya ID angka valid
        $sanitizedIds = [];
        foreach ($memberIds as $id) {
            if (is_numeric($id) && (int)$id > 0) {
                $sanitizedIds[] = (int)$id;
            }
        }

        if (empty($sanitizedIds)) {
            if ($isPreview || $this->request->isAJAX()) {
                if (ob_get_length()) ob_end_clean();
                header('Content-Type: text/html; charset=UTF-8', true, 400);
                echo '<div class="alert alert-danger m-4 text-center">Daftar ID anggota tidak valid.</div>';
                exit;
            }
            return redirect()->back()->with('swal_icon', 'error')
                ->with('swal_title', 'Error')
                ->with('swal_text', 'Daftar ID anggota tidak valid.');
        }

        // Batas maksimal 50 anggota
        if (count($sanitizedIds) > 50) {
            return redirect()->back()->with('swal_icon', 'warning')
                ->with('swal_title', 'Batas Maksimal Ekspor')
                ->with('swal_text', 'Maksimal 50 anggota per dokumen PDF.');
        }

        $db = db_connect();

        // Ambil data anggota diselaraskan dengan pendaftaran online Kemnaker
        $members = $db->table('members m')
            ->select([
                'm.ID',
                'm.MemberNo',
                'm.Fullname',
                'm.IdentityNo',
                'm.Email',
                'm.Phone',
                'm.AddressNow',
                'm.RegisterDate',
                'm.EndDate',
                'm.PhotoUrl',
                'm.JenisAnggota_id',
                'sa.Nama as StatusAnggota',
                'ja.jenisanggota as JenisAnggota',
                'mji.Nama as JenisIdentitas',
                'mu.nama_unit as UnitKerja',
            ])
            ->join('status_anggota sa', 'sa.id = m.StatusAnggota_id', 'left')
            ->join('jenis_anggota ja', 'ja.id = m.JenisAnggota_id', 'left')
            ->join('master_jenis_identitas mji', 'mji.id = m.IdentityType_id', 'left')
            ->join('master_unit mu', 'mu.id = m.UnitKerja_id', 'left')
            ->whereIn('m.ID', $sanitizedIds)
            ->orderBy('m.ID', 'ASC')
            ->get()->getResult();

        if (empty($members)) {
            return redirect()->back()->with('swal_icon', 'warning')
                ->with('swal_title', 'Data Tidak Ditemukan')
                ->with('swal_text', 'Anggota yang dipilih tidak ditemukan di database.');
        }

        // Pengaturan perpustakaan untuk kop
        $namaPerpustakaan = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? 'PERPUSTAKAAN KEMENTERIAN KETENAGAKERJAAN';
        $alamatPerpustakaan = $db->table('settingparameters')->where('Name', 'AlamatPerpustakaan')->get()->getRow()->Value ?? '';

        $membersData = [];
        foreach ($members as $m) {
            // Path foto anggota (fallback jika file tidak ada)
            $photoPath = null;
            if (!empty($m->PhotoUrl)) {
                $possiblePaths = [
                    FCPATH . 'uploads/anggota/' . $m->PhotoUrl,
                    ROOTPATH . 'public/uploads/anggota/' . $m->PhotoUrl,
                ];
                foreach ($possiblePaths as $path) {
                    if (file_exists($path) && is_file($path)) {
                        $photoPath = $path;
                        break;
                    }
                }
            }

            $membersData[] = [
                'member'    => $m,
                'photoPath' => $photoPath,
            ];
        }

        $petugasNama = session()->get('name') ?? session()->get('username') ?? 'Petugas Perpustakaan';

        $dataView = [
            'namaPerpustakaan'   => $namaPerpustakaan,
            'alamatPerpustakaan' => $alamatPerpustakaan,
            'membersData'        => $membersData,
            'petugasNama'        => $petugasNama,
            'tglCetak'           => date('d/m/Y'),
        ];

        // Mode Pratinjau: Tampilkan dokumen HTML langsung di modal/tab (instan, tajam, bebas error plugin PDF & tanpa download)
        if ($isPreview) {
            if (ob_get_length()) {
                ob_end_clean();
            }
            header('Content-Type: text/html; charset=UTF-8');
            echo view('Anggota\Views\export_pdf', $dataView);
            exit;
        }

        // Mode Unduh Dokumen: Buat dan download file .pdf melalui Dompdf
        $html = view('Anggota\Views\export_pdf', $dataView);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');

        // Nonaktifkan E_DEPRECATED agar output stream tidak tercemar notice PHP 8.4
        $prevErr = error_reporting();
        error_reporting($prevErr & ~E_DEPRECATED);
        $dompdf->render();
        error_reporting($prevErr);

        // Nama file PDF
        if (count($members) === 1) {
            $memberNo = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $members[0]->MemberNo ?? '1');
            $filename = 'data-anggota_' . $memberNo . '.pdf';
        } else {
            $filename = 'data-anggota_' . date('Y-m-d') . '.pdf';
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }
}
