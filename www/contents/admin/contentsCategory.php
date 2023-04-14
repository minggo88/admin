<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../contentsCategory_class.php';

function getNavi()
{
	$ret = array(
		''=>'',
		''=>''
	);
	return $ret;
}

$js = new ContentsCategory($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'edit') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'del_multi') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->delMulti();
}
else if($_GET['pg_mode'] == 'save_ranking') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->saveRanking();
}
else if($_GET['pg_mode'] == 'save_sub_ranking') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->saveSubRanking();
}
else if($_GET['pg_mode'] == 'get_bbs_code') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->getBbsCode();
}
else if($_GET['pg_mode'] == 'get_cts_code') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->getCtsCode();
}
else if($_GET['pg_mode'] == 'get_curriculum_code') {
	//ajaxCheck();
	//ajaxCheckUser();
	ajaxCheckAdmin();
	$js->getCurriculumCode();
}
else { 
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;

	//checkUser();
	checkAdmin();

	//$interface->setBasicInterface('user','a4');
	$interface->setBasicInterface('admin','a4');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','contents01');

	/*
	$interface->setPlugIn('jcarousel');
	$interface->setPlugIn('cycle');
	$interface->setPlugIn('tablednd');
	$interface->setPlugIn('lightbox');
	$interface->setPlugIn('tooltip');

	$interface->setPlugIn('');
	$interface->addScript('');
	$interface->addCss('');
	$interface->layout['contents'] = '';
	$interface->layout['js_tpl_left'] = '';
	$interface->layout['js_tpl_main'] = '';
	*/
	$interface->layout['js_tpl_left'] = 'menu.html?contents';
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'contents/contentsCategory_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'contents/contentsCategory_form.html';
		$js->editForm();
	}
	else {
		$interface->setPlugIn('tablednd'); 
		$interface->setPlugIn('dragsort');
		$interface->layout['js_tpl_main'] = 'contents/contentsCategory_list.html';
		$js->lists();
	}
	$print = 'layout';

	$interface->display($print);
}
?>