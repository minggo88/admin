<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once './bbsSetup_class.php';

function getNavi()
{
	$ret = array(
		'커뮤니티'=>'/bbs/admin/bbsSetup.php',
		'<span class="link">게시판 설정 관리</span>'=>'/bbs/admin/bbsSetup.php'
	);
	return $ret;
}

//프로그램 시작
$js = new BbsSetup($tpl);
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
else if($_POST['pg_mode'] == 'edit_menu') {
	ajaxCheckAdmin();
	$js->editMenu();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'get_skin') {
	ajaxCheckAdmin();
	$js->getSkin();
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
	$tpl->assign('cur_page','comm01');
	$interface->layout['js_tpl_left'] = 'menu.html?community';
	if($_GET['pg_mode']=='form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_setup_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode']=='form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_setup_form.html';
		$js->editForm();
	}
	else if($_GET['pg_mode']=='form_menu') {
		$tpl->assign('cur_page','comm02');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_setup_menu.html';
		$js->lists();
	}
	else {
		$interface->layout['js_tpl_main'] = 'bbs/bbs_setup_list.html';
		$js->lists();
	}
	$print = 'layout';
	$interface->display();
}
$dbcon->close();
?>