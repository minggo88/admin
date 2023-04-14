<?php
/*--------------------------------------------
Date : 2012-09-01
Author : Danny Hwang
comment :
--------------------------------------------*/

class Ledger extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_wallet_wallet';
		$config['query_func'] = 'ledgerQuery';
		$config['write_mode'] = 'ajax';

		/************************************/
		// $config['file_dir'] = '/data/bbs';
		// $config['thumb_dir'] = '/data/thumbnail';
		// $config['temp_dir'] = '/data/editorTemp';
		// $config['editor_dir'] = '/data/editor';

		/************************************/
		$config['no_tag'] = array('walletno','symbol','address','account','hashkey','name','regdate','otpkey','otppin','otplock','locked','autolocked','walletkey','balance','core','deposit_check_time');
		$config['no_space'] = array('walletno','symbol','address','account','hashkey','regdate','otpkey','otppin','otplock','locked','autolocked','walletkey','balance','core','deposit_check_time');
		$config['staple_article'] = array();

		/************************************/
		$config['bool_file'] = FALSE;
		$config['file_target'] = array();
		$config['file_size'] = 2;
		$config['upload_limit'] = FALSE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_width'] = 0;
		$config['thumb_height'] = 0;
		$config['thumb_size'] = array();

		/************************************/
		$config['bool_editor'] = FALSE;
		$config['editor_target'] = array();
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = FALSE;
		$config['editor_thumb_width'] = 150;
		$config['editor_thumb_height'] = 150;

		/************************************/
		$config['bool_navi_page'] = TRUE;
		if(empty($_GET['loop_scale'])) {
			$config['loop_scale'] = 20;
		}
		else {
			$config['loop_scale'] = $_GET['loop_scale'];
		}
		$config['page_scale'] = 20;
		$config['navi_url'] = '/ledger/admin/ledgerAdmin.php';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';// ajax or link
		$config['navi_load_id'] = 'main_contents';

		$this->BASIC($config,$tpl);
	}

	/**
	 * 목록
	 * pg_mode : list
	 */
	function lists($bool_return=false)
	{
		$query = array();
		$query['table_name'] = " js_exchange_wallet_txn t left join js_member m1 on t.userno=m1.userno left join js_member m2 on t.address_relative=m2.mobile AND m2.mobile <> '' AND m2.mobile IS NOT NULL left join js_exchange_wallet w on t.address_relative=w.address and w.symbol=t.symbol ";
		$query['tool'] = 'select';

		$query['fields'] = " t.regdate, t.txndate, t.symbol, t.address, t.amount, t.fee, t.fee_relative, t.address_relative, t.txnid, t.userno, t.status, t.direction, t.txn_type, t.key_relative, m1.userid userid, m1.name username, if(w.userno is null , m2.userid, w.userno) userno_relative, (select userid from js_member where userno=if(w.userno is null , m2.userid, w.userno)) userid_relative, (select name from js_member where userno=if(w.userno is null , m2.userid, w.userno)) username_relative ";
		$query['where'] = " WHERE t.symbol='{$this->dbcon->escape($_POST['symbol'])}' and t.txn_type NOT IN ('B','I') ".$this->srchQry2();
		if($_REQUEST['filter_out_txn']=='Y') {
			$query['where'].= " and m2.userid is null and w.userno is null and t.userno<>'2' ";
			if($_REQUEST['symbol']=='HTC') {
				$query['where'].= " and t.address_relative like '0x%' and t.regdate>='2021-02-16 00:00:00' ";
			}
			if($_REQUEST['symbol']=='GWS') {
				$query['where'].= " and t.address_relative like '0x%' and t.regdate>='2021-03-03 14:00:00' ";
			}
			if($_REQUEST['inout']=='out') {
				$query['where'].= " and t.txn_type='S' and t.direction='O' and t.status='D' ";
			}
			if($_REQUEST['inout']=='in') {
				$query['where'].= " and t.txn_type='R' and t.direction='I' and t.status='D' ";
			}
		}
		//날짜 기간 검색
		if($_POST['start_date']){
			$t = $_POST['start_date'];
			$query['where'] .= " AND '{$this->dbcon->escape($t)}' <= t.txndate ";
		}
		if($_POST['end_date']){
			$t = $_POST['end_date'];
			$query['where'] .= " AND t.txndate <= '{$this->dbcon->escape($t)} 23:59:59' ";
		}
		if($_GET['sort_target'] ) {
			$query['order'] = ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['order'] .= ', ';
				}
				if($_GET['sort_target'][$i]=='name') {
					$query['order'] .= 'm.'.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
				} else {
					$query['order'] .= 't.'.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
				}
			}
		} else {
			$query['order'] = ' ORDER BY t.regdate DESC';
		}
		// var_dump($query); exit;
		$list = $this->bList($query,'loop','_lists',true);
		return $list;
	}

	// 미사용 - js 에서 처리
	function _lists($row)
	{
		global $tradeapi;
		unset($row['account']);
		if($_POST['pg_mode']=='transaction_list') {
			// 추천한사람 추천받은사람 상세설정
			if($row['txn_type']=='I'||$row['txn_type']=='IR'||$row['txn_type']=='IE') {
				$invite_info = $this->dbcon->query_unique_object("select userno, receiver_address from js_exchange_share_link where id='".$this->dbcon->escape($row['key_relative'])."'");
				$inviter_info = $this->dbcon->query_unique_object("select userno, userid, name from js_member where userno='".$this->dbcon->escape($invite_info->userno)."'");
				$row['inviter_userno'] = $inviter_info->userno;
				$row['inviter_userid'] = $inviter_info->userid;
				$row['inviter_username'] = $inviter_info->name;
				$invitee_info = $this->dbcon->query_unique_object("select userno, userid, name from js_member where userid like '%".$this->dbcon->escape($invite_info->receiver_address)."'");
				$row['invitee_userno'] = $invitee_info->userno;
				$row['invitee_userid'] = $invitee_info->userid;
				$row['invitee_username'] = $invitee_info->name;
			}

			// 보낸사람정보, 받는사람정보 상세설정
			if($row['direction']=='I') { // 받은경우
				$row['receiver_userid'] = $row['userid'];
				$row['receiver_userno'] = $row['userno'];
				$row['receiver_username'] = $row['username'];
				$row['receiver_address'] = $row['address'];
				$row['sender_address'] = $row['address_relative'];
				if($row['userid_relative']) {
					$row['sender_userid'] = $row['userid_relative'];
					$row['sender_userno'] = $row['userno_relative'];
					$row['sender_username'] = $row['username_relative'];
				} else {
					// 전화번호로 검색
					$exbds_member = $this->dbcon->query_unique_object("select * from js_member where mobile='".$this->dbcon->escape($row['address_relative'])."'");
					if($exbds_member) {
						$row['sender_userid'] = $exbds_member->userid;
						$row['sender_userno'] = $exbds_member->userno;
						$row['sender_username'] = $exbds_member->name;
					} else {
						// 지갑에서 확인해봅니다.
						$morrow_wallet = null;
						// $morrow_wallet = $this->dbcon->query_unique_object("select * from morrow_wallet.js_exchange_wallet where address='".$this->dbcon->escape($row['address_relative'])."'");
						if($morrow_wallet) {
							$morrow_member = $this->dbcon->query_unique_object("select * from morrow_wallet.js_member where userno='".$this->dbcon->escape($morrow_wallet->userno)."'");
							$row['sender_userid'] = $morrow_wallet->userid;
							$row['sender_userno'] = $morrow_member->userno;
							$row['sender_username'] = ($morrow_member->name ? $morrow_member->name : $morrow_member->bank_owner).'<span class="text-danger">(지갑)</span>';
						} else {
							$row['sender_userno'] = '';
							$row['sender_userid'] = '';
							$row['sender_username'] = '<span class="text-danger">외부지갑</span>';
						}
					}
				}
			} else { // 보낸경우
				$row['sender_userid'] = $row['userid'];
				$row['sender_userno'] = $row['userno'];
				$row['sender_username'] = $row['username'];
				$row['sender_address'] = $row['address'];
				$row['receiver_address'] = $row['address_relative'];
				if($row['userid_relative']) {
					$row['receiver_userid'] = $row['userid_relative'];
					$row['receiver_userno'] = $row['userno_relative'];
					$row['receiver_username'] = $row['username_relative'];
				} else {
					// 전화번호로 검색
					$exbds_member = $this->dbcon->query_unique_object("select * from js_member where mobile='".$this->dbcon->escape($row['address_relative'])."'");
					if($exbds_member) {
						$row['receiver_userid'] = $exbds_member->userid;
						$row['receiver_userno'] = $exbds_member->userno;
						$row['receiver_username'] = $exbds_member->name;
					} else {
						// 지갑에서 확인해봅니다.
						$morrow_wallet = null;
						// $morrow_wallet = $this->dbcon->query_unique_object("select * from morrow_wallet.js_exchange_wallet where address='".$this->dbcon->escape($row['address_relative'])."'");
						if($morrow_wallet) {
							$morrow_member = $this->dbcon->query_unique_object("select * from morrow_wallet.js_member where userid='".$this->dbcon->escape($morrow_wallet->name)."'");
							$row['receiver_userid'] = $morrow_wallet->userid;
							$row['receiver_userno'] = $morrow_member->userno;
							$row['receiver_username'] = ($morrow_member->name ? $morrow_member->name : $morrow_member->bank_owner).'<span class="text-danger">(지갑)</span>';
						} else {
							$row['receiver_userno'] = '';
							$row['receiver_userid'] = '';
							$row['receiver_username'] = '<span class="text-danger">외부지갑</span>';
						}
					}
				}
			}
		}

		// 외부 전송 수수료
		if($_REQUEST['filter_out_txn']=='Y' && isset($_REQUEST['inout']) && !$row['fee_relative'] && $row['key_relative']) {
			$row['fee_relative'] = $tradeapi->get_transaction_fee ($row['symbol'], $row['key_relative'], $row['address']);
			if($row['fee_relative']) {
				$tradeapi->query("UPDATE exbds.js_exchange_wallet_txn SET fee_relative='{$tradeapi->escape($row['fee_relative'])}' WHERE txnid='{$tradeapi->escape($row['txnid'])}' ");
			}
		}

		return $row;
	}

	function view()
	{
		if(empty($_GET['userid'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['fields'] = "*";
		$query['where'] = 'where userid=\''.$_GET['userid'].'\'';
		$this->bView($query);
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _view($row)
	{
		$row['regdate'] = date('Y-m-d H:s:i',$row['regdate']);
		return $row;
	}

	function editForm()
	{
		// $query = array();
		// $query['table_name'] = $this->config['table_name'];
		// $query['tool'] = 'row';
		// $query['fields'] = "userid,
		// 	userpw,
		// 	name,
		// 	nickname,
		// 	sid_a,
		// 	sid_b,
		// 	SUBSTRING_INDEX(phone, '-', 1) AS phone_a,
		// 	SUBSTRING_INDEX(SUBSTRING_INDEX(phone, '-', -2), '-', 1) AS phone_b,
		// 	SUBSTRING_INDEX(phone, '-', -1) AS phone_c,
		// 	SUBSTRING_INDEX(mobile, '-', 1) AS mobile_a,
		// 	mobile,
		// 	SUBSTRING_INDEX(SUBSTRING_INDEX(mobile, '-', -2), '-', 1) AS mobile_b,
		// 	SUBSTRING_INDEX(mobile, '-', -1) AS mobile_c,
		// 	email,
		// 	SUBSTRING_INDEX(email, '@', 1) AS email_a,
		// 	SUBSTRING_INDEX(email, '@', -1) AS email_b,
		// 	zipcode,
		// 	address_a,
		// 	address_b,
		// 	bool_email,
		// 	bool_sms,
		// 	bool_lunar,
		// 	level_code,
		// 	pin,
		// 	regdate,
		// 	bool_confirm_email,
		// 	bool_confirm_mobile,
		// 	bool_email_krw_input,
		// 	bool_sms_krw_input,
		// 	bool_email_krw_output,
		// 	bool_sms_krw_output,
		// 	bool_email_btc_trade,
		// 	bool_email_btc_input,
		// 	bool_email_btc_output
		// 	";
		// $userid = $this->tpl->skin == 'admin' ? $_GET['userid'] :	 $_SESSION['USER_ID'];
		// $query['where'] = 'where userid=\''.$userid.'\'';
		// $row = $this->dbcon->query($query);
		// //$row['pin'] = str_replace('**',$row['pin']);
		// $row['regdate'] = date('Y-m-d H:s:i',$row['regdate']);
		// $this->tpl->assign($row);
		// $this->tpl->assign('srch_url',$this->srchUrl());

		// // 본인인증 정보 다시 확인하도록 로직 추가
		// $query = "select * from js_realname where userid='$userid' ";
		// $this->tpl->assign('realname_info',$this->dbcon->query_unique_array($query));
	}

	function write()
	{
	}

	function edit()
	{
		// global $config_basic;
		// if(empty($_POST['userid'])) {
		// 	if($config_basic['bool_ssl'] > 0) {
		// 		errMsg('회원아이디가 필요합니다.!');
		// 	}
		// 	else {
		// 		jsonMsg(0,'회원아이디가 필요합니다.!');
		// 	}
		// }
		// $query = "select captcha_string from fusion_captcha where captcha_ip='" . $_COOKIE['token'] . "' ";
		// $code = strtoupper($this->dbcon->query_unique_value($query));

		// if($this->tpl->skin != 'admin') {  // 2014-07-09 : 고객요청사항 - 관리자는 패스할께요 Danny

		// 	if( $code != strtoupper($_POST['securimagecode']) ) {
		// 		if($config_basic['bool_ssl'] > 0) {
		// 			errMsg('올바른 그림인증문자를 입력해주세요.');
		// 		}
		// 		else {
		// 			jsonMsg(0, '올바른 그림인증문자를 입력해주세요.');
		// 		}
		// 	}

		// }
		// if(empty($_POST['bool_passwd'])) {
		// 	$_POST['userpw'] = '';
		// }
		// $query = array();
		// $query['where'] = 'where userid=\''.$_POST['userid'].'\'';
		// if($this->bEdit($query,$_POST)) {
		// 	if($config_basic['bool_ssl'] > 0) {
		// 		replaceGo('//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
		// 	}
		// 	else {
		// 		jsonMsg(1);
		// 	}
		// }
		// else {
		// 	if($config_basic['bool_ssl'] > 0) {
		// 		errMsg('회원정보 수정이 정상적으로 수행되지 않았습니다.!\n\n관리자에게 문의하세요.!');
		// 	}
		// 	else {
		// 		jsonMsg(0);
		// 	}
		// }
	}

	function _write($arr)
	{
		//[2014-06-23 benant] phone, mobile 사용하지 않음. 아래 주석은 번호를 3개로 나눠서 받을때 사용함.
//		@ $arr['phone'] = $arr['phone_a'].'-'.$arr['phone_b'].'-'.$arr['phone_c'];
		//[2014-07-31 benant] mobile 이 주석이 풀려서인지 회원정보 수정할때 값을 넘기지 않는데 mobile이 빈값으로 설정되면서 기존 값을 지워버림. 문제해결을 위해 주석처리하려다가 혹시 다른곳에서는 사용할수도 있어서 그냥 수정할때는 mobile이 설정되지 않도록 함.
		// if($_POST['pg_mode']!='edit'){
		// 	$arr['mobile'] = $arr['mobile_a'].$arr['mobile_b'].$arr['mobile_c'];
		// }
		// //@ $arr['email'] = $arr['email_a'].'@'.$arr['email_b'];
		// if(!empty($arr['userpw'])) {
		// 	$arr['userpw'] = md5($arr['userpw']);
		// }

		// if(!empty($arr['sid_b'])) {
		// 	$arr['sid_a'] = (string) $arr['sid_a'];
		// 	$arr['sid_b'] = (string) $arr['sid_b'];
		// 	//$arr['sid_b'] = md5($arr['sid_b']);
		// }
		// $arr['bool_email'] = empty($arr['bool_email']) ? 0 : 1;
		// $arr['bool_sms'] = empty($arr['bool_sms']) ? 0 : 1;
		// return $arr;
	}

	//회원삭제시 회원 주문 데이터
	function del($walletno='',$bool_return=0)
	{
		if(empty($userid)) {
			$userid = $_GET['userid'];
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'delete';
		$query['where'] = 'where walletno=\''.$walletno.'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);

		// [2014-07-26] 본인인증 정보 삭제 추가
		// 본인인증 뿐만 아니라 회원거래내역 및 bitcoin 계좌 정보까지 모두 삭제해야 할것으로 보임.
		// 아니면 사용한 아이디로는 다시는 사용못하도록 해야 함.
		$query = "delete from js_realname where userid='$userid' ";
		@$this->dbcon->query($query);

		if(!$result) {
			if($bool_return > 0) {
				return false;
			}
			else {
				jsonMsg(0);
			}
		}
		else {
			if($bool_return > 0) {
				return true;
			}
			else {
				jsonMsg(1);
			}
		}
	}

	//체크항목 삭제하기
	function multiDel()
	{
		if(empty($_GET['userid'])) {
			jsonMsg(0);
		}
		foreach ($_GET['userid'] as $key => $val) {
			$result = $this->del($val,1);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function srchQry()
	{
		$arr = array();
		if(!empty($_GET['name'])) {
			$arr[] = " name like '%{$this->dbcon->escape($_GET['name'])}%' ";
		}
		if($_POST['pg_mode']=='transaction_list') {
			if(trim($_GET['searchval'])!='') {
				$arr[] = " t1.`txnid` like '%{$this->dbcon->escape($_GET['searchval'])}%' || t1.`sender_address` like '%{$this->dbcon->escape($_GET['searchval'])}%' || t1.`receiver_address` like '%{$this->dbcon->escape($_GET['searchval'])}%' || t2.`name` like '%{$this->dbcon->escape($_GET['searchval'])}%' || t3.`name` like '%{$this->dbcon->escape($_GET['searchval'])}%' ";
			}
		} else {
			if(trim($_GET['searchval'])!='') {
				$arr[] = " `name` like '%{$this->dbcon->escape($_GET['searchval'])}%' ";
			}
		}
		$ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';
		return $ret;
	}

	function srchQry2()
	{
		$arr = array();
		if(trim($_GET['searchval'])!='') {
			// txn_type
			$txn_type = '';
			switch($_GET['searchval']) { // 트렌젝션 종류. R:(외부)입금, W:(외부)출금, D:배당, E:교환, A:출석체크, I:초대하기, S:보내기, P:결제(pay), BO:보너스, R:환불(refund), DO: HTP 받음, EQ:ArATube Event QRCode
				case '입금': case '외부입금': $txn_type = "OR t.txn_type='R'"; break;
				case '출금': case '외부출금': $txn_type = "OR t.txn_type='W'"; break;
				case '배당': $txn_type = "OR t.txn_type='D'"; break;
				case '출석체크': $txn_type = "OR t.txn_type='A'"; break;
				case '추천': $txn_type = "OR t.txn_type IN ('IE','IR')"; break;
				case '피추천인': $txn_type = "OR t.txn_type='IE'"; break;
				case '추천인': $txn_type = "OR t.txn_type='IR'"; break;
				case '보내기': $txn_type = "OR t.txn_type='S'"; break;
				case '결제': $txn_type = "OR t.txn_type='P'"; break;
				case '보너스': $txn_type = "OR t.txn_type='BO'"; break;
				// case '환불': $txn_type = "OR t.txn_type='R'"; break;
				case '아라튜브 후원': case '후원': $txn_type = "OR t.txn_type='DO'"; break;
				case '아라튜브 이벤트 참여': case '이벤트 참여': $txn_type = "OR t.txn_type='EQ'"; break;
				case '경매 입찰': case '입찰': $txn_type = "OR t.txn_type='Au'"; break;
				case '경매 낙찰': case '낙찰': $txn_type = "OR t.txn_type='AS'"; break;
				case '경매 유찰': case '유찰': $txn_type = "OR t.txn_type='AR'"; break;
			}

			//
			$arr[] = " t.txnid like '%{$this->dbcon->escape($_GET['searchval'])}%' OR t.address like '%{$this->dbcon->escape($_GET['searchval'])}%'
			OR t.address_relative like '%{$this->dbcon->escape($_GET['searchval'])}%' OR m1.name like '%{$this->dbcon->escape($_GET['searchval'])}%' OR m1.userid like '%{$this->dbcon->escape($_GET['searchval'])}%' {$txn_type} ";
		}

		$ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';
		return $ret;
	}

	function srchUrl($hide_sort=0,$hide_loop=0)
	{
		$arr = array();
		if(!empty($_GET['name'])) { $arr[] = 'name='.$_GET['name']; }
		if(!empty($_GET['searchkey'])) { $arr[] = 'searchkey='.$_GET['searchkey']; }
		if(!empty($_GET['searchval'])) { $arr[] = 'searchval='.$_GET['searchval']; }
		if(!$hide_sort) {
			if(!empty($_GET['sort_target'])) { $arr[] = 'sort_target='.$_GET['sort_target']; }
			if(!empty($_GET['sort_method'])) { $arr[] = 'sort_method='.$_GET['sort_method']; }
		}
		$ret = sizeof($arr) > 0 ? '&'.implode('&',$arr) : '';
		return $ret;
	}

	/**
	 * 잔액 조회 대표 메소드.
	 * @param String 잔액 조회 대상. wallet : 미리 계산된 잔액.  txn : 트랜젝션에서 계산하는 잔액. BC: 블록체인 잔액
	 */
	public function getBalance ($source='wallet', $walletno='') {
		$method = '_get_balance_'.strtolower($source);
		if(method_exists($this, $method)){
			return $this->{$method}($walletno);
		}
	}

	public function bc_transaction_list () {
		$address = $_GET['searchval'];
		if($address) {
			$wallet = $this->get_wallet_by_address($address, 'GWS');
		} else {
			$wallet['address'] = '*';
			$wallet['account'] = '*';
			$wallet['symbol'] = 'GWS';
		}
		include __DIR__."/../api/lib/Coind.php";
		$Coind = new Coind($wallet['symbol']);
		$b = $Coind->getListTransactionAddress($wallet['address'], $wallet['account'], $this->config['loop_scale'], $_GET['start']); // 너무 많이 가져오는 걸 막기위해 $js->config['loop_scale'] 만큼만 가져오도록 함.
		return $b ? $b : 0;
	}

	function get_wallet ($walletno) {
		$query = array();
		$query['fields'] = "*";
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = "where walletno='{$this->dbcon->escape($walletno)}'";
		return $this->dbcon->query_unique_array($query);
	}
	function get_wallet_by_address ($address, $symbol='GWS') {
		$query = array();
		$query['fields'] = "*";
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = "where address='{$this->dbcon->escape($address)}' and symbol='{$this->dbcon->escape($symbol)}' ";
		return $this->dbcon->query_unique_array($query);
	}
	function get_wallet_name ($walletno) {
		$query = "select name from ".$this->config['table_name']."  where walletno='{$this->dbcon->escape($walletno)}'";
		return $this->dbcon->query_unique_value($query);
	}



}

function ledgerQuery($arr)
{
	$qry = array();
	// if(!empty($arr['walletno']))  { $qry[] = 'walletno=\''.$arr['walletno'].'\''; }
	if(!empty($arr['symbol']))  { $qry[] = 'symbol=\''.$arr['symbol'].'\''; }
	if(isset($arr['account']))  { $qry[] = 'account=\''.$arr['account'].'\''; }
	if(isset($arr['address']))  { $qry[] = 'address=\''.$arr['address'].'\''; }
	if(isset($arr['hashkey']))  { $qry[] = 'hashkey=\''.$arr['hashkey'].'\''; }
	if(isset($arr['name']))  { $qry[] = 'name=\''.$arr['name'].'\''; }
	if(isset($arr['otpkey']))  { $qry[] = 'otpkey=\''.$arr['otpkey'].'\''; }
	if(isset($arr['otppin']))  { $qry[] = 'otppin=\''.$arr['otppin'].'\''; }
	if(isset($arr['otplock']))  { $qry[] = 'otplock=\''.$arr['otplock'].'\''; }
	if(isset($arr['locked']))  { $qry[] = 'locked=\''.$arr['locked'].'\''; }
	if(isset($arr['autolocked']))  { $qry[] = 'autolocked=\''.$arr['autolocked'].'\''; }
	if(isset($arr['walletkey']))  { $qry[] = 'walletkey=\''.$arr['walletkey'].'\''; }
	if(isset($arr['balance']))  { $qry[] = 'balance=\''.$arr['balance'].'\''; }
	if(isset($arr['core']))  { $qry[] = 'core=\''.$arr['core'].'\''; }
	if(isset($arr['deposit_check_time']))  { $qry[] = 'deposit_check_time=\''.$arr['deposit_check_time'].'\''; }
	if($_POST['pg_mode'] == 'write') {
		$qry[] = 'regdate=UNIX_TIMESTAMP()';
	}else {
		if(isset($arr['regdate']))  { $qry[] = 'regdate=\''.$arr['regdate'].'\''; }
	}
	$qry = implode(',',$qry);
	return $qry;
}
