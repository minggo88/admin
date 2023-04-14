<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './banner_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link"></span>'=>'/admin/shopinfo_admin.php'
	);
	return $ret;
}

$js = new Banner($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'write_info') {
	ajaxCheckAdmin();
	$js->writeInfo();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_POST['pg_mode'] == 'edit_info') {
	ajaxCheckAdmin();
	$js->editInfo();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'del_info') {
	ajaxCheckAdmin();
	$js->delInfo();
}
else if($_GET['pg_mode'] == 'check_bool') {
	ajaxCheckAdmin();
	$js->checkBool();
}
else if($_GET['pg_mode'] == 'edit_rank') {
	ajaxCheckAdmin();
	$js->editRank();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());

	if(empty($_GET['bannercode'])) {
		$tpl->assign('cur_page','design02');
	}
	else {
		$tpl->assign('cur_page','banner'.$_GET['bannercode']);
	}
	
	$interface->layout['js_tpl_left'] = 'menu.html?design';
	//배너등록폼
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'design/banner_form.html';
		$js->newForm();
	}
	//배너항목등록폼
	else if($_GET['pg_mode'] == 'form_new_info') {
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'design/bannerInfo_form.html';
		$js->infoNewForm();
	}
	//배너수정폼
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'design/banner_form.html';
		$js->editForm();
	}
	//배너항목수정폼
	else if($_GET['pg_mode'] == 'form_edit_info') {
		$tpl->assign('cur_page','design03');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'design/bannerInfo_form.html';
		$js->infoEditForm();
	}
	//항목리스트
	else if($_GET['pg_mode'] == 'list_info') {
		$tpl->assign('cur_page','design03');
		$interface->layout['js_tpl_main'] = 'design/bannerInfo_list.html';
		$js->infoList();
	}
	//배너리스트
	else {
		$interface->setPlugIn('tooltip');
		$interface->layout['js_tpl_main'] = 'design/banner_list.html';
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>