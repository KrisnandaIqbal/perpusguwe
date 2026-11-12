<?php

// Fungsi bantu untuk halaman depan (beranda). Dimuat lewat helper('landing') di Home::index().
// Isi view ada di app/Modules/Home/Views/ (index.php + folder landing/); di sini hanya data tetap dan logika kecil.

if (! function_exists('landing_setting')) {
    /** Nilai pengaturan perpustakaan (tabel settingparameters); $default dipakai bila kosong. */
    function landing_setting(string $name, string $default = ''): string
    {
        $row = db_connect()->table('settingparameters')->where('Name', $name)->get()->getRow();

        return ($row && $row->Value !== '') ? $row->Value : $default;
    }
}

if (! function_exists('landing_asset')) {
    /** URL berkas di public/assets/home/ dengan ?v=waktu-ubah, jadi peramban memuat ulang otomatis saat berkas diganti. */
    function landing_asset(string $path): string
    {
        $berkas = FCPATH . 'assets/home/' . $path;

        return base_url('assets/home/' . $path) . (is_file($berkas) ? '?v=' . filemtime($berkas) : '');
    }
}

if (! function_exists('landing_icon')) {
    /** Ikon garis (SVG inline). $name = kunci di landing_icon_paths(). */
    function landing_icon(string $name, string $class = ''): string
    {
        return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            . landing_icon_paths()[$name] . '</svg>';
    }
}

if (! function_exists('landing_icon_filled')) {
    /** Ikon media sosial berisi penuh (logo merek tidak cocok digambar dengan garis). */
    function landing_icon_filled(string $name): string
    {
        return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="' . landing_icon_filled_paths()[$name] . '"/></svg>';
    }
}

if (! function_exists('landing_icon_paths')) {
    function landing_icon_paths(): array
    {
        return [
            'search'   => '<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/>',
            'up-right' => '<path d="M7 17 17 7M9 7h8v8"/>',
            'right'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'close'    => '<path d="m6 6 12 12M18 6 6 18"/>',
            'chevron'  => '<path d="m6 9 6 6 6-6"/>',
            'home'     => '<path d="M4 10.5 12 4l8 6.5V19a1.5 1.5 0 0 1-1.5 1.5H15v-6H9v6H5.5A1.5 1.5 0 0 1 4 19Z"/>',
            'grid'     => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.6"/>',
            'tamu'     => '<path d="M6 3.5h11a1.5 1.5 0 0 1 1.5 1.5v14a1.5 1.5 0 0 1-1.5 1.5H6Z"/><path d="M6 3.5v17M9.5 8h5.5M9.5 11.5h5.5M9.5 15h3"/>',
            'jam'      => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
            'perisai'  => '<path d="M12 3.5 5 6v5.5c0 4.2 2.8 7.2 7 9 4.2-1.8 7-4.8 7-9V6Z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
            'surat'    => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 6 8-6"/>',
            'lokasi'   => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.4"/>',
            'registrasi' => '<circle cx="9.5" cy="8" r="3.5"/><path d="M3.5 19.5c.6-3.3 3-5.3 6-5.3s5.4 2 6 5.3M18.5 8v6M15.5 11h6"/>',
            'pinjam'   => '<path d="M4 8V5.5A1.5 1.5 0 0 1 5.5 4H8M16 4h2.5A1.5 1.5 0 0 1 20 5.5V8M20 16v2.5a1.5 1.5 0 0 1-1.5 1.5H16M8 20H5.5A1.5 1.5 0 0 1 4 18.5V16"/><path d="M8 8.5v7M11 8.5v7M13.5 8.5v7M16 8.5v7M3 12h18"/>',
            'kembali'  => '<path d="M3.5 13.5 6 6h12l2.5 7.5V18a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2Z"/><path d="M3.5 13.5H8l1.5 2.5h5l1.5-2.5h4.5M12 3v7M9.5 7.5 12 10l2.5-2.5"/>',
            'baca'     => '<path d="M3 10.5h18M5 10.5V20M19 10.5V20M8 10.5V7.5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3"/><path d="M9.5 4h5"/>',
            'isbn'     => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M7 8v6M9.5 8v6M12 8v6M15.5 8v6M17.5 8v6M7 16.5h10.5"/>',
            'pin'      => '<path d="M12 21s-6.5-5.6-6.5-11A6.5 6.5 0 0 1 18.5 10c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/>',
            'phone'    => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
            'mail'     => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4.5 7 7.5 6 7.5-6"/>',
            'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
            'external' => '<path d="M7 17 17 7M9 7h8v8"/>',
            'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.8" cy="7.2" r=".6"/>',
            'facebook' => '<path d="M14 8.5h2.5V5H14c-2.2 0-3.5 1.5-3.5 3.6V11H8v3.5h2.5V20H14v-5.5h2.4L17 11h-3V9c0-.3.3-.5.6-.5Z"/>',
            'x'        => '<path d="m5 5 14 14M19 5 5 19"/>',
            'youtube'  => '<rect x="3.5" y="6.5" width="17" height="11" rx="3"/><path d="m10.5 9.5 4 2.5-4 2.5Z"/>',
            'sirkulasi' => '<path d="M4.5 9.5A8 8 0 0 1 19 7.5M19.5 14.5A8 8 0 0 1 5 16.5"/><path d="M19.5 3.5v4.5H15M4.5 20.5V16H9"/>',
            'browse'    => '<circle cx="12" cy="12" r="8.5"/><path d="m15.5 8.5-2 5-5 2 2-5Z"/>',
            'statistik' => '<path d="M5 20v-6M11 20V5M17 20v-9M3 20h18"/>',
            'katalog'   => '<path d="M12 6.5C10 5 7 4.5 4 5v13c3-.5 6 0 8 1.5 2-1.5 5-2 8-1.5V5c-3-.5-6 0-8 1.5ZM12 6.5v13"/>',
            'anggota'   => '<circle cx="9" cy="8.5" r="3.2"/><path d="M3 19c.5-3 2.8-4.8 6-4.8s5.5 1.8 6 4.8M16 5.6a3.2 3.2 0 0 1 0 5.8M18 14.6c1.8.6 3 2.2 3.3 4.4"/>',
        ];
    }
}

