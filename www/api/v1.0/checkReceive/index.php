<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

/**
 * 입금 확인 스크립트. 
 * 
 * 계좌이체의 입금알림 메시지처럼 외부에서 입금 정보를 전달 받아 해당 정보의 입금 처리를 하는 스크립트입니다.
 * 각 화폐별로 입금 처리 방식이 다릅니다. 
 * 모든 회원에 대한 주기적 작업은 이곳이 아니라 크론잡에서 실행합니다. 
 * 
 * 예 : http://api.loc.kmcse.com/v1.0/checkReceive/?symbol=USD&deposit_info=농협%202012/02/11%20841104-51-015988%2020,000원(홍길동)입금.잔액2,249,718원
 */

// validate parameters
$symbol = strtoupper(checkSymbol(setDefault($_REQUEST['symbol'], ''))); // 코인
$deposit_info = setDefault($_REQUEST['deposit_info'], ''); // 입금 정보

// $deposit_info = '농협 2012/02/11 841104-51-015988 10,000원(홍길동)입금.잔액2,249,718원'; // test value

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// get currency informations
$t = $tradeapi->get_currency($symbol);
if(!$t) {
    $tradeapi->error('032',__('Symbole 정보가 없습니다.'));
}
$currency = (object) $t[0];

// transaction start
$tradeapi->transaction_start();
try {

    if($symbol=='KRW') {
        // 입금 문자 분석. ... 입금액, 입금자 정보 추출.
        // 농협 입금 문자 예제: 농협 2012/02/11 841104-51-015988 767,500원(류성복)입금.잔액2,249,718원
        preg_match('/ (.[0-9\.,]*)원\((.*)\)/', $deposit_info, $match);
        $sender = $match ? $match[2] : "";
        $amount = $match ? str_replace(',', '', $match[1]) : "";
        
        // 중복채크
        $where = array(
            'symbol' => $symbol,
            'sender_name' => $sender,
            'receive_amount' => $amount,
            'deposit_info' => $deposit_info
        );
        $deposit_txn = $tradeapi->db_get_row('js_trade_deposit_txn', $where);
        if($deposit_txn && trim($deposit_txn->regdate)!='') {
            $tradeapi->error('033', __('이미 처리했습니다.'));
        }
        
        // 신청 정보에서 맞는 정보 추출 - MyISAM 테이블이라 트랜젝션과 무관하게 요청정보는 저장됩니다.
        // $sql = "INSERT INTO js_trade_deposit_txn SET regdate=SYSDATE(), symbol='{$tradeapi->escape($symbol)}', sender_name='{$tradeapi->escape($sender)}', receive_amount='{$tradeapi->escape($amount)}', deposit_info='{$tradeapi->escape($deposit_info)}', status='O' ";
        // $tradeapi->query($sql);
        $regdate = date('Y-m-d H:i:s');
        $data = array(
            'regdate' => $regdate,
            'symbol' => $symbol,
            'sender_name' => $sender,
            'receive_amount' => $amount,
            'deposit_info' => $deposit_info,
            'status' => 'O'
        );
        $tradeapi->db_insert('js_trade_deposit_txn', $data);
        // 일단 전달 받은 정보는 저장합니다. 하지만 처리는 안합니다. 
        if($sender=='' || $amount=='') {
            $tradeapi->error('',__('입금 정보에서 입금자와 입금액을 찾지 못했습니다.'));
        }
    
        // 입금 신청 정보 찾기.
        // $sql = "select * from js_exchange_wallet_txn txn_type='R' AND status='O' AND symbol='{$tradeapi->escape($symbol)}' AND address_relative='{$tradeapi->escape($sender)}' AND amount='{$tradeapi->escape($amount)}' ";
        // $wallet_txn = $tradeapi->query_fetch_object($sql);
        $where = array(
            'txn_type'=>array('R','D'),
            'status'=>'O',
            'symbol'=>$symbol,
            'address_relative'=>$sender,
            'amount'=>$amount
        );
        $wallet_txn = $tradeapi->db_get_row('js_exchange_wallet_txn', $where);
        
        // 입금 완료처리.
        if($wallet_txn && $wallet_txn->userno > 0) {
            $r = $tradeapi->add_wallet($wallet_txn->userno, $symbol, $amount);
            if($r) {
                // 입금정보 처리완료로 상태 변경
                $data = array('status'=>'D');
                $where = array(
                    'regdate' => $regdate,
                    'symbol' => $symbol,
                    'sender_name' => $sender,
                    'receive_amount' => $amount,
                    'deposit_info' => $deposit_info,
                    'status' => 'O'
                );
                $tradeapi->db_update('js_trade_deposit_txn', $data, $where);
                // 지갑 거래정보 처리완료로 상태 변경.
                $data = array('status'=>'D', 'key_relative'=>$regdate); // key_relative 값은 입금 날짜로 하면 좋습니다.
                $where = array('txnid' => $wallet_txn->txnid);
                $tradeapi->db_update('js_exchange_wallet_txn', $data, $where);
                
            }
        }
        
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
