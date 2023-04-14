<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './sms_class.php';
include_once '../lib/Snoopy.class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new SMS($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

/************
kind_target
************
am: 전체회원
sm: 선택회원
as: 전체학생
ss : 선택학생
ak : 기수전체
sk : 기수선택
ac : 해당반 전체
sc : 해당반 선택
************/

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'send_sms') {
	ajaxCheckAdmin();
	if(!empty($_POST['kind_target'])) {
		if($_POST['kind_target'] == 'am' || $_POST['kind_target'] == 'as') {
			$js->allSms();
		}
		else {
			$js->memberSendSms();
		}
	}
	else {
		$js->memberSendSms();
	}
}
else if($_GET['pg_mode'] == 'del_multi') {
	ajaxCheckAdmin();
	$js->delMulti();
}
else { 
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;


	if($_GET['pg_mode'] == 'form_sms') {
		$interface->setBasicInterface('admin','iframe');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_contents'] = 'marketing/sendSms_form.html';
	}
	else if($_GET['pg_mode'] == 'form_sms2') {
		$interface->setBasicInterface('admin','iframe');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_contents'] = 'marketing/sendSms2_form.html';
		$js->setSmsTarget();
	}
	else {
		$interface->setBasicInterface('admin');
		$interface->addNavi(getNavi());
		$interface->layout['js_tpl_left'] = 'menu.html?marketing';
		if($_GET['pg_mode'] == 'list') {
			$tpl->assign('cur_page','marketing06');
			$interface->layout['js_tpl_main'] = 'marketing/sms_list.html';
			$js->lists();
		}
		else {
			$tpl->assign('cur_page','marketing05');
			$interface->setPlugIn('atools');
			$interface->setPlugIn('form');
			$interface->layout['js_tpl_main'] = 'marketing/sms_form.html';
			$js->viewForm();
		}
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>