<?php

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;

/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
/*********************************************
에레메세지 출력 관련 처리함수
*********************************************/
function ajaxMsg($val,$msg='',$encode=FALSE)
{
	if(empty($msg)) { echo '['.$val.']'; }
	else {
		if($encode) { $msg = urlencode($msg); }
		$msg = addslashes($msg);
		echo '['.$val.',\''.$msg.'\']';
	}
	exit();
}

function jsonMsg($bool_value,$msg='',$msg1='',$msg2='')
{
	include_once($_SERVER['DOCUMENT_ROOT'].'/lib/json_class.php');
	$json = new Services_JSON();
	$arr = array();
	$arr["bool"] = $bool_value;
	if(!empty($msg)) { $arr["msg"] = $msg; }
	if(!empty($msg1)) { $arr["msg1"] = $msg1; }
	if(!empty($msg2)) { $arr["msg2"] = $msg2; }
	echo $json->encode($arr);
	exit();
}

function alert($msg,$close=0)
{
	$msg = addslashes($msg);
	echo '<script>';
	echo 'alert("'.$msg.'");';
	if($close) { echo 'self.close();'; }
	echo '</script>';
}

function errMsg($msg,$back=1)
{
	$msg = addslashes($msg);
	echo '<script>';
	echo 'alert("'.$msg.'");';
	if($back) { echo 'history.back();'; }
	echo '</script>';
	exit();
}

function responseFail($msg) {
	if($GLOBALS['config_basic']['bool_ssl'] > 0) {
		errMsg($msg);
	} else {
		jsonMsg(0, $msg);
	}
}

function responseSuccess($msg='', $url='') {
	if($GLOBALS['config_basic']['bool_ssl'] > 0) {
		replaceGo($url, $msg);
	} else {
		jsonMsg(1, $msg);
	}
}

function errWinClose($msg)
{
	$msg = addslashes($msg);
	echo '<script>';
	echo 'alert("'.$msg.'");';
	echo 'self.close();';
	echo '</script>';
	exit();
}

function alertGo($msg,$url=0,$target='self',$close=0,$w=100,$h=100,$t=0,$l=0,$s='no')
{
	echo '<script>';
	if($msg) { echo 'alert("'.$msg.'");'; }
	if($target == 'winopen') { echo "window.open('$url','','width=$w,height=$h,top=$t,left=$l,scrollbars=$s');"; }
	else { if($url) { echo 'window.'.$target.'.location.replace("'.$url.'");'; }}
	if($close) { echo 'self.close();'; }
	echo '</script>';
	exit();
}

function confirmGo($msg,$url,$target='self',$back=0,$close=0)
{
	echo '<script>';
	echo 'if(confirm("'.$msg.'")) window.'.$target.'.location.replace("'.$url.'");';
	if($back) { echo 'else history.back();'; }
	else if($close) { echo 'close();'; }
	else { echo 'else return;'; }
	echo '</script>';
	exit();
}

function replaceGo($ret_url='', $msg='')
{
	echo '<script>';
	if($msg) { echo 'alert("'.$msg.'");'; }
	if($ret_url) {
	echo 'location.replace("'.$ret_url.'");';
	} else {
		echo 'location.reload();';
	}
	echo '</script>';
}

function decodeGo($ret_url)
{
	$ret_url = base64_decode($ret_url);
	echo '<script>';
	echo 'location.replace("'.$ret_url.'");';
	echo '</script>';
}
/********************************************
ajax 실행시 비정상적인 접근을 막는 함수
********************************************/

//비회원 허용하는 경우 사용
function ajaxCheck()
{
	$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
	$referer = $arr_referer['host'];
	if($referer != $_SERVER['SERVER_NAME']) {
		echo Lang::main_basic9;
		exit();
	}
}

function loadCheck()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
		$referer = $arr_referer['host'];
		if($referer != $_SERVER['SERVER_NAME']) {
			errMsg(Lang::main_basic9);
		}
	}
}

//회원전용 페이지에서 사용
function checkUser($ret_url='')
{
	if(empty($_SESSION['USER_ID'])) {
		if(empty($ret_url)) {
			// alertGo('','/login/'.base64_encode($_SERVER['REQUEST_URI']));
			alertGo('',LOGIN_PAGE);
		}
		else {
			// alertGo('','/login/'.$ret_url);
			alertGo('', preg_replace('/ret_url=.[^&]*/', 'ret_url='.$ret_url,LOGIN_PAGE));
		}
	}
}

//Trade 회원전용 페이지에서 사용
function checkUserTrade()
{
    if(empty($_SESSION['USER_REALNAME']) || $_SESSION['USER_REALNAME'] == '0') {
            alertGo('You need to authenticate.','/certification');
    }
}

