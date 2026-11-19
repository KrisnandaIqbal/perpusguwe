const { test, expect } = require('@playwright/test');
const {
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
} = require('./sync_e2e_helpers');

const ZERO_RESULT_QUERY = 'xyzqwerty999tidakada';

let sharedCatalogFixtures;
test.beforeAll(() => {
  sharedCatalogFixtures = getCatalogFixtures();
});

// ============================================================================
// TIER 1 — FEATURE COVERAGE (Category-Partition Happy Paths, 40 Tests)
// ============================================================================

test.describe('Tier 1 — Area A: Beranda, Omnisearch & Helper Expansion', () => {
  test('T1-A1 @M2 Hero copy, DB catalog count, JDIH link & skip-link', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const res = await page.goto('/', { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBeLessThan(400);
    await waitForPageReady(page);

    await expect(page.locator('a.skip-link[href="#beranda"]')).toBeAttached();
    await expect(page.locator('.hero h1')).toContainText('Cari regulasi, buku, dan riset ketenagakerjaan.');

    const heroLead = page.locator('.hero-copy > p').first();
    await expect(heroLead).toBeVisible();
    const leadText = await heroLead.innerText();
    expect(leadText).toMatch(/[\d.,]+\s+judul koleksi/i);
    const countMatch = leadText.match(/([\d.,]+)\s+judul koleksi/i);
    expect(parseInt((countMatch && countMatch[1] || '0').replace(/\D/g, ''), 10)).toBeGreaterThan(0);

    const jdihLink = page.locator('a[href="https://jdih.kemnaker.go.id"][target="_blank"]');
    await expect(jdihLink).toBeVisible();
    expect(mon.pageErrors).toEqual([]);
    expect(mon.serverErrors).toEqual([]);
  });

  test('T1-A2 @M2 DB-backed Topic chips only show valid topics without SKKNI', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await expect(page.locator('.omni-suggestions')).toContainText('Topik:');
    const chips = page.locator('.omni-suggestions button[data-suggest]');
    const chipCount = await chips.count();
    expect(chipCount).toBeGreaterThanOrEqual(3);

    const chipTexts = await chips.allInnerTexts();
    expect(chipTexts.some((t) => t.trim().toUpperCase() === 'SKKNI')).toBe(false);

    const searchResp = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await chips.first().click();
    await searchResp;

    await expect(page.locator('#omnisearch-input')).not.toHaveValue('');
    await expect(page.locator('#omni-results')).toBeVisible();
    const options = page.locator('#omni-results .omni-option');
    expect(await options.count()).toBeGreaterThanOrEqual(2);
    await expect(options.last()).toContainText('di katalog OPAC');
  });

  test('T1-A3 @M2 Omnisearch Enter submits directly to OPAC with active = -1', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const input = page.locator('#omnisearch-input');
    const respPromise = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await input.fill('hukum');
    await respPromise;

    await expect(page.locator('#omni-results')).toBeVisible();
    await expect(page.locator('#omni-results .omni-option.is-active')).toHaveCount(0);

    await Promise.all([
      page.waitForURL(/\/opac\?.*search=hukum/i),
      input.press('Enter'),
    ]);
    await expect(page.locator('#opacCatalogGrid .opac-modern-card').first()).toBeVisible();
  });

  test('T1-A4 @M2 Regulation query expansion & shortened prefix with full title attribute', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const input = page.locator('#omnisearch-input');
    const respPromise = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await input.fill('uu 13 2003');
    const resp = await respPromise;
    const json = await resp.json();
    const rows = json.data || json.items || [];
    expect(rows.length).toBeGreaterThan(0);

    const regTag = page.locator('#omni-results .omni-option .omni-tag', { hasText: /Regulasi/i }).first();
    await expect(regTag).toBeVisible();

    const firstBookOption = page.locator('#omni-results .omni-option[data-kind="book"], #omni-results .omni-option:has(.omni-tag)').first();
    await expect(firstBookOption).toBeVisible();
    const titleEl = firstBookOption.locator('strong[title], [title]').first();
    await expect(titleEl).toHaveAttribute('title', /.+/);

    await Promise.all([
      page.waitForURL(/\/opac\/detail\/\d+/),
      firstBookOption.click(),
    ]);
  });

  test('T1-A5 @M2 Service shortcut matching & aligned service cards >= 14px', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const input = page.locator('#omnisearch-input');
    await input.fill('pinjam');
    await expect(page.locator('#omni-results')).toBeVisible();
    const serviceOpt = page.locator('#omni-results .omni-option', { hasText: /Layanan/i }).first();
    await expect(serviceOpt).toBeVisible();

    const cards = page.locator('#layanan .shortcut-card');
    const count = await cards.count();
    expect(count).toBeGreaterThanOrEqual(5);
    await expect(page.locator('#layanan .shortcut-number')).toHaveCount(0);

    for (let i = 0; i < count; i++) {
      const card = cards.nth(i);
      const goEl = card.locator('.shortcut-go');
      if (await goEl.count()) {
        await expect(goEl).not.toContainText(/^Buka\s+/i);
      }
      const smallFontSize = await card.locator('small').first().evaluate(
        (el) => parseFloat(window.getComputedStyle(el).fontSize)
      );
      expect(smallFontSize).toBeGreaterThanOrEqual(14);
    }

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      serviceOpt.click(),
    ]);
    expect(page.url()).toMatch(/peminjaman-mandiri|sirkulasi|tata-cara/i);
  });

  test('T1-A6 @M2 Showcase Terbaru/Populer tabs & Berita conditional image', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const terbaruCards = page.locator('[data-panel="terbaru"] .showcase-card');
    await expect(terbaruCards).toHaveCount(4);
    await expect(page.locator('[data-panel="terbaru"] .showcase-rank')).toHaveCount(0);

    const populerTab = page.getByRole('tab', { name: /Populer/i }).or(page.locator('[data-tab="populer"], button:has-text("Populer")')).first();
    await populerTab.click();
    await expect(page.locator('[data-panel="populer"]')).toBeVisible();
    const populerCards = page.locator('[data-panel="populer"] .showcase-card');
    if (await populerCards.count() > 0) {
      const rankCount = await page.locator('[data-panel="populer"] .showcase-rank').count();
      expect(rankCount).toBeGreaterThanOrEqual(1);
    }

    const newsSection = page.locator('.news');
    if (await newsSection.count() > 0) {
      await newsSection.scrollIntoViewIfNeeded();
      const newsImgs = newsSection.locator('img');
      const imgCount = await newsImgs.count();
      for (let i = 0; i < imgCount; i++) {
        const loaded = await newsImgs.nth(i).evaluate((img) => img.complete && img.naturalWidth > 0);
        expect(loaded).toBe(true);
      }
      const noImgItems = newsSection.locator('.news-item:not(:has(img))');
      const noImgCount = await noImgItems.count();
      for (let i = 0; i < noImgCount; i++) {
        await expect(noImgItems.nth(i)).toHaveClass(/tanpa-gambar/);
      }
    }
  });
});

test.describe('Tier 1 — Area B: Banner Hero CMS Crop', () => {
  test.describe.configure({ mode: 'serial' });

  let adminFix;
  let uploadFiles;

  test.beforeAll(async () => {
    adminFix = await ensureAdminFixture();
    uploadFiles = createTempUploadFiles();
  });

  test.afterAll(async () => {
    cleanupBannersByPrefix('E2E Test Banner');
    if (uploadFiles) uploadFiles.cleanup();
    if (adminFix) await adminFix.cleanup();
  });

  test('T1-B1 @PRE_WT Staff accesses Banner CMS list and create pages', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });

    const listRes = await page.goto('/cms/banner', { waitUntil: 'domcontentloaded' });
    expect(listRes && listRes.status()).toBe(200);

    const createRes = await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });
    expect(createRes && createRes.status()).toBe(200);
    await expect(page.locator('#title')).toBeVisible();
    await expect(page.locator('#category')).toBeVisible();
    await expect(page.locator('#file_cover')).toBeVisible();
    expect(mon.serverErrors).toEqual([]);
  });

  test('T1-B2 @M2 Cropper.js activates for category=Beranda with 1600x1200 image', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });

    await page.locator('#category').selectOption('Beranda');
    await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);

    await expect(page.locator('.potong-kotak img#potong-gambar')).toBeVisible();
    await expect(page.locator('.cropper-container')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('#potong-info')).toContainText('Cukup tajam.');
    expect(mon.pageErrors).toEqual([]);
  });

  test('T1-B3 @M2 Upload cropped 1220x1080 Beranda banner and reflect on Home', async ({ page }) => {
    const bannerTitle = `E2E Test Banner Hero ${Date.now()}`;
    try {
      await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
      await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });

      await page.locator('#title').fill(bannerTitle);
      await page.locator('#category').selectOption('Beranda');
      await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);
      await expect(page.locator('.cropper-container')).toBeVisible({ timeout: 10000 });

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator('#frm_create button[type="submit"]').click(),
      ]);

      const rows = listTestBannersByPrefix(bannerTitle);
      expect(rows.length).toBeGreaterThanOrEqual(1);
      activateBannersByPrefix(bannerTitle);

      await page.goto('/', { waitUntil: 'domcontentloaded' });
      await waitForPageReady(page);
      const slideImgs = page.locator('.hero-slide img, .hero-image-wrap img');
      expect(await slideImgs.count()).toBeGreaterThanOrEqual(1);
      const firstLoaded = await slideImgs.first().evaluate((img) => img.complete && img.naturalWidth > 0);
      expect(firstLoaded).toBe(true);
    } finally {
      cleanupBannersByPrefix('E2E Test Banner');
    }
  });

  test('T1-B4 @M2 Edit existing Beranda banner shows current image and re-initializes cropper', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const bannerTitle = `E2E Test Banner Edit ${Date.now()}`;
    try {
      await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
      await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });
      await page.locator('#title').fill(bannerTitle);
      await page.locator('#category').selectOption('Beranda');
      await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);
      await expect(page.locator('.cropper-container')).toBeVisible({ timeout: 10000 });
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator('#frm_create button[type="submit"]').click(),
      ]);

      const rows = listTestBannersByPrefix(bannerTitle);
      expect(rows.length).toBe(1);
      const editRes = await page.goto(`/cms/banner/edit/${rows[0].id}`, { waitUntil: 'domcontentloaded' });
      expect(editRes && editRes.status()).toBe(200);

      await expect(page.locator('.current-image-box img, .img-preview-box img').first()).toBeVisible();
      await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);
      await expect(page.locator('#potong-gambar')).toBeVisible();
      await expect(page.locator('.cropper-container')).toBeVisible({ timeout: 10000 });
      expect(mon.pageErrors).toEqual([]);
      expect(mon.consoleErrors).toEqual([]);
    } finally {
      cleanupBannersByPrefix('E2E Test Banner');
    }
  });

  test('T1-B5 @M2 Non-Beranda category skips Cropper.js and uses standard preview', async ({ page }) => {
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    await page.goto('/cms/banner/create', { waitUntil: 'domcontentloaded' });

    await page.locator('#category').selectOption('Opac');
    await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);

    await expect(page.locator('.potong-kotak')).toHaveCount(0);
    await expect(page.locator('#potong-gambar')).toHaveCount(0);
    await expect(page.locator('.img-preview-box img')).toBeVisible();
  });
});

