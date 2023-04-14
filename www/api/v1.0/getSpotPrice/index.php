<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
if(is_array($_REQUEST['symbol'])) { // 배열로 잘못들어오는 경우가 있어서 배열로 들어오면 CSV 문자열로 바꿉니다.
    $_REQUEST['symbol'] = implode(',', $_REQUEST['symbol']);
}
$symbol = checkSymbol(strtoupper(setDefault($_REQUEST['symbol'], 'ALL')));
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange)));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 여러 가격을 알려고 할때.
if(strpos($symbol, ',')!==false) {
    $symbol = explode(',', $symbol);
}
// 전체 조회시.
if($symbol=='ALL') {
    $symbol = '';
}

// check wallet owner
$currency = $tradeapi->get_spot_price($symbol, $exchange);

// response
$tradeapi->success($currency);
