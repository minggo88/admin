<?php
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
include_once '../lib/common_user.php';
//include_once '../lib/common_admin.php';
include_once './checkserver_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Checkserver($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;

//checkUser();
//checkAdmin();

$interface->setBasicInterface('user','a3');
$interface->addNavi(getNavi());
$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/checkserver/css/checkserver.css');

if(!empty($_GET['pg_mode'])) {
	$interface->layout['js_tpl_main'] = 'checkserver/'.$_GET['pg_mode'].'.html';
} else {
	$interface->layout['js_tpl_main'] = 'checkserver/systemcheck.html';
}
//$js->view();

$print = 'layout';

$interface->display($print);
?>