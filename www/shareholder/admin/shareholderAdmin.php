<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../shareholder_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Coins($tpl, $dbcon);
// $js->dbcon = &$dbcon;
$js->json = &$json;
$js->config['mode'] = 'member';


checkAdmin();
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
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' || $_REQUEST['length']=='-1' ? false : true;
	$r = $js->lists();
	$cnt = count($r);
	$total = $js->total;
	for($i=0;$i<$cnt;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}
else {
	checkAdmin();
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->addScript('/template/'.getSiteCode().'/admin/shareholder/js/list.js');
	$interface->layout['js_tpl_main'] = 'shareholder/list.html';

	$print = 'layout';
	$interface->display($print);
}