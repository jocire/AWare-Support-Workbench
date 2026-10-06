# AWare Support Workbench

AWare Support Workbench provides production-safe troubleshooting sessions for WordPress support engineers.

It allows an engineer to temporarily isolate selected plugins for a specific authenticated browser session without changing the site-wide plugin state for other administrators, visitors, background requests, or normal site traffic.

## Version

1.0.0-rc1

## What it does

AWare Support Workbench creates temporary Engineer Sessions that can isolate selected plugins for troubleshooting.

Isolation is:

- limited to the engineer's authenticated browser session;
- bound to a temporary diagnostic token;
- bound to the current WordPress login cookie;
- request-scoped rather than site-wide;
- automatically terminated when the session expires or is stopped;
- designed to fail open to the site's normal plugin state when the session is invalid.

The plugin does not globally deactivate selected plugins and does not modify the site's normal `active_plugins` configuration during an Engineer Session.

## Engineer Session modes

### Production Safe

Production Safe mode
