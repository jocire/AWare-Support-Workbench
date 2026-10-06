<?php
require_once dirname( __DIR__ ) . '/test-lib.php';

$root   = dirname( __DIR__, 2 );
$source = file_get_contents( $root . '/templates/mu-bootstrap.php' );

$required = array(
	"empty( \$_COOKIE['aware_sw_session'] )" => 'no-token fast path',
	"hash( 'sha256', \$aware_sw_token )" => 'diagnostic token hash',
	"hash( 'sha256', (string) \$_COOKIE[ \$aware_sw_auth_cookie_name ] )" => 'login-cookie binding',
	"status = 'active'" => 'active-session check',
	"expires_at > %s" => 'expiry check',
	"add_filter( 'option_active_plugins'" => 'request-scoped plugin filter',
	"add_filter( 'site_option_active_sitewide_plugins'" => 'request-scoped network plugin filter',
);

foreach ( $required as $needle => $label ) {
	aware_test_assert( str_contains( $source, $needle ), 'Missing isolation safety guard: ' . $label );
}

$forbidden = array(
	"update_option( 'active_plugins'" => 'persisting active_plugins',
	"update_site_option( 'active_sitewide_plugins'" => 'persisting network plugin state',
	'deactivate_plugins(' => 'global plugin deactivation',
	'activate_plugin(' => 'global plugin activation',
	'file_put_contents(' => 'file mutation from the MU bootstrap',
	'ini_set(' => 'PHP runtime/server setting mutation',
);

foreach ( $forbidden as $needle => $label ) {
	aware_test_assert( ! str_contains( $source, $needle ), 'Forbidden MU-bootstrap behavior detected: ' . $label );
}

echo "PASS: isolation source guardrails prevent global plugin/server mutation paths.\n";
