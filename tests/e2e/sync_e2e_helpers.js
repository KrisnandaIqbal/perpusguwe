const fs = require('fs');
const os = require('os');
const path = require('path');
const { execFileSync } = require('child_process');
const { expect } = require('@playwright/test');

const PHP_BIN = process.env.PHP_BIN || 'C:\\laragon\\bin\\php\\php-8.3.30-Win32-vs16-x64\\php.exe';
const DB_HOST = process.env.E2E_DB_HOST || '127.0.0.1';
const DB_NAME = process.env.E2E_DB_NAME || 'inlislite_dev';
const DB_USER = process.env.E2E_DB_USER || 'root';
const DB_PASS = process.env.E2E_DB_PASS || '';

const PROJECT_ROOT = path.resolve(__dirname, '..', '..');
const BANNER_UPLOAD_DIR = path.join(PROJECT_ROOT, 'public', 'uploads', 'banner');

function runPhpJson(phpBody, payload = {}) {
  const payloadB64 = Buffer.from(JSON.stringify(payload), 'utf8').toString('base64');
  const script = `<?php
$payload = json_decode(base64_decode('${payloadB64}'), true) ?: [];
$pdo = new PDO(
    'mysql:host=${DB_HOST};dbname=${DB_NAME};charset=utf8mb4',
    '${DB_USER}',
    '${DB_PASS}',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);
${phpBody}
`;
  const raw = execFileSync(PHP_BIN, [], {
    input: script,
    encoding: 'utf8',
    windowsHide: true,
  });
  const trimmed = raw.replace(/^\uFEFF/, '').trim();
  const lines = trimmed.split(/\r?\n/);
  const lastLine = lines[lines.length - 1] || '{}';
  return JSON.parse(lastLine);
}

function uniqueSuffix(label = 'w') {
  const rand = Math.random().toString(36).slice(2, 7);
  const worker = process.env.TEST_PARALLEL_INDEX || '0';
  return `${label}${worker}_${Date.now().toString(36).slice(-4)}${rand}`;
}

/**
 * Resolve or provision member credentials for OPAC Anggota / Portal tests.
 * Never mutates existing `admin`, `inlislite`, `26102700001`, or `26102700002` rows.
 */
