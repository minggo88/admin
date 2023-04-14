<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../member_class.php';

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
	$r = $js->lists();
	$cnt = count($r);
	$total = $js->total;
	for($i=0;$i<$cnt;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}
else if ($_POST['pg_mode'] == 'lock') {
	ajaxCheckAdmin();
	$js->lock();
}
else if ($_POST['pg_mode'] == 'unlock') {
	ajaxCheckAdmin();
	$js->unlock();
} else { // generate page
	checkAdmin();
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->layout['js_tpl_main'] = 'coins/member.html';


	if($_GET['pg_mode']=='write') {
		// $js->assign_default_value();
		// $interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_write.js');
		// $interface->layout['js_tpl_main'] = 'coins/coin_write.html';
	} else if($_GET['pg_mode']=='edit') {
		// $js->editForm();
		// $interface->addScript('/template/'.getSiteCode().'/admin/coins/js/coin_write.js');
		// $interface->layout['js_tpl_main'] = 'coins/coin_write.html';
	} else {
		$interface->setPlugIn('datatables');
		// $interface->addScript('/template/'.getSiteCode().'/admin/coins/js/member.js');
	$interface->addScript('/template/'.getSiteCode().'/admin/coins/js/member.js');
		$interface->layout['js_tpl_main'] = 'coins/member.html';
	}

	$print = 'layout';
	$interface->display($print);
}
