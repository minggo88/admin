<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/common_admin.php';

CheckAdmin();

//파일 업로드 위치
$dir_loc = dirname(__file__)."/../../../auction/data/";

$idx = $_POST['idx'];
if(!$idx){
	$goods_idx = gen_idx();
} else {
	$goods_idx = $idx;
}
$dir_loc .= $goods_idx.'/';
if(!file_exists($dir_loc)) {
	mkdir($dir_loc, 0777, true);
}

//유니크 화일이름
if($_FILES['main_pic'] && $_FILES['main_pic']['size']>0) {
	$tmp_filename= gen_idx();
	$main_pic = $tmp_filename.$_FILES["main_pic"]["name"];
	// var_dump($_FILES["main_pic"], $dir_loc.$main_pic, file_exists($dir_loc) );exit;
	$result = fileUpload($_FILES["main_pic"], $dir_loc.$main_pic);
	if(!$result){ write_result(false, '대표 이미지를 저장하지 못했습니다. 이미지를 확인해주세요.'); }
}

if($_FILES['sub1_pic'] && $_FILES['sub1_pic']['size']>0) {
	$tmp_filename= gen_idx();
	$sub1_pic = $tmp_filename.$_FILES['sub1_pic']["name"];
	$result = fileUpload($_FILES['sub1_pic'], $dir_loc.$sub1_pic);
	if(!$result){ write_result(false, '서브1 이미지를 저장하지 못했습니다. 이미지를 확인해주세요.'); }
}

if($_FILES['sub2_pic'] && $_FILES['sub2_pic']['size']>0) {
	$tmp_filename= gen_idx();
	$sub2_pic = $tmp_filename.$_FILES['sub2_pic']["name"];
	$result = fileUpload($_FILES['sub2_pic'], $dir_loc.$sub2_pic);
	if(!$result){ write_result(false, '서브2 이미지를 저장하지 못했습니다. 이미지를 확인해주세요.'); }
}

if($_FILES['sub3_pic'] && $_FILES['sub3_pic']['size']>0) {
	$tmp_filename= gen_idx();
	$sub3_pic = $tmp_filename.$_FILES['sub3_pic']["name"];
	$result = fileUpload($_FILES['sub3_pic'], $dir_loc.$sub3_pic);
	if(!$result){ write_result(false, '서브2 이미지를 저장하지 못했습니다. 이미지를 확인해주세요.'); }
}

if($_FILES['sub4_pic'] && $_FILES['sub4_pic']['size']>0) {
	$tmp_filename= gen_idx();
	$sub4_pic = $tmp_filename.$_FILES['sub4_pic']["name"];
	$result = fileUpload($_FILES['sub4_pic'], $dir_loc.$sub4_pic);
	if(!$result){ write_result(false, '서브4 이미지를 저장하지 못했습니다. 이미지를 확인해주세요.'); }
}

if($_FILES['animation'] && $_FILES['animation']['size']>0) {
	$tmp_filename= gen_idx();
	$animation = $tmp_filename.$_FILES['animation']["name"];
	$result = fileUpload($_FILES['animation'], $dir_loc.$animation);
	if(!$result){ write_result(false, '애니메이션 이미지를 저장하지 못했습니다. 이미지를 확인해주세요.'); }
}

$goods_type     = $_POST['auction_type'];
$title          = $_POST['title'];
$content        = $_POST['content'];
$sell_price     = $_POST['sell_price']*1;
$start_date     = $_POST['start_date'] ? date("Y-m-d H:i:s", strtotime($_POST['start_date'])) : '';
$end_date       = $_POST['end_date'] ? date("Y-m-d H:i:s", strtotime($_POST['end_date'])) : '';
$userid        = $_POST['userid'];
$old_userid     = $_POST['old_userid'];     //혹시 userid 등록 변경할수 잇어서
$auction_title  = $_POST['auction_title'];  //옥션 제목
$auction_idx    = $_POST['auction_idx'];    //옥션 pk

//userid 회원 정보 확인 필요 -- 추가 필요

