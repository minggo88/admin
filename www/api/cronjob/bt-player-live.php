<?php
ob_implicit_flush(true);
ignore_user_abort(1);
set_time_limit(0);

define('__API_RUNMODE__', 'live');
include(dirname(__file__).'/../lib/TradeApi.php');
//var_dump(__API_RUNMODE__); exit;

/**
 * 자동 거래 봇.
 * 단순 해외 시세이 맞게 호가를 생성하고 매매도 진행하는 봇 스크립트입니다.
 * 테스트용 데이터 생성을 목적으로 작동됩니다.
 * 서비스 라이브 시에 중단됩니다.
 */

$symbol = $argv[1] ? strtoupper($argv[1]) : '';
$exchange = $argv[2] ? strtoupper($argv[2]) : $tradeapi->default_exchange; // usd, krw
$base_price = $argv[3] ? preg_replace( '/[^0-9.]/', '', $argv[3]) : '';
$host = $argv[4] ? ($argv[4]) : 'https://api.kmcse.com';
$deamon = $argv[5] ? false : true; // 단일 실행 용.
$run = true;

$tradeapi->logging = true;
$tradeapi->set_log_dir(dirname(__file__).'/../log/bt-player-live/');
$tradeapi->set_log_name($symbol.'_'.$exchange);
// $clear_time = date('Ymd');//''; // 마지막 로그 초기화 시간값. 시작시 이전로그 삭제하려면 빈문자열 넣으면 됩니다. 분단위로 초기화 하려면 YmdHi 까지 저장하도록 하면 됩니다.



if(! $symbol) {
    $tradeapi->write_log( 'Please, set the symbol.');
    exit('Please, set the symbol.');
}
if($symbol=='ALLKILL') {
    @ exec("kill -9 `ps -ef | grep -i bt-player | grep -v grep | awk '{print $2}'`", $output);
    $tradeapi->write_log( 'All processes have been terminated.');
    exit('All processes have been terminated.');
}
// 프로세스 작동중인지 확인. 작동중이면 종료.
$file = __file__;
if($deamon) {
    @ exec("ps  -ef| grep -i '{$file} {$symbol} {$exchange}' | grep -v grep", $output);
    if(count($output)>1) {
        $tradeapi->write_log( 'Already working.');
        exit('Already working.');
    }
}
$tradeapi->write_log( 'Process start.');

$user_jar = array( // testing account
    array('no'=>'3599', 'id'=>'Samantha.Mathis@gmail.com', 'pw'=>'TkaksGbQrD'),
    array('no'=>'3600', 'id'=>'Alan.Tudyk@gmail.com', 'pw'=>'AlanJ7bM9')
);

// 쿠키파일 저장폴더
$cookie_dir = __DIR__.'/../cache/cookies';
if(!file_exists($cookie_dir)) {
    mkdir($cookie_dir, 0777);
}
// var_dump($user_jar, $cookie_dir); exit;

