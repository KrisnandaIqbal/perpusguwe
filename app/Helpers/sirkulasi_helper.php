<?php

if (!function_exists('get_sirkulasi_holiday_settings')) {
    /**
     * Mengambil daftar hari libur aktif dari tabel `holidays` dan
     * pengaturan hari Sabtu/Minggu libur dari `settingparameters`.
     * Hasil di-cache secara statis di memori per request.
     *
     * @return array [0 => holidayList (array hash 'Y-m-d' => true), 1 => weekendDaysToIgnore (array int)]
     */
    function get_sirkulasi_holiday_settings(): array {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $weekendDaysToIgnore = [6, 7]; // 6 = Sabtu, 7 = Minggu
        $holidayList = [];

        try {
            $db = \Config\Database::connect();

            // Cek setting weekend dari settingparameters
            if ($db->tableExists('settingparameters')) {
                $settingsRows = $db->table('settingparameters')
                    ->select('Name, Value')
                    ->whereIn('Name', ['IsSaturdayHoliday', 'IsSundayHoliday'])
                    ->get()
                    ->getResult();

                if (!empty($settingsRows)) {
                    $sat = 'True';
                    $sun = 'True';
                    foreach ($settingsRows as $row) {
                        if ($row->Name === 'IsSaturdayHoliday') $sat = $row->Value;
                        if ($row->Name === 'IsSundayHoliday') $sun = $row->Value;
                    }
                    $weekendDaysToIgnore = [];
                    if ($sat !== 'False') $weekendDaysToIgnore[] = 6;
                    if ($sun !== 'False') $weekendDaysToIgnore[] = 7;
                }
            }

            // Cek holidays aktif dari tabel holidays
            if ($db->tableExists('holidays')) {
                $holidayRows = $db->table('holidays')
                    ->select('Dates')
                    ->where('active', 1)
                    ->get()
                    ->getResult();

                foreach ($holidayRows as $h) {
                    $holidayList[date('Y-m-d', strtotime($h->Dates))] = true;
                }
            }
        } catch (\Throwable $e) {
            if (function_exists('log_message')) {
                log_message('warning', 'get_sirkulasi_holiday_settings warning: ' . $e->getMessage());
            }
        }

        $cached = [$holidayList, $weekendDaysToIgnore];
        return $cached;
    }
}

if (!function_exists('hitungJatuhTempoHariKerja')) {
    /**
     * Menghitung tanggal jatuh tempo berdasarkan hari kerja (melewati weekend dan libur nasional).
     * Jam peminjaman dipertahankan. Hitungan dimulai dari H+1 setelah tanggal pinjam.
     *
     * @param string|\DateTimeInterface $tanggalPinjam Format Y-m-d H:i:s atau Y-m-d
     * @param int $jumlahHariKerja Jumlah hari kerja peminjaman
     * @param array|null $customHolidayList Daftar libur kustom (opsional)
     * @param array|null $customWeekends Daftar weekend kustom (opsional)
     * @return string Format Y-m-d H:i:s
     */
    function hitungJatuhTempoHariKerja($tanggalPinjam, $jumlahHariKerja = 10, $customHolidayList = null, $customWeekends = null): string {
        if (empty($tanggalPinjam)) {
            return '';
        }

        $timeStr = '00:00:00';
        if ($tanggalPinjam instanceof \DateTimeInterface) {
            $dt = new \DateTime($tanggalPinjam->format('Y-m-d H:i:s'));
            $timeStr = $tanggalPinjam->format('H:i:s');
        } else {
            $dt = new \DateTime($tanggalPinjam);
            $parts = explode(' ', trim($tanggalPinjam));
            if (isset($parts[1])) {
                $timeStr = $parts[1];
            }
        }

        $remainingDays = (int)$jumlahHariKerja;
        if ($remainingDays <= 0) {
            return $dt->format('Y-m-d') . ' ' . $timeStr;
        }

        if ($customHolidayList !== null && $customWeekends !== null) {
            $holidayList = $customHolidayList;
            $weekendDaysToIgnore = $customWeekends;
        } else {
            [$dbHolidays, $dbWeekends] = get_sirkulasi_holiday_settings();
            $holidayList = $customHolidayList ?? $dbHolidays;
            $weekendDaysToIgnore = $customWeekends ?? $dbWeekends;
        }

        while ($remainingDays > 0) {
            $dt->modify('+1 day');
            $dow = (int)$dt->format('N'); // 1 = Senin, ..., 7 = Minggu
            $ymd = $dt->format('Y-m-d');

            if (in_array($dow, $weekendDaysToIgnore)) {
                continue;
            }

            if (isset($holidayList[$ymd])) {
                continue;
            }

            $remainingDays--;
        }

        return $dt->format('Y-m-d') . ' ' . $timeStr;
    }
}

