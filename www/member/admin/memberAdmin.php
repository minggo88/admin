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
$js->config['mode'] = 'member';

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'member_email') {
	ajaxCheckAdmin();
	$js->memberMail();
}
else if($_POST['pg_mode'] == 'member_sms') {
	ajaxCheckAdmin();
	// $js->memberSms();
}
else if($_POST['pg_mode'] == 'change_level_multi') {
	ajaxCheckAdmin();
	// $js->changeLevelMulti();
}
else if($_POST['pg_mode'] == 'xls_insert') {
	//ajaxCheckAdmin();
	$js->memberXlsInsert();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'multi_del') {
	ajaxCheckAdmin();
	$js->multiDel();
}
else if ($_POST['pg_mode'] == 'passwd_default') {
	ajaxCheckAdmin();
	$js->passwdDefault();
}
else if($_POST['pg_mode'] == 'getcustomers') {
	ajaxCheckAdmin();
	$js->loopGetcustomers();
}
else if($_POST['pg_mode'] == 'getcustomersbalance') {
    ajaxCheckAdmin();
    $js->loopGetcustomersbalance();
}
else if($_POST['pg_mode'] == 'getemailhistory') {
	ajaxCheckAdmin();
	// $js->loopGetemailhostory('json');
}
else if($_GET['pg_mode'] == 'confirm_email') {
	ajaxCheckAdmin();
	// $js->confirmEmail();
}
else if($_GET['pg_mode'] == 'confirm_realname') {
	ajaxCheckAdmin();
	$js->confirmRealname();
}
else {

	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;

	if($_GET['pg_mode'] == 'member_xls') {
		header( "Content-type: application/vnd.ms-excel; charset=euc-kr" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
		header( "Pragma: public" );
		header( "Content-Disposition: attachment; filename=member.xls" );
		$interface->layout['contents'] = 'member/member_xls.html';
		$js->config['bool_navi_page'] = false;
		$js->lists();
		$print = 'contents';
	}
	else {
		$interface->setBasicInterface('admin');
		$interface->addNavi(getNavi());
		$tpl->assign('cur_page','member01');
		//$interface->addCss('/template/admin/member/css/member.css');
		$interface->layout['js_tpl_left'] = 'menu.html?main';
		$tpl->assign('mode',$js->config['mode']);

		if($_GET['pg_mode'] == 'form_edit') {
			$interface->setPlugIn('popup');
			$interface->setPlugIn('form');
			$interface->layout['js_tpl_main'] = 'member/member_form.html';
			$js->loopLevel();
			$js->editForm();
		}
		else if($_GET['pg_mode'] == 'view') {
			//$interface->setPlugIn('cleditor');
			$interface->setPlugIn('form');
			$interface->setPlugIn('popup');
			$interface->layout['js_tpl_main'] = 'member/customer-view-layout.html';
			$interface->layout['js_tpl_main_sub'] = 'member/customer-view.html';
			$js->loopGetbuysmartcoinhistories('tpl');
			$js->view();
		}
		else if($_GET['pg_mode'] == 'customers' || $_GET['pg_mode'] == 'customers_withdraw') {
			
			$interface->setPlugIn('datatables-1.10.19');

			$js->tpl->assign('currency_list', $js->get_currency());

			if(trim($_GET['start_date'])=='') { $_GET['start_date'] = ''; }
			if(trim($_GET['end_date'])=='') { $_GET['end_date'] = ''; }

			$interface->layout['js_tpl_main'] = 'member/customers.html';
			$interface->layout['js_tpl_main_sub'] = 'member/customer-lists.html';
		}
        else if ($_GET['pg_mode'] == 'balance') {

            $interface->setPlugIn('datatables-1.10.19');

            $interface->layout['js_tpl_main'] = 'member/customers-balance.html';
            $interface->layout['js_tpl_main_sub'] = 'member/customer-balance-lists.html';
        }
		else {

			$interface->setPlugIn('datatables-1.10.19');

			$interface->setPlugIn('form');
			$interface->setPlugIn('popup');
			$interface->setPlugIn('dragsort');
			$interface->layout['js_tpl_main'] = 'member/member-lists.html';
			$js->loopLevel();
			$js->lists();
		}
		$print = 'layout';
	}
	$interface->display($print);
}
$dbcon->close();
?>