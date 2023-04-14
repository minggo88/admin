<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_admin.php';
include_once './configAdmin_class.php';

function getNavi()
{
	$ret = array(
		'몰기본관리'=>'/admin/shopinfo.php',
		'<span class="link">관리자기본설정</span>'=>'/admin/shopinfo_admin.php'
	);
	return $ret;
}

$js = new ConfigAdmin($tpl);
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
else if($_POST['pg_mode'] == 'edit_passwd') {
	ajaxCheckAdmin();
	$js->editPasswd();
}
else if($_POST['pg_mode'] == 'save_otp') {
	ajaxCheckAdmin();
	$js->saveOtp();
}
else if($_POST['pg_mode'] == 'get_access_log') {
	ajaxCheckAdmin();
	$js->getAccessLog();
}
else if($_GET['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'get_value') {
	ajaxCheckAdmin();
	$js->getFormValue();
}
else {

	checkAdmin();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());

	if($_GET['pg_mode']=='mypage') { // 기본관리 > 관리자설정 > 비밀번호설정

		$interface->setPlugIn('form');
		$interface->layout['js_tpl_left'] = 'menu.html?main';
		$interface->layout['js_tpl_main'] = 'basic/configMypage_form.html';

	} else { // 기본관리 > 관리자설정 > 비밀번호설정

		include dirname(__file__) . "/../lib/GoogleAuthenticator.php";
		$ga = new PHPGangsta_GoogleAuthenticator();
		$otpinfo = $dbcon->query_unique_object("SELECT otpkey, otpuse FROM js_admin WHERE adminid='".$dbcon->escape($_SESSION['ADMIN_ID'])."' ");
		$interface->tpl->assign('otpuse', $otpinfo->otpuse);
		$otp_secret = $otpinfo->otpkey;
		if(!$otp_secret) {
			$otp_secret = $ga->createSecret();
			$dbcon->query_unique_value("UPDATE js_admin SET otpkey='{$dbcon->escape($otp_secret)}' WHERE adminid='".$dbcon->escape($_SESSION['ADMIN_ID'])."' ");
		}
		$qrCodeUrl = $ga->getQRCodeGoogleUrl($_SERVER['HTTP_HOST'], $otp_secret);
		$interface->tpl->assign('qrCodeUrl', $qrCodeUrl);
		$interface->setPlugIn('switchery');
		
		$interface->addScript('/template/'.getSiteCode().'/admin/basic/js/configAdmin.js');



		$tpl->assign('cur_page','config02');
		$interface->setPlugIn('form');
		$interface->layout['js_tpl_left'] = 'menu.html?main';
		$interface->layout['js_tpl_main'] = 'basic/configAdmin_form.html';
		$js->lists();
	}

	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>