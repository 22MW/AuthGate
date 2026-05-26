<?php
defined('ABSPATH') || exit;
// $redirect is set by maybe_protect_page() before include
get_header();
?>
<main id="content" class="site-main authgate-protected-page">
    <div class="authgate-protected-page__inner card">
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
