<?php
exit('미사용'); // buy_direct 사용해주세요.
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();
$userno_buy = $userno; 

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol'))); // 코인
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange))); // 구매 화폐
// $price = checkQuotePrice(checkZero(checkNumber($_REQUEST['price']), 'price'), $exchange );// 가격
$price = checkZero(checkNumber($_REQUEST['price']), 'price');// 가격
$volume = checkZero(checkNumber($_REQUEST['volume']), 'volume');// 구매량

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

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

// 구매금액이 매도1호가보다 높은경우 매도1호가로 변경. - 주문오류로 더 높은 가격으로 구매하지 못하게 하기위함. - 미사용. 
if( $min_sell_price > 0 && $price > $min_sell_price ) {
    $price = $min_sell_price;
}
// 더 높은 가격으로 주문해도 주문한 가격까지 매도 주문을 처리합니다. 주문윗가격으로는 매수 안합니다.
// 시장가: 매수가 지정 없이 모든 매도 물량을 대상으로 수량만큼 구매합니다. 주문사량이 많은 경우 구매하지 못한 주문은 마지막 주문가에 쌓아둡니다.

// 가격 상승/하락/보합
$price_updown = $price > $current_price ? 'U' : ($price < $current_price ? 'D' : '-') ;


// 지갑 - 구매금액을 확인해야 해서 $exchange 지갑을 가져옵니다.
$wallet_exchange = $tradeapi->get_wallet($userno_buy, $exchange);
if ( count($wallet_exchange) > 0 ) {
    $wallet_exchange = $wallet_exchange[0];
}
$wallet_symbol = $tradeapi->get_wallet($userno_buy, $symbol);
if ( count($wallet_symbol) > 0 ) {
    $wallet_symbol = $wallet_symbol[0];
}

// 구매금액
$amount = $price * $volume;
// 총 수수료
// 매도/매수 어떤거든 주문할때는 수수료가 선 차감하는것 없음. 매매가 발생할때 차감됨. 
// 매수자는 수수료/세금 차감 없음. 
// 매도자는 수수료/세금 차감함.
// $fee = $tradeapi->cal_fee($exchange, 'buy', $amount);
// 총 세금(보통 없지만 혹시 필요할때를 대비해서...)
// $tax = $tradeapi->cal_tax($exchange, 'buy', $amount);
// 총 구매금액
// $total_amount = $amount + $fee + $tax;
$total_amount = $amount;

// check balance
if($wallet_exchange->confirmed < $total_amount) {
    $tradeapi->error('016', __('There is not enough balance to buy.'));
}




// transaction start
$tradeapi->transaction_start();

try {

    // 주문 목록에 구매내역 등록
    $address_buy = $wallet_symbol->address; // krw 계좌는 저장할 필요 없음.
    $r = $tradeapi->write_buy_order($userno_buy, $address_buy, $symbol, $exchange, $price, $volume, $total_amount);
    $orderid_buy = $tradeapi->_recently_query['last_insert_id'];

    // 지갑에서 비용 차감. 즉, USD 차감.
    $r = $tradeapi->charge_buy_price($userno_buy, $exchange, $total_amount);

    // 주문 목록에서 동일 가격의 판매내역이 있는지 확인하고
    $orders_sell = $tradeapi->get_order_by_price('S', $symbol, $exchange, $price);

    $trade_price = 0; // 최종 거래된 가격을 현재가에 반영하기 위한 변수
    $avg_trade_price = array();
    if(count($orders_sell)>0) {
        $trade_price = $price;
    }

    // 거래
    $remain_volume_buy = $volume; // 남은 매수 수량

    foreach($orders_sell as $order_sell) {
        if(empty($order_sell->orderid) || $order_sell->volume_remain <= 0 ) {
            continue;
        }
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

        // 판매자 지갑에 돈 지불.
        $userno_sell = $order_sell->userno;
        // 거래 수수료
        $fee = $tradeapi->cal_fee($exchange, 'sell', $amount);
        // 거래 세금(보통 없지만 혹시 필요할때를 대비해서...)
        $tax_transaction = $tradeapi->cal_tax($exchange, 'sell', $amount);
        // 양도 소득세
        // 미 판매 수량의 평균 매수가를 구해야 함.
        $tax_income = 0 ; //$tradeapi->cal_tax($exchange, 'buy', $amount);
        // 거래대금. = 가격*수량
        $trade_amount = $trade_volume * $trade_price;
        // 판매자 거래대금. = 거래대금 - 거래 수수료 - 거래 세금 - 양도 소득 세금.
        $trade_receive = $trade_amount - $fee - $tax_transaction - $tax_income;
        // 판매 대금 지급
        $tradeapi->add_wallet($userno_sell, $exchange, $trade_receive);
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
        
        // 거래 내역 저장.
        $tradeapi->write_trade_txn($symbol, $exchange, $trade_price, $trade_volume, $orderid_buy, $orderid_sell, $fee, $tax_transaction, $tax_income, $price_updown);
        $txnid = $tradeapi->_recently_query['last_insert_id'];
        // 거래 내역 인댁스 저장. 쩝.
        $tradeapi->write_trade_ordertxn($symbol, $exchange, $userno_sell, $orderid_sell, $txnid);
        $tradeapi->write_trade_ordertxn($symbol, $exchange, $userno_buy, $orderid_buy, $txnid);

        // 호가 데이터 갱신 - 거래가에 호가 갱신.
        // $tradeapi->set_quote_data($symbol, $exchange, $trade_price);

        // 남은구매량이 없으면 종료
        if( $remain_volume_buy <= 0 ) {
            break;
        }
    }

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
$avg_trade_price = count($avg_trade_price) > 0 ? ( array_sum($avg_trade_price) / ($volume-$remain_volume_buy) ) : 0;

// gen return value
$r = array('price'=>$avg_trade_price, 'volume'=>$volume-$remain_volume_buy, 'amount'=>round($avg_trade_price*($volume-$remain_volume_buy),4)*1, 'order_price'=>$price, 'remain_volume'=>$remain_volume_buy);

// response
$tradeapi->success($r);
