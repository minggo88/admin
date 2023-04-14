<?php
ob_start();
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
if(! defined ('__API_RUNMODE__')) {
	$runmode = 'live';
	if(strpos($_SERVER['HTTP_HOST'], 'loc.')!== false) {
		$runmode = 'loc';
	}
	if(strpos($_SERVER['HTTP_HOST'], 'dev.')!== false) {
		$runmode = 'dev';
	}
	if(strpos($_SERVER['HTTP_HOST'], 'stage.')!== false) {
		$runmode = 'stage';
	}
	define('__API_RUNMODE__', $runmode);
}
date_default_timezone_set('Asia/Seoul');
//putenv("TZ=Asia/Seoul"); 
if(session_id() == '') {
	session_name('token');
	if(class_exists('Memcached')) {
		require('session.class.memcached.php');
		$SessionMemCached = new SessionMemCached(true);
	} else {
		ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']);
		ini_set("session.save_path",__DIR__."/../../_session");
		ini_set("session.cookie_lifetime", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.cache_expire", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.gc_maxlifetime", "3600");  //기본 세션타임 1시간으로 설정 합니다.
		session_cache_limiter('private'); 
		session_start();
	}
}
error_reporting(E_ERROR | E_WARNING | E_PARSE); // 개발시 오류 메시지표시용.
if(__API_RUNMODE__=='live') {
	ini_set('display_error','off'); // 라이브 서비스용.
	error_reporting(0);
} else {
	ini_set('display_error','on'); // 개발시 오류 메시지표시용.
	error_reporting(E_ALL^E_NOTICE);
}
header("Content-Type: text/html; charset=UTF-8");
header ("Cache-Control: no-cache, must-revalidate"); // 브라우져캐싱
header ("Pragma: no-cache"); // 브라우져캐싱
//register_shutdown_function("__shutdown");

//var_dump($_SESSION);

// XSS 공격 방어용
function strip_tags_all($v) {
	if (is_array($v)) {
		foreach($v as $key => $val){
			$v[$key] = strip_tags_all($val);
		}
	} else {
		$v = trim(strip_tags($v));
	}
	return $v;
}
$use_html_params = array('contents'); // 태그 날리지 말것 확인.
foreach ($_POST as $key => $val) {
	if (!in_array($key, $use_html_params)) {
		$_POST[$key] = strip_tags_all($val);
	}
}
foreach ($_GET as $key => $val) {
	if (!in_array($key, $use_html_params)) {
		$_GET[$key] = strip_tags_all($val);
	}
}
foreach ($_REQUEST as $key => $val) {
	if (!in_array($key, $use_html_params)) {
		$_REQUEST[$key] = strip_tags_all($val);
	}
}
// XSS 공격 방어용


function checkPhpVersion ($a='0',$b='0',$c='0')             
{
	$PHP_VERSION = substr(str_pad(preg_replace('/\D/','', PHP_VERSION), 3, '0'), 0, 3);
	return $PHP_VERSION >= ($a.$b.$c);
}

function __shutdown() {
	global $dbcon;
	if (isset($dbcon->connect) && $dbcon->connect) {
		$dbcon->close();
	}
	else {
		@mysqli_close();
	}
}

if (!checkPhpVersion(4,1)) {
	global $_COOKIE, $_ENV, $_FILES, $_GET, $_POST, $_SERVER, $_SESSION;
	global $HTTP_COOKIE_VARS, $HTTP_ENV_VARS, $HTTP_POST_FILES, $HTTP_GET_VARS,
		$HTTP_POST_VARS, $HTTP_SERVER_VARS, $HTTP_SESSION_VARS, $PHP_SELF;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_SESSION = &$HTTP_SESSION_VARS;
	if (!isset($PHP_SELF) || empty($PHP_SELF)) $PHP_SELF =  $HTTP_SERVER_VARS['PHP_SELF'];
}

//현재 선택된 언어
if(@$_COOKIE['lang']){
	$lang = $_SESSION['lang'] = $_COOKIE['lang']; 
}else{
	$support_lang = array('en','kr','cn','zh'); // @todo 번역 파일에서 추출하도록 수정 필요.
	$_GET['lang'] = in_array($_GET['lang'], $support_lang) ? $_GET['lang'] : 'kr';
	$lang = $_SESSION['lang'] = $_GET['lang'];
	setcookie('lang', 'kr', null, '/');    
}

include_once $_SERVER["DOCUMENT_ROOT"].'/lib/language/i18n.class.php';
$i18n = new i18n($_SERVER["DOCUMENT_ROOT"].'/lib/language/lang/lang_'.$lang.'.json', $_SERVER["DOCUMENT_ROOT"].'/lib/language/langcache/', $lang,'Lang');

if(strpos($_SERVER.SCRIPT_NAME, '/admin/')!==false) {
	$i18n -> init();
}

include_once $_SERVER["DOCUMENT_ROOT"].'/lib/basic_config.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/basic_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/db_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/util.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/shopUtil.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/json_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/mail_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/Snoopy.class.php';


$dbcon = new DB($db_host,$db_name,$db_user,$db_pass,$db_charset);
$json = new Services_JSON();
// sql injection 임시 해결용 
// 실재로 DB 쿼리에서 bind 처리해야 함. 일단 대규모로 사용하기 때문에 쿼리에서 사용하는 $_POST, $_GET, $_REQUEST 를 escape 해버리고 변수를 다시 담아 사용하도록 함.
function escape_all($v) {
	if (is_array($v)) {
		foreach($v as $key => $val){
			$v[$key] = escape_all($val);
		}
	} else {
		$v = $GLOBALS['dbcon']->escape($v);
	}
	return $v;
}
$_POST_ESCAPE = array();
foreach ($_POST as $key => $val) {
	$_POST_ESCAPE[$key] = escape_all($val);
}
$_GET_ESCAPE = array();
foreach ($_GET as $key => $val) {
	$_GET_ESCAPE[$key] = escape_all($val);
}
$_REQUEST_ESCAPE = array();
foreach ($_REQUEST as $key => $val) {
	$_REQUEST_ESCAPE[$key] = escape_all($val);
}
// sql injection 임시 해결용


define('WEB_URL',"http://".$_SERVER["HTTP_HOST"]);
define('ROOT_DIR',$_SERVER["DOCUMENT_ROOT"]);
define('EDITOR_DIR',ROOT_DIR.'/data/editorTemp');
define('EDITOR_URL',WEB_URL."/data/editorTemp");
define('EDITOR_SAVE_DIR',ROOT_DIR."/data/editor");
define('EDITOR_SAVE_URL',WEB_URL."/data/editor");
getSiteCode();

if(!empty($_POST['pg_mode'])) {
	$_GET['pg_mode'] = '';
}
else if(!empty($_GET['pg_mode'])) {
	$_POST['pg_mode'] = '';
}
else {
	$_GET['pg_mode'] = $_POST['pg_mode'] = '';
}

// react script version 값 수정. 파일 수정날짜로. 
$react_version = filemtime(__dir__.'/../template/admin/smc/js/kmcsetrade.2.chunk.js');
define('__REACT_VERSION__', $react_version);

ob_clean();