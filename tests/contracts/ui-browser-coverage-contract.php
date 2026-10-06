<?php
require_once dirname( __DIR__ ) . '/test-lib.php';
$root = dirname( __DIR__, 2 );
$admin = file_get_contents( $root . '/includes/class-aware-sw-admin.php' );
$spec = file_get_contents( $root . '/tests/browser/troubleshooting.spec.js' );
$coverage = file_get_contents( $root . '/tests/browser/UI-COVERAGE.md' );
foreach ( [
    'aware-sw-page-title','aware-sw-tab-troubleshooting','aware-sw-session-status','aware-sw-session-heading',
    'aware-sw-plugin-isolation-heading','aware-sw-mode','aware-sw-plugin-table','aware-sw-plugin-col-name',
    'aware-sw-plugin-col-version','aware-sw-plugin-col-status','aware-sw-plugins-select-all','aware-sw-plugins-clear',
    'aware-sw-plugin-toggle-all','aware-sw-session-submit','aware-sw-session-stop','aware-sw-session-notice','aware-sw-isolation-verified','aware-sw-session-started-message','aware-sw-session-stopped-message'
] as $id ) {
    aware_test_assert( str_contains( $admin, $id ), "UI exposes stable browser-test hook {$id}." );
    aware_test_assert( str_contains( $spec, '#' . $id ), "Playwright uses direct selector #{$id}." );
}
foreach ( [
    'aware-sw-tab-maintenance','aware-sw-search','aware-sw-scan-selected','aware-sw-scan-pause','aware-sw-scan-resume',
    'aware-sw-targeted-heading','aware-sw-target-form','aware-sw-target-url','aware-sw-target-open','aware-sw-evidence','aware_sw_open_target'
] as $removed ) {
    aware_test_assert( ! str_contains( $admin, $removed ), "Removed UI/runtime hook {$removed} stays absent." );
}
aware_test_assert( str_contains( $spec, '#aware-sw-tab-maintenance' ), 'Playwright explicitly guards against Maintenance tab regression.' );
aware_test_assert( str_contains( $coverage, 'Support Workbench UI coverage' ), 'Element-by-element browser UI coverage map exists.' );
echo "PASS: final Engineer Session UI has stable hooks and mapped browser coverage.\n";
