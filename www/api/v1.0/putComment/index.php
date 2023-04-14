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
$bbscode = checkEmpty($_REQUEST['bbscode'],'');
$contents = checkEmpty($_REQUEST['contents'],'');
$ipaddr = $_SERVER['REMOTE_ADDR'];
$idx = setDefault($_REQUEST['idx'], '');
$thread = checkNumber(setDefault($_REQUEST['thread'], 1));
$pos = checkNumber(setDefault($_REQUEST['pos'], 1));
$depth = checkNumber(setDefault($_REQUEST['depth'], 1));

// --------------------------------------------------------------------------- //

$data = array(
    'link_idx'=>$link_idx, 
    'userno'=>$userno, 
    'userid'=>$userid, 
    'passwd'=>$userno, 
    'bbscode'=>$bbscode, 
    'author'=>$author, 
    'contents'=>$contents, 
    'ipaddr'=>$ipaddr, 
    'thread'=>$thread, 
    'pos'=>$pos, 
    'regdate'=>time()
);

// 슬레이브 디비 사용하도록 설정.d
$tradeapi->set_db_link('slave');

if($idx) {
    $tradeapi->db_update('js_bbs_comment', $data, array('idx'=>$idx));
} else {
    $idx = $tradeapi->gen_id();
    $data['idx'] = $idx;
    $tradeapi->db_insert('js_bbs_comment', $data);
}


// response
$tradeapi->success(array('idx'=>$idx));
