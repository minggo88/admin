<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$cid = addHit(loadParam('cid'));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

if(!$void) {
  $c = $tradeapi->query_list_tsv("SELECT idx, bbscode, userid, author, subject_kr, subject_en, subject_cn, contents_kr, contents_en, contents_cn, website, file, hit, regdate FROM js_bbs_main where idx='{$tradeapi->escape($cid)}'");
}

// response
$tradeapi->success($c);
