<?php
/**
 * ============================================================================
 * SCRIPT MIGRASI KOLEKSI: SLiMS (perpustakaan_kemnaker) -> INLISLite (perpustakaan_inlislite)
 * ============================================================================
 *
 * CARA PAKAI (dijalankan lewat SSH, dari folder root inlislite):
 *
 *   php migrasi_slims_ke_inlislite.php --dry-run                 (default, aman, tidak ubah apapun)
 *   php migrasi_slims_ke_inlislite.php --dry-run --biblio-id=2   (test 1 judul spesifik)
 *   php migrasi_slims_ke_inlislite.php --dry-run --limit=20      (test 20 judul pertama)
 *
 *   php migrasi_slims_ke_inlislite.php --execute --biblio-id=2   (BENAR-BENAR migrasi 1 judul)
 *   php migrasi_slims_ke_inlislite.php --execute --limit=50 --offset=0   (migrasi 50 judul pertama)
 *   php migrasi_slims_ke_inlislite.php --execute                (migrasi SEMUA, jalankan paling akhir)
 *
 * CATATAN PENTING:
 * - Script ini AMAN dijalankan berulang kali (idempotent). Untuk tiap judul,
 *   ia akan MENGGANTI (hapus lalu insert ulang) baris `catalogs` + `catalog_ruas`
 *   yang match BIBID-nya, bukan menumpuk data baru.
 * - Proses per-judul dibungkus TRANSACTION: kalau satu langkah gagal, judul itu
 *   di-rollback (baris lama TIDAK jadi terhapus), lalu script lanjut ke judul berikutnya.
 * - CoverURL yang sudah ada di baris lama akan dibawa/dipertahankan ke baris baru.
 * - Kredensial database dibaca otomatis dari file .env di folder ini.
 * - Catatan (notes) yang terindikasi berisi hash password/kredensial akan
 *   otomatis disaring dan diganti placeholder, tidak ikut termigrasi apa adanya.
 *
 * ============================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

// ----------------------------------------------------------------------------
// 1. PARSE ARGUMEN CLI
// ----------------------------------------------------------------------------
$options = getopt('', ['dry-run', 'execute', 'limit::', 'offset::', 'biblio-id::']);
$DRY_RUN   = !isset($options['execute']); // default TRUE kecuali --execute eksplisit diberikan
$LIMIT     = isset($options['limit']) ? (int) $options['limit'] : null;
$OFFSET    = isset($options['offset']) ? (int) $options['offset'] : 0;
$ONLY_ID   = isset($options['biblio-id']) ? (int) $options['biblio-id'] : null;

echo "============================================================\n";
echo " MIGRASI SLiMS -> INLISLite\n";
echo " Mode: " . ($DRY_RUN ? "DRY-RUN (preview saja, TIDAK mengubah database)" : "EXECUTE (akan mengubah database!)") . "\n";
if ($ONLY_ID) echo " Filter: hanya biblio_id = $ONLY_ID\n";
if ($LIMIT) echo " Limit: $LIMIT baris, offset $OFFSET\n";
echo "============================================================\n\n";

if (!$DRY_RUN) {
    echo "Anda akan menjalankan mode EXECUTE. Ini akan mengubah data di perpustakaan_inlislite.\n";
    echo "Ketik 'YA' (huruf besar) untuk lanjut, atau Ctrl+C untuk batal: ";
    $confirm = trim(fgets(STDIN));
    if ($confirm !== 'YA') {
        echo "Dibatalkan.\n";
        exit(1);
    }
    echo "\n";
}

// ----------------------------------------------------------------------------
// 2. BACA KREDENSIAL DARI .env (folder root inlislite, sejajar file ini)
// ----------------------------------------------------------------------------
function parse_env($path)
{
    if (!file_exists($path)) {
        die("ERROR: file .env tidak ditemukan di: $path\n");
    }
    $env = [];
    foreach (file($path) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $val] = explode('=', $line, 2);
        $env[trim($key)] = trim($val);
    }
    return $env;
}

$envPath = __DIR__ . '/.env';
$env = parse_env($envPath);

$DB_HOST = $env['database.default.hostname'] ?? 'localhost';
$DB_USER = $env['database.default.username'] ?? null;
$DB_PASS = $env['database.default.password'] ?? null;
$DB_INLISLITE = $env['database.default.database'] ?? 'perpustakaan_inlislite';
$DB_SLIMS = 'perpustakaan_kemnaker'; // nama database SLiMS

if (!$DB_USER || $DB_PASS === null) {
    die("ERROR: tidak berhasil membaca kredensial database dari .env\n");
}

// ----------------------------------------------------------------------------
// 3. KONEKSI DATABASE
// ----------------------------------------------------------------------------
$slims = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_SLIMS);
if ($slims->connect_error) die("Gagal konek ke SLiMS: " . $slims->connect_error . "\n");
$slims->set_charset('latin1'); // sesuai charset asli tabel biblio dkk

$inlis = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_INLISLITE);
if ($inlis->connect_error) die("Gagal konek ke INLISLite: " . $inlis->connect_error . "\n");
$inlis->set_charset('utf8mb4');

echo "Koneksi database OK.\n\n";

// ----------------------------------------------------------------------------
// 4. HELPER FUNCTIONS
// ----------------------------------------------------------------------------

/** Perbaiki encoding teks dari SLiMS (latin1/mixed) supaya aman disimpan sebagai utf8mb4 */
function fix_encoding($str)
{
    if ($str === null) return null;
    $str = (string) $str;
    if ($str === '') return $str;

    // Sudah UTF-8 valid, tapi tetap perlu dijamin tidak ada byte nyasar
    if (mb_check_encoding($str, 'UTF-8')) {
        return $str;
    }

    // Coba Windows-1252 dulu (superset ISO-8859-1, menangani smart quotes/dash dari Word)
    $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $str);
    if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
        return $converted;
    }

    // Fallback ke ISO-8859-1 murni
    $converted2 = @mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
    if ($converted2 !== false && mb_check_encoding($converted2, 'UTF-8')) {
        return $converted2;
    }

    // Jaring pengaman terakhir: paksa buang byte yang tidak valid, dijamin hasil akhir valid UTF-8
    $safe = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
    if ($safe !== false && mb_check_encoding($safe, 'UTF-8')) {
        return $safe;
    }
    // Kalau masih gagal juga (sangat jarang), sisakan karakter ASCII cetak saja
    return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $str);
}

