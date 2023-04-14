<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol')));
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange))); // 구매 화폐
$trading_type = strtolower(setDefault($_REQUEST['trading_type'], '')); // 주문 종류, buy, sell, 빈값(=전부)
$cnt = setDefault($_REQUEST['cnt'], '10'); // 호가 갯수

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// check wallet owner
$quote_list = $tradeapi->get_quote_list($symbol, $exchange, $trading_type, $cnt);

// response
$tradeapi->success($quote_list);
