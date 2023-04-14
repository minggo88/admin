<?php
function miniToday()
{
	global $dbcon;
	global $tpl;
	global $json;
	require_once(ROOT_DIR.'/shop/today_class.php');
	$today = new TodayViewGoods($tpl);
	$today->dbcon = &$dbcon;
	$today->json = &$json;
	$today->config['bool_navi_page'] = FALSE;
	$tpl->define('contents','shop/shop_today_mini.html');
	$today->lists('mini');
	echo $tpl->fetch('contents');
}

?>