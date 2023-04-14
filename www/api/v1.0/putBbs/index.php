<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();
$user_info = $tradeapi->db_get_row('js_member', array('userno'=>$userno));
$author = $user_info->name;
$userid = $user_info->userid;

// validate parameters
$link_idx = checkEmpty($_REQUEST['link_idx']);
$bbscode = checkEmpty($_REQUEST['bbscode'],'bbscode');
$subject = checkEmpty($_REQUEST['subject'],'제목');
$media = setDefault($_REQUEST['media'],'');
$contents = checkEmpty($_REQUEST['contents'],'내용');
$ipaddr = $_SERVER['REMOTE_ADDR'];
$idx = setDefault($_REQUEST['idx'], '');
$thread = checkNumber(setDefault($_REQUEST['thread'], 1));
$pos = checkNumber(setDefault($_REQUEST['pos'], 1));
$depth = checkNumber(setDefault($_REQUEST['depth'], 1));
$file_src = setDefault($_REQUEST['file_src'], '');

// --------------------------------------------------------------------------- //


// s3 이미지 파일 경로 변경
if($file_src && strpos($file_src, '/tmp/')!==false && strpos($file_src, '.s3.')!==false) {
    $new_file_src = $tradeapi->copy_tmpfile_to_s3($file_src);
    if( $new_file_src ) {
        $tradeapi->delete_file_to_s3($file_src);
        $file_src = $new_file_src;
    } else {
        $tradeapi->error('200', '이미지를 S3저장소에 저장하지 못했습니다.');
    }
}


$data = array(
    'link_idx'=>$link_idx, 
    'userid'=>$userid, 
    'passwd'=>$userno, 
    'bbscode'=>$bbscode, 
    'author'=>$author, 
    'subject_kr'=>$subject, 
    'media'=>$media, 
    'contents_kr'=>$contents, 
    'ipaddr'=>$ipaddr, 
    'file_src'=>$file_src, 
    // 'thread'=>$thread, 
    // 'pos'=>$pos, 
    'regdate'=>time()
);

// 슬레이브 디비 사용하도록 설정.d
$tradeapi->set_db_link('slave');

if($idx) {
    $tradeapi->db_update('js_bbs_main', $data, array('idx'=>$idx));
} else {
    // $idx = '';//$tradeapi->gen_id();
    // $data['idx'] = ''; //$idx;
    $tradeapi->db_insert('js_bbs_main', $data);
    $idx = $tradeapi->_recently_query['last_insert_id'];
}


// response
$tradeapi->success(array('idx'=>$idx));
