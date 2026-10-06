# Troubleshooting

## Workbench page is inaccessible

Confirm the normal plugin is active and the current user can activate plugins. If a stale managed MU bootstrap remains after an abnormal development build, deactivate Support Workbench and verify its managed MU file is removed before reactivating. Current lifecycle tests protect this behavior.

## Isolation does not appear to apply

Confirm an Engineer Session is active and check the Workbench status box. If selected plugins are isolated but the early bootstrap did not apply on the current request, Workbench displays an explicit warning.

## Another browser is also isolated

That should not happen. Sessions are bound to both the Workbench token and originating WordPress logged-in cookie hash. Treat any reproducible cross-browser leakage as a release blocker and investigate the session/bootstrap boundary before release.
