<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

$url = setDefault(trim($_REQUEST['url']), '');
$domain = parse_url($url);
// var_dump($domain);
// exit;

$html = '';
if($url) {
    $html = $tradeapi->get_cache($url);
    if(!$html) {
        $html = $tradeapi->remote_get($url);
        if($html) {
            $tradeapi->set_cache($url, $html, 60*60*24);
        }
    }

    // 도매인 미포함 경로 ... 도메인 표함 경로로 변경
    $html = str_replace(' src="/', ' src="//'.$domain['host'].'/', $html);
    $html = str_replace(' href="/', ' href="//'.$domain['host'].'/', $html);
}

echo $html;