$loop_start_time = time(); // 무한반복 시작시간. PHP 매모리 누수 이슈로 서비스 이상이 발생함. 그래서 한시간에 한번씩 작업을 강재로 종료시켜서 메모리를 반환해주기로 함. 자동 실행은 ps_check.sh에서 실행.
while($run) {

    // 사용자 추출.
    $i = time()%count($user_jar);
    $userno = $user_jar[ $i ]['no'];
    $userid = $user_jar[ $i ]['id'];
    $userpw = $user_jar[ $i ]['pw'];
    // set cookie jar
    $tradeapi->setCookieJar (__DIR__.'/../cache/cookies/cookies.'.$userno.'.txt');

    // 1. 해외 사이트 가격 가져오기. 내부 코인은 유사 코인을 설정하기.
    switch($symbol) {
        // case 'SCC' :
        //     $symbol_t = 'EMC2'; // 참고코인
        //     $base_price = ''; // 기준가. 특정 가격으로 고정시킬때 사용.
        //     break;
        // case 'JIN' :
        //     $symbol_t = 'MCO'; // 참고코인
        //     // $base_price = ''; // 기준가. 특정 가격으로 고정시킬때 사용.
        //     break;
        // case 'FINT' :
        //     $symbol_t = 'LSK'; // 참고코인
        //     $base_price = '1000'; // 기준가. 특정 가격으로 고정시킬때 사용.
        //     break;
        // case 'APC' :
        //     $symbol_t = 'GTO'; // 참고코인
        //     $base_price = ''; // 기준가. 특정 가격으로 고정시킬때 사용.
        //     break;
        default :
            $symbol_t = $symbol; // 참고코인
            // $base_price = ''; // 기준가. 특정 가격으로 고정시킬때 사용.
    }
    // $tp = $tradeapi->get_external_spot_price($symbol_t, $exchange);
    // binance 가격으로 싱크
    if($base_price>0) {
        $market_price = $base_price;
    } else {
        $tp = $tradeapi->get_external_price($symbol_t, 'KRW', 'binance'); // 시세는 USD로 고정
        $market_price = 0;
        if($tp) {
            $market_price = $tp*1;
        }
    }
    // var_dump('$market_price:'.$market_price, $symbol, $symbol_t, $exchange , $tp); exit;
    $tradeapi->write_log( 'current_price:'.$market_price); //exit;

    if($market_price > 0) {
        // 대량거래
        $_big_trade = false;
        // 라이브 당분간 대량거래 미발생 시킴.
        if(mt_rand(1,1000)>990) { // 1% 확률로 대량거래를 발생시킴
            $_big_trade = true; // 매매시 매수나 매도량을 크게 늘림.
        }


        // login
        // var_dump($host.'/login/', "userid={$userid}&userpw={$userpw}&pg_mode=auth", 1); //exit;
        $r = $tradeapi->remote_post($host.'/v1.0/login/', "userid={$userid}&userpw={$userpw}&pg_mode=auth&uuid=T1&os=curl&fcm_tokenid=1234");
        $r = json_decode($r);
        // var_dump($r); exit;
        // get token
        $token = $r->success ? $r->payload->token : '';
        if( $r && $r->success) {
            $token = $r->payload->token;
        } else {
            $tradeapi->write_log("Fail login. userid:{$userid}, userpw={$userpw}"); exit($host.'/v1.0/login/'." Fail login. userid:{$userid}");
            continue;
        }
        // $tradeapi->write_log("success login. userid:{$userid}, token={$token}");
        $r = null;
        // var_dump($token); exit;

        // 1일 이상된 미거래주문 취소처리하기.
        // 당분간 수동 작동시켜보기.
        cancel_old_order($tradeapi, $host, $token, $symbol, $exchange);
        // exit($userid.' 취소 완료');

        // $market_price = $symbol=='SCC' ? $market_price/1000*100 : $market_price; // 2018.8.16 그대로 둠. 따로 이야기 나올때 다시 조정하기.
        // $market_price = $symbol=='SCC' ? 0.05 : $market_price; // $ 0.01 에 비슷하게 가격 맞추기.  가격의 최소값이 0.01이라서 0.05 선으로 나오도록 기준가격 수정.
        // 2. 호가 생성 - 종가 기준 매수 15호가 매도 15호가, 수량은 0 ~ 0.1% (전체 매수 가능금액, 매도 가능수량)
        // 호가 생성.
        // $delta = mt_rand(-1000, 1000)/100000; // -1% ~ +1%
        // $delta_price = $market_price * $delta;
        // $market_price += $delta_price; // buy sell에 무관하게 가격을 정해야 함. 그래야 매매가 됨.
        $delta_rate = mt_rand(-25, 25)/10000; // 기본 -0.25% ~ +0.25% 범위로 매매한다.
        // $delta_rate = mt_rand(-150, 150)/10000; // 기본 1.5% 범위로 매매한다.
        // $delta_rate = mt_rand(-300, 300)/10000; // 기본 3% 범위로 매매한다.
        if(mt_rand(0,100)>95) { // 5% 확률로
            $d = mt_rand(500, 1000);
            $delta_rate = mt_rand(-1 * $d, $d)/10000; // 가끔 5%범위로 매매한다.
        }
        if($market_price<1) {
            $delta_rate = mt_rand(-5000, 5000)/10000; // 가격 범위를 좀더 넓힘. 너무 정직하게 거래하고 있어서 차트가 이상함.
        }
        if($market_price>=1 && $market_price<1000) {
            // $delta_rate = mt_rand(-10, 10)/100; // 가격 범위를 좀더 넓힘. 너무 정직하게 거래하고 있어서 차트가 이상함.
        }
        if($market_price>=1000 && $market_price<2000) {
            // $delta_rate = mt_rand(-10, 10)/100; // 가격 범위를 좀더 넓힘. 너무 정직하게 거래하고 있어서 차트가 이상함.
        }
        if($market_price>=2000 && $market_price<5000) {
            // $delta_rate = mt_rand(-10, 10)/100; // 가격 범위를 좀더 넓힘. 너무 정직하게 거래하고 있어서 차트가 이상함.
        }
        if($market_price>=500 && $market_price<10000) {// 가격 범위를 좀더 넓힘. 너무 정직하게 거래하고 있어서 차트가 이상함.
        }
        // $delta_rate = mt_rand(-10, 10)/100; // 가격 범위를 좀더 넓힘. 너무 정직하게 거래하고 있어서 차트가 이상함.
        if($_big_trade) { // 대량거래시 가격범위를 30%로 넓힘.
            if(mt_rand(1,2)==1) {
                $delta_rate = mt_rand(1100, 3000)/10000;
            } else {
                $delta_rate = mt_rand(1100, 3000)/10000 * -1;
            }
        }
        // var_dump('$delta_rate:'.$delta_rate); //exit;



        $delta_price = $market_price * $delta_rate;
        $order_price = $market_price + $delta_price; // buy sell에 무관하게 가격을 정해야 함. 그래야 매매가 됨.
        // var_dump($delta_rate, $delta_price, $market_price, $order_price); exit;
        // 호가 단위 가격으로 설정
        $order_price = $tradeapi->get_quote_price ($order_price, $exchange);
        // var_dump('$order_price:'.$order_price); exit;

        // 현재가
        $current_price = $tradeapi->remote_post($host."/v1.0/getSpotPrice/", "symbol={$symbol}");
        // $tradeapi->write_log("unite curren_price={$current_price}");
        $current_price = json_decode($current_price);
        if($current_price->success) {
            $current_price = $current_price->payload[0]->price_close;
        } else {
            $current_price = $market_price;
        }
        // var_dump($current_price); exit;

        // buy ? sell ?
        $dice = mt_rand(1,10);
        $method = $dice > 5 ? 'sell' : 'buy'; // 기본 반반 확률
        if($current_price < $market_price) { // 시세가 현재가보다 높을경우 매수 우위
            $method = $dice < 4 ? 'sell' : 'buy';
        } elseif ($current_price > $market_price) { // 시세가 현재가보다 낮은경우 매도 우휘
            $method = $dice > 7 ? 'buy' : 'sell';
        }

        // 거래금액
        $amount = mt_rand(10, 1000)*100; // 1회 거래금액을 1천원~10만원 사이로 지정. -> 100~500 으로 변경
        // 대량거래 발생. 빅거래시 매매가 발생하는 경우 거래량을 확 늘려.
        if($_big_trade && ($current_price < $order_price && $method=='buy' || $current_price > $order_price && $method=='sell')) {
            $amount * mt_rand(10, 200);
        }
        // 수량 설정.
        // $volume = mt_rand(10, 800)/1000; // 거래횟수를 늘리고 거래량은 줄임.
        $volume = round($amount / $order_price, 4);

        // 매매
        $r = $tradeapi->remote_post($host."/v1.0/{$method}/", "token={$token}&symbol={$symbol}&exchange={$exchange}&price=".numtostr($order_price)."&volume=".numtostr($volume));
        $tradeapi->write_log("userid: $userid, method: $method, symbol: $symbol, order_price:".numtostr($order_price).", volume: ".numtostr($volume).", delta_rate:{$delta_rate}, result: $r");
        $r = null;

        // exit('매매 테스트 종료');

    }

    if(! $deamon) {

        //var_dump($host."/api/v1.0/{$method}/", "symbol={$symbol}&exchange={$exchange}&price={$order_price}&volume={$volume}", 'current_price:'.$market_price, '$delta_price:'.$delta_price, '$r:', $r);
        echo "userno: $userno ".PHP_EOL;
        echo "url: $host/api/v1.0/{$method}/?token={$token}&symbol={$symbol}&exchange={$exchange}&price={$order_price}&volume={$volume}".PHP_EOL;
        echo 'current_price:'.$market_price.PHP_EOL;
        echo 'delta_price:'.$delta_price.PHP_EOL;
        echo 'result:'.$r.PHP_EOL;
        $run = false;

    } else {

        // 무한반복 시작한지 10분이 지났으면 일단 종료.
        if(time() - $loop_start_time > 60*10) {
            exit('시스템 매모리 반환');
        }

        $max_sleep_time = 90; //
        switch(date('H')) {
            case '10':
            case '14':
            case '15':
            case '16':
                $max_sleep_time = 60; // 시간을 좀더 줄임.
            break;
            case '08':
            case '09':
            case '11':
            case '12':
            case '13':
            case '14':
            case '17':
            case '18':
            case '19':
            case '20':
            case '21':
            case '22':
            case '23':
                $max_sleep_time = 15; // 시간을 좀더 줄임.  60초
            break;
        }

        sleep(mt_rand(1, $max_sleep_time)); // 작동 시간 랜덤 1~120초.
        // usleep(500000); // 테스트용

    }

}

