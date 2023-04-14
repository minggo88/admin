<?php
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

include(dirname(__file__) . '/../lib/TradeApi.php');

// 한번에 확인할 지갑 갯수
$work_cnt = 100;

$tradeapi->set_logging(true);
$tradeapi->set_log_dir(__dir__.'/../log/'.basename(__dir__).'/'.basename(__file__, '.php').'/');
$tradeapi->set_log_name('');

// -------------------------------------------------------------------- //

$cmd = __file__;
@exec('ps -ef | grep "'.$cmd.'" | grep -v grep ', $match);
if( count($match) > 1 ) {
	var_dump($match);
    exit('중복 작업 종료.'.PHP_EOL); // 중복 작업 종료.
}

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

$loop_start_time = time(); // 무한반복 시작시간. PHP 매모리 누수 이슈로 서비스 이상이 발생함. 그래서 한시간에 한번씩 작업을 강재로 종료시켜서 메모리를 반환해주기로 함. 자동 실행은 ps_check.sh에서 실행.
$msgs = array(); // 작업할 내용.
while (true) {

    $t = time();

    // 확인할 지갑 추출.
	$msgs = $tradeapi->get_unworked_deposit_msg($work_cnt);
    // $tradeapi->write_log('msg count: ' . (is_array($msgs) ? count($msgs) : '0'));

    foreach ($msgs as $row) {

        /**
         * SMS 자동입금처리
         * SMS 입금안내 문자를 분석해 동일한 입금신청 정보를 처리하는 스크립트입니다.
         *
         * 예: https://www.uniteglobal.io/api/v1.0/depositSms/index.php?msg=[Web%EB%B0%9C%EC%8B%A0]%EB%86%8D%ED%98%91%20%EC%9E%85%EA%B8%8810,000%EC%9B%9007/30%2014:47%20301-****-7746-81%20%ED%99%8D%EA%B8%B8%EB%8F%99%20%EC%9E%94%EC%95%A13,061,132%EC%9B%90&sender_phone_number=08088888888
         */

        // validate parameters
        $msg = $row->msg;
        $sender_phone_number = $row->$sender_phone_number;
        $exchange = 'KRW';

// [Web발신]
// 2022/06/23 10:48
// 입금  1,000,000
// 이호진
// 129***39704015
// 기업
		$msg = trim($msg); 
		$msg = preg_replace("/\r\n|\r|\n/", '↲', $msg); 
// --> [Web발신]↲2022/06/23 10:48↲입금  1,000,000↲이호진↲129***39704015↲기업
    	// $tradeapi->write_log('msg : ' . $msg);

        // 기업은행 일때
		if (preg_match('/기업$/', trim($msg))) {
			$msg_pt = '/\[Web발신\]↲(.*?)↲입금(.*)↲(.*)↲(.*)↲기업/';
			preg_match($msg_pt, $msg, $r);
            $amount = '';
            $date = '';
            $address = '';
            $sender = '';
            if ($r) {
				$amount = trim($r[2]); //   1,000,000
				$amount = preg_replace('/[^0-9.]/','',$amount);
				$date = trim($r[1]); // 2022/06/23 10:48
				$date = $date ? date('Y-m-d H:i:s', strtotime($date)) : '';
				$address = trim($r[4]); //129***39704015
				$sender = trim($r[3]);
				if(preg_match('/ 대$/', $sender)) { // 입금자 뒤에 "　　 대"로 끝나면 제거
					$sender = trim(str_replace('　','',preg_replace('/ 대$/','',$sender)));
				}
            }
        } 

		// 입금자 뒤에 "　　 대"로 끝나면 제거
		if(preg_match('/ (.*)$/', $sender)) {
			$sender = trim(str_replace('　','',preg_replace('/ (.*)$/','',$sender)));
		}

        // var_dump($s, $r, $amount, $date, $address, $sender);//exit();
		// $tradeapi->write_log('$s: '.$s);
		// $tradeapi->write_log('$r: '.$r);
		// $tradeapi->write_log('$amount: '.$amount);
		// $tradeapi->write_log('$date: '.$date);
		// $tradeapi->write_log('$address: '.$address);
		// $tradeapi->write_log('$sender: '.$sender);

        $r = array('msg' => $msg, 'sender_phone_number' => $sender_phone_number);
        if ($amount && $sender && $address) { //  && $date

			// 입금 받은 시간 이전에 신청한 건만 처리함.
			$msg_time = substr($row->regtime, 0, 10) + 60*10; // 입금하고 신청하는 분들 있어서 10분뒤로 넘김.
			// $tradeapi->write_log('$msg_time: '.$msg_time);

			$r = check_n_deposit_request($exchange, $msg_time,  $sender, $amount, $row);
			if(!$r) {
				// 이름 값이 이름(회사명 형식이면 이름값을 (로 짤라서 앞에부분(이름)으로 다시 검색해 본다.
				if (strpos($sender, '(') !== false) {
					$t = explode('(', $sender);
					$sender = $t[0];
					if (trim($sender)) {
						$r = check_n_deposit_request($exchange, $msg_time,  $sender, $amount, $row);
					}
				}
			}

			// $tradeapi->write_log(' 입금 확인 완료 ');

        }

	} // 입금정보 for 종료

	// exit('test end');

	sleep(10);
	// exit('중복처리 방지를 위해 작업후 10초 쉬고 종료합니다.');


}


function check_n_deposit_request($exchange, $msg_time,  $sender, $amount, $row) {
	global $tradeapi;

	// 입금액, 입금자명 같은 신청정보 확인.
	
	$sql = "SELECT * FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($exchange)}' AND regdate<=FROM_UNIXTIME($msg_time) AND `status`='O' AND txn_type='R' AND address_relative='{$tradeapi->escape($sender)}' AND amount='{$tradeapi->escape($amount)}' ORDER BY txnid DESC LIMIT 1";
	// $tradeapi->write_log($sql);
	$txn_info = $tradeapi->query_fetch_object($sql);
	// $tradeapi->write_log('$txn_info:'.print_r($txn_info, true));
	$del_time = time() - strtotime($txn_info->regdate);
	// $tradeapi->write_log('$del_time:'.$del_time);
	// var_dump($txn_info, $sql, $del_time < 60*60*24*3 ,  $del_time);exit;
	if ($txn_info && $del_time < 60 * 60 * 24 * 3 && $del_time > 0) { // 3일 이내 신청 정보가 있을때만 자동처리.
		$tradeapi->add_wallet($txn_info->userno, $exchange, $amount);// 지갑 잔액 추가
		$tradeapi->query("UPDATE js_exchange_wallet_txn SET `status` = 'D', txndate=NOW(), msg='{$tradeapi->escape($row->msg)}' WHERE txnid='{$tradeapi->escape($txn_info->txnid)}' ");// txn 상태 변경( O -> D )
		$tradeapi->query("UPDATE js_deposit_msg SET done='Y' WHERE msg='".$tradeapi->escape($row->msg)."' ");
		return true;
	} 
	return false;
}