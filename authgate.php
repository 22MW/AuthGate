<?php

/**
 * Plugin Name: AuthGate by 22MW
 * Plugin URI: https://wordpress.org/plugins/authgate
 * Description: Frontend authentication & access control for WordPress
 * Version: 1.0.0
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

// Bloquear wp-admin, wp-login.php y wp-signup.php — redirigir al home si no es admin
add_action('init', function () {
    if (wp_doing_ajax()) return;

    $uri    = $_SERVER['REQUEST_URI'] ?? '';
    $action = $_GET['action'] ?? '';

    // Permitir flujos de recuperación/reset de contraseña en wp-login.php
    $is_password_flow = in_array($action, ['rp', 'resetpass', 'lostpassword'], true);

    $is_restricted = (
        strpos($uri, '/wp-admin') !== false ||
        (strpos($uri, 'wp-login.php') !== false && !$is_password_flow) ||
        strpos($uri, 'wp-signup.php') !== false ||
        strpos($uri, 'wp-register.php') !== false
    );

    if ($is_restricted && !current_user_can('manage_options')) {
        wp_safe_redirect(home_url('/'));
        exit;
    }
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
