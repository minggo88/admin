<?php
/**
 * 1:1 문의 게시판 목록
 */
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$lastCid = checkNumber(setDefault($_REQUEST['lastCid'], 0));
$limit = checkNumber(setDefault($_REQUEST['limit'], 20));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

$sitecode = $tradeapi->get_site_code();

$query = "SELECT COUNT(idx) FROM js_mtom WHERE sitecode='{$tradeapi->escape($sitecode)}' AND userno='{$tradeapi->escape($userno)}'  ";
$total = $tradeapi->query_one($query);

$query = "SELECT * FROM js_mtom WHERE sitecode='{$tradeapi->escape($sitecode)}' AND userno='{$tradeapi->escape($userno)}' ORDER BY regdate DESC LIMIT {$tradeapi->escape($lastCid)}, {$tradeapi->escape($limit)}";
$query = "SELECT * FROM js_mtom WHERE sitecode='{$tradeapi->escape($sitecode)}' ORDER BY regdate DESC LIMIT {$tradeapi->escape($lastCid)}, {$tradeapi->escape($limit)}";
$c = $tradeapi->query_list_object($query);

// response
$tradeapi->success($c);
