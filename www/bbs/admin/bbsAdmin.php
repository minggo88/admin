<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../../lib/common_admin.php';
include_once '../bbs_class.php';
include_once '../comment_class.php';
include_once './bbsSetup_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

if(empty($_GET['start'])) { $_GET['start'] = 0; }


if (empty($_REQUEST['bbscode'])) {
	$query = array();
	$query['table_name'] = 'js_bbs_info';
	$query['tool'] = 'select_one';
	$query['fields'] = 'bbscode';
	$query['where'] = 'order by ranking desc limit 1';
	$_GET['bbscode'] = $_REQUEST['bbscode'] = $dbcon->query($query,__FILE__,__LINE__);
}

$js = new BBS($tpl);
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
else if($_POST['pg_mode'] == 'del_multi') {
	ajaxCheckAdmin();
	$js->delMulti();
}
else if($_POST['pg_mode'] == 'move_multi') {
	ajaxCheckAdmin();
	$js->moveMulti();
}
else if($_GET['pg_mode'] == 'thumbnail') {
	ajaxCheckAdmin();
	$js->makeThumbnail();
}
else if($_GET['pg_mode'] == 'del') {
	$js->config['write_mode'] = 'link';
	if($js->del($_GET['idx'],1)) {
		alertGo('정상적으로 삭제되었습니다.',base64_decode($_GET['ret_url']));
	}
}
else {
	checkAdmin();
	$setup = new BbsSetup($tpl);
	$setup->dbcon = &$dbcon;
	$setup->json = &$json;

	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	//$interface->setBasicInterface('admin');
	//$interface->layout['js_tpl_left'] = 'bbs/menu.html';
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());

	$tpl->assign('cur_page','bbs'.$_GET['bbscode']);

	//$interface->layout['js_tpl_left'] = 'menu.html?community';

	$interface->layout['js_tpl_left'] = 'menu.html?main';
	
	$setup->lists();
	if($_GET['pg_mode'] == 'form_new') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('typehead');
		$tpl->assign('author',$_SESSION['ADMIN_NAME']);
		$interface->addScript('/template/'.getSiteCode().'/admin/bbs/js/bbs_form_new.js');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_form.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_reply') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('typehead');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_form.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->setPlugIn('typehead');
		$interface->addScript('/template/'.getSiteCode().'/admin/bbs/js/bbs_form_edit.js');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_form.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'view') {
		$comment = new Comment($tpl);
		$comment->dbcon = &$dbcon;
		$comment->json = &$json;
		$interface->setPlugIn('form');
		$interface->addScript('/template/'.getSiteCode().'/admin/bbs/js/bbs_view.js');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_view.html';
		$interface->layout['js_tpl_main_sub_list'] = 'bbs/bbs_view_list.html';
		$interface->layout['js_tpl_main_comment_form'] = 'bbs/comment_form.html';
		$interface->layout['js_tpl_main_comment_list'] = 'bbs/comment_list.html';
		$comment->lists();
		$js->view();
		$js->listView();
	}
	else {
		$interface->addScript('/template/'.getSiteCode().'/admin/bbs/js/bbs_list.js');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_list.html';
		$js->listNotice();
		$js->lists();
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>