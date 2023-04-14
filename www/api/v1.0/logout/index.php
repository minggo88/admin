<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// validate parameters

// --------------------------------------------------------------------------- //

// token 값을 확인해 없으면 전달하라고 합니다.
if(trim($_REQUEST['token'])=='' && trim($_REQUEST['token'])=='' ) {
    $tradeapi->error('035',__('Please enter the token value.'));
}

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// save access time
// $tradeapi->save_accessdate($ledgerapi->get_login_account(), $uuid);

$tradeapi->logout();
// cookie 제거
setCookie('token', '', -1, '/', '.aratube.io');
setCookie('token', '', -1, '/', '.loc.aratube.io');
setCookie('token', '', -1, '/', '.dev.aratube.io');
setCookie('token', '', -1, '/', '.stage.aratube.io');
setCookie('token', '', -1, '/', '.kmcse.com');//.loc.kmcse.com
setCookie('token', '', -1, '/', '.loc.kmcse.com');//.loc.kmcse.com
setCookie('token', '', -1, '/', '.stage.kmcse.com');//.loc.kmcse.com
setCookie('token', '', -1, '/', '.auction.kmcse.com');//.loc.kmcse.com
setCookie('token', '', -1, '/', '.auction.loc.kmcse.com');//.loc.kmcse.com
setCookie('token', '', -1, '/', '.auction.dev.kmcse.com');//.loc.kmcse.com
setCookie('token', '', -1, '/', '.auction.stage.kmcse.com');//.loc.kmcse.com

// response
$tradeapi->success($return);
