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

if($_GET['pg_mode']=='confirm') {
	
	$query = "select btcWalletBtcTxnidx from js_btc_withdraw_request where idx='".intval($_POST['idx'])."' ";
	$btcWalletBtcTxnidx = $dbcon->query_unique_value($query);
	
	$dbcon->query("set autocommit=0");
	$query = "update js_btc_wallet_btc_txn set address='".  mysqli_real_escape_string($_POST['txnid'])."' where idx='$btcWalletBtcTxnidx' ";
	
//	echo $query;
	if( $dbcon->query($query) ) {
		$query = "update js_btc_withdraw_request set status='5' where idx='".intval($_POST['idx'])."' ";
		$dbcon->query($query);
		$dbcon->query("commit");
		echo 'success';
	} else {
		echo 'fail';
	}
	exit;
}


$interface = new ControlUserInteface();
$interface->tpl = &$tpl;





$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());
$tpl->assign('cur_page','bitcoin07');
$interface->layout['js_tpl_left'] = 'menu.html?bitcoin';
$interface->setPlugIn('popup');
$interface->setPlugIn('form');
$interface->setPlugIn('kendo_web');
$interface->layout['js_tpl_main'] = 'bitcoin/btc_admin_withdraw_request.html';
$print = 'layout';
$page = empty($_GET['page']) ? 1 : $_GET['page'];

$loop_page = $btcService->btcWalletBtcTxnDao->selectAdminBtcWithdrawRequestListCount();
$loop_btc_info = $btcService->btcWalletBtcTxnDao->selectAdminBtcWithdrawRequestList($page);

$interface->tpl->assign('page', $page);
$interface->tpl->assign('loop_page', $loop_page);
$interface->tpl->assign("loop_btc_info", $loop_btc_info);


$interface->display($print);

?>