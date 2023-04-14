<?php
function miniCart()
{
	global $dbcon;
	global $json;
	global $tpl;

	require_once(ROOT_DIR.'/shop/cart_class.php');

	$minicart = new ShopCart($tpl);
	$minicart->dbcon = &$dbcon;
	$minicart->json = &$json;

	//$js->info_level = getShopConfig('level');


	$minicart->config['bool_navi_page'] = FALSE;

	$tpl->define('contents','shop/shop_cart_mini.html');
	$minicart->lists('mini');
	echo $tpl->fetch('contents');
}

?>