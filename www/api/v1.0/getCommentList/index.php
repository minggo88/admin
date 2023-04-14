<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$link_idx = checkEmpty($_REQUEST['link_idx']);
$bbscode = setDefault($_REQUEST['bbscode'],'');
$lastCid = checkNumber(setDefault($_REQUEST['lastCid'], 0));
$limit = checkNumber(setDefault($_REQUEST['limit'], 20));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.d
$tradeapi->set_db_link('slave');

$query = "SELECT idx, link_idx, userno, userid, bbscode, author, contents, ipaddr, thread, pos, like_cnt, regdate regtime, from_unixtime(regdate) regdate, userno='{$userno}' my_comment  FROM js_bbs_comment WHERE warning_date IS NULL AND link_idx='{$tradeapi->escape($link_idx)}' ";
if($bbscode) { $query.= " and bbscode='{$tradeapi->escape($bbscode)}' "; }
$query.= " ORDER BY idx DESC LIMIT {$tradeapi->escape($lastCid)}, {$tradeapi->escape($limit)}";
$c = $tradeapi->query_list_object($query);

// response
$tradeapi->success($c);
