<?php
defined('ABSPATH') || exit;

class AuthGate_Forms
{

    /** @var self|null */
    private static $instance = null;

    /** @var string[] Overlays de popup pendientes de imprimir en wp_footer */
    private static $footer_overlays = array();

    /** @return self */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_shortcode('authgate_login',           array($this, 'shortcode_login'));
        add_shortcode('authgate_register',        array($this, 'shortcode_register'));
        add_shortcode('authgate_auth',            array($this, 'shortcode_auth'));
        add_shortcode('authgate_combined',        array($this, 'shortcode_auth'));
        add_shortcode('authgate_reset_password',  array($this, 'shortcode_reset_password'));
        add_shortcode('authgate_reset',           array($this, 'shortcode_reset_password'));

        add_action('init',                  array($this, 'register_rewrite_rules'));
        add_action('init',                  array($this, 'maybe_redirect_wp_admin'));
        add_filter('query_vars',            array($this, 'register_query_vars'));
        add_action('wp_enqueue_scripts',    array($this, 'enqueue_assets'));
        add_action('template_redirect',     array($this, 'handle_auth_slug_page'), 9);
        add_action('template_redirect',     array($this, 'maybe_protect_page'), 11);
        add_action('wp_footer',             array($this, 'print_footer_overlays'));
        add_action('login_init',            array($this, 'maybe_block_wp_login'));
        add_filter('login_url',             array($this, 'filter_login_url'), 10, 3);
        add_filter('retrieve_password_message', array($this, 'filter_reset_password_message'), 10, 4);

        // AJAX — usuarios no logueados y logueados
        add_action('wp_ajax_nopriv_authgate_login',    array($this, 'ajax_login'));
        add_action('wp_ajax_authgate_login',           array($this, 'ajax_login'));
        add_action('wp_ajax_nopriv_authgate_register', array($this, 'ajax_register'));
        add_action('wp_ajax_authgate_register',        array($this, 'ajax_register'));
        add_action('wp_ajax_nopriv_authgate_lost_password',    array($this, 'ajax_lost_password'));
        add_action('wp_ajax_authgate_lost_password',            array($this, 'ajax_lost_password'));
        add_action('wp_ajax_nopriv_authgate_reset_password',   array($this, 'ajax_reset_password'));
        add_action('wp_ajax_authgate_reset_password',           array($this, 'ajax_reset_password'));

