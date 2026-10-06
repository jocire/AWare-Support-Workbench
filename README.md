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

Production Safe mode is intended for troubleshooting directly on a live WordPress site while minimizing impact on other users.

Selected plugins are isolated only for the engineer's session.

### Sandbox

Sandbox mode provides the same session-scoped isolation model but is intended for broader troubleshooting workflows where the engineer deliberately wants fewer restrictions.

## How isolation works

AWare Support Workbench installs a small managed MU bootstrap.

The bootstrap runs early in the WordPress request lifecycle so selected plugins can be filtered before normal plugins load.

The MU bootstrap contains only the minimum logic required for request and session isolation. The main plugin remains responsible for:

- the administrative interface;
- Engineer Session management;
- authorization;
- session configuration;
- bootstrap installation and maintenance;
- cleanup and lifecycle management.

When AWare Support Workbench is deactivated, active Engineer Sessions are terminated and the managed MU bootstrap is removed.

## Safety model

Engineer Sessions are designed to avoid permanent or global configuration changes.

Key safeguards include:

- authenticated administrator access;
- nonce-protected session-changing actions;
- browser-specific session binding;
- diagnostic token validation;
- session expiration;
- Workbench self-protection;
- fail-open behavior for invalid or expired sessions;
- automatic cleanup on session stop and plugin deactivation.

AWare Support Workbench itself cannot be selected for isolation.

## Important limitations

An Engineer Session is not a cloned staging environment.

Code executed before the MU bootstrap, server-level configuration, external services, persistent database writes performed by other software, and infrastructure outside WordPress are not isolated by this plugin.

Troubleshooting on production websites should still be performed carefully.

## Requirements

- WordPress 6.5 or newer
- PHP 8.1 or newer
- Permission to install plugins and manage the site's MU plugin directory

The WordPress installation must allow AWare Support Workbench to create and remove its managed MU bootstrap.

## Installation

1. Upload the AWare Support Workbench ZIP through **Plugins → Add New → Upload Plugin**.
2. Activate **AWare Support Workbench**.
3. Open **Tools → Support Workbench**.
4. Select the plugins you want to isolate.
5. Choose an Engineer Session mode.
6. Start the Engineer Session.
7. Reproduce and investigate the issue in the same browser session.
8. Stop the Engineer Session when troubleshooting is complete.

## Uninstallation

Deactivating AWare Support Workbench terminates active Engineer Sessions and removes the managed MU bootstrap.

Uninstalling the plugin also removes its stored session data.

## Documentation

Architecture notes, security information, development guidance, and additional documentation are available in the project repository and GitHub Wiki.

## Support and issues

Please report bugs and technical issues through the project repository.

Security issues should be reported privately according to the repository's security policy.

## License

AWare Support Workbench is licensed under the GNU General Public License v2.0 or later.
