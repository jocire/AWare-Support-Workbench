# Browser acceptance tests

The Playwright suite exercises the complete normal Support Workbench UI for Engineer Session plugin isolation.

It verifies the inactive and active session states, plugin selection controls, Production Safe and Sandbox/Development modes, session start/update/stop, the persistent admin notice, and the admin-bar Engineer Session indicator.

Run against a local/staging WordPress site with:

```bash
npm run test:browser
```

The suite intentionally contains no crawling, maintenance scanning, targeted diagnostics, or evidence-collector tests. Those features are outside Support Workbench's final scope.
