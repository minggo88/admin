<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment :
History :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../../api/lib/TradeApi.php';
include_once '../inout_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}
$js = new Coins($tpl, $dbcon);
// $js->dbcon = &$dbcon;
$js->json = &$json;


checkAdmin();
$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
if ($_POST['pg_mode'] == 'list') {
	ajaxCheckAdmin();

	// 조회용 DB 사용.
	$dbcon = connect_db_slave();
	$js->dbcon = $dbcon;

	$_GET['sort_target'] = array('regdate');
	$_GET['sort_method'] = array('desc');
	if($_REQUEST['order']) {
		$i=0;
		foreach($_REQUEST['order'] as $order) {
			$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
			$_GET['sort_method'][$i] = $order['dir'];
			$i++;
		}
	}
    if($_POST['name']) { $_GET['name'] = $_POST['name']; $GLOBALS['_GET_ESCAPE']['name'] = $GLOBALS['_POST_ESCAPE']['name']; }

    $_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
	$_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
	$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
	$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
	$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $js->config['loop_scale'];
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;
	$r = $js->lists();
	$cnt = count($r);
	$total = $js->total;
	for($i=0;$i<$cnt;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
		$r[$i]->btn_permit = $_SESSION['ADMIN_DESIGN'];
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}else if ($_POST['pg_mode'] == 'withdraw') {
	ajaxCheckAdmin();
	$js->withdraw();
}else if ($_POST['pg_mode'] == 'dividend') {
	ajaxCheckAdmin();
	$js->dividend();
}else if ($_POST['pg_mode'] == 'airdrop') {
	ajaxCheckAdmin();
	$js->airdrop();
}else if ($_POST['pg_mode'] == 'cancel') {
	ajaxCheckAdmin();
	$js->cancel();
}else if ($_POST['pg_mode'] == 'deposit') {
	ajaxCheckAdmin();
	$js->deposit();
} else { // generate page
	checkAdmin();

	// 조회용 DB 사용.
	$dbcon = connect_db_slave();
	$js->dbcon = $dbcon;

	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->layout['js_tpl_main'] = 'coins/inout.html';

	if($_GET['pg_mode']=='write') {
		// $js->assign_default_value();
		// $interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_write.js');
		// $interface->layout['js_tpl_main'] = 'coins/coin_write.html';
	} else if($_GET['pg_mode']=='edit') {
		// $js->editForm();
		// $interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_write.js');
		// $interface->layout['js_tpl_main'] = 'coins/coin_write.html';
	} else if($_GET['pg_mode']=='dividend') {
		$js->editForm();
		$interface->addScript('//unpkg.com/canvas-datagrid/dist/canvas-datagrid.js');
		$interface->addScript('//unpkg.com/xlsx/dist/xlsx.full.min.js');
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/dividend.js');
		$interface->layout['js_tpl_main'] = 'coins/dividend.html';
	} else if($_GET['pg_mode']=='airdrop') { 
		// 스톡옵션 (airdrop) 
		$js->editForm();
		$interface->addScript('//unpkg.com/canvas-datagrid/dist/canvas-datagrid.js');
		$interface->addScript('//unpkg.com/xlsx/dist/xlsx.full.min.js');
		$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/airdrop.js');
		$interface->layout['js_tpl_main'] = 'coins/airdrop.html';

	} else {
		
		$interface->setPlugIn('datatables');
		$currency_info = $js->dbcon->query_unique_array("SELECT * FROM js_trade_currency where symbol='{$js->dbcon->escape($_GET['symbol'])}'",__FILE__,__LINE__);
		$js->tpl->assign('currency_info', $currency_info);
	
//		if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d', time() - 60*60*24*30); }
//		if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d'); }

		if($_GET['symbol']=='USD' || $_GET['symbol']=='KRW'){
			if($_GET['txn_type']=='W'){
				$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/out_krw.js');
				$interface->layout['js_tpl_main'] = 'coins/out_krw.html';
			}else{
				$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/in_krw.js');
				$interface->layout['js_tpl_main'] = 'coins/in_krw.html';
			}
		}else{
			$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/inout.js');
			$interface->layout['js_tpl_main'] = 'coins/inout.html';
		}
	}

	$print = 'layout';
	$interface->display($print);
}
