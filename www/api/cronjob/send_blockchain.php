<?php
/**
 * Send 처리 못한 트랜젝션을 BlockChain에 Send 하는 Background 스크립트
 *
 * js_exchange_wallet_txn 에서 Txn이 잆는 send 트랜젝션정보를 추출해서 블록체인에 send 명령합니다.
 * send가 성공하면 txnid 와 fee를 js_exchange_wallet_txn에 저장합니다.
 * 발송자의 잔액에서 fee를 차감합니다.
 * -- 수수료는 사용자가 신청시 미리 저장합니다. 즉, 여기서 보낼때는 전송금액 - 전송수수료 한 금액만큼만 실제로 보냅니다.
 * -- ETH 토큰은 출금지갑에서 직접 보내는 걸로 되어 있어서 수정하지 않음.
 *
 * 크론잡에 등록합니다. * * * * * /usr/bin/php .../www/api/cronjob/send_blockchain.php BTC
 *
 *
 *
 */
//  exit; // msc는 db라 작동 안시킴.
ob_implicit_flush(true);
ignore_user_abort(1);
set_time_limit(0);

$symbol = $argv[1] ? strtoupper($argv[1]) : '';
// $symbol = ''; // 태스트용입니다. 특정 코드 테스트할때 사용합니다.
if(!$symbol) {
	exit('symbol이 없습니다.');
}

@exec('ps -ef | grep "'.__FILE__.' '.$symbol.'" | grep -v grep ', $match);
if( count($match) > 1 ) {
    exit('중복 작업 종료.'); // 중복 작업 종료.
}

include(dirname(__file__).'/../lib/TradeApi.php');

$tradeapi->logging = true;
$tradeapi->set_log_dir(dirname(__file__).'/../log/');
$tradeapi->set_log_name(basename(__file__));
$tradeapi->set_stop_process(false);
$clear_time = date('Ymd');//''; // 마지막 로그 초기화 시간값. 시작시 이전로그 삭제하려면 빈문자열 넣으면 됩니다. 분단위로 초기화 하려면 YmdHi 까지 저장하도록 하면 됩니다.

//exchange 용 site
// $site = "morrow";

// 화폐 정보
$currency = $tradeapi->query_fetch_object("select * from js_exchange_currency where symbol='{$symbol}' "); // account, wallet
// 출금 지갑
if($currency->auto_withdrwal_userno) {
	$withdrawal_wallet = $tradeapi->query_fetch_object("select * from js_exchange_wallet where userno='{$currency->auto_withdrwal_userno}' and symbol='{$symbol}' "); // account, wallet
}

