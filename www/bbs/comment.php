<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once './comment_class.php';

$js = new Comment($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;


if($_POST['pg_mode'] == 'write') {
	ajaxCheck();
	$js->write();
}
else if($_POST['pg_mode'] == 'reply') {
	ajaxCheck();
	$js->reply();
}
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheck();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	if(!$js->checkPasswd()) {
		errMsg(Lang::main_bbs1);
	}
	$js->config['write_mode'] = 'link';
	if($js->del()) {
		alertGo('정상적으로 삭제되었습니다.',base64_decode($_GET['ret_url']));
	}
}
else {
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/bbs/css/bbs.css');

	if($_GET['pg_mode'] == 'form_new') {
		$interface->layout['contents'] = 'bbs/comment_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		if(!$js->checkPasswd()) {
			$interface->layout['contents'] = 'bbs/comment_view.html';
		}
		else {
			$interface->layout['contents'] = 'bbs/comment_form_edit.html';
			$js->editForm();
		}
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