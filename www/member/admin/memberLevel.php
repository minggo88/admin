<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once './memberLevel_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new MemberLevel($tpl);
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
else if($_POST['pg_mode'] == 'change_level') {
	ajaxCheckAdmin();
	$js->changeLevel();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'save_order') {
	ajaxCheckAdmin();
	$js->saveOrder();
}
else { 
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','member02');
	$interface->setPlugIn('form');
	$interface->setPlugIn('tablednd');
	$interface->setPlugIn('popup');
	$interface->addCss('/template/admin/member/css/memberLevel.css');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->layout['js_tpl_main'] = '/member/level_list.html';
	if(empty($_GET['pg_mode'])) {
		$js->lists();
	}
	else {
		if($_GET['pg_mode'] == 'form_edit') {
			$js->editForm();
		}
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>