<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// check wallet owner
$currency = $tradeapi->get_symbol();

// response
$tradeapi->success($currency);
