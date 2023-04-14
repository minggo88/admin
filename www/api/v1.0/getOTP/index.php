<?php
include dirname(__file__) . "/../../lib/TradeApi.php";
include dirname(__file__) . "/../../lib/GoogleAuthenticator.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$userid = checkLoginUser(checkEmpty($_REQUEST['userid'], 'userid')); // User ID

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 회원정보 
$user_info = $tradeapi->get_user_info($userno);

// otpkey 값 확인 없으면 새로 생성.
if(trim($user_info->otpkey)=='') {
    $ga = new PHPGangsta_GoogleAuthenticator();
    $user_info->otpkey = $ga->createSecret(); // 시크릿키 생성
    $tradeapi->save_user_otpkey($userno, $user_info->otpkey);
}

$payload = array('otpkey'=>$user_info->otpkey);

// response
$tradeapi->success($payload);
