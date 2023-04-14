<?php
include dirname(__file__)."/../../lib/TradeApi.php";

/**
 * 파일 업로드 API Method
 */
// 로그인 세션 확인.
// $tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
// $file_class = checkFileClass(strtolower(checkEmpty($_REQUEST['file_class'], 'file class'))); // 코인

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// file upload to s3
// var_dump($_FILES); exit;
$files = $tradeapi->save_file_to_s3($_FILES['file_data']);

// response
$tradeapi->success($files);
