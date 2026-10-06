<?php
require dirname(__DIR__) . '/test-lib.php';
if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
if (!defined('HOUR_IN_SECONDS')) define('HOUR_IN_SECONDS', 3600);
if (!defined('COOKIEPATH')) define('COOKIEPATH', '/');
if (!defined('LOGGED_IN_COOKIE')) define('LOGGED_IN_COOKIE', 'wordpress_logged_in_test');
if (!defined('AWARE_SW_COOKIE')) define('AWARE_SW_COOKIE', 'aware_sw_session');
if (!defined('ARRAY_A')) define('ARRAY_A', 'ARRAY_A');
$GLOBALS['aware_test_now'] = '2026-10-05 08:00:00';
$GLOBALS['aware_test_user_id'] = 77;
$GLOBALS['aware_test_logged_in'] = true;
$GLOBALS['aware_test_cap'] = true;
$_COOKIE[LOGGED_IN_COOKIE] = 'auth-cookie-value';
function add_action(...$args) {}
function is_user_logged_in(){ return $GLOBALS['aware_test_logged_in']; }
function current_user_can($cap){ return $GLOBALS['aware_test_cap']; }
function get_current_user_id(){ return $GLOBALS['aware_test_user_id']; }
function current_time($type, $gmt=false){ return $GLOBALS['aware_test_now']; }
function wp_json_encode($v){ return json_encode($v); }
function wp_unslash($v){ return $v; }
function is_ssl(){ return false; }
class FakeWPDB {
    public string $prefix = 'wp_'; public int $insert_id = 0; public array $rows = []; public array $updates = [];
    function insert($table,$data,$formats){ $this->insert_id++; $data['id']=$this->insert_id; $this->rows[$this->insert_id]=$data; return 1; }
    function prepare($sql,...$args){ return ['sql'=>$sql,'args'=>$args]; }
    function get_row($prepared,$mode){
        [$token,$auth,$uid,$now]=$prepared['args'];
        foreach ($this->rows as $row) {
            if ($row['token_hash']===$token && $row['auth_cookie_hash']===$auth && (int)$row['user_id']===(int)$uid && $row['status']==='active' && $row['expires_at']>$now) return $row;
        }
        return null;
    }
    function update($table,$data,$where,$formats=[],$where_formats=[]){
        foreach ($this->rows as $id=>&$row) {
            $ok=true; foreach ($where as $k=>$v) if (($row[$k]??null)!=$v) $ok=false;
            if ($ok) foreach ($data as $k=>$v) $row[$k]=$v;
        }
        $this->updates[]=['data'=>$data,'where'=>$where]; return 1;
    }
}
$GLOBALS['wpdb'] = new FakeWPDB();
class AWare_SW_Installer { static function sessions_table(): string { return 'wp_aware_sw_sessions'; } }
require dirname(__DIR__,2) . '/includes/class-aware-sw-session.php';
$session = AWare_SW_Session::instance()->create(['plugin-a/a.php','plugin-a/a.php','plugin-b/b.php'], 'bogus');
aware_test_assert(!empty($session['token']), 'session token should be created');
aware_test_same('production_safe', $session['mode'], 'invalid mode must normalize to production_safe');
aware_test_same(['plugin-a/a.php','plugin-b/b.php'], $session['disabled_plugins'], 'disabled plugins must be de-duplicated');
aware_test_assert(isset($_COOKIE[AWARE_SW_COOKIE]), 'session cookie should be mirrored into request state');
$current = AWare_SW_Session::instance()->current();
aware_test_same((int)$session['id'], (int)$current['id'], 'current session should resolve by token/auth/user binding');
aware_test_same(['plugin-a/a.php','plugin-b/b.php'], $current['disabled_plugins'], 'current session should expose isolation config');
$_COOKIE[LOGGED_IN_COOKIE] = 'different-auth';
aware_test_same([], AWare_SW_Session::instance()->current(), 'auth-cookie mismatch must invalidate current session');
$_COOKIE[LOGGED_IN_COOKIE] = 'auth-cookie-value';
AWare_SW_Session::instance()->stop_current();
aware_test_same([], AWare_SW_Session::instance()->current(), 'stopped session must no longer resolve');
echo "PASS: session creation, auth binding, mode normalization, de-duplication and termination.\n";
