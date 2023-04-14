<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol')));
$address = checkEmpty($_REQUEST['address'], 'address');

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

$c = $tradeapi->validate_address($symbol, $address);
if($c) {
	// 내부 지갑인지 확인. 내부지갑은 수수료 0 처리 하기 위함.
	$inner_wallet = $tradeapi->query_fetch_object("SELECT * from kmcse_trade.js_exchange_wallet WHERE address='{$tradeapi->escape($address)}' and symbol='{$tradeapi->escape($symbol)}' ");
	if(!$inner_wallet) {
		$inner_wallet = $tradeapi->query_fetch_object("SELECT * FROM morrow_wallet.js_wallet_wallet WHERE address='{$tradeapi->escape($address)}' and symbol='{$tradeapi->escape($symbol)}' ");
	}
	$r = array('validate'=>true, 'inner_wallet'=>$inner_wallet ? true : false);
} else {
	$r = array('validate'=>false, 'inner_wallet'=>false);
}

// response
$tradeapi->success($r);
