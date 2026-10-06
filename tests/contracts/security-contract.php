<?php
require dirname(__DIR__) . '/test-lib.php';
$root=dirname(__DIR__,2);
$session=file_get_contents($root.'/includes/class-aware-sw-session.php');
$mu=file_get_contents($root.'/templates/mu-bootstrap.php');
$admin=file_get_contents($root.'/includes/class-aware-sw-admin.php');
foreach (['auth_cookie_hash', 'hash( \'sha256\', $raw_auth )', "status='active'", 'expires_at > %s', 'httponly', 'samesite'] as $n) aware_test_contains($n,$session,'session security invariant');
foreach (["auth_cookie_hash", "wordpress_logged_in_", "suppress_errors", "option_active_plugins", "site_option_active_sitewide_plugins"] as $n) aware_test_contains($n,$mu,'MU bootstrap security invariant');
foreach (["current_user_can( 'activate_plugins' )", "check_admin_referer"] as $n) aware_test_contains($n,$admin,'admin authorization invariant');
aware_test_contains("plugin_basename( AWARE_SW_FILE )",$admin,'Workbench must protect itself from isolation');
echo "PASS: authorization, binding, expiry and self-protection security contracts.\n";
