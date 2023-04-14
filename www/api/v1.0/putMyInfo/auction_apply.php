<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

//상품 구매 등록
// 로그인 세션 확인.
$tradeapi->checkLogin();

$userid = $tradeapi->get_login_userid();
$userno = $tradeapi->get_login_userno();

$auction_idx= checkEmpty($_REQUEST['auction_idx'], 'auction_idx');  //auction idx
$price      = checkNumber(checkEmpty($_REQUEST['price'], 'price'));          //금액(추가할)
$symbol     = 'GWS'; // GWS 고정

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

//경매 정보 확인
// $query = "SELECT g.idx FROM js_auction_list as a inner join js_auction_goods as g on a.goods_idx=g.idx
//     WHERE a.auction_idx='{$tradeapi->escape($auction_idx)}' and now()>=a.start_date and now()<=a.end_date";
// $get_auction_goods = $tradeapi->query_one($query);
$query = "SELECT auction_idx, goods_idx, start_date, end_date FROM js_auction_list WHERE auction_idx='{$tradeapi->escape($auction_idx)}' ";
$auction_info = $tradeapi->query_fetch_object($query);
if(!$auction_info){
    $tradeapi->error('005', __('No auction now.').$query);
}
if($auction_info->start_date && $auction_info->start_date>date('Y-m-d H:i:s')) {
	$tradeapi->error('005', __('경매가 시작되지 않았습니다.').' '.__('시작날짜를 확인해주세요.'));
}
if($auction_info->end_date && $auction_info->end_date<date('Y-m-d H:i:s')) {
	$tradeapi->error('005', __('경매가 종료되었습니다.').' '.__('다른 경매상품에 참여해주세요.'));
}

//자신것은 입찰 못하게 막는다
$query = "SELECT userid FROM js_auction_inventory WHERE userid='{$userid}' and goods_idx='{$get_auction_goods}'";
$chk_owner = $tradeapi->query_one($query);
if($chk_owner){
    $tradeapi->error('005', __('You can\'t apply your goods.'));
}

//제품 최대 금액 확인
$query = "SELECT max(auction_price) as max_price FROM js_auction_apply WHERE auction_idx='{$tradeapi->escape($auction_idx)}'";
$max_price = $tradeapi->query_one($query);

//사용자 입찰 금액 확인
$query = "SELECT auction_price as user_price FROM js_auction_apply WHERE auction_idx='{$tradeapi->escape($auction_idx)}' and userid='{$tradeapi->escape($userid)}'";
$user_price = $tradeapi->query_one($query);
if(!$user_price) {$user_price = 0;}
if($max_price >= ($user_price+$price)){
    $tradeapi->error('005', __('The apply price is less than or equal to the maximum price.'));
}

// 지갑 잔액 확인.
$user_wallet = $tradeapi->query_fetch_object("SELECT userno, symbol, confirmed, locked, autolocked, account, address FROM js_exchange_wallet WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($symbol)}' ");
if($user_wallet->confirmed < $price) {
	$tradeapi->error('005', __('잔액이 부족합니다.'));
}

// 관리자 지갑 확인
$manager_userno = '2';
$manager_wallet = $tradeapi->query_fetch_object("SELECT userno, symbol, confirmed, locked, autolocked, account, address FROM js_exchange_wallet WHERE userno='{$manager_userno}' AND symbol='{$tradeapi->escape($symbol)}' ");
if(!$manager_wallet) {
	$tradeapi->error('030', __('경매관리자 지갑을 설정해주세요.'));
}

$tradeapi->transaction_start();

// 잔액 감액
$tradeapi->query("UPDATE js_exchange_wallet SET confirmed = confirmed - {$tradeapi->escape($price)} WHERE userno='{$tradeapi->escape($userno)}' AND symbol='{$tradeapi->escape($symbol)}'  ");
$sql = "INSERT INTO js_exchange_wallet_txn SET `userno`='{$tradeapi->escape($userno)}', `symbol`='{$tradeapi->escape($symbol)}', `address`='{$tradeapi->escape($user_wallet->address)}', `regdate`=NOW(), `txndate`=NOW(), `address_relative`='{$tradeapi->escape($manager_wallet->address)}', `txn_type`='Au', `direction`='O', `amount`='{$tradeapi->escape($price)}', `fee`=0, `tax`=0, `status`='D', `key_relative`='{$tradeapi->escape($auction_idx)}', `txn_method`='COIN', `msg`='bidding' ";
$tradeapi->query($sql);
// 관리자계정 증액
$tradeapi->query("UPDATE js_exchange_wallet SET confirmed = confirmed + {$tradeapi->escape($price)} WHERE userno='{$manager_userno}' AND symbol='{$tradeapi->escape($symbol)}'  ");
$sql = "INSERT INTO js_exchange_wallet_txn SET `userno`='{$manager_userno}', `symbol`='{$tradeapi->escape($symbol)}', `address`='{$tradeapi->escape($manager_wallet->address)}', `regdate`=NOW(), `txndate`=NOW(), `address_relative`='{$tradeapi->escape($user_wallet->address)}', `txn_type`='Au', `direction`='I', `amount`='{$tradeapi->escape($price)}', `fee`=0, `tax`=0, `status`='D', `key_relative`='{$tradeapi->escape($auction_idx)}', `txn_method`='COIN', `msg`='bidding' ";
$tradeapi->query($sql);

//옥션 등록, 존재 하면 금액 추가(재 apply)
$query = "INSERT INTO js_auction_apply (auction_idx ,goods_idx, userid, auction_price, reg_date) VALUES
    ('{$tradeapi->escape($auction_idx)}','{$tradeapi->escape($goods_idx)}','{$tradeapi->escape($userid)}','{$tradeapi->escape($price)}', now())
    on duplicate key update auction_price=auction_price+{$tradeapi->escape($price)}, mod_date=now()";
$re = $tradeapi->query($query);
if($re){
	// 입찰 로그 작성
	$tradeapi->query("INSERT INTO js_auction_apply_list SET auction_idx='{$tradeapi->escape($auction_idx)}', userid='{$tradeapi->escape($userid)}', auction_price={$tradeapi->escape($price)}, reg_date=NOW() ");

	$tradeapi->transaction_end('commit');
    $tradeapi->success();

}else {
    $tradeapi->error('005', __('A system error has occurred.'));
}