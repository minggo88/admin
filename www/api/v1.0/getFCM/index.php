<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$userid = checkLoginUser(checkEmpty($_REQUEST['userid'], 'userid')); // User ID
$uuid = checkUUID(checkEmpty($_REQUEST['uuid'], 'uuid')); // UUID

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// fcm 정보 
$payload = $tradeapi->get_fcm_info($userno, $uuid);

// response
$tradeapi->success($payload);
