# Contributing

AWare Support Workbench is currently developed as a private engineering project.

## Before submitting changes

1. Keep the managed MU bootstrap minimal.
2. Do not implement Engineer Session isolation by persisting a reduced global plugin list.
3. Keep shared-state mutations separate from request-scoped isolation.
4. Add regression coverage for bug fixes and safety-sensitive changes.
5. Run `php tests/run.php` and PHP lint.
6. Run the browser acceptance suite for UI/workflow changes.
7. Run the browser acceptance suite for release-candidate changes to the session/bootstrap engine.
8. Update README/Wiki/CHANGELOG when behavior changes.

Never commit credentials, customer data, cookies, API keys, production exports, or private support evidence.