function ajaxCheckUser()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		if(empty($_SESSION['USER_ID'])) {
			jsonMsg(0,'err_sess');
		}
		else {
			$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
			$referer = $arr_referer['host'];
			if($referer != $_SERVER['SERVER_NAME']) {
				echo Lang::main_basic9;
				exit();
			}
		}
	}
}

function loadCheckUser()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		if(empty($_SESSION['USER_ID'])) {
			alertGo('','/member/memberAuth.php?ret_url='.base64_encode($_SERVER['HTTP_REFERER']));
		}
		else {
			$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
			$referer = $arr_referer['host'];
			if($referer != $_SERVER['SERVER_NAME']) {
				errMsg(Lang::main_basic9);
			}
		}
	}
}

//관리자 모드

function checkAdmin($kind_right='',$ret_url='')
{
	if(empty($_SESSION['ADMIN_ID'])) {
		if(empty($ret_url)) {
			alertGo('','/admin/auth.php?ret_url='.base64_encode($_SERVER['REQUEST_URI']));
		}
		else {
			alertGo('','/admin/auth.php?ret_url='.$ret_url);
		}
	}
}

function checkRight($kind_right) {
	global $dbcon;
	$kind_right = preg_replace('/[a-zA-Z1-0_]/','',$kind_right);
	$adminid = $_SESSION['ADMIN_ID'];
	$right_value = $dbcon->query_unique_value("select {$kind_right} from js_admin where adminid='{$dbcon->escape($adminid)}' ");
	if($right_value != 1){
		responseFail('사용권한이 없습니다. 로그인 계정을 확인해주세요.');
	}
}

function ajaxCheckAdmin()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		if(empty($_SESSION['ADMIN_ID'])) {
			jsonMsg(0,'err_sess');
		}
		else {
			$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
			$referer = $arr_referer['host'];
			if($referer != $_SERVER['SERVER_NAME']) {
				echo Lang::main_basic9;
				exit();
			}
		}
	}
}

function loadCheckAdmin()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		if(empty($_SESSION['ADMIN_ID'])) {
			alertGo('','/admin/auth.php?ret_url='.base64_encode($_SERVER['HTTP_REFERER']));
		}
		else {
			$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
			$referer = $arr_referer['host'];
			if($referer != $_SERVER['SERVER_NAME']) {
				errMsg(Lang::main_basic9);
			}
		}
	}
}

//Staff 모드

function checkStaff($kind_right='',$ret_url='')
{
	if(empty($_SESSION['STAFF_ID'])) {
		if(empty($ret_url)) {
			alertGo('','/staff/auth.php?ret_url='.base64_encode($_SERVER['REQUEST_URI']));
		}
		else {
			alertGo('','/staff/auth.php?ret_url='.$ret_url);
		}
	}
}

function ajaxCheckStaff()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		if(empty($_SESSION['STAFF_ID'])) {
			jsonMsg(0,'err_sess');
		}
		else {
			$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
			$referer = $arr_referer['host'];
			if($referer != $_SERVER['SERVER_NAME']) {
				echo Lang::main_basic9;
				exit();
			}
		}
	}
}

function loadCheckStaff()
{
	if(empty($_SERVER['HTTP_REFERER'])) {
		errMsg(Lang::main_basic9);
	}
	else {
		if(empty($_SESSION['STAFF_ID'])) {
			alertGo('','/staff/auth.php?ret_url='.base64_encode($_SERVER['HTTP_REFERER']));
		}
		else {
			$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
			$referer = $arr_referer['host'];
			if($referer != $_SERVER['SERVER_NAME']) {
				errMsg(Lang::main_basic9);
			}
		}
	}
}



/*********************************************
각종 코드를 생성하는 함수
*********************************************/
//영문자 숫자 조합으로 출력
function getCode()
{
	$arr = array();
	for($i=0;$i<6 ;$i++) { $arr[] = chr(mt_rand(65,90)); }
	$ret = implode('',$arr).mt_rand(1000000,9999999);
	return $ret;
}

//숫자만을 출력
function randCode()
{
	mt_srand((double)microtime()*1000000);
	$ret = time().mt_rand(10000,99999);
	return $ret;
}

function getArrayKeys($arr,$value)
{
	foreach($arr as $key=>$val) {
		if(gettype($val) == 'array') {
			if( in_array($value,$arr[$key])) {
				return $key;
			}
		}
		else {
			if($val==$value) {
				return $key;
			}
		}
	}
}

