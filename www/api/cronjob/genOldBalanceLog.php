<?php
/**
 * 특정 회원의 코인 보유량을 가입날짜부터 확인해서 저장하는 스크립트입니다.
 * 테스트 대이터 생성용입니다.
 * 회원별, 코인별 보유량을 날짜별로 저장합니다.
 * 00:00에 실행시키시면됩니다.
 *
 * $ php genOldBalanceLog.php BTC USD 회원번호
 */
define('__API_RUNMODE__', 'live');
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

// 대상 회원 추출
$usernos = array();
// 거래내역에 있는 회원번호
$txs = $tradeapi->query_list_object("select distinct(userno) userno from js_exchange_wallet_txn where symbol='{$tradeapi->escape($SYMBOL)}' ");
foreach($txs as $txn) {
    $usernos[] = $txn->userno;
}
// 주문내역에 있는 회원번호
$orders = $tradeapi->query_list_object("select distinct(userno) userno from js_trade_{$symbol}{$exchange}_order ");
foreach($orders as $o) {
    $usernos[] = $o->userno;
}
$usernos = array_unique($usernos);
// var_dump($usernos); //exit;
foreach($usernos as $userno) {

    // 가입날짜 확인
    $rtime = $tradeapi->query_one("SELECT regdate FROM js_member WHERE userno='{$tradeapi->escape($userno)}'");
    if(!$rtime) {
        echo ($userno.' 가입날짜가 없습니다.'); continue;
    } else {
        $rdate = date('Y-m-d', $rtime);
    }
    // 이전 보유량.
    $pbalance = 0;
    // 마지막 확인 날짜
    $edate = $tradeapi->query_one("SELECT `date` FROM js_trade_daily_balance WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($SYMBOL)}' ");
    $edate = $edate ? $edate : date('Y-m-d');
    // var_dump($edate); exit;
    while($rdate<$edate) {

        $rtime += 60*60*24;
        $ndate = date('Y-m-d', $rtime);

        // 잔액 조회
        if($SYMBOL=='KRW') {
            $t = array();
            $symbols = $tradeapi->query_list_object("select symbol from js_trade_currency ");
            foreach($symbols as $s) {
                $s->symbol = strtolower($s->symbol);
                $t = $tradeapi->query_one("SHOW TABLES LIKE 'js_trade_{$s->symbol}{$exchange}_order'");
                if($t) {
                    $t[] = $s->symbol;
                }
            }
            $symbols = $t;

            // $sql= "SELECT income + buy - outgoing - sell AS balance, income, outgoing, sell, buy FROM (
            //     SELECT
            //     (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($ndate)} 00:00:00' AND direction='I' AND txn_type<>'B') income,
            //     (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($ndate)} 00:00:00' AND direction='O' AND txn_type<>'B') outgoing,";

            // foreach($symbols as $s) {
            //     $sql.= " + IFNULL((SELECT SUM(o.volume_remain*price) FROM js_trade_{$s}{$exchange}_order o WHERE o.userno=w.userno AND `status` IN ('O','T') ),0)";
            // }
            //     (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$symbol}{$exchange}_txn t LEFT JOIN js_trade_{$symbol}{$exchange}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($ndate)} 00:00:00' ) sell,
            // $sql.= ") sell";

            //     (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$symbol}{$exchange}_txn t LEFT JOIN js_trade_{$symbol}{$exchange}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($rdandatete)} 00:00:00' ) buy
            //     ) t";



            // $sql.= ") trading,   IFNULL((SELECT balance FROM js_trade_daily_balance b WHERE b.userno=w.userno AND b.symbol=w.symbol ORDER BY `date` DESC LIMIT 1 ),0)  balance_before
            //     FROM js_exchange_wallet w
            //     WHERE w.symbol='{$symbol}'
            //     ) t WHERE t.confirmed + t.trading <> t.balance_before OR (t.balance_before>0 AND t.confirmed + t.trading=0)";
            // // var_dump($sql); //exit;
        } else {
            $sql = "SELECT income + buy - outgoing - sell AS balance, income, outgoing, sell, buy FROM (
                SELECT
                (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($ndate)} 00:00:00' AND direction='I' AND txn_type<>'B') income,
                (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($ndate)} 00:00:00' AND direction='O' AND txn_type<>'B') outgoing,
                (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$symbol}{$exchange}_txn t LEFT JOIN js_trade_{$symbol}{$exchange}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($ndate)} 00:00:00' ) sell,
                (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$symbol}{$exchange}_txn t LEFT JOIN js_trade_{$symbol}{$exchange}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($rdandatete)} 00:00:00' ) buy
                ) t";

        }


        // $balance = $tradeapi->query_fetch_object($sql);
        $balance = $tradeapi->query_one($sql);
        // var_dump($sql, $balance); exit;
        if($pbalance != $balance) {
            var_dump("INSERT INTO js_trade_daily_balance SET `date`='{$tradeapi->escape($rdate)}', `userno`='{$tradeapi->escape($userno)}', `symbol`='{$tradeapi->escape($SYMBOL)}', `balance`='{$tradeapi->escape($balance)}'");// exit;
            // 잔액 저장
            $tradeapi->query("INSERT INTO js_trade_daily_balance SET `date`='{$tradeapi->escape($rdate)}', `userno`='{$tradeapi->escape($userno)}', `symbol`='{$tradeapi->escape($SYMBOL)}', `balance`='{$tradeapi->escape($balance)}'");
            $pbalance = $balance;
        }

        $rdate = $ndate;
    }

}
echo 'Process end.';