        // Hooks de log nativos de WP
        add_action('wp_login',        array($this, 'log_login_ok'), 10, 2);
        add_action('wp_login_failed', array($this, 'log_login_fail'));
    }

    // -------------------------------------------------------------------------
    // Popup overlays en footer
    // -------------------------------------------------------------------------

    /** @return void */
    public function print_footer_overlays()
    {
        foreach (self::$footer_overlays as $html) {
            echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
        }
    }

    // -------------------------------------------------------------------------
    // Assets
    // -------------------------------------------------------------------------

    /** @return void */
    public function enqueue_assets()
    {
        if (is_admin()) return;

        wp_enqueue_script('password-strength-meter');
        wp_enqueue_style('dashicons');
        wp_enqueue_style(
            'authgate-forms',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/auth-forms.css',
            array('dashicons'),
            '1.0.0'
        );
        if (AuthGate_Settings::custom_css_enabled()) {
            $custom_css = AuthGate_Settings::get_custom_css();
            if ($custom_css) {
                wp_add_inline_style('authgate-forms', $custom_css);
            }
        }
        wp_enqueue_script(
            'authgate-forms',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/auth-forms.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_localize_script('authgate-forms', 'authGate', array(
            'ajax_url'              => admin_url('admin-ajax.php'),
            'login_nonce'           => wp_create_nonce('authgate_login'),
            'register_nonce'        => wp_create_nonce('authgate_register'),
            'lost_password_nonce'   => wp_create_nonce('authgate_lost_password'),
            'reset_password_nonce'  => wp_create_nonce('authgate_reset_password'),
            'redirect_default'      => apply_filters('authgate_default_redirect', home_url('/')),
            'str_success_login'     => AuthGate_Settings::get_string('success_login'),
            'str_success_reg'       => AuthGate_Settings::get_string('success_register'),
            'str_error_generic'     => AuthGate_Settings::get_string('error_generic'),
            'str_pass_mismatch'     => AuthGate_Settings::get_string('error_pass_mismatch'),
            'str_weak_password'     => AuthGate_Settings::get_string('error_weak_password'),
            'str_generate_pass'     => AuthGate_Settings::get_string('btn_generate_pass'),
            'strength_labels'       => array(
                __('Muy débil', 'authgate'),
                __('Débil', 'authgate'),
                __('Media', 'authgate'),
                __('Fuerte', 'authgate'),
                __('Muy fuerte', 'authgate'),
            ),
        ));
    }

    // -------------------------------------------------------------------------
    // Página mínima (sin header/footer del tema)
    // -------------------------------------------------------------------------

    /**
     * Renderiza una página mínima con solo el logo del sitio y el contenido de auth.
     * Llama a wp_head() / wp_footer() para cargar scripts y estilos normalmente.
     *
     * @param string $content HTML del formulario
     * @return void  — hace exit al terminar
     */
    private function render_auth_minimal_page($content)
    {
        ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php bloginfo('name'); ?></title>
<?php wp_head(); ?>
</head>
<body class="authgate-page">

<div class="authgate-page__logo">
    <?php
    $logo_url = AuthGate_Settings::get('login_logo_url', '');
    if ($logo_url) :
        ?>
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
    <?php elseif (has_custom_logo()) :
        echo get_custom_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core WP function, returns safe HTML
    else :
        echo '<span class="authgate-page__site-name">' . esc_html(get_bloginfo('name')) . '</span>';
    endif;
    ?>
</div>

<div class="authgate-standalone-page">
    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
        <?php
        exit;
    }

    // -------------------------------------------------------------------------
    // Rewrite rules — URLs personalizadas
    // -------------------------------------------------------------------------

    /** @return void */
    public function register_rewrite_rules()
    {
        $login_slug = sanitize_title(AuthGate_Settings::get('login_slug', 'acceder'));
        $reset_slug = sanitize_title(AuthGate_Settings::get('reset_slug', 'restablecer-contrasena'));

        if ($login_slug) {
            add_rewrite_rule('^' . preg_quote($login_slug, '/') . '/?$', 'index.php?authgate_page=login', 'top');
        }
        if ($reset_slug) {
            add_rewrite_rule('^' . preg_quote($reset_slug, '/') . '/?$', 'index.php?authgate_page=reset', 'top');
        }

        // Flag puesta por maybe_upgrade() o activate() — flush diferido hasta init
        if (get_option('authgate_needs_rewrite_flush')) {
            delete_option('authgate_needs_rewrite_flush');
            flush_rewrite_rules(false);
        }
    }

    /**
     * @param string[] $vars
     * @return string[]
     */
    public function register_query_vars($vars)
    {
        $vars[] = 'authgate_page';
        return $vars;
    }

    /** @return void */
    public function handle_auth_slug_page()
    {
        $page = get_query_var('authgate_page');
        if (!$page) return;

        status_header(200);

        if ($page === 'login') {
            $slug_redirect = esc_url_raw(AuthGate_Settings::get('login_slug_redirect', '')) ?: home_url('/');

            if (is_user_logged_in()) {
                wp_safe_redirect($slug_redirect);
                exit;
            }
            // Si viene de página protegida respeta el redirect_to; si no, usa el redirect de la página de login.
            $redirect = esc_url_raw(wp_unslash($_GET['redirect_to'] ?? '')) ?: $slug_redirect;
            $content  = do_shortcode('[authgate_auth mode="inline" redirect="' . esc_attr($redirect) . '"]');
            $this->render_auth_minimal_page($content);
        }

        if ($page === 'reset') {
            $this->render_auth_minimal_page($this->render_reset_form());
        }
    }

    // -------------------------------------------------------------------------
    // Bloqueo wp-login.php
    // -------------------------------------------------------------------------

    /** @return void */
    public function maybe_redirect_wp_admin()
    {
        if (is_user_logged_in()) return;
        if (!(bool) AuthGate_Settings::get('block_wp_login', false)) return;

        $request_path = parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $request_path = '/' . ltrim((string) $request_path, '/');

        if (!in_array($request_path, array('/wp-admin', '/wp-admin/'), true)) return;

        $login_slug = sanitize_title(AuthGate_Settings::get('login_slug', ''));
        if (!$login_slug) return;

        wp_safe_redirect(home_url('/' . $login_slug . '/'), 302);
        exit;
    }

    /** @return void */
    public function maybe_block_wp_login()
    {
        if (!(bool) AuthGate_Settings::get('block_wp_login', false)) return;

        $login_slug = sanitize_title(AuthGate_Settings::get('login_slug', ''));
        if (!$login_slug) return;

        $action = sanitize_key(wp_unslash($_GET['action'] ?? ''));

        // WP necesita estas acciones en wp-login.php
        if (in_array($action, array('logout', 'postpass'), true)) return;

        // Acción de reset password: redirigir a nuestra página
        if (in_array($action, array('rp', 'resetpass', 'lostpassword'), true)) {
            $this->do_redirect_to_reset_page();
            return;
        }

        // En multisite solo redirigimos el reset; el resto permanece accesible
        if (is_multisite()) return;

        $url = home_url('/' . $login_slug . '/');
        $redirect_to = wp_validate_redirect(
            esc_url_raw(wp_unslash($_GET['redirect_to'] ?? '')),
            ''
        );
        if ($redirect_to) {
            $url = add_query_arg('redirect_to', rawurlencode($redirect_to), $url);
        }

        wp_safe_redirect($url, 302);
        exit;
    }

    /** @return void */
    private function do_redirect_to_reset_page()
    {
        $reset_slug = sanitize_title(AuthGate_Settings::get('reset_slug', ''));
        if (!$reset_slug) return;

        $key   = sanitize_text_field(wp_unslash($_GET['key'] ?? ''));
        $login = sanitize_text_field(wp_unslash($_GET['login'] ?? ''));

        $url = home_url('/' . $reset_slug . '/');
        if ($key && $login) {
            $url = add_query_arg(array('key' => $key, 'login' => $login), $url);
        }

        wp_safe_redirect($url, 302);
        exit;
    }

    // -------------------------------------------------------------------------
    // Filtros de URLs de autenticación
    // -------------------------------------------------------------------------

    /**
     * @param string $login_url
     * @param string $redirect
     * @return string
     */
    public function filter_login_url($login_url, $redirect)
    {
        $slug = sanitize_title(AuthGate_Settings::get('login_slug', ''));
        if (!$slug) return $login_url;

        $url = home_url('/' . $slug . '/');
        if ($redirect) {
            $url = add_query_arg('redirect_to', rawurlencode($redirect), $url);
        }
        return $url;
    }

    /**
     * Cambia la URL de reset en el email enviado por retrieve_password().
     *
     * @param string $message
     * @param string $key
     * @param string $user_login
     * @return string
     */
    public function filter_reset_password_message($message, $key, $user_login)
    {
        $reset_slug = sanitize_title(AuthGate_Settings::get('reset_slug', ''));
        if (!$reset_slug) return $message;

        $old_url = network_site_url('wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode($user_login), 'login');
        $new_url = add_query_arg(
            array('key' => $key, 'login' => rawurlencode($user_login)),
            home_url('/' . $reset_slug . '/')
        );

        return str_replace($old_url, $new_url, $message);
    }

    // -------------------------------------------------------------------------
    // Shortcodes
    // -------------------------------------------------------------------------

    /**
     * @param array $atts
     * @return string
     */
    public function shortcode_login($atts)
    {
        if (is_user_logged_in()) return '';
        $atts = shortcode_atts(array('mode' => 'inline', 'redirect' => '', 'button_class' => '', 'label' => ''), $atts, 'authgate_login');
        return $this->render_wrapper('login', $atts);
    }

    /**
     * @param array $atts
     * @return string
     */
    public function shortcode_register($atts)
    {
        if (is_user_logged_in()) return '';
        if (!$this->registration_allowed()) return '';
        $atts = shortcode_atts(array('mode' => 'inline', 'redirect' => '', 'button_class' => '', 'label' => ''), $atts, 'authgate_register');
        return $this->render_wrapper('register', $atts);
    }

    /**
     * @param array $atts
     * @return string
     */
    public function shortcode_auth($atts)
    {
        if (is_user_logged_in()) return '';
        $atts = shortcode_atts(array('mode' => 'inline', 'redirect' => '', 'default_tab' => 'login', 'button_class' => '', 'label' => ''), $atts, 'authgate_auth');
        if (!$this->registration_allowed()) {
            $atts['default_tab'] = 'login';
        }
        return $this->render_wrapper('combined', $atts);
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    /**
     * @param string $type   'login' | 'register' | 'combined'
     * @param array  $atts
     * @return string
     */
    private function render_wrapper($type, $atts)
    {
        $mode     = in_array($atts['mode'], array('inline', 'popup'), true) ? $atts['mode'] : 'inline';
        $redirect = $atts['redirect'] ? esc_url_raw($atts['redirect']) : '';

        ob_start();

        if ($mode === 'popup') {
            $uid      = uniqid('authgate-popup-');
            $btn_text = !empty($atts['label']) ? sanitize_text_field($atts['label']) : AuthGate_Settings::get_string('btn_popup');

            // Solo el trigger va inline (puede estar dentro de un <form>)
            $extra_class = !empty($atts['button_class']) ? ' ' . implode(' ', array_map('sanitize_html_class', preg_split('/\s+/', trim($atts['button_class'])))) : '';
            echo '<button type="button" class="authgate-popup-trigger button' . esc_attr($extra_class) . '" data-target="' . esc_attr($uid) . '">';
            echo esc_html($btn_text);
            echo '</button>';

            // El overlay se encola para wp_footer — nunca dentro de un <form>
            ob_start();
            echo '<div class="authgate-overlay" id="' . esc_attr($uid) . '" aria-modal="true" role="dialog">';
            echo '<div class="authgate-modal">';
            echo '<button type="button" class="authgate-close" aria-label="' . esc_attr__('Cerrar', 'authgate') . '">&times;</button>';
            $this->render_form($type, $redirect, $atts['default_tab'] ?? 'login', false);
            echo '</div></div>';
            self::$footer_overlays[] = ob_get_clean();
        } else {
            $this->render_form($type, $redirect, $atts['default_tab'] ?? 'login', true);
        }

        return ob_get_clean();
    }

    /**
     * @param string $type
     * @param string $redirect
     * @param string $default_tab
     * @return void
     */
    private function render_form($type, $redirect, $default_tab = 'login', $show_home_link = false)
    {
        $tpl_dir = __DIR__ . '/templates/';
        $registration_allowed = $this->registration_allowed();
        $inline_intro_html = $show_home_link ? AuthGate_Settings::get_inline_intro_html() : '';

        if (!$registration_allowed && $default_tab === 'register') {
            $default_tab = 'login';
        }

        if ($type === 'combined') {
            include $tpl_dir . 'form-combined.php';
        } elseif ($type === 'register') {
            if (!$registration_allowed) return;
            include $tpl_dir . 'form-register.php';
        } else {
            include $tpl_dir . 'form-login.php';
        }
    }

    /** @return bool */
    public static function is_mailmint_available()
    {
        return class_exists('Mint\\MRM\\DataBase\\Models\\ContactGroupModel');
    }

    // -------------------------------------------------------------------------
    // Protección de páginas
    // -------------------------------------------------------------------------

    /** @return void */
    public function maybe_protect_page()
    {
        if (is_admin() || is_user_logged_in()) return;

        $excluded_pages = (array) AuthGate_Settings::get('excluded_pages', array());
        $post_id        = get_queried_object_id();

        if ($post_id && in_array($post_id, $excluded_pages, true)) return;

        if (!$this->page_requires_login()) return;

        $redirect = esc_url_raw(home_url(add_query_arg(array())));
        include __DIR__ . '/templates/page-protected.php';
        exit;
    }

    /**
     * Detecta si la página actual requiere estar logueado.
     *
     * @return bool
     */
    private function page_requires_login()
    {
        // Post/página con visibilidad privada en WP
        $post = get_queried_object();
        if ($post instanceof WP_Post && $post->post_status === 'private') {
            return true;
        }

        // Las integraciones pueden añadir más condiciones (ej. WC myaccount)
        return (bool) apply_filters('authgate_is_protected_page', false);
    }

    // -------------------------------------------------------------------------
    // AJAX Login
    // -------------------------------------------------------------------------

    /** @return void */
    public function ajax_login()
    {
        check_ajax_referer('authgate_login', 'nonce');

        // Antibot: honeypot
        if (!empty($_POST['authgate_hp'])) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_spam')));
        }

        // Antibot: tiempo mínimo de envío (3 segundos)
        $form_time = (int) ($_POST['authgate_time'] ?? 0);
        if ($form_time && (time() - $form_time) < 3) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_spam')));
        }

        $ip = $this->get_client_ip();

        // Blacklist
        $blacklist = (array) AuthGate_Settings::get('blacklist', array());
        if (in_array($ip, $blacklist, true)) {
            $this->write_log(0, '', 'blocked', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_blocked')));
        }

        // Rate limit
        if ($this->is_rate_limited($ip)) {
            $this->write_log(0, '', 'blocked', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_blocked')));
        }

        $username = sanitize_user(wp_unslash($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        if (!$username || !$password) {
            $this->increment_rate_limit($ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_invalid')));
        }

        $user = wp_signon(array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        ), is_ssl());

        if (is_wp_error($user)) {
            $this->increment_rate_limit($ip);
            $this->write_log(0, $username, 'login_fail', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_invalid')));
        }

        // Reset rate limit tras login exitoso
        $this->reset_rate_limit($ip);

        $redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to'] ?? ''));
        $redirect_to = wp_validate_redirect($redirect_to, apply_filters('authgate_default_redirect', home_url('/')));

        wp_send_json_success(array(
            'message'     => AuthGate_Settings::get_string('success_login'),
            'redirect_to' => $redirect_to,
        ));
    }

    // -------------------------------------------------------------------------
    // AJAX Register
    // -------------------------------------------------------------------------

    /** @return void */
    public function ajax_register()
    {
        check_ajax_referer('authgate_register', 'nonce');

        if (!$this->registration_allowed()) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_generic')));
        }

        // Antibot: honeypot
        if (!empty($_POST['authgate_hp'])) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_spam')));
        }

        // Antibot: tiempo mínimo
        $form_time = (int) ($_POST['authgate_time'] ?? 0);
        if ($form_time && (time() - $form_time) < 3) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_spam')));
        }

        $ip = $this->get_client_ip();

        // Blacklist
        $blacklist = (array) AuthGate_Settings::get('blacklist', array());
        if (in_array($ip, $blacklist, true)) {
            $this->write_log(0, '', 'blocked', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_blocked')));
        }

        // Rate limit
        if ($this->is_rate_limited($ip)) {
            $this->write_log(0, '', 'blocked', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_blocked')));
        }

        $email      = sanitize_email(wp_unslash($_POST['email'] ?? ''));
        $password   = $_POST['password'] ?? '';
        $password2  = $_POST['password2'] ?? '';
        $first_name = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
        $last_name  = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));

        if (empty($_POST['authgate_gdpr'])) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_gdpr')));
        }

        if ($password && $password2 && $password !== $password2) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_pass_mismatch')));
        }

        if (!is_email($email)) {
            $this->increment_rate_limit($ip);
            wp_send_json_error(array('message' => __('Introduce un email válido.', 'authgate')));
        }

        // Crear usuario — las integraciones pueden sobreescribir via filtro (ej. WooCommerce)
        $user_id = apply_filters('authgate_create_user', null, $email, $password);
        if (null === $user_id) {
            $username = sanitize_user(current(explode('@', $email)));
            $username = wp_unique_prefixed_id($username);
            $user_id  = wp_create_user($username, $password ?: wp_generate_password(), $email);
        }

        if (is_wp_error($user_id)) {
            $this->increment_rate_limit($ip);
            $this->write_log(0, $email, 'register_fail', $ip);
            wp_send_json_error(array('message' => $user_id->get_error_message()));
        }

        if ($first_name || $last_name) {
            wp_update_user(array(
                'ID'         => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name,
            ));
        }

        $this->write_log($user_id, $email, 'register_ok', $ip);

        $opt_in = empty($_POST['authgate_newsletter']);
        do_action('authgate_user_registered', $user_id, $email, $first_name, $last_name, $opt_in);

        // Auto-login tras registro
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, false, is_ssl());

        $redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to'] ?? ''));
        $redirect_to = wp_validate_redirect($redirect_to, apply_filters('authgate_default_redirect', home_url('/')));

        wp_send_json_success(array(
            'message'     => AuthGate_Settings::get_string('success_register'),
            'redirect_to' => $redirect_to,
        ));
    }

    // -------------------------------------------------------------------------
    // Rate limiting
    // -------------------------------------------------------------------------

    /**
     * @param string $ip
     * @return bool
     */
    private function is_rate_limited($ip)
    {
        $data = get_transient('authgate_rl_' . md5($ip));
        if (!$data) return false;
        $max = (int) AuthGate_Settings::get('max_attempts', 10);
        return (int) $data['count'] >= $max;
    }

    /**
     * @param string $ip
     * @return void
     */
    private function increment_rate_limit($ip)
    {
        $key  = 'authgate_rl_' . md5($ip);
        $data = get_transient($key);

        if (!$data) {
            $data = array('ip' => $ip, 'count' => 0);
        }

        $data['count']++;
        set_transient($key, $data, HOUR_IN_SECONDS);
    }

    /**
     * @param string $ip
     * @return void
     */
    private function reset_rate_limit($ip)
    {
        delete_transient('authgate_rl_' . md5($ip));
    }

    // -------------------------------------------------------------------------
    // Log
    // -------------------------------------------------------------------------

    /**
     * @param int    $user_id
     * @param string $username
     * @param string $event
     * @param string $ip
     * @return void
     */
    private function write_log($user_id, $username, $event, $ip)
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $wpdb->insert(
            $wpdb->prefix . 'authgate_log',
            array(
                'user_id'    => (int) $user_id,
                'username'   => substr($username, 0, 100),
                'event'      => substr($event, 0, 30),
                'ip'         => substr($ip, 0, 45),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                'created_at' => current_time('mysql'),
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * Hook wp_login — registra login exitoso.
     *
     * @param string  $user_login
     * @param WP_User $user
     * @return void
     */
    public function log_login_ok($user_login, $user)
    {
        $this->write_log($user->ID, $user_login, 'login_ok', $this->get_client_ip());
    }

    /**
     * Hook wp_login_failed — registra intento fallido.
     *
     * @param string $username
     * @return void
     */
    public function log_login_fail($username)
    {
        $this->write_log(0, $username, 'login_fail', $this->get_client_ip());
    }

    /** @return string */
    public function shortcode_reset_password()
    {
        return $this->render_reset_form();
    }

    /**
     * Renderiza el formulario de reset leyendo key/login de $_GET.
     *
     * @return string
     */
    private function render_reset_form()
    {
        $key   = sanitize_text_field(wp_unslash($_GET['key'] ?? ''));
        $login = sanitize_user(wp_unslash($_GET['login'] ?? ''));

        ob_start();
        include __DIR__ . '/templates/form-reset-password.php';
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------
    // AJAX Reset Password
    // -------------------------------------------------------------------------

    /** @return void */
    public function ajax_reset_password()
    {
        check_ajax_referer('authgate_reset_password', 'nonce');

        $key   = sanitize_text_field(wp_unslash($_POST['reset_key'] ?? ''));
        $login = sanitize_user(wp_unslash($_POST['reset_login'] ?? ''));
        $pass  = wp_unslash($_POST['new_password'] ?? '');
        $pass2 = wp_unslash($_POST['new_password2'] ?? '');

        if (!$key || !$login || !$pass) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_generic')));
        }

        if ($pass !== $pass2) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_pass_mismatch')));
        }

        $user = check_password_reset_key($key, $login);
        if (is_wp_error($user)) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_reset_key')));
        }

        reset_password($user, $pass);

        $slug        = sanitize_title(AuthGate_Settings::get('login_slug', ''));
        $redirect_to = $slug ? home_url('/' . $slug . '/') : wp_login_url();

        wp_send_json_success(array(
            'message'     => AuthGate_Settings::get_string('success_reset_password'),
            'redirect_to' => $redirect_to,
        ));
    }

    // -------------------------------------------------------------------------
    // AJAX Lost Password
    // -------------------------------------------------------------------------

    /** @return void */
    public function ajax_lost_password()
    {
        check_ajax_referer('authgate_lost_password', 'nonce');

        $ip = $this->get_client_ip();

        // Blacklist
        $blacklist = (array) AuthGate_Settings::get('blacklist', array());
        if (in_array($ip, $blacklist, true)) {
            $this->write_log(0, '', 'blocked', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_blocked')));
        }

        // Rate limit
        if ($this->is_rate_limited($ip)) {
            $this->write_log(0, '', 'blocked', $ip);
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_blocked')));
        }

        $email = sanitize_email(wp_unslash($_POST['lost_email'] ?? ''));

        if (!$email || !is_email($email)) {
            wp_send_json_error(array('message' => AuthGate_Settings::get_string('error_generic')));
        }

        // retrieve_password() returns true or WP_Error; always return success to avoid user enumeration
        retrieve_password($email);

        wp_send_json_success(array(
            'message' => AuthGate_Settings::get_string('success_lost_password'),
        ));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Devuelve la IP del cliente de forma segura.
     * Por defecto solo confía en REMOTE_ADDR.
     * Usa el filtro `authgate_trust_proxies` en `true` para permitir cabeceras de proxy (HTTP_CF_CONNECTING_IP, HTTP_X_FORWARDED_FOR, HTTP_X_REAL_IP).
     *
     * @return string
     */
    private function get_client_ip()
    {
        $trust_proxies = (bool) apply_filters('authgate_trust_proxies', false);

        if (!$trust_proxies) {
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
                if (filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                    return trim($ip);
                }
            }

            return '0.0.0.0';
        }

        $candidates = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );

        foreach ($candidates as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = sanitize_text_field(wp_unslash(current(explode(',', $_SERVER[$key]))));
                if (filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                    return trim($ip);
                }
            }
        }

        return '0.0.0.0';
    }

    /** @return bool */
    private function registration_allowed()
    {
        return (bool) apply_filters('authgate_registration_allowed', AuthGate_Settings::registration_enabled());
    }
}
