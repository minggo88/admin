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


if ($_POST['pg_mode'] == 'check_total_btc') {
	
	$listAccount = $btcService->bitcoind->getbalance();
	echo "$listAccount btc";
	exit;
	
} else {


$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());
$interface->layout['js_tpl_left'] = 'menu.html?main';

$interface->setPlugIn('popup');
$interface->setPlugIn('form');
$interface->setPlugIn('kendo_web');
$interface->layout['js_tpl_main'] = 'bitcoin/btc_admin.html';
$print = 'layout';
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$loop_page = $btcService->btcWalletBtcTxnDao->selectAdminBtcListCount();
$loop_btc_info = $btcService->btcWalletBtcTxnDao->selectAdminBtcList($page);

$interface->tpl->assign('page', $page);
$interface->tpl->assign('loop_page', $loop_page);
$interface->tpl->assign("loop_btc_info", $loop_btc_info);


$interface->display($print);

}

?>