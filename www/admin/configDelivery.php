<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './configDelivery_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">배송정책설정</span>'=>'/admin/shopinfo_delivery.php'
	);
	return $ret;
}

$js = new ConfigDelivery($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'edit_company' || $_POST['pg_mode'] == 'edit_clause') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'edit_delivery') {
	ajaxCheckAdmin();
	$js->editAccount();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','config09');

	$interface->setPlugIn('cheditor');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_left'] = 'menu.html?config';
	$interface->layout['js_tpl_main'] = 'basic/configDelivery_form.html';
	$js->viewForm();
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>