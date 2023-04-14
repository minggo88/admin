<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './email_class.php';

function getNavi()
{
	$ret = array(
		'마케팅관리'=>'/admin/status.php',
		'<span class="link">자동메일설정</span>'=>'/admin/email.php'
	);
	return $ret;
}

$js = new ShopEmail($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'send_email') {
	ajaxCheckAdmin();
	if(!empty($_POST['kind_target'])) {
		if($_POST['kind_target'] == 'am') {
			$js->memberAllSms();
		}
		else {
			$js->sendEmail();
		}
	}
	else {
		$js->sendEmail();
	}
}
else { 
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	if($_GET['pg_mode'] == 'form_email') {
		$interface->setBasicInterface('admin','iframe');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_contents'] = 'marketing/sendEmail_form.html';
	}
	else if($_GET['pg_mode'] == 'form_email2') {
		$interface->setBasicInterface('admin','iframe');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_contents'] = 'marketing/sendEmail_form2.html';
		$js->setEmailTarget();
	}
	else {
		$interface->setBasicInterface('admin');
		$interface->addNavi(getNavi());
		$tpl->assign('cur_page','marketing04');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_left'] = 'menu.html?marketing';	
		$interface->layout['js_tpl_main'] = 'marketing/email_form.html';
		$js->viewForm();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>