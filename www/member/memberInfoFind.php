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

if($_POST['pg_mode'] == 'find_id') {
	ajaxCheck();
	$js->searchUserid();
}
else if($_POST['pg_mode'] == 'find_pw') {
	ajaxCheck();
	$js->serachUserpwd();
}
else if($_POST['pg_mode'] == 'reset_pw') {
	ajaxCheck();
	$js->resetPw();
}
else if($_POST['pg_mode'] == 'email_resend') {
	ajaxCheck();
	$js->emailResend();
}
else {
	// if(!empty($_SESSION['USER_ID'])) { alertGo('','/'); }
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('form');
	$interface->setPlugIn('popup');
	$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/member/css/search_form.css');
	if($_GET['pg_mode']=='resetpw') {
		$interface->addScript('/template/'.getSiteCode().'/'.$tpl->skin.'/js/resetpw.js');
		$interface->layout['js_tpl_main'] = 'member/resetpw.html';
	} else {
		if ($tpl->skin=='shop') {       
			$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?cscenter';
			$interface->layout['js_tpl_main'] = 'member/find_id.html';
		} else {
			$interface->layout['js_tpl_main'] = 'member/find_info.html';
		}
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();