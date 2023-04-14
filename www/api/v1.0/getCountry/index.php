<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// validate parameters

// --------------------------------------------------------------------------- //

// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

// pot 생성용 
__('Korea (the Republic of)');
__('China');
__('Hong Kong');
__('Macau');
__('Singapore');
__('Malaysia');
__('Indonesia');
__('Mongolia');
__('Thailand');
__('Australia');
__('United States');
__('United Kingdom');
__('Canada');
__('Japan');

$sec = 0; //3600;
$lang = $tradeapi->get_i18n_lang();
// var_dump($lang, $_SESSION, $_COOKIE); exit;

$tradeapi->set_cache_dir($tradeapi->cache_dir.'/getCountry/'.$lang);
$cache_id = 'getCountry';
$c = $tradeapi->get_cache($cache_id);
if($c=='') {
    $c = $tradeapi->get_country();
    for($i=0; $i<count($c); $i++) {
        $c[$i]->name = __($c[$i]->name);
    }
    $c = $tradeapi->set_cache($cache_id, $c, $sec);
}
$tradeapi->clear_old_file($sec);

$tradeapi->success($c);
