<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../faq_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Faq($tpl);
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
else if($_GET['pg_mode'] == 'write_code') {
	ajaxCheckAdmin();
	$js->writeCode();
}
else if($_GET['pg_mode'] == 'edit_code') {
	ajaxCheckAdmin();
	$js->editCode();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'del_multi') {
	ajaxCheckAdmin();
	$js->delMulti();
}
else if($_GET['pg_mode'] == 'del_code') {
	ajaxCheckAdmin();
	$js->delCode();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','comm04');
	//css설정
	$interface->layout['js_tpl_left'] = 'menu.html?main';
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addScript('/template/'.getSiteCode().'/admin/cscenter/js/faq_form_new.js');
		$interface->layout['js_tpl_main'] = 'cscenter/faq_form.html';
		$js->newForm();
		$js->listCode();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addScript('/template/'.getSiteCode().'/admin/cscenter/js/faq_form_edit.js');
		$interface->layout['js_tpl_main'] = 'cscenter/faq_form.html';
		$js->editForm();
		$js->listCode();
	}
	else {
		$interface->addScript('/template/'.getSiteCode().'/admin/cscenter/js/faq_list.js');
		$interface->layout['js_tpl_main'] = 'cscenter/faq_list.html';
		$js->lists();
		$js->listCode();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>