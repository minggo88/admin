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
$tpl->assign('cur_page','bitcoin05');
$interface->layout['js_tpl_left'] = 'menu.html?bitcoin';
$interface->setPlugIn('popup');
$interface->setPlugIn('form');
$interface->layout['js_tpl_main'] = 'bitcoin/fee_admin.html';
$print = 'layout';

$pageType = isset($_GET['type']) ? $_GET['type'] : "";

if($pageType == 'save') {
	 $btcService->btcTradeCriterionDao->updateValue($_POST['btc_trading_rate'], 'BTC_TRADING_RATE');
	 $btcService->btcTradeCriterionDao->updateValue($_POST['site_trading_rate'], 'SITE_TRADING_RATE');
	 $btcService->btcTradeCriterionDao->updateValue($_POST['withdrawal_fee'], 'WITHDRAWAL_FEE');
	 $btcService->btcTradeCriterionDao->updateValue($_POST['krw_request_amount'], 'KRW_REQUEST_AMOUNT');
	 $btcService->btcTradeCriterionDao->updateValue($_POST['btc_request_amount'], 'BTC_REQUEST_AMOUNT');
} else {
	$btcService->setBtcCriterion();

	$interface->tpl->assign('BTC_TRADING_RATE', $btcService->_BTC_TRADING_RATE);
	$interface->tpl->assign('SITE_TRADING_RATE', $btcService->_SITE_TRADING_RATE);
	$interface->tpl->assign('WITHDRAWAL_FEE', $btcService->_WITHDRAWAL_FEE);
	$interface->tpl->assign('KRW_REQUEST_AMOUNT', $btcService->_KRW_REQUEST_AMOUNT);
	$interface->tpl->assign('BTC_REQUEST_AMOUNT', $btcService->_BTC_REQUEST_AMOUNT);
}
$interface->display($print);

?>