$loop_start_time = time(); // 무한반복 시작시간. PHP 매모리 누수 이슈로 서비스 이상이 발생함. 그래서 한시간에 한번씩 작업을 강재로 종료시켜서 메모리를 반환해주기로 함. 자동 실행은 ps_check.sh에서 실행.
$txns = array();
while (true) {
    $t = time();
    $clear_time_2 = date('Ymd');
    if($clear_time != $clear_time_2) {
        $tradeapi->write_log( "clear log", true );
        $clear_time = $clear_time_2;
    }

    if(!$txns || count($txns)<1) {
        $txns = $tradeapi->find_unsended_txn_list($symbol, 2);
    }
    // if($symbol=='GWS') {
    //     // testing
    //     $txns = $tradeapi->query_list_object("SELECT UNIX_TIMESTAMP(regdate) reg_time, UNIX_TIMESTAMP(regdate) reg_time_origin, symbol, txnid, address sender_address, userno, address_relative receiver_address, '' receiver_wallet_no, txn_type, amount, fee, msg `message` FROM js_exchange_wallet_txn WHERE txnid='380973' ");
    //     var_dump($txns);
    //     // exit;
    // }
    $tradeapi->write_log(  "working txns: ".(is_array($txns) ? count($txns) : '0') );

    foreach($txns as $txn) {
		// $sender_wallet = $tradeapi->get_wallet($txn->userno); // account, wallet
		// 보내는 사람
		if($withdrawal_wallet) {
			$sender_wallet = $withdrawal_wallet;
		} else {
			$sender_wallet = $tradeapi->query_fetch_object("select * from js_exchange_wallet where userno='{$txn->userno}' and symbol='{$symbol}' "); // account, wallet
		}
        // if($symbol=='GWS') {
        //     var_dump('sender_wallet', $sender_wallet);
        //     // exit();
        // }

		// 출금 수수료
		$fee = $txn->fee; // 전송 등록할때 수수료를 미리 저장합니다.
		// 수수료 값이 없으면 다시 계산합니다. - 불필요해보여 주석처리함.
		// if(!$fee) {
		// 	if($currency->fee_out) {
		// 		$fee = $currency->fee_out;
		// 	}
		// }
		$send_amount = $txn->amount; // 수수료 이미 차감했음. amount에 있는금액 그대로 보내면된.. - $fee; // 실제 전송 금액

		// 수신자 주소에 "Address=" 문자 있으면 제거
		$txn->receiver_address = stripos($txn->receiver_address, 'Address=')!==false ? str_ireplace('Address=','',$txn->receiver_address) : $txn->receiver_address;

        // send
        try {
			$error_msg = '';
            $txnid = $tradeapi->send_coin ($txn->symbol, $sender_wallet->address, $sender_wallet->userno, $txn->receiver_address, $send_amount, '', $txn->msg, $sender_wallet->userno);
        } catch(Exception $e) {
            $error_msg = $e->getMessage();
            $txnid = false;
        }

        if(!$txnid) {
            $tradeapi->write_log(  " - sending fail. {$error_msg}, reg_time: {$txn->reg_time}, amount: {$send_amount}, receiver_address: {$txn->receiver_address} " );
            $check_time = time() + (60*5); // 5분간격으로 계속 시도.
            // 등록된지 12시간이 지나도록 보내지 못하는 경우
            // $d = time()-$txn->reg_time;
            // if( 60*60*12 <= $d && $d < 60*60*13   ) { // 12시간 후 한번반 보내려고 했는데 5분간격이라.. 채크... 시간잡기 난해햠... 그냥 안보내기로 ...
                // 관리자에게 알림.
                // $tradeapi->send_slack_msg("[{$txn->symbol} - 미발송 TR 발생] 발송주소: {$txn->sender_address}, 수신주소: {$txn->receiver_address}, 금액: {$send_amount}, 등록날짜: ".date('Y-m-d H:i:s', $txn->reg_time)." ", "#system_alarm");
                // check_time을 내일로 변경.(계속해서 발송되는 것 방지.)
                // $check_time = time() + (60*60*12);
            // }
            // 트랜젝션에 check_time 저장
            // $tradeapi->update_wallet_txn ($txn->reg_time_origin, $txn->symbol, $txn->txnid, $sender_wallet->walletno, $txn->receiver_address, array('check_time'=>$check_time));


            // if($symbol=='GWS') {
            //     exit(" - sending fail. {$error_msg}, reg_time: {$txn->reg_time}, amount: {$send_amount}, receiver_address: {$txn->receiver_address} ");
            // }

            continue;
        }

        // 블록체인 수수료 구함. - GWS 블록체인은 수수료 없음.
        // $fee = $tradeapi->get_transaction_fee ($txn->symbol, $txnid, $sender_wallet->address); //$tradeapi->cal_fee($symbol, 'out', $amount);
        // $fee = $fee < 0 ? $fee * -1 : $fee;
        // $fee = 0;

        // DB 트랜젝션 시작
        // mysqli_commit 을 사용하면 select 결과를 새로 가져오지 않고 기존값을 그대로 가져오는 현상 있음. 그래서 처리해야할 트랜잭션을 안겨져오는 현상이 발생함.
        // $tradeapi->transaction_start();
        // 수수료 차감. - GWS 블록체인은 수수료 없음.
        // $tradeapi->remove_balance($sender_wallet->walletno, $fee);

		// 트랜젝션에 txnid, fee 저장
		// $tradeapi->update_wallet_txn ($txn->reg_time_origin, $txn->symbol, $txn->txnid, $sender_wallet->walletno, $txn->receiver_address, array('txnid'=>$txnid, 'status'=>'S', 'fee'=>$fee, 'check_time'=>time()));
		$tradeapi->query("update js_exchange_wallet_txn set key_relative='{$tradeapi->escape($txnid)}', `status`='D', `fee`='{$tradeapi->escape($fee)}'  where txnid='{$tradeapi->escape($txn->txnid)}' ");
        // DB 트랜젝션 끝
        // $tradeapi->transaction_end('commit');
        // 성공로그 작성
        $tradeapi->write_log(  " - txnid: {$txn->txnid}, sending complete!, blockchain txnid: {$txnid}, fee : {$fee} " );

        // if($symbol=='GWS') {
        //     var_dump($txn->symbol, $sender_wallet->address, $sender_wallet->userno, $txn->receiver_address, $send_amount, '', $txn->msg, $txn->txnid, $fee, "update js_exchange_wallet_txn set key_relative='{$tradeapi->escape($txnid)}', `status`='D', `fee`='{$tradeapi->escape($fee)}'  where txnid='{$tradeapi->escape($txn->txnid)}' ");
        //     exit('test success');
        // }

	}

	// exit('테스트 끝.');

    // 무한반복 시작한지 10분이 지났으면 일단 종료.
    if(time() - $loop_start_time > 60*10) {
        exit('시스템 매모리 반환');
    }

    // 남은 작업 확인.
    $txns = $tradeapi->find_unsended_txn_list($symbol, 2);
    if(count($txns)<1) { // 작업할것 없으면 3초 쉬고
        sleep(1);
    } else { // 작업할것 남았으면 바로 다음 작업
        sleep(1); // 서버부하를 줄이기 위해 잠시 쉽니다.
    }

}