if (! function_exists('landing_icon_filled_paths')) {
    function landing_icon_filled_paths(): array
    {
        return [
            'instagram' => 'M12 7.3a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm6-7.9a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0ZM21.1 8c-.1-1.5-.4-2.8-1.5-3.9S17.5 2.7 16 2.6c-1.5-.1-6.1-.1-7.7 0-1.5.1-2.8.4-3.9 1.5S2.9 6.5 2.8 8c-.1 1.6-.1 6.2 0 7.8.1 1.5.4 2.8 1.5 3.9s2.4 1.4 3.9 1.5c1.6.1 6.1.1 7.7 0 1.5-.1 2.8-.4 3.9-1.5s1.4-2.4 1.5-3.9c.1-1.6.1-6.2 0-7.8Zm-2 9.5a3.1 3.1 0 0 1-1.8 1.8c-1.2.5-4.2.4-5.5.4s-4.3.1-5.5-.4a3.1 3.1 0 0 1-1.8-1.8c-.5-1.2-.4-4.2-.4-5.5s-.1-4.3.4-5.5a3.1 3.1 0 0 1 1.8-1.8c1.2-.5 4.2-.4 5.5-.4s4.3-.1 5.5.4a3.1 3.1 0 0 1 1.8 1.8c.5 1.2.4 4.2.4 5.5s.1 4.3-.4 5.5Z',
            'facebook'  => 'M13.4 21v-7.6h2.6l.4-3h-3V8.5c0-.9.2-1.5 1.5-1.5h1.6V4.3c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.2H7.7v3h2.6V21h3.1Z',
            'x'         => 'M17.2 3.5h2.9l-6.3 7.2 7.4 9.8h-5.8l-4.5-5.9-5.2 5.9H2.8l6.7-7.7-7.1-9.3h5.9l4.1 5.4 4.8-5.4Zm-1 15.3h1.6L7.5 5.1H5.8l10.4 13.7Z',
            'youtube'   => 'M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8ZM10 15V9l5.2 3-5.2 3Z',
        ];
    }
}

