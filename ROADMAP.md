# AWare Support Workbench — Roadmap

## Product goal

Create a focused WordPress support-engineering tool that lets an engineer reproduce a real plugin conflict on the actual site while selected plugins are removed only from that engineer's authenticated browser context.

Core workflow:

**Reproduce → Isolate → Compare → Narrow → Resolve or Escalate**

## Final product boundary

Support Workbench is intentionally small. It provides:

- browser-bound Engineer Sessions;
- request-scoped plugin isolation;
- Production Safe and Sandbox / Development modes;
- clear active/inactive status and production-state warnings;
- safe activation/deactivation management of the early MU bootstrap;
- tests proving that other browsers and production configuration remain unaffected.

Support Workbench does **not** contain a maintenance crawler, URL inventory, bulk scanner, targeted diagnostics panel, browser/PHP evidence collector, asset profiler, or scan history. Those are separate product concerns.

## Current foundation

- dedicated server-side Engineer Session table;
- high-entropy opaque session token;
- session bound to WordPress user and originating login-cookie material;
- managed early MU bootstrap;
- request-scoped filtering of normal and network-active plugins;
- no-token bootstrap fast path;
- bootstrap refresh on update;
- bootstrap removal and session termination on deactivation;
- bootstrap recreation on activation;
- direct-selector browser acceptance coverage;
- focused PHP/contract and browser acceptance suites.

## Remaining hardening

### Isolation reliability

- persistent object-cache validation;
- multisite validation;
- concurrent Engineer Session validation;
- unusual/custom authentication-cookie validation;
- fail-safe behavior under partial filesystem/database failure.

### Compatibility

- validate common hosting stacks and security plugins;
- validate network-activated plugin behavior on multisite;
- validate common page/object caches do not leak isolated responses;
- document known incompatibilities clearly.

### Release quality

- keep idle overhead negligible;
- keep the MU bootstrap minimal;
- require PHP/contracts and browser UI tests before release;
- preserve a clear distinction between request-scoped isolation and shared database/files/external side effects.
