# Security and Privacy

## Trust model

Workbench is an administrator support tool. Only trusted administrators/support engineers should be able to create or change Engineer Sessions.

## Diagnostic token

The troubleshooting browser receives a high-entropy opaque diagnostic token. Server-side storage uses its hash rather than relying on the raw token as the durable identifier.

## Authentication binding

The Engineer Session is bound to the originating WordPress logged-in cookie hash. The Workbench token by itself is insufficient to activate isolation in another browser login.

## Expiry

Sessions are short-lived and validated against server-side status/expiry on requests that carry the diagnostic cookie.

## Cookie behavior

The Workbench session cookie is intended to be HTTP-only, same-site constrained and secure on HTTPS.

## Evidence minimization

Workbench stores minimal Engineer Session state rather than unbounded browser/server logs. Error/message lengths and collections are capped, and server path exposure is reduced where practical.

## Production configuration

The MU isolation mechanism filters plugin option reads for the request. It must not use global activation/deactivation writes as the implementation of Engineer Session isolation.

## Shared-state warning

Session-scoped plugin loading does not isolate WordPress data, caches, filesystem or external services. Support procedures must avoid dangerous state-changing workflows on live sites unless explicitly designed and guarded.

## Secrets

Do not commit:

- WordPress credentials;
- `.env` files containing secrets;
- API keys;
- production database exports;
- authentication cookies/tokens;
- customer evidence containing sensitive data.

The repository `.gitignore` excludes common local secret/test artifacts, but review commits before pushing.
