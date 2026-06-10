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

    /**
     * Claves que se guardan por subsite (get_option) aunque estemos en multisite.
     *
     * @return string[]
     */
    private static function subsite_keys(): array {
        return array('excluded_pages', 'mailmint_list_id');
    }

    /**
     * Guarda una opción de ajustes.
     * En multisite, las claves de subsite_keys() usan get/update_option (por sitio);
     * el resto usa site options (red).
     *
     * @param string $key   Sin prefijo 'authgate_'
     * @param mixed  $value
     * @return void
     */
    private static function update_setting( string $key, $value ): void {
        if (self::is_network() && !in_array($key, self::subsite_keys(), true)) {
            update_site_option('authgate_' . $key, $value);
        } else {
            update_option('authgate_' . $key, $value);
        }
    }

    /**
     * @param string $hook
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'authgate') === false) return;
        wp_enqueue_media();
        $css = '.authgate-admin-toast{position:fixed;bottom:32px;left:50%;transform:translateX(-50%);background:#1e1e1e;color:#fff;padding:12px 20px;border-radius:2px;font-size:13px;line-height:1.4;z-index:999999;box-shadow:0 2px 6px rgba(0,0,0,.3);animation:authgate-toast-in .2s ease;transition:opacity .35s;max-width:420px;white-space:nowrap}.authgate-admin-toast.is-error{background:#b33654}.authgate-admin-toast.is-hiding{opacity:0}@keyframes authgate-toast-in{from{opacity:0;transform:translateX(-50%) translateY(8px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}';
        wp_add_inline_style('wp-admin', $css);
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
        if (self::is_network() && !in_array($key, self::subsite_keys(), true)) {
            return get_site_option('authgate_' . $key, $default);
        }
        return get_option('authgate_' . $key, $default);
    }

    /** @return bool */
    public static function registration_enabled(): bool {
        return (bool) get_option('users_can_register');
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
        $defs    = self::string_definitions();
        $default = $defs[$key] ?? '';
        $saved   = self::is_network()
            ? get_site_option('authgate_str_' . $key, '')
            : get_option('authgate_str_' . $key, '');
        return $saved !== '' ? $saved : $default;
    }

    /** @return void */
    public function render_page() {
        if (!current_user_can(self::required_cap())) {
            wp_die(esc_html__('Sin permiso.', 'authgate'));
        }

        $tab = sanitize_key($_GET['tab'] ?? 'general');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('AuthGate by 22MW', 'authgate'); ?></h1>
            <nav class="nav-tab-wrapper" style="margin-bottom:20px;">
                <?php
                $tabs = array(
                    'general'    => __('General', 'authgate'),
                    'shortcodes' => __('Shortcodes', 'authgate'),
                    'blocked'    => __('IPs bloqueadas', 'authgate'),
                    'log'        => __('Registro de accesos', 'authgate'),
                );
                foreach ($tabs as $slug => $label) :
                    $active = $tab === $slug ? 'nav-tab-active' : '';
                    $url    = add_query_arg(array('page' => 'authgate', 'tab' => $slug), self::settings_base_url());
                    ?>
                    <a href="<?php echo esc_url($url); ?>" class="nav-tab <?php echo esc_attr($active); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php
            if ($tab === 'general') {
                $this->render_tab_general();
            } elseif ($tab === 'shortcodes') {
                $this->render_tab_shortcodes();
            } elseif ($tab === 'blocked') {
                $this->render_tab_blocked();
            } elseif ($tab === 'log') {
                $this->render_tab_log();
            }
            ?>
        </div>
        <?php
    }

    /** @return void */
    private function render_tab_general() {
        $max_attempts   = (int) self::get('max_attempts', 10);
        $excluded_pages = (array) self::get('excluded_pages', array());
        $all_pages      = get_pages(array('sort_column' => 'post_title'));
        $string_defs    = self::string_definitions();
        ?>
        <form id="authgate-settings-form" method="post" action="<?php echo esc_url(self::admin_post_url()); ?>">
            <input type="hidden" name="action" value="authgate_save">
            <?php wp_nonce_field('authgate_save', '_authgate_nonce'); ?>

            <!-- Rate limiting -->
            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
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
            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Registro de usuarios', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;">
                    <?php esc_html_e('Estos controles usan las opciones nativas de WordPress y WooCommerce. Si el registro está desactivado, AuthGate ocultará la parte de registro en frontend.', 'authgate'); ?>
                </p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Permitir registro', 'authgate'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="users_can_register" value="1" <?php checked(self::registration_enabled()); ?>>
                                <?php esc_html_e('Permitir que los visitantes creen una cuenta', 'authgate'); ?>
                            </label>
                            <p class="description"><?php esc_html_e('Equivale a la opción nativa de WordPress “Cualquiera puede registrarse”.', 'authgate'); ?></p>
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
                </table>
            </div>

            <?php if (!self::is_network()) : ?>
            <!-- Exclusiones -->
            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Exclusiones', 'authgate'); ?></h2>
                <p class="description" style="margin-bottom:16px;">
                    <?php esc_html_e('El plugin intercepta automáticamente cualquier página que requiera login y muestra su formulario. Marca aquí las páginas donde NO debe actuar (quedará el comportamiento nativo de WP/WooCommerce).', 'authgate'); ?>
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
            <?php endif; ?>

            <!-- Strings editables -->
            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
                <h2 style="margin-top:0;"><?php esc_html_e('Textos del formulario', 'authgate'); ?></h2>
                <table class="form-table">
                    <?php
                    $textarea_keys = self::textarea_string_keys();
                    $wysiwyg_keys  = self::wysiwyg_string_keys();
                    foreach ($string_defs as $key => $default) :
                        $is_wysiwyg  = in_array($key, $wysiwyg_keys, true);
                        $is_textarea = !$is_wysiwyg && in_array($key, $textarea_keys, true);
                    ?>
                        <tr>
                            <th scope="row">
                                <label for="authgate_str_<?php echo esc_attr($key); ?>">
                                    <code><?php echo esc_html($key); ?></code>
                                </label>
                            </th>
                            <td>
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
                                <textarea id="authgate_str_<?php echo esc_attr($key); ?>"
                                          name="strings[<?php echo esc_attr($key); ?>]"
                                          style="width:440px;height:80px;"><?php echo esc_textarea(self::get_string($key)); ?></textarea>
                                <?php else : ?>
                                <input type="text"
                                       id="authgate_str_<?php echo esc_attr($key); ?>"
                                       name="strings[<?php echo esc_attr($key); ?>]"
                                       value="<?php echo esc_attr(self::get_string($key)); ?>"
                                       style="width:440px;">
                                <?php endif; ?>
                                <?php if ($default !== '') : ?>
                                <p class="description">
                                    <?php esc_html_e('Por defecto:', 'authgate'); ?>
                                    <em><?php echo esc_html($default); ?></em>
                                </p>
                                <?php endif; ?>
                                <?php if ($key === 'field_gdpr') : ?>
                                <p class="description"><?php esc_html_e('Usa {privacy_url} para insertar el enlace a la política de privacidad.', 'authgate'); ?></p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- URLs personalizadas -->
            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
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
            <div style="background:#fff;padding:24px;margin-bottom:20px;border:1px solid #ccd0d4;border-radius:4px;">
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

            // AJAX save
            var $form = $('#authgate-settings-form');
            var $btn  = $form.find('[type="submit"]');

            $form.on('submit', function(e){
                e.preventDefault();
                if (typeof tinyMCE !== 'undefined') { tinyMCE.triggerSave(); }
                $btn.prop('disabled', true);
                var data = new FormData(this);
                fetch(ajaxurl, { method: 'POST', body: data, credentials: 'same-origin' })
                    .then(function(r){ return r.json(); })
                    .then(function(res){
                        if (res.success) {
                            authgateToast(res.data.message);
                        } else {
                            authgateToast(res.data && res.data.message ? res.data.message : '<?php echo esc_js(__('Error al guardar.', 'authgate')); ?>', 'error');
                        }
                    })
                    .catch(function(){
                        authgateToast('<?php echo esc_js(__('Error al guardar.', 'authgate')); ?>', 'error');
                    })
                    .finally(function(){
                        $btn.prop('disabled', false);
                    });
            });

            function authgateToast(msg, type) {
                var $t = $('<div class="authgate-admin-toast' + (type === 'error' ? ' is-error' : '') + '">').text(msg);
                $('body').append($t);
                setTimeout(function(){ $t.addClass('is-hiding'); }, 2800);
                setTimeout(function(){ $t.remove(); }, 3200);
            }
        })(jQuery);
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
                <tr><td><code>button_class</code></td><td><?php esc_html_e('clases CSS', 'authgate'); ?></td><td><?php esc_html_e('Clases adicionales para el botón del popup (solo mode=popup).', 'authgate'); ?></td></tr>
            </table>
            <div class="authgate-sc-example">[authgate_auth]
