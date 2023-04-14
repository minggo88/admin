<?php
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
include_once '../lib/common_user.php';
//include_once '../lib/common_admin.php';
include_once './contents_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Contents($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;

//checkUser();
//checkAdmin();

$interface->setBasicInterface('user','a4');
//$interface->setBasicInterface('admin','a4');
$interface->addNavi(getNavi());
$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/contents/css/contents.css');

$interface->layout['js_tpl_left'] = 'left_menu.html';

$interface->layout['js_tpl_main'] = 'contents/contents_view.html';
$js->view();

$print = 'layout';

$interface->display($print);
?>