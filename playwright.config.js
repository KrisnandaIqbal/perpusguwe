// @ts-check
const { defineConfig, devices } = require('@playwright/test');

// Ubah lewat variabel lingkungan, mis. E2E_BASE_URL=http://localhost/ npx playwright test
const baseURL = process.env.E2E_BASE_URL || 'http://perpusguwe-main.test/';

module.exports = defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  retries: 0,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'], channel: 'chrome' } },
  ],
});
