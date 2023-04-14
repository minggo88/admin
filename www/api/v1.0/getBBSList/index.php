<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters
$link_idx = $_REQUEST['link_idx'];
$bbscode = $_REQUEST['bbscode'];
$lastCid = checkNumber(setDefault($_REQUEST['lastCid'], 0));
$limit = checkNumber(setDefault($_REQUEST['limit'], 20));

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.d
$tradeapi->set_db_link('slave');

$query = 'SELECT count(idx) FROM js_bbs_main where bbscode=\''.$bbscode.'\'';
if($link_idx) { $query.= " and link_idx='{$tradeapi->escape($link_idx)}' "; }
$total = $tradeapi->query_one($query);
$lang = $tradeapi->get_i18n_lang();
$lang_c = preg_replace('/[^a-zA-Z]/','', $lang);
if($lang_c=='ko') $lang_c='kr';
if($lang_c=='zh') $lang_c='cn';

$query = "SELECT idx, bbscode, link_idx, userid, author, IF(subject_{$lang_c}='', subject_kr, subject_{$lang_c}) `subject`, media, IF(contents_{$lang_c}='', contents_kr, contents_{$lang_c}) `contents`, `website`, `file`, `file_src`, `hit`, `regdate` FROM js_bbs_main WHERE bbscode='{$tradeapi->escape($bbscode)}' ";
if($link_idx) { $query.= " and link_idx='{$tradeapi->escape($link_idx)}' "; }
$query.= " ORDER BY idx DESC LIMIT {$tradeapi->escape($lastCid)}, {$tradeapi->escape($limit)}";
$c = $tradeapi->query_list_object($query);


// response
$tradeapi->success($c);
