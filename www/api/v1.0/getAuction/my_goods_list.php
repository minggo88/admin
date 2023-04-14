<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();

$userid = $tradeapi->get_login_userid();
$userno = $tradeapi->get_login_userno();

// validate parameters

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

//회원별 인벤토리
$query ="SELECT
    i.goods_idx, i.reg_date buy_date, i.buy_price,
	g.title AS goods_title, g.goods_type, g.main_pic, g.sub1_pic, g.sub2_pic, g.sub3_pic, g.sub4_pic, g.animation, g.content, g.reg_date goods_reg_date
    FROM js_auction_inventory AS i
	INNER JOIN js_auction_goods AS g ON i.goods_idx=g.idx
    where i.userid='{$tradeapi->escape($userid)}'";

$payload = $tradeapi->query_list_object($query);
for($i=0; $i<count($payload); $i++) {
	$row = $payload[$i];

    // l.sell_price, l.wish_price, IF(l.finish='N', IF((NOW()>=l.start_date AND NOW()<=l.end_date), 'P', IF((NOW()<l.start_date), 'N', IF((NOW()>l.end_date), 'E', ''))), 'E') AS auction_status,
	$auction = $tradeapi->query_fetch_object("SELECT auction_idx, auction_title, sell_price, wish_price, start_date, end_date, reg_date auction_reg_date, finish, IF(finish='N', IF((NOW()>=start_date AND NOW()<=end_date), 'P', IF((NOW()<start_date), 'N', IF((NOW()>end_date), 'E', ''))), 'E') AS auction_status FROM js_auction_list WHERE goods_idx='{$tradeapi->escape($row->goods_idx)}' ORDER BY auction_idx DESC LIMIT 1");

	// (SELECT IFNULL(auction_price, l.sell_price) FROM js_auction_apply a WHERE a.auction_idx=l.auction_idx AND a.reg_date<=l.end_date ORDER BY auction_price DESC, reg_date LIMIT 1) price
	$auction->price = $tradeapi->query_one("SELECT auction_price FROM js_auction_apply a WHERE a.auction_idx='{$tradeapi->escape($auction->auction_idx)}' AND a.reg_date<='{$tradeapi->escape($auction->end_date)}' ORDER BY auction_price DESC, reg_date LIMIT 1");

	$payload[$i] = array_merge( (array) $row, (array) $auction);
}

// response
$tradeapi->success($payload);