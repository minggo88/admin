<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once './configClass_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new ClassName($tpl);
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

	$interface->setBasicInterface('admin','a4');
	$interface->addNavi(getNavi());

	$interface->setPlugIn('tablednd'); 
	$tpl->assign('cur_page','config10');
	$interface->layout['js_tpl_left'] = 'menu.html?config10';
	$interface->layout['js_tpl_main'] = '_list.html';
	$js->editForm();
	$print = 'layout';
	$interface->display($print);
}
?>