function chkSid($sid1,$sid2)
{
	$ret = 1;
	$sid = $sid1.$sid2;
	$len = strlen($sid);
	if ($len <> 13) {
		$ret = 0;
	}
	if (!ereg('^[[:digit:]]{6}[1-4][[:digit:]]{6}$', $sid)) {
		$ret = 0;
	}
	$birth_year = ('3' < $sid[6]) ? '19' : '20';
	$birth_year += substr($sid, 0, 2);
	$birth_month = substr($sid, 2, 2);
	$birth_date = substr($sid, 4, 2);
	if (!checkdate($birth_month, $birth_date, $birth_year)) {
		$ret = 0;
	}
	for ($i = 0; $i < 13; $i++) {
		$buf[$i] = (int) $sid[$i];
	}
	$multipliers = array(2,3,4,5,6,7,8,9,2,3,4,5);
	for ($i = $sum = 0; $i < 12; $i++) {
		$sum += ($buf[$i] *= $multipliers[$i]);
	}
	if ((11 - ($sum % 11)) % 10 != $buf[12]) {
		$ret = 0;
	}
	return $ret;
}

function infoSid($sid1,$sid2)
{
	$arr = array();
	$sex = substr($sid2,0,1);
	$yy_1 = $sex < 3 ? 19 : 20;
	$yy_2 = substr($sid1,0,2);
	$weight = $sex > 2 ? 1:0;
	$age = date("Y", time()) - (1900+($weight*100)+$yy_2);
	$age++;
	$arr['sex'] = $sex%2 == 1? 'm' : 'f';
	$arr['year']=$yy_1.$yy_2;
	$arr['age'] = $age;
	$arr['adult'] = $arr['age'] >18 ? 1 : 0;
	$arr['mon']=substr($sid1,2,2);
	$arr['day']=substr($sid1,4,2);
	return $arr;
}

function getAge($input,$div=0)
{
	$yy = substr($input,0,2);
	$weight = $div > 2 ? 1:0;
	$age = date("Y", time()) - (1900+($weight*100)+$yy);
	$age++;
	return $age;
}

function chkCid($cid)
{
	$ret = 1;
	$weight = '137137135';
	$len = strlen($cid);
	$sum = 0;
	if ($len <> 10) { $ret = 0; }
	for ($i = 0; $i < 9; $i++) { $sum = $sum + (substr($cid,$i,1)*substr($weight,$i,1)); }
	$sum = $sum + ((substr($cid,8,1)*5)/10);
	$rst = $sum%10;
	if ($rst == 0) { $result = 0; }
	else { $result = 10 - $rst; }
	$saub = substr($cid,9,1);
	if ($result <> $saub) { $ret = 0; }
	return $ret;
}

function removeBadTag($input)
{
	$pattern = array(
		"@<(script|style|head|object|marquee)[^>]*?>.*?</\s*\1\s*>@is",
		"@<meta*?[^>]*?>@is",
		"@<iframe*?[^>]*?>@is",
		"@</\s*iframe\s*>@is",
		"@<object*?[^>]*?>@is",
		"@</object>@is",
		"@<body*?[^>]*?>@is",
		"@</\s*body>@is",
		"@<html\s*>@is",
		"@</\s*html\s*>@is",
	);
	$replace = array('','','','','','','','','','');
	return $input = preg_replace($pattern,$replace,$input);
}

function cutStr($text, $length, $suffix='...',$div='utf-8')
{
	if($div == 'euc-kr') {
		if (strlen($text) <= $length) { return $text; }
		$cpos = $length - 1;
		$count_2B = 0;
		$lastchar = $text[$cpos];
		while (ord($lastchar)>127 && $cpos>=0) {
			$count_2B++;
			$cpos--;
			$lastchar = $text[$cpos];
		}
		return substr($text,0,(($count_2B % 2)?$length-1:$length)).$suffix;
	}
	else {
		if(!$text || !$length) { return $text; }
		if(function_exists('iconv')) {
			$unicode_str = iconv("UTF-8","UCS-2",$text);
			if(strlen($unicode_str) < $length*2) { return $text; }
			$output_str = substr($unicode_str, 0, $length*2);
			return iconv("UCS-2","UTF-8",$output_str).$suffix;
		}
		$arr = array();
		return preg_match('/.{'.$length.'}/su', $text, $arr) ? $arr[0].$suffix : $text;
	}
}