[authgate_auth mode="popup" button_class="btn btn-primary"]
[authgate_auth mode="inline" default_tab="register" redirect="<?php echo esc_html(home_url('/gracias/')); ?>"]</div>
        </div>

        <div class="authgate-sc-block">
            <h3>[authgate_login]</h3>
            <p><?php esc_html_e('Solo formulario de login.', 'authgate'); ?></p>
            <table class="authgate-sc-table">
                <tr><th><?php esc_html_e('Parámetro', 'authgate'); ?></th><th><?php esc_html_e('Valores', 'authgate'); ?></th><th><?php esc_html_e('Descripción', 'authgate'); ?></th></tr>
                <tr><td><code>mode</code></td><td><code>inline</code> · <code>popup</code></td><td><?php esc_html_e('inline: incrustado. popup: modal.', 'authgate'); ?></td></tr>
                <tr><td><code>redirect</code></td><td><?php esc_html_e('URL', 'authgate'); ?></td><td><?php esc_html_e('URL tras login exitoso.', 'authgate'); ?></td></tr>
                <tr><td><code>button_class</code></td><td><?php esc_html_e('clases CSS', 'authgate'); ?></td><td><?php esc_html_e('Clases adicionales para el botón del popup.', 'authgate'); ?></td></tr>
            </table>
            <div class="authgate-sc-example">[authgate_login]
