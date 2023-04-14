<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../comment_class.php';


$js = new Comment($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'reply') {
	ajaxCheckAdmin();
	$js->reply();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheckAdmin();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del')  {
	ajaxCheckAdmin();
	if($js->del()) {
		jsonMsg(1);
	}
	else {
		jsonMsg(0);
	}
}
else {
	loadCheckAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	if($_GET['pg_mode'] == 'form_edit') {
		$interface->layout['contents'] = 'bbs/comment_form_edit.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'form_reply') {
		$interface->layout['contents'] = 'bbs/comment_form_reply.html';
		$js->replyForm();
	}
	else {
		$interface->layout['contents'] = 'bbs/comment_list.html';
		$js->lists();
	}
	$print = 'contents';
	$interface->display($print);
}
$dbcon->close();
?>