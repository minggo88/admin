<?php
include dirname(__file__) . "/../../lib/TradeApi.php";

// 로그인 세션 강제 로그아웃.
$tradeapi->logout();

// validate parameters
$firstname = setDefault($_REQUEST['firstname'], '');
if($firstname=='') {
    $tradeapi->error('101', __('Enter your first name!'));
}
$lastname = setDefault($_REQUEST['lastname'], '');
if($lastname=='') {
    $tradeapi->error('102', __('Enter your last name!'));
}
$username = $firstname.' '.$lastname;

$userid = setDefault($_REQUEST['userid'], '');
$userid = trim(str_ireplace('&nbsp;','',$userid));
if($userid=='') {
    $tradeapi->error('103', __('Please enter your member ID.'));
}
if($tradeapi->get_i18n_lang()=='zh') { // 중국어는 핸드폰번호로 가입.
    // 국가코드 확인. 앞에 2자리에 대문자 국가코드 있어야 함.
    if(!preg_match('/^([A-Z]{2})/', $userid)) {
        $tradeapi->error('103', __('The member ID must contain a country code in uppercase letters.'));
    }
    // 전화번호 확인. 앞 2자리 재외하면 모두 숫자여야합니다.
    if(preg_match('/[^0-9]/', substr($userid,2))) {
        $tradeapi->error('103', __('Please enter your phone number.'));
    }
    // $mobile = $userid;
    $mobile = setDefault($_REQUEST['mobile'], '');
    $mobile = trim(str_ireplace('&nbsp;','',$mobile));
    if($mobile=='') {
        $tradeapi->error('103', __('Please enter the mobile value.'));
    }
    $bool_confirm_email = '0';
    $email = '';
} else { // 다른언어는 이메일로 가입.
    // 이메일 확인.
    $tradeapi->checkEmail($userid);
    $email = $userid;
    $bool_confirm_email = '1';
    $mobile = '';
}
$userpw = setDefault($_REQUEST['userpw'], '');
if($userpw=='') {
    $tradeapi->error('104', __('Enter your password!'));
}

// $uuid = checkUUID(checkEmpty($_REQUEST['uuid'], 'UUID'));
// $os = checkEmpty($_REQUEST['os'], 'OS');
// $fcm_tokenid = checkEmpty($_REQUEST['fcm_tokenid'], 'fcm_tokenid');

// --------------------------------------------------------------------------- //


// 마스터 디비 사용하도록 설정.
$tradeapi->set_db_link('master');

// 아이디 중복 확인.
$sql = "select count(*) from js_member where userid='{$tradeapi->escape($userid)}' ";
$cnt = $tradeapi->query_one($sql);
$cnt_withdraw = $tradeapi->query_one("select count(*) from js_withdraw where userid='{$tradeapi->escape($userid)}' ");
if($cnt > 0 || $cnt_withdraw > 0) {
    $tradeapi->error('105', __('Please enter another ID that is not duplicated.'));
}
// var_dump($_SERVER); exit;
// 웹회원가입에 전송해 가입처리한다. 쩝. 이메일 발송/ SMS 발송 때문에 이리함.
$host = substr_count($_SERVER['HTTP_HOST'],'.')>2 ? str_replace('api.','',$_SERVER['HTTP_HOST']) : str_replace('api.','www.',$_SERVER['HTTP_HOST']);
if(strpos($_SERVER['HTTP_HOST'], 'loc.')!==false) { // 개발자 환경.
    $host = 'http://'.$host;
} else if(strpos($_SERVER['HTTP_HOST'], 'dev.')!==false) { // 개발서버 환경.
    $host = 'http://'.$host.':8080'; // nginx에서 post 값을 전달하지 못하는 현상이 발생하기도 함. 그래서 nginx가 아닌 apache에게 바로 전달함.
} else {
    $host = 'http://'.$host.':880'; // nginx에서 post 값을 전달하지 못하는 현상이 발생하기도 함. 그래서 nginx가 아닌 apache에게 바로 전달함.
}
$url = $host.'/join';
$data = array(
    'pg_mode'=>'write',
    'name'=>$username,
    'firstname'=>$firstname,
    'lastname'=>$lastname,
    'userid'=>$userid,
    'userpw'=>$userpw,
    'email'=>$email,
    'mobile'=>$mobile
);
// var_dump($url,$data);exit;
$r = $tradeapi->remote_post($url, $data, null, $host.'/formjoin');
// var_dump($r);exit; // {"bool":1,"msg":"Sign up is complete. Please login."}
if($r) {
    $r = json_decode($r);
    if($r->bool) {
        // response
        $tradeapi->success();
    } else {
        $msg = $r->msg ? $r->msg : __('There was an error processing your subscription. Please contact administrator.');
        $tradeapi->error('000', $msg);
    }
} else {
    $tradeapi->error('000', __('There was an error processing your subscription. Please contact administrator.'));
}

