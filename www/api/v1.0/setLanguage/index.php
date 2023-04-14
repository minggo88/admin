<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// validate parameters
$code = setDefault($_REQUEST['code'], '');

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

if($code=='kr'){
    $code = 'ko';
}
if($code=='cn'){
    $code = 'zh';
}
if($code) {
    setcookie('lang', $code, null, '/');
    $_SESSION['lang'] = $code;
}

$tradeapi->success();
