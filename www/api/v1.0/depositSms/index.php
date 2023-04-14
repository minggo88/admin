<?php
include dirname(__file__) . "/../../lib/TradeApi.php";
$tradeapi->set_logging(true);
$tradeapi->set_log_dir(__dir__.'/../../log/'.basename(__dir__).'/');
$tradeapi->set_log_name('');
$tradeapi->write_log("REQUEST: " . json_encode($_REQUEST));

/**
 * SMS 자동입금처리
 * SMS 입금안내 문자를 항아리에 담는 스크립트입니다. 실제 처리는 cronjob/despositSms.php 에서 30초후 처리합니다.
 * 동일한 입금문자를 같은 시간에 2번 받아 2건을 처리하는 상황이 발생했음. (신청건도 2건이라 ... 그랬음.)
 * 이를 막기위해 항아리에 저장하는 스풀링 방식 적용함. msg 는 동일할 수 없어서 unique 처리되어 있음. 따라서 동시에 같은 메시지를 받아도 하나만 처리됨.
 *
 * 예: https://www.uniteglobal.io/api/v1.0/depositSms/index.php?msg=[Web%EB%B0%9C%EC%8B%A0]%EB%86%8D%ED%98%91%20%EC%9E%85%EA%B8%8810,000%EC%9B%9007/30%2014:47%20301-****-7746-81%20%ED%99%8D%EA%B8%B8%EB%8F%99%20%EC%9E%94%EC%95%A13,061,132%EC%9B%90&sender_phone_number=08088888888
 */

// 로그인 세션 확인.
// $tradeapi->checkLoginAdmin();
// $tradeapi->check_admin_permission('order');

// validate parameters
$msg = checkEmpty($_REQUEST['msg']); // 입금문자 메시지
$sender_phone_number = checkEmpty($_REQUEST['sender_phone_number']);// 발송자 전화번호
// --------------------------------------------------------------------------- //
// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// $msg = preg_replace("/\r\n|\r|\n/", ' ', $msg); //[Web발신]농협 입금300,000원07/19 07:29 301-****-7746-81 홍길동(회사 잔액3,061,132원
$r = array('msg'=>preg_replace("/\r\n|\r|\n/", ' ', $msg), 'sender_phone_number'=>$sender_phone_number);

// 항아리에 저장.
try {

    $regtime = str_replace('.','',sprintf('%01.6f', array_sum(explode(' ',microtime()))));
    $sql = "INSERT IGNORE INTO js_deposit_msg set regtime='{$tradeapi->escape($regtime)}', done='N', msg='{$tradeapi->escape($msg)}', sender_address='{$tradeapi->escape($sender_phone_number)}', token='".$tradeapi->escape($_REQUEST['token'])."' ";
    $tradeapi->query($sql);
    $tradeapi->success($r);

} catch (Exception $e) {

    $tradeapi->error('000', '메시지에서 금액, 입금자, 계좌번호를 찾지 못했습니다. '.$amount .','. $sender .','. $address .','. $date);

}
