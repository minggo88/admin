<?php
include dirname(__file__) . '/SimpleRestful.php';
include dirname(__file__) . '/Coind.php';
include_once dirname(__file__) . '/vendor/autoload.php';

use Twilio\Rest\Client as TwilioRestClient;
use paragraph1\phpFCM\Client as paragraph1phpFCMClient;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;

/**
 *
 */
if (!defined('__LOADED_EXCHANGEAPI__')) {
    class ExchangeApi extends SimpleRestful
    {

        public $default_exchange = 'KRW';

        /**
         * Exchange API Class 생성자
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
         * API 방식의 로그인 처리.
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
            $_SESSION['USER_NAME'] = $name ? $name : $row['name'];
            $_SESSION['USER_NICKNAME'] = $name ? $name : $row['nickname'];
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

        public function checkLogout()
        {
            if ($this->isLogin()) {
                $this->error('002',__('Please logout.'));
            }
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
        // coin jsonrpc helper

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

		/**
		 * 지갑 생성
		 * @param number $userno 회원번호. 내부에서는 __APP_NAME__.'/'.__API_RUNMODE__.'/'.$userno 형식으로 변환해 account로 사용합니다.
		 * @param string $symbol 심볼코드.
		 * @return Boolean|String  지갑 생성이 성공하면 새 주소 문자열을 리턴합니다. 실패시 false.
		 */
        public function create_wallet($userno, $symbol)
        {
            $coind = $this->load_coind($symbol);
            $r = $coind->genNewAddress($this->get_account_by_userno($userno), $userno);
            if($r===false && $coind->getError()) {
                $this->error('055', __('Failed to connect to coin server. details: ').$coind->getError());
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
        // exchange - utils

		/**
		 * generate order code
		 */
		public function gen_order_code($prefix='') {
			$time = str_replace('.','',sprintf('%01.6f', array_sum(explode(' ',microtime()))));// 16자리 milliseconds 순서대로 정렬시키기위해 랜덤숫자 제거 .mt_rand(111111,999999); 6자리랜덤숫자
			return strtoupper($prefix.base_convert($time, 10, 36)); // 36진법으로 변경
		}

        // ----------------------------------------------------------------- //
        // Model

        public function get_admin_phone_number($adminid='sms') {
            return $this->query_one("SELECT admin_mobile FROM js_admin WHERE adminid='{$this->escape($adminid)}' ");
        }


        /**
         * 지갑 관련 앱 버전 정보 조회
         * @param String device 앱 디바이스 정보.
         * @return Object 버전정보.
         */
        public function get_version($app, $device) {
            $sql = "select version, version_min, note from js_exchange_app_verison where app='".$this->escape($app)."' and device='".$this->escape($device)."' ";
            return $this->query_fetch_object($sql);
        }

        /**
         * 전자지갑 정보 조회
         * Exchange 에서는 미보유 암호화폐도 리턴하도록 쿼리 수정함.
         * 평가금액 및 현재가 정보도 포함하도록 추가함.
         * @param Number 회원번호
         * @param Symbol 종목코드(BTC, LTC, KRW)
         * @param Symbol 거래화폐(KRW, USD)
         * @return Object 전자지갑 정보. userno, symbol, confirmed, unconfirmed, address, regdate
         */
        public function get_wallet($userno, $symbol, $exchange='', $simple_query=false)
        {
            if($simple_query) {
                $sql = "SELECT userno, symbol, account, address, locked, confirmed, autolocked, regdate, deposit_check_time FROM js_exchange_wallet WHERE userno='{$this->escape($userno)}' ";
                if($symbol!='' && strtolower($symbol)!='all') {
                    $sql.= ' AND symbol="'.strtoupper($this->escape($symbol)).'" ';
                }
            } else {
		        $exchange = trim($exchange)=='' ? $this->default_exchange : $exchange;
		        $sql = "SELECT
                t1.symbol, t1.name, t1.display_decimals, t1.crypto_currency, t1.backup_address, t1.color, t1.transaction_outlink, t1.fee_exchange_ratio, t1.fee_out, t1.creatable, t1.menu, IFNULL(t1.fee_symbol, t1.symbol) fee_symbol, t1.withdrawable, t1.sendable, t1.out_min_volume, t1.out_max_volume, t1.out_max_volume_1day,
		        t2.userno, t2.confirmed, t2.unconfirmed, t2.locked, t2.autolocked, IF(t1.creatable='Y', t2.address, t1.backup_address) address, IFNULL(t2.confirmed, 0) balance, IFNULL(t2.confirmed*IFNULL(t1.price, 1), 0) balance_evaluated,
		        '{$this->escape($exchange)}' exchange, t2.regdate,
		        t3.price_open, t3.price_high, t3.price_low, IFNULL(t3.price_close, 1) price_close, t3.price_chagne_percent, t3.volume
		        FROM js_exchange_wallet t2
		        LEFT JOIN js_exchange_currency t1  ON t1.symbol=t2.symbol
		        LEFT JOIN js_exchange_price t3 ON t1.symbol=t3.symbol AND t3.exchange='{$this->escape($exchange)}'
		        WHERE t2.userno='{$this->escape($userno)}' ";
		        if($symbol!='' && strtolower($symbol)!='all') {
		            $sql.= ' AND t1.symbol="'.strtoupper($this->escape($symbol)).'"';
		        }
				$sql.= ' AND t2.active="Y" ';
				$sql.= ' AND t1.symbol IS NOT NULL ';
		        // $sql.= ' AND t2.symbol<>"FINT" ';
				// $sql.= ' ORDER BY t2.symbol';
				$sql.= ' ORDER BY t1.sortno';
			}
            // exit($sql);
            return $this->query_list_object($sql);
        }

        public function get_row_wallet($userno, $symbol)
        {
            $sql = "SELECT userno, symbol, locked, autolocked, confirmed, unconfirmed, account, address, regdate, deposit_check_time FROM js_exchange_wallet where userno='{$this->escape($userno)}' AND symbol='{$this->escape(strtoupper($symbol))}' ";
            return $this->query_fetch_object($sql);
        }

        /**
         * 이메일아이디로 로그인하던 회원을 위해 지갑이름을 이메일아이디로부터 구하는 함수.
         * @param String User ID
         * @return String 전자지갑이름.
         */
        public function get_member_by_userid($userid)
        {
            $sql = 'select userid, userpw from js_member where userid="'.$this->escape($userid).'" limit 1';
            return $this->query_fetch_object($sql);
        }

        /**
         * address로 전자지갑 정보 조회
         * @param String Wallet address
         * @param String Symbol
         * @return Object 전자지갑 정보. account, address, hashkey, name
         */
        public function get_wallet_by_address($address, $symbol)
        {
            $sql = 'select userno, symbol, account, address, locked, confirmed, autolocked, regdate, deposit_check_time from js_exchange_wallet where address="'.$this->escape($address).'" and symbol="'.$this->escape($symbol).'"  ';
            return $this->query_fetch_object($sql);
        }

        public function check_duplicated_transaction($userno, $amount) {
            $sql = "SELECT count(*) from js_exchange_wallet_txn where userno='".$this->escape($userno)."' AND amount='".$this->escape($amount)."' and regdate >= FROM_UNIXTIME(UNIX_TIMESTAMP()-5) ";
            return $this->query_one($sql) ? true : false;
        }

        public function get_list_wallet($userno, $symbol='')
        {
            $sql = 'select userno, symbol, confirmed, unconfirmed, address, regdate from js_exchange_wallet where userno='.$this->escape($userno).'';
            if($symbol!='') {
                $sql.= ' and symbol="'. strtoupper($this->escape($symbol)). '" ';
            }
            return $this->query_list_object($sql);
        }

        public function check_wallet_autolock($userno, $symbol) {
            $autolocked = false;
            $symbol = strtoupper($symbol);
            $walletinfo = $this->get_row_wallet($userno, $symbol);
            if($walletinfo->autolocked=='Y') { // autolocked Y 상태면 계속 잠금처리.
                $autolocked = true;
            } else {
                $sql = "SELECT COUNT(*) cnt FROM js_exchange_wallet_txn WHERE regdate > FROM_UNIXTIME(UNIX_TIMESTAMP() - 60) AND txn_type='S' AND userno='".$this->escape($userno)."'";
                $cnt = $this->query_one($sql);
                if($cnt>=9) { // 10번째 되는 시점에 autolock 걸림.
                    $autolocked = true;
                    $sql = "UPDATE js_exchange_wallet SET autolocked='Y' WHERE userno='".$this->escape($userno)."' AND symbol='".$this->escape($symbol)."'";
                    $this->query($sql);
                }
            }
            return $autolocked;
        }

        public function get_sendable_amount($userno, $symbol) {
            $walletinfo = $this->get_row_wallet($userno, $symbol);
            return $walletinfo ? $walletinfo->confirmed : 0;
        }

        /**
         * get wallet check deposit
         * @param String $symbopl search coin symbol.
         * @param Number $rows Number of rows on a page. default value is 50.
         * @param Array The array value containing the wallet object.
         */
        public function get_wallet_check_deposit($symbol, $rows=50) {
            $sql = "SELECT userno, symbol, account, address from js_exchange_wallet WHERE symbol in (select symbol from js_exchange_currency where active='Y' and check_deposit='Y') and address<>'' and  deposit_check_time < (UNIX_TIMESTAMP()-10) ";
            if($symbol!='') {
                $sql.= "AND symbol='".$this->escape($symbol)."' ";
            }
            $sql.= "ORDER BY deposit_check_time, regdate desc LIMIT 0, ".$this->escape($rows)."";
            return $this->query_list_object($sql);
        }

        public function get_unreceived_list($_receive_day, $rows=50) {
            $sql = "SELECT * FROM `js_exchange_share_link` WHERE reg_time < UNIX_TIMESTAMP()-60*60*24*{$_receive_day} AND pay_time='' AND return_time='' limit ".$this->escape($rows)."";
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
            return $this->query($sql);
        }

        public function save_wallet($userno, $symbol, $address, $confirmed=0) {
            $sql = 'insert into js_exchange_wallet set userno='.$this->escape($userno).', symbol="'.strtoupper($this->escape($symbol)).'", regdate=SYSDATE(), confirmed='.$this->escape($confirmed).', address="'.$this->escape($address).'" ON DUPLICATE KEY UPDATE address="'.$this->escape($address).'" ';
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
         * 새 지갑을 생성합니다.
         * create_wallet 은 블록체인에 지갑을 생성시키는 메소드고
         * gen_wallet을 create_wallet + save_wallet 메소드고
         * create_new_wallet은 기분화폐를 반영해서 생성하는 메소드입니다.
         * @param Number $userno 회원번호
         * @param String $symbol 화폐심볼 
         * @return String 주소
         */
        public function create_new_wallet($userno, $symbol) {
            // 기준화폐있는지 확인
            $currency = $this->get_currency($symbol);
            if ($currency[0] && $currency[0]->base_coin) { // 토큰인경우 base_coin 생성 후 적용
                // 기준화폐의 지갑 주소가 있는지 확인. 
                $base_coin_wallet = $this->get_wallet($userno, $currency[0]->base_coin);
                if ($base_coin_wallet[0] && $base_coin_wallet[0]->address) { // 기준화폐지급이 있으면 해당 주소를 사용.
                    $address = $base_coin_wallet[0]->address;
                } else { // 기준화폐 주소가 없으면 생성.
                    $address = $this->create_wallet($userno, $currency[0]->base_coin);
                    $this->save_wallet($userno, $currency[0]->base_coin, $address);
                }
            } else { // 독립 코인인경우 주소 생성
                $address = $this->create_wallet($userno, $symbol);
            }
            //var_dump($address);exit;
            if (!$address) {
                $coind = $this->load_coind($symbol);
                $errmsg = $coind->getError();
                $this->error('014', __('Failed to create new address. ') . $errmsg);
            }
            // save address
            $this->save_wallet($userno, $symbol, $address);
            $this->active_wallet($userno, $symbol);
            return $address;
        }

        public function get_wallet_txn_list($symbol, $userno, $page=1, $rows=10, $txnid='0', $sdate='', $edate='', $direction='', $order='newest', $app_no='') {
            $order = strtolower($order)=='oldest' ? 'ASC' : 'DESC';
            if($txnid>0) {$page=1;}
            if(strpos($symbol, ',')!==false || is_array($symbol)) {
                $symbol = explode(',', $symbol);
            } else {
                $symbol = array($symbol);
            }
            if(is_array($symbol)) {
                $symbol = array_map('strtoupper',array_map('trim',$symbol));
			}
            $sn = ($page-1) * $rows;
            $sql = " SELECT userno, txnid, symbol, unix_timestamp(txndate) `time`, unix_timestamp(regdate) `regtime`, regdate, IF(txn_type='R', address_relative, address) from_address, IF(txn_type='R', address, address_relative) to_address, if(direction='I', 'in', 'out') direction, amount, fee, status, address, key_relative, address_relative, txn_type, msg, app_no, (select app_type from js_app where js_app.app_no=js_exchange_wallet_txn.app_no) app_type, (select app_id from js_app where js_app.app_no=js_exchange_wallet_txn.app_no) app_id FROM js_exchange_wallet_txn FORCE INDEX(PRIMARY) WHERE symbol IN ('".implode("','",$symbol)."')  AND userno='".$this->escape($userno)."' AND txn_type NOT IN ('B') "; //
            if($txnid>0) {
                $sql.= " AND txnid < '$txnid' ";
            }
            if($direction!='') {
                $direction = $direction == 'in' ? 'I' : ( $direction =='out' ? 'O' : '');
                $sql.= " AND direction = '{$direction}' ";
            }
            if($sdate!='') {
                $sql.= " AND regdate >= '{$sdate} 00:00:00' ";
            }
            if($edate!='') {
                $sql.= " AND regdate <= '{$edate} 23:59:59' ";
            }
            if($app_no) {
                $sql.= " AND app_no = '{$this->escape($app_no)}' ";
            }
            $sql.= " ORDER BY txnid {$order} LIMIT ".$this->escape($sn).", ".$this->escape($rows)."";
            // exit($sql);
            $r = $this->query_list_object($sql);
            $currencies = $this->get_currency();
            foreach($currencies as $key => $val) {
                $currencies[$val->symbol] = $val;
                unset($currencies[$key]);
            }
            for($i=0; $i<count($r); $i++) {
                $row = $r[$i];
                $currency = $currencies[$row->symbol];
                if($currency->transaction_outlink) {
                    $r[$i]->transaction_outlink = '';
                    if($r[$i]->txn_type=='R'||$r[$i]->txn_type=='W') {
                        $r[$i]->transaction_outlink = str_replace('$key_relative', $r[$i]->key_relative, $currency->transaction_outlink);
                    }
                }

                // 거래내역 종류 문구 설정
                $r[$i]->txn_type_str = '';
                switch($r[$i]->txn_type) {
                    case 'R': $r[$i]->txn_type_str = __('Deposit'); break;
                    case 'W': $r[$i]->txn_type_str = __('Withdrawal'); break;
                    case 'E': $r[$i]->txn_type_str = __('Exchange'); break;
                    case 'A': $r[$i]->txn_type_str = __('Attendance'); break;
                    case 'I': $r[$i]->txn_type_str = __('Invite'); break;
                    case 'BO': $r[$i]->txn_type_str = __('Bonus'); break;
                    case 'S': $r[$i]->txn_type_str = ($r[$i]->direction=='in') ? __('받기') : __('Send'); break;
                }

                // 보낸사람 받는사람 정보 설정.
                if($r[$i]->txn_type='S') {
                    if($r[$i]->direction=='in') {
                        // 보낸사람 정보 만들기
                        $r[$i]->from_address = $row->address_relative;
                        $r[$i]->from_userno = $row->key_relative;
                        $r[$i]->from_userno = $this->query_one("SELECT userno FROM js_exchange_wallet WHERE symbol='{$this->escape($row->symbol)}' AND address='{$this->escape($row->address_relative)}'");
                        $r[$i]->from_name = $this->query_one("SELECT IFNULL(name,'') FROM js_member WHERE userno = '{$this->escape($r[$i]->from_userno)}'");
                        // 받는사람 정보 만들기
                        $r[$i]->to_address = $row->address;
                        $r[$i]->to_userno = $row->userno;
                        $r[$i]->to_name = $this->query_one("SELECT IFNULL(name,'') FROM js_member WHERE userno = '{$this->escape($row->userno)}'");
                    } else {
                        // 보낸사람 정보 만들기
                        $r[$i]->from_address = $row->address;
                        $r[$i]->from_userno = $row->userno;
                        $r[$i]->from_name = $this->query_one("SELECT IFNULL(name,'') FROM js_member WHERE userno = '{$this->escape($row->userno)}'");
                        // 받는사람 정보 만들기
                        $r[$i]->to_address = $row->address_relative;
						$r[$i]->to_userno = '';
						$r[$i]->to_name = '';
                        if($r[$i]->status=='O') { // 미처리시 받은 사람이 아직 가입을 않했기 때문에 address값을 그대로 사용.
							$r[$i]->to_userno = '';
							$r[$i]->to_name = $row->address_relative;
                        }
                        if($r[$i]->status=='D') { // 완료시 받은 사람이 가입 했기 때문에 이름을 가져온다.
                            $r[$i]->to_userno = $this->query_one("SELECT userno FROM js_exchange_wallet WHERE symbol='{$this->escape($row->symbol)}' AND address='{$this->escape($row->address_relative)}'");
                            $r[$i]->to_name = $this->query_one("SELECT IFNULL(name,'') FROM js_member WHERE userno = '{$this->escape($r[$i]->to_userno)}'");
                        }
                    }
                }

                $r[$i]->status_txt = $r[$i]->status=='D' ? __('Complete') : ($r[$i]->status=='P' ? __('Process') : ($r[$i]->status=='C' ? __('Cancel') : __('Waiting')));
                // 숫자형으로 변경.
                $r[$i]->amount = $this->clean_number($r[$i]->amount);
                $r[$i]->fee = $this->clean_number($r[$i]->fee);
            }
            return $r;
        }

        /**
         * find wallet transaction
         * @param Array $search search items by array type. key: columne name, value: search value.
         * @param Number $page search page. default value is 1.
         * @param Number $rows Number of rows on a page. default value is 10.
         */
        public function find_wallet_txn_list($search=array(), $page=1, $rows=10) {
            $sn = ($page-1) * $rows;
            $sql = "SELECT txnid, symbol, address, regdate, txndate, address_relative, txn_type, amount, fee, status, key_relative FROM js_exchange_wallet_txn WHERE 1 ";
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
            // exit($sql);
            return $this->query_list_object($sql);
        }

        /**
         * find unsended wallet transaction
         * 2019.05.09 GWS 지갑의 send txn 정보만 처리합니다. 타 블록체인 처리가 필요하면 쿼리에 symbol 추가하세요.
         * @param String $symbol Coin Symbol.
         * @param Number $rows Number of rows on a page. default value is 10.
         * @param String|Array $txn_type Transaction Type. S: 보내기(기본값), W: 출금(기본 제외). 여러개를 조회하려면 배열로 값 전달.
         */
        public function find_unsended_txn_list($symbol, $rows='10', $txn_type='S') {
            if(is_array($txn_type)) {
                $txn_type = implode("','",$txn_type);
            }
            // $sql = "SELECT SUBSTR(reg_time, 1, 10) reg_time, reg_time reg_time_origin, symbol, txnid, sender_address, sender_wallet_no, receiver_address, receiver_wallet_no, txn_type, amount, fee, `message` FROM js_exchange_wallet_txn WHERE symbol='{$this->escape($symbol)}' AND txnid='' AND txn_type='S' AND check_time<".(time()-60)." ORDER BY reg_time LIMIT 0, ".$this->escape($rows)."";
            $sql = "SELECT UNIX_TIMESTAMP(regdate) reg_time, UNIX_TIMESTAMP(regdate) reg_time_origin, symbol, txnid, address sender_address, userno, address_relative receiver_address, '' receiver_wallet_no, txn_type, amount, fee, msg `message`
			FROM js_exchange_wallet_txn WHERE symbol='{$this->escape($symbol)}' AND (key_relative='' OR key_relative IS NULL) AND txn_type IN ('{$txn_type}') AND direction='O' AND `status`='O' ORDER BY reg_time LIMIT 0, ".$this->escape($rows)."";
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
         * @param string key_relative. 입금/출금 트랜젝션 아이디.
         * @param string transaction datetime. YYYY-MM-DD HH:ii:ss
         */
        public function add_wallet_txn($userno, $address, $symbol, $address_relative, $txn_type, $direction, $amount, $fee, $tax, $status="O", $key_relative="", $txndate='', $msg='', $app_no='', $txn_method='COIN', $nft_id='') {
            $fee = trim( preg_replace('/[^0-9\-.]/','',$fee));
            $fee = $fee=='' ? '0' : $fee;
            $tax = trim( preg_replace('/[^0-9\-.]/','',$tax));
            $tax = $tax=='' ? '0' : $tax;
            $amount = trim( preg_replace('/[^0-9\-.]/','',$amount));
            $amount = $amount=='' ? '0' : $amount;
            $txndate = trim( $txndate );
            $txndate = $txndate=='' && $status=='D' ? date('Y-m-d H:i:s') : $txndate;

            $direction = trim( $direction );

            $sql = "insert into js_exchange_wallet_txn set ";
            // txnid = bigint(20) auto-increadable
            $sql.= " userno='".strtoupper($this->escape($userno))."', ";
            $sql.= " symbol='".strtoupper($this->escape($symbol))."', ";
            $sql.= " address='".$this->escape($address)."', ";
            $sql.= " regdate=sysdate(), ";
            if($txndate) {
                $sql.= " txndate='".$this->escape($txndate)."', ";
            }
            $sql.= " address_relative='".$this->escape($address_relative)."', ";
            $sql.= " txn_type='".$this->escape($txn_type)."', ";
            $sql.= " txn_method='".$this->escape($txn_method)."', ";
            $sql.= " direction='".$this->escape($direction)."', ";
            $sql.= " amount='".$this->escape($amount)."', ";
            $sql.= " fee='".$this->escape($fee)."', ";
            $sql.= " tax='".$this->escape($tax)."', ";
            $sql.= " status='".$this->escape($status)."', ";
            $sql.= " key_relative='".$this->escape($key_relative)."', ";
            $sql.= " msg='".$this->escape($msg)."', ";
            $sql.= " nft_id='".$this->escape($nft_id)."', ";
            $sql.= " app_no='".$this->escape($app_no)."' ";
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
            return $this->query($sql);
        }

        public function cancel_wallet_txn($userno, $symbol, $txnid) {
            $sql = "UPDATE js_exchange_wallet_txn SET status='C' WHERE userno='".$this->escape($userno)."' AND symbol='".$this->escape($symbol)."' AND status='O'";
            if(is_array($txnid)) {
                for($i=0;$i<count($txnid);$i++) {
                    $txnid[$i] = $this->escape($txnid[$i]);
                }
                $sql.= " AND txnid in ('" . implode("','",$txnid) . "') ";
            } else {
                $sql.= " AND txnid='".$this->escape($txnid)."' ";
            }
            return $this->query($sql);
        }


        public function get_fee($symbol, $action) {
            $sql = "SELECT fee_in, fee_out, fee_exchange_ratio, IFNULL(fee_symbol, symbol) fee_symbol FROM js_exchange_currency WHERE symbol='".strtoupper($this->escape($symbol))."' ";
            $currency = $this->query_fetch_object($sql);
            $out = array( 'action'=>'withdraw', 'fee'=>$currency->fee_out, 'unit_type'=>'fixed', 'symbol'=>$currency->fee_symbol );
            $in = array( 'action'=>'receive', 'fee'=>$currency->fee_in, 'unit_type'=>'fixed', 'symbol'=>$currency->fee_symbol );
            $exchange = array( 'action'=>'exchange', 'fee'=>$currency->fee_exchange_ratio, 'unit_type'=>'ratio', 'symbol'=>$currency->fee_symbol );
            $fee = array($out, $in, $exchange);
            switch($action) {
                case 'withdraw': $fee = array($out); break;
                case 'receive': $fee = array($in); break;
                case 'exchange': $fee = array($exchange); break;
            }
            return $fee;
        }

        public function cal_fee($symbol, $action, $amount) {
            $sql = "select fee_in, fee_out, fee_out_ratio, fee_exchange_ratio, display_decimals, IFNULL(fee_symbol, symbol) fee_symbol from js_exchange_currency where symbol='".strtoupper($this->escape($symbol))."' ";
            $currency = $this->query_fetch_object($sql);
            $fee = 0;
            switch($action) {
                case 'withdraw':
                    $fee = $currency->fee_out_ratio>0 ? ceil($currency->fee_out_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals) : $currency->fee_out*1;
                break;
                case 'receive': $fee = $currency->fee_in*1; break;
                case 'exchange': $fee = ceil($currency->fee_exchange_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals); break;
            }
            return $fee;
        }

        public function cal_tax($symbol, $action, $amount) {
            $sql = "select tax_in_ratio, tax_out_ratio, tax_exchange_ratio, display_decimals from js_exchange_currency where symbol='".strtoupper($this->escape($symbol))."' ";
            $currency = $this->query_fetch_object($sql);
            $tax = 0;
            switch($action) {
                case 'withdraw': $tax = $currency->tax_out; break;
                case 'receive': $tax = $currency->tax_in_ratio; break;
                case 'exchange': $tax = ceil($currency->tax_exchange_ratio * $amount * pow(10, $currency->display_decimals))/pow(10, $currency->display_decimals); break; // income tax는 여기서 말고 따로 계산하기.
            }
            return $tax;
        }

        public function get_currency($symbol='') {
            $sql = "select symbol, name, fee_in, fee_out, fee_out_ratio, fee_buy_ratio, fee_sell_ratio, tax_in_ratio, tax_out_ratio, trade_min_volume, trade_max_volume, out_min_volume, out_max_volume, out_max_volume_1day, display_decimals, regdate, creatable, backup_address, color, transaction_outlink, circulating_supply, max_supply, circulating_supply*price market_cap, price, crypto_currency, base_coin, active, auto_withdrwal_userno, icon_url, IFNULL(fee_symbol, symbol) fee_symbol, withdrawable, sendable, sortno, menu from js_exchange_currency where active='Y' ";
			if($symbol!='') {
				$sql .= " and symbol='".strtoupper($this->escape($symbol))."' ";
			}
            //$sql .= " order by  sortno";
            $sql .= "  ORDER BY menu DESC ";
            $r = $this->query_list_object($sql);
            for($i=0; $i<count($r); $i++) {
                // 숫자형으로 변경.
                $r[$i]->fee_in = $this->clean_number($r[$i]->fee_in);
                $r[$i]->fee_out = $this->clean_number($r[$i]->fee_out);
                $r[$i]->fee_out_ratio = $this->clean_number($r[$i]->fee_out_ratio);
                $r[$i]->fee_buy_ratio = $this->clean_number($r[$i]->fee_buy_ratio);
                $r[$i]->fee_sell_ratio = $this->clean_number($r[$i]->fee_sell_ratio);
                $r[$i]->tax_in_ratio = $this->clean_number($r[$i]->tax_in_ratio);
                $r[$i]->tax_out_ratio = $this->clean_number($r[$i]->tax_out_ratio);
                $r[$i]->trade_min_volume = $this->clean_number($r[$i]->trade_min_volume);
                $r[$i]->trade_max_volume = $this->clean_number($r[$i]->trade_max_volume);
                $r[$i]->out_min_volume = $this->clean_number($r[$i]->out_min_volume);
                $r[$i]->out_max_volume = $this->clean_number($r[$i]->out_max_volume);
                $r[$i]->out_max_volume_1day = $this->clean_number($r[$i]->out_max_volume_1day);
                $r[$i]->display_decimals = $this->clean_number($r[$i]->display_decimals);
                $r[$i]->circulating_supply = $this->clean_number($r[$i]->circulating_supply);
                $r[$i]->max_supply = $this->clean_number($r[$i]->max_supply);
                $r[$i]->market_cap = $this->clean_number($r[$i]->market_cap);
                $r[$i]->price = $this->clean_number($r[$i]->price);
            }
            return $r;
        }

        public function get_symbol() {
            $sql = "select symbol, name, regdate, color from js_exchange_currency where active='Y' and menu='Y' order by sortno ";
            return $this->query_list_object($sql);
        }

        public function get_last_price($symbol='', $exchange='KRW') {
            $symbol = strtoupper($this->escape($symbol));
            $exchange = strtoupper($this->escape($exchange));
            $sql = "select price_close from js_exchange_price where symbol='{$symbol}' and exchange='{$exchange}' ";
            $r = $this->query_fetch_object($sql);
            return $r->price_close ? $r->price_close*1 : 1;
        }

        public function get_display_decimals($symbol) {
            $symbol = strtoupper($this->escape($symbol));
            $sql = "select display_decimals from js_exchange_currency where symbol='{$symbol}' ";
            return $this->query_one($sql);
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
         * uniteglobal의 가격을 가져오는 걸로 바꿔야합니다.
         * 다만, 테이블은 그대로 사용합니다. 가격 변경은 crontab폴더에 스크립트로 작동 시킵니다.
         * @todo uniteglobal의 가격을 js_exchange_price 테이블에 반영시키는 스크립트 새로 만들기.
         */
        public function get_spot_price($symbol='', $exchange='KRW') {
            if(is_array($symbol)) {
                $symbol = implode("','", $symbol);
            } else {
                $symbol = trim($symbol);
            }
            $sql = "select symbol, volume, price_open, price_close, price_high, price_low, price_open_12, price_close_12, price_high_12, price_low_12, price_open_1, price_close_1, price_high_1, price_low_1 from js_exchange_price where 1 ";
            if($symbol!='') {
                $sql .= " and symbol in ('".strtoupper($symbol)."') ";
            }
            if($exchange!='') {
                $sql .= " and exchange='".strtoupper($this->escape($exchange))."' ";
            }

            $r = $this->query_list_object($sql);
            for($i=0 ; $i<count($r) ; $i++) {
                $row = $r[$i];
                $price = $row->price_close ? $row->price_close : 0;
                $digit = 0; //$this->get_quote_digit($price, $exchange);
                $r[$i]->volume = number_format($row->volume, 2, '.', '');
                $r[$i]->price_open = number_format($row->price_open, $digit, '.', '');
                $r[$i]->price_close = number_format($row->price_close, $digit, '.', '');
                $r[$i]->price_high = number_format($row->price_high, $digit, '.', '');
                $r[$i]->price_low = number_format($row->price_low, $digit, '.', '');
            }
            return $r;
        }

        public function set_default_exchange () {
            $default_exchange = $this->query_one("SELECT exchange FROM js_config_site WHERE active='1' AND domain='".$this->escape($_SERVER['HTTP_HOST'])."' ");
            $this->default_exchange = $default_exchange ? $default_exchange : $this->default_exchange;
        }

        /**
         * 현재가 저장하는 메소드
         * 거래소에서사 쓰일때는 거래소 테이블 조회로 insert를 했지만 교환소에서는 uniteglobal.io 사이트에서 api로 받아와서 저장해야합니다.
         * @todo uniteglobal.io 가격을 가져와서 저장되도록 수정하기.
         */
        // function set_current_price_data($symbol, $exchange) {
        //     $price = $this->get_current_price_data($symbol, $exchange);
        //     $sql_update = " volume='".$this->escape($price->volume)."', ";
        //     $sql_update.= " price_high='".$this->escape($price->price_high)."', ";
        //     $sql_update.= " price_low='".$this->escape($price->price_low)."', ";
        //     $sql_update.= " price_open='".$this->escape($price->price_open)."', ";
        //     $sql_update.= " price_close='".$this->escape($price->price_close)."', ";
        //     $sql_update.= " price_chagne_percent='".$this->escape($price->price_chagne_percent)."', ";
        //     $sql_update.= " volume_12='".$this->escape($price->volume_12)."', ";
        //     $sql_update.= " price_high_12='".$this->escape($price->price_high_12)."', ";
        //     $sql_update.= " price_low_12='".$this->escape($price->price_low_12)."', ";
        //     $sql_update.= " price_open_12='".$this->escape($price->price_open_12)."', ";
        //     $sql_update.= " price_close_12='".$this->escape($price->price_close_12)."', ";
        //     $sql_update.= " price_chagne_percent_12='".$this->escape($price->price_chagne_percent_12)."', ";
        //     $sql_update.= " volume_1='".$this->escape($price->volume_1)."', ";
        //     $sql_update.= " price_high_1='".$this->escape($price->price_high_1)."', ";
        //     $sql_update.= " price_low_1='".$this->escape($price->price_low_1)."', ";
        //     $sql_update.= " price_open_1='".$this->escape($price->price_open_1)."', ";
        //     $sql_update.= " price_close_1='".$this->escape($price->price_close_1)."', ";
        //     $sql_update.= " price_chagne_percent_1='".$this->escape($price->price_chagne_percent_1)."' ";

        //     $sql = " INSERT INTO js_exchange_price SET ";
        //     $sql.= " symbol='".strtoupper($this->escape($symbol))."', ";
        //     $sql.= " exchange='".strtoupper($this->escape($exchange))."', ";
        //     $sql.= $sql_update;
        //     $sql.= " ON DUPLICATE KEY UPDATE ";
        //     $sql.= $sql_update;
        //     $r = $this->query($sql);
        //     if($r) {
        //         $sql = "UPDATE js_exchange_currency SET price='{$this->escape($price->price_close)}' WHERE symbol='{$this->escape(strtoupper($symbol))}' ";
        //         $this->query($sql);
        //     }
        //     return $r;
        // }

        /**
         * 핸드폰 번호나 이메일로 가입여부를 확인합니다.
         * @param String 이메일로 검색할지 핸드폰번호로 검색할지 지정합니다. mobile: 핸드폰, email: 이메일, userid: 회원아이디
         * @param Array 검색할 값들을 배열에 담아서 전달합니다.
         * @return String 콤마로 묶은 가입된 전화번호나 이메일을 전달합니다.
         */
        function check_join($media, $values=array()) {
            $media = $media=='mobile' ? 'mobile' : ($media=='userid' ? 'userid' : 'email'); // 값이 컬럼명이라 전달받은 값은 비교용으로만 사용하고 값은 하드코딩합니다.
            $values = array_unique($values);
            $values = array_map(array($this, 'escape'), $values);
            $values = implode("','", $values);
            $sql = "SELECT {$media} AS `values`, '".__('joined')."' status  FROM js_member WHERE {$media} in ('".$values."') ";
            return $this->query_list_object($sql);
        }

        function check_waiting($media, $values=array()) {
            $media = $media=='mobile' ? 'M' : 'E';
            $values = array_unique($values);
            $values = array_map(array($this, 'escape'), $values);
            $values = implode("','", $values);
            $sql = "SELECT receiver_address AS `values`, '".__('in progress')."' status FROM js_exchange_share_link WHERE media='{$media}' and receiver_address in ('".$values."') ";
            return $this->query_list_object($sql);
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
            $sql.= " ON DUPLICATE KEY UPDATE ";
            $sql.= " os = '".$this->escape($os)."', ";
            $sql.= " fcm_tokenid = '".$this->escape($fcm_tokenid)."', ";
            $sql.= " ip = '".$this->escape($_SERVER['REMOTE_ADDR'])."' ";
            return $this->query($sql);
        }

        function get_fcm_info($userno, $uuid) {
            $sql = " SELECT userno, uuid, os, fcm_tokenid, regdate FROM js_member_device WHERE ";
            $sql.= " userno = '".$this->escape($userno)."' AND ";
            $sql.= " uuid = '".$this->escape($uuid)."' ";
            return $this->query_fetch_object($sql);
        }

        function save_withdraw($userno, $symbol, $real_amount, $fee, $to_address, $from_address) {
            $sql = " INSERT INTO js_exchange_wallet_txn SET";
            $sql.= " userno = '".$this->escape($userno)."', ";
            $sql.= " symbol = '".$this->escape($symbol)."', ";
            $sql.= " address = '".$this->escape($from_address)."', ";
            $sql.= " regdate = SYSDATE(), ";
            $sql.= " address_relative = '".$this->escape($to_address)."', ";
            $sql.= " txn_type = 'W', ";
            $sql.= " amount = '".$this->escape($real_amount)."', ";
            $sql.= " fee = '".$this->escape($fee)."', ";
            $sql.= " status = 'O' ";
            return $this->query($sql);
        }

        function get_member_info ($userno) {
            $sql = " SELECT userno, userid, name, nickname, mobile, mobile_country_code, email, bool_email, bool_sms, bool_push, regdate, otpkey, bool_confirm_email, bool_confirm_mobile, bool_confirm_idimage, bank_name, bank_account, bank_owner, bool_realname, image_identify_url, image_mix_url, pin FROM js_member WHERE userno = '".$this->escape($userno)."' ";
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

        function get_permission_code ($pin='', $bool_confirm_mobile=0, $bool_confirm_idimage=0) {
            $p[] = $pin ? '1' : '0';
            $p[] = $bool_confirm_mobile ? '1' : '0';
            $p[] = $bool_confirm_idimage ? '1' : '0';
            return implode('', $p);
        }

        function get_member_info_by_userid ($userid) {
            $sql = " SELECT userno, userid, userpw, name, nickname, mobile, email, bool_email, bool_sms, bool_push, regdate, otpkey, bool_confirm_email, bool_confirm_mobile, bool_realname, image_identify_url, image_mix_url, pin FROM js_member WHERE userid = '".$this->escape($userid)."' ";
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
            if(trim($data['pin'])!=''){$sql.= ", pin = md5('".$this->escape($data['pin'])."') ";}
            // if(isset($data['phone'])){$sql.= ", phone = '".$this->escape($data['phone'])."' ";}
            if(isset($data['mobile'])){$sql.= ", mobile = '".$this->escape($data['mobile'])."' ";}
            if(isset($data['email'])){$sql.= ", email = '".$this->escape($data['email'])."' ";}
            if(isset($data['bool_email'])){$sql.= ", bool_email = '".$this->escape($data['bool_email'])."' ";}
            if(isset($data['bool_sms'])){$sql.= ", bool_sms = '".$this->escape($data['bool_sms'])."' ";}
            if(isset($data['bool_push'])){$sql.= ", bool_push = '".$this->escape($data['bool_push'])."' ";}
            // if(isset($data['bool_lunar'])){$sql.= ", bool_lunar = '".$this->escape($data['bool_lunar'])."' ";}
            if(isset($data['birthday'])){$sql.= ", birthday = '".$this->escape($data['birthday'])."' ";}
            if(isset($data['image_identify_url'])){$sql.= ", image_identify_url = '".$this->escape($data['image_identify_url'])."' ";}
            if(isset($data['image_mix_url'])){$sql.= ", image_mix_url = '".$this->escape($data['image_mix_url'])."' ";}
            $sql.= " WHERE userno = '".$this->escape($data['userno'])."' ";
            return $this->query($sql);
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
            if($s->tickers) {
                foreach($s->tickers as $row) {
                    if($row->base == $SYMBOL && $row->target == $EXCHANGE && $row->market && $row->market->identifier) {
                        if($row->base == $SYMBOL && $row->target == $EXCHANGE && (empty($markets) || in_array($row->market->identifier, $markets)) ) {
                            $ohlc[] = array(
                                'market'=>$row->market->name,
                                'vol'=>$row->volume,
                                'last'=>$row->last,
                                'high'=>$row->last, // 값이 없는데 이전 형식에 맞추기 위해 넣음.
                                'low'=>$row->last, // 값이 없는데 이전 형식에 맞추기 위해 넣음.
                                'change_daily'=>'' // 값이 없는데 이전 형식에 맞추기 위해 넣음.
                            );
                            break;
                        }
                        $ohlc = $s;
                    }
                }
            }
            return $ohlc;
        }

        /**
         * get Other Market Price Infomation (Block.cc)
         * https://data.block.cc/
         * https://data.block.cc/doc/?shell#price
         */
        function get_other_list($symbol, $exchange, $market='') {
            $s = $this->get_external_ticker($symbol,$exchange);
            $t = array();
            if($s->code=='0') {
                foreach($s->data->list as $row) {
                    $t[] = array(
                        'site_name'=>ucwords($row->market),
                        'volume'=>$row->vol,
                        'price_close'=>$row->last,
                        'price_high'=>$row->high,
                        'price_low'=>$row->low,
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
        function get_external_spot_price($SYMBOL,$EXCHANGE, $market='upbit') {
            $price = 0;
            $coinid = '';
            $symbol = strtolower($SYMBOL);
            $exchange = strtolower($EXCHANGE);
            $list = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_list.json'));
            foreach($list as $row) {
                if($symbol == strtolower($row->symbol)) {$coinid=$row->id; break;}
            }
            if($market) {
                $m = json_decode(file_get_contents(__SRF_DIR__.'/data/coingecko_exchanges.json'));
                foreach($m as $row) {
                    if($row->id == strtolower($market)) {$market=$row->id; break;}
                }
            }
            if($coinid) { //  && $market
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
         * 환율 조회
         * @param String 기준 화폐. USD, BTC, ETH, KRW
         * @param String 교환 화폐. USD, KRW
         * @return number 교환비.소숫점 6자리. usd -> btc일때 1USD =  0.000026BTC. 소숫점6자리 미만은 0으로 표시됨. 
         */
        function get_exchange_rate_price($SYMBOL,$EXCHANGE) {
            // https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@{apiVersion}/{date}/{endpoint}
            // https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/btc/usd.json
            // https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/krw/usd.min.json
            // https://raw.githubusercontent.com/fawazahmed0/currency-api/1/latest/currencies/btc/usd.json
            $price = 0;
            $symbol = strtolower($SYMBOL);
            $exchange = strtolower($EXCHANGE);
            $url = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/{$symbol}/{$exchange}.min.json";
            $s = $this->get_cache($url);
            if(!$s) {
                $s = $this->set_cache($url, $this->remote_get($url), 60);
            }
            if($s) {
                $s = (array) json_decode($s);
                $price = $s[$exchange];
            }
            return $price;
        }


        // ----------------------------------------------------------------- //
        // SMS

        /**
         * SMS 발송
         * @param String 국가코드 (2글자)
         * @param String 핸드폰번호 (국가번호포함). 예: +8201093277306
         * @param String SMS 메시지.
         * @param String 예약발송날짜. 예: 2019.07.01.01:00:00
         * @return Boolena 작업결과. false: 미발송, true: 발송.
         */
        function send_sms($country_code, $tran_phone, $tran_msg, $tran_date='')
        {
            $row = $this->get_config('js_config_sms'); //,'tran_callback, guest_no, guest_key'
            $row->tran_callback = str_replace('-','',$row->tran_callback);
            $tran_phone = str_replace('-','',$tran_phone);
            //$userid = $row->guest_no;           // 문자나라 아이디
            //$passwd = $row->guest_key;           // 문자나라 비밀번호
            //$hpSender = $row->tran_callback;         // 보내는분 핸드폰번호
            //$adminPhone = $row->tran_callback;       // 비상시 메시지를 받으실 관리자 핸드폰번호
            //$hpReceiver = $tran_phone;       // 받는분의 핸드폰번호
            //$hpMesg = $tran_msg;           // 메시지
            $tran_date = empty($tran_date) ? '' : $tran_date;

            // send
            if($country_code=='KR') {
                // 한국 neosolution.com 사용 코드
                if(! $this->send_sms_twilio($row->guest_no, $row->guest_key, $row->tran_callback, $tran_phone, $tran_msg)) {
                    return false;
                }
            } else {
                // 글로벌용 twilio.com 사용 코드
                if(! $this->send_sms_twilio($row->guest_no, $row->guest_key, $row->tran_callback, $tran_phone, $tran_msg)) {
                    return false;
                }
            }

            // save send log
            $this->query("INSERT INTO js_sms SET tran_phone='{$this->escape($tran_phone)}', tran_callback='{$this->escape($row->tran_callback)}', tran_date='{$this->escape($tran_date)}', tran_msg='{$this->escape($tran_msg)}', tran_result='success', regdate=UNIX_TIMESTAMP() ");

            return true;
        }

        /**
         * send sms international
         * https://www.twilio.com/console
         * @param String $sid 고객번호
         * @param String $token 고객비밀번호
         * @param String $from 발송자 전화번호
         * @param String $to 수신자 전화번호
         * @param String $msg 전송 메시지
         * @return Mixed 전송 txn id. 빈문자열 : 전송 실패. 문자열: 전송 성공.
         */
        private function send_sms_twilio($sid, $token, $from, $to, $msg) {
            if(trim($msg)=='' || trim($to)=='' || !$sid || !$token || !$from) {
                return null;
            }

            $to = preg_replace('/[^0-9\+]/', '', $to);

            // Your Account SID and Auth Token from twilio.com/console
            $client = new TwilioRestClient($sid, $token);
            // Use the client to do fun stuff like send text messages!
            $_r = null;
            try {
                $call = $client->messages->create(
                    // the number you'd like to send the message to
                    $to,
                    array(
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => $from, //'+18782052142',
                        // the body of the text message you'd like to send
                        'body' => $msg
                    )
                );
                $_r = $call->sid;
            } catch (Exception $e) {}
            return $_r;
        }

        /**
         * twilio lookup api를 이용해 핸드폰번호가 올바른지 확인합니다.
         * https://www.twilio.com/lookup
         * https://www.twilio.com/docs/lookup/quickstart?code-sample=code-lookup-with-national-formatted-number&code-language=PHP&code-sdk-version=5.x
         */
        public function lookup_phone_number_twilio($phone_number) {
            if(trim($phone_number)=='') {
                return null;
            }
            $_r = null;
            try {
                $config = $this->query_fetch_object("select guest_no, guest_key from js_config_sms where code='basic' ");
                $sid = $config->guest_no;
                $token = $config->guest_key;
                $url = "https://{$sid}:{$token}@lookups.twilio.com/v1/PhoneNumbers/{$phone_number}";
                $r = json_decode($this->remote_get($url));
                // var_dump($r); //exit;
                if($r->status!="404") {
                    $_r = preg_replace('/^\+/', '', $r->phone_number);
                }
            } catch (Exception $e) {}
            return $_r;
        }
        /**
         * 정규식으로 국제전화번호가 있는 전화번호로 만들기
         * 01012341234
         * 12345678901
         * 821012341234
         * 123456789012
         * +8201012341234
         * 12345678901234
         *
         * 국제전화번호 형식이라면 + 포함 14자리수라고 합니다. https://codingdojang.com/scode/354
         * 위키에서는 11~15자리라고 하네요. 쩝. https://en.wikipedia.org/wiki/E.164
         * 우리는 +와 0을 제거하니 14자리 전화번호는 12자리가 됩니다. 만약 15자리 국제전화번호였다면 13자리가 될겁니다. 13자리 미만은 국제전화 번호가 없다고 처리하는것이 맞을것 같아 이를 정규식으로 처리합니다.
         * 만약 국제전화번호가 없다면 접속자 아이피 기준 국가번호를 붙여 리턴합니다.
         * @param number $phone_number 전화번호
         * @return number 국제전화번호 포함된 전화번호
         */
        public function lookup_phone_number_regexp ($phone_number) {
            $t = preg_replace('/[^0-9]/', '', $phone_number.'');
            if($t=='') {
                return null;
            }
            if(
                strlen($t)<=10  // 10자리 이하는 그냥 국가번호 없는걸로 처리.
            ) {
                $country_calling_code = preg_replace('/[^0-9]/','',$this->get_country_calling_code($_SERVER['REMOTE_ADDR']));
                $phone_number = $country_calling_code . $t;
            }
            return $phone_number;
        }


        /**
         * neosolution 문자발송
         * @param String 고객번호
         * @param String 고객비밀번호
         * @param String 발송자 전화번호
         * @param String 수신자 전화번호
         * @param String 전송 메시지
         * @param String 예약 전송 날짜.
         */
        private function send_sms_neosolution($guest_no, $guest_key, $tran_callback, $tran_phone, $tran_msg, $tran_date='' ) {
            $guest_no = urlencode($guest_no);
            $guest_key = urlencode($guest_key);
            $tran_phone = urlencode($tran_phone);
            $tran_callback = urlencode($tran_callback);
            $tran_date = urlencode($tran_date);
            $tran_msg = urlencode(iconv("UTF-8", "EUC-KR",$tran_msg));
            $url = "http://www.nesolution.com/service/sms.aspx?cmd=SendSms&method=GET&";
            $url.= "guest_no={$guest_no}&guest_key={$guest_key}&tran_phone={$tran_phone}&";
            $url.= "tran_callback={$tran_callback}&tran_date={$tran_date}&tran_msg={$tran_msg}";
            $result = $this->remote_request($url);
            // $snoopy->fetchtext($url);
            // $send_result = iconv('EUC-KR', 'UTF-8', $snoopy->results);
        }

        // firebase smarttalk-wallet
        private $_APIKEY_ = 'AAAAlaZtcAA:APA91bH11JUOmKCux8Ev1hqUcg3XSZu0_w32bcpJfyjKVHwQ86ptKxCxIbIe5v0DtOG4JWxfbE0-xRgpN4rmUJj6MPTMX22FLYTj1c9PzGljj8myFliPUEsGQjAYNHQAK0IHL6CeKf-j';

        public function send_fcm_message($user_token, $body, $title='KMCSE', $data=array(), $icon=null, $color='#ffffff', $badge=1) {
            $user_token = is_array($user_token) ? $user_token : ( $user_token ? array($user_token) : false );
            $icon = $icon ? $icon : 'https://www.kmcse.com/img/favicon_64.png';
            if(!$user_token || count($user_token)<1) {return false;}
            $client = new paragraph1phpFCMClient();
            $client->setApiKey($this->_APIKEY_);
            $client->injectHttpClient(new \GuzzleHttp\Client());
            $note = new Notification($title, $body);
            $note->setIcon($icon)
                ->setColor($color)
                ->setBadge($badge);
            $message = new Message();
            foreach($user_token as $token) {
                $message->addRecipient(new Device($token));
            }
            $message->setNotification($note);
            if(!empty($data)) {$message->setData($data);}
            $response = $client->send($message);
            return $response->getStatusCode()=='200' ? true : false;
        }

        /**
         * FCM 데이터 전송
         * @param String|Array $user_token FCM Token값들
         * @param Array $data 전송할 데이터.
         * @return Boolean 전송 결과.
         */
        public function send_fcm_data($user_token, $data) {
            $user_token = is_array($user_token) ? $user_token : ( $user_token ? array($user_token) : false );
            if(!$user_token || count($user_token)<1) {return false;}
            if(!is_array($data) || empty($data)) {return false;}
            $client = new paragraph1phpFCMClient();
            $client->setApiKey($this->_APIKEY_);
            $client->injectHttpClient(new \GuzzleHttp\Client());
            $message = new Message();
            foreach($user_token as $token) {
                $message->addRecipient(new Device($token));
            }
            $message->setData( $data );
            $response = $client->send($message);
            // var_dump($response);
            return $response->getStatusCode()=='200' ? true : false;
        }

        private $country_calling_codes = array('93','355','213','1684','376','244','1264','672','1268','54','374','297','61','43','994','1242','973','880','1246','375','32','501','229','1441','975','591','387','267','55','246','1284','673','359','226','257','855','237','1','238','1345','236','235','56','86','57','269','242','243','682','506','385','357','420','45','253','1767','18','670','593','20','503','240','291','372','251','298','500','679','358','33','594','689','241','220','995','49','233','350','30','299','1473','590','1671','502','224','245','592','509','504','852','36','354','91','62','964','353','44','972','39','225','1876','81','962','7','254','686','965','996','856','371','961','266','231','218','423','370','352','853','389','261','265','60','960','223','356','692','596','222','230','262','52','691','373','377','976','382','1664','212','258','264','674','977','31','599','687','64','505','227','234','683','6723','1670','47','968','92','680','970','507','675','595','51','63','48','351','1','974','262','40','7','250','685','378','239','966','221','381','248','232','65','421','386','677','252','27','82','34','94','290','1869','1758','1721','508','1784','597','268','46','41','886','992','255','66','228','690','676','1868','216','90','993','1649','688','1340','256','380','971','44','1','598','998','678','58','84','681','967','260','263');

        public function remove_country_calling_code_devider($phone_number) {
            foreach($this->country_calling_codes as $code) {
                $phone_number = preg_replace('/^'.$code.'0/', $code, $phone_number);
            }
            return $phone_number;
        }

        public function add_country_calling_code_devider($phone_number) {
            foreach($this->country_calling_codes as $code) {
                $phone_number = preg_replace('/^'.$code.'([1-9])(.*)/', $code."0$1$2", $phone_number);
            }
            return $phone_number;
        }

        /**
         * 전화번호의 국가번호, 지역번호 부분을 전산에서 사용하는 번호로 맞춤.
         * 중복없는 유니크한 번호가 됩니다.
         *
         * https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers
         *
         * @param String 국가번호가 있는 전화번호
         * @return Number 전산에서 사용하는 국가번호가 있는 전화번호
         */
        public function reset_phone_number($phone_number) {
            $phone_number = preg_replace('/[^0-9]/','',$phone_number); // +82 처럼 국가번호구분자인 + 값을 모두 지운다
            // 국가번호 없으면 접속자 아이피 기반으로 국가번호 설정합니다.
            if(preg_match('/^0/', $phone_number)) {
                $country_calling_code = preg_replace('/[^0-9]/','',$this->get_country_calling_code($_SERVER['REMOTE_ADDR']));
                $phone_number = $country_calling_code . preg_replace('/^0/', '', $phone_number, 0);
            }
            $phone_number = preg_replace('/^0/', '', $phone_number, 0); // 전화번호 앞 지역번호 삭제

            $phone_number = preg_replace('/(8210.{8}).*/','$1',$phone_number); //010뒤 8자리만 사용하도록 수정
            $phone_number = preg_replace('/(82010.{8}).*/','$1',$phone_number); //010뒤 8자리만 사용하도록 수정
            if(preg_match('/^01/', $phone_number) && strlen($phone_number)=='11') { // 01x 8888 9999 - 한국 핸더폰 번호 또는 미국,캐나다 번호... 중복가능성있어 twilio lookup api를 사용합니다. (무료, 속도느림주의)
                $phone_number = $this->lookup_phone_number_regexp($phone_number);
                // $lookup_result = $this->lookup_phone_number_twilio($phone_number);
                // if($lookup_result) { // lookup 결과가 있으면 올바른 국제전화번호형식입니다.
                //     $phone_number = $lookup_result;
                // } else { // lookup 결과가 없으면 국제전화번호형식이 아닙니다.
                //     $phone_number = preg_replace('/^0+/','',$phone_number);
                //     $phone_number = '82'.$phone_number;
                // }
            } else {
                $phone_number = preg_replace('/^0+/','',$phone_number); // 082 처럼 의미없는 0이 앞에 있는경우 제거합니다. (국가번호 없이 들어오는것이 있어서 제외)
            }
            $phone_number = $this->remove_country_calling_code_devider($phone_number); //국가번호 + 지역번호 사이 구분자(0) 처리. 82010... 으로 들어오는 것은 8210으로 변경하기.
            return $phone_number;
        }

        // ----------------------------------------------------------------- //
        // validator method

        function checkMedia($s)
        {
            $media = array('mobile','email','userid');
            if (!in_array($s, $media)) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please enter the correct media.'));
            }
            return $s;
        }
        function checkFeeAction($s)
        {
            $s = trim($s);
            $feeaction = array('', 'withdraw', 'receive', 'exchange', 'all'); // out -> withdraw,  in -> receive로 action 명 정정.
            if ( ! in_array($s, $feeaction)) {
                $this->error('015', $GLOBALS['simplerestful']->displayParamName().__('Please enter the correct action value.'));
            }
            return $s;
        }
        function checkLoginUser($s) {
            if($s != $this->get_login_userid()) {
                $this->error('021', $GLOBALS['simplerestful']->displayParamName().__('You can only view your personal information.'));
            }
            return $s;
        }

        function checkCountryCode($s)
        {
            if($s && !preg_match('/^[A-Z]{2}$/', $s)) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please enter the correct country code.'));
            }
            return $s;
        }

        function checkMobileNumber($s)
        {
            if($s && preg_match('/[^0-9\+\-]/', $s)) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please enter the correct mobile number.'));
            }
            return $s;
        }

        function checkIncludedCallingCode($s)
        {
            $r = false;
            foreach($this->country_calling_codes as $code) {
                if(preg_match('/^'.$code.'/', $s)) {
                    $r = true;
                    break;
                }
            }
            if(!$r) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please add a country calling code.'));
            }
            return $s;
        }

        function checkSocialName($s)
        {
            $social_names = array('kakao','naver','google','mobile','email','guest','facebook','');
            if(! in_array($s,$social_names)) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please enter the correct social name.'));
            }
            return $s;
        }

        function checkPinNumber($s)
        {
            if(strlen($s)!=6) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please enter 6 numbers for PIN number.'));
            }
            return $s;
        }

        function checkShareType($s)
        {
            $share_type = array('send','invite');
            if(! in_array($s,$share_type)) {
                $this->error('011', $GLOBALS['simplerestful']->displayParamName().__('Please enter the correct share type.'));
            }
            return $s;
        }

    }
    $GLOBALS['exchangeapi'] = new ExchangeApi;
    define('__LOADED_EXCHANGEAPI__', true);

    // ----------------------------------------------------------------- //
    // validator function

    if(!function_exists('checkMedia')){function checkMedia($s){
        return $GLOBALS['exchangeapi']->checkMedia($s);
    }}
    if(!function_exists('checkFeeAction')){function checkFeeAction($s){
            return $GLOBALS['exchangeapi']->checkFeeAction($s);
    }}
    if(!function_exists('checkLoginUser')){function checkLoginUser($s){
        return $GLOBALS['exchangeapi']->checkLoginUser($s);
    }}
    if(!function_exists('checkQuotePrice')){function checkQuotePrice($s, $e){
        return $GLOBALS['exchangeapi']->checkQuotePrice($s, $e);
    }}
    if(!function_exists('checkCountryCode')){function checkCountryCode($s){
        return $GLOBALS['exchangeapi']->checkCountryCode($s);
    }}
    if(!function_exists('checkMobileNumber')){function checkMobileNumber($s){
        return $GLOBALS['exchangeapi']->checkMobileNumber($s);
    }}
    if(!function_exists('checkIncludedCallingCode')){function checkIncludedCallingCode($s){
        return $GLOBALS['exchangeapi']->checkIncludedCallingCode($s);
    }}
    if(!function_exists('checkSocialName')){function checkSocialName($s){
        return $GLOBALS['exchangeapi']->checkSocialName($s);
    }}
    if(!function_exists('checkPinNumber')){function checkPinNumber($s){
        return $GLOBALS['exchangeapi']->checkPinNumber($s);
    }}
    if(!function_exists('checkShareType')){function checkShareType($s){
        return $GLOBALS['exchangeapi']->checkShareType($s);
    }}
    if(!function_exists('isSymbol')){function isSymbol($s){
        if (preg_match('/[^0-9a-zA-Z,.]/', $s)) {
            $GLOBALS['ledgerapi']->error('011', __('Please enter the correct symbol.'));
        }
        return $s;
    }}

}