function dateTrans($month="",$day="",$year="")
{
	$arr = array();
	$arr_today = getdate(time());
	if(empty($month)) { $month = $arr_today['mon']; }
	if(empty($day)) { $day = $arr_today['mday']; }
	if(empty($year)) { $year = $arr_today['year']; }
	$date = mktime(0,0,0,$month,$day,$year);
	$arr_date = getdate($date);
	$n = 1;
	while(checkdate($arr_date['mon'],$n,$arr_date['year'])) { $n++; }
	$arr['cnt_day'] = --$n;
	if($arr_date['wday'] == 0) { $dayName = '일'; }
	else if($arr_date['wday'] == 1) { $dayName = '월'; }
	else if($arr_date['wday'] == 2) { $dayName = '화'; }
	else if($arr_date['wday'] == 3) { $dayName = '수'; }
	else if($arr_date['wday'] == 4) { $dayName = '목'; }
	else if($arr_date['wday'] == 5) { $dayName = '금'; }
	else if($arr_date['wday'] == 6) { $dayName = '토'; }
	$arr['dayName'] = $dayName;
	return $arr;
}

function chkUrl($input)
{
	$input = strtolower($input);
	if(preg_match('@^(http://)\S+$@i',$input)) { }
	else if(preg_match('@^[^(http://)]\S+$@i',$input)) { $input = 'http://'.$input; }
	else { $input = ''; }
	return $input;
}

function listDir($dir)
{
	chdir($dir);
	if(!($dp = opendir($dir))) { echo('지정한 폴더를 열수 없습니다.'); }
	$no = 1;
	$arr = array();
	while($file = readdir($dp)) {
		if(is_dir($file)) {
			if($file != '.' && $file != '..') {
				$arr[$no] = $file;
				$no++;
			}
		}
	}
	closedir($dp);
	return $arr;
}

function listFile($dir)
{
	chdir($dir);
	if(!($dp = opendir($dir))) { echo('지정한 폴더를 열수 없습니다.'); }
	$arr = array();
	while($file = readdir($dp)) {
		if(is_file($file)) { if($file != '.' && $file != '..') { $arr[] = $file; }}
	}
	closedir($dp);
	return $arr;
}

function AnigifCheck($path){
	$str = @file_get_contents($path);
	$strChk = true;
	$frameCnt = $idx = 0;
	$gifFrame = chr(hexdec("0x21")).chr(hexdec("0xF9")).chr(hexdec("0x04"));
	$gfLenth = strlen($gifFrame);
	if(strlen($str) <= 0 ){
		return FALSE;
		exit;
	}
	while( $strChk == true ){
		if( strpos($str, $gifFrame,$idx) ){
			$frameCnt++;
			$idx = strpos($str, $gifFrame,$idx)+$gfLenth;
			$strChk = true;
		}
		else if( $frameCnt >= 3 || !strpos($str, $gifFrame,$idx) ){
			break;
		}
	}
	return $frameCnt > 1 ? TRUE : FALSE;
}

/* 기본설정사항을 가지고 온다.*/
/*
js_config_account
js_config_basic
js_config_delivery
js_config_delivery_company
js_config_delivery_region
js_config_email
js_config_payment
*/
function getSiteCode($domain='')
{
	$dbcon = $GLOBALS['dbcon'];
	$code = $_SESSION['__SITECODE__'];
	if(! $_SESSION['__SITECODE__']) {
		$domain = $domain ? $domain : $_SERVER['HTTP_HOST'];
        echo $domain;
		$query = "select code from js_config_site where domain='".$dbcon->escape($domain)."' ";
		$code = $dbcon->query_unique_value($query,__FILE__,__LINE__);
	}
	$_SESSION['__SITECODE__'] = $code;
	if($_COOKIE['sitecode']!=$code) {
		setcookie('sitecode', $code, null, '/');
		$_COOKIE['sitecode']=$code;
	}
	return $code;
}

function getConfig($table_name,$fields='')
{
	$dbcon = $GLOBALS['dbcon'];
	$query = array();
	$query['table_name'] = $table_name;
	$query['tool'] = 'row';
	if(empty($fields)) {
		$query['fields'] = '*';
	}
	else {
		$query['fields'] = $fields;
	}
	$query['where'] = " WHERE code='".getSiteCode()."' ";
	$row = $dbcon->query($query,__FILE__,__LINE__);
	return $row;
}

function getSymbolactive($symbol='')
{
	$dbcon = $GLOBALS['dbcon'];
	$query = "select active from js_trade_currency where symbol='".$dbcon->escape($symbol)."' ";
	$active = $dbcon->query_unique_value($query,__FILE__,__LINE__);
	if($symbol=='BDC') { $active = 'N';} // 메뉴클릭 안되게
	return $active;
}

function loopConfig($table_name,$fields='')
{
	$dbcon = $GLOBALS['dbcon'];
	$query = array();
	$query['table_name'] = $table_name;
	$query['tool'] = 'select';
	if(empty($fields)) {
		$query['fields'] = '*';
	}
	else {
		$query['fields'] = $fields;
	}
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$ret = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$ret[] = $row;
	}
	return $ret;
}

