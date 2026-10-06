require('dotenv').config();
const { defineConfig } = require('@playwright/test');
module.exports = defineConfig({
  testDir: './tests/browser',
  timeout: 90000,
  fullyParallel: false,
  workers: 1,
  expect: { timeout: 10000 },
  use: {
    baseURL: process.env.AWARE_WP_BASE_URL || 'https://wpml-test.test',
    ignoreHTTPSErrors: true,
    trace: 'retain-on-failure'
  }
});
