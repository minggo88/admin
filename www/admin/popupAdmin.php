<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './popup_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">관리자기본설정</span>'=>'/admin/shopinfo_admin.php'
	);
	return $ret;
}

$js = new Popup($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'check_bool') {
	ajaxCheckAdmin();
	$js->checkBool();
}
else if($_GET['pg_mode'] == 'edit_rank') {
	ajaxCheckAdmin();
	$js->editRank();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','marketing03');
	$interface->layout['js_tpl_left'] = 'menu.html?marketing';
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('kendo_web');
		$interface->layout['js_tpl_main'] = 'popup/popup_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('kendo_web');
		$interface->layout['js_tpl_main'] = 'popup/popup_form.html';
		$js->editForm();
	}
	else {
		$interface->layout['js_tpl_main'] = 'popup/popup_list.html';
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>