if (!function_exists('tambahHariKerja')) {
    /**
     * Alias dari hitungJatuhTempoHariKerja
     */
    function tambahHariKerja($tanggal, $jumlah, $customHolidayList = null, $customWeekends = null): string {
        return hitungJatuhTempoHariKerja($tanggal, $jumlah, $customHolidayList, $customWeekends);
    }
}

if (!function_exists('hitungHariKerjaTerlambat')) {
    /**
     * Menghitung jumlah hari kerja keterlambatan pengembalian buku.
     * Jika kembali pada/sebelum tanggal jatuh tempo, mengembalikan 0 (tidak pernah negatif).
     *
     * @param string|\DateTimeInterface $jatuhTempo Tanggal jatuh tempo
     * @param string|\DateTimeInterface|null $tanggalKembali Tanggal kembali (default: now)
     * @param array|null $customHolidayList
     * @param array|null $customWeekends
     * @return int Jumlah hari kerja keterlambatan (>= 0)
     */
    function hitungHariKerjaTerlambat($jatuhTempo, $tanggalKembali = null, $customHolidayList = null, $customWeekends = null): int {
        if (empty($jatuhTempo)) {
            return 0;
        }

        $dueDt = $jatuhTempo instanceof \DateTimeInterface ? clone $jatuhTempo : new \DateTime($jatuhTempo);
        $returnDt = $tanggalKembali 
            ? ($tanggalKembali instanceof \DateTimeInterface ? clone $tanggalKembali : new \DateTime($tanggalKembali)) 
            : new \DateTime();

        $dueYmd = $dueDt->format('Y-m-d');
        $returnYmd = $returnDt->format('Y-m-d');

        // Jika kembali pada atau sebelum tanggal jatuh tempo -> tidak terlambat (0 hari)
        if ($returnYmd <= $dueYmd) {
            return 0;
        }

        if ($customHolidayList !== null && $customWeekends !== null) {
            $holidayList = $customHolidayList;
            $weekendDaysToIgnore = $customWeekends;
        } else {
            [$dbHolidays, $dbWeekends] = get_sirkulasi_holiday_settings();
            $holidayList = $customHolidayList ?? $dbHolidays;
            $weekendDaysToIgnore = $customWeekends ?? $dbWeekends;
        }

        // Hitung hari kerja dari H+1 setelah DueDate sampai ReturnDate
        $curr = clone $dueDt;
        $lateCount = 0;

        while (true) {
            $curr->modify('+1 day');
            $ymd = $curr->format('Y-m-d');
            if ($ymd > $returnYmd) {
                break;
            }

            $dow = (int)$curr->format('N');
            if (in_array($dow, $weekendDaysToIgnore)) {
                continue;
            }
            if (isset($holidayList[$ymd])) {
                continue;
            }

            $lateCount++;
        }

        return $lateCount;
    }
}

