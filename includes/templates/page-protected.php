<?php
defined('ABSPATH') || exit;
// $redirect is set by maybe_protect_page() before include
get_header();
?>
<main id="content" class="site-main authgate-protected-page">
    <div class="authgate-protected-page__inner card">
        <div class="authgate-protected-page__logo">
            <?php
            $logo_url = AuthGate_Settings::get('login_logo_url', '');
            if ($logo_url) :
                ?>
                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <?php elseif (has_custom_logo()) :
                echo get_custom_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            else : ?>
                <span class="authgate-protected-page__site-name"><?php echo esc_html(get_bloginfo('name')); ?></span>
            <?php endif; ?>
        </div>

        <h1 class="entry-title authgate-protected-page__title">
            <?php echo esc_html(AuthGate_Settings::get_string('protected_title')); ?>
        </h1>
        <p class="authgate-protected-page__desc">
            <?php echo esc_html(AuthGate_Settings::get_string('protected_desc')); ?>
        </p>

        <?php
        $default_tab = 'login';
        include __DIR__ . '/form-combined.php';
        ?>
    </div>
</main>
<?php
get_footer();
