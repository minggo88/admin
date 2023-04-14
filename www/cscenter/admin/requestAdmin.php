<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../request_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Request($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
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
	$tpl->assign('cur_page','center03');
	//css설정
	$interface->addCss('/template/admin/cscenter/css/request.css');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	if($_GET['pg_mode'] == 'view') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cleditor');
		$interface->layout['js_tpl_main'] = 'cscenter/request_view.html';
		$js->view();
	}
	else {
		$interface->layout['js_tpl_main'] = 'cscenter/request_list.html';
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>