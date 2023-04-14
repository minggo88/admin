<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment :
History :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../tradehistory_class.php';

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
	$_GET['sort_target'] = array('txnid');
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
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}
else if ($_POST['pg_mode'] == 'withdraw') {
	ajaxCheckAdmin();
	$js->withdraw();
}
else if ($_POST['pg_mode'] == 'deposit') {
	ajaxCheckAdmin();
	$js->deposit();

} else { // generate page
	checkAdmin();
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('form');
	$interface->setPlugIn('popup');
	$interface->setPlugIn('dragsort');

	$interface->setPlugIn('switchery');
	$interface->setPlugIn('datatables');
	$interface->setPlugIn('bootstrap-toggle');

//	if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d', time() - 60*60*24*30); }
//	if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d'); }

	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->layout['js_tpl_main'] = 'coins/tradehistory.html';
	$print = 'layout';
	$interface->display($print);
}
