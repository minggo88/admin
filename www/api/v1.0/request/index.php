<?php
include dirname(__file__) . "/../../lib/TradeApi.php";
$tradeapi->set_stop_process(false);


$resps = array();
$reqs = array();

//$_REQUEST['item'] ='[{"method":"getSpotPrice", "params":{"symbol":"all", "token":"bbfo3ei5lt130lps04n2rpbdhc"}},{"method":"getCurrency", "params":{"symbol":"all", "token":"bbfo3ei5lt130lps04n2rpbdhc"}},{"method":"getSymbolList", "params":{"token":"bbfo3ei5lt130lps04n2rpbdhc"}},{"method":"getChartData", "params":{"symbol":"SCC","exchange":"USD","period":"1h","return_type":"TSV","token":"bbfo3ei5lt130lps04n2rpbdhc"}},{"method":"getChartData", "params":{"symbol":"BTC","exchange":"USD","period":"1h","return_type":"TSV","token":"bbfo3ei5lt130lps04n2rpbdhc"}}]';

//$_REQUEST['item'] ='[{"method":"getSpotPrice", "params":{"symbol":"SCC","exchange":"USD", "token":"5ikjvue0gdfleccgl1lb6uvgls"}},{"method":"getChartData", "params":{"symbol":"SCC","exchange":"USD","period":"1h","return_type":"TSV","token":"5ikjvue0gdfleccgl1lb6uvgls"}},{"method":"getQuoteList", "params":{"symbol":"SCC","exchange":"USD", "token":"5ikjvue0gdfleccgl1lb6uvgls"}}]';


if(is_array($_REQUEST['item'])) { // 배열로 전달 받은 경우 그대로 사용.
    $reqs = $_REQUEST['item'];
} else { // 배열이 아니면 형식 확인.
    $req = json_decode($_REQUEST['item']); // 전달 받은 값이 json 형식이여야 합니다. 아니면 파싱 없이 빈 결과 리턴.
    if(is_array($req)) { // 배열이면 
        if(isset($req[0]->method)) { // 첫번째 row에 method가 정의되었는지 확인
            $reqs = $req;
        }
    } else { // 배열이 아니면 
        if(isset($req->method)) { // method가 정의되었는지 확인.
            $reqs = array($req);
        }
    }
}

foreach($reqs as $req) {
    $req = is_string($req) ? json_decode($req) : (object) $req ;
   
    // var_dump($req);exit('1');

    // check method
    if(!isset($req->method) || trim($req->method)=='' || !file_exists(__DIR__.'/../'.$req->method)) {
        continue;
    }
    ob_start();
    try {
        $_REQUEST = (array) $req->params;
        $return_type = $_REQUEST['return_type']!='' ? $_REQUEST['return_type'] : 'json';
		$file_path = __DIR__.'/../'.$req->method.'/index.php';
		$file_path = strpos($req->method, '.php')!==false ? __DIR__.'/../'.$req->method : __DIR__.'/../'.$req->method.'/index.php';
        include($file_path);
        $res = ob_get_contents();
    } catch(Exception $e) {
        $res = $e->getMessage();
    }
    ob_clean();

    $resps[] = array(
        'method'=>$req->method,
        'data'=>$res
    );
}
ob_clean();


$tradeapi->success($resps);