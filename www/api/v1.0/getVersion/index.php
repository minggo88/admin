<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $ledgerapi->checkLogin();
// $login_account = $ledgerapi->get_login_account();
// $login_walletno = $ledgerapi->get_login_walletno();

// validate parameters
$device = strtoupper(checkEmpty($_REQUEST['device'], 'device'));
$service = strtolower(setDefault($_REQUEST['service'], 'exchange')); // 기본 지갑앱 버전을 확인합니다.

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// get version information
$version = $tradeapi->get_version($device, $service);
if(!$version) {
    $tradeapi->error('053',__('There is no version information.'));
}

// response
$tradeapi->success($version);
