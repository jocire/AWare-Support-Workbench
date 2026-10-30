
# AWare Support Workbench

AWare Support Workbench provides session-scoped plugin isolation for WordPress support engineers, allowing plugin conflicts to be investigated on live websites without changing global plugin activation states.

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

Only selected active plugins can be temporarily excluded from loading. The plugin does not support session-specific activation of inactive plugins, theme switching, or isolated content and settings changes.

## Engineer Session modes

### Production Safe

Production Safe mode is intended for non-destructive troubleshooting directly on a live WordPress website while minimizing impact on other users.

Selected active plugins are temporarily excluded from loading only within the engineer's authenticated browser session. Their global activation state remains unchanged.

Engineers may:

- temporarily isolate selected active plugins;
- navigate the website's frontend and administration;
- reproduce and investigate plugin conflicts;
- compare behavior with and without selected plugins;
- end the session to restore normal plugin loading.

Engineers must not modify WordPress content, settings, plugins, themes, or other production data.

These are operational restrictions. The current implementation does not provide comprehensive technical enforcement against persistent changes.

### Sandbox

Sandbox mode provides the same session-scoped plugin isolation mechanism but is intended for broader diagnostic workflows with fewer operational restrictions.

**Sandbox mode is not an isolated WordPress installation, database, or staging environment.**

It shares the production website's database, filesystem, configuration, and external integrations.

Changes to content, settings, plugins, themes, or other persistent data may affect the live website.

Sandbox mode does not automatically prevent or reverse production changes.

For troubleshooting that requires persistent modifications, a separate staging environment is recommended.

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

Plugin isolation is implemented by filtering WordPress's effective active-plugin lists for validated Engineer Session requests.

The normal plugin activation configuration remains unchanged.

When AWare Support Workbench is deactivated, active Engineer Sessions are terminated and the managed MU bootstrap is removed.

## Safety model

Engineer Sessions are designed to isolate plugin loading without modifying the website's global plugin activation configuration.

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

These safeguards protect the plugin-isolation mechanism. They do not guarantee that all operations performed during an Engineer Session are read-only.

The current implementation does not provide complete database, filesystem, or external-service isolation.

## Important limitations

**AWare Support Workbench provides session-specific plugin isolation, not a complete sandbox or staging environment.**

### Supported troubleshooting operations

The following operations are supported within an Engineer Session:

- Temporarily isolate selected active WordPress plugins.
- Investigate plugin conflicts and compatibility issues.
- Navigate the frontend and WordPress administration without intentionally changing production data.
- Compare behavior with different combinations of isolated plugins.
- End the session to restore normal plugin loading.

Selected plugins remain globally active and continue to load for requests outside the validated Engineer Session.

### Restricted operations

During production troubleshooting, engineers must not:

- create, edit, publish, or delete posts, pages, products, or other content;
- change WordPress, plugin, or theme settings;
- install, activate, deactivate, update, or delete plugins or themes through the normal WordPress administration controls;
- modify menus, widgets, templates, or other persistent site configuration;
- create, edit, or delete users, roles, or permissions;
- modify database records or files;
- submit forms, place orders, or perform other actions that modify production data;
- trigger operations that send emails, process payments, or modify external services.

**These restrictions describe permitted usage, not guaranteed technical blocking.**

Workbench does not currently provide comprehensive write protection for WordPress administration, AJAX, REST API, or frontend operations.

### Technical isolation boundaries

The following components are not automatically isolated:

- **Database:** Engineer Sessions use the production database. Persistent database changes can affect the live website.
- **Filesystem:** Plugins, themes, media, and other files remain shared across sessions.
- **Must-use plugins:** MU plugins and code executed before the Workbench bootstrap are not isolated.
- **WordPress settings:** Changes made through the normal WordPress administration interfaces can affect production.
- **Caching:** Persistent object caches, page caches, and other caching layers may interfere with session-specific behavior.
- **Background processing:** WordPress cron jobs, scheduled actions, and asynchronous processes are not isolated.
- **External integrations:** API calls, emails, payment processing, webhooks, and third-party services may still execute.
- **Server configuration:** Hosting infrastructure, PHP configuration, web server rules, and server-level services remain shared.

AJAX, REST API, and frontend interactions may trigger persistent changes even when no WordPress settings are explicitly saved.

Compatibility with persistent caching, multisite installations, concurrent Engineer Sessions, and unusual authentication configurations requires additional validation.

### Session termination and restoration

Ending or expiring an Engineer Session restores normal plugin loading for subsequent requests.

However, ending a session does not:

- undo database modifications;
- restore modified or deleted files;
- revert WordPress settings;
- reverse external API requests;
- cancel emails, payments, or other completed actions;
- automatically clear caches or undo background operations.

**Important:** Engineers should perform non-destructive troubleshooting only when working directly on production websites.

For investigations that require modifying content, settings, plugins, themes, or other persistent state, use a separate staging environment.

## Requirements

- WordPress 6.5 or newer
- PHP 8.1 or newer
- Permission to install plugins and manage the site's MU plugin directory

The WordPress installation must allow AWare Support Workbench to create and remove its managed MU bootstrap.

## Installation

1. Upload the AWare Support Workbench ZIP through **Plugins → Add New → Upload Plugin**.
2. Activate **AWare Support Workbench**.
3. Open **Tools → Support Workbench**.
4. Select the active plugins you want to isolate.
5. Choose an Engineer Session mode.
6. Start the Engineer Session.
7. Reproduce and investigate the issue in the same browser session without making persistent production changes.
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
