# Testing

## PHP/contracts

```bash
npm run test:php
```

These cover Engineer Session behavior, authentication binding, MU filtering, lifecycle removal/recreation, security, focused UI contracts, and regression guards that prevent removed Maintenance Scan/diagnostics subsystems from returning.

## Browser UI

```bash
npm run test:browser
```

The Playwright suite runs against a real WordPress test site, uses direct stable selectors, and validates every normal Engineer Session control/state: inactive state, plugin selection, both modes, start/update/stop lifecycle, persistent notice, admin-bar indicator, and early-isolation status.

## Full release gate

```bash
npm test
```

This runs the PHP/contract suite followed by the browser acceptance suite. Both must pass for a release candidate.
