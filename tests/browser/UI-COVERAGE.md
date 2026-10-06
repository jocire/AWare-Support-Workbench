# Support Workbench UI coverage

| UI/state | Direct selector | Browser coverage |
| --- | --- | --- |
| Page title | `#aware-sw-page-title` | workspace render |
| Troubleshooting tab | `#aware-sw-tab-troubleshooting` | workspace render |
| Session state container | `#aware-sw-session-status` | inactive + active lifecycle |
| Session heading | `#aware-sw-session-heading` | inactive + active lifecycle |
| Plugin isolation heading | `#aware-sw-plugin-isolation-heading` | workspace render |
| Plugin table | `#aware-sw-plugin-table` | workspace render |
| Name/version/status headers | `#aware-sw-plugin-col-name`, `#aware-sw-plugin-col-version`, `#aware-sw-plugin-col-status` | workspace render |
| Select all | `#aware-sw-plugins-select-all` | selection controls |
| Clear selection | `#aware-sw-plugins-clear` | selection controls |
| Master checkbox | `#aware-sw-plugin-toggle-all` | selection controls |
| Plugin checkboxes | `.aware-sw-plugin-select` | selection + session lifecycle |
| Mode selector | `#aware-sw-mode` | workspace + update lifecycle |
| Start/update session | `#aware-sw-session-submit` | session lifecycle |
| Stop session | `#aware-sw-session-stop` | session lifecycle |
| Admin-bar session indicator | `#wp-admin-bar-aware-sw-engineer-session` | active + stopped state |
| Persistent Engineer Session notice | `#aware-sw-session-notice` | active + stopped state |
| Early-isolation verified state | `#aware-sw-isolation-verified` | active lifecycle |
| Start/stop success messages | `#aware-sw-session-started-message`, `#aware-sw-session-stopped-message` | lifecycle redirects |
| Started/stopped messages | `#aware-sw-session-started-message`, `#aware-sw-session-stopped-message` | lifecycle redirects |

Removed UI is protected by PHP contracts: Maintenance Scan and targeted diagnostics/evidence controls must remain absent.
