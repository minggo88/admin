<?php
exit('통합지갑으로 처리해서 지갑소스의 입금확인만 작동시킵니다.');
/**
 * 입금 트랜젝션 확인 및 지갑에 적용
 *
 * 입금 들어온 트렌젝션을 확인해서 처리하지 않은 입금내역은 계좌에 추가해줍니다.
 * 확인 순서: 가장오래전에 확인한 계좌, 가장 최근에 생성된 계좌
 * 1분에 10개(1계좌당 6초)의 작업으로 처리합니다.
 * 부하 발생시 코인별로 프로세스를 작동시키세요.
 */
ob_implicit_flush(true);
ignore_user_abort(1);
set_time_limit(0);

include(dirname(__file__).'/../lib/TradeApi.php');

$symbol = $argv[1] ? strtoupper($argv[1]) : '';
// $symbol = ''; // 태스트용입니다. 특정 코드 테스트할때 사용합니다.

// 제외할 트렌젝션 날짜
// 블록체인 변경건으로 초기 코인 지급 완료 이후의 트렌젝션만 확인해야함.
// 블록체인 장부의 내용이 깨끗한경우 빈값으로 설정하세요.
$min_time = ''; //'1540998000'; //  '2018-11-01'; var_dump( strtotime($min_date), date('Y-m-d H:i:s',strtotime($min_date)) );

// 한번에 확인할 지갑 갯수
$work_cnt = 50;

// -------------------------------------------------------------------- //

if(trim($symbol)==''){
    exit('The symbol code is missing and the operation is terminated.'.PHP_EOL);
}
// 콜드월랫 정보 추출.
$currency = $tradeapi->get_currency($symbol);
if(!$currency || count($currency)<1){
    exit('No currency information was found in the symbol code.'.PHP_EOL);
}
$currency = $currency[0];
// var_dump($currency); exit;

$cmd = __file__;
$cmd.= $symbol!='' ? " $symbol" : '';
@exec('ps -ef | grep "'.$cmd.'" | grep -v grep ', $match);
if( count($match) > 1 ) {
    exit('Already running.'.PHP_EOL); // 중복 작업 종료.
}

$tradeapi->logging = true;
$tradeapi->set_log_dir(dirname(__file__).'/../log/');
$tradeapi->set_log_name(basename(__file__));
// $clear_time = date('Ymd');//''; // 마지막 로그 초기화 시간값. 시작시 이전로그 삭제하려면 빈문자열 넣으면 됩니다. 분단위로 초기화 하려면 YmdHi 까지 저장하도록 하면 됩니다.

