<?php
defined('ABSPATH') || exit;

if (!AuthGate_Settings::registration_enabled()) {
    return;
}
?>
<div class="authgate authgate--register">
    <?php if (!empty($inline_intro_html)) : ?>
        <div class="authgate__intro"><?php echo wp_kses_post(wpautop($inline_intro_html)); ?></div>
    <?php endif; ?>

    <h2 class="authgate__title"><?php echo esc_html(AuthGate_Settings::get_string('register_title')); ?></h2>

    <div class="authgate__message" role="alert" aria-live="polite"></div>

    <form class="authgate__form" data-action="authgate_register" novalidate>
        <?php if (!empty($redirect)) : ?>
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>">
        <?php endif; ?>

        <!-- Honeypot -->
        <div class="authgate__hp" aria-hidden="true">
            <input type="text" name="authgate_hp" tabindex="-1" autocomplete="off">
        </div>

        <!-- Timestamp antibot -->
        <input type="hidden" name="authgate_time" value="<?php echo esc_attr(time()); ?>">

        <div class="authgate__row authgate__row--fields">
            <div class="authgate__field" style="flex:1;min-width:0">
                <input type="text"
                    id="authgate-reg-firstname"
                    name="first_name"
                    class="authgate__input"
                    placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_firstname')); ?>"
                    autocomplete="given-name"
                    required>
            </div>
            <div class="authgate__field" style="flex:1;min-width:0">
                <input type="text"
                    id="authgate-reg-lastname"
                    name="last_name"
                    class="authgate__input"
                    placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_lastname')); ?>"
                    autocomplete="family-name"
                    required>
            </div>
        </div>

        <div class="authgate__field">
            <input type="email"
                id="authgate-reg-email"
                name="email"
                class="authgate__input"
                placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_email_reg')); ?>"
                autocomplete="email"
                required>
        </div>

        <?php if (apply_filters('authgate_show_password_field', true)) : ?>
            <div class="authgate__field">
                <label for="authgate-reg-pass" class="authgate__label">
                    <?php echo esc_html(AuthGate_Settings::get_string('field_pass_reg')); ?>
                </label>
                <div class="authgate__input-wrap">
                    <input type="password"
                        id="authgate-reg-pass"
                        name="password"
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
                <label for="authgate-reg-pass2" class="authgate__label">
                    <?php echo esc_html(AuthGate_Settings::get_string('field_pass_reg_confirm')); ?>
                </label>
                <div class="authgate__input-wrap">
                    <input type="password"
                        id="authgate-reg-pass2"
                        name="password2"
                        class="authgate__input"
                        autocomplete="new-password"
                        required>
                    <button type="button" class="authgate__toggle-pass" aria-label="<?php esc_attr_e('Mostrar contraseña', 'authgate'); ?>">
                        <span class="dashicons dashicons-visibility"></span>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $gdpr_label = str_replace('{privacy_url}', esc_url(get_privacy_policy_url()), AuthGate_Settings::get_string('field_gdpr'));
        $allowed    = array('a' => array('href' => array(), 'target' => array(), 'rel' => array()), 'strong' => array(), 'em' => array());
        ?>
        <label class="authgate__consent">
            <input type="checkbox" name="authgate_gdpr" value="1" required>
            <span><?php echo wp_kses($gdpr_label, $allowed); ?></span>
        </label>

        <?php if (AuthGate_Forms::is_mailmint_available()) : ?>
            <label class="authgate__consent">
                <input type="checkbox" name="authgate_newsletter" value="1">
                <span><?php echo esc_html(AuthGate_Settings::get_string('field_newsletter')); ?></span>
            </label>
        <?php endif; ?>

        <button type="submit" class="authgate__btn btn btn-secondary alt">
            <?php echo esc_html(AuthGate_Settings::get_string('btn_register')); ?>
        </button>

        <?php
        $legal = AuthGate_Settings::get_string('legal_text');
        if ($legal) : ?>
            <div class="authgate__legal"><?php echo wp_kses_post(wpautop($legal)); ?></div>
        <?php endif; ?>

        <?php do_action('authgate_register_form_fields'); ?>
    </form>

    <?php if (!empty($show_home_link)) : ?>
        <p class="authgate__switch authgate__switch--home">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="authgate__link">
                <?php echo esc_html(AuthGate_Settings::get_string('link_to_home')); ?>
            </a>
        </p>
    <?php endif; ?>
</div>