async function ensureMemberFixture(options = {}) {
  const {
    withLoan = true,
    active = 1,
    izin = 1,
    diumumkan = 0,
    forceEphemeral = false,
  } = options;

  const envMemberNo = process.env.E2E_MEMBER_NO || process.env.E2E_MEMBER_USER || '';
  const envMemberPass = process.env.E2E_MEMBER_PASS || '';

  if (!forceEphemeral && envMemberNo && envMemberPass && active === 1) {
    return {
      isEphemeral: false,
      memberNo: envMemberNo,
      password: envMemberPass,
      fullname: envMemberNo,
      cleanup: async () => {},
    };
  }

  const suffix = uniqueSuffix('m');
  const memberNo = `e2e_${suffix}`.slice(0, 28);
  const password = `E2ePass!${suffix}`;
  const fullname = `E2E Anggota ${suffix}`;
  const email = `${memberNo}@example.test`;

  const created = runPhpJson(
    `
    $hasIzin = (bool) $pdo->query("SHOW COLUMNS FROM members LIKE 'IzinPersonalisasi'")->fetch();
    $hasDiumumkan = (bool) $pdo->query("SHOW COLUMNS FROM members LIKE 'IzinPersonalisasiDiumumkan'")->fetch();

    $cols = ['MemberNo', 'Fullname', 'IdentityNo', 'Email', 'UnitKerja_id', 'StatusAnggota_id', 'active', 'IsKeranjang', 'CreateDate'];
    $vals = [':no', ':name', ':ident', ':email', 2, 3, ':active', 0, 'NOW()'];
    $params = [
        ':no' => $payload['memberNo'],
        ':name' => $payload['fullname'],
        ':ident' => '99' . substr(preg_replace('/[^0-9]/', '', md5($payload['memberNo'])), 0, 14),
        ':email' => $payload['email'],
        ':active' => (int) $payload['active'],
    ];

    if ($hasIzin) {
        $cols[] = 'IzinPersonalisasi';
        $vals[] = ':izin';
        $params[':izin'] = (int) $payload['izin'];
    }
    if ($hasDiumumkan) {
        $cols[] = 'IzinPersonalisasiDiumumkan';
        $vals[] = ':diumumkan';
        $params[':diumumkan'] = (int) $payload['diumumkan'];
    }

    $sql = 'INSERT INTO members (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $memberId = (int) $pdo->lastInsertId();

    $hash = password_hash($payload['password'], PASSWORD_BCRYPT);
    $uStmt = $pdo->prepare("
        INSERT INTO users (email, username, first_name, last_name, password_hash, active, category, member_id, branch_id, anggota, created_at, updated_at)
        VALUES (:email, :username, :fname, 'E2E', :hash, :active, 'anggota', :mid, 1, 1, NOW(), NOW())
    ");
    $uStmt->execute([
        ':email' => $payload['email'],
        ':username' => $payload['memberNo'],
        ':fname' => $payload['fullname'],
        ':hash' => $hash,
        ':active' => (int) $payload['active'],
        ':mid' => $memberId,
    ]);
    $userId = (int) $pdo->lastInsertId();

    $grp = $pdo->query("SELECT id FROM auth_groups WHERE name IN ('keanggotaan', 'anggota') ORDER BY id ASC LIMIT 1")->fetch();
    if ($grp) {
        $gStmt = $pdo->prepare("INSERT INTO auth_groups_users (group_id, user_id) VALUES (?, ?)");
        $gStmt->execute([(int) $grp['id'], $userId]);
    }

    $loanId = null;
    $loanItemId = null;
    if (!empty($payload['withLoan'])) {
        $colRow = $pdo->query("
            SELECT col.ID AS col_id, cat.ID AS cat_id
            FROM collections col
            JOIN catalogs cat ON cat.ID = col.Catalog_id
            WHERE cat.IsOPAC = 1
            ORDER BY col.ID ASC
            LIMIT 1
        ")->fetch();
        if ($colRow) {
            $lStmt = $pdo->prepare("
                INSERT INTO collectionloans (CollectionCount, LoanCount, Member_id, Branch_id, CreateDate, active)
                VALUES (1, 1, :mid, 1, NOW(), 1)
            ");
            $lStmt->execute([':mid' => $memberId]);
            $loanId = (int) $pdo->lastInsertId();

            $liStmt = $pdo->prepare("
                INSERT INTO collectionloanitems (CollectionLoan_id, LoanDate, DueDate, LoanStatus, Collection_id, member_id, CreateDate, Branch_id, active)
                VALUES (:lid, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'Loan', :cid, :mid, NOW(), 1, 1)
            ");
            $liStmt->execute([
                ':lid' => $loanId,
                ':cid' => (int) $colRow['col_id'],
                ':mid' => $memberId,
            ]);
            $loanItemId = (int) $pdo->lastInsertId();
        }
    }

    echo json_encode([
        'memberId' => $memberId,
        'userId' => $userId,
        'loanId' => $loanId,
        'loanItemId' => $loanItemId,
        'hasIzinColumn' => $hasIzin,
    ]);
    `,
    { memberNo, password, fullname, email, active, izin, diumumkan, withLoan }
  );

  return {
    isEphemeral: true,
    memberId: created.memberId,
    userId: created.userId,
    loanId: created.loanId,
    loanItemId: created.loanItemId,
    hasIzinColumn: created.hasIzinColumn,
    memberNo,
    password,
    fullname,
    email,
    cleanup: async () => {
      try {
        runPhpJson(
          `
          $mid = (int) $payload['memberId'];
          $uid = (int) $payload['userId'];
          if ($mid > 0) {
              $pdo->prepare("DELETE FROM collectionloanitems WHERE member_id = ?")->execute([$mid]);
              $pdo->prepare("DELETE FROM collectionloans WHERE Member_id = ?")->execute([$mid]);
              $hasSearchLog = (bool) $pdo->query("SHOW TABLES LIKE 'opac_search_log'")->fetch();
              if ($hasSearchLog) {
                  $pdo->prepare("DELETE FROM opac_search_log WHERE member_id = ?")->execute([$mid]);
              }
              $pdo->prepare("DELETE FROM members WHERE ID = ? AND MemberNo LIKE 'e2e_%'")->execute([$mid]);
          }
          if ($uid > 0) {
              $pdo->prepare("DELETE FROM auth_groups_users WHERE user_id = ?")->execute([$uid]);
              $pdo->prepare("DELETE FROM users WHERE id = ? AND username LIKE 'e2e_%'")->execute([$uid]);
          }
          echo json_encode(['ok' => true]);
          `,
          { memberId: created.memberId, userId: created.userId }
        );
      } catch (err) {
        console.warn('[sync_e2e_helpers] member cleanup warning:', err.message);
      }
    },
  };
}

