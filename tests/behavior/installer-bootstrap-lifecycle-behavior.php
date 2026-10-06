<?php
require dirname(__DIR__) . '/test-lib.php';

$root = dirname(__DIR__, 2);
$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'aware-sw-lifecycle-' . bin2hex(random_bytes(5));
$mu = $tmp . DIRECTORY_SEPARATOR . 'mu-plugins';
mkdir($mu, 0777, true);

if (!defined('ABSPATH')) define('ABSPATH', $tmp . DIRECTORY_SEPARATOR);
if (!defined('WPMU_PLUGIN_DIR')) define('WPMU_PLUGIN_DIR', $mu);
if (!defined('AWARE_SW_DIR')) define('AWARE_SW_DIR', $root . DIRECTORY_SEPARATOR);
if (!defined('AWARE_SW_BOOTSTRAP_VERSION')) define('AWARE_SW_BOOTSTRAP_VERSION', 'test');
if (!defined('AWARE_SW_COOKIE')) define('AWARE_SW_COOKIE', 'aware_sw_session');
if (!defined('HOUR_IN_SECONDS')) define('HOUR_IN_SECONDS', 3600);

function trailingslashit($v){ return rtrim($v, '/\\') . DIRECTORY_SEPARATOR; }
function wp_mkdir_p($v){ return is_dir($v) || mkdir($v, 0777, true); }
function update_option($a,$b,$c=false){ return true; }
function delete_option($a){ return true; }
function get_option($a,$b=''){ return $b; }
function is_ssl(){ return false; }

$GLOBALS['wpdb'] = new class {
    public string $prefix = 'wp_';
    public function query($sql){ return 1; }
    public function get_charset_collate(){ return ''; }
};

require $root . '/includes/class-aware-sw-installer.php';

aware_test_assert(AWare_SW_Installer::install_bootstrap(), 'Activation path should install MU bootstrap');
$path = AWare_SW_Installer::bootstrap_path();
aware_test_assert(is_file($path), 'MU bootstrap should exist after installation');
$installed = file_get_contents($path);
$canonical = file_get_contents($root . '/templates/mu-bootstrap.php');
aware_test_same($canonical, $installed, 'Installed MU bootstrap must match canonical template');

aware_test_assert(AWare_SW_Installer::remove_bootstrap(), 'Deactivation path should report successful MU bootstrap removal');
clearstatcache(true, $path);
aware_test_assert(!is_file($path), 'MU bootstrap must not remain after deactivation removal');

aware_test_assert(AWare_SW_Installer::install_bootstrap(), 'Reactivation path should recreate MU bootstrap');
clearstatcache(true, $path);
aware_test_assert(is_file($path), 'MU bootstrap must exist again after reactivation installation');
aware_test_same($canonical, file_get_contents($path), 'Recreated MU bootstrap must match canonical template');

@unlink($path);
@rmdir($mu);
@rmdir($tmp);

echo "PASS: MU bootstrap is removed on deactivation and recreated on activation.\n";