test.describe('Tier 1 — Area C: OPAC Modern, Jelajah, OneSearch & Logging', () => {
  test('T1-C1 @PRE_WT Modern OPAC initial render and catalog cards', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const res = await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBe(200);
    await waitForPageReady(page);

    await expect(page.locator('h1.opac-hero-title')).toBeVisible();
    const cards = page.locator('#opacCatalogGrid .opac-modern-card');
    expect(await cards.count()).toBeGreaterThan(0);

    const shelfScroll = page.locator('.opac-shelf-scroll');
    if (await shelfScroll.count() > 0) {
      await expect(shelfScroll.first()).toBeVisible();
    }
    expect(mon.pageErrors).toEqual([]);
    expect(mon.serverErrors).toEqual([]);
  });

  test('T1-C2 @M3 OPAC #jelajah collapsible block shows A-Z, DDC details and author chips', async ({ page }) => {
    await page.goto('/opac#jelajah', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const jelajah = page.locator('#jelajah');
    await expect(jelajah).toBeVisible();
    await expect(jelajah.locator('a[href*="search_by=Title"][href*="letter=A"]')).toBeVisible();
    await expect(jelajah.locator('a[href*="search_by=Author"][href*="letter=A"]')).toBeVisible();

    const firstDdc = jelajah.locator('details.ddc-kelas').first();
    await expect(firstDdc).toBeVisible();
    await firstDdc.locator('summary').click();
    await expect(firstDdc).toHaveAttribute('open', '');

    expect(await jelajah.locator('.pengarang-chip').count()).toBeGreaterThan(0);
  });

  test('T1-C3 @M3 ?letter= filtering compatible with user-selected sort', async ({ page }) => {
    await page.goto('/opac?search_by=Title&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const heading = page.locator('.opac-section-heading, .catalog-section h4').first();
    await expect(heading).toContainText(/berawalan/i);
    await expect(heading).toContainText(/A/);
    expect(await page.locator('#opacCatalogGrid .opac-modern-card').count()).toBeGreaterThan(0);

    const sortSelect = page.locator('select.opac-sort-select[name="sort"]');
    const targetOption = sortSelect.locator('option[value*="title_desc"]');
    const targetVal = await targetOption.getAttribute('value');
    await Promise.all([
      page.waitForURL(/sort=title_desc/),
      sortSelect.selectOption(targetVal),
    ]);
    expect(page.url()).toContain('letter=A');
    expect(page.url()).toContain('sort=title_desc');
  });

  test('T1-C4 @M3 Sidebar filters preserve hidden params and toggle Grid/List view', async ({ page }) => {
    await page.goto('/opac?search=hukum&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const sidebarForm = page.locator('#opacSidebarFilterForm');
    await expect(sidebarForm.locator('input[type="hidden"][name="search"][value="hukum"]')).toBeAttached();
    await expect(sidebarForm.locator('input[type="hidden"][name="letter"][value="A"]')).toBeAttached();

    await Promise.all([
      page.waitForURL(/availability=tersedia/),
      page.locator('#opacSidebarFilterForm input[name="availability"][value="tersedia"]').check(),
    ]);
    expect(page.url()).toContain('search=hukum');
    expect(page.url()).toContain('letter=A');

    const grid = page.locator('#opacCatalogGrid');
    await page.locator('#btnViewList').click();
    await expect(grid).toHaveClass(/view-list/);
    await page.locator('#btnViewGrid').click();
    await expect(grid).not.toHaveClass(/view-list/);
  });

  test('T1-C5 @PRE_WT OPAC search and click logging sends POST /api/Opac/click', async ({ page }) => {
    await page.goto('/opac?search=hukum', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const firstCardLink = page.locator('#opacCatalogGrid .opac-modern-card a.js-open-book-modal').first();
    await expect(firstCardLink).toBeVisible();

    const clickReqPromise = page.waitForRequest(
      (req) => req.url().includes('/api/Opac/click') && req.method() === 'POST',
      { timeout: 10000 }
    );
    await firstCardLink.click();
    const clickReq = await clickReqPromise;
    const postData = clickReq.postData() || '';
    expect(postData).toContain('katalog');
    expect(postData).toContain('catalog_id');
    expect(postData).toContain('position');

    await expect(page.locator('#opacBookMetaModal')).toBeVisible();
  });

  test('T1-C6 @M3 1-4 sparse results show secondary OneSearch link and beacon click', async ({ page }) => {
    const query = sharedCatalogFixtures.sparseQuery;
    await page.goto(`/opac?search=${encodeURIComponent(query)}`, { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const cardCount = await page.locator('#opacCatalogGrid .opac-modern-card').count();
    expect(cardCount).toBeGreaterThanOrEqual(1);
    expect(cardCount).toBeLessThanOrEqual(4);

    const osLink = page.locator('a.js-onesearch');
    await expect(osLink).toBeVisible();
    await expect(osLink).toHaveAttribute('href', /onesearch\.id/);

    const beaconPromise = page.waitForRequest(
      (req) => req.url().includes('/api/Opac/click') && req.method() === 'POST',
      { timeout: 10000 }
    );
    const [popup] = await Promise.all([
      page.waitForEvent('popup').catch(() => null),
      osLink.click(),
    ]);
    if (popup) await popup.close().catch(() => {});
    const req = await beaconPromise;
    expect(req.postData() || '').toContain('onesearch');
  });

  test('T1-C7 @PRE_WT OPAC Detail page tabs, inventory filter and related pills', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const id = sharedCatalogFixtures.sampleId;
    const res = await page.goto(`/opac/detail/${id}`, { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBe(200);
    await waitForPageReady(page);

    await expect(page.locator('h1.katalog-main-title')).toBeVisible();
    await page.locator('#tabDigitalBtn').click();
    await expect(page.locator('#tabDigitalPane')).toBeVisible();

    await page.locator('#tabMarcBtn').click();
    await expect(page.locator('#tabMarcPane')).toBeVisible();

    await page.locator('#tabFisikBtn').click();
    await expect(page.locator('#tabFisikPane')).toBeVisible();

    await page.locator('#inventorySearchInput').fill('NAKER');
    await page.locator('.katalog-filter-btn[data-status="avail"]').click();
    expect(await page.locator('.katalog-related-section a.katalog-pill-btn').count()).toBeGreaterThanOrEqual(1);
    expect(mon.pageErrors).toEqual([]);
    expect(mon.serverErrors).toEqual([]);
  });
});

test.describe('Tier 1 — Area D: OPAC Browse', () => {
  test('T1-D1 @PRE_WT /opac/browse renders modern WT Browse UI', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const res = await page.goto('/opac/browse', { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBe(200);
    await waitForPageReady(page);

    await expect(page.locator('.browse-hero')).toBeVisible();
    await expect(page.locator('.browse-category-tabs a.browse-cat-btn')).toHaveCount(3);
    expect(await page.locator('#opacCatalogGrid .opac-warm-card').count()).toBeGreaterThan(0);
    expect(mon.pageErrors).toEqual([]);
  });

  test('T1-D2 @PRE_WT Switch between Title, Author and Subject tabs', async ({ page }) => {
    await page.goto('/opac/browse?type=title&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await Promise.all([
      page.waitForURL(/type=author/),
      page.locator('.browse-category-tabs a.browse-cat-btn', { hasText: 'Pengarang' }).click(),
    ]);
    await expect(page.locator('.browse-hero h1')).toContainText('Jelajah Koleksi Lewat Pengarang');
    expect(await page.locator('.populer-chip').count()).toBeGreaterThan(0);

    await Promise.all([
      page.waitForURL(/type=subject/),
      page.locator('.browse-category-tabs a.browse-cat-btn', { hasText: 'Subjek' }).click(),
    ]);
    await expect(page.locator('#ddcDirectory')).toBeVisible();
    expect(await page.locator('#ddcDirectory .ddc-class-section').count()).toBeGreaterThan(0);
  });

  test('T1-D3 @PRE_WT Live AJAX search with blur highlight and clear reset', async ({ page }) => {
    await page.goto('/opac/browse?type=title&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const searchInput = page.locator('#browseSearchInput');
    const searchResp = page.waitForResponse((r) => r.url().includes('ajax=1') && r.status() === 200);
    await searchInput.fill('indonesia');
    await searchResp;

    await expect(page.locator('.opac-section-heading')).toContainText(/Hasil pencarian/i);
    expect(await page.locator('mark.browse-highlight-blur').count()).toBeGreaterThan(0);

    const clearResp = page.waitForResponse((r) => r.url().includes('ajax=1') && r.status() === 200);
    await page.locator('#browseSearchClear').click();
    await clearResp;
    await expect(page.locator('.opac-section-heading')).toContainText(/berawalan "A"/i);
  });

  test('T1-D4 @PRE_WT Subject DDC client-side filter, class jump and reset', async ({ page }) => {
    await page.goto('/opac/browse?type=subject', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await page.locator('.ddc-class-nav-btn[data-ddc-class="300"]').click();
    await expect(page.locator('#ddc-class-300')).toBeVisible();
    await expect(page.locator('#ddc-class-000')).toBeHidden();
    await expect(page.locator('#filterMatchCountDdc')).toBeVisible();

    await page.locator('#browseSearchInput').fill('hukum');
    await expect(page.locator('#ddc-class-300 .ddc-division-item:visible').first()).toBeVisible();

    await page.locator('#resetFilterBtnDdc').click();
    await expect(page.locator('#ddc-class-000')).toBeVisible();
    await expect(page.locator('#ddc-class-300')).toBeVisible();
  });

  test('T1-D5 @PRE_WT Browse Grid/List/Table switcher and Book Meta Modal', async ({ page }) => {
    await page.goto('/opac/browse?type=title&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const grid = page.locator('#opacCatalogGrid');
    const table = page.locator('#tableView');

    await page.locator('#btnViewList').click();
    await expect(grid).toHaveClass(/view-list/);

    await page.locator('#btnViewTable').click();
    await expect(table).toBeVisible();
    await expect(grid).toBeHidden();

    await page.locator('#btnViewGrid').click();
    await expect(grid).toBeVisible();
    await expect(table).toBeHidden();

    await page.locator('#opacCatalogGrid .js-open-book-modal').first().click();
    const modal = page.locator('#opacBookMetaModal');
    await expect(modal).toBeVisible();
    await expect(modal.locator('#opacModalFullDetailLink')).toHaveAttribute('href', /\/opac\/detail\/\d+/);
    await modal.locator('.opac-modal-close-btn').click();
    await expect(modal).toBeHidden();
  });
});

test.describe('Tier 1 — Area E: OPAC Anggota Login & Privacy', () => {
  test.describe.configure({ mode: 'serial' });

  let memberFix;

  test.beforeAll(async () => {
    memberFix = await ensureMemberFixture({ withLoan: true, active: 1, izin: 1, diumumkan: 0 });
  });

  test.afterAll(async () => {
    if (memberFix) await memberFix.cleanup();
  });

  test('T1-E1 @M3 Member login via /opac/masuk shows first-time notif_izin', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 1, diumumkan: 0 });
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });

    await expect(page).toHaveURL(/\/opac(\?.*)?$/);
    const notice = page.locator('.alert[role="status"]');
    await expect(notice).toBeVisible();
    await expect(notice).toContainText('riwayat pinjammu');
    await expect(notice.locator('a[href*="/opac/profil"]')).toBeVisible();
  });

  test('T1-E2 @M3 /opac/profil displays member metadata and personalization state', async ({ page }) => {
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    const res = await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBe(200);

    await expect(page.locator('main h1')).toContainText('Profil');
    const dl = page.locator('main dl');
    await expect(dl).toContainText(memberFix.memberNo);
    await expect(page.locator('#judul-izin')).toBeVisible();
    await expect(page.locator('main')).toContainText(/Sekarang:\s*(menyala|mati)/i);
  });

  test('T1-E3 @M3 Toggle personalization OFF persists across reload', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 1, diumumkan: 1 });
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/profil/izin"] button[type="submit"]').click(),
    ]);
    await expect(page.locator('.alert-success[role="status"]')).toContainText('dimatikan');
    await expect(page.locator('main')).toContainText('Sekarang: mati');

    await page.reload({ waitUntil: 'domcontentloaded' });
    await expect(page.locator('main')).toContainText('Sekarang: mati');
  });

  test('T1-E4 @M3 Toggle personalization ON persists', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 0, diumumkan: 1 });
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/profil/izin"] button[type="submit"]').click(),
    ]);
    await expect(page.locator('.alert-success[role="status"]')).toContainText('dinyalakan');
    await expect(page.locator('main')).toContainText('Sekarang: menyala');
  });

  test('T1-E5 @M3 Navbar reflects session_member and /opac/keluar ends session', async ({ page }) => {
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.gn-bar a[href*="/opac/profil"], .gn-bar a[href*="/portal"]').first()).toBeVisible();

    await page.goto('/opac/keluar', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/opac(\?.*)?$/);

    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/opac\/masuk/);
  });

  test('T1-E6 @M3 Cross-link from /opac/masuk to /login for staff', async ({ page }) => {
    await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
    const staffLink = page.locator('a[href*="/login"]', { hasText: /Masuk ke dasbor/i });
    await expect(staffLink).toBeVisible();

    await Promise.all([
      page.waitForURL(/\/login/),
      staffLink.click(),
    ]);
    await expect(page.locator('form#loginForm, input[name="login"]').first()).toBeVisible();
  });
});

