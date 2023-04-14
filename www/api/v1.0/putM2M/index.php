<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$subject = checkEmpty(loadParam('subject'), 'subject'); // subject
$contents = checkEmpty(loadParam('contents'), 'contents'); // contents
$idx = setDefault(loadParam('idx'), ''); // post idx

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// 쓰기
if($idx) {
    // 작성자 확인
    $post_info = $tradeapi->query_fetch_object("SELECT userno, rplcontents FROM js_mtom WHERE idx='{$tradeapi->escape($idx)}' ");
    if($post_info->userno != $userno) {
        $tradeapi->error("100", __('Author information is different.'));
    }
    if($post_info->rplcontents) {
        $tradeapi->error("101", __('Posts with comments can not be modified.'));
    }
    // 수정
    $sql = "UPDATE js_mtom SET `subject`='{$tradeapi->escape($subject)}', `contents`='{$tradeapi->escape($contents)}' WHERE `idx`='{$tradeapi->escape($idx)}' AND `userno`='{$tradeapi->escape($userno)}' ";
    $_r = $tradeapi->query($sql);
} else {
    // 작성자 정보
    $user_info = $tradeapi->query_fetch_object("SELECT userid, if(name='', nickname, name) name FROM js_member WHERE userno='{$tradeapi->escape($userno)}' ");
    // 새글쓰기
    $sql = "INSERT INTO js_mtom SET `subject`='{$tradeapi->escape($subject)}', `contents`='{$tradeapi->escape($contents)}', `sitecode`='{$tradeapi->escape($tradeapi->get_site_code())}', `userno`='{$tradeapi->escape($userno)}',`userid`='{$tradeapi->escape($user_info->userid)}', `author`='{$tradeapi->escape($user_info->name)}', ipaddr='{$tradeapi->escape($_SERVER['REMOTE_ADDR'])}', regdate=UNIX_TIMESTAMP() ";
    $_r = $tradeapi->query($sql);
    // 새글 번호
    $idx = $tradeapi->_recently_query['last_insert_id'];
}

// response
if($_r) {
    $tradeapi->success(array('idx'=>$idx, 'sql'=>$sql));
} else {
    $tradeapi->error('005', __('A system error has occurred.'));
}