/**
 * Resolve or provision staff admin credentials for CMS Banner / Dashboard tests.
 */
async function ensureAdminFixture(options = {}) {
  const { forceEphemeral = false } = options;
  const envAdminUser = process.env.E2E_ADMIN_USER || process.env.E2E_STAFF_USER || '';
  const envAdminPass = process.env.E2E_ADMIN_PASS || process.env.E2E_STAFF_PASS || '';

  if (!forceEphemeral && envAdminUser && envAdminPass) {
    return {
      isEphemeral: false,
      username: envAdminUser,
      password: envAdminPass,
      cleanup: async () => {},
    };
  }

  const suffix = uniqueSuffix('a');
  const username = `e2e_adm_${suffix}`.slice(0, 28);
  const password = `E2eAdm!${suffix}`;
  const email = `${username}@example.test`;

  const created = runPhpJson(
    `
    $hash = password_hash($payload['password'], PASSWORD_BCRYPT);
    $uStmt = $pdo->prepare("
        INSERT INTO users (email, username, first_name, last_name, password_hash, active, category, branch_id, admin, created_at, updated_at)
        VALUES (:email, :username, 'E2E', 'Admin', :hash, 1, 'admin', 1, 1, NOW(), NOW())
    ");
    $uStmt->execute([
        ':email' => $payload['email'],
        ':username' => $payload['username'],
        ':hash' => $hash,
    ]);
    $userId = (int) $pdo->lastInsertId();

    $grp = $pdo->query("SELECT id FROM auth_groups WHERE name = 'admin' ORDER BY id ASC LIMIT 1")->fetch();
    if ($grp) {
        $gStmt = $pdo->prepare("INSERT INTO auth_groups_users (group_id, user_id) VALUES (?, ?)");
        $gStmt->execute([(int) $grp['id'], $userId]);
    }
    echo json_encode(['userId' => $userId]);
    `,
    { username, password, email }
  );

  return {
    isEphemeral: true,
    userId: created.userId,
    username,
    password,
    email,
    cleanup: async () => {
      try {
        runPhpJson(
          `
          $uid = (int) $payload['userId'];
          if ($uid > 0) {
              $pdo->prepare("DELETE FROM auth_groups_users WHERE user_id = ?")->execute([$uid]);
              $pdo->prepare("DELETE FROM users WHERE id = ? AND username LIKE 'e2e_adm_%'")->execute([$uid]);
          }
          echo json_encode(['ok' => true]);
          `,
          { userId: created.userId }
        );
      } catch (err) {
        console.warn('[sync_e2e_helpers] admin cleanup warning:', err.message);
      }
    },
  };
}

