<?php
/**
 * ============================================================================
 * SCRIPT MIGRASI EKSEMPLAR: SLiMS (item) -> INLISLite (collections)
 * ============================================================================
 *
 * PRASYARAT: Script migrasi katalog (migrasi_slims_ke_inlislite.php) HARUS
 * sudah dijalankan lebih dulu untuk biblio_id yang bersangkutan, karena
 * script ini butuh mencari `catalogs.ID` lewat BIBID (BIB000001 dst).
 *
 * CARA PAKAI (dijalankan lewat SSH, dari folder root inlislite):
 *
 *   php migrasi_eksemplar_slims_ke_inlislite.php --dry-run
 *   php migrasi_eksemplar_slims_ke_inlislite.php --dry-run --item-code=NAKER08638L
 *   php migrasi_eksemplar_slims_ke_inlislite.php --dry-run --limit=20
 *
 *   php migrasi_eksemplar_slims_ke_inlislite.php --execute --item-code=NAKER08638L
 *   php migrasi_eksemplar_slims_ke_inlislite.php --execute --limit=50 --offset=0
 *   php migrasi_eksemplar_slims_ke_inlislite.php --execute
 *
 * CATATAN:
 * - NomorBarcode DAN NoInduk diisi sama persis dari item_code SLiMS.
 * - Idempotent: dicocokkan lewat NomorBarcode (kolom UNIQUE). Kalau sudah
 *   ada, baris lama diganti (hapus lalu insert ulang), bukan menumpuk.
 * - Eksemplar yang biblio_id-nya BELUM ada di catalogs (BIBID belum
 *   dimigrasi) akan di-skip dan dicatat sebagai error, supaya bisa
 *   dijalankan ulang belakangan setelah katalog induknya beres.
 * ============================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

// ----------------------------------------------------------------------------
// 1. PARSE ARGUMEN CLI
// ----------------------------------------------------------------------------
$options = getopt('', ['dry-run', 'execute', 'limit::', 'offset::', 'item-code::']);
$DRY_RUN   = !isset($options['execute']);
$LIMIT     = isset($options['limit']) ? (int) $options['limit'] : null;
$OFFSET    = isset($options['offset']) ? (int) $options['offset'] : 0;
$ONLY_CODE = isset($options['item-code']) ? $options['item-code'] : null;

echo "============================================================\n";
echo " MIGRASI EKSEMPLAR: SLiMS (item) -> INLISLite (collections)\n";
echo " Mode: " . ($DRY_RUN ? "DRY-RUN (preview saja)" : "EXECUTE (akan mengubah database!)") . "\n";
if ($ONLY_CODE) echo " Filter: hanya item_code = $ONLY_CODE\n";
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
// 2. BACA KREDENSIAL & KONEKSI
// ----------------------------------------------------------------------------
function parse_env($path)
{
    if (!file_exists($path)) die("ERROR: file .env tidak ditemukan di: $path\n");
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

$env = parse_env(__DIR__ . '/.env');
$DB_HOST = $env['database.default.hostname'] ?? 'localhost';
$DB_USER = $env['database.default.username'] ?? null;
$DB_PASS = $env['database.default.password'] ?? null;
$DB_INLISLITE = $env['database.default.database'] ?? 'perpustakaan_inlislite';
$DB_SLIMS = 'perpustakaan_kemnaker';

if (!$DB_USER || $DB_PASS === null) die("ERROR: gagal membaca kredensial dari .env\n");

$slims = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_SLIMS);
if ($slims->connect_error) die("Gagal konek SLiMS: " . $slims->connect_error . "\n");
$slims->set_charset('latin1');

$inlis = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_INLISLITE);
if ($inlis->connect_error) die("Gagal konek INLISLite: " . $inlis->connect_error . "\n");
$inlis->set_charset('utf8mb4');

echo "Koneksi database OK.\n\n";

// ----------------------------------------------------------------------------
// 3. HELPERS
// ----------------------------------------------------------------------------
function fix_encoding($str)
{
    if ($str === null) return null;
    $str = (string) $str;
    if ($str === '') return $str;

    if (mb_check_encoding($str, 'UTF-8')) {
        return $str;
    }

    $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $str);
    if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
        return $converted;
    }

    $converted2 = @mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
    if ($converted2 !== false && mb_check_encoding($converted2, 'UTF-8')) {
        return $converted2;
    }

    $safe = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
    if ($safe !== false && mb_check_encoding($safe, 'UTF-8')) {
        return $safe;
    }
    return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $str);
}

function make_bibid($biblio_id)
{
    return 'BIB' . str_pad($biblio_id, 6, '0', STR_PAD_LEFT);
}

// Mapping status SLiMS -> Status_id INLISLite
// '0'/'' /NULL = Tersedia(1), RSK = Rusak(3), EB = Eksemplar Berlebih(12), TR = Tidak Relevan(13)
$STATUS_MAP = [
    'RSK' => 3,
    'EB'  => 12,
    'TR'  => 13,
];
function map_status($code, $map)
{
    $code = trim((string) $code);
    if ($code === '' || $code === '0') return 1; // Tersedia
    return $map[$code] ?? 1; // fallback aman ke Tersedia kalau kode tak dikenal
}

// Mapping coll_type_id SLiMS -> Category_id INLISLite (collectioncategorys.ID)
$CATEGORY_MAP = [
    4 => 7,  // Umum -> CL1 Koleksi Umum
    1 => 13, // Ketenagakerjaan -> KTK
    3 => 14, // Fiction -> FIC
];
function map_category($collTypeId, $map)
{
    return $map[(int) $collTypeId] ?? 1; // default: 000 Tidak Diketahui
}

// Nilai tetap (tidak ada data SLiMS untuk dipetakan / sudah disepakati diseragamkan)
const MEDIA_ID_DEFAULT    = 2;  // Buku
const SOURCE_ID_DEFAULT   = 2;  // Hadiah/Hibah
const RULE_ID_DEFAULT     = 1;  // Dapat dipinjam
const LOCATION_LIBRARY_ID = 1;  // satu-satunya lokasi perpustakaan yang ada

// Cache lookup Catalog_id (BIBID -> catalogs.ID) & Location_id (Code -> ID) supaya tidak query berulang
$catalogIdCache  = [];
$locationIdCache = [];

function get_catalog_id($inlis, $bibid, &$cache)
{
    if (array_key_exists($bibid, $cache)) return $cache[$bibid];
    $stmt = $inlis->prepare("SELECT ID FROM catalogs WHERE BIBID = ?");
    $stmt->bind_param('s', $bibid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $cache[$bibid] = $row ? (int) $row['ID'] : null;
    return $cache[$bibid];
}

function get_location_id($inlis, $code, &$cache)
{
    $code = trim((string) $code);
    if ($code === '') $code = 'SL'; // default lokasi utama untuk data kosong
    if (array_key_exists($code, $cache)) return $cache[$code];
    $stmt = $inlis->prepare("SELECT ID FROM locations WHERE Code = ?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $cache[$code] = $row ? (int) $row['ID'] : null;
    return $cache[$code];
}

$stats = ['total' => 0, 'inserted' => 0, 'skipped' => 0, 'errors' => 0, 'error_list' => []];

// ----------------------------------------------------------------------------
// 4. AMBIL DAFTAR ITEM
// ----------------------------------------------------------------------------
$where = '1=1';
if ($ONLY_CODE) {
    $stmtEsc = $slims->real_escape_string($ONLY_CODE);
    $where = "item_code = '{$stmtEsc}'";
}
$limitSql = '';
if ($LIMIT !== null) {
    $limitSql = "LIMIT " . (int) $OFFSET . ", " . (int) $LIMIT;
}

$sql = "SELECT * FROM item WHERE $where ORDER BY item_id ASC $limitSql";
$res = $slims->query($sql);
if (!$res) die("Query daftar item gagal: " . $slims->error . "\n");

$items = $res->fetch_all(MYSQLI_ASSOC);
echo "Akan memproses " . count($items) . " eksemplar.\n\n";

// ----------------------------------------------------------------------------
// 5. PROSES SATU PER SATU
// ----------------------------------------------------------------------------
foreach ($items as $item) {
    $stats['total']++;
    $itemCode = trim($item['item_code']);

    try {
        if ($itemCode === '') {
            throw new Exception("item_id={$item['item_id']}: item_code kosong, dilewati");
        }

        $bibid = make_bibid((int) $item['biblio_id']);
        $catalogId = get_catalog_id($inlis, $bibid, $catalogIdCache);
        if (!$catalogId) {
            throw new Exception("item_code=$itemCode: induk katalog $bibid belum ada di INLISLite (migrasi katalog dulu)");
        }

        $locationCode = $item['location_id'];
        $locationId = get_location_id($inlis, $locationCode, $locationIdCache);
        if (!$locationId) {
            throw new Exception("item_code=$itemCode: kode lokasi '$locationCode' tidak ditemukan di INLISLite");
        }

        $statusId = map_status($item['item_status_id'], $STATUS_MAP);
        $categoryId = map_category($item['coll_type_id'], $CATEGORY_MAP);
        $locLibId = LOCATION_LIBRARY_ID;
        $mediaId  = MEDIA_ID_DEFAULT;
        $sourceId = SOURCE_ID_DEFAULT;
        $ruleId   = RULE_ID_DEFAULT;
        $callNumber = fix_encoding($item['call_number'] ?? '');
        $price = !empty($item['price']) ? (int) $item['price'] : null;
        $receivedDate = !empty($item['received_date']) && $item['received_date'] !== '0000-00-00'
            ? $item['received_date'] : null;
        $inputDate = !empty($item['input_date']) && $item['input_date'] !== '0000-00-00 00:00:00'
            ? $item['input_date'] : date('Y-m-d H:i:s');

        // Cek existing (idempotent, by NomorBarcode)
        $stmtCheck = $inlis->prepare("SELECT ID FROM collections WHERE NomorBarcode = ?");
        $stmtCheck->bind_param('s', $itemCode);
        $stmtCheck->execute();
        $existing = $stmtCheck->get_result()->fetch_assoc();
        $stmtCheck->close();
        $oldId = $existing['ID'] ?? null;

        if ($DRY_RUN) {
            $stats['inserted']++;
            $ket = $oldId ? "akan MENGGANTI ID=$oldId" : "akan jadi baris BARU";
            echo "[DRY-RUN] $itemCode (biblio_id={$item['biblio_id']} -> Catalog_id=$catalogId, Location=$locationCode->$locationId, Status=$statusId, Category=$categoryId) -- $ket\n";
            continue;
        }

        $inlis->begin_transaction();

        if ($oldId) {
            $del = $inlis->prepare("DELETE FROM collections WHERE ID = ?");
            $del->bind_param('i', $oldId);
            $del->execute();
            $del->close();
        }

        $stmtIns = $inlis->prepare("
            INSERT INTO collections
            (NomorBarcode, NoInduk, CallNumber, Catalog_id, Location_id, Location_Library_id, Status_id,
             Category_id, Media_id, Source_id, Rule_id,
             Price, TanggalPengadaan, CreateDate, CreateTerminal, ISOPAC, ISDRM, active)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?, 'SLIMS_MIGRATION', 1, 0, 1)
        ");
        $stmtIns->bind_param(
            'sssiiiiiiiiiss',
            $itemCode, $itemCode, $callNumber, $catalogId, $locationId, $locLibId, $statusId,
            $categoryId, $mediaId, $sourceId, $ruleId,
            $price, $receivedDate, $inputDate
        );
        $stmtIns->execute();
        $newId = $inlis->insert_id;
        $stmtIns->close();

        $inlis->commit();
        $stats['inserted']++;
        echo "[OK] $itemCode -> collections.ID=$newId (Catalog_id=$catalogId)\n";
    } catch (Throwable $e) {
        if (!$DRY_RUN) $inlis->rollback();
        $stats['errors']++;
        $stats['error_list'][] = $e->getMessage();
        echo "[ERROR] " . $e->getMessage() . "\n";
    }
}

// ----------------------------------------------------------------------------
// 6. RINGKASAN
// ----------------------------------------------------------------------------
echo "\n============================================================\n";
echo " RINGKASAN\n";
echo "============================================================\n";
echo "Mode        : " . ($DRY_RUN ? "DRY-RUN" : "EXECUTE") . "\n";
echo "Total diproses : {$stats['total']}\n";
echo "Berhasil       : {$stats['inserted']}\n";
echo "Error          : {$stats['errors']}\n";
if (!empty($stats['error_list'])) {
    echo "\nDaftar error (maks 50 ditampilkan):\n";
    foreach (array_slice($stats['error_list'], 0, 50) as $e) {
        echo " - $e\n";
    }
}
echo "============================================================\n";

$slims->close();
$inlis->close();