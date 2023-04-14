<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once '../api/lib/TradeApi.php';
include_once 'member_class.php';

// exit('1');
function getNavi()
{
$ret = array();
	return $ret;
}

$js = new Member($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'edit') {
	if($config_basic['bool_ssl'] > 0) {
		checkUser();
	} else {
		ajaxCheckUser();
	}
	$js->edit();
}
elseif($_POST['pg_mode'] == 'edit_photo') {
    if($config_basic['bool_ssl'] > 0) {
        checkUser();
    } else {
        ajaxCheckUser();
    }
    $js->edit_photo();
}
elseif($_POST['pg_mode'] == 'edit_pin') {
	if($config_basic['bool_ssl'] > 0) {
		checkUser();
	} else {
		ajaxCheckUser();
	}
	$js->edit_pin();
}
elseif($_POST['pg_mode'] == 'certification') {
	if($config_basic['bool_ssl'] > 0) {
		checkUser();
	} else {
		ajaxCheckUser();
	}
	//$js->edit_pin();
}
elseif($_POST['pg_mode'] == 'save_bank_info') {
	if($config_basic['bool_ssl'] > 0) {
		checkUser();
	} else {
		ajaxCheckUser();
	}
	$js->saveBankInfo($_POST['bank_name'], $_POST['bank_account'], $_POST['bank_owner'], $_POST['image_bank_url']);
}
elseif($_POST['pg_mode'] == 'send_confirm_number') {
	if($config_basic['bool_ssl'] > 0) {
		checkUser();
	} else {
		ajaxCheckUser();
	}
	$js->send_confirm_number($_POST['phone_number'], $_POST['phone_country_code']);
}
elseif($_POST['pg_mode'] == 'confirm_number') {
	if($config_basic['bool_ssl'] > 0) {
		checkUser();
	} else {
		ajaxCheckUser();
	}
	$js->confirm_number($_POST['confirm_number']);
}
else {
	checkUser();
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('popup');
	$interface->setPlugIn('form');

	if($_GET['pg_mode'] == 'edit_pin') {
		$interface->layout['js_tpl_main'] = 'member/edit_pin_form.html';
	} else if($_GET['pg_mode'] == 'certification') {
		$interface->addScript('/template/'.getSiteCode().'/script/Javascript-Load-Image/load-image.all.min.js');
        $interface->layout['js_tpl_main'] = 'member/certification_form.html';
    } else if($_GET['pg_mode'] == 'certify_form') {
        $interface->layout['js_tpl_main'] = 'member/certification_form_new.html';
    } else if($_GET['pg_mode'] == 'otp_form') {
        $interface->layout['js_tpl_main'] = 'member/otp_form.html';
    } else {

		$sitecode = "";				// NICE로부터 부여받은 사이트 코드
		$sitepasswd = "";			// NICE로부터 부여받은 사이트 패스워드
		$cb_encode_path = dirname(__file__)."/../lib/CheckPlusSafe_PHP/64bit/CPClient";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
		$authtype = "M";      	// 없으면 기본 선택화면, X: 공인인증서, M: 핸드폰, C: 카드
		$popgubun 	= "N";		//Y : 취소버튼 있음 / N : 취소버튼 없음
		$customize 	= "";			//없으면 기본 웹페이지 / Mobile : 모바일페이지
		$reqseq = "REQ_".$_SESSION['USER_ID'];     // 요청 번호, 이는 성공/실패후에 같은 값으로 되돌려주게 되므로
		// 업체에서 적절하게 변경하여 쓰거나, 아래와 같이 생성한다.
		// $reqseq = `$cb_encode_path SEQ $sitecode`;
		// CheckPlus(본인인증) 처리 후, 결과 데이타를 리턴 받기위해 다음예제와 같이 http부터 입력합니다.
		$returnurl = "https://www.smcc.io/lib/CheckPlusSafe_PHP/checkplus_success_after_realnamecheck.php";	// 성공시 이동될 URL
		$errorurl = "https://www.smcc.io/lib/CheckPlusSafe_PHP/checkplus_fail.php";		// 실패시 이동될 URL

		// reqseq값은 성공페이지로 갈 경우 검증을 위하여 세션에 담아둔다.
		$_SESSION["REQ_SEQ"] = $reqseq;

		// 입력될 plain 데이타를 만든다.
		$plaindata =  "7:REQ_SEQ" . strlen($reqseq) . ":" . $reqseq .
					"8:SITECODE" . strlen($sitecode) . ":" . $sitecode .
					"9:AUTH_TYPE" . strlen($authtype) . ":". $authtype .
					"7:RTN_URL" . strlen($returnurl) . ":" . $returnurl .
					"7:ERR_URL" . strlen($errorurl) . ":" . $errorurl .
					"11:POPUP_GUBUN" . strlen($popgubun) . ":" . $popgubun .
					"9:CUSTOMIZE" . strlen($customize) . ":" . $customize ;

		$enc_data = `$cb_encode_path ENC $sitecode $sitepasswd $plaindata`;
		if( $enc_data == -1 )
		{
			$returnMsg = "암/복호화 시스템 오류입니다.";
			$enc_data = "";
		}
		else if( $enc_data== -2 )
		{
			$returnMsg = "암호화 처리 오류입니다.";
			$enc_data = "";
		}
		else if( $enc_data== -3 )
		{
			$returnMsg = "암호화 데이터 오류 입니다.";
			$enc_data = "";
		}
		else if( $enc_data== -9 )
		{
			$returnMsg = "입력값 오류 입니다.";
			$enc_data = "";
		}
		$interface->tpl->assign("enc_data", $enc_data);

		if($_GET['test']=='1') {
			$interface->layout['js_tpl_main'] = 'member/edit_form_1.html';
		} else {
			$interface->layout['js_tpl_main'] = 'member/edit_form.html';
		}
	}
	$js->editForm();
	$print = 'layout';
	$interface->display($print);
}
$dbcon->close();
?>
