# Changelog

All notable changes to WP AuthGate are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versions follow [Semantic Versioning](https://semver.org/).

---

## [1.2.4] — 2026-06-21

### Added
- Added a setting to choose whether blocked technical routes redirect to the home page or to the custom login slug.

### Changed
- Applied the technical-route destination to `/wp-admin`, `wp-login.php`, and `wp-signup.php`.
- Updated public readme text to describe multisite per-site settings more accurately.

### Fixed
- Kept password-reset, reset-password and logout flows out of the technical-route redirect.

### QA
- Manual QA requested before release.

## [1.2.3] — 2026-06-21

### Changed
- Updated plugin header metadata: author, author URI, license URI and compatibility requirements.
- Raised declared requirements to WordPress 6.5+, PHP 8.1+ and WooCommerce 10.0+.

### QA
- Metadata-only release requested by user.

## [1.2.2] — 2026-06-20

### Changed
- Improved multisite network registration copy so it describes the global registration policy correctly.
- Moved the site-level “Config global” link into the header badge style.

### Fixed
- Restored the WooCommerce generated-password setting in each multisite site where WooCommerce is active.
- Fixed site-level AuthGate save toast behavior by processing all AuthGate admin roots.
- Fixed the left layout margin on the site-level AuthGate screen in multisite.

### QA
- Manual QA confirmed by user before release.

## [1.2.1] — 2026-06-20

### Added
- Added multisite scope handling for AuthGate settings with network, site and fallback behavior.
- Added per-site text overrides with fallback to network/default values.
- Added per-site CSS mode to inherit global CSS, override it or disable custom CSS locally.
- Added network-level registration policy control from AuthGate.
- Added a global settings link from each site-level AuthGate screen.

### Changed
- Restored the global CSS tab in network admin so inherited CSS can be edited centrally.
- Moved multisite registration control to the network-level AuthGate settings.
- Renamed the admin Style tab to CSS and moved the Save CSS button below the editor.
- Updated white and dark CSS presets with the latest confirmed frontend sizing and button styles.
- Removed the logo from protected-page cards rendered inside the site theme.
- Kept the logo on the standalone custom AuthGate login URL and linked the custom logo to the site home page.

### Fixed
- Fixed unauthenticated `/wp-admin` access redirecting to WordPress signup instead of the configured AuthGate login slug.
- Improved site-level AuthGate screen styling to match the 22MW admin shell.
- Improved empty per-site text fields so inherited values are visible as guidance.
- Unified AJAX save toasts across AuthGate admin screens and made the toast visible while scrolled down.

### QA
- Manual multisite QA confirmed by user before release.

## [1.2.0.3] — 2026-06-20

### Changed
- Removed the logo from the protected-page card shown inside the site theme.
- Kept the logo on the standalone custom AuthGate login URL.

### Fixed
- Linked the standalone custom login logo to the site home page.

## [1.2.0.2] — 2026-06-20

### Added
- Added multisite scope handling for AuthGate settings with network, site and fallback behavior.
- Added per-site text overrides with fallback to network/default values.
- Added per-site CSS mode to inherit global CSS, override it or disable custom CSS locally.
- Added network-level registration policy control from AuthGate.
- Added a global settings link from each site-level AuthGate screen.

### Changed
- Restored the global Style tab in network admin so inherited CSS can be edited centrally.
- Moved multisite registration control to the network-level AuthGate settings.
- Updated white and dark CSS presets with the latest confirmed frontend sizing and button styles.

### Fixed
- Fixed unauthenticated `/wp-admin` access redirecting to WordPress signup instead of the configured AuthGate login slug.
- Improved site-level AuthGate screen styling to match the 22MW admin shell.
- Improved empty per-site text fields so inherited values are visible as guidance.

## [1.2.0] — 2026-06-17

### Added
- Added the 22MW visual admin shell for the AuthGate settings area.
- Added reusable `22mw-back` base CSS and JavaScript assets for the plugin admin UI.
- Added dark/light admin mode, horizontal section navigation and vertical internal subnavigation.
- Added searchable excluded-pages selector with removable chips.

### Changed
- Normalized the AuthGate admin markup to use the shared `mw22-back` base classes.
- Reduced AuthGate-specific admin CSS and JavaScript to plugin-specific overrides only.
- Improved settings cards, controls, tables, CodeMirror styling and save notifications in the admin area.

### QA
- Manual QA confirmed by user for the updated admin UI, settings save flow, Textos, Estilo, General and protected-page frontend checks.

## [1.1.1] — 2026-06-16

### Added
- Added admin controls for native WordPress registration and WooCommerce generated-password behavior.
- Added `label` support for popup shortcode buttons.
- Added inline home link for embedded forms.
- Added WYSIWYG intro text for inline login, register and combined forms.
- Added dedicated “Textos” settings tab with independent saving.
- Added optional “CSS propio” settings tab with activation checkbox, CodeMirror editor, sanitization and white/dark presets.

### Changed
- Hidden registration UI when WordPress registration is disabled.
- Hidden Mail Mint newsletter checkbox when Mail Mint is not available.
- Improved protected-page card layout, logo handling, inputs, buttons, spacing and default CSS presets.
- Synchronized white and dark CSS presets so only colors differ.

### Security
- Added conservative sanitization for custom CSS before saving.
- Kept settings saves behind capability checks and nonces.

### QA
- Manual QA confirmed for Blocks A, B, C and D.

## [1.1.0.1] — 2026-06-16

### Added
- Added AuthGate admin control for the native WordPress `users_can_register` option.
- Added AuthGate admin control for WooCommerce `woocommerce_registration_generate_password` when WooCommerce is active.

### Changed
- Registration UI is hidden on frontend when WordPress registration is disabled.
- Registration AJAX now respects the native WordPress registration setting.
- WooCommerce integration no longer re-enables AuthGate registration when WordPress/AuthGate registration is disabled.

### QA
- P1 manual QA confirmed by user.

## [1.1.0] — 2026-05-26

### Added
- Multisite network admin settings page (global options) and per-subsite settings page (exclusions + Mail Mint list).
- `subsite_keys()` routing: `excluded_pages` and `mailmint_list_id` stored per-site via `get_option`; all other settings use `get_site_option` on network.
- `login_slug_redirect` setting: configurable redirect URL after login from the standalone `/slug/` page (default: home). Protected-page `redirect_to` flow unchanged.
- AJAX settings save with WordPress-style snackbar Toast (no page reload); toast CSS injected via `wp_add_inline_style`.
- GitHub Releases auto-updater (`class-github-updater.php`): hooks into `site_transient_update_plugins`, prefers named ZIP asset, fixes extracted folder name.
- `uninstall.php`: drops `authgate_log` table, removes all `authgate_*` options and rate-limit transients on uninstall.
- `deploy-release.sh`: automated release script — clean `release` branch, `authgate.zip`, GitHub release with changelog, merge to `main` and tag.
- Media library button now enqueues on both `admin_enqueue_scripts` and `network_admin_enqueue_scripts` to fix network admin context.

## [1.0.0] — 2026-05-26

### Added
- Custom login, register, combined (tabbed), and password-reset forms via shortcodes.
- `[authgate_login]`, `[authgate_register]`, `[authgate_combined]`, `[authgate_reset]`, `[authgate_protected]` shortcodes.
- GDPR required consent checkbox with configurable privacy-policy link.
- Optional newsletter opt-in checkbox.
- Honeypot field + submission timestamp for spam protection.
- Per-IP rate limiting via WordPress transients.
- Popup / overlay form mode (modal triggered by any button).
- Standalone login page with configurable slug (no theme header/footer).
- Password strength indicator and "generate password" helper on registration.
- Show/hide password toggle on all password inputs.
- WooCommerce integration: creates accounts as WC customers, redirects to My Account, respects auto-password setting, forwards `woocommerce_register_form` action, protects My Account endpoints.
- Mail Mint newsletter subscription on registration (respects double opt-in setting, configurable list ID).
- Multisite support: settings panel at Network Admin level, options stored as site options.
- Full admin settings panel (labels, redirects, GDPR text, newsletter text, legal text, Mail Mint list ID, standalone page slug).
- Admin bar hidden for non-admin users.
- `wp-admin`, `wp-login.php`, `wp-signup.php` blocked for non-admins; password-reset flows (`rp`, `resetpass`, `lostpassword`) are always permitted.
- Hook-driven architecture: `authgate_default_redirect`, `authgate_create_user`, `authgate_registration_allowed`, `authgate_show_password_field`, `authgate_register_form_fields`, `authgate_is_protected_page`, `authgate_user_registered`.
- Full i18n readiness — text domain `authgate`, `languages/` folder, all strings wrapped in i18n functions.