if (!function_exists('hitungSisaHariKerjaPinjam')) {
    /**
     * Menghitung sisa hari kerja pinjaman sebelum jatuh tempo untuk tampilan daftar peminjaman.
     * Mengembalikan informasi sisa hari atau hari keterlambatan.
     *
     * @param string|\DateTimeInterface $jatuhTempo
     * @param string|\DateTimeInterface|null $tanggalSekarang
     * @return array ['is_overdue' => bool, 'days' => int, 'text' => string, 'badge_class' => string]
     */
    function hitungSisaHariKerjaPinjam($jatuhTempo, $tanggalSekarang = null): array {
        if (empty($jatuhTempo)) {
            return ['is_overdue' => false, 'days' => 0, 'text' => '-', 'badge_class' => 'secondary'];
        }

        $dueDt = $jatuhTempo instanceof \DateTimeInterface ? clone $jatuhTempo : new \DateTime($jatuhTempo);
        $nowDt = $tanggalSekarang 
            ? ($tanggalSekarang instanceof \DateTimeInterface ? clone $tanggalSekarang : new \DateTime($tanggalSekarang)) 
            : new \DateTime();

        $dueYmd = $dueDt->format('Y-m-d');
        $nowYmd = $nowDt->format('Y-m-d');

        if ($nowYmd > $dueYmd) {
            $lateDays = hitungHariKerjaTerlambat($jatuhTempo, $nowDt);
            return [
                'is_overdue' => true,
                'days' => $lateDays,
                'text' => '+' . $lateDays . ' hari terlambat',
                'badge_class' => 'danger'
            ];
        }

        if ($nowYmd === $dueYmd) {
            return [
                'is_overdue' => false,
                'days' => 0,
                'text' => 'Jatuh Tempo Hari Ini',
                'badge_class' => 'warning'
            ];
        }

        // $nowYmd < $dueYmd: Hitung sisa hari kerja dari H+1 sekarang sampai dueYmd
        [$holidayList, $weekendDaysToIgnore] = get_sirkulasi_holiday_settings();
        $curr = clone $nowDt;
        $remaining = 0;

        while (true) {
            $curr->modify('+1 day');
            $ymd = $curr->format('Y-m-d');
            if ($ymd > $dueYmd) {
                break;
            }

            $dow = (int)$curr->format('N');
            if (in_array($dow, $weekendDaysToIgnore)) {
                continue;
            }
            if (isset($holidayList[$ymd])) {
                continue;
            }

            $remaining++;
        }

        $badgeClass = ($remaining <= 3) ? 'warning' : 'info';
        return [
            'is_overdue' => false,
            'days' => $remaining,
            'text' => 'Sisa ' . $remaining . ' hari',
            'badge_class' => $badgeClass
        ];
    }
}

if (!function_exists('get_pad_number')) {
    function get_pad_number($counter, $prefix = 'TRX-', $zero_length = 4)
    {        
        $doc_number = strtoupper($prefix).str_pad($counter , $zero_length , "0" , STR_PAD_LEFT);

        return $doc_number;
    }
}

if (!function_exists('get_cart_return')) {
    function get_cart_return()
    {
        $cart = new \App\Libraries\Cart();
        $contents = array();
        foreach($cart->contents() as $row){
            if(strtoupper($row['name']) == 'RETURN') {
                $contents[] = $row;
            }
        }

        return json_decode(json_encode($contents), FALSE);
    }
}

if (!function_exists('get_cart_loan')) {
    function get_cart_loan()
    {
        $cart = new \App\Libraries\Cart();
        $contents = array();
        foreach($cart->contents() as $row){
            if(strtoupper($row['name']) == 'LOAN') {
                $contents[] = $row;
            }
        }

        return json_decode(json_encode($contents), FALSE);
    }
}

