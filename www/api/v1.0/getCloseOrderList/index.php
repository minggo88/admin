<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol')));
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange)));
$orderid = checkNumber(setDefault($_REQUEST['orderid'], '0'));
$page = checkNumber(setDefault($_REQUEST['page'], '1'));
$rows = checkNumber(setDefault($_REQUEST['rows'], '10'));

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// check previos address
$txns = $tradeapi->get_order_list($userno, 'close', $symbol, $exchange, $page, $rows, $orderid);

// response
$tradeapi->success($txns);
