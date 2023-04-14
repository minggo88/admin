<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/
//include_once '../lib/common_user.php';
include_once '../../lib/common_admin.php';
//include_once '../../lib/common_admin.php';
include_once '../bitcoin_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Analysis($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;


checkAdmin();
$interface = new ControlUserInteface();
$interface->tpl = &$tpl;

$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());
$tpl->assign('cur_page','bitcoin01');
$interface->layout['js_tpl_left'] = 'menu.html?bitcoin';
$interface->setPlugIn('popup');
$interface->setPlugIn('form');
$interface->setPlugIn('kendo_web');
$interface->layout['js_tpl_main'] = 'bitcoin/bitcoin_admin.html';
$print = 'layout';

$page = empty($_GET['page']) ? 1 : $_GET['page'];

$loop_page = $btcService->btcViewDao->getAdminTradeInfoCount();
$loop_trade_info = $btcService->btcViewDao->getAdminTradeInfo($page);

$interface->tpl->assign('page', $page);
$interface->tpl->assign('loop_page', $loop_page);
$interface->tpl->assign('loop_trade_info', $loop_trade_info);

$interface->display($print);

?>