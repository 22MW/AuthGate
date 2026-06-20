<?php
defined('ABSPATH') || exit;

class AuthGate_Settings {

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
        $menu_hook = self::is_network() ? 'network_admin_menu' : 'admin_menu';
        $post_pfx  = self::is_network() ? 'network_admin_post_' : 'admin_post_';

        add_action($menu_hook, array($this, 'register_menu'));
        add_action('admin_enqueue_scripts',         array($this, 'enqueue_admin_assets'));
        add_action('network_admin_enqueue_scripts',  array($this, 'enqueue_admin_assets'));
        add_action($post_pfx . 'authgate_save',          array($this, 'save_settings'));
        add_action('wp_ajax_authgate_save',              array($this, 'save_settings'));
        add_action($post_pfx . 'authgate_save_strings',  array($this, 'save_strings'));
        add_action('wp_ajax_authgate_save_strings',      array($this, 'save_strings'));
        add_action($post_pfx . 'authgate_save_css',      array($this, 'save_css'));
        add_action('wp_ajax_authgate_save_css',          array($this, 'save_css'));
        add_action($post_pfx . 'authgate_unblock',       array($this, 'handle_unblock'));
        add_action($post_pfx . 'authgate_blacklist_add', array($this, 'handle_blacklist_add'));
        add_action($post_pfx . 'authgate_blacklist_del', array($this, 'handle_blacklist_del'));
        add_action($post_pfx . 'authgate_clean_log',     array($this, 'handle_clean_log'));

