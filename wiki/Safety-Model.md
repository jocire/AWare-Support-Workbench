# Safety Model

## What Workbench can safely claim

For a validated Engineer Session, Workbench is designed to alter the **effective plugin loading decision for matching requests only**. It does not intentionally persist the Engineer Session's selected plugin set into WordPress's normal plugin activation options.

A different browser/login session should continue to receive the production plugin configuration.

## What Workbench must not claim

Workbench should not claim that arbitrary execution on a live WordPress site is "100% isolated" or incapable of affecting production.

All Engineer Session requests still run against shared infrastructure such as:

- the same database;
- the same filesystem/uploads;
- the same object cache;
- the same external services;
- the same payment/email/webhook integrations.

A plugin or theme may perform a shared side effect during an otherwise request-scoped experiment.

## Read-only first

The default production approach is:

```text
Observe
  -> collect evidence
  -> form hypothesis
  -> run bounded request-scoped comparison
  -> compare evidence
  -> recommend next action
```

Persistent mutation is a separate class of operation and needs explicit policy/guardrails.

## Release-blocking invariants

Treat these as release blockers:

- Engineer Session isolation persists selected plugin state into WordPress global activation options;
- session authentication/binding or expiry validation can be bypassed;
- Workbench can isolate itself;
- deactivation leaves the managed MU bootstrap active;
- activation cannot recreate the current managed MU bootstrap;
- the request-scoped bootstrap mutates server configuration or plugin activation state.

## Fail-open principle

When the bootstrap cannot validate its diagnostic context, it should leave the production plugin state alone rather than guessing.

## Production Safe vs Sandbox / Development

Production Safe should prioritize serialized, conservative, read-only-first diagnostics.

Sandbox / Development may later permit broader experiments, but it should use the same evidence model so a production finding can continue into a disposable reproduction environment.
