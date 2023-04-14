<?php
/**
 * JIN-DB 용 입금 처리 api
 */
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();
// $userno_buy = $userno; 

// validate parameters
$symbol = 'JIN';
$txn_date = setDefault($_REQUEST['txn_date'], date('Y-m-d H:i:s'));
$amount = checkZero(checkNumber($_REQUEST['amount']), 'amount');// 입금액
$to_address = checkEmpty($_REQUEST['to_address'], 'to_address');
$from_address = checkEmpty($_REQUEST['from_address'], 'from_address');
$txnid = checkEmpty($_REQUEST['txnid'], 'txnid');
$x_check_url = setDefault($_REQUEST['x_check_url'], '');


// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// transaction start
$tradeapi->transaction_start();
try {

    // x-check 
    if($x_check_url) {
        $txn = $tradeapi->remote_post($x_check_url, 'txnid='.$txnid);
        $txn = @ json_decode($txn);
        if(!$txn || $txn->txnid!=$txnid || $txn->symbol!=$symbol || $txn->amount!=$amount || $txn->to_address!=$to_address || $txn->from_address!=$from_address ) {
            $tradeapi->error('005', __('A x-system error has occurred.'));
        }
    } else {
        $tradeapi->error('005', __('A x-system error has occurred.'));
    }

    // 받는사람 지갑
    $wallet = $tradeapi->get_wallet_by_address($to_address, $symbol);
    var_dump($wallet); exit;

    $fee = 0;
    $tax = 0;
    
    $r = $tradeapi->add_wallet_txn($wallet->userno, $to_address, $symbol, $from_address, 'R', 'I', $amount, $fee, $tax, 'D', $txnid, $txn_date);
    if(!$r) {
        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Fail to write transaction log. (to_address:".$to_address.', symbol:'.$symbol.', from_address:'.$from_address.', amount:'.$amount.', fee:'.$fee.', status:D, txnid:'.$txnid.', txn_date:'.$txn_date.') ');
    } else {
        // 포인트 지급.
        $tradeapi->add_wallet($wallet->userno, $symbol, $amount);
        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Deposit processing completed. (to_address:".$txn->to_address.', symbol:'.$wallet->symbol.', from_address:'.$txn->from_address.', amount:'.$txn->amount.', fee:'.$txn->fee.', status:'.$txn->status.', txnid:'.$txn->txnid.', txn_date:'.$txn_date.') ');
    }

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
