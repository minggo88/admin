<?php
ignore_user_abort(1);
set_time_limit(0);

/**
 * 코인 가격 정보 파일 만들기 스크립트. 
 * data.block.cc/api 이용.
 * 1분 간격 실행 - cronjob 사용.
 * 각각의 단위작업별로 실행시간이 다릅니다.
 */

include (__DIR__.'/../lib/TradeApi.php');

// 다른 거래소 가격데이터 미리 캐싱. 5분에 한번
if(date('i')%5==0) { $runable_fn1 = true; } else { $runable_fn1 = false; }
// 시가총액 계산용 유통공급량 저장. 1시간에 한번
if(date('i')%60==5) { $runable_fn2 = true; } else { $runable_fn2 = false; }
// $runable_fn1 = 1;
// $runable_fn2 = 1;


//가격 대이터 가져오기
$sql = "SELECT symbol, name, 'upbit' as market FROM js_trade_currency WHERE active='Y' AND crypto_currency='Y' AND tradable='Y'";
$currencies = $tradeapi->query_list_object($sql);
$nc = array();
$nc[] = (object) array('symbol'=>'MCO', 'name'=>'크립토닷컴', 'market'=>'upbit');
$currencies = array_merge($currencies, $nc);
// var_dump($currencies); exit;
// $sql = "SELECT symbol FROM js_trade_currency WHERE active='Y' AND crypto_currency='N'";
// $exchange = $tradeapi->query_list_object($sql);
$exchange = array();
$exchange[] = (object) array('symbol'=>'KRW');
// var_dump($currencies, $exchange); exit;
$t = array();
foreach($exchange as $s) { $t[] = $s->symbol; }
$exchange = $t;

foreach($currencies as $currency) {
    
    $s = $currency->symbol;
    
    // 다른 거래소 가격데이터 미리 캐싱.
    if($runable_fn1) {
        foreach($exchange as $e) {
            $url = 'https://data.block.cc/api/v1/tickers?symbol={$symbol}&currency={$exchange}';
            $u = str_replace(array('{$symbol}', '{$exchange}'), array($s, $e), $url);
            $r = $tradeapi->remote_get($u);
            // fail  -  {"code": 429,"message": "Requests are too frequent. For support, please send an email to support@block.cc"}
            // success  -  {"code": 0,"message": "success","data": {...}}
            $code = trim($r)=='' ? 'fail' : (json_decode($r))->code ;
            if($code=='0') { // 성공일때만 저장.
                file_put_contents(dirname(__file__).'/../data/blockcc_ticker_'.$s.$e.'.json', $r);
            }
        }
    }

    // 시가총액 계산용 유통공급량 저장.
    //         "available_supply": "41040405095.0",   // = Circulating Supply (시총 게산용)
    //         "max_supply": "100000000000",        // Max Supply
    if($runable_fn2) {
        $sql = '';
        $ticker = $tradeapi->get_coinmarketcap_ticker($currency->name);
        if($ticker && is_array($ticker) && isset($ticker[0])) {
            $ticker = (object) $ticker[0];
            $ticker->available_supply = (double) str_replace(',','',$ticker->available_supply);
            $ticker->max_supply = (double) str_replace(',','',$ticker->max_supply);
            $sql = "UPDATE js_trade_currency SET circulating_supply='{$tradeapi->escape($ticker->available_supply)}', max_supply='{$tradeapi->escape($ticker->max_supply)}' WHERE symbol='{$tradeapi->escape($s)}' ";
        } else {
            if(!file_exists(__dir__."/../lib/{$s}/{$s}Coind.php")) {
                continue;
            }
            // coinmarketcap 사이트에 미등록 된 스마트코인 계열은 아래 방식으로 계산합니다. max supply는 처음 등록값을 그대로 사용합니다.
            $total_balance = $tradeapi->get_wallet_balance_total ($s); // 전체 잔액 합계
            $detault_balance = $tradeapi->get_wallet_balance ($s, '', ''); // 시스템 디폴드 account의 잔액. 미사용 금액입니다.
            $detault_balance = $detault_balance->confirmed ? $detault_balance->confirmed : 0 ;
            $balance = $total_balance - $detault_balance;
            $balance = $balance < 0 ? 0 : $balance ; // 유통량이 음수면 0으로 변경.
            $sql = "UPDATE js_trade_currency SET circulating_supply='{$tradeapi->escape($balance)}' WHERE symbol='{$tradeapi->escape($s)}' ";
        }
        if($sql) {
            $tradeapi->query($sql);
        }
    }

    // sleep(1);
}

exit('success');
