# Architecture

## Runtime model

```text
request
  |
  +-- managed MU bootstrap
  |     +-- no Workbench cookie -> return immediately
  |     +-- validate session token/auth-cookie hash/expiry
  |     +-- filter effective plugin list for this request
  |
  +-- normal Support Workbench plugin
        +-- Engineer Session lifecycle
        +-- admin UI and permissions
```

Session state is stored server-side and addressed by an opaque high-entropy token. The session is also bound to the originating WordPress logged-in cookie material, so the same account in another browser does not automatically inherit isolation.

The normal plugin owns UI, session lifecycle, and managed-bootstrap lifecycle. The MU bootstrap contains only the minimum early-loading isolation logic. There is no crawler or diagnostics/evidence collection subsystem in the final Support Workbench scope.
