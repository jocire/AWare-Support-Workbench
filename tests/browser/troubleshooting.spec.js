const { test, expect } = require('@playwright/test');
const { login, openWorkbench } = require('./helpers');

async function ensureInactive(page) {
  const stop = page.locator('#aware-sw-session-stop');
  if (await stop.count()) {
    await Promise.all([
      page.waitForURL(/page=aware-support-workbench/, { timeout: 90000 }),
      stop.click(),
    ]);
    await expect(page.locator('#aware-sw-session-status')).toHaveAttribute('data-state', 'inactive');
  }
}

test.describe.serial('Support Workbench UI', () => {
  test('renders the focused troubleshooting workspace with no Maintenance Scan UI', async ({ page }) => {
    await login(page);
    await openWorkbench(page);
    await ensureInactive(page);

    await expect(page.locator('#aware-sw-page-title')).toBeVisible();
    await expect(page.locator('#aware-sw-tab-troubleshooting')).toHaveClass(/nav-tab-active/);
    await expect(page.locator('#aware-sw-tab-maintenance')).toHaveCount(0);
    await expect(page.locator('#aware-sw-session-status')).toHaveAttribute('data-state', 'inactive');
    await expect(page.locator('#aware-sw-session-heading')).toHaveText('No Engineer Session Active');
    await expect(page.locator('#aware-sw-plugin-isolation-heading')).toHaveText('Plugin isolation');

    const table = page.locator('#aware-sw-plugin-table');
    await expect(table).toBeVisible();
    await expect(page.locator('#aware-sw-plugin-col-name')).toHaveText('Active plugin');
    await expect(page.locator('#aware-sw-plugin-col-version')).toHaveText('Version');
    await expect(page.locator('#aware-sw-plugin-col-status')).toHaveText('Isolation status');

    const mode = page.locator('#aware-sw-mode');
    await expect(mode).toHaveValue('production_safe');
    await expect(mode.locator('option')).toHaveCount(2);
    await expect(page.locator('#aware-sw-session-submit')).toHaveText('Start Engineer Session');
  });

  test('supports direct plugin selection controls', async ({ page }) => {
    await login(page);
    await openWorkbench(page);
    await ensureInactive(page);

    const boxes = page.locator('.aware-sw-plugin-select');
    test.skip(await boxes.count() < 1, 'No isolatable plugins are active.');

    await page.locator('#aware-sw-plugins-select-all').click();
    await expect(boxes.first()).toBeChecked();
    await expect(page.locator('#aware-sw-plugin-toggle-all')).toBeChecked();

    await page.locator('#aware-sw-plugins-clear').click();
    await expect(boxes.first()).not.toBeChecked();
    await expect(page.locator('#aware-sw-plugin-toggle-all')).not.toBeChecked();

    await page.locator('#aware-sw-plugin-toggle-all').check();
    await expect(boxes.first()).toBeChecked();
    await page.locator('#aware-sw-plugin-toggle-all').uncheck();
    await expect(boxes.first()).not.toBeChecked();
  });

  test('starts, updates, and stops an Engineer Session', async ({ page }) => {
    test.setTimeout(180000);
    await login(page);
    await openWorkbench(page);
    await ensureInactive(page);

    const boxes = page.locator('.aware-sw-plugin-select');
    test.skip(await boxes.count() < 1, 'No isolatable plugins are active.');

    await boxes.first().check();
    await page.locator('#aware-sw-mode').selectOption('production_safe');

    await Promise.all([
      page.waitForURL(/started=1/, { timeout: 90000 }),
      page.locator('#aware-sw-session-submit').click(),
    ]);

    await expect(page.locator('#aware-sw-session-status')).toHaveAttribute('data-state', 'active');
    await expect(page.locator('#aware-sw-session-heading')).toHaveText('Engineer Session Active');
    await expect(page.locator('#aware-sw-session-started-message')).toBeVisible();
    await expect(page.locator('#aware-sw-session-notice')).toBeVisible();
    await expect(page.locator('#wp-admin-bar-aware-sw-engineer-session')).toBeVisible();
    await expect(page.locator('#aware-sw-session-submit')).toHaveText('Update Engineer Session');
    await expect(page.locator('#aware-sw-session-stop')).toBeVisible();
    await expect(page.locator('#aware-sw-isolation-verified')).toBeVisible();

    await page.locator('#aware-sw-mode').selectOption('sandbox');
    await Promise.all([
      page.waitForURL(/started=1/, { timeout: 90000 }),
      page.locator('#aware-sw-session-submit').click(),
    ]);
    await expect(page.locator('#aware-sw-session-status')).toContainText('Sandbox / Development');

    await Promise.all([
      page.waitForURL(/stopped=1/, { timeout: 90000 }),
      page.locator('#aware-sw-session-stop').click(),
    ]);
    await expect(page.locator('#aware-sw-session-status')).toHaveAttribute('data-state', 'inactive');
    await expect(page.locator('#aware-sw-session-stopped-message')).toBeVisible();
    await expect(page.locator('#aware-sw-session-notice')).toHaveCount(0);
    await expect(page.locator('#wp-admin-bar-aware-sw-engineer-session')).toHaveCount(0);
  });

});
