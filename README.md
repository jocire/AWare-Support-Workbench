# AWare Support Workbench

AWare Support Workbench is a focused WordPress support-engineering plugin for troubleshooting plugin conflicts directly on a live site without globally changing the site's active-plugin configuration.

Current development version: **1.0.0-rc1**

## Core workflow

1. Open **Tools → Support Workbench**.
2. Select one or more currently active plugins to isolate.
3. Choose **Production Safe** or **Sandbox / Development** mode.
4. Start the Engineer Session.
5. Reproduce the real issue normally in the same browser.
6. Change the isolated plugin set and update the Engineer Session as needed.
7. Stop the Engineer Session to restore normal loading in that browser.

## Engineer Session isolation

The managed MU bootstrap runs before normal plugins are loaded. Without a Workbench session cookie it returns immediately. With a valid session it verifies the opaque session token, expiry, and originating WordPress login-cookie hash, then filters the effective plugin list only for that authenticated troubleshooting browser.

Support Workbench does not use WordPress's global plugin activation/deactivation APIs to implement isolation and does not intentionally modify the stored `active_plugins` option for an Engineer Session.

A second browser—even when logged into the same WordPress account—does not inherit the session because its WordPress login-cookie material differs. Anonymous visitors continue using the production plugin configuration.

## Scope

Support Workbench intentionally focuses on Engineer Sessions and request-scoped plugin isolation. It does **not** include broad maintenance crawling, URL inventories, bulk scanning, targeted evidence panels, browser/PHP evidence collectors, or site-health reporting. Those are separate product concerns.

The site database, files, caches, external services, cron jobs, payments, email, and other shared resources remain shared. Engineer Session isolation changes which selected plugins load for validated requests; it is not a cloned environment or transaction boundary.

## Installation

Requirements:

- WordPress 6.5+
- PHP 8.1+
- administrator with plugin-management capability
- writable `wp-content/mu-plugins/` directory

Activate **AWare Support Workbench** normally. The plugin creates and manages:

```text
wp-content/mu-plugins/aware-support-workbench-bootstrap.php
```

On deactivation, active Engineer Sessions are terminated and the managed MU bootstrap is removed. On activation it is recreated from the current canonical template.

## Tests and release gates

Run PHP/contract tests:

```bash
npm run test:php
```

Run browser acceptance tests against the configured WordPress test site:

```bash
npm run test:browser
```

The release gate is the PHP/contract suite plus the browser acceptance suite. The browser tests exercise the final administrator-facing Engineer Session workflow against a real WordPress test site.

## Documentation

The `wiki/` directory contains the canonical installation, architecture, Engineer Session, security, testing, development, and release documentation.