if (!function_exists('cek_suspend_anggota')) {
    /**
     * Memeriksa apakah anggota sedang dalam masa suspend peminjaman.
     * Aturan: terlambat N hari menghasilkan suspend N hari, dihitung dari tanggal pengembalian (CreateDate).
     *
     * @param int|string $memberId ID anggota
     * @param string|\DateTimeInterface|null $checkDate Tanggal pengecekan (default: now)
     * @return array ['is_suspended' => bool, 'message' => string, 'suspend_until' => string|null, 'suspend_days' => int]
     */
    function cek_suspend_anggota($memberId, $checkDate = null): array {
        if (empty($memberId)) {
            return ['is_suspended' => false, 'message' => '', 'suspend_until' => null, 'suspend_days' => 0];
        }

        $nowDt = $checkDate
            ? ($checkDate instanceof \DateTimeInterface ? clone $checkDate : new \DateTime($checkDate))
            : new \DateTime();

        $db = \Config\Database::connect();

        // 1. Cek catatan pelanggaran aktif dengan JumlahSuspend > 0
        $hasExpiredViolation = false;
        if ($db->tableExists('pelanggaran')) {
            $violations = $db->table('pelanggaran')
                ->where('Member_id', $memberId)
                ->where('JumlahSuspend >', 0)
                ->where('active', 1)
                ->orderBy('CreateDate', 'DESC')
                ->get()
                ->getResult();

            $maxSuspendUntil = null;
            $maxSuspendDays = 0;

            foreach ($violations as $v) {
                $createDt = new \DateTime($v->CreateDate);
                $suspendDays = (int)$v->JumlahSuspend;
                // Suspend N hari dihitung dari tanggal pengembalian
                $untilDt = clone $createDt;
                $untilDt->modify("+{$suspendDays} days");
                $untilDt->setTime(23, 59, 59);

                if ($nowDt <= $untilDt) {
                    if ($maxSuspendUntil === null || $untilDt > $maxSuspendUntil) {
                        $maxSuspendUntil = $untilDt;
                        $maxSuspendDays = $suspendDays;
                    }
                } else {
                    $hasExpiredViolation = true;
                }
            }

            if ($maxSuspendUntil !== null) {
                $formattedUntil = $maxSuspendUntil->format('d/m/Y');
                return [
                    'is_suspended'  => true,
                    'message'       => "Anggota sedang disuspend hingga {$formattedUntil} (sanksi keterlambatan {$maxSuspendDays} hari).",
                    'suspend_until' => $maxSuspendUntil->format('Y-m-d H:i:s'),
                    'suspend_days'  => $maxSuspendDays,
                ];
            }

            // Jika ada pelanggaran yang masa berlakunya telah habis, nonaktifkan otomatis (active = 0)
            if ($hasExpiredViolation) {
                foreach ($violations as $v) {
                    $createDt = new \DateTime($v->CreateDate);
                    $suspendDays = (int)$v->JumlahSuspend;
                    $untilDt = clone $createDt;
                    $untilDt->modify("+{$suspendDays} days");
                    $untilDt->setTime(23, 59, 59);

                    if ($nowDt > $untilDt) {
                        $db->table('pelanggaran')
                            ->where('ID', $v->ID)
                            ->update([
                                'active'     => 0,
                                'UpdateDate' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
            }
        }

        // 2. Cek status anggota di tabel members jika disuspend manual oleh admin (StatusAnggota_id = 5 adalah Suspend)
        if ($db->tableExists('members')) {
            $member = $db->table('members')
                ->select('ID, MemberNo, Fullname, StatusAnggota_id')
                ->where('ID', $memberId)
                ->get()
                ->getRow();

            if ($member && (int)$member->StatusAnggota_id === 5) {
                // Jika statusnya 5 tetapi karena pelanggaran sanksi yang sekarang sudah habis/kadaluarsa,
                // kembalikan status anggota ke Aktif (3) secara otomatis.
                if ($hasExpiredViolation) {
                    $db->table('members')->where('ID', $memberId)->update(['StatusAnggota_id' => 3]);
                    return [
                        'is_suspended'  => false,
                        'message'       => '',
                        'suspend_until' => null,
                        'suspend_days'  => 0,
                    ];
                }

                return [
                    'is_suspended'  => true,
                    'message'       => 'Anggota sedang dalam status Suspend oleh admin perpustakaan.',
                    'suspend_until' => null,
                    'suspend_days'  => 0,
                ];
            }
        }

        return [
            'is_suspended'  => false,
            'message'       => '',
            'suspend_until' => null,
            'suspend_days'  => 0,
        ];
    }
}

if (!function_exists('sinkronkan_status_suspend_anggota')) {
    /**
     * Menyinkronkan status keanggotaan dengan data suspend pelanggaran sirkulasi.
     * Jika anggota diaktifkan kembali atau diubah statusnya ke selain Suspend (StatusAnggota_id != 5),
     * maka semua catatan suspend aktif di tabel pelanggaran dinonaktifkan (active = 0).
     *
     * @param int|string $memberId
     * @param int|string $newStatusId
     * @return int Jumlah catatan pelanggaran suspend yang dinonaktifkan
     */
    function sinkronkan_status_suspend_anggota($memberId, $newStatusId): int {
        if (empty($memberId)) {
            return 0;
        }

        $newStatusId = (int) $newStatusId;
        $db = \Config\Database::connect();

        // 5 adalah ID status 'Suspend'
        // Jika status baru BUKAN Suspend (misalnya 3 = Aktif, 4 = Nonaktif, 6 = Bebas Pustaka, dll)
        if ($newStatusId !== 5 && $db->tableExists('pelanggaran')) {
            $updated = $db->table('pelanggaran')
                ->where('Member_id', $memberId)
                ->where('JumlahSuspend >', 0)
                ->where('active', 1)
                ->update([
                    'active'         => 0,
                    'UpdateDate'     => date('Y-m-d H:i:s'),
                    'UpdateBy'       => (function_exists('login_id') && login_id()) ? login_id() : ((function_exists('user_id') && user_id()) ? user_id() : 1),
                    'UpdateTerminal' => (function_exists('service') && service('request')) ? service('request')->getIPAddress() : '127.0.0.1',
                ]);

            return $updated ? (int) $db->affectedRows() : 0;
        }

        return 0;
    }
}

