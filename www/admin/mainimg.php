<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './mainimg_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link"></span>'=>'/admin/shopinfo_admin.php'
	);
	return $ret;
}

$js = new MainImg($tpl);
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

	$tpl->assign('cur_page','design04');
	
	$interface->layout['js_tpl_left'] = 'menu.html?design';
	//배너등록폼
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'design/mainimg_form.html';
		$js->newForm();
	}
	//배너수정폼
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'design/mainimg_form.html';
		$js->editForm();
	}
	//배너리스트
	else {
		$interface->setPlugIn('tooltip');
		$interface->layout['js_tpl_main'] = 'design/mainimg_list.html';
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>