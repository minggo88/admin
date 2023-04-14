<?php
/**
 * 바로구매
 * 특정 매도주문건(orderid)에 대해서만 매매하는 api 메소드입니다.
 * orderid 주문정보의 가격으로 거래됩니다.
 * orderid 주문정보의 수량까지만 거래됩니다.
 */
include dirname(__file__) . "/../../lib/TradeApi.php";

$tradeapi->set_logging(true);
$tradeapi->set_log_dir(__dir__.'/../../log/'.basename(__dir__).'/');
$tradeapi->set_log_name('');
$tradeapi->write_log("REQUEST: " . json_encode($_REQUEST));

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();
$userno_buy = $userno;

// validate parameters
$orderid = checkEmpty($_REQUEST['orderid'], 'orderid'); // 주문번호
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol'))); // 코인
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange))); // 구매 화폐
$price = checkZero(checkNumber($_REQUEST['price']), 'price');// 가격
$volume = checkZero(checkNumber($_REQUEST['volume']), 'volume');// 구매량
$amount = setDefault($_REQUEST['amount'], ''); // 구매금액
// 소숫점 4자리까지 수량을 쓸 수 있도록 하기.
if($volume<0.0001) {
    $tradeapi->error('000', __('Please enter only 4 decimal places for quantity.')); //수량에는 소수점 4자리까지만 입력해주세요.
}

// --------------------------------------------------------------------------- //
// 매매 시간 확인 ( 9 ~ 18시 ) , 토요일(6)/일요일(7) 에는 매매 중지
// if(date('H') < 9 || 18 <= date('H') || date('N')=='6' || date('N')=='7') {
//     $tradeapi->error('100', '매매시간이 아닙니다. 평일 오전 9에서 오후 6시 사이에 매매해주세요.');
// }
// 매매 설정 확인
$config_basic = $tradeapi->get_config('js_config_basic');
if($config_basic->bool_trade!='1') {
    $tradeapi->error('100', '매매시간이 아닙니다. 평일 오전 9에서 오후 6시 사이에 매매해주세요.');
}

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// 화폐 정보
$currency = $tradeapi->db_get_row('js_trade_currency', array('symbol'=>$symbol));
// 최소 거래량 확인.
if($currency->trade_min_volume>0 && $currency->trade_min_volume > $volume ) {
	$tradeapi->error('041',str_replace(array('{trade_min_volume}','{symbol}'), array($currency->trade_min_volume*1, $symbol), __('거래수량을 {trade_min_volume} {symbol}이상으로 입력해주세요.')));
}

// 주문정보
$order_info = $tradeapi->db_get_row('js_trade_'.strtolower($symbol).strtolower($exchange).'_order', array('orderid'=>$orderid));
// var_dump($order_info); exit;
if(empty($order_info->orderid)) {
    $tradeapi->error('043', __('주문정보를 찾을 수 없습니다.'));
}
if($order_info->trading_type != 'S' ) {
    $tradeapi->error('046', __('매도 주문을 선택해주세요.'));
}
if($order_info->volume_remain <= 0 ) {
    $tradeapi->error('044', __('거래가능한 주문수량이 없습니다.'));
}

// 구매주문가격이 판매가격과 같은지 확인
if($price != $order_info->price) {
    $tradeapi->error('045', __('판매가격과 다른 구매가격을 입력하여 구매하지 못했습니다.'));
}

// 매매 가격 범위 밖인지 확인.
$trade_price_info = (object) $tradeapi->get_trade_price_info($symbol, $exchange);
if($trade_price_info->trade_max_price && $trade_price_info->trade_max_price < $price) {
    $tradeapi->error('101','매매 가격 범위('.number_format($trade_price_info->trade_min_price).' ~ '.number_format($trade_price_info->trade_max_price).')로 매매하실 수 있습니다.');
}
if($trade_price_info->trade_min_price && $trade_price_info->trade_min_price > $price) {
    $tradeapi->error('102','매매 가격 범위('.number_format($trade_price_info->trade_min_price).' ~ '.number_format($trade_price_info->trade_max_price).')로 매매하실 수 있습니다.');
}

