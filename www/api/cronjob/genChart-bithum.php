<?php
/**
 * 차트 데이터 빗썸에서 가져오기. 
 * 외부 차트 데이터와 유사하게 만들기 위함.
 * 개발서버만 사용. 라이브는 사용 안함.
 */
require("../lib/TradeApi.php");
if(__API_RUNMODE__ == 'live') { exit('It is not used in live server.'); }
// 데이터 
ob_implicit_flush(true);
ini_set('output_buffering','off');
ini_set('zlib.output_compression', 0);
ob_start();

$full = $argv[1] ? 'yes' : 'no';

// $symbols = array('BTC', 'ETH', 'LTC', 'BCH', 'QTUM', 'EOS', 'XRP','SCC');
$symbols = array('BTC');
// $sql = "SELECT symbol, name FROM js_trade_currency WHERE active='Y' AND crypto_currency='Y'";
// $currencies = $tradeapi->query_list_object($sql);
$intervals = array('01M', '03M', '05M', '10M', '30M', '01H', '12H', '24H');
// $intervals = array('12H');

foreach($symbols as $symbol) {
// foreach($currencies as $currency) {
//     $symbol = $currency->symbol;

    foreach($intervals as $interval) {
        echo "symbol: $symbol, interval: $interval start \n"; ob_flush();
        // 현재가 가져오기.
        $sql = "select price_close from js_trade_price where symbol='{$symbol}' AND exchange='{$tradeapi->default_exchange}' ";
        $price_close = $tradeapi->query_fetch_object($sql);
        $price_close = $price_close->price_close;


        // 빗썸 데이터 가져오기.
        $etime = time();
        $stime = $etime - 60*60*24*60;
        $time2 = mt_rand(111,999);
        $tmp_symbol = $symbol=='SCC' ? 'ZIL' : $symbol;
        $url = "https://www.bithumb.com/resources/chart/{$tmp_symbol}_xcoinTrade_{$interval}.json?symbol={$symbol}&resolution=0.5&from={$stime}&to={$etime}&strTime={$etime}{$time2}";
        $data = $tradeapi->remote_get($url);
        $data = json_decode($data);
        if(is_array($data) && count($data)>0) {
            // 빗썸 현재가 가져오기.
            $current = end($data);

            // $price_close_bitthumb = $current[2]/1000;
            // 가격 변환비 계산.
            // $ratio = $price_close / $price_close_bitthumb;
            $ratio = 0.98;
            // var_dump($current[1], $current[1]*$ratio); exit;

            // 차트 데이터 입력.
            foreach($data as $row) {
                if($full=='no') {
                    $row = end($data);
                }
                switch($interval) {
                    case '01M': $term = '1m'; break;
                    case '03M': $term = '3m'; break;
                    case '05M': $term = '5m'; break;
                    case '10M': $term = '10m'; break;
                    case '30M': $term = '30m'; break;
                    case '01H': $term = '1h'; break;
                    case '12H': $term = '12h'; break;
                    case '24H': $term = '1d'; break;
                }
                // var_dump($row);
                //1535940000000,8128000,8110000,8136000,8102000,32606.68047907
                // 날짜, 시가, 종가, 고가, 저가, 거래량
                $d = $row[0]/1000;
                $p1 = number_format($row[1]*$ratio, 2, '.', '');
                $p2 = number_format($row[2]*$ratio, 2, '.', '');
                $p3 = number_format($row[3]*$ratio, 2, '.', '');
                $p4 = number_format($row[4]*$ratio, 2, '.', '');
                $v5 = number_format($row[5], 4, '.', '');

                $price = " open='{$p1}', close='{$p2}', high='{$p3}', low='{$p4}', volume='{$v5}' ";
                if($full=='yes') {
                    $sql = "insert into js_trade_".strtolower($symbol)."krw_chart set term='{$term}', date='".date('Y-m-d H:i:s', $d)."', {$price} ON DUPLICATE KEY UPDATE {$price} ";
                } else {
                    $sql = "insert ignore into js_trade_".strtolower($symbol)."krw_chart set term='{$term}', date='".date('Y-m-d H:i:s', $d)."', {$price} ";
                }
                // echo $sql."\n";exit;
                $tradeapi->query($sql);
                if($full=='no') {
                    break;
                }
            }
        }
        echo "symbol: $symbol, interval: $interval end \n"; ob_flush();
        sleep(1);
        // exit;
    }

    // 24시간 마지막 데이터 추출.
    $sql = "SELECT open,high,low,close,volume FROM js_trade_".strtolower($symbol)."krw_chart WHERE term='1d' ORDER BY DATE DESC LIMIT 1 ";
    $p24 = $tradeapi->query_fetch_object($sql);
    $sql = "SELECT open,high,low,close,volume FROM js_trade_".strtolower($symbol)."krw_chart WHERE term='12h' ORDER BY DATE DESC LIMIT 1 ";
    $p12 = $tradeapi->query_fetch_object($sql);
    $sql = "SELECT open,high,low,close,volume FROM js_trade_".strtolower($symbol)."krw_chart WHERE term='1h' ORDER BY DATE DESC LIMIT 1 ";
    $p1 = $tradeapi->query_fetch_object($sql);

    $sql = "update js_trade_price set ";
    $p = round(($p24->close - $p24->open) / $p24->open * 100, 2);   
    $sql .=" price_open='{$tradeapi->escape($p24->open)}', price_high='{$tradeapi->escape($p24->high)}', price_low='{$tradeapi->escape($p24->low)}', price_close='{$tradeapi->escape($p24->close)}', volume='{$tradeapi->escape($p24->volume)}', price_chagne_percent='{$p}' , ";
    $p = round(($p12->close - $p12->open) / $p12->open * 100, 2);
    $sql .=" price_open_12='{$tradeapi->escape($p12->open)}', price_high_12='{$tradeapi->escape($p12->high)}', price_low_12='{$tradeapi->escape($p12->low)}', price_close_12='{$tradeapi->escape($p12->close)}', volume_12='{$tradeapi->escape($p12->volume)}', price_chagne_percent='{$p}' , ";
    $p = round(($p1->close - $p1->open) / $p1->open * 100, 2);
    $sql .=" price_open_1='{$tradeapi->escape($p1->open)}', price_high_1='{$tradeapi->escape($p1->high)}', price_low_1='{$tradeapi->escape($p1->low)}', price_close_1='{$tradeapi->escape($p1->close)}', volume_1='{$tradeapi->escape($p1->volume)}', price_chagne_percent='{$p}'  ";
    $sql .=" where symbol='{$tradeapi->escape($symbol)}'  ";
    $tradeapi->query($sql);
    exit($sql);

}


