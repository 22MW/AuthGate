# Changelog

All notable changes to WP AuthGate are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versions follow [Semantic Versioning](https://semver.org/).

---

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
