<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

$_REQUEST['userno'] = $userno;

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// get my member information
$r = $tradeapi->save_member_info($_REQUEST);

// response
$tradeapi->success($r);