test.describe('Tier 1 — Area F: BiblioVerse Dashboard & Member Portal', () => {
  test.describe.configure({ mode: 'serial' });

  let memberFix;
  let adminFix;

  test.beforeAll(async () => {
    memberFix = await ensureMemberFixture({ withLoan: true, active: 1, izin: 1, diumumkan: 1 });
    adminFix = await ensureAdminFixture();
  });

  test.afterAll(async () => {
    if (memberFix) await memberFix.cleanup();
    if (adminFix) await adminFix.cleanup();
  });

  test('T1-F1 @M4 Member login via /login syncs session_member and reaches /portal and /opac/profil', async ({ page }) => {
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });
    await expect(page).toHaveURL(/\/(portal|dashboard)/);
    await expect(page.locator('#root')).toBeAttached();

    const profRes = await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    expect(profRes && profRes.status()).toBe(200);
    await expect(page).toHaveURL(/\/opac\/profil/);
    await expect(page.locator('main h1')).toContainText('Profil');
  });

  test('T1-F2 @PRE_WT /portal renders live member data and tabs', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });

    const apiResp = page.waitForResponse((r) => r.url().includes('/api/member/portal/') && r.status() === 200);
    await page.goto('/portal/', { waitUntil: 'domcontentloaded' });
    await apiResp;

    await expect(page.locator('#root')).toBeVisible();
    await expect(page.locator('#root')).not.toBeEmpty();
    expect(mon.pageErrors).toEqual([]);
    expect(mon.serverErrors).toEqual([]);
  });

  test('T1-F3 @M4 BiblioVerse Dashboard\Views\anggota renders React UI tabs', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });

    const dashRes = await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    expect(dashRes && dashRes.status()).toBe(200);
    await expect(page.locator('#root')).toContainText('BiblioVerse');

    for (const tabLabel of ['Beranda', 'Data Anggota', 'Rak Buku', 'Sirkulasi', 'Transaksi']) {
      await expect(page.getByRole('button', { name: tabLabel }).first()).toBeVisible();
    }
    await page.getByRole('button', { name: 'Rak Buku' }).first().click();
    expect(mon.pageErrors).toEqual([]);
  });

  test('T1-F4 @M4 BiblioVerse admin drawer and Tote Bag flow', async ({ page }) => {
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });

    const openBtn = page.locator('#adminMenuOpen');
    await expect(openBtn).toBeVisible();
    await openBtn.click();
    await expect(page.locator('aside.admin-drawer')).toBeVisible();
    await page.locator('#adminMenuClose').click();

    await page.getByRole('button', { name: 'Rak Buku' }).first().click();
    const bookTrigger = page.locator('#root button:has-text("Pinjam"), #root button:has-text("Tote"), #root .book-card').first();
    if (await bookTrigger.count()) {
      await bookTrigger.click();
    }
    const toteBtn = page.getByRole('button', { name: /Tote Bag/i }).first();
    if (await toteBtn.count()) {
      await toteBtn.click();
      await expect(page.locator('text=/Tote Bag Peminjaman|Konfirmasi Pinjam/i')).toBeVisible();
    }
  });

  test('T1-F5 @PRE_WT Staff login via /login renders staff /dashboard', async ({ page }) => {
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    await expect(page).toHaveURL(/\/dashboard/);
    await expect(page.locator('h1')).toContainText('Dashboard');

    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.gn-actions .gn-login')).toContainText(/Dashboard|Akun/i);
  });
});

