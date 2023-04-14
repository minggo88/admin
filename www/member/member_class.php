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
		$query['fields'] = "userno,userid,userpw,name,nickname,phone,mobile,email,
			IF(bool_email>0,'수신함','수신안함') AS bool_email,
			IF(bool_sms>0,'수신함','수신안함') AS bool_sms,
			level_code,bool_confirm_email,bool_confirm_mobile,
			(select level_name from js_member_level where level_code='".$table_name.".level_code') AS level_name,regdate";
		$query['where'] = 'where 1 '.$this->srchQry().$sort;
		// exit($query['fields'] );
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
		$row['joindate'] = date('Y-m-d',$row['regdate']);
		// $row['total_emoney'] = empty($row['total_emoney']) ? 0 : $row['total_emoney'];
		// $row['total_order_amount'] = empty($row['total_order_amount']) ? 0 : $row['total_order_amount'];
		// $row['total_pay_amount'] = empty($row['total_pay_amount']) ? 0 : $row['total_pay_amount'];

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
		// var_dump($row); exit;
		$row['permission'] = $this->get_permission_code($row['bool_confirm_mobile'], $row['bool_confirm_idimage'], $row['bool_confirm_bank']);
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
		$query['fields'] = "userid,userno,
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
			bool_confirm_bank,
			bank_name,
			bank_account,
			bank_owner,
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
		$row['permission'] = $this->get_permission_code($row['bool_confirm_mobile'], $row['bool_confirm_idimage'], $row['bool_confirm_bank']);
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
		$row['mobile'] = $row['mobile'] ? str_replace('+82','',$row['mobile']) : '';
		// $row['mobile'] = strpos($row['userid'],'mobile82')!==false ? str_replace('mobile82', '0', $row['userid']): ''; // 아이디를 전화번호로 사용할때 쓰던 내용
		$row['mobile_origin'] = str_replace($codeTxt,"",$row['mobile']);
		$row['mobile_origin'] = str_replace(str_replace('+','',$codeTxt),'0',$row['mobile']);

		//$row['pin'] = str_replace('**',$row['pin']);
		$row['regdate'] = date('Y-m-d H:s:i',$row['regdate']);
		$this->tpl->assign($row);
		$this->tpl->assign('srch_url',$this->srchUrl());

		// 본인인증 정보 다시 확인하도록 로직 추가
		//$query = "select * from js_realname where userid='$userid' ";
		// var_dump($row);exit;

		// 신분증 반려 매시지
		$row['reject_idimage_msg'] = $this->get_member_meta($row['userno'], 'reject_idimage_msg');

		// 통장 반려 매시지
		$row['reject_bank_msg'] = $this->get_member_meta($row['userno'], 'reject_bank_msg');
		// var_dump($row); exit;


		$this->tpl->assign('realname_info',$row);

		return $row;
	}

	function get_permission_code ($bool_confirm_mobile=0, $bool_confirm_idimage=0, $bool_confirm_bank=0) {
		$p[] = $_SESSION['USER_NO']  ? '1' : '0';
		$p[] = $_SESSION['USER_NO']  ? '1' : '0';
		$p[] = $bool_confirm_mobile ? '1' : '0';
		$p[] = $bool_confirm_idimage ? '1' : '0';
		$p[] = $bool_confirm_bank ? '1' : '0';
		return implode('', $p);
	}

	function write()
	{
		global $config_basic, $btcService, $tradeapi;
		// exit($_POST['name']);
		// $_POST['username'] = str_ireplace('&nbsp;','',$_POST['username']);
		// if(trim($_POST['username']) == '') {
		// 	responseFail(Lang::main_member10-1);
		// }
		// $_POST['firstname'] = str_ireplace('&nbsp;','',$_POST['firstname']);
		// if(trim($_POST['firstname']) == '') {
		// 	responseFail(Lang::main_member10);
		// }
		// $_POST['lastname'] = str_ireplace('&nbsp;','',$_POST['lastname']);
		// if(trim($_POST['lastname']) == '') {
		// 	responseFail(Lang::main_member11);
		// }
		$_POST['name'] = str_ireplace('&nbsp;','',$_POST['name']);
		if(trim($_POST['name']) == '') {
			responseFail(Lang::main_member11);
		}
		$_POST['userid'] = str_ireplace('&nbsp;','',$_POST['userid']);
		if(trim($_POST['userid']) == '') {
			responseFail(Lang::main_member9);
		}
		if($_POST['userid'] == 'guest') {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_member1);
			}
			else {
				jsonMsg(0, Lang::main_member1);
			}
		}

		$_POST['email'] = str_ireplace('&nbsp;','',$_POST['email']);
		if($_COOKIE['lang']!='cn' && trim($_POST['email']) == '') {
			responseFail(Lang::main_member12);
		}
		// 이메일 인증 여부 값 
		$_POST['bool_email'] = $_POST['bool_email']=='1' ? '1' : '';


		if($_POST['userpw'] == '') {
			responseFail(Lang::main_member13);
		}

		// 아이디 중복 채크
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		$cnt_withdraw = $this->dbcon->query_unique_value("SELECT COUNT(*) FROM js_withdraw WHERE userid='".$GLOBALS['_POST_ESCAPE']['userid']."' ");
		if($cnt > 0 || $cnt_withdraw>0) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_member1);
			}
			else {
				jsonMsg(0, Lang::main_member1);
			}

		}

		// 추천인 확인
		$recomid = trim($_POST['recomid']);
		if($recomid) {
			$recomno = $this->dbcon->query_one("SELECT userno FROM js_member WHERE userid='{$this->dbcon->escape($recomid)}'");
			if(!$recomno) {
				responseFail('올바른 소개자(아이디)를 입력해주세요.');
			}
		}

		//회원레벨 코드를 가지고 온다.
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select_one';
		$query['fields'] = 'level_code';
		$query['where'] = 'where ranking < 100 order by ranking desc';
		$_POST['level_code'] = $this->dbcon->query($query,__FILE__,__LINE__);
		$query = array();
		$query['tool'] = 'insert_idx';
		$result = $this->bWrite($query,$_POST);
		if(!$result['result']) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_member3);
			}
			else {
				jsonMsg(0, Lang::main_member3);
			}
		}

		// 회원번호.
		$userno = $result['idx'];

		// 가입시 본인인증이 되었으면 본인인증 처리함.
		if(!empty($_SESSION['tmprealnameid']) && strpos($_SESSION['tmprealnameid'], 'tmp_')!==false) {
			$query = "select name from js_realname where userid='".$this->dbcon->escape($_SESSION['tmprealnameid'])."' ";
			$_name = $this->dbcon->query_unique_value($query);
			if(!empty($_name)){
				// [2014-07-31] 회원가입시 본인인증을 한 상태라면 작성된 이름이 아닌 본인인증된 이름을 사용하도록 변경함.
				$query = "update js_member set bool_confirm_mobile = 1, level_code='JB37', name='".$this->dbcon->escape($_name)."' where userid='".$this->dbcon->escape($_POST['userid'])."' ";
				$this->dbcon->query($query);

				$query = "update js_realname set userid='".$this->dbcon->escape($_POST['userid'])."' where userid='".$this->dbcon->escape($_SESSION['tmprealnameid'])."' ";
				$this->dbcon->query($query);
				$_SESSION['tmprealnameid'] = ''; // 처리후 임시값 삭제
			}
		}


		//가입확인메일 발송
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'userno, userid,name, mobile, email, bool_email, bool_sms';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		// 가입 확인 이메일 발송. - 이메일 수신여부와 상관없이 발송하도록 함.
		/*
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
		*/

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
			$address = '';// 느려서 여기서는 안하고 나중에 입금페이지에서 자동생성함. $tradeapi->create_wallet($userno, $tradeapi->default_exchange); // 이것도 은행 연동해서 느려지면 수동으로 변경하기.
			$tradeapi->save_wallet($userno, $tradeapi->default_exchange, $address);
			// $address = $tradeapi->create_wallet($userno, 'SCC'); // 작동은 되지만 시간이 오래걸려서 수동으로 생성하도록 여기서는 제외시킴.
			// $tradeapi->save_wallet($userno, 'SCC', $address);
		}
		// btc 지급 초기화
		// if(!empty($btcService)){
		// 	$address = $btcService->bitcoind->getnewaddress($userid);
		// 	$btcService->btcTradeCriterionDao->setWallet($_POST['userid'], $address);
		// }

		if($config_basic['bool_ssl'] > 0) {
			replaceGo('//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?pg_mode=join_ok&userid='.$_POST['userid']);
		}
		else {
			jsonMsg(1,$_POST['userid']);
			// jsonMsg(1, 'Email was sent for verification. Please check your email and verify.'); // 이메일 인증이 필요할때 . Email was sent for verification. Please check your email and verify.
			// jsonMsg(1, 'Sign up is complete. Please login.'); // 이메일 인증이 필요 없을때. Sign up is complete. Please login.
		}
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
		$query = "update js_member set bool_confirm_email=1 where userid='".$this->dbcon->escape($p_userid)."' ";
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
		if(empty($bank_name)){
			jsonMsg(0,Lang::main_member4);
		}
		if(empty($bank_account)){
			jsonMsg(0,Lang::main_member5);
		}
		if(empty($bank_owner)){
			jsonMsg(0,Lang::main_member6);
		}
		include ROOT_DIR.'/cheditor/imageUpload/s3.php';
		$S3 = new S3();
        // 은행통장사본
        if($image_bank_url) {
			// s3 임시파일을 정식 파일로 이동
			if(strpos($image_bank_url, '.s3.')!==false && strpos($image_bank_url, '/tmp/')!==false) {
				$image_bank_url_new = $S3->copy_tmpfile_to_s3($image_bank_url);
				$S3->delete_file_to_s3($image_bank_url);
				$image_bank_url = $image_bank_url_new;
			}
			$sql_image_bank = ", image_bank_url='{$this->dbcon->escape($image_bank_url)}', bool_confirm_bank=0 ";
        }
		$query = "update js_member set bank_name='".$this->dbcon->escape($bank_name)."', bank_account='".$this->dbcon->escape($bank_account)."', bank_owner='".$this->dbcon->escape($bank_owner)."' {$sql_image_bank} where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' ";
		if($this->dbcon->query($query)) {
			// 은행통장사본
			if($image_bank_url) {
				$_member_info = $this->get_member_info($_SESSION['USER_NO']);
				if($_member_info->image_bank_url && $_member_info->image_bank_url != $image_bank_url) {
					$S3->delete_file_to_s3($_member_info->image_bank_url);
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
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_member9);
			}
			else {
				jsonMsg(0,Lang::main_member9);
			}
		}
		// if(empty($_POST['securimagecode'])) {
		// 	if($this->tpl->skin != 'admin') {  // 2014-07-09 : 고객요청사항 - 관리자는 패스할께요 Danny
		// 		if($config_basic['bool_ssl'] > 0) {
		// 			errMsg('그림인증문자를 입력해주세요.');
		// 		}
		// 		else {
		// 			jsonMsg(0,'그림인증문자를 입력해주세요.');
		// 		}
		// 	}
		// }
		$query = "select captcha_string from fusion_captcha where captcha_ip='" . $this->dbcon->escape($_COOKIE['token']) . "' ";
		$code = strtoupper($this->dbcon->query_unique_value($query));

		if($this->tpl->skin != 'admin') {  // 2014-07-09 : 고객요청사항 - 관리자는 패스할께요 Danny
			if( $code != strtoupper($_POST['securimagecode']) ) {
				if($config_basic['bool_ssl'] > 0) {
					errMsg(Lang::main_ledger4);
				}
				else {
					jsonMsg(0, Lang::main_ledger4);
				}
			}
		}
		if(empty($_POST['bool_passwd'])) {
			$_POST['userpw'] = '';
		}

		if(isset($_POST['mobile'])){
			$_POST['mobile'] = '+'.$_POST['mobile']; // DB에서 불러온 다음 + 를 지워 버리더라구요, 그래서 추가
		}

		// 이미지 복사 및 이전파일 삭제 - tmp 에 있는건 자동으로 삭제 할겁니다. 그래서 사용하는건 tmp에서 복사해야합니다.

		include ROOT_DIR.'/cheditor/imageUpload/s3.php';
		$S3 = new S3();

		// 신분증
		if($_POST['image_identify_url_new']!="" && $_POST['image_identify_url_new']!=$_POST['image_identify_url_old']) {
			$_r = $S3->copy_tmpfile_to_s3($_POST['image_identify_url_new']);
			if($_r) {
				$_POST['image_identify_url'] = $_r;
				if($_POST['image_identify_url_old']!="") {
					$S3->delete_file_to_s3($_POST['image_identify_url_old']);
				}
			}
		} else {
		    unset($_POST['image_identify_url']);
		}
		// 신분증 + 사용자
		if($_POST['image_mix_url_new']!="" && $_POST['image_mix_url_new']!=$_POST['image_mix_url_old']) {
			$_r = $S3->copy_tmpfile_to_s3($_POST['image_mix_url_new']);
			if($_r) {
				$_POST['image_mix_url'] = $_r;
				if($_POST['image_mix_url_old']!=""){
					$S3->delete_file_to_s3($_POST['image_mix_url_old']);
				}
			}
		} else {
            unset($_POST['image_mix_url']);
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
				errMsg(Lang::main_ledger5);
			}
			else {
				jsonMsg(0);
			}
		}
	}

    // 18.10.18 Brad add
    function edit_photo()
    {
        global $config_basic, $tradeapi;
        if(empty($_POST['userid'])) {
            if($config_basic['bool_ssl'] > 0) {
                errMsg(Lang::main_member9);
            }
            else {
                jsonMsg(0,Lang::main_member9);
            }
        }

        if(empty($_POST['bool_passwd'])) {
            $_POST['userpw'] = '';
        }

        // 이미지 복사 및 이전파일 삭제 - tmp 에 있는건 자동으로 삭제 할겁니다. 그래서 사용하는건 tmp에서 복사해야합니다.

		include ROOT_DIR.'/cheditor/imageUpload/s3.php';
		$S3 = new S3();

        // 신분증
        if($_POST['image_identify_url_new']!="" && $_POST['image_identify_url_new']!=$_POST['image_identify_url_old']) {
            $_r = $S3->copy_tmpfile_to_s3($_POST['image_identify_url_new']);
            if($_r) {
                $_POST['image_identify_url'] = $_r;
                if($_POST['image_identify_url_old']!="") {
                    $S3->delete_file_to_s3($_POST['image_identify_url_old']);
                }
			}
			$_POST['bool_confirm_idimage'] = '0'; // 수정시 신분증이미지 인증상태를 초기화합니다.
        } else {
            unset($_POST['image_identify_url']);
        }
        // 신분증 + 사용자
        if($_POST['image_mix_url_new']!="" && $_POST['image_mix_url_new']!=$_POST['image_mix_url_old']) {
            $_r = $S3->copy_tmpfile_to_s3($_POST['image_mix_url_new']);
            if($_r) {
                $_POST['image_mix_url'] = $_r;
                if($_POST['image_mix_url_old']!=""){
                    $S3->delete_file_to_s3($_POST['image_mix_url_old']);
                }
            }
			$_POST['bool_confirm_idimage'] = '0'; // 수정시 신분증이미지 인증상태를 초기화합니다.
        } else {
            unset($_POST['image_mix_url']);
        }

        // 은행통장사본
        if($_POST['image_bank_url_new']!="" && $_POST['image_bank_url_new']!=$_POST['image_bank_url_old']) {
            $_r = $S3->copy_tmpfile_to_s3($_POST['image_bank_url_new']);
            if($_r) {
                $_POST['image_bank_url'] = $_r;
                if($_POST['image_bank_url_old']!="") {
                    $S3->delete_file_to_s3($_POST['image_bank_url_old']);
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
                errMsg(Lang::main_ledger5);
            }
            else {
                jsonMsg(0);
            }
        }
    }

	function edit_pin()
	{
		global $config_basic;
		if(empty($_SESSION['USER_ID'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_ledger6);
			}
			else {
				jsonMsg(0,Lang::main_ledger6);
			}
		}

		if(empty($_POST['pin'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_ledger7);
			}
			else {
				jsonMsg(0,Lang::main_ledger7);
			}
		}

		if(strlen($_POST['pin'])>6 || strlen($_POST['pin'])<4 || preg_match('/\D/', $_POST['pin'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_ledger8);
			}
			else {
				jsonMsg(0,Lang::main_ledger8);
			}
		}

		$query = "update js_member set pin='".md5($_POST['pin'])."' where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."'";
		if($this->dbcon->query($query)) {
			if($config_basic['bool_ssl'] > 0) {
				replaceGo('/edit_pin');
			}
			else {
				jsonMsg(1);
			}
		}
		else {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_ledger5);
			}
			else {
				jsonMsg(0);
			}
		}
	}

	function confirmRealname($p_confirm_number='') {

		if(empty($_GET['userid'])) {
			jsonMsg(0, Lang::main_ledger11);
		}
		foreach ($_GET['userid'] as $key => $val) {
			$query = "SELECT * FROM js_member WHERE userid='{$this->dbcon->escape($val)}'";
			$m = $this->dbcon->query_unique_object($query);

			$query = "UPDATE js_member SET bool_confirm_mobile=1, level_code='JB37' WHERE userid='{$this->dbcon->escape($val)}'";
			$result = $this->dbcon->query($query);
			$query = "INSERT INTO js_realname SET userid='{$m->userid}', ciphertime='".time()."', requestnumber='SEQ_{$m->userid}', authtype='관리자수동인증', NAME='{$m->name}' on duplicate key update ciphertime='".time()."', requestnumber='SEQ_{$m->userid}', authtype='관리자수동인증', NAME='{$m->name}' ";
			$this->dbcon->query($query);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1, Lang::main_ledger12);

	}

	/**
	 * send confirm number to email
	 */
	function send_confirm_email($email) {
		if(empty($email)){
			jsonMsg(0,Lang::main_ledger9);
		}
		// 회원가입 이메일 인증시도
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$this->dbcon->escape($email).'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt != 0) {
				jsonMsg(0,'err_duplicate');
		} else {
			$tmpnum = mt_rand(111111, 999999);
			// 회원가입 인증
			$query = "INSERT INTO js_member_verify SET email='{$email}', confirm_number='{$tmpnum}', created_at=NOW() on duplicate key update confirm_number='{$tmpnum}', updated_at=NOW() ";
			if($this->dbcon->query($query)) {
				// 메일 보내기
				include_once ROOT_DIR.'/admin/email_class.php';
				$mail = new ShopEmail($this->tpl);
				$mail->dbcon = &$this->dbcon;
				$mail->json = &$this->json;

				// $arr = array();
				// $arr['mail_to'] = $email;
				// $arr['mail_subject'] = '['.$mail->config['config_basic']['shop_name'].'] 회원가입 이메일 인증 메일입니다';
				// $arr['mail_to_name'] = $email;
				// $this->tpl->assign('email',$email);

				// $mail_body = $this->tpl->fetch('mail_contents');
				// $arr['mail_body'] = $mail_body;
				// $mail->sendAutoMail($arr,'join');

				$mail->getConfigMail('confirm');
				$config_mail = $mail->config['config_mail'];

				$arr = array();
				$arr['mail_to'] = $email;
				$arr['mail_subject'] = str_replace('{UserName}', $email, Lang::main_basic27);
				$arr['mail_to_name'] = $email;

				$this->tpl->assign('userpw',$tmpnum);
				$this->tpl->assign('userid',$email);
				$this->tpl->assign('name',$email);
				$this->tpl->assign('tmpnum',$tmpnum);
				$this->tpl->assign('from_name',$config_mail['from_name']);
				$this->tpl->assign('lang',$_SESSION['lang']); // 현재 언어.
				$this->tpl->assign('email_sign', str_replace('{name}', Lang('main_sitename'), Lang('main_email_sign')));
				for ($i=15; $i<=27; $i++) {
					$this->tpl->assign("main_basic{$i}", Lang("main_basic{$i}") ); // 언어 설정 값이 없을때 Lang::main_basic15 처럼 호출하면 PHP 애러발생. 함수형식으로 변경함.
				}
				$this->tpl->assign("main_member76", Lang("main_member76") );
				$mail_body = $this->tpl->fetch('mail_contents');
				$arr['mail_body'] = $mail_body;

				if($mail->sendAutoMail($arr,'confirm')) {
					jsonMsg(1,'email');
				} else {
					jsonMsg(0,'err_mail');
				}
				// jsonMsg(1,'email');

			} else {
				jsonMsg(0,'err_db');
			}
		}
	}

	function check_confirm_number($email,$confirm_number) {
		if(empty($email)){
			jsonMsg(0,Lang::main_ledger9);
		}
		$query = array();
		$query['table_name'] = 'js_member_verify';
		$query['tool'] = 'row';
		$query['fields'] = 'name,confirm_number';
		$query['where'] = 'where email=\''.$this->dbcon->escape($email).'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		if($confirm_number == $row['confirm_number']) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0,'err_confirm_number');
		}
	}

	/**
	 * send confirm number to mobile phone
	 */
	function send_confirm_number($p_phone_number, $mobile_country_code) {
		$_phone_number = preg_replace('/[^0-9\+]/', '', $p_phone_number);
		if(empty($_phone_number)){
			jsonMsg(0,Lang::main_ledger9);
		}
		// 휴대폰 번호 중복체크 로직 18.10.24 Brad
		$query = array();
        $query['table_name'] = $this->config['table_name'];
        $query['tool'] = 'count';
        $query['where'] = 'where userid!=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\' and mobile=\''.$this->dbcon->escape($p_phone_number).'\'';
        $cnt = $this->dbcon->query($query,__FILE__,__LINE__);
        if($cnt != 0) {
            jsonMsg(0,'err_duplicate');
        } else {
            $tmpnum = mt_rand(111111, 999999);
            $query = "update js_member set confirm_number='$tmpnum', confirm_mobile_number='{$this->dbcon->escape($p_phone_number)}', mobile_country_code='{$this->dbcon->escape($mobile_country_code)}' where userid='".$this->dbcon->escape($_SESSION['USER_ID'])."' "; // 인증 단계에서는 이전 인증받은 전화번호를 지우지 않습니다. 인증이 완료되면 새로운 번호로 바꿉니다.
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
			jsonMsg(0,Lang::main_ledger14);
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
			jsonMsg(0,Lang::main_ledger14);
		}
	}

	function _write($arr)
	{
		//[2014-06-23 benant] phone, mobile 사용하지 않음. 아래 주석은 번호를 3개로 나눠서 받을때 사용함.
//		@ $arr['phone'] = $arr['phone_a'].'-'.$arr['phone_b'].'-'.$arr['phone_c'];
		//[2014-07-31 benant] mobile 이 주석이 풀려서인지 회원정보 수정할때 값을 넘기지 않는데 mobile이 빈값으로 설정되면서 기존 값을 지워버림. 문제해결을 위해 주석처리하려다가 혹시 다른곳에서는 사용할수도 있어서 그냥 수정할때는 mobile이 설정되지 않도록 함.
		if($_POST['pg_mode']!='edit' && isset($arr['mobile_a'])){
			$arr['mobile'] = $arr['mobile_a'].$arr['mobile_b'].$arr['mobile_c'];
		}
		//@ $arr['email'] = $arr['email_a'].'@'.$arr['email_b'];
		if(!empty($arr['userpw'])) {
			$arr['userpw'] = md5($arr['userpw']);
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
		// 로그인 및 회원정보 확인
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

		// 잔액 확인 (매매 가능한 잔액이 있는지 ... )
		$userno = $_SESSION['USER_NO'];
		$stocks = $this->dbcon->query_list_object_column("SELECT * FROM js_trade_currency ", 'symbol');
		$wallets = $this->dbcon->query_list_object("SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($userno)}' ");
		foreach($wallets as $w) {
			$stock = $stocks[$w->symbol];
			// 매매 가능한 잔액이 있는지 확인 - 있으면 탈퇴 불가
			if($stock->tradable=='Y' && $w->confirmed >= $stock->trade_min_volume) {
				jsonMsg(0,'매매 가능한 주식이 확인되어 회원 탈퇴처리를 하지 못했습니다.');
			}
			// 출금 가능한 잔액이 있는지 확인 - 있으면 탈퇴 불가
			if($w->symbol=='KRW' && $w->confirmed >= $stock->out_min_volume) {
				jsonMsg(0,'출금 가능한 기업페이가 확인되어 회원 탈퇴처리를 하지 못했습니다.');
			}
		}

		// 탈퇴 처리
		$query = array();
		$query['table_name'] = 'js_withdraw';
		$query['tool'] = 'insert';
		$query['fields'] = withdrawQuery($_POST);
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		$query = 'INSERT INTO js_member_withdraw
			(userno, userid,userpw,name,nickname,sid_a,sid_b,phone,mobile,email,zipcode,address_a,address_b,bool_email,bool_sms,bool_lunar,birthday,level_code,regdate)
			SELECT userno, userid,userpw,name,nickname,sid_a,sid_b,phone,mobile,email,zipcode,address_a,address_b,bool_email,bool_sms,bool_lunar,birthday,level_code,regdate
			FROM js_member WHERE userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'delete';
		$query['where'] = 'where userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}

		// 본인 인증 정보도 삭제 2014-08-11 -
		// 본인인증 뿐만 아니라 회원거래내역 및 bitcoin 계좌 정보까지 모두 삭제해야 할것으로 보임.
		// 아니면 사용한 아이디로는 다시는 사용못하도록 해야 함.
		$query = 'delete from js_realname where userid=\''.$this->dbcon->escape($_SESSION['USER_ID']).'\'';
		@$this->dbcon->query($query);

		// 회원잔액 사이트 관리자 지갑으로 이동
		foreach($wallets as $w) {
			if($w->confirmed > 0) {

				$m_wallet = $this->dbcon->query_fetch_object("SELECT * FROM js_exchange_wallet WHERE userno='2' AND symbol='{$this->dbcon->escape($w->symbol)}' "); // walletmanager

				// insert balance to manager wallet
				$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed+{$w->confirmed} WHERE userno='{$m_wallet->userno}' AND symbol='{$this->dbcon->escape($w->symbol)}' ");
				$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET userno='{$m_wallet->userno}', symbol='{$this->dbcon->escape($w->symbol)}', address='{$this->dbcon->escape($m_wallet->address)}', regdate=NOW(), txndate=NOW(), address_relative='{$this->dbcon->escape($w->address)}', txn_type='S', direction='I', amount='$w->confirmed', fee=0, fee_relative='', tax=0, status='D', key_relative='', msg='회원 탈퇴 신정으로 잔액을 관리자에게 이동'  ");

				// remove balance from user wallet
				$this->dbcon->query("UPDATE js_exchange_wallet SET confirmed=confirmed-{$w->confirmed} WHERE userno='{$w->userno}' AND symbol='{$this->dbcon->escape($w->symbol)}' ");
				$this->dbcon->query("INSERT INTO js_exchange_wallet_txn SET userno='{$w->userno}', symbol='{$this->dbcon->escape($w->symbol)}', address='{$this->dbcon->escape($w->address)}', regdate=NOW(), txndate=NOW(), address_relative='{$this->dbcon->escape($m_wallet->address)}', txn_type='S', direction='O', amount='$w->confirmed', fee=0, fee_relative='', tax=0, status='D', key_relative='', msg='회원 탈퇴 신정으로 잔액을 관리자에게 이동'  ");

			}
		}



		unset($_SESSION['USER_ID']);
		unset($_SESSION['USER_NAME']);
		unset($_SESSION['USER_LEVEL']);
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
		$mobile = $GLOBALS['_POST_ESCAPE']['mobile_a'].$GLOBALS['_POST_ESCAPE']['mobile_b'].$GLOBALS['_POST_ESCAPE']['mobile_c'];
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where name=\''.$GLOBALS['_POST_ESCAPE']['name'].'\' && mobile=\''.$mobile.'\' ';
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
			jsonMsg(0, ('main_member13'));
		}
		if(strlen($_POST['userpw'])<8) {
			jsonMsg(0, ('main_member17'));
		}
		$token = $_POST['token'];
		$sql = "select userno from js_member_meta where name='tmp_pw' and value='{$GLOBALS['_POST_ESCAPE']['token']}' ";
		$userno = $this->dbcon->query_unique_value($sql);
		if(! $userno) {
			jsonMsg(0, '사용할 수 없는 토큰입니다.');
		}
		$newpw = md5($_POST['userpw']);
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
		// $query['fields'] = 'userpw=\''.md5($tmp_pw).'\'';
		// $query['where'] = 'where email=\''.$email.'\'';
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
		$_GET['sort_target'] = array('m.regdate');
		$_GET['sort_method'] = array('desc');
		if($_REQUEST['order']) {
			$i=0;
			foreach($_REQUEST['order'] as $order) {
				$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
				$_GET['sort_method'][$i] = $order['dir'];
				$i++;
			}
		}
		// $_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
		// $_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
		$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
		$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
		$this->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $this->config['loop_scale'];
		$this->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;


		if (empty($_GET['sort_method'])) {
			$sort_method = 'desc';
		}
		else {
			$sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
		}

		// 검색
		if($_POST['userid']) { $_GET['userid'] = $_POST['userid']; $GLOBALS['_GET_ESCAPE']['userid'] = $GLOBALS['_POST_ESCAPE']['userid']; }
		if($_POST['name']) { $_GET['name'] = $_POST['name']; $GLOBALS['_GET_ESCAPE']['name'] = $GLOBALS['_POST_ESCAPE']['name']; }
		if($_POST['email']) { $_GET['email'] = $_POST['email']; $GLOBALS['_GET_ESCAPE']['email'] = $GLOBALS['_POST_ESCAPE']['email']; }
		if($_POST['mobile']) { $_GET['mobile'] = $_POST['mobile']; $GLOBALS['_GET_ESCAPE']['mobile'] = $GLOBALS['_POST_ESCAPE']['mobile']; }
		if($_POST['nickname']) { $_GET['nickname'] = $_POST['nickname']; $GLOBALS['_GET_ESCAPE']['nickname'] = $GLOBALS['_POST_ESCAPE']['nickname']; }
		if($_POST['start_date']) { $_GET['start_date'] = $_POST['start_date']; $GLOBALS['_GET_ESCAPE']['start_date'] = $GLOBALS['_POST_ESCAPE']['start_date']; }
		if($_POST['end_date']) { $_GET['end_date'] = $_POST['end_date']; $GLOBALS['_GET_ESCAPE']['end_date'] = $GLOBALS['_POST_ESCAPE']['end_date']; }
		$qry = $this->srchQry_member();

		$table_name = 'js_member as m 
			left join js_exchange_wallet as krw on m.userno = krw.userno and krw.symbol="KRW"';
		$fields = 'm.userno, m.email, m.userid, m.name, m.nickname, m.mobile,
			ifnull(krw.confirmed, 0) as krw, 
			m.regdate, m.passwd_default, recomid, ifnull((select name from js_member where userid=m.recomid),"") recomname';
		$where = ' where 1 '.$qry;
		$query = array();
		$query['table_name'] = $table_name;
		$query['fields'] = $fields;
		$query['where'] = $where;

		$query['tool'] = 'count';
		$total = $this->dbcon->query($query,__FILE__,__LINE__);

		$sort = ' ORDER BY ';
		if($_GET['sort_target'] ) {
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$sort .= ', ';
				}
				$sort .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$sort .= 'm.regdate DESC';
		}
		$limit = ' limit '.$_GET['start'].', '.$this->config['loop_scale'];
		$query['tool'] = 'select';
		$query['where'] .= $sort.$limit;
		// var_dump($query); exit;
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$row['list_cnt'] = $i;
			$row['no'] = $total - $_GET['start'] - $row['list_cnt'];

			$row['regdate'] = date('Y-m-d H:i:s', $row['regdate']);
			$row['mobile'] = str_replace('8201','821',  str_replace('+82', '82', $row['mobile']));
			$row['etc'] = '';
			$loop[] = $row;
			$i++;
        }
		exit(json_encode(array('data'=>$loop,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
	}

    function loopGetcustomersbalance($mode='tpl') {
        $_GET['sort_target'] = array('jm.userno');
        $_GET['sort_method'] = array('desc');
        if($_REQUEST['order']) {
            $i=0;
            foreach($_REQUEST['order'] as $order) {
                $_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
                $_GET['sort_method'][$i] = $order['dir'];
                $i++;
            }
        }
        // $_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
        // $_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
        $_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
        $page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
        $this->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $this->config['loop_scale'];
        $this->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;


        if (empty($_GET['sort_method'])) {
            $sort_method = 'desc';
        }
        else {
            $sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
        }

        // 검색
        if($_POST['userid']) { $_GET['userid'] = $_POST['userid']; $GLOBALS['_GET_ESCAPE']['userid'] = $GLOBALS['_POST_ESCAPE']['userid']; }
        if($_POST['name']) { $_GET['name'] = $_POST['name']; $GLOBALS['_GET_ESCAPE']['name'] = $GLOBALS['_POST_ESCAPE']['name']; }
        if($_POST['symbol_name']) { $_GET['symbol_name'] = $_POST['symbol_name']; $GLOBALS['_GET_ESCAPE']['symbol_name'] = $GLOBALS['_POST_ESCAPE']['symbol_name']; }
        if($_POST['goods_grade']) { $_GET['goods_grade'] = $_POST['goods_grade']; $GLOBALS['_GET_ESCAPE']['goods_grade'] = $GLOBALS['_POST_ESCAPE']['goods_grade']; }

        $qry = $this->srchQry_member_balance();

        $table_name = " js_exchange_wallet as jew left join js_member jm on jew.userno = jm.userno left join js_trade_currency jtc on jew.symbol = jtc.symbol ";
        $fields = " jm.userno, jm.userid, jm.name, jew.symbol, jtc.name as symbol_name, jew.goods_grade, jew.confirmed, jew.address";
        $where = " where 1 and jm.userno !=2 and jew.symbol not in ('AAT', 'NFTN', 'USD', 'ETH') and jm.userno is not null ". $qry;

        $query = array();
        $query['table_name'] = $table_name;
        $query['fields'] = $fields;
        $query['where'] = $where;

        $query['tool'] = 'count';

        $total = $this->dbcon->query($query,__FILE__,__LINE__);

        $sort = ' ORDER BY ';
        if($_GET['sort_target'] ) {
            for($i=0;$i<count($_GET['sort_target']);$i++) {
                if($i>0) {
                    $sort .= ', ';
                }
                $sort .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
            }
        } else {
            $sort .= 'jm.userno DESC';
        }
        $limit = ' limit '.$_GET['start'].', '.$this->config['loop_scale'];

        $query['tool'] = 'select';
        $query['where'] .= $sort.$limit;
        // var_dump($query); exit;
        $result = $this->dbcon->query($query,__FILE__,__LINE__);
        $loop = array();
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $row['list_cnt'] = $i;
            $row['no'] = $total - $_GET['start'] - $row['list_cnt'];

            $loop[] = $row;
            $i++;
        }
        exit(json_encode(array('data'=>$loop,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
    }

	function loopGetwithdraw($mode='tpl')
	{
		$_GET['sort_target'] = array('m.regdate');
		$_GET['sort_method'] = array('desc');
		if($_REQUEST['order']) {
			$i=0;
			foreach($_REQUEST['order'] as $order) {
				$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
				$_GET['sort_method'][$i] = $order['dir'];
				$i++;
			}
		}
		// $_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
		// $_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
		$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
		$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
		$this->config['loop_scale'] = $_REQUEST['length'] ? $_REQUEST['length']*1 : $this->config['loop_scale'];
		$this->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;


		if (empty($_GET['sort_method'])) {
			$sort_method = 'desc';
		}
		else {
			$sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
		}

		// 검색
		if($_POST['userid']) { $_GET['userid'] = $_POST['userid']; $GLOBALS['_GET_ESCAPE']['userid'] = $GLOBALS['_POST_ESCAPE']['userid']; }
		if($_POST['name']) { $_GET['name'] = $_POST['name']; $GLOBALS['_GET_ESCAPE']['name'] = $GLOBALS['_POST_ESCAPE']['name']; }
		if($_POST['email']) { $_GET['email'] = $_POST['email']; $GLOBALS['_GET_ESCAPE']['email'] = $GLOBALS['_POST_ESCAPE']['email']; }
		if($_POST['mobile']) { $_GET['mobile'] = $_POST['mobile']; $GLOBALS['_GET_ESCAPE']['mobile'] = $GLOBALS['_POST_ESCAPE']['mobile']; }
		if($_POST['start_date']) { $_GET['start_date'] = $_POST['start_date']; $GLOBALS['_GET_ESCAPE']['start_date'] = $GLOBALS['_POST_ESCAPE']['start_date']; }
		if($_POST['end_date']) { $_GET['end_date'] = $_POST['end_date']; $GLOBALS['_GET_ESCAPE']['end_date'] = $GLOBALS['_POST_ESCAPE']['end_date']; }
		$qry = $this->srchQry_member();

		$table_name = 'js_withdraw as w
			left join js_member_withdraw as m on w.userid = m.userid
			left join js_exchange_wallet as krw on m.userno = krw.userno and krw.symbol="KRW"';
		$fields = 'w.userid, w.contents, w.regdate, m.userno, m.name, m.mobile, m.email, m.regdate as joindate, ifnull(krw.confirmed, 0) as krw ';
		$where = ' where 1 AND m.mobile not like "%_w" AND m.name<>""  '.$qry;
		$query = array();
		$query['table_name'] = $table_name;
		$query['fields'] = $fields;
		$query['where'] = $where;

		$query['tool'] = 'count';
		$total = $this->dbcon->query($query,__FILE__,__LINE__);

		$sort = ' ORDER BY ';
		if($_GET['sort_target'] ) {
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$sort .= ', ';
				}
				$sort .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$sort .= 'w.regdate DESC';
		}
		$limit = ' limit '.$_GET['start'].', '.$this->config['loop_scale'];
		$query['tool'] = 'select';
		$query['where'] .= $sort.$limit;
		// var_dump($query); exit;
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		$i = 0;
		while ($row = mysqli_fetch_assoc($result)) {
			$row['list_cnt'] = $i;
			$row['no'] = $total - $_GET['start'] - $row['list_cnt'];

			$row['regdate'] = date('Y-m-d H:i:s', $row['regdate']);
			$row['joindate'] = date('Y-m-d H:i:s', $row['joindate']);
			$row['mobile'] = str_replace('8201','821',  str_replace('+82', '82', $row['mobile']));
			$row['etc'] = '';
			$loop[] = $row;
			$i++;
        }
		exit(json_encode(array('data'=>$loop,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
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
		global $config_basic;
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where userid=\''.$GLOBALS['_POST_ESCAPE']['userid'].'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_member7);
			}
			else {
				jsonMsg(0,'err_id');
			}
		}
		$query['tool'] = 'row';
		$query['fields'] = 'userno,userid,userpw,name,mobile,level_code,bank_account';
		$query['fields'].= ',bool_confirm_email,bool_confirm_mobile,bool_confirm_idimage,bool_email_krw_input,bool_sms_krw_input,bool_email_krw_output,bool_sms_krw_output,bool_email_btc_trade,bool_email_btc_input,bool_email_btc_output,bool_confirm_join';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

        //echo $row['permission']; exit;

		if($row['bool_confirm_join'] < 1) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg('관리자 심사 후 사용하실 수 있습니다.');
			}
			else {
				jsonMsg(0, '관리자 심사 후 사용하실 수 있습니다.');
			}
		}
		// 이메일 인증 확인시 사용 - 미사용 필요시 오픈
		// if($row['bool_confirm_email'] < 1) {
		// 	if($config_basic['bool_ssl'] > 0) {
		// 		errMsg(Lang::main_member8);
		// 	}
		// 	else {
		// 		jsonMsg(0,Lang::main_member8);
		// 	}
		// }
		if($row['userpw'] != md5($_POST['userpw'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_ledger17);
			}
			else {
				jsonMsg(0,'err_pw');
			}
		}
		

		$_SESSION['USER_NO'] = $row['userno'];
		$_SESSION['USER_ID'] = $row['userid'];
		$_SESSION['USER_NAME'] = $row['name'];
		$_SESSION['USER_MOBILE'] = $row['mobile'];
		$_SESSION['USER_LEVEL'] = $row['level_code'];

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

		if(empty($_SESSION['USER_ID'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_ledger18);
			}
			else {
				jsonMsg(0,'err_id');
			}
		}

		$ret_url = empty($_REQUEST['ret_url']) ? '/' : base64_decode($_REQUEST['ret_url']);

        // Phone 인증이 되지 않은 경우 certification url로 이동 (18.10.22 Brad)
        // if(!$row['bool_confirm_mobile']) {
        //    $ret_url = "/certification";
        // }

		if($config_basic['bool_ssl'] > 0) {
			replaceGo('//'.$_SERVER['SERVER_NAME'].$ret_url);
		}
		else {
			jsonMsg(1,$ret_url);
		}
	}


	//2009-01-25
	function out()
	{
		$device_type=$_SESSION['device_type'];
		// var_dump($device_type); exit;
		unset($_SESSION['USER_ID']);
		unset($_SESSION['USER_NAME']);
		unset($_SESSION['USER_LEVEL']);
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
			session_start();
			// setcookie('token', '', )
			$_SESSION['device_type'] = $device_type;
			alertGo('','/');
		}
		else {
			errMsg(Lang::admin_4);
		}
	}

	function memberXlsInsert()
	{
		$csv_file = $_SERVER["DOCUMENT_ROOT"].'/data/xls/'.mt_rand().'.csv'; // 2012-08-15 임시 엑셀 저장

		if(file_exists($_FILES['xls']['tmp_name'])) {
			if (!move_uploaded_file($_FILES['xls']['tmp_name'], $csv_file) ) {
				jsonMsg(0,Lang::main_ledger19);
			}
			$csvload = file($csv_file);
			$csvarray = explode("\n",implode($csvload));

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
					$userpw = md5(str_replace('-','',$phone));
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
					$arr[] = 'level_code=\'BW38\'';
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

	function srchQry_member()
	{
		$arr = array();
		if(!empty($_GET['srch_userid'])) {
			$arr[] = 'm.userid like \'%'.$GLOBALS['_GET_ESCAPE']['srch_userid'].'%\'';
		}
		if(!empty($_GET['userid'])) {
			$arr[] = 'm.userid like \'%'.$GLOBALS['_GET_ESCAPE']['userid'].'%\'';
		}
		if(!empty($_GET['name'])) {
			$arr[] = 'm.name like \'%'.urldecode($GLOBALS['_GET_ESCAPE']['name']).'%\'';
		}
		if(isset($_GET['mobile'])) { $arr[] = 'm.mobile like \'%'.$GLOBALS['_GET_ESCAPE']['mobile'].'%\''; }
		if(isset($_GET['nickname'])) { $arr[] = 'm.nickname like \'%'.$GLOBALS['_GET_ESCAPE']['nickname'].'%\''; }
		if(isset($_GET['email'])) { $arr[] = 'm.email like \'%'.$GLOBALS['_GET_ESCAPE']['email'].'%\''; }

		if(isset($_GET['start_date'])) { $arr[] = ' m.regdate>=\''.strtotime($_GET['start_date'].' 00:00:00').'\''; }
		if(isset($_GET['end_date'])) { $arr[] = ' m.regdate<=\''.strtotime($_GET['end_date'].' 23:59:59').'\''; }
		$ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';

		return $ret;
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
		if(isset($_GET['mobile'])) { $arr[] = 'mobile like \'%'.$GLOBALS['_GET_ESCAPE']['mobile'].'%\''; }
		if(isset($_GET['email'])) { $arr[] = 'email like \'%'.$GLOBALS['_GET_ESCAPE']['email'].'%\''; }
		if(isset($_GET['bool_email'])) { $arr[] = 'ok_mail=\''.$GLOBALS['_GET_ESCAPE']['ok_mail'].'\''; }
		if(isset($_GET['ok_sms'])) { $arr[] = 'ok_sms=\''.$GLOBALS['_GET_ESCAPE']['ok_sms'].'\''; }

		if(isset($_GET['start_date'])) { $arr[] = ' regdate>=\''.strtotime($_GET['start_date'].' 00:00:00').'\''; }
		if(isset($_GET['end_date'])) { $arr[] = ' regdate<=\''.strtotime($_GET['end_date'].' 23:59:59').'\''; }

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

    function srchQry_member_balance()
    {
        $arr = array();
        if(!empty($_GET['userid'])) {
            $arr[] = 'jm.userid like \'%'.$GLOBALS['_GET_ESCAPE']['userid'].'%\'';
        }

        if(!empty($_GET['name'])) {
            $arr[] = 'jm.name like \'%'.$GLOBALS['_GET_ESCAPE']['name'].'%\'';
        }

        if(!empty($_GET['symbol_name'])) {
            $arr[] = 'jtc.name like \'%'.$GLOBALS['_GET_ESCAPE']['symbol_name'].'%\'';
        }

        if(!empty($_GET['goods_grade'])) {
            $arr[] = 'jew.goods_grade = \''.$GLOBALS['_GET_ESCAPE']['goods_grade'].'\'';
        }

        $ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';
        return $ret;
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
		// $query = "SELECT userno, userid, NAME, mobile, bool_confirm_mobile, email, bool_confirm_email, image_identify_url, image_mix_url, bool_confirm_idimage, bank_name, bank_account, bank_owner
		// FROM js_member
		// ORDER BY bool_confirm_idimage ASC, image_identify_url DESC";
		$query['table_name'] = 'js_member';
		$query['tool'] = 'select';
		$query['fields'] = "userno, userid, `name`, regdate, mobile, bool_confirm_mobile, email, bool_confirm_email, image_identify_url, image_mix_url, bool_confirm_idimage, bank_name, bank_account, bank_owner, image_bank_url, bool_confirm_bank, bool_confirm_join, recomid, ifnull((select name from js_member where userid=m.recomid), '') recomname";
		$query['where'] = 'where 1';
		if($_GET['searchval']) {
			$query['where'] .= " AND (userid LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR `name` LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR email LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR mobile LIKE '%".$this->dbcon->escape($_GET['searchval'])."%') ";
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
			$list[$i]['confirm_idimage_date'] = $this->get_member_meta($list[$i]['userno'], 'confirm_idimage_date');
			$list[$i]['reject_idimage_date'] = $this->get_member_meta($list[$i]['userno'], 'reject_idimage_date');
			$list[$i]['confirm_bank_date'] = $this->get_member_meta($list[$i]['userno'], 'confirm_bank_date');
			$list[$i]['reject_bank_date'] = $this->get_member_meta($list[$i]['userno'], 'reject_bank_date');
		}
		var_dump($list); exit;
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
		// 			$this->dbcon->query("INSERT IGNORE INTO js_exchange_wallet SET userno='{$this->dbcon->escape($userno)}', symbol='{$this->dbcon->escape($s->symbol)}', active='Y', locked='N', bool_sell='1', bool_buy='1', bool_withdraw='1', confirmed='{$this->dbcon->escape($s->balance)}', unconfirmed='0', account='', address='', regdate=NOW()");
		// 		}
		// 		$this->dbcon->query("UPDATE js_exchange_wallet_previous SET userno='{$this->dbcon->escape($userno)}' WHERE nickname='{$this->dbcon->escape($nickname)}' AND userno='0'");
		// 	}
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
	 * mobile, email, idimage 3가지 승인 / 미승인 처리
	 */
	function confirm( $type, $value, $userno ) {
		if(! in_array($type, array('email', 'idimage', 'mobile', 'join'))) {
			return false;
		}
		$sql = "update js_member set ";
		switch($type) {
			case 'join':
				$sql .= " bool_confirm_join = '1'" ;
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
		$r = $this->dbcon->query($sql);
		if($r) {
			if($type=='idimage') { // 신분증 인증일때 sms 발송.
				if($value=='1') {
					$this->set_member_meta($userno, 'confirm_idimage_date', date('Y-m-d H:i:s'));
					$_member_info = $this->get_member_info($userno);
					$msg_data = array();
					$msg_data['tran_phone'] = $_member_info['mobile'];
					$config_basic = getConfig('js_config_basic','shop_name,shop_url');
					// 가입시 국가코드에 따라 승인 문자를 다르게...
					if($_member_info['mobile_country_code']=='KR') {
						$confirm_msg = '보내주신 신분증 사진을 확인한 결과 정상적으로 승인되었습니다.';
					} else if($_member_info['mobile_country_code']=='CN') {
						$confirm_msg = '您上传的身份证照片已通过审核。';
					} else {
						$confirm_msg = 'We checked the ID photo you sent and approved it normally.';
					}

					$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . $confirm_msg;
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
					$_member_info = $this->get_member_info($userno);
					$msg_data = array();
					$msg_data['tran_phone'] = $_member_info['mobile'];
					$config_basic = getConfig('js_config_basic','shop_name,shop_url');
					$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . '보내주신 통장사본을 확인한 결과 정상적으로 승인되었습니다.';
					@ sendSms($msg_data);
				}
			}
			if($type=='join') { // 
				$this->_confirm_join($userno);
			}
		}
		return $r;
	}

	/**
	 * mobile, email, idimage 3가지 승인 / 미승인 처리
	 */
	function reject ( $type, $userno, $msg='' ) {
		if(! in_array($type, array('idimage'))) {
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
				$sql .= " bool_confirm_bank = '0'" ;
			break;
		}
		$sql .= " where userno = '{$userno}' ";
		$r = $this->dbcon->query($sql);
		if($r) {
			if($type=='idimage') { // 신분증 인증일때 sms 발송.
				$this->set_member_meta($userno, 'reject_idimage_date', date('Y-m-d H:i:s'));
				$this->set_member_meta($userno, 'reject_idimage_msg', $msg);
				$_member_info = $this->get_member_info($userno);
				$msg_data = array();
				$msg_data['tran_phone'] = $_member_info['mobile'];
				$config_basic = getConfig('js_config_basic','shop_name,shop_url');

				// 가입시 국가코드에 따라 승인 문자를 다르게...
				if($_member_info['mobile_country_code']=='KR') {
					$msg = '신분증 인증이 반려되었습니다. 올바른 사진을 등록하셨는지 확인해 주세요.';
				} else if($_member_info['mobile_country_code']=='CN') {
					$msg = '您上传的身份证照片未通过审核。请按照要求上传。';
				} else {
					$msg = 'Your ID has been denied. Please make sure you have registered the correct photo.';
				}

				$msg_data['tran_msg']  = '['.$config_basic['shop_name'].'] ' . $msg;
				@ sendSms($msg_data);
			}
			if($type=='bank') { // 인행계좌 인증일때
				// 기존 이미지 삭제하기.
				$member_image = $this->dbcon->query_unique_object("select image_bank_url from js_member where userno='{$userno}'");
				if($member_image->image_bank_url){
					include ROOT_DIR.'/cheditor/imageUpload/s3.php';
					$S3 = new S3();
					@ $S3->delete_file_to_s3($member_image->image_bank_url);
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
					@ sendSms($msg_data);
				}
			}
		}
		return $r;
	}

	/**
	 * mobile, email, idimage, bank 4가지 승인 / 미승인 처리
	 */
	function delete_img ( $imgurl, $userno ) {
		include ROOT_DIR.'/cheditor/imageUpload/s3.php';
		$S3 = new S3();
		// 이미지 삭제하기.
		$member_image = $this->dbcon->query_unique_object("select image_identify_url, image_mix_url, image_bank_url from js_member where userno='{$userno}'  ");
		if($member_image->image_identify_url == $imgurl){
			@ $S3->delete_file_to_s3($member_image->image_identify_url);
			$sql = "update js_member set image_identify_url=''  where userno='{$userno}' ";
			$this->dbcon->query($sql);
		}
		if($member_image->image_mix_url == $imgurl){
			@$S3->delete_file_to_s3($member_image->image_mix_url);
			$sql = "update js_member set image_mix_url=''  where userno='{$userno}' ";
			$this->dbcon->query($sql);
		}
		if($member_image->image_bank_url == $imgurl){
			@$S3->delete_file_to_s3($member_image->image_bank_url);
			$sql = "update js_member set image_bank_url=''  where userno='{$userno}' ";
			$this->dbcon->query($sql);
		}
		return true;
	}

	/**
	 * 회원 lock  처리
	 */
	function lock( $type, $userno, $month ) {
		// global $tradeapi;

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
		// global $tradeapi;
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

	//패스워드 및 pin 같이 리셋
	public function passwdDefault() {
		$query = "update js_member set userpw='".md5('123456')."', pin='".md5('123456')."', passwd_default=passwd_default+1 where userno='{$this->dbcon->escape($_POST['userno'])}'";
		//var_dump($query); exit;
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function get_currency() {
		return $this->dbcon->query_fetch_array("select * from js_exchange_currency where active='Y'");
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
	if(isset($arr['mobile_country_code']))  { $qry[] = 'mobile_country_code=\''.$GLOBALS['dbcon']->escape($arr['mobile_country_code']).'\''; }
	if(isset($arr['email']))  { $qry[] = 'email=\''.$GLOBALS['dbcon']->escape($arr['email']).'\''; }
	if(isset($arr['zipcode']))  { $qry[] = 'zipcode=\''.$GLOBALS['dbcon']->escape($arr['zipcode']).'\''; }
	if(isset($arr['address_a']))  { $qry[] = 'address_a=\''.$GLOBALS['dbcon']->escape($arr['address_a']).'\''; }
	if(isset($arr['address_b']))  { $qry[] = 'address_b=\''.$GLOBALS['dbcon']->escape($arr['address_b']).'\''; }
	if(isset($arr['bool_email']))  { $qry[] = 'bool_email=\''.$GLOBALS['dbcon']->escape($arr['bool_email']).'\''; }
	if(isset($arr['bool_sms']))  { $qry[] = 'bool_sms=\''.$GLOBALS['dbcon']->escape($arr['bool_sms']).'\''; }
	if(isset($arr['bool_lunar']))  { $qry[] = 'bool_lunar=\''.$GLOBALS['dbcon']->escape($arr['bool_lunar']).'\''; }
	if(isset($arr['birthday']))  { $qry[] = 'birthday=\''.$GLOBALS['dbcon']->escape($arr['birthday']).'\''; }
	if(!empty($arr['level_code']))  { $qry[] = 'level_code=\''.$GLOBALS['dbcon']->escape($arr['level_code']).'\''; }
	if(!empty($arr['introduce']))  { $qry[] = 'introduce=\''.$GLOBALS['dbcon']->escape($arr['introduce']).'\''; }
	if(!empty($arr['hit']))  { $qry[] = 'hit=\''.$GLOBALS['dbcon']->escape($arr['hit']).'\''; }

	if(isset($arr['recomid']))  { $qry[] = 'recomid=\''.$GLOBALS['dbcon']->escape($arr['recomid']).'\''; }
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
	if(!empty($arr['tran_date']))  { $qry[] = 'tran_date='.$GLOBALS['dbcon']->escape($arr['tran_date']); }
	if(!empty($arr['tran_msg']))  { $qry[] = 'tran_msg=\''.$GLOBALS['dbcon']->escape($arr['tran_msg']).'\''; }
	if(!empty($arr['tran_result']))  { $qry[] = 'tran_result=\''.$GLOBALS['dbcon']->escape($arr['tran_result']).'\''; }
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	return implode(',',$qry);
}
?>