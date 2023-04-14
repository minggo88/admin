<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

class SMS extends BASIC
{
	function SMS(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_config_sms';
		$config['query_func'] = 'configSmsQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
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
		$config['loop_scale'] = 10;
		$config['page_scale'] = 10;
		$config['navi_url'] = '';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';// ajax or link
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		$query['table_name'] = 'js_sms';
		$query['where'] = ' order by regdate desc';
		/**page navigation*****
		$query['where'] = 'where 1 '.$this->srchQry();
		$this->config['navi_qry'] = $this->getUrl('',FALSE);
		**********************/
		$this->bList($query,'loop','_lists');

	}

	function _lists($row)
	{
		$row['regdate'] = date('Y-m-d H:m:i',$row['regdate']);
		return $row;
	}

	function viewForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['where'] = "where code='".getSiteCode()."' ";
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign($row);
	}

	function edit()
	{
		$query = array();
		$query['where'] = "where code='".getSiteCode()."' ";
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function delMulti()
	{
		$query = array();
		$query['table_name'] = 'js_sms';
		$query['tool'] = 'delete';
		foreach ($_GET['idxs'] as $key => $val) {
			$query['where'] = 'where idx=\''.$val.'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function setSmsTarget()
	{
		if($_GET['kind_target'] == 'ss') {
			if(!empty($_GET['codes']))  {
				$arr = array();
				foreach ($_GET['codes'] as $key => $val) {
					$query = array();
					$query['table_name'] = 'js_student';
					$query['tool'] = 'select_one';
					$query['fields'] = 'mobile';
					$query['where'] = 'where idx=\''.$val.'\'';
					$mobile = $this->dbcon->query($query,__FILE__,__LINE__);
					if(!empty($mobile)) {
						$arr[] = str_replace('-','',$mobile);
					}
				}
				$tran_phone = implode(',',$arr);
				$this->tpl->assign('tran_phone',$tran_phone);
			}
		}
		else {
			if(!empty($_GET['userid']))  {
				$arr = array();
				foreach ($_GET['userid'] as $key => $val) {
					$query = array();
					$query['table_name'] = 'js_member';
					$query['tool'] = 'select_one';
					$query['fields'] = 'mobile';
					$query['where'] = 'where userid=\''.$val.'\'';
					$mobile = $this->dbcon->query($query,__FILE__,__LINE__);
					if(!empty($mobile)) {
						$arr[] = str_replace('-','',$mobile);
					}
				}
				$tran_phone = implode(',',$arr);
				$this->tpl->assign('tran_phone',$tran_phone);
			}
		}
	}




	function memberSendSms()
	{
		$msg_data = array();
		$msg_data['tran_phone'] = $_POST['tran_phone'];
		$msg_data['tran_msg'] = $_POST['tran_msg'];
		if(sendSms($msg_data)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	//전체회원 문자 보내기
	function allSms()
	{
		$query = array();
		if($_POST['kind_target'] == 'sm') {
			$query['table_name'] = 'js_student';
		}
		else {
			$query['table_name'] = 'js_member';
		}
		$query['tool'] = 'select';
		$query['fields'] = 'mobile';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$row['mobile'] = str_replace('-','',$row['mobile']);
			if(strlen($row['mobile']) > 0) {
				$arr[] = str_replace('-','',$row['mobile']);
			}
		}

		if(empty($arr)) {
			jsonMsg(0);
		}

		$tran_phone = implode(',',$arr);
		$msg_data = array();
		$msg_data['tran_phone'] = $tran_phone;
		$msg_data['tran_msg'] = $_POST['tran_msg'];
		if(sendSms($msg_data)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}
}

function smsQuery($arr)
{
	$qry = array();
	if(!empty($arr['tran_phone']))  { $qry[] = 'tran_phone=\''.$arr['tran_phone'].'\''; }
	if(!empty($arr['tran_callback']))  { $qry[] = 'tran_callback=\''.$arr['tran_callback'].'\''; }
	if(!empty($arr['tran_date']))  { $qry[] = 'tran_date=\''.$arr['tran_date'].'\''; }
	if(!empty($arr['tran_msg']))  { $qry[] = 'tran_msg=\''.$arr['tran_msg'].'\''; }
	if(!empty($arr['tran_result']))  { $qry[] = 'tran_result=\''.$arr['tran_result'].'\''; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

function configSmsQuery($arr)
{
	$qry = array();
	if(isset($arr['bool_sms']))  { $qry[] = 'bool_sms=\''.$arr['bool_sms'].'\''; }
	if(isset($arr['tran_callback']))  { $qry[] = 'tran_callback=\''.$arr['tran_callback'].'\''; }
	if(isset($arr['guest_no']))  { $qry[] = 'guest_no=\''.$arr['guest_no'].'\''; }
	if(isset($arr['guest_key']))  { $qry[] = 'guest_key=\''.$arr['guest_key'].'\''; }
	if(isset($arr['bool_msg_join']))  { $qry[] = 'bool_msg_join=\''.$arr['bool_msg_join'].'\''; }
	if(!empty($arr['msg_join']))  { $qry[] = 'msg_join=\''.$arr['msg_join'].'\''; }
	if(isset($arr['bool_msg_ordercash']))  { $qry[] = 'bool_msg_ordercash=\''.$arr['bool_msg_ordercash'].'\''; }
	if(!empty($arr['msg_ordercash']))  { $qry[] = 'msg_ordercash=\''.$arr['msg_ordercash'].'\''; }
	if(isset($arr['bool_msg_admin_ordercash']))  { $qry[] = 'bool_msg_admin_ordercash=\''.$arr['bool_msg_admin_ordercash'].'\''; }
	if(!empty($arr['msg_admin_ordercash']))  { $qry[] = 'msg_admin_ordercash=\''.$arr['msg_admin_ordercash'].'\''; }
	if(isset($arr['bool_msg_ordercard']))  { $qry[] = 'bool_msg_ordercard=\''.$arr['bool_msg_ordercard'].'\''; }
	if(!empty($arr['msg_ordercard']))  { $qry[] = 'msg_ordercard=\''.$arr['msg_ordercard'].'\''; }
	if(isset($arr['bool_msg_admin_ordercard']))  { $qry[] = 'bool_msg_admin_ordercard=\''.$arr['bool_msg_admin_ordercard'].'\''; }
	if(!empty($arr['msg_admin_ordercard']))  { $qry[] = 'msg_admin_ordercard=\''.$arr['msg_admin_ordercard'].'\''; }
	if(isset($arr['bool_msg_payment']))  { $qry[] = 'bool_msg_payment=\''.$arr['bool_msg_payment'].'\''; }
	if(!empty($arr['msg_payment']))  { $qry[] = 'msg_payment=\''.$arr['msg_payment'].'\''; }
	if(isset($arr['bool_msg_delivery']))  { $qry[] = 'bool_msg_delivery=\''.$arr['bool_msg_delivery'].'\''; }
	if(!empty($arr['msg_delivery']))  { $qry[] = 'msg_delivery=\''.$arr['msg_delivery'].'\''; }
	if(isset($arr['bool_msg_passwd']))  { $qry[] = 'bool_msg_passwd=\''.$arr['bool_msg_passwd'].'\''; }
	if(!empty($arr['msg_passwd']))  { $qry[] = 'msg_passwd=\''.$arr['msg_passwd'].'\''; }
	return implode(',',$qry);
}

?>