function analysisVisitant()
{
	global $dbcon;

	$qry = array();
	if(!empty($_SESSION['USER_ID']))  { $qry[] = 'userid=\''.$_SESSION['USER_ID'].'\''; }
	if(!empty($_SERVER['HTTP_REFERER'])) { $qry[] = 'access_path=\''.$_SERVER['HTTP_REFERER'].'\''; }
	else { $qry[] = 'access_path=\''.$_SERVER['SCRIPT_NAME'].'\''; }

	$arr = checkAgent();
	$qry[] = 'browser=\''.$arr['browser'].'\'';
	$qry[] = 'os=\''.$arr['os'].'\'';
	$qry[] = 'ipaddr=\''.$_SERVER["REMOTE_ADDR"].'\'';
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	$qry = implode(',',$qry);

	$query = array();
	$query['table_name'] = 'js_analysis_visitant';
	$query['tool'] = 'insert';
	$query['fields'] = $qry;
	if ($_SERVER["REMOTE_ADDR"]!='112.175.14.26') {
		$dbcon->query($query,__FILE__,__LINE__);
	}
}

function checkAgent()
{
	global $HTTP_SERVER_VARS;
	$OS    = array(
		/* PC */
		array('Windows CE', 'Windows CE'),
		array('Win98', 'Windows 98'),
		array('Windows 9x', 'Windows ME'),
		array('Windows me', 'Windows ME'),
		array('Windows 98', 'Windows 98'),
		array('Windows 95', 'Windows 95'),
		array('Windows NT 6', 'Windows Vista'),
		array('Windows NT 5.2', 'Windows 2003/XP x64'),
		array('Windows NT 5.01', 'Windows 2000 SP1'),
		array('Windows NT 5.1', 'Windows XP'),
		array('Windows NT 5', 'Windows 2000'),
		array('Windows NT', 'Windows NT'),
		array('Macintosh', 'Macintosh'),
		array('Mac_PowerPC', 'Mac PowerPC'),
		array('Unix', 'Unix'),
		array('bsd', 'BSD'),
		array('Linux', 'Linux'),
		array('Wget', 'Linux'),
		array('windows', 'ETC Windows'),
		array('mac', 'ETC Mac'),
		/* MOBILE */
		array('PSP', 'PlayStation Portable'),
		array('Symbian', 'Symbian PDA'),
		array('Nokia', 'Nokia PDA'),
		array('LGT', 'LG Mobile'),
		array('Android', 'Android'),
		array('Mobile', 'ETC Mobile'),
		/* WEB ROBOT */
		array('Googlebot', 'GoogleBot'),
		array('OmniExplorer', 'OmniExplorerBot'),
		array('MJ12bot', 'majestic12Bot'),
		array('ia_archiver', 'Alexa(IA Archiver)'),
		array('Yandex', 'Yandex bot'),
		array('Inktomi', 'Inktomi Slurp'),
		array('Giga', 'GigaBot'),
		array('Jeeves', 'Jeeves bot'),
		array('Planetwide', 'IBM Planetwide bot'),
		array('bot', 'ETC Robot'),
		array('Crawler', 'ETC Robot'),
		array('library', 'ETC Robot')
	);

	$BW = array(
		/* MOBILE */
		array('PSP', 'PlayStation Portable'),
		array('Symbian', 'Symbian PDA'),
		array('Nokia', 'Nokia PDA'),
		array('LGT', 'LG Mobile'),
		array('Android', 'Android'),
		array('Mobile', 'Mobile'),
		array('mobile', 'Mobile'),
		/* BROWSER */
		array('MSIE 11', 'MSIE 11'),
		array('MSIE 10', 'MSIE 10'),
		array('MSIE 9', 'MSIE 9'),
		array('MSIE 8', 'MSIE 8'),
		array('MSIE 7', 'MSIE 7'),
		array('MSIE 6', 'MSIE 6'),
		array('MSIE 5', 'MSIE 5'),
		array('MSIE 4', 'MSIE 4'),
		array('MSIE 3', 'MSIE 3'),
		array('MSIE 2', 'MSIE 2'),
		array('MSIE', 'ETC InternetExplorer'),
		array('Firefox', 'FireFox'),
		array('Chrome', 'Chrome'),
		array('Safari', 'Safari'),
		array('Opera', 'Opera'),
		array('Lynx', 'Lynx'),
		array('LibWWW', 'LibWWW'),
		array('Konqueror', 'Konqueror'),
		array('Internet Ninja', 'Internet Ninja'),
		array('Download Ninja', 'Download Ninja'),
		array('WebCapture', 'WebCapture'),
		array('LTH', 'LTH Browser'),
		array('Gecko', 'Gecko compatible'),
		array('Mozilla', 'Mozilla compatible'),
		array('wget', 'Wget command'),
		/* WEB ROBOT */
		array('Googlebot', 'GoogleBot'),
		array('OmniExplorer', 'OmniExplorerBot'),
		array('MJ12bot', 'majestic12Bot'),
		array('ia_archiver', 'Alexa(IA Archiver)'),
		array('Yandex', 'Yandex bot'),
		array('Inktomi', 'Inktomi Slurp'),
		array('Giga', 'GigaBot'),
		array('Jeeves', 'Jeeves bot'),
		array('Planetwide', 'IBM Planetwide bot'),
		array('bot', 'ETC Robot'),
		array('Crawler', 'ETC Robot')
	);

	foreach($OS as $val) {
		if(preg_match('/^.*('.$val[0].').*$/i', $_SERVER['HTTP_USER_AGENT'])) {
			$os_name = $val[1];
			break;
		}
	}

	foreach($BW as $val) {
		if(preg_match('/^.*('.$val[0].').*$/i', $_SERVER['HTTP_USER_AGENT'])) {
			$browser_name = $val[1];
			break;
		}
	}

	if(empty($os_name)) { $os_name = 'NONE'; }
	if(empty($browser_name)) { $browser_name = 'NONE'; }

	$ret = array(
		"os" => $os_name,
		"browser" => $browser_name,
	);
	return $ret;
}

