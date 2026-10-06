# Release Checklist

- [ ] `npm run test:php` passes.
- [ ] `npm run test:browser` passes against the release build.
- [ ] `npm test` passes from a clean dependency install.
- [ ] Deactivation removes the managed MU bootstrap and terminates active sessions.
- [ ] Reactivation recreates the current managed MU bootstrap.
- [ ] Engineer Session start/update/stop works in the real WordPress browser flow.
- [ ] Workbench cannot isolate itself.
- [ ] No global plugin activation/deactivation API is used to implement Engineer Session isolation.
- [ ] No Maintenance Scan/crawler/targeted diagnostics/evidence collector code is present.
- [ ] `.env`, `node_modules`, Playwright reports/results, and local ZIPs are not staged.
