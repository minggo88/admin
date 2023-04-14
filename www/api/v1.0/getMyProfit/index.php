<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$userno = $tradeapi->setDefault($_REQUEST['userno'], $userno); // 회원번호
$sdate = $tradeapi->setDefault($_REQUEST['sdate'], date('Y-m-01')); // 0000-00-00 형식
$edate = $tradeapi->setDefault($_REQUEST['edate'], ''); // 0000-00-00 형식
$next_date = $edate ? date('Y-m-d', strtotime($edate)+60*60*24) : date('Y-m-d', time()+60*60*24); // edate 포함으로 검색하기위해 다음날짜로 설정
// 다음날까지 검색해야 합니다.
if($next_date>date('Y-m-d', time()+60*60*24)) {
	$next_date = date('Y-m-d', time()+60*60*24); // 기본은 오늘날짜 전까지 
}

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// 리턴 값
$r = (object) array(
	'total'=> (object) array(
		'investment_amount' => 0,         // 투자금액 = ∑ 자산별 (기초평가금액 + ∑입금평가금액 - ∑출금평가금액)
		'investment_income' => 0,         // 투자손익 = ∑ 자산별 (기말평가금액 - 기초평가금액 + ∑출금평가금액 - ∑입금평가금액)
		'investment_income_percent' => 0, // 투자손익률 = (총투자손익 / 총투자금액) * 100
		'basic_evaluation_amount' => 0,   // 기초평가금액 = 기초 USD + 기초 가상자산 평가금액 (해당일 종가, 00시 기준)
		'final_evaluation_amount' => 0,   // 기말평가금액 = 기말 USD + 기말 가상자산 평가금액 (해당일 종가, 00시 기준)
		'available_withdraw_amount' => 0, // 출금가능금액 = USD 잔액
		'available_order_amount' => 0,    // 주문가능금액 = USD 잔액
		'deposit_evaluation_amount' => 0, // 입금평가금액 = 기간 내 입금 USD + 입금 가상자산 평가금액 (해당시간 시세기준)
		'withdraw_evaluation_amount' => 0 // 출금평가금액 = 기간 내 출금 USD + 출금 가상자산 평가금액 (해당시간 시세기준)
	),
	'detail'=>array()
);

$EXCHANGE = strtoupper($tradeapi->default_exchange);
$exchange = strtolower($tradeapi->default_exchange);

// 종목정보
// js_exchange_currency 에서 js_trade_currency로 변경. 매매 종목정보는 js_trade_currency에 저장되기때문입니다.
$t = array();
$currency = $tradeapi->query_list_object("SELECT symbol FROM js_trade_currency "); 
foreach($currency as $c) {
	$c->symbol = strtolower($c->symbol);
	// 테이블 확인.
	if($tradeapi->query_one("SHOW TABLES LIKE 'js_trade_{$c->symbol}{$exchange}_order'")) {
		$t[] = $c->symbol;
	}
}
$currency = $t;

// 지갑 가져오기
$wallet = $tradeapi->query_list_object("SELECT w.symbol, w.confirmed, w.address, c.name, c.symbol, c.icon_url FROM js_exchange_wallet w LEFT JOIN js_trade_currency c ON w.symbol=c.symbol WHERE w.userno='{$tradeapi->escape($userno)}' AND c.symbol IS NOT NULL ");