/***********************************
문자나라 sms 전송을 위한 함수
http://www.munjanara.co.kr/MSG
******입력값 배열로 넘김**************
tran_phone
tran_msg
***********************************/
/*
function sendSms($msg_data) {
	global $dbcon;
	$row = getConfig('js_config_sms','tran_callback, guest_no, guest_key');
	$row['tran_callback'] = str_replace('-','',$row['tran_callback']);
	$msg_data['tran_phone'] = str_replace('-','',$msg_data['tran_phone']);
	$userid = $row['guest_no'];           // 문자나라 아이디
	$passwd = $row['guest_key'];           // 문자나라 비밀번호



	$hpSender = $row['tran_callback'];         // 보내는분 핸드폰번호
	$adminPhone = $row['tran_callback'];       // 비상시 메시지를 받으실 관리자 핸드폰번호
	$hpReceiver = $msg_data['tran_phone'];       // 받는분의 핸드폰번호
	$hpMesg = $msg_data['tran_msg'];           // 메시지
	$hpMesg = iconv("UTF-8", "EUC-KR",$hpMesg);
	$hpMesg = urlencode($hpMesg);




	$endAlert = 0;  // 전송완료알림창 ( 1:띄움, 0:안띄움 )
	$url = "/MSG/send/web_admin_send.htm?userid=$userid&passwd=$passwd&sender=$hpSender&receiver=$hpReceiver&encode=1&end_alert=$endAlert&message=$hpMesg";
	$fp = fsockopen("211.233.20.184", 80, $errno, $errstr, 10);
	if(!$fp) {
		//echo "$errno : $errstr";
		return false;
	}
	fwrite($fp, "GET $url HTTP/1.0\r\nHost: 211.233.20.184\r\n\r\n");
	$flag = 0;
	$out = '';
	while(!feof($fp)){
		$row = fgets($fp, 1024);
		if($flag) {
			$out .= $row;
		}
		if($row=="\r\n") {
			$flag = 1;
		}
	}
	fclose($fp);
	$send_result = explode('|',trim($out));
	if($send_result[0] == '9') {
		$ret = true;
		$result_msg = '성공';
	}
	else if($send_result[0] == '1') {
		$ret = false;
		$result_msg = '필수전달값이 빠짐';
	}
	else if($send_result[0] == '2') {
		$ret = false;
		$result_msg = '존재하지 않는 아이디';
	}
	else if($send_result[0] == '3') {
		$ret = false;
		$result_msg = '비밀번호 인증실패';
	}
	else if($send_result[0] == '4') {
		$ret = false;
		$result_msg = '잔액이 충분하지 않음';
	}
	else if($send_result[0] == '5') {
		$ret = false;
		$result_msg = '받는 번호가 잘못됨';
	}
	else if($send_result[0] == '6') {
		$ret = false;
		$result_msg = '보내는 번호가 잘못됨';
	}
	else if($send_result[0] == '7') {
		$ret = false;
		$result_msg = '서비스 이용이 중지된 아이디';
	}
	else {
		$ret = false;
		$result_msg = '전송실패';
	}

	$qry = array();
	$qry[] = 'tran_phone=\''.$msg_data['tran_phone'].'\'';
	$qry[] = 'tran_callback=\''.$row['tran_callback'].'\'';
	//$qry[] = 'tran_date=\''.$arr['tran_date'].'\'';
	$qry[] = 'tran_msg=\''.$msg_data['tran_msg'].'\'';
	$qry[] = 'tran_result=\''.$result_msg.'\'';
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	$qry = implode(',',$qry);

	$query = array();
	$query['table_name'] = 'js_sms';
	$query['tool'] = 'insert';
	$query['fields'] = $qry;
	$dbcon->query($query,__FILE__,__LINE__);

	return $ret;
}
*/

