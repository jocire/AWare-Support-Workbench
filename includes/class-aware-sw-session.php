<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AWare_SW_Session {
    private static ?self $instance = null;
    private const DURATION = 4 * HOUR_IN_SECONDS;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action( 'wp_logout', [ $this, 'handle_logout' ], 10, 1 );
    }

    public function create( array $disabled_plugins, string $mode = 'production_safe' ): array {
        if ( ! is_user_logged_in() || ! current_user_can( 'activate_plugins' ) ) {
            return [];
        }

        $user_id = get_current_user_id();
        $raw_auth = $this->raw_logged_in_cookie();
        if ( '' === $raw_auth ) {
            return [];
        }

        $this->terminate_for_user( $user_id );

        $token = bin2hex( random_bytes( 32 ) );
        $started = current_time( 'mysql', true );
        $expires = gmdate( 'Y-m-d H:i:s', time() + self::DURATION );
        $config = [
            'disabled_plugins' => array_values( array_unique( array_map( 'strval', $disabled_plugins ) ) ),
            'safety' => [
                'concurrency'             => 1,
                'serialized_experiments' => true,
                'mutation_guard'          => true,
            ],
        ];

        global $wpdb;
        $ok = $wpdb->insert(
            AWare_SW_Installer::sessions_table(),
            [
                'token_hash'       => hash( 'sha256', $token ),
                'user_id'          => $user_id,
                'auth_cookie_hash' => hash( 'sha256', $raw_auth ),
                'status'           => 'active',
                'mode'             => 'sandbox' === $mode ? 'sandbox' : 'production_safe',
                'started_at'       => $started,
                'expires_at'       => $expires,
                'config'           => wp_json_encode( $config ),
                'last_seen_at'     => $started,
            ],
            [ '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
        );

        if ( false === $ok ) {
            return [];
        }

        $this->set_cookie( $token, time() + self::DURATION );
        return [
            'id'               => (int) $wpdb->insert_id,
            'token'            => $token,
            'mode'             => 'sandbox' === $mode ? 'sandbox' : 'production_safe',
            'started_at'       => $started,
            'expires_at'       => $expires,
            'disabled_plugins' => $config['disabled_plugins'],
        ];
    }

    public function current(): array {
        if ( ! is_user_logged_in() ) {
            return [];
        }
        $token = isset( $_COOKIE[ AWARE_SW_COOKIE ] ) ? (string) wp_unslash( $_COOKIE[ AWARE_SW_COOKIE ] ) : '';
        if ( strlen( $token ) < 32 ) {
            return [];
        }
        $raw_auth = $this->raw_logged_in_cookie();
        if ( '' === $raw_auth ) {
            return [];
        }

        global $wpdb;
        $table = AWare_SW_Installer::sessions_table();
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id,user_id,status,mode,started_at,expires_at,config,last_seen_at FROM {$table} WHERE token_hash=%s AND auth_cookie_hash=%s AND user_id=%d AND status='active' AND expires_at > %s LIMIT 1",
                hash( 'sha256', $token ),
                hash( 'sha256', $raw_auth ),
                get_current_user_id(),
                current_time( 'mysql', true )
            ),
            ARRAY_A
        );
        if ( ! is_array( $row ) ) {
            return [];
        }

        $config = json_decode( (string) $row['config'], true );
        if ( ! is_array( $config ) ) {
            $config = [];
        }
        $row['disabled_plugins'] = array_values( (array) ( $config['disabled_plugins'] ?? [] ) );
        $row['safety'] = (array) ( $config['safety'] ?? [] );
        return $row;
    }

    public function stop_current(): void {
        $session = $this->current();
        if ( $session ) {
            global $wpdb;
            $wpdb->update(
                AWare_SW_Installer::sessions_table(),
                [ 'status' => 'terminated' ],
                [ 'id' => (int) $session['id'] ],
                [ '%s' ],
                [ '%d' ]
            );
        }
        $this->set_cookie( '', time() - HOUR_IN_SECONDS );
    }

    public function handle_logout( int $user_id ): void {
        if ( $user_id > 0 ) {
            $this->terminate_for_user( $user_id );
        }
        $this->set_cookie( '', time() - HOUR_IN_SECONDS );
    }

    public function terminate_for_user( int $user_id ): void {
        global $wpdb;
        $wpdb->update(
            AWare_SW_Installer::sessions_table(),
            [ 'status' => 'terminated' ],
            [ 'user_id' => $user_id, 'status' => 'active' ],
            [ '%s' ],
            [ '%d', '%s' ]
        );
    }

    private function raw_logged_in_cookie(): string {
        $name = defined( 'LOGGED_IN_COOKIE' ) ? LOGGED_IN_COOKIE : '';
        return $name && isset( $_COOKIE[ $name ] ) ? (string) wp_unslash( $_COOKIE[ $name ] ) : '';
    }

    private function set_cookie( string $value, int $expires ): void {
        if ( headers_sent() ) {
            return;
        }
        $path = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
        setcookie( AWARE_SW_COOKIE, $value, [
            'expires'  => $expires,
            'path'     => $path,
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ] );
        if ( '' === $value ) {
            unset( $_COOKIE[ AWARE_SW_COOKIE ] );
        } else {
            $_COOKIE[ AWARE_SW_COOKIE ] = $value;
        }
    }
}
