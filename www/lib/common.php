<?php
ob_start();
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
// 서버아이피보정
$_SERVER["SERVER_ADDR"] = isset($_SERVER["HTTP_X_SERVER_ADDRESS"]) && $_SERVER["HTTP_X_SERVER_ADDRESS"] ? $_SERVER["HTTP_X_SERVER_ADDRESS"] : $_SERVER["SERVER_ADDR"];
// 접속자아이피보정
$_SERVER["REMOTE_ADDR"] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] ?  explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"])[0]: $_SERVER["SERVER_ADDR"];

if(! defined ('__APP_NAME__')) {
    define('__APP_NAME__', 'TRADE');
}
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

error_reporting(E_ERROR | E_WARNING | E_PARSE); // 개발시 오류 메시지표시용.
if(__API_RUNMODE__=='live') {
	ini_set('display_error','off'); // 라이브 서비스용.
	error_reporting(0);
} else {
	ini_set('display_error','on'); // 개발시 오류 메시지표시용.
	error_reporting(E_ALL^E_NOTICE);
}

date_default_timezone_set('Asia/Seoul');
//putenv("TZ=Asia/Seoul");
if(session_id() == '') {
	// //세션키가 쿠키에 없고 전달 받은 경우 쿠키에 설정한다. - 미사용
	// if($_POST['token']) {
	// 	setCookie('token', '', -1, '/', '.kmcse.com');//.loc.kmcse.com
	// 	setCookie('token', '', -1, '/', '.loc.kmcse.com');//.loc.kmcse.com
	// 	setCookie('token', '', -1, '/', '.nft.kmcse.com');//.loc.kmcse.com
	// 	setCookie('token', '', -1, '/', '.nft.loc.kmcse.com');//.loc.kmcse.com
	// 	setCookie('token', $_POST['token'], -1);
	// 	setCookie('token', $_POST['token'], 0, '/', '.'.$_SERVER['HTTP_HOST']);
	// 	$_COOKIE['token'] = $_POST['token'];
	// 	// 페이지 리로드
	// 	header('Location: '.$_SERVER['REQUEST_URI']); exit('<script>window.location.replace("'.$_SERVER['REQUEST_URI'].'");</script>');
	// }

	session_name('token');
	if(class_exists('Memcached')) {
		require('session.class.memcached.php');
		$SessionMemCached = new SessionMemCached(true);
	} else {
		$_session_dir = __DIR__."/../../_session";
		if(!file_exists($_session_dir)){mkdir($_session_dir, 0777, true);}
		ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']);
		ini_set("session.save_path",$_session_dir);
		ini_set("session.cookie_lifetime", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.cache_expire", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.gc_maxlifetime", "3600");  //기본 세션타임 1시간으로 설정 합니다.
		session_cache_limiter('private');
		session_start();
	}
}
if(isset($_GET['device_type']) && $_GET['device_type']=='app') {
	$_SESSION['device_type'] = 'app';
}
$_GET['device_type'] = $_SESSION['device_type'];
define('SERVICE_DOMAIN',substr(ini_get('session.cookie_domain'),0));
define('TRADE_SERVICE_DOMAIN','trade.'.SERVICE_DOMAIN);
define('AUCTION_SERVICE_DOMAIN','auction.'.SERVICE_DOMAIN);
$_SERVER['LOGIN_PAGE'] = '/login/'.base64_encode($_SERVER['REQUEST_URI']); // 내부 로그인 페이지
define('RET_URL', base64_encode('//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) );
$_SERVER['RET_URL'] = RET_URL; // exbds 통합 로그인 페이지
// $_SERVER['LOGIN_PAGE'] = '//'.SERVICE_DOMAIN.'?ret_url='.RET_URL; // exbds 통합 로그인 페이지
define('LOGIN_PAGE',$_SERVER['LOGIN_PAGE']);

header("Content-Type: text/html; charset=UTF-8");
header ("Cache-Control: no-cache, must-revalidate"); // 브라우져캐싱
header ("Pragma: no-cache"); // 브라우져캐싱
//error_reporting(E_ALL^E_NOTICE);
//error_reporting(0);
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
/**
 * request parameter중에 해킹에 사용되는 이름이 있는경우 애러처리합니다.
 * HcPcEgmp, kXvrbKwH
 */
function check_hacker_string($v) {
	if (is_array($v)) {
		foreach($v as $key => $val){
			$v[$key] = check_hacker_string($val);
		}
	} else {
		if(
			strpos($v, 'sample@email.tst')!==false ||
			strpos($v, 'HcPcEgmp')!==false ||
			strpos($v, 'kXvrbKwH')!==false
		) {
			exit('<script>alert("Unable to access");window.location.href="/";</script>');
		}
	}
}
$use_html_params = array('contents','contents_kr','contents_en','contents_cn','rplcontents'); // 태그 날리지 말것 확인.
foreach ($_POST as $key => $val) {
	check_hacker_string($val);
	if (!in_array($key, $use_html_params)) {
		$_POST[$key] = strip_tags_all($val);
	}
}
foreach ($_GET as $key => $val) {
	check_hacker_string($val);
	if (!in_array($key, $use_html_params)) {
		$_GET[$key] = strip_tags_all($val);
	}
}
foreach ($_REQUEST as $key => $val) {
	check_hacker_string($val);
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

// $support_lang = array('kr','en','cn','zh'); // @todo 번역 파일에서 추출하도록 수정 필요.
$support_lang = array('kr','en','cn'); // DB 컬럼에 사용되는 값(국가코드)로 지정(언어코드를 국가코드로 해서 환장함.)
define('SUPPORT_LANG', $support_lang);

//현재 선택된 언어
if(@$_COOKIE['lang']){
	$lang = $_SESSION['lang'] = $_COOKIE['lang'];
}else{
	$_GET['lang'] = in_array($_GET['lang'], SUPPORT_LANG) ? $_GET['lang'] : 'kr';
	$_GET['lang'] = $_GET['lang']=='ko' ? $_GET['lang'] : 'kr';
	$_GET['lang'] = $_GET['lang']=='zh' ? $_GET['lang'] : 'cn';
	$lang = $_SESSION['lang'] = $_GET['lang'];
	setcookie('lang', 'kr', null, '/');
}
switch($lang) {
	case 'ko': $lang = 'kr'; break;
	case 'zh': $lang = 'cn'; break;
	case 'zh-cn': $lang = 'cn'; break;
}
if(strpos($_SERVER["SCRIPT_NAME"],'/admin/')!==false) {
	$lang = 'kr';
}
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/language/i18n.class.php';
$i18n = new i18n($_SERVER["DOCUMENT_ROOT"].'/lib/language/lang/lang_'.$lang.'.json', $_SERVER["DOCUMENT_ROOT"].'/lib/language/langcache/', $lang,'Lang');
$i18n -> init();

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

define('WEB_URL',"//".$_SERVER["HTTP_HOST"]);
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

// echo '<!-- _GET[device_type]:'; var_dump($_GET['device_type']);  echo ' -->';// exit;
// echo '<!-- _SESSION[device_type]:'; var_dump($_SESSION);  echo ' -->';// exit;