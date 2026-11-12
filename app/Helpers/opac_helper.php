<?php

if (!function_exists('buildFilterUrl')) {
    /**
     * Generate URL filter bersih menuju halaman OPAC untuk kriteria tertentu
     * tanpa membawa parameter pencarian/filter lama yang menyebabkan konflik hasil kosong.
     *
     * @param string $key   Nama parameter filter, misalnya 'Publisher', 'Author', 'Subject'
     * @param string $value Nilai parameter
     * @param string $baseUrl (opsional) base url, default 'opac'
     * @return string URL lengkap
     */
    function buildFilterUrl(string $key, string $value, string $baseUrl = 'opac'): string
    {
        if ($key === 'Subject') {
            return base_url($baseUrl) . '?' . http_build_query([
                'search'    => $value,
                'search_by' => 'Subject',
            ]);
        }

        return base_url($baseUrl) . '?' . http_build_query([
            $key => $value,
        ]);
    }
}

if (!function_exists('splitSubjects')) {
    /**
     * Memisahkan string subjek menjadi daftar subjek individual:
     * - Pisahkan berdasarkan titik koma (;)
     * - Pisahkan juga berdasarkan strip ganda (--) atau strip tunggal pemisah sub-subjek (-)
     * - Bersihkan spasi dan hapus duplikat (case-insensitive)
     *
     * @param string|null $subjectString
     * @return array
     */
    function splitSubjects(?string $subjectString): array
    {
        if (empty($subjectString)) {
            return [];
        }

        $parts = explode(';', $subjectString);
        $result = [];
        $seen = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;

            // Pisahkan jika ada sub-subjek bertanda '--' atau ' - '
            $subParts = preg_split('/\s*--\s*|\s+-\s+/', $part);
            foreach ($subParts as $sub) {
                $sub = trim($sub, " \t\n\r\0\x0B.,;:-");
                if ($sub === '') continue;

                $lower = mb_strtolower($sub);
                if (!isset($seen[$lower])) {
                    $seen[$lower] = true;
                    $result[] = $sub;
                }
            }
        }

        return $result;
    }
}

