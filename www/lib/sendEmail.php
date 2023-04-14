<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
//header("Content-Type: text/html; charset=UTF-8");
header("Content-Type: text/html; charset=euc-kr");

include_once $_SERVER["DOCUMENT_ROOT"].'/lib/basic_config.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/basic_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/db_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/util.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/json_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/admin/email_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/mail_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/interface_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/tpl/Template_.class.php';

define('ROOT_DIR',$_SERVER["DOCUMENT_ROOT"]);

$dbcon = new DB($db_host,$db_name,$db_user,$db_pass,$db_charset);
$json = new Services_JSON();

$tpl = new Template_;
$tpl->template_dir = ROOT_DIR.'/template';
$tpl->compile_dir = ROOT_DIR.'/../compile';
$tpl->prefilter = 'adjustPath';
$tpl->postfilter = 'arrangeSpace';
//$tpl->compile_check = false; 
$tpl->skin = 'user';

$js = new ShopEmail($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

$js->sendEmail();

?>