if (! function_exists('landing_services')) {
    /** Semua layanan yang aktif, dengan kunci id. Layanan keanggotaan online hanya muncul bila diaktifkan di .env. */
    function landing_services(): array
    {
        $keanggotaanOnline = env('Is_keanggotaan_online') == 1;

        $services = [
            'registrasi' => ['title' => 'Pendaftaran Anggota', 'short' => 'Daftar anggota secara mandiri', 'url' => base_url('home/pendaftaran_online'), 'keywords' => 'daftar registrasi anggota member kartu', 'enabled' => $keanggotaanOnline],
            'pinjam'     => ['title' => 'Peminjaman', 'short' => 'Pindai barcode, langsung pinjam', 'url' => base_url('peminjaman-mandiri'), 'keywords' => 'pinjam peminjaman sirkulasi scan barcode', 'enabled' => true],
            'kembali'    => ['title' => 'Pengembalian', 'short' => 'Kembalikan buku lewat drop-box', 'url' => base_url('pengembalian-mandiri'), 'keywords' => 'kembali pengembalian sirkulasi drop box', 'enabled' => true],
            'baca'       => ['title' => 'Baca di Tempat', 'short' => 'Reservasi meja di ruang baca', 'url' => base_url('baca-ditempat'), 'keywords' => 'baca tempat ruang meja reservasi', 'enabled' => true],
            'isbn'       => ['title' => 'Layanan ISBN', 'short' => 'Ajukan ISBN publikasi Anda', 'url' => base_url('home/layanan-isbn'), 'keywords' => 'isbn perpusnas terbit publikasi penerbit', 'enabled' => true],
            'tamu'       => ['title' => 'Buku Tamu', 'short' => 'Catat kunjungan Anda', 'url' => base_url('buku-tamu'), 'keywords' => 'tamu kunjungan pengunjung', 'enabled' => true],
            'sirkulasi'  => ['title' => 'Tata Cara Sirkulasi', 'short' => 'Panduan pinjam & kembali buku', 'url' => base_url('home/tata-cara-sirkulasi'), 'keywords' => 'sirkulasi tata cara aturan panduan pinjam kembali denda perpanjang', 'enabled' => true],
        ];

        return array_filter($services, static fn($s) => $s['enabled']);
    }
}

if (! function_exists('landing_shortcuts')) {
    /** Layanan untuk kartu "Akses cepat" dan pencarian, sesuai urutan tampil. Tiap butir memuat 'id' dan 'icon'. */
    function landing_shortcuts(): array
    {
        $services = landing_services();
        $hasil    = [];

        foreach (['registrasi', 'pinjam', 'kembali', 'baca', 'isbn', 'tamu'] as $id) {
            if (isset($services[$id])) {
                $hasil[] = ['id' => $id, 'icon' => landing_icon($id)] + $services[$id];
            }
        }

        return $hasil;
    }
}

if (! function_exists('landing_footer')) {
    /**
     * Isi footer. Alamat, surel, telepon, deskripsi, dan media sosial diambil dari pengaturan (halaman Nama Perpustakaan);
     * alamat, surel, dan deskripsi memakai teks Kemnaker RI bila kosong. Media sosial yang kosong tidak ditampilkan.
     * Jam layanan dan direktori tidak punya kolom yang bisa dipakai di pengaturan, jadi tetap di sini.
     */
    function landing_footer(): array
    {
        $rows = db_connect()->table('settingparameters')
            ->whereIn('Name', ['NamaLokasiPerpustakaan', 'EmailPerpustakaan', 'Phone', 'TentangKami', 'Instagram', 'Facebook', 'Youtube'])
            ->get()->getResultArray();
        $db = array_column($rows, 'Value', 'Name');

        // Isian bisa berupa tautan penuh atau hanya nama akun ("@akun"), jadi tautan disusun dari nama akunnya.
        $akun = static fn (string $nilai, string $dasar): string => preg_match('#^https?://#i', $nilai) ? $nilai : $dasar . ltrim($nilai, '@/');
        $sosial = [];
        foreach ([
            'instagram' => ['Instagram', 'Instagram', 'https://www.instagram.com/'],
            'facebook'  => ['Facebook', 'Facebook', 'https://www.facebook.com/'],
            'youtube'   => ['YouTube', 'Youtube', 'https://www.youtube.com/@'],
        ] as $id => [$label, $kunci, $dasar]) {
            if (($nilai = trim($db[$kunci] ?? '')) !== '') {
                $sosial[$id] = [$label, $akun($nilai, $dasar)];
            }
        }

        return [
            'alamat'    => trim($db['NamaLokasiPerpustakaan'] ?? '') ?: 'Jl. Jenderal Gatot Subroto Kav. 51, RT.5/RW.4, Kuningan Timur, Setiabudi, Jakarta Selatan 12950 · Gedung B',
            'surel'     => trim($db['EmailPerpustakaan'] ?? '') ?: 'perpustakaan@kemnaker.go.id',
            'telepon'   => trim($db['Phone'] ?? ''),
            'tentang'   => trim($db['TentangKami'] ?? '') ?: 'Perpustakaan Kementerian Ketenagakerjaan Republik Indonesia berkomitmen menyediakan layanan perpustakaan yang berkualitas bagi seluruh pegawai dan masyarakat. Kami menghimpun koleksi buku, regulasi, laporan riset, dan publikasi ketenagakerjaan untuk mendukung perumusan kebijakan serta peningkatan literasi insan ketenagakerjaan Indonesia.',
            'jam'       => [['Senin – Kamis', '07.30 – 16.00 WIB'], ['Jumat', '07.30 – 16.30 WIB']],
            'sosial'    => $sosial,
            'direktori' => [
                ['Kementerian Ketenagakerjaan', 'https://kemnaker.go.id'],
                ['Perpustakaan Nasional RI', 'https://www.perpusnas.go.id'],
                ['Satu Data Ketenagakerjaan', 'https://satudata.kemnaker.go.id'],
                ['SIAPkerja', 'https://siapkerja.kemnaker.go.id'],
                ['JDIH Kemnaker', 'https://jdih.kemnaker.go.id'],
            ],
        ];
    }
}

