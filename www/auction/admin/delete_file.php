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
$img_type = $_POST['image'];
if(!$img_type) {
	exit('이미지 종류가 없습니다.');
}
$a_img_type = array('main_pic','sub1_pic','sub2_pic','sub3_pic','sub4_pic','animation');
if(!in_array($img_type, $a_img_type)) {
	exit('올바른 이미지 종류값을 보내주세요.');
}

$dir_loc .= $goods_idx.'/';

// 이전 정보 조회
$old_goods_info = $dbcon->query_unique_object("SELECT * FROM js_auction_goods WHERE idx='{$dbcon->escape($goods_idx)}' ");

// 이전 이미지 삭제
if($img_type=='main_pic' && $old_goods_info->main_pic && file_exists($dir_loc.$old_goods_info->main_pic)) {unlink($dir_loc.$old_goods_info->main_pic);}
if($img_type=='sub1_pic' && $old_goods_info->sub1_pic && file_exists($dir_loc.$old_goods_info->sub1_pic)) {unlink($dir_loc.$old_goods_info->sub1_pic);}
if($img_type=='sub2_pic' && $old_goods_info->sub2_pic && file_exists($dir_loc.$old_goods_info->sub2_pic)) {unlink($dir_loc.$old_goods_info->sub2_pic);}
if($img_type=='sub3_pic' && $old_goods_info->sub3_pic && file_exists($dir_loc.$old_goods_info->sub3_pic)) {unlink($dir_loc.$old_goods_info->sub3_pic);}
if($img_type=='sub4_pic' && $old_goods_info->sub4_pic && file_exists($dir_loc.$old_goods_info->sub4_pic)) {unlink($dir_loc.$old_goods_info->sub4_pic);}
if($img_type=='animation' && $old_goods_info->animation && file_exists($dir_loc.$old_goods_info->animation)) {unlink($dir_loc.$old_goods_info->animation);}

$dbcon->query("update js_auction_goods set {$img_type}='' where idx='{$dbcon->escape($goods_idx)}' ");

exit('ok');