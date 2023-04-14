<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once $_SERVER["DOCUMENT_ROOT"] . '/lib/common_admin.php';
include_once '../auction_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new auction($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

checkAdmin();
$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());

if ($_POST['pg_mode'] == 'list') {
	ajaxCheckAdmin();
	$_GET['sort_target'] = array('start_date');
	$_GET['sort_method'] = array('desc');
	if ($_REQUEST['order']) {
		$i = 0;
		// Query ErrorNo : 1054<br />Query Error Message : Unknown column 't1.userid' in 'order clause'<br />Query String : select count(*) from js_trade_gwskrw_order t1  WHERE 1  ORDER BY t1.userid asc<br />Source Error File : basic_class.php<br />Source Error Line : 81<br />Error Source File : orderHistoryAdmin.php
		foreach ($_REQUEST['order'] as $order) {
			$_GET['sort_target'][$i] = $_REQUEST['columns'][$order['column']]['data'];
			$_GET['sort_method'][$i] = $order['dir'];
			$i++;
		}
	}
	$_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
	$_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
	$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start'] * 1 : 0;
	$page = $_REQUEST['draw'] ? $_REQUEST['draw'] * 1 : 1;
	$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length'] * 1 : $js->config['loop_scale'];
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length']) == 'ALL' ? false : true;
	$r = $js->reportLists();				//list
	$total = $js->reportLists_cnt();		//list total count

	exit(json_encode(array('data' => $r, 'draw' => $page, 'recordsFiltered' => $total, 'recordsTotal' => $total)));
} else if ($_POST['pg_mode'] == 'delete') {
	ajaxCheckAdmin();
	$r = $js->deleteReport();
	exit(json_encode(array('result'=>!!$r, 'message'=>$js->_message)));
} else if ($_POST['pg_mode'] == 'show-goods') {
	ajaxCheckAdmin();
	$r = $js->showReportGoods();
	exit(json_encode(array('result'=>!!$r, 'message'=>$js->_message)));
} else if ($_POST['pg_mode'] == 'hide-goods') {
	ajaxCheckAdmin();
	$r = $js->hideReportGoods();
	exit(json_encode(array('result'=>!!$r, 'message'=>$js->_message)));
} else if ($_POST['pg_mode'] == 'delete-goods') {
	ajaxCheckAdmin();
	$r = $js->deleteReportGoods();
	exit(json_encode(array('result'=>!!$r, 'message'=>$js->_message)));
} else if ($_GET['pg_mode'] == 'history' || $_GET['pg_mode'] == '') {

	$interface->addCss('/template/' . getSiteCode() . '/admin/css/plugins/dataTables/jquery.dataTables.min.css');
	$interface->addCss('/template/' . getSiteCode() . '/admin/css/plugins/dataTables/responsive.dataTables.min.css');
	$interface->addScript('/template/' . getSiteCode() . '/admin/js/plugins/dataTables/jquery.dataTables.min.js');
	$interface->addScript('/template/' . getSiteCode() . '/admin/js/plugins/dataTables/dataTables.responsive.min.js');
	$interface->addScript('/template/' . getSiteCode() . '/admin/auction/js/auctionReport.js');

	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->layout['js_tpl_main'] = 'auction/auction_reporttop.html';
	$interface->layout['js_tpl_main_sub'] = 'auction/auction_reportlist.html';
}

$print = 'layout';
$interface->display($print);

$dbcon->close();
