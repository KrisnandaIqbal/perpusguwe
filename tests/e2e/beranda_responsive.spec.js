const { test, expect } = require('@playwright/test');

const VIEWPORTS = [
  { name: 'iPhone (375px)', width: 375, height: 812 },
  { name: 'Tablet (768px)', width: 768, height: 1024 },
  { name: 'Laptop (1280px)', width: 1280, height: 800 },
];

for (const vp of VIEWPORTS) {
  test.describe(`Verifikasi Beranda - ${vp.name}`, () => {
    test.use({ viewport: { width: vp.width, height: vp.height } });

    test(`Memenuhi standar responsive di ${vp.name}`, async ({ page }) => {
      await page.goto('http://perpusdev.test/');

      // 1. Cek Scroll Horizontal
      const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
      const innerWidth = await page.evaluate(() => window.innerWidth);
      console.log(`[${vp.name}] innerWidth: ${innerWidth}, scrollWidth: ${scrollWidth}`);
      expect(scrollWidth).toBeLessThanOrEqual(innerWidth);

      // 2. Cek Search Bar
      const searchInput = page.locator('#omnisearch-input');
      await expect(searchInput).toBeVisible();
      const searchBox = await searchInput.boundingBox();
      expect(searchBox.height).toBeGreaterThanOrEqual(44);

      // 3. Cek Foto Hero / Carousel terlihat dan tidak collapse
      const heroWrap = page.locator('.hero-image-wrap');
      await expect(heroWrap).toBeVisible();
      const heroBox = await heroWrap.boundingBox();
      expect(heroBox.height).toBeGreaterThan(250);
      expect(heroBox.width).toBeGreaterThan(250);

      if (vp.width === 375) {
        // Pada HP, search bar harus full-width (> 80% dari lebar layar)
        expect(searchBox.width).toBeGreaterThan(250);

        // Cek font-size input minimal 16px di HP (supaya iOS tidak auto-zoom)
        const fontSize = await searchInput.evaluate((el) => parseFloat(window.getComputedStyle(el).fontSize));
        expect(fontSize).toBeGreaterThanOrEqual(16);

        // Tombol search touch target min 44x44px
        const searchBtn = page.locator('.catalog-search button');
        const btnBox = await searchBtn.boundingBox();
        expect(btnBox.width).toBeGreaterThanOrEqual(44);
        expect(btnBox.height).toBeGreaterThanOrEqual(44);

        // 3. Cek Header & Navigasi di HP: navbar atas (.gn-bar) disembunyikan, bottom nav aktif
        await expect(page.locator('.gn-bar')).toBeHidden();
        const bottomNav = page.locator('.gn-bottom-nav');
        await expect(bottomNav).toBeVisible();

        // 4. Cek Grid Kolom Katalog Buku (Showcase) di HP = 2 kolom
        const showcaseGrid = page.locator('.showcase-grid').first();
        const gridCols = await showcaseGrid.evaluate((el) => {
          return window.getComputedStyle(el).gridTemplateColumns.split(' ').length;
        });
        console.log(`[${vp.name}] showcase grid columns: ${gridCols}`);
        expect(gridCols).toBe(2);

        // 5. Cek Cover Buku Aspect Ratio 2/3
        const covers = page.locator('.showcase-cover img, .book-cover-md');
        const coverCount = await covers.count();
        if (coverCount > 0) {
          const firstCoverBox = await covers.first().boundingBox();
          const ratio = firstCoverBox.width / firstCoverBox.height;
          console.log(`[${vp.name}] cover aspect ratio: ${ratio.toFixed(2)} (target ~0.67)`);
          expect(ratio).toBeGreaterThan(0.55);
          expect(ratio).toBeLessThan(0.75);
        }

        // 6. Cek Footer Stack Vertikal di HP
        const footerMain = page.locator('.footer-main');
        const footerCols = await footerMain.evaluate((el) => {
          return window.getComputedStyle(el).gridTemplateColumns.split(' ').length;
        });
        expect(footerCols).toBe(1);
      }

      if (vp.width === 768) {
        // Tablet: Showcase 3 kolom
        const showcaseGrid = page.locator('.showcase-grid').first();
        const gridCols = await showcaseGrid.evaluate((el) => {
          return window.getComputedStyle(el).gridTemplateColumns.split(' ').length;
        });
        console.log(`[${vp.name}] showcase grid columns: ${gridCols}`);
        expect(gridCols).toBe(3);
      }

      if (vp.width === 1280) {
        // Desktop: Showcase 4 kolom
        const showcaseGrid = page.locator('.showcase-grid').first();
        const gridCols = await showcaseGrid.evaluate((el) => {
          return window.getComputedStyle(el).gridTemplateColumns.split(' ').length;
        });
        console.log(`[${vp.name}] showcase grid columns: ${gridCols}`);
        expect(gridCols).toBe(4);

        // Desktop: Menu Horizontal terlihat, burger tersembunyi
        const burger = page.locator('#gn-burger');
        await expect(burger).toBeHidden();
        const nav = page.locator('#gn-nav');
        await expect(nav).toBeVisible();
      }
    });
  });
}
