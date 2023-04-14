<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../member_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Member($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;
$js->config['table_name'] = 'js_member_withdraw';
$js->config['mode'] = 'withdraw';

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'getwithdraw') {
	ajaxCheckAdmin();
	$js->loopGetwithdraw();
}
else if($_POST['pg_mode'] == 'member_email') {
	ajaxCheckAdmin();
	$js->memberMail();
}
else if($_POST['pg_mode'] == 'member_sms') {
	ajaxCheckAdmin();
	$js->memberSms();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'multi_del') {
	ajaxCheckAdmin();
	$js->multiDel();
}
else if($_GET['pg_mode'] == 'rollback') {
	ajaxCheckAdmin();
	$js->memberRollback();
}
else { 
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','member01');
	//$interface->addCss('/template/admin/member/css/member.css');
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$tpl->assign('mode',$js->config['mode']);

	if($_GET['pg_mode'] == 'view') {
		//$interface->setPlugIn('cleditor');
		$interface->setPlugIn('form');
		$interface->setPlugIn('popup');
		$interface->layout['js_tpl_main'] = 'member/withdraw-view-layout.html';
		$interface->layout['js_tpl_main_sub'] = 'member/withdraw-view.html';
		$js->view($js->config['mode']);
	}
	else {
		$interface->setPlugIn('form');
		$interface->setPlugIn('dragsort');

		if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-d', time() - 60*60*24*365); }
		if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d'); }
		
		$interface->layout['js_tpl_main'] = 'member/withdraw.html';
		$interface->layout['js_tpl_main_sub'] = 'member/withdraw_list.html';
		$js->loopLevel();
		$js->lists($js->config['mode']);
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>