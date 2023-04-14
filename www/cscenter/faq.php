<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once './faq_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Faq($tpl);
// $js->dbcon = &$dbcon;
$js->dbcon = connect_db_slave();
$js->json = &$json;

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/cscenter/css/faq.css');
$interface->setBasicInterface('user','a3');
$interface->addNavi(getNavi());

$interface->layout['js_tpl_main'] = 'cscenter/faq.html';
$js->listCode();
$js->lists();
$print = 'layout';
$interface->display($print);
$dbcon->close();
?>