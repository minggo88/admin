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
$tpl->assign('cur_page','bitcoin02');
$interface->layout['js_tpl_left'] = 'menu.html?bitcoin';
$interface->setPlugIn('popup');
$interface->setPlugIn('form');
$interface->setPlugIn('kendo_web');
$interface->layout['js_tpl_main'] = 'bitcoin/account_admin.html';
$print = 'layout';
$_GET['pg_mode'] = empty($_GET['pg_mode']) ? 'list' : $_GET['pg_mode'];
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$loop_page = $btcService->btcViewDao->getAdminUserAccountInfoCount();
$loop_user_info = $btcService->btcViewDao->getAdminUserAccountInfo($page);

for($i=0 ; $i < sizeof($loop_user_info) ; $i++) {
    $user_buy = $btcService->btcViewDao->getAdminUserAccountBuyInfo($loop_user_info[$i]["userid"]);
    $user_sell = $btcService->btcViewDao->getAdminUserAccountSellInfo($loop_user_info[$i]["userid"]);

    $loop_user_info[$i]["buy_amount"] = $user_buy["amount"];
    $loop_user_info[$i]["sell_amount"] = $user_sell["amount"];
//    $loop_user_info[$i]["fee"] = $user_buy["site_trading_fee"] + $user_sell["site_trading_fee"];
    $loop_user_info[$i]["fee"] = $user_sell["site_trading_fee"];

}
$interface->tpl->assign('page', $page);
$interface->tpl->assign('loop_page', $loop_page);
$interface->tpl->assign('loop_user_info', $loop_user_info);


$interface->display($print);
?>