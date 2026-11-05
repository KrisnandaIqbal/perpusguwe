const { test, expect } = require('@playwright/test');

test('halaman beranda terbuka tanpa error server', async ({ page }) => {
  const response = await page.goto('/');
  expect(response.status()).toBeLessThan(400);
  await expect(page).toHaveTitle(/.+/);
});
