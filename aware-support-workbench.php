<?php
/**
 * Plugin Name: AWare Support Workbench
 * Description: Production-safe troubleshooting sessions for WordPress support engineers, with tokenized Engineer Sessions and early request-scoped plugin isolation.
 * Version: 1.0.0-rc1
 * Author: AWare Tools
 * Requires at least: 6.5
 * Requires PHP: 8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'AWARE_SW_VERSION', '1.0.0-rc1' );
define( 'AWARE_SW_BOOTSTRAP_VERSION', '4' );
define( 'AWARE_SW_FILE', __FILE__ );
define( 'AWARE_SW_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWARE_SW_URL', plugin_dir_url( __FILE__ ) );
define( 'AWARE_SW_COOKIE', 'aware_sw_session' );

require_once AWARE_SW_DIR . 'includes/class-aware-sw-installer.php';
require_once AWARE_SW_DIR . 'includes/class-aware-sw-session.php';
require_once AWARE_SW_DIR . 'includes/class-aware-sw-admin.php';

register_activation_hook( __FILE__, [ 'AWare_SW_Installer', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'AWare_SW_Installer', 'deactivate' ] );

add_action( 'plugins_loaded', static function (): void {
    AWare_SW_Installer::maybe_refresh_bootstrap();
    AWare_SW_Session::instance();
    AWare_SW_Admin::instance();
} );
