<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../mtom_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Mtom($tpl);
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
else if($_GET['pg_mode'] == 'del_multi') {
	ajaxCheckAdmin();
	$js->delMulti();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','comm06');
	//css설정
	//$interface->addCss('/template/admin/mypage/css/mtom.css');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	if($_GET['pg_mode'] == 'view') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'mypage/mtom_view.html';
		$js->view();
	}
	else {
		$interface->layout['js_tpl_main'] = 'mypage/mtom_list.html';
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>