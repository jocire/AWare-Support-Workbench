# How Isolation Works

## Why a must-use bootstrap is required

Normal WordPress plugins load after WordPress has already started resolving the active plugin list. A normal plugin cannot reliably prevent another normal plugin from being loaded for the current request.

Workbench therefore installs a tiny managed MU bootstrap that runs earlier.

## Request flow

```text
Request begins
   |
   v
WordPress loads MU plugins
   |
   v
AWare bootstrap
   |
   +-- no aware_sw_session cookie --> immediate return
   |
   +-- token present
         |
         +-- resolve WordPress logged-in cookie
         +-- hash token + login cookie
         +-- query active unexpired Workbench session
         +-- read disabled_plugins from session config
         |
         +-- add option filters
                |
                +-- option_active_plugins
                +-- site_option_active_sitewide_plugins
   |
   v
WordPress loads the resulting effective plugin list
```

## Important implementation detail

Workbench filters plugin option **reads**. It does not implement isolation by writing a reduced plugin list back to `active_plugins`.

Conceptually:

```php
add_filter( 'option_active_plugins', ... );
```

not:

```php
update_option( 'active_plugins', ... );
```

The same applies to network-active plugin filtering.

## Fail-open behavior

If the session table cannot produce a valid matching active session, the bootstrap returns and WordPress uses the normal plugin configuration.

This prevents a stale/invalid Workbench cookie from globally disabling plugins.

## Idle path

When no diagnostic cookie is present, the bootstrap returns before session lookup or diagnostic instrumentation. Normal visitors therefore avoid the troubleshooting workflow entirely.

## What is and is not isolated

Isolated:

- effective normal plugin loading for the validated request;
- effective network-plugin loading for the validated request.

Not automatically isolated:

- database writes;
- filesystem writes;
- object/page caches;
- external APIs/webhooks;
- cron/background jobs;
- email/payments/orders;
- other shared state.

See [Safety Model](Safety-Model).
