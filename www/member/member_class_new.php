<?php
/*--------------------------------------------
Date : 2012-09-01
Author : Danny Hwang
comment :
--------------------------------------------*/

class Member extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_member';
		$config['query_func'] = 'memberQuery';
		$config['write_mode'] = 'ajax';

		/************************************/
		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';

		/************************************/
		$config['no_tag'] = array('address_a','address_b','email_a','email_b');
		$config['no_space'] = array('userid','email_a','email_b');
		$config['staple_article'] = array();

		/************************************/
		$config['bool_file'] = FALSE;
		$config['file_target'] = array('imageIdentify','imageMix');
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
			$config['loop_scale'] = 10;
		}
		else {
			$config['loop_scale'] = $GLOBALS['_GET_ESCAPE']['loop_scale'];
		}
		$config['page_scale'] = 10;
		$config['navi_url'] = '/member/admin/memberAdmin.php';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';// ajax or link
		$config['navi_load_id'] = 'main_contents';

		$this->BASIC($config,$tpl);
	}

	/**************************************
	pg_mode = list
	관리자모드에서 회원 목록을 보여주는 부분
	** mode **
	member
	withdraw
	**************************************/
	function lists($mode ='member')
	{
		if($mode == 'member') {
			$table_name = $this->config['table_name'];
		}
		else {
			$table_name = 'js_member_withdraw';
		}

		if (empty($_GET['sort_target'])) {
			$sort_target = 'regdate';
		}
		else {
			$sort_target = $GLOBALS['_GET_ESCAPE']['sort_target'];
		}

		if (empty($_GET['sort_method'])) {
			$sort_method = 'desc';
		}
		else {
			$sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
		}

		$sort = ' order by '.$sort_target.' '.$sort_method;
		$query = array();
		$query['table_name'] = $table_name;
		$query['tool'] = 'select';
		$query['fields'] = "userid,
			userpw,
			name,
			nickname,
			sid_a,
			sid_b,
			phone,
			mobile,
			mobile_country_code,
			email,
			zipcode,
			address_a,
			address_b,
			IF(bool_email>0,'수신함','수신안함') AS bool_email,
			IF(bool_sms>0,'수신함','수신안함') AS bool_sms,
			level_code,
			bool_confirm_email,
			bool_confirm_mobile,
			bool_confirm_idimage,
			bool_confirm_bank,
			bank_account,
			image_identify_url,
			image_mix_url,
			gender,
			(select level_name from js_member_level where level_code=".$table_name.".level_code) AS level_name,regdate";
		$query['where'] = 'where 1 '.$this->srchQry().$sort;
		//var_dump($query);
		$this->config['navi_qry'] = $this->srchUrl();
		$this->bList($query);
		$this->tpl->assign('srch_url_sort',$this->srchUrl(1,0));
		$this->tpl->assign('srch_url_loop',$this->srchUrl(0,1));
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _lists($row)
	{
		if($this->config['mode'] == 'withdraw') {
			$query = array();
			$query['table_name'] = 'js_withdraw';
			$query['tool'] = 'row';
			$query['fields'] = 'contents,regdate';
			$query['where'] = 'where userid=\''.$this->dbcon->escape($row['userid']).'\'';
			$row_withdraw = $this->dbcon->query($query,__FILE__,__LINE__);
			$row['contents'] = $row_withdraw['contents'];
			$row['withdrawdate'] = date('Y-m-d',$row_withdraw['regdate']);
		}
		$row['total_emoney'] = empty($row['total_emoney']) ? 0 : $row['total_emoney'];
		$row['total_order_amount'] = empty($row['total_order_amount']) ? 0 : $row['total_order_amount'];
		$row['total_pay_amount'] = empty($row['total_pay_amount']) ? 0 : $row['total_pay_amount'];
		return $row;
	}

	function listLevel()
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select';
		$query['where'] = 'order by ranking';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_level',$loop);

		$query = array();
		$query['tool'] = 'select';
		$query['where'] = 'where level_code=\''.$GLOBALS['_GET_ESCAPE']['level_code'].'\' order by regdate desc';
		$this->config['navi_qry'] = $this->srchUrl();
		$this->bList($query,'loop','_listLevel');
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _listLevel($row)
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select';
		$query['where'] = 'order by ranking';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$row['select_id'] = $row['userid'].'_'.$row['no'];
		$option_level = '<select id="'.$row['select_id'].'">';
		while ($_row = mysqli_fetch_assoc($result)) {
			$selected = ($row['level_code'] == $_row['level_code']) ? 'selected="selected"' : '';
			$option_level .= '<option value="'.$_row['level_code'].'" '.$selected.'>'.$_row['level_name'].'</option>';
		}
		$option_level .= '</select>';
		$row['level'] = $option_level;
		return $row;
	}

	function loopLevel()
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select';
		$query['fields'] = 'level_code,level_name';
		$query['where'] = 'where ranking < 100 order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_level',$loop);
	}

	function view($mode='member')
	{
		if(empty($_GET['userid'])) {
			jsonMsg(0);
		}

		if($mode == 'member') {
			$table_name = $this->config['table_name'];
		}
		else {
			$table_name = 'js_member_withdraw';
		}
		$query = array();
		$query['table_name'] = $table_name;
		$query['fields'] = "userid,
			userpw,
			name,
			nickname,
			sid_a,
			sid_b,
			phone,
			mobile,
			mobile_country_code,
			email,
			zipcode,
			address_a,
			address_b,
			IF(bool_email>0,'수신함','수신안함') AS bool_email,
			IF(bool_sms>0,'수신함','수신안함') AS bool_sms,
			level_code,
			bool_confirm_email,
			bool_confirm_mobile,
			bool_confirm_idimage,
			bool_confirm_bank,
			bank_account,
			(select level_name from js_member_level where level_code=".$table_name.".level_code) AS level_name,
			regdate";
		$query['where'] = 'where userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';
		$this->bView($query);
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function get_member_info($userno) {
		$sql = "select * from js_member where userno='{$userno}'";
		return $this->dbcon->query_unique_array($sql);
	}

	function _view($row)
	{
		if($this->config['mode'] == 'withdraw') {
			$query = array();
			$query['table_name'] = 'js_withdraw';
			$query['tool'] = 'row';
			$query['fields'] = 'contents,regdate';
			$query['where'] = 'where userid=\''.$this->dbcon->escape($row['userid']).'\'';
			$row_withdraw = $this->dbcon->query($query,__FILE__,__LINE__);
			$row['contents'] = $row_withdraw['contents'];
			$row['withdrawdate'] = date('Y-m-d',$row_withdraw['regdate']);
		}
		$row['regdate'] = date('Y-m-d H:s:i',$row['regdate']);
		$row['permission'] = $this->get_permission_code($row['bool_confirm_mobile'], $row['bool_confirm_idimage'], $row['bank_account'], $row['bool_confirm_bank']);
		return $row;
	}

	function clauseForm()
	{
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['tool'] = 'row';
		$query['fields'] = 'clause_agreement, clause_private';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$row['clause_agreement'] = htmlspecialchars_decode($row['clause_agreement']);
		$row['clause_private'] = htmlspecialchars_decode($row['clause_private']);
		$this->tpl->assign($row);
	}

	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = "userid,
			userpw,
			name,
			nickname,
			sid_a,
			sid_b,
			SUBSTRING_INDEX(phone, '-', 1) AS phone_a,
			SUBSTRING_INDEX(SUBSTRING_INDEX(phone, '-', -2), '-', 1) AS phone_b,
			SUBSTRING_INDEX(phone, '-', -1) AS phone_c,
			SUBSTRING_INDEX(mobile, '-', 1) AS mobile_a,
			mobile,
			mobile_country_code,
			SUBSTRING_INDEX(SUBSTRING_INDEX(mobile, '-', -2), '-', 1) AS mobile_b,
			SUBSTRING_INDEX(mobile, '-', -1) AS mobile_c,
			email,
			SUBSTRING_INDEX(email, '@', 1) AS email_a,
			SUBSTRING_INDEX(email, '@', -1) AS email_b,
			birthday,
			zipcode,
			address_a,
			address_b,
			bool_email,
			bool_sms,
			bool_lunar,
			level_code,
			pin,
			regdate,
			bool_confirm_email,
			bool_confirm_mobile,
			bool_confirm_idimage,
			bank_name,
			bank_account,
			bank_owner,
			bool_confirm_bank,
			bool_email_krw_input,
			bool_sms_krw_input,
			bool_email_krw_output,
			bool_sms_krw_output,
			bool_email_btc_trade,
			bool_email_btc_input,
			bool_email_btc_output,
			image_identify_url,
			image_mix_url,
			image_bank_url,
			bool_realname,
			gender
			";
		$userid = $this->tpl->skin == 'admin' ? $GLOBALS['_GET_ESCAPE']['userid'] :	 $_SESSION['USER_ID'];
		$query['where'] = 'where userid=\''.$userid.'\'';
		$row = $this->dbcon->query($query);
		$row['permission'] = $this->get_permission_code($row['bool_confirm_mobile'], $row['bool_confirm_idimage'], $row['bank_account'], $row['bool_confirm_bank']);
		$row['permission_lv'] = strripos($row['permission'], '1');
		$codeTxt = "";
		switch($row['mobile_country_code']) {
		    case "AU": $codeTxt = "+61"; break;
            case "CA": $codeTxt = "+1"; break;
            case "CN": $codeTxt = "+86"; break;
            case "HK": $codeTxt = "+852"; break;
            case "ID": $codeTxt = "+62"; break;
            case "JP": $codeTxt = "+81"; break;
            case "MO": $codeTxt = "+853"; break;
            case "MY": $codeTxt = "+60"; break;
            case "MN": $codeTxt = "+976"; break;
            case "SG": $codeTxt = "+65"; break;
            case "KR": $codeTxt = "+82"; break;
            case "TH": $codeTxt = "+66"; break;
            case "UK": $codeTxt = "+44"; break;
            case "US": $codeTxt = "+1"; break;
            default : break;
		}
		$row['mobile_origin'] = str_replace($codeTxt,"",$row['mobile']);
		$row['pin_star'] = strlen($row['pin'])>0 ? substr($row['pin'], 0, 2).str_repeat('*',strlen($row['pin'])-2) : $row['pin'];
		$row['regdate'] = date('Y-m-d H:s:i',$row['regdate']);
		$this->tpl->assign($row);
		$this->tpl->assign('srch_url',$this->srchUrl());

		// 본인인증 정보 다시 확인하도록 로직 추가
		//$query = "select * from js_realname where userid='$userid' ";
		$this->tpl->assign('realname_info',$row);

		return $row;
	}

	function get_permission_code ($bool_confirm_mobile=0, $bool_confirm_idimage=0, $bank_account=0, $bool_confirm_bank=0) {
		$p[] = $_SESSION['USER_NO']  ? '1' : '0';
		$p[] = $_SESSION['USER_NO']  ? '1' : '0';
		$p[] = $bool_confirm_mobile ? '1' : '0';
		$p[] = $bool_confirm_idimage ? '1' : '0';
		$p[] = $bank_account ? '1' : '0';
		$p[] = $bool_confirm_bank ? '1' : '0';
		return implode('', $p);
	}

	function write()
	{
		global $config_basic, $btcService, $tradeapi, $ledgerapi;

		/**
		 *정회원 가입시 : 3,000,000 원->1,000,000 원
		 * - 정회원 가입하면 300만원에->100만원 해당하는 걸 ArA Pay 에 넣어 준다. -> USD 에 넣어 준다

		 *수동
		 * 주주회원 가입시 : 10,000,000 원
		 * - 주주회원 가입하면 5000  Ara Point 를 넣어준다.
		*/

		// $_POST['userid'] = $_POST['email'];
		// $GLOBALS['_POST_ESCAPE']['userid'] = $GLOBALS['_POST_ESCAPE']['email'];
		$userpw = $_POST['userpw']; // DB에 저장할때 hash 처리되어 변경되기 때문에 $userpw 에 담아두고 로그인할때 사용합니다.
		$level_code = $_POST['level_code'];

		if($_POST['userid'] == 'guest') {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('다른 아이디를 입력해주세요.');
			}
			else {
				jsonMsg(0, '다른 아이디를 입력해주세요.');
			}
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt > 0) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('다른 아이디를 입력해주세요.');
			}
			else {
				jsonMsg(0, '다른 아이디를 입력해주세요.');
			}

		}
		// 추천인 아이디 확인.
		if(trim($_POST['recomid'])) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'count';
			$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['recomid'].'\'';
			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$cnt) {
				if($config_basic['bool_ssl'] > 0) {
					errMsg('올바른 추천인 아이디를 입력해주세요.');
				} else {
					jsonMsg(0, '올바른 추천인 아이디를 입력해주세요.');
				}
			}
		}

		// 입금자명 확인. - 일반회원은 POST에 name이 없습니다. 정회원과 주주회원은 값이 꼭 있어야 합니다.
		if( $_POST['level_code']!='DE88' && !trim($_POST['name']) ) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('올바른 입금자명를 입력해주세요.');
			} else {
				jsonMsg(0, '올바른 입금자명를 입력해주세요.');
			}
		}

		//회원레벨 코드를 가지고 온다.
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select_one';
		$query['fields'] = 'level_code';
		$query['where'] = 'where ranking < 100 order by ranking desc';
		if(empty($level_code)) {
			$_POST['level_code'] = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		if($_POST['deposit_amount']=='1구좌 10,000,000원') {
			$_POST['deposit_amount'] = 9;
		} else if($_POST['deposit_amount']=='1,000,000원') {
			// 2019-10-29 정회원가입은 중개소 입금신청목록에 insert한다.
			$bank_info = explode("/",$_POST['deposit_account']);

			$arr = array();
			$arr[] = 'krw_amount=1000000';
			$arr[] = 'bank_user=\''.$_POST['name'].'\'';
			$arr[] = 'reg_date=	NOW()';
			$arr[] = 'state=\'N\'';
			$arr[] = 'bank=\''.trim($bank_info[0]).'\'';
			$arr[] = 'bank_addr=\''.trim($bank_info[1]).'\'';
			$arr[] = 'bank_name=\''.trim($bank_info[2]).'\'';
			$arr[] = 'user_id=\''.$_POST['userid'].'\'';
			$arr[] = 'req_page=\'J\'';
			$qry = implode(',',$arr);

			$query = array();
			$query['table_name'] = 'js_wallet_krw_request';
			$query['tool'] = 'insert';
			$query['fields'] = $qry;
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			$_POST['deposit_amount'] = 1;

		}

		$query = array();
		$query['tool'] = 'insert_idx';
		$result = $this->bWrite($query,$_POST);
		if(!$result['result']) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('정상적인 회원가입이 되지 않았습니다.! 관리자에게 문의하시기 바랍니다.!');
			}
			else {
				jsonMsg(0, '[E003] There was an error processing your subscription. Please contact administrator.');
			}
		}

		// 회원번호.
		$userno = $result['idx'];

		// 가입시 본인인증이 되었으면 본인인증 처리함.
		if(!empty($_SESSION['tmprealnameid']) && strpos($_SESSION['tmprealnameid'], 'tmp_')!==false) {
			$query = "select name from js_realname where userid='".mysqli_real_escape_string($_SESSION['tmprealnameid'])."' ";
			$_name = $this->dbcon->query_unique_value($query);
			if(!empty($_name)){
				// [2014-07-31] 회원가입시 본인인증을 한 상태라면 작성된 이름이 아닌 본인인증된 이름을 사용하도록 변경함.
				$query = "update js_member set bool_confirm_mobile = 1, level_code='JB37', name='".$this->dbcon->escape($_name)."' where userid='".mysqli_real_escape_string($_POST['userid'])."' ";
				$this->dbcon->query($query);

				$query = "update js_realname set userid='".mysqli_real_escape_string($_POST['userid'])."' where userid='".mysqli_real_escape_string($_SESSION['tmprealnameid'])."' ";
				$this->dbcon->query($query);
				$_SESSION['tmprealnameid'] = ''; // 처리후 임시값 삭제
			}
		}

		//가입확인메일 발송
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'userno, userid, name, mobile, email, bool_email, bool_sms';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);


		// 가입 확인 이메일 발송. - 이메일 수신여부와 상관없이 발송하도록 함.
		if( trim($row['email'])!='' ) { //$row['bool_email'] > 0) {
			include_once ROOT_DIR.'/admin/email_class.php';
			$mail = new ShopEmail($this->tpl);
			$mail->dbcon = &$this->dbcon;
			$mail->json = &$this->json;

			$mail->getConfigMail('join');

			if($GLOBALS['config_basic']['bool_confirm_email']=='1') {
				$confirm_email_code = getCode();
				$this->set_member_meta($row['userno'], 'confirm_email_code', $confirm_email_code);
			}

			$arr = array();
			$arr['mail_to'] = $row['email'];
			$arr['mail_subject'] = '['.$mail->config['config_basic']['shop_name'].'] '.$row['name'].'님의 가입확인 메일입니다';
			$arr['mail_to_name'] = $row['name'];
			$this->tpl->assign('userid',$row['userid']);
			$this->tpl->assign('name',$row['name']);
			if($GLOBALS['config_basic']['bool_confirm_email']=='1') {
				$this->tpl->assign('confirm_email_url',$_SERVER['HTTP_HOST'].'/member/memberJoin.php?pg_mode=confirm_email&userid='.$this->dbcon->escape($row['userid']).'&code='.$this->dbcon->escape($confirm_email_code));
			}
			$mail_body = $this->tpl->fetch('mail_contents');
			$arr['mail_body'] = $mail_body;
			$mail->sendAutoMail($arr,'join');
		}

		if( trim($row['mobile'])!='' ) { //$row['bool_sms'] > 0) {
			$config_msg = getConfig('js_config_sms','bool_msg_join, msg_join');
			if($config_msg['bool_msg_join'] > 0) {
				$msg_data = array();
				$msg_data['tran_phone'] = $_POST['mobile_a'].$_POST['mobile_b'].$_POST['mobile_c'];
				$replace_src = array('[회사이름]', '[회사URL]','[회원이름]','[회원아이디]');
				$replace_rslt = array($config_basic['shop_name'],$config_basic['shop_url'],$_POST['name'],$_POST['userid']);
				$msg_data['tran_msg']  = str_replace($replace_src, $replace_rslt, $config_msg['msg_join']);

				// sendSms($msg_data); // 2014-07-27 황인석 수정 - 이길한 대표메일요청 : 메일 2.그리고 가입시 발송되는  SMS 중지시켜주세요.
			}
		}

		// 기본 지갑 주소 생성
		// USD, SCC
		if(!empty($tradeapi)){
			$address = $tradeapi->create_wallet($userno, $tradeapi->default_exchange); // 이것도 은행 연동해서 느려지면 수동으로 변경하기.
			$tradeapi->save_wallet($userno, $tradeapi->default_exchange, $address);
			// $address = $tradeapi->create_wallet($userno, 'SCC'); // 작동은 되지만 시간이 오래걸려서 수동으로 생성하도록 여기서는 제외시킴.
			// $tradeapi->save_wallet($userno, 'SCC', $address);
		}
		// btc 지급 초기화
		// if(!empty($btcService)){
		// 	$address = $btcService->bitcoind->getnewaddress($userid);
		// 	$btcService->btcTradeCriterionDao->setWallet($_POST['userid'], $address);
		// }

		//아라 지갑테이블 데이터 생성

		//지갑에 이미 동일한 아이디가 있는가?
		$query = array();
		$query['table_name'] = 'js_wallet_wallet';
		$query['tool'] = 'count';
		$query['where'] = 'where name=\''.$_POST['userid'].'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);

		//지갑에 존재하지 않는 아이디라면 지갑 정보 생성?
		if($cnt == 0) {
			//이미 지갑에 회원가입 api가 있기때문에 curl 로 api 호출
			if(__API_RUNMODE__){$add = __API_RUNMODE__.".";}
			$method = 'http';
			if($add=='live.') {
				$add='';
				$method = 'https';
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$method."://".$add."wallet.araexchange.co.kr/api/v1.0/createNewAccount/index.php");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "wallet_name=".$_POST['userid']."&secret_key=".$_POST['userpw']."&email=");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			curl_exec($ch);
			curl_close ($ch);

		}

		if($config_basic['bool_ssl'] > 0) {
			replaceGo('//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?pg_mode=join_ok&userid='.$_POST['userid']);
		}
		else {

			//가입완료후 바로 로그인 처리로 변경
			$this->directLogin($_POST['userid'], $userpw );

			// jsonMsg(1,$_POST['userid']);
			// jsonMsg(1, 'Email was sent for verification. Please check your email and verify.'); // 이메일 인증이 필요할때 . Email was sent for verification. Please check your email and verify
			jsonMsg(1, 'Sign up is complete. Please login.'); // 이메일 인증이 필요 없을때. Sign up is complete. Please login.

		}
	}

	function directLogin($userid, $passwd){
		$query['table_name'] = 'js_member';
		$query['tool'] = 'row';
		$query['fields'] = 'userno,userid,userpw,name,level_code,bank_account';
		$query['fields'].= ',bool_confirm_email,bool_confirm_mobile,bool_confirm_idimage,bool_confirm_bank,bool_email_krw_input,bool_sms_krw_input,bool_email_krw_output,bool_sms_krw_output,bool_email_btc_trade,bool_email_btc_input,bool_email_btc_output';
		$query['where'] = 'where userid=\''.$userid.'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		if($row['userpw'] != hash('sha512', $passwd)) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('Passwords do not match.');
			}
			else {
				jsonMsg(0,'err_pw'.$userid.$passwd);
			}
		}

		$_SESSION['USER_NO'] = $row['userno'];
		$_SESSION['USER_ID'] = $row['userid'];
		$_SESSION['USER_NAME'] = $row['name'];
		$_SESSION['USER_LEVEL'] = $row['level_code'];
		$_SESSION['USER_LEVELNAME'] = $this->getLevelname($row['level_code']);

		// SCC Account 여부
		$query = array();
		$query['table_name'] = 'js_exchange_wallet';
		$query['tool'] = 'count';
		$query['where'] = 'where userno=\''.$this->dbcon->escape($_SESSION['USER_NO']).'\' and symbol=\'SCC\'';
		$scc_cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($scc_cnt > 0) {
			$_SESSION['SCC_ACCOUNT'] = $scc_cnt;
		} else {
			$_SESSION['SCC_ACCOUNT'] = '0';
		}

		$query = "select * from js_exchange_wallet where userno='".$this->dbcon->escape($_SESSION['USER_NO'])."' and symbol = 'SCC' ";
		$scc_info = $this->dbcon->query_unique_array($query);
		if( !empty($_realname_info) && !empty($_realname_info['userid']) ) {
			$_SESSION['USER_REALNAME'] = '1';
//			$_SESSION['USER_REALNAME'] = $_realname_info['name'];
			$_SESSION['USER_GENDER'] = $_realname_info['gender'];
			$_SESSION['USER_BIRTHDATE'] = $_realname_info['birthdate'];
		} else {
			$_SESSION['USER_REALNAME'] = '0';
		}


		// 본인인증여부
		$query = "select * from js_member where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
		$_realname_info = $this->dbcon->query_unique_array($query);
		if( !empty($_realname_info) && $_realname_info['bool_realname'] != '0' ) {
			$_SESSION['USER_REALNAME'] = '1';
//			$_SESSION['USER_REALNAME'] = $_realname_info['name'];
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

		//지갑 api에 로그인
		$query = "select * from js_wallet_wallet where name='".$_SESSION['USER_ID']."' and symbol = 'ARP' ";
		$wallet = $this->dbcon->query_unique_array($query);
		$query = "select * from js_wallet_wallet where name='".$_SESSION['USER_ID']."' and symbol = 'GWS' ";
		$wallet2 = $this->dbcon->query_unique_array($query);

		$_SESSION['WALLETNO'] = $wallet['walletno'];
		$_SESSION['ACCOUNT'] = $wallet['account'];
		$_SESSION['ADDRESS'] = $wallet['address'];

		$_SESSION['WALLETNO_ARP'] = $wallet['walletno'];
		$_SESSION['ACCOUNT_ARP'] = $wallet['account'];
		$_SESSION['ADDRESS_ARP'] = $wallet['address'];

		$_SESSION['WALLETNO_GWS'] = $wallet2['walletno'];
		$_SESSION['ACCOUNT_GWS'] = $wallet2['account'];
		$_SESSION['ADDRESS_GWS'] = $wallet2['address'];
	}

	function joinOk()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'name';
		$query['where'] = 'where userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';
		$name = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('name',$name);
	}

	function confirmEmailOK($p_userid){
		$query = "update js_member set bool_confirm_email=1 where userid='".mysqli_real_escape_string($p_userid)."' ";
		if( $this->dbcon->query($query,__FILE__,__LINE__) ) {
			$this->tpl->assign('confirm_ok',true);
		} else {
			$this->tpl->assign('confirm_ok',false);
		}
	}

	/**
	 * 회원 은행정보 저장
	 */
	function saveBankInfo($bank_name, $bank_account, $bank_owner, $image_bank_url='') {
		global $config_basic, $tradeapi;

		if(empty($bank_name)){
			jsonMsg(0,'은행 이름을 적어주세요.');
		}
		if(empty($bank_account)){
			jsonMsg(0,'계좌번호를 적어주세요.');
		}
		if(empty($bank_owner)){
			jsonMsg(0,'예금주 이름을 적어주세요.');
		}
        // 은행통장사본
        if($image_bank_url) {
			$sql_image_bank = ", image_bank_url='{$this->dbcon->escape($image_bank_url)}', bool_confirm_bank=0 ";
        }

		$query = "update js_member set bank_name='".$this->dbcon->escape($bank_name)."', bank_account='".$this->dbcon->escape($bank_account)."', bank_owner='".$this->dbcon->escape($bank_owner)."' {$sql_image_bank} where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
		if($this->dbcon->query($query)) {
			// 은행통장사본
			if($image_bank_url) {
				$_member_info = $this->get_member_info($_SESSION['USER_NO']);
				if($_member_info->image_bank_url && $_member_info->image_bank_url != $image_bank_url) {
					$tradeapi->delete_file_to_s3($_member_info->image_bank_url);
				}
			}
			jsonMsg(1);
		} else {
			jsonMsg(0,'err_db');
		}

	}

	function edit()
	{
		global $config_basic, $tradeapi;
		if(empty($_POST['userid'])) {
			responseFail('회원아이디가 없습니다. 웹페이지를 새로고침 후 다시 수정해주세요.');
		}
		if(empty($_POST['name'])) {
			responseFail('이름을 입력해주세요.');
		}
		if(! empty($_POST['bool_passwd'])) {
			if(empty($_POST['userpw'])) {
				responseFail('변경할 비밀번호를 입력해주세요.');
			}
			if(iconv_strlen($_POST['userpw'], 'utf-8')<8) {
				responseFail('8글자 이상의 비밀번호를 입력해주세요.');
			}
		} else {
			$_POST['userpw'] = '';
		}

		$query = "select captcha_string from fusion_captcha where captcha_ip='" . $this->dbcon->escape($_COOKIE['token']) . "' ";
		$code = strtoupper($this->dbcon->query_unique_value($query));
		if($this->tpl->skin != 'admin') {  // 2014-07-09 : 고객요청사항 - 관리자는 패스할께요 Danny
			if( $code != strtoupper($_POST['securimagecode']) ) {
				responseFail('올바른 보안문자를 입력해주세요.');
			}
		}
		// 이미지 복사 및 이전파일 삭제 - tmp 에 있는건 자동으로 삭제 할겁니다. 그래서 사용하는건 tmp에서 복사해야합니다.

		// 신분증
		if($_POST['image_identify_url_new']!="" && $_POST['image_identify_url_new']!=$_POST['image_identify_url_old']) {
			$_r = $tradeapi->copy_tmpfile_to_s3($_POST['image_identify_url_new']);
			if($_r) {
				$_POST['image_identify_url'] = $_r;
				if($_POST['image_identify_url_old']!="") {
					$tradeapi->delete_file_to_s3($_POST['image_identify_url_old']);
				}
			}
		} else {
		    unset($_POST['image_identify_url']);
		}
		// 신분증 + 사용자
		if($_POST['image_mix_url_new']!="" && $_POST['image_mix_url_new']!=$_POST['image_mix_url_old']) {
			$_r = $tradeapi->copy_tmpfile_to_s3($_POST['image_mix_url_new']);
			if($_r) {
				$_POST['image_mix_url'] = $_r;
				if($_POST['image_mix_url_old']!=""){
					$tradeapi->delete_file_to_s3($_POST['image_mix_url_old']);
				}
			}
		} else {
            unset($_POST['image_mix_url']);
        }

		$query = array();
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		if($this->bEdit($query,$_POST)) {
			// js_wallet_wallet pw 수정.
			if(! empty($_POST['bool_passwd'])) {
				// bEdit에서 $_POST 값을 변경해버려서 아래에서는 비번을 sha512 변환없이 그대로 사용합니다.
				$query = "update js_wallet_wallet set hashkey = '".$this->dbcon->escape($_POST['userpw'])."' where name='{$GLOBALS['_POST_ESCAPE']['userid']}' ";
				$this->dbcon->query($query);
				// var_dump($query, $_POST); exit;
			}
			responseSuccess('회원정보를 수정했습니다.', '//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
		}
		else {
			responseFail('회원정보를 수정하지 못했습니다..!\n\n관리자에게 문의해주세요.!');
		}
	}

    // 18.10.18 Brad add
    function edit_photo()
    {
        global $config_basic, $tradeapi;
        if(empty($_POST['userid'])) {
            if($config_basic['bool_ssl'] > 0) {
                errMsg('Please enter your member ID!');
            }
            else {
                jsonMsg(0,'Please enter your member ID!');
            }
        }

        if(empty($_POST['bool_passwd'])) {
            $_POST['userpw'] = '';
        }

        // 이미지 복사 및 이전파일 삭제 - tmp 에 있는건 자동으로 삭제 할겁니다. 그래서 사용하는건 tmp에서 복사해야합니다.

        // 신분증
        if($_POST['image_identify_url_new']!="" && $_POST['image_identify_url_new']!=$_POST['image_identify_url_old']) {
            $_r = $tradeapi->copy_tmpfile_to_s3($_POST['image_identify_url_new']);
            if($_r) {
                $_POST['image_identify_url'] = $_r;
                if($_POST['image_identify_url_old']!="") {
                    $tradeapi->delete_file_to_s3($_POST['image_identify_url_old']);
                }
						}
						$_POST['bool_confirm_idimage'] = '0'; // 수정시 신분증이미지 인증상태를 초기화합니다.
        } else {
            unset($_POST['image_identify_url']);
				}

        // 신분증 + 사용자
        if($_POST['image_mix_url_new']!="" && $_POST['image_mix_url_new']!=$_POST['image_mix_url_old']) {
            $_r = $tradeapi->copy_tmpfile_to_s3($_POST['image_mix_url_new']);
            if($_r) {
                $_POST['image_mix_url'] = $_r;
                if($_POST['image_mix_url_old']!=""){
                    $tradeapi->delete_file_to_s3($_POST['image_mix_url_old']);
                }
            }
						$_POST['bool_confirm_idimage'] = '0'; // 수정시 신분증이미지 인증상태를 초기화합니다.
        } else {
            unset($_POST['image_mix_url']);
        }

        // 은행통장사본
        if($_POST['image_bank_url_new']!="" && $_POST['image_bank_url_new']!=$_POST['image_bank_url_old']) {
            $_r = $tradeapi->copy_tmpfile_to_s3($_POST['image_bank_url_new']);
            if($_r) {
                $_POST['image_bank_url'] = $_r;
                if($_POST['image_bank_url_old']!="") {
                    $tradeapi->delete_file_to_s3($_POST['image_bank_url_old']);
                }
						}
						$_POST['bool_confirm_bank'] = '0'; // 수정시 통장사본인증 상태를 초기화합니다.
        } else {
            unset($_POST['image_bank_url']);
				}

        $query = array();
        $query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
        if($this->bEdit($query,$_POST)) {
            if($config_basic['bool_ssl'] > 0) {
                replaceGo('//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
            }
            else {
                jsonMsg(1);
            }
        }
        else {
            if($config_basic['bool_ssl'] > 0) {
                errMsg('Your membership information has not been modified properly.!\n\nPlease contact your administrator.!');
            }
            else {
                jsonMsg(0);
            }
        }
    }

	function edit_pin()
	{
		global $config_basic, $_POST_ESCAPE;
		if(empty($_SESSION['USER_ID'])) {
			responseFail('로그인을 해주세요.!');
		}

		if(empty($_POST['pin'])) {
			responseFail('새 거래보안번호를 입력하여 주세요.!');
		}

		if(strlen($_POST['pin'])>6 || strlen($_POST['pin'])<4 || preg_match('/\D/', $_POST['pin'])) {
			responseFail('4~6자리 숫자만을 입력해 주세요.!');
		}

		$sql = "SELECT pin FROM js_member WHERE userid='".$_SESSION['USER_ID']."' ";
		$pin_db = $this->dbcon->query_unique_value($sql);
		if($pin_db != $_POST['pin_c'] && $pin_db != '') {
			responseFail('거래보안번호를 변경하지 못했습니다. 현재 거래보안번호가 올바른지 확인해주세요.');
		}

		// $query = "UPDATE js_member SET pin='{$_POST_ESCAPE['pin']}' WHERE userid='".$_SESSION['USER_ID']."' ";
		$query = "UPDATE js_member SET userpw='".md5($_POST['pin'])."', pin='".md5($_POST['pin'])."' WHERE userid='".$_SESSION['USER_ID']."' ";
		if($this->dbcon->query($query)) {
			responseSuccess($msg='', '/edit_pin');
		} else {
			responseFail('거래보안번호를 변경하지 못했습니다.');
		}
	}

	function confirmRealname($p_confirm_number) {

		if(empty($_GET['userid'])) {
			jsonMsg(0, '회원을 선택해주세요.');
		}
		foreach ($_GET['userid'] as $key => $val) {
			$query = "SELECT * FROM js_member WHERE userid='{$this->dbcon->escape($val)}'";
			$m = $this->dbcon->query_unique_object($query);

			$query = "UPDATE js_member SET bool_confirm_mobile=1, level_code='JB37' WHERE userid='{$this->dbcon->escape($val)}'";
			$result = $this->dbcon->query($query);
			$query = "INSERT INTO js_realname SET userid='{$this->dbcon->escape($m->userid)}', ciphertime='".time()."', requestnumber='SEQ_{$this->dbcon->escape($m->userid)}', authtype='관리자수동인증', NAME='{$this->dbcon->escape($m->name)}' on duplicate key update ciphertime='".time()."', requestnumber='SEQ_{$this->dbcon->escape($m->userid)}', authtype='관리자수동인증', NAME='{$this->dbcon->escape($m->name)}' ";
			$this->dbcon->query($query);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1, '본인인증 처리를 하지 못했습니다.');

	}

	/**
	 * send confirm number to mobile phone
	 */
	function send_confirm_number($p_phone_number, $mobile_country_code) {
		$_phone_number = preg_replace('/[^0-9\+]/', '', $p_phone_number);
		if(empty($_phone_number)){
			jsonMsg(0,'Please write your phone number.');
		}
		// 휴대폰 번호 중복체크 로직 18.10.24 Brad
		$query = array();
        $query['table_name'] = $this->config['table_name'];
        $query['tool'] = 'count';
		$query['where'] = 'where userid!=\''.$_SESSION['USER_ID'].'\' and mobile=\''.$this->dbcon->escape($p_phone_number).'\'';
        $cnt = $this->dbcon->query($query,__FILE__,__LINE__);
        if($cnt != 0) {
			jsonMsg(0,'err_duplicate');
        } else {
			$tmpnum = mt_rand(111111, 999999);
            $query = "update js_member set confirm_number='$tmpnum', confirm_mobile_number='{$this->dbcon->escape($p_phone_number)}', mobile_country_code='{$this->dbcon->escape($mobile_country_code)}' where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
            if($this->dbcon->query($query)) {
                $msg_data = array();
                $msg_data['tran_phone'] = $_phone_number;
                $config_basic = getConfig('js_config_basic','shop_name,shop_url');
                // $msg_data['tran_msg']  = '핸드폰인증을 위해 인증번호['.$tmpnum.']를 입력해 주세요.';
                $msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . str_replace('{tmpnum}', $tmpnum, Lang('main_member72'));
                if(sendSms($msg_data)) {
                    jsonMsg(1);
                }
                else {
                    jsonMsg(0,'err_sms');
                }
            } else {
                jsonMsg(0,'err_db');
            }
		}
	}

	/**
	 * check mobile confirm number
	 */
	function confirm_number($p_confirm_number) {
		$p_confirm_number = preg_replace('/[^0-9]/', '', $p_confirm_number);
		if(empty($p_confirm_number)){
			jsonMsg(0,'Enter the correct certification number.');
		}
		$query = "select * from js_member where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
		$_member_info = $this->dbcon->query_unique_object($query);
		if( $_member_info->confirm_number == $p_confirm_number ) {
			$mobile = $_member_info->confirm_mobile_number;
			$mobile = preg_replace('/^8201/','821',str_replace('+','',$mobile)); // +8201088889999 -> 821088889999
			$query = "update js_member set bool_confirm_mobile='1', mobile='".$this->dbcon->escape($mobile)."', confirm_number='', bool_realname='1' where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
			if($this->dbcon->query($query)) {
			    $_SESSION['USER_REALNAME'] = 1;
				jsonMsg(1);
			}
			else {
				jsonMsg(0,'err_db');
			}
		} else {
			jsonMsg(0,'Please enter the correct certification number.');
		}
	}

	function _write($arr)
	{
		//[2014-06-23 benant] phone, mobile 사용하지 않음. 아래 주석은 번호를 3개로 나눠서 받을때 사용함.
//		@ $arr['phone'] = $arr['phone_a'].'-'.$arr['phone_b'].'-'.$arr['phone_c'];
		//[2014-07-31 benant] mobile 이 주석이 풀려서인지 회원정보 수정할때 값을 넘기지 않는데 mobile이 빈값으로 설정되면서 기존 값을 지워버림. 문제해결을 위해 주석처리하려다가 혹시 다른곳에서는 사용할수도 있어서 그냥 수정할때는 mobile이 설정되지 않도록 함.
		if($_POST['pg_mode']!='edit' && $_POST['pg_mode']!='edit_photo'){
			$arr['mobile'] = $arr['mobile_a'].$arr['mobile_b'].$arr['mobile_c'];
		}
		//@ $arr['email'] = $arr['email_a'].'@'.$arr['email_b'];
		if(!empty($arr['userpw'])) {
			$arr['userpw'] = hash('sha512', $arr['userpw']); //md5($arr['userpw']);
		}

		if(!empty($arr['sid_b'])) {
			$arr['sid_a'] = (string) $arr['sid_a'];
			$arr['sid_b'] = (string) $arr['sid_b'];
			//$arr['sid_b'] = md5($arr['sid_b']);
		}
		$arr['bool_email'] = empty($arr['bool_email']) ? 0 : 1;
		$arr['bool_sms'] = empty($arr['bool_sms']) ? 0 : 1;
		return $arr;
	}

	function withdraw()
	{
		if(empty($_SESSION['USER_ID'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			jsonMsg(0,'err_userid');
		}

		$query = array();
		$query['table_name'] = 'js_withdraw';
		$query['tool'] = 'insert';
		$query['fields'] = withdrawQuery($_POST);
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		$query = 'INSERT INTO js_member_withdraw
			(userid,userpw,name,nickname,sid_a,sid_b,phone,mobile,email,zipcode,address_a,address_b,bool_email,bool_sms,bool_lunar,birthday,level_code,regdate)
			SELECT userid,userpw,name,nickname,sid_a,sid_b,phone,mobile,email,zipcode,address_a,address_b,bool_email,bool_sms,bool_lunar,birthday,level_code,regdate
			FROM js_member WHERE userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		// $query = array();
		// $query['table_name'] = 'js_member'; // $this->config['table_name']
		// $query['tool'] = 'delete';
		// $query['where'] = 'where userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		$query = 'update js_member set userpw = \'a251bc8bba42aa32d66b3cf9d2b0830ccfc4756961598444dd05633e803164b491b841505ac50cd6e00ee7f55f0d557b16f6b9d3d4a5accc3245c8341d16b7fb\' where userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}

		// 본인 인증 정보도 삭제 2014-08-11 -
		// 본인인증 뿐만 아니라 회원거래내역 및 bitcoin 계좌 정보까지 모두 삭제해야 할것으로 보임.
		// 아니면 사용한 아이디로는 다시는 사용못하도록 해야 함.
		$query = 'delete from js_realname where userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		@$this->dbcon->query($query);

		unset($_SESSION['USER_ID']);
		unset($_SESSION['USER_NAME']);
		unset($_SESSION['USER_LEVEL']);
		unset($_SESSION['USER_LEVELNAME']);
		jsonMsg(1);
	}

	function memberRollback()
	{
		if(empty($_GET['userid'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			jsonMsg(0,'err_userid');
		}

		$query = 'INSERT INTO js_member
			(userid,userpw,name,nickname,sid_a,sid_b,phone,mobile,email,zipcode,address_a,address_b,bool_email,bool_sms,bool_lunar,birthday,level_code,regdate)
			SELECT userid,userpw,name,nickname,sid_a,sid_b,phone,mobile,email,zipcode,address_a,address_b,bool_email,bool_sms,bool_lunar,birthday,level_code,regdate
			FROM js_member_withdraw WHERE userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}

		$query = 'DELETE T1.*, T2.*
			FROM js_member_withdraw AS T1, js_withdraw AS T2
			WHERE T1.userid = T2.userid AND T1.userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function memberMail()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'name,email';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$result = $this->_batchEmail($row['email'],$row['name']);
		if($result) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	//회원삭제시 회원 주문 데이터
	function del($userid='',$bool_return=0)
	{
		if(empty($userid)) {
			$userid = $_GET['userid'];
		}

		if($this->config['mode'] == 'withdraw') {
			$query = 'DELETE T1.*, T2.*
				FROM js_member_withdraw AS T1, js_withdraw AS T2
				WHERE T1.userid = T2.userid AND T1.userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		else {
			//회원 정보 삭제
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'delete';
			$query['where'] = 'where userid=\''.$this->dbcon->escape($userid).'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);

			// [2014-07-26] 본인인증 정보 삭제 추가
			// 본인인증 뿐만 아니라 회원거래내역 및 bitcoin 계좌 정보까지 모두 삭제해야 할것으로 보임.
			// 아니면 사용한 아이디로는 다시는 사용못하도록 해야 함.
			$query = "delete from js_realname where userid='$this->dbcon->escape($userid)' ";
			@$this->dbcon->query($query);



		}
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

	//사용자아이디 중복 체크
	function overlapCheck()
	{
		$arr_userid = array('guest','admin');
		if(in_array($_GET['userid'],$arr_userid)) {
			jsonMsg(0);
		}
		else {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'count';
			$query['where'] = 'where userid=\''.$GLOBALS['_GET_ESCAPE']['userid'].'\'';

			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			if($cnt > 0) {
				jsonMsg(0);
			}
			else {
				jsonMsg(1);
			}
		}
	}

	//아이디 찾기
	function searchUserid()
	{
//		$mobile = $_POST['mobile_a'].'-'.$_POST['mobile_b'].'-'.$_POST['mobile_c'];
		$mobile = $_POST['mobile_a'].$_POST['mobile_b'].$_POST['mobile_c'];
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where name=\''.$GLOBALS['_POST_ESCAPE']['name'].'\' && mobile=\''.$this->dbcon->escape($mobile).'\' ';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if(empty($cnt)) {
			jsonMsg(0);
		}
		$query['tool'] = 'select_one';
		$query['fields'] = 'email';
		$userid = $this->dbcon->query($query,__FILE__,__LINE__);
		list($id, $domain) = explode('@', $userid);
		$_cnt_id = strlen($id);
		$_newid = substr($id, 0, 3);
		for($i=3;$i<$_cnt_id;$i++){
			$_newid .= '*';
		}
		jsonMsg(1,$_newid.'@'.$domain);
	}

	// 비밀번호 변경.
	function resetPw() {
		if(!$_POST['userpw']) {
			jsonMsg(0, __('main_member13'));
		}
		if(strlen($_POST['userpw'])<8) {
			jsonMsg(0, __('main_member17'));
		}
		$token = $_POST['token'];
		$sql = "select userno from js_member_meta where name='tmp_pw' and value='{$GLOBALS['_POST_ESCAPE']['token']}' ";
		$userno = $this->dbcon->query_unique_value($sql);
		if(! $userno) {
			jsonMsg(0, '사용할 수 없는 토큰입니다.');
		}
		$newpw = hash('sha512', $_POST['userpw']);
		$sql = "UPDATE js_member SET userpw='{$this->dbcon->escape($newpw)}' where userno='{$this->dbcon->escape($userno)}' ";
		$this->dbcon->query($sql);
		$this->del_member_meta($userno, 'tmp_pw');
		jsonMsg(1);
	}

	//패스워드찾기
	function serachUserpwd()
	{
//		$mobile = $_POST['mobile_a'].'-'.$_POST['mobile_b'].'-'.$_POST['mobile_c'];
//		$mobile = $_POST['mobile_a'].$_POST['mobile_b'].$_POST['mobile_c'];
//		$email = $_POST['email_a'].'@'.$_POST['email_b'];
        $email = $GLOBALS['_POST_ESCAPE']['email'];

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where email=\''.$email.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			jsonMsg(0);
		}
		//임시패스워드 발급하기
		$arr = array();
		for($i=0;$i<16;$i++) {
			$arr[] = chr(mt_rand(65,90));
		}
		$tmp_pw = implode('',$arr);

		// 기존 비밀번호 변경하는 방식.
		// $query['tool'] = 'update';
		// $query['fields'] = 'userpw=\''.hash('sha512', $tmp_pw).'\'';
		// $query['where'] = 'where email=\''.$this->dbcon->escape($email).'\'';
		// $result = $this->dbcon->query($query,__FILE__,__LINE__);
		// if(!$result) {
		// 	jsonMsg(0);
		// }
		$query['tool'] = 'row';
		$query['fields'] = 'userno, userid, name, mobile, email, bool_email, bool_sms';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		// 임시 번호 생성해 확인하기. - 지금은 저장.
		$this->set_member_meta($row['userno'], 'tmp_pw',$tmp_pw);


		if($_POST['trans_method'] == 'email') {
			include_once ROOT_DIR.'/admin/email_class.php';
			$mail = new ShopEmail($this->tpl);
			$mail->dbcon = &$this->dbcon;
			$mail->json = &$this->json;

			$mail->getConfigMail('passwd');
			$config_mail = $mail->config['config_mail'];

			$arr = array();
			$arr['mail_to'] = $row['email'];
			$arr['mail_subject'] = str_replace('{UserName}', $row['name'], Lang::main_basic14);
			$arr['mail_to_name'] = $row['name'];

			$this->tpl->assign('userid',$row['userid']);
			$this->tpl->assign('userpw',$tmp_pw);
			$this->tpl->assign('name',$row['name']);
			$this->tpl->assign('from_name',$config_mail['from_name']);
			$this->tpl->assign('lang',$_SESSION['lang']); // 현재 언어.
			$this->tpl->assign('email_sign', str_replace('{name}', Lang('main_sitename'), Lang('main_email_sign')));
			for ($i=15; $i<=25; $i++) {
				$this->tpl->assign("main_basic{$i}", Lang("main_basic{$i}") ); // 언어 설정 값이 없을때 Lang::main_basic15 처럼 호출하면 PHP 애러발생. 함수형식으로 변경함.
			}
			$mail_body = $this->tpl->fetch('mail_contents');

			$arr['mail_body'] = $mail_body;

			if($mail->sendAutoMail($arr,'passwd')) {
				jsonMsg(1,'email');
			}
			else {
				jsonMsg(0,'err_mail');
			}
		}
		else {
			$config_msg = getConfig('js_config_sms','bool_msg_passwd, msg_passwd');
			$msg_data = array();
			if(empty($_SESSION['CONFIG_BASIC'])) {
				$config_basic = getConfig('js_config_basic','shop_name,shop_url');
			}
			else {
				$config_basic = $_SESSION['CONFIG_BASIC'];
			}
			$msg_data['tran_phone'] = $row['mobile'];
			$replace_src = array('[회사이름]', '[회사URL]','[회원이름]','[비밀번호]');
			$replace_rslt = array($config_basic['shop_name'],$config_basic['shop_url'],$row['name'],$tmp_pw);
			$msg_data['tran_msg']  = str_replace($replace_src, $replace_rslt, $config_msg['msg_passwd']);
			if(sendSms($msg_data)) {
				jsonMsg(1);
			}
			else {
				jsonMsg(0,'err_sms');
			}
		}
		jsonMsg(1,$_POST['trans_method']);
	}

	//이메일 인증 다시 보내기
	function emailResend()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['email'].'\' && email=\''.$GLOBALS['_POST_ESCAPE']['email'].'\' ';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if(empty($cnt)) {
			jsonMsg(0);
		}
		$query['tool'] = 'select_one';
		$query['fields'] = 'userid';
		$userid = $this->dbcon->query($query,__FILE__,__LINE__);


		include_once ROOT_DIR.'/admin/email_class.php';
		$mail = new ShopEmail($this->tpl);
		$mail->dbcon = &$this->dbcon;
		$mail->json = &$this->json;

		$mail->getConfigMail('join');

		$arr = array();
		$arr['mail_to'] = $userid;
		$arr['mail_subject'] = '['.$mail->config['config_basic']['shop_name'].'] '.$userid.'님의 가입확인 메일입니다';
		$arr['mail_to_name'] = $userid;
		$this->tpl->assign('userid',$userid);
		$this->tpl->assign('name',$userid);
		$this->tpl->assign('lang',$_SESSION['lang']); // 현재 언어.
		$mail_body = $this->tpl->fetch('mail_contents');
		$arr['mail_body'] = $mail_body;
		if($mail->sendAutoMail($arr,'join')) {
			jsonMsg(1,'email');
		}
		else {
			jsonMsg(0,'err_mail');
		}

	}

	function loopGetcustomers($mode='tpl')
	{
		if (empty($_GET['sort_method'])) {
			$sort_method = 'desc';
		}
		else {
			$sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
		}

		$qry = $this->srchQry();
		if($_REQUEST['start_date']) {
			$qry .= " AND M.regdate >= '".strtotime($_REQUEST['start_date'].' 00:00:00')."' ";
		}
		if($_REQUEST['end_date']) {
			$qry .= " AND M.regdate <= '".strtotime($_REQUEST['end_date'].' 23:59:59')."' ";
		}
		if($_REQUEST['userid']) {
			$qry .= " AND M.userid like '%".trim($_REQUEST['userid'])."%' ";
		}
		if($_REQUEST['name']) {
			$qry .= " AND M.`name` like '%".trim($_REQUEST['name'])."%' ";
		}
		if($_REQUEST['mobile']) {
			$qry .= " AND M.mobile like '%".trim($_REQUEST['mobile'])."%' ";
		}
		if($_REQUEST['email']) {
			$qry .= " AND M.email like '%".trim($_REQUEST['email'])."%' ";
		}

		$table_name = "
			js_member AS M
				LEFT JOIN js_ara_center CE
					ON M.center_code = CE.center_code
				LEFT JOIN js_ara_credit P
					ON M.userid=P.user_id
				LEFT JOIN js_wallet_bds AS WB
					ON M.userno = WB.userno";

		if($_REQUEST['div_page'] == "walletcustomers"){
			$table_name .= " LEFT JOIN
				(
					SELECT
					T.name,
					SUM(CASE WHEN T.symbol='ARP' THEN IFNULL(T.balance, 0) END) AS ara_pay,
					SUM(CASE WHEN T.symbol='GWS' THEN IFNULL(T.balance, 0) END) AS wallet_gws,
					SUM(CASE WHEN T.symbol='KRW' THEN IFNULL(T.balance, 0) END) AS wallet_krw
					FROM (
						SELECT NAME, symbol, balance FROM js_wallet_wallet WHERE NAME<>''
						) T
					GROUP BY T.name
				) T1 ON M.userid=T1.name";

		}else if($_REQUEST['div_page'] == "customers"){
			$table_name .= " LEFT JOIN
				(
					SELECT
					T2.userno,
					SUM(CASE WHEN T2.symbol='GWS' THEN IFNULL(T2.confirmed, 0) END) AS gws,
					SUM(CASE WHEN T2.symbol='KRW' THEN IFNULL(T2.confirmed, 0) END) AS krw
					FROM (
						SELECT userno, symbol, confirmed FROM js_exchange_wallet WHERE userno<>''
					) T2 GROUP BY T2.userno) T3 ON M.userno=T3.userno";
		}

		if($_REQUEST['div_page'] == "walletcustomers"){
			$fields = "M.userno, M.userid, M.level_code, IFNULL(M.recomid, '') as recomid, IFNULL(CE.center_name, '') as center_name, M.locked, IF(M.untildate>0, FROM_UNIXTIME(M.untildate, '%Y-%m-%d %H:%i:%s'),'-') as untildate, M.name, M.mobile, M.email, FROM_UNIXTIME(M.regdate, '%Y-%m-%d %H:%i:%s') as regdate, IFNULL(P.amount,0) ara_point,
			T1.ara_pay AS ara_pay, T1.wallet_gws AS wallet_gws, WB.amount AS wallet_bds, T1.wallet_krw AS wallet_krw";

		}else if($_REQUEST['div_page'] == "customers"){
			$fields = "M.userno, M.userid, M.name, T3.gws AS gws, T3.krw AS krw, M.mobile, M.email, FROM_UNIXTIME(M.regdate, '%Y-%m-%d %H:%i:%s') as regdate";
		}

		if (empty($_GET['sort_target'])) {
			$sort_target = 'M.regdate';
		}
		else {
			$sort_target = 'M.'.$GLOBALS['_GET_ESCAPE']['sort_target'];
		}
		$sort = ' ORDER BY '.$sort_target.' '.$sort_method;
		$where = ' WHERE 1 '.$qry;

		$query = array();
		$query['table_name'] = $table_name;
		$query['fields'] = $fields;
		$query['where'] = $where;
		$query['tool'] = 'count';
		$total = $this->dbcon->query($query,__FILE__,__LINE__);

		$query['tool'] = 'select';
		$query['where'] .= $sort;

		//var_dump($_REQUEST['div_page']); exit;
		$result = $this->dbcon->query($query,__FILE__,__LINE__);

		$loop = array();
		$i = 0;

		while ($row = mysqli_fetch_assoc($result)) {
			$row['list_cnt'] = $i;
			$row['no'] = $total- $_GET['start'] - $row['list_cnt'];
			$loop[] = $row;
			$i++;
		}

		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			//$ret['total'] = $sum;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_comments',$loop);
    }
	}

	function loopGetbuysmartcoinhistories($mode='tpl')
	{

		if (empty($_GET['sort_method'])) {
			$sort_method = 'desc';
		}
		else {
			$sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
		}

		if(empty($_REQUEST['start_date'])) {
			$qry = $this->srchQry();
		} else {
			$qry = $this->srchQry();
		}

		if (empty($_GET['sort_target'])) {
			$sort_target = 'idx';
		}
		else {
			$sort_target = $GLOBALS['_GET_ESCAPE']['sort_target'];
		}
		$sort = ' order by '.$sort_target.' '.$sort_method;

		$query = array();
		$query['table_name'] = 'js_buysmartcoin';
		$query['fields'] = '*';
		$query['where'] = 'where userid = \''.$GLOBALS['_REQUEST_ESCAPE']['userid'].'\' '.$qry.$sort;

		$query['tool'] = 'count';
		$total = $this->dbcon->query($query,__FILE__,__LINE__);

		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$row['list_cnt'] = $i;
			$row['no'] = $total- $_GET['start'] - $row['list_cnt'];

			$row['etc'] = '';

			$loop[] = $row;
			$i++;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			//$ret['total'] = $sum;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_buysmartcoinhistories',$loop);
        }

	}

	function auth()
	{
		global $config_basic, $tradeapi;
		$query = array();
		$query['table_name'] = $this->config['table_name'];

		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';

		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);

		//존재하지 않는 아이디인가?
		if($cnt == 0) {
			//회원정보가 js_wallet_wallet 에 있는지 체크
			$query['table_name'] = "js_wallet_wallet";
			$query['tool'] = 'row';
			$query['where'] = 'where name=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
			$row = $this->dbcon->query($query,__FILE__,__LINE__);
		 	if($row['name']){

				//js_wallet_wallet 에 이미 해당 아이디가 있을경우, js_member에 인서트
				$arr = array();
				$arr[] = 'userid=\''.$this->dbcon->escape($row['name']).'\'';
				$arr[] = 'userpw=\''.$this->dbcon->escape($row['hashkey']).'\'';
				$arr[] = 'name=\'\'';
				$arr[] = 'level_code=\'DE88\'';
				$arr[] = 'regdate=UNIX_TIMESTAMP()';
				$qry = implode(',',$arr);
				$query = array();
				$query['table_name'] = 'js_member';
				$query['tool'] = 'insert';
				$query['fields'] = $qry;
				$result = $this->dbcon->query($query,__FILE__,__LINE__);
			}else{
				if($config_basic['bool_ssl'] > 0) {
					errMsg('ID does not exist. Please check your ID.');
				}
				else {
					jsonMsg(0,'err_id');
				}
			}
		}

		//js_wallet_wallet 에 USD 필드가 없는경우 한줄 넣어줌
		$query['table_name'] = "js_wallet_wallet";
		$query['tool'] = 'row';
		$query['where'] = 'where name=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\' and symbol = \'KRW\' ';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		if(empty($row)){
			//account와 hashkey는 이미 가입한 ARP에서 가져다가 넣기
			$query['table_name'] = "js_wallet_wallet";
			$query['tool'] = 'row';
			$query['where'] = 'where name=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\' and symbol = \'ARP\' ';
			$row2 = $this->dbcon->query($query,__FILE__,__LINE__);

			$arr = array();
			$arr[] = 'name=\''.$this->dbcon->escape($row2['name']).'\'';
			$arr[] = 'symbol=\'KRW\'';
			$arr[] = 'account=\''.$this->dbcon->escape($row2['account']).'\'';
			$arr[] = 'address=\'\'';
			$arr[] = 'hashkey=\''.$this->dbcon->escape($row2['hashkey']).'\'';
			$arr[] = 'balance=0';
			$arr[] = 'core=\'D\'';
			$arr[] = 'regdate=NOW()';
			$qry = implode(',',$arr);
			$query = array();
			$query['table_name'] = 'js_wallet_wallet';
			$query['tool'] = 'insert';
			$query['fields'] = $qry;
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
		}

		$query['table_name'] = 'js_member';
		$query['tool'] = 'row';
		$query['fields'] = 'userno,userid,userpw,name,level_code,bank_account,mobile';
		$query['fields'].= ',bool_confirm_email,bool_confirm_mobile,bool_confirm_idimage,bool_confirm_bank,bool_email_krw_input,bool_sms_krw_input,bool_email_krw_output,bool_sms_krw_output,bool_email_btc_trade,bool_email_btc_input,bool_email_btc_output';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

        //echo $row['permission']; exit;

		/*
		if($row['bool_confirm_email'] < 1) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('You did not verify your email. Please check your email verification.');
			}
			else {
				jsonMsg(0,'You did not verify your email. Please check your email verification.');
			}
		}
		*/

		if($row['userpw'] != hash('sha512', $_POST['userpw'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('Passwords do not match.');
			}
			else {
				jsonMsg(0,'err_pw');
			}
		}

		$_SESSION['USER_NO'] = $row['userno'];
		$_SESSION['USER_ID'] = $row['userid'];
		$_SESSION['USER_NAME'] = $row['name'];
		$_SESSION['USER_LEVEL'] = $row['level_code'];
		$_SESSION['USER_LEVELNAME'] = $this->getLevelname($row['level_code']);

		// 본인인증여부
		$query = "select * from js_member where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
		$_realname_info = $this->dbcon->query_unique_array($query);
		if( !empty($_realname_info) && $_realname_info['bool_realname'] != '0' ) {
			$_SESSION['USER_REALNAME'] = '1';
//			$_SESSION['USER_REALNAME'] = $_realname_info['name'];
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

		//지갑 api에 로그인
		$query = "select * from js_wallet_wallet where name='".$_SESSION['USER_ID']."' and symbol = 'ARP' ";
		$wallet = $this->dbcon->query_unique_array($query);
		$query = "select * from js_wallet_wallet where name='".$_SESSION['USER_ID']."' and symbol = 'GWS' ";
		$wallet2 = $this->dbcon->query_unique_array($query);

		$_SESSION['WALLETNO'] = $wallet['walletno'];
		$_SESSION['ACCOUNT'] = $wallet['account'];
		$_SESSION['ADDRESS'] = $wallet['address'];

		$_SESSION['WALLETNO_ARP'] = $wallet['walletno'];
		$_SESSION['ACCOUNT_ARP'] = $wallet['account'];
		$_SESSION['ADDRESS_ARP'] = $wallet['address'];

		$_SESSION['WALLETNO_GWS'] = $wallet2['walletno'];
		$_SESSION['ACCOUNT_GWS'] = $wallet2['account'];
		$_SESSION['ADDRESS_GWS'] = $wallet2['address'];


		//$query = array();
		//$query['table_name'] = 'js_student';
		//$query['tool'] = 'count';
		//$query['where'] = 'where userid=\''.$row['userid'].'\'';
		//$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		/*
		if($cnt > 0) {
			$_SESSION['BOOL_STUDENT'] = 1;

			$query = array();
			$query['table_name'] = 'js_student';
			$query['tool'] = 'select_one';
			$query['fields'] = 'max(idx)';
			$query['where'] = 'where userid=\''.$row['userid'].'\'';
			$idx = $this->dbcon->query($query,__FILE__,__LINE__);

			$query = array();
			$query['table_name'] = 'js_student AS T1, js_course AS T2';
			$query['tool'] = 'row';
			$query['fields'] = 'T2.course_no';
			$query['where'] = 'where T1.course_code=T2.course_code && T1.idx=\''.$idx.'\'';
			$row = $this->dbcon->query($query,__FILE__,__LINE__);

			$_SESSION['USER_KISU'] = $row['course_no'];

		}
		else {
			$_SESSION['BOOL_STUDENT'] = 0;
			$_SESSION['USER_KISU'] = 0;
		}
		*/

		if(empty($_SESSION['USER_ID'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('You need to try again.');
			}
			else {
				jsonMsg(0,'err_id');
			}
		}

		//var_dump($_REQUEST['ret_url']); exit;
		$ret_url = empty($_REQUEST['ret_url']) ? '/' : base64_decode($_REQUEST['ret_url']);

        // Phone 인증이 되지 않은 경우 certification url로 이동 (18.10.22 Brad)
        // if(!$row['bool_confirm_mobile']) {
        //    $ret_url = "/certification";
		// }
		// 인증된 전화번호로 SMS 발송.
		if($row['bool_confirm_mobile']) {
			$msg_data = array();
			$msg_data['tran_phone'] = $row['mobile'];
			if($msg_data['tran_phone']) {
                $config_basic = getConfig('js_config_basic','shop_name,shop_url');
				$msg_data['tran_msg']  = "[".$config_basic['shop_name']."] ".str_replace('{site_name}',$config_basic['shop_name'],Lang('main_258'))." ".date('m/d H:i'); // 로그인 안내 {site_name} 사이트에 로그인 하였습니다.
				/* 고객요청 */
				// @sendSms($msg_data);
			}
		}

		if($config_basic['bool_ssl'] > 0) {
			// replaceGo('//'.$_SERVER['SERVER_NAME'].$ret_url);
		}
		else {
			jsonMsg(1,$ret_url);
		}
	}


	//2009-01-25
	function out()
	{
		unset($_SESSION['USER_ID']);
		unset($_SESSION['USER_NAME']);
		unset($_SESSION['USER_LEVEL']);
		unset($_SESSION['USER_LEVELNAME']);
		unset($_SESSION['BOOL_STUDENT']);
		unset($_SESSION['USER_KISU']);

		unset($_SESSION['bool_confirm_email']);
		unset($_SESSION['bool_confirm_mobile']);
		unset($_SESSION['bool_email_krw_input']);
		unset($_SESSION['bool_sms_krw_input']);
		unset($_SESSION['bool_email_krw_output']);
		unset($_SESSION['bool_sms_krw_output']);
		unset($_SESSION['bool_email_btc_trade']);
		unset($_SESSION['bool_email_btc_input']);
		unset($_SESSION['bool_email_btc_output']);
		unset($_SESSION['USER_REALNAME']);

		if(empty($_SESSION['USER_ID']) && empty($_SESSION['USER_NAME']) && empty($_SESSION['USER_LEVEL']) && empty($_SESSION['BOOL_STUDENT'])) {
			session_destroy();
			alertGo('','/');
		}
		else {
			errMsg("로그아웃되지 않았습니다.\n\n다시 시도하여 주세요.");
		}
	}

	function memberXlsInsert()
	{
		$csv_file = $_SERVER["DOCUMENT_ROOT"].'/data/xls/'.mt_rand().'.csv'; // 2012-08-15 임시 엑셀 저장

		if(file_exists($_FILES['xls']['tmp_name'])) {
			if (!move_uploaded_file($_FILES['xls']['tmp_name'], $csv_file) ) {
				jsonMsg(0,'파일이 올바른지 확인해 주세요');
			}
			$csvload = file($csv_file);
			$csvarray = split("\n",implode($csvload));

			$arr_field = array('userid','name','phone','mobile','email','zipcode','address_a,address_b');

			for($i=1;$i<count($csvarray)-1;$i++){
				$csvarray[$i] = iconv("EUC-KR","UTF-8", $csvarray[$i]);
				//각 행을 콤마를 기준으로 각 필드에 나누고 db입력시 에러가 없게 하기위해서 addslashes함수를 이용해 \를 붙인다
				list($userid,
					$name,
					$email,
					$phone,
					$mobile,
					$zipcode,
					$address_a,
					$address_b) = explode(",",addslashes($csvarray[$i]));

				foreach ($arr_field as $key => $val) {
					if(!empty(${$val})) {
						${$val} = trim(${$val});
					}
					else {
						${$val} = '';
					}
				}


				$query = array();
				$query['table_name'] = $this->config['table_name'];
				$query['tool'] = 'count';//select,select_one,select_affect,row,,insert,insert_idx,update,delete,drop
				$query['where'] = 'where userid=\''.$this->dbcon->escape($userid).'\'';
				$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
				if($cnt == 0) {
					$userpw = hash('sha512', str_replace('-','',$phone)); //md5(str_replace('-','',$phone));
					$arr = array();
					$arr[] = 'userid=\''.$this->dbcon->escape($userid).'\'';
					$arr[] = 'userpw=\''.$this->dbcon->escape($userpw).'\'';
					$arr[] = 'name=\''.$this->dbcon->escape($name).'\'';
					$arr[] = 'phone=\''.$this->dbcon->escape($phone).'\'';
					$arr[] = 'mobile=\''.$this->dbcon->escape($mobile).'\'';
					$arr[] = 'email=\''.$this->dbcon->escape($email).'\'';
					$arr[] = 'zipcode=\''.$this->dbcon->escape($zipcode).'\'';
					$arr[] = 'address_a=\''.$this->dbcon->escape($address_a).'\'';
					$arr[] = 'address_b=\''.$this->dbcon->escape($address_b).'\'';
					$arr[] = 'level_code=\'DE88\'';
					$arr[] = 'regdate=UNIX_TIMESTAMP()';
					$qry = implode(',',$arr);
					$query = array();
					$query['table_name'] = 'js_member';
					$query['tool'] = 'insert';
					$query['fields'] = $qry;
					$result = $this->dbcon->query($query,__FILE__,__LINE__);
					if(!$result) {
						jsonMsg(0);
					}
				}

			}
			//입력이 된후 업로드된 파일을 삭제한다
			unlink($csv_file);
		}
		jsonMsg(1);
	}

	function srchQry()
	{
		$arr = array();
		if(!empty($_GET['srch_userid'])) {
			$arr[] = 'userid like \'%'.$GLOBALS['_GET_ESCAPE']['srch_userid'].'%\'';
		}
		if(!empty($_GET['name'])) {
			$arr[] = 'name like \'%'.urldecode($GLOBALS['_GET_ESCAPE']['name']).'%\'';
		}
		if(!empty($_GET['level_code'])) {
			$arr[] = 'level_code=\''.$GLOBALS['_GET_ESCAPE']['level_code'].'\''; }
		if(!empty($_GET['phone_a']) || !empty($_GET['phone_b']) || !empty($_GET['phone_c'])) {
			$phone = array();
			if(!empty($_GET['phone_a'])) {
				$phone[] = $GLOBALS['_GET_ESCAPE']['phone_a'];
			}
			if(!empty($_GET['phone_b'])) {
				$phone[] = $GLOBALS['_GET_ESCAPE']['phone_b'];
			}
			if(!empty($_GET['phone_c'])) {
				$phone[] = $GLOBALS['_GET_ESCAPE']['phone_c'];
			}
			$arr[] = 'phone like \'%'.implode('-',$phone).'%\'';
		}
		if(!empty($_GET['mobile_a']) || !empty($_GET['mobile_b']) || !empty($_GET['mobile_c'])) {
			$mobile = array();
			if(!empty($_GET['mobile_a'])) {
				$mobile[] = $GLOBALS['_GET_ESCAPE']['mobile_a'];
			}
			if(!empty($_GET['mobile_b'])) {
				$mobile[] = $GLOBALS['_GET_ESCAPE']['mobile_b'];
			}
			if(!empty($_GET['mobile_c'])) {
				$mobile[] = $GLOBALS['_GET_ESCAPE']['mobile_c'];
			}
			$arr[] = 'mobile like \'%'.implode('-',$mobile).'%\'';
		}
		if(isset($_GET['bool_email'])) { $arr[] = 'ok_mail=\''.$GLOBALS['_GET_ESCAPE']['ok_mail'].'\''; }
		if(isset($_GET['ok_sms'])) { $arr[] = 'ok_sms=\''.$GLOBALS['_GET_ESCAPE']['ok_sms'].'\''; }
		$ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';
		return $ret;
	}

	function srchUrl($hide_sort=0,$hide_loop=0)
	{
		$arr = array();
		//if(!empty($_GET['start'])) { $arr[] = 'start='.$GLOBALS['_GET_ESCAPE']['start']; }
		if(!empty($_GET['srch_userid'])) { $arr[] = 'srch_userid='.$GLOBALS['_GET_ESCAPE']['srch_userid']; }
		//if(!empty($_GET['userid'])) { $arr[] = 'userid='.$GLOBALS['_GET_ESCAPE']['userid']; }
		if(!$hide_sort) {
			if(!empty($_GET['loop_scale'])) { $arr[] = 'loop_scale='.$GLOBALS['_GET_ESCAPE']['loop_scale']; }
		}
		if(!empty($_GET['name'])) { $arr[] = 'name='.$GLOBALS['_GET_ESCAPE']['name']; }
		if(!empty($_GET['level_code'])) { $arr[] = 'level_code='.$GLOBALS['_GET_ESCAPE']['level_code']; }
		if(!empty($_GET['phone_a'])) { $arr[] = 'phone_a='.$GLOBALS['_GET_ESCAPE']['phone_a']; }
		if(!empty($_GET['phone_b'])) { $arr[] = 'phone_b='.$GLOBALS['_GET_ESCAPE']['phone_b']; }
		if(!empty($_GET['phone_c'])) { $arr[] = 'phone_c='.$GLOBALS['_GET_ESCAPE']['phone_c']; }
		if(!empty($_GET['mobile_a'])) { $arr[] = 'mobile_a='.$GLOBALS['_GET_ESCAPE']['mobile_a']; }
		if(!empty($_GET['mobile_b'])) { $arr[] = 'mobile_b='.$GLOBALS['_GET_ESCAPE']['mobile_b']; }
		if(!empty($_GET['mobile_c'])) { $arr[] = 'mobile_c='.$GLOBALS['_GET_ESCAPE']['mobile_c']; }
		if(!empty($_GET['hit'])) { $arr[] = 'hit='.$GLOBALS['_GET_ESCAPE']['hit']; }
		if(!empty($_GET['bool_email'])) { $arr[] = 'bool_email='.$GLOBALS['_GET_ESCAPE']['bool_email']; }
		if(!empty($_GET['bool_sms'])) { $arr[] = 'bool_sms='.$GLOBALS['_GET_ESCAPE']['bool_sms']; }

		if(!empty($_GET['bool_confirm_email'])) { $arr[] = 'bool_confirm_email='.$GLOBALS['_GET_ESCAPE']['bool_confirm_email']; }
		if(!empty($_GET['bool_confirm_mobile'])) { $arr[] = 'bool_confirm_mobile='.$GLOBALS['_GET_ESCAPE']['bool_confirm_mobile']; }
		if(!empty($_GET['bool_email_krw_input'])) { $arr[] = 'bool_email_krw_input='.$GLOBALS['_GET_ESCAPE']['bool_email_krw_input']; }
		if(!empty($_GET['bool_sms_krw_input'])) { $arr[] = 'bool_sms_krw_input='.$GLOBALS['_GET_ESCAPE']['bool_sms_krw_input']; }
		if(!empty($_GET['bool_email_krw_output'])) { $arr[] = 'bool_email_krw_output='.$GLOBALS['_GET_ESCAPE']['bool_email_krw_output']; }
		if(!empty($_GET['bool_sms_krw_output'])) { $arr[] = 'bool_sms_krw_output='.$GLOBALS['_GET_ESCAPE']['bool_sms_krw_output']; }
		if(!empty($_GET['bool_email_btc_trade'])) { $arr[] = 'bool_email_btc_trade='.$GLOBALS['_GET_ESCAPE']['bool_email_btc_trade']; }
		if(!empty($_GET['bool_email_btc_input'])) { $arr[] = 'bool_email_btc_input='.$GLOBALS['_GET_ESCAPE']['bool_email_btc_input']; }
		if(!empty($_GET['bool_email_btc_output'])) { $arr[] = 'bool_email_btc_output='.$GLOBALS['_GET_ESCAPE']['bool_email_btc_output']; }

		if(!$hide_sort) {
			if(!empty($_GET['sort_target'])) { $arr[] = 'sort_target='.$GLOBALS['_GET_ESCAPE']['sort_target']; }
			if(!empty($_GET['sort_method'])) { $arr[] = 'sort_method='.$GLOBALS['_GET_ESCAPE']['sort_method']; }
		}
		//if(!empty($_GET['loop_scale'])) { $arr[] = 'loop_scale='.$GLOBALS['_GET_ESCAPE']['loop_scale']; }
		$ret = sizeof($arr) > 0 ? '&'.implode('&',$arr) : '';
		return $ret;
	}

	function getLevelname($level_code)
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select_one';
		$query['fields'] = 'level_name';
		$query['where'] = 'where level_code=\''.$level_code.'\'';
		$level_name = $this->dbcon->query($query,__FILE__,__LINE__);
		return $level_name;
	}

	function getCentername($code)
	{
		$query = array();
		$query['table_name'] = 'js_ara_center';
		$query['tool'] = 'select_one';
		$query['fields'] = 'center_name';
		$query['where'] = 'where center_code=\''.$code.'\'';
		$center_name = $this->dbcon->query($query,__FILE__,__LINE__);
		return $center_name;
	}

	/* Ara */
	function getAraCreditBuy()
	{
		$query = array();
		$query['table_name'] = 'js_ara_credit_buy_txn';
		$query['tool'] = 'select_one';
		$query['fields'] = 'SUM(amount)';
		$query['where'] = 'where 1';
		$ara_credit_buy_txn = $this->dbcon->query($query,__FILE__,__LINE__);
		return $ara_credit_buy_txn;
	}

	function getAraCreditSell()
	{
		$query = array();
		$query['table_name'] = 'js_ara_credit_sell';
		$query['tool'] = 'select_one';
		$query['fields'] = 'SUM(remain_amount)';
		$query['where'] = 'where 1';
		$ara_credit_sell = $this->dbcon->query($query,__FILE__,__LINE__);
		return $ara_credit_sell;
	}

	function getWalletArp()
	{

		// $query = array();
		// $query['table_name'] = 'js_wallet_wallet';
		// $query['tool'] = 'select_one';
		// $query['fields'] = 'SUM(balance)';
		// $query['where'] = 'where symbol = \'ARP\'';
		// $wallet_arp = $this->dbcon->query($query,__FILE__,__LINE__);
		$wallet_arp = $this->getAraCreditBuy() * 1;

		return $wallet_arp;
	}

	function getWalletArpRate()
	{
		// $query = array();
		// $query['table_name'] = 'js_wallet_wallet';
		// $query['tool'] = 'select_one';
		// $query['fields'] = 'SUM(balance)';
		// $query['where'] = 'where symbol = \'ARP\'';
		// $wallet_arp = $this->dbcon->query($query,__FILE__,__LINE__);

		$wallet_arp = $this->getAraCreditBuy() * 1.1;
		$wallet_arp_rate =  round( $wallet_arp / 600000000 * 100,2); //총 6억(GWS의 10배)
		if($wallet_arp_rate < 308) $wallet_arp_rate = 30;
		else $wallet_arp_rate = $wallet_arp_rate;
		return $wallet_arp_rate;
	}

	function getWalletGws()
	{
		// $query = array();
		// $query['table_name'] = 'js_wallet_wallet';
		// $query['tool'] = 'select_one';
		// $query['fields'] = 'SUM(balance)';
		// $query['where'] = 'where symbol = \'GWS\' AND ( name != \'walletmanager\' AND name !=  \'coldwallet\' )';
		// $wallet_gws = $this->dbcon->query($query,__FILE__,__LINE__);
		// // var_dump($wallet_gws); exit;
		// return $wallet_gws;

		/* Goldwings = Reward1 + Reward4 */
		// Reward1
		$query = array();
		$query['table_name'] = 'js_ara_credit_buy';
		$query['tool'] = 'select_one';
		$query['fields'] = 'SUM(receive_wings) * 2'; // Reward2 개념으로 적용 수정
		$query['where'] = 'where state = \'Y\' AND  buyer <> \'walletmanager\'';
		$reward1 = $this->dbcon->query($query,__FILE__,__LINE__);

		// Reward4
		$query = array();
		$query['table_name'] = 'js_check_shop_order_no';
		$query['tool'] = 'select_one';
		$query['fields'] = 'SUM(amount)';
		$query['where'] = 'where mode = \'IN\' AND  user_id <> \'walletmanager\' AND user_id <> \'newtest2\' ';
		$reward4 = $this->dbcon->query($query,__FILE__,__LINE__);

		$goldwings = $reward1 + $reward4;

		return $goldwings;

	}

	function getWalletGwsRate()
	{
		// var_dump($this->getWalletGws());
		$wallet_gws = $this->getWalletGws();
		$wallet_gws_rate =  round( $wallet_gws / 60000000 * 100,2);
		if($wallet_gws_rate < 28) $wallet_gws_rate = 28;
		else $wallet_gws_rate = $wallet_gws_rate;
		return $wallet_gws_rate;
	}

	function get_member_meta($userno, $name) {
		$sql = "SELECT `value` FROM js_member_meta WHERE `userno`='{$this->dbcon->escape($userno)}' AND `name`='{$this->dbcon->escape($name)}' ";
		$r = $this->dbcon->query_unique_value($sql);
		return $r ? $r : '';
	}
	function set_member_meta($userno, $name, $value) {
		$sql = "INSERT INTO js_member_meta SET `userno`='{$this->dbcon->escape($userno)}', `name`='{$this->dbcon->escape($name)}', `value`='{$this->dbcon->escape($value)}' ON DUPLICATE KEY UPDATE `value`='{$this->dbcon->escape($value)}' ";
		return $this->dbcon->query($sql);
	}
	function del_member_meta($userno, $name) {
		$sql = "DELETE FROM js_member_meta WHERE `userno`='{$this->dbcon->escape($userno)}' AND `name`='{$this->dbcon->escape($name)}' ";
		return $this->dbcon->query($sql);
	}

	/**
	 * 인증 관련 데이터만 추출
	 */
	function get_confirm_data() {
		// $query = "SELECT userno, userid, NAME, mobile, bool_confirm_mobile, email, bool_confirm_email, image_identify_url, image_mix_url, bool_confirm_idimage, bank_name, bank_account, bank_owner, image_bank_url, bool_confirm_bank
		// FROM js_member
		// ORDER BY bool_confirm_idimage ASC, image_identify_url DESC";
		$query['table_name'] = 'js_member m';
		$query['tool'] = 'select';
		$query['fields'] = "userno, userid, name, nickname, regdate, mobile, bool_confirm_mobile, email, bool_confirm_email, image_identify_url, image_mix_url, bool_confirm_idimage, bank_name, bank_account, bank_owner, image_bank_url, bool_confirm_bank, bool_confirm_join, recomid, ifnull((select name from js_member where userid=m.recomid), '') recomname";
		$query['where'] = 'where 1';
		if($_GET['searchval']) {
			$query['where'] .= " AND (userid LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR name LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR email LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR mobile LIKE '%".$this->dbcon->escape($_GET['searchval'])."%') ";
		}
		if($_GET['sort_target'] ) {
			$query['where'] .= ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['where'] .= ', ';
				}
				$query['where'] .= $_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$query['where'] .= ' ORDER BY bool_confirm_idimage ASC, image_identify_url DESC ';
		}
		$list = $this->bList($query,'loop','_lists',true);
		for($i=0; $i<count($list); $i++) {
			// if($list[$i]['bool_confirm_join']!='1' && $list[$i]['nickname']) {
			// 	$_nickname = $list[$i]['nickname'];
			// 	$prev_stocks = $this->dbcon->query_list_array("SELECT ewp.*, IFNULL(tc.name,'') stock_name FROM js_exchange_wallet_previous ewp LEFT JOIN js_trade_currency tc ON ewp.symbol=tc.symbol WHERE ewp.nickname='{$this->dbcon->escape($_nickname)}' AND ewp.userno='0' "); // 작업중
			// 	$list[$i]['prev_stocks'] = json_encode($prev_stocks);
			// 	$prev_airdrop = $this->dbcon->query_list_array("SELECT ewp.*, IFNULL(tc.name,'') stock_name FROM js_trade_airdrop ewp LEFT JOIN js_trade_currency tc ON ewp.symbol=tc.symbol WHERE ewp.nickname='{$this->dbcon->escape($_nickname)}' AND ewp.userno='0' "); // 작업중
			// 	$list[$i]['prev_airdrop'] = json_encode($prev_airdrop);
			// } else {
			// 	$list[$i]['prev_stocks'] = '[]';
			// 	$list[$i]['prev_airdrop'] = '[]';
			// }
			$list[$i]['confirm_idimage_date'] = $this->get_member_meta($list[$i]['userno'], 'confirm_idimage_date');
			$list[$i]['reject_idimage_date'] = $this->get_member_meta($list[$i]['userno'], 'reject_idimage_date');
			$list[$i]['confirm_bank_date'] = $this->get_member_meta($list[$i]['userno'], 'confirm_bank_date');
			$list[$i]['reject_bank_date'] = $this->get_member_meta($list[$i]['userno'], 'reject_bank_date');
		}
		return $list;
	}

	/**
	 * 가입 승인시 작업
	 */
	private function _confirm_join($userno) {
		$nickname = $this->dbcon->query_one("SELECT nickname FROM js_member WHERE userno='{$this->dbcon->escape($userno)}' ");
		// // 기존 잔액(주식수)가져오기 js_exchange_wallet_previous
		// if($nickname) {
		// 	$stocks = $this->dbcon->query_list_object("SELECT * FROM js_exchange_wallet_previous WHERE nickname='{$this->dbcon->escape($nickname)}' AND userno='0'");
		// 	if($stocks) {
		// 		foreach($stocks as $s) {
		// 			$balance = $s->balance;
		// 			// 스톡옵션에 지급하지 말아야할 수량 있는지 확인 후 차감하기
		// 			$airdrop_waiting = $this->dbcon->query_one("SELECT SUM(volumn) FROM js_trade_airdrop WHERE nickname='{$this->dbcon->escape($nickname)}' AND userno='0' AND NOW()<=lockup_date AND volumn>0 ");
		// 			$balance = $balance > $airdrop_waiting ? $balance - $airdrop_waiting : 0;
		// 			if($balance>0) {
		// 				$this->dbcon->query("INSERT IGNORE INTO js_exchange_wallet SET userno='{$this->dbcon->escape($userno)}', symbol='{$this->dbcon->escape($s->symbol)}', active='Y', locked='N', bool_sell='1', bool_buy='1', bool_withdraw='1', confirmed='{$this->dbcon->escape($balance)}', unconfirmed='0', account='', address='', regdate=NOW()");
		// 			}
		// 		}
		// 		$this->dbcon->query("UPDATE js_exchange_wallet_previous SET userno='{$this->dbcon->escape($userno)}' WHERE nickname='{$this->dbcon->escape($nickname)}' AND userno='0'");
		// 	}
		// }
		// // 기존 스톡옵션 사용자 지정 js_trade_airdrop ( 넣어주는것은 크론잡에서 매일 작동합니다. )
		// if($nickname) {
		// 	// 스톡옵션 사용자 번호 적용
		// 	$this->dbcon->query("UPDATE js_trade_airdrop SET userno='{$this->dbcon->escape($userno)}' WHERE nickname='{$this->dbcon->escape($nickname)}' AND userno='0'");
		// }
		// // 포인트 지급
		// $prev_krw_amount = preg_replace('/[^0-9.]/','',$_POST['prev_krw_amount']);
		// if($prev_krw_amount>0) {
		// 	$wallet_krw = $this->dbcon->query_one("SELECT COUNT(*) FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($userno)}' AND symbol='USD' ");
		// 	if($wallet_krw) {
		// 		$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed+'{$this->dbcon->escape($prev_krw_amount)}' WHERE userno='{$this->dbcon->escape($userno)}' AND symbol='USD'");
		// 	} else {
		// 		$this->dbcon->query("INSERT INTO js_exchange_wallet SET userno='{$this->dbcon->escape($userno)}', confirmed='{$this->dbcon->escape($prev_krw_amount)}', symbol='USD', active='Y', regdate=NOW() ");
		// 	}
		// 	$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET userno='{$this->dbcon->escape($userno)}', symbol='USD', address='', regdate=NOW(), txndate=NOW(), address_relative='', txn_type='R', direction='I', amount='{$this->dbcon->escape($prev_krw_amount)}', fee='0', fee_relative='', tax='0', status='D', key_relative='', txn_method='', msg='관리자가 이전 기업페이를 지급했습니다.', video_idx='' ");
		// }
	}

	/**
	 * mobile, email, idimage, bank 4가지 승인 / 미승인 처리
	 */
	function confirm( $type, $value, $userno ) {
		if(! in_array($type, array('email', 'idimage', 'mobile','bank','join'))) {
			return false;
		}
		$sql = "update js_member set ";
		switch($type) {
			case 'join':
				$sql .= " bool_confirm_join = '1'" ; // 승인처리만합니다.
			break;
			case 'email':
				$sql .= " bool_confirm_email = '". ($value=='1' ? '1' : '0') . "'" ;
			break;
			case 'idimage':
				$sql .= " bool_confirm_idimage = '". ($value=='1' ? '1' : '0') . "'" ;
			break;
			case 'mobile':
				$sql .= " bool_confirm_mobile = '". ($value=='1' ? '1' : '0') . "'" ;
			break;
			case 'bank':
				$sql .= " bool_confirm_bank = '". ($value=='1' ? '1' : '0') . "'" ;
			break;
		}
		$sql .= " where userno = '{$userno}' ";
		// var_dump($sql); exit;
		$r = $this->dbcon->query($sql);
		if($r) {
			if($type=='join') { // 가입처리합니다.
				$this->_confirm_join($userno);
			}
			if($type=='idimage') { // 신분증 인증일때 sms 발송.
				if($value=='1') {
					$this->set_member_meta($userno, 'confirm_idimage_date', date('Y-m-d H:i:s'));
					$this->set_member_meta($userno, 'reject_idimage_msg', '');
					$_member_info = $this->get_member_info($userno);
					$msg_data = array();
					$msg_data['tran_phone'] = $_member_info['mobile'];
					$config_basic = getConfig('js_config_basic','shop_name,shop_url');
					$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . '보내주신 신분증 사진을 확인한 결과 정상적으로 승인되었습니다.';
					@ sendSms($msg_data);
				}
			}
			if($type=='mobile') { // mobile 인증일때
				if($value!='1') { // 비승인시 핸폰번호 지우기. 회원가입을 못하기 때문에...
					$sql = "update js_member set mobile = '' where userno='{$userno}' ";
					$this->dbcon->query($sql);
				}
			}
			if($type=='bank') { // 은행계좌 인증일때 sms 발송.
				if($value=='1') {
					$this->set_member_meta($userno, 'confirm_bank_date', date('Y-m-d H:i:s'));
					$this->set_member_meta($userno, 'reject_bank_msg', '');
					$_member_info = $this->get_member_info($userno);
					$msg_data = array();
					$msg_data['tran_phone'] = $_member_info['mobile'];
					$config_basic = getConfig('js_config_basic','shop_name,shop_url');
					$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . '보내주신 통장사본을 확인한 결과 정상적으로 승인되었습니다.';
					@ sendSms($msg_data);


					// 추천인 정보
					$recomid = $this->dbcon->query_unique_value("SELECT recomid FROM js_member WHERE userno='{$userno}' ");
					$recomno = $this->dbcon->query_unique_value("SELECT userno from js_member WHERE userid='{$this->dbcon->escape($recomid)}' ");
					if($recomno) {

						// 초대하기 정보
						$receiver_address = preg_replace('/^820/', '82', str_replace('+8201', '821', $_member_info['mobile']));
						$invite_info = $this->dbcon->query_unique_object("SELECT * FROM js_exchange_share_link where receiver_address='{$this->dbcon->escape($receiver_address)}' and userno='{$recomno}' and pay_time='' and share_type='I' ");
						// 2020.10.12 1 HTC로 변경
						$amount = 1; //preg_replace('[^0-9.]','',$invite_info->amount); // 1 HTC로 고정
						$symbol = 'HTC'; //$invite_info->symbol; // htc로 고정
						$sender_userno = '2'; // 보너스 지급 계정 회원번호(walletmanager)
						if($invite_info && $amount>0 && $invite_info->pay_time=='') {

							// 지급자 지갑 정보
							if($sender_userno) {
								$sender_wallet = $this->dbcon->query_unique_object("select * from js_exchange_wallet where symbol='{$this->dbcon->escape($symbol)}' and userno='$sender_userno' ");
							}

							// 추천받은 회원(Invitee)에게 1000HTP 지급하기
							include_once(dirname(__file__).'/../api/lib/'.strtoupper($symbol).'/'.strtoupper($symbol).'Coind.php');
							$class_name = strtoupper($symbol).'Coind';
							$Coind = new $class_name(__API_RUNMODE__);
							$htc_wallet = $this->dbcon->query_unique_object("select * from js_exchange_wallet where symbol='{$this->dbcon->escape($symbol)}' and userno='$userno' ");
							if(!$htc_wallet) {
								// 지갑 만들기
								$address = $Coind->genNewAddress(__APP_NAME__.'/'.__API_RUNMODE__.'/'.$userno, $userno); // __APP_NAME__ , __API_RUNMODE__ 는 common.php 에서 정의함.
								if($address) {
									$this->dbcon->query('INSERT INTO js_exchange_wallet SET userno='.$this->dbcon->escape($userno).', symbol="'.$this->dbcon->escape($symbol).'", regdate=SYSDATE(), confirmed=0, address="'.$this->dbcon->escape($address).'" ');
								}
							}
							$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed+{$amount} where userno='".$this->dbcon->escape($userno)."' AND symbol='{$this->dbcon->escape($symbol)}' ");
							$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET amount={$amount}, userno='".$userno."', address='{$this->dbcon->escape($htc_wallet->address)}', symbol='{$this->dbcon->escape($symbol)}', regdate=NOW(), txndate=NOW(), txn_type='IE', direction='I', fee=0, tax=0, txn_method='COIN', service_name='WALLET', status='D', key_relative='{$this->dbcon->escape($invite_info->id)}', address_relative='{$this->dbcon->escape($sender_wallet->address)}' ");
							// 지급자 지갑에서 차감
							if($sender_userno) {
								$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed-{$amount} where userno='".$this->dbcon->escape($sender_userno)."' AND symbol='{$this->dbcon->escape($symbol)}' ");
								$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET amount={$amount}, userno='".$sender_userno."', address='{$this->dbcon->escape($sender_wallet->address)}', symbol='{$this->dbcon->escape($symbol)}', regdate=NOW(), txndate=NOW(), txn_type='I', direction='O', fee=0, tax=0, txn_method='COIN', service_name='WALLET', status='D', key_relative='{$this->dbcon->escape($invite_info->id)}', address_relative='{$this->dbcon->escape($htc_wallet->address)}' ");
							}

							// 추천인(Inviter)에게 1000HTP 지급하기
							$htc_wallet = $this->dbcon->query_unique_object("SELECT * FROM js_exchange_wallet WHERE symbol='{$this->dbcon->escape($symbol)}' AND userno='{$recomno}' ");
							if(!$htc_wallet) {
								// 지갑 만들기
								$address = $Coind->genNewAddress(__APP_NAME__.'/'.__API_RUNMODE__.'/'.$recomno, $recomno); // __APP_NAME__ , __API_RUNMODE__ 는 common.php 에서 정의함.
								if($address) {
									$this->dbcon->query('INSERT INTO js_exchange_wallet SET userno='.$this->dbcon->escape($recomno).', symbol="'.$this->dbcon->escape($symbol).'", regdate=SYSDATE(), confirmed=0, address="'.$this->dbcon->escape($address).'" ');
								}
							}
							$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed+{$amount} WHERE userno='".$recomno."' AND symbol='{$this->dbcon->escape($symbol)}' ");
							$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET amount={$amount}, userno='".$recomno."', address='{$this->dbcon->escape($htc_wallet->address)}', symbol='{$this->dbcon->escape($symbol)}', regdate=NOW(), txndate=NOW(), txn_type='IR', direction='I', fee=0, tax=0, txn_method='COIN', service_name='WALLET', status='D', key_relative='{$this->dbcon->escape($invite_info->id)}', address_relative='{$this->dbcon->escape($sender_wallet->address)}' ");
							// 지급자 지갑에서 차감
							if($sender_userno) {
								$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed-{$amount} where userno='".$this->dbcon->escape($sender_userno)."' AND symbol='{$this->dbcon->escape($symbol)}' ");
								$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET amount={$amount}, userno='".$sender_userno."', address='{$this->dbcon->escape($sender_wallet->address)}', symbol='{$this->dbcon->escape($symbol)}', regdate=NOW(), txndate=NOW(), txn_type='I', direction='O', fee=0, tax=0, txn_method='COIN', service_name='WALLET', status='D', key_relative='{$this->dbcon->escape($invite_info->id)}', address_relative='{$this->dbcon->escape($htc_wallet->address)}' ");
							}

							// save pay time
							$this->dbcon->query("UPDATE js_exchange_share_link SET pay_time=UNIX_TIMESTAMP() WHERE `id`='{$this->dbcon->escape($invite_info->id)}' ");
						}
					}

				}
			}
		}
		return $r;
	}

	/**
	 * mobile, email, idimage, bank 4가지 승인 / 미승인 처리
	 */
	function reject ( $type, $userno, $msg='' ) {
		global $tradeapi;

		if(! in_array($type, array('idimage','bank'))) {
			return false;
		}
		$sql = "update js_member set ";
		switch($type) {
			case 'email':
			// $sql .= " bool_confirm_email = '". ($value=='1' ? '1' : '0') . "'" ;
			break;
			case 'idimage':
			$msg = ( $msg!='' ? $msg : '신분증 인증이 반려되었습니다. 올바른 사진을 등록하셨는지 확인해 주세요.');
			$sql .= " bool_confirm_idimage = '0'" ;
			break;
			case 'mobile':
			// $sql .= " bool_confirm_mobile = '". ($value=='1' ? '1' : 'z0') . "'" ;
			break;
			case 'bank':
			$msg = ( $msg!='' ? $msg : '은행계좌 인증이 반려되었습니다. 올바른 통장사본을 등록하셨는지 확인해 주세요.');
			$sql .= " bool_confirm_bank = '". ($value=='1' ? '1' : '0') . "'" ;
			break;
		}
		$sql .= " where userno = '{$userno}' ";
		$r = $this->dbcon->query($sql);
		if($r) {
			if($type=='idimage') { // 신분증 인증일때
				// 기존 이미지 삭제하기.
				$member_image = $this->dbcon->query_unique_object("select image_identify_url, image_mix_url from js_member where userno='{$userno}'  ");
				if($member_image->image_identify_url){
					@ $tradeapi->delete_file_to_s3($member_image->image_identify_url);
				}
				if($member_image->image_mix_url){
					@$tradeapi->delete_file_to_s3($member_image->image_mix_url);
				}
				$sql = "update js_member set image_identify_url='', image_mix_url=''  where userno='{$userno}' ";
				$this->dbcon->query($sql);
				//  sms 발송.
				$this->set_member_meta($userno, 'reject_idimage_date', date('Y-m-d H:i:s'));
				$this->set_member_meta($userno, 'reject_idimage_msg', $msg);
				$_member_info = $this->get_member_info($userno);
				$msg_data = array();
				$msg_data['tran_phone'] = $_member_info['mobile'];
				$config_basic = getConfig('js_config_basic','shop_name,shop_url');
				$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . $msg;
				if($msg_data['tran_phone']){
					sendSms($msg_data);
				}
			}
			if($type=='bank') { // 인행계좌 인증일때
				// 기존 이미지 삭제하기.
				$member_image = $this->dbcon->query_unique_object("select image_bank_url from js_member where userno='{$userno}'");
				if($member_image->image_bank_url){
					@ $tradeapi->delete_file_to_s3($member_image->image_bank_url);
				}
				$sql = "update js_member set image_bank_url='' where userno='{$userno}' ";
				$this->dbcon->query($sql);
				//  sms 발송.
				$this->set_member_meta($userno, 'reject_bank_date', date('Y-m-d H:i:s'));
				$this->set_member_meta($userno, 'reject_bank_msg', $msg);
				$_member_info = $this->get_member_info($userno);
				$msg_data = array();
				$msg_data['tran_phone'] = $_member_info['mobile'];
				$config_basic = getConfig('js_config_basic','shop_name,shop_url');
				$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . $msg;
				if($msg_data['tran_phone']){
					sendSms($msg_data);
				}
			}
		}
		return $r;
	}

	/**
	 * mobile, email, idimage, bank 4가지 승인 / 미승인 처리
	 */
	function delete_img ( $imgurl, $userno ) {
		global $tradeapi;
		// 이미지 삭제하기.
		$member_image = $this->dbcon->query_unique_object("select image_identify_url, image_mix_url, image_bank_url from js_member where userno='{$userno}'  ");
		if($member_image->image_identify_url == $imgurl){
			@ $tradeapi->delete_file_to_s3($member_image->image_identify_url);
			$sql = "update js_member set image_identify_url=''  where userno='{$userno}' ";
			$this->dbcon->query($sql);
		}
		if($member_image->image_mix_url == $imgurl){
			@$tradeapi->delete_file_to_s3($member_image->image_mix_url);
			$sql = "update js_member set image_mix_url=''  where userno='{$userno}' ";
			$this->dbcon->query($sql);
		}
		if($member_image->image_bank_url == $imgurl){
			@$tradeapi->delete_file_to_s3($member_image->image_bank_url);
			$sql = "update js_member set image_bank_url=''  where userno='{$userno}' ";
			$this->dbcon->query($sql);
		}
		return true;
	}

	/**
	 * 회원 lock  처리
	 */
	function lock( $type, $userno, $month ) {
		global $tradeapi;

		$userid = $this->dbcon->query_unique_value("select userid from js_member where userno='{$userno}' ");

		// if(! in_array($type, array('lock'))) {
		// 	return false;
		// }
		//lock_term lock 기간 추가
		$sql = "update js_member set ";
		if($month==12) {
			$sql .= " locked = 'Y', untildate=UNIX_TIMESTAMP(NOW() + INTERVAL 12 MONTH), lock_term=12 ";
		} elseif($month==6) {
			$sql .= " locked = 'Y', untildate=UNIX_TIMESTAMP(NOW() + INTERVAL 6 MONTH), lock_term=6 ";
		} else {
			$sql .= " locked = 'N', untildate=0, lock_term=0 ";
		}
		$sql .= " where userno = '{$userno}' ";

		$r = $this->dbcon->query($sql);
		if($r) {
			// 지갑정보 Lock
			$sql = "update js_wallet_wallet set ";
			if($month==12 || $month==6) {
				$sql .= " locked = 'Y'";
			} else {
				$sql .= " locked = 'N'";
			}
			$sql .= " where name='{$userid}' ";
			$this->dbcon->query($sql);
		}
		return $r;
	}

	/**
		 * levelchange  처리
		 */
	function levelchange( $type, $userno, $levelcode ) {
		global $tradeapi;
		$userid = $this->dbcon->query_unique_value("select userid from js_member where userno = '{$userno}' ");
		$sql = "update js_member set level_code = '{$levelcode}' where userno = '{$userno}' ";
		$r = $this->dbcon->query($sql);
		if($r) {
			// 잔액 지급이나 보너스 지급은 입금신청관리페이지(환전신청관리 > USD 포인트 입금관리)에서 일괄 처리합니다.
			// if($levelcode=='BW38') { // 정회원일때 (300만원을->100만원) js_wallet_wallet 에 + 해 준다.(2019-10-28-오팀장) 회원등급을 또 변경 할때(정회원->일반회원->정회원)는 최초 한번만 적용 한다.
			// 	if($level_change_cnt==0) { // 최초 한번만 적용 한다.
			// 		$sql = "update js_wallet_wallet set balance = balance + 1000000  where symbol='USD' AND name = '{$userid}' ";
			// 		$q = $this->dbcon->query($sql);
			// 	}
			// }
			$qry = "update js_member set level_change_cnt = level_change_cnt + 1  where userid = '{$userid}' ";
			$this->dbcon->query($qry);
		}
		return $r;
	}

}

function memberQuery($arr)
{
	$qry = array();
	if(!empty($arr['userid']))  { $qry[] = 'userid=\''.$GLOBALS['dbcon']->escape($arr['userid']).'\''; }
	if(!empty($arr['userpw']))  { $qry[] = 'userpw=\''.$GLOBALS['dbcon']->escape($arr['userpw']).'\''; }
	if(isset($arr['name']) )  { $qry[] = 'name=\''.$GLOBALS['dbcon']->escape($arr['name']).'\''; }
	if(isset($arr['nickname']))  { $qry[] = 'nickname=\''.$GLOBALS['dbcon']->escape($arr['nickname']).'\''; }
	if(isset($arr['sid_a']))  { $qry[] = 'sid_a=\''.$GLOBALS['dbcon']->escape($arr['sid_a']).'\''; }
	if(isset($arr['sid_b']))  { $qry[] = 'sid_b=\''.$GLOBALS['dbcon']->escape($arr['sid_b']).'\''; }
	if(isset($arr['phone']))  { $qry[] = 'phone=\''.$GLOBALS['dbcon']->escape($arr['phone']).'\''; }
	if(isset($arr['mobile']))  { $qry[] = 'mobile=\''.$GLOBALS['dbcon']->escape($arr['mobile']).'\''; }
	// if(isset($arr['mobile_country_code']))  { $qry[] = 'mobile_country_code=\''.$GLOBALS['dbcon']->escape($arr['mobile_country_code']).'\''; }
	if(isset($arr['email']))  { $qry[] = 'email=\''.$GLOBALS['dbcon']->escape($arr['email']).'\''; }
	if(isset($arr['zipcode']))  { $qry[] = 'zipcode=\''.$GLOBALS['dbcon']->escape($arr['zipcode']).'\''; }
	if(isset($arr['address_a']))  { $qry[] = 'address_a=\''.$GLOBALS['dbcon']->escape($arr['address_a']).'\''; }
	if(isset($arr['address_b']))  { $qry[] = 'address_b=\''.$GLOBALS['dbcon']->escape($arr['address_b']).'\''; }
	if(isset($arr['bool_email']))  { $qry[] = 'bool_email=\''.$GLOBALS['dbcon']->escape($arr['bool_email']).'\''; }
	if(isset($arr['bool_sms']))  { $qry[] = 'bool_sms=\''.$GLOBALS['dbcon']->escape($arr['bool_sms']).'\''; }
	if(isset($arr['bool_lunar']))  { $qry[] = 'bool_lunar=\''.$GLOBALS['dbcon']->escape($arr['bool_lunar']).'\''; }
	if(isset($arr['birthday']))  { $qry[] = 'birthday=\''.$GLOBALS['dbcon']->escape($arr['birthday']).'\''; }
	if(!empty($arr['level_code']))  { $qry[] = 'level_code=\''.$GLOBALS['dbcon']->escape($arr['level_code']).'\''; }
	if(!empty($arr['recomid']))  { $qry[] = 'recomid=\''.$GLOBALS['dbcon']->escape($arr['recomid']).'\''; }
	if(!empty($arr['center_code']))  { $qry[] = 'center_code=\''.$GLOBALS['dbcon']->escape($arr['center_code']).'\''; }
	if(!empty($arr['deposit_amount']))  { $qry[] = 'deposit_amount=\''.$GLOBALS['dbcon']->escape($arr['deposit_amount']).'\''; }
	if(!empty($arr['introduce']))  { $qry[] = 'introduce=\''.$GLOBALS['dbcon']->escape($arr['introduce']).'\''; }
	if(!empty($arr['hit']))  { $qry[] = 'hit=\''.$GLOBALS['dbcon']->escape($arr['hit']).'\''; }

	//if(isset($arr['bank_name']))  { $qry[] = 'bank_name=\''.$GLOBALS['dbcon']->escape($arr['bank_name']).'\''; }
	//if(isset($arr['bank_account']))  { $qry[] = 'bank_account=\''.$GLOBALS['dbcon']->escape($arr['bank_account']).'\''; }
	//if(isset($arr['bank_owner']))  { $qry[] = 'bank_owner=\''.$GLOBALS['dbcon']->escape($arr['bank_owner']).'\''; }

	// if(isset($arr['bool_confirm_email']))  { $qry[] = 'bool_confirm_email=\''.$GLOBALS['dbcon']->escape($arr['bool_confirm_email']).'\''; }
	$qry[] = 'bool_confirm_email=1'; // allow join without confirm email check

	if(isset($arr['bool_confirm_mobile']))  { $qry[] = 'bool_confirm_mobile=\''.$GLOBALS['dbcon']->escape($arr['bool_confirm_mobile']).'\''; }
	if(isset($arr['bool_email_krw_input']))  { $qry[] = 'bool_email_krw_input=\''.$GLOBALS['dbcon']->escape($arr['bool_email_krw_input']).'\''; }
	if(isset($arr['bool_sms_krw_input']))  { $qry[] = 'bool_sms_krw_input=\''.$GLOBALS['dbcon']->escape($arr['bool_sms_krw_input']).'\''; }
	if(isset($arr['bool_email_krw_output']))  { $qry[] = 'bool_email_krw_output=\''.$GLOBALS['dbcon']->escape($arr['bool_email_krw_output']).'\''; }
	if(isset($arr['bool_sms_krw_output']))  { $qry[] = 'bool_sms_krw_output=\''.$GLOBALS['dbcon']->escape($arr['bool_sms_krw_output']).'\''; }
	if(isset($arr['bool_email_btc_trade']))  { $qry[] = 'bool_email_btc_trade=\''.$GLOBALS['dbcon']->escape($arr['bool_email_btc_trade']).'\''; }
	if(isset($arr['bool_email_btc_input']))  { $qry[] = 'bool_email_btc_input=\''.$GLOBALS['dbcon']->escape($arr['bool_email_btc_input']).'\''; }
	if(isset($arr['bool_email_btc_output']))  { $qry[] = 'bool_email_btc_output=\''.$GLOBALS['dbcon']->escape($arr['bool_email_btc_output']).'\''; }

	if(isset($arr['image_identify_url']))  { $qry[] = 'image_identify_url=\''.$GLOBALS['dbcon']->escape($arr['image_identify_url']).'\''; }
	if(isset($arr['image_mix_url']))  { $qry[] = 'image_mix_url=\''.$GLOBALS['dbcon']->escape($arr['image_mix_url']).'\''; }

	if($_POST['pg_mode'] == 'write') {
		$qry[] = 'regdate=UNIX_TIMESTAMP()';
	}
	else {
		if(isset($arr['regdate']))  { $qry[] = 'regdate=\''.$GLOBALS['dbcon']->escape($arr['regdate']).'\''; }
	}
	$qry = implode(',',$qry);
	return $qry;
}

function withdrawQuery($arr)
{
	$qry = array();
	if(!empty($_SESSION['USER_ID']))  { $qry[] = 'userid=\''.$GLOBALS['dbcon']->escape($_SESSION['USER_ID']).'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$GLOBALS['dbcon']->escape($arr['contents']).'\''; }
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	return implode(',',$qry);
}

function smsQuery($arr)
{
	$qry = array();
	if(!empty($arr['tran_phone']))  { $qry[] = 'tran_phone=\''.$GLOBALS['dbcon']->escape($arr['tran_phone']).'\''; }
	if(!empty($arr['tran_callback']))  { $qry[] = 'tran_callback=\''.$GLOBALS['dbcon']->escape($arr['tran_callback']).'\''; }
	if(!empty($arr['tran_date']))  { $qry[] = 'tran_date=\''.$GLOBALS['dbcon']->escape($arr['tran_date']).'\''; }
	if(!empty($arr['tran_msg']))  { $qry[] = 'tran_msg=\''.$GLOBALS['dbcon']->escape($arr['tran_msg']).'\''; }
	if(!empty($arr['tran_result']))  { $qry[] = 'tran_result=\''.$GLOBALS['dbcon']->escape($arr['tran_result']).'\''; }
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	return implode(',',$qry);
}
?>