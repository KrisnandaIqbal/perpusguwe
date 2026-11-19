const { test, expect } = require('@playwright/test');

const VIEWPORTS = [
  { name: 'iPhone (375px)', width: 375, height: 812 },
  { name: 'Tablet (768px)', width: 768, height: 1024 },
  { name: 'Laptop (1280px)', width: 1280, height: 800 },
  { name: 'Wide Desktop (1440px)', width: 1440, height: 900 },
];

for (const vp of VIEWPORTS) {
  test.describe(`Verifikasi Browse Katalog Persis OPAC - ${vp.name}`, () => {
    test.use({ viewport: { width: vp.width, height: vp.height } });

    test(`Memenuhi standar responsive & persis OPAC di ${vp.name}`, async ({ page }) => {
      await page.goto('http://perpusdev.test/opac/browse?type=title&letter=A');

      // 1. Cek Scroll Horizontal (bebas overflow)
      const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
      const innerWidth = await page.evaluate(() => window.innerWidth);
      expect(scrollWidth).toBeLessThanOrEqual(innerWidth);

      // 2. Cek Header Katalog & Tombol View Switcher
      const catalogHeader = page.locator('.opac-catalog-header');
      await expect(catalogHeader).toBeVisible();

      const btnGrid = page.locator('#btnViewGrid');
      const btnList = page.locator('#btnViewList');
      const btnTable = page.locator('#btnViewTable');

      await expect(btnGrid).toBeVisible();
      await expect(btnList).toBeVisible();
      await expect(btnTable).toBeVisible();

      // 3. Cek Grid Katalog Buku Menggunakan Kelas OPAC (.opac-catalog-grid & .opac-warm-card)
      const grid = page.locator('#opacCatalogGrid');
      await expect(grid).toBeVisible();

      const cards = page.locator('#opacCatalogGrid > .opac-col-book');
      const cardCount = await cards.count();
      expect(cardCount).toBeGreaterThan(0);

      // Kartu buku harus memiliki styling .opac-warm-card persis OPAC
      const warmCard = page.locator('#opacCatalogGrid .opac-warm-card').first();
      await expect(warmCard).toBeVisible();

      // Aspect ratio sampul buku ~0.67 (2/3)
      const book3d = page.locator('#opacCatalogGrid .opac-book-3d').first();
      if (await book3d.count() > 0) {
        const bBox = await book3d.boundingBox();
        const ratio = bBox.width / bBox.height;
        expect(ratio).toBeGreaterThan(0.55);
        expect(ratio).toBeLessThan(0.78);
      }

      // 4. Verifikasi Di Layar Mobile (375px): 2 Kolom Compact Persis OPAC
      if (vp.width === 375 && cardCount >= 2) {
        const card1 = await cards.nth(0).boundingBox();
        const card2 = await cards.nth(1).boundingBox();
        // Berada di baris yang sama (koordinat y hampir identik)
        expect(Math.abs(card1.y - card2.y)).toBeLessThan(10);
      }

      // 5. Verifikasi Interaktivitas Switch View: List Mode
      await btnList.click();
      await expect(grid).toHaveClass(/view-list/);
      await expect(btnList).toHaveClass(/active/);
      await expect(btnGrid).not.toHaveClass(/active/);

      // 6. Verifikasi Interaktivitas Switch View: Table Mode
      await btnTable.click();
      const table = page.locator('#tableView');
      await expect(table).toBeVisible();
      await expect(grid).toBeHidden();
      await expect(btnTable).toHaveClass(/active/);

      // 7. Kembalikan ke Grid Mode
      await btnGrid.click();
      await expect(grid).toBeVisible();
      await expect(grid).not.toHaveClass(/view-list/);
      await expect(table).toBeHidden();
      await expect(btnGrid).toHaveClass(/active/);

      // 8. Cek Modal Pop-up Bibliografis (Klik Sampul Buku)
      const firstCardCover = page.locator('#opacCatalogGrid .js-open-book-modal').first();
      await firstCardCover.click();
      const modal = page.locator('#opacBookMetaModal');
      await expect(modal).toBeVisible();
      await page.waitForTimeout(500);

      // Tutup modal dengan Escape / tombol close
      const modalCloseBtn = page.locator('#opacBookMetaModal .opac-modal-close-btn');
      await modalCloseBtn.click();
      await expect(modal).toBeHidden();

      // 9. Cek Pagination Bawah Persis OPAC
      const paginationContainer = page.locator('.opac-pagination-container');
      await expect(paginationContainer).toBeVisible();
      const pageInfo = page.locator('.opac-page-info');
      await expect(pageInfo).toBeVisible();
    });

    test(`Verifikasi browse pengarang & pencarian live di ${vp.name}`, async ({ page }) => {
      // 1. Buka browse tipe author
      await page.goto('http://perpusdev.test/opac/browse?type=author&letter=A');

      const authorCards = page.locator('#opacCatalogGrid > .opac-col-book');
      expect(await authorCards.count()).toBeGreaterThan(0);

      // 2. Cek Live Search dengan menunggu respons AJAX
      const searchInput = page.locator('#browseSearchInput');
      const searchResponse = page.waitForResponse(resp => resp.url().includes('ajax=1') && resp.status() === 200);
      await searchInput.fill('indonesia');
      await searchResponse;

      // Header harus menampilkan hasil pencarian
      const heading = page.locator('.opac-section-heading');
      await expect(heading).toContainText(/Hasil pencarian/);

      // Grid tetap ada dan menampilkan hasil
      await expect(page.locator('#opacCatalogGrid')).toBeVisible();

      // 3. Cek Empty State
      const emptyResponse = page.waitForResponse(resp => resp.url().includes('ajax=1') && resp.status() === 200);
      await searchInput.fill('xyzabc999tidakmungkinada');
      await emptyResponse;

      await expect(page.locator('text=Koleksi tidak ditemukan')).toBeVisible();
    });
  });
}
