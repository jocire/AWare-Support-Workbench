# Changelog

## 1.0.0-rc1

- Removed the fixture-based live-isolation harness, fixture plugin, dedicated Playwright config, npm commands, and related release documentation.
- Simplified release validation to the PHP/contract suite plus the real WordPress browser acceptance suite.
- Removed Targeted diagnostics and the latest-evidence panel.
- Removed browser/PHP/navigation/asset diagnostics collectors and their storage/instrumentation.
- Removed target-opening admin action and same-site URL launcher.
- Finalized Support Workbench scope around Engineer Session lifecycle and request-scoped plugin isolation.
- Added regression guards requiring removed Maintenance Scan and diagnostics subsystems to remain absent.

## 0.8.0-dev

- Refocused Support Workbench on Engineer Sessions, plugin isolation, and one-URL Targeted diagnostics.
- Removed the bulk Maintenance Scan workspace, URL inventory/crawler, scan queue, pause/resume, scan history, and related runtime/test code from this plugin.
- Added a same-site, nonce/capability-protected Targeted diagnostics launcher available only during an active Engineer Session.
- Added a latest-evidence panel for request duration, peak memory, PHP shutdown error, browser errors/navigation evidence, and queued script/style counts.
- Reworked browser tests around direct selectors for the focused troubleshooting + targeted diagnostics UI.
- Updated product documentation and release contracts to enforce the new boundary; broad proactive scanning belongs in a separate maintenance/scanner product.

## 0.7.3-dev

- Maintenance target inventory is now provenance-based: registered current-user admin menu screens plus real published frontend permalinks only.
- Removed the synthetic Block Editor/New Post target and all other hardcoded/common-route assumptions from Maintenance target discovery/search.
- Search now filters the existing Targets section in place instead of rendering a separate autocomplete UI.
- Frontend targets are loaded in bounded pages with a Load more control; search queries published titles/slugs server-side without preloading every permalink.
- Browser search coverage now derives queries from URLs/keys that actually exist on the test installation.

## 0.7.2-dev

- Hardened the Playwright suite to prefer direct `#id` / `data-*` selectors throughout.
- Added stable nonvisual UI hooks for tabs, headings, plugin-table columns, scan notices, confirmation, and pause/resume controls.
- Fixed the empty-selection browser test so native dialogs are handled before the triggering click.
- Added direct metadata hooks to dynamic backend/frontend autocomplete options.
- Updated UI coverage contracts to enforce the direct-selector testing policy.

## 0.7.1-dev

- Completed a full UI-to-browser-test audit for Troubleshooting and Maintenance Scan.
- Added stable IDs and accessible labels/state attributes for Engineer Session status, mode, action buttons, plugin table, Maintenance target search, target checkboxes, and scan-history cleanup.
- Expanded Playwright coverage for navigation, inactive/active/update/stop session states, bulk/master selection controls, both Engineer Session modes, unified backend/frontend autocomplete, frontend chip removal, empty-selection validation, scan confirmation, scan completion, history cleanup, and removed-control regressions.
- Added accessibility-state handling for Maintenance Scan autocomplete (`aria-expanded`).

- Simplified Maintenance Scan to checkbox selection, **Clear**, and **Scan selected targets** only.
- Removed backend/frontend type and owner filter dropdowns and all associated JavaScript.
- Removed per-row **Scan** buttons, the hidden single-scan form, dedicated single-scan request handling, and associated JavaScript.
- Upgraded Maintenance Scan search to unified AJAX autocomplete across current-site backend admin screens and published frontend URLs/titles.
- Selecting a backend autocomplete result checks and reveals the corresponding backend target; selecting a frontend result adds it to the selected frontend targets.
- Removed the Troubleshooting **Next: scan the affected admin screen** section and its Maintenance Scan CTA.
- Updated browser/regression tests for the simplified Maintenance Scan workflow.
- Increased Playwright's default test timeout to 90 seconds for slower local WordPress environments and changed the fallback test URL to `https://wpml-test.test`.

## 0.6.9-dev

- Removed the redundant **Select visible backend** bulk-selection control from Maintenance Scan.
- Kept explicit target selection, **Clear**, and **Scan selected targets** as the simpler scan workflow.
- Updated browser acceptance coverage to match the current Maintenance Scan UI and verify the removed control stays absent.

