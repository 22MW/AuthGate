<?php
defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

// Tabla de logs
// phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $wpdb->prefix . 'authgate_log'));

// Opciones de red (multisite)
if (is_multisite()) {
    delete_site_option('authgate_db_version');
    delete_site_option('authgate_max_attempts');
    delete_site_option('authgate_login_logo_url');
    delete_site_option('authgate_login_slug');
    delete_site_option('authgate_login_slug_redirect');
    delete_site_option('authgate_reset_slug');
    delete_site_option('authgate_block_wp_login');
    delete_site_option('authgate_blocked_route_redirect');
    delete_site_option('authgate_blacklist');

    // Strings editables (prefijo authgate_str_*)
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $wpdb->query("DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE 'authgate_%'");
}

// Opciones de site (single o por subsite)
// phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'authgate_%'");

// Transients del rate limiter
// phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_authgate_%' OR option_name LIKE '_transient_timeout_authgate_%'");
