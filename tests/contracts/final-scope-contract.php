<?php
require_once dirname( __DIR__ ) . '/test-lib.php';
$root = dirname( __DIR__, 2 );
$main = file_get_contents( $root . '/aware-support-workbench.php' );
$admin = file_get_contents( $root . '/includes/class-aware-sw-admin.php' );
aware_test_assert( ! is_file( $root . '/includes/class-aware-sw-diagnostics.php' ), 'Legacy diagnostics collector class is physically removed.' );

aware_test_assert( ! is_dir( $root . '/tests/live-isolation' ), 'Legacy live-isolation suite is physically removed.' );
aware_test_assert( ! is_dir( $root . '/tests/fixtures/aware-isolation-regression-fixture' ), 'Legacy isolation fixture plugin is physically removed.' );
aware_test_assert( ! is_file( $root . '/playwright.isolation.config.js' ), 'Legacy isolation Playwright config is physically removed.' );
$package = file_get_contents( $root . '/package.json' );
foreach ( [ 'test:isolation-live', 'playwright.isolation.config.js', 'aware-isolation-regression-fixture' ] as $removed ) {
    aware_test_not_contains( $removed, $package, 'removed live-isolation harness' );
}
foreach ( [ 'AWare_SW_Diagnostics', 'class-aware-sw-diagnostics.php', 'aware_sw_open_target', 'aware-sw-targeted-heading', 'aware-sw-target-form', 'aware-sw-target-url', 'aware-sw-evidence' ] as $removed ) {
    aware_test_not_contains( $removed, $main . "\n" . $admin, 'removed diagnostics subsystem' );
}
foreach ( [ 'aware-sw-tab-maintenance', 'aware-sw-search', 'aware-sw-scan-selected', 'aware-sw-scan-pause', 'aware-sw-scan-resume' ] as $removed ) {
    aware_test_not_contains( $removed, $admin, 'removed Maintenance Scan subsystem' );
}
echo "PASS: final runtime contains only Engineer Session troubleshooting scope.\n";
