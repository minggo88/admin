<?php
include dirname(__file__) . "/../../lib/ExchangeApi.php";
// if($_SERVER['REMOTE_ADDR']!='61.74.240.65') {$exchangeapi->error('001','시스템 정검중입니다.');}
$exchangeapi->set_logging(true);
// $exchangeapi->set_log_dir(__dir__.'/../../log/'.basename(__dir__).'/');
// if(__API_RUNMODE__=='live'||__API_RUNMODE__=='loc') {
	$exchangeapi->set_log_dir($exchangeapi->log_dir.'/'.basename(__dir__).'/');
// } else {
	// $exchangeapi->set_log_dir(__dir__.'/');
// }
$exchangeapi->set_log_name('');
$exchangeapi->write_log("REQUEST: " . json_encode($_REQUEST));


// 보내기로 들어온 금액들 확인할 날짜.
$_receive_day = 1;

// -------------------------------------------------------------------- //


// 거래소 api는 토큰을 전달 받을때만 작동하도록 되어 있어서 로그인시 token을 생성해 줍니다.
// $exchangeapi->token = session_create_id();
session_start();
session_regenerate_id(); // 로그인할때마다 token 값을 바꿉니다.

// 로그인 세션 확인.
// $exchangeapi->checkLogout();

// validate parameters
$social_id = checkEmpty(loadParam('social_id')); // userid로 사용합니다.
$mobile_calling_code = setDefault(loadParam('mobile_calling_code'),'82'); // 국제전화번호
$mobile_country_code = setDefault(loadParam('mobile_country_code'),'KR'); // 국가코드
$social_name = checkSocialName(setDefault(loadParam('social_name'),''));
$mobile = checkMobileNumber(setDefault(loadParam('mobile'), ''));
$name = setDefault(loadParam('name'),'');
$nickname = setDefault(loadParam('nickname'),'');
$email = checkEmail(setDefault(loadParam('email'), ''));
// $pin = checkPinNumber(checkNumber(checkEmpty(loadParam('pin'))));
$userpw = setDefault(loadParam('userpw'),'');
$pin = setDefault(loadParam('pin'),'');
if($social_name=='guest' || $social_name=='kakao') {
    $pin = '    ';// guest 비번은 통일.
} else {
    if(strlen($pin)!=6) {
        $exchangeapi->error('010',__('Please enter a 6-digit number.'));
    }
}
$uuid = setDefault(loadParam('uuid'), '-');
$os = setDefault(loadParam('os'), '-');
$fcm_tokenid = setDefault(loadParam('fcm_tokenid'), '-');
$app_id = setDefault($_REQUEST['app_id'], ''); // 가맹점 아이디

// --------------------------------------------------------------------------- //
// 데이터 가공 - 입력값의 형식이 달라서 생기는 오류를 막아야 합니다. ㅜ.ㅜ

// $social_name 이 kakao로 들어오면 앱 업그래이드 하라고 안내하기.
// if($social_name!='mobile') { // 사용하는 social_name을 추가해서 계속 유지합니다.
//     $exchangeapi->error('099',__('The version of the program is incorrect. Please download again.'));
// }

// 전화번호
if($mobile) {
    $mobile = checkMobileNumber($mobile); // 0-9,+,- 문자 말고 다른것이 있는지 문자만 확인
    $mobile = $exchangeapi->reset_phone_number($mobile);
    $mobile = checkIncludedCallingCode($mobile); // 국가전화코드 있는지 확인.
}

// social_id
if($social_name=='mobile' || $social_name=='kakao' || $social_name=='naver') {
    $social_id = checkMobileNumber($social_id); // 0-9,+,- 문자 말고 다른것이 있는지 문자만 확인
    $social_id = $exchangeapi->reset_phone_number($social_id);
    $social_id = checkIncludedCallingCode($social_id); // 국가전화코드 있는지 확인.
    // $mobile = $social_id; // 핸드폰 번호를 받을 수 없어 제외합니다. 가입시 핸드폰번호로 가입하는 경우 모바일 값을 핸드폰 번호로 같이 맞춥니다.
}
if($social_name=='email') {
    $email = $social_id;
}

