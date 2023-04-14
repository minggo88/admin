<?php
/**
 * 보유량 저장 스크립트
 * 회원별, 코인별 보유량을 날짜별로 저장합니다.
 * 00:00에 실행시키시면됩니다.
 *
 * $ php genBalanceLog.php BTC USD
 */
// define('__API_RUNMODE__', 'live');
include(dirname(__file__).'/../lib/TradeApi.php');

ignore_user_abort(1);
set_time_limit(0);

$SYMBOL = $argv[1] ? strtoupper($argv[1]) : '';
$EXCHANGE = $argv[2] ? strtoupper($argv[2]) : $tradeapi->default_exchange; // usd, krw
$symbol = strtolower($SYMBOL);
$exchange = strtolower($EXCHANGE);
$tradeapi->checkSymbol($symbol);
$tradeapi->checkSymbol($exchange);

$filename = __file__;
if(! $symbol) {
    exit('Please, set the symbol.');
}

// 프로세스 작동중인지 확인. 작동중이면 종료.
@ exec("ps  -ef| grep -i '{$filename} {$SYMBOL} {$EXCHANGE}' | grep -v grep", $output);
if(count($output)>1) {
    exit();
}

echo "$symbol start \n"; ob_flush();

// 잔액 저장
$sql = "INSERT INTO js_trade_daily_balance
SELECT ydate, userno, symbol, confirmed+trading  FROM(
    SELECT
        SUBDATE(CURDATE(),1) ydate, w.userno, w.symbol, w.confirmed,
        IFNULL((SELECT SUM(o.volume_remain) FROM js_trade_{$symbol}{$exchange}_order o WHERE o.userno=w.userno AND `status` IN ('O','T') ),0) trading,
        IFNULL((SELECT balance FROM js_trade_daily_balance b WHERE b.userno=w.userno AND b.symbol=w.symbol ORDER BY `date` DESC LIMIT 1 ),0)  balance_before
    FROM js_exchange_wallet w
    WHERE w.symbol='{$symbol}'
    ) t WHERE t.confirmed + t.trading <> t.balance_before OR (t.balance_before>0 AND t.confirmed + t.trading=0)";
if($SYMBOL=='KRW') {
    $sql = "INSERT INTO js_trade_daily_balance
    SELECT ydate, userno, symbol, confirmed+trading  FROM(
        SELECT
            SUBDATE(CURDATE(),1) ydate, w.userno, w.symbol, w.confirmed, ( 0 ";
    $symbols = $tradeapi->query_list_object("select symbol from js_trade_currency ");
    foreach($symbols as $s) {
        $s->symbol = strtolower($s->symbol);
        // 테이블 확인.
        $t = $tradeapi->query_one("SHOW TABLES LIKE 'js_trade_{$s->symbol}{$exchange}_order'");
        if($t) {
            $sql.= " + IFNULL((SELECT SUM(o.volume_remain*price) FROM js_trade_{$s->symbol}{$exchange}_order o WHERE o.userno=w.userno AND `status` IN ('O','T') ),0)";
        }
    }
    $sql.= ") trading,   IFNULL((SELECT balance FROM js_trade_daily_balance b WHERE b.userno=w.userno AND b.symbol=w.symbol ORDER BY `date` DESC LIMIT 1 ),0)  balance_before
        FROM js_exchange_wallet w
        WHERE w.symbol='{$symbol}'
        ) t WHERE t.confirmed + t.trading <> t.balance_before OR (t.balance_before>0 AND t.confirmed + t.trading=0)";
    // var_dump($sql); //exit;
}
$tradeapi->query($sql);


echo 'Process end.';
