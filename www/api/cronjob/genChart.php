<?php

/**
 * 거래소 차트 데이터 생성.
 * 
 * 실행 명령어: 
 * $ php genChart.php JIN USD 
 * $ php genChart.php JIN USD start
 * 종료 명령어:
 * $ php genChart.php JIN USD stop
 */
include(dirname(__file__) . '/../lib/TradeApi.php');
/*
ignore_user_abort(1);
set_time_limit(0);

// $symbol = $argv[1] ? strtoupper($argv[1]) : '';
// $exchange = $argv[2] ? strtoupper($argv[2]) : $tradeapi->default_exchange; // usd, krw
// $runmode = $argv[3]; // start / stop
$runmode = $argv[1]; // start / stop

$filename = __file__;
if ($runmode == 'stop') {
    @exec("kill -9 `ps -ef | grep -i '{$filename}' | grep -v grep | awk '{print $2}'`", $output);
    exit('Processes have been terminated.');
}

// 프로세스 작동중인지 확인. 작동중이면 종료.
@exec("ps  -ef| grep -i '{$filename}' | grep -v grep", $output);
if (count($output) > 1) {
    exit();
}

$tradeapi->set_db_link('master');

$loop_start_time = time(); // 무한반복 시작시간. PHP 매모리 누수 이슈로 서비스 이상이 발생함. 그래서 한시간에 한번씩 작업을 강재로 종료시켜서 메모리를 반환해주기로 함. 자동 실행은 ps_check.sh에서 실행. 
while (true) {


    // $tradeapi->set_db_link('master');
    // $tradeapi->_db_connect('master');
    $items = $tradeapi->query_list_object("SELECT symbol, exchange FROM js_trade_currency WHERE active='Y' AND symbol<>'{$tradeapi->escape($tradeapi->default_exchange)}'");
    // var_dump($items); exit;
    // $tradeapi->db_close(); // fork 전에 db 접속 종료

    $cnt = count($items);
    for ($i = 0; $i < $cnt; $i++) {

        // fork 용 fork 로 병렬처리하려 했지만 DB 접속이 종료되서 사용 못함.
        // $pid = function_exists('pcntl_fork') ? pcntl_fork() : 0;
        // if (!$pid) {
        //     $tradeapi->set_db_link('master'); // fork 후 db 접속
        //     exit('child pid: '.$pid.PHP_EOL);

            $item = $items[$i];
            $symbol = $item->symbol;
            $exchange = $item->exchange;
            $table_txn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn';
            $table_chart = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_chart';

            $tradeapi->check_table_exists($table_txn);

            if ($symbol && $exchange && $tradeapi->check_table_exists($table_txn) && $tradeapi->check_table_exists($table_chart)) {
                // echo "$symbol start \n";
                // ob_flush();
    
                // 매모리 태이블 사이즈 증가시키기.
                // $tradeapi->query("SET GLOBAL tmp_table_size = 1024 * 1024 * 1024 * 2 "); // 
                // $tradeapi->query("SET GLOBAL max_heap_table_size = 1024 * 1024 * 1024 * 2 "); // 
                // $mt = microtime(true);
    
                // 1분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(1));
                $tradeapi->save_bong_data($symbol, $exchange, '1m', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '1m');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 3분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(3));
                $tradeapi->save_bong_data($symbol, $exchange, '3m', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '3m');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 5분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(5));
                $tradeapi->save_bong_data($symbol, $exchange, '5m', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '5m');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 10분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(10));
                $tradeapi->save_bong_data($symbol, $exchange, '10m', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '10m');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 15분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(15));
                $tradeapi->save_bong_data($symbol, $exchange, '15m', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '15m');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 30분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(30));
                $tradeapi->save_bong_data($symbol, $exchange, '30m', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '30m');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 60분봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(60));
                $tradeapi->save_bong_data($symbol, $exchange, '1h', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '1h');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 12시간봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(720));
                $tradeapi->save_bong_data($symbol, $exchange, '12h', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '12h');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 1일봉
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(1440));
                $tradeapi->save_bong_data($symbol, $exchange, '1d', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '1d');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 1주
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(1440 * 7));
                $tradeapi->save_bong_data($symbol, $exchange, '1w', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '1w');
                $_bong = null; // var_dump(microtime(1) - $mt);
                // 1월
                $_bong = $tradeapi->get_bong_data($symbol, $exchange, $tradeapi->get_start_time(1440 * 30));
                $tradeapi->save_bong_data($symbol, $exchange, '1M', $_bong->date, $_bong->open, $_bong->high, $_bong->low, $_bong->close, $_bong->volume);
                $tradeapi->delete_old_data($symbol, $exchange, '1M');
                $_bong = null; // var_dump(microtime(1) - $mt);
    
            // fork 용
            // }
            // exit('children stop');
        }
    }
    
    // fork 용 - 부모 대기하기
    // while (function_exists('pcntl_waitpid') && pcntl_waitpid(0, $status) != -1) {
    //     $status = pcntl_wexitstatus($status);
    // }

    unset($_bong);

    // 무한반복 시작한지 10분이 지났으면 일단 종료. 
    // if (time() - $loop_start_time > 60 * 10) {
    //     exit('시스템 매모리 반환');
    // }

    // exit('1');
    sleep(1); // 1초에 한번씩 다시 작동, 코인별로 프로세스 분리해서 작동 간격을 줄임.

}

echo 'Process end.';
*/