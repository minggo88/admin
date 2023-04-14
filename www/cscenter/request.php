<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once './request_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Request($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	if($config_basic['bool_ssl'] > 0) {
		$js->config['write_mode'] = 'post';
	}
	else {
		ajaxCheck();
	}	
	$js->write();
}
else {
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a4');
	$interface->addNavi(getNavi());
	$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/cscenter/css/partnershop.css');
	$interface->setPlugIn('form');
	$interface->setPlugIn('cheditor');
	$interface->layout['js_tpl_left_menu'] = 'left_menu.html';
	$interface->layout['js_tpl_main'] = 'cscenter/request_form.html';
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>