<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../ledger_class.php';
include_once '../../api/lib/TradeApi.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Ledger($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	// $js->write();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	// $js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'multi_del') {
	ajaxCheckAdmin();
	$js->multiDel();
}
else if($_POST['pg_mode'] == 'get_bc_balance') {
	ajaxCheckAdmin();
	$b = $js->getBalance('BC', $_POST['walletno']);
	exit(json_encode(array('balance'=>$b)));
}
else if($_POST['pg_mode'] == 'wallet_list' || $_POST['pg_mode'] == 'transaction_list' || $_POST['pg_mode'] == 'bc_transaction_list') {
	ajaxCheckAdmin();
	$_GET['sort_target'] = $_REQUEST['order'][0]['column'] ? $_REQUEST['columns'][$_REQUEST['order'][0]['column']]['data']  : ($_POST['pg_mode'] == 'transaction_list' ? 'regdate':'walletno');
	$_GET['sort_method'] = $_REQUEST['order'][0]['dir'] ? $_REQUEST['order'][0]['dir'] : 'desc';
	if($_POST['pg_mode'] == 'transaction_list') {
		$_GET['sort_target'] = array($_GET['sort_target']);
		$_GET['sort_method'] = array($_GET['sort_method']);
	}
	$_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
	$_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
	$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
	$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
	$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $js->config['loop_scale'];
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;
	if($_POST['pg_mode'] == 'bc_transaction_list') {
		$r = $js->bc_transaction_list();
		$total = count($r);
	} else {
		$r = $js->lists();
		$cnt = count($r);
		$total = $js->total;
		for($i=0;$i<$cnt;$i++) {
			$r[$i] = (object) $r[$i];
			$r[$i]->no = $total - $i;
		}
		exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;

	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','member01');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$tpl->assign('mode',$js->config['mode']);

	$interface->setPlugIn('datatables-1.10.19');

	if($_GET['pg_mode'] == 'transaction') {
		//  checkRight('right_wallet02');
		// $interface->setPlugIn('form');
		// $interface->addCss('/template/'.getSiteCode().'/admin/wallet/css/transaction.css');
		$interface->layout['js_tpl_main'] = 'wallet/external_transaction.html';
		$interface->addScript('/template/'.getSiteCode().'/admin/wallet/js/external_transaction.js');
	//} else {
		// checkRight('right_bdswallet02');
		//$interface->layout['js_tpl_main'] = 'wallet/ledger_list.html';
		//$interface->addScript('/template/'.getSiteCode().'/admin/wallet/js/ledger_list.js');
		// $js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();