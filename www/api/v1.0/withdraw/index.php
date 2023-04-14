<?php
include dirname(__file__) . "/../../lib/TradeApi.php";
if(date('YmdH')>='2021030309' && date('YmdH')<'2021030314') {
    if($_SERVER['REMOTE_ADDR']!='61.74.240.65') {$tradeapi->error('001','시스템 정검중입니다.');}
}

$tradeapi->set_logging(true);
$tradeapi->set_log_dir(__dir__.'/../../log/'.basename(__dir__).'/');
$tradeapi->set_log_name('');
$tradeapi->write_log("REQUEST: " . json_encode($_REQUEST));


// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(checkEmpty($_REQUEST['symbol'], 'symbol'))); // 코인
$from_address = checkEmpty($_REQUEST['from_address'], 'from_address'); // 송신인 주소
$to_address = checkEmpty($_REQUEST['to_address'], 'to_address'); // 수취인 주소
$amount = checkZero(checkNumber($_REQUEST['amount']), 'amount');// 송금량
$pin = checkEmpty($_REQUEST['pin'], 'pin'); // 계좌 송금 비번.

// --------------------------------------------------------------------------- //
// 판매자 아이디인지 확인 - 입출금 불가
$is_manager_account = $tradeapi->query_one("SELECT COUNT(*) FROM js_trade_currency WHERE manager_userno='{$tradeapi->escape($userno)}'  ");
if($is_manager_account) {
    $tradeapi->error('100', '판매자용 계정은 입금과 출금을 신청하실 수 없습니다.');
}

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// 최소 출금 가능금액 확인.
$currency_info = $tradeapi->get_currency($symbol);
$currency_info = $currency_info[0];
if($currency_info->out_min_volume>0 && $currency_info->out_min_volume>$amount) {
    $tradeapi->error('028', __('Please enter a value larger than the minimum withdrawal amount.'));
}
if($currency_info->out_max_volume>0 && $currency_info->out_max_volume<$amount) {
    $tradeapi->error('029', __('출금하실 금액을 최대출금액보다 작게 입력해주세요.'));
}

// pin 번호 확인.
$user_info = $tradeapi->get_user_info($userno);
if($user_info->pin!=md5($pin)) {
    $tradeapi->error('025', __('Please enter the correct PIN number.'));
}

// 지갑 소유주 확인.  (USD 제외 Brad add 18.10.24)
$wallet_info = $tradeapi->get_wallet($userno, $symbol);
$wallet_info = is_array($wallet_info) && count($wallet_info)>0 ? $wallet_info[0] : $wallet_info;
if($symbol != "USD" && $symbol != "KRW") {
    if($wallet_info->address!=$from_address) {
        $tradeapi->error('026', __('Please enter the member\'s withdrawal address.'));
    }
}
// check locked
if($wallet_info->locked != 'N') {
	$tradeapi->error('048',__('지갑이 잠겨있어 출금하실 수 없습니다.'));
}
if($wallet_info->bool_widthraw=='0') {
	$tradeapi->error('048-1',__('지갑이 잠겨있어 출금하실 수 없습니다.'));
}

// 은행 인증여부 확인
if(($symbol == "USD"||$symbol == "KRW") && $user_info->bool_confirm_bank!='1') {
    $tradeapi->error('033', __('은행계좌 인증을 완료 후 출금 신청해주세요.'));
}

// 지갑 잔액 확인.
if($wallet_info->confirmed<$amount) {
    $tradeapi->error('027', __('You can not withdraw more than your balance.'));
}

// 주소가 내부 지갑인지 확인하기.
$receiver_inner_wallet = $tradeapi->get_wallet_by_address($to_address, $symbol);
// $receiver_wallet = $exchangeapi->query_fetch_object("SELECT * from kmcse_tradejs_exchange_wallet WHERE symbol='{$exchangeapi->escape($symbol)}' AND address='{$exchangeapi->escape($to_address)}' ");
// if(!$receiver_wallet) $receiver_wallet = $exchangeapi->query_fetch_object("SELECT * FROM morrow_wallet.js_wallet_wallet WHERE symbol='{$exchangeapi->escape($symbol)}' AND address='{$exchangeapi->escape($to_address)}' ");
if( !$receiver_inner_wallet && ($symbol!='USD'||$symbol!='KRW') ) {
	$exchangeapi->error('033', '외부출금기능 시스템 점검중입니다.');
}

// 수수료 계산.
$fee = $tradeapi->cal_fee($symbol, 'withdraw', $amount);
if($receiver_inner_wallet) { // 내부에 받는지갑이 있으면 수수료 0 처리
    $fee = 0;
}
// 수수료보다 작은 금액 출금 확인.
if($fee>$amount) {
    $tradeapi->error('031', __('Withdrawal amount is less than the fee.'));
}
$real_amount = $amount - $fee;
if($real_amount<=0) {
    $tradeapi->error('032', __('실 출금액이 없습니다.').' '.__('수수료보다 많은 금액을 출금해주세요.'));
}

// transaction start
$tradeapi->transaction_start();

// 지갑 잔액 차감
// 신청금액에서 출금 수수료를 제하기 때문에 전체 신청금액($amount) 를 잔액에서 차감하면 됨.
$tradeapi->del_wallet($userno, $symbol, $amount);

// 출금 신청.
$tradeapi->save_withdraw($userno, $symbol, $real_amount, $fee, $to_address, $from_address, __('Member requested withdrawal.'));

// 성공시 commit
$tradeapi->transaction_end('commit');

// 관리자에게 SMS 알림.
if(__API_RUNMODE__=='live'){
    $tran_phone = $tradeapi->get_admin_phone_number('sms');
    $tran_msg  = "[".str_replace('api.','',$_SERVER['HTTP_HOST'])."] {$user_info->userid}회원님이 ".number_format($amount).$symbol."을 {$to_address}로 출금 신청했습니다. ";
    @$tradeapi->send_sms($tran_phone, $tran_msg);
}

// response
$tradeapi->success();
