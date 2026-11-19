const { test, expect } = require('@playwright/test');

const VIEWPORTS = [
  { name: 'iPhone (375px)', width: 375, height: 812 },
  { name: 'Tablet (768px)', width: 768, height: 1024 },
  { name: 'Laptop (1280px)', width: 1280, height: 800 },
  { name: 'Wide Desktop (1440px)', width: 1440, height: 900 },
];

for (const vp of VIEWPORTS) {
  test.describe(`Verifikasi OPAC - ${vp.name}`, () => {
    test.use({ viewport: { width: vp.width, height: vp.height } });

    test(`Memenuhi standar responsive di ${vp.name}`, async ({ page }) => {
      await page.goto('http://perpusdev.test/opac');

      // 1. Cek Scroll Horizontal
      const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
      const innerWidth = await page.evaluate(() => window.innerWidth);
      console.log(`[${vp.name}] innerWidth: ${innerWidth}, scrollWidth: ${scrollWidth}`);
      expect(scrollWidth).toBeLessThanOrEqual(innerWidth);

      // 2. Cek Search Input
      const searchInput = page.locator('input[name="search"]');
      await expect(searchInput).toBeVisible();

      if (vp.width === 375) {
        // Pada HP, Bottom Navigation Bar harus aktif & terlihat
        const bnav = page.locator('.gn-bottom-nav');
        await expect(bnav).toBeVisible();

        // Filter button drawer terlihat di HP
        const btnFilterSheet = page.locator('#btnToggleFilterSheet');
        await expect(btnFilterSheet).toBeVisible();

        // Cek interaksi Bottom Sheet: Buka drawer
        await btnFilterSheet.click();
        const drawer = page.locator('#opacSidebarDrawer');
        await expect(drawer).toHaveClass(/is-open/);
        const backdrop = page.locator('#opacDrawerBackdrop');
        await expect(backdrop).toHaveClass(/is-open/);

        // Cek tombol tutup drawer (minimal 44x44px)
        const closeBtn = page.locator('#btnCloseFilterSheet');
        await expect(closeBtn).toBeVisible();
        const closeBtnBox = await closeBtn.boundingBox();
        expect(closeBtnBox.width).toBeGreaterThanOrEqual(44);
        expect(closeBtnBox.height).toBeGreaterThanOrEqual(44);

        // Tutup drawer
        await closeBtn.click();
        await expect(drawer).not.toHaveClass(/is-open/);

        // 3. Cek Grid Katalog Buku di HP = 2 kolom proporsional & compact
        const cards = page.locator('.opac-catalog-grid > .opac-col-book');
        const cardCount = await cards.count();
        console.log(`[${vp.name}] Jumlah kartu buku: ${cardCount}`);
        expect(cardCount).toBeGreaterThan(0);

        if (cardCount >= 4) {
          const card1 = await cards.nth(0).boundingBox();
          const card2 = await cards.nth(1).boundingBox();
          const card3 = await cards.nth(2).boundingBox();
          const card4 = await cards.nth(3).boundingBox();
          // Baris 1: kartu 1 dan 2 berada di baris yang sama (top hampir identik)
          expect(Math.abs(card1.y - card2.y)).toBeLessThan(10);
          // Baris 2: kartu 3 dan 4 berada di baris berikutnya yang sama
          expect(Math.abs(card3.y - card4.y)).toBeLessThan(10);
        }

        // 4. Cek Aspect-Ratio Cover Buku ~ 2/3 (0.67)
        const book3d = page.locator('.opac-book-3d').first();
        if (await book3d.count() > 0) {
          const bBox = await book3d.boundingBox();
          const ratio = bBox.width / bBox.height;
          console.log(`[${vp.name}] opac-book-3d aspect ratio: ${ratio.toFixed(2)} (target ~0.67)`);
          expect(ratio).toBeGreaterThan(0.55);
          expect(ratio).toBeLessThan(0.78);
        }

        // 5. Cek Rak Shelf Horizontal memuat buku compact (lebar ~78px)
        const shelfItem = page.locator('.opac-shelf-item').first();
        if (await shelfItem.count() > 0) {
          const itemBox = await shelfItem.boundingBox();
          console.log(`[${vp.name}] shelf item width: ${itemBox.width.toFixed(1)}px (target ~78px)`);
          expect(itemBox.width).toBeLessThanOrEqual(85);
        }
      }

      if (vp.width === 768) {
        // Tablet: 4 buku per baris
        const cards = page.locator('.opac-catalog-grid > .opac-col-book');
        const cardCount = await cards.count();
        console.log(`[${vp.name}] Jumlah kartu buku tablet: ${cardCount}`);
        expect(cardCount).toBeGreaterThan(0);

        // Bottom nav tersembunyi di tablet
        const bnav = page.locator('.gn-bottom-nav');
        await expect(bnav).toBeHidden();
      }

      if (vp.width === 1280) {
        // Desktop Laptop: Tombol drawer HP tersembunyi
        const btnFilterSheet = page.locator('#btnToggleFilterSheet');
        await expect(btnFilterSheet).toBeHidden();

        // Desktop Laptop: Bottom Nav tersembunyi
        const bnav = page.locator('.gn-bottom-nav');
        await expect(bnav).toBeHidden();

        // Desktop Laptop: Sidebar ramping terlihat di kiri
        const sidebar = page.locator('#opacSidebarDrawer');
        await expect(sidebar).toBeVisible();

        // Desktop: Menu Navigasi Utama terlihat
        const nav = page.locator('#gn-nav');
        await expect(nav).toBeVisible();
      }

      if (vp.width === 1440) {
        // Wide Desktop: Grid 4 buku per baris sesuai desain
        const cards = page.locator('.opac-catalog-grid > .opac-col-book');
        const cardCount = await cards.count();
        console.log(`[${vp.name}] Jumlah kartu buku wide desktop: ${cardCount}`);
        expect(cardCount).toBeGreaterThan(0);

        if (cardCount >= 4) {
          const card1 = await cards.nth(0).boundingBox();
          const card2 = await cards.nth(1).boundingBox();
          const card3 = await cards.nth(2).boundingBox();
          const card4 = await cards.nth(3).boundingBox();
          expect(Math.abs(card1.y - card2.y)).toBeLessThan(10);
          expect(Math.abs(card2.y - card3.y)).toBeLessThan(10);
          expect(Math.abs(card3.y - card4.y)).toBeLessThan(10);
        }
      }
    });
  });
}
