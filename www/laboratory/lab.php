<?php
/*--------------------------------------------
Date :
Author : FirstGleam - http://www.firstgleam.com
comment : 
History : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './lab_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Lab($tpl);
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
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->setPlugIn('popup');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_main'] = 'laboratory/'.$_GET['pg_mode'].'.html';
	$js->loopYear();
	$js->calendar();
	$print = 'layout';

	$interface->display($print);
}
?>