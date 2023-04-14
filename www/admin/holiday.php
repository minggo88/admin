<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/
//include_once '../lib/common_user.php';
include_once '../lib/common_admin.php';
include_once './holiday_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Holiday($tpl);
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
else { 
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;

	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','config09');
	$interface->layout['js_tpl_left'] = 'menu.html?config';
	$interface->setPlugIn('popup');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_main'] = 'basic/holiday_list.html';
	$js->loopYear();
	$js->calendar();
	$print = 'layout';

	$interface->display($print);
}
?>