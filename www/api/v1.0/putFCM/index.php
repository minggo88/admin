<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$userid = checkLoginUser(checkEmpty($_REQUEST['userid'], 'userid')); // User ID
$uuid = checkUUID(checkEmpty($_REQUEST['uuid'], 'uuid')); // UUID
$os = checkEmpty($_REQUEST['os'], 'os'); // OS
$fcm_tokenid = checkEmpty($_REQUEST['fcm_tokenid'], 'fcm_tokenid'); // FCM Token ID

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// 중복 확인
$_r = $tradeapi->get_fcm_info($userno, $uuid);
if($_r && count($_r)>0 && $_r->uuid==$uuid) {
    $tradeapi->error('023', __('The UUID that is already registered.'));
}

// 회원정보 
$_r = $tradeapi->put_fcm_info($userno, $uuid, $os, $fcm_tokenid);

// response
if($_r) {
    $tradeapi->success();
} else {
    $tradeapi->error('005', __('A system error has occurred.'));
}