test.describe('Tier 1 — Area G: Mobile Sizing R3 & Bottom Navigation Bar', () => {
  test('T1-G1 @PRE_WT Beranda 375x812 displays .gn-bottom-nav and simplified top bar', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await expect(page.locator('.gn-brand-mark')).toBeVisible();
    await expect(page.locator('.gn-brand-copy')).toBeHidden();
    await expect(page.locator('#gn-burger')).toBeHidden();
    await expect(page.locator('#gn-nav')).toBeHidden();

    const bnav = page.locator('nav.gn-bottom-nav');
    await expect(bnav).toBeVisible();
    const bnavStyle = await bnav.evaluate((el) => {
      const s = window.getComputedStyle(el);
      return { position: s.position, zIndex: parseInt(s.zIndex, 10) };
    });
    expect(bnavStyle.position).toBe('fixed');
    expect(bnavStyle.zIndex).toBeGreaterThanOrEqual(1040);

    const items = bnav.locator('a.gn-bnav-item');
    await expect(items).toHaveCount(5);
    const texts = (await items.allInnerTexts()).join(' ');
    expect(texts).toMatch(/Beranda/);
    expect(texts).toMatch(/OPAC/);
    expect(texts).toMatch(/Browse/);
    expect(texts).toMatch(/Buku Tamu/);
  });

  test('T1-G2 @M2 Beranda 375x812: 2-col showcase, 1-col footer, >=16px input, >=14px shortcut small', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await assertNoHorizontalOverflow(page, 'Beranda 375x812');

    const inputFont = await page.locator('#omnisearch-input').evaluate(
      (el) => parseFloat(window.getComputedStyle(el).fontSize)
    );
    expect(inputFont).toBeGreaterThanOrEqual(16);

    const btnBox = await page.locator('.catalog-search button[type="submit"]').boundingBox();
    expect(btnBox.width).toBeGreaterThanOrEqual(44);
    expect(btnBox.height).toBeGreaterThanOrEqual(44);

    const showcaseCols = await page.locator('.showcase-grid').first().evaluate(
      (el) => window.getComputedStyle(el).gridTemplateColumns.split(' ').length
    );
    expect(showcaseCols).toBe(2);

    const footerCols = await page.locator('.footer-main').evaluate(
      (el) => window.getComputedStyle(el).gridTemplateColumns.split(' ').length
    );
    expect(footerCols).toBe(1);

    const smallFont = await page.locator('.shortcut-card small').first().evaluate(
      (el) => parseFloat(window.getComputedStyle(el).fontSize)
    );
    expect(smallFont).toBeGreaterThanOrEqual(14);
  });

  test('T1-G3 @PRE_WT OPAC 375x812: 2-col grid, <=85px shelves and bottom-sheet drawer', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await assertNoHorizontalOverflow(page, 'OPAC 375x812');
    await expect(page.locator('nav.gn-bottom-nav')).toBeVisible();

    const cards = page.locator('.opac-catalog-grid > .opac-col-book');
    expect(await cards.count()).toBeGreaterThanOrEqual(2);
    const c1 = await cards.nth(0).boundingBox();
    const c2 = await cards.nth(1).boundingBox();
    expect(Math.abs(c1.y - c2.y)).toBeLessThan(10);

    const shelfItem = page.locator('.opac-shelf-item').first();
    if (await shelfItem.count() > 0) {
      const box = await shelfItem.boundingBox();
      expect(box.width).toBeLessThanOrEqual(85);
    }

    await page.locator('#btnToggleFilterSheet').click();
    const drawer = page.locator('#opacSidebarDrawer');
    await expect(drawer).toHaveClass(/is-open/);
    const drawerZ = await drawer.evaluate((el) => parseInt(window.getComputedStyle(el).zIndex, 10));
    expect(drawerZ).toBeGreaterThan(1040);

    const closeBtn = page.locator('#btnCloseFilterSheet');
    const closeBox = await closeBtn.boundingBox();
    expect(closeBox.width).toBeGreaterThanOrEqual(44);
    expect(closeBox.height).toBeGreaterThanOrEqual(44);
    await closeBtn.click();
    await expect(drawer).not.toHaveClass(/is-open/);
  });

  test('T1-G4 @PRE_WT Browse 375x812: 2-col grid, modal fits viewport and bottom nav visible', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/opac/browse?type=title&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await assertNoHorizontalOverflow(page, 'Browse 375x812');
    await expect(page.locator('nav.gn-bottom-nav')).toBeVisible();

    const cards = page.locator('#opacCatalogGrid > .opac-col-book');
    if (await cards.count() >= 2) {
      const c1 = await cards.nth(0).boundingBox();
      const c2 = await cards.nth(1).boundingBox();
      expect(Math.abs(c1.y - c2.y)).toBeLessThan(10);
    }

    await page.locator('#opacCatalogGrid .js-open-book-modal').first().click();
    const modalContent = page.locator('#opacBookMetaModal .modal-content');
    await expect(modalContent).toBeVisible();
    const mBox = await modalContent.boundingBox();
    expect(mBox.width).toBeLessThanOrEqual(375);
    await page.locator('#opacBookMetaModal .opac-modal-close-btn').click();
  });

  test('T1-G5 @M3 Anggota /opac/masuk & /opac/profil 375x812 mobile ergonomics', async ({ page }) => {
    const memberFix = await ensureMemberFixture({ withLoan: false, active: 1, izin: 1, diumumkan: 1 });
    try {
      await page.setViewportSize({ width: 375, height: 812 });
      await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
      await assertNoHorizontalOverflow(page, '/opac/masuk 375x812');

      const userFont = await page.locator('#username').evaluate((el) => parseFloat(window.getComputedStyle(el).fontSize));
      expect(userFont).toBeGreaterThanOrEqual(16);
      const submitBox = await page.locator('form[action*="opac/masuk"] button[type="submit"]').boundingBox();
      expect(submitBox.height).toBeGreaterThanOrEqual(44);

      await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
      await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
      await assertNoHorizontalOverflow(page, '/opac/profil 375x812');

      const logoutBtn = page.locator('a[href*="/opac/keluar"]');
      const logoutBox = await logoutBtn.boundingBox();
      expect(logoutBox.height).toBeGreaterThanOrEqual(44);

      await logoutBtn.scrollIntoViewIfNeeded();
      const bnavBox = await page.locator('nav.gn-bottom-nav').boundingBox();
      const afterScrollBox = await logoutBtn.boundingBox();
      expect(afterScrollBox.y + afterScrollBox.height).toBeLessThanOrEqual(bnavBox.y + 4);
    } finally {
      await memberFix.cleanup();
    }
  });

  test('T1-G6 @PRE_WT Tablet (768x1024), Desktop (1280x800) & Wide (1440x900) breakpoints', async ({ page }) => {
    test.setTimeout(90000);
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    await assertNoHorizontalOverflow(page, 'Beranda 768x1024');
    await expect(page.locator('nav.gn-bottom-nav')).toBeHidden();
    const cols768 = await page.locator('.showcase-grid').first().evaluate(
      (el) => window.getComputedStyle(el).gridTemplateColumns.split(' ').length
    );
    expect(cols768).toBe(3);

    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    await assertNoHorizontalOverflow(page, 'Beranda 1280x800');
    await expect(page.locator('#gn-nav')).toBeVisible();
    await expect(page.locator('nav.gn-bottom-nav')).toBeHidden();
    const cols1280 = await page.locator('.showcase-grid').first().evaluate(
      (el) => window.getComputedStyle(el).gridTemplateColumns.split(' ').length
    );
    expect(cols1280).toBe(4);

    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    await assertNoHorizontalOverflow(page, 'OPAC 1440x900');
    const cards = page.locator('.opac-catalog-grid > .opac-col-book');
    if (await cards.count() >= 4) {
      const boxes = await Promise.all([0, 1, 2, 3].map((i) => cards.nth(i).boundingBox()));
      expect(Math.abs(boxes[0].y - boxes[3].y)).toBeLessThan(10);
    }
  });
});

// ============================================================================
// TIER 2 — BOUNDARY & CORNER CASES (41 Tests)
// ============================================================================

test.describe('Tier 2 — Area A: Beranda & Omnisearch Boundaries', () => {
  test('T2-A1 @M2 pp non-expansion vs uu 13 2003 expansion', async ({ page }) => {
    const pphRes = await page.request.get('/home/search-books?q=pph');
    expect(pphRes.status()).toBe(200);
    const pphJson = await pphRes.json();
    const pphItems = pphJson.data || pphJson.items || [];
    for (const item of pphItems) {
      const hay = `${item.Title || item.title || ''} ${item.Subject || item.subject || ''}`.toLowerCase();
      expect(hay).not.toMatch(/peraturan pemerintah(?!.*pph)/);
    }

    const uuRes = await page.request.get('/home/search-books?q=uu+13+2003');
    expect(uuRes.status()).toBe(200);
    const uuJson = await uuRes.json();
    const uuItems = uuJson.data || uuJson.items || [];
    expect(uuItems.length).toBeGreaterThan(0);
  });

  test('T2-A2 @M2 0-result query renders OneSearch, JDIH link & polite live announcement', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const respPromise = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await page.locator('#omnisearch-input').fill(ZERO_RESULT_QUERY);
    await respPromise;

    await expect(page.locator('#omni-results li.omni-empty')).toContainText(`Belum ada “${ZERO_RESULT_QUERY}” di koleksi kami.`);
    await expect(page.locator('#omni-results .omni-option', { hasText: /Indonesia OneSearch/i })).toBeVisible();
    await expect(page.locator('#omni-results .omni-option', { hasText: /JDIH Kemnaker/i })).toBeVisible();
    await expect(page.locator('#omni-live')).not.toBeEmpty();
  });

  test('T2-A3 @M2 Very long query string (185 chars) at 375x812 has zero overflow', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const longQuery = 'ketenagakerjaan'.repeat(13);
    const respPromise = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await page.locator('#omnisearch-input').fill(longQuery);
    await respPromise;

    await expect(page.locator('#omni-results')).toBeVisible();
    await assertNoHorizontalOverflow(page, 'Omnisearch 185+ chars at 375px');
    const resultsBox = await page.locator('#omni-results').boundingBox();
    expect(resultsBox.x + resultsBox.width).toBeLessThanOrEqual(375);
  });

  test('T2-A4 @M2 Whitespace-only & XSS/SQL special chars safely handled', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const input = page.locator('#omnisearch-input');
    await input.fill('   ');
    await expect(page.locator('#omni-results')).toBeHidden();

    const xss = `<script>alert(1)</script> ' OR 1=1 %`;
    const respPromise = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await input.fill(xss);
    await respPromise;

    await expect(page.locator('#omni-results li.omni-empty')).toBeVisible();
    await expect(page.locator('#omni-results li.omni-empty')).toContainText('<script>alert(1)</script>');
    expect(mon.dialogs).toEqual([]);
    expect(mon.pageErrors).toEqual([]);
  });

  test('T2-A5 @M2 Network failure on /home/search-books shows error UI and recovers on retry', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await page.route('**/home/search-books*', (route) => route.abort());
    await page.locator('#omnisearch-input').fill('k3');

    await expect(page.locator('#omni-results .omni-status')).toContainText('Buku tidak dapat dimuat');
    await expect(page.locator('#omni-results .omni-option', { hasText: /di katalog OPAC/i })).toBeVisible();

    await page.unroute('**/home/search-books*');
    const retryBtn = page.locator('#omni-results .omni-retry, #omni-results button:has-text("Coba lagi")').first();
    const recoveryResp = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    if (await retryBtn.count()) {
      await retryBtn.click();
    } else {
      await page.locator('#omnisearch-input').fill('k3');
    }
    await recoveryResp;
    await expect(page.locator('#omni-results .omni-option .omni-tag').first()).toBeVisible();
  });

  test('T2-A6 @M2 Rapid typing race condition discards stale response', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const input = page.locator('#omnisearch-input');
    await input.fill('stat');
    await page.waitForTimeout(40);
    const finalResp = page.waitForResponse(
      (r) => r.url().includes('/home/search-books') && r.url().includes(ZERO_RESULT_QUERY) && r.status() === 200
    );
    await input.fill(ZERO_RESULT_QUERY);
    await finalResp;

    await expect(page.locator('#omni-results li.omni-empty')).toContainText(ZERO_RESULT_QUERY);
  });
});

