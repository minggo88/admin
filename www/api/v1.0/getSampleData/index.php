<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(setDefault($_REQUEST['symbol'], 'ALL')));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 전체 조회시.
if($symbol=='ALL') {
    $symbol = '';
}

// check wallet owner
// $currency = $tradeapi->get_currency($symbol);
$tradeapi->set_cache_dir($tradeapi->cache_dir.'/getCurrency/');
$cache_id = 'getCurrency-'.$symbol;
$cachetime = 60;
$c = $tradeapi->get_cache($cache_id, $cachetime);
if($c=='') {
    $c = $tradeapi->set_cache($cache_id, $tradeapi->get_currency($symbol), $cachetime);
}
//$tradeapi->clear_old_file($cachetime);

// response
$tradeapi->success($c);
