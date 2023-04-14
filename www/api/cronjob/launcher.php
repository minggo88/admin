<?php
ignore_user_abort(1);
set_time_limit(0);

/**
 * CLI로 실행해야 하는것들을 실행시켜줍니다. 
 * cronjob으로 실행해야 하는것들이 많아져서 launcher에 모아둡니다.
 * 이 파일을 매분 cronjob으로 실행시킵니다. 
 * 각각의 스크립트들은 background로 실행 시킵니다.
 * 각각의 스크립트는 1분에 하나씩만 작동하도록 구성해주세요.  
 * @todo 각각의 스크립트를 async로 실행시켜야 함.
 */

$current_dir_path = dirname(__file__);

// @ exec("ps  -ef| grep -i '".__file__." ' | grep -v grep", $output);
@ exec("ps  -ef| grep -i 'launcher.php ' | grep -v grep", $output);
if(count($output)>1) {
    exit('Already working.');
}


$domain = isset($argv[1]) ? trim($argv[1]) : '';// 서버마다 바꿔줘야 함.
if($domain == '') {

    echo "[Fail] Please enter the domain of the current server.\n";
    exit;

} else {

    echo "$domain server start launcher.\n";

    while(true) {

        /**
         * get block.cc ticker api data
         * 10분에 한번씩 실행합니다. 중복여부는 스스로 처리합니다.
         * @todo 작업 시간이 걸려 async 적용 후 실행시키기. 그전에는 cronjob 사용.
         */
        if(date('i')%10==2) {
            
            $cmd = "/usr/bin/php {$current_dir_path}/currency.php 1> /dev/null 2>&1 &";
            // echo "$cmd\n";
            // Proc_Close(Proc_Open ($cmd, Array (), $foo));

        }

        /**
         * trade.php 실행. - 차트 데이터 생성.
         * 10분에 한번씩 실행합니다. 중복여부는 스스로 처리합니다.
         * @todo 쿼리 분석 하기.
         */
        if(date('i')%10==0) {
            $cmd = "/usr/bin/php {$current_dir_path}/trade.php 1> /dev/null 2>&1 &";
            // echo "$cmd\n";
            Proc_Close(Proc_Open ($cmd, Array (), $foo)); //잠시 중단합니다
        }

        /**
         * trade.php 실행. - bithumb 차트 데이터 생성.
         * 1분에 한번씩 실행합니다. 중복여부는 스스로 처리합니다.
         */
        // if(date('i')%10==0) {
            // $cmd = "/usr/bin/php {$current_dir_path}/genChart.php 1> /dev/null 2>&1 &";
            // echo "$cmd\n";
            // Proc_Close(Proc_Open ($cmd, Array (), $foo)); //잠시 중단합니다
        // }


        /**
         * bt-player.php 실행.
         * 10분에 한번씩 실행합니다. 중복여부는 스스로 처리합니다.
         */
        if(date('i')%10==1) {
            $symbols = array('btc', 'scc', 'eth', 'ltc', 'bch', 'qtum', 'eos', 'xrp');
            // $exchanges = array_merge($symbols, array('usd'));
            $exchanges = array('krw');
            foreach($symbols as $symbol ) {
                foreach($exchanges as $exchage ) {
                    if($symbol == $exchage) {
                        continue;
                    }
                    $cmd = "/usr/bin/php {$current_dir_path}/bt-player.php {$symbol} {$exchage} {$domain} 1> /dev/null 2>&1 &";
                    // echo "$cmd\n";
                    Proc_Close(Proc_Open ($cmd, Array (), $foo));

                }
            }
        }

        // next
        $rs = 60 - date('s'); // 60 - 현재 초 == 남은 초
        sleep($rs); // 00초에 작업 시작.

    }
}
