# Changelog

All notable changes to WP AuthGate are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versions follow [Semantic Versioning](https://semver.org/).

---

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
