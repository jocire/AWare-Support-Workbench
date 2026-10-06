const { expect } = require('@playwright/test');

async function login(page) {
  const user = process.env.AWARE_WP_USER;
  const pass = process.env.AWARE_WP_PASSWORD;

  if (!user || !pass) {
    throw new Error('Set AWARE_WP_USER and AWARE_WP_PASSWORD for browser tests.');
  }

  await page.goto('/wp-login.php', { waitUntil: 'domcontentloaded' });
  await page.locator('#user_login').fill(user);
  await page.locator('#user_pass').fill(pass);

  await Promise.all([
    page.waitForLoadState('domcontentloaded'),
    page.locator('#wp-submit').click(),
  ]);

  const loginError = page.locator('#login_error');
  if (await loginError.count()) {
    const message = (await loginError.innerText()).trim();
    throw new Error(`WordPress login failed: ${message}`);
  }

  if (/\/wp-login\.php(?:\?|$)/.test(page.url())) {
    throw new Error(
      `WordPress login did not leave wp-login.php. Check AWARE_WP_BASE_URL, ` +
      `AWARE_WP_USER and AWARE_WP_PASSWORD. Current URL: ${page.url()}`
    );
  }

  // Verify authentication using a URL that is always available to an administrator.
  await page.goto('/wp-admin/', { waitUntil: 'domcontentloaded' });
  if (/\/wp-login\.php(?:\?|$)/.test(page.url())) {
    throw new Error('WordPress authentication cookie was not retained after login.');
  }

  await expect(page.locator('body.wp-admin')).toBeVisible();
}

async function openWorkbench(page, query = '') {
  const suffix = query ? `&${query}` : '';
  const response = await page.goto(
    `/wp-admin/tools.php?page=aware-support-workbench${suffix}`,
    { waitUntil: 'domcontentloaded' }
  );

  if (/\/wp-login\.php(?:\?|$)/.test(page.url())) {
    throw new Error('Workbench navigation redirected to WordPress login.');
  }

  if (response && response.status() >= 400) {
    throw new Error(`Workbench page returned HTTP ${response.status()}: ${page.url()}`);
  }

  const body = await page.locator('body').innerText();
  if (/Sorry, you are not allowed to access this page/i.test(body)) {
    throw new Error(
      'The logged-in user cannot access Support Workbench, or the plugin is not active on the target WordPress site.'
    );
  }

  await expect(page.locator('body.wp-admin')).toBeVisible();
}

module.exports = { login, openWorkbench };
