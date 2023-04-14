<?php
// 로그인할때 token을 사용자별로 새로 만듧니다.
$_REQUEST['token'] = sha1($_REQUEST['social_name'].'/'.$_REQUEST['social_id'].'/'.time()); // php 기본 세션은 md5를 사용하기 때문에 타 로그인과 구분됨.

// exit(__SESSION_ID__);
include dirname(__file__) . "/../../lib/ExchangeApi.php";
// if($_SERVER['REMOTE_ADDR']!='61.74.240.65') {$exchangeapi->error('001','시스템 정검중입니다.');}
$exchangeapi->set_logging(true);
// $exchangeapi->set_log_dir(__dir__.'/../../log/'.basename(__dir__).'/');
// if(__API_RUNMODE__=='live'||__API_RUNMODE__=='loc') {
	$exchangeapi->set_log_dir($exchangeapi->log_dir.'/'.basename(__dir__).'/');
// } else {
// 	$exchangeapi->set_log_dir(__dir__.'/');
// }
$exchangeapi->set_log_name('');
$exchangeapi->write_log("REQUEST: " . json_encode($_REQUEST));

// 거래소 api는 토큰을 전달 받을때만 작동하도록 되어 있어서 로그인시 token을 생성해 줍니다.
// $exchangeapi->token = session_create_id();
session_start();


// validate parameters
$social_id = checkEmpty(loadParam('social_id'), 'Social ID'); // password로 사용합니다.
$social_name = checkSocialName(setDefault(loadParam('social_name'), ''));
if($social_name!='guest') {
    $mobile_country_code = setDefault(loadParam('mobile_country_code'), ''); // ?? 왜 필수로 했었지?? ㅜ.ㅜ --> 옵션처리함(2022.04.14)
} else {
    $mobile_country_code = setDefault(loadParam('mobile_country_code'), '');
}
$pin = setDefault(loadParam('pin'), ''); // 비밀번호 , android iso 가 아니면 비번 입력해야 함.
$userpw = setDefault(loadParam('userpw'), ''); // 비밀번호 , android iso 가 아니면 비번 입력해야 함.
if($social_name=='guest') {
    $userpw = '    ';// guest 비번은 통일.
}
// userpw 없이 pin으로 비번을 전송하는 경우(예전 API)가 있어서 예외처리함
if(!$userpw && $pin) {
    $userpw = $pin;
}

$uuid = setDefault(loadParam('uuid'), '-');
$os = strtolower(setDefault(loadParam('os'), '-'));
if(!$userpw) {
    if($social_name=='guest' || $social_name=='mobile' && ($os=='android' || $os=='ios') ) { // guest 이거나 android 앱 이거나 ios 앱은 비번없이 로그인 가능 
        ;
    } else { // 그외 이메일이나 아이디나 소셜로그인은 비번 있어야함.
        $exchangeapi->error('005', __('Please enter a password.'));
    }
}
$fcm_tokenid = setDefault(loadParam('fcm_tokenid'), '-');
$app_id = setDefault($_REQUEST['app_id'], ''); // 가맹점 아이디
$name = setDefault(loadParam('name'), ''); // 회원이름, 닉네임
$nickname = $name;

// check test id
/*if(
	strpos($social_id, '93277306')===false && strpos($social_id, '12345678')===false && strpos($social_id, '12345670')===false
) {
    $exchangeapi->error('000', '공식서비스를 이용해주세요.'.$social_id);
}*/


// --------------------------------------------------------------------------- //
// 데이터 가공 - 입력값의 형식이 달라서 생기는 오류를 막아야 합니다. ㅜ.ㅜ

// $social_name 이 kakao로 들어오면 앱 업그래이드 하라고 안내하기.
// if($social_name!='mobile') { // 사용하는 social_name을 추가해서 계속 유지합니다.
//     $exchangeapi->error('099',__('The version of the program is incorrect. Please download again.'));
// }

// social_id
if($social_name=='mobile' || $social_name=='kakao' || $social_name=='naver') {
    loadParam('social_id'); // 애러 표시할때 social_id 값이 잘못됬음을 표시하기 위해 로드함.
    $social_id = checkMobileNumber($social_id); // 0-9,+,- 문자 말고 다른것이 있는지 문자만 확인
    $social_id = $exchangeapi->reset_phone_number($social_id);
    $social_id = checkIncludedCallingCode($social_id); // 국가전화코드 있는지 확인.
}

// 국가코드설정
$mobile_country_code = '';
switch($social_name) {
    case 'kakao':
    case 'naver':
        $mobile_country_code = 'KR'; break;
    case 'google':
    case 'mobile':
        $mobile_country_code = $exchangeapi->get_country_code($_SERVER['REMOTE_ADDR']); // set value with IP
        if(!$mobile_country_code) { // set value with phone number
            $country_data = $exchangeapi->get_country();
            foreach($country_data as $row) {
                $country_calling_code = str_replace('+','',$row->colling_code);
                if(preg_match('/^('.$country_calling_code.'|\+'.$country_calling_code.')/', $mobile)) {
                    $mobile_country_code = $row->code; break;
                }
            }
        }
}
$exchangeapi->set_language_by_countrycode($mobile_country_code);

// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$exchangeapi->set_db_link('master');

// 디비에서 사용하는 아이디, 비번으로 변경.
$userid = $social_name . $social_id;

// 이전에 사용하던 방식.. .맨 위에서 토큰 재생성합니다.
// // 거래소 api는 토큰을 전달 받을때만 작동하도록 되어 있어서 로그인시 token을 생성해 줍니다.
// session_start();
// // 로그인할때마다 token 값을 바꿉니다.
// // session_regenerate_id();
// // 로그인시 사용자 아이디별로 세션값 생성. 사용자 충돌 피하기 위함.
// define('__SESSION_ID__', hash('sha256', $userid.'/'.time()));
// session_id(__SESSION_ID__);

// 계정 정보 확인.
$member = $exchangeapi->get_member_info_by_userid($userid);
if (!$member) {
    
    if($social_name=='guest') { // guset 는 가입 안했어도 그냥 바로 가입시키고 넘긴다. 가입하나 안하나 크게 의미가 없어서임.

        $userpw_md5 = ''; //md5($userpw);
        $pin = md5('    ');
        $sql = "insert into js_member set userid='{$exchangeapi->escape($userid)}', userpw='{$exchangeapi->escape($userpw_md5)}', `name`='{$exchangeapi->escape($name)}', nickname='{$exchangeapi->escape($nickname)}',email='{$exchangeapi->escape($email)}',mobile='{$exchangeapi->escape($mobile)}',level_code='',regdate=UNIX_TIMESTAMP(), bool_sms=0, bool_email=0, pin='{$exchangeapi->escape($pin)}', mobile_country_code='{$exchangeapi->escape($mobile_country_code)}', reg_ip='{$exchangeapi->escape($_SERVER['REMOTE_ADDR'])}' ";
        $exchangeapi->query($sql);
        $new_userno = $exchangeapi->_recently_query['last_insert_id'];
        $member = $exchangeapi->get_member_info_by_userid($userid);

        // 기본 지갑 생성
        $default_coins = array('SMP', 'SPAY');
        foreach($default_coins as $coin) {
            $address = $exchangeapi->create_wallet($new_userno, $coin);
            $exchangeapi->save_wallet($new_userno, $coin, $address);
        }

        // app 별로 가입 보너스 있는지 확인
        if($app_id) {
            $app_no = $exchangeapi->query_one("SELECT app_no FROM js_app WHERE app_id='{$exchangeapi->escape($app_id)}' ");
            $signup_point = $exchangeapi->query_one("SELECT points FROM js_game_actions WHERE code='SignUp' AND app_no='{$exchangeapi->escape($app_no)}'");
            if($signup_point) {
                $wallet = $exchangeapi->get_row_wallet($new_userno, 'SMP');
                $exchangeapi->add_wallet($new_userno, 'SMP', $signup_point);
                $exchangeapi->add_wallet_txn($new_userno, $wallet->address, 'SMP', '', 'BO', 'I', $signup_point, $fee=0, $tax=0, $status="D", $key_relative="", $txndate='', $msg='', $app_no);
            }
        }

        // put fcm tokenid + add device
        $_r = $exchangeapi->get_fcm_info($member->userno, $uuid);
        if(!$_r) {
            $exchangeapi->put_fcm_info($member->userno, $uuid, $os, $fcm_tokenid);
        }

    } else { // guest가 아니면 가입여부 확인한다.
        // $exchangeapi->error('041', __('The information does not match. Please check your ID!'));
        $exchangeapi->error('041', __('The information does not match.').' '.__('Please check your ID!'));
    }

}

// 가입시 비밀번호가 공백으로 저장되는 현상이 있어서 로그인시 비밀번호가 공백이고 pin은 값이 있으면  pin값으로 비밀번호를 대체합니다.
if($member->userpw=='' && $member->pin!='') {
	$member->userpw = $member->pin;
}

// 비밀번호 확인. - 소셜로그인은 비번 확인 안함. 이유는 소셜아이디 값을 알수 없어서임. 그리고 send 할때만 비번이 필요하기때문임.
if($social_name=='guest' || $social_name=='mobile' && ($os=='android' || $os=='ios') ) { // guest 이거나 android 앱 이거나 ios 앱은 비번없이 로그인 가능 
} else {
    if(md5($userpw) != $member->userpw) {
        $exchangeapi->error('031', __('The information does not match. Please check your ID!').','.$userpw.','.md5($userpw).','.$member->userpw);
    }
}

// 이메일 인증 확인.
// if($member->bool_confirm_email < 1) {
//     $exchangeapi->error('031', __('You did not verify your email. Please check your email verification!'));
// }

