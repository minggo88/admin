<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once 'member_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Member($config,$tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'withdraw') {
	ajaxCheckUser();
	$js->withdraw();
}
else { 
	checkUser();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_main'] = 'member/withdraw_form.html';
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>