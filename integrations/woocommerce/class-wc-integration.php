<?php
defined('ABSPATH') || exit;

/**
 * WooCommerce integration for WP AuthGate.
 *
 * Hooks into core AuthGate filters/actions to add WooCommerce-specific behavior.
 * This file can be extracted into a standalone add-on plugin by:
 *   1. Moving this folder to its own plugin directory.
 *   2. Adding a plugin header with "Requires Plugins: authgate".
 *   3. Replacing the direct class reference with a plugins_loaded hook.
 */
class AuthGate_WC_Integration {

    /** @var self|null */
    private static $instance = null;

    /** @return self */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Quitar texto de privacidad nativo de WC (AuthGate usa su propio checkbox GDPR)
        add_action('init', function () {
            remove_action('woocommerce_register_form', 'wc_registration_privacy_policy_text', 20);
        }, 20);

        // Redirect por defecto → Mi Cuenta de WooCommerce
        add_filter('authgate_default_redirect', array($this, 'set_myaccount_redirect'));

        // Crear usuario como cliente WC en lugar de wp_create_user
        add_filter('authgate_create_user', array($this, 'create_wc_customer'), 10, 3);

        // Respetar el permiso de registro definido por WordPress/AuthGate.
        add_filter('authgate_registration_allowed', array($this, 'wc_registration_allowed'));

        // Ocultar campo de contraseña si WC genera contraseña automática
        add_filter('authgate_show_password_field', array($this, 'wc_show_password_field'));

        // Reenviar los hooks nativos de WC dentro del formulario de registro
        add_action('authgate_register_form_fields', array($this, 'forward_wc_register_form'));

        // Proteger páginas de Mi Cuenta y sus endpoints
        add_filter('authgate_is_protected_page', array($this, 'protect_account_page'));

        // Suscribir a Mail Mint tras el registro (si el usuario no ha hecho opt-out)
        add_action('authgate_user_registered', array($this, 'maybe_subscribe_to_mailmint'), 10, 5);
    }

    // -------------------------------------------------------------------------
    // Filtros de integración
    // -------------------------------------------------------------------------

    /**
     * @param string $url
     * @return string
     */
    public function set_myaccount_redirect($url) {
        return function_exists('wc_get_page_permalink')
            ? wc_get_page_permalink('myaccount')
            : $url;
    }

    /**
     * @param int|null       $user_id  null = el core aún no ha creado el usuario
     * @param string         $email
     * @param string         $password
     * @return int|\WP_Error
     */
    public function create_wc_customer($user_id, $email, $password) {
        if (!function_exists('wc_create_new_customer')) return $user_id;
        return wc_create_new_customer($email, '', $password);
    }

    /**
     * @param bool $allowed
     * @return bool
     */
    public function wc_registration_allowed($allowed) {
        return (bool) $allowed;
    }

    /**
     * @param bool $show
     * @return bool
     */
    public function wc_show_password_field($show) {
        if (get_option('woocommerce_registration_generate_password') === 'yes') return false;
        return $show;
    }

    /** @return void */
    public function forward_wc_register_form() {
        do_action('woocommerce_register_form');
    }

    /**
     * @param bool $protected
     * @return bool
     */
    public function protect_account_page($protected) {
        if ($protected) return true;
        return function_exists('is_account_page') && is_account_page();
    }

    // -------------------------------------------------------------------------
    // Mail Mint
    // -------------------------------------------------------------------------

    /**
     * Suscribe al contacto en Mail Mint tras el registro si el usuario no hizo opt-out.
     * Respeta el double opt-in configurado en Mail Mint.
     *
     * @param int    $user_id
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param bool   $opt_in     true = usuario SÍ quiere recibir comunicaciones
     * @return void
     */
    public function maybe_subscribe_to_mailmint($_user_id, $email, $first_name, $last_name, $opt_in) {
        if (!$opt_in) return;

        if (
            !class_exists('Mint\MRM\DataStores\ContactData') ||
            !class_exists('Mint\MRM\DataBase\Models\ContactModel') ||
            !class_exists('Mint\MRM\DataBase\Models\ContactGroupModel')
        ) {
            return;
        }

        if (\Mint\MRM\DataBase\Models\ContactModel::is_contact_exist($email)) {
            return;
        }

        $double_optin_settings = get_option('_mrm_optin_settings', array());
        $double_optin          = isset($double_optin_settings['enable']) ? $double_optin_settings['enable'] : true;
        $status                = $double_optin ? 'pending' : 'subscribed';

        $list_id = (int) AuthGate_Settings::get('mailmint_list_id', 0);
        $lists   = $list_id ? array(array('id' => $list_id)) : array();

        $contact    = new \Mint\MRM\DataStores\ContactData($email, array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'status'     => $status,
            'source'     => __('Web registration', 'authgate'),
            'lists'      => $lists,
        ));
        $contact_id = \Mint\MRM\DataBase\Models\ContactModel::insert($contact);

        if (!$contact_id) return;

        if (!empty($lists)) {
            \Mint\MRM\DataBase\Models\ContactGroupModel::set_lists_to_contact($lists, $contact_id);
        }

        if ($double_optin && class_exists('Mint\MRM\Admin\API\Controllers\MessageController')) {
            \Mint\MRM\Admin\API\Controllers\MessageController::get_instance()->send_double_opt_in($contact_id);
        }
    }
}
