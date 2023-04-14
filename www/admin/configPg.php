<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './configPg_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">PG사 설정</span>'=>'/admin/configPg.php'
	);
	return $ret;
}
$js = new ConfigPg($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','config07');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_left'] = 'menu.html?config';
	$interface->layout['js_tpl_main'] = 'basic/configPg_form.html';
	$js->viewForm();
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>