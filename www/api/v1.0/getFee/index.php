<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol')));
$action = checkFeeAction(strtolower(setDefault($_REQUEST['action'], 'ALL')));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 전체 조회시.
if($action=='all') {
    $action = '';
}
// check wallet owner
// $fee = $tradeapi->get_fee($symbol, $action);
$tradeapi->set_cache_dir($tradeapi->cache_dir.'/getFee/');
$cache_id = 'getFee-'.$symbol.'/'.$action;
$cachetime = 60;
$c = $tradeapi->get_cache($cache_id);
if($c=='') {
    $c = $tradeapi->set_cache($cache_id, $tradeapi->get_fee($symbol, $action), $cachetime);
}
$tradeapi->clear_old_file($cachetime);

// response
$tradeapi->success($c);
