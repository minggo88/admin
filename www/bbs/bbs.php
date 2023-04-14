<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once './bbs_class.php';
include_once './comment_class.php';

/**************************************
페이지 구분을 위한 함수
페이지 설정을 위한 함수들 이 함수 이외 코드는 절대로 손대리 말것
**************************************/
function getNavi()
{
	global $config;
	$subCode = $config['bbscode'];
	$subLinker = $config['title'];
	$ret = array('<span class="link">'.$subLinker.'</span>'=>'/bbs/bbs.php?pg_mode=list&amp;bbscode='.$_GET['bbscode']);
	return $ret;
}

/**************************************
게시판 프로그램의 시작
**************************************/
if(empty($_REQUEST['bbscode'])) {
	errMsg(Lang::admin_6);
}

$js = new BBS($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

//새글 올리기
if($_POST['pg_mode'] == 'write') {
	if($config_basic['bool_ssl'] > 0) {
		$js->config['write_mode'] = 'post';
	}
	else {
		ajaxCheck();
	}
	if($js->info_bbs['bool_anti_spam']> 0) {
		$js->antiSpam();
	}
	$js->write();
}
//응답글 올리기
else if($_POST['pg_mode'] == 'reply') {
	ajaxCheck();
	if($js->info_bbs['bool_anti_spam']> 0) {
		$js->antiSpam();
	}
	$js->reply();
}
//수정하기
else if($_POST['pg_mode'] == 'edit') {
	ajaxCheck();
	$js->edit();
}
else if($_GET['pg_mode'] == 'del') {
	if(!$js->checkPasswd()) {
		errMsg(Lang::main_bbs1);
	}
	$js->config['write_mode'] = 'link';
	if($js->del($_GET['idx'],1)) {
		alertGo('정상적으로 삭제되었습니다.',base64_decode($_GET['ret_url']));
	}
}
else {

	if(empty($_GET['start'])) {
		$_GET['start'] = 0;
	}

	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());

	//메뉴 구분
	if ($tpl->skin=='shop') {
		if($js->info_bbs['kind_menu'] == 'comm') {
			$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?community';
		}
		else {
			$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?cscenter';
		}
	} else {
		if($js->info_bbs['kind_menu'] == 'comm') {
			//$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?community';
			$interface->layout['js_tpl_left_menu'] = 'left_menu.html';
		}
		else {
			//$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?cscenter';
			$interface->layout['js_tpl_left_menu'] = 'left_menu.html';
		}
	}



	$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/bbs/css/bbs_common.css');

	//폼관련
	if($_GET['pg_mode'] == 'form_new') {
		if(!$js->userRightCheck('write')) {
			alertGo('','bbs_nopermission.php');
		}
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/bbs/css/bbs_form.css');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_form.html';
		$interface->layout['js_tpl_main_sub'] = 'bbs/'.$js->info_bbs['skin'].'/bbs_form_sub.html';
		$js->newForm();
	}
	else if($_GET['pg_mode'] == 'form_edit') {
		if(!$js->checkPasswd()) {
			errMsg(Lang::main_bbs1);
		}
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/bbs/css/bbs_form.css');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_form.html';
		$interface->layout['js_tpl_main_sub'] = 'bbs/'.$js->info_bbs['skin'].'/bbs_form_sub.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'form_reply') {
		if(!$js->userRightCheck('write')) {
			alertGo('','bbs_nopermission.php');
		}
		$interface->setPlugIn('form');
		$interface->setPlugIn('cheditor');
		$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/bbs/css/bbs_form.css');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_form.html';
		$interface->layout['js_tpl_main_sub'] = 'bbs/'.$js->info_bbs['skin'].'/bbs_form_sub.html';
		$js->editForm();
	}
	else if($_GET['pg_mode'] == 'view') {
		//비밀번호체크 및 권한 체크
		if(empty($_GET['passwd'])) {
			if(!$js->userRightCheck('view')) {
				alertGo('','bbs_nopermission.php');
			}
		}
		else {
			if(!$js->checkPasswd()) {
				errMsg(Lang::main_bbs1);
			}
		}
		$interface->setPlugIn('popup');
		$interface->setPlugIn('lightbox');
		$interface->setPlugIn('form');
		$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/bbs/css/bbs_view.css');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_view.html';
		$interface->layout['js_tpl_main_sub'] = 'bbs/'.$js->info_bbs['skin'].'/bbs_view_sub.html';
		$interface->layout['js_bbs_popup'] = 'bbs/bbs_popup.html';
		if($js->info_bbs['bool_comment'] > 0) {
			$js->displayCommentForm();
			$comment = new Comment($tpl);
			$comment->dbcon = &$dbcon;
			$comment->json = &$json;
			$interface->layout['js_tpl_main_comment_form'] = 'bbs/comment_form.html';
			$interface->layout['js_tpl_main_comment_list'] = 'bbs/comment_list.html';
			$comment->lists();
		}
		$js->view();
		if($js->info_bbs['bool_view_list'] > 0) {
			$interface->layout['js_tpl_main_sub_list'] = 'bbs/'.$js->info_bbs['skin'].'/bbs_list_sub.html';
			$js->listView();
		}
		$js->displayButton('write');
		$js->displayButton('del');
	}
	else {//pg_mode = list

		// 조회용 DB 사용.
		$js->dbcon = connect_db_slave();

		//접근권한
		if(!$js->userRightCheck('access')) {
			alertGo('','bbs_nopermission.php');
		}
		$interface->setPlugIn('popup');
		$interface->addCss('/template/'.getSiteCode().'/bbs/css/bbs_list.css');
		$interface->layout['js_tpl_main'] = 'bbs/bbs_list.html';
		$interface->layout['js_tpl_main_sub'] = 'bbs/'.$js->info_bbs['skin'].'/bbs_list_sub.html';
		$interface->layout['js_bbs_popup'] = 'bbs/bbs_popup.html';
		$js->listNotice();
		$js->lists();
		$js->displayButton('write');
	}
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();

?>