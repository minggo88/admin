<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

$tradeapi->set_logging(true);
$tradeapi->set_log_dir(__dir__.'/../../log/'.basename(__dir__).'/');
$tradeapi->set_log_name('');
$tradeapi->write_log("REQUEST: " . json_encode($_REQUEST));

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol'))); // 코인
$exchange = checkSymbol(strtoupper(setDefault($_REQUEST['exchange'], $tradeapi->default_exchange))); // 구매 화폐
$orderid = checkEmpty($_REQUEST['orderid'], 'orderid'); // 주문번호.고유키

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// 주문정보
$order = $tradeapi->get_order($symbol, $exchange, $orderid);

// 소유주 확인.
if ($order->userno != $userno) {
    $tradeapi->error('018', __('You can only process your own orders.'));
}

// 상태 확인.
if ($order->status == 'close') {
    $tradeapi->error('019', __('You can not cancel because already completed an order.'));
}

// transaction start
$tradeapi->transaction_start();
try {

    // 지갑 - 선취한 금액을 되돌려 줍니다.
    if($order->trading_type == 'buy') { // 구매시 선취차감한 KRW를 되돌려줍니다.
        $_refund = $order->price * $order->volume_remain;
        $tradeapi->add_wallet($userno, $exchange, $_refund);
    } else { // 판매시 선취차감한 코인을 되돌려줍니다.
        $tradeapi->add_wallet($userno, $symbol, $order->volume_remain);
    }

    // 주문 취소 처리
    $tradeapi->cancel_order($symbol, $exchange, $orderid);

    // 호가 데이터 갱신 - memory db를 사용했더니 데이터 반영이 늦어서 오류 가격 중복 오류 발생. innodb로 변경함.
    $tradeapi->set_quote_data($symbol, $exchange, $order->price); // 주문 가격으로 전달 받은 가격에 대해 호가를 갱신합니다.
    
    // 성공시 commit
    $tradeapi->transaction_end('commit');

} catch (Exception $e) {

    // 실패시 rollback
    $tradeapi->transaction_end('rollback');
    $tradeapi->error('005', __('A system error has occurred.'));

}
// transaction end

// response
$tradeapi->success();
