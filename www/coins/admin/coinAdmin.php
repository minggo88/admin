<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../coin_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}
$js = new Coins($tpl, $dbcon);
$js->json = &$json;


$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
if ($_POST['pg_mode'] == 'list') {
	ajaxCheckAdmin();
	$_GET['sort_target'] = array('reg_time');
	$_GET['sort_method'] = array('desc');
	if($_REQUEST['order']) {
		$i=0;
		foreach($_REQUEST['order'] as $order) {
			$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
			$_GET['sort_method'][$i] = $order['dir'];
			$i++;
		}
	}
	$_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
	$_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
	$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
	$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
	$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $js->config['loop_scale'];
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;
	$r = $js->get_currency_list();
	$total = count($r);
	for($i=0;$i<$total;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
} else if ($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$_POST['exchange'] = $_POST['exchange'] ? $_POST['exchange'] : 'KRW';
	$js->write($_POST);
} else if ($_POST['pg_mode'] == 'delete') {
	ajaxCheckAdmin();
	$_POST['exchange'] = $_POST['exchange'] ? $_POST['exchange'] : 'KRW';
	$js->del();
} else if ($_POST['pg_mode'] == 'create_manager') {
	ajaxCheckAdmin();
	$js->create_manager();
} else if ($_POST['pg_mode'] == 'add_krw_to_seller') {
	ajaxCheckAdmin();
	$js->add_krw_to_seller();
} else if ($_POST['pg_mode'] == 'finance_delete') {
	ajaxCheckAdmin();
	$js->finance_delete();
} else if ($_POST['pg_mode'] == 'finance_insert') {
	ajaxCheckAdmin();
	$js->finance_insert();
} else if ($_POST['pg_mode'] == 'finance_update') {
	ajaxCheckAdmin();
	$js->finance_update();
} else if ($_POST['pg_mode'] == 'confirm') {
	ajaxCheckAdmin();
	$js->confirm($_POST['type'], $_POST['value'], $_POST['idx']);
// } else if ($_POST['pg_mode'] == 'get_old_member_balance') {
// 	ajaxCheckAdmin();
// 	$js->get_old_member_balance();
// } else if ($_POST['pg_mode'] == 'get_old_member_airdrop') {
// 	ajaxCheckAdmin();
// 	$js->get_old_member_airdrop();
// } else if ($_POST['pg_mode'] == 'write_old_stock_data') {
// 	ajaxCheckAdmin();
// 	$js->write_old_stock_data();
// } else if ($_POST['pg_mode'] == 'write_old_airdrop_data') {
// 	ajaxCheckAdmin();
// 	$js->write_old_airdrop_data();
} else { // generate page
	checkAdmin();
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->layout['js_tpl_left'] = 'menu.html?main';

	if($_GET['pg_mode']=='write') {
		if($_GET['auction_goods_idx']) {
			// 중복 확인
			if($js->check_duplicate_trade_currency($_GET['auction_goods_idx'])) {
				errMsg('이미 사용중인 상품입니다.');
			}
			// 상품 정보 셋팅
			$add_default_value = $js->assign_auction_goods_info($_GET['auction_goods_idx']);
			$js->assign_default_value($add_default_value);
		}

		$interface->setPlugIn('typehead'); // /template/'.getSiteCode().'/admin/js/plugins/typehead/bootstrap3-typeahead.min.js
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_write.js');
		$interface->layout['js_tpl_main'] = 'coins/coin_write.html';
	} else if($_GET['pg_mode']=='old_member_airdrop') {
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/old_member_airdrop.js');
		$interface->layout['js_tpl_main'] = 'coins/old_member_airdrop.html';
	} else if($_GET['pg_mode']=='old_member_balance') {
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/old_member_balance.js');
		$interface->layout['js_tpl_main'] = 'coins/old_member_balance.html';
	} else if($_GET['pg_mode']=='edit') {
		$js->editForm();

		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_write.js');
		$interface->layout['js_tpl_main'] = 'coins/coin_write.html';
	} else if($_GET['pg_mode']=='finance') {
		$js->editFinanceForm(); 
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_finance.js');
		$interface->layout['js_tpl_main'] = 'coins/coin_finance.html';
	} else {

		$interface->setPlugIn('switchery');
		$interface->setPlugIn('datatables');
		$interface->setPlugIn('bootstrap-toggle');
		
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin.js');
		$interface->layout['js_tpl_main'] = 'coins/coin.html';
	}

	$print = 'layout';
	$interface->display($print);
}
