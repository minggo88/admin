<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$from_date = checkDateFormat( setDefault($_REQUEST['from_date'], date('Y-m-d', time()-(60*60*24*7)) ) );
$to_date = checkDateFormat( setDefault($_REQUEST['to_date'], date('Y-m-d', time()) ) );

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// check previos address
$r = $tradeapi->get_my_revenue_status($userno, $from_date, $to_date);

// response
$tradeapi->success($r);
