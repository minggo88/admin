<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();

$userid = $tradeapi->get_login_userid();
// validate parameters
$goods_type  = $_REQUEST['goods_type'];
$auction_idx  = $_REQUEST['auction_idx'];

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

//옥션 판매 아이템 상세정보
//회사 분이면 seller_id = 'ara_company'
// $query ="SELECT g.title, a.sell_price as start_price, a.start_date, a.end_date, a.goods_idx, g.goods_type,
//     a.auction_idx AS idx,
// 	(SELECT COUNT(*) FROM js_auction_apply t WHERE t.auction_idx = a.auction_idx) bids,
//     IF(MAX(l.auction_price)=auction_price, userid, '') AS register_userid,
//     (IFNULL(MAX(l.auction_price), 0)) AS price,
//     (SELECT IFNULL(auction_price, 0) FROM js_auction_apply WHERE goods_idx=g.idx and userid='{$userid}' LIMIT 1) AS my_price,
//     (SELECT userid FROM js_auction_inventory WHERE goods_idx=g.idx LIMIT 1) AS seller_id,
//     IF( (NOW()>=a.start_date AND NOW()<=a.end_date), 'P', IF((NOW()<a.start_date), 'N', IF((NOW()>a.end_date), 'E', ''))) AS `status`
//     FROM js_auction_list as a
//     INNER JOIN js_auction_goods AS g  on a.goods_idx= g.idx
//     LEFT JOIN js_auction_apply AS l ON l.auction_idx = a.auction_idx";
// if($goods_type){
//     $query .=" WHERE g.goods_type='{$tradeapi->escape($goods_type)}'";
// }
// $payload = $tradeapi->query_list_object($query);

// 모든 상품이 표시되고 상품의 옥션 정보가 보여지는 방식으로 변경.
$sql = "SELECT * FROM js_auction_goods WHERE 1 ";
if($goods_type) {
	$sql .= "AND goods_type='{$tradeapi->escape($goods_type)}' ";
}
if($auction_idx) {
	// $sql = "SELECT g.* FROM js_auction_goods g LEFT JOIN js_auction_list l ON g.idx = l.goods_idx WHERE 1 "; // 경매가 전혀 없는 처음 상품만 등록된 경우도 표시할때
	$sql = "SELECT g.* FROM js_auction_goods g, js_auction_list l WHERE g.idx = l.goods_idx "; // 경매가 있는 상품만 표시할때
	$sql .= "AND auction_idx='{$tradeapi->escape($auction_idx)}' ";
	$sql .= "GROUP BY g.idx ";
}
$goods_list = $tradeapi->query_list_object($sql);

for($i=0; $i<count($goods_list); $i++) {
	$goods = (array) $goods_list[$i];
	$goods['goods_idx'] = $goods['idx'];

	$query = "SELECT auction_idx, sell_price AS start_price, start_date, end_date, goods_idx,
	(select userid from js_auction_inventory i where i.goods_idx=l.goods_idx limit 1) AS seller_userid,
	IF(l.finish='N', IF((NOW()>=start_date AND NOW()<=end_date), 'P', IF((NOW()<start_date), 'N', IF((NOW()>end_date), 'E', ''))), 'E')  AS `status`,
	unit_price FROM js_auction_list l WHERE goods_idx='".$tradeapi->escape($goods['idx'])."' ORDER BY auction_idx DESC LIMIT 1";
	$auction = (array) $tradeapi->query_fetch_object($query);
	if($auction) {
		$auction['start_price'] *= 1;
		$auction['unit_price'] *= 1;

		$auction['my_price'] = $tradeapi->query_one("SELECT IFNULL(MAX(auction_price), 0) FROM js_auction_apply WHERE auction_idx='".$tradeapi->escape($auction['auction_idx'])."' AND userid='{$userid}'");
		$auction['my_price'] *= 1;
		$auction['bids'] = $tradeapi->query_one("SELECT COUNT(*) FROM js_auction_apply WHERE auction_idx='".$tradeapi->escape($auction['auction_idx'])."' ");
		$auction['bids'] *= 1;

		$t = $tradeapi->query_fetch_object("SELECT auction_price, userid  FROM js_auction_apply WHERE auction_idx='".$tradeapi->escape($auction['auction_idx'])."' AND '".$tradeapi->escape($auction['start_date'])."' <= reg_date AND reg_date < '".$tradeapi->escape($auction['end_date'])."' ORDER BY auction_price DESC, reg_date ASC  ");
		$goods['winner_userid'] = isset($t->userid) ? $t->userid : '';
		$auction['price'] = isset($t->auction_price) && $t->auction_price > $auction['start_price'] ? $t->auction_price : $auction['start_price'];
		$auction['price'] *= 1;
	}

	$goods = array_merge($goods, $auction);
	// var_dump($t, $auction['start_price'], isset($t->auction_price) && $t->auction_price > $auction['start_price'], $goods['price']); exit;
	// var_dump($goods); exit;
	$goods_list[$i] = $goods;
}

$payload = $goods_list;
// response
$tradeapi->success($payload);
