<?php

/**
 * 입금 트랜젝션 확인 및 지갑에 적용
 *
 * 입금 들어온 트렌젝션을 확인해서 처리하지 않은 입금내역은 계좌에 추가해줍니다.
 * 확인 순서: 가장오래전에 확인한 계좌, 가장 최근에 생성된 계좌
 * 1분에 10개(1계좌당 6초)의 작업으로 처리합니다.
 * 부하 발생시 코인별로 프로세스를 작동시키세요.
 * 바로빌 API 사용 버전입니다.
 */
ob_implicit_flush(true);
ignore_user_abort(1);
set_time_limit(0);

include(dirname(__file__) . '/../lib/TradeApi.php');
include(dirname(__file__) . '/../lib/BaroService/BankAccount.php');
$BankAccount = new BankAccount();

$tradeapi->logging = true;
$tradeapi->set_log_dir(dirname(__file__) . '/../log/');
$tradeapi->set_log_name(basename(__file__));

// -------------------------------------------------------------------- //

$cmd = __file__;
@exec('ps -ef | grep "' . $cmd . '" | grep -v grep ', $match);
if (count($match) > 1) {
	$tradeapi->write_log('중복 작업 종료.');
	exit(); // 중복 작업 종료.
}
$tradeapi->write_log('작업 시작.');

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');


$t = time();
$exchange = 'KRW';
$limit_date = date('Y-m-d H:i:s',time() - 60*60*24*30); // 하루내 신청한 건만 조회

// 은행통장에 거래내역 가져오기
$bank_address = '3550064618653'; // 농협 / 123-1234-1234321 / (주)주식회사
$txns = $BankAccount->GetDailyBankAccountLog($bank_address);
// var_dump(count($txns), $bank_address); exit;
$tradeapi->write_log('거래내역 갯수:'.count($txns));
foreach ($txns as $txn) {

	$amount  = $txn->Deposit; // 입금금액
	$txn_time  = $txn->TransDT ? strtotime($txn->TransDT) : ''; // yyyyMMddHHmmss --> 입금시간
	$sender  = $txn->TransRemark; // 입금자명
	$address = $bank_address; // 통장주소

	if ($amount && $sender && $address && $txn_time) { //  && $date
		$tradeapi->write_log('입금 정보. amount:'.$amount.', txn_time:'.$txn_time.', sender:'.$sender.', address:'.$address);

		// 입금 받은 시간 이전에 신청한 건만 처리함.
		$txn_date = date('Y-m-d H:i:s',$txn_time); // 입금날짜
		$check_date = date('Y-m-d H:i:s',$txn_time + 60 * 10); // 입금하고 신청하는 분들 있어서 10분뒤로 넘김.

		// 입금액, 입금자명 같은 신청정보 확인.
		if (check_request($sender, $amount, $check_date, $limit_date, $exchange)) {
			continue; // 작업 완료. 다음걸로 패스
		} else {
			// 이름 값이 이름(회사명 형식이면 이름값을 (로 짤라서 앞에부분(이름)으로 다시 검색해 본다.
			if (strpos($sender, '(') !== false) {
				$t = explode('(', $sender);
				$sender = $t[0];
				if (trim($sender)) {
					// 입금액, 입금자명 같은 신청정보 확인.
					if (check_request($sender, $amount, $check_date, $limit_date, $exchange)) {
						continue; // 작업 완료. 다음걸로 패스
					}
				}
			}
		}
		$tradeapi->write_log('입금 신청 정보(DB값) 찾지못해 입금처리 못함.');
	} else {
		// $tradeapi->write_log('입금 필수 정보 없음');
	}

} // 입금정보 for 종료

$tradeapi->write_log('작업 종료.');


/**
 * 입금 신청 내역 확인 및 입금 처리
 */
function check_request($sender, $amount, $check_date, $limit_date, $exchange) {
	global $tradeapi;
	// 입금액, 입금자명 같은 신청정보 확인.
	$sql = "SELECT * FROM js_exchange_wallet_txn WHERE symbol='{$tradeapi->escape($exchange)}' AND regdate<='{$tradeapi->escape($check_date)}' AND regdate>='{$tradeapi->escape($limit_date)}' AND `status`='O' AND txn_type='R' AND address_relative='{$tradeapi->escape($sender)}' AND amount='{$tradeapi->escape($amount)}' ORDER BY txnid DESC LIMIT 1";
	$tradeapi->write_log('sql:'.$sql);
	$txn_info = $tradeapi->query_fetch_object($sql);
	$delta_time = time() - strtotime($txn_info->regdate);
	if ($txn_info && $delta_time < 60 * 60 * 24 * 3 && $delta_time > 0) { // 3일 이내 신청 정보가 있을때만 자동처리.
		$tradeapi->add_wallet($txn_info->userno, $exchange, $amount); // 지갑 잔액 추가
		$tradeapi->query("UPDATE js_exchange_wallet_txn SET `status` = 'D', txndate=NOW() WHERE txnid='{$tradeapi->escape($txn_info->txnid)}' "); // txn 상태 변경( O -> D )
		$tradeapi->write_log('입금 처리완료. txnid:'.$txn_info->txnid);
		return true;
	} else {
		return false;
	}
}