// 지갑별 상세 정보
foreach($wallet as $w) {
	$SYMBOL = strtoupper($w->symbol);
	$symbol = strtolower($w->symbol);
	if($SYMBOL != 'KRW' && $SYMBOL != 'USD') { // 매매 종목의 잔액 ... 게산하고

		// 기초잔고
		$sql = "SELECT income + buy - outgoing - sell balance,  income, outgoing, sell FROM (
			SELECT
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($sdate)} 00:00:00' AND direction='I' AND txn_type<>'B' AND `status` <> 'C') income,
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($sdate)} 00:00:00' AND direction='O' AND txn_type<>'B' AND `status` <> 'C') outgoing,
			(SELECT IFNULL(SUM(volume),0) FROM js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($sdate)} 00:00:00' ) sell,
			(SELECT IFNULL(SUM(volume),0) FROM js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($sdate)} 00:00:00' ) buy
			) t";
		$s_balance = $tradeapi->query_one($sql);
		// 기초가격
		$s_price = $tradeapi->query_one("SELECT close FROM `js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_chart` WHERE `date`='{$tradeapi->escape($sdate)} 00:00:00' AND term='1d'");
		// 기초평가금액
		$basic_evaluation_amount = $s_balance * $s_price;

		// 기말잔고
		$sql = "SELECT income + buy - outgoing - sell balance,  income, outgoing, sell FROM (
			SELECT
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' AND direction='I' AND txn_type<>'B' AND `status` <> 'C') income,
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' AND direction='O' AND txn_type<>'B' AND `status` <> 'C') outgoing,
			(SELECT IFNULL(SUM(volume),0) FROM js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($next_date)} 00:00:00' ) sell,
			(SELECT IFNULL(SUM(volume),0) FROM js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($next_date)} 00:00:00' ) buy
			) t";
		$e_balance = $tradeapi->query_one($sql);
		// 기말가격
		$e_price = $tradeapi->query_one("SELECT close FROM `js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_chart` WHERE `date`='{$tradeapi->escape($edate)} 00:00:00' AND term='1d'");
		// 기말평가금액
		$final_evaluation_amount = $e_balance * $e_price;

		// 기간 중 입금
		// $sql = "SELECT IFNULL(SUM(amount),0) FROM `js_exchange_wallet_txn` WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($SYMBOL)}' AND txn_type<>'B' AND direction='I'"; // 기간입금수량
		$sql = "SELECT IFNULL(SUM(t.amount* c.close),0) amount FROM js_exchange_wallet_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_chart c ON c.date=DATE(t.txndate) AND c.term='1d' WHERE t.userno='{$tradeapi->escape($userno)}' AND t.txn_type<>'B' AND t.direction='I' AND `status` <> 'C' AND t.symbol='{$tradeapi->escape($SYMBOL)}' AND '{$tradeapi->escape($sdate)} 00:00:00'<=txndate AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' "; // 기간 입금액 SUM(입금수량 * 가격)
		$deposit_evaluation_amount = $tradeapi->query_one($sql); // 기간 입금액 SUM(입금수량 * 가격)

		// 기간 중 출금
		// $sql = "SELECT IFNULL(SUM(amount+fee+tax),0) FROM `js_exchange_wallet_txn` WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($SYMBOL)}' AND txn_type<>'B' AND direction='O'"; // 기간출금수량
		$sql = "SELECT IFNULL(SUM(t.amount* c.close),0) amount FROM js_exchange_wallet_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_chart c ON c.date=DATE(t.txndate) AND c.term='1d' WHERE t.userno='{$tradeapi->escape($userno)}' AND t.txn_type<>'B' AND t.direction='O' AND `status` <> 'C' AND t.symbol='{$tradeapi->escape($SYMBOL)}' AND '{$tradeapi->escape($sdate)} 00:00:00'<=txndate AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' "; // 기간 입금액 SUM(입금수량 * 가격)
		$withdraw_evaluation_amount = $tradeapi->query_one($sql); // 기간출금액 SUM(출금수량 * 가격)

		// 매도금액 krw에서 증액으로 계산합니다.
		// $sql = "SELECT IFNULL(SUM(volume*price),0) FROM js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND '{$tradeapi->escape($sdate)} 00:00:00'<time_traded AND time_traded<'{$tradeapi->escape($next_date)} 00:00:00' "; // 기간 입금액 SUM(입금수량 * 가격)
		// $sell_amount = $tradeapi->query_one($sql); // 기간출금액 SUM(출금수량 * 가격)

		// 코인 데이터
		$coin = (object) array(
			'symbol'=>$w->symbol,
			'icon_url'=>$w->icon_url,
			'name'=>__($w->name),
			// 'sell_amount'=>$sell_amount,
			'basic_balance'=>$s_balance,
			'basic_price'=>$s_price,
			'basic_evaluation_amount'=>$basic_evaluation_amount,
			'final_balance'=>$e_balance,
			'final_price'=>$e_price,
			'final_evaluation_amount'=>$final_evaluation_amount,
			'deposit_evaluation_amount'=>$deposit_evaluation_amount,
			'withdraw_evaluation_amount'=>$withdraw_evaluation_amount,
			'investment_amount'=>$basic_evaluation_amount + $deposit_evaluation_amount - $withdraw_evaluation_amount,
			'investment_income'=>$final_evaluation_amount - $basic_evaluation_amount - $deposit_evaluation_amount + $withdraw_evaluation_amount, // + $sell_amount,
		);
		$r->detail[$w->symbol] = $coin;

		// 전체 데이터
		$r->total->investment_amount += $coin->investment_amount;         // 투자금액 = ∑ 자산별 (기초평가금액 + ∑입금평가금액 - ∑출금평가금액)
		$r->total->investment_income += $coin->investment_income;         // 투자손익 = ∑ 자산별 (기말평가금액 - 기초평가금액 + ∑출금평가금액 - ∑입금평가금액)
		$r->total->basic_evaluation_amount += $coin->basic_evaluation_amount;   // 기초평가금액 = 기초 USD + 기초 가상자산 평가금액 (해당일 종가, 00시 기준)
		$r->total->final_evaluation_amount += $coin->final_evaluation_amount;   // 기말평가금액 = 기말 USD + 기말 가상자산 평가금액 (해당일 종가, 00시 기준)
		$r->total->deposit_evaluation_amount += $coin->deposit_evaluation_amount;   // 입금평가금액 = 기간 내 입금 USD + 입금 가상자산 평가금액 (해당시간 시세기준)
		$r->total->withdraw_evaluation_amount += $coin->withdraw_evaluation_amount;   // 출금평가금액 = 기간 내 출금 USD + 출금 가상자산 평가금액 (해당시간 시세기준)

	} else { // USD 일때

		// 매매한 종목 찾기
		$currency_buy = array();
		$currency_sell = array();
		foreach($currency as $c) {
			$sql = "SELECT 
			(SELECT COUNT(*) FROM js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($sdate)} 00:00:00' ) sell
			,( SELECT COUNT(*) FROM js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($sdate)} 00:00:00') buy";
			$t = $tradeapi->query_fetch_object($sql);
			if($t->buy>0) $currency_buy[] = $c;
			if($t->sell>0) $currency_sell[] = $c;
		}



		// 기초잔고
		$sql = "SELECT income - outgoing + sell - buy balance,  income, outgoing, sell FROM (
			SELECT
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($sdate)} 00:00:00' AND direction='I' AND txn_type<>'B' AND `status` <> 'C') income,
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($sdate)} 00:00:00' AND direction='O' AND txn_type<>'B' AND `status` <> 'C') outgoing,";
		// sell
		$sql.= "( 0 ";
		foreach($currency_sell as $c) {
			$sql.= " + (SELECT IFNULL(SUM(volume*price - fee),0) FROM js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($sdate)} 00:00:00' )";
		}
		$sql.= ") sell, ";
		// buy
		$sql.= "( 0 ";
		foreach($currency_buy as $c) {
			$sql.= " + (SELECT IFNULL(SUM(volume*price),0) FROM js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($sdate)} 00:00:00' )";
		}
		$sql.= ") buy ";
		$sql.= ") t";
		$basic_evaluation_amount = $tradeapi->query_one($sql);

		// 기말잔고
		$sql = "SELECT income - outgoing + sell - buy balance,  income, outgoing, sell FROM (
			SELECT
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' AND direction='I' AND txn_type<>'B' AND `status` <> 'C') income,
			(SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($SYMBOL)}' AND userno='{$tradeapi->escape($userno)}' AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' AND direction='O' AND txn_type<>'B' AND `status` <> 'C') outgoing,";
		// sell 
		$sql.= "( 0 ";
		foreach($currency as $c) {
			$sql.= " + (SELECT IFNULL(SUM(volume*price - fee),0) FROM js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND time_traded<'{$tradeapi->escape($next_date)} 00:00:00' )";
		}
		$sql.= ") sell, ";
		// buy
		$sql.= "( 0 ";
		foreach($currency as $c) {
			$sql.= " + (SELECT IFNULL(SUM(volume*price),0) FROM js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($c)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_buy=ot.orderid AND time_traded<'{$tradeapi->escape($next_date)} 00:00:00' )";
		}
		$sql.= ") buy ";
		$sql.= ") t";
		// var_dump($sql);
		$final_evaluation_amount = $tradeapi->query_one($sql);

		// 기간 중 입금
		// $sql = "SELECT IFNULL(SUM(amount),0) FROM `js_exchange_wallet_txn` WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($SYMBOL)}' AND txn_type<>'B' AND direction='I'"; // 기간입금수량
		$sql = "SELECT IFNULL(SUM(t.amount),0) amount FROM js_exchange_wallet_txn t WHERE t.userno='{$tradeapi->escape($userno)}' AND t.txn_type<>'B' AND t.direction='I' AND `status` <> 'C' AND t.symbol='{$tradeapi->escape($SYMBOL)}' AND '{$tradeapi->escape($sdate)} 00:00:00'<=txndate AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' "; // 기간 입금액 SUM(입금수량 * 가격)
		$deposit_evaluation_amount = $tradeapi->query_one($sql); // 기간 입금액 SUM(입금수량 * 가격)

		// 기간 중 출금
		// $sql = "SELECT IFNULL(SUM(amount+fee+tax),0) FROM `js_exchange_wallet_txn` WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($SYMBOL)}' AND txn_type<>'B' AND direction='O'"; // 기간출금수량
		$sql = "SELECT IFNULL(SUM(t.amount),0) amount FROM js_exchange_wallet_txn t WHERE t.userno='{$tradeapi->escape($userno)}' AND t.txn_type<>'B' AND t.direction='O' AND `status` <> 'C' AND t.symbol='{$tradeapi->escape($SYMBOL)}' AND '{$tradeapi->escape($sdate)} 00:00:00'<=txndate AND txndate<'{$tradeapi->escape($next_date)} 00:00:00' "; // 기간 입금액 SUM(입금수량 * 가격)
		$withdraw_evaluation_amount = $tradeapi->query_one($sql); // 기간출금액 SUM(출금수량 * 가격)

		// 매도금액
		// $sql = "SELECT IFNULL(SUM(volume*price),0) FROM js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_txn t LEFT JOIN js_trade_{$tradeapi->escape($symbol)}{$tradeapi->escape($exchange)}_ordertxn ot ON t.txnid=ot.txnid WHERE ot.userno='{$tradeapi->escape($userno)}' AND t.orderid_sell=ot.orderid AND '{$tradeapi->escape($sdate)} 00:00:00'<time_traded AND time_traded<'{$tradeapi->escape($next_date)} 00:00:00' "; // 기간 입금액 SUM(입금수량 * 가격)
		// $sell_amount = $tradeapi->query_one($sql); // 기간출금액 SUM(출금수량 * 가격)

		// 코인 데이터
		$coin = (object) array(
			'symbol'=>$w->symbol,
			'icon_url'=>$w->icon_url,
			'name'=>__($w->name),
			// 'sell_amount'=>$sell_amount,
			'basic_balance'=>$basic_evaluation_amount,
			'basic_evaluation_amount'=>$basic_evaluation_amount, // 기초평가금액
			'final_balance'=> $edate==date('Y-m-d') ? $w->confirmed : $final_evaluation_amount, 
			'final_evaluation_amount'=>$final_evaluation_amount, // 기말평가금액
			'deposit_evaluation_amount'=>$deposit_evaluation_amount, // 입금평가금액
			'withdraw_evaluation_amount'=>$withdraw_evaluation_amount, // 출금평가금액
			'investment_amount'=>$basic_evaluation_amount + $deposit_evaluation_amount - $withdraw_evaluation_amount, // 투자금액
			'investment_income'=>$final_evaluation_amount - $basic_evaluation_amount - $deposit_evaluation_amount + $withdraw_evaluation_amount + $sell_amount, // 투자손익
		);
		$r->detail[$w->symbol] = $coin;

		// 전체 데이터
		$r->total->investment_amount += $coin->investment_amount;         // 투자금액 = ∑ 자산별 (기초평가금액 + ∑입금평가금액 - ∑출금평가금액)
		$r->total->investment_income += $coin->investment_income;         // 투자손익 = ∑ 자산별 (기말평가금액 - 기초평가금액 + ∑출금평가금액 - ∑입금평가금액)
		$r->total->basic_evaluation_amount += $coin->basic_evaluation_amount;   // 기초평가금액 = 기초 USD + 기초 가상자산 평가금액 (해당일 종가, 00시 기준)
		$r->total->final_evaluation_amount += $coin->final_evaluation_amount;   // 기말평가금액 = 기말 USD + 기말 가상자산 평가금액 (해당일 종가, 00시 기준)
		$r->total->deposit_evaluation_amount += $coin->deposit_evaluation_amount;   // 입금평가금액 = 기간 내 입금 USD + 입금 가상자산 평가금액 (해당시간 시세기준)
		$r->total->withdraw_evaluation_amount += $coin->withdraw_evaluation_amount;   // 출금평가금액 = 기간 내 출금 USD + 출금 가상자산 평가금액 (해당시간 시세기준)
		$r->total->available_withdraw_amount = $w->confirmed; // 출금가능원화은 현제 잔액으로 표시.  $coin->final_evaluation_amount; // 출금가능금액 = USD 잔액
		$r->total->available_order_amount = $w->confirmed; // 주문가능원화은 현제 잔액으로 표시.  $coin->final_evaluation_amount;    // 주문가능금액 = USD 잔액
	}

}

$r->total->investment_income_percent = $r->total->investment_amount>0 ? round(($r->total->investment_income / $r->total->investment_amount) * 100, 2) : 0 ;  // 투자손익률 = (총투자손익 / 총투자금액) * 100

// response
$tradeapi->success($r);
