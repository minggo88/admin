<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

/**
 * 관리용 스크립트
 * 캐시 삭제, 로그 삭제 등의 보안과 무관한 관리 작업들을 실행합니다. 
 */

// 캐시 삭제
$tradeapi->clear_old_file(1);
// nginx 캐시 삭제
exec('rm -rf /tmp/nginx/*');

// response
$tradeapi->success();
