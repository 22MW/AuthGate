<?php

/**
 * Plugin Name: AuthGate by 22MW
 * Plugin URI: https://wordpress.org/plugins/authgate
 * Description: Frontend authentication & access control for WordPress
 * Version: 1.1.1
 * Author: 22MW
 * Author URI: http://22mw.online
 * License: GPLv2 o posterior
 * Text Domain: authgate
 * Domain Path: /languages
 */

// Salida de seguridad.
defined('ABSPATH') || exit;

add_action('init', function () {
    load_plugin_textdomain('authgate', false, dirname(plugin_basename(__FILE__)) . '/languages');
}, 1);

// Sistema de autenticación
require_once __DIR__ . '/includes/class-auth-install.php';
require_once __DIR__ . '/includes/class-auth-settings.php';
require_once __DIR__ . '/includes/class-auth-forms.php';
require_once __DIR__ . '/includes/class-github-updater.php';

// Auto-updater desde GitHub Releases
add_action('init', function () {
    (new AuthGate_Github_Updater())->register_hooks();
});

// Ocultar barra de admin a no-admins
add_filter('show_admin_bar', function ($show) {
    return current_user_can('manage_options') ? $show : false;
});

register_activation_hook(__FILE__, array('AuthGate_Install', 'activate'));

add_action('plugins_loaded', function () {
    AuthGate_Install::maybe_upgrade();
    AuthGate_Settings::get_instance();
    AuthGate_Forms::get_instance();

    if (class_exists('WooCommerce')) {
        require_once __DIR__ . '/integrations/woocommerce/class-wc-integration.php';
        AuthGate_WC_Integration::get_instance();
    }
});
