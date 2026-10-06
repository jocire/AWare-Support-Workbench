# Developer Reference

## `AWare_SW_Installer`

Creates/updates/removes the managed MU bootstrap and performs activation/deactivation lifecycle cleanup.

## `AWare_SW_Session`

Creates, reads, updates, expires, and terminates browser-bound Engineer Sessions.

## `AWare_SW_Admin`

Renders the focused Troubleshooting workspace, plugin-isolation controls, session status, admin notice, and admin-bar indicator. It handles session start/update/stop actions.

## Managed MU bootstrap

Validates the Workbench token, WordPress auth-cookie hash, status and expiry, then filters active plugin/network-plugin values only for that request. It contains no UI, crawler, diagnostics, evidence, or business logic.