/**
 * Sanitasi catatan: buang isi kalau terindikasi mengandung data sensitif
 * (hash password bcrypt/$2y$, atau kombinasi username:hash yang bocor).
 * Ditemukan di data lama SLiMS (kemungkinan bekas insiden/kebocoran data).
 */
function sanitize_notes($str)
{
    if ($str === null || $str === '') return $str;
    // Pola hash bcrypt: $2y$10$... atau $2a$/$2b$ diikuti banyak karakter alfanumerik
    if (preg_match('/\$2[aby]\$\d{2}\$[A-Za-z0-9\.\/]{40,}/', $str)) {
        return '[Catatan dihapus otomatis - terindikasi berisi data sensitif/kredensial]';
    }
    return $str;
}

/** Bangun string Value untuk catalog_ruas dari subfield, format "$a ... $b ..." */
function build_marc_value(array $subfields, $forceIncludeA = false)
{
    $parts = [];
    foreach ($subfields as $code => $val) {
        $val = trim((string) $val);
        if ($val === '' && !($forceIncludeA && $code === 'a' && empty($parts))) {
            continue;
        }
        $parts[] = "\${$code} {$val}";
    }
    if (empty($parts)) return null;
    return implode(' ', $parts);
}

/** Generate BIBID/ControlNumber dari biblio_id SLiMS, format BIB000001 dst */
function make_bibid($biblio_id)
{
    return 'BIB' . str_pad($biblio_id, 6, '0', STR_PAD_LEFT);
}

// Mapping kode bahasa SLiMS -> kode bahasa INLISLite (refferenceitems Refference_id=5)
$LANGUAGE_MAP = [
    'id'  => 'ind',
    'en'  => 'eng',
    'FRA' => 'fre',
];

// Mapping authority_type SLiMS -> tag MARC INLISLite
$MAIN_TAG_MAP  = ['p' => '100', 'o' => '110', 'c' => '111'];
$ADDED_TAG_MAP = ['p' => '700', 'o' => '710', 'c' => '711'];

// Mapping status eksemplar (dipakai nanti di skrip migrasi eksemplar terpisah, disiapkan di sini untuk referensi)
// EB=12, TR=13, RSK=3, default(null)=1

$stats = [
    'total'    => 0,
    'inserted' => 0,
    'skipped'  => 0,
    'errors'   => 0,
    'error_list' => [],
];

