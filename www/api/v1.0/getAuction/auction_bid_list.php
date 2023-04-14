<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();

$userid = $tradeapi->get_login_userid();
// validate parameters
$auction_idx  = $_REQUEST['auction_idx'];

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

//옥션 판매 아이템 상세정보
//회사 분이면 seller_id = 'ara_company'
$query ="SELECT auction_idx, goods_idx, userid, auction_price as bid_price, reg_date FROM js_auction_apply WHERE auction_idx='{$tradeapi->escape($auction_idx)}'";

$payload = $tradeapi->query_list_object($query);
// response
$tradeapi->success($payload);