<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();
$userno_buy = $userno; 

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol'))); // 
$deposit_amount = checkZero(checkNumber($_REQUEST['deposit_amount']), 'volume');// 입금액
$deposit_name = checkEmpty($_REQUEST['deposit_name'], 'deposit_name');// 입금자명
$address = checkEmpty($_REQUEST['address'], 'symbol_address');// 입금주소

// --------------------------------------------------------------------------- //
// 판매자 아이디인지 확인 - 입출금 불가
$is_manager_account = $tradeapi->query_one("SELECT COUNT(*) FROM js_trade_currency WHERE manager_userno='{$tradeapi->escape($userno)}'  ");
if($is_manager_account) {
    $tradeapi->error('100', '판매자용 계정은 입금과 출금을 신청하실 수 없습니다.');
}

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// transaction start
$tradeapi->transaction_start();
try {

    $fee = 0;
    $tax = 0;

    // 호가 데이터 갱신 - memory db를 사용했더니 데이터 반영이 늦어서 오류 가격 중복 오류 발생. innodb로 변경함.
    $tradeapi->add_wallet_txn($userno, $address, $symbol, $deposit_name, 'R', 'I', $deposit_amount, $fee, $tax, "O"); 

    // 관리자에게 SMS 알림.
    $tran_phone = $tradeapi->get_admin_phone_number('sms');
    $tran_msg  = "[".str_replace('api.','',$_SERVER['HTTP_HOST'])."] {$user_info->userid}회원님이 ".number_format($deposit_amount).$symbol."을 입금 신청했습니다. ";
    @$tradeapi->send_sms($tran_phone, $tran_msg);

    // 성공시 commit
    $tradeapi->transaction_end('commit');

} catch(Exception $e) {

    // 실패시 rollback
    $tradeapi->transaction_end('rollback');
    $tradeapi->error('005', __('A system error has occurred.'));
    
}
// transaction end

// response
$tradeapi->success();
