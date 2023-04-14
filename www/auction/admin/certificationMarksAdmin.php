<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once $_SERVER["DOCUMENT_ROOT"] . '/lib/common_admin.php';
include_once '../certification_marks_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new certification_marks($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

checkAdmin();
$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
$interface->setBasicInterface('admin');
$interface->addNavi(getNavi());

if ($_POST['pg_mode'] ) {

	switch($_POST['pg_mode']) {
		case 'list' :
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
			$js->config['loop_scale'] = $_REQUT['length'] ? $_REQUEST['length'] * 1 : $js->config['loop_scale'];
			$js->config['bool_navi_page'] = strtoupper($_REQUEST['length']) == 'ALL' ? false : true;
			$r = $js->lists();				//list
			$total = $js->lists_cnt();		//list total count
		
			exit(json_encode(array('data' => $r, 'draw' => $page, 'recordsFiltered' => $total, 'recordsTotal' => $total)));
			break;
		case 'delete' :
			ajaxCheckAdmin();
			$js->delete();
			break;
		case 'write' :
			ajaxCheckAdmin();
			$js->write();
			break;
		case 'edit' :
			ajaxCheckAdmin();
			$js->edit();
			break;
			
	}

} else {
	
	switch($_GET['pg_mode']) {

		case 'edit' :

			$js->editForm();
			// break 없습니다. 아래 write부분도 실행합니다.

		case 'write' :

			if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d'); }
			if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d', time() + 60*60*24*30); }

			$interface->addScript('/template/'.getSiteCode().'/admin/auction/js/certificationMarksWrite.js');
			$interface->layout['js_tpl_main'] = 'auction/certification_marks_form.html';

			break;

		default:

			$interface->setPlugIn('datatables');
			$interface->addScript('/template/' . getSiteCode() . '/admin/auction/js/certificationMarksList.js');
			$interface->layout['js_tpl_main'] = 'auction/certification_marks_list.html';

	}

	$interface->layout['js_tpl_left'] = 'menu.html?main';

	$print = 'layout';
	$interface->display($print);
}

$dbcon->close();
