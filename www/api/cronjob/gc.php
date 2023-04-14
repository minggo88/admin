<?php
/**
 * 청소하자!!
 */
ob_implicit_flush(true);
ignore_user_abort(1);
set_time_limit(0);

$_REQUEST['token'] = 'gc';

include(dirname(__file__).'/../lib/TradeApi.php');

// 세션 청소
session_gc();

// 로그 청소
$tradeapi->delete_cache();
$tradeapi->delete_log();