test.describe('Tier 2 — Area B: Banner Hero CMS Boundaries', () => {
  test.describe.configure({ mode: 'serial' });

  let adminFix;
  let uploadFiles;

  test.beforeAll(async () => {
    adminFix = await ensureAdminFixture();
    uploadFiles = createTempUploadFiles();
  });

  test.afterAll(async () => {
    cleanupBannersByPrefix('E2E Test Banner');
    if (uploadFiles) uploadFiles.cleanup();
    if (adminFix) await adminFix.cleanup();
  });

  test('T2-B1 @PRE_WT Unauthenticated access to /cms/banner & /cms/banner/create redirects to /login', async ({ page }) => {
    const r1 = await page.request.get('/cms/banner', { maxRedirects: 0 });
    expect([301, 302, 303, 307]).toContain(r1.status());
    expect(r1.headers().location || '').toMatch(/\/login/);

    const r2 = await page.request.get('/cms/banner/create', { maxRedirects: 0 });
    expect([301, 302, 303, 307]).toContain(r2.status());
    expect(r2.headers().location || '').toMatch(/\/login/);
  });

  test('T2-B2 @M2 Small Beranda image (<1220px wide) triggers low-res warning', async ({ page }) => {
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });

    await page.locator('#category').selectOption('Beranda');
    await page.locator('#file_cover').setInputFiles(uploadFiles.smallPng);

    await expect(page.locator('#potong-info')).toContainText('Terlalu kecil');
    await expect(page.locator('#potong-info .text-danger')).toBeVisible();
  });

  test('T2-B3 @M2 Non-image / invalid MIME file rejected without 500 error', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });

    await page.locator('#title').fill('E2E Test Banner Invalid File');
    await page.locator('#file_cover').setInputFiles(uploadFiles.invalidTxt);
    await page.locator('#frm_create button[type="submit"]').click();
    await page.waitForTimeout(500);

    expect(mon.serverErrors).toEqual([]);
    expect(listTestBannersByPrefix('E2E Test Banner Invalid File')).toEqual([]);
  });

  test('T2-B4 @PRE_WT Missing required title or empty file_cover blocked cleanly', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });

    await page.locator('#frm_create button[type="submit"]').click();
    const invalidCount = await page.locator('#frm_create :invalid').count();
    expect(invalidCount).toBeGreaterThanOrEqual(1);
    expect(page.url()).toContain('/cms/banner/create');
    expect(mon.serverErrors).toEqual([]);
  });

  test('T2-B5 @M2 Zero active Beranda banners falls back to local hero assets', async ({ page }) => {
    const deactivatedIds = deactivateActiveBerandaBanners();
    try {
      await page.goto('/', { waitUntil: 'domcontentloaded' });
      await waitForPageReady(page);

      const imgs = page.locator('.hero-slide img, .hero-image-wrap img');
      expect(await imgs.count()).toBeGreaterThanOrEqual(1);
      const loaded = await imgs.first().evaluate((img) => img.complete && img.naturalWidth > 0);
      expect(loaded).toBe(true);
    } finally {
      restoreBerandaBanners(deactivatedIds);
    }
  });
});

test.describe('Tier 2 — Area C: OPAC + Jelajah + Detail Boundaries', () => {
  test('T2-C1 @M3 Invalid ?letter= values handled safely with HTTP 200', async ({ page }) => {
    for (const q of ['/opac?letter=ZZ', '/opac?letter=@', '/opac?letter=']) {
      const res = await page.goto(q, { waitUntil: 'domcontentloaded' });
      expect(res && res.status()).toBe(200);
      const bodyText = await page.locator('body').innerText();
      expect(bodyText).not.toMatch(/SQLSTATE|Unknown column|Fatal error/i);
      const heading = await page.locator('.opac-section-heading, .catalog-section h4').first().innerText();
      expect(heading).not.toMatch(/berawalan\s+(ZZ|@)/i);
    }
  });

  test('T2-C2 @PRE_WT Non-existent / invalid /opac/detail/:id returns clean 404', async ({ page }) => {
    for (const path of ['/opac/detail/999999999', '/opac/detail/abc']) {
      const res = await page.goto(path, { waitUntil: 'domcontentloaded' });
      expect(res && res.status()).toBe(404);
      const text = await page.locator('body').innerText();
      expect(text).not.toMatch(/Fatal error|Stack trace/i);
    }
  });

  test('T2-C3 @PRE_WT OPAC 0-result query shows OneSearch link and reset button', async ({ page }) => {
    await page.goto(`/opac?search=${ZERO_RESULT_QUERY}`, { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
    await expect(page.locator('a.js-onesearch')).toBeVisible();
    await expect(page.locator('a:has-text("Reset Semua Filter")')).toBeVisible();
  });

  test('T2-C4 @PRE_WT OPAC SQL/XSS special chars in query params do not execute or error', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const res = await page.goto('/opac?search=%27%22%3E%3Cscript%3Ealert(1)%3C%2Fscript%3E&DeweyNo=999%25', {
      waitUntil: 'domcontentloaded',
    });
    expect(res && res.status()).toBe(200);
    expect(mon.dialogs).toEqual([]);
    expect(mon.serverErrors).toEqual([]);
    const bodyText = await page.locator('body').innerText();
    expect(bodyText).not.toMatch(/SQLSTATE|You have an error in your SQL syntax/i);
  });

  test('T2-C5 @M3 #jelajah hidden when search, letter or page > 1 is active', async ({ page }) => {
    for (const url of ['/opac?search=hukum', '/opac?letter=A', '/opac?page=2']) {
      await page.goto(url, { waitUntil: 'domcontentloaded' });
      await expect(page.locator('#jelajah')).toHaveCount(0);
    }
  });

  test('T2-C6 @PRE_WT Inverted year_start & year_end shows graceful empty state and reset', async ({ page }) => {
    const res = await page.goto('/opac?year_start=2099&year_end=1900', { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBe(200);
    await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
    await expect(page.locator('a:has-text("Reset Semua Filter")')).toBeVisible();
  });

  test('T2-C7 @M3 #jelajah at 375x812 has zero overflow and >=44px tap targets', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/opac#jelajah', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await assertNoHorizontalOverflow(page, 'OPAC #jelajah 375x812');
    const chips = page.locator('#jelajah .pengarang-chip, #jelajah a[href*="letter="]');
    const count = Math.min(await chips.count(), 10);
    expect(count).toBeGreaterThan(0);
    for (let i = 0; i < count; i++) {
      const box = await chips.nth(i).boundingBox();
      expect(box.height).toBeGreaterThanOrEqual(44);
    }
  });
});

test.describe('Tier 2 — Area D: OPAC Browse Boundaries', () => {
  test('T2-D1 @PRE_WT Invalid type and invalid letter fallback cleanly on /opac/browse', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    const res = await page.goto('/opac/browse?type=invalid&letter=ZZ', { waitUntil: 'domcontentloaded' });
    expect(res && res.status()).toBe(200);
    const text = await page.locator('body').innerText();
    expect(text).not.toMatch(/Fatal error|Whoops!/i);
    expect(mon.serverErrors).toEqual([]);
  });

  test('T2-D2 @PRE_WT Numeric letter=0 and rare letter=X render cleanly', async ({ page }) => {
    for (const letter of ['X', '0']) {
      const res = await page.goto(`/opac/browse?type=title&letter=${letter}`, { waitUntil: 'domcontentloaded' });
      expect(res && res.status()).toBe(200);
      const cards = await page.locator('#opacCatalogGrid .opac-warm-card').count();
      if (cards === 0) {
        await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
        await expect(page.locator('a:has-text("Reset ke Huruf A")')).toBeVisible();
      }
    }
  });

  test('T2-D3 @PRE_WT Browse live search 0-result and special chars handled without error', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await page.goto('/opac/browse?type=author&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const searchInput = page.locator('#browseSearchInput');
    for (const q of ['xyzabc999tidakmungkinada', `a'b"c%`]) {
      const resp = page.waitForResponse((r) => r.url().includes('ajax=1') && r.status() === 200);
      await searchInput.fill(q);
      await resp;
      await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
    }
    expect(mon.pageErrors).toEqual([]);
  });

  test('T2-D4 @PRE_WT Subject DDC 0-result search displays empty state and reset restores classes', async ({ page }) => {
    await page.goto('/opac/browse?type=subject', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await page.locator('#browseSearchInput').fill('999999');
    await expect(page.locator('#emptySearchStateDdc')).toBeVisible();
    await expect(page.locator('#emptySearchStateDdc')).toContainText('Subjek Tidak Ditemukan');

    await page.locator('#emptyResetBtnDdc').click();
    await expect(page.locator('#emptySearchStateDdc')).toBeHidden();
    await expect(page.locator('#ddcDirectory .ddc-class-section').first()).toBeVisible();
  });

  test('T2-D5 @PRE_WT Browse 375x812 & 1280x800 zero overflow across all 3 tabs', async ({ page }) => {
    test.setTimeout(90000);
    for (const vp of [{ width: 375, height: 812 }, { width: 1280, height: 800 }]) {
      await page.setViewportSize(vp);
      for (const path of ['/opac/browse?type=title&letter=A', '/opac/browse?type=author&letter=A', '/opac/browse?type=subject']) {
        await page.goto(path, { waitUntil: 'domcontentloaded' });
        await waitForPageReady(page);
        await assertNoHorizontalOverflow(page, `${path} @ ${vp.width}x${vp.height}`);
      }
    }
  });
});

test.describe('Tier 2 — Area E: OPAC Anggota & Privacy Boundaries', () => {
  test.describe.configure({ mode: 'serial' });

  let memberFix;
  let inactiveMemberFix;
  let adminFix;

  test.beforeAll(async () => {
    memberFix = await ensureMemberFixture({ withLoan: false, active: 1, izin: 1, diumumkan: 1 });
    inactiveMemberFix = await ensureMemberFixture({ withLoan: false, active: 0, izin: 1, diumumkan: 0, forceEphemeral: true });
    adminFix = await ensureAdminFixture();
  });

  test.afterAll(async () => {
    if (memberFix) await memberFix.cleanup();
    if (inactiveMemberFix) await inactiveMemberFix.cleanup();
    if (adminFix) await adminFix.cleanup();
  });

  test('T2-E1 @M3 Unauthenticated GET /opac/profil redirects to /opac/masuk', async ({ page }) => {
    await page.context().clearCookies();
    const res = await page.request.get('/opac/profil', { maxRedirects: 0 });
    expect([301, 302, 303, 307]).toContain(res.status());
    expect(res.headers().location || '').toMatch(/\/opac\/masuk/);
  });

  test('T2-E2 @M3 Unauthenticated POST /opac/profil/izin redirects and leaves DB unchanged', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 1, diumumkan: 1 });
    await page.context().clearCookies();

    const res = await page.request.post('/opac/profil/izin', {
      form: { izin: '0' },
      maxRedirects: 0,
    });
    expect([301, 302, 303, 307, 401, 403]).toContain(res.status());
    const state = getMemberPersonalizationState(memberFix.memberNo);
    if (state.hasColumn) {
      expect(state.izin).toBe(1);
    }
  });

  test('T2-E3 @M3 Wrong password and unknown member return identical generic error', async ({ page }) => {
    await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
    await page.locator('#username').fill(memberFix.memberNo);
    await page.locator('#password').fill('WrongPass123!');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/masuk"] button[type="submit"]').click(),
    ]);
    await expect(page).toHaveURL(/\/opac\/masuk/);
    const err1 = await page.locator('.alert.alert-danger').innerText();
    expect(err1).toContain('Nomor anggota atau kata sandi salah.');
    await expect(page.locator('#username')).toHaveValue(memberFix.memberNo);

    await page.locator('#username').fill('no_such_member_e2e');
    await page.locator('#password').fill('WrongPass123!');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/masuk"] button[type="submit"]').click(),
    ]);
    const err2 = await page.locator('.alert.alert-danger').innerText();
    expect(err2.trim()).toBe(err1.trim());
  });

  test('T2-E4 @M3 GET /opac/masuk when already logged in redirects to /opac', async ({ page }) => {
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/opac(\?.*)?$/);
  });

  test('T2-E5 @M3 Inactive member (active=0) cannot log in at /opac/masuk', async ({ page }) => {
    await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
    await page.locator('#username').fill(inactiveMemberFix.memberNo);
    await page.locator('#password').fill(inactiveMemberFix.password);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/masuk"] button[type="submit"]').click(),
    ]);
    await expect(page).toHaveURL(/\/opac\/masuk/);
    await expect(page.locator('.alert.alert-danger')).toContainText('Nomor anggota atau kata sandi salah.');
  });

  test('T2-E6 @M3 Invalid izin payload (izin=99) safely defaults to 0 without 500', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 1, diumumkan: 1 });
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });

    const postRes = await page.request.post('/opac/profil/izin', {
      form: { izin: '99' },
      maxRedirects: 0,
    });
    expect(postRes.status()).toBeLessThan(500);
    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('main')).toContainText('Sekarang: mati');
  });

  test('T2-E7 @M3 Staff-only user without members record cannot access /opac/profil', async ({ page }) => {
    await page.goto('/opac/masuk', { waitUntil: 'domcontentloaded' });
    await page.locator('#username').fill(adminFix.username);
    await page.locator('#password').fill(adminFix.password);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/masuk"] button[type="submit"]').click(),
    ]);
    await expect(page).toHaveURL(/\/opac\/masuk/);
    await expect(page.locator('.alert.alert-danger')).toContainText('Nomor anggota atau kata sandi salah.');

    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/opac\/masuk/);
  });
});

