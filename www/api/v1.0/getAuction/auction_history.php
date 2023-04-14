<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();

// validate parameters
$auction_idx  = checkEmpty($_REQUEST['auction_idx'], 'auction_idx');

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 모든 상품이 표시되고 상품의 옥션 정보가 보여지는 방식으로 변경.
$sql = "SELECT
	reg_date,
	auction_idx,
	replace(userid,'mobile82','0') userid,
	trim(auction_price)+0 auction_price,
	(SELECT TRIM(SUM(auction_price))+0 FROM js_auction_apply_list al2 WHERE al2.reg_date<=al.reg_date AND al2.auction_idx=al.auction_idx AND al2.userid=al.userid) sum_auction_price
FROM js_auction_apply_list al
WHERE auction_idx='{$tradeapi->escape($auction_idx)}'
ORDER BY reg_date DESC";
$payload = $tradeapi->query_list_object($sql);
// response
$tradeapi->success($payload);
