<?php
ob_implicit_flush(true);
ignore_user_abort(1);
set_time_limit(0);

define('__API_RUNMODE__', 'live');
include(dirname(__file__).'/../lib/TradeApi.php');
//var_dump(__API_RUNMODE__); exit;
$tradeapi->set_logging(true);
$tradeapi->set_log_dir(__dir__.'/../log/'.basename(__dir__).'/'.basename(__file__, '.php').'/');
$tradeapi->set_log_name('');

/**
 * AirDrop(스톡옵션?) 수량을 보유기간이 지나면 각 회원 지갑에 넣어줍니다.
 * 매일 00:10에 작동 시킵니다.
 */
$tradeapi->write_log(' 작업 시작 ');
$airdrops = $tradeapi->query_list_object('SELECT * FROM js_trade_airdrop WHERE userno>0 AND volumn>0 AND lockup_date<NOW() AND paydate IS NULL');
$tradeapi->write_log('총 작업 건수: '.count($airdrops).'건');
foreach($airdrops as $row) {
    $tradeapi->write_log('건별 작업 시작. no: '.($row->no).', symbol: '.($row->symbol).', userno: '.($row->userno).', volumn: '.($row->volumn).' ');
    $now_date = date('Y-m-d H:i:s');
    // 회원지갑에 수량 넣기
    $r = $tradeapi->add_wallet($row->userno, $row->symbol, $row->volumn);
    if($r) {$tradeapi->write_log(' 회원지갑에 추가 - 성공 ');}
    else {$tradeapi->write_log(' 회원지갑에 추가 - 실패 ');}
    $wallet = $tradeapi->get_wallet($row->userno, $row->symbol);
    $wallet = $wallet ? $wallet[0] : NULL;
    $tradeapi->db_insert("js_exchange_wallet_txn", array(
        'userno'=>$row->userno,
        'symbol'=>$row->symbol,
        'address'=>$wallet->address,
        'regdate'=>$now_date,
        'txndate'=>$now_date,
        'address_relative'=>'',
        'txn_type'=>'S',
        'direction'=>'I',
        'amount'=>$row->volumn,
        'fee'=>0,
        'fee_relative'=>'',
        'tax'=>0,
        'status'=>'D',
        'key_relative'=>$row->no,
        'msg'=>'스톡옵션 보유기간이 지나서 회원 지갑에 추가함.'
    ));
    // 완료 처리하기
    $r = $tradeapi->db_update("js_trade_airdrop", array('paydate'=>$now_date), array('no'=>$row->no) );
    if($r) {$tradeapi->write_log(' 완료 처리 - 성공 ');}
    else {$tradeapi->write_log(' 완료 처리 - 실패 ');}
}
$tradeapi->write_log(' 작업 종료 ');