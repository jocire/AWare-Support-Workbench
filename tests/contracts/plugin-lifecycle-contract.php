<?php
require dirname(__DIR__) . '/test-lib.php';
$root=dirname(__DIR__,2);
$main=file_get_contents($root.'/aware-support-workbench.php');
$installer=file_get_contents($root.'/includes/class-aware-sw-installer.php');
$uninstall=file_get_contents($root.'/uninstall.php');
foreach (["register_activation_hook", "register_deactivation_hook", "maybe_refresh_bootstrap", "AWare_SW_Session::instance()", "AWare_SW_Admin::instance()"] as $n) aware_test_contains($n,$main,'plugin bootstrap contract');
foreach (["install_bootstrap", "remove_bootstrap", "status = 'terminated'", "delete_option( self::OPTION_BOOTSTRAP_VERSION )", "clearstatcache", "chmod", 'return ! is_file( $path )'] as $n) aware_test_contains($n,$installer,'installer lifecycle contract');
foreach (["aware-support-workbench-bootstrap.php", "DROP TABLE IF EXISTS", "delete_option"] as $n) aware_test_contains($n,$uninstall,'uninstall cleanup contract');
echo "PASS: activation/deactivation/update/uninstall lifecycle contracts.\n";