function getMemberPersonalizationState(memberNo) {
  return runPhpJson(
    `
    $hasIzin = (bool) $pdo->query("SHOW COLUMNS FROM members LIKE 'IzinPersonalisasi'")->fetch();
    if (!$hasIzin) {
        echo json_encode(['hasColumn' => false]);
        exit;
    }
    $stmt = $pdo->prepare("SELECT ID, IzinPersonalisasi, IzinPersonalisasiDiumumkan FROM members WHERE MemberNo = ? LIMIT 1");
    $stmt->execute([$payload['memberNo']]);
    $row = $stmt->fetch();
    echo json_encode([
        'hasColumn' => true,
        'found' => (bool) $row,
        'izin' => $row ? (int) $row['IzinPersonalisasi'] : null,
        'diumumkan' => $row ? (int) $row['IzinPersonalisasiDiumumkan'] : null,
    ]);
    `,
    { memberNo }
  );
}

function setMemberPersonalizationState(memberNo, { izin = 1, diumumkan = 0 }) {
  return runPhpJson(
    `
    $hasIzin = (bool) $pdo->query("SHOW COLUMNS FROM members LIKE 'IzinPersonalisasi'")->fetch();
    if (!$hasIzin) {
        echo json_encode(['hasColumn' => false]);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE members SET IzinPersonalisasi = ?, IzinPersonalisasiDiumumkan = ? WHERE MemberNo = ?");
    $stmt->execute([(int) $payload['izin'], (int) $payload['diumumkan'], $payload['memberNo']]);
    echo json_encode(['hasColumn' => true, 'updated' => $stmt->rowCount()]);
    `,
    { memberNo, izin, diumumkan }
  );
}

/**
 * Query sample catalog IDs & sparse search terms directly from DB (read-only).
 */
function getCatalogFixtures() {
  return runPhpJson(
    `
    $sample = $pdo->query("
        SELECT c.ID, c.Title, c.Author, c.Subject, c.DeweyNo
        FROM catalogs c
        WHERE c.IsOPAC = 1
          AND EXISTS (SELECT 1 FROM collections col WHERE col.Catalog_id = c.ID)
        ORDER BY (c.ID = 36866) DESC, c.ID DESC
        LIMIT 1
    ")->fetch();
    if (!$sample) {
        $sample = $pdo->query("SELECT ID, Title, Author, Subject, DeweyNo FROM catalogs WHERE IsOPAC = 1 LIMIT 1")->fetch();
    }

    // Find a phrase returning 1..4 matches on OPAC search for sparse-result OneSearch tests.
    $sparseQuery = 'Himpunan Peraturan Menteri Ketenagakerjaan tahun 2016 Jilid III';
    $candidates = $pdo->query("
        SELECT Title FROM catalogs
        WHERE IsOPAC = 1 AND CHAR_LENGTH(Title) BETWEEN 18 AND 65
        ORDER BY ID DESC
        LIMIT 80
    ")->fetchAll();
    foreach ($candidates as $cand) {
        $t = trim($cand['Title']);
        if ($t === '' || strpbrk($t, '"%_') !== false || str_contains($t, chr(39)) || str_contains($t, chr(92))) {
            continue;
        }
        $st = $pdo->prepare("SELECT COUNT(*) FROM catalogs WHERE IsOPAC = 1 AND Title LIKE ?");
        $st->execute(['%' . $t . '%']);
        $cnt = (int) $st->fetchColumn();
        if ($cnt >= 1 && $cnt <= 4) {
            $sparseQuery = $t;
            break;
        }
    }

    $berandaBanner = $pdo->query("SELECT id, title, file_cover, active FROM t_banner WHERE category = 'Beranda' ORDER BY id DESC LIMIT 1")->fetch();

    echo json_encode([
        'sampleId' => $sample ? (int) $sample['ID'] : 36866,
        'sampleTitle' => $sample ? $sample['Title'] : '',
        'sparseQuery' => $sparseQuery,
        'berandaBannerId' => $berandaBanner ? (int) $berandaBanner['id'] : null,
    ]);
    `
  );
}

