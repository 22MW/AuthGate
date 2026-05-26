=== WP AuthGate ===
Contributors: 22mw
Tags: login, register, authentication, access control, woocommerce
Requires at least: 6.2
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Frontend authentication & access control for WordPress — custom login, register and password-reset forms with GDPR consent, spam protection, and WooCommerce support.

== Description ==

WP AuthGate replaces the default WordPress login and registration flow with a polished, fully-customisable frontend experience. It ships with:

* **Custom login form** — shortcode `[authgate_login]`
* **Custom registration form** — shortcode `[authgate_register]`
* **Combined form** — shortcode `[authgate_combined]` (tab-based login + register)
* **Password reset** — shortcode `[authgate_reset]`
* **Page protection** — shortcode `[authgate_protected]` to restrict any page to logged-in users
* **Popup / overlay mode** — embed any form inside a modal triggered by a button
* **GDPR checkbox** — required consent field with configurable privacy-policy link
* **Newsletter opt-in** — optional checkbox wired to Mail Mint (or any hook)
* **Spam protection** — honeypot field + submission timestamp check
* **Rate limiting** — per-IP lockout via WordPress transients
* **WooCommerce integration** — creates WooCommerce customers, respects auto-password setting, protects My Account pages, forwards WC registration hooks
* **Standalone login page** — optional minimal page (no theme header/footer) with custom slug
* **Multisite-ready** — settings panel at Network Admin level when Multisite is active

WP AuthGate is hook-driven. Every behaviour can be extended or overridden from a theme or add-on plugin without editing core files.

= WooCommerce integration =

When WooCommerce is active, WP AuthGate automatically:

* Creates new accounts as WooCommerce customers (`wc_create_new_customer`)
* Redirects after login to the My Account page
* Hides the password field when WooCommerce is configured to auto-generate passwords
* Forwards the `woocommerce_register_form` action inside the registration form
* Protects the My Account page and all its endpoints

= Developer hooks =

* `authgate_default_redirect` — filter the post-login redirect URL
* `authgate_create_user` — filter/override user creation (receives `$user_id`, `$email`, `$password`)
* `authgate_registration_allowed` — filter whether registration is open
* `authgate_show_password_field` — filter whether the password field is displayed
* `authgate_register_form_fields` — action fired inside the registration form
* `authgate_is_protected_page` — filter whether the current page requires login
* `authgate_user_registered` — action fired after a successful registration (`$user_id`, `$email`, `$first_name`, `$last_name`, `$opt_in`)

== Installation ==

1. Upload the `authgate` folder to `/wp-content/plugins/`.
2. Activate the plugin through **Plugins > Installed Plugins**.
3. Go to **Settings > AuthGate Config** (or **Network Admin > Settings > AuthGate Config** on Multisite) and configure labels, redirects, and optional integrations.
4. Add the desired shortcode to any page.

== Frequently Asked Questions ==

= Does it work without WooCommerce? =

Yes. WooCommerce support is loaded only when WooCommerce is active. All core features work on plain WordPress.

= Can I style the forms to match my theme? =

The forms inherit your theme's input, label, and button styles. WP AuthGate only adds structural CSS (widths, spacing, layout). You can override any class in your theme stylesheet.

= Is it compatible with Multisite? =

Yes. When Multisite is active the settings panel moves to **Network Admin > Settings** and options are stored as network options, so all sites share a single configuration.

= Where is the admin area blocked? =

WP AuthGate redirects non-admin users away from `/wp-admin`, `wp-login.php`, and `wp-signup.php`. Password-reset flows (`action=rp`, `action=resetpass`, `action=lostpassword`) are always allowed.

== Screenshots ==

1. Combined login/register form with tab navigation.
2. Standalone login page (no theme header/footer).
3. Settings panel in WordPress admin.

== Changelog ==

= 1.1.0 =
* Added multisite support: network admin settings page for global options; per-subsite settings page for exclusions and Mail Mint list.
* Added `login_slug_redirect` setting: configurable redirect after login from the standalone login page.
* Added GitHub Releases auto-updater — updates appear in Dashboard › Updates.
* Added AJAX settings save with snackbar Toast notification (no page reload).
* Added `uninstall.php` for full cleanup on plugin deletion.
* Fixed media library button not opening in network admin context.

= 1.0.0 =
* Initial public release.
* Custom login, register, combined, and password-reset forms via shortcodes.
* GDPR consent and newsletter opt-in checkboxes.
* Honeypot + timestamp spam protection.
* Rate limiting via transients.
* WooCommerce integration (customers, My Account redirect, auto-password, page protection).
* Mail Mint newsletter subscription support.
* Popup / overlay form mode.
* Standalone login page with configurable slug.
* Multisite support (network-level settings and options).
* Full i18n readiness (text domain `authgate`, `languages/` folder).

== Upgrade Notice ==

= 1.0.0 =
Initial release — no upgrade steps required.