// ----------------------------------------------------------------------------
// 5. AMBIL DAFTAR BIBLIO YANG AKAN DIPROSES
// ----------------------------------------------------------------------------
$where = '1=1';
if ($ONLY_ID) {
    $where = "biblio_id = " . (int) $ONLY_ID;
}
$limitSql = '';
if ($LIMIT !== null) {
    $limitSql = "LIMIT " . (int) $OFFSET . ", " . (int) $LIMIT;
}

$sql = "SELECT biblio_id FROM biblio WHERE $where ORDER BY biblio_id ASC $limitSql";
$res = $slims->query($sql);
if (!$res) die("Query daftar biblio gagal: " . $slims->error . "\n");

$biblioIds = [];
while ($row = $res->fetch_assoc()) {
    $biblioIds[] = (int) $row['biblio_id'];
}

echo "Akan memproses " . count($biblioIds) . " judul.\n\n";

// ----------------------------------------------------------------------------
// 6. PROSES SATU PER SATU
// ----------------------------------------------------------------------------
foreach ($biblioIds as $biblio_id) {
    $stats['total']++;
    $bibid = make_bibid($biblio_id);

    try {
        // --- Ambil data induk biblio ---
        $stmt = $slims->prepare("
            SELECT b.*, g.gmd_code, p.publisher_name, pl.place_name, l.language_id as lang_code
            FROM biblio b
            LEFT JOIN mst_gmd g ON g.gmd_id = b.gmd_id
            LEFT JOIN mst_publisher p ON p.publisher_id = b.publisher_id
            LEFT JOIN mst_place pl ON pl.place_id = b.publish_place_id
            LEFT JOIN mst_language l ON l.language_id = b.language_id
            WHERE b.biblio_id = ?
        ");
        $stmt->bind_param('i', $biblio_id);
        $stmt->execute();
        $biblio = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$biblio) {
            throw new Exception("Data biblio_id=$biblio_id tidak ditemukan (terhapus?)");
        }

        // --- Ambil pengarang (join mst_author, urut level) ---
        $stmt = $slims->prepare("
            SELECT a.author_name, a.authority_type, ba.level
            FROM biblio_author ba
            JOIN mst_author a ON a.author_id = ba.author_id
            WHERE ba.biblio_id = ?
            ORDER BY ba.level ASC
        ");
        $stmt->bind_param('i', $biblio_id);
        $stmt->execute();
        $authors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // --- Ambil subjek ---
        $stmt = $slims->prepare("
            SELECT t.topic
            FROM biblio_topic bt
            JOIN mst_topic t ON t.topic_id = bt.topic_id
            WHERE bt.biblio_id = ?
        ");
        $stmt->bind_param('i', $biblio_id);
        $stmt->execute();
        $subjects = array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'topic');
        $stmt->close();

        // ------------------------------------------------------------------
        // SUSUN DATA UNTUK TABEL `catalogs`
        // ------------------------------------------------------------------
        $title       = fix_encoding($biblio['title']);
        $sor         = fix_encoding(trim($biblio['sor'] ?? ''));
        $authorNames = array_map(fn($a) => fix_encoding($a['author_name']), $authors);
        $authorJoined = implode('; ', $authorNames);

        // Penanggung jawab: pakai SOR kalau ada, fallback ke gabungan nama pengarang
        $penanggungJawab = $sor !== '' ? $sor : $authorJoined;

        $publisherName = fix_encoding($biblio['publisher_name'] ?? '');
        $placeName     = fix_encoding($biblio['place_name'] ?? '');
        $publishYear   = fix_encoding($biblio['publish_year'] ?? '');
        $collation     = fix_encoding($biblio['collation'] ?? '');
        $isbn          = fix_encoding($biblio['isbn_issn'] ?? '');
        $callNumber    = fix_encoding($biblio['call_number'] ?? '');
        $classification = fix_encoding($biblio['classification'] ?? '');
        $notes         = sanitize_notes(fix_encoding($biblio['notes'] ?? ''));
        $edition       = fix_encoding($biblio['edition'] ?? '');
        $seriesTitle   = fix_encoding($biblio['series_title'] ?? '');
        $specDetail    = sanitize_notes(fix_encoding($biblio['spec_detail_info'] ?? ''));
        $subjectJoined = implode('; ', array_map('fix_encoding', $subjects));

        $isOpac = empty($biblio['opac_hide']) ? 1 : 0; // dibalik: opac_hide=1 -> IsOPAC=0
        $worksheetId = ((int) $biblio['frequency_id'] !== 0) ? 4 : 1; // 4=Terbitan Berkala, 1=Monograf

        $langCodeSlims = $biblio['language_id'] ?? null;
        $langCodeInlis = $LANGUAGE_MAP[$langCodeSlims] ?? '###';

        $publikasi = trim(
            ($placeName ? $placeName : '') .
            ($publisherName ? ' : ' . $publisherName : '') .
            ($publishYear ? ', ' . $publishYear : '')
        );

        // Gabungkan notes + spec_detail_info (spec_detail_info tidak punya tag resmi, fallback ke catatan)
        $noteFinal = trim($notes);
        if ($specDetail !== '') {
            $noteFinal = trim($noteFinal . ($noteFinal !== '' ? "\n" : '') . "[Info Detail Spesifik] $specDetail");
        }

        // Cek apakah BIBID ini sudah ada (read-only, aman dijalankan di mode manapun)
        $existingCoverUrl = null;
        $oldId = null;
        $stmtCheck = $inlis->prepare("SELECT ID, CoverURL FROM catalogs WHERE BIBID = ?");
        $stmtCheck->bind_param('s', $bibid);
        $stmtCheck->execute();
        $existing = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();
        if ($existing) {
            $existingCoverUrl = $existing['CoverURL'];
            $oldId = $existing['ID'];
        }

        if ($DRY_RUN) {
            // Dry-run: TIDAK ada perintah tulis (INSERT/DELETE) sama sekali, cuma preview.
            $stats['inserted']++; // dihitung sebagai "akan diinsert"
            $ket = $oldId ? "akan MENGGANTI baris lama ID=$oldId" : "akan jadi baris BARU";
            echo "[DRY-RUN] biblio_id=$biblio_id ($bibid): \"$title\" -- OK ($ket)\n";
            continue;
        }

        // ------------------------------------------------------------------
        // MULAI TRANSAKSI (per judul) -- hanya di mode EXECUTE
        // ------------------------------------------------------------------
        $inlis->begin_transaction();

        if ($oldId) {
            $del1 = $inlis->prepare("DELETE FROM catalog_ruas WHERE CatalogId = ?");
            $del1->bind_param('i', $oldId);
            $del1->execute();
            $del1->close();

            $del2 = $inlis->prepare("DELETE FROM catalogs WHERE ID = ?");
            $del2->bind_param('i', $oldId);
            $del2->execute();
            $del2->close();
        }

        // --- INSERT ke `catalogs` ---
        $stmtIns = $inlis->prepare("
            INSERT INTO catalogs
            (ControlNumber, BIBID, Title, Author, Edition, Publisher, PublishLocation, PublishYear,
             Publikasi, Subject, PhysicalDescription, ISBN, CallNumber, Note, Languages, DeweyNo,
             IsOPAC, IsBNI, IsKIN, IsRDA, Worksheet_id, CoverURL, CreateDate, CreateTerminal, active)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,1,1,?,?,NOW(),'SLIMS_MIGRATION',1)
        ");
        // Urutan tipe: 16x 's' (ControlNumber..DeweyNo), lalu 'i' (IsOPAC), 'i' (Worksheet_id), 's' (CoverURL)
        $bindTypes = str_repeat('s', 16) . 'iis';
        $stmtIns->bind_param(
            $bindTypes,
            $bibid, $bibid, $title, $authorJoined, $edition, $publisherName, $placeName, $publishYear,
            $publikasi, $subjectJoined, $collation, $isbn, $callNumber, $noteFinal, $langCodeInlis,
            $classification, $isOpac, $worksheetId, $existingCoverUrl
        );
        $stmtIns->execute();
        $newId = $inlis->insert_id;
        $stmtIns->close();

        // ------------------------------------------------------------------
        // SUSUN & INSERT catalog_ruas
        // ------------------------------------------------------------------
        $ruasRows = []; // [tag, ind1, ind2, value]

        // 245 - Judul & Penanggung Jawab
        $v245 = build_marc_value(['a' => $title, 'c' => $penanggungJawab], true);
        if ($v245) $ruasRows[] = ['245', '#', '#', $v245];

        // 100/110/111 - Pengarang Utama (level 1, hanya satu / ambil yang pertama)
        // 700/710/711 - Pengarang Tambahan (level > 1)
        $mainDone = false;
        foreach ($authors as $a) {
            $type = $a['authority_type'] ?: 'p';
            $name = fix_encoding($a['author_name']);
            if ($name === '') continue;

            if (!$mainDone && (int) $a['level'] <= 1) {
                $tag = $MAIN_TAG_MAP[$type] ?? '100';
                $ind1 = ($tag === '100') ? '0' : '#';
                $v = build_marc_value(['a' => $name]);
                if ($v) $ruasRows[] = [$tag, $ind1, '#', $v];
                $mainDone = true;
            } else {
                $tag = $ADDED_TAG_MAP[$type] ?? '700';
                $ind1 = ($tag === '700') ? '0' : '#';
                $v = build_marc_value(['a' => $name]);
                if ($v) $ruasRows[] = [$tag, $ind1, '#', $v];
            }
        }

        // 264 - Penerbitan (RDA)
        $v264 = build_marc_value(['a' => $placeName, 'b' => $publisherName, 'c' => $publishYear]);
        if ($v264) $ruasRows[] = ['264', '#', '1', $v264];

        // 300 - Deskripsi Fisik
        $v300 = build_marc_value(['a' => $collation]);
        if ($v300) $ruasRows[] = ['300', '#', '#', $v300];

        // 020 - ISBN
        $v020 = build_marc_value(['a' => $isbn]);
        if ($v020) $ruasRows[] = ['020', '#', '#', $v020];

        // 082 - No. Klas DDC
        $v082 = build_marc_value(['a' => $classification]);
        if ($v082) $ruasRows[] = ['082', '#', '#', $v082];

        // 084 - No. Panggil
        $v084 = build_marc_value(['a' => $callNumber]);
        if ($v084) $ruasRows[] = ['084', '#', '#', $v084];

        // 500 - Catatan (gabungan notes + spec_detail_info)
        $v500 = build_marc_value(['a' => $noteFinal]);
        if ($v500) $ruasRows[] = ['500', '#', '#', $v500];

        // 600 - Subjek
        $v600 = build_marc_value(['a' => $subjectJoined]);
        if ($v600) $ruasRows[] = ['600', '0', '#', $v600];

        // 490 - Judul Seri (tersimpan, belum tampil di form edit saat ini)
        $v490 = build_marc_value(['a' => $seriesTitle]);
        if ($v490) $ruasRows[] = ['490', '0', '#', $v490];

        // 008 - Bahasa (posisi karakter 39-41)
        $v008 = '$a ' . date('ymd') . str_repeat('#', 29) . $langCodeInlis . '##';
        $ruasRows[] = ['008', '#', '#', $v008];

        // --- Insert semua baris catalog_ruas ---
        $stmtRuas = $inlis->prepare("
            INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, CreateDate, CreateTerminal, active)
            VALUES (?,?,?,?,?,0,NOW(),'SLIMS_MIGRATION',1)
        ");
        foreach ($ruasRows as $r) {
            [$tag, $ind1, $ind2, $val] = $r;
            $stmtRuas->bind_param('issss', $newId, $tag, $ind1, $ind2, $val);
            $stmtRuas->execute();
        }
        $stmtRuas->close();

        $inlis->commit();
        $stats['inserted']++;
        echo "[OK] biblio_id=$biblio_id ($bibid) -> catalogs.ID=$newId: \"$title\"\n";
    } catch (Throwable $e) {
        if (!$DRY_RUN) {
            $inlis->rollback();
        }
        $stats['errors']++;
        $stats['error_list'][] = "biblio_id=$biblio_id: " . $e->getMessage();
        echo "[ERROR] biblio_id=$biblio_id: " . $e->getMessage() . "\n";
    }
}

// ----------------------------------------------------------------------------
// 7. RINGKASAN
// ----------------------------------------------------------------------------
echo "\n============================================================\n";
echo " RINGKASAN\n";
echo "============================================================\n";
echo "Mode        : " . ($DRY_RUN ? "DRY-RUN" : "EXECUTE") . "\n";
echo "Total diproses : {$stats['total']}\n";
echo "Berhasil       : {$stats['inserted']}\n";
echo "Error          : {$stats['errors']}\n";
if (!empty($stats['error_list'])) {
    echo "\nDaftar error:\n";
    foreach ($stats['error_list'] as $e) {
        echo " - $e\n";
    }
}
echo "============================================================\n";

$slims->close();
$inlis->close();