<?php
defined('ABSPATH') || exit;

// $key y $login vienen del método render_reset_form() que hace include de este archivo
$key       = isset($key) ? $key : sanitize_text_field(wp_unslash($_GET['key'] ?? ''));
$login     = isset($login) ? $login : sanitize_user(wp_unslash($_GET['login'] ?? ''));
$key_valid = false;

if ($key && $login) {
    $user_check = check_password_reset_key($key, $login);
    $key_valid  = !is_wp_error($user_check);
}
?>
<div class="authgate authgate--reset">
    <h2 class="authgate__title"><?php echo esc_html(AuthGate_Settings::get_string('reset_title')); ?></h2>

    <?php if (!$key_valid) : ?>
        <div class="authgate__message is-error" role="alert">
            <?php echo esc_html(AuthGate_Settings::get_string('error_reset_key')); ?>
        </div>
        <?php
        $login_slug = sanitize_title(\AuthGate_Settings::get('login_slug', 'acceder'));
        $login_url  = $login_slug ? home_url('/' . $login_slug . '/') : wp_login_url();
        ?>
        <a href="<?php echo esc_url(add_query_arg('lost', '1', $login_url)); ?>"
           class="authgate__btn btn btn-secondary alt"
           style="display:block;text-align:center;margin-top:1rem;text-decoration:none;">
            <?php echo esc_html(AuthGate_Settings::get_string('btn_lost_password')); ?>
        </a>
    <?php else : ?>
        <div class="authgate__message" role="alert" aria-live="polite"></div>

        <form class="authgate__form" data-action="authgate_reset_password" novalidate>
            <input type="hidden" name="reset_key" value="<?php echo esc_attr($key); ?>">
            <input type="hidden" name="reset_login" value="<?php echo esc_attr($login); ?>">

            <div class="authgate__field">
                <label for="authgate-reset-pass" class="authgate__label">
                    <?php echo esc_html(AuthGate_Settings::get_string('field_new_pass')); ?>
                </label>
                <div class="authgate__input-wrap">
                    <input type="password"
                        id="authgate-reset-pass"
                        name="new_password"
                        class="authgate__input"
                        autocomplete="new-password"
                        required>
                    <button type="button" class="authgate__toggle-pass" aria-label="<?php esc_attr_e('Mostrar contraseña', 'authgate'); ?>">
                        <span class="dashicons dashicons-visibility"></span>
                    </button>
                </div>
                <div class="authgate__strength" style="display:none;">
                    <div class="authgate__strength-bar" aria-hidden="true">
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <span class="authgate__strength-label"></span>
                </div>
                <button type="button" class="authgate__generate-pass">
                    <?php echo esc_html(AuthGate_Settings::get_string('btn_generate_pass')); ?>
                </button>
            </div>

            <div class="authgate__field">
                <label for="authgate-reset-pass2" class="authgate__label">
                    <?php echo esc_html(AuthGate_Settings::get_string('field_new_pass_confirm')); ?>
                </label>
                <div class="authgate__input-wrap">
                    <input type="password"
                        id="authgate-reset-pass2"
                        name="new_password2"
                        class="authgate__input"
                        autocomplete="new-password"
                        required>
                    <button type="button" class="authgate__toggle-pass" aria-label="<?php esc_attr_e('Mostrar contraseña', 'authgate'); ?>">
                        <span class="dashicons dashicons-visibility"></span>
                    </button>
                </div>
            </div>

            <button type="submit" class="authgate__btn btn btn-secondary alt">
                <?php echo esc_html(AuthGate_Settings::get_string('btn_reset_password')); ?>
            </button>
        </form>
    <?php endif; ?>
</div>
