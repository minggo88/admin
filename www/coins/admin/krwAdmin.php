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
$pageType = isset($_GET['type']) ? $_GET["type"] : "";

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;

$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());
$tpl->assign('cur_page','bitcoin03');
$interface->layout['js_tpl_left'] = 'menu.html?bitcoin';
$interface->setPlugIn('popup');
$interface->setPlugIn('form');
$interface->layout['js_tpl_main'] = 'bitcoin/krw_admin.html';
$print = 'layout';
$page = empty($_GET['page']) ? 1 : $_GET['page'];
$loop_page = $btcService->btcWalletKrwTxnDao->selectAdminKrwListCount();
$loop_krw_info = $btcService->btcWalletKrwTxnDao->selectAdminKrwList($page);
$interface->tpl->assign('page', $page);
$interface->tpl->assign('loop_page', $loop_page);
$interface->tpl->assign('loop_krw_info', $loop_krw_info);

if($pageType == 'update') {
    $bank_name = trim($_POST['bank_name'])==''?"ADMIN":trim($_POST['bank_name']);
    $account = trim($_POST['account'])==''?"ADMIN":trim($_POST['account']);
    $bankowner = trim($_POST['bankowner'])==''?"ADMIN":trim($_POST['bankowner']);
    $idx = $btcService->requestKrw($_POST['krw_user_id'], "C", $_POST['krw_amount'], $bank_name, $account, $bankowner);
    $btcService->confirmKrw($idx, $_SESSION["ADMIN_ID"]);

	//$result = $btcService->btcWalletKrwDao->updateAmountByUserid($_POST['krw_user_id'], $_POST['krw_amount']);
	echo "success";
	exit;
} else if($pageType == 'search') {
	$_userid = $dbcon->query_unique_value("select userid from js_member where userid='".  trim($_POST['krw_user_id'])."' ");
	echo $_userid;
	exit;
}else if($pageType == 'confirm') {
	$btcService->confirmKrw($_POST['idx'], $_SESSION["ADMIN_ID"]);
	
	echo "success";
	exit;
}else if($pageType == 'reset') {
	$btcService->resetPayOutKrw($_POST['idx'], $_SESSION["ADMIN_ID"]);
	
	echo "success";
	exit;
}

$interface->display($print);

?>