<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class AWare_SW_Admin {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_post_aware_sw_start_session', [ $this, 'start_session' ] );
        add_action( 'admin_post_aware_sw_stop_session', [ $this, 'stop_session' ] );
        add_action( 'admin_notices', [ $this, 'session_notice' ] );
        add_action( 'admin_bar_menu', [ $this, 'admin_bar' ], 100 );
        add_action( 'admin_head', [ $this, 'engineer_session_admin_bar_style' ] );
        add_action( 'wp_head', [ $this, 'engineer_session_admin_bar_style' ] );
    }

    public function menu(): void {
        add_management_page( 'AWare Support Workbench', 'Support Workbench', 'activate_plugins', 'aware-support-workbench', [ $this, 'render' ] );
    }

    public function admin_bar( WP_Admin_Bar $bar ): void {
        $session = AWare_SW_Session::instance()->current();
        if ( ! current_user_can( 'activate_plugins' ) || ! $session ) {
            return;
        }

        $bar->add_node( [
            'id'     => 'aware-sw-engineer-session',
            'parent' => 'top-secondary',
            'title'  => '<span class="aware-sw-engineer-session-label">ENGINEER SESSION</span>',
            'href'   => admin_url( 'tools.php?page=aware-support-workbench' ),
            'meta'   => [
                'class' => 'aware-sw-engineer-session-node',
                'title' => 'AWare Engineer Session #' . (int) $session['id'] . ' is active in this browser',
            ],
        ] );
    }

    public function engineer_session_admin_bar_style(): void {
        if ( ! is_admin_bar_showing() || ! current_user_can( 'activate_plugins' ) || ! AWare_SW_Session::instance()->current() ) {
            return;
        }
        echo '<style id="aware-sw-engineer-session-admin-bar-style">'
            . '#wpadminbar #wp-admin-bar-aware-sw-engineer-session > .ab-item{background:#b32d2e!important;color:#fff!important;font-weight:700!important;letter-spacing:.04em;}'
            . '#wpadminbar #wp-admin-bar-aware-sw-engineer-session > .ab-item:hover,#wpadminbar #wp-admin-bar-aware-sw-engineer-session > .ab-item:focus{background:#8a2424!important;color:#fff!important;}'
            . '#wpadminbar #wp-admin-bar-aware-sw-engineer-session .ab-item:before{color:#fff!important;}'
            . '</style>';
    }

    public function session_notice(): void {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }
        $session = AWare_SW_Session::instance()->current();
        if ( ! $session ) {
            return;
        }
        $count = count( (array) $session['disabled_plugins'] );
        $mode = 'sandbox' === $session['mode'] ? 'Sandbox / Development' : 'Production Safe';
        echo '<div id="aware-sw-session-notice" class="notice notice-warning"><p><strong>ENGINEER SESSION ACTIVE</strong> — Session #' . esc_html( (string) $session['id'] ) . ' · ' . esc_html( $mode ) . ' · ' . esc_html( (string) $count ) . ' plugin(s) isolated in this browser only. Production configuration remains unchanged.</p></div>';
    }

    public function start_session(): void {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_die( 'Insufficient permissions.' );
        }
        check_admin_referer( 'aware_sw_start_session' );
        $disabled = isset( $_POST['disabled_plugins'] ) ? (array) wp_unslash( $_POST['disabled_plugins'] ) : [];
        $disabled = array_values( array_unique( array_filter( array_map( 'sanitize_text_field', $disabled ) ) ) );
        $self = plugin_basename( AWARE_SW_FILE );
        $disabled = array_values( array_filter( $disabled, static fn( $plugin ) => $plugin !== $self ) );
        $mode = isset( $_POST['mode'] ) && 'sandbox' === sanitize_key( wp_unslash( $_POST['mode'] ) ) ? 'sandbox' : 'production_safe';

        $session = AWare_SW_Session::instance()->create( $disabled, $mode );
        $arg = $session ? 'started=1' : 'session_error=1';
        wp_safe_redirect( admin_url( 'tools.php?page=aware-support-workbench&' . $arg ) );
        exit;
    }

    public function stop_session(): void {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            wp_die( 'Insufficient permissions.' );
        }
        check_admin_referer( 'aware_sw_stop_session' );
        AWare_SW_Session::instance()->stop_current();
        wp_safe_redirect( admin_url( 'tools.php?page=aware-support-workbench&stopped=1' ) );
        exit;
    }


    public function render(): void {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $all_plugins = get_plugins();
        global $wpdb;
        // Read the production active-plugin option directly because the MU isolation
        // bootstrap intentionally filters option_active_plugins inside this browser.
        $raw_active = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1", 'active_plugins' ) );
        $active = is_string( $raw_active ) ? (array) maybe_unserialize( $raw_active ) : [];
        $self = plugin_basename( AWARE_SW_FILE );
        $active = array_values( array_filter( $active, static fn( $plugin ) => $plugin !== $self ) );
        $session = AWare_SW_Session::instance()->current();
        $disabled = array_values( (array) ( $session['disabled_plugins'] ?? [] ) );
        $session_id = (int) ( $session['id'] ?? 0 );
        $bootstrap_ok = file_exists( AWare_SW_Installer::bootstrap_path() );
        $bootstrap_applied = ! empty( $GLOBALS['aware_sw_bootstrap_context']['applied'] );

        echo '<div class="wrap"><h1 id="aware-sw-page-title">AWare Support Workbench <small>' . esc_html( AWARE_SW_VERSION ) . '</small></h1>';
        echo '<nav class="nav-tab-wrapper"><a id="aware-sw-tab-troubleshooting" class="nav-tab nav-tab-active" href="' . esc_url( admin_url( 'tools.php?page=aware-support-workbench' ) ) . '">Troubleshooting</a></nav>';
        echo '<p>Isolate one or more active plugins inside a private Engineer Session. Other browsers and visitors continue using the normal production plugin configuration.</p>';

        if ( isset( $_GET['started'] ) ) {
            echo '<div class="notice notice-success inline"><p><span id="aware-sw-session-started-message">Engineer Session started or updated.</span></p></div>';
        }
        if ( isset( $_GET['stopped'] ) ) {
            echo '<div class="notice notice-success inline"><p><span id="aware-sw-session-stopped-message">Engineer Session stopped. Normal plugin loading has been restored for this browser.</span></p></div>';
        }
        if ( isset( $_GET['session_error'] ) ) {
            echo '<div class="notice notice-error inline"><p>The Engineer Session could not be started. Verify that you are logged in and that the Workbench MU bootstrap is available.</p></div>';
        }


        echo '<div id="aware-sw-session-status" data-state="' . esc_attr( $session ? 'active' : 'inactive' ) . '" style="padding:18px;border:2px solid ' . ( $session ? '#2271b1' : '#8c8f94' ) . ';background:#fff;margin:16px 0">';
        if ( $session ) {
            echo '<h2 id="aware-sw-session-heading" style="margin-top:0;color:#135e96">Engineer Session Active</h2>';
            echo '<p><strong>This browser is in diagnostic mode.</strong> Other visitors and browsers continue using the normal production configuration.</p>';
            echo '<p>Mode: <strong>' . esc_html( 'sandbox' === $session['mode'] ? 'Sandbox / Development' : 'Production Safe' ) . '</strong> · Isolated plugins: <strong>' . esc_html( (string) count( $disabled ) ) . '</strong> · Session #' . esc_html( (string) $session_id ) . '</p>';
            if ( $disabled && ! $bootstrap_applied ) {
                echo '<div id="aware-sw-isolation-warning" class="notice notice-error inline" style="margin:12px 0 0"><p><strong>Isolation is NOT active on this request.</strong> The session exists, but the early MU bootstrap did not apply the selected plugin isolation. Do not rely on this test result.</p></div>';
            } elseif ( $disabled && $bootstrap_applied ) {
                echo '<p id="aware-sw-isolation-verified" style="color:#008a20"><strong>✓ Early isolation verified for this request.</strong></p>';
            }
        } else {
            echo '<h2 id="aware-sw-session-heading" style="margin-top:0">No Engineer Session Active</h2>';
            echo '<p>Select the plugins you want to remove from this browser session, then start troubleshooting.</p>';
            if ( ! $bootstrap_ok ) {
                echo '<p><strong style="color:#b32d2e">Deep isolation is unavailable because the early MU bootstrap could not be installed.</strong></p>';
            }
        }
        echo '</div>';

        echo '<h2 id="aware-sw-plugin-isolation-heading">Plugin isolation</h2>';
        echo '<p>Select any combination of currently active plugins. Starting the form again replaces the current isolation set, so you can quickly compare different combinations.</p>';
        echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" id="aware-sw-isolation-form">';
        echo '<input type="hidden" name="action" value="aware_sw_start_session">';
        wp_nonce_field( 'aware_sw_start_session' );
        echo '<div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin:10px 0">';
        echo '<button type="button" class="button" id="aware-sw-plugins-select-all">Select all</button>';
        echo '<button type="button" class="button" id="aware-sw-plugins-clear">Clear selection</button>';
        echo '<label for="aware-sw-mode" style="margin-left:8px"><strong>Mode:</strong> <select id="aware-sw-mode" name="mode" aria-label="Engineer Session mode"><option value="production_safe"' . selected( (string) ( $session['mode'] ?? 'production_safe' ), 'production_safe', false ) . '>Production Safe</option><option value="sandbox"' . selected( (string) ( $session['mode'] ?? '' ), 'sandbox', false ) . '>Sandbox / Development</option></select></label>';
        echo '</div>';

        echo '<table id="aware-sw-plugin-table" class="widefat striped" style="max-width:1100px"><thead><tr><td id="aware-sw-plugin-col-select" class="check-column"><input type="checkbox" id="aware-sw-plugin-toggle-all" aria-label="Select all plugins"></td><th id="aware-sw-plugin-col-name">Active plugin</th><th id="aware-sw-plugin-col-version">Version</th><th id="aware-sw-plugin-col-status">Isolation status</th></tr></thead><tbody>';
        foreach ( $active as $plugin_file ) {
            $data = (array) ( $all_plugins[ $plugin_file ] ?? [] );
            $name = (string) ( $data['Name'] ?? $plugin_file );
            $version = (string) ( $data['Version'] ?? '' );
            $is_disabled = in_array( $plugin_file, $disabled, true );
            echo '<tr><th scope="row" class="check-column"><input type="checkbox" class="aware-sw-plugin-select" name="disabled_plugins[]" value="' . esc_attr( $plugin_file ) . '"' . checked( $is_disabled, true, false ) . ' aria-label="' . esc_attr( 'Isolate ' . $name ) . '"></th><td><strong>' . esc_html( $name ) . '</strong><br><code>' . esc_html( $plugin_file ) . '</code></td><td>' . esc_html( $version ?: '—' ) . '</td><td>' . ( $is_disabled ? '<strong style="color:#b32d2e">Isolated in current session</strong>' : 'Loaded normally' ) . '</td></tr>';
        }
        if ( ! $active ) {
            echo '<tr><td colspan="4">No other active plugins were found.</td></tr>';
        }
        echo '</tbody></table>';
        echo '<p><button type="submit" class="button button-primary" id="aware-sw-session-submit">' . esc_html( $session ? 'Update Engineer Session' : 'Start Engineer Session' ) . '</button></p>';
        echo '</form>';

        if ( $session ) {
            echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin-top:8px"><input type="hidden" name="action" value="aware_sw_stop_session">';
            wp_nonce_field( 'aware_sw_stop_session' );
            echo '<button class="button" id="aware-sw-session-stop">Stop Engineer Session / restore normal state</button></form>';
        }


        echo '<script>(function(){var boxes=function(){return Array.prototype.slice.call(document.querySelectorAll(".aware-sw-plugin-select"));};var set=function(v){boxes().forEach(function(b){b.checked=v;});};var a=document.getElementById("aware-sw-plugins-select-all"),c=document.getElementById("aware-sw-plugins-clear"),t=document.getElementById("aware-sw-plugin-toggle-all");if(a)a.addEventListener("click",function(){set(true);if(t)t.checked=true;});if(c)c.addEventListener("click",function(){set(false);if(t)t.checked=false;});if(t)t.addEventListener("change",function(){set(!!t.checked);});})();</script>';
        echo '</div>';
    }
}
