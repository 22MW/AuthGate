<?php defined('ABSPATH') || exit; ?>
<div class="authgate authgate--login">
    <h2 class="authgate__title"><?php echo esc_html(AuthGate_Settings::get_string('login_title')); ?></h2>

    <div class="authgate__message" role="alert" aria-live="polite"></div>

    <form class="authgate__form" data-action="authgate_login" novalidate>
        <?php if (!empty($redirect)) : ?>
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect); ?>">
        <?php endif; ?>

        <!-- Honeypot -->
        <div class="authgate__hp" aria-hidden="true">
            <input type="text" name="authgate_hp" tabindex="-1" autocomplete="off">
        </div>

        <!-- Timestamp antibot -->
        <input type="hidden" name="authgate_time" value="<?php echo esc_attr(time()); ?>">

        <div class="authgate__field">
            <input type="text"
                id="authgate-login-user"
                name="username"
                class="authgate__input"
                placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_user')); ?>"
                autocomplete="username"
                required>
        </div>

        <div class="authgate__field">
            <div class="authgate__input-wrap">
                <input type="password"
                    id="authgate-login-pass"
                    name="password"
                    class="authgate__input"
                    placeholder="<?php echo esc_attr(AuthGate_Settings::get_string('field_pass')); ?>"
                    autocomplete="current-password"
                    required>
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
            <button type="button" class="authgate__lost-trigger authgate__link">
                <?php echo esc_html(AuthGate_Settings::get_string('forgot_password')); ?>
            </button>
        </div>

        <button type="submit" class="authgate__btn btn btn-secondary alt">
            <?php echo esc_html(AuthGate_Settings::get_string('btn_login')); ?>
        </button>
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

    <?php if (!empty($show_home_link)) : ?>
        <p class="authgate__switch authgate__switch--home">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="authgate__link">
                <?php esc_html_e('Ir a la página de inicio', 'authgate'); ?>
            </a>
        </p>
    <?php endif; ?>
</div>
