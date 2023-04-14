<?php
/*--------------------------------------------
Date :
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/
//include_once '../lib/common_user.php';
include_once '../../lib/common_admin.php';
include_once '../contents_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new Contents($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'del_multi') {
	ajaxCheckAdmin();
	$js->delMulti();
}
else if($_GET['pg_mode'] == 'save_ranking') {
	ajaxCheckAdmin();
	$js->saveRanking();
}
else { 
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin','a4');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','contents02');

	$interface->layout['js_tpl_left'] = 'menu.html?main';
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addScript('/template/'.getSiteCode().'/admin/contents/js/contents_form.js');
		$interface->layout['js_tpl_main'] = 'contents/contents_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addScript('/template/'.getSiteCode().'/admin/contents/js/contents_form.js');
		$interface->layout['js_tpl_main'] = 'contents/contents_form.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'view') {
		$interface->layout['js_tpl_main'] = 'contents/contents_view.html';
		$js->view();
	}
	else {
		$interface->setPlugIn('tablednd'); 
		$interface->addScript('/template/'.getSiteCode().'/admin/contents/js/contents_list.js');
		$interface->layout['js_tpl_main'] = 'contents/contents_list.html';
		$js->lists();
	}
	$print = 'layout';

	$interface->display($print);
}