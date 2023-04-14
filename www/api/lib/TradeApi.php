<?php
include dirname(__file__) . '/SimpleRestful.php';
include dirname(__file__) . '/Coind.php';
include dirname(__file__) . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Twilio\Rest\Client;
use Intervention\Image\ImageManager;

/**
 *
 */
if (!defined('__LOADED_TRADEAPI__')) {
    class TradeApi extends SimpleRestful
    {

        public $default_exchange = 'KRW';

        /**
         * Trade API Class 생성자
         *
         * 인증 환경 설정
         */
        public function __construct()
        {
            $this->set_cache_dir(dirname(__file__) . '/../cache/');
            $this->set_logging(false);
            $this->set_log_dir(dirname(__file__) . "/../log/");
            parent::__construct();
            $this->_set_auth_env();
            $this->set_default_exchange();
        }

        // ----------------------------------------------------------------- //
        // Common Function


        /**
         * 쿼리를 실행하고 결과를 배열 속 Object로 리턴. 여러 row의 결과를 배열로 받을때 사용합니다.
         */
        public function query_list_tsv($query, $reverse = false)
        {
            $return = array();
            $row_title = array();
            $result = $this->query($query);
            while ($row = $this->_fetch_object($result)) {
                if (!empty($row)) {
                    $_t = '';
                    $_r = '';
                    foreach($row as $key => $val) {
                        if(count($row_title)<1) {
                            $_t.= $_t == '' ? $key : "\t".$key;
                        }
                        $_r.= $_r == '' ? $val : "\t".$val;
                    }
                    $return[] = $_r;
                    if(count($row_title)<1) {
                        $row_title[] = $_t;
                    }
                }
            }
            if($reverse) {
                $return = array_reverse($return);
            }
            $this->_db_free_result($result);
            return implode("\n", array_merge($row_title, $return));
        }


        // ----------------------------------------------------------------- //
        // Authorization

        private function _set_auth_env()
        {
            // //if (session_status() == PHP_SESSION_NONE) {
            // if(session_id() == '') {
            //     // ini_set("session.save_path", $_SERVER['DOCUMENT_ROOT'] . "/../_session");
            //     ini_set("session.save_path", dirname(__file__) . "/../../../_session");
            //     ini_set("session.cookie_lifetime", "3600"); //기본 세션타임 1시간으로 설정 합니다.
            //     ini_set("session.cache_expire", "3600"); //기본 세션타임 1시간으로 설정 합니다.
            //     ini_set("session.gc_maxlifetime", "3600"); //기본 세션타임 1시간으로 설정 합니다.
            //     session_cache_limiter('private');
            //     session_start();
            // }
        }

        /**
         * API 방식의 로그인 처리. - 작업중. 일단을 HTTP POST 웹사이트 방식의 로그인으로 진행.
         */
        public function login($userno, $userid, $name, $level_code)
        {

            $sql = "select count(*) from js_member where userid='{$this->escape($userid)}' ";
            $cnt = $this->query_one($sql);
            if($cnt == 0) {
                return false;
            }

            // $query['tool'] = 'row';
            // $query['fields'] = 'userno,userid,userpw,name,level_code,bank_account';
            // $query['fields'].= ',bool_confirm_email,bool_confirm_mobile,bool_confirm_idimage,bool_email_krw_input,bool_sms_krw_input,bool_email_krw_output,bool_sms_krw_output,bool_email_btc_trade,bool_email_btc_input,bool_email_btc_output';
            $sql = " select * from js_member where userid='{$this->escape($userid)}'  ";
            $row = $this->query_fetch_array($sql);
            if($row['userno']!=$userno) {
                return false;
            }

            $_SESSION['USER_NO'] = $userno;
            $_SESSION['USER_ID'] = $userid;
            $_SESSION['USER_NAME'] = $name;
            $_SESSION['USER_LEVEL'] = $level_code;

            // SCC Account 여부
            $sql = "select count(*) from js_exchange_wallet where userno='{$this->escape($userno)}' and symbol='SCC' ";
            $scc_cnt = $this->query_one($sql);
            if($scc_cnt > 0) {
                $_SESSION['SCC_ACCOUNT'] = $scc_cnt;
            } else {
                $_SESSION['SCC_ACCOUNT'] = '0';
            }

            // 본인인증여부
            $query = "select * from js_member where userid='".$this->escape($userid)."' ";
            $_realname_info = $this->query_fetch_array($query);
            if( !empty($_realname_info) && $_realname_info['bool_realname'] != '0' ) {
                $_SESSION['USER_REALNAME'] = '1';
                $_SESSION['USER_GENDER'] = $_realname_info['gender'];
                $_SESSION['USER_BIRTHDATE'] = $_realname_info['birthdate'];
            } else {
                $_SESSION['USER_REALNAME'] = '0';
            }
            $_SESSION['bool_confirm_email'] = $row['bool_confirm_email'];
            $_SESSION['bool_confirm_mobile'] = $row['bool_confirm_mobile'];
            $_SESSION['bool_email_krw_input'] = $row['bool_email_krw_input'];
            $_SESSION['bool_sms_krw_input'] = $row['bool_sms_krw_input'];
            $_SESSION['bool_email_krw_output'] = $row['bool_email_krw_output'];
            $_SESSION['bool_sms_krw_output'] = $row['bool_sms_krw_output'];
            $_SESSION['bool_email_btc_trade'] = $row['bool_email_btc_trade'];
            $_SESSION['bool_email_btc_input'] = $row['bool_email_btc_input'];
            $_SESSION['bool_email_btc_output'] = $row['bool_email_btc_output'];

            return $_SESSION['USER_NO'] ? true : false;
        }

        public function login_admin($id, $pw) {
            // 계정 정보 확인.
            $sql = "select * from js_admin where adminid='{$this->escape($id)}' ";
            $admin = $this->query_fetch_object($sql);
            if(!$admin) {
                return false;
            }
            // var_dump($admin, md5($pw));exit($sql);
            // 비밀번호 확인.
            if(md5($pw) != $admin->adminpw) {
                return false;
            }
            $_SESSION['ADMIN_ID'] = $admin->adminid;
            $_SESSION['ADMIN_KIND'] = $admin->kind_admin;
            $_SESSION['ADMIN_NAME'] = $admin->kind_name;
            $_SESSION['ADMIN_RIGHT_BASIC'] = $admin->right_basic;
            $_SESSION['ADMIN_RIGHT_SCHEDULE'] = $admin->right_schedule;
            $_SESSION['ADMIN_RIGHT_CONTENTS'] = $admin->right_contents;
            $_SESSION['ADMIN_RIGHT_GOODS'] = $admin->right_goods;
            $_SESSION['ADMIN_RIGHT_PLAN'] = $admin->right_plan;
            $_SESSION['ADMIN_RIGHT_ORDER'] = $admin->right_order;
            $_SESSION['ADMIN_RIGHT_MEMBER'] = $admin->right_member;
            $_SESSION['ADMIN_RIGHT_COMMUNITY'] = $admin->right_community;
            $_SESSION['ADMIN_RIGHT_MARKETING'] = $admin->right_marketing;
            $_SESSION['ADMIN_RIGHT_DATA'] = $admin->right_data;
            $_SESSION['ADMIN_RIGHT_DESIGN'] = $admin->right_design;
            $_SESSION['ADMIN_RIGHT_WALLET'] = $admin->right_wallet;
            $_SESSION['ADMIN_MOBILE'] = $admin->admin_mobile;
            return true;
        }



        public function logout()
        {
            unset($_SESSION['USER_NO']);
            unset($_SESSION['USER_ID']);
            unset($_SESSION['USER_NAME']);
            unset($_SESSION['USER_LEVEL']);

            unset($_SESSION['ADMIN_ID']);
            unset($_SESSION['ADMIN_NAME']);
            unset($_SESSION['ADMIN_KIND']);
            unset($_SESSION['ADMIN_RIGHT_BASIC']);
            unset($_SESSION['ADMIN_RIGHT_SCHEDULE']);
            unset($_SESSION['ADMIN_RIGHT_CONTENTS']);
            unset($_SESSION['ADMIN_RIGHT_GOODS']);
            unset($_SESSION['ADMIN_RIGHT_PLAN']);
            unset($_SESSION['ADMIN_RIGHT_ORDER']);
            unset($_SESSION['ADMIN_RIGHT_MEMBER']);
            unset($_SESSION['ADMIN_RIGHT_COMMUNITY']);
            unset($_SESSION['ADMIN_RIGHT_MARKETING']);
            unset($_SESSION['ADMIN_RIGHT_DATA']);
            unset($_SESSION['ADMIN_RIGHT_DESIGN']);
            unset($_SESSION['ADMIN_MOBILE']);
            // session_regenerate_id();
			session_destroy();
            return !$_SESSION['WALLETNO'] ? true : false;
        }

        public function isLogin()
        {
            return $this->get_login_userno() ? true : false;
        }

        public function checkLogin()
        {
            if (!$this->isLogin()) {
                $this->error('001',__('Please login.'));
            }
            // $_SESSION['USER_NO'] = '1'; // for testing
        }

        public function checkLoginAdmin()
        {
            if (!$_SESSION['ADMIN_ID']) {
                $this->error('001',__('Please login.'));
            }
        }

        public function check_admin_permission($mode)
        {
            if (!$_SESSION['ADMIN_RIGHT_'.strtoupper($mode)]) {
                $this->error('001',__('You do not have permission.'));
            }
        }

        public function get_login_userno()
        {
            return $_SESSION['USER_NO'];
        }

        public function get_login_userid()
        {
            return $_SESSION['USER_ID'];
        }

        // ----------------------------------------------------------------- //
        // trade - coin jsonrpc helper

        private $_coinds = array();

        /**
         * 암호화폐별 기본거래수수료 추출.
         * 최대 수수료로 send시 문제 없이 보낼수 있는 값이 들어 있습니다.
         */
        public function get_coin_txn_fee($symbol)
        {
            $coind = $this->load_coind($symbol);
            return $coind->coind->txn_fee;
        }

        /**
         * userno로 account 코드 생성 함수.
         *
         * 코인 노드에 여러 서비스를 이용할 경우를 대비해서 앱이름/실행환경/사용자번호 방식으로 account 코드를 생성합니다.
         * 단일 account일경우에는 서비스에서 지정한 account를 사용합니다.
         */
        public function get_account_by_userno($userno) {
            return __APP_NAME__.'/'.__API_RUNMODE__.'/'.$userno;
        }

        public function load_coind($symbol)
        {
            if($this->_coinds[$symbol]) {
                $coind = $this->_coinds[$symbol];
            } else {
                $coind = new Coind($symbol);
                $this->_coinds[$symbol] = $coind;
            }
            return $coind;
        }

        public function exists_coind_file($symbol) 
        {
            return file_exists(__DIR__.'/'.strtoupper($symbol)) ;
        }

        public function create_wallet($userno, $symbol)
        {
            $currency = $this->db_get_row('js_trade_currency', array('symbol'=>$symbol));
            if($currency->crypto_crrency=='Y' ) {
                if($this->exists_coind_file($symbol)) {
                    exit($symbol.'{symbol}의 PHP모듈이 없습니다. 생성해주세요.');
                    $this->error('055', str_replace('{symbol}', $symbol, __('{symbol}의 PHP모듈이 없습니다. 생성해주세요.')));
                }
                $coind = $this->load_coind($symbol);
                $r = $coind->genNewAddress($this->get_account_by_userno($userno), $userno);
                if($r===false && $coind->getError()) {
                    $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
                }
            } else {
                $r = $userno;
                if($this->exists_coind_file($symbol)) {
                    $coind = $this->load_coind($symbol);
                    $r = $coind->genNewAddress($this->get_account_by_userno($userno), $userno);
                }
            }
            return $r;
        }

        public function get_wallet_balance_total ($symbol)
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->getBalanaceTotal();
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            return $r;
        }

        public function get_wallet_balance ($symbol, $address, $account, $passwd='')
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->getBalanaceAddress($address, $account, $passwd);
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            return $r;
        }

        public function get_wallet_transaction_list ($symbol, $address, $userno, $count=100, $from=0, $fromid='')
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->getListTransactionAddress($address, $this->get_account_by_userno($userno), $count=100, $from=0, $fromid='');
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            $r = $this->fix_txn_status_code($r);
            return $r;
        }

        public function get_wallet_receive_list ($symbol, $address, $userno)
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->getListReceiveAddress($address, $this->get_account_by_userno($userno));
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            $r = $this->fix_txn_status_code($r);
            return $r;
        }

        public function get_wallet_transaction ($symbol, $txnid, $address, $account='')
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->getTransaction($txnid, $address, $account);
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            $r = $this->fix_txn_status_code($r);
            return $r;
        }

        public function get_transaction_fee ($symbol, $txnid, $address, $account='')
        {
            $r = $this->get_wallet_transaction ($symbol, $txnid, $address, $account);
            return $r->fee;
        }

        /**
         * txn 상태 코드를 거래소에 맞게 수정하는 메소드
         * @param array txn 데이터. 상태값을 포함한 테이터입니다. 상태 변수명은 status입니다.
         */
        public function fix_txn_status_code ($txns) {
            $is_object_param = false;
            if(!is_array($txns)) {
                $txns = array($txns);
                $is_object_param = true;
            }
            for($i=0; $i<count($txns); $i++) {
                $row = $txns[$i];
                $change_array = false;
                if(is_array($row)) { // row 형식이 array이면 object로 변경해 작업한 후 다시 array로 변경합니다.
                    $row = (object) $row;
                    $change_array = true;
                }
                if($row->status=='S') { // 상태값이 S인경우 거래소에서는 D로 바꿔 사용합니다.
                    $row->status = 'D';
                }
                if($change_array) {
                    $row = (array) $row;
                }
                $txns[$i] = $row;
            }
            if($is_object_param) {
                $txns = $txns[0];
            }
            return $txns;
        }

        /**
         * send coin
         * @param String Symbol
         * @param String sender address.
         * @param String Sender userno.
         * @param String Receiver address.
         * @param Number Amount of coin.
         * @param Number Amount of fee. - Not working.
         * @return String Transaction ID.
         */
        public function send_coin ($symbol, $from_address, $userno, $to_address, $amount, $fee, $msg='', $passwd='')
        {
            $fee *= 1;
            $amount *= 1;
            if($amount<=0) {
                return false;
            }
            $coind = $this->load_coind($symbol);
            $from_account = $this->get_account_by_userno($userno);
            $r = $coind->send($from_address, $from_account, $to_address, $amount, $fee, $msg, $passwd);
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            return $r;
        }

        public function validate_address ($symbol, $address)
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->validateAddress($address);
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
            }
            return $r;
        }

        // ----------------------------------------------------------------- //
        // trade - utils

        public function send_sms($tran_phone, $tran_msg) {
            $code = $this->query_one(" SELECT code FROM js_config_site WHERE domain='".$this->escape($_SERVER['HTTP_HOST'])."' ");
            $row = $this->query_fetch_array("SELECT tran_callback, guest_no, guest_key FROM js_config_sms WHERE code='{$this->escape($code)}'");
            // 글로벌용 twilio.com 사용 코드
            $row['tran_callback'] = str_replace('-','',$row['tran_callback']);
            $tran_phone = preg_replace('/[^0-9\+]/', '', $tran_phone);
            if(!$tran_phone || !$tran_msg) {
                return false;
            }
            $guest_no = $row['guest_no'];
            $guest_key = $row['guest_key'];
            $tran_callback = $row['tran_callback'];
            $tran_date = '0'; // 예약발송일때 사용.
            // $tran_msg = $tran_msg;
            // Your Account SID and Auth Token from twilio.com/console
            $client = new Client($guest_no, $guest_key);
            // Use the client to do fun stuff like send text messages!
            $send_result = '';
            if(__API_RUNMODE__=='live'){
                try {
                    $call = $client->messages->create(
                        // the number you'd like to send the message to
                        $tran_phone,
                        array(
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => $tran_callback, //'+18782052142',
                            // the body of the text message you'd like to send
                            'body' => $tran_msg
                        )
                    );
                    $send_result = $call->sid;
                } catch (Exception $e) {}
                if(!$send_result) {
                    return false;
                }
            }
            $sql = " INSERT INTO js_sms SET ";
            $sql.= " tran_phone='".$this->escape($tran_phone)."', ";
            $sql.= " tran_callback='".$this->escape($tran_callback)."', ";
            $sql.= " tran_date='".$this->escape($tran_date)."', ";
            $sql.= " tran_msg='".$this->escape($tran_msg)."', ";
            $sql.= " tran_result='".$this->escape($send_result)."', ";
            $sql.= " regdate=UNIX_TIMESTAMP() ";
            return $this->query($sql);
        }

        // get price to quote price
        public function get_quote_price ($price, $exchange='KRW') {
            $unit_price = $this->get_quote_unit($price, $exchange);
            return floor($price / $unit_price) * $unit_price;
        }

        public function get_quote_digit ($price, $exchange='KRW') {
            $quote_unit = $this->get_quote_unit ($price, $exchange);
            $quote_unit = $this->numtostr($quote_unit);
            $r = 0;
            if($quote_unit<1) {
                $r = strlen(str_replace('0.','',$quote_unit));
            }
            return $r;
        }

        public function get_base_price($symbol) {
            $symbol = strtolower($symbol);
            $prev_avg_price = $this->query_one("SELECT ROUND(SUM(volume * price) / SUM(volume)) prev_avg_price FROM `js_trade_{$symbol}krw_txn` FORCE INDEX(time_traded) WHERE time_traded LIKE CONCAT((SELECT DATE(MAX(time_traded)) FROM `js_trade_{$symbol}krw_txn` FORCE INDEX(time_traded)  WHERE time_traded < DATE(NOW())),'%')");
            if($prev_avg_price <= 0) {
                $prev_avg_price = $this->query_one("SELECT price_close FROM `js_trade_price` WHERE symbol='{$this->escape($symbol)}'");
            }
            return $prev_avg_price;
        }
        public function get_trade_price_info($symbol) {
            $base_price = $this->get_base_price($symbol);
            $trade_max_price = floor($base_price * (1 + 0.3));
            $trade_min_price = ceil($base_price * (1 - 0.3));
            $trade_min_price = $trade_min_price<1 ? 1 : $trade_min_price;
            return array('base_price'=>$base_price, 'trade_max_price'=> $trade_max_price, 'trade_min_price'=> $trade_min_price );
        }
    
        /**
         * calculate quote price unit
         * https://www.miraeassetdaewoo.com/hki/hki3061/n94.do - 일본, 홍콩, 싱가폴, 미국, 한국 호가
         */
        public function get_quote_unit ($price, $exchange='KRW') {
            $price = $price ? $price : '0';
            $exchange = $exchange ? strtoupper($exchange) : 'KRW';
            switch ($exchange) {
                case 'USD':
                    if ($price >= 1) {
                        return 0.01;
                    } else if ($price < 1 && $price >= 0.01) {
                        return 0.001;
                    } else if ($price < 0.01 && $price >= 0.001) {
                        return 0.0001;
                    } else {
                        return 0.00001;
                    }
                    break;
                case 'SGD':
                    if ($price >= 10) {
                        return 0.02;
                    } else if ($price < 10 && $price > 1) {
                        return 0.01;
                    } else if ($price <= 1) {
                        return 0.005;
                    }
                    break;
                case 'JPY':
                    if ($price > 50000000) {
                        return 100000;
                    } else if ($price <= 50000000 && $price > 30000000) {
                        return 50000;
                    } else if ($price <= 30000000 && $price > 5000000) {
                        return 10000;
                    } else if ($price <= 5000000 && $price > 3000000) {
                        return 5000;
                    } else if ($price <= 3000000 && $price > 500000) {
                        return 1000;
                    } else if ($price <= 500000 && $price > 300000) {
                        return 500;
                    } else if ($price <= 300000 && $price > 50000) {
                        return 100;
                    } else if ($price <= 50000 && $price > 30000) {
                        return 50;
                    } else if ($price <= 30000 && $price > 5000) {
                        return 10;
                    } else if ($price <= 5000 && $price > 3000) {
                        return 5;
                    } else if ($price <= 3000) {
                        return 1;
                    }
                    break;
                case 'KRW':
                    if ($price >= 1000000) {
                        return 10000;
                    } else if ($price < 1000000 && $price >= 100000) {
                        return 1000;
                    } else if ($price < 100000 && $price >= 10000) {
                        return 100;
                    } else if ($price < 10000 && $price >= 1000) {
                        return 10;
                    } else if ($price < 1000 && $price >= 100) {
                        return 1;
                    } else if ($price < 100 && $price >= 10) {
                        return 0.1;
                    } else {
                        return 0.01;
                    }
                    break;
                default:
                    return 0.0001;
                    break;
            }

        }


        // ----------------------------------------------------------------- //
        // Model

        public function get_admin_phone_number($adminid='sms') {
            return $this->query_one("SELECT admin_mobile FROM js_admin WHERE adminid='{$this->escape($adminid)}' ");
        }

        /**
         * 지갑 관련 앱 버전 정보 조회
         * @param String $device 앱 디바이스 정보.
         * @param String $service 서비스 종류. trade: 거래소, exchange:교환지갑앱,
         * @return Object 버전정보.
         */
        public function get_version($device, $service='trade') {
			if(in_array($service, array('trade', 'exchange', 'auction'))) {
				$table_name = 'js_'.$service.'_app_verison';
			} else {
				$table_name = 'js_trade_app_verison';
			}
            $sql = "select version, version_min, note from {$table_name} where device='".$this->escape($device)."' ";
            return $this->query_fetch_object($sql);
        }

        /**
         * 전자지갑 정보 조회
         * @param Number 회원번호
         * @param Symbol 종목코드(BTC, LTC, KRW)
         * @return Object 전자지갑 정보. userno, symbol, confirmed, unconfirmed, address, regdate
         */
        public function get_wallet($userno, $symbol)
        {
            $sql = 'SELECT t1.userno, t1.symbol, t1.confirmed, t1.unconfirmed, IF(t2.creatable="Y", t1.address, t2.backup_address) address, t1.regdate, t1.locked, t2.name, t2.icon_url, t1.bool_sell, t1.bool_buy, t1.bool_withdraw
            FROM js_exchange_wallet t1 LEFT JOIN js_trade_currency t2 ON t1.symbol=t2.symbol
            WHERE t1.userno='.$this->escape($userno).' ';
            if($symbol!='' && strtolower($symbol)!='all') {
                $sql.= ' and t1.symbol="'.strtoupper($this->escape($symbol)).'"';
            }
            return $this->query_list_object($sql);
        }
        public function get_wallet_by_address($address, $symbol)
        {
            $sql = "SELECT t1.userno, t1.symbol, t1.confirmed, t1.unconfirmed, IF(t2.creatable='Y', t1.address, t2.backup_address) address, t1.regdate FROM js_exchange_wallet t1 LEFT JOIN js_trade_currency t2 ON t1.symbol=t2.symbol WHERE t1.address='{$this->escape($address)}' AND t1.symbol='".strtoupper($this->escape($symbol))."' ";
            // exit($sql);
            return $this->query_fetch_object($sql);
        }


        public function get_balance($userno, $symbol)
        {
            $sql = "SELECT confirmed FROM js_exchange_wallet WHERE userno={$this->escape($userno)} AND symbol='{$this->escape(strtoupper($symbol))}' ";
            return $this->query_one($sql);
        }

        public function get_list_wallet($userno, $symbol='')
        {
            $sql = 'select userno, symbol, confirmed, unconfirmed, address, regdate from js_exchange_wallet where userno='.$this->escape($userno).'';
            if($symbol!='') {
                $sql.= ' and symbol="'. strtoupper($this->escape($symbol)). '" ';
            }
            return $this->query_list_object($sql);
        }

        /**
         * get wallet check deposit
         * @param String $symbopl search coin symbol.
         * @param Number $rows Number of rows on a page. default value is 50.
         * @param Array The array value containing the wallet object.
         */
        public function get_wallet_check_deposit($symbol, $rows=50) {
            $sql = "SELECT userno, symbol, account, address from js_exchange_wallet WHERE symbol in (select symbol from js_trade_currency where active='Y' and check_deposit='Y') and address<>'' and  deposit_check_time < (UNIX_TIMESTAMP()-10) ";
            if($symbol!='') {
                $sql.= "AND symbol='".$this->escape($symbol)."' ";
            }
            $sql.= "ORDER BY deposit_check_time, regdate desc LIMIT 0, ".$this->escape($rows)."";
            return $this->query_list_object($sql);
        }

        /**
         * find wallet transaction pool
         * @param Number $userno user number
         * @param Boolean Query results.
         */
        public function update_check_deposit_time($userno, $symbol) {
            $sql = "UPDATE js_exchange_wallet SET deposit_check_time=UNIX_TIMESTAMP() WHERE userno='".$this->escape($userno)."' AND symbol='".$this->escape($symbol)."' ";
            return $this->query($sql);
        }

        public function add_wallet($userno, $symbol, $amount)
        {
            $amount = $this->numtostr($amount); // 4.0E-5 처럼 들어오는 숫자를 0.00004 처럼 숫자형 문자열로 변환함.
            if(empty($amount)) {
                return true; // 0은 오류처리 없이 넘긴다.
            }
            if(preg_match('/[^\-\+0-9.]/', $amount) ) {
                $this->error('002', '[amount] '.__('Please enter the number.'));
            }
            // wallet 있는지 확인. 없으면 생성하기.
            $wallet = $this->get_wallet($userno, $symbol);
            if(isset($wallet[0]->userno)) {
                $sql = 'update js_exchange_wallet set ';
                if($amount>=0) {
                    $sql.= 'confirmed=confirmed + '.$this->escape($amount).' ';
                } else {
                    $sql.= 'confirmed=confirmed - '.$this->escape($amount*-1).' ';
                }
                $sql.= 'where userno='.$this->escape($userno).' and symbol="'.strtoupper($this->escape($symbol)).'"';
                $this->write_log("[add_wallet] sql:{$sql}, $userno, $symbol, $amount, before: ".$this->get_balance($userno, $symbol));
                return $this->query($sql);
            } else {
                return $this->save_wallet($userno, $symbol, '', $amount);
            }
        }

        public function del_wallet($userno, $symbol, $amount)
        {
            $amount = $this->numtostr($amount); // 4.0E-5 처럼 들어오는 숫자를 0.00004 처럼 숫자형 문자열로 변환함.
            if(empty($amount)) {
                return true; // 0은 오류처리 없이 넘긴다.
            }
            if(preg_match('/[^\-\+0-9.]/', $amount) ) {
                $this->error('002', '[amount] '.__('Please enter the number.').$userno. $symbol. $amount);
            }
            $sql = 'update js_exchange_wallet set ';
            $sql.= 'confirmed=confirmed - '.$this->escape($amount).' ';
            $sql.= 'where userno='.$this->escape($userno).' and symbol="'.strtoupper($this->escape($symbol)).'"';
            $this->write_log("[del_wallet] sql:{$sql}, $userno, $symbol, $amount, before: ".$this->get_balance($userno, $symbol));
            return $this->query($sql);
        }

        public function save_wallet($userno, $symbol, $address, $confirmed=0) {
            $sql = 'insert into js_exchange_wallet set userno='.$this->escape($userno).', symbol="'.strtoupper($this->escape($symbol)).'", regdate=SYSDATE(), confirmed='.$this->escape($confirmed).', address="'.$this->escape($address).'" ON DUPLICATE KEY UPDATE address="'.$this->escape($address).'" ';
            $this->write_log("[save_wallet] sql:{$sql}, $userno, $symbol, $address, $confirmed");
            return $this->query($sql);
        }

        public function gen_wallet($userno, $symbol) {
            $address = $this->create_wallet($userno, $symbol);
            return $this->save_wallet($userno, $symbol, $address, 0);
        }

        public function active_wallet($userno, $symbol)
        {
            $sql = "UPDATE js_exchange_wallet SET active='Y' WHERE userno='{$this->escape($userno)}' AND symbol='{$this->escape($symbol)}' ";
            return $this->query($sql);
        }

        public function inactive_wallet($userno, $symbol)
        {
            $sql = "UPDATE js_exchange_wallet SET active='N' WHERE userno='{$this->escape($userno)}' AND symbol='{$this->escape($symbol)}' ";
            return $this->query($sql);
        }

        public function delete_wallet($userno, $symbol)
        {
            return $this->inactive_wallet($userno, $symbol);
        }

        /**
         * 새 거래소용 지갑을 생성합니다.
         * create_wallet 은 블록체인에 지갑을 생성시키는 메소드고
         * gen_wallet을 create_wallet + save_wallet 메소드고
         * create_new_wallet은 기분화폐를 반영해서 생성하는 메소드입니다.
         * @param Number $userno 회원번호
         * @param String $symbol 화폐심볼 
         * @return String 주소
         */
        public function create_new_trade_wallet($userno, $symbol) {
            $address = $this->create_wallet($userno, $symbol);
            $this->save_wallet($userno, $symbol, $address);
            $this->active_wallet($userno, $symbol);
            return $address;
        }

        public function get_wallet_txn_list($symbol, $userno, $page=1, $rows=10, $txnid='0') {
            if($txnid>0) {
                $page=1;
            }
            $sn = ($page-1) * $rows;

            // from & where query
            $sql = "FROM js_exchange_wallet_txn FORCE INDEX(PRIMARY) WHERE symbol='".strtoupper($this->escape($symbol))."' AND userno='".$this->escape($userno)."' AND txn_type NOT IN ('B') ";
            if($txnid>0) {
                $sql.= " AND txnid < '$txnid' ";
            }

            // total cnt
            $total_cnt = $this->query_one(" SELECT COUNT(*) " . $sql);
            // row
            $sql = " SELECT txnid, symbol, unix_timestamp(txndate) `time`, unix_timestamp(regdate) `regtime`, IF(txn_type='R', address_relative, address) from_address, IF(txn_type='R', address, address_relative) to_address, if(txn_type='R' OR txn_type='D', 'in', 'out') direction, amount, fee, status, key_relative, '{$total_cnt}' as tot_cnt " . $sql . " ORDER BY txnid DESC LIMIT ".$this->escape($sn).", ".$this->escape($rows)."";
            // var_dump($sql);
            $r = $this->query_list_object($sql);
            $currency = $this->get_currency($symbol);
            $currency = isset($currency[0]) ? $currency[0] : (object) array('transaction_outlink'=>false);
            for($i=0; $i<count($r); $i++) {
                $r[$i]->transaction_outlink = '';
                if($currency->transaction_outlink) {
                    $r[$i]->transaction_outlink = str_replace('$key_relative', $r[$i]->key_relative, $currency->transaction_outlink);
                }
            }
            return $r;
        }

        public function get_receive_volume($symbol, $userno) {
            $sql = "SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno='{$this->escape($userno)}' AND symbol='{$this->escape(strtoupper($symbol))}' AND txn_type='R' AND status='D' ";
            return $this->query_one($sql) * 1;
        }

        /**
         * find wallet transaction
         * @param Array $search search items by array type. key: columne name, value: search value.
         * @param Number $page search page. default value is 1.
         * @param Number $rows Number of rows on a page. default value is 10.
         */
        public function find_wallet_txn_list($search=array(), $page=1, $rows=10) {
            $sn = ($page-1) * $rows;
            $sql = "SELECT txnid, symbol, address, regdate, txndate, address_relative, txn_type, amount, fee, tax, status, key_relative FROM js_exchange_wallet_txn WHERE 1 ";
            foreach($search as $key => $val) {
                if(strpos($val, ',')!==false) {
                    $val = explode(',', $val);
                    $t = array();
                    foreach($val as $row) {
                        $t[] = $this->escape($row);
                    }
                    $sql.= " AND $key in ('".implode("','", $t)."') ";
                } else {
                    $sql.= " AND $key='".$this->escape($val)."' ";
                }
            }
            $sql.= " LIMIT ".$this->escape($sn).", ".$this->escape($rows)."";
            return $this->query_list_object($sql);
        }

        /**
         * find unsended wallet transaction
         * @param Number $rows Number of rows on a page. default value is 10.
         */
        public function find_unsended_txn_list($symbol, $rows='10') {
            // $sql = "SELECT SUBSTR(reg_time, 1, 10) reg_time, reg_time reg_time_origin, symbol, txnid, sender_address, sender_wallet_no, receiver_address, receiver_wallet_no, txn_type, amount, fee, `message` FROM js_exchange_wallet_txn WHERE symbol='{$this->escape($symbol)}' AND txnid='' AND txn_type='S' AND check_time<".(time()-60)." ORDER BY reg_time LIMIT 0, ".$this->escape($rows)."";
            $sql = "SELECT UNIX_TIMESTAMP(regdate) reg_time, UNIX_TIMESTAMP(regdate) reg_time_origin, symbol, txnid, address sender_address, userno, address_relative receiver_address, '' receiver_wallet_no, txn_type, amount, fee, msg `message`
			FROM js_exchange_wallet_txn WHERE symbol='{$this->escape($symbol)}' AND key_relative='' AND txn_type='S' AND direction='O' AND `status`='O' ORDER BY reg_time LIMIT 0, ".$this->escape($rows)."";
            return $this->query_list_object($sql);
        }

        /**
         * add wallet transaction
         *
         * (참고) 개발시 js_exchange_wallet_txn.userno 값이 없는 경우 아래 쿼리로 강제로 등록 가능.
         * UPDATE js_exchange_wallet_txn t1 SET userno=(SELECT userno FROM js_exchange_wallet WHERE symbol=t1.symbol AND address=t1.address LIMIT 1 )
         *
         * @param Number 회원번호
         * @param string receiver address
         * @param string coin symbol
         * @param string sender address
         * @param string 트렌젝션 종류. I:입금, O:출금, B:구매, S:판매
         * @param string amount
         * @param string fee
         * @param string tax
         * @param string status. O: 준비중, P: 팬딩, T: 처리중, C: 종료
         * @param string key_relative. 매수/매도와 관련된 테이블과 orderid값. ex) js_trade_btckrw_order.orderid, 또는 입금/출금 트랜젝션 아이디.
         * @param string transaction datetime. YYYY-MM-DD HH:ii:ss
         */
        public function add_wallet_txn($userno, $address, $symbol, $address_relative, $txn_type, $direction, $amount, $fee, $tax, $status="O", $key_relative="", $txndate='') {
            $fee = trim( preg_replace('/[^0-9\-.]/','',$fee));
            $fee = $fee=='' ? '0' : $fee;
            $tax = trim( preg_replace('/[^0-9\-.]/','',$tax));
            $tax = $tax=='' ? '0' : $tax;
            $amount = trim( preg_replace('/[^0-9\-.]/','',$amount));
            $amount = $amount=='' ? '0' : $amount;
            $txndate = trim( $txndate );
            $txndate = $txndate=='' ? date('Y-m-d H:i:s') : $txndate;

            $sql = "insert into js_exchange_wallet_txn set ";
            // txnid = bigint(20) auto-increadable
            $sql.= " userno='".strtoupper($this->escape($userno))."', ";
            $sql.= " symbol='".strtoupper($this->escape($symbol))."', ";
            $sql.= " address='".$this->escape($address)."', ";
            $sql.= " regdate=sysdate(), ";
            $sql.= " txndate='".$this->escape($txndate)."', ";
            $sql.= " address_relative='".$this->escape($address_relative)."', ";
            $sql.= " txn_type='".$this->escape($txn_type)."', ";
            $sql.= " direction='".$this->escape($direction)."', ";
            $sql.= " amount='".$this->escape($amount)."', ";
            $sql.= " fee='".$this->escape($fee)."', ";
            $sql.= " tax='".$this->escape($tax)."', ";
            $sql.= " status='".$this->escape($status)."', ";
            $sql.= " key_relative='".$this->escape($key_relative)."' ";
            $this->write_log("[add_wallet_txn] sql:{$sql}, $userno, $address, $symbol, $address_relative, $txn_type, $amount, $fee, $tax, $status, $key_relative, $txndate");
            return $this->query($sql);
        }

        public function update_wallet_txn($txnid, $address, $symbol, $address_relative, $txn_type, $amount, $fee, $tax, $status, $key_relative, $txndate) {
            $sql = "UPDATE js_exchange_wallet_txn SET ";
            $sql.= " txndate='".$this->escape($txndate)."', ";
            $sql.= " address_relative='".$this->escape($address_relative)."', ";
            $sql.= " txn_type='".$this->escape($txn_type)."', ";
            $sql.= " amount=".$this->escape($amount).", ";
            $sql.= " fee=".$this->escape($fee).", ";
            $sql.= " tax=".$this->escape($tax).", ";
            $sql.= " status='".$this->escape($status)."' ";
            $sql.= " WHERE ";
            $sql.= " txnid='".$this->escape($txnid)."' ";
            // $sql.= " symbol='".strtoupper($this->escape($symbol))."' and ";
            // $sql.= " address='".$this->escape($address)."' and ";
            // $sql.= " key_relative='".$this->escape($key_relative)."' ";
            $this->write_log("[update_wallet_txn] sql:{$sql}, $txnid, $address, $symbol, $address_relative, $txn_type, $amount, $fee, $tax, $status, $key_relative, $txndate");
            return $this->query($sql);
        }


        public function get_fee($symbol, $action) {
            $sql = "select fee_in, fee_out, fee_buy_ratio, fee_sell_ratio from js_trade_currency where symbol='".strtoupper($this->escape($symbol))."' ";
            $currency = $this->query_fetch_object($sql);
            $out = array( 'action'=>'withdraw', 'fee'=>$currency->fee_out, 'unit_type'=>'fixed' );
            $in = array( 'action'=>'receive', 'fee'=>$currency->fee_in, 'unit_type'=>'fixed' );
            $buy = array( 'action'=>'buy', 'fee'=>$currency->fee_buy_ratio, 'unit_type'=>'ratio' );
            $sell = array( 'action'=>'sell', 'fee'=>$currency->fee_sell_ratio, 'unit_type'=>'ratio' );
            $fee = array($out, $in, $buy, $sell);
            switch($action) {
                case 'withdraw': $fee = array($out); break;
                case 'receive': $fee = array($in); break;
                case 'buy': $fee = array($buy); break;
                case 'sell': $fee = array($sell); break;
            }
            return $fee;
        }

        public function cal_fee($symbol, $action, $amount) {
            $sql = "select fee_in, fee_out, fee_out_ratio, fee_buy_ratio, fee_sell_ratio, display_decimals from js_trade_currency where symbol='".strtoupper($this->escape($symbol))."' ";
            $currency = $this->query_fetch_object($sql);
            $fee = 0;
            switch($action) {
                case 'withdraw': $fee = $currency->fee_out_ratio>0 ? ceil($currency->fee_out_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals) : $currency->fee_out*1; break;
                case 'receive': $fee = $currency->fee_in*1; break;
                case 'buy': $fee = ceil($currency->fee_buy_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals); break;
                case 'sell': $fee = ceil($currency->fee_sell_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals); break;
            }
            return $fee;
        }

        public function cal_tax($symbol, $action, $amount) {
            $sql = "select tax_in_ratio, tax_out_ratio, tax_buy_ratio, tax_sell_ratio, display_decimals from js_trade_currency where symbol='".strtoupper($this->escape($symbol))."' ";
            $currency = $this->query_fetch_object($sql);
            $tax = 0;
            switch($action) {
                case 'withdraw': $tax = $currency->tax_out; break;
                case 'receive': $tax = $currency->tax_in_ratio; break;
                case 'buy': $tax = ceil($currency->tax_buy_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals); break;
                case 'sell': $tax = ceil($currency->tax_cell_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals); break; // income tax는 여기서 말고 따로 계산하기.
            }
            return $tax;
        }

        public function get_currency($symbol='',$crypto_currency='', $name='') {
            $sql = "SELECT *, circulating_supply*price market_cap ";
            $sql.= " FROM js_trade_currency WHERE active='Y' AND symbol<>'' ";
            if($symbol!='') {
                $sql .= " AND symbol='".strtoupper($this->escape($symbol))."' ";
            }
            if($crypto_currency!='') {
                $sql .= " AND crypto_currency='".($crypto_currency=='Y'?'Y':'N')."' ";
            }
            if($name!='') {
                $sql .= " AND name LIKE '%{$this->escape($name)}%' ";
            }
            $sql .= " ORDER BY sortno";
            // var_dump($sql); exit;
            return $this->query_list_object($sql);
        }

        public function get_symbol() {
            $sql = "select symbol, name, regdate, color, icon_url from js_trade_currency where active='Y' and menu='Y' order by sortno ";
            return $this->query_list_object($sql);
        }

        public function get_last_price($symbol='', $exchange='KRW') {
            $symbol = strtoupper($this->escape($symbol));
            $exchange = strtoupper($this->escape($exchange));
            $sql = "select price_close from js_trade_price where symbol='{$symbol}' and exchange='{$exchange}' ";
            $r = $this->query_fetch_object($sql);
            return $r->price_close;
        }

        /**
         * 환율 구하는 메소드
         * price로 사용되는 기본 거래포인트(USD와 1:1)에서 다른 화폐단위로 변환하기위한 환율을 구합니다.
         *
         * 예를들어 SCC 거래 가격을 BTC로 볼때는 아래처럼 환율을 구한후 가격(price)에 곱해줍니다.
         * $exchange_rate = $this->get_point_exchange_rate('SCC', 'BTC');
         * echo "price: ".($price * $exchange_rate);
         */
        public function get_point_exchange_rate($symbol, $exchange='KRW') {
            $exchange_rate = 1;
            if($exchange!='KRW') {
                $exchange_rate = $this->get_last_price($symbol, 'KRW');
            }
            return 1/$exchange_rate;
        }

        /**
         * 현재가 구하는 메소드
         *
         */
        public function get_spot_price($symbol='', $exchange='KRW') {
            if(is_array($symbol)) {
                $symbol = implode("','", $symbol);
            } else {
                $symbol = trim($symbol);
            }
            // $sql = "select symbol, volume, price_open, price_close, price_high, price_low, price_open_12, price_close_12, price_high_12, price_low_12, price_open_1, price_close_1, price_high_1, price_low_1 from js_trade_price where 1 and symbol in (select symbol from js_trade_currency where active='Y' and tradable='Y') ";
            $sql = " SELECT t1.symbol, t1.volume, t1.price_open, t1.price_close, t1.price_high, t1.price_low, t1.price_open_12, t1.price_close_12, t1.price_high_12, t1.price_low_12, t1.price_open_1, t1.price_close_1, t1.price_high_1, t1.price_low_1, t2.icon_url, t2.name, t2.circulating_supply*t2.price market_cap";
            $sql.= " FROM js_trade_price AS t1 LEFT JOIN js_trade_currency AS t2 ON t1.symbol=t2.symbol ";
            $sql.= " WHERE 1 AND t2.active='Y' AND t2.tradable='Y' ";

            if($symbol!='') {       
                $sql .= " and t1.symbol in ('".strtoupper($symbol)."') ";
            }
            if($exchange!='') {
                $sql .= " and t1.exchange='".strtoupper($this->escape($exchange))."' ";
            }
            // var_dump($sql);exit;
            $r = $this->query_list_object($sql);
            for($i=0 ; $i<count($r) ; $i++) {
                $row = $r[$i];
                $price = $row->price_close ? $row->price_close : 0;
                $digit = $this->get_quote_digit($price, $exchange);
                $r[$i]->volume = number_format($row->volume, 2, '.', '');
                $r[$i]->price_open = number_format($row->price_open, $digit, '.', '');
                $r[$i]->price_close = number_format($row->price_close, $digit, '.', '');
                $r[$i]->price_high = number_format($row->price_high, $digit, '.', '');
                $r[$i]->price_low = number_format($row->price_low, $digit, '.', '');
            }
            return $r;
        }

        /**
         * 호가 데이터
         * 매도 10개, 매수 10개 로 구분해서 쿼리 날려 데이터 뽑아 옵니다.
         * @param String $symbol coin symbol
         * @param String $exchange exchange symbol
         * @param String $trading_type 매매형식. empty: 전체호가, buy: 구매호가, sell: 판매호가
         * @param Number $cnt 갯수. buy나 sell 각각의 호가 데이터 갯수. 10이면 buy호가 10개 sell호가 10개를 리턴합니다.
         */
        public function get_quote_list($symbol, $exchange, $trading_type='', $cnt=10) {
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);

            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_quote';
            // $table = 'js_trade_'.strtolower($symbol).'krw_quote';
            // sell
            $sell = array();
            if($trading_type=='' || $trading_type=='sell') {
                $sql = "select '{$symbol}' symbol, '{$exchange}' exchange, case when trading_type='B' then 'buy' else 'sell' end as trading_type, volume, price from {$table} where trading_type='S' order by price  limit {$cnt}";
                $sell = $this->query_list_object($sql);
                for($i=0 ; $i<count($sell) ; $i++) {
                    $sell[$i]->volume = number_format($sell[$i]->volume, 5, '.', '');
                    $digit = $this->get_quote_digit($sell[$i]->price, $exchange);
                    $sell[$i]->price = number_format($sell[$i]->price, $digit, '.', '');
                }
                $sell = array_reverse($sell); // 높은 가격이 위로 가도록 재정렬.
            }
            // buy
            $buy = array();
            if($trading_type=='' || $trading_type=='buy') {
                $sql = "select '{$symbol}' symbol, '{$exchange}' exchange, case when trading_type='B' then 'buy' else 'sell' end as trading_type, volume, price from {$table} where trading_type='B' order by price desc limit {$cnt}";
                $buy = $this->query_list_object($sql);
                for($i=0 ; $i<count($buy) ; $i++) {
                    $buy[$i]->volume = number_format($buy[$i]->volume, 5, '.', '');
                    $digit = $this->get_quote_digit($buy[$i]->price, $exchange);
                    $buy[$i]->price = number_format($buy[$i]->price, $digit, '.', '');
                }
            }
            return array_merge($sell, $buy); // 높은가격->낮은가격 순서로 정렬했기때문에 합치기만 해서 리턴함.
        }

        public function get_trading_list($symbol, $exchange, $page=1, $rows=20, $txnid='0') {
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);

            if($txnid>0) {
                $page = 1;
            }
            $sn = ($page-1) * $rows;
            $table_txn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn';
            $table_order = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            // $table = 'js_trade_'.strtolower($symbol).'krw_txn';

            // 1. where 조건에 time_traded 추가하기. 그래서 인덱스를 타도록 하기.
            // 1-1 검색 시작 데이터의 날짜 - 현제페이지 첫번째 row의 time_traded 가져오기.
            $sql = "SELECT txnid FROM {$table_txn} ORDER BY txnid DESC LIMIT  ".(($page-1) * $rows).", 1";
            $start_txnid = $this->query_one($sql);

            // 1-2 검색 종료 데이터의 날짜 - 다음페이지 첫번째 row의 time_traded 가져오기.
            $sql = "SELECT txnid FROM {$table_txn} ORDER BY txnid DESC LIMIT  ".(($page) * $rows).", 1";
            $end_txnid = $this->query_one($sql);

            // 2. sub 쿼리 분리하기. (SELECT COUNT(volume) FROM js_trade_btckrw_txn)
            $sql = "SELECT COUNT(volume) cnt FROM {$table_txn}";
            $cnt = $this->query_one($sql);
            $cnt = $cnt>0 ? $cnt : 0;

            $sql = " SELECT txnid, UNIX_TIMESTAMP(time_traded) AS time_traded, volume, '{$symbol}' AS symbol, price, '{$exchange}' AS exchange, '{$cnt}' as tot_cnt, price_updown FROM {$table_txn} WHERE 1 ";
            if($txnid>0) {
                $sql.= " AND txnid < $txnid ";
            }
            if($start_txnid) {
                $sql.= " AND txnid <= '".$this->escape($start_txnid)."' ";
            }
            if($end_txnid) {
                $sql.= " AND txnid >= '".$this->escape($end_txnid)."' ";
            }
            //$sql.= "ORDER BY time_traded DESC LIMIT ".$this->escape($sn).", ".$this->escape($rows)."";
            $sql.= "ORDER BY txnid DESC LIMIT ".$this->escape($rows)."";
            $r = $this->query_list_object($sql);
            for($i=0 ; $i<count($r) ; $i++) {
                $r[$i]->volume = number_format($r[$i]->volume, 5, '.', '');
                $digit = $this->get_quote_digit($r[$i]->price, $exchange);
                $r[$i]->price = number_format($r[$i]->price, $digit, '.', '');
            }
            return $r;
        }

        public function set_default_exchange () {
            $default_exchange = $this->query_one("SELECT exchange FROM js_config_site WHERE active='1' AND domain='".$this->escape($_SERVER['HTTP_HOST'])."' ");
            $this->default_exchange = $default_exchange ? $default_exchange : $this->default_exchange;
        }

        /**
         * 종합분석(손익현황)
         * @param number 회원번호
         * @param String 검색 시작 날짜
         * @param String 검색 종료 날짜
         * @return Ojbect 종합 분석 현황
         */
        public function get_my_revenue_status($userno, $from_date, $to_date) {
            $r = (object) array(
                'total_buy_calculated'=>'',
                'total_sell_calculated'=>'',
                'total_profit'=>'',
                'total_profit_percent'=>'',
                'withdrawable_amount'=>'',
                'orderable_amount'=>'',
                'total_deposit'=>'',
                'total_withdraw'=>'',
                'detail'=>array()
            );
            $userno = preg_replace('/[^0-9]/', '', $userno);
            if($userno) {

                // withdrawable_amount - 출금 가능한 원화
                $r->withdrawable_amount = $this->query_one("SELECT confirmed balance FROM js_exchange_wallet WHERE symbol='{$this->escape($this->default_exchange)}' AND userno='{$this->escape($userno)}' ");
                // withdrawable_amount - 주문 가능한 원화
                $r->orderable_amount = $r->withdrawable_amount;

                // 화폐 가져오기.
                $sql = "SELECT * FROM js_trade_currency WHERE active='Y' ";
                $currency = $this->query_list_object($sql);

                // 화폐별로 상세데이터 만들기.
                foreach($currency as $c) {
                    if($c->crypto_currency!='Y') {
                        continue;
                    }
                    $item = (object) array(
                        'symbol'=>$c->symbol,
                        'name'=>$c->name,
                        'start_balance' => '',
                        'start_price' => '',
                        'end_balance' => '',
                        'end_price' => '',
                        'profit' => '',
                        'deposit' => '',
                        'withdraw' => ''
                    );
                    // 입금액
                    $item->deposit = $this->query_one("SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno='{$this->escape($userno)}' AND symbol='{$this->escape(strtoupper($c->symbol))}' AND txn_type='R' AND txndate>='{$this->escape($from_date)} 00:00:00' AND txndate<='{$this->escape($to_date)} 23:59:59' ");
                    // total_deposit
                    $r->total_deposit += $item->deposit * $c->price;
                    // 출금액
                    $item->withdraw = $this->query_one("SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno='{$this->escape($userno)}' AND symbol='{$this->escape(strtoupper($c->symbol))}' AND txn_type='W' AND txndate>='{$this->escape($from_date)} 00:00:00' AND txndate<='{$this->escape($to_date)} 23:59:59' ");
                    // total_withdraw
                    $r->total_withdraw += $item->withdraw * $c->price;
                    // start_balance = 시작날까지 매수한 수량 - 시작날까지 매도한 수량 + 시작날까지 입금한 수량 - 시작날까지 출금한 수량
                    $item->start_balance = $this->query_one("SELECT
                    (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno=2 AND symbol='{$this->escape(strtoupper($c->symbol))}' AND txn_type='R' AND txndate<='{$this->escape($to_date)} 23:59:59')
                    - (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno=2 AND symbol='{$this->escape(strtoupper($c->symbol))}' AND txn_type='S' AND txndate<='{$this->escape($to_date)} 23:59:59')
                    + (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_order WHERE userno='{$this->escape($userno)}' AND trading_type='B' AND STATUS='D' AND time_traded<='{$this->escape($to_date)} 23:59:59')
                    - (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_order WHERE userno='{$this->escape($userno)}' AND trading_type='S' AND STATUS='D' AND time_traded<='{$this->escape($to_date)} 23:59:59')
                    AS balance");
                    // end_balance = 종료날까지 매수한 수량 - 종료날까지 매도한 수량 + 종료날까지 입금한 수량 - 종료날까지 출금한 수량
                    $item->end_balance = $this->query_one("SELECT
                    (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno=2 AND symbol='{$this->escape(strtoupper($c->symbol))}' AND txn_type='R' AND txndate<='{$this->escape($to_date)} 23:59:59')
                    - (SELECT IFNULL(SUM(amount),0) FROM js_exchange_wallet_txn WHERE userno=2 AND symbol='{$this->escape(strtoupper($c->symbol))}' AND txn_type='S' AND txndate<='{$this->escape($to_date)} 23:59:59')
                    + (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_order WHERE userno='{$this->escape($userno)}' AND trading_type='B' AND STATUS='D' AND time_traded<='{$this->escape($to_date)} 23:59:59')
                    - (SELECT IFNULL(SUM(volume),0) FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_order WHERE userno='{$this->escape($userno)}' AND trading_type='S' AND STATUS='D' AND time_traded<='{$this->escape($to_date)} 23:59:59')
                    AS balance");

                    // 시작일 종가
                    $item->start_price = $this->query_one("SELECT `close` FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_chart WHERE term = '1d' AND DATE = '{$this->escape($from_date)} 00:00:00'");
                    // 종료일 종가
                    $item->end_price = $this->query_one("SELECT `close` FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_chart WHERE term = '1d' AND DATE = '{$this->escape($to_date)} 00:00:00'");

                    // 시작날짜 평가잔액
                    $item->start_amount = $item->start_price * $item->start_balance;
                    $r->total_start_amount += $item->start_amount;
                    // 종료날짜 평가잔액
                    $item->end_amount = $item->end_price * $item->end_balance;
                    $r->total_end_amount += $item->end_amount;
                    // 수익
                    $item->profit = $item->end_amount - $item->start_amount;

                    // 구매 금액 - 검색 기간내에 매수하고 매도한 내역만 뽑아서 사용. 거래중인건 제외
                    $r->total_buy_calculated += $this->query_one("SELECT IFNULL(SUM(amount),0) FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_order
                    WHERE userno = '{$this->escape($userno)}'
                    AND `status`='D' AND trading_type='B'
                    AND time_order>='{$this->escape($from_date)} 00:00:00' AND time_order<='{$this->escape($to_date)} 23:59:59'
                    AND time_traded>='{$this->escape($from_date)} 00:00:00' AND time_traded<='{$this->escape($to_date)} 23:59:59'");
                    // 매도 금액
                    $r->total_sell_calculated += $this->query_one("SELECT IFNULL(SUM(amount),0) FROM js_trade_{$this->escape(strtolower($c->symbol))}{$this->escape(strtolower($this->default_exchange))}_order
                    WHERE userno = '{$this->escape($userno)}'
                    AND `status`='D' AND trading_type='S'
                    AND time_order>='{$this->escape($from_date)} 00:00:00' AND time_order<='{$this->escape($to_date)} 23:59:59'
                    AND time_traded>='{$this->escape($from_date)} 00:00:00' AND time_traded<='{$this->escape($to_date)} 23:59:59'");

                    // 상세정보 추가
                    $r->detail[] = $item;

                }

                // 전체 수익
                $r->total_profit = $r->total_end_amount - $r->total_start_amount;
                $r->total_profit_percent = $r->total_profit ? number_format($r->total_profit * 100 / $r->total_start_amount, 2).'%' : '0%';

            }
            return $r;
        }

        public function get_my_trading_list ($userno, $symbol, $exchange, $page=1, $rows=20, $txnid='0') {
            $userno = preg_replace('/[^0-9]/', '', $userno);
            if(!$userno) {
                return array();
            }
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);

            if($txnid>0) {
                $page = 1;
            }
            $sn = ($page-1) * $rows;
            $table_txn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn';
            $table_order = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $table_ordertxn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_ordertxn';

            // 2. sub 쿼리 분리하기. (SELECT COUNT(volume) FROM js_trade_btckrw_txn)
            $sql = "SELECT COUNT(*) cnt FROM {$table_ordertxn} FORCE INDEX(userno) WHERE userno='".$this->escape($userno)."' ";
            $cnt = $this->query_one($sql);
            $cnt = $cnt>0 ? $cnt : 0;

            if($cnt<1) {
                return array();
            }

            // 1. where 조건에 txnid 추가하기. 그래서 인덱스를 타도록 하기.
            // 1-1 검색 시작 데이터의 txnid - 현제페이지 첫번째 row의 txnid 가져오기.
            $sql = "SELECT t2.txnid FROM {$table_ordertxn} t1 FORCE INDEX(userno) LEFT JOIN {$table_txn} t2 ON t1.`txnid`=t2.`txnid` WHERE t1.userno='".$this->escape($userno)."' ORDER BY t1.txnid DESC, t1.orderid DESC LIMIT ".(($page-1) * $rows).", 1";
            $start_txnid = $this->query_one($sql);

            // 1-2 검색 종료 데이터의 txnid - 다음페이지 첫번째 row의 txnid 가져오기.
            $sql = "SELECT t2.txnid FROM {$table_ordertxn} t1 FORCE INDEX(userno) LEFT JOIN {$table_txn} t2 ON t1.`txnid`=t2.`txnid` WHERE t1.userno='".$this->escape($userno)."' ORDER BY t1.txnid DESC, t1.orderid DESC LIMIT ".(($page) * $rows).", 1";
            $end_txnid = $this->query_one($sql);

            $sql = " SELECT t2.txnid, UNIX_TIMESTAMP(t2.time_traded) AS time_traded, t2.volume, '{$symbol}' AS symbol, t2.price, '{$exchange}' AS exchange, (price * volume) AS amount, '{$cnt}' as tot_cnt, t2.price_updown, IF(t2.orderid_buy=t1.orderid, 'buy', 'sell') trading_type, t1.orderid FROM {$table_ordertxn} t1 FORCE INDEX(userno) LEFT JOIN {$table_txn} t2 ON t2.`txnid`=t1.`txnid` WHERE t1.userno='".$this->escape($userno)."' ";
            if($txnid>0) {
                $sql.= " AND t1.txnid < $txnid ";
            }
            if($start_txnid) {
                $sql.= " AND t2.txnid <= '".$this->escape($start_txnid)."' ";
            }
            if($end_txnid) {
                $sql.= " AND t2.txnid >= '".$this->escape($end_txnid)."' ";
            }
            // $sql.= "ORDER BY time_traded DESC LIMIT ".$this->escape($sn).", ".$this->escape($rows)."";
            $sql.= "ORDER BY t1.txnid DESC, t1.orderid DESC LIMIT ".$this->escape($rows)."";
            $r = $this->query_list_object($sql);
            for($i=0 ; $i<count($r) ; $i++) {
                $r[$i]->volume = number_format($r[$i]->volume, 5, '.', '');
                $digit = $this->get_quote_digit($r[$i]->price, $exchange);
                $r[$i]->price = number_format($r[$i]->price, $digit, '.', '');
            }
            return $r;
        }

        /**
         * 주문 목록 조회
         * @param Number $userno 회원번호. 필요시
         * @param String $status 상태코드. close: 종료, open: 신규등록, cancel: 취소된것, trading: 거래중, unclose: 미종료: all: 전체
         * @param String $symbol 코인 심볼
         * @param String $exchange 매매 화폐 심볼
         * @param Number $page 페이징. 페이지
         * @param Number $rows 페이징. 한페이지에 보여줄 갯수
         * @param Number $orderid 주문번호.
         * @param String $tranding_type 매매종류. B:구매, S:판매
         * @param String $order_by 정렬 컬럼명.
         * @param String $order_method 정렬 방식. DESC: 역순(큰 -> 작, 기본값), ASC: 정순(작 -> 큰)
         * @return Array 주문정보 객체 포함한 배열.
         */
        public function get_order_list($userno='', $status, $symbol, $exchange, $page=1, $rows=20, $orderid='0', $trading_type='', $order_by='orderid', $order_method='DESC') {
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);
            $trading_type = $trading_type ? ($trading_type=='B' ? 'B' : 'S') : '';
            // $digit = $this->get_quote_digit($price, $exchange);

            $login_userno = $this->get_login_userno();

            switch($status) {
                case 'close' : $status = "'C'";  break;
                case 'open' : $status = "'O'";  break;
                case 'delete' : case 'cancel' : $status = "'D'";  break;
                case 'trading' : $status = "'T'";  break;
                case 'unclose' : $status = "'O','T'";  break;
                case 'all' : default : $status = "'O','T','C'"; // delete/cancel 은 제외합니다.
            }
            if($orderid>0) {
                $page = 1;
            }
            $sn = ($page-1) * $rows;
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            // $table = 'js_trade_'.strtolower($symbol).'krw_order';

            // total cnt
            $sql = "select count(orderid) cnt from {$table} where 1 ";
            if($userno) {
                $sql.= " AND userno ='".$this->escape($userno)."' ";
            }
            if($trading_type) {
                $sql.= " AND trading_type ='".$this->escape($trading_type)."' ";
            }
            // $sql = "select count(orderid) cnt from {$table} where 1 ";
            if($status) {
                $sql.= " AND status IN ({$status}) ";
            }
            if($orderid>0) {
                $sql.= " AND orderid < ".$this->escape($orderid)." ";
            }
            $cnt = $this->query_fetch_object($sql);
            $cnt = isset($cnt->cnt) ? $cnt->cnt : 0;
            // order list
            $sql = " SELECT orderid, userno, IF(userno='{$login_userno}', 'Y', 'N') my_order, address, amount, UNIX_TIMESTAMP(time_order) AS time_order, '{$cnt}' as tot_cnt, CASE WHEN trading_type='B' THEN 'buy' ELSE 'sell' END AS trading_type, '{$symbol}' AS symbol, '{$exchange}' AS exchange, price, volume, volume_remain, CASE WHEN status='C' THEN 'close' WHEN status='O' THEN 'open' WHEN status='D' THEN 'cancel' ELSE 'trading' END AS status , UNIX_TIMESTAMP(time_traded) AS time_traded FROM {$table} FORCE INDEX(userno, PRIMARY) WHERE 1 ";
            if($userno) {
                $sql.= " AND userno ='".$this->escape($userno)."' ";
            }
            if($trading_type) {
                $sql.= " AND trading_type ='".$this->escape($trading_type)."' ";
            }
            // $sql = " SELECT orderid, userno, address, UNIX_TIMESTAMP(time_order) AS time_order, '{$cnt}' as tot_cnt, CASE WHEN trading_type='B' THEN 'buy' ELSE 'sell' END AS trading_type, '{$symbol}' AS symbol, '{$exchange}' AS exchange, price, volume, volume_remain, CASE WHEN status='C' THEN 'close' WHEN status='O' THEN 'open' WHEN status='D' THEN 'cancel' ELSE 'trading' END AS status , UNIX_TIMESTAMP(time_traded) AS time_traded FROM {$table} FORCE INDEX(userno, PRIMARY) WHERE 1 ";
            if($status) {
                $sql.= " AND status IN ({$status}) ";
            }
            if($orderid>0) {
                $sql.= " AND orderid < ".$this->escape($orderid)." ";
            }
            $sql.= " ORDER BY {$order_by} {$order_method} ";
            $sql.= " LIMIT ".$this->escape($sn).", ".$this->escape($rows)."";
            $r = $this->query_list_object($sql);
            for($i=0 ; $i<count($r) ; $i++) {
                $r[$i]->volume = number_format($r[$i]->volume, 5, '.', '');
                $digit = $this->get_quote_digit($r[$i]->price, $exchange);
                $r[$i]->price = number_format($r[$i]->price, $digit, '.', '');
            }
            return $r;
        }

        public function get_order($symbol, $exchange, $orderid) {
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $sql = "select orderid, userno, address, UNIX_TIMESTAMP(time_order) time_order,
            case when trading_type='B' then 'buy' else 'sell' end as trading_type, '{$symbol}' as symbol, '{$exchange}' as exchange,
            price, volume, volume_remain,
            case when status='C' then 'close' when status='O' then 'open' else 'trading' end as status ,
            UNIX_TIMESTAMP(time_traded) time_traded
            from {$table} where orderid='".$this->escape($orderid)."' ";
            return $this->query_fetch_object($sql);
        }

        public function cancel_order($symbol, $exchange, $orderid) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $sql = "update {$table} set ";
            $sql.= ' status="D", volume_remain="0" '; // 취소시 모두 환급해주기때문에 남은 volume을 0으로 처리
            $sql.= ' where orderid="'.$this->escape($orderid).'" ';
            return $this->query($sql);
        }

        public function write_buy_order($userno, $address, $symbol, $exchange, $price, $volume, $amount) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $status = '';
            $sql = "insert into $table set ";
            $sql.= ' userno='.$this->escape($userno).', ';
            $sql.= ' address="'.$this->escape($address).'", ';
            $sql.= ' time_order=sysdate(), ';
            $sql.= ' time_traded=NULL, ';
            $sql.= ' trading_type="B", ';
            $sql.= ' price='.$this->escape($price).', ';
            $sql.= ' volume='.$this->escape($volume).', ';
            $sql.= ' volume_remain='.$this->escape($volume).', ';
            $sql.= ' amount='.$this->escape($amount).', ';
            $sql.= ' status="O" ';
            $this->write_log($sql.','.__FILE__.','.__LINE__);
            return $this->query($sql);
        }

        public function write_sell_order($userno, $address, $symbol, $exchange, $price, $volume, $amount) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $status = '';
            $sql = "insert into $table set ";
            $sql.= ' userno='.$this->escape($userno).', ';
            $sql.= ' address="'.$this->escape($address).'", ';
            $sql.= ' time_order=sysdate(), ';
            $sql.= ' time_traded=NULL, ';
            $sql.= ' trading_type="S", ';
            $sql.= ' price='.$this->escape($price).', ';
            $sql.= ' volume='.$this->escape($volume).', ';
            $sql.= ' volume_remain='.$this->escape($volume).', ';
            $sql.= ' amount='.$this->escape($amount).', ';
            $sql.= ' status="O" ';
            return $this->query($sql);
        }

        public function charge_buy_price($userno, $exchange, $amount) {
            $sql = "update js_exchange_wallet set ";
            $sql.= 'confirmed=confirmed - '.$this->escape($amount).' ';
            $sql.= 'where ';
            $sql.= 'userno='.$this->escape($userno).' and ';
            $sql.= 'symbol="'.strtoupper($this->escape($exchange)).'" ';
            $this->write_log("[charge_buy_price] sql:{$sql}, $userno, $exchange, $amount, before: ".$this->get_balance($userno, $exchange));
            return $this->query($sql);
        }

        public function charge_sell_price($userno, $symbol, $amount) {
            $sql = "update js_exchange_wallet set ";
            $sql.= 'confirmed=confirmed - '.$this->escape($amount).' ';
            $sql.= 'where ';
            $sql.= 'userno='.$this->escape($userno).' and ';
            $sql.= 'symbol="'.strtoupper($this->escape($symbol)).'" ';
            // var_dump($sql); // exit;
            $this->write_log("[charge_sell_price] sql:{$sql}, $userno, $symbol, $amount, before: ".$this->get_balance($userno, $symbol));
            return $this->query($sql);
        }

        /**
         * 특정 가격의 주문내역 추출.
         */
        public function get_order_by_price($trading_type, $symbol, $exchange, $price, $userno='') {
            // $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            // $sql = "select orderid, userno, UNIX_TIMESTAMP(time_order) time_order, price, volume_remain, status, time_traded from {$table} where ";
            // $sql.= ' trading_type="'.$this->escape($trading_type).'" and price='.$this->escape($price).' and `status` IN ("O", "T") ';
            // if($userno > 0) { $sql.= ' AND userno <> "'.$this->escape($userno).'" '; }
            // $sql.= " order by time_order ";
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $sql = "SELECT orderid, userno, UNIX_TIMESTAMP(time_order) time_order, price, volume_remain, status, time_traded FROM {$table} WHERE ";
            $sql.= ' trading_type="'.$this->escape($trading_type).'"';
            if($trading_type=='S' && $price) { // 매도 정보를 가져갈때는 매수주문가보다 이하인 걸 추출
                $sql.= ' AND price<='.$this->escape($price);
            }
            if($trading_type=='B' && $price) { // 매수 정보를 가져갈때는 매도주문가보다 이상인 걸 추출
                $sql.= ' AND price>='.$this->escape($price);
            }
            $sql.= ' AND `status` IN ("O", "T") AND volume_remain>0  ';
            if($userno > 0) { $sql.= ' AND userno <> "'.$this->escape($userno).'" '; }

            if($trading_type=='S') { // 매도 정보를 가져갈때는 낮은 가격순 > 선주문순으로 정렬해서 처리하도록 넘겨줌.
                $sql.= " ORDER BY price, time_order ";
            }
            if($trading_type=='B') { // 매수 정보를 가져갈때는 높은 가격순 > 선주문순으로 정렬해서 처리하도록 넘겨줌.
                $sql.= " ORDER BY price DESC, time_order ";
            }
            // exit($sql);
            return $this->query_list_object($sql);
        }

        // 사용자 선택 가격까지 거래 시키고 남은건 호가창에 남길때 사용.
        // public function get_order_by_price($trading_type, $symbol, $exchange, $price, $userno='') {
        //     $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
        //     $sql = "SELECT orderid, userno, UNIX_TIMESTAMP(time_order) time_order, price, volume_remain, status, time_traded FROM {$table} WHERE ";
        //     $sql.= ' trading_type="'.$this->escape($trading_type).'"';
        //     if($trading_type=='S' && $price) { // 매도 정보를 가져갈때는 매수주문가보다 이하인 걸 추출
        //         $sql.= ' AND price<='.$this->escape($price);
        //     }
        //     if($trading_type=='B' && $price) { // 매수 정보를 가져갈때는 매도주문가보다 이상인 걸 추출
        //         $sql.= ' AND price>='.$this->escape($price);
        //     }
        //     $sql.= ' AND `status` IN ("O", "T") ';
        //     if($userno > 0) { $sql.= ' AND userno <> "'.$this->escape($userno).'" '; }

        //     if($trading_type=='S') { // 매도 정보를 가져갈때는 낮은 가격순 > 선주문순으로 정렬해서 처리하도록 넘겨줌.
        //         $sql.= " ORDER BY price, time_order ";
        //     }
        //     if($trading_type=='B') { // 매수 정보를 가져갈때는 높은 가격순 > 선주문순으로 정렬해서 처리하도록 넘겨줌.
        //         $sql.= " ORDER BY price DESC, time_order ";
        //     }
        //     // exit($sql);
        //     return $this->query_list_object($sql);
        // }

        public function trade_order($orderid, $symbol, $exchange, $volume, $status) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order';
            $sql = "update {$table} set ";
            $sql.= ' volume_remain=volume_remain - '.$this->escape($volume).', ';
            $sql.= ' status="'.$this->escape($status).'", ';
            $sql.= ' time_traded=sysdate() ';
            $sql.= ' where ';
            $sql.= ' orderid="'.$this->escape($orderid).'" ';
            return $this->query($sql);
        }

        public function write_trade_txn($symbol, $exchange, $price, $volume, $orderid_buy, $orderid_sell, $fee, $tax_transaction, $tax_income, $price_updown) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn ';
            $sql = "insert into {$table} set ";
            $sql.= ' time_traded=sysdate(), ';
            $sql.= ' volume='.$this->escape($volume).', ';
            $sql.= ' price='.$this->escape($price).', ';
            $sql.= ' orderid_buy="'.$this->escape($orderid_buy).'", ';
            $sql.= ' orderid_sell="'.$this->escape($orderid_sell).'", ';
            $sql.= ' fee="'.$this->escape($fee).'", ';
            $sql.= ' tax_transaction="'.$this->escape($tax_transaction).'", ';
            $sql.= ' tax_income="'.$this->escape($tax_income).'", ';
            $sql.= ' price_updown="'.$this->escape($price_updown).'" ';
            return $this->query($sql);
        }

        public function write_trade_ordertxn($symbol, $exchange, $userno, $orderid, $txnid) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_ordertxn ';
            $sql = "insert into {$table} set ";
            $sql.= ' userno='.$this->escape($userno).', ';
            $sql.= ' orderid='.$this->escape($orderid).', ';
            $sql.= ' txnid="'.$this->escape($txnid).'" ';
            return $this->query($sql);
        }

        public function get_buy_ordertxn($symbol, $exchange, $userno, $cnt='20') {
            $symbol = $this->escape(strtolower($symbol));
            $exchange = $this->escape(strtolower($exchange));
            $cnt = $this->escape(preg_replace( '/[^0-9]/', '', $cnt));
            $cnt = $cnt ? $cnt : 20;
            $sql = " SELECT _txn.volume, _txn.price ";
            $sql.= " FROM js_trade_".$symbol.$exchange."_ordertxn _ot LEFT JOIN js_trade_".$symbol.$exchange."_txn _txn ON _ot.`txnid`=_txn.txnid LEFT JOIN js_trade_".$symbol.$exchange."_order _order ON _ot.`orderid`=_order.`orderid` ";
            $sql.= " WHERE _ot.userno={$this->escape($userno)} AND _order.trading_type='B' ";
            $sql.= " ORDER BY _ot.txnid DESC ";
            $sql.= " LIMIT {$cnt} ";
            return $this->query_list_object($sql);
        }

        public function get_bong_data($symbol, $exchange, $from_time, $to_time='') {
            $from_time = trim(preg_replace('/[^0-9]/', '', $from_time))=='' ? '' : $from_time;
            if(!$from_time) {
                return false;
            }
            $from_sql = " '{$this->escape($from_time)}' <= time_traded ";
            $to_time = trim(preg_replace('/[^0-9]/', '', $to_time))=='' ? '' : $to_time;
            $to_sql = " 1 ";
            if($to_time) {
                $to_sql = " time_traded < '{$this->escape($to_time)}' ";
            }
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn ';
            // if( !$this->check_table_exists($table) ) return false;

            // 시고저종 단일쿼리 - 시간내 거래가 있으면 첫 거래를 시가로 사용.
//             $sql = "SELECT `date`, IFNULL(`open`, close_prev) `open`, IFNULL(high, close_prev) high, IFNULL(low, close_prev) low, IFNULL(`close`, close_prev) `close`, `volume`
// FROM (
// SELECT
// '{$this->escape($from_time)}' date
// ,(SELECT price FROM {$table} WHERE {$from_sql} AND {$to_sql} ORDER BY time_traded LIMIT 1) `open`
// ,(SELECT price FROM {$table} WHERE time_traded < '{$this->escape($from_time)}' ORDER BY time_traded DESC LIMIT 1) `close_prev`
// ,(SELECT MAX(price)  FROM {$table} WHERE {$from_sql} AND {$to_sql} ) high
// ,(SELECT MIN(price) low FROM {$table} WHERE {$from_sql} AND {$to_sql} ) low
// ,(SELECT price FROM {$table} WHERE {$from_sql} AND {$to_sql} ORDER BY time_traded DESC LIMIT 1) `close`
// ,(SELECT ifnull(SUM(volume),0) FROM {$table} WHERE {$from_sql} AND {$to_sql} ) `volume`
// )t";
//             $r = $this->query_fetch_object($sql);
            // 시고저종 단일쿼리 - 시간내 거래가 있어도 시가를 이전봉 종가로 처리
            $sql = "SELECT `date`, `open`, IFNULL(high, `open`) high, IFNULL(low, `open`) low, IFNULL(`close`, `open`) `close`, `volume`
FROM (
SELECT
'{$this->escape($from_time)}' date
,(SELECT price FROM {$table} WHERE time_traded < '{$this->escape($from_time)}' ORDER BY time_traded DESC LIMIT 1) `open`
,(SELECT MAX(price)  FROM {$table} WHERE {$from_sql} AND {$to_sql} ) high
,(SELECT MIN(price) low FROM {$table} WHERE {$from_sql} AND {$to_sql} ) low
,(SELECT price FROM {$table} WHERE {$from_sql} AND {$to_sql} ORDER BY time_traded DESC LIMIT 1) `close`
,(SELECT ifnull(SUM(volume),0) FROM {$table} WHERE {$from_sql} AND {$to_sql} ) `volume`
)t";
            $r = $this->query_fetch_object($sql);
            // 거래내역이 전혀 없어서 시가 없으면 현재가로 시가를 사용하기.
            if(!$r->open) {
                $r->open = $this->query_one("SELECT price_close FROM js_trade_price WHERE symbol='{$this->escape($symbol)}'");
            }
            if(!$r->high || $r->high < $r->open) { $r->high = $r->open; } // 시가를 전봉 종가를 사용하기때문에 고가와 시가를 비교한다.
            if(!$r->low || $r->low > $r->open) { $r->low = $r->open; } // 시가를 전봉 종가를 사용하기때문에 저가와 시가를 비교한다.
            if(!$r->close) { $r->close = $r->open; } // 종가 값이 없으면 시가로 사용한다.
            // var_dump($r); exit;
//             // 검색 시간 속 거래내역을 기준으로 시고저종 추출
//             $sql = "SELECT `date`, IFNULL(`open`, 0) `open`, IFNULL(high, 0) high, IFNULL(low, 0) low, IFNULL(`close`, 0) `close`, `volume`
// FROM (
// SELECT
// '{$this->escape($from_time)}' date
// ,(SELECT price FROM {$table} WHERE {$from_sql} AND {$to_sql} ORDER BY time_traded LIMIT 1) `open`
// ,(SELECT MAX(price)  FROM {$table} WHERE {$from_sql} AND {$to_sql} ) high
// ,(SELECT MIN(price) low FROM {$table} WHERE {$from_sql} AND {$to_sql} ) low
// ,(SELECT price FROM {$table} WHERE {$from_sql} AND {$to_sql} ORDER BY time_traded DESC LIMIT 1) `close`
// ,(SELECT ifnull(SUM(volume),0) FROM {$table} WHERE {$from_sql} AND {$to_sql} ) `volume`
// )t";
//             echo ($sql."\n"); //exit;
//             // 거래내역이 없는경우 이전 마지막 종가 추출.
//             $r = $this->query_fetch_object($sql);
//             if($r->open*1==0) {
//                 $sql = "SELECT price FROM {$table} WHERE time_traded < '{$this->escape($from_time)}' ORDER BY time_traded DESC LIMIT 1";
//                 $close_prev = $this->query_one($sql);
//                 if($close_prev) {
//                     $r->open = $close_prev;
//                     $r->high = $close_prev;
//                     $r->low = $close_prev;
//                     $r->close = $close_prev;
//                 }
//             }
            return $r;
        }
        public function get_bong_data_old($symbol, $exchange, $from_time, $to_time='') {
            $from_time = trim(preg_replace('/[^0-9]/', '', $from_time))=='' ? time() : $from_time;
            $to_time = trim(preg_replace('/[^0-9]/', '', $to_time))=='' ? '' : $to_time;
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn ';
            $sql = "SELECT '$from_time' date, IFNULL(MAX(open), IFNULL(MAX(close), 0)) open, IFNULL(MAX(high), IFNULL(MAX(close), 0)) high, IFNULL(MAX(low), IFNULL(MAX(close), 0)) low, IFNULL(MAX(close), 0) close, IFNULL(MAX(volume), 0) volume ";
            $sql.= "FROM (";
            // 현재봉의 고가, 저가 총거래량 구하기.
            $sql.= "(SELECT NULL open,MAX(price) high,MIN(price) low,NULL close,SUM(volume) volume ";
            $sql.= "	FROM $table FORCE INDEX(time_traded)";
            $sql.= "	WHERE '$from_time' <= time_traded ";
            if($to_time!='') {// 기본은 현재까지
                $sql.= "	    AND time_traded < '$to_time'";
            }
            $sql.= "	LIMIT 1)";
            $sql.= "UNION ALL";
            // 이전 봉의 마지막 거래 가격이 시작가로 사용.
            $sql.= "(SELECT price open,NULL high,NULL low,NULL close, null volume ";
            $sql.= "	FROM $table FORCE INDEX(time_traded)";
            $sql.= "	WHERE '$from_time' > time_traded ";
            if($to_time!='') {// 기본은 현재까지
                $sql.= "	    AND time_traded < '$to_time'";
            }
            $sql.= "	ORDER BY time_traded DESC LIMIT 1)";
            $sql.= "UNION ALL";
            // 거래가 없는 경우를 위해 종가는 가장 마지막 거래 데이터로 구합니다.
            $sql.= "(SELECT NULL open,NULL high,NULL low,price close, null volume ";
            $sql.= "	FROM $table FORCE INDEX(time_traded)";
            $sql.= "	ORDER BY time_traded DESC LIMIT 1)";
            $sql.= ") t";
            // var_dump ($sql);
            return $this->query_fetch_object($sql);
        }




        function save_bong_data($symbol, $exchange, $term, $date, $open, $high, $low, $close, $volume) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_chart ';
            if(!$open || !$high || !$low || !$close || !$volume) {return false;}
            // $open = $open ? $open : '0';
            // $high = $high ? $high : '0';
            // $low = $low ? $low : '0';
            // $close = $close ? $close : '0';
            // $volume = $volume ? $volume : '0';
            // if( !$this->check_table_exists($table) ) return false;
            $sql = " INSERT INTO $table ";
            $sql.= " SET term='".$this->escape($term)."', date='".$this->escape($date)."', open='".$this->escape($open)."', high='".$this->escape($high)."', low='".$this->escape($low)."', close='".$this->escape($close)."', volume='".$this->escape($volume)."' ";
            $sql.= " ON DUPLICATE KEY UPDATE open='".$this->escape($open)."', high='".$this->escape($high)."', low='".$this->escape($low)."', close='".$this->escape($close)."', volume='".$this->escape($volume)."' ";
            // var_dump($sql);
            return $this->query($sql);
        }

        function delete_old_data($symbol, $exchange, $term) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_chart ';
            // if( !$this->check_table_exists($table) ) return false;
            $sql = 'DELETE  FROM `'.$this->escape($table).'` WHERE term = "'.$this->escape($term).'" ';
            $sql.= 'AND DATE <= (SELECT DATE FROM `'.$this->escape($table).'` FORCE INDEX (PRIMARY) ORDER BY DATE DESC LIMIT 1000, 1)';
        }

        /**
         * 호가 데이터 생성.
         * 거래된 가격의 호가만
         */
        function set_quote_data($symbol, $exchange, $price) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_quote ';
            $table_order = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order ';

            // 해당 가격의 매수, 매도 량 가져오기. groupby 를 하면 temptable을 생성해서 그냥 각각 쿼리 날려 인덱스만 태워서 가져도록 함.
            $sql = "select price, sum(volume_remain) volume, trading_type FROM {$table_order} WHERE `status` IN ('O', 'T') AND price='{$price}' AND volume_remain>0 AND  trading_type='B' ";
            $r = $this->query_fetch_object($sql);
            if(! $r->volume) {
                $sql = "select price, sum(volume_remain) volume, trading_type FROM {$table_order} WHERE `status` IN ('O', 'T') AND price='{$price}' AND volume_remain>0 AND  trading_type='S' ";
                $r = $this->query_fetch_object($sql);
            }
            // 해당 가격의 호가만 수정.
            if($r->volume > 0 ) {
                $sql = "INSERT INTO {$table} SET volume='{$r->volume}', trading_type ='{$r->trading_type}', price='{$price}' ON DUPLICATE KEY UPDATE volume='{$r->volume}', trading_type ='{$r->trading_type}' ";
            } else {
                $sql = "DELETE FROM {$table}  WHERE price='{$price}' ";
            }
            return $this->query($sql);
        }

            // 싱크 오류가 발생하는 경우 발생해서 1분에 한번씩 전체 싱크를 맞춥니다.
        /**
         * 호가 데이터 전체 생성.
         */
        function set_quote_data_total($symbol, $exchange) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_quote ';
            $table_order = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order ';
            $price = $this->get_last_price($symbol, $exchange);
            $sql = " TRUNCATE TABLE {$table} ";
            $this->query($sql);
            $sql = " INSERT INTO {$table} ";
            $sql.= " SELECT * FROM ( ";
            $sql.= " (SELECT  ";
            $sql.= " price, SUM(volume_remain) volume, trading_type ";
            $sql.= " FROM {$table_order} ";
            $sql.= " WHERE `status` IN ('O', 'T') ";
            if($price>0) { $sql.= " AND price>='{$price}' "; }
            $sql.= " AND trading_type='S' ";
            $sql.= " GROUP BY price ";
            // $sql.= " ORDER BY price  ";
            // $sql.= " LIMIT 10 ";
            $sql.= " ) ";
            $sql.= " UNION ALL ";
            $sql.= " (SELECT  ";
            $sql.= " price, SUM(volume_remain) volume, trading_type ";
            $sql.= " FROM {$table_order} ";
            $sql.= " WHERE `status` IN ('O', 'T') ";
            if($price>0) { $sql.= " AND price<='{$price}' "; }
            $sql.= " AND trading_type='B' ";
            $sql.= " GROUP BY price ";
            // $sql.= " ORDER BY price DESC ";
            // $sql.= " LIMIT 10 ";
            $sql.= " ) ";
            $sql.= " )t";
            return $this->query($sql);
        }
        /**
         * 현재가 구한는 쿼리 ..
         * 마지막 거래 내역을 통해 현재가를 구합니다.
         */
        function get_current_price_data($symbol, $exchange) {
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);
            $table_txn = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_txn ';
            $sql = " SELECT ";
            $sql.= "   '{$symbol}' symbol, ";
            $sql.= "   '{$exchange}' exchange, ";
            $sql.= "   IFNULL(MAX(volume),0) volume, ";
            $sql.= "   IFNULL(MAX(price_high),IFNULL(MAX(price_close),0)) price_high, ";
            $sql.= "   IFNULL(MAX(price_low),IFNULL(MAX(price_close),0)) price_low, ";
            $sql.= "   IFNULL(MAX(price_open),IFNULL(MAX(price_close),0)) price_open, ";
            $sql.= "   IFNULL(MAX(price_close),0) price_close, ";
            $sql.= "   IFNULL(FORMAT((MAX(price_close) - MAX(price_open)) / MAX(price_open) * 100,2),0) price_chagne_percent, ";
            $sql.= "   IFNULL(MAX(volume_12),0) volume_12, ";
            $sql.= "   IFNULL(MAX(price_high_12),IFNULL(MAX(price_close),0)) price_high_12, ";
            $sql.= "   IFNULL(MAX(price_low_12),IFNULL(MAX(price_close),0)) price_low_12, ";
            $sql.= "   IFNULL(MAX(price_open_12),IFNULL(MAX(price_close),0)) price_open_12, ";
            $sql.= "   IFNULL(MAX(price_close),0) price_close_12, ";
            $sql.= "   IFNULL(FORMAT((MAX(price_close) - MAX(price_open_12)) / MAX(price_open_12) * 100,2),0) price_chagne_percent_12, ";
            $sql.= "   IFNULL(MAX(volume_1),0) volume_1, ";
            $sql.= "   IFNULL(MAX(price_high_1),IFNULL(MAX(price_close),0)) price_high_1, ";
            $sql.= "   IFNULL(MAX(price_low_1),IFNULL(MAX(price_close),0)) price_low_1, ";
            $sql.= "   IFNULL(MAX(price_open_1),IFNULL(MAX(price_close),0)) price_open_1, ";
            $sql.= "   IFNULL(MAX(price_close),0) price_close_1, ";
            $sql.= "   IFNULL(FORMAT((MAX(price_close) - MAX(price_open_1)) / MAX(price_open_1) * 100,2),0) price_chagne_percent_1 ";
            $sql.= " FROM ";
            $sql.= "   ( ";
            $sql.= "     (SELECT SUM(volume) volume,MAX(price) price_high,MIN(price) price_low,NULL price_open,NULL price_close, NULL volume_12,NULL price_high_12,NULL price_low_12,NULL price_open_12,NULL price_close_12,NULL volume_1,NULL price_high_1,NULL price_low_1,NULL price_open_1,NULL price_close_1   ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     WHERE time_traded > FROM_UNIXTIME( UNIX_TIMESTAMP() - 60*60*24 ) )  ";
            $sql.= "     UNION ALL  ";
            $sql.= "     (SELECT NULL volume,NULL price_high,NULL price_low,price price_open,NULL price_close, NULL volume_12,NULL price_high_12,NULL price_low_12,NULL price_open_12,NULL price_close_12,NULL volume_1,NULL price_high_1,NULL price_low_1,NULL price_open_1,NULL price_close_1  ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     WHERE time_traded > FROM_UNIXTIME( UNIX_TIMESTAMP() - 60*60*24 ) ";
            $sql.= "     ORDER BY time_traded  ";
            $sql.= "     LIMIT 1 )  ";
            $sql.= "     UNION ALL  ";
            $sql.= "     (SELECT NULL volume,NULL price_high,NULL price_low,NULL price_open,price price_close, NULL volume_12,NULL price_high_12,NULL price_low_12,NULL price_open_12,NULL price_close_12,NULL volume_1,NULL price_high_1,NULL price_low_1,NULL price_open_1,NULL price_close_1  ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     ORDER BY time_traded DESC  ";
            $sql.= "     LIMIT 1 ) ";
            $sql.= "     UNION ALL ";
            $sql.= "     ( SELECT  NULL volume,NULL price_high,NULL price_low,NULL price_open,NULL price_close, SUM(volume) volume_12,MAX(price) price_high_12,MIN(price) price_low_12,NULL price_open_12,NULL price_close_12,NULL volume_1,NULL price_high_1,NULL price_low_1,NULL price_open_1,NULL price_close_1  ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     WHERE time_traded > FROM_UNIXTIME( UNIX_TIMESTAMP() - 60*60*12 ) )  ";
            $sql.= "     UNION ALL  ";
            $sql.= "     (SELECT  NULL volume,NULL price_high,NULL price_low,NULL price_open,NULL price_close, NULL volume_12,NULL price_high_12,NULL price_low_12,price price_open_12,NULL price_close_12,NULL volume_1,NULL price_high_1,NULL price_low_1,NULL price_open_1,NULL price_close_1  ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     WHERE time_traded > FROM_UNIXTIME( UNIX_TIMESTAMP() - 60*60*12 ) ";
            $sql.= "     ORDER BY time_traded  ";
            $sql.= "     LIMIT 1 )  ";
            $sql.= "     UNION ALL  ";
            $sql.= "     ( SELECT NULL volume,NULL price_high,NULL price_low,NULL price_open,NULL price_close,NULL volume_12,NULL price_high_12,NULL price_low_12,NULL price_open_12,NULL price_close_12, SUM(volume) volume_1, MAX(price) price_high_1, MIN(price) price_low_1, NULL price_open_1, NULL price_close_1  ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     WHERE time_traded > FROM_UNIXTIME( UNIX_TIMESTAMP() - 60*60*1 ) )  ";
            $sql.= "     UNION ALL  ";
            $sql.= "     (SELECT NULL volume,NULL price_high,NULL price_low,NULL price_open,NULL price_close,NULL volume_12,NULL price_high_12,NULL price_low_12,NULL price_open_12,NULL price_close_12, NULL volume_1,NULL price_high_1,NULL price_low_1,price price_open_1,NULL price_close_1  ";
            $sql.= "     FROM {$table_txn} FORCE INDEX (time_traded)  ";
            $sql.= "     WHERE time_traded > FROM_UNIXTIME( UNIX_TIMESTAMP() - 60*60*1 ) ";
            $sql.= "     ORDER BY time_traded  ";
            $sql.= "     LIMIT 1 )  ";
            $sql.= "   ) t  ";
            return $this->query_fetch_object($sql);
        }

        function set_current_price_data($symbol, $exchange) {
            $price = $this->get_current_price_data($symbol, $exchange);
            $sql_update = " volume='".$this->escape($price->volume)."', ";
            $sql_update.= " price_high='".$this->escape($price->price_high)."', ";
            $sql_update.= " price_low='".$this->escape($price->price_low)."', ";
            $sql_update.= " price_open='".$this->escape($price->price_open)."', ";
            $sql_update.= " price_close='".$this->escape($price->price_close)."', ";
            $sql_update.= " price_chagne_percent='".$this->escape($price->price_chagne_percent)."', ";
            $sql_update.= " volume_12='".$this->escape($price->volume_12)."', ";
            $sql_update.= " price_high_12='".$this->escape($price->price_high_12)."', ";
            $sql_update.= " price_low_12='".$this->escape($price->price_low_12)."', ";
            $sql_update.= " price_open_12='".$this->escape($price->price_open_12)."', ";
            $sql_update.= " price_close_12='".$this->escape($price->price_close_12)."', ";
            $sql_update.= " price_chagne_percent_12='".$this->escape($price->price_chagne_percent_12)."', ";
            $sql_update.= " volume_1='".$this->escape($price->volume_1)."', ";
            $sql_update.= " price_high_1='".$this->escape($price->price_high_1)."', ";
            $sql_update.= " price_low_1='".$this->escape($price->price_low_1)."', ";
            $sql_update.= " price_open_1='".$this->escape($price->price_open_1)."', ";
            $sql_update.= " price_close_1='".$this->escape($price->price_close_1)."', ";
            $sql_update.= " price_chagne_percent_1='".$this->escape($price->price_chagne_percent_1)."' ";

            $sql = " INSERT INTO js_trade_price SET ";
            $sql.= " symbol='".strtoupper($this->escape($symbol))."', ";
            $sql.= " exchange='".strtoupper($this->escape($exchange))."', ";
            $sql.= $sql_update;
            $sql.= " ON DUPLICATE KEY UPDATE ";
            $sql.= $sql_update;
            $r = $this->query($sql);
            if($r) {
				/**무한대로 가격이 0으로 바뀌는 로직 수정***/
                $sql = "UPDATE js_trade_currency SET price='{$this->escape($price->price_close)}' WHERE symbol='{$this->escape(strtoupper($symbol))}' ";
                $this->query($sql);
            }
            return 'err_sess';
        }

        function get_max_buy_price($symbol, $exchange) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order ';
            $sql = "SELECT MAX(price) price FROM {$table} WHERE STATUS IN ('O', 'T') AND trading_type='B' ";
            $_r = $this->query_fetch_object($sql);
            return $_r->price;
        }

        function get_min_sell_price($symbol, $exchange) {
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_order ';
            $sql = "SELECT MIN(price) price FROM {$table} WHERE STATUS IN ('O', 'T') AND trading_type='S' ";
            $_r = $this->query_fetch_object($sql);
            return $_r->price;
        }

        function get_user_info($userno) {
            $sql = "SELECT * FROM js_member WHERE userno = '".$this->escape($userno)."' ";
            return $this->query_fetch_object($sql);
        }

        function save_user_otpkey($userno, $otpkey) {
            $sql = " UPDATE js_member SET  ";
            $sql.= " otpkey = '".$this->escape($otpkey)."' ";
            $sql.= " WHERE ";
            $sql.= " userno = ".$this->escape($userno)." ";
            return $this->query($sql);
        }

        function put_fcm_info($userno, $uuid, $os, $fcm_tokenid) {
            $sql = " INSERT INTO js_member_device SET ";
            $sql.= " userno = '".$this->escape($userno)."', ";
            $sql.= " uuid = '".$this->escape($uuid)."', ";
            $sql.= " os = '".$this->escape($os)."', ";
            $sql.= " fcm_tokenid = '".$this->escape($fcm_tokenid)."', ";
            $sql.= " ip = '".$this->escape($_SERVER['REMOTE_ADDR'])."', ";
            $sql.= " regdate = SYSDATE() ";
            return $this->query($sql);
        }

        function get_fcm_info($userno, $uuid) {
            $sql = " SELECT userno, uuid, os, fcm_tokenid, regdate FROM js_member_device WHERE ";
            $sql.= " userno = '".$this->escape($userno)."' AND ";
            $sql.= " uuid = '".$this->escape($uuid)."' ";
            return $this->query_fetch_object($sql);
        }

        function get_chart_data($symbol, $exchange, $period, $cnt=1000) {
            $cnt = preg_replace('/[^0-9]/', '', $cnt);
            $cnt = $cnt>0 ? $cnt : 0;
            $table = 'js_trade_'.strtolower($symbol).strtolower($exchange).'_chart ';
            $sql = " SELECT date, open, high, low, close, volume, '".$symbol."' as symbol FROM {$table} FORCE INDEX(PRIMARY) WHERE ";
            $sql.= " term = '".$this->escape($period)."' ";
            $sql.= " ORDER BY DATE DESC ";
            $sql.= " limit ".$cnt;
            return $this->query_list_tsv($sql, true);
        }

        function save_withdraw($userno, $symbol, $real_amount, $fee, $to_address, $from_address) {
            $sql = " INSERT INTO js_exchange_wallet_txn SET";
            $sql.= " userno = '".$this->escape($userno)."', ";
            $sql.= " symbol = '".$this->escape($symbol)."', ";
            $sql.= " address = '".$this->escape($from_address)."', ";
            $sql.= " regdate = SYSDATE(), ";
            $sql.= " address_relative = '".$this->escape($to_address)."', ";
            $sql.= " txn_type = 'W', ";
            $sql.= " direction = 'O', ";
            $sql.= " amount = '".$this->escape($real_amount)."', ";
            $sql.= " fee = '".$this->escape($fee)."', ";
            $sql.= " status = 'O' ";
            $this->write_log("[save_withdraw] sql:{$sql}, $userno, $symbol, $real_amount, $fee, $to_address, $from_address");
            return $this->query($sql);
        }

        function get_member_info ($userno) {
            $sql = " SELECT userno, userid, name, nickname, phone, mobile, mobile_country_code, email, bool_email, bool_sms, bool_lunar, birthday, level_code, regdate, otpkey, bool_confirm_email, bool_confirm_mobile, bool_confirm_idimage, bank_name, bank_account, bank_owner, bool_realname, image_identify_url, image_mix_url, gender, image_bank_url, bool_confirm_bank FROM js_member WHERE userno = '".$this->escape($userno)."' ";
            return $this->query_fetch_object($sql);
        }

        function get_member_meta($userno, $meta_name) {
            $r = $this->query_one("SELECT `value` FROM js_member_meta WHERE `userno`='{$this->escape($userno)}' AND `name`='{$this->escape($meta_name)}' ");
            return $r ? $r : '';
        }
        function set_member_meta($userno, $name, $value) {
            return $this->query("INSERT INTO js_member_meta SET `userno`='{$this->escape($userno)}', `name`='{$this->escape($name)}', `value`='{$this->escape($value)}' ON DUPLICATE KEY UPDATE `value`='{$this->escape($value)}' ");
        }
        function del_member_meta($userno, $name) {
            return $this->query("DELETE FROM js_member_meta WHERE `userno`='{$this->escape($userno)}' AND `name`='{$this->escape($name)}' ");
        }

        function get_permission_code ($bool_confirm_mobile=0, $bool_confirm_idimage=0, $bool_confirm_bank=0) {
            // $p[] = $this->isLogin() ? '1' : '0';
            // $p[] = $this->isLogin() ? '1' : '0';
            // $p[] = $bool_confirm_mobile ? '1' : '0';
            // $p[] = $bool_confirm_idimage ? '1' : '0';
			// $p[] = $bank_info ? '1' : '0';
            $p = array();
            $p[] = $this->isLogin() ? '1' : '0';
            $p[] = end($p) && $this->isLogin() ? '1' : '0';
            $p[] = end($p) && $bool_confirm_mobile ? '1' : '0';
            $p[] = end($p) && $bool_confirm_mobile && $bool_confirm_idimage ? '1' : '0';
            $p[] = end($p) && $bool_confirm_idimage && $bool_confirm_bank ? '1' : '0';
            return implode('', $p);
        }

        function get_member_info_by_userid ($userid) {
            $sql = " SELECT userno, userid, userpw, name, nickname, phone, mobile, email, bool_email, bool_sms, bool_lunar, birthday, level_code, regdate, otpkey, bool_confirm_email, bool_confirm_mobile, bool_realname, image_identify_url, image_mix_url, gender FROM js_member WHERE userid = '".$this->escape($userid)."' ";
            return $this->query_fetch_object($sql);
        }

        function save_member_info ($data) {
            if(! $data['userno']) {
                return false;
            }
            $sql = "UPDATE js_member SET";
            $sql.= " userno = '".$this->escape($data['userno'])."' ";
            if(isset($data['name'])){$sql.= ", name = '".$this->escape($data['name'])."' ";}
            if(isset($data['nickname'])){$sql.= ", nickname = '".$this->escape($data['nickname'])."' ";}
            if(isset($data['phone'])){$sql.= ", phone = '".$this->escape($data['phone'])."' ";}
            if(isset($data['mobile'])){$sql.= ", mobile = '".$this->escape($data['mobile'])."' ";}
            if(isset($data['email'])){$sql.= ", email = '".$this->escape($data['email'])."' ";}
            if(isset($data['bool_email'])){$sql.= ", bool_email = '".$this->escape($data['bool_email'])."' ";}
            if(isset($data['bool_sms'])){$sql.= ", bool_sms = '".$this->escape($data['bool_sms'])."' ";}
            if(isset($data['bool_lunar'])){$sql.= ", bool_lunar = '".$this->escape($data['bool_lunar'])."' ";}
            if(isset($data['birthday'])){$sql.= ", birthday = '".$this->escape($data['birthday'])."' ";}
            if(isset($data['image_identify_url'])){$sql.= ", image_identify_url = '".$this->escape($data['image_identify_url'])."' ";}
            if(isset($data['image_mix_url'])){$sql.= ", image_mix_url = '".$this->escape($data['image_mix_url'])."' ";}
            $sql.= " WHERE userno = '".$this->escape($data['userno'])."' ";
            return $this->query($sql);
        }

        function get_unworked_deposit_msg ($cnt=50) {
            $regtime = (time()-30 ) . '000000'; // 3최근 30초 간 메시지는 제외
            $stime = (time()-60*60*24*3 ) . '000000'; // 3일전 저장된 매시지만 확인
            $sql = "SELECT *, from_unixtime(SUBSTRING(regtime, 1,10)) regdate FROM js_deposit_msg where done='N' and regtime<='{$this->escape($regtime)}' and regtime>'{$stime}' order by regtime desc limit 0, $cnt "; // 최근 3일간 입금문자중에서 100건 가져온다.
            $r = $this->query_list_object($sql);
            $r = array_reverse($r); // 가져온결과중에 가장 오래전것부터 확인한다. 동일인이 2건 입금하는경우 처음것부터 처리하기 위함.
            return $r;
        }

        // ----------------------------------------------------------------- //
        // External Data

        /**
         * get block.cc ticker api data
         * https://api.coinmarketcap.com/v1/ticker/Ripple/
         * <code>
         * [
         *     {
         *         "id": "ripple",  // coinmarketcap.com 아이디
         *         "name": "XRP",   // 이름
         *         "symbol": "XRP", // 심볼
         *         "rank": "2",     // 순위
         *         "price_usd": "0.3314697721",     // usd 가격
         *         "price_btc": "0.00008975",       // btc 가격
         *         "24h_volume_usd": "476152227.127",   // 24시간 usd 거래량
         *         "market_cap_usd": "13603653723.0",   //
         *         "available_supply": "41040405095.0",   // = 유통 공급량, Circulating Supply (시총 게산용)
         *         "total_supply": "99991724864.0",     // 총 공급량
         *         "max_supply": "100000000000",        // 최대 공급량
         *         "percent_change_1h": "-0.42",
         *         "percent_change_24h": "2.09",
         *         "percent_change_7d": "-8.8",
         *         "last_updated": "1547544244"     // 마지막 수정시간
         *     }
         * ]
         * </code>
         */
        function get_coinmarketcap_ticker($name) {
            $name = str_replace(' ', '-', strtolower($name));
            $s = @ file_get_contents(dirname(__file__).'/../data/coinmarketcap_ticker_'.$name.'.json');
            if(!$s) {
                $url = "https://api.coinmarketcap.com/v1/ticker/{$name}/";
                $s = $this->get_cache($url); // 캐시 타임이 짧아 서버에서 값을 못받아 캐시 타임 늘림.
                if(!$s) {
                    $s = $this->set_cache($url, $this->remote_get($url), 30);
                }
            }
            if($s!='') {
                $s = json_decode($s);
                $s = $s;
            }
            return $s;
        }

        /**
         * get block.cc ticker api data
         */
        function get_blockcc_ticker($symbol, $exchange, $market='') {
            $symbol = strtoupper($symbol);
            $exchange = strtoupper($exchange);
            $market = $market ? trim($market) : '';
            $s = @ file_get_contents(dirname(__file__).'/../data/blockcc_ticker_'.$symbol.$exchange.'.json');
            if(!$s) {
                $url = "https://data.block.cc/api/v1/tickers?symbol={$symbol}&currency={$exchange}&market={$market}";
                $s = $this->get_cache($url); // 캐시 타임이 짧아 서버에서 값을 못받아 캐시 타임 늘림.
                if(!$s) {
                    $s = $this->set_cache($url, $this->remote_get($url), 30);
                }
            }
            if($s!='') {
                $s = json_decode($s);
                if($market && count($s->data->list)>0) {
                    $t = array();
                    for($i=0; $i<count($s->data->list); $i++) {
                        $r = $s->data->list[$i];
                        if(strtolower($r->market)==strtolower($market)) {
                            $t[] = $r;
                            break;
                        }
                    }
                    $s->data->list = $t;
                }
            }
            return $s;
        }

        function get_external_price($SYMBOL, $EXCHANGE, $market='') {
            $price = 0;
            $coinid = '';
            $symbol = strtolower($SYMBOL);
            $coin_name = $this->query_one("SELECT `name` FROM js_trade_currency WHERE symbol='{$this->escape($SYMBOL)}' ");
            $exchange = strtolower($EXCHANGE);
            $list = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_list.json'));
            foreach($list as $row) {
                if($symbol == strtolower($row->symbol) && strtolower($coin_name) == strtolower($row->name)) {$coinid=$row->id; break;}
            }
            $m = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_exchanges.json'));
            foreach($m as $row) {
                if($row->id == strtolower($market)) {$market=$row->id; break;}
            }
            if($coinid && $market) {
                $url = "https://api.coingecko.com/api/v3/exchanges/{$market}/tickers?coin_ids={$coinid}";
                $s = $this->get_cache($url);
                if(!$s) {
                    $s = $this->set_cache($url, $this->remote_get($url), 10);
                }
                if($s) {
                    $s = json_decode($s);
                    if($s->tickers) {
                        foreach($s->tickers as $row) {
                            if($row->base == $SYMBOL and $row->target == $EXCHANGE) {
                                $price = $row->last;
                            }
                        }
                    }
                }
            }
            return $price;
        }

        function get_external_ticker($SYMBOL, $EXCHANGE, $market='') {
            $ohlc = array();
            $coinid = '';
            $symbol = strtolower($SYMBOL);
            $exchange = strtolower($EXCHANGE);
            $list = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_list.json'));
            foreach($list as $row) {
                if($symbol == strtolower($row->symbol)) {$coinid=$row->id; break;}
            }
            $markets = array();
            if($market) {
                $m = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_exchanges.json'));
                foreach($m as $row) {
                    if($row->id == strtolower($market)) {$market=$row->id; break;}
                }
                $markets = array($market);
            }
            $t = array();
            $url = "https://api.coingecko.com/api/v3/coins/{$coinid}/tickers";
            $s = $this->get_cache($url);
            if(!$s) {$s = $this->set_cache($url, $this->remote_get($url), 10);}
            $s = $s ? json_decode($s) : null;
            if($s->tickers) {
                foreach($s->tickers as $row) {
                    if($row->base == $SYMBOL && $row->target == $EXCHANGE && $row->market && $row->market->identifier) {
                        if($row->base == $SYMBOL && $row->target == $EXCHANGE && (empty($markets) || in_array($row->market->identifier, $markets)) ) {
                            var_dump($row); exit;
                            $ohlc[] = array(
                                'market'=>$row->market->name,
                                'vol'=>$row->volume,
                                'last'=>$row->last,
                                // 'high'=>$row->last, // 값이 없는데 이전 형식에 맞추기 위해 넣음.
                                // 'low'=>$row->last, // 값이 없는데 이전 형식에 맞추기 위해 넣음.
                                'change_daily'=>'' // 값이 없는데 이전 형식에 맞추기 위해 넣음.
                            );
                            break;
                        }
                    }
                }
            }
            // var_dump($ohlc); exit;
            return $ohlc;
        }


        /**
         * get Other Market Price Infomation (Block.cc)
         * https://data.block.cc/
         * https://data.block.cc/doc/?shell#price
         */
        function get_other_list($symbol, $exchange) {
            // $url = "https://data.block.cc/api/v1/tickers?symbol={$symbol}&currency={$exchange}";
            // $s = $this->remote_get($url);
            // if($s!='') {
            //     $s = json_decode($s);
            // }
            $s = $this->get_external_ticker($symbol,$exchange);
            $t = array();
            if($s) {
                foreach($s as $row) {
                    $row = (object) $row;
                    $t[] = array(
                        'site_name'=>ucwords($row->market),
                        'volume'=>$row->vol,
                        'price'=>$row->last,
                        'change_daily'=>$row->change_daily
                    );
                }
            }
            return $t;
        }

        /**
         * get Spot Price(Current Price)
         * @param String Currency Code(Symbol). ex) BTC, LTC, ETH, ...
         * @param String Exchange Currency Code(Symbol). ex) USD, KRW, JPY, ...
         * @param Object list of the price value object
         */
        function get_external_spot_price($SYMBOL,$EXCHANGE) {
            $price = 0;
            $coinid = '';
            $symbol = strtolower($SYMBOL);
            $exchange = strtolower($EXCHANGE);
            $list = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_list.json'));
            foreach($list as $row) {
                if($symbol == strtolower($row->symbol)) {$coinid=$row->id; break;}
            }
            if($coinid && $market) {
                $url = "https://api.coingecko.com/api/v3/simple/price?ids={$coinid}&vs_currencies={$exchange}&include_market_cap=true&include_24hr_vol=true&include_24hr_change=true";
                $s = $this->get_cache($url);
                if(!$s) {
                    $s = $this->set_cache($url, $this->remote_get($url), 10);
                }
                if($s) {
                    $s = json_decode($s);
                    $price = $s->{$coinid}->{$exchange};
                }
            }
            return $price;
        }

        /**
         * get location Infomation by IP (Block.cc)
         * https://data.block.cc/
         * https://data.block.cc/api/v1/ip/211.197.241.239
         */
        function get_ip_location($ip) {
            $url = "https://data.block.cc/api/v1/ip/={$ip}";
            $s = $this->remote_get($url);
            if($s!='') {
                $s = json_decode($s);
            }
            $t = '';
            if($s->code=='0') {
                $t = $s->data->country_name;
                /*{
  "code": 0,
  "message": "success",
  "data": {
    "ip": "211.197.241.239",
    "country_code": "KR",
    "country_name": "Korea, Republic of",
    "region_name": "Gyeonggi-do",
    "city_name": "Seongnam",
    "latitude": 37.43861,
    "longitude": 127.13778,
    "zip_code": "461-805",
    "time_zone": "+09:00"
  }
}*/
            }
            return $t;
        }

        /**
         * 차트의 시작 시간을 리턴합니다.
         * @param number 분값. 1분봉은 1. 3분봉은 3. 1시간봉은 60. 1일봉은 1440, 1주일봉은 1440*7, 1월봉은 1440*30, 단지 구분자입니다.
         * @param number 기준시간. Unix Timestamp. 없으면 서버시간. UTC0 시간대 값으로 넘겨주세요.
         * @return String 시작 날짜(2019-08-08 08:00:00). UTC0 시간대 값입니다.
         */
        function get_start_time($min, $time=false) {
            $d = '';
            $time = $time ? $time : time(); // - date('Z'); // UTC0 시간대의 unix timestamp로 변환. - 이유는 PHP에서 출력 시간대를 한국으로 잡았고 서버,DB 시간은 UTC0라서 그렇습니다. DB에 저장된 값이 UTC0날짜값이라서 UTC0날짜로 변환해야합니다.

            if($min<=60) { // 분봉
                $m = sprintf('%02d', intval(date('i',$time) / $min) * $min);
                $d = date("Y-m-d H:$m:00",$time);
            }
            if(60<$min && $min<1440 ) { // 시봉
                $h = intval($min / 60);
                $h = sprintf('%02d', intval(date('H',$time) / $h) * $h);
                $d = date("Y-m-d $h:00:00",$time);
            }
            if($min==1440) { // 일봉
                $t = $time; //time()-$min*60; // ?? 왜 하루를 뺐지? 현재시간을 사용하도록 수정.
                $m = sprintf('%02d', date("m", $t ));
                $d = sprintf('%02d', date("d", $t ));
                $h = intval($min / 60);
                $h = sprintf('%02d', intval(date('H',$time) / $h) * $h);
                $d = date("Y-m-$d $h:00:00",$time);
            }
            if($min==1440*7) { // 주봉 - 매주 월요일
                $w = date('w',$time);
                $t = strtotime('-'.($w-1).'days');
                $d = date("Y-m-d 00:00:00", $t);
            }
            if($min==1440*30) { // 월봉 - 매월 1일
                $d = date("Y-m-01 00:00:00",$time);
            }

            return $d;
        }

        // ----------------------------------------------------------------- //
        // AWS

        private $S3_KEY = 'AKIA42T7UG7KZBISQB7Z'; // kmcse 전용 회원 Access key 
        private $S3_SECRET_KEY = 'jMktTFwipepeWMp1m1XNnVb4D0iH8p7McHEl8nog'; // kmcse 전용 회원 Secret key 
        PRIVATE $S3_REGION = 'ap-northeast-2';
        PRIVATE $S3_BUCKET = 'teaplat';

        public function delete_file_to_s3($objecturl) {

            // 업로드된 파일 S3에 저장하기.
            $bucket = $this->S3_BUCKET;

            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
                'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                    'key' => $this->S3_KEY,
                    'secret' => $this->S3_SECRET_KEY
                )
            ]);

            // https://kmcse.s3.ap-northeast-2.amazonaws.com/tmp/201807/5ca75e71eba894e05f72e8f7918e41c48b1d29d4b55c2b6d7ac026e633f8432e.png
            $keyname = substr($objecturl, strpos($objecturl, '.amazonaws.com/')+strlen('.amazonaws.com/'));
            try {
                $result = $s3->deleteObject(array(
                    'Bucket' => $bucket,
                    'Key' => $keyname,
                ));
            } catch (S3Exception $e) {
                // echo $e->getMessage() . PHP_EOL;
                return false;
            }

            return $result['DeleteMarker'];

        }

        public function copy_tmpfile_to_s3($objecturl) {

            // 업로드된 파일 S3에 저장하기.
            $bucket = $this->S3_BUCKET;

            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
                'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                    'key' => $this->S3_KEY,
                    'secret' => $this->S3_SECRET_KEY
                )
            ]);

            $oldkeyname = substr($objecturl, strpos($objecturl, 'tmp/'));
            $keyname = str_replace('tmp/', '', $oldkeyname);

            try {
                $result = $s3->copyObject(array(
                    'Bucket' => $bucket,
                    'Key' => $keyname,
                    'CopySource'=> "{$bucket}/{$oldkeyname}",
                    'ACL'    => 'public-read' // file permission
                ));
            } catch (S3Exception $e) {
                // echo $e->getMessage() . PHP_EOL;
                return false;
            }

            return $result['ObjectURL'];

        }

        function move_tmpfile_to_s3($objecturl) {

            // 업로드된 파일 S3에 저장하기.
            $bucket = $this->S3_BUCKET;
    
            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
                'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                    'key' => $this->S3_KEY,
                    'secret' => $this->S3_SECRET_KEY
                )
            ]);
    
            $oldkeyname = substr($objecturl, strpos($objecturl, 'tmp/'));
            $keyname = str_replace('tmp/', '', $oldkeyname);
    
            try {
                $result = $s3->copyObject(array(
                    'Bucket' => $bucket,
                    'Key' => $keyname,
                    'CopySource'=> "{$bucket}/{$oldkeyname}",
                    'ACL'    => 'public-read' // file permission
                ));
            } catch (S3Exception $e) {
                // echo $e->getMessage() . PHP_EOL;
                return false;
            }
    
            $this->delete_file_to_s3($objecturl);
    
            return $result['ObjectURL'];
    
        }
    
        /**
         * save file to S3
         */
        public function save_file_to_s3($files) {

            // 업로드 파일 배열로 정리.
            // $_FILES 변수에 정의되는 형식으로 전달 받게 됩니다.
            // file_data[] 이름으로 보낼때 $files 구조는 다음과 같습니다.
            //     array(5) {
            //       ["name"]=>array(1) {[0]=>string(9) "kenny.png"}
            //       ["type"]=>array(1) {[0]=>string(9) "image/png"}
            //       ["tmp_name"]=>array(1) {[0]=>string(24) "C:\xampp\tmp\php7CAF.tmp"}
            //       ["error"]=>array(1) {[0]=>int(0)}
            //       ["size"]=>array(1) {[0]=>int(132929)}
            //     }
            // file_data 이름으로 보낼때 $files 구조는 다음과 같습니다.
            //     array(5) {
            //       ["name"]=>string(9) "kenny.png"
            //       ["type"]=>string(9) "image/png"
            //       ["tmp_name"]=>string(23) "C:\xampp\tmp\phpD7C.tmp"
            //       ["error"]=>int(0)
            //       ["size"]=>int(132929)
            //     }

            if(! is_array($files['name'])) {
                $files['name'] = array($files['name']);
                $files['type'] = array($files['type']);
                $files['tmp_name'] = array($files['tmp_name']);
                $files['error'] = array($files['error']);
                $files['size'] = array($files['size']);
            }

            // 업로드된 파일 S3에 저장하기.
            $bucket = $this->S3_BUCKET;

            $s3 = new S3Client([
                'version' => 'latest',
                'region'  => $this->S3_REGION, // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
                'credentials' => array( // 이렇게 해야 합니다. 아니면 아래 putObject 부분에서 curl 애러가 발생하며 접속도 못합니다.
                    'key' => $this->S3_KEY,
                    'secret' => $this->S3_SECRET_KEY
                )
            ]);

            $r = array();
            // try {
                for($i=0; $i<count($files['name']); $i++) {

                    $file_type = $files['type'][$i];
                    $file_size = $files['size'][$i];
                    $file_sourcefile = $files['tmp_name'][$i];
                    $file_old_name = $files['name'][$i];
                    $file_name = hash('sha256', basename($file_old_name).'-'.time().mt_rand(111111,999999));
                    $file_ext = end(explode('.',$file_old_name));
                    $keyname = 'tmp/'.date('Ym').'/'.$file_name.'.'.$file_ext;

                    // file check
                    if($file_size > 0 && file_exists($file_sourcefile)) {

                        // image orientation
                        if (strpos($file_type, 'image/')!==false && extension_loaded('imagick')) {
                            $manager = new ImageManager(array('driver' => 'imagick'));
                            $img = $manager->make($file_sourcefile)->orientate();
                            $img->save($file_sourcefile);
                        }

                        // Upload data.
                        $result = $s3->putObject([
                            'Bucket' => $bucket,    // file storeage
                            'Key'    => $keyname,   // file name
                            'SourceFile' => $file_sourcefile, // file contents
                            'ACL'    => 'public-read' // file permission
                        ]);
                        // Print the URL to the object.
                        // echo $result['ObjectURL'] . PHP_EOL;
                        // $r[] = array('file'=>$file, 'ur'=>$result['ObjectURL']);
                        $r[] = array('name'=>$file_old_name, 'url'=>$result['ObjectURL']);
                    } else {
                        $r[] = array('name'=>$file_old_name, 'url'=>'');
                    }

                }
                // if(count($r)==1) {
                //     $r = $r[0];
                // };
            // } catch (S3Exception $e) {
            //     var_dump($result, $e);
            //     exit('1');
            //     // echo $e->getMessage() . PHP_EOL;
            //     return false;
            // }

            return $r;

        }

        // ----------------------------------------------------------------- //
        // validator method

        function checkRetrunType($s)
        {
            if (! in_array($s, array('json', 'xml', 'tsv', 'csv', 'html'))) {
                $this->error('024', __('Please enter the correct return type.'));
            }
            return $s;
        }
        function checkSymbol($s)
        {
            if (preg_match('/[^0-9a-zA-Z,.]/', $s)) {
                $this->error('011', __('Please enter the correct symbol.'));
            }
            return $s;
        }
        function checkFeeAction($s)
        {
            $s = trim($s);
            $feeaction = array('', 'withdraw', 'receive', 'sell', 'buy', 'all'); // out -> withdraw,  in -> receive로 action 명 정정.
            if ( ! in_array($s, $feeaction)) {
                $this->error('015', __('Please enter the correct action value.'));
            }
            return $s;
        }
        function checkLoginUser($s) {
            if($s != $this->get_login_userid()) {
                $this->error('021', __('You can only view your personal information.'));
            }
            return $s;
        }
        /**
         * UUID check
         * base format https://en.wikipedia.org/wiki/Universally_unique_identifier
         * However, do not check the size for scalability.
         */
        function checkUUID($s) {
            if(preg_match('/[^0-9a-zA-Z\-]/', $s)) {
                $this->error('022', __('Please enter the correct UUID.'));
            }
            return $s;
        }

        function checkFileClass($s)
        {
            $_classes = array('profile','id','common');
            if (! in_array($s, $_classes)) {
                $this->error('011', __('Please enter the correct symbol.'));
            }
            return $s;
        }

        function checkDateFormat($s)
        {
            $d = date('Y-m-d', strtotime( $s ));
            if ($d!=$s) {
                $this->error('011', __('Please enter the correct date.'));
            }
            return $s;
        }

        function checkQuotePrice($s, $e)
        {
            $d = $this->get_quote_price($s, $e);
            if ($d!=$s) {
                $this->error('029', str_replace('{quote_unite}',$this->get_quote_unit($s, $e),__('Please enter the price according to the quotation unit({quote_unite}).')));
            }
            return $s;
        }

        function addHit($idx)
        {
            $this->query("UPDATE js_bbs_main SET hit=hit=1 WHERE idx='{$this->escape($idx)}'");
            return $idx;
        }

    }
    $GLOBALS['tradeapi'] = new TradeApi;
    define('__LOADED_TRADEAPI__', true);

    // ----------------------------------------------------------------- //
    // validator function

    function checkRetrunType($s)
    {
        return $GLOBALS['tradeapi']->checkRetrunType($s);
    }
    function checkSymbol($s)
    {
        return $GLOBALS['tradeapi']->checkSymbol($s);
    }
    function checkFeeAction($s)
    {
        return $GLOBALS['tradeapi']->checkFeeAction($s);
    }
    function checkLoginUser($s)
    {
        return $GLOBALS['tradeapi']->checkLoginUser($s);
    }
    function checkUUID($s)
    {
        return $GLOBALS['tradeapi']->checkUUID($s);
    }
    function checkFileClass($s)
    {
        return $GLOBALS['tradeapi']->checkFileClass($s);
    }
    function checkDateFormat($s)
    {
        return $GLOBALS['tradeapi']->checkDateFormat($s);
    }
    function checkQuotePrice($s, $e)
    {
        return $GLOBALS['tradeapi']->checkQuotePrice($s, $e);
    }
    if(!function_exists('addHit')){function addHit($idx){
        return $GLOBALS['tradeapi']->addHit($idx);
    }}

}

