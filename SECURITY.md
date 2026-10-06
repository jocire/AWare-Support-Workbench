# Security Policy

AWare Support Workbench is an administrator-level troubleshooting tool. Security-sensitive changes should preserve the documented session-binding, expiry, fail-open, least-scope, and read-only-first principles.

## Reporting

Do not disclose exploitable findings in public issues. Use the private repository's security/reporting channel or contact the repository owner directly.

## Release blockers

The following are release blockers:

- Engineer Session state leaking into unrelated browser sessions;
- persistent modification of WordPress global plugin activation state by isolation;
- authentication/session binding bypass;
- stale/invalid session continuing to isolate plugins;
- unintended exposure of credentials, cookies, tokens, customer data, or absolute sensitive paths;
- regression failures in session binding, bootstrap lifecycle, or global plugin-state safety contracts.
