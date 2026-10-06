# Development Setup

This page describes the supported local development setup for AWare Support Workbench.

## Required tools

For normal PHP/unit/contract development:

- WordPress development/test site
- PHP CLI compatible with the plugin's supported PHP version
- WP-CLI
- Git
- VS Code or another editor

For browser acceptance tests, also install:

- Node.js 20 or newer
- npm (included with Node.js)
- Playwright Chromium (`npx playwright install chromium`)

## Composer

Composer is **not currently required**. The plugin has no `composer.json` and no Composer-managed PHP dependencies. Do not add Composer just to run the current test suite. If PHP dependencies are introduced later, this page and the root README should be updated at the same time.

## Clone / local repository

Example:

```powershell
git clone https://github.com/jocire/AWare-Support-Workbench.git
cd AWare-Support-Workbench
```

If you already copied the plugin into a local folder, initialize/connect it as described in [Git Workflow](Git-Workflow).

## Install Node test dependencies

From the repository root:

```powershell
npm install
npx playwright install chromium
```

`npm install` installs development/test dependencies only. They are not shipped as part of the WordPress plugin runtime.

## Local environment file

Copy `.env.example` to `.env`:

```powershell
Copy-Item .env.example .env
```

Edit `.env`:

```dotenv
AWARE_WP_BASE_URL=https://wpml-test.test
AWARE_WP_USER=your-admin-user
AWARE_WP_PASSWORD=your-password
```

Do **not** use PowerShell `$env:NAME=...` syntax inside `.env`. That syntax is only for setting environment variables interactively in a PowerShell session.

`.env` is ignored by Git. Never commit real WordPress credentials.

The Playwright configuration loads `.env` through `dotenv`. These variables are used only by browser acceptance tests and are not read by the production WordPress plugin.

## WordPress installation

The browser tests exercise the plugin that is installed on the WordPress site configured by `AWARE_WP_BASE_URL`. Running `npm` from `C:\dev\aware-support-workbench` does not automatically make WordPress use that checkout.

### Recommended Windows/Laragon setup: directory junction

Keep the Git checkout at:

```text
C:\dev\aware-support-workbench
```

and expose that exact working tree to WordPress with a directory junction. This avoids copying files after every edit.

First make sure this path does not already contain a different copy of the plugin:

```text
C:\laragon\www\WPML-Test\wp-content\plugins\aware-support-workbench
```

If it contains an older copied build, deactivate it in WordPress and rename/remove that folder. Then, from an Administrator PowerShell if required:

```powershell
New-Item -ItemType Junction `
  -Path "C:\laragon\www\WPML-Test\wp-content\plugins\aware-support-workbench" `
  -Target "C:\dev\aware-support-workbench"
```

Verify it:

```powershell
Get-Item "C:\laragon\www\WPML-Test\wp-content\plugins\aware-support-workbench" | Format-List FullName,LinkType,Target
```

WordPress now executes the same files that VS Code edits in the Git checkout. Activate **AWare Support Workbench** normally in WordPress.

Confirm the managed MU bootstrap exists at:

```text
wp-content/mu-plugins/aware-support-workbench-bootstrap.php
```

## Test layers

### Fast PHP/contract suite

Run after every meaningful PHP change:

```powershell
npm run test:php
```

Equivalent direct command:

```powershell
php tests/run.php
```

This is fast and does not require a browser.

### Browser acceptance tests

These require the WordPress site in `.env` to be running:

```powershell
npm run test:browser
```

### Full repository test command

```powershell
npm test
```

This runs the PHP/contract suite followed by the browser acceptance suite.

## Recommended workflow

1. Pull latest changes.
2. `npm install` if `package.json` changed.
3. Make the code change.
4. Run `npm run test:php`.
5. Run `npm run test:browser` for UI/browser-affecting changes.
6. Run `npm test` before a release candidate.
7. Review `git diff`.
8. Commit and push.

## Troubleshooting

If Laragon uses a locally trusted HTTPS certificate, Playwright is configured to tolerate local HTTPS certificate errors for the test target.


### Browser-test authentication or missing-control failures

If a browser test reports a missing Workbench button, tab, or admin element, first verify all of the following before treating it as a plugin defect:

1. `AWARE_WP_BASE_URL` points to the intended Laragon site.
2. The `.env` credentials can log into that site as an administrator.
3. The Workbench plugin is active on that same site.
4. The WordPress plugin directory points to the current Git checkout (the junction setup above is recommended).
5. The checkout and installed plugin both report the same development version.

The browser helper deliberately fails with explicit login, permission, HTTP, or plugin-availability errors so environment failures are distinguishable from UI regressions. Browser tests run serially because troubleshooting/session tests mutate shared WordPress state.

If Chromium is missing:

```powershell
npx playwright install chromium
```

If Node dependencies are inconsistent:

```powershell
Remove-Item -Recurse -Force node_modules
npm install
```
