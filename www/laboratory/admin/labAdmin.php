<?php
/*--------------------------------------------
Date :
Author : FirstGleam - http://www.firstgleam.com
comment :
History :
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../lab_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Lab($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_POST['pg_mode'] == 'joinnsave') {
	ajaxCheckAdmin();
	$js->loopJoinnsave('json');
}
else if($_POST['pg_mode'] == 'houravg') {
	ajaxCheckAdmin();
	$js->loopHouravg('json');
}
else if($_POST['pg_mode'] == 'newhouravg') {
	ajaxCheckAdmin();
	$js->loopNewhouravg('json');
}
else if($_POST['pg_mode'] == 'weekavg') {
	ajaxCheckAdmin();
	$js->loopWeekavg('json');
}
else if($_POST['pg_mode'] == 'newweekavg') {
	ajaxCheckAdmin();
	$js->loopNewweekavg('json');
}
else if($_POST['pg_mode'] == 'locationavg') {
	ajaxCheckAdmin();
	$js->loopLocationavg('json');
}
else if($_POST['pg_mode'] == 'newlocationavg') {
	ajaxCheckAdmin();
	$js->loopNewlocationavg('json');
}
else if($_POST['pg_mode'] == 'ageavg') {
	ajaxCheckAdmin();
	$js->loopAgeavg('json');
}
else if($_POST['pg_mode'] == 'newageavg') {
	ajaxCheckAdmin();
	$js->loopNewageavg('json');
}
else if($_POST['pg_mode'] == 'jobavg') {
	ajaxCheckAdmin();
	$js->loopJobavg('json');
}
else if($_POST['pg_mode'] == 'newjobavg') {
	ajaxCheckAdmin();
	$js->loopNewjobavg('json');
}
else if($_POST['pg_mode'] == 'sexavg') {
	ajaxCheckAdmin();
	$js->loopSexavg('json');
}
else if($_POST['pg_mode'] == 'newsexavg') {
	ajaxCheckAdmin();
	$js->loopNewsexavg('json');
}
else if($_POST['pg_mode'] == 'sellable_sections') {
	ajaxCheckAdmin();

	// 조회용 DB 사용.
	$dbcon = connect_db_slave();
	$js->dbcon = $dbcon;

	$js->loopSellableSections('json');
}elseif($_POST['pg_mode'] == 'loopStateNowData') {//현재 회원별 현황
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
	$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
	$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
	$js->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $js->config['loop_scale'];
	$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;
	$r = $js->loopNowState();
	//$cnt = count($r);
	$total = $js->total;
	/*
	for($i=0;$i<$cnt;$i++) {
		$r[$i] = (object) $r[$i];
		$r[$i]->no = $total - $i;
	}*/
	exit(json_encode(array('data'=>$r,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}
else if($_POST['pg_mode'] == 'inquired_sections') {
	ajaxCheckAdmin();

	// 조회용 DB 사용.
	$dbcon = connect_db_slave();
	$js->dbcon = $dbcon;

	$js->loopInquiredSections('json');
}
else if($_POST['pg_mode'] == 'stat_income') {
	ajaxCheckAdmin();

	// 조회용 DB 사용.
	$dbcon = connect_db_slave();
	$js->dbcon = $dbcon;

	$r = $js->loopStatIncome();
	$total = count($r);
	exit(json_encode(array('data'=>$r,'draw'=>1,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
}
else {
	checkAdmin();

	// 조회용 DB 사용.
	$dbcon = connect_db_slave();
	$js->dbcon = $dbcon;

	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;

	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','config09');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->setPlugIn('popup');
	$interface->setPlugIn('form');
	$interface->setPlugIn('datatables');
	$interface->layout['js_tpl_main'] = 'laboratory/'.$_GET['pg_mode'].'.html';
	if($_GET['pg_mode']=='tpl_view'){
		$js->loopSellableSections();
		$js->loopInquiredSections();
	} elseif($_GET['pg_mode']=='kpi_total'){
		//$js->loopTotal();
		// $js->tradeState();
		$js->loopTradeState();
	} elseif($_GET['pg_mode']=='stat_total'){
		$js->loopStatTotal();
	} elseif($_GET['pg_mode']=='kpi_joinnsave'){
		$js->loopJoinnsave();
	} elseif($_GET['pg_mode']=='stat_published'){
		$js->loopHouravg();
		$js->loopWeekavg();
	} elseif($_GET['pg_mode']=='new_stat_published'){
		$js->loopNewhouravg();
		$js->loopNewweekavg();
	} elseif($_GET['pg_mode']=='analyze_published'){
		$js->loopLocationavg();
		$js->loopAgeavg();
		$js->loopJobavg();
		$js->loopSexavg();
	} elseif($_GET['pg_mode']=='stat_income'){	//현재 회원현황
		// <link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
		$interface->addCss('/template/admin/admin/css/plugins/datapicker/datepicker3.css');
		$interface->addCss('/template/admin/admin/css/plugins/daterangepicker/daterangepicker-bs3.css');
		$interface->setPlugIn('datatables-1.10.19');
		$interface->addScript('/template/admin/admin/laboratory/js/stat_income.js');

		// $tpl->assign('gen_time', $js->genDataTotalNowState());// 데이터 생성(10분 캐시)
		$js->setTradeCurrency(); // 화폐종류 추가
		// $js->statIncome(); //
		$_GET['type'] = $_GET['type'] ? $_GET['type'] : 'daily';
	
		if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d', time() - 60*60*24*30); }
		if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d'); }

	} elseif($_GET['pg_mode']=='stat_now'){	//현재 회원현황
		$interface->setPlugIn('datatables-1.10.19');
		$interface->addScript('/template/admin/admin/laboratory/js/stat_now.js');
		// $tpl->assign('gen_time', $js->genDataTotalNowState());// 데이터 생성(10분 캐시)
		$js->loopTotalNowState(); //
	} elseif($_GET['pg_mode']=='new_analyze_published'){
		$js->loopNewlocationavg();
		$js->loopNewageavg();
		$js->loopNewjobavg();
		$js->loopNewsexavg();
	}
	$print = 'layout';

	$interface->display($print);
}
?>