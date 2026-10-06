<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;
$bootstrap = trailingslashit( WPMU_PLUGIN_DIR ) . 'aware-support-workbench-bootstrap.php';
if ( is_file( $bootstrap ) ) {
    if ( ! is_writable( $bootstrap ) ) {
        @chmod( $bootstrap, defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644 );
    }
    @unlink( $bootstrap );
    clearstatcache( true, $bootstrap );

    // A failed delete must still leave the orphaned MU file inert.
    if ( is_file( $bootstrap ) ) {
        @file_put_contents( $bootstrap, "<?php\n/** AWare Support Workbench uninstalled safeguard. */\nreturn;\n", LOCK_EX );
    }
}

delete_option( 'aware_sw_bootstrap_version' );
$table = $wpdb->prefix . 'aware_sw_sessions';
$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
