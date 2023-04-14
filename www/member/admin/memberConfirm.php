<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../member_class_new.php';
include_once '../../api/lib/TradeApi.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Member($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;
$js->config['mode'] = 'member';

if ($_POST['pg_mode'] == 'list') {
	ajaxCheckAdmin();
	$_GET['sort_target'] = array('bool_confirm_idimage', 'image_identify_url');
	$_GET['sort_method'] = array('asc', 'desc');
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
	$_GET_ESCAPE['start'] = $js->dbcon->escape($_GET['start']);
	$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
	$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $js->config['loop_scale'];
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;

	// lists 매소드 방식에 맞추기.
	$r = $js->get_confirm_data();
	$rows = count($r);
	$total = $js->total;
	for($i=0;$i<$rows;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
	}
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$rows)));
}
else if($_POST['pg_mode'] == 'confirm') {
	ajaxCheckAdmin();
	$r = $js->confirm($_POST['type'], $_POST['value'], $_POST_ESCAPE['userno']);
	if($r) {
		responseSuccess();
	} else {
		responseFail('인증정보를 변경하지 못했습니다.');
	}
}
else if($_POST['pg_mode'] == 'reject') {
	ajaxCheckAdmin();
	$r = $js->reject($_POST['type'], $_POST['userno'], $_POST_ESCAPE['reject_reason']);
	if($r) {
		responseSuccess();
	} else {
		responseFail('반려 처리를 하지 못했습니다.');
	}
}
else {

	checkAdmin();
	//checkRight('right_member03');exit;
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;

	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','member01');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$tpl->assign('mode',$js->config['mode']);

	$interface->setPlugIn('datatables');
 	//$interface->addScript('/template/'.getSiteCode().'/admin/member/js/member_confirm.js');
	// image popup
	$interface->setPlugIn('magnific-popup');
	// Bootstrap Toggle
	$interface->setPlugIn('bootstrap-toggle');

	$interface->layout['js_tpl_main'] = 'member/member_confirm.html';
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>