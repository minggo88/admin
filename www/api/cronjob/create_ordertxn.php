<?php

include(dirname(__file__).'/../lib/TradeApi.php');

ignore_user_abort(1);
set_time_limit(0);


$symbol = $argv[1] ? strtoupper($argv[1]) : 'SCC';
$exchange = $argv[2] ? strtoupper($argv[2]) : $tradeapi->default_exchange;
$table_txn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn';
$table_order = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
$table_ordertxn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_ordertxn';

// 테이블 있는지 확인 
$sql = "show tables like '{$table_ordertxn}' ";
$r = $tradeapi->query_fetch_object($sql);
if(!$r) {
    $sql = "CREATE TABLE `{$table_ordertxn}` (
        `userno` bigint(20) NOT NULL COMMENT '회원번호',
        `orderid` bigint(20) NOT NULL COMMENT '주문번호',
        `txnid` bigint(20) NOT NULL COMMENT '거래번호',
        KEY `userno` (`userno`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원별 주문번호, 매매번호를 연결한 인덱스용 테이블'";
    $tradeapi->query($sql);
}

$sql = "TRUNCATE TABLE `$table_ordertxn`";
$tradeapi->query($sql);

$p = 0;
$row = 100;
while($p>=0) {
    $s = $p * $row;
    $sql = "select orderid, userno from $table_order where status in ('C', 'T') and userno not in(814,815) limit $s, $row"; 
    $orders = $tradeapi->query_list_object($sql);
    if(count($orders)<1) {
        echo "작업 완료.".PHP_EOL;
        break;
    }
    foreach($orders as $order) {
        echo "$order->orderid 작업 시작, ";
        $sql = "select txnid from $table_txn where orderid_buy='$order->orderid' ";
        $txns = $tradeapi->query_list_object($sql);
        if(count($txns)<1) {
            continue;
        }
        $sql_i = "insert into $table_ordertxn (userno, orderid, txnid) values ";
        $sql_v = array();
        foreach($txns as $txn) {
            $sql_v[] = "($order->userno, $order->orderid, $txn->txnid) ";
        }
        
        $sql = "select txnid from $table_txn where orderid_sell='$order->orderid' ";
        $txns = $tradeapi->query_list_object($sql);
        // var_dump($sql, $txns); exit;
        foreach($txns as $txn) {
            $sql_v[] = "($order->userno, $order->orderid, $txn->txnid) ";
        }
        $sql = $sql_i.implode(',', $sql_v);
        $tradeapi->query($sql);
        echo "끝 ".PHP_EOL;
    }
    $p++;
    // exit;
}
