<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './configPayment_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">주문관련설정</span>'=>'/admin/shopinfo_payment.php'
	);
	return $ret;
}


$js = new ConfigPayment($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'edit_account') {
	ajaxCheckAdmin();
	$js->editAccount();
}
else if($_GET['pg_mode'] == 'get_account') {
	ajaxCheckAdmin();
	$js->getAccount();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','config08');
	$interface->setPlugIn('cheditor');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_left'] = 'menu.html?config';
	$interface->layout['js_tpl_main'] = 'basic/configPayment_form.html';
	$js->viewForm();
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>