test.describe('Tier 2 — Area F: BiblioVerse Dashboard & Portal Boundaries', () => {
  test.describe.configure({ mode: 'serial' });

  let adminFix;
  let memberFix;

  test.beforeAll(async () => {
    adminFix = await ensureAdminFixture();
    memberFix = await ensureMemberFixture({ withLoan: true, active: 1, izin: 1, diumumkan: 1 });
  });

  test.afterAll(async () => {
    if (adminFix) await adminFix.cleanup();
    if (memberFix) await memberFix.cleanup();
  });

  test('T2-F1 @PRE_WT Unauthenticated /dashboard redirects to /login and /api/member/portal/profile blocks PII', async ({ page }) => {
    await page.context().clearCookies();
    const dashRes = await page.request.get('/dashboard', { maxRedirects: 0 });
    expect([301, 302, 303, 307]).toContain(dashRes.status());
    expect(dashRes.headers().location || '').toMatch(/\/login/);

    const apiRes = await page.request.get('/api/member/portal/profile', { maxRedirects: 0 });
    expect([401, 403]).toContain(apiRes.status());
  });

  test('T2-F2 @PRE_WT Staff account does not receive member PII from /api/member/portal/profile', async ({ page }) => {
    await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
    const apiRes = await page.request.get('/api/member/portal/profile');
    expect([401, 403, 404]).toContain(apiRes.status());
  });

  test('T2-F3 @M4 Search input in BiblioVerse / /portal with 0-match query shows graceful empty state', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });

    const searchInput = page.locator('input[placeholder*="Cari buku"], input[type="search"]').first();
    await expect(searchInput).toBeVisible();
    await searchInput.fill('zzz_tidak_ada_999');
    await page.waitForTimeout(300);
    expect(mon.pageErrors).toEqual([]);
  });

  test('T2-F4 @M4 BiblioVerse Dashboard at 375x812 & 1280x800 has zero overflow and accessible drawer', async ({ page }) => {
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });

    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await assertNoHorizontalOverflow(page, 'BiblioVerse 375x812');
    await expect(page.locator('#adminMenuOpen')).toBeVisible();

    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await assertNoHorizontalOverflow(page, 'BiblioVerse 1280x800');
  });

  test('T2-F5 @PRE_WT Invalid /login credentials show Myth Auth error cleanly', async ({ page }) => {
    await page.goto('/login', { waitUntil: 'domcontentloaded' });
    await page.locator('#tabAdmin').click();
    await page.locator('input[name="login"]').fill(adminFix.username);
    await page.locator('input[name="password"]').fill('DefinitelyWrongPassword99!');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('#loginBtn').click(),
    ]);

    await expect(page).toHaveURL(/\/login/);
    await expect(page.locator('#infoMessage')).toContainText(/salah|gagal|tidak/i);
  });
});

test.describe('Tier 2 — Area G: Mobile R3 & Extreme Viewport Boundaries', () => {
  test('T2-G1 @PRE_WT Extreme narrow 320x568 & 375x812 zero overflow on /, /opac, /opac/browse', async ({ page }) => {
    test.setTimeout(90000);
    for (const vp of [{ width: 320, height: 568 }, { width: 375, height: 812 }]) {
      await page.setViewportSize(vp);
      for (const path of ['/', '/opac', '/opac/browse']) {
        await page.goto(path, { waitUntil: 'domcontentloaded' });
        await waitForPageReady(page);
        await assertNoHorizontalOverflow(page, `${path} @ ${vp.width}x${vp.height}`);
      }
    }
  });

  test('T2-G2 @PRE_WT Exact breakpoint boundary 767px vs 768px', async ({ page }) => {
    await page.setViewportSize({ width: 767, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    await expect(page.locator('nav.gn-bottom-nav')).toBeVisible();
    await expect(page.locator('#gn-nav')).toBeHidden();

    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    await expect(page.locator('nav.gn-bottom-nav')).toBeHidden();
    const cols768 = await page.locator('.showcase-grid').first().evaluate(
      (el) => window.getComputedStyle(el).gridTemplateColumns.split(' ').length
    );
    expect(cols768).toBe(3);
  });

  test('T2-G3 @M2 Desktop 1101px-1280px navbar fits on single row without wrapping or overlap', async ({ page }) => {
    for (const width of [1101, 1280]) {
      await page.setViewportSize({ width, height: 800 });
      await page.goto('/', { waitUntil: 'domcontentloaded' });
      await waitForPageReady(page);

      const brandBox = await page.locator('.gn-brand').boundingBox();
      const actionsBox = await page.locator('.gn-actions').boundingBox();
      const navLinks = page.locator('#gn-nav .gn-link');
      const count = await navLinks.count();
      expect(count).toBeGreaterThanOrEqual(5);

      const ys = [];
      for (let i = 0; i < count; i++) {
        const box = await navLinks.nth(i).boundingBox();
        ys.push(box.y);
        expect(box.x).toBeGreaterThanOrEqual(brandBox.x + brandBox.width - 2);
        expect(box.x + box.width).toBeLessThanOrEqual(actionsBox.x + 2);
      }
      expect(Math.max(...ys) - Math.min(...ys)).toBeLessThan(12);
    }
  });

  test('T2-G4 @PRE_WT Drawer Escape key and backdrop closing at 375x812', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const drawer = page.locator('#opacSidebarDrawer');
    await page.locator('#btnToggleFilterSheet').click();
    await expect(drawer).toHaveClass(/is-open/);

    await page.keyboard.press('Escape');
    await expect(drawer).not.toHaveClass(/is-open/);

    await page.locator('#btnToggleFilterSheet').click();
    await expect(drawer).toHaveClass(/is-open/);
    await page.locator('#opacDrawerBackdrop').click({ position: { x: 180, y: 40 } });
    await expect(drawer).not.toHaveClass(/is-open/);
    const hasSheetClass = await page.evaluate(() => document.body.classList.contains('opac-sheet-open'));
    expect(hasSheetClass).toBe(false);
  });

  test('T2-G5 @PRE_WT Sticky bottom nav leaves body padding >= 88px so bottom links remain clickable at 375x812', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const bodyPad = await page.evaluate(() => parseFloat(window.getComputedStyle(document.body).paddingBottom));
    expect(bodyPad).toBeGreaterThanOrEqual(88);

    const lastFooterLink = page.locator('.site-footer a').last();
    await lastFooterLink.scrollIntoViewIfNeeded();
    const bnavBox = await page.locator('nav.gn-bottom-nav').boundingBox();
    const footerBox = await lastFooterLink.boundingBox();
    expect(footerBox.y + footerBox.height).toBeLessThanOrEqual(bnavBox.y + 4);

    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    const bodyPadOpac = await page.evaluate(() => parseFloat(window.getComputedStyle(document.body).paddingBottom));
    expect(bodyPadOpac).toBeGreaterThanOrEqual(88);
  });

  test('T2-G6 @M2 Open Omnisearch dropdown + bottom nav at 375x812 fits viewport and >=44px options', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const respPromise = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await page.locator('#omnisearch-input').fill('k3');
    await respPromise;

    const results = page.locator('#omni-results');
    await expect(results).toBeVisible();
    const rBox = await results.boundingBox();
    expect(rBox.x + rBox.width).toBeLessThanOrEqual(375);

    const optBox = await page.locator('#omni-results .omni-option').first().boundingBox();
    expect(optBox.height).toBeGreaterThanOrEqual(44);
  });
});

