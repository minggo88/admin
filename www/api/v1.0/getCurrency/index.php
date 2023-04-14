<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(setDefault($_REQUEST['symbol'], 'ALL')));
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange)));
$name = setDefault($_REQUEST['name'], '');
$cal_base_price = setDefault($_REQUEST['cal_base_price'], '');
$cal_price_change = setDefault($_REQUEST['cal_price_change'], '');

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 전체 조회시.
if($symbol=='ALL') {
    $symbol = '';
}

// check wallet owner
$tradeapi->set_cache_dir(__SRF_DIR__.'/cache/getCurrency/');
$cache_id = 'getCurrency-'.$symbol.'-'.$name;
$cachetime = 60;
$c = $tradeapi->get_cache($cache_id, $cachetime);
// if($c=='') {
    $c = $tradeapi->set_cache($cache_id, $tradeapi->get_currency($symbol, '', $name), $cachetime);
// }
$tradeapi->clear_old_file($cachetime);


if($c && $cal_base_price=='Y') {
    
// SELECT 'eth' symbol, SUM(volume * price) / SUM(volume) prev_avg_price FROM `js_trade_ethkrw_txn` FORCE INDEX(time_traded) WHERE time_traded LIKE CONCAT((SELECT DATE(MAX(time_traded)) FROM `js_trade_ethkrw_order` WHERE `status` IN ('T', 'C')),'%')
// UNION ALL
// SELECT 'btc' symbol, SUM(volume * price) / SUM(volume) prev_avg_price FROM `js_trade_btckrw_txn` FORCE INDEX(time_traded) 
// WHERE time_traded LIKE CONCAT((SELECT DATE(MAX(time_traded)) FROM `js_trade_btckrw_order` WHERE `status` IN ('T', 'C')),'%')

    $sql = array();
    foreach($c as $row) {
        if($row->tradable!='Y' || !$row->symbol) continue; // 매매가능 종목만 계산합니다.
        $symbol = strtolower($row->symbol);
        $SYMBOL = strtoupper($row->symbol);
        $sql[] = "SELECT '{$SYMBOL}' symbol, ROUND(SUM(volume * price) / SUM(volume)) prev_avg_price FROM `js_trade_{$symbol}{$exchange}_txn` FORCE INDEX(time_traded) WHERE time_traded LIKE CONCAT((SELECT DATE(MAX(time_traded)) FROM `js_trade_{$symbol}{$exchange}_txn` FORCE INDEX(time_traded)  WHERE time_traded < DATE(NOW())),'%')";
    }
    $base_prices = $tradeapi->query_list_object(implode(' UNION ALL ', $sql));
    for($i=0; $i<count($c); $i++) {
        $symbol = $c[$i]->symbol;
        foreach($base_prices as $p) {
            if($symbol == $p->symbol) {
                $c[$i]->base_price = $p->prev_avg_price;
                break;
            }
        }
    }
}

// 가격 변동 계산
if($c && $cal_price_change=='Y') {

    
    for($i=0; $i<count($c); $i++) {
        $row = $c[$i];
        $currency = $tradeapi->get_currency($row->symbol);
        $currency = $currency ? $currency[0] : null;
        if(!$currency || $currency->tradable!='Y') continue;
        $js_trade_price = $tradeapi->get_spot_price($row->symbol, $row->exchange);
        $js_trade_price = $js_trade_price ? $js_trade_price[0] : null;
        if(!$js_trade_price) continue;
        // var_dump($js_trade_price, $row->symbol, $js_trade_price->price_close ,$js_trade_price->price_open, $js_trade_price->price_close - $js_trade_price->price_open); exit;
        $js_trade_price->display_decimals = $currency->display_decimals;
        $js_trade_price->price_diff = ($js_trade_price->price_close - $js_trade_price->price_open);
        $js_trade_price->price_diff_str = real_number_format($js_trade_price->price_diff, $currency->display_decimals);
        $js_trade_price->price_diff_per = ($js_trade_price->price_close - $js_trade_price->price_open)/$js_trade_price->price_close * 100;
        $js_trade_price->price_diff_per_str = real_number_format($js_trade_price->price_diff_per, 2);
        $js_trade_price->trade_value = real_number_format(($js_trade_price->price_close * $js_trade_price->volume), 0);
        $js_trade_price->trade_value_str = real_number_format($js_trade_price->trade_value, 0);
        $c[$i] = (object) array_merge((array) $row, (array) $js_trade_price);
    }
}

// response
$tradeapi->success($c);