if(!$idx){      //신규

    $sql = "INSERT INTO js_auction_inventory (userid, goods_idx, buy_price, reg_date) values ('{$dbcon->escape($userid)}', '{$goods_idx}', 0, now())";
    $dbcon->query($sql);

    //아이템 정보 넣기
    $sql = "INSERT INTO js_auction_goods (idx, main_pic, sub1_pic, sub2_pic, sub3_pic, sub4_pic, animation, goods_type, title, content, reg_date)
        VALUES ('{$dbcon->escape($goods_idx)}', '{$dbcon->escape($main_pic)}', '{$dbcon->escape($sub1_pic)}', '{$dbcon->escape($sub2_pic)}', '{$dbcon->escape($sub3_pic)}',
        '{$dbcon->escape($sub4_pic)}', '{$dbcon->escape($animation)}', '{$dbcon->escape($goods_type)}', '{$dbcon->escape($title)}', '{$dbcon->escape($content)}', now())";
    $result = $dbcon->query($sql);

    //시작일 종료일 있으면 경매 시작
    if($start_date && $end_date && $result){
        //옥션 pk 생성 = type의 3글자 + 날짜 시분초ms까지
        $auction_idx = substr($dbcon->escape($goods_type), 0, 3).substr(date("ymdHisu"), 0, 13);

        $sql = "INSERT INTO js_auction_list (auction_idx, goods_idx, auction_title, start_date, end_date, sell_price, finish, reg_date)
            VALUES ('{$auction_idx}', '{$dbcon->escape($goods_idx)}', '{$dbcon->escape($auction_title)}', '{$dbcon->escape($start_date)}', '{$dbcon->escape($end_date)}',
            {$dbcon->escape($sell_price)}, 'N', now())";
        $result = $dbcon->query($sql);
    }
}else{          //수정
    // //회사 아이템 수정 - 상품 보유자를 다른사람으로 바꿀때 주석 푸세요.
    $sql = "UPDATE js_auction_inventory SET userid ='{$dbcon->escape($userid)}' WHERE goods_idx='{$dbcon->escape($goods_idx)}' and userid='{$dbcon->escape($old_userid)}'";
    $re = $dbcon->query($sql);

    if($re){
		// 이전 정보 조회
		$old_goods_info = $dbcon->query_unique_object("SELECT * FROM js_auction_goods WHERE idx='{$dbcon->escape($goods_idx)}' ");

        //아이템 정보 수정
        $sql = "UPDATE js_auction_goods SET goods_type='{$dbcon->escape($goods_type)}', title='{$dbcon->escape($title)}', content='{$dbcon->escape($content)}', mod_date=now()";

        if($main_pic){$sql .= ", main_pic='{$dbcon->escape($main_pic)}'";}
        if($sub1_pic){$sql .= ", sub1_pic='{$dbcon->escape($sub1_pic)}'";}
        if($sub2_pic){$sql .= ", sub2_pic='{$dbcon->escape($sub2_pic)}'";}
        if($sub3_pic){$sql .= ", sub3_pic='{$dbcon->escape($sub3_pic)}'";}
        if($sub4_pic){$sql .= ", sub4_pic='{$dbcon->escape($sub4_pic)}'";}
		if($animation){$sql .= ", animation='{$dbcon->escape($animation)}'";}

        $sql .= " WHERE idx='{$dbcon->escape($goods_idx)}'";
        $result = $dbcon->query($sql);

        //시작일 종료일 있으면 경매 시작
        if($auction_idx){
            $sql = "UPDATE js_auction_list SET start_date='{$dbcon->escape($start_date)}', end_date='{$dbcon->escape($end_date)}', auction_title='{$dbcon->escape($auction_title)}', sell_price='{$sell_price}', mod_date=now() WHERE auction_idx='{$dbcon->escape($auction_idx)}'";
            $result = $dbcon->query($sql);
		} else {
			if($start_date && $end_date) {
				$auction_idx = substr($dbcon->escape($goods_type), 0, 3).substr(date("ymdHisu"), 0, 13);
				$sql = "INSERT INTO js_auction_list (auction_idx, goods_idx, auction_title, start_date, end_date, sell_price, finish, reg_date)
				VALUES ('{$auction_idx}', '{$dbcon->escape($goods_idx)}', '{$dbcon->escape($auction_title)}', '{$dbcon->escape($start_date)}', '{$dbcon->escape($end_date)}',
				{$dbcon->escape($sell_price)}, 'N', now())";
				$result = $dbcon->query($sql);
			}
		}

		// 이전 이미지 삭제
		if($main_pic && $old_goods_info->main_pic && file_exists($dir_loc.$old_goods_info->main_pic)) {unlink($dir_loc.$old_goods_info->main_pic);}
		if($sub1_pic && $old_goods_info->sub1_pic && file_exists($dir_loc.$old_goods_info->sub1_pic)) {unlink($dir_loc.$old_goods_info->sub1_pic);}
		if($sub2_pic && $old_goods_info->sub2_pic && file_exists($dir_loc.$old_goods_info->sub2_pic)) {unlink($dir_loc.$old_goods_info->sub2_pic);}
		if($sub3_pic && $old_goods_info->sub3_pic && file_exists($dir_loc.$old_goods_info->sub3_pic)) {unlink($dir_loc.$old_goods_info->sub3_pic);}
		if($sub4_pic && $old_goods_info->sub4_pic && file_exists($dir_loc.$old_goods_info->sub4_pic)) {unlink($dir_loc.$old_goods_info->sub4_pic);}
		if($animation && $old_goods_info->animation && file_exists($dir_loc.$old_goods_info->animation)) {unlink($dir_loc.$old_goods_info->animation);}
    }
}
$dbcon->close();

write_result(true);

function fileUpload($file,$save_file)
{
    //if($file['size'] > 20000) {
        //echo('err_size');
       // return false;
    //}
    //폴더에 동일한 파일이 존재하는지 확인한후 동일한 화일이 존재할 경우 삭제한다.
    if(file_exists($save_file) ){
        unlink($save_file);
    }
    if(move_uploaded_file($file['tmp_name'],$save_file)) {
        @unlink($file['tmp_name']);
        return TRUE;
    }
    else {
        //echo('err_upload');
        return false;
    }
}

//pk 생성
function gen_idx() {
	$time = str_replace('.','',sprintf('%01.6f', array_sum(explode(' ',microtime())))); // 16자리 milliseconds
	return strtoupper(base_convert($time, 10, 36)); // 36진법으로 변경
}


function write_result($result, $msg='') {
	if($result){
		echo '<script>alert("저장했습니다.");location.href="/auction/admin/auctionAdmin.php";</script>';
	} else {
		echo '<script>alert("저장하지 못했습니다. '.$msg.'");';
		echo '</script>';
	}
	exit();
}