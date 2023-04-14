<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './configStudentcode_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">관리자기본설정</span>'=>'/admin/shopinfo_admin.php'
	);
	return $ret;
}

$js = new ConfigStudentcode($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','config11');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_left'] = 'menu.html?config';
	$interface->layout['js_tpl_main'] = 'basic/configStudentcode_form.html';
	$js->editForm();
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>