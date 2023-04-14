<?php

// // 모바일 접속시 /trade/btc 로 이동
// if(
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'Android')!==false ||
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry')!==false ||
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')!==false ||
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')!==false ||
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'iPod')!==false ||
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini')!==false ||
// 	strpos($_SERVER['HTTP_USER_AGENT'], 'IEMobile')!==false
// ) {
// 	header('Location: /trade/btc'); exit('<script>window.location.replace("/trade/btc");</script>');
// }
// 관리자 접속시 관리자 url로 이동
if(strpos($_SERVER['HTTP_HOST'], 'admin.')!==false) {
	header('Location: /admin/'); exit('<script>window.location.replace("/admin/");</script>');
}
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
include_once './lib/common_user.php';
include_once './member/member_class.php';

$mem = new Member($tpl);
$mem->json = &$json;
// 조회용 DB 사용.
$dbcon = connect_db_slave();
$mem->dbcon = &$dbcon;

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;

if ($_GET['pg_mode'] == 'load_calendar') {
	loadCheck();
	$interface->layout['contents'] = 'calendar_load.html';
	$js->calendar();
	$print = 'contents';
}
else {

	$interface->setBasicInterface('user','a3');

	// if(BOOL_MOBILE) {
	// 	$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/css/js_header_m.css');
	// 	$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/css/js_main_m.css');
	// } else {
		$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/css/js_header_main.css');
		$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/css/js_main.css');
	// }

	$new_item = $dbcon->query_list_array("SELECT symbol, exchange, name, regdate, icon_url FROM js_trade_currency c WHERE c.active='Y' ORDER BY c.regdate DESC LIMIT 4 "); // 최신 종목 4개
	// var_dump($new_item); exit;
	$tpl->assign('new_item',$new_item);

	$main_symbol = $dbcon->query_list_one("SELECT p.symbol FROM js_trade_price p, js_trade_currency c WHERE p.symbol=c.symbol AND c.active='Y' ORDER BY p.volume DESC, p.price_close DESC LIMIT 5 "); // 실시간 인기 종목 5개
	$main_symbol = is_array($main_symbol) ? $main_symbol : array();
	$tpl->assign('main_symbol',$main_symbol);
	$tpl->assign('main_symbol_json', json_encode($main_symbol));

	$interface->addScript('/template/'.getSiteCode().'/'.$tpl->skin.'/js/kmcsetrade-set.js?v='.__REACT_VERSION__);
	$interface->addScript('/template/'.getSiteCode().'/'.$tpl->skin.'/js/kmcsetrade.2.chunk.js?v='.__REACT_VERSION__);
	$interface->addScript('/template/'.getSiteCode().'/'.$tpl->skin.'/js/kmcsetrade.main.chunk.js?v='.__REACT_VERSION__);

	$interface->layout['js_tpl_header'] = 'js_header_main.html';
	$interface->layout['js_tpl_main'] = 'js_main_.html';

	// $js->mainImg();
	// $js->hitGoodsList();
	$js->loopYear();
	//var_dump(__API_RUNMODE__,$interface); exit;

	// $js->popup();
	$print = 'layout';

}
$interface->display($print);
$dbcon->close();
?>