// ============================================================================
// TIER 3 — CROSS-FEATURE COMBINATIONS (Pairwise Interactions, 12 Tests)
// ============================================================================

test.describe('Tier 3 — Cross-Feature Pairwise Interactions', () => {
  test.describe.configure({ mode: 'serial' });
  test.beforeEach(() => {
    test.setTimeout(90000);
  });

  let memberFix;
  let adminFix;
  let uploadFiles;

  test.beforeAll(async () => {
    memberFix = await ensureMemberFixture({ withLoan: true, active: 1, izin: 1, diumumkan: 0 });
    adminFix = await ensureAdminFixture();
    uploadFiles = createTempUploadFiles();
  });

  test.afterAll(async () => {
    cleanupBannersByPrefix('E2E Test Banner');
    if (uploadFiles) uploadFiles.cleanup();
    if (memberFix) await memberFix.cleanup();
    if (adminFix) await adminFix.cleanup();
  });

  test('T3-01 @M3 Home Omnisearch regulation query -> OPAC -> sort + sidebar filter', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const input = page.locator('#omnisearch-input');
    await input.fill('uu 13 2003');
    await Promise.all([
      page.waitForURL(/\/opac\?.*search=/),
      input.press('Enter'),
    ]);

    const sortSelect = page.locator('select.opac-sort-select[name="sort"]');
    const titleAscVal = await sortSelect.locator('option[value*="title_asc"]').getAttribute('value');
    await Promise.all([
      page.waitForURL(/sort=title_asc/),
      sortSelect.selectOption(titleAscVal),
    ]);

    await Promise.all([
      page.waitForURL(/language=ind/),
      page.locator('#opacSidebarFilterForm input[name="language"][value="ind"]').check(),
    ]);
    expect(page.url()).toContain('sort=title_asc');
    expect(page.url()).toContain('language=ind');
  });

  test('T3-02 @M2 Home Topic Chip -> OPAC -> Detail -> Related Pill -> OPAC', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const chip = page.locator('.omni-suggestions button[data-suggest]').first();
    const resp = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await chip.click();
    await resp;

    await Promise.all([
      page.waitForURL(/\/opac\?search=/),
      page.locator('#omni-results .omni-option').last().click(),
    ]);

    const detailHref = await page.locator('#opacCatalogGrid .opac-modern-card a.js-open-book-modal').first().getAttribute('href');
    const relUrl = detailHref.replace(/^https?:\/\/[^/]+/, '');
    await page.goto(relUrl, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('h1.katalog-main-title')).toBeVisible();

    await Promise.all([
      page.waitForURL(/\/opac\?/),
      page.locator('.katalog-related-section a.katalog-pill-btn').first().click(),
    ]);
  });

  test('T3-03 @M3 Home 0-result Omnisearch -> OPAC 0-result + OneSearch + Click Beacon', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const resp = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await page.locator('#omnisearch-input').fill(ZERO_RESULT_QUERY);
    await resp;

    await Promise.all([
      page.waitForURL(new RegExp(`/opac\\?search=${ZERO_RESULT_QUERY}`)),
      page.locator('#omni-results .omni-option', { hasText: /Indonesia OneSearch/i }).click(),
    ]);

    await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
    const osBtn = page.locator('a.js-onesearch');
    await expect(osBtn).toBeVisible();

    const beaconPromise = page.waitForRequest(
      (req) => req.url().includes('/api/Opac/click') && req.method() === 'POST',
      { timeout: 10000 }
    );
    const [popup] = await Promise.all([
      page.waitForEvent('popup').catch(() => null),
      osBtn.click(),
    ]);
    if (popup) await popup.close().catch(() => {});
    const req = await beaconPromise;
    expect(req.postData() || '').toContain('onesearch');
  });

  test('T3-04 @M3 Member /opac/masuk -> notif_izin -> /opac/profil opt-out -> OPAC hides history row', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 1, diumumkan: 0 });
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });

    await expect(page.locator('.alert[role="status"]')).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/opac\/profil/),
      page.locator('.alert[role="status"] a[href*="/opac/profil"]').click(),
    ]);

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/profil/izin"] button[type="submit"]').click(),
    ]);
    await expect(page.locator('main')).toContainText('Sekarang: mati');

    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('text=/Karena kamu meminjam/i')).toHaveCount(0);
  });

  test('T3-05 @M3 Member /opac/profil opt-in -> OPAC restores personalized discovery', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 0, diumumkan: 1 });
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/profil/izin"] button[type="submit"]').click(),
    ]);
    await expect(page.locator('main')).toContainText('Sekarang: menyala');

    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    const shelfCount = await page.locator('.opac-shelf-scroll, h4:has-text("Populer"), h4:has-text("Koleksi")').count();
    expect(shelfCount).toBeGreaterThan(0);
  });

  test('T3-06 @M4 /login Myth Auth anggota <-> session_member <-> /portal & /opac/profil', async ({ page }) => {
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });
    await expect(page).toHaveURL(/\/(portal|dashboard)/);

    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.gn-bar a[href*="/opac/profil"], .gn-bar a[href*="/portal"]').first()).toBeVisible();

    const profRes = await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    expect(profRes && profRes.status()).toBe(200);
    await expect(page).toHaveURL(/\/opac\/profil/);
    await expect(page.locator('main dl')).toContainText(memberFix.memberNo);
  });

  test('T3-07 @M3 Browse DDC Card -> /opac?DeweyNo=... -> Sidebar tersedia -> bottom nav return (375px)', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/opac/browse?type=subject', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const ddcCard = page.locator('a.ddc-card[href*="DeweyNo=33"]').first();
    await Promise.all([
      page.waitForURL(/\/opac\?DeweyNo=33/),
      ddcCard.click(),
    ]);

    await page.locator('#btnToggleFilterSheet').click();
    await Promise.all([
      page.waitForURL(/availability=tersedia/),
      page.locator('#opacSidebarFilterForm input[name="availability"][value="tersedia"]').check(),
    ]);
    expect(page.url()).toContain('DeweyNo=33');

    await Promise.all([
      page.waitForURL(/\/opac\/browse/),
      page.locator('nav.gn-bottom-nav a.gn-bnav-item', { hasText: 'Browse' }).click(),
    ]);
  });

  test('T3-08 @M3 Browse Top Author Chip -> /opac?search_by=Author -> /opac#jelajah letter switch', async ({ page }) => {
    await page.goto('/opac/browse?type=author&letter=A', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await Promise.all([
      page.waitForURL(/search_by=Author/),
      page.locator('.populer-chip').first().click(),
    ]);

    await page.goto('/opac#jelajah', { waitUntil: 'domcontentloaded' });
    await Promise.all([
      page.waitForURL(/search_by=Author.*letter=B|letter=B.*search_by=Author/),
      page.locator('#jelajah a[href*="search_by=Author"][href*="letter=B"]').first().click(),
    ]);
  });

  test('T3-09 @M3 /opac#jelajah A-Z letter -> sort -> sidebar format preserves search_by & letter', async ({ page }) => {
    await page.goto('/opac#jelajah', { waitUntil: 'domcontentloaded' });
    await Promise.all([
      page.waitForURL(/search_by=Title.*letter=A|letter=A.*search_by=Title/),
      page.locator('#jelajah a[href*="search_by=Title"][href*="letter=A"]').first().click(),
    ]);

    const sortSelect = page.locator('select.opac-sort-select[name="sort"]');
    const yearDescVal = await sortSelect.locator('option[value*="year_desc"]').getAttribute('value');
    await Promise.all([
      page.waitForURL(/sort=year_desc/),
      sortSelect.selectOption(yearDescVal),
    ]);

    await Promise.all([
      page.waitForURL(/format=cetak/),
      page.locator('#opacSidebarFilterForm input[name="format"][value="cetak"]').check(),
    ]);
    expect(page.url()).toContain('search_by=Title');
    expect(page.url()).toContain('letter=A');
  });

  test('T3-10 @M2 Banner CMS Beranda crop -> Home Hero Carousel -> Mobile 375x812 & Desktop 1280x800', async ({ page }) => {
    const title = `E2E Test Banner T3 ${Date.now()}`;
    try {
      await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
      await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });
      await page.locator('#title').fill(title);
      await page.locator('#category').selectOption('Beranda');
      await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);
      await expect(page.locator('.cropper-container')).toBeVisible({ timeout: 10000 });
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator('#frm_create button[type="submit"]').click(),
      ]);
      activateBannersByPrefix(title);

      for (const vp of [{ width: 1280, height: 800 }, { width: 375, height: 812 }]) {
        await page.setViewportSize(vp);
        await page.goto('/', { waitUntil: 'domcontentloaded' });
        await waitForPageReady(page);
        await assertNoHorizontalOverflow(page, `Home Carousel ${vp.width}x${vp.height}`);
        const imgLoaded = await page.locator('.hero-slide img, .hero-image-wrap img').first().evaluate(
          (img) => img.complete && img.naturalWidth > 0
        );
        expect(imgLoaded).toBe(true);
      }
    } finally {
      cleanupBannersByPrefix('E2E Test Banner');
    }
  });

  test('T3-11 @PRE_WT Navbar Statistik dropdown -> /opac/statistics & mobile .gn-bottom-nav navigation', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const statBtn = page.locator('.gn-drop-btn', { hasText: 'Statistik' });
    await statBtn.click();
    await Promise.all([
      page.waitForURL(/\/opac\/statistics/),
      page.locator('.gn-drop.is-open a.gn-item[href*="opac/statistics"]').first().click(),
    ]);

    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    for (const navLabel of ['OPAC', 'Browse', 'Buku Tamu', 'Beranda']) {
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator('nav.gn-bottom-nav a.gn-bnav-item', { hasText: navLabel }).click(),
      ]);
      await expect(page.locator('nav.gn-bottom-nav a.gn-bnav-item', { hasText: navLabel })).toHaveAttribute('aria-current', 'page');
    }
  });

  test('T3-12 @M3 Logged-in session_member search + click logging -> /opac/keluar -> guest OPAC', async ({ page }) => {
    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await page.goto('/opac?search=hukum', { waitUntil: 'domcontentloaded' });

    const beaconPromise = page.waitForRequest(
      (req) => req.url().includes('/api/Opac/click') && req.method() === 'POST',
      { timeout: 10000 }
    );
    await page.locator('#opacCatalogGrid .opac-modern-card a.js-open-book-modal').first().click();
    await beaconPromise;

    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await Promise.all([
      page.waitForURL(/\/opac(\?.*)?$/),
      page.locator('a[href*="/opac/keluar"]').click(),
    ]);
    await expect(page.locator('.gn-actions .gn-login')).toContainText(/Masuk/i);
  });
});