echo 'Process end.';


function cancel_old_order($tradeapi, $host, $token, $symbol, $exchange, $orderid='', $t=0) {
    // var_dump('================',$symbol, $exchange, $orderid, $t);
    // 1일 이상된 미거래주문 취소처리하기.
    $r = $tradeapi->remote_post($host.'/v1.0/getOpenOrderList/', "token={$token}&symbol={$symbol}&exchange={$exchange}&orderid={$orderid}&rows=100&page=1");
    $r = json_decode($r);
    // var_dump($host.'/v1.0/getOpenOrderList/', $r); exit;
    // if($t>=10) { // 너무 오래 작업하지 않도록 하기 위함. 어짜피 미거래건만 추출하기 때문에 100개정도 확인했으면 됬음.
    //     // exit('작업 종료 $t :'.$t);
    //     return true;
    // }
    if($r->success) {
        if($r->payload && count($r->payload)>0) {
            foreach($r->payload as $order) {
                // var_dump($t.','. $order->orderid.','.$order->status.','. date('Y-m-d H:i:s', $order->time_order)); // exit;
                $orderid = $order->orderid;
                if($order->orderid && $order->time_order < time()-60*60*24) {
                    $r2 = $tradeapi->remote_post($host.'/v1.0/cancel/', "token={$token}&symbol={$symbol}&exchange={$exchange}&orderid={$orderid}");
                    $tradeapi->write_log('취소 $orderid :'.$orderid. ', $r2:'. $r2);
                    //var_dump('$r2:'. $r2.', $orderid:'.$orderid); //exit;
                }
            }
            // usleep(200000); // sleep 0.2 sec.
            // $t++;
            // return cancel_old_order($tradeapi, $host, $token, $symbol, $exchange, $orderid, $t);
        } else {
            // $tradeapi->write_log('취소 작업 종료 $t :'.$t);
            return true;
        }
    } else {
        return false;
    }
}