// 현재가
$current_price = $tradeapi->get_spot_price($symbol, $exchange);
$min_sell_price = $tradeapi->get_min_sell_price($symbol, $exchange);
if(count($current_price)>0) {
    $current_price = $current_price[0];
    $current_price = $current_price->price_close;
} else { // 거래가 없는경우 현재가는 매도1호가로 설정.
    // 매도 1호가
    $current_price = $min_sell_price;
}
// var_dump($current_price, $min_sell_price); exit;

// 주문수량 확인 - 판매수량보다 많으면 안됨.
if($volume > $order_info->volume_remain) {
    $tradeapi->error('042', __('남은 판매수량보다 많이 주문하실 수 없습니다.'));
}

// 지갑 - 구매금액을 확인해야 해서 $exchange 지갑을 가져옵니다.
$wallet_exchange = $tradeapi->db_get_row('js_exchange_wallet',  array('userno'=>$userno_buy, 'symbol'=>$exchange));
if(!$wallet_exchange || !$wallet_exchange->address) {// 구매자 지갑 없으면 생성.
    $tradeapi->create_new_trade_wallet($userno_buy, $exchange);
    $wallet_exchange = $tradeapi->db_get_row('js_exchange_wallet',  array('userno'=>$userno_buy, 'symbol'=>$exchange));
}
// check locked
if($wallet_exchange->locked != 'N') {
	$tradeapi->error('048', str_replace('{symbol}', $exchange, __('{symbol}지갑이 잠겨있어 매도하실 수 없습니다.')));
}
$wallet_symbol = $tradeapi->db_get_row('js_exchange_wallet',  array('userno'=>$userno_buy, 'symbol'=>$symbol));
if(!$wallet_symbol || !$wallet_symbol->address) {// 구매자 지갑 없으면 생성.
    $tradeapi->create_new_trade_wallet($userno_buy, $symbol);
    $wallet_symbol = $tradeapi->db_get_row('js_exchange_wallet',  array('userno'=>$userno_buy, 'symbol'=>$symbol));
}
// check locked
if($wallet_symbol->locked != 'N') {
	$tradeapi->error('048', str_replace('{symbol}', $symbol, __('{symbol}지갑이 잠겨있어 매도하실 수 없습니다.')));
}

// 구매금액 -  주문가와 주문수량으로 구매금액을 계산하고 잔액을 확인만 함. 실제 구매금액은 거래 금액별로 다시 계산합니다.
$amount = $amount ? $amount : $price * $volume;
// 지불금액이 1원 밑이면 alert
if($amount<1) {
    $tradeapi->error('000', __('Your payment amount is too low. Please raise the quantity.')); // 지불금액이 너무 낮습니다. 수량을 올려주세요.
}
// 총 수수료
// 매도/매수 어떤거든 주문할때는 수수료가 선 차감하는것 없음. 매매가 발생할때 차감됨.
// 매수자는 수수료/세금 차감 없음.
// 매도자는 수수료/세금 차감함.
$fee = $tradeapi->cal_fee($exchange, 'buy', $amount);
// 총 세금(보통 없지만 혹시 필요할때를 대비해서...)
$tax = $tradeapi->cal_tax($exchange, 'buy', $amount);
// 총 구매금액
$total_amount = $amount + $fee + $tax;
// $total_amount = $amount;

// check balance
if($wallet_exchange->confirmed < $total_amount) {
    $tradeapi->error('016', __('There is not enough balance to buy.'));
}

// 수수료 계좌정보 조회
$user_fee = $tradeapi->get_member_info(2);// walletmanager 코인별로 분리해야 한다면 $currency->fee_save_userno 컬럼 추가해서 분리하기.
if(!$user_fee) {
    $tradeapi->error('017', __('There is no fee account information.'));
}
$wallet_exchange_fee = $tradeapi->get_wallet($user_fee->userno, $exchange);
$wallet_exchange_fee = $wallet_exchange_fee ? $wallet_exchange_fee[0] : null;
if(!$wallet_exchange_fee) {
    $tradeapi->save_wallet($user_fee->userno, $exchange, ''); // 지갑 생성 하기.
    $wallet_exchange_fee = $tradeapi->get_wallet($user_fee->userno, $exchange);
    $wallet_exchange_fee = $wallet_exchange_fee ? $wallet_exchange_fee[0] : null;
}

