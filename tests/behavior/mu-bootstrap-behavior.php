<?php
require dirname(__DIR__) . '/test-lib.php';
if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
if (!defined('ARRAY_A')) define('ARRAY_A', 'ARRAY_A');
$_COOKIE = ['aware_sw_session'=>str_repeat('a',64),'wordpress_logged_in_fixture'=>'auth'];
$GLOBALS['aware_filters']=[];
function add_filter($tag,$cb,$priority=10){ $GLOBALS['aware_filters'][$tag]=$cb; }
class wpdb {
    public string $prefix='wp_';
    function suppress_errors($v){ return false; }
    function prepare($sql,...$args){ return $args; }
    function get_row($args,$mode){ return ['config'=>json_encode(['disabled_plugins'=>['bad/bad.php','other/other.php']])]; }
}
$GLOBALS['wpdb']=new wpdb();
require dirname(__DIR__,2) . '/templates/mu-bootstrap.php';
aware_test_assert(!empty($GLOBALS['aware_sw_bootstrap_context']['applied']), 'MU bootstrap should mark isolation as applied');
$active = $GLOBALS['aware_filters']['option_active_plugins'](['good/good.php','bad/bad.php','other/other.php']);
aware_test_same(['good/good.php'], $active, 'MU bootstrap must remove selected active plugins');
$network = $GLOBALS['aware_filters']['site_option_active_sitewide_plugins'](['bad/bad.php'=>1,'good/good.php'=>2]);
aware_test_same(['good/good.php'=>2], $network, 'MU bootstrap must remove selected network plugins');
$network_false = $GLOBALS['aware_filters']['site_option_active_sitewide_plugins'](false);
aware_test_same([], $network_false, 'MU bootstrap must safely normalize a false network-active plugin value');
echo "PASS: MU bootstrap performs early request-scoped plugin filtering.\n";
