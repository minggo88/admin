<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './configBasic_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">기본정보설정</span>'=>'/admin/shopinfo_basic.php'
	);
	return $ret;
}

$js = new ConfigBasic($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('form');
	if($_GET['pg_mode'] == 'form_logo') {
		$tpl->assign('cur_page','design01');
		$interface->layout['js_tpl_left'] = 'menu.html?design';
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'basic/configLogo_form.html';
		$js->viewForm();
	}
	else if($_GET['pg_mode'] == 'form_map') {
		$tpl->assign('cur_page','design02');
		$interface->layout['js_tpl_left'] = 'menu.html?design';
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'basic/configMap_form.html';
		$js->viewForm();
	}
	else if($_GET['pg_mode'] == 'form_ssl') {
		$tpl->assign('cur_page','config06');
		$interface->layout['js_tpl_left'] = 'menu.html?config';
		$interface->setPlugIn('cheditor');
		$interface->layout['js_tpl_main'] = 'basic/configSSL_form.html';
		$js->viewForm();
	}
	else {//$_GET['pg_mode'] == 'form_basic02'
		$interface->setPlugIn('cheditor');
		$tpl->assign('cur_page','config01');
		$interface->layout['js_tpl_left'] = 'menu.html?config';
		$interface->layout['js_tpl_main'] = 'basic/configBasic_form.html';
		$js->viewForm();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>