// transaction start
$tradeapi->transaction_start();

try {

    // 주문 목록에 구매내역 등록
    $address_buy = $wallet_symbol->address; // krw 계좌는 저장할 필요 없음.

    $r = $tradeapi->write_buy_order($userno_buy, $address_buy, $symbol, $exchange, $price, $volume, $total_amount);
    $orderid_buy = $tradeapi->_recently_query['last_insert_id'];

    $trade_price = 0; // 최종 거래된 가격을 반영하기 위한 변수
    $trade_volume = 0; // 최종 거래된 수량을 반영하기 위한 변수
    $remain_volume_buy = $volume; // 남은 매수 수량
    $avg_trade_price = array();

    // 판매정보 변수명 변경
    $order_sell = $order_info;

    // 구매량과 판매량을 비교해서 판매내역 수정.
    $orderid_sell = $order_sell->orderid; // 매도주문아이디
    $remain_volume_sell = $order_sell->volume_remain; // 남은 매도주문량
    if($remain_volume_sell <= $remain_volume_buy) {
        $trade_volume = $remain_volume_sell;
        $trade_status_sell = 'C'; // 판매물량을 전부 소진하니 판매상태를 완료 처리.
    } else { //if ($remain_volume_sell > $remain_volume_buy) {
        $trade_volume = $remain_volume_buy;
        $trade_status_sell = 'T'; // 판매물량이 남았으니 판매상태를 거래중 처리.
    }

    // 거래가격 = 실제로 최종 거래된 가격
    $trade_price = $order_sell->price ;
    $avg_trade_price[] = $trade_price * $trade_volume; // 각 금액별로 거래가격 * 거래량을 저장해 두었다가 평균을 냅니다.

    // 거래대금. = 가격*수량 매매 건별 가격으로 수수료 처리하기 위해 여기로 이동.
    $trade_amount = $trade_volume * $trade_price;

    // 구매자 지갑에서 USD 차감.
    $tradeapi->charge_buy_price($userno_buy, $exchange, $trade_amount);

    // 판매자 지갑에 돈 지불.
    $userno_sell = $order_sell->userno;
    // 거래 수수료
    $fee = $tradeapi->cal_fee($exchange, 'sell', $trade_amount);
    // 거래 세금(보통 없지만 혹시 필요할때를 대비해서...)
    $tax_transaction = 0; // $tradeapi->cal_tax($exchange, 'sell', $trade_amount);
    // 양도 소득세
    // 미 판매 수량의 평균 매수가를 구해야 함.
    $tax_income = 0 ; //$tradeapi->cal_tax($exchange, 'buy', $trade_amount);
    // 판매자 거래대금. = 거래대금 - 거래 수수료 - 거래 세금 - 양도 소득 세금.
    $trade_receive = $trade_amount - $fee - $tax_transaction - $tax_income;
    // 원단위 절삭.
    $trade_receive = floor($trade_receive); // floor($trade_receive*1)/1;
    // 판매 대금 지급
    $tradeapi->add_wallet($userno_sell, $exchange, $trade_receive);
    // 수수료 계좌에 수수료 지급.
    if($fee>0) {
        $tradeapi->add_wallet($user_fee->userno, $exchange, $fee);
        // $tradeapi->add_wallet_txn($user_fee->userno, $wallet_exchange_fee->address, $exchange, $userno_sell, 'R', $fee, 0, 0, "D", $orderid_buy, date('Y-m-d H:i:s'));
    }
    if($tax_transaction>0) {
        $tradeapi->add_wallet($user_fee->userno, $exchange, $tax_transaction);
        // $tradeapi->add_wallet_txn($user_fee->userno, $wallet_exchange_fee->address, $exchange, $userno_sell, 'R', $tax_transaction, 0, 0, "D", $orderid_buy, date('Y-m-d H:i:s'));
    }
    if($tax_income>0) {
        $tradeapi->add_wallet($user_fee->userno, $exchange, $tax_income);
        // $tradeapi->add_wallet_txn($user_fee->userno, $wallet_exchange_fee->address, $exchange, $userno_sell, 'R', $tax_income, 0, 0, "D", $orderid_buy, date('Y-m-d H:i:s'));
    }

    // 판매 주문 수정.
    $tradeapi->trade_order($orderid_sell, $symbol, $exchange, $trade_volume, $trade_status_sell);

    // 구매자 지갑에 코인 지불
    $tradeapi->add_wallet($userno_buy, $symbol, $trade_volume);

    // 남은 구매량
    $remain_volume_buy = $remain_volume_buy > $remain_volume_sell ? $remain_volume_buy - $remain_volume_sell : 0;



    // 구매 주문 수정.
    if( $remain_volume_buy > 0 ) {
        $trade_status_buy = 'T';
    } else {
        $trade_status_buy = 'C';
    }
    $tradeapi->trade_order($orderid_buy, $symbol, $exchange, $trade_volume, $trade_status_buy);

    // 가격 상승/하락/보합
    $price_updown = $trade_price > $current_price ? 'U' : ($trade_price < $current_price ? 'D' : '-') ;

    // 거래 내역 저장.
    $tradeapi->write_trade_txn($symbol, $exchange, $trade_price, $trade_volume, $orderid_buy, $orderid_sell, $fee, $tax_transaction, $tax_income, $price_updown);
    $txnid = $tradeapi->_recently_query['last_insert_id'];
    // 거래 내역 인댁스 저장. 쩝.
    $tradeapi->write_trade_ordertxn($symbol, $exchange, $userno_sell, $orderid_sell, $txnid);
    $tradeapi->write_trade_ordertxn($symbol, $exchange, $userno_buy, $orderid_buy, $txnid);


    // 호가 데이터 갱신 - 거래가에 호가 갱신.
    $tradeapi->set_quote_data($symbol, $exchange, $trade_price);

    // 호가 데이터 갱신 - 주문가에 주문량 남아 있을수 있어서 호가 갱신함.
    $tradeapi->set_quote_data($symbol, $exchange, $price);

    // @todo 현제가 거래 - 그냥 현제가로 매수할때


    // 구매 거래 완료시 구매내역 업데이트
    // if($remain_volume_buy < $volume) {
    //     if($remain_volume_buy <= 0 ) {
    //         $trade_status = 'C'; // 완료 처리.
    //         $remain_volume_buy = 0; // 남은 물량 0으로 설정.
    //     } else {
    //         $trade_status = 'T'; // 판매물량이 남았으니 거래중 처리.
    //     }
    //     $r = $tradeapi->trade_order($orderid_buy, $symbol, $exchange, $remain_volume_buy, $trade_status);
    // }

    // 성공시 commit
    $tradeapi->transaction_end('commit');

    if($trade_price>0) {
        // 현재가 갱신
        $tradeapi->set_current_price_data($symbol, $exchange);
    }

} catch(Exception $e) {

    // 실패시 rollback
    $tradeapi->transaction_end('rollback');
    $tradeapi->error('005', __('A system error has occurred.'));

}
// transaction end

// 평균 거래금액
$avg_trade_price = count($avg_trade_price) > 0 ? round( array_sum($avg_trade_price) / ($volume-$remain_volume_buy), 4 ) : 0;
$remain_volume_buy = round($remain_volume_buy, 4);

// gen return value
$r = array('price'=>$avg_trade_price, 'volume'=>round($volume-$remain_volume_buy,4), 'amount'=>round($avg_trade_price*($volume-$remain_volume_buy),4)*1, 'order_price'=>$price, 'remain_volume'=>$remain_volume_buy, 'orderid'=>$orderid_buy);

// response
$tradeapi->success($r);
