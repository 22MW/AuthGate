<?php
defined('ABSPATH') || exit;

class AuthGate_Install {

    const DB_VERSION        = '1.2';
    const DB_VERSION_OPTION = 'authgate_db_version';

    /** @return void */
    public static function activate() {
        self::create_log_table();
        self::flush_custom_rewrite_rules();
        update_option(self::DB_VERSION_OPTION, self::DB_VERSION);
    }

    /** Ejecutar en cada carga para aplicar upgrades de BD si los hubiera. */
    public static function maybe_upgrade() {
        $current = get_option(self::DB_VERSION_OPTION);

        if ($current !== self::DB_VERSION) {
            self::create_log_table();

            // 1.1: resetear strings que cambiaron de valor por defecto
            if (version_compare($current, '1.1', '<')) {
                delete_option('authgate_str_field_newsletter');
            }

            // 1.2: registrar rewrite rules de URLs personalizadas
            if (version_compare($current, '1.2', '<')) {
                self::flush_custom_rewrite_rules();
            }

            update_option(self::DB_VERSION_OPTION, self::DB_VERSION);
        }
    }

    /**
     * Marca que las rewrite rules deben regenerarse en el próximo init.
     * No llama a add_rewrite_rule() directamente porque plugins_loaded y
     * register_activation_hook se ejecutan antes de que $wp_rewrite esté listo.
     *
     * @return void
     */
    public static function flush_custom_rewrite_rules() {
        update_option('authgate_needs_rewrite_flush', '1');
    }

    /** @return void */
    private static function create_log_table() {
        global $wpdb;

        $table   = $wpdb->prefix . 'authgate_log';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id     BIGINT UNSIGNED NOT NULL DEFAULT 0,
            username    VARCHAR(100)    NOT NULL DEFAULT '',
            event       VARCHAR(30)     NOT NULL DEFAULT '',
            ip          VARCHAR(45)     NOT NULL DEFAULT '',
            user_agent  VARCHAR(255)    NOT NULL DEFAULT '',
            created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event   (event),
            KEY idx_ip      (ip),
            KEY idx_user_id (user_id),
            KEY idx_created (created_at)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
