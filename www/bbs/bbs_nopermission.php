<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once './bbs_class.php';

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;

$interface->setBasicInterface('user','a4');
if ($tpl->skin=='shop') {       
	$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?community';
} else {
	$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?join';
}
$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/bbs/css/bbs_no_access.css');
$interface->layout['js_tpl_main'] = 'bbs/bbs_nopermission.html';
$print = 'layout';
$interface->display($print);
$dbcon->close();
?>