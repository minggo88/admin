<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

$return = array();

$returnVal = array(
    'userno' => $_SESSION['USER_NO']
);

$return[] = $returnVal;

// response
$tradeapi->success($return);