        if (self::is_network()) {
            add_action('admin_menu',                        array($this, 'register_site_menu'));
            add_action('admin_post_authgate_save_site',     array($this, 'save_site_settings'));
            add_action('wp_ajax_authgate_save_site',        array($this, 'save_site_settings'));
        }
    }

    /** @return bool */
    private static function is_network(): bool {
        return is_multisite();
    }

    /** @return string */
    private static function required_cap(): string {
        return self::is_network() ? 'manage_network' : 'manage_options';
    }

    /** URL base de la página de ajustes (varía en red). */
    private static function settings_base_url(): string {
        return self::is_network()
            ? network_admin_url('settings.php')
            : admin_url('options-general.php');
    }

    /** URL del endpoint admin-post (varía en red). */
    private static function admin_post_url(): string {
        return self::is_network()
            ? network_admin_url('admin-post.php')
            : admin_url('admin-post.php');
    }

    private const SCOPE_NETWORK = 'network';
    private const SCOPE_SITE = 'site';
    private const SCOPE_SITE_WITH_NETWORK_FALLBACK = 'site_with_network_fallback';

    /**
     * Mapa explícito de scopes multisite.
     *
     * MS0 mantiene el comportamiento actual para evitar migraciones implícitas:
     * - `network`: usa site options en multisite.
     * - `site`: usa options del sitio actual.
     * - `site_with_network_fallback`: reservado para MS2/MS3/MS4.
     *
     * @return array<string,string>
     */
    private static function multisite_scope_map(): array {
        return array(
            'blacklist'                                  => self::SCOPE_NETWORK,
            'block_wp_login'                            => self::SCOPE_NETWORK,
            'custom_css'                                => self::SCOPE_SITE_WITH_NETWORK_FALLBACK,
            'custom_css_enabled'                        => self::SCOPE_SITE_WITH_NETWORK_FALLBACK,
            'custom_css_mode'                           => self::SCOPE_SITE,
            'inline_intro_html'                         => self::SCOPE_NETWORK,
            'login_logo_url'                            => self::SCOPE_NETWORK,
            'login_slug'                                => self::SCOPE_NETWORK,
            'login_slug_redirect'                       => self::SCOPE_NETWORK,
            'max_attempts'                              => self::SCOPE_NETWORK,
            'reset_slug'                                => self::SCOPE_NETWORK,
            'excluded_pages'                            => self::SCOPE_SITE,
            'mailmint_list_id'                          => self::SCOPE_SITE,
            'users_can_register'                        => self::SCOPE_SITE,
            'woocommerce_registration_generate_password'=> self::SCOPE_SITE,
        );
    }

    /**
     * Claves que se guardan por subsite (get_option) aunque estemos en multisite.
     *
     * @return string[]
     */
    private static function subsite_keys(): array {
        return array_keys(
            array_filter(
                self::multisite_scope_map(),
                static function (string $scope): bool {
                    return self::SCOPE_SITE === $scope;
                }
            )
        );
    }

    /** @return string */
    private static function setting_scope(string $key): string {
        if (0 === strpos($key, 'str_')) {
            return self::SCOPE_SITE_WITH_NETWORK_FALLBACK;
        }

        $map = self::multisite_scope_map();
        return $map[$key] ?? self::SCOPE_NETWORK;
    }

    /** @return bool */
    private static function setting_uses_site_option(string $key): bool {
        return self::SCOPE_SITE === self::setting_scope($key);
    }

    /** @return string */
    private static function option_name(string $key): string {
        return 'authgate_' . $key;
    }

    /**
     * Guarda una opción de ajustes.
     * En multisite, el scope explícito decide si se guarda en red o sitio.
     *
     * @param string $key   Sin prefijo 'authgate_'
     * @param mixed  $value
     * @return void
     */
    private static function update_setting( string $key, $value ): void {
        if (self::is_network() && !self::setting_uses_site_option($key)) {
            update_site_option(self::option_name($key), $value);
        } else {
            update_option(self::option_name($key), $value);
        }
    }

    /**
     * @param string $hook
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'authgate') === false) return;
        wp_enqueue_media();
        wp_enqueue_code_editor(array('type' => 'text/css'));
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
        $asset_url = plugin_dir_url(dirname(__FILE__));
        $version   = '1.1.1';

        wp_enqueue_style(
            'mw22-back',
            $asset_url . 'assets/css/22mw-back.css',
            array(),
            $version
        );
        wp_enqueue_style(
            'authgate-back',
            $asset_url . 'assets/css/authgate-back.css',
            array('mw22-back'),
            $version
        );
        wp_enqueue_script(
            'mw22-back',
            $asset_url . 'assets/js/22mw-back.js',
            array(),
            $version,
            true
        );
        wp_enqueue_script(
            'authgate-back',
            $asset_url . 'assets/js/authgate-back.js',
            array('mw22-back'),
            $version,
            true
        );
    }

    /** @return void */
    public function register_menu() {
        if (self::is_network()) {
            add_submenu_page(
                'settings.php',
                __('AuthGate by 22MW', 'authgate'),
                __('AuthGate Config', 'authgate'),
                'manage_network',
                'authgate',
                array($this, 'render_page')
            );
        } else {
            add_options_page(
                __('AuthGate by 22MW', 'authgate'),
                __('AuthGate Config', 'authgate'),
                'manage_options',
                'authgate',
                array($this, 'render_page')
            );
        }
    }

    /**
     * Obtiene una opción de ajustes.
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public static function get($key, $default = '') {
        if (self::is_network()) {
            if (self::SCOPE_SITE_WITH_NETWORK_FALLBACK === self::setting_scope($key)) {
                $site_value = get_option(self::option_name($key), null);
                if (null !== $site_value && '' !== $site_value) {
                    return $site_value;
                }
                return get_site_option(self::option_name($key), $default);
            }

            if (!self::setting_uses_site_option($key)) {
                return get_site_option(self::option_name($key), $default);
            }
        }

        return get_option(self::option_name($key), $default);
    }

    /** @return bool */
    public static function network_allows_user_registration(): bool {
        if (!is_multisite()) {
            return true;
        }

        return in_array(get_site_option('registration', 'none'), array('user', 'all'), true);
    }

    /** @return string */
    private static function network_registration_label(): string {
        $labels = array(
            'none' => __('Registro desactivado en la red', 'authgate'),
            'user' => __('Solo cuentas de usuario', 'authgate'),
            'blog' => __('Solo nuevos sitios', 'authgate'),
            'all'  => __('Sitios y cuentas de usuario', 'authgate'),
        );

        $value = is_multisite() ? get_site_option('registration', 'none') : 'user';
        return $labels[$value] ?? sprintf(__('Valor desconocido: %s', 'authgate'), $value);
    }

    /** @return bool */
    public static function registration_enabled(): bool {
        if (self::is_network()) {
            return self::network_allows_user_registration();
        }

        return (bool) get_option('users_can_register');
    }

    /** @return string */
    private static function network_registration_mode(): string {
        $mode = (string) get_site_option('registration', 'none');
        return in_array($mode, array('none', 'user', 'blog', 'all'), true) ? $mode : 'none';
    }

    /** @return bool */
    private static function is_woocommerce_active(): bool {
        return class_exists('WooCommerce');
    }

    /**
     * Definición de todos los strings públicos editables.
     *
     * @return array<string,string> key => valor por defecto
     */
    public static function string_definitions() {
        return array(
            'login_title'       => __('Iniciar sesión', 'authgate'),
            'register_title'    => __('Crear cuenta', 'authgate'),
            'tab_login'         => __('Entrar', 'authgate'),
            'tab_register'      => __('Registrarse', 'authgate'),
            'field_user'        => __('Email o usuario', 'authgate'),
            'field_pass'        => __('Contraseña', 'authgate'),
            'field_remember'    => __('Recordarme', 'authgate'),
            'field_firstname'   => __('Nombre', 'authgate'),
            'field_lastname'    => __('Apellidos', 'authgate'),
            'field_email_reg'   => __('Email', 'authgate'),
            'field_pass_reg'        => __('Contraseña', 'authgate'),
            'field_pass_reg_confirm'=> __('Repite la contraseña', 'authgate'),
            'error_pass_mismatch'   => __('Las contraseñas no coinciden.', 'authgate'),
            'btn_login'         => __('Entrar', 'authgate'),
            'btn_register'      => __('Crear cuenta', 'authgate'),
            'btn_popup'         => __('Acceder', 'authgate'),
            'forgot_password'   => __('¿Olvidaste tu contraseña?', 'authgate'),
            'link_to_register'  => __('¿No tienes cuenta? Regístrate', 'authgate'),
            'link_to_login'     => __('¿Ya tienes cuenta? Entra aquí', 'authgate'),
            'link_to_home'      => __('Ir a la página de inicio', 'authgate'),
            'success_login'     => __('Sesión iniciada. Redirigiendo…', 'authgate'),
            'success_register'  => __('Cuenta creada. Redirigiendo…', 'authgate'),
            'error_invalid'     => __('Usuario o contraseña incorrectos.', 'authgate'),
            'error_blocked'     => __('Demasiados intentos. Inténtalo en una hora.', 'authgate'),
            'error_spam'        => __('Formulario no válido. Inténtalo de nuevo.', 'authgate'),
            'error_generic'     => __('Se ha producido un error. Inténtalo de nuevo.', 'authgate'),
            'protected_title'   => __('Acceso restringido', 'authgate'),
            'protected_desc'    => __('Debes iniciar sesión para ver esta página.', 'authgate'),
            'field_gdpr'        => __('He leído y acepto la <a href="{privacy_url}" target="_blank">política de privacidad</a>', 'authgate'),
            'field_newsletter'  => __('NO quiero recibir comunicaciones comerciales', 'authgate'),
            'legal_text'        => __('En cumplimiento del RGPD, le informamos que sus datos serán tratados para gestionar su cuenta. Puede ejercer sus derechos de acceso, rectificación y supresión contactando con nosotros.', 'authgate'),
            'error_gdpr'        => __('Debes aceptar la política de privacidad para continuar.', 'authgate'),
            'field_lost_email'      => __('Tu email de cuenta', 'authgate'),
            'btn_lost_password'     => __('Enviar instrucciones', 'authgate'),
            'success_lost_password' => __('Si existe una cuenta con ese email, recibirás instrucciones para recuperar tu contraseña.', 'authgate'),
            'back_to_login'         => __('← Volver al inicio de sesión', 'authgate'),
            'reset_title'           => __('Restablecer contraseña', 'authgate'),
            'field_new_pass'        => __('Nueva contraseña', 'authgate'),
            'field_new_pass_confirm'=> __('Repite la nueva contraseña', 'authgate'),
            'btn_reset_password'    => __('Guardar contraseña', 'authgate'),
            'success_reset_password'=> __('Contraseña actualizada. Redirigiendo…', 'authgate'),
            'error_reset_key'       => __('El enlace no es válido o ha expirado. Solicita uno nuevo.', 'authgate'),
            'error_weak_password'   => __('La contraseña es demasiado débil. Usa mayúsculas, minúsculas, números y símbolos.', 'authgate'),
            'btn_generate_pass'     => __('Generar contraseña segura', 'authgate'),
        );
    }

    /**
     * Claves que usan editor WYSIWYG (TinyMCE) — admiten HTML completo vía wp_kses_post.
     *
     * @return string[]
     */
    public static function wysiwyg_string_keys() {
        return array('legal_text');
    }

    /** @return string */
    public static function get_inline_intro_html(): string {
        return (string) self::get('inline_intro_html', '');
    }

    /** @return string */
    public static function get_custom_css(): string {
        $presets = self::css_presets();
        return (string) self::get('custom_css', $presets['white']);
    }

    /** @return bool */
    public static function custom_css_enabled(): bool {
        if (self::is_network() && 'disabled' === get_option(self::option_name('custom_css_mode'), 'inherit')) {
            return false;
        }

        return (bool) self::get('custom_css_enabled', true);
    }

    /** @return string */
    private static function custom_css_mode(): string {
        $mode = (string) get_option(self::option_name('custom_css_mode'), 'inherit');
        return in_array($mode, array('inherit', 'override', 'disabled'), true) ? $mode : 'inherit';
    }

    /** @return array<string,string> */
    private static function css_presets(): array {
        $common = <<<'CSS'
.authgate,
.authgate-protected-page__inner.card {
    padding: clamp(24px, 4vw, 36px);
    border-radius: 24px;
}

.authgate-protected-page__inner .authgate {
    padding: 0;
    border: 0;
    border-radius: 0;
    box-shadow: none;
}

.authgate-protected-page__logo {
    text-align: center;
    margin-bottom: 18px;
}

.authgate__message {
    font-size: var(--fs-small, 1.575rem);
}

.authgate-protected-page__logo img,
.authgate-protected-page__logo .custom-logo {
    display: inline-block;
    width: auto;
    height: auto;
    max-width: min(220px, 70%);
    max-height: 110px;
    object-fit: contain;
}

.authgate-protected-page__logo .custom-logo-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.authgate-protected-page__site-name {
    font: inherit;
    font-weight: 700;
}

.authgate-protected-page__title,
.authgate-protected-page__desc,
.authgate__title,
.authgate__intro {
    text-align: center;
}

.authgate-protected-page__title,
.authgate__title {
    margin: 0 0 10px;
    font-size: clamp(1.35rem, 2vw, 1.75rem) !important;
    line-height: 1.2;
}

.authgate-protected-page__desc,
.authgate__intro {
    margin-bottom: 24px;
    font-size: 1.5rem;
    line-height: 1.55;
}

.authgate__form {
    display: grid;
    gap: 12px;
}

.authgate__field,
.authgate__row {
    margin-bottom: 0;
}

.authgate__input {
    min-height: 46px;
    padding: 11px 16px;
    border-radius: 999px;
    font: inherit;
    font-size: 1.5rem !important;
    box-shadow: none;
}

.authgate__input:focus {
    outline: none;
}

.authgate__btn {
    min-height: 48px;
    margin-top: 6px;
    border-radius: 999px;
    font: inherit;
    font-weight: 700;
    font-size: var(--fs-body, 1.5rem) !important;
}

.authgate__tabs {
    margin-bottom: 22px;
}

.authgate__tab {
    padding: 10px 14px;
    border-radius: 999px 999px 0 0 !important;
    font-size: var(--fs-body, 1.5rem) !important;
}

.authgate__switch {
    margin-top: 14px;
}

.authgate__legal {
    margin-top: 14px;
    text-align: center;
    font-size: 0.9rem;
    line-height: 1.5;
}

.authgate__label {
    font-size: var(--fs-small, 1.2rem);
}

.authgate__link,
.authgate__switch-link,
.authgate__back-login,
.authgate__check-label {
    font-size: var(--fs-small, 1.375rem);
}

.authgate__btn {
    border-radius: 999px !important;
    font-size: var(--fs-body, 1.5rem) !important;
}

.authgate__btn:hover {
    opacity: .6;
}

.authgate__lost-trigger {
    display: none !important;
}
CSS;

        $white = <<<'CSS'
.authgate,
.authgate-protected-page__inner.card {
    background: #ffffff;
    color: inherit;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 18px 48px rgba(15, 23, 42, 0.10);
}

.authgate__input {
    border: 1px solid rgba(15, 23, 42, 0.16);
    background: #ffffff;
    color: inherit;
}

.authgate__input:focus {
    border-color: currentColor;
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
}

.authgate__btn {
    background-color: #000000 !important;
    border: none !important;
    color: #ffffff !important;
}
CSS;

        $dark = <<<'CSS'
.authgate,
.authgate-protected-page__inner.card {
    background: #111827;
    color: #f9fafb;
    border: 1px solid rgba(249, 250, 251, 0.12);
    box-shadow: 0 18px 48px rgba(0, 0, 0, 0.28);
}

.authgate-protected-page__title,
.authgate-protected-page__desc,
.authgate__title,
.authgate__intro,
.authgate__label,
.authgate__check-label,
.authgate__legal {
    color: #f9fafb;
}

.authgate__input {
    border: 1px solid rgba(249, 250, 251, 0.24);
    background: #ffffff;
    color: #111827;
}

.authgate__input:focus {
    border-color: #f9fafb;
    box-shadow: 0 0 0 3px rgba(249, 250, 251, 0.16);
}

.authgate__btn {
    background-color: #ffffff !important;
    border: none !important;
    color: #111827 !important;
}
CSS;

        return array(
            'white' => $common . "\n" . $white,
            'dark'  => $common . "\n" . $dark,
        );
    }

    /**
     * Claves de string que se renderizan como textarea (admiten HTML básico).
     *
     * @return string[]
     */
    public static function textarea_string_keys() {
        return array('field_gdpr');
    }

    /**
     * Allowed HTML tags for textarea strings.
     *
     * @return array<string,array>
     */
    private static function html_string_allowed_tags() {
        return array(
            'a'      => array('href' => array(), 'target' => array(), 'rel' => array()),
            'strong' => array(),
            'em'     => array(),
            'br'     => array(),
            'p'      => array(),
        );
    }

    /**
     * Devuelve un string por su clave, con fallback al valor por defecto.
     *
     * @param string $key
     * @return string
     */
    public static function get_string($key) {
        $defs = self::string_definitions();
        return (string) self::get('str_' . $key, $defs[$key] ?? '');
    }

    /** @return void */
    public function render_page() {
        if (!current_user_can(self::required_cap())) {
            wp_die(esc_html__('Sin permiso.', 'authgate'));
        }

        $tab = sanitize_key($_GET['tab'] ?? 'general');
        if (self::is_network() && 'strings' === $tab) {
            $tab = 'general';
        }
        ?>
        <div class="mw22-back authgate-back" data-mw22-back data-mw22-theme-key="authgateBackTheme" data-mw22-updated-message="Ajustes guardados.">
            <header class="mw22-back__header">
                <a class="mw22-back__brand" href="https://22mw.online/" target="_blank" rel="noopener noreferrer">
                    <span class="mw22-back__mark" aria-hidden="true">
                        <svg viewBox="0 0 45 56" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="0 20 44.9189011 20 44.9189011 15 4.48417582 15 4.48417582 12.5 44.9189011 12.5 45 12.5 45 0 0.0810989011 0 0.0810989011 5 40.5168132 5 40.5168132 7.5 0 7.5 0 20"/>
                            <polygon points="0.0810989011 42 44.9189011 42 44.9189011 37 4.48417582 37 4.48417582 34.5 45 34.5 45 22 0.0810989011 22 0.0810989011 27 40.5168132 27 40.5168132 29.5 0 29.5 0 42"/>
                            <polygon points="21 44 0 44 0 56 5.37209302 56 5.37209302 48.8 7.81395349 48.8 7.81395349 56 13.1860465 56 13.1860465 48.8 15.627907 48.8 15.627907 56 21 56"/>
                            <polygon transform="translate(34.5,50) rotate(-180) translate(-34.5,-50)" points="24 44 24 56 29.372093 56 29.372093 48.8 31.8139535 48.8 31.8139535 56 37.1860465 56 37.1860465 48.8 39.627907 48.8 39.627907 56 45 56 45 44"/>
                        </svg>
                    </span>
                    <div class="mw22-back__title-row">
                        <h1><?php esc_html_e('AuthGate', 'authgate'); ?></h1>
                        <span class="mw22-back__version"><?php echo esc_html__('by 22mw.online', 'authgate') . ' · v' . esc_html(get_file_data(dirname(__DIR__) . '/authgate.php', array('Version' => 'Version'))['Version']); ?></span>
                    </div>
                </a>
                <div class="mw22-back__actions">
                    <button type="button" class="mw22-back__theme" data-mw22-back-theme="dark"><?php esc_html_e('Oscuro', 'authgate'); ?></button>
                    <button type="button" class="mw22-back__theme" data-mw22-back-theme="light"><?php esc_html_e('Claro', 'authgate'); ?></button>
                </div>
            </header>

            <nav class="mw22-back__menu" aria-label="<?php echo esc_attr__('Secciones de AuthGate', 'authgate'); ?>">
                <?php
                $tabs = array(
                    'general'    => __('General', 'authgate'),
                    'shortcodes' => __('Shortcodes', 'authgate'),
                    'blocked'    => __('Seguridad', 'authgate'),
                    'log'        => __('Accesos', 'authgate'),
                );

                if (!self::is_network()) {
                    $tabs = array_merge(
                        array_slice($tabs, 0, 1, true),
                        array(
                            'strings' => __('Textos', 'authgate'),
                        ),
                        array_slice($tabs, 1, null, true)
                    );
                }

                $tabs = array_merge(
                    array_slice($tabs, 0, 1, true),
                    array('css' => __('CSS', 'authgate')),
                    array_slice($tabs, 1, null, true)
                );
                foreach ($tabs as $slug => $label) :
                    $active = $tab === $slug ? 'is-active' : '';
                    $url    = add_query_arg(array('page' => 'authgate', 'tab' => $slug), self::settings_base_url());
                    ?>
                    <a href="<?php echo esc_url($url); ?>" class="mw22-back__menu-item <?php echo esc_attr($active); ?>">
                        <span><?php echo esc_html($label); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <main class="mw22-back__content">
            <?php
            if ($tab === 'general') {
                $this->render_tab_general();
            } elseif ($tab === 'strings') {
                $this->render_tab_strings();
            } elseif ($tab === 'css') {
                $this->render_tab_css();
            } elseif ($tab === 'shortcodes') {
                $this->render_tab_shortcodes();
            } elseif ($tab === 'blocked') {
                $this->render_tab_blocked();
            } elseif ($tab === 'log') {
                $this->render_tab_log();
            }
            ?>
            </main>
        </div>
        <?php
    }

    /** @return void */
    private function render_tab_general() {
        $max_attempts   = (int) self::get('max_attempts', 10);
        $excluded_pages = (array) self::get('excluded_pages', array());
        $all_pages      = get_pages(array('sort_column' => 'post_title'));
        ?>
        <form id="authgate-settings-form" method="post" action="<?php echo esc_url(self::admin_post_url()); ?>" data-authgate-ajax-form>
            <input type="hidden" name="action" value="authgate_save">
            <?php wp_nonce_field('authgate_save', '_authgate_nonce'); ?>

            <div class="mw22-back-section-layout" data-mw22-back-subnav>
                <aside class="mw22-back-subnav" aria-label="<?php echo esc_attr__('Opciones generales', 'authgate'); ?>">
                    <a href="#authgate-section-rate" class="is-active"><?php esc_html_e('Rate limiting', 'authgate'); ?></a>
                    <a href="#authgate-section-registration"><?php esc_html_e('Registro', 'authgate'); ?></a>
                    <?php if (!self::is_network()) : ?>
                        <a href="#authgate-section-exclusions"><?php esc_html_e('Exclusiones', 'authgate'); ?></a>
                    <?php endif; ?>
                    <a href="#authgate-section-urls"><?php esc_html_e('URLs y login', 'authgate'); ?></a>
                    <?php if (!self::is_network()) : ?>
                        <a href="#authgate-section-mailmint"><?php esc_html_e('Mail Mint', 'authgate'); ?></a>
                    <?php endif; ?>
                </aside>
                <div class="mw22-back-section-content">
            <!-- Rate limiting -->
            <div id="authgate-section-rate" class="mw22-back-section" style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Rate limiting', 'authgate'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Máx. intentos antes de bloquear', 'authgate'); ?></th>
                        <td>
                            <input type="number" name="max_attempts" value="<?php echo esc_attr($max_attempts); ?>" min="1" max="200" style="width:80px;">
                            <p class="description"><?php esc_html_e('Bloqueo de 1 hora al superar este límite. Por defecto: 10.', 'authgate'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Registro -->
            <div id="authgate-section-registration" class="mw22-back-section" style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Registro de usuarios', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;">
                    <?php esc_html_e('Estos controles usan las opciones nativas de WordPress y WooCommerce. Si el registro está desactivado, AuthGate ocultará la parte de registro en frontend.', 'authgate'); ?>
                </p>
                <table class="form-table">
                    <?php if (self::is_network()) : ?>
                    <tr>
                        <th scope="row"><?php esc_html_e('Política de red', 'authgate'); ?></th>
                        <td>
                            <fieldset>
                                <label><input type="radio" name="network_registration" value="none" <?php checked(self::network_registration_mode(), 'none'); ?>> <?php esc_html_e('Registro desactivado', 'authgate'); ?></label><br>
                                <label><input type="radio" name="network_registration" value="user" <?php checked(self::network_registration_mode(), 'user'); ?>> <?php esc_html_e('Permitir cuentas de usuario', 'authgate'); ?></label><br>
                                <label><input type="radio" name="network_registration" value="blog" <?php checked(self::network_registration_mode(), 'blog'); ?>> <?php esc_html_e('Permitir nuevos sitios', 'authgate'); ?></label><br>
                                <label><input type="radio" name="network_registration" value="all" <?php checked(self::network_registration_mode(), 'all'); ?>> <?php esc_html_e('Permitir sitios y cuentas de usuario', 'authgate'); ?></label>
                            </fieldset>
                            <p class="description">
                                <?php esc_html_e('Equivale a la opción nativa de multisite “Permitir nuevos registros”. AuthGate usará esta política global para mostrar u ocultar el registro.', 'authgate'); ?>
                            </p>
                        </td>
                    </tr>
                    <?php else : ?>
                    <?php $network_allows_registration = self::network_allows_user_registration(); ?>
                    <tr>
                        <th scope="row"><?php esc_html_e('Permitir registro', 'authgate'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="users_can_register" value="1" <?php checked((bool) get_option('users_can_register')); ?> <?php disabled(!$network_allows_registration); ?>>
                                <?php esc_html_e('Permitir que los visitantes creen una cuenta', 'authgate'); ?>
                            </label>
                            <?php if (!$network_allows_registration) : ?>
                                <p class="description"><?php esc_html_e('La red multisite tiene bloqueado el registro de usuarios. Este sitio no puede activarlo.', 'authgate'); ?></p>
                            <?php else : ?>
                                <p class="description"><?php esc_html_e('Equivale a la opción nativa de WordPress “Cualquiera puede registrarse”.', 'authgate'); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (self::is_woocommerce_active()) : ?>
                    <tr>
                        <th scope="row"><?php esc_html_e('Contraseña WooCommerce', 'authgate'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="woocommerce_registration_generate_password" value="1" <?php checked(get_option('woocommerce_registration_generate_password'), 'yes'); ?>>
                                <?php esc_html_e('Enviar enlace de configuración de contraseña', 'authgate'); ?>
                            </label>
                            <p class="description"><?php esc_html_e('Equivale a la opción nativa de WooCommerce. Si está activa, AuthGate no mostrará campos de contraseña en el registro.', 'authgate'); ?></p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endif; ?>
                </table>
            </div>

            <?php if (!self::is_network()) : ?>
            <!-- Exclusiones -->
            <div id="authgate-section-exclusions" class="mw22-back-section" style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Exclusiones', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;">
                    <?php esc_html_e('El plugin intercepta automáticamente cualquier página que requiera login y muestra su formulario. Marca aquí las páginas donde NO debe actuar (quedará el comportamiento nativo de WP/WooCommerce).', 'authgate'); ?>
                </p>
                <div class="authgate-page-picker" data-authgate-page-picker>
                    <label class="screen-reader-text" for="authgate_page_picker_search"><?php esc_html_e('Buscar página para excluir', 'authgate'); ?></label>
                    <input type="search"
                           id="authgate_page_picker_search"
                           class="authgate-page-picker__search"
                           placeholder="<?php echo esc_attr__('Empieza a escribir para buscar páginas…', 'authgate'); ?>"
                           autocomplete="off"
                           data-authgate-page-search>
                    <div class="authgate-page-picker__results" data-authgate-page-results hidden>
                        <?php foreach ($all_pages as $page) : ?>
                            <button type="button"
                                    class="authgate-page-picker__result"
                                    data-page-id="<?php echo esc_attr($page->ID); ?>"
                                    data-page-title="<?php echo esc_attr($page->post_title); ?>">
                                <?php echo esc_html($page->post_title); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="authgate-page-picker__selected" data-authgate-page-selected>
                        <?php foreach ($all_pages as $page) : ?>
                            <?php if (in_array($page->ID, $excluded_pages, true)) : ?>
                                <span class="authgate-page-picker__chip" data-page-id="<?php echo esc_attr($page->ID); ?>">
                                    <input type="hidden" name="excluded_pages[]" value="<?php echo esc_attr($page->ID); ?>">
                                    <?php echo esc_html($page->post_title); ?>
                                    <button type="button" aria-label="<?php echo esc_attr(sprintf(__('Quitar %s', 'authgate'), $page->post_title)); ?>" data-authgate-remove-page>×</button>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- URLs personalizadas -->
            <div id="authgate-section-urls" class="mw22-back-section" style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('URLs personalizadas', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;">
                    <?php esc_html_e('Sustituye wp-login.php por tus propias URLs. Guarda y luego visita Ajustes › Permalinks para activar los cambios.', 'authgate'); ?>
                </p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="authgate_login_slug"><?php esc_html_e('Slug de login', 'authgate'); ?></label></th>
                        <td>
                            <span style="color:#666;"><?php echo esc_html(trailingslashit(home_url())); ?></span><input type="text"
                                   id="authgate_login_slug"
                                   name="login_slug"
                                   value="<?php echo esc_attr(self::get('login_slug', 'acceder')); ?>"
                                   style="width:200px;">
                            <p class="description"><?php esc_html_e('Deja vacío para no usar URL personalizada.', 'authgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="authgate_login_slug_redirect"><?php esc_html_e('Redirección tras login (página /slug/)', 'authgate'); ?></label></th>
                        <td>
                            <input type="url"
                                   id="authgate_login_slug_redirect"
                                   name="login_slug_redirect"
                                   value="<?php echo esc_attr(self::get('login_slug_redirect', '')); ?>"
                                   style="width:360px;"
                                   placeholder="<?php echo esc_attr(home_url('/')); ?>">
                            <p class="description"><?php esc_html_e('URL a la que redirigir tras login desde la página /slug/. Deja vacío para ir al inicio. Si el usuario venía de una página protegida se usa esa página en su lugar.', 'authgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="authgate_reset_slug"><?php esc_html_e('Slug de reset contraseña', 'authgate'); ?></label></th>
                        <td>
                            <span style="color:#666;"><?php echo esc_html(trailingslashit(home_url())); ?></span><input type="text"
                                   id="authgate_reset_slug"
                                   name="reset_slug"
                                   value="<?php echo esc_attr(self::get('reset_slug', 'restablecer-contrasena')); ?>"
                                   style="width:200px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="authgate_login_logo_url"><?php esc_html_e('Logo página de login', 'authgate'); ?></label></th>
                        <td>
                            <?php $logo_url = self::get('login_logo_url', ''); ?>
                            <input type="url"
                                   id="authgate_login_logo_url"
                                   name="login_logo_url"
                                   value="<?php echo esc_attr($logo_url); ?>"
                                   style="width:360px;"
                                   placeholder="https://...">
                            <button type="button" class="button" id="authgate_select_logo">
                                <?php esc_html_e('Seleccionar imagen', 'authgate'); ?>
                            </button>
                            <?php if ($logo_url) : ?>
                            <div style="margin-top:8px;">
                                <img src="<?php echo esc_url($logo_url); ?>" style="max-height:60px;display:block;">
                            </div>
                            <?php endif; ?>
                            <p class="description"><?php esc_html_e('Logo que aparece en /acceder/ y /restablecer-contrasena/. Deja vacío para mostrar el nombre del sitio.', 'authgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="authgate_inline_intro_html"><?php esc_html_e('Texto bajo el logo', 'authgate'); ?></label></th>
                        <td>
                            <?php
                            wp_editor(self::get_inline_intro_html(), 'authgate_inline_intro_html', array(
                                'textarea_name' => 'inline_intro_html',
                                'media_buttons' => false,
                                'teeny'         => false,
                                'editor_height' => 140,
                                'quicktags'     => true,
                                'tinymce'       => array(
                                    'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,alignleft,aligncenter,alignright,bullist,numlist,blockquote,link,unlink,undo,redo,removeformat',
                                ),
                            ));
                            ?>
                            <p class="description"><?php esc_html_e('Se muestra bajo el logo en formularios inline de login, registro y combinado. Déjalo vacío para no mostrar nada.', 'authgate'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Ocultar wp-login.php', 'authgate'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="block_wp_login" value="1" <?php checked((bool) self::get('block_wp_login', false)); ?>>
                                <?php esc_html_e('Redirigir visitas a wp-login.php a la URL personalizada', 'authgate'); ?>
                            </label>
                            <?php if (is_multisite()) : ?>
                            <p class="description"><?php esc_html_e('En multisite, solo se redirige la página de reset; wp-login.php permanece accesible para el resto de acciones.', 'authgate'); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <?php if (!self::is_network()) : ?>
            <!-- Mail Mint -->
            <div id="authgate-section-mailmint" class="mw22-back-section" style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Mail Mint — Suscripción al registro', 'authgate'); ?></h2>
                <?php if (!class_exists('Mint\MRM\DataBase\Models\ContactGroupModel')) : ?>
                    <p><?php esc_html_e('Mail Mint no está activo.', 'authgate'); ?></p>
                <?php else :
                    $model    = \Mint\MRM\DataBase\Models\ContactGroupModel::class;
                    $raw      = method_exists($model, 'get_all')
                        ? $model::get_all('lists')
                        : $model::get_all_to_custom_select('lists'); // phpcs:ignore -- fallback para versiones antiguas
                    $mm_lists = isset($raw['data']) ? $raw['data'] : array();
                    $saved_list = (int) self::get('mailmint_list_id', 0);
                ?>
                <p class="description" style="margin-bottom:12px;">
                    <?php esc_html_e('Los usuarios que no marquen "NO quiero recibir comunicaciones" se añadirán automáticamente a la lista seleccionada.', 'authgate'); ?>
                </p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="authgate_mailmint_list"><?php esc_html_e('Lista de suscripción', 'authgate'); ?></label></th>
                        <td>
                            <select id="authgate_mailmint_list" name="mailmint_list_id">
                                <option value="0"><?php esc_html_e('— Sin lista —', 'authgate'); ?></option>
                                <?php foreach ($mm_lists as $list) : ?>
                                    <option value="<?php echo esc_attr($list['id']); ?>" <?php selected($saved_list, (int) $list['id']); ?>>
                                        <?php echo esc_html($list['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php endif; ?>
            </div>
            <?php endif; // !is_network ?>

                </div>
            </div>
            <?php submit_button(__('Guardar ajustes', 'authgate')); ?>
        </form>
        <script>
        (function($){
            // Media uploader
            $('#authgate_select_logo').on('click', function(e){
                e.preventDefault();
                var frame = wp.media({ title: '<?php echo esc_js(__('Seleccionar logo', 'authgate')); ?>', multiple: false, library: { type: 'image' } });
                frame.on('select', function(){
                    var url = frame.state().get('selection').first().toJSON().url;
                    $('#authgate_login_logo_url').val(url);
                });
                frame.open();
            });

        })(jQuery);
        </script>
        <?php
    }

    /** @return void */
    private function render_tab_strings() {
        $string_defs   = self::string_definitions();
        $textarea_keys = self::textarea_string_keys();
        $wysiwyg_keys  = self::wysiwyg_string_keys();
        ?>
        <form id="authgate-strings-form" method="post" action="<?php echo esc_url(self::admin_post_url()); ?>" data-authgate-ajax-form>
            <input type="hidden" name="action" value="authgate_save_strings">
            <?php wp_nonce_field('authgate_save_strings', '_authgate_nonce'); ?>

            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Textos del formulario', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;"><?php esc_html_e('Estos textos se guardan desde esta pestaña de forma independiente al resto de ajustes.', 'authgate'); ?></p>
                <div class="authgate-strings-grid">
                    <?php foreach ($string_defs as $key => $default) :
                        $is_wysiwyg  = in_array($key, $wysiwyg_keys, true);
                        $is_textarea = !$is_wysiwyg && in_array($key, $textarea_keys, true);
                        $field_class = ($is_wysiwyg || $is_textarea) ? ' authgate-string-field--wide' : '';
                        ?>
                        <div class="authgate-string-field<?php echo esc_attr($field_class); ?>">
                            <label for="authgate_str_<?php echo esc_attr($key); ?>">
                                <code><?php echo esc_html($key); ?></code>
                            </label>
                            <?php if ($is_wysiwyg) :
                                wp_editor(self::get_string($key), 'authgate_str_' . $key, array(
                                    'textarea_name' => 'strings[' . $key . ']',
                                    'media_buttons' => false,
                                    'teeny'         => true,
                                    'editor_height' => 150,
                                    'tinymce'       => array(
                                        'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,removeformat',
                                    ),
                                ));
                            elseif ($is_textarea) : ?>
                                <textarea id="authgate_str_<?php echo esc_attr($key); ?>" name="strings[<?php echo esc_attr($key); ?>]" rows="4"><?php echo esc_textarea(self::get_string($key)); ?></textarea>
                            <?php else : ?>
                                <input type="text" id="authgate_str_<?php echo esc_attr($key); ?>" name="strings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr(self::get_string($key)); ?>">
                            <?php endif; ?>
                            <?php if ($default !== '') : ?>
                                <p class="description"><?php esc_html_e('Por defecto:', 'authgate'); ?> <em><?php echo esc_html($default); ?></em></p>
                            <?php endif; ?>
                            <?php if ($key === 'field_gdpr') : ?>
                                <p class="description"><?php esc_html_e('Usa {privacy_url} para insertar el enlace a la política de privacidad.', 'authgate'); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php submit_button(__('Guardar textos', 'authgate')); ?>
        </form>
        <?php
    }

    /** @return void */
    private function render_tab_css() {
        $presets = self::css_presets();
        ?>
        <form id="authgate-css-form" method="post" action="<?php echo esc_url(self::admin_post_url()); ?>" data-authgate-ajax-form>
            <input type="hidden" name="action" value="authgate_save_css">
            <?php wp_nonce_field('authgate_save_css', '_authgate_nonce'); ?>

            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('CSS propio', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;"><?php esc_html_e('CSS opcional para ajustar el aspecto de los formularios AuthGate. Se renderiza solo si está activado.', 'authgate'); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Activar CSS propio', 'authgate'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="custom_css_enabled" value="1" <?php checked(self::custom_css_enabled()); ?>>
                                <?php esc_html_e('Cargar este CSS en frontend', 'authgate'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="authgate_custom_css"><?php esc_html_e('Editor CSS', 'authgate'); ?></label></th>
                        <td>
                            <textarea id="authgate_custom_css" name="custom_css" rows="16" style="width:100%;font-family:monospace;"><?php echo esc_textarea(self::get_custom_css()); ?></textarea>
                            <p class="description"><?php esc_html_e('No se permiten @import, javascript:, expression(), behavior ni -moz-binding.', 'authgate'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Guardar CSS', 'authgate')); ?>
            </div>

            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Presets copiables', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;"><?php esc_html_e('Copia un preset y pégalo en el editor CSS si quieres usarlo como punto de partida.', 'authgate'); ?></p>
                <h3><?php esc_html_e('Preset blanco', 'authgate'); ?></h3>
                <textarea readonly rows="10" style="width:100%;font-family:monospace;margin-bottom:16px;"><?php echo esc_textarea($presets['white']); ?></textarea>
                <h3><?php esc_html_e('Preset oscuro', 'authgate'); ?></h3>
                <textarea readonly rows="10" style="width:100%;font-family:monospace;"><?php echo esc_textarea($presets['dark']); ?></textarea>
            </div>

        </form>
        <script>
        (function(){
            if (window.wp && wp.codeEditor) {
                wp.codeEditor.initialize('authgate_custom_css', { codemirror: { mode: 'css', lineNumbers: true, lineWrapping: true } });
            }
        })();
        </script>
        <?php
    }

    /** @return void */
    private function render_tab_shortcodes() {
        $login_slug = esc_html(trailingslashit(home_url()) . self::get('login_slug', 'acceder'));
        $reset_slug = esc_html(trailingslashit(home_url()) . self::get('reset_slug', 'restablecer-contrasena'));
        ?>
        <style>
            .authgate-sc-table { width:100%; border-collapse:collapse; margin-bottom:8px; }
            .authgate-sc-table th { background:#f6f7f7; padding:8px 12px; text-align:left; font-weight:600; border:1px solid #e2e4e7; }
            .authgate-sc-table td { padding:8px 12px; border:1px solid #e2e4e7; vertical-align:top; font-size:13px; }
            .authgate-sc-table td code { background:#f0f0f1; padding:2px 5px; border-radius:3px; font-size:12px; }
            .authgate-sc-block { background:#fff; padding:24px; margin-bottom:20px; border:1px solid #ccd0d4; border-radius:4px; }
            .authgate-sc-block h3 { margin-top:0; font-family:monospace; font-size:15px; background:#f0f0f1; display:inline-block; padding:4px 10px; border-radius:3px; }
            .authgate-sc-example { background:#f6f7f7; border:1px solid #e2e4e7; border-radius:3px; padding:10px 14px; font-family:monospace; font-size:12px; margin-top:12px; white-space:pre-wrap; }
        </style>

        <div class="authgate-sc-block">
            <h3>[authgate_auth]</h3>
            <p><?php esc_html_e('Formulario combinado login + registro. El más completo — recomendado.', 'authgate'); ?></p>
            <table class="authgate-sc-table">
                <tr><th><?php esc_html_e('Parámetro', 'authgate'); ?></th><th><?php esc_html_e('Valores', 'authgate'); ?></th><th><?php esc_html_e('Descripción', 'authgate'); ?></th></tr>
                <tr><td><code>mode</code></td><td><code>inline</code> · <code>popup</code></td><td><?php esc_html_e('inline: incrustado en la página. popup: muestra un botón que abre un modal.', 'authgate'); ?></td></tr>
                <tr><td><code>default_tab</code></td><td><code>login</code> · <code>register</code></td><td><?php esc_html_e('Pestaña activa al cargar (solo mode=inline).', 'authgate'); ?></td></tr>
                <tr><td><code>redirect</code></td><td><?php esc_html_e('URL', 'authgate'); ?></td><td><?php esc_html_e('URL a la que redirigir tras login o registro. Por defecto: Mi Cuenta.', 'authgate'); ?></td></tr>
                <tr><td><code>label</code></td><td><?php esc_html_e('texto', 'authgate'); ?></td><td><?php esc_html_e('Texto del botón del popup (solo mode=popup).', 'authgate'); ?></td></tr>
                <tr><td><code>button_class</code></td><td><?php esc_html_e('clases CSS', 'authgate'); ?></td><td><?php esc_html_e('Clases adicionales para el botón del popup (solo mode=popup).', 'authgate'); ?></td></tr>
            </table>
            <div class="authgate-sc-example">[authgate_auth]
[authgate_auth mode="popup" label="Acceder ahora" button_class="btn btn-primary"]
[authgate_auth mode="inline" default_tab="register" redirect="<?php echo esc_html(home_url('/gracias/')); ?>"]</div>
        </div>

        <div class="authgate-sc-block">
            <h3>[authgate_login]</h3>
            <p><?php esc_html_e('Solo formulario de login.', 'authgate'); ?></p>
            <table class="authgate-sc-table">
                <tr><th><?php esc_html_e('Parámetro', 'authgate'); ?></th><th><?php esc_html_e('Valores', 'authgate'); ?></th><th><?php esc_html_e('Descripción', 'authgate'); ?></th></tr>
                <tr><td><code>mode</code></td><td><code>inline</code> · <code>popup</code></td><td><?php esc_html_e('inline: incrustado. popup: modal.', 'authgate'); ?></td></tr>
                <tr><td><code>redirect</code></td><td><?php esc_html_e('URL', 'authgate'); ?></td><td><?php esc_html_e('URL tras login exitoso.', 'authgate'); ?></td></tr>
                <tr><td><code>label</code></td><td><?php esc_html_e('texto', 'authgate'); ?></td><td><?php esc_html_e('Texto del botón del popup.', 'authgate'); ?></td></tr>
                <tr><td><code>button_class</code></td><td><?php esc_html_e('clases CSS', 'authgate'); ?></td><td><?php esc_html_e('Clases adicionales para el botón del popup.', 'authgate'); ?></td></tr>
            </table>
            <div class="authgate-sc-example">[authgate_login]
[authgate_login mode="popup" label="Entrar" button_class="btn btn-secondary"]</div>
        </div>

        <div class="authgate-sc-block">
            <h3>[authgate_register]</h3>
            <p><?php esc_html_e('Solo formulario de registro.', 'authgate'); ?></p>
            <table class="authgate-sc-table">
                <tr><th><?php esc_html_e('Parámetro', 'authgate'); ?></th><th><?php esc_html_e('Valores', 'authgate'); ?></th><th><?php esc_html_e('Descripción', 'authgate'); ?></th></tr>
                <tr><td><code>mode</code></td><td><code>inline</code> · <code>popup</code></td><td><?php esc_html_e('inline: incrustado. popup: modal.', 'authgate'); ?></td></tr>
                <tr><td><code>redirect</code></td><td><?php esc_html_e('URL', 'authgate'); ?></td><td><?php esc_html_e('URL tras registro exitoso.', 'authgate'); ?></td></tr>
                <tr><td><code>label</code></td><td><?php esc_html_e('texto', 'authgate'); ?></td><td><?php esc_html_e('Texto del botón del popup.', 'authgate'); ?></td></tr>
                <tr><td><code>button_class</code></td><td><?php esc_html_e('clases CSS', 'authgate'); ?></td><td><?php esc_html_e('Clases adicionales para el botón del popup.', 'authgate'); ?></td></tr>
            </table>
            <div class="authgate-sc-example">[authgate_register]
[authgate_register mode="popup" label="Crear cuenta"]</div>
        </div>

        <div class="authgate-sc-block">
            <h3>[authgate_reset_password]</h3>
            <p><?php esc_html_e('Formulario de nueva contraseña. Úsalo en la página configurada como "Slug de reset contraseña". Lee los parámetros key y login de la URL automáticamente — no requiere atributos.', 'authgate'); ?></p>
            <div class="authgate-sc-example">[authgate_reset_password]</div>
            <p class="description" style="margin-top:8px;"><?php printf(esc_html__('URL activa: %s', 'authgate'), '<code>' . esc_html($reset_slug) . '/?key=…&login=…</code>'); ?></p>
        </div>

        <div class="authgate-sc-block" style="background:#f0f6fc;">
            <h3 style="background:#dbeafe;"><?php esc_html_e('URLs personalizadas activas', 'authgate'); ?></h3>
            <table class="authgate-sc-table">
                <tr><td><?php esc_html_e('Página de login', 'authgate'); ?></td><td><code><?php echo esc_html($login_slug); ?></code></td></tr>
                <tr><td><?php esc_html_e('Página de reset', 'authgate'); ?></td><td><code><?php echo esc_html($reset_slug); ?></code></td></tr>
            </table>
        </div>
        <?php
    }

    /** @return void */
    private function render_tab_blocked() {
        global $wpdb;

        $blacklist = (array) self::get('blacklist', array());

        // Leer IPs bloqueadas por rate limit desde transients
        $blocked = array();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $rows = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_authgate_rl_%'
             ORDER BY option_id DESC LIMIT 200"
        );

        foreach ($rows as $row) {
            $data = maybe_unserialize($row->option_value);
            if (!is_array($data)) continue;

            $key    = str_replace('_transient_authgate_rl_', '', $row->option_name);
            $expiry = (int) get_option('_transient_timeout_authgate_rl_' . $key, 0);
            $remaining = $expiry - time();
            if ($remaining <= 0) continue;

            $blocked[] = array(
                'key'       => $key,
                'ip'        => $data['ip'] ?? '—',
                'count'     => $data['count'] ?? 0,
                'remaining' => $remaining,
            );
        }
        ?>
        <h2><?php esc_html_e('IPs bloqueadas por rate limit', 'authgate'); ?></h2>

        <?php if (empty($blocked)) : ?>
            <p><?php esc_html_e('No hay IPs bloqueadas en este momento.', 'authgate'); ?></p>
        <?php else : ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('IP', 'authgate'); ?></th>
                        <th><?php esc_html_e('Intentos', 'authgate'); ?></th>
                        <th><?php esc_html_e('Expira en', 'authgate'); ?></th>
                        <th><?php esc_html_e('Acciones', 'authgate'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blocked as $b) :
                        $mins = ceil($b['remaining'] / 60);
                        ?>
                        <tr>
                            <td><?php echo esc_html($b['ip']); ?></td>
                            <td><?php echo esc_html($b['count']); ?></td>
                            <td><?php printf(esc_html__('%d min', 'authgate'), (int) $mins); ?></td>
                            <td>
                                <form method="post" action="<?php echo esc_url(self::admin_post_url()); ?>" style="display:inline;">
                                    <input type="hidden" name="action" value="authgate_unblock">
                                    <input type="hidden" name="key" value="<?php echo esc_attr($b['key']); ?>">
                                    <?php wp_nonce_field('authgate_unblock', '_authgate_nonce'); ?>
                                    <button type="submit" class="button"><?php esc_html_e('Desbloquear', 'authgate'); ?></button>
                                </form>
                                <form method="post" action="<?php echo esc_url(self::admin_post_url()); ?>" style="display:inline;margin-left:4px;">
                                    <input type="hidden" name="action" value="authgate_blacklist_add">
                                    <input type="hidden" name="ip" value="<?php echo esc_attr($b['ip']); ?>">
                                    <input type="hidden" name="key" value="<?php echo esc_attr($b['key']); ?>">
                                    <?php wp_nonce_field('authgate_blacklist_add', '_authgate_nonce'); ?>
                                    <button type="submit" class="button button-link-delete"><?php esc_html_e('Añadir a blacklist', 'authgate'); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2 style="margin-top:32px;"><?php esc_html_e('Blacklist permanente', 'authgate'); ?></h2>

        <form method="post" action="<?php echo esc_url(self::admin_post_url()); ?>" style="margin-bottom:16px;">
            <input type="hidden" name="action" value="authgate_blacklist_add">
            <?php wp_nonce_field('authgate_blacklist_add', '_authgate_nonce'); ?>
            <input type="text" name="ip" placeholder="<?php esc_attr_e('IP a bloquear', 'authgate'); ?>" style="width:220px;">
            <button type="submit" class="button button-primary"><?php esc_html_e('Añadir', 'authgate'); ?></button>
        </form>

        <?php if (empty($blacklist)) : ?>
            <p><?php esc_html_e('No hay IPs en la blacklist.', 'authgate'); ?></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:500px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('IP bloqueada', 'authgate'); ?></th>
                        <th><?php esc_html_e('Acción', 'authgate'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blacklist as $ip) : ?>
                        <tr>
                            <td><?php echo esc_html($ip); ?></td>
                            <td>
                                <form method="post" action="<?php echo esc_url(self::admin_post_url()); ?>">
                                    <input type="hidden" name="action" value="authgate_blacklist_del">
                                    <input type="hidden" name="ip" value="<?php echo esc_attr($ip); ?>">
                                    <?php wp_nonce_field('authgate_blacklist_del', '_authgate_nonce'); ?>
                                    <button type="submit" class="button button-link-delete"><?php esc_html_e('Eliminar', 'authgate'); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php
    }

    /** @return void */
    private function render_tab_log() {
        global $wpdb;

        $table    = $wpdb->prefix . 'authgate_log';
        $per_page = 50;
        $paged    = max(1, (int) ($_GET['paged'] ?? 1));
        $offset   = ($paged - 1) * $per_page;
        $filter_event = sanitize_key($_GET['filter_event'] ?? '');
        $filter_ip    = sanitize_text_field($_GET['filter_ip'] ?? '');

        $where = 'WHERE 1=1';
        $args  = array();
        if ($filter_event) {
            $where .= ' AND event = %s';
            $args[] = $filter_event;
        }
        if ($filter_ip) {
            $where .= ' AND ip = %s';
            $args[] = $filter_ip;
        }

        $count_sql = "SELECT COUNT(*) FROM {$table} {$where}";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $total = (int) $wpdb->get_var($wpdb->prepare($count_sql, ...$args));

        $data_sql = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $data_args = array_merge($args, array($per_page, $offset));
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $rows = $wpdb->get_results($wpdb->prepare($data_sql, $data_args));

        $total_pages = ceil($total / $per_page);

        $events = array('login_ok', 'login_fail', 'register_ok', 'register_fail', 'blocked');
        ?>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h2 style="margin:0;"><?php esc_html_e('Registro de accesos', 'authgate'); ?></h2>
            <form method="post" action="<?php echo esc_url(self::admin_post_url()); ?>">
                <input type="hidden" name="action" value="authgate_clean_log">
                <?php wp_nonce_field('authgate_clean_log', '_authgate_nonce'); ?>
                <select name="days">
                    <option value="30"><?php esc_html_e('Borrar registros > 30 días', 'authgate'); ?></option>
                    <option value="60"><?php esc_html_e('Borrar registros > 60 días', 'authgate'); ?></option>
                    <option value="90"><?php esc_html_e('Borrar registros > 90 días', 'authgate'); ?></option>
                    <option value="0"><?php esc_html_e('Borrar todo', 'authgate'); ?></option>
                </select>
                <button type="submit" class="button"><?php esc_html_e('Limpiar', 'authgate'); ?></button>
            </form>
        </div>

        <!-- Filtros -->
        <form method="get" style="margin-bottom:16px;">
            <input type="hidden" name="page" value="authgate">
            <input type="hidden" name="tab" value="log">
            <select name="filter_event">
                <option value=""><?php esc_html_e('Todos los eventos', 'authgate'); ?></option>
                <?php foreach ($events as $ev) : ?>
                    <option value="<?php echo esc_attr($ev); ?>" <?php selected($filter_event, $ev); ?>>
                        <?php echo esc_html($ev); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="filter_ip" value="<?php echo esc_attr($filter_ip); ?>" placeholder="<?php esc_attr_e('Filtrar por IP', 'authgate'); ?>" style="width:160px;">
            <button type="submit" class="button"><?php esc_html_e('Filtrar', 'authgate'); ?></button>
        </form>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Fecha', 'authgate'); ?></th>
                    <th><?php esc_html_e('Evento', 'authgate'); ?></th>
                    <th><?php esc_html_e('Usuario', 'authgate'); ?></th>
                    <th><?php esc_html_e('IP', 'authgate'); ?></th>
                    <th><?php esc_html_e('Navegador', 'authgate'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)) : ?>
                    <tr><td colspan="5"><?php esc_html_e('Sin resultados.', 'authgate'); ?></td></tr>
                <?php else : ?>
                    <?php foreach ($rows as $row) :
                        $color = strpos($row->event, '_ok') !== false ? '#0a6b0a' : (strpos($row->event, 'block') !== false ? '#8b0000' : '#666');
                        ?>
                        <tr>
                            <td><?php echo esc_html($row->created_at); ?></td>
                            <td><span style="color:<?php echo esc_attr($color); ?>;font-weight:600;"><?php echo esc_html($row->event); ?></span></td>
                            <td><?php echo esc_html($row->username ?: '—'); ?></td>
                            <td><?php echo esc_html($row->ip); ?></td>
                            <td style="font-size:12px;color:#666;"><?php echo esc_html(substr($row->user_agent, 0, 80)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) :
            $base_url = add_query_arg(array('page' => 'authgate', 'tab' => 'log', 'filter_event' => $filter_event, 'filter_ip' => $filter_ip), self::settings_base_url());
            ?>
            <div style="margin-top:12px;">
                <?php if ($paged > 1) : ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $paged - 1, $base_url)); ?>" class="button">&laquo;</a>
                <?php endif; ?>
                <span style="margin:0 8px;"><?php printf(esc_html__('Página %d de %d', 'authgate'), (int) $paged, (int) $total_pages); ?></span>
                <?php if ($paged < $total_pages) : ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $paged + 1, $base_url)); ?>" class="button">&raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php
    }

    /** @return void */
    public function save_settings() {
        $is_ajax = wp_doing_ajax();
        if ($is_ajax) {
            check_ajax_referer('authgate_save', '_authgate_nonce');
        } else {
            check_admin_referer('authgate_save', '_authgate_nonce');
        }
        if (!current_user_can(self::required_cap())) {
            $is_ajax ? wp_send_json_error(array('message' => __('Sin permiso.', 'authgate'))) : wp_die();
        }

        self::update_setting('max_attempts',        max(1, (int) ($_POST['max_attempts'] ?? 10)));

        if (self::is_network()) {
            $network_registration = sanitize_key($_POST['network_registration'] ?? 'none');
            if (!in_array($network_registration, array('none', 'user', 'blog', 'all'), true)) {
                $network_registration = 'none';
            }
            update_site_option('registration', $network_registration);
        }

        self::update_setting('login_logo_url',      esc_url_raw(wp_unslash($_POST['login_logo_url'] ?? '')));
        self::update_setting('inline_intro_html',   wp_kses_post(wp_unslash($_POST['inline_intro_html'] ?? '')));
        self::update_setting('login_slug',          sanitize_title(wp_unslash($_POST['login_slug'] ?? 'acceder')));
        self::update_setting('login_slug_redirect', esc_url_raw(wp_unslash($_POST['login_slug_redirect'] ?? '')));
        self::update_setting('reset_slug',          sanitize_title(wp_unslash($_POST['reset_slug'] ?? 'restablecer-contrasena')));
        self::update_setting('block_wp_login',      !empty($_POST['block_wp_login']));
        if (!self::is_network()) {
            update_option('users_can_register', !empty($_POST['users_can_register']) ? 1 : 0);

            if (self::is_woocommerce_active()) {
                update_option('woocommerce_registration_generate_password', !empty($_POST['woocommerce_registration_generate_password']) ? 'yes' : 'no');
            }

            self::update_setting('mailmint_list_id', max(0, (int) ($_POST['mailmint_list_id'] ?? 0)));
            $excluded = array_filter(array_map('intval', (array) ($_POST['excluded_pages'] ?? array())));
            self::update_setting('excluded_pages', $excluded);
        }

        AuthGate_Install::flush_custom_rewrite_rules();

        if ($is_ajax) {
            wp_send_json_success(array('message' => __('Ajustes guardados.', 'authgate')));
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'updated' => '1'), self::settings_base_url()));
        exit;
    }

    /** @return void */
    public function save_strings() {
        $is_ajax = wp_doing_ajax();
        if ($is_ajax) {
            check_ajax_referer('authgate_save_strings', '_authgate_nonce');
        } else {
            check_admin_referer('authgate_save_strings', '_authgate_nonce');
        }
        if (!current_user_can(self::required_cap())) {
            $is_ajax ? wp_send_json_error(array('message' => __('Sin permiso.', 'authgate'))) : wp_die();
        }

        $this->save_string_values();

        if ($is_ajax) {
            wp_send_json_success(array('message' => __('Textos guardados.', 'authgate')));
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'tab' => 'strings', 'updated' => '1'), self::settings_base_url()));
        exit;
    }

    /** @return void */
    public function save_css() {
        $is_ajax = wp_doing_ajax();
        if ($is_ajax) {
            check_ajax_referer('authgate_save_css', '_authgate_nonce');
        } else {
            check_admin_referer('authgate_save_css', '_authgate_nonce');
        }
        if (!current_user_can(self::required_cap())) {
            $is_ajax ? wp_send_json_error(array('message' => __('Sin permiso.', 'authgate'))) : wp_die();
        }

        self::update_setting('custom_css_enabled', !empty($_POST['custom_css_enabled']));
        self::update_setting('custom_css', self::sanitize_custom_css(wp_unslash($_POST['custom_css'] ?? '')));

        if ($is_ajax) {
            wp_send_json_success(array('message' => __('CSS guardado.', 'authgate')));
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'tab' => 'css', 'updated' => '1'), self::settings_base_url()));
        exit;
    }

    /** @param string $css */
    private static function sanitize_custom_css(string $css): string {
        $css = wp_strip_all_tags($css);
        $css = preg_replace('/@import\b[^;]*;?/i', '', $css);
        $css = preg_replace('/expression\s*\([^)]*\)/i', '', $css);
        $css = preg_replace('/javascript\s*:/i', '', $css);
        $css = preg_replace('/behavior\s*:/i', '', $css);
        $css = preg_replace('/-moz-binding\s*:/i', '', $css);
        return trim((string) $css);
    }

    /** @return void */
    private function save_string_values(bool $site_scope = false): void {
        $defs          = self::string_definitions();
        $textarea_keys = self::textarea_string_keys();
        $wysiwyg_keys  = self::wysiwyg_string_keys();
        $allowed_tags  = self::html_string_allowed_tags();
        foreach (array_keys($defs) as $key) {
            $raw = wp_unslash($_POST['strings'][$key] ?? '');
            if (in_array($key, $wysiwyg_keys, true)) {
                $val = wp_kses_post($raw);
            } elseif (in_array($key, $textarea_keys, true)) {
                $val = wp_kses($raw, $allowed_tags);
            } else {
                $val = sanitize_text_field($raw);
            }

            if ($site_scope) {
                update_option(self::option_name('str_' . $key), $val);
            } else {
                self::update_setting('str_' . $key, $val);
            }
        }
    }

    /** @return void */
    private function render_site_css_section(): void {
        $presets = self::css_presets();
        $mode    = self::custom_css_mode();
        ?>
        <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
            <h2 style="margin-top:0;"><?php esc_html_e('Estilo de este sitio', 'authgate'); ?></h2>
            <p class="description" style="margin-bottom:16px;">
                <?php esc_html_e('Puedes heredar el CSS global de la red, sobrescribirlo solo en esta web o desactivar el CSS propio en este sitio.', 'authgate'); ?>
            </p>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Modo CSS', 'authgate'); ?></th>
                    <td>
                        <label><input type="radio" name="custom_css_mode" value="inherit" <?php checked($mode, 'inherit'); ?>> <?php esc_html_e('Heredar CSS global de la red', 'authgate'); ?></label><br>
                        <label><input type="radio" name="custom_css_mode" value="override" <?php checked($mode, 'override'); ?>> <?php esc_html_e('Usar CSS propio para este sitio', 'authgate'); ?></label><br>
                        <label><input type="radio" name="custom_css_mode" value="disabled" <?php checked($mode, 'disabled'); ?>> <?php esc_html_e('Desactivar CSS propio en este sitio', 'authgate'); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="authgate_site_custom_css"><?php esc_html_e('CSS de este sitio', 'authgate'); ?></label></th>
                    <td>
                        <textarea id="authgate_site_custom_css" name="custom_css" rows="14" style="width:100%;font-family:monospace;"><?php echo esc_textarea(get_option(self::option_name('custom_css'), '')); ?></textarea>
                        <p class="description"><?php esc_html_e('Solo se usa si eliges “Usar CSS propio para este sitio”. Deja vacío para no añadir reglas locales.', 'authgate'); ?></p>
                    </td>
                </tr>
            </table>
            <details>
                <summary><?php esc_html_e('Ver presets copiables', 'authgate'); ?></summary>
                <h3><?php esc_html_e('Preset blanco', 'authgate'); ?></h3>
                <textarea readonly rows="8" style="width:100%;font-family:monospace;margin-bottom:16px;"><?php echo esc_textarea($presets['white']); ?></textarea>
                <h3><?php esc_html_e('Preset oscuro', 'authgate'); ?></h3>
                <textarea readonly rows="8" style="width:100%;font-family:monospace;"><?php echo esc_textarea($presets['dark']); ?></textarea>
            </details>
        </div>
        <?php
    }

    /** @return void */
    public function handle_unblock() {
        check_admin_referer('authgate_unblock', '_authgate_nonce');
        if (!current_user_can(self::required_cap())) wp_die();

        $key = sanitize_key($_POST['key'] ?? '');
        if ($key) {
            delete_transient('authgate_rl_' . $key);
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'tab' => 'blocked'), self::settings_base_url()));
        exit;
    }

    /** @return void */
    public function handle_blacklist_add() {
        check_admin_referer('authgate_blacklist_add', '_authgate_nonce');
        if (!current_user_can(self::required_cap())) wp_die();

        $ip  = sanitize_text_field($_POST['ip'] ?? '');
        $key = sanitize_key($_POST['key'] ?? '');

        if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
            $list   = (array) self::get('blacklist', array());
            $list[] = $ip;
            self::update_setting('blacklist', array_unique($list));

            if ($key) {
                delete_transient('authgate_rl_' . $key);
            }
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'tab' => 'blocked'), self::settings_base_url()));
        exit;
    }

    /** @return void */
    public function handle_blacklist_del() {
        check_admin_referer('authgate_blacklist_del', '_authgate_nonce');
        if (!current_user_can(self::required_cap())) wp_die();

        $ip   = sanitize_text_field($_POST['ip'] ?? '');
        $list = (array) self::get('blacklist', array());
        $list = array_filter($list, fn($i) => $i !== $ip);
        self::update_setting('blacklist', array_values($list));

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'tab' => 'blocked'), self::settings_base_url()));
        exit;
    }

    // -------------------------------------------------------------------------
    // Multisite: página de ajustes por subsite (Exclusiones + Mail Mint)
    // -------------------------------------------------------------------------

    /** @return void */
    public function register_site_menu() {
        add_options_page(
            __('AuthGate', 'authgate'),
            __('AuthGate', 'authgate'),
            'manage_options',
            'authgate-site',
            array($this, 'render_site_page')
        );
    }

    /** @return void */
    private function render_site_strings_section(): void {
        $defs          = self::string_definitions();
        $textarea_keys = self::textarea_string_keys();
        $wysiwyg_keys  = self::wysiwyg_string_keys();
        ?>
        <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
            <h2 style="margin-top:0;"><?php esc_html_e('Textos de este sitio', 'authgate'); ?></h2>
            <p class="description" style="margin-bottom:16px;">
                <?php esc_html_e('Deja un campo vacío para heredar el texto global configurado en la red. Si la red tampoco tiene texto, se usará el valor por defecto del plugin.', 'authgate'); ?>
            </p>
            <div class="authgate-strings-grid">
                <?php foreach ($defs as $key => $default) :
                    $field_id   = 'authgate_site_str_' . $key;
                    $saved      = get_option('authgate_str_' . $key, '');
                    $inherited  = self::get_string($key);
                    $is_wide    = in_array($key, $textarea_keys, true) || in_array($key, $wysiwyg_keys, true);
                    $field_class = $is_wide ? 'authgate-string-field authgate-string-field--wide' : 'authgate-string-field';
                    ?>
                    <div class="<?php echo esc_attr($field_class); ?>">
                        <label for="<?php echo esc_attr($field_id); ?>"><code><?php echo esc_html($key); ?></code></label>
                        <?php if (in_array($key, $wysiwyg_keys, true)) : ?>
                            <?php wp_editor($saved, $field_id, array(
                                'textarea_name' => 'strings[' . $key . ']',
                                'textarea_rows' => 5,
                                'media_buttons' => false,
                                'teeny'         => false,
                            )); ?>
                        <?php elseif (in_array($key, $textarea_keys, true)) : ?>
                            <textarea id="<?php echo esc_attr($field_id); ?>" name="strings[<?php echo esc_attr($key); ?>]" rows="4" placeholder="<?php echo esc_attr($inherited); ?>"><?php echo esc_textarea($saved); ?></textarea>
                        <?php else : ?>
                            <input type="text" id="<?php echo esc_attr($field_id); ?>" name="strings[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($saved); ?>" placeholder="<?php echo esc_attr($inherited); ?>">
                        <?php endif; ?>
                        <p class="description">
                            <?php printf(esc_html__('Heredado si queda vacío: %s', 'authgate'), esc_html($inherited)); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /** @return void */
    public function render_site_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Sin permiso.', 'authgate'));
        }

        $excluded_pages = (array) self::get('excluded_pages', array());
        $all_pages      = get_pages(array('sort_column' => 'post_title'));
        ?>
        <div class="mw22-back authgate-back" data-mw22-back data-mw22-theme-key="authgateBackTheme" data-mw22-updated-message="Ajustes guardados.">
            <header class="mw22-back__header">
                <a class="mw22-back__brand" href="https://22mw.online/" target="_blank" rel="noopener noreferrer">
                    <span class="mw22-back__mark" aria-hidden="true">
                        <svg viewBox="0 0 45 56" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="0 20 44.9189011 20 44.9189011 15 4.48417582 15 4.48417582 12.5 44.9189011 12.5 45 12.5 45 0 0.0810989011 0 0.0810989011 5 40.5168132 5 40.5168132 7.5 0 7.5 0 20"/>
                            <polygon points="0.0810989011 42 44.9189011 42 44.9189011 37 4.48417582 37 4.48417582 34.5 45 34.5 45 22 0.0810989011 22 0.0810989011 27 40.5168132 27 40.5168132 29.5 0 29.5 0 42"/>
                            <polygon points="21 44 0 44 0 56 5.37209302 56 5.37209302 48.8 7.81395349 48.8 7.81395349 56 13.1860465 56 13.1860465 48.8 15.627907 48.8 15.627907 56 21 56"/>
                            <polygon transform="translate(34.5,50) rotate(-180) translate(-34.5,-50)" points="24 44 24 56 29.372093 56 29.372093 48.8 31.8139535 48.8 31.8139535 56 37.1860465 56 37.1860465 48.8 39.627907 48.8 39.627907 56 45 56 45 44"/>
                        </svg>
                    </span>
                    <div class="mw22-back__title-row">
                        <h1><?php esc_html_e('AuthGate', 'authgate'); ?></h1>
                        <span class="mw22-back__version"><?php esc_html_e('Ajustes de este sitio', 'authgate'); ?></span>
                    </div>
                </a>
                <div class="mw22-back__actions">
                    <a class="button button-secondary" href="<?php echo esc_url(network_admin_url('settings.php?page=authgate')); ?>"><?php esc_html_e('Config global', 'authgate'); ?></a>
                    <button type="button" class="mw22-back__theme" data-mw22-back-theme="dark"><?php esc_html_e('Oscuro', 'authgate'); ?></button>
                    <button type="button" class="mw22-back__theme" data-mw22-back-theme="light"><?php esc_html_e('Claro', 'authgate'); ?></button>
                </div>
            </header>

            <main class="mw22-back__content">
            <p class="description" style="margin-bottom:20px;">
                <?php esc_html_e('Ajustes específicos de este sitio. La activación/desactivación del registro se gestiona desde AuthGate en el administrador de red.', 'authgate'); ?>
            </p>

            <form id="authgate-site-settings-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-authgate-ajax-form>
                <input type="hidden" name="action" value="authgate_save_site">
                <?php wp_nonce_field('authgate_save_site', '_authgate_nonce'); ?>

                <?php $this->render_site_strings_section(); ?>

                <?php $this->render_site_css_section(); ?>

                <!-- Exclusiones -->
                <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                    <h2 style="margin-top:0;"><?php esc_html_e('Exclusiones', 'authgate'); ?></h2>
                    <p class="description" style="margin-bottom:16px;">
                        <?php esc_html_e('Páginas donde AuthGate no debe actuar (queda el comportamiento nativo de WP/WooCommerce).', 'authgate'); ?>
                    </p>
                    <div style="columns:2;column-gap:32px;">
                        <?php foreach ($all_pages as $page) : ?>
                            <label style="display:flex;align-items:center;gap:8px;padding:4px 0;break-inside:avoid;">
                                <input type="checkbox"
                                       name="excluded_pages[]"
                                       value="<?php echo esc_attr($page->ID); ?>"
                                       <?php checked(in_array($page->ID, $excluded_pages, true)); ?>>
                                <?php echo esc_html($page->post_title); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Mail Mint -->
                <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                    <h2 style="margin-top:0;"><?php esc_html_e('Mail Mint — Suscripción al registro', 'authgate'); ?></h2>
                    <?php if (!class_exists('Mint\MRM\DataBase\Models\ContactGroupModel')) : ?>
                        <p><?php esc_html_e('Mail Mint no está activo.', 'authgate'); ?></p>
                    <?php else :
                        $model      = \Mint\MRM\DataBase\Models\ContactGroupModel::class;
                        $raw        = method_exists($model, 'get_all')
                            ? $model::get_all('lists')
                            : $model::get_all_to_custom_select('lists'); // phpcs:ignore -- fallback para versiones antiguas
                        $mm_lists   = isset($raw['data']) ? $raw['data'] : array();
                        $saved_list = (int) self::get('mailmint_list_id', 0);
                    ?>
                    <p class="description" style="margin-bottom:12px;">
                        <?php esc_html_e('Los usuarios que no marquen "NO quiero recibir comunicaciones" se añadirán automáticamente a la lista seleccionada.', 'authgate'); ?>
                    </p>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="authgate_mailmint_list"><?php esc_html_e('Lista de suscripción', 'authgate'); ?></label></th>
                            <td>
                                <select id="authgate_mailmint_list" name="mailmint_list_id">
                                    <option value="0"><?php esc_html_e('— Sin lista —', 'authgate'); ?></option>
                                    <?php foreach ($mm_lists as $list) : ?>
                                        <option value="<?php echo esc_attr($list['id']); ?>" <?php selected($saved_list, (int) $list['id']); ?>>
                                            <?php echo esc_html($list['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php endif; ?>
                </div>

                <?php submit_button(__('Guardar', 'authgate')); ?>
            </form>
            </main>
        </div>
        <?php
    }

    /** @return void */
    public function save_site_settings() {
        $is_ajax = wp_doing_ajax();
        if ($is_ajax) {
            check_ajax_referer('authgate_save_site', '_authgate_nonce');
        } else {
            check_admin_referer('authgate_save_site', '_authgate_nonce');
        }
        if (!current_user_can('manage_options')) {
            $is_ajax ? wp_send_json_error(array('message' => __('Sin permiso.', 'authgate'))) : wp_die();
        }

        $this->save_string_values(true);

        $css_mode = sanitize_key($_POST['custom_css_mode'] ?? 'inherit');
        if (!in_array($css_mode, array('inherit', 'override', 'disabled'), true)) {
            $css_mode = 'inherit';
        }
        update_option(self::option_name('custom_css_mode'), $css_mode);
        update_option(self::option_name('custom_css'), self::sanitize_custom_css(wp_unslash($_POST['custom_css'] ?? '')));

        $excluded = array_filter(array_map('intval', (array) ($_POST['excluded_pages'] ?? array())));
        update_option('authgate_excluded_pages', $excluded);
        update_option('authgate_mailmint_list_id', max(0, (int) ($_POST['mailmint_list_id'] ?? 0)));

        if ($is_ajax) {
            wp_send_json_success(array('message' => __('Ajustes guardados.', 'authgate')));
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate-site', 'updated' => '1'), admin_url('options-general.php')));
        exit;
    }

    /** @return void */
    public function handle_clean_log() {
        global $wpdb;

        check_admin_referer('authgate_clean_log', '_authgate_nonce');
        if (!current_user_can(self::required_cap())) wp_die();

        $days  = (int) ($_POST['days'] ?? 30);
        $table = $wpdb->prefix . 'authgate_log';

        if ($days === 0) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $wpdb->query("TRUNCATE TABLE {$table}");
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery, PluginCheck.Security.DirectDB.UnescapedDBParameter
            $wpdb->query($wpdb->prepare(
                "DELETE FROM {$table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days
            ));
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'tab' => 'log'), self::settings_base_url()));
        exit;
    }
}
