<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/


include_once $_SERVER["DOCUMENT_ROOT"].'/lib/common_admin.php';

if(__API_RUNMODE__=='live') {
	exit('미사용기능입니다.'); // 라이브에서는 삭제 없음.
}

ajaxCheckAdmin();

//파일 업로드 위치
$dir_loc = dirname(__file__)."/../../../auction/data/";

$goods_idx = $_POST['idx'];
if(!$goods_idx) {
	exit('상품번호가 없습니다.');
}

$dir_loc .= $goods_idx.'/';

// 이전 정보 조회
$old_goods_info = $dbcon->query_unique_object("SELECT * FROM js_auction_goods WHERE idx='{$dbcon->escape($goods_idx)}' ");
$old_auction_list = $dbcon->query_all_object("SELECT * FROM js_auction_list WHERE goods_idx='{$dbcon->escape($goods_idx)}' ");
$old_auctions = array();
foreach($old_auction_list as $row) {
	$old_auctions[] = $row->auction_idx;
}
$old_auctions = implode("','", $old_auctions);
$old_auction_inventory = $dbcon->query_all_object("SELECT * FROM js_auction_list WHERE goods_idx='{$dbcon->escape($goods_idx)}' ");

//아이템 정보 수정
$result = $dbcon->query("DELETE FROM js_auction_goods WHERE idx='{$dbcon->escape($goods_idx)}'");
$result = $dbcon->query("DELETE FROM js_auction_list WHERE goods_idx='{$dbcon->escape($goods_idx)}'");
$result = $dbcon->query("DELETE FROM js_auction_inventory WHERE goods_idx='{$dbcon->escape($goods_idx)}'");
$result = $dbcon->query("DELETE FROM js_auction_apply WHERE auction_idx in ('{$old_auctions}') ");
$result = $dbcon->query("DELETE FROM js_auction_apply_list WHERE auction_idx in ('{$old_auctions}') ");

// echo("\n DELETE FROM js_auction_goods WHERE idx='{$dbcon->escape($goods_idx)}'");
// echo("\n DELETE FROM js_auction_list WHERE goods_idx='{$dbcon->escape($goods_idx)}'");
// echo("\n DELETE FROM js_auction_inventory WHERE goods_idx='{$dbcon->escape($goods_idx)}'");
// echo("\n DELETE FROM js_auction_apply WHERE auction_idx in ('{$old_auctions}') ");
// echo("\n DELETE FROM js_auction_apply_list WHERE auction_idx in ('{$old_auctions}') ");


// 이전 이미지 삭제
if($old_goods_info->main_pic && file_exists($dir_loc.$old_goods_info->main_pic)) {unlink($dir_loc.$old_goods_info->main_pic);}
if($old_goods_info->sub1_pic && file_exists($dir_loc.$old_goods_info->sub1_pic)) {unlink($dir_loc.$old_goods_info->sub1_pic);}
if($old_goods_info->sub2_pic && file_exists($dir_loc.$old_goods_info->sub2_pic)) {unlink($dir_loc.$old_goods_info->sub2_pic);}
if($old_goods_info->sub3_pic && file_exists($dir_loc.$old_goods_info->sub3_pic)) {unlink($dir_loc.$old_goods_info->sub3_pic);}
if($old_goods_info->sub4_pic && file_exists($dir_loc.$old_goods_info->sub4_pic)) {unlink($dir_loc.$old_goods_info->sub4_pic);}
if($old_goods_info->animation && file_exists($dir_loc.$old_goods_info->animation)) {unlink($dir_loc.$old_goods_info->animation);}

exit('ok');