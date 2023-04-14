<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once './mtom_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Mtom($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckUser();
	$js->write();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckUser();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckUser();
	$js->del();
}
else {
	checkUser();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());
	
	// 조회용 DB 사용. 
	$js->dbcon = connect_db_slave();

	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'mypage/mtom_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'mypage/mtom_form.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'view') {
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