/**
 * send sms international
 * https://www.twilio.com/console
 */
function sendSmsInternational($sid, $token, $from, $to, $msg) {
	if(trim($msg)=='' || trim($to)=='' || !$sid || !$token || !$from) {
		return false;
	}
	// +82 처럼 +가 없으면 +를 붙여준다
	if(strpos($to,'+')!==0) {
		$to = '+'.$to;
	}
	// Your Account SID and Auth Token from twilio.com/console
	$client = new Client($sid, $token);
	// Use the client to do fun stuff like send text messages!
	$_r = '';
    try {
		$call = $client->messages->create(
			// the number you'd like to send the message to
			$to,
			array(
				// A Twilio phone number you purchased at twilio.com/console
				'from' => $from, //'+18782052142',
				// the body of the text message you'd like to send
				'body' => $msg
			)
		);
		$_r = $call->sid;
    } catch (Exception $e) {}
	return $_r;
}

function sendSms($msg_data)
{
	global $dbcon;

	$row = getConfig('js_config_sms','tran_callback, guest_no, guest_key');
	$row['tran_callback'] = str_replace('-','',$row['tran_callback']);
	$msg_data['tran_phone'] = str_replace('-','',$msg_data['tran_phone']);
	//$userid = $row['guest_no'];           // 문자나라 아이디
	//$passwd = $row['guest_key'];           // 문자나라 비밀번호
	//$hpSender = $row['tran_callback'];         // 보내는분 핸드폰번호
	//$adminPhone = $row['tran_callback'];       // 비상시 메시지를 받으실 관리자 핸드폰번호
	//$hpReceiver = $msg_data['tran_phone'];       // 받는분의 핸드폰번호
	//$hpMesg = $msg_data['tran_msg'];           // 메시지
	//$msg_data['tran_msg'] = iconv("UTF-8", "EUC-KR",$msg_data['tran_msg']);
	$tran_date = empty($msg_data['tran_date']) ? '' : $msg_data['tran_date'];

	// 한국 neosolution.com 사용 코드
	// $snoopy = new Snoopy;
	// $_tran_phone = urlencode($msg_data['tran_phone']);
	// $_tran_callback = urlencode($row['tran_callback']);
	// $_tran_date = urlencode($tran_date);
	// $_tran_msg = urlencode($msg_data['tran_msg']);
	// $_guest_no = urlencode($row['guest_no']);
	// $_guest_key = urlencode($row['guest_key']);
	// $cmd = "SendSms";
	// $method = "GET";
	// $url = "http://www.nesolution.com/service/sms.aspx?cmd=$cmd&method=$method&";
	// $url = $url . "guest_no={$_guest_no}&guest_key={$_guest_key}&tran_phone={$_tran_phone}&";
	// $url = $url . "tran_callback={$_tran_callback}&tran_date={$_tran_date}&tran_msg={$_tran_msg}";
	// $snoopy->fetchtext($url);
	// $send_result = iconv('EUC-KR', 'UTF-8', $snoopy->results);

	// 글로벌용 twilio.com 사용 코드
	$_tran_phone = preg_replace('/[^0-9\+]/', '', $msg_data['tran_phone']);
	$_tran_msg = $msg_data['tran_msg'];
	$_guest_no = $row['guest_no'];
	$_guest_key = $row['guest_key'];
	$_tran_callback = $row['tran_callback'];
	$send_result = sendSmsInternational($_guest_no, $_guest_key, $_tran_callback, $_tran_phone, $_tran_msg);
	if(!$send_result) {
	    return false;
	}

	$qry = array();
	$qry[] = 'tran_phone=\''.$dbcon->escape($msg_data['tran_phone']).'\'';
	$qry[] = 'tran_callback=\''.$dbcon->escape($row['tran_callback']).'\'';
	//$qry[] = 'tran_date=\''.$dbcon->escape($arr['tran_date']).'\'';
	$qry[] = 'tran_msg=\''.$dbcon->escape($msg_data['tran_msg']).'\'';
	$qry[] = 'tran_result=\''.$dbcon->escape($send_result).'\'';
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	$qry = implode(',',$qry);

	$query = array();
	$query['table_name'] = 'js_sms';
	$query['tool'] = 'insert';
	$query['fields'] = $qry;
	return $dbcon->query($query,__FILE__,__LINE__);

}