## 0.6.8-dev

- Fixed managed MU-bootstrap lifecycle cleanup so deactivation verifies removal instead of silently ignoring `unlink()` failure.
- Activation now records the bootstrap revision only after the managed MU bootstrap was actually written.
- Added Windows/read-only best-effort cleanup and an inert fail-safe if the filesystem refuses deletion.
- Added executable filesystem lifecycle coverage proving deactivate/remove and activate/recreate behavior.

## 0.6.7-dev

- Added dotenv-based local test configuration via `.env.example`; test credentials remain test-only and Git-ignored.
- Added Development Setup and Git Workflow Wiki pages covering PHP/WP-CLI, Node/npm, Playwright, Laragon, VS Code and release-test commands.
- Clarified that Composer is not currently required because the plugin has no Composer-managed PHP dependencies.

- Added a live multi-browser Engineer Session isolation regression suite covering same-user/different-browser binding, anonymous traffic, REST, admin-ajax, invalid/expired contexts, background/WP-CLI exclusion, and production-state invariants.
- Added an isolation source contract that rejects global plugin activation writes and server-setting/file mutation paths from the MU bootstrap.
- Expanded README and GitHub Wiki documentation with detailed usage, Engineer Session internals, isolation architecture, safety boundaries, security guidance, and release gates.
- Added repository hygiene defaults for GitHub/Playwright development artifacts.
- Fixed a PHP 8.1+ deprecation in the MU bootstrap when the network-active plugins option/filter value is `false` instead of an array.
- Network plugin isolation now normalizes the filter input before removing selected plugins.
- Bumped the managed MU bootstrap revision so existing bootstrap files refresh automatically on plugin load.
- Added behavior coverage for a `false` network-active plugin value.

## 0.6.6-dev

- Maintenance Scan now treats PHP notices (`E_NOTICE`, `E_USER_NOTICE`) as findings in addition to warnings/errors.
- Updated PHP finding copy so non-fatal notices and warnings are described accurately.
- Added regression coverage for notice/warning detection.


## 0.6.5-dev

- Fixed row-level **Scan** so it can never fall through into the full Maintenance inventory.
- Removed the duplicate hidden `screen_key` control that could overwrite the clicked row target during native form submission.
- Single-scan requests now fail closed when the target key is missing or invalid instead of scanning every page.
- Modal-confirmed single scans now submit the actual clicked button as the form submitter.
- Added a regression test covering one-target queue semantics.

## 0.6.4-dev

- Fixed inert per-target **Scan** buttons in Maintenance Scan.
- Scan controls now use native form submission as a no-JavaScript fallback.
- JavaScript progressively enhances the native submission with the existing confirmation modal and now uses delegated click handling plus `preventDefault()`.
- Added a regression test covering the exact per-target Scan button contract.

## 0.6.3-dev

- Reduced Maintenance findings to three supported signals: WordPress-related console/runtime errors, actionable PHP errors, and slow requests.
- Removed asset/dependency inference from the finding count.
- Added filtering for browser-extension noise and unrelated external scripts.
- Retained queued assets as optional technical evidence only.

## 0.6.2-dev

- Simplified Maintenance result rows: queued assets are represented by a compact `Queued` tag rather than expanded asset dumps.
- Added issue-only `Details` disclosures with categorized explanations, concrete evidence, why the issue matters, and recommended next checks.
- Kept request time visible in the compact row while normal queued assets remain informational rather than findings.


## 0.6.0-dev

- Added executable behavior tests for Engineer Sessions, MU isolation, Maintenance Scan evidence, and admin-screen discovery.
- Added lifecycle, security, diagnostics, admin-workspace, documentation, and existing Maintenance Scan regression coverage under one runner.
- Added PowerShell and shell test wrappers.
- Added optional Playwright browser acceptance suite.
- Added GitHub Actions PHP test/lint workflow with optional real-site browser acceptance.
- Rebuilt README as the project entry point.
- Added GitHub Wiki source covering installation, troubleshooting, scanning, architecture, testing, security/privacy, developer reference, and release validation.

## 0.5.5-dev

- Made Troubleshooting the first workspace.
- Added bulk plugin isolation selection.
- Consolidated admin-screen diagnostics into Maintenance Scan.
- Added request time, memory, and lightweight PHP error context to Maintenance Scan.