// 국가코드설정
// $mobile_country_code = '';
switch($social_name) {
    case 'email':
        $mobile_country_code = $exchangeapi->get_country_code($_SERVER['REMOTE_ADDR']);
        $mobile_country_code = $mobile_country_code ? $mobile_country_code : 'KR'; break;
    case 'kakao':
    case 'naver':
        $mobile_country_code = 'KR'; break;
    case 'google':
    case 'mobile':
        if(!$mobile_country_code) { // 전달받은 국가코드가 없으면
            $mobile_country_code = $exchangeapi->get_country_code($_SERVER['REMOTE_ADDR']); // set value with IP
        }
        if(!$mobile_country_code && $mobile) { // set value with phone number
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

// 디비에서 사용하는 아이디, 비번으로 변경.
$userid = $social_name . $social_id;
$userpw = $userpw ? md5($userpw) : '';
$pin = $pin ? md5($pin) : ''; //$pin ? md5($pin) : '';


// --------------------------------------------------------------------------- //

// 마스터 디비 사용하도록 설정.
$exchangeapi->set_db_link('master');

// 계정 정보 확인.
$member = $exchangeapi->get_member_info_by_userid($userid);
if($member) {
    $exchangeapi->error('041', __('Already joined.').' '.__('Please login!'));
}

$cnt_withdraw = $exchangeapi->query_one("select count(*) from js_withdraw where userid='{$exchangeapi->escape($userid)}' ");
if($cnt_withdraw > 0) {
    $exchangeapi->error('105', __('Please enter another ID.'));
}

$exchangeapi->transaction_start();// DB 트랜젝션 시작

// 가입
$sql = "insert into js_member set userid='{$exchangeapi->escape($userid)}', userpw='{$exchangeapi->escape($userpw)}', `name`='{$exchangeapi->escape($name)}', nickname='{$exchangeapi->escape($nickname)}', phone='',email='{$exchangeapi->escape($email)}',mobile='{$exchangeapi->escape($mobile)}',zipcode='',address_a='',address_b='',level_code='',regdate=UNIX_TIMESTAMP(), bool_sms=0, bool_email=0, pin='{$exchangeapi->escape($pin)}', mobile_country_code='{$exchangeapi->escape($mobile_country_code)}' ";
$exchangeapi->query($sql);
$new_userno = $exchangeapi->_recently_query['last_insert_id'];
$member = $exchangeapi->get_member_info_by_userid($userid);
if($member->userno != $new_userno) {
    $exchangeapi->error('000', __('Fail join!'));
}

// 가입 방법 저장
$join_mehod_columns = $exchangeapi->query_one("SHOW COLUMNS FROM `js_member` LIKE 'join_method'");
if(!$join_mehod_columns) {
    $exchangeapi->query("ALTER TABLE `js_member` ADD COLUMN `join_method` VARCHAR(50) DEFAULT '' NOT NULL COMMENT '가입경로(가입시 social_name으로 사용). guest, mobile, email, naver, kakao, google, facebook, ...' AFTER `userpw` ");
}
$exchangeapi->query("UPDATE `js_member` SET `join_method`='".$exchangeapi->escape($social_name)."' WHERE userno='{$exchangeapi->escape($member->userno)}' AND `join_method`='' ");

// 가입자 아이피 저장 컬럼 확인 및 추가
$join_ip = $exchangeapi->query_one("SHOW COLUMNS FROM `js_member` WHERE `Field`='join_ip'");
if(!$join_ip) {
    $exchangeapi->query("ALTER TABLE `js_member` ADD COLUMN `join_ip` VARCHAR(100) DEFAULT '' NOT NULL COMMENT '가입IP' ");
}
$exchangeapi->query("UPDATE `js_member` SET `join_ip`='".$exchangeapi->escape($_SERVER['REMOTE_ADDR'])."' WHERE userno='{$exchangeapi->escape($member->userno)}' AND `join_ip`='' ");

// 기본 코인(ETH 생성)
if(__API_RUNMODE__=='live') {
    if(!$exchangeapi->query_one("select address from js_exchange_wallet where symbol='ETH' and userno='{$member->userno}' ")) {
        $address = $exchangeapi->create_wallet($member->userno, 'ETH');
        $exchangeapi->save_wallet($member->userno, 'ETH', $address);
        $exchangeapi->save_wallet($member->userno, 'AIL', $address);
    }
}
// 기본 지갑 생성
$default_coins = array('KRW','USD'); //, 'SMP', 'SPAY'
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

// 보내기 금액 받기.
$media = false;
$receiver_address = false;
// if($email) { // 이메일로 추천하는 경우는 나중에 작업하기.
//     $media = 'E';
//     $receiver_address = $email;
// }
if($mobile) { // 핸드폰번호로 추천/보내기 하는경우만 처리.
    $media = 'M';
    $receiver_address = $mobile;
}
if($media && $receiver_address) {
	// 추천링크가 있는지 확인 (아라에서는 가입시 지급하는 것이 아니라서 추천인 설정을 따로 여기서합니다.)
	$sql = "SELECT * FROM js_exchange_share_link FORCE INDEX(media_receiver_address, pay_order) WHERE media='{$exchangeapi->escape($media)}' AND receiver_address='{$exchangeapi->escape($receiver_address)}' AND reg_time>= UNIX_TIMESTAMP()-60*60*24*{$_receive_day} AND share_type='I' AND pay_time='' ORDER BY download_time DESC, read_time DESC, reg_time DESC LIMIT 1";
	$invite_link = $exchangeapi->query_fetch_object($sql);
	if($invite_link && $invite_link->userno) {
		$inviter_userid = $exchangeapi->query_one("SELECT userid FROM js_member WHERE userno='$invite_link->userno'");
		if($inviter_userid) {
			$exchangeapi->query("UPDATE js_member SET recomid='{$exchangeapi->escape($inviter_userid)}' WHERE userno='{$exchangeapi->escape($member->userno)}' ");
			// $exchangeapi->query("UPDATE js_exchange_share_link SET pay_time=UNIX_TIMESTAMP() WHERE  `id`='{$exchangeapi->escape($share_link_info->id)}' ");
		}
	}


	// 이미 추천인 가입내역이 있는지 확인합니다.
	// 초대하기(I)는 위에서 처리했습니다.
    $sql = "SELECT count(*) cnt FROM js_exchange_share_link FORCE INDEX(media_receiver_address) WHERE media='{$exchangeapi->escape($media)}' AND receiver_address='{$exchangeapi->escape($receiver_address)}' AND pay_time<>'' AND share_type<>'I' ";// pay_time 이 있는걸 하나 찾습니다.
    $paid = $exchangeapi->query_one($sql);
    if(!$paid) {
        $sql = "SELECT * FROM js_exchange_share_link FORCE INDEX(media_receiver_address, pay_order) WHERE media='{$exchangeapi->escape($media)}' AND receiver_address='{$exchangeapi->escape($receiver_address)}' AND reg_time>= UNIX_TIMESTAMP()-60*60*24*{$_receive_day} AND pay_time='' AND share_type<>'I' ORDER BY download_time DESC, read_time DESC, reg_time DESC LIMIT 1"; // 마지막으로 다운로드한 또는 읽은 또는 공유된 한개를 추출합니다.
        $share_link_info = $exchangeapi->query_fetch_object($sql);
        if($share_link_info) { // 지급가능한 것이 있을때만 작동. 날짜 지나거나 하면 지금 안함.
            // 공유링크정보에 지급날짜 등록
            $exchangeapi->query("UPDATE js_exchange_share_link SET pay_time=UNIX_TIMESTAMP() where `id`='{$exchangeapi->escape($share_link_info->id)}'");
            // 보낸사람 지갑
            $sender_wallet = $exchangeapi->get_row_wallet($share_link_info->userno, $share_link_info->symbol);
            $sender_info = $exchangeapi->get_member_info($share_link_info->userno);
            // 가입자에게 보내기 금액 지급 전에 지갑이 없으면 지갑 생성하기(address가 없다고 표시 안나오도록)
            $receiver_wallet = $exchangeapi->get_row_wallet($member->userno, $share_link_info->symbol);
            if(!$receiver_wallet->address) {
                // 메인넷 있는지 확인.
                $symbol_base_coin = $exchangeapi->query_one("SELECT base_coin FROM js_exchange_currency where `symbol`='{$exchangeapi->escape($share_link_info->symbol)}'");
                if($symbol_base_coin) {
                    $address_base_coin = $exchangeapi->query_one("SELECT address FROM js_exchange_wallet where `symbol`='{$exchangeapi->escape($symbol_base_coin)}' AND userno='{$exchangeapi->escape($member->userno)}'");
                    if($address_base_coin) {
                        $address = $address_base_coin;
                    } else {
                        $address = $exchangeapi->create_wallet($member->userno, $share_link_info->symbol);
                        $exchangeapi->save_wallet($member->userno, $symbol_base_coin, $address);
                    }
                }
                $exchangeapi->save_wallet($member->userno, $share_link_info->symbol, $address);
                $receiver_wallet = $exchangeapi->get_row_wallet($member->userno, $share_link_info->symbol);
            }
            $receiver_info = $exchangeapi->get_member_info($receiver_wallet->userno);

            // 보내는 사람 주소.
            $sender_address = '';
            if($share_link_info->share_type=='S'){ // 보내기일때 보낸사람 주소를 설정.
                $sender_address = $sender_wallet->address;
            }
            // 가입자에게 보내기 금액(또는 추천인 보너스 금액) 지급
            $exchangeapi->add_wallet($receiver_wallet->userno, $share_link_info->symbol, $share_link_info->amount);
            $exchangeapi->add_wallet_txn($receiver_wallet->userno, $receiver_wallet->address, $share_link_info->symbol, $sender_address, $share_link_info->share_type, 'I', $share_link_info->amount, 0, 0, 'D', $share_link_info->id, date('Y-m-d H:i:s'));// DB 처리라 상태는 완료로 처리함.
            // 보낸사람의 txn 정보를 처리 완료로 변경.
            if($share_link_info->share_type=='S'){
                $address = '';
                if($receiver_wallet->address) {// 받는사람 지갑 주소가 있으면 해당 주소로 보낸정보를 갱신합니다. 왜? 받는사람 이름을 가져올때 사용하는 값이 지갑 address라서입니다.
                    $address = ", `address_relative`='{$exchangeapi->escape($receiver_wallet->address)}' ";
                }
                $exchangeapi->query("UPDATE js_exchange_wallet_txn SET `status`='D', `txndate`=NOW() {$address} WHERE userno='{$exchangeapi->escape($share_link_info->userno)}' AND key_relative='{$exchangeapi->escape($share_link_info->id)}' AND txn_type='S' AND direction='O' AND STATUS='O'");
            }
			// 친구초대시 추천인에게 설치 보너스 지급 (10 BDS)
			// Morrow Wallet는 거래소 인증 후 지급
            // if($share_link_info->share_type=='I'){
            //     $exchangeapi->add_wallet($share_link_info->userno, $share_link_info->symbol, '5'); // symbol은  BDS 입니다.
            //     $exchangeapi->add_wallet_txn($share_link_info->userno, $sender_wallet->address, $share_link_info->symbol, $receiver_wallet->address, $share_link_info->share_type, 'I', '5', 0, 0, 'D', $share_link_info->id, date('Y-m-d H:i:s'));// DB 처리라 상태는 완료로 처리함.
            // }

            if($share_link_info->amount>0) {
				// 보너스 지급 안내 메시지 발송
				// Morrow Wallet는 거래소 인증 후 지급
                // if($share_link_info->share_type=='I') {
                //     $title = '['.__APP_NAME__.'] 초대보상 지급 안내 ';
                //     $body = "초대보상({$share_link_info->amount} {$share_link_info->symbol})이 지급되었습니다. ".__APP_NAME__."을 설치해주셔서 감사합니다. " . date('m-d H:i');
                //     $user_token = $exchangeapi->query_list_one("SELECT fcm_tokenid FROM js_member_device WHERE userno='" . $exchangeapi->escape($receiver_info->userno) . "' GROUP BY fcm_tokenid ");
                //     $exchangeapi->send_fcm_message($user_token, $body, $title);
                //     $body = "{$receiver_info->name}님이 앱을 설치하셔서 초대보상({$share_link_info->amount} {$share_link_info->symbol})이 지급되었습니다. 감사합니다. " . date('m-d H:i');
                //     $user_token = $exchangeapi->query_list_one("SELECT fcm_tokenid FROM js_member_device WHERE userno='" . $exchangeapi->escape($sender_info->userno) . "' GROUP BY fcm_tokenid ");
                //     $exchangeapi->send_fcm_message($user_token, $body, $title);
                // }

                // 보내기 완료 안내 메시지 발송
                if($share_link_info->share_type=='S') {
                    $title = '['.__APP_NAME__.'] 보내기 안내 ';
                    $body = "{$sender_info->name}님이 보내신 {$share_link_info->amount} {$share_link_info->symbol}를 지급했습니다. ".__APP_NAME__."을 설치해주셔서 감사합니다. " . date('m-d H:i');
                    $user_token = $exchangeapi->query_list_one("SELECT fcm_tokenid FROM js_member_device WHERE userno='" . $exchangeapi->escape($receiver_info->userno) . "' GROUP BY fcm_tokenid ");
                    $exchangeapi->send_fcm_message($user_token, $body, $title);
                    $body = "{$receiver_info->name}님이 앱을 설치하셔서 {$share_link_info->amount} {$share_link_info->symbol}을 보냈습니다." . date('m-d H:i');
                    $user_token = $exchangeapi->query_list_one("SELECT fcm_tokenid FROM js_member_device WHERE userno='" . $exchangeapi->escape($sender_info->userno) . "' GROUP BY fcm_tokenid ");
                    $exchangeapi->send_fcm_message($user_token, $body, $title);
                }
            }

        }
    }
}

// put fcm tokenid + add device
$_r = $exchangeapi->get_fcm_info($member->userno, $uuid);
if(!$_r) {
    $exchangeapi->put_fcm_info($member->userno, $uuid, $os, $fcm_tokenid);
}

$exchangeapi->transaction_end('commit');// DB 트랜젝션 끝

// login - userno, $userid, $name, $level_code)
$exchangeapi->login($member->userno, $member->userid, $member->name, $member->level_code);

// 나는 몇번째 지갑인가?
$my_wallet_no = $exchangeapi->query_one("SELECT COUNT(*)+1 FROM js_member WHERE 1000<=userno AND userno<'{$member->userno}' "); // 회원은 1000번부터 시작합니다.

// next-page = 5

// response
$exchangeapi->success(array('token'=>session_id()));