if (!function_exists('enrichCatalogsWithCirculation')) {
    /**
     * Memperkaya daftar katalog dengan informasi ketersediaan eksemplar, nomor panggil, dan lokasi rak
     * dalam satu query batch.
     *
     * @param array $catalogs Daftar objek atau array katalog
     * @return array Daftar katalog yang sudah diperkaya
     */
    function enrichCatalogsWithCirculation(array $catalogs): array
    {
        if (empty($catalogs)) {
            return $catalogs;
        }

        $ids = [];
        foreach ($catalogs as $cat) {
            $id = is_array($cat) ? ($cat['ID'] ?? null) : ($cat->ID ?? null);
            if ($id) {
                $ids[] = (int) $id;
            }
        }

        if (empty($ids)) {
            return $catalogs;
        }

        $ids = array_values(array_unique($ids));
        $db  = \Config\Database::connect();

        $rows = $db->table('collections col')
            ->select("
                col.Catalog_id,
                COUNT(col.ID) AS total_eks,
                SUM(CASE WHEN col.Status_id = 1 THEN 1 ELSE 0 END) AS avail_eks,
                SUM(CASE WHEN col.Status_id = 5 THEN 1 ELSE 0 END) AS loan_eks,
                MAX(NULLIF(TRIM(col.CallNumber), '')) AS col_call_number,
                MAX(NULLIF(TRIM(col.Rak), '')) AS col_rak,
                MAX(NULLIF(TRIM(loc.Name), '')) AS loc_name
            ", false)
            ->join('locations loc', 'loc.ID = col.Location_id', 'left')
            ->whereIn('col.Catalog_id', $ids)
            ->groupBy('col.Catalog_id')
            ->get()
            ->getResultArray();

        $statsByCat = [];
        foreach ($rows as $r) {
            $statsByCat[(int) $r['Catalog_id']] = $r;
        }

        foreach ($catalogs as &$cat) {
            $id   = is_array($cat) ? ($cat['ID'] ?? 0) : ($cat->ID ?? 0);
            $stat = $statsByCat[(int) $id] ?? null;

            $totalEks = (int) ($stat['total_eks'] ?? 0);
            $availEks = (int) ($stat['avail_eks'] ?? 0);
            $loanEks  = (int) ($stat['loan_eks'] ?? 0);
            $colCall  = $stat['col_call_number'] ?? '';
            $colRak   = $stat['col_rak'] ?? '';
            $locName  = $stat['loc_name'] ?? '';

            if ($colRak && $locName && stripos($locName, $colRak) === false) {
                $shelfLocation = $colRak . ' / ' . $locName;
            } elseif ($colRak) {
                $shelfLocation = $colRak;
            } elseif ($locName) {
                $shelfLocation = $locName;
            } else {
                $shelfLocation = 'Rak Koleksi Umum';
            }

            if (is_array($cat)) {
                $cat['TotalEks']      = $totalEks;
                $cat['AvailEks']      = $availEks;
                $cat['LoanEks']       = $loanEks;
                $cat['ColCallNumber'] = $colCall;
                $cat['ShelfLocation'] = $shelfLocation;
            } else {
                $cat->TotalEks      = $totalEks;
                $cat->AvailEks      = $availEks;
                $cat->LoanEks       = $loanEks;
                $cat->ColCallNumber = $colCall;
                $cat->ShelfLocation = $shelfLocation;
            }
        }
        unset($cat);

        return $catalogs;
    }
}

if (!function_exists('renderModernBookCard')) {
    /**
     * Render kartu buku vertikal modern dengan sampul dinamis/gambar, badge sirkulasi,
     * nomor panggil, lokasi rak, metadata, dan tombol detail.
     *
     * @param object|array $item Data katalog
     * @param string $colClass Kelas grid Bootstrap (misal: 'col-xl-4 col-md-6' atau 'col-lg-3 col-md-6')
     * @return string HTML kartu buku
     */
    function renderModernBookCard($item, string $colClass = 'col-xl-4 col-md-6'): string
    {
        $isArr      = is_array($item);
        $id         = $isArr ? ($item['ID'] ?? 0) : ($item->ID ?? 0);
        $title      = trim((string) ($isArr ? ($item['Title'] ?? 'Tanpa Judul') : ($item->Title ?? 'Tanpa Judul')));
        $author     = trim((string) ($isArr ? ($item['Author'] ?? '') : ($item->Author ?? '')));
        $publisher  = trim((string) ($isArr ? ($item['Publisher'] ?? '') : ($item->Publisher ?? '')));
        $year       = trim((string) ($isArr ? ($item['PublishYear'] ?? '') : ($item->PublishYear ?? '')));
        $edition    = trim((string) ($isArr ? ($item['Edition'] ?? '') : ($item->Edition ?? '')));
        $subjectRaw = trim((string) ($isArr ? ($item['Subject'] ?? '') : ($item->Subject ?? '')));
        $coverURL   = trim((string) ($isArr ? ($item['CoverURL'] ?? '') : ($item->CoverURL ?? '')));
        $isbn       = trim((string) ($isArr ? ($item['ISBN'] ?? '') : ($item->ISBN ?? '')));
        $note       = trim(strip_tags((string) ($isArr ? ($item['Note'] ?? '') : ($item->Note ?? ''))));
        $physDesc   = trim((string) ($isArr ? ($item['PhysicalDescription'] ?? '') : ($item->PhysicalDescription ?? '')));
        $pubLoc     = trim((string) ($isArr ? ($item['PublishLocation'] ?? '') : ($item->PublishLocation ?? '')));

        $catCall    = trim((string) ($isArr ? ($item['CallNumber'] ?? '') : ($item->CallNumber ?? '')));
        $colCall    = trim((string) ($isArr ? ($item['ColCallNumber'] ?? '') : ($item->ColCallNumber ?? '')));
        $callNumber = $catCall !== '' ? $catCall : ($colCall !== '' ? $colCall : 'Koleksi Umum');

        $totalEks   = (int) ($isArr ? ($item['TotalEks'] ?? 0) : ($item->TotalEks ?? 0));
        $availEks   = (int) ($isArr ? ($item['AvailEks'] ?? 0) : ($item->AvailEks ?? 0));
        $shelfLoc   = trim((string) ($isArr ? ($item['ShelfLocation'] ?? 'Rak Koleksi Umum') : ($item->ShelfLocation ?? 'Rak Koleksi Umum')));

        $subjects   = splitSubjects($subjectRaw);
        $firstSubj  = !empty($subjects) ? $subjects[0] : ($publisher !== '' ? $publisher : 'Koleksi Perpustakaan');
        $topLabel   = mb_strtoupper(mb_substr($firstSubj, 0, 24));

        // Periksa apakah file cover benar-benar ada di disk
        $hasValidCoverFile = false;
        $coverImgSrc       = '';
        if ($coverURL !== '' && $coverURL !== 'default-cover.jpg' && $coverURL !== 'default-cover.png') {
            if (filter_var($coverURL, FILTER_VALIDATE_URL)) {
                $hasValidCoverFile = true;
                $coverImgSrc       = $coverURL;
            } else {
                $localPath = FCPATH . 'uploads/katalog/' . $coverURL;
                if (is_file($localPath)) {
                    $hasValidCoverFile = true;
                    $coverImgSrc       = base_url('uploads/katalog/' . $coverURL);
                }
            }
        }

        // Pilih palet warna sampul buku berdasarkan ID
        $palettes = [
            ['bg' => 'linear-gradient(145deg, #10254c 0%, #0a1833 100%)', 'accent' => '#f59e0b', 'pill_bg' => 'rgba(245, 158, 11, 0.16)'],
            ['bg' => 'linear-gradient(145deg, #093529 0%, #052119 100%)', 'accent' => '#fbbf24', 'pill_bg' => 'rgba(16, 185, 129, 0.2)'],
            ['bg' => 'linear-gradient(145deg, #153462 0%, #0c203f 100%)', 'accent' => '#fcd34d', 'pill_bg' => 'rgba(59, 130, 246, 0.22)'],
            ['bg' => 'linear-gradient(145deg, #4a1e0c 0%, #2d1106 100%)', 'accent' => '#fbbf24', 'pill_bg' => 'rgba(245, 158, 11, 0.2)'],
            ['bg' => 'linear-gradient(145deg, #2b194d 0%, #180d2e 100%)', 'accent' => '#f59e0b', 'pill_bg' => 'rgba(168, 85, 247, 0.2)'],
        ];
        $palette = $palettes[$id % count($palettes)];

        // Status ketersediaan eksemplar
        if ($totalEks > 0 && $availEks > 0) {
            $statusBadgeHtml = '<span class="opac-status-pill status-available"><i class="fa-regular fa-circle-check me-1"></i>Tersedia di Rak</span>';
            $eksCountHtml    = esc($availEks . '/' . $totalEks . ' Eks.');
        } elseif ($totalEks > 0 && $availEks === 0) {
            $statusBadgeHtml = '<span class="opac-status-pill status-borrowed"><i class="fa-regular fa-clock me-1"></i>Dipinjam</span>';
            $eksCountHtml    = esc('0/' . $totalEks . ' Eks.');
        } else {
            // Jika belum ada data di tabel collections, tampilkan default 1/1 Eks Tersedia di Rak
            $statusBadgeHtml = '<span class="opac-status-pill status-available"><i class="fa-regular fa-circle-check me-1"></i>Tersedia di Rak</span>';
            $eksCountHtml    = '1/1 Eks.';
        }

        // Baris metadata bawah: Edisi · Tahun · Subjek
        $metaParts = [];
        if ($edition !== '') {
            $metaParts[] = $edition;
        } elseif ($publisher !== '') {
            $metaParts[] = $publisher;
        }
        if ($year !== '' && $year !== '0000' && $year !== '-') {
            $metaParts[] = $year;
        }
        if (!empty($subjects)) {
            $metaParts[] = implode(', ', array_slice($subjects, 0, 2));
        }
        $metaLine = !empty($metaParts) ? implode(' · ', $metaParts) : 'Koleksi Perpustakaan';

        $displayAuthor = $author !== '' && $author !== '-' ? $author : 'Tim Penyusun / Instansi';
        $footerRightOnCover = $edition !== '' ? $edition : ($publisher !== '' ? mb_substr($publisher, 0, 18) : 'Koleksi Resmi');
        $detailUrl = base_url('opac/detail/' . $id);

        // Siapkan Penerbit & Tahun untuk pop-up
        $pubAndYear = $publisher !== '' ? $publisher : ($pubLoc !== '' ? $pubLoc : 'Penerbit Kemnaker / Umum');
        if ($year !== '' && $year !== '-' && $year !== '0000') {
            $pubAndYear .= ' (' . $year . ')';
        }

        // Siapkan ISBN & Edisi untuk pop-up
        $isbnParts = [];
        if ($isbn !== '' && $isbn !== '-') {
            $isbnParts[] = $isbn;
        }
        if ($edition !== '' && $edition !== '-') {
            $isbnParts[] = $edition;
        } elseif ($physDesc !== '' && $physDesc !== '-') {
            $isbnParts[] = $physDesc;
        }
        $isbnAndEdition = !empty($isbnParts) ? implode(' · ', $isbnParts) : 'Koleksi Cetak Resmi';

        // Siapkan Abstrak / Ringkasan Koleksi untuk pop-up
        if ($note !== '' && $note !== '-') {
            $abstractText = $note;
        } else {
            $subjSummary  = !empty($subjects) ? implode(', ', $subjects) : 'literatur umum dan ketenagakerjaan';
            $abstractText = 'Koleksi bibliografis berjudul "' . $title . '" yang disusun oleh ' . $displayAuthor
                . ($publisher !== '' ? ' dan diterbitkan oleh ' . $publisher : '')
                . ($year !== '' && $year !== '-' ? ' pada tahun ' . $year : '')
                . '. Mencakup bahasan mengenai ' . $subjSummary . '.';
        }

        $isAvailable = ($totalEks === 0 || $availEks > 0);
        $statusModalText = $isAvailable
            ? 'Tersedia di Rak (' . ($totalEks > 0 ? $availEks . '/' . $totalEks : '1/1') . ' Eks.)'
            : 'Dipinjam (0/' . $totalEks . ' Eks.)';

        $modalData = [
            'id'              => $id,
            'title'           => $title,
            'author'          => $displayAuthor,
            'pubAndYear'      => $pubAndYear,
            'shelfLoc'        => $shelfLoc,
            'isbnAndEdition'  => $isbnAndEdition,
            'callNumber'      => $callNumber,
            'abstract'        => $abstractText,
            'subjects'        => !empty($subjects) ? $subjects : ['Koleksi Umum'],
            'isAvailable'     => $isAvailable,
            'statusModalText' => $statusModalText,
            'detailUrl'       => $detailUrl,
            'hasCover'        => $hasValidCoverFile,
            'coverImgSrc'     => $coverImgSrc,
            'paletteBg'       => $palette['bg'],
            'paletteAccent'   => $palette['accent'],
            'palettePillBg'   => $palette['pill_bg'],
            'topLabel'        => $topLabel,
            'year'            => ($year !== '' && $year !== '-') ? $year : '',
            'footerRight'     => $footerRightOnCover,
        ];
        $modalJsonAttr = htmlspecialchars(json_encode($modalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');

        ob_start();
        ?>
        <div class="<?= esc($colClass) ?>">
            <div class="opac-modern-card h-100 d-flex flex-column" data-catalog-id="<?= (int) $id ?>" data-book-meta="<?= $modalJsonAttr ?>">
                <!-- Area Sampul Buku -->
                <a href="<?= $detailUrl ?>" class="opac-cover-stage text-decoration-none js-open-book-modal">
                    <div class="opac-book-3d" style="background: <?= $palette['bg'] ?>;">
                        <?php if ($hasValidCoverFile): ?>
                            <img src="<?= esc($coverImgSrc) ?>"
                                 alt="<?= esc($title) ?>"
                                 class="opac-real-cover-img"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <?php endif; ?>

                        <div class="opac-generated-cover" style="display: <?= $hasValidCoverFile ? 'none' : 'flex' ?>;">
                            <div class="opac-book-spine"></div>
                            <div class="opac-cover-geo-circle"></div>
                            <div class="opac-cover-geo-rect"></div>

                            <div class="opac-cover-top d-flex justify-content-between align-items-center">
                                <span class="opac-cover-tag" style="background: <?= $palette['pill_bg'] ?>; color: <?= $palette['accent'] ?>;">
                                    <?= esc($topLabel) ?>
                                </span>
                                <?php if ($year !== '' && $year !== '-'): ?>
                                    <span class="opac-cover-year"><?= esc($year) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="opac-cover-middle my-auto">
                                <div class="opac-cover-title"><?= esc(mb_strimwidth($title, 0, 85, '...')) ?></div>
                                <div class="opac-cover-author"><?= esc(mb_strimwidth($displayAuthor, 0, 55, '...')) ?></div>
                            </div>

                            <div class="opac-cover-bottom d-flex justify-content-between align-items-end">
                                <span class="opac-cover-call"><?= esc(mb_strimwidth($callNumber, 0, 18, '')) ?></span>
                                <span class="opac-cover-edition" style="color: <?= $palette['accent'] ?>;">
                                    <?= esc(mb_strimwidth($footerRightOnCover, 0, 18, '')) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Area Informasi & Sirkulasi -->
                <div class="opac-card-body d-flex flex-column flex-grow-1">
                    <!-- Baris 1: Status & Jumlah Eksemplar -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <?= $statusBadgeHtml ?>
                        <span class="opac-eks-count"><?= $eksCountHtml ?></span>
                    </div>

                    <!-- Baris 2: Nomor Panggil & Lokasi Rak -->
                    <div class="opac-call-row mb-2 text-truncate" title="<?= esc($callNumber . ' · ' . $shelfLoc) ?>">
                        <span class="opac-call-badge"><?= esc($callNumber) ?></span>
                        <span class="opac-shelf-text">&middot; <?= esc($shelfLoc) ?></span>
                    </div>

                    <!-- Baris 3: Judul Buku -->
                    <h5 class="opac-book-heading mb-1" title="<?= esc($title) ?>">
                        <a href="<?= $detailUrl ?>" class="text-decoration-none js-open-book-modal">
                            <?= esc($title) ?>
                        </a>
                    </h5>

                    <!-- Baris 4: Pengarang -->
                    <div class="opac-book-author text-truncate mb-1" title="<?= esc($displayAuthor) ?>">
                        <?= esc($displayAuthor) ?>
                    </div>

                    <!-- Baris 5: Edisi · Tahun · Subjek -->
                    <div class="opac-book-submeta mb-3" title="<?= esc($metaLine) ?>">
                        <?= esc($metaLine) ?>
                    </div>

                    <!-- Baris 6: Tombol Lihat Detail & Sirkulasi -->
                    <div class="mt-auto pt-1">
                        <a href="<?= $detailUrl ?>" class="btn opac-btn-detail w-100 js-open-book-modal">
                            Lihat Detail &amp; Sirkulasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
