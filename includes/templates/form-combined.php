<?php
defined('ABSPATH') || exit;
$default_tab = $default_tab ?? 'login';
$registration_allowed = isset($registration_allowed) ? (bool) $registration_allowed : AuthGate_Settings::registration_enabled();

if (!$registration_allowed) {
    $default_tab = 'login';
}
?>
<div class="authgate authgate--combined">
    <?php if (!empty($inline_intro_html)) : ?>
        <div class="authgate__intro"><?php echo wp_kses_post(wpautop($inline_intro_html)); ?></div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="authgate__tabs" role="tablist">
        <button type="button"
            class="authgate__tab <?php echo $default_tab === 'login' ? 'is-active' : ''; ?>"
            role="tab"
            aria-selected="<?php echo $default_tab === 'login' ? 'true' : 'false'; ?>"
            data-tab="login">
            <?php echo esc_html(AuthGate_Settings::get_string('tab_login')); ?>
        </button>
        <?php if ($registration_allowed) : ?>
            <button type="button"
                class="authgate__tab <?php echo $default_tab === 'register' ? 'is-active' : ''; ?>"
                role="tab"
                aria-selected="<?php echo $default_tab === 'register' ? 'true' : 'false'; ?>"
                data-tab="register">
                <?php echo esc_html(AuthGate_Settings::get_string('tab_register')); ?>
            </button>
        <?php endif; ?>
    </div>

    <!-- Panel Login -->
    <div class="authgate__panel <?php echo $default_tab === 'login' ? 'is-active' : ''; ?>" data-panel="login" role="tabpanel">
        <div class="authgate__message" role="alert" aria-live="polite"></div>

        <form class="authgate__form" data-action="authgate_login" novalidate>
            <?php if (!empty($redirect)) : ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>">
            <?php endif; ?>

            <div class="authgate__hp" aria-hidden="true">
                <input type="text" name="authgate_hp" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="authgate_time" value="<?php echo esc_attr(time()); ?>">

            <div class="authgate__field">
                <input type="text" id="authgate-c-login-user" name="username" class="authgate__input"
                    placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_user')); ?>"
                    autocomplete="username" required>
            </div>

            <div class="authgate__field">
                <div class="authgate__input-wrap">
                    <input type="password" id="authgate-c-login-pass" name="password" class="authgate__input"
                        placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_pass')); ?>"
                        autocomplete="current-password" required>
                    <button type="button" class="authgate__toggle-pass" aria-label="<?php esc_attr_e('Mostrar contraseña', 'authgate'); ?>">
                        <span class="dashicons dashicons-visibility"></span>
                    </button>
                </div>
            </div>

            <div class="authgate__row authgate__row--between">
                <label class="authgate__check-label">
                    <input type="checkbox" name="remember" value="1">
                    <?php echo esc_html(AuthGate_Settings::get_string('field_remember')); ?>
                </label>
                <button type="button" class="authgate__lost-trigger authgate__link btn alt">
                    <?php echo esc_html(AuthGate_Settings::get_string('forgot_password')); ?>
                </button>
            </div>

            <button type="submit" class="authgate__btn btn btn-secondary alt">
                <?php echo esc_html(AuthGate_Settings::get_string('btn_login')); ?>
            </button>

            <?php if ($registration_allowed) : ?>
                <p class="authgate__switch">
                    <a href="#" class="authgate__switch-link" data-switch-to="register">
                        <?php echo esc_html(AuthGate_Settings::get_string('link_to_register')); ?>
                    </a>
                </p>
            <?php endif; ?>

            <?php if (!empty($show_home_link)) : ?>
                <p class="authgate__switch authgate__switch--home">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="authgate__link">
                        <?php esc_html_e('Ir a la página de inicio', 'authgate'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </form>

        <div class="authgate__lost-panel" style="display:none;">
            <div class="authgate__message" role="alert" aria-live="polite"></div>
            <form class="authgate__form" data-action="authgate_lost_password" novalidate>
                <div class="authgate__field">
                    <input type="email"
                        name="lost_email"
                        class="authgate__input"
                        placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_lost_email')); ?>"
                        autocomplete="email"
                        required>
                </div>
                <button type="submit" class="authgate__btn btn btn-secondary alt">
                    <?php echo esc_html(AuthGate_Settings::get_string('btn_lost_password')); ?>
                </button>
            </form>
            <p class="authgate__switch">
                <a href="#" class="authgate__back-login">
                    <?php echo esc_html(AuthGate_Settings::get_string('back_to_login')); ?>
                </a>
            </p>
        </div>
    </div>

    <?php if ($registration_allowed) : ?>
    <!-- Panel Registro -->
    <div class="authgate__panel <?php echo $default_tab === 'register' ? 'is-active' : ''; ?>" data-panel="register" role="tabpanel">
        <div class="authgate__message" role="alert" aria-live="polite"></div>

        <form class="authgate__form" data-action="authgate_register" novalidate>
            <?php if (!empty($redirect)) : ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>">
            <?php endif; ?>

            <div class="authgate__hp" aria-hidden="true">
                <input type="text" name="authgate_hp" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="authgate_time" value="<?php echo esc_attr(time()); ?>">

            <div class="authgate__row authgate__row--fields">
                <div class="authgate__field" style="flex:1;min-width:0">
                    <input type="text" id="authgate-c-reg-firstname" name="first_name" class="authgate__input"
                        placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_firstname')); ?>"
                        autocomplete="given-name" required>
                </div>
                <div class="authgate__field" style="flex:1;min-width:0">
                    <input type="text" id="authgate-c-reg-lastname" name="last_name" class="authgate__input"
                        placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_lastname')); ?>"
                        autocomplete="family-name" required>
                </div>
            </div>

            <div class="authgate__field">
                <input type="email" id="authgate-c-reg-email" name="email" class="authgate__input"
                    placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_email_reg')); ?>"
                    autocomplete="email" required>
            </div>

            <?php if (apply_filters('authgate_show_password_field', true)) : ?>
                <div class="authgate__field">
                    <label for="authgate-c-reg-pass" class="authgate__label">
                        <?php echo esc_html(AuthGate_Settings::get_string('field_pass_reg')); ?>
                    </label>
                    <div class="authgate__input-wrap">
                        <input type="password" id="authgate-c-reg-pass" name="password" class="authgate__input" autocomplete="new-password" required>
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
                    <label for="authgate-c-reg-pass2" class="authgate__label">
                        <?php echo esc_html(AuthGate_Settings::get_string('field_pass_reg_confirm')); ?>
                    </label>
                    <div class="authgate__input-wrap">
                        <input type="password" id="authgate-c-reg-pass2" name="password2" class="authgate__input" autocomplete="new-password" required>
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

            <p class="authgate__switch">
                <a href="#" class="authgate__switch-link" data-switch-to="login">
                    <?php echo esc_html(AuthGate_Settings::get_string('link_to_login')); ?>
                </a>
            </p>

            <?php do_action('authgate_register_form_fields'); ?>

            <?php if (!empty($show_home_link)) : ?>
                <p class="authgate__switch authgate__switch--home">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="authgate__link">
                        <?php esc_html_e('Ir a la página de inicio', 'authgate'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

</div>