function listTestBannersByPrefix(prefix = 'E2E Test Banner') {
  return runPhpJson(
    `
    $stmt = $pdo->prepare("SELECT id, title, file_cover, active, category FROM t_banner WHERE title LIKE ? ORDER BY id DESC");
    $stmt->execute([$payload['prefix'] . '%']);
    echo json_encode(['rows' => $stmt->fetchAll()]);
    `,
    { prefix }
  ).rows || [];
}

function activateBannersByPrefix(prefix = 'E2E Test Banner') {
  return runPhpJson(
    `
    $stmt = $pdo->prepare("UPDATE t_banner SET active = 1 WHERE title LIKE ?");
    $stmt->execute([$payload['prefix'] . '%']);
    echo json_encode(['updated' => $stmt->rowCount()]);
    `,
    { prefix }
  );
}

function cleanupBannersByPrefix(prefix = 'E2E Test Banner') {
  const res = runPhpJson(
    `
    $stmt = $pdo->prepare("SELECT id, file_cover FROM t_banner WHERE title LIKE ?");
    $stmt->execute([$payload['prefix'] . '%']);
    $rows = $stmt->fetchAll();
    $del = $pdo->prepare("DELETE FROM t_banner WHERE id = ?");
    $files = [];
    foreach ($rows as $r) {
        if (!empty($r['file_cover'])) {
            $files[] = $r['file_cover'];
        }
        $del->execute([(int) $r['id']]);
    }
    echo json_encode(['deleted' => count($rows), 'files' => $files]);
    `,
    { prefix }
  );
  for (const file of res.files || []) {
    const full = path.join(BANNER_UPLOAD_DIR, path.basename(file));
    if (fs.existsSync(full)) {
      try {
        fs.unlinkSync(full);
      } catch (_) {}
    }
  }
  return res.deleted || 0;
}

function deactivateActiveBerandaBanners() {
  const res = runPhpJson(
    `
    $rows = $pdo->query("SELECT id FROM t_banner WHERE category = 'Beranda' AND active = 1")->fetchAll();
    $ids = array_map(fn($r) => (int) $r['id'], $rows);
    if (!empty($ids)) {
        $in = implode(',', $ids);
        $pdo->exec("UPDATE t_banner SET active = 0 WHERE id IN ($in)");
    }
    echo json_encode(['ids' => $ids]);
    `
  );
  return res.ids || [];
}

function restoreBerandaBanners(ids = []) {
  if (!ids || ids.length === 0) return;
  runPhpJson(
    `
    $clean = array_values(array_filter(array_map('intval', $payload['ids']), fn($v) => $v > 0));
    if (!empty($clean)) {
        $in = implode(',', $clean);
        $pdo->exec("UPDATE t_banner SET active = 1 WHERE id IN ($in)");
    }
    echo json_encode(['restored' => count($clean)]);
    `,
    { ids }
  );
}

/**
 * Generate real binary JPEG/PNG/TXT upload files in os.tmpdir() using PHP GD.
 */
function createTempUploadFiles() {
  const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'perpus-e2e-'));
  const largeJpg = path.join(dir, 'hero-1600x1200.jpg');
  const smallPng = path.join(dir, 'small-400x300.png');
  const invalidTxt = path.join(dir, 'invalid-banner.txt');

  runPhpJson(
    `
    $im1 = imagecreatetruecolor(1600, 1200);
    $bg1 = imagecolorallocate($im1, 15, 63, 180);
    $fg1 = imagecolorallocate($im1, 240, 244, 255);
    imagefilledrectangle($im1, 0, 0, 1600, 1200, $bg1);
    imagefilledrectangle($im1, 120, 120, 1480, 1080, $fg1);
    imagejpeg($im1, $payload['largeJpg'], 85);
    imagedestroy($im1);

    $im2 = imagecreatetruecolor(400, 300);
    $bg2 = imagecolorallocate($im2, 30, 64, 175);
    imagefilledrectangle($im2, 0, 0, 400, 300, $bg2);
    imagepng($im2, $payload['smallPng']);
    imagedestroy($im2);

    echo json_encode(['ok' => true]);
    `,
    { largeJpg, smallPng }
  );

  fs.writeFileSync(invalidTxt, 'Not an image file for E2E negative MIME validation.\n', 'utf8');

  return {
    dir,
    largeJpg,
    smallPng,
    invalidTxt,
    cleanup: () => {
      try {
        fs.rmSync(dir, { recursive: true, force: true });
      } catch (_) {}
    },
  };
}

