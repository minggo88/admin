<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$idx = checkEmpty($_REQUEST['idx'], 'idx');

// --------------------------------------------------------------------------- //


// 슬레이브 디비 사용하도록 설정.d
$tradeapi->set_db_link('master');

$c = $tradeapi->db_get_row('js_bbs_main', array('idx'=>$idx));

if($c->idx != $idx) {
    $tradeapi->error('100', __('다른 회원님이 작성한 글입니다.'));
} else {
    $tradeapi->db_delete('js_bbs_main', array('idx'=>$idx));
}

// response
$tradeapi->success(array('idx'=>$idx));