[authgate_login mode="popup" button_class="btn btn-secondary"]</div>
        </div>

        <div class="authgate-sc-block">
            <h3>[authgate_register]</h3>
            <p><?php esc_html_e('Solo formulario de registro.', 'authgate'); ?></p>
            <table class="authgate-sc-table">
                <tr><th><?php esc_html_e('Parámetro', 'authgate'); ?></th><th><?php esc_html_e('Valores', 'authgate'); ?></th><th><?php esc_html_e('Descripción', 'authgate'); ?></th></tr>
                <tr><td><code>mode</code></td><td><code>inline</code> · <code>popup</code></td><td><?php esc_html_e('inline: incrustado. popup: modal.', 'authgate'); ?></td></tr>
                <tr><td><code>redirect</code></td><td><?php esc_html_e('URL', 'authgate'); ?></td><td><?php esc_html_e('URL tras registro exitoso.', 'authgate'); ?></td></tr>
                <tr><td><code>button_class</code></td><td><?php esc_html_e('clases CSS', 'authgate'); ?></td><td><?php esc_html_e('Clases adicionales para el botón del popup.', 'authgate'); ?></td></tr>
            </table>
            <div class="authgate-sc-example">[authgate_register]
[authgate_register mode="popup"]</div>
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
        self::update_setting('login_logo_url',      esc_url_raw(wp_unslash($_POST['login_logo_url'] ?? '')));
        self::update_setting('login_slug',          sanitize_title(wp_unslash($_POST['login_slug'] ?? 'acceder')));
        self::update_setting('login_slug_redirect', esc_url_raw(wp_unslash($_POST['login_slug_redirect'] ?? '')));
        self::update_setting('reset_slug',          sanitize_title(wp_unslash($_POST['reset_slug'] ?? 'restablecer-contrasena')));
        self::update_setting('block_wp_login',      !empty($_POST['block_wp_login']));
        update_option('users_can_register', !empty($_POST['users_can_register']) ? 1 : 0);

        if (self::is_woocommerce_active()) {
            update_option('woocommerce_registration_generate_password', !empty($_POST['woocommerce_registration_generate_password']) ? 'yes' : 'no');
        }

        if (!self::is_network()) {
            self::update_setting('mailmint_list_id', max(0, (int) ($_POST['mailmint_list_id'] ?? 0)));
            $excluded = array_filter(array_map('intval', (array) ($_POST['excluded_pages'] ?? array())));
            self::update_setting('excluded_pages', $excluded);
        }

        AuthGate_Install::flush_custom_rewrite_rules();

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
            self::update_setting('str_' . $key, $val);
        }

        if ($is_ajax) {
            wp_send_json_success(array('message' => __('Ajustes guardados.', 'authgate')));
        }

        wp_safe_redirect(add_query_arg(array('page' => 'authgate', 'updated' => '1'), self::settings_base_url()));
        exit;
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
    public function render_site_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Sin permiso.', 'authgate'));
        }

        $excluded_pages = (array) self::get('excluded_pages', array());
        $all_pages      = get_pages(array('sort_column' => 'post_title'));
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('AuthGate', 'authgate'); ?></h1>
            <p class="description" style="margin-bottom:20px;">
                <?php esc_html_e('Ajustes específicos de este sitio. Los ajustes globales (textos, URLs, rate limiting) se gestionan desde el administrador de red.', 'authgate'); ?>
            </p>

            <form id="authgate-site-settings-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="authgate_save_site">
                <?php wp_nonce_field('authgate_save_site', '_authgate_nonce'); ?>

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
            <script>
            (function($){
                var $form = $('#authgate-site-settings-form');
                var $btn  = $form.find('[type="submit"]');
                $form.on('submit', function(e){
                    e.preventDefault();
                    $btn.prop('disabled', true);
                    var data = new FormData(this);
                    fetch(ajaxurl, { method: 'POST', body: data, credentials: 'same-origin' })
                        .then(function(r){ return r.json(); })
                        .then(function(res){
                            if (res.success) {
                                authgateSiteToast(res.data.message);
                            } else {
                                authgateSiteToast(res.data && res.data.message ? res.data.message : '<?php echo esc_js(__('Error al guardar.', 'authgate')); ?>', 'error');
                            }
                        })
                        .catch(function(){
                            authgateSiteToast('<?php echo esc_js(__('Error al guardar.', 'authgate')); ?>', 'error');
                        })
                        .finally(function(){ $btn.prop('disabled', false); });
                });
                function authgateSiteToast(msg, type) {
                    var $t = $('<div class="authgate-admin-toast' + (type === 'error' ? ' is-error' : '') + '">').text(msg);
                    $('body').append($t);
                    setTimeout(function(){ $t.addClass('is-hiding'); }, 2800);
                    setTimeout(function(){ $t.remove(); }, 3200);
                }
            })(jQuery);
            </script>
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