function genPaging($p_total, $p_page=1, $p_append='', $p_cnt_rows=10, $p_cnt_pages=10, $p_page_name='page',$justify='')
{

	// 페이징 박스 정렬용 
	if($justify == 'center') {// 가운데정렬
		$justify = 'justify-content-center';
	} else if($justify == 'end') { // 오른쪽 정렬
		$justify = 'justify-content-end';
	} else { // 왼쪽 정렬(기본)
		$justify = '';
	}

	$_max_pages = '';
	$_max_pages = ceil($p_total / $p_cnt_rows );
	$_php_self = htmlspecialchars($_SERVER['PHP_SELF'] );


	$_return = '<ul class="pagination '.$justify.'">';

	// 첫페이지로
	if ($p_total > 0 && $p_page > 1) {
		$_return .= '<li class="page-item"><a href="?page=1" class="page-link">First</a></li>';
	} else {
		$_return .= '<li class="page-item disabled"><a href="javascript:;" class="page-link">First</a></li>';
	}

	// 이전페이지로
	if ($p_total > 0 && $p_page > 1) {
		$_return .= '<li class="page-item"><a href="?page=' . ($p_page - 1) . '" class="page-link">Previous</a></li>';
	} else {
		$_return .= '<li class="page-item disabled"><a href="javascript:;" class="page-link">Previous</a></li>';
	}

	// 중간 페이지 번호영역
	$batch = ceil($p_page / $p_cnt_pages );
	$end = $batch * $p_cnt_pages;
	if ($end == $p_page) {
		//$end = $end + $p_cnt_pages - 1;
	//$end = $end + ceil($p_cnt_pages/2);
	}
	if ($end > $_max_pages) {
		$end = $_max_pages;
	}
	$start = $end - $p_cnt_pages + 1;
	$start = ($start<1) ? 1 : $start;
	$links = '';

	for($i = $start; $i <= $end; $i ++) {
		if ($i == $p_page) {
			$_return .= '<li class="page-item active"><a class="page-link current" href="#"><strong>'.$i.'</strong></a></li>';
		} else {
			$_return .= '<li class="page-item"><a href="?page=' . $i . '" class="page-link">'.$i.'</a></li>';
		}
	}

	// 다음페이지로
//	$_return .= '<a href="javascript:;" class="direction prev"><span></span> 이전</a>';
	if ($p_total > 0 && $p_page < $_max_pages) {
		$_return .= '<li class="page-item"><a href="?page=' . ($p_page + 1) . '" class="page-link" >Next</a></li>';
	} else {
		$_return .= '<li class="page-item disabled"><a href="javascript:;" class="page-link">Next</a></li>';
	}

	// 마지막 페이지
	if ($p_total > 0 && $p_page == $_max_pages) {
		$_return .= '<li class="page-item disabled"><a class="page-link" href="javascript:;">Last</a></li>';
	} else {
		$_return .= '<li class="page-item"><a href="?page=' . $_max_pages . '" class="page-link">Last</a></li>';
	}

	$_return .= '</ul>';

	return $_return;

}

/**
 * 실수의 소숫점 아래부분의 0000은 삭제하고 콤마를 붙인다.
 * @param type $p_float
 * @return type
 */
function clean_float_number($p_float=0, $p_showsign=true) {
	if(empty($p_float)) {
		return '';
	}
	if(is_numeric($p_float)) {
		$p_float *= 1;
		$sign = $p_float > 0 ? '+' : '-';
	} else {
		$sign = strpos($p_float, '-')!==false ? '-' : '+';
		$p_float = preg_replace('/[^0-9.]/', '', $p_float);
		$p_float = $p_float*1;
	}
	$cnt = strpos($p_float, '.')!==false ? strlen(substr($p_float, strpos($p_float,'.')+1)) : 0;
	if($p_showsign) {
		return $sign.' '.number_format($p_float, $cnt);
	} else {
		return number_format($p_float, $cnt);
	}
}

/**
 * 마이크로 타임(초) 숫자 생성
 * Unix Timestamp 기반 소숫점 6자리 숫자 생성 후 소숫점 제거.  1630574144.670157  --> 1630574144670157
 */
function gen_microtime() {
	return str_replace('.','',sprintf('%01.6f', array_sum(explode(' ',microtime())))); // 16자리 milliseconds
}

/**
 * 중복없는 아이디(대문자) 생성
 * @param String $prefix. 접두사
 * @param String $subfix. 점미사
 */
function gen_id($prefix='', $subfix='') {
	$time = gen_microtime();
	return strtoupper($prefix.base_convert($time, 10, 36).$subfix); // 36진법으로 변경
}
