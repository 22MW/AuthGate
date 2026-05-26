(function ($) {
    'use strict';

    // -----------------------------------------------------------------------
    // Tabs en formulario combinado
    // -----------------------------------------------------------------------
    $(document).on('click', '.authgate__tab', function () {
        var $tab    = $(this);
        var $auth   = $tab.closest('.authgate--combined');
        var target  = $tab.data('tab');

        $auth.find('.authgate__tab').removeClass('is-active').attr('aria-selected', 'false');
        $tab.addClass('is-active').attr('aria-selected', 'true');

        $auth.find('.authgate__panel').removeClass('is-active');
        $auth.find('.authgate__panel[data-panel="' + target + '"]').addClass('is-active');
    });

    // Links de cambio de tab desde dentro del formulario
    $(document).on('click', '.authgate__switch-link', function (e) {
        e.preventDefault();
        var target = $(this).data('switch-to');
        $(this).closest('.authgate--combined').find('.authgate__tab[data-tab="' + target + '"]').trigger('click');
    });

    // -----------------------------------------------------------------------
    // Toggle mostrar/ocultar contraseña
    // -----------------------------------------------------------------------
    $(document).on('click', '.authgate__toggle-pass', function () {
        var $input = $(this).closest('.authgate__input-wrap').find('.authgate__input');
        var $icon  = $(this).find('.dashicons');
        var isPass = $input.attr('type') === 'password';
        $input.attr('type', isPass ? 'text' : 'password');
        $icon.toggleClass('dashicons-visibility', !isPass).toggleClass('dashicons-hidden', isPass);
    });

    // -----------------------------------------------------------------------
    // Popup
    // -----------------------------------------------------------------------
    $(document).on('click', '.authgate-popup-trigger', function () {
        var target = '#' + $(this).data('target');
        $(target).addClass('is-open');
        $('body').css('overflow', 'hidden');
    });

    function closePopup($overlay) {
        $overlay.removeClass('is-open');
        $('body').css('overflow', '');
    }

    $(document).on('click', '.authgate-overlay', function (e) {
        if ($(e.target).is('.authgate-overlay')) {
            closePopup($(this));
        }
    });

    $(document).on('click', '.authgate-close', function () {
        closePopup($(this).closest('.authgate-overlay'));
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closePopup($('.authgate-overlay.is-open'));
        }
    });

    // -----------------------------------------------------------------------
    // Fortaleza de contraseña
    // -----------------------------------------------------------------------
    function getPasswordScore(pass) {
        if (typeof wp === 'undefined' || !wp.passwordStrength) return -1;
        return wp.passwordStrength.meter(pass, [], '');
    }

    function updateStrengthUI($input) {
        var $field   = $input.closest('.authgate__field');
        var $wrap    = $field.find('.authgate__strength');
        var $bar     = $wrap.find('.authgate__strength-bar');
        var $label   = $wrap.find('.authgate__strength-label');
        var pass     = $input.val();

        if (!pass) {
            $wrap.hide();
            $bar.removeAttr('data-score');
            $label.text('');
            return;
        }

        var score = getPasswordScore(pass);
        if (score < 0) return;
        score = Math.max(0, Math.min(4, score));

        $wrap.show();
        $bar.attr('data-score', score);
        $label.text(authGate.strength_labels[score] || '');
    }

    $(document).on('input', '[name="password"], [name="new_password"]', function () {
        updateStrengthUI($(this));
    });

    // -----------------------------------------------------------------------
    // Generar contraseña segura
    // -----------------------------------------------------------------------
    function generateSecurePassword() {
        var chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*()_+-=?';
        var arr   = new Uint32Array(16);
        crypto.getRandomValues(arr);
        return Array.from(arr).map(function (n) { return chars[n % chars.length]; }).join('');
    }

    $(document).on('click', '.authgate__generate-pass', function () {
        var $form = $(this).closest('form');
        var pass  = generateSecurePassword();
        var $p1   = $form.find('[name="password"], [name="new_password"]');
        var $p2   = $form.find('[name="password2"], [name="new_password2"]');
        $p1.val(pass).attr('type', 'text');
        $p2.val(pass).attr('type', 'text');
        $p1.trigger('input');
    });

    // Si la URL tiene ?lost=1 (viene desde enlace expirado), abrir panel de recuperación
    if (window.location.search.indexOf('lost=1') !== -1) {
        var $trigger = $('.authgate__lost-trigger').first();
        if ($trigger.length) $trigger.trigger('click');
    }

    // -----------------------------------------------------------------------
    // Lost password toggle
    // -----------------------------------------------------------------------
    $(document).on('click', '.authgate__lost-trigger', function () {
        var $container = $(this).closest('.authgate--login, .authgate__panel[data-panel="login"]');
        $container.find('.authgate__form[data-action="authgate_login"]').hide();
        $container.find('.authgate__message').first().hide().removeClass('is-error is-success').text('');
        $container.find('.authgate__lost-panel').show();
    });

    $(document).on('click', '.authgate__back-login', function (e) {
        e.preventDefault();
        var $container = $(this).closest('.authgate--login, .authgate__panel[data-panel="login"]');
        $container.find('.authgate__lost-panel').hide();
        $container.find('.authgate__form[data-action="authgate_login"]').show();
    });

    // -----------------------------------------------------------------------
    // Envío AJAX de formularios
    // -----------------------------------------------------------------------
    $(document).on('submit', '.authgate__form', function (e) {
        e.preventDefault();

        var $form    = $(this);
        var $btn     = $form.find('.authgate__btn');
        var $msg     = $form.siblings('.authgate__message').first();
        var action   = $form.data('action');
        var nonceMap = {
            authgate_login:           authGate.login_nonce,
            authgate_register:        authGate.register_nonce,
            authgate_lost_password:   authGate.lost_password_nonce,
            authgate_reset_password:  authGate.reset_password_nonce,
        };
        var nonce = nonceMap[action] || authGate.login_nonce;

        // Validar contraseñas coinciden (registro y reset)
        if (action === 'authgate_register' || action === 'authgate_reset_password') {
            var passField = action === 'authgate_register' ? 'password' : 'new_password';
            var $pass  = $form.find('[name="' + passField + '"]');
            var $pass2 = $form.find('[name="' + passField + '2"]');
            if ($pass.length && $pass2.length && $pass.val() !== $pass2.val()) {
                $msg.addClass('is-error').text(authGate.str_pass_mismatch).show();
                return;
            }
            // Validar fortaleza mínima (score >= 3)
            if ($pass.length) {
                var score = getPasswordScore($pass.val());
                if (score >= 0 && score < 3) {
                    $msg.addClass('is-error').text(authGate.str_weak_password).show();
                    return;
                }
            }
        }

        var data = $form.serialize() + '&action=' + action + '&nonce=' + encodeURIComponent(nonce);

        // Limpiar mensaje previo
        $msg.removeClass('is-error is-success').text('').hide();
        $btn.addClass('is-loading');

        $.post(authGate.ajax_url, data, function (response) {
            $btn.removeClass('is-loading');

            if (response.success) {
                $msg.addClass('is-success').text(response.data.message).show();
                if (action === 'authgate_lost_password') {
                    $form.hide();
                } else {
                    var redirect = response.data.redirect_to || authGate.redirect_default;
                    setTimeout(function () {
                        window.location.href = redirect;
                    }, 800);
                }
            } else {
                $msg.addClass('is-error').text(response.data.message || authGate.str_error_generic).show();
            }
        }).fail(function () {
            $btn.removeClass('is-loading');
            $msg.addClass('is-error').text(authGate.str_error_generic).show();
        });
    });

})(jQuery);
