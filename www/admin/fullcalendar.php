<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './fullcalendar_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">일정관리</span>'=>'/admin/shopinfo_admin.php'
	);
	return $ret;
}

$js = new FullCalendar($tpl);
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
else if($_GET['pg_mode'] == 'edit_drag') {
	ajaxCheckAdmin();
	$js->dragEdit();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else {
	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('form');
	$interface->setPlugIn('fullcalendar');
	$interface->setPlugIn('popup');
	$interface->layout['js_tpl_left'] = 'menu.html?config';
	$interface->layout['js_tpl_main'] = 'basic/fullcalendar.html';
	$js->lists();
	$print = 'layout';
	$interface->display($print);
}

$dbcon->close();
?>