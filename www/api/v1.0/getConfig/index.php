<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 확인.
// $tradeapi->checkLogin();
// $userno = $tradeapi->get_login_userno();

// validate parameters

// --------------------------------------------------------------------------- //


// 슬레이브 디비 사용하도록 설정.
$tradeapi->set_db_link('slave');

$query = "select code from js_config_site where domain='".$tradeapi->escape($_SERVER['HTTP_HOST'])."' ";
$site_code = $tradeapi->query_one($query);

$query = "select code, shop_name, shop_ename, shop_url, shop_admin_email, shop_phone, shop_fax, shop_private_clerk, shop_clerk_email, shop_address, site_title, img_logo  from js_config_basic  WHERE code='".$tradeapi->escape($site_code)."' ";
$site_info = $tradeapi->query_fetch_object($query);

$query = "select bank_name, account_no, account_user, concat(bank_name,' / ', account_no,' / ', account_user) bank_full_info  from js_config_account  WHERE coin='KRW' ";
$bank_info = $tradeapi->query_fetch_object($query);
$site_info = array_merge((array) $site_info, (array) $bank_info );

// response
$tradeapi->success($site_info);