if (! function_exists('landing_has_cover')) {
    /** True bila berkas sampul buku benar-benar ada di uploads/katalog/. */
    function landing_has_cover($book): bool
    {
        return ! empty($book->CoverURL) && is_file(FCPATH . 'uploads/katalog/' . $book->CoverURL);
    }
}

if (! function_exists('landing_cover_style')) {
    /**
     * Warna sampul buatan sendiri untuk buku tanpa gambar sampul: ['warna' => [latar, aksen], 'terang' => bool].
     * Warna mengikuti unit penerbit; buku umum dapat warna dari $warnaAcak, tetap sama untuk buku yang sama.
     */
    function landing_cover_style($book): array
    {
        $warnaDivisi = [
            ['pola' => '/barenbang/i',                                         'warna' => ['#ea580c', '#ffe1c9'], 'terang' => false],
            ['pola' => '/binalavotas/i',                                       'warna' => ['#1d4ed8', '#cfe0ff'], 'terang' => false],
            ['pola' => '/\birjen\b|inspektorat\s+jenderal/i',                  'warna' => ['#eaff00', '#2f3300'], 'terang' => true],
            ['pola' => '/\bsekjen\b|\bsetjen\b|sekretariat\s+jenderal/i',      'warna' => ['#a7f0b4', '#14532d'], 'terang' => true],
            ['pola' => '/\bphi\b|\bjsk\b|hubungan\s+industrial\s+dan\s+jaminan/i', 'warna' => ['#ffffff', '#1f2a44'], 'terang' => true],
            ['pola' => '/binapenta/i',                                         'warna' => ['#ff8fc1', '#5c0a33'], 'terang' => true],
            ['pola' => '/binwasnaker/i',                                       'warna' => ['#d91f26', '#ffd9d9'], 'terang' => false],
        ];
        $warnaAcak = [
            ['#6c3fd1', '#e2d6ff'], ['#0f766e', '#ccfbf1'], ['#101521', '#f2b544'], ['#7c4a2d', '#f6dcc4'],
            ['#7e1f6b', '#f7d3ee'], ['#1f6f5c', '#c9f0de'], ['#3a3f4b', '#e6e8ee'], ['#4b2e83', '#d9ccff'],
        ];

        $teks = ($book->Author ?? '') . ' ' . ($book->Publisher ?? '');
        foreach ($warnaDivisi as $divisi) {
            if (preg_match($divisi['pola'], $teks)) {
                return ['warna' => $divisi['warna'], 'terang' => $divisi['terang']];
            }
        }

        return ['warna' => $warnaAcak[crc32((string) $book->ID) % count($warnaAcak)], 'terang' => false];
    }
}

if (! function_exists('landing_hero_photos')) {
    /**
     * Foto carousel hero: semua gambar di public/assets/home/hero/, urut menurut nama berkas (paling banyak 8).
     * Nama berkas jadi label foto: "02-ruang-baca-anak.jpg" tampil sebagai "02 / Ruang Baca Anak" (nomor dihitung ulang).
     * Folder kosong: dipakai library-hero.jpg.
     */
    function landing_hero_photos(): array
    {
        $berkas = glob(FCPATH . 'assets/home/hero/*.{jpg,jpeg,png,webp,avif}', GLOB_BRACE) ?: [];
        natcasesort($berkas);

        $foto = [];
        foreach (array_slice(array_values($berkas), 0, 8) as $path) {
            $nama  = preg_replace('/^[0-9]+[-_ ]*/', '', pathinfo($path, PATHINFO_FILENAME));
            $label = ucwords(trim(str_replace(['-', '_'], ' ', $nama))) ?: 'Perpustakaan';
            $foto[] = [
                'src'   => landing_asset('hero/' . rawurlencode(basename($path))),
                'label' => $label,
                'alt'   => 'Foto ' . $label . ', Perpustakaan Kementerian Ketenagakerjaan',
            ];
        }

        return $foto ?: [[
            'src'   => landing_asset('library-hero.jpg'),
            'label' => 'Ruang Baca',
            'alt'   => 'Ruang baca perpustakaan dengan rak buku kayu, bangku, dan meja baca',
        ]];
    }
}
