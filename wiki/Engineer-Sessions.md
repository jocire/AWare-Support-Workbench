# Engineer Sessions

An Engineer Session is a short-lived server-side troubleshooting context bound to one authenticated browser login.

## Session identity

A session is identified by a high-entropy browser token. Workbench stores only the token hash in the session table.

The session is additionally bound to the hash of the originating WordPress `wordpress_logged_in_*` cookie.

That distinction matters because two browsers can be logged into the **same WordPress account** while having different authentication-cookie values. Only the browser whose login cookie matches the stored hash receives the isolation context.

## Session data

The session record contains small control state such as:

- token hash;
- user ID;
- authentication-cookie hash;
- mode;
- selected/disabled plugin list;
- status;
- timestamps / expiry.

It is not a copy of the site.

## Request validation

For a request to receive isolation, all relevant checks must succeed:

1. diagnostic cookie exists;
2. token format is valid;
3. matching server-side session exists;
4. session status is active;
5. session is not expired;
6. originating WordPress login-cookie hash matches.

If validation fails, the bootstrap does not apply the diagnostic plugin filter and WordPress proceeds with the normal production configuration.

## Same account, different browser

Expected behavior:

```text
Browser A
same WP user
login cookie A
Workbench token T
=> isolation applies

Browser B
same WP user
login cookie B
Workbench token absent or copied T
=> isolation does not apply
```

The session/authentication contracts and browser workflow tests protect this boundary; any reproducible cross-browser leakage remains a release blocker.
