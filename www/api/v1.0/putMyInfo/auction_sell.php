<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

//개인 상품 판매 등록
// 로그인 세션 확인.
$tradeapi->checkLogin();

$userid = $tradeapi->get_login_userid();
$userno = $tradeapi->get_login_userno();

$goods_idx  = checkEmpty($_REQUEST['goods_idx'], 'goods_idx');          //goods idx
$sell_price      = checkEmpty($_REQUEST['sell_price'], 'price');                  //판매금액
$wish_price      = setDefault($_REQUEST['wish_price'], '0');                  //희망금액
$start_date = checkEmpty($_REQUEST['start_date'], 'start_date');        //시작일
$end_date   = checkEmpty($_REQUEST['end_date'], 'end_date');            //종료일
$auction_title  = checkEmpty($_REQUEST['auction_title'], 'auction_title');            //옥션 제목

$start_date     = date("Y-m-d H:i:s", strtotime($start_date));
$end_date       = date("Y-m-d H:i:s", strtotime($end_date));

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

//제품 정보 가져오기
// $query = "SELECT g.idx FROM js_auction_inventory as i INNER JOIN js_auction_goods as g on g.idx=i.goods_idx WHERE i.goods_idx='{$tradeapi->escape($goods_idx)}'";
$query = "SELECT g.*, i.userid FROM js_auction_goods as g LEFT JOIN js_auction_inventory as i on g.idx=i.goods_idx AND i.userid='{$tradeapi->escape($userid)}' WHERE g.idx='{$tradeapi->escape($goods_idx)}'";
$goods_info = $tradeapi->query_fetch_object($query);
if(!$goods_info){
    $tradeapi->error('005', __('No item.'));
}
if($goods_info->userid != $userid){
    $tradeapi->error('005', __('보유중인 상품이 아닙니다.'));
}

// try{
    // transaction start
    $tradeapi->transaction_start();

    //옥션 pk 생성 = type의 3글자 + 날짜 시분초ms까지
    $auction_id = substr($tradeapi->escape($goods_info->goods_type), 0, 3).substr(date("ymdHisu"), 0, 13);

	$sql = "INSERT INTO js_auction_list (auction_idx, goods_idx, auction_title, start_date, end_date, sell_price, wish_price, finish, reg_date) VALUES ('{$auction_id}', '{$tradeapi->escape($goods_idx)}', '{$tradeapi->escape($auction_title)}', '{$tradeapi->escape($start_date)}', '{$tradeapi->escape($end_date)}', {$tradeapi->escape($sell_price)}, {$tradeapi->escape($wish_price)}, 'N', NOW())";
    $result = $tradeapi->query($sql);

    // 성공시 commit
    $tradeapi->transaction_end('commit');
    $tradeapi->success();

// } catch(Exception $e) {
//     // 실패시 rollback
//     $tradeapi->transaction_end('rollback');
//     $tradeapi->error('005', __('A system error has occurred.'));
// }