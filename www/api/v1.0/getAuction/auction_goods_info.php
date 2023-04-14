<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();

$userid = $tradeapi->get_login_userid();
$userno = $tradeapi->get_login_userno();

// validate parameters
$goods_idx  = checkEmpty($_REQUEST['goods_idx'], 'goods_idx');          // goods_idx

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

//회원별 인벤토리
// $query ="SELECT g.idx, g.title, g.content, g.main_pic, g.sub1_pic, g.sub2_pic, g.sub3_pic, g.sub4_pic, g.goods_type, g.reg_date, g.mod_date, i.userid,
//     IF((SELECT auction_price FROM js_auction_apply WHERE goods_idx=i.goods_idx) <>'',
//     (SELECT auction_price FROM js_auction_apply WHERE goods_idx=i.goods_idx), i.buy_price) AS price
//     FROM js_auction_inventory AS i INNER JOIN js_auction_goods AS g ON i.goods_idx=g.idx
//     where g.idx='{$tradeapi->escape($goods_idx)}'";
$query ="SELECT g.idx, g.title, g.content, g.main_pic, g.sub1_pic, g.sub2_pic, g.sub3_pic, g.sub4_pic, g.animation, g.goods_type, g.reg_date, g.mod_date, i.userid,
    IF((SELECT auction_price FROM js_auction_apply WHERE goods_idx=i.goods_idx) <>'',
    (SELECT auction_price FROM js_auction_apply WHERE goods_idx=i.goods_idx), i.buy_price) AS price
    FROM js_auction_goods AS g LEFT JOIN js_auction_inventory AS i ON i.goods_idx=g.idx
    where g.idx='{$tradeapi->escape($goods_idx)}'";

$payload = $tradeapi->query_list_object($query);
// response
$tradeapi->success($payload);