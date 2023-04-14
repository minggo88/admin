<?php 
/*--------------------------------------------
Date : 2019-08-28
Author : Kenny Han
comment : 코인별 거래소 주문 내역
History : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../orderHistory_class.php';

function getNavi(){ $ret = array(''=>'',''=>''); return $ret;}
$js = new orderHistory($tpl, $dbcon);
// $js->dbcon = &$dbcon;
$js->json = &$json;


checkAdmin();
$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
if ($_POST['pg_mode'] == 'list') {
	ajaxCheckAdmin();
	$_GET['sort_target'] = array('txnid');
	$_GET['sort_method'] = array('desc');
	if($_REQUEST['order']) {
		$i=0;
		// Query ErrorNo : 1054<br />Query Error Message : Unknown column 't1.userid' in 'order clause'<br />Query String : select count(*) from js_trade_gwskrw_order t1  WHERE 1  ORDER BY t1.userid asc<br />Source Error File : basic_class.php<br />Source Error Line : 81<br />Error Source File : orderHistoryAdmin.php
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
	$r = $js->lists();
	$cnt = count($r);
	$total = $js->total;
	for($i=0;$i<$cnt;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
} else { // generate page
	checkAdmin();
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	
	if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d', time() - 60*60*24*30); }
	if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d'); }

	$interface->setPlugIn('datatables');
	// $interface->addScript('/template/'.getSiteCode().'/admin/coins/js/orderHistory.js');
	$interface->layout['js_tpl_main'] = 'coins/orderHistory.html';

	$print = 'layout';
	$interface->display($print);
}