function attachRuntimeMonitors(page) {
  const consoleErrors = [];
  const pageErrors = [];
  const serverErrors = [];
  const dialogs = [];

  page.on('console', (msg) => {
    if (msg.type() === 'error') {
      consoleErrors.push(msg.text());
    }
  });
  page.on('pageerror', (err) => {
    pageErrors.push(err.message);
  });
  page.on('response', (res) => {
    if (res.status() >= 500) {
      serverErrors.push(`${res.status()} ${res.url()}`);
    }
  });
  page.on('dialog', async (dialog) => {
    dialogs.push(dialog.message());
    await dialog.dismiss().catch(() => {});
  });

  return { consoleErrors, pageErrors, serverErrors, dialogs };
}

async function waitForPageReady(page) {
  await page.waitForLoadState('domcontentloaded');
  await page.evaluate(async () => {
    if (document.fonts && document.fonts.ready) {
      await Promise.race([
        document.fonts.ready,
        new Promise((resolve) => setTimeout(resolve, 1500)),
      ]);
    }
  }).catch(() => {});
}

async function assertNoHorizontalOverflow(page, label = 'page') {
  const metrics = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
  }));
  expect(
    metrics.scrollWidth,
    `Horizontal overflow on ${label}: scrollWidth=${metrics.scrollWidth} > innerWidth=${metrics.innerWidth}`
  ).toBeLessThanOrEqual(metrics.innerWidth);
}

async function loginViaMythAuth(page, { login, password, role = 'anggota' }) {
  const res = await page.goto('/login', { waitUntil: 'domcontentloaded' });
  expect(res && res.status()).toBeLessThan(400);

  if (role === 'admin') {
    const adminTab = page.locator('#tabAdmin');
    if (await adminTab.count()) {
      await adminTab.click();
    }
  } else {
    const anggotaTab = page.locator('#tabAnggota');
    if (await anggotaTab.count()) {
      await anggotaTab.click();
    }
  }

  await page.locator('input[name="login"]').fill(login);
  const pwInput = page.locator('input[name="password"]');
  if (role === 'admin' && await pwInput.isVisible()) {
    await pwInput.fill(password);
  }

  const submitBtn = page.locator('#loginBtn, button[type="submit"]').first();
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    submitBtn.click(),
  ]);
}

async function loginViaOpacMasuk(page, { username, password }) {
  const res = await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
  expect(res && res.status()).toBeLessThan(400);
  await page.locator('#username, input[name="username"]').first().fill(username);
  await page.locator('#password, input[name="password"]').first().fill(password);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.locator('form[action*="opac/masuk"] button[type="submit"]').click(),
  ]);
}

module.exports = {
  PHP_BIN,
  runPhpJson,
  uniqueSuffix,
  ensureMemberFixture,
  ensureAdminFixture,
  getMemberPersonalizationState,
  setMemberPersonalizationState,
  getCatalogFixtures,
  listTestBannersByPrefix,
  activateBannersByPrefix,
  cleanupBannersByPrefix,
  deactivateActiveBerandaBanners,
  restoreBerandaBanners,
  createTempUploadFiles,
  attachRuntimeMonitors,
  waitForPageReady,
  assertNoHorizontalOverflow,
  loginViaMythAuth,
  loginViaOpacMasuk,
};
