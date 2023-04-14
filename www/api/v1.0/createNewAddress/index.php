<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(checkEmpty($_REQUEST['symbol'], 'symbol'));

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$exchangeapi->set_db_link('master');
// check previos address
$wallet = $exchangeapi->get_row_wallet($userno, $symbol);
$wallet = is_array($wallet) && count($wallet) > 0 ? $wallet[0] : $wallet;
if ($wallet->address != '') {
    $address = $wallet->address;
}

if (!$address) {
    $address = $exchangeapi->create_new_wallet($userno, $symbol);
}


// response
$exchangeapi->success(array('address' => $address));
