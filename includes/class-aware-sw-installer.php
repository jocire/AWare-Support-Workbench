<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AWare_SW_Installer {
    private const OPTION_BOOTSTRAP_VERSION = 'aware_sw_bootstrap_version';
    private const BOOTSTRAP_FILE = 'aware-support-workbench-bootstrap.php';

    public static function activate(): void {
        self::create_tables();

        if ( self::install_bootstrap() ) {
            update_option( self::OPTION_BOOTSTRAP_VERSION, AWARE_SW_BOOTSTRAP_VERSION, false );
        } else {
            delete_option( self::OPTION_BOOTSTRAP_VERSION );
        }
    }

    public static function deactivate(): void {
        global $wpdb;
        $table = self::sessions_table();

        // Session state is no longer trusted once Workbench is inactive.
        $wpdb->query( "UPDATE {$table} SET status = 'terminated' WHERE status = 'active'" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

        self::remove_bootstrap();
        delete_option( self::OPTION_BOOTSTRAP_VERSION );
        self::expire_cookie();
    }

    public static function maybe_refresh_bootstrap(): void {
        $stored = (string) get_option( self::OPTION_BOOTSTRAP_VERSION, '' );
        if ( AWARE_SW_BOOTSTRAP_VERSION !== $stored || ! file_exists( self::bootstrap_path() ) ) {
            if ( self::install_bootstrap() ) {
                update_option( self::OPTION_BOOTSTRAP_VERSION, AWARE_SW_BOOTSTRAP_VERSION, false );
            }
        }
    }

    public static function create_tables(): void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table = self::sessions_table();
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            token_hash char(64) NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            auth_cookie_hash char(64) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            mode varchar(32) NOT NULL DEFAULT 'production_safe',
            started_at datetime NOT NULL,
            expires_at datetime NOT NULL,
            config longtext NOT NULL,
            last_seen_at datetime NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY token_hash (token_hash),
            KEY user_status (user_id,status),
            KEY expires_at (expires_at)
        ) {$charset};";
        dbDelta( $sql );
    }

    public static function sessions_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'aware_sw_sessions';
    }

    public static function bootstrap_path(): string {
        return trailingslashit( WPMU_PLUGIN_DIR ) . self::BOOTSTRAP_FILE;
    }

    public static function install_bootstrap(): bool {
        $dir = WPMU_PLUGIN_DIR;
        if ( ! is_dir( $dir ) && ! wp_mkdir_p( $dir ) ) {
            return false;
        }
        $template = AWARE_SW_DIR . 'templates/mu-bootstrap.php';
        if ( ! is_readable( $template ) || ! is_writable( $dir ) ) {
            return false;
        }
        $contents = file_get_contents( $template );
        if ( false === $contents ) {
            return false;
        }
        return false !== file_put_contents( self::bootstrap_path(), $contents, LOCK_EX );
    }

    public static function remove_bootstrap(): bool {
        $path = self::bootstrap_path();

        if ( ! is_file( $path ) ) {
            return true;
        }

        // Windows and some hosting environments can leave a generated file
        // read-only. Make a best-effort permission reset before deletion.
        if ( ! is_writable( $path ) ) {
            @chmod( $path, defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644 );
        }

        $removed = @unlink( $path );
        clearstatcache( true, $path );

        if ( $removed && ! is_file( $path ) ) {
            return true;
        }

        /*
         * Fail safe: if the filesystem refuses deletion, make the stale MU
         * bootstrap inert so an inactive Workbench can never keep applying
         * request isolation. Reactivation will overwrite this file from the
         * canonical template.
         */
        $inert = "<?php\n/** AWare Support Workbench inactive bootstrap safeguard. */\nreturn;\n";
        @file_put_contents( $path, $inert, LOCK_EX );
        clearstatcache( true, $path );

        return ! is_file( $path );
    }

    private static function expire_cookie(): void {
        if ( headers_sent() ) {
            return;
        }
        $path = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
        setcookie( AWARE_SW_COOKIE, '', [
            'expires'  => time() - HOUR_IN_SECONDS,
            'path'     => $path,
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ] );
    }
}
