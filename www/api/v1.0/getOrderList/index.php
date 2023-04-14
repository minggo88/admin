<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol')));
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange)));
$trading_type = setDefault($_REQUEST['trading_type'], '');
$trading_type = $trading_type ? ( $trading_type == 'buy' ? 'B' : 'S' ) : ''; // change to db value
$orderid = checkNumber(setDefault($_REQUEST['orderid'], '0'));
$page = checkNumber(setDefault($_REQUEST['page'], '1'));
$rows = checkNumber(setDefault($_REQUEST['rows'], '10'));
$order_by = setDefault($_REQUEST['order_by'], 'orderid');
$order_method = setDefault($_REQUEST['order_method'], 'desc');
$status = setDefault($_REQUEST['status'], 'all');

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// check previos address
$txns = $tradeapi->get_order_list('', $status, $symbol, $exchange, $page, $rows, $orderid, $trading_type);

// response
$tradeapi->success($txns);
