<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_user.php';
include_once 'member_class.php';
include_once '../api/lib/TradeApi.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Member($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	if($config_basic['bool_ssl'] > 0) {
		$js->config['write_mode'] = 'post';
	}
	else {
		ajaxCheck();
	}
	// $_POST['userid'] = $_POST['email'];
	$js->write();
}
else if($_GET['pg_mode'] == 'overlap_check') {
	ajaxCheck();
	$js->overlapCheck();
}
else {
	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('user','a3');
	$interface->addNavi(getNavi());
	if ($tpl->skin=='shop') {
		$interface->layout['js_tpl_left_menu'] = 'js_left_menu.html?category';
	} else {
		$interface->layout['js_tpl_left'] = 'js_left_menu.html?join';
	}
	if($_GET['pg_mode'] == 'join_email') {
        $interface->layout['js_tpl_main'] = 'member/join_email.html';
        //$js->joinOk();
        $print = 'layout';
    }
	else if($_GET['pg_mode'] == 'join_ok') {
		$interface->layout['js_tpl_main'] = 'member/join_ok.html';
		//$js->joinOk();
		$print = 'layout';
	}
	/*
	else if($_GET['pg_mode'] == 'confirm_email') {
		$query = "select * from js_member where userid='".$_GET_ESCAPE['userid']."' ";
		$_member_info = $js->dbcon->query_unique_array($query);
		$interface->tpl->assign("member_info", $_member_info);
		$interface->layout['js_tpl_main'] = 'member/confirm_email.html';
		$js->confirmEmailOK($_GET['userid']);

		$print = 'layout';
	}
	*/

	elseif($_POST['pg_mode'] == 'send_confirm_email') {
		// if($config_basic['bool_ssl'] > 0) {
		// 	checkUser();
		// } else {
		// 	ajaxCheckUser();
		// }
		$js->send_confirm_email($_POST['email']);
	}
	elseif($_POST['pg_mode'] == 'check_confirm_number') {
		$js->check_confirm_number($_POST['email'], $_POST['confirm_number']);
	}
	else if($_GET['pg_mode'] == 'form_join') {
		if($_SESSION['USER_ID']) {alertGo('','/');} // 로그인 회원 메인페이지로 이동
		$query = "select * from js_realname where userid='".$dbcon->escape($_SESSION['tmprealnameid'])."' ";
		$_realname_info = $js->dbcon->query_unique_array($query);
		$s1 = substr($_realname_info['mobile_number'], 0, 3);
		$s3 = substr($_realname_info['mobile_number'], -4);
		$s2 = str_replace(array($s1, $s3), '', $_realname_info['mobile_number']);
		$_realname_info['mobile_number_split'] = array( 's1'=>$s1, 's2'=>$s2, 's3'=>$s3 );
		$interface->tpl->assign("realname_info", $_realname_info);

		$interface->setPlugIn('form');
		$interface->setPlugIn('popup');
		$interface->layout['js_tpl_main'] = 'member/join_form.html';
		$print = 'layout';
	}
	else if($_GET['pg_mode'] == 'realname_check') {
		$interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/contents/css/contents.css');


		$query = "select * from js_realname where userid='".$dbcon->escape($_SESSION['USER_ID'])."' ";
		$_realname_info = $js->dbcon->query_unique_array($query);
//		if(empty($_realname_info)){
			$sitecode = "G5799";				// NICE로부터 부여받은 사이트 코드
			$sitepasswd = "4XB4Y7E32TK3";			// NICE로부터 부여받은 사이트 패스워드
			$cb_encode_path = dirname(__file__)."/../lib/CheckPlusSafe_PHP/64bit/CPClient";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
			$authtype = "M";      	// 없으면 기본 선택화면, X: 공인인증서, M: 핸드폰, C: 카드
			$popgubun 	= "N";		//Y : 취소버튼 있음 / N : 취소버튼 없음
			$customize 	= "";			//없으면 기본 웹페이지 / Mobile : 모바일페이지
			$reqseq = "REQ_".$_SESSION['USER_ID'];     // 요청 번호, 이는 성공/실패후에 같은 값으로 되돌려주게 되므로
											// 업체에서 적절하게 변경하여 쓰거나, 아래와 같이 생성한다.
	//		$reqseq = `$cb_encode_path SEQ $sitecode`;
			// CheckPlus(본인인증) 처리 후, 결과 데이타를 리턴 받기위해 다음예제와 같이 http부터 입력합니다.
			// $returnurl = "https://www.smcc.io/lib/CheckPlusSafe_PHP/checkplus_success.php";	// 성공시 이동될 URL
			$returnurl = "https://www.smcc.io/lib/CheckPlusSafe_PHP/checkplus_success_join.php";	// 성공시 이동될 URL
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
//		} else {
			$interface->tpl->assign("realname_info", $_realname_info);
//		}

		$interface->layout['js_tpl_main'] = 'member/realname_check.html';
		$print = 'layout';
	}
	else {
		if($_SESSION['USER_ID']) {alertGo('','/');} // 로그인 회원 메인페이지로 이동
		$interface->setPlugIn('form');
		$_SESSION['tmprealnameid'] = $_GET['tmprealnameid'];
		$interface->layout['js_tpl_main'] = 'member/join_clause.html';
		$js->clauseForm();
		$print = 'layout';
	}
	$interface->display($print);
}
$dbcon->close();
?>
