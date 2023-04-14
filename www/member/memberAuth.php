<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once 'member_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Member($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'auth') {
	if(!empty($_SESSION['USER_ID'])) { alertGo('','/'); }
	if($config_basic['bool_ssl'] > 0) {
		$js->config['write_mode'] = 'post';
	}
	else {
		//ajaxCheck();
	}
	$js->auth();
}
else if($_GET['pg_mode'] == 'out') {
	$js->out();
}
else {
	if(!empty($_SESSION['USER_ID'])) { alertGo('','/'); }
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('form');
	$interface->setPlugIn('kakao_login');
	if ($tpl->skin=='shop') {       
		$interface->layout['js_tpl_left'] = 'js_left.html';
		$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?category';
		$interface->layout['js_tpl_main'] = 'member/auth_form.html';
	} else {
		$interface->layout['js_tpl_header'] = 'js_header.html';
		$interface->layout['js_tpl_main'] = 'member/auth_form.html';
	}
	if(!empty($_GET['ret_mode'])) {
		if($_GET['ret_mode'] == 'order') {
			$ret_url = base64_decode($_GET['ret_url']);
			$arr_url = parse_url($ret_url);
			$tpl->assign('url_query',$arr_url['query']);
		}
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>