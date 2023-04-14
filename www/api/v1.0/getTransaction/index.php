<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// validate parameters
$txnid = checkNumber(setDefault($_REQUEST['txnid'], '0'));

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// check previos address
$txns = $tradeapi->find_wallet_txn_list(array('txnid'=>$txnid));
$txns = $txns[0];

$t = array();
$t['txnid'] = $txns->txnid;
$t['symbol'] = $txns->symbol;
$t['amount'] = $txns->amount;
$t['fee'] = $txns->fee;
$t['tax'] = $txns->tax;
if($txns->txn_type=='R') {
    if($txns->direction=='O') {
        $t['from_address'] = $txns->address;
        $t['to_address'] = $txns->address_relative;
    } else {
        $t['from_address'] = $txns->address_relative;
        $t['to_address'] = $txns->address;
    }
}
if($txns->txn_type=='S'||$txns->txn_type=='W') {
    if($txns->direction=='I') {
        $t['from_address'] = $txns->address_relative;
        $t['to_address'] = $txns->address;
    } else {
        $t['from_address'] = $txns->address;
        $t['to_address'] = $txns->address_relative;
    }
}

// response
$tradeapi->success($t);