// put fcm tokenid + add device : inert or update
// $_r = $exchangeapi->get_fcm_info($member->userno, $uuid);
// if (!$_r) {
    $exchangeapi->put_fcm_info($member->userno, $uuid, $os, $fcm_tokenid);
// }

// login - userno, $userid, $name, $level_code)
$_r = $exchangeapi->login($member->userno, $member->userid, $member->name, $member->level_code);
if (!$_r) {
    $exchangeapi->error('007', __('Login failed.'));
}

// 로그인 알림.
$title = '[KMCSE] 로그인 안내 ';
$body = 'SAM중소기업비상장거래에 로그인하셨습니다.' . date('m-d H:i');
$user_token = $exchangeapi->query_list_one("SELECT fcm_tokenid FROM js_member_device WHERE userno='" . $exchangeapi->escape($member->userno) . "' GROUP BY fcm_tokenid ");
$exchangeapi->send_fcm_message($user_token, $body, $title);


$next_step = '5';
// pin 번호 입력
if (trim($member->userpw) == '') {
    $next_step = '4';
}
// 이름
if (trim($member->name) == '') {
    $next_step = '2';
}

// 기본 지갑 생성
// 이미 가입시 생성합니다. 아니면 작동
// $default_coins = array('SMP', 'SPAY');
// foreach($default_coins as $coin) {
//     $wallet = $exchangeapi->get_row_wallet($member->userno, $coin);
//     if(!$wallet) {
//         $address = $exchangeapi->create_wallet($member->userno, $coin);
//         $exchangeapi->save_wallet($member->userno, $coin, $address);
//     }
// }

$r = array('token' => session_id(), 'next_step' => $next_step, 'name'=>$member->name);

// var_dump('token' , session_id(), 'next_step' , $next_step, $_SESSION);

// 게임 승/패수
if($game_name) {
    $t = $exchangeapi->query_fetch_object("SELECT cnt_win, cnt_lose FROM `js_game_member` WHERE game_name='{$exchangeapi->escape($game_name)}' AND userno='{$exchangeapi->escape($member->userno)}'");
    $r['cnt_win'] = ($t->cnt_win*1).'';
    $r['cnt_lose'] = ($t->cnt_lose*1).'';
    // 30분 이내 로그인한 회원수 - 게임방 종류별로 참여인원을 카운트합니다.
    $r['play_info'] = $exchangeapi->query_list_object("SELECT IFNULL(room_type,'') room_type, COUNT(userno) cnt_player FROM `js_game_play` WHERE game_name='{$exchangeapi->escape($game_name)}' AND reg_date>='".date('Y-m-d H:i:s', time()-60*10)."'");

    $exchangeapi->query("UPDATE `js_game_member` SET login_date=NOW() WHERE game_name='{$exchangeapi->escape($game_name)}' AND userno='{$exchangeapi->escape($member->userno)}'");

    // 게임 처음 로그인시 보너스 10HTP 지급하기. - 게임이름 안넘어 와서 적용 못함. 게임 패치후 적용하기.
    // if($game_name=='AraMatgo') {
    //     $received_bonus = $exchangeapi->query_one("SELECT COUNT(*) FROM js_exchange_wallet_txn WHERE symbol='HTP' AND userno='{$exchangeapi->escape($member->userno)}' AND service_name='{$exchangeapi->escape($game_name)}' AND txn_type='BO' AND direction='I' AND amount='10' ");
    //     if(!$received_bonus) {
    //         $wallet_fee = $exchangeapi->query_fetch_object("SELECT * FROM js_exchange_wallet WHERE userno='2' AND symbol='HTP' "); // walletmanager
    //         $exchangeapi->add_wallet($member->userno, 'HTP', 10);
    //         $wallet = $exchangeapi->query_fetch_object("SELECT * FROM js_exchange_wallet WHERE userno='{$exchangeapi->escape($member->userno)}' AND symbol='HTP' ");
    //         $exchangeapi->query("INSERT INTO js_exchange_wallet_txn SET userno='{$exchangeapi->escape($wallet->userno)}', symbol='{$exchangeapi->escape($wallet->symbol)}', address='{$exchangeapi->escape($wallet->address)}', regdate=now(), txndate=now(), address_relative='{$exchangeapi->escape($wallet_fee->address)}', txn_type='BO', direction='I', amount='10', fee='0', fee_relative='', tax='0', status='D', key_relative='".time()."', txn_method='COIN', service_name='{$exchangeapi->escape($game_name)}'  ");
    //     }
    // }
}

// 나는 몇번째 지갑인가?
$r['my_wallet_no'] = $exchangeapi->query_one("SELECT COUNT(*)+1 FROM js_member WHERE 1000<=userno AND userno<'{$member->userno}' "); // 회원은 1000번부터 시작합니다.

// response
$exchangeapi->success($r);//, 'SESSION'=>$_SESSION
