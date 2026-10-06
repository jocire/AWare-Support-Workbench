<?php
/**
 * AWare Support Workbench early bootstrap.
 * Automatically managed by the main plugin. Do not edit manually.
 *
 * Intentionally tiny: if no diagnostic cookie is present, this file returns
 * before performing any database lookup or registering any filter.
 */

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

if ( empty( $_COOKIE['aware_sw_session'] ) ) {
    return;
}

$aware_sw_token = (string) $_COOKIE['aware_sw_session'];
if ( strlen( $aware_sw_token ) < 32 || strlen( $aware_sw_token ) > 200 ) {
    return;
}

if ( ! isset( $GLOBALS['wpdb'] ) || ! $GLOBALS['wpdb'] instanceof wpdb ) {
    return;
}

/*
 * MU plugins load before WordPress calls wp_cookie_constants(), so the
 * LOGGED_IN_COOKIE constant normally does not exist yet. Resolve the cookie
 * directly from the request instead of depending on the later constant.
 *
 * If a site explicitly defines LOGGED_IN_COOKIE in wp-config.php we honor it.
 * Otherwise use WordPress's standard wordpress_logged_in_* cookie name.
 */
$aware_sw_auth_cookie_name = defined( 'LOGGED_IN_COOKIE' ) ? (string) LOGGED_IN_COOKIE : '';
if ( '' === $aware_sw_auth_cookie_name || empty( $_COOKIE[ $aware_sw_auth_cookie_name ] ) ) {
    $aware_sw_auth_cookie_name = '';
    foreach ( array_keys( $_COOKIE ) as $aware_sw_cookie_name ) {
        if ( str_starts_with( (string) $aware_sw_cookie_name, 'wordpress_logged_in_' ) ) {
            $aware_sw_auth_cookie_name = (string) $aware_sw_cookie_name;
            break;
        }
    }
}
if ( '' === $aware_sw_auth_cookie_name || empty( $_COOKIE[ $aware_sw_auth_cookie_name ] ) ) {
    return;
}

$aware_sw_token_hash = hash( 'sha256', $aware_sw_token );
$aware_sw_auth_hash  = hash( 'sha256', (string) $_COOKIE[ $aware_sw_auth_cookie_name ] );
$aware_sw_table      = $GLOBALS['wpdb']->prefix . 'aware_sw_sessions';
$aware_sw_now        = gmdate( 'Y-m-d H:i:s' );

$aware_sw_previous_suppress = $GLOBALS['wpdb']->suppress_errors( true );
$aware_sw_row = $GLOBALS['wpdb']->get_row(
    $GLOBALS['wpdb']->prepare(
        "SELECT config FROM {$aware_sw_table} WHERE token_hash = %s AND auth_cookie_hash = %s AND status = 'active' AND expires_at > %s LIMIT 1",
        $aware_sw_token_hash,
        $aware_sw_auth_hash,
        $aware_sw_now
    ),
    ARRAY_A
);
$GLOBALS['wpdb']->suppress_errors( $aware_sw_previous_suppress );

if ( ! is_array( $aware_sw_row ) || empty( $aware_sw_row['config'] ) ) {
    return;
}

$aware_sw_config = json_decode( (string) $aware_sw_row['config'], true );
if ( ! is_array( $aware_sw_config ) || empty( $aware_sw_config['disabled_plugins'] ) || ! is_array( $aware_sw_config['disabled_plugins'] ) ) {
    return;
}

$aware_sw_disabled = array_fill_keys( array_map( 'strval', $aware_sw_config['disabled_plugins'] ), true );
if ( ! $aware_sw_disabled ) {
    return;
}

$GLOBALS['aware_sw_bootstrap_context'] = [
    'applied'          => true,
    'disabled_plugins' => array_keys( $aware_sw_disabled ),
    'auth_cookie_name' => $aware_sw_auth_cookie_name,
];

add_filter( 'option_active_plugins', static function ( $plugins ) use ( $aware_sw_disabled ) {
    return array_values(
        array_filter(
            (array) $plugins,
            static fn( $plugin ) => ! isset( $aware_sw_disabled[ (string) $plugin ] )
        )
    );
}, -9999 );

add_filter( 'site_option_active_sitewide_plugins', static function ( $plugins ) use ( $aware_sw_disabled ) {
    $plugins = is_array( $plugins ) ? $plugins : [];
    foreach ( array_keys( $aware_sw_disabled ) as $plugin ) {
        unset( $plugins[ $plugin ] );
    }
    return $plugins;
}, -9999 );