$loop_start_time = time(); // 무한반복 시작시간. PHP 매모리 누수 이슈로 서비스 이상이 발생함. 그래서 한시간에 한번씩 작업을 강재로 종료시켜서 메모리를 반환해주기로 함. 자동 실행은 ps_check.sh에서 실행.
$wallets = array(); // 작업할 내용.
while (true) {
    $t = time();
    // $clear_time_2 = date('Ymd');
    // if($clear_time != $clear_time_2) {
    //     $tradeapi->write_log( "clear log", true );
    //     $clear_time = $clear_time_2;
    // }

    // 확인할 지갑 추출.
    if(!$wallets || count($wallets)<1) {
        $wallets = $tradeapi->get_wallet_check_deposit($symbol, $work_cnt);
    }
    $tradeapi->write_log( "wallet count: ".(is_array($wallets) ? count($wallets) : '0') );
    // var_dump($wallets); //exit;
    foreach($wallets as $wallet) {
        $wallet = (object) $wallet;
        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] deposit check start. " ); // 라이브서버에서는 제외하기

        // txn list 가져오기.
        $txns = $tradeapi->get_wallet_receive_list ($wallet->symbol, $wallet->address, $wallet->userno);
        // var_dump($wallet->symbol, $wallet->address, $wallet->userno, $txns);exit;

        if(is_array($txns) && count($txns)>0 ) { // 입금 확인
            foreach($txns as $txn) {
                $txn = (object) $txn; // array든 object든 object로 변경해서 작업.
                $txn->txnid = trim($txn->txnid);
                // txnid 없으면 패스.
                if(trim($txn->txnid)=='') {
                    $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Except for empty transaction id. txnid: {$txn->txnid} " ); // 라이브서버에서는 제외하기
                    continue;
                }
                // 제외할 날짜 확인.
                if(trim($txn->time)!=='' && $txn->time <= $min_time) {
                    $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Except for older transactions. txnid: {$txn->txnid}, txntime: {$txn->time}, mintime: {$min_time} " ); // 라이브서버에서는 제외하기
                    continue;
                }
                // 팬딩 상태일때는 패스. 성공(D)일대 저장하면서 포인트 추가함.
                if($txn->status!='D') {
                    $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Except for pedding transactions. txnid: {$txn->txnid}, status: {$txn->status} " ); // 라이브서버에서는 제외하기
                    // var_dump($txn);exit('팬딩 상태 발생. ');
                    continue;
                }
                // txnid 가 이미 있는지 확인. 있으면 다음걸로 패스 없으면 insert
                // $sql = "select txnid, status from js_exchange_wallet_txn where key_relative='".$tradeapi->escape($txn->txnid)."' and limit 1 ";
                // $txn_db = $tradeapi->query_fetch_object($sql);
                $txn_db = $tradeapi->find_wallet_txn_list(array('key_relative'=>$txn->txnid, 'symbol'=>$symbol), 1, 1) ; // symbol별로 txnid는 유일하기 때문에 symbol과 txnid만 검색함.
                $txn_db = $txn_db[0] ? $txn_db[0] : false;
                // $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Already processed it. " );
                // var_dump($wallet->symbol, $wallet->address, $wallet->userno, $txns, $txn_db, $currency->backup_address);exit;
                if(!$txn_db) { // 없을때만 작업.
                    // $tradeapi->transaction_start();
                    // 트랜젝션 저장.
                    // $txn_date = $txn->time!='' ? date('Y-m-d H:i:s', $txn->time) : '';
                    $txn_date = $txn->txn_date!='' ? $txn->txn_date : date('Y-m-d H:i:s'); // $txn->txn_date 값이 없는 블록체인은 현재시간으로 사용해 저장합니다.
                    $r = $tradeapi->add_wallet_txn($wallet->userno, $txn->to_address, $wallet->symbol, $txn->from_address, 'R', 'I', $txn->amount, $txn->fee, '0', $txn->status, $txn->txnid, $txn_date);
                    //                             $userno,         $address,         $symbol,         $address_relative, $txn_type, $amount, $fee,     $tax, $status="O", $key_relative="", $txndate=''
                    if(!$r) {
                        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Fail to write transaction log. (to_address:".$txn->to_address.', symbol:'.$wallet->symbol.', from_address:'.$txn->from_address.', amount:'.$txn->amount.', fee:'.$txn->fee.', status:'.$txn->status.', txnid:'.$txn->txnid.', txn_date:'.$txn_date.') ');
                    } else {
                        // 포인트 지급.
                        $tradeapi->add_wallet($wallet->userno, $wallet->symbol, $txn->amount);
                        // $tradeapi->transaction_end();
                        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Deposit processing completed. (to_address:".$txn->to_address.', symbol:'.$wallet->symbol.', from_address:'.$txn->from_address.', amount:'.$txn->amount.', fee:'.$txn->fee.', status:'.$txn->status.', txnid:'.$txn->txnid.', txn_date:'.$txn_date.') ');
                    }
                    // exit('입금 처리 발생. '); // testing
                }
            }
            // 지갑 잔액 확인.
            $balance = $tradeapi->get_wallet_balance($wallet->symbol, $wallet->address, $wallet->userno);
            $balance = $balance->confirmed;
            // 수수료 빼고 나머지 금액 전액 이체
            $txn_fee = $tradeapi->get_coin_txn_fee($wallet->symbol);
            $send_amount = $balance - $txn_fee;
            $tradeapi->write_log('$send_amount:'. $send_amount);
            if($send_amount > 0) {
                // 보관하기
                $tradeapi->write_log('backup_address:'. $currency->backup_address);
                if(trim($currency->backup_address)!='' && $wallet->address!=$currency->backup_address) {
                    $backup_txnid = $tradeapi->send_coin ($wallet->symbol, $wallet->address, $wallet->userno, $currency->backup_address, $send_amount,0,'', $wallet->userno);
                    if(!$backup_txnid) {
                        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Failed to send to backup address. (to_address:".$currency->backup_address.', symbol:'.$wallet->symbol.', from_address:'.$wallet->address.', amount:'.$txn->amount.', fee:0, userno:'.$wallet->userno.') ');
                        // 관리자에게 알림.
                        $tradeapi->send_slack_msg("[".__APP_NAME__." - {$wallet->symbol} 입금액 이동 실패 ] 발송주소: {$wallet->address}, 수신주소: {$currency->backup_address}, 금액: {$txn->amount}, 날짜: ".date('Y-m-d H:i:s', $txn->reg_time ? $txn->reg_time : time()).". 수동으로 백업계좌의 수신주소로 이동시켜주세요. ", "#system_alarm");
                    } else {
                        $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] Success to send to backup address. (txnid: {$backup_txnid}, to_address:".$currency->backup_address.', symbol:'.$wallet->symbol.', from_address:'.$wallet->address.', amount:'.$txn->amount.', fee:0, userno:'.$wallet->userno.') ');
                        $tradeapi->add_wallet_txn($wallet->userno, $wallet->address, $wallet->symbol, $currency->backup_address, 'B', 'O', $txn->amount, '0', '0', 'D', $backup_txnid, date('Y-m-d H:i:s'));
                        // 콜드 월렛이 현 서버에 있는지 확인 있으면 잔액 증가시키기.
                        $coldwallet = $tradeapi->get_wallet_by_address($currency->backup_address, $wallet->symbol);
                        // 콜드월랫 잔액 증가.
                        if($coldwallet->userno) {
                            $tradeapi->add_wallet($coldwallet->userno, $coldwallet->symbol, $txn->amount);
                            $tradeapi->add_wallet_txn($coldwallet->userno, $coldwallet->address, $coldwallet->symbol, $wallet->address, 'B', 'I', $txn->amount, '0', '0', 'D', $backup_txnid, date('Y-m-d H:i:s'));
                        }
                    }
                }
            }
            // exit('지갑 하나 확인 완료.'); // for testing

        } else {
            // $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] empty txn. " ); // 라이브서버에서는 제외하기
        }
        $tradeapi->update_check_deposit_time($wallet->userno, $wallet->symbol);
        // $tradeapi->write_log( "[".$wallet->symbol."-".$wallet->userno."] deposit check end. " ); // 라이브서버에서는 제외하기
    }

    // exit('1'); // testing

    // 무한반복 시작한지 10분이 지났으면 일단 종료.
    if(time() - $loop_start_time > 60*10) {
        exit('시스템 매모리 반환');
    }

    // 남은 작업 확인.
    $wallets = $tradeapi->get_wallet_check_deposit($symbol, $work_cnt);
    if(count($wallets)<1) { // 작업할것 없으면 6초 간격으로 쉬도록 시간 설정.
        $t = 6 - (time() - $t);
        if($t>0) {
            sleep( $t );
        }
    } else { // 작업할것 남았으면 바로 다음 작업
        sleep(1); // 서버부하를 줄이기 위해 잠시 쉽니다.
    }

}
