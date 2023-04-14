<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol')));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// // 전체 조회시.
// if($symbol=='ALL') {
//     $symbol = '';
// }

$cache_id = sha1(basename(__file__).'-'.$symbol);
$c = $tradeapi->get_cache($cache_id);
if(!$c) {
    // $c = (object) array(
    //     "years" => array(2016,2017,2018,2019,2020,2021,2022), // 년도
    //     "sales" => array(40.0323, 40.0323, 40.0323, 110.0323, 360.0323, 450.0323, 600.0323), // 매출
    //     "opt_profit" => array(60, 60, 60, 90, 110, 170, 150), // 영업이익
    //     "net_profit" => array(10, 10, 10, 20, 40, 50, 40), // 순이익
    //     "assets" => array(60, 60, 40, 110, 360, 450, 600), // 자산
    //     "debt" => array(60, 60, 60, 390, 560, 670, 850), // 부채
    //     "equity" => array(60, 60, 60, 390, 560, 670, 850), // 자본
    //     "debt_ratio" => array(60, 60, 60, 390, 560, 670, 850), // 부채비율
    //     "roe" => array(60, 60, 60, 390, 560, 670, 850) // 자기자본이익률 Return on Equity
    // );
    $c = $tradeapi->query_fetch_object("SELECT 
        GROUP_CONCAT(years) years,
        GROUP_CONCAT(sales) sales,
        GROUP_CONCAT(opt_profit) opt_profit,
        GROUP_CONCAT(net_profit) net_profit,
        GROUP_CONCAT(assets) assets,
        GROUP_CONCAT(debt) debt,
        GROUP_CONCAT(equity) equity,
        GROUP_CONCAT(debt_ratio) debt_ratio,
        GROUP_CONCAT(roe) roe
    FROM `js_trade_currency_finance`
    WHERE symbol='{$symbol}'
    ORDER BY years DESC");
    if($c && $c->years) {$c->years = array_map( function($v) {return $v*1;}, explode(',', $c->years));}
    if($c && $c->sales) {$c->sales = array_map( function($v) {return $v*1;}, explode(',', $c->sales));}
    if($c && $c->opt_profit) {$c->opt_profit = array_map( function($v) {return $v*1;}, explode(',', $c->opt_profit));}
    if($c && $c->net_profit) {$c->net_profit = array_map( function($v) {return $v*1;}, explode(',', $c->net_profit));}
    if($c && $c->assets) {$c->assets = array_map( function($v) {return $v*1;}, explode(',', $c->assets));}
    if($c && $c->debt) {$c->debt = array_map( function($v) {return $v*1;}, explode(',', $c->debt));}
    if($c && $c->equity) {$c->equity = array_map( function($v) {return $v*1;}, explode(',', $c->equity));}
    if($c && $c->debt_ratio) {$c->debt_ratio = array_map( function($v) {return $v*1;}, explode(',', $c->debt_ratio));}
    if($c && $c->roe) {$c->roe = array_map( function($v) {return $v*1;}, explode(',', $c->roe));}
    $tradeapi->set_cache($cache_id, $c, 60);
    $c->cached = false;
} else {
    $c->cached = true;
}

// response
$tradeapi->success($c);

/*
CREATE TABLE `js_trade_currency_finance` (
  `symbol` varchar(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT '거래소 종목 코드(symbol)',
  `years` char(4) COLLATE utf8mb4_general_ci NOT NULL COMMENT '년도',
  `sales` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '매출',
  `opt_profit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '영업이익',
  `net_profit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '순이익',
  `assets` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '자산',
  `debt` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '부채',
  `equity` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '자본',
  `debt_ratio` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '부채비율',
  `roe` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '' COMMENT '자기자본이익률',
  PRIMARY KEY (`symbol`,`years`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPRESSED COMMENT='거래소 상품별 재무정보'
*/


