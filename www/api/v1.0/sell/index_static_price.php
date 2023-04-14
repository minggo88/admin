<?php
exit('미사용'); // sell_direct 사용해주세요.
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();
$userno_sell = $userno;

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
$max_buy_price = $tradeapi->get_max_buy_price($symbol, $exchange);
if(count($current_price)>0) {
    $current_price = $current_price[0];
    $current_price = $current_price->price_close;
} else { // 거래가 없는경우 현재가는 매수1호가로 설정.
    $current_price = $max_buy_price;
}
// 판매금액이 매수1호가보다 낮은경우 매수1호가로 변경. - 주문오류로 더 낮은 가격으로 판매하지 못하게 하기위함. - 미사용. 
if( $max_buy_price > 0 && $price < $max_buy_price ) {
    $price = $max_buy_price;
}
// 더 높은 가격으로 주문해도 주문한 가격까지 매도 주문을 처리합니다. 주문윗가격으로는 매수 안합니다.
// 시장가: 매수가 지정 없이 모든 매도 물량을 대상으로 수량만큼 구매합니다. 주문사량이 많은 경우 구매하지 못한 주문은 마지막 주문가에 쌓아둡니다.

// 가격 상승/하락/보합
$price_updown = $price > $current_price ? 'U' : ($price < $current_price ? 'D' : '-') ;

// 지갑 - 퍈매량을 확인해야 해서 $exchange 지갑을 가져옵니다.
$wallet_symbol = $tradeapi->get_wallet($userno_sell, $symbol);
if ( count($wallet_symbol) == 1 ) {
    $wallet_symbol = $wallet_symbol[0];
}
$wallet_exchange = $tradeapi->get_wallet($userno_sell, $exchange);
if ( count($wallet_exchange) == 1 ) {
    $wallet_exchange = $wallet_exchange[0];
}

// 판매금액
$amount = $price * $volume;

// check balance - 판매하는 코인의 남은수가 판매수량보다 작은지 확인하고 작으면 애러.
if($wallet_symbol->confirmed < $volume) {
    $tradeapi->error('017', __('There is not enough balance to sell.'));
}

// transaction start
$tradeapi->transaction_start();
try {

    // 주문 목록에 판매내역 등록
    $address_sell = $wallet_symbol->address;
    $r = $tradeapi->write_sell_order($userno_sell, $address_sell, $symbol, $exchange, $price, $volume, $amount);
    $orderid_sell = $tradeapi->_recently_query['last_insert_id'];

    // 지갑에서 비용 차감. 즉, 코인(BTC) 차감.
    $r = $tradeapi->charge_sell_price($userno_sell, $symbol, $volume);

    // 주문 목록에서 동일 가격의 구매내역이 있는지 확인하고
    $orders_buy = $tradeapi->get_order_by_price('B', $symbol, $exchange, $price);
    // var_dump($orders_buy); exit;
    
    $trade_price = 0; // 최종 거래된 가격을 현재가에 반영하기 위한 변수
    $avg_trade_price = array(); // 평균 거래 가격.
    if(count($orders_buy)>0) { // 초기값 설정.
        $trade_price = $price;
    }
    
    // 거래 - 고정가거래
    $remain_volume_sell = $volume; //  남은 판매 주문량 
    foreach($orders_buy as $order_buy) {

        
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
        // $trade_volume이 0이면 거래 종료 다음거래로 패스.
        if($trade_volume==0) { 
            continue;
        }

        // 거래가격 = 실제로 최종 거래된 가격
        $trade_price = $order_buy->price ; 
        $avg_trade_price[] = $trade_price * $trade_volume; // 각 금액별로 거래가격 * 거래량을 저장해 두었다가 평균을 냅니다.

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
        // 판매자 지갑에 금액 입금.
        $tradeapi->add_wallet($userno_sell, $exchange, $trade_receive);

        // 거래 후 남은 판매량.
        $remain_volume_sell = $remain_volume_sell > $remain_volume_buy ? $remain_volume_sell - $remain_volume_buy : 0; // 판매량 - 구매호가량
        // 판매 내역 수정
        if( $remain_volume_sell > 0 ) {
            $trade_status_sell = 'T';
        } else {
            $trade_status_sell = 'C';
        }
        $tradeapi->trade_order($orderid_sell, $symbol, $exchange, $trade_volume, $trade_status_sell);

        // 거래 내역 저장.
        $tradeapi->write_trade_txn($symbol, $exchange, $trade_price, $trade_volume, $orderid_buy, $orderid_sell, $fee, $tax_transaction, $tax_income, $price_updown);
        $txnid = $tradeapi->_recently_query['last_insert_id'];
        // 거래 내역 인댁스 저장. 쩝.
        $tradeapi->write_trade_ordertxn($symbol, $exchange, $userno_sell, $orderid_sell, $txnid);
        $tradeapi->write_trade_ordertxn($symbol, $exchange, $userno_buy, $orderid_buy, $txnid);

        // 호가 데이터 갱신 - 거래가에 호가 갱신.
        // $tradeapi->set_quote_data($symbol, $exchange, $trade_price);
        
        // 남은 판매량이 없으면 종료
        if( $remain_volume_sell <= 0 ) {
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
$r = array('price'=>$avg_trade_price, 'volume'=>$volume-$remain_volume_sell, 'amount'=>round($avg_trade_price*($volume-$remain_volume_sell),4)*1, 'order_price'=>$price, 'remain_volume'=>$remain_volume_sell);

// response
$tradeapi->success($r);
