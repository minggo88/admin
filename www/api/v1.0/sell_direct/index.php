<?php
/**
 * 바로판매
 * 특정 매수주문건(orderid)에 대해서만 매매하는 api 메소드입니다.
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
$userno_sell = $userno;

// validate parameters
$orderid = checkEmpty($_REQUEST['orderid'], 'orderid'); // 주문번호
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol'))); // 코인
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange))); // 구매 화폐
$price = checkZero(checkNumber($_REQUEST['price']), 'price');// 가격
$volume = checkZero(checkNumber($_REQUEST['volume']), 'volume');// 구매량
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
if($order_info->trading_type != 'B' ) {
    $tradeapi->error('046', __('매수 주문을 선택해주세요.'));
}
if($order_info->volume_remain <= 0 ) {
    $tradeapi->error('044', __('거래가능한 주문수량이 없습니다.'));
}

// 구매주문가격이 판매가격과 같은지 확인
if($price != $order_info->price) {
    $tradeapi->error('045', __('구매가격과 다른 판매가격을 입력하여 판매하지 못했습니다.'));
}

// 매매 가격 범위 밖인지 확인.
$trade_price_info = (object) $tradeapi->get_trade_price_info($symbol, $exchange);
if($trade_price_info->trade_max_price && $trade_price_info->trade_max_price < $price) {
    $tradeapi->error('101','매매 가격 범위('.number_format($trade_price_info->trade_min_price).' ~ '.number_format($trade_price_info->trade_max_price).')에 속하는 가격을 입력해주세요.');
}
if($trade_price_info->trade_min_price && $trade_price_info->trade_min_price > $price) {
    $tradeapi->error('102','매매 가격 범위('.number_format($trade_price_info->trade_min_price).' ~ '.number_format($trade_price_info->trade_max_price).')에 속하는 가격을 입력해주세요.');
}

// 현재가
$current_price = $tradeapi->get_spot_price($symbol, $exchange);
$max_buy_price = $tradeapi->get_max_buy_price($symbol, $exchange);
if(count($current_price)>0) {
    $current_price = $current_price[0];
    $current_price = $current_price->price_close;
} else { // 거래가 없는경우 현재가는 매수1호가로 설정.
    $current_price = $max_buy_price;
}
// 판매금액이 매수1호가보다 낮은경우 매수1호가로 변경. - 주문오류로 더 낮은 가격으로 판매하지 못하게 하기위함. - 미사용.
// if( $max_buy_price > 0 && $price < $max_buy_price ) {
//     $price = $max_buy_price;
// }
// 더 높은 가격으로 주문해도 주문한 가격까지 매도 주문을 처리합니다. 주문윗가격으로는 매수 안합니다.
// 시장가: 매수가 지정 없이 모든 매도 물량을 대상으로 수량만큼 구매합니다. 주문사량이 많은 경우 구매하지 못한 주문은 마지막 주문가에 쌓아둡니다.

// 주문수량 확인 - 판매수량보다 많으면 안됨.
if($volume > $order_info->volume_remain) {
    $tradeapi->error('042', __('남은 구매수량보다 많이 주문하실 수 없습니다.'));
}

// 지갑 - 퍈매량을 확인해야 해서 $exchange 지갑을 가져옵니다.
$wallet_symbol = $tradeapi->get_wallet($userno_sell, $symbol);
if(!$wallet_symbol || !$wallet_symbol->address) {// 구매자 지갑 없으면 생성.
    $tradeapi->create_new_trade_wallet($userno_sell, $symbol);
    $wallet_symbol = $tradeapi->db_get_row('js_exchange_wallet',  array('userno'=>$userno_sell, 'symbol'=>$symbol));
}
// check locked
if($wallet_symbol->locked != 'N') {
	$tradeapi->error('048', str_replace('{symbol}', $symbol, __('{symbol}지갑이 잠겨있어 매도하실 수 없습니다.')));
}
$wallet_exchange = $tradeapi->get_wallet($userno_sell, $exchange);
if(!$wallet_exchange || !$wallet_exchange->address) {// 구매자 지갑 없으면 생성.
    $tradeapi->create_new_trade_wallet($userno_sell, $exchange);
    $wallet_exchange = $tradeapi->db_get_row('js_exchange_wallet',  array('userno'=>$userno_sell, 'symbol'=>$exchange));
}
// check locked
if($wallet_exchange->locked != 'N') {
	$tradeapi->error('048', str_replace('{symbol}', $exchange, __('{symbol}지갑이 잠겨있어 매도하실 수 없습니다.')));
}

// 판매금액
$amount = $price * $volume;
// 지불금액이 10원 밑이면 alert
if($amount<10) {
    $tradeapi->error('000', __('Your payment amount is too low. Please raise the quantity.')); // 지불금액이 너무 낮습니다. 수량을 올려주세요.
}

// check balance - 판매하는 코인의 남은수가 판매수량보다 작은지 확인하고 작으면 애러.
if($wallet_symbol->confirmed < $volume) {
    $tradeapi->error('017-1', __('There is not enough balance to sell.'));
}

// 수수료 계좌정보 조회
$user_fee = $tradeapi->get_member_info(2);// walletmanager 코인별로 분리해야 한다면 $currency->fee_save_userno 컬럼 추가해서 분리하기.
if(!$user_fee) {
    $tradeapi->error('017-2', __('There is no fee account information.'));
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

    // 주문 목록에 판매내역 등록
    $address_sell = $wallet_symbol->address;

    $r = $tradeapi->write_sell_order($userno_sell, $address_sell, $symbol, $exchange, $price, $volume, $amount);
    $orderid_sell = $tradeapi->_recently_query['last_insert_id'];

    $trade_price = 0; // 최종 거래된 가격을 반영하기 위한 변수
    $trade_volume = 0; // 최종 거래된 수량을 반영하기 위한 변수
    $remain_volume_sell = $volume; //  남은 판매 주문량
    $avg_trade_price = array();

    // // 주문 목록에서 동일 가격의 구매내역이 있는지 확인하고
    // $orders_buy = $tradeapi->get_order_by_price('B', $symbol, $exchange, $price);
    // if(!$orders_buy || count($orders_buy)<1) { // 초기값 설정.
    //     // 매수 주문이 없으니 주문금액 전액을 지갑에서 비용 차감. 즉, 코인(BTC) 차감.
    //     $tradeapi->charge_sell_price($userno_sell, $symbol, $volume);
    // }  else {
    //     // 매수 주문이 있을때는 아래에서 각각 매매하면서 해당 수량으로 BTC를 차감합니다.
    //     foreach($orders_buy as $order_buy) {

            // 구매정보 변수명 변경
            $order_buy = $order_info;

            // 판매량과 구매량을 비교해서
            $orderid_buy = $order_buy->orderid; // 구매주문아이디
            $remain_volume_buy = $order_buy->volume_remain; // 남은 구매 주문량

            if($remain_volume_buy <= $remain_volume_sell) {
                $trade_volume = $remain_volume_buy;
                $trade_status_buy = 'C'; // 판매물량을 전부 소진하니 완료 처리.
            } else { //if ($remain_volume_buy > $remain_volume_sell) {
                $trade_volume = $remain_volume_sell;
                $trade_status_buy = 'T'; // 판매물량이 남았으니 거래중 처리.
            }

            // 거래가격 = 실제로 최종 거래된 가격
            $trade_price = $order_buy->price ;
            $avg_trade_price[] = $trade_price * $trade_volume; // 각 금액별로 거래가격 * 거래량을 저장해 두었다가 평균을 냅니다.

            // 판매자 지갑에서 코인 차감. 예, 코인(BTC) 차감.
            $tradeapi->charge_sell_price($userno_sell, $symbol, $trade_volume);

            // 구매자 지갑에 코인 지불.
            $userno_buy = $order_buy->userno;
            $tradeapi->add_wallet($userno_buy, $symbol, $trade_volume);

            // 구매내역 수정.(거래)
            $r = $tradeapi->trade_order($orderid_buy, $symbol, $exchange, $trade_volume, $trade_status_buy);

            // 거래 금액.
            $trade_amount = $trade_volume * $trade_price; // 거래량 * 매수가
            // 판매자 수수료
            $fee = $tradeapi->cal_fee($exchange, 'sell', $trade_amount);
            // 총 세금(보통 없지만 혹시 필요할때를 대비해서...)
            $tax_transaction = $tradeapi->cal_tax($exchange, 'sell', $trade_amount);
            // 양도 소득세
            // 미 판매 수량의 평균 매수가를 구해야 함.
            $tax_income = 0 ; //$tradeapi->cal_tax($exchange, 'buy', $amount);
            // 판매자가 받는 금액
            $trade_receive = $trade_amount - $fee - $tax_transaction - $tax_income;
            // 원단위 절삭.
            $trade_receive = floor($trade_receive); // floor($trade_receive*1)/1;
            // 판매자 지갑에 금액 입금. 예. USD 입금(수수료 제한금액).
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

            // 거래 후 남은 판매량.
            $remain_volume_sell = $remain_volume_sell > $remain_volume_buy ? $remain_volume_sell - $remain_volume_buy : 0; // 판매량 - 구매호가량
            // 판매 내역 수정
            if( $remain_volume_sell > 0 ) {
                $trade_status_sell = 'T';
            } else {
                $trade_status_sell = 'C';
            }
            $tradeapi->trade_order($orderid_sell, $symbol, $exchange, $trade_volume, $trade_status_sell);

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

        //     // 남은 판매량이 없으면 종료
        //     if( $remain_volume_sell <= 0 ) {
        //         break;
        //     }
        // }

        // 매수 주문들 처리 하고도 남은구매량이 있으면 구매자의 USD 차감함.
        // if( $remain_volume_sell > 0 ) {
        //     $tradeapi->charge_sell_price($userno_sell, $symbol, $remain_volume_sell); // 남은 주문 수량 차감.
        // }
    // }

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
    //     $r = $tradeapi->trade_order($orderid_sell, $symbol, $exchange, $remain_volume_buy, $trade_status);
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
$avg_trade_price = count($avg_trade_price) > 0 ? ( array_sum($avg_trade_price) / ($volume-$remain_volume_sell) ) : 0;

// gen return value
$r = array('price'=>$avg_trade_price, 'volume'=>$volume-$remain_volume_sell, 'amount'=>round($avg_trade_price*($volume-$remain_volume_sell),4)*1, 'order_price'=>$price, 'remain_volume'=>$remain_volume_sell, 'orderid'=>$orderid_sell);

// response
$tradeapi->success($r);