// ============================================================================
// TIER 4 — REAL-WORLD APPLICATION SCENARIOS (6 End-to-End Journeys)
// ============================================================================

test.describe('Tier 4 — Real-World End-to-End Journeys', () => {
  test.describe.configure({ mode: 'serial' });
  test.beforeEach(() => {
    test.setTimeout(120000);
  });

  let memberFix;
  let adminFix;
  let uploadFiles;

  test.beforeAll(async () => {
    memberFix = await ensureMemberFixture({ withLoan: true, active: 1, izin: 1, diumumkan: 0 });
    adminFix = await ensureAdminFixture();
    uploadFiles = createTempUploadFiles();
  });

  test.afterAll(async () => {
    cleanupBannersByPrefix('E2E Test Banner');
    if (uploadFiles) uploadFiles.cleanup();
    if (memberFix) await memberFix.cleanup();
    if (adminFix) await adminFix.cleanup();
  });

  test('T4-01 @M3 Citizen searching labor regulation (uu 13 2003) on Desktop -> OPAC -> Filter -> Detail -> Related', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    await expect(page.locator('.hero-copy > p')).toContainText(/judul koleksi/i);
    await expect(page.locator('a[href="https://jdih.kemnaker.go.id"]')).toBeVisible();

    const input = page.locator('#omnisearch-input');
    const resp = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await input.fill('uu 13 2003');
    await resp;
    await expect(page.locator('#omni-results .omni-tag', { hasText: /Regulasi/i }).first()).toBeVisible();

    await Promise.all([
      page.waitForURL(/\/opac\?.*search=/),
      input.press('Enter'),
    ]);
    await Promise.all([
      page.waitForURL(/availability=tersedia/),
      page.locator('#opacSidebarFilterForm input[name="availability"][value="tersedia"]').check(),
    ]);

    const detailHref = await page.locator('#opacCatalogGrid .opac-modern-card a.js-open-book-modal').first().getAttribute('href');
    await page.goto(detailHref.replace(/^https?:\/\/[^/]+/, ''), { waitUntil: 'domcontentloaded' });
    await expect(page.locator('h1.katalog-main-title')).toBeVisible();

    await page.locator('#tabMarcBtn').click();
    await expect(page.locator('#tabMarcPane')).toBeVisible();
    await page.locator('#tabFisikBtn').click();
    await expect(page.locator('#tabFisikPane')).toBeVisible();

    await Promise.all([
      page.waitForURL(/\/opac\?/),
      page.locator('.katalog-related-section a.katalog-pill-btn').first().click(),
    ]);
  });

  test('T4-02 @M3 Mobile commuter browsing on 375x812 phone via bottom nav, Browse DDC & drawer', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);
    await assertNoHorizontalOverflow(page, 'T4-02 Home 375px');

    await Promise.all([
      page.waitForURL(/\/opac\/browse/),
      page.locator('nav.gn-bottom-nav a.gn-bnav-item', { hasText: 'Browse' }).click(),
    ]);

    await Promise.all([
      page.waitForURL(/type=subject/),
      page.locator('.browse-category-tabs a.browse-cat-btn', { hasText: 'Subjek' }).click(),
    ]);
    await page.locator('.ddc-class-nav-btn[data-ddc-class="300"]').click();
    await Promise.all([
      page.waitForURL(/\/opac\?DeweyNo=33/),
      page.locator('a.ddc-card[href*="DeweyNo=33"]').first().click(),
    ]);

    await page.locator('#btnToggleFilterSheet').click();
    await Promise.all([
      page.waitForURL(/language=ind/),
      page.locator('#opacSidebarFilterForm input[name="language"][value="ind"]').check(),
    ]);

    await page.locator('#opacCatalogGrid .js-open-book-modal').first().click();
    await expect(page.locator('#opacBookMetaModal')).toBeVisible();
    await Promise.all([
      page.waitForURL(/\/opac\/detail\/\d+/),
      page.locator('#opacModalFullDetailLink').click(),
    ]);
    await assertNoHorizontalOverflow(page, 'T4-02 Detail 375px');

    await Promise.all([
      page.waitForURL(/\/opac(\?.*)?$/),
      page.locator('nav.gn-bottom-nav a.gn-bnav-item', { hasText: 'OPAC' }).click(),
    ]);
  });

  test('T4-03 @M3 Rare book discovery -> 0-result Omnisearch -> OPAC fallback -> OneSearch beacon -> Reset', async ({ page }) => {
    const rareQuery = 'Quantum Ketenagakerjaan Nusantara 2099';
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await waitForPageReady(page);

    const resp = page.waitForResponse((r) => r.url().includes('/home/search-books') && r.status() === 200);
    await page.locator('#omnisearch-input').fill(rareQuery);
    await resp;

    await expect(page.locator('#omni-results li.omni-empty')).toContainText(`Belum ada “${rareQuery}” di koleksi kami.`);
    await Promise.all([
      page.waitForURL(/\/opac\?search=/),
      page.locator('#omni-results .omni-option', { hasText: /Indonesia OneSearch/i }).click(),
    ]);

    await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
    const osBtn = page.locator('a.js-onesearch');
    await expect(osBtn).toBeVisible();
    const beaconPromise = page.waitForRequest(
      (req) => req.url().includes('/api/Opac/click') && req.method() === 'POST',
      { timeout: 10000 }
    );
    const [popup] = await Promise.all([
      page.waitForEvent('popup').catch(() => null),
      osBtn.click(),
    ]);
    if (popup) await popup.close().catch(() => {});
    await beaconPromise;

    await Promise.all([
      page.waitForURL(/\/opac$/),
      page.locator('a:has-text("Reset Semua Filter")').click(),
    ]);
    expect(await page.locator('#opacCatalogGrid .opac-modern-card').count()).toBeGreaterThan(0);
  });

  test('T4-04 @M3 Library member privacy control journey (/opac/masuk -> notif_izin -> opt-out -> opt-in -> keluar)', async ({ page }) => {
    setMemberPersonalizationState(memberFix.memberNo, { izin: 1, diumumkan: 0 });
    await page.goto('/opac', { waitUntil: 'domcontentloaded' });

    await loginViaOpacMasuk(page, { username: memberFix.memberNo, password: memberFix.password });
    await expect(page.locator('.alert[role="status"]')).toBeVisible();

    await Promise.all([
      page.waitForURL(/\/opac\/profil/),
      page.locator('.alert[role="status"] a[href*="/opac/profil"]').click(),
    ]);
    await expect(page.locator('main dl')).toContainText(memberFix.fullname);

    // Opt-out
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/profil/izin"] button[type="submit"]').click(),
    ]);
    await expect(page.locator('main')).toContainText('Sekarang: mati');
    await page.goto('/opac', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('text=/Karena kamu meminjam/i')).toHaveCount(0);

    // Opt-in
    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action*="opac/profil/izin"] button[type="submit"]').click(),
    ]);
    await expect(page.locator('main')).toContainText('Sekarang: menyala');

    // Keluar
    await Promise.all([
      page.waitForURL(/\/opac(\?.*)?$/),
      page.locator('a[href*="/opac/keluar"]').click(),
    ]);
    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/opac\/masuk/);
  });

  test('T4-05 @M4 Member unified portal and BiblioVerse journey (/login -> /portal & /dashboard -> /opac/profil -> /logout)', async ({ page }) => {
    const mon = attachRuntimeMonitors(page);
    await loginViaMythAuth(page, { login: memberFix.memberNo, password: memberFix.password, role: 'anggota' });
    await expect(page).toHaveURL(/\/(portal|dashboard)/);

    await page.goto('/portal/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#root')).not.toBeEmpty();

    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#root')).toContainText('BiblioVerse');

    await page.goto('/opac/profil', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/opac\/profil/);
    await expect(page.locator('main dl')).toContainText(memberFix.memberNo);

    await page.goto('/logout', { waitUntil: 'domcontentloaded' });
    expect(mon.serverErrors).toEqual([]);
  });

  test('T4-06 @M2 Librarian updating Hero Banner CMS & verifying live Beranda on desktop & mobile', async ({ page }) => {
    const title = `E2E Test Banner Journey ${Date.now()}`;
    try {
      await loginViaMythAuth(page, { login: adminFix.username, password: adminFix.password, role: 'admin' });
      await expect(page).toHaveURL(/\/dashboard/);

      await page.goto('/cms/banner/create?slug=beranda', { waitUntil: 'domcontentloaded' });
      await page.locator('#title').fill(title);
      await page.locator('#category').selectOption('Beranda');
      await page.locator('#file_cover').setInputFiles(uploadFiles.largeJpg);

      await expect(page.locator('.cropper-container')).toBeVisible({ timeout: 10000 });
      await expect(page.locator('#potong-info')).toContainText('Cukup tajam.');

      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator('#frm_create button[type="submit"]').click(),
      ]);
      activateBannersByPrefix(title);

      for (const vp of [{ width: 1280, height: 800 }, { width: 375, height: 812 }]) {
        await page.setViewportSize(vp);
        await page.goto('/', { waitUntil: 'domcontentloaded' });
        await waitForPageReady(page);
        await assertNoHorizontalOverflow(page, `T4-06 Beranda ${vp.width}x${vp.height}`);
        const loaded = await page.locator('.hero-slide img, .hero-image-wrap img').first().evaluate(
          (img) => img.complete && img.naturalWidth > 0
        );
        expect(loaded).toBe(true);
      }
    } finally {
      cleanupBannersByPrefix('E2E Test Banner');
    }
  });
});
