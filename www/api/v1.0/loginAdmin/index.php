<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 거래소 api는 토큰을 전달 받을때만 작동하도록 되어 있어서 로그인시 token을 생성해 줍니다.
$tradeapi->token = session_create_id();
session_start();
session_regenerate_id(); // 로그인할때마다 token 값을 바꿉니다. 

// 로그인 세션 확인.
// $tradeapi->checkLogout();

// validate parameters
$userid = checkEmpty($_REQUEST['userid'], 'User ID');
$userpw = checkEmpty($_REQUEST['userpw'], 'Secret Key');

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// login
if(! $tradeapi->login_admin($userid, $userpw)) {
    $tradeapi->error('041', __('The information does not match. Please check your ID!'));
}

// response
$tradeapi->success(array('token'=>session_id()));
