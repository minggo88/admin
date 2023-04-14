<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
class ConfigPayment extends BASIC
{
	function ConfigPayment(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_config_payment';
		$config['query_func'] = 'paymentQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/attach';
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
		$config['bool_navi_page'] = FALSE;
		$config['loop_scale'] = 10;
		$config['page_scale'] = 10;
		$config['navi_url'] = '';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';// ajax or link
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function viewForm()
	{
		$this->insertRecord();
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = "where code='".getSiteCode()."' ";
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function edit()
	{
		$this->insertRecord();
		
		$arr = array(
			'bool_cash',//카드
			'bool_card',//카드
			'bool_accounttrans',//계좌이체
			'bool_vaccount'//가상계좌
		);
		foreach ($arr as $key => $val) {
			if(empty($_POST[$val])) {
				$_POST[$val] = 0;
			}
		}

		$query = array();
		$query['where'] = "where code='".getSiteCode()."' ";
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		return $arr;
	}

	function editAccount()
	{
		$query = array();
		$query['table_name'] = 'js_config_account';
		$query['tool'] = 'delete';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			for ($i = 0; $i < sizeof($_POST['bank_name']) ; $i++) {
				if(!empty($_POST['bank_name'][$i])) {
					$query['tool'] = 'insert';
					$query['fields'] = 'bank_name=\''.$_POST['bank_name'][$i].'\',account_no=\''.$_POST['account_no'][$i].'\',account_user=\''.$_POST['account_user'][$i].'\'';
					$result = $this->dbcon->query($query,__FILE__,__LINE__);
					if(!$result) {
						jsonMsg(0);
					}
				}
			}
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function getAccount()
	{
		$query = array();
		$query['table_name'] = 'js_config_account';
		$query['tool'] = 'select';
		$query['fields'] = 'bank_name,account_no,account_user';
		$query['where'] = 'order by idx';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) { $loop[] = $row;	}
		$arr = array();
		$arr['bool'] = 1;
		$arr['msg'] = $loop;
		echo $this->json->encode($arr);
	}

	function insertRecord()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = "where code='".getSiteCode()."' ";
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			$query['tool'] = 'insert';
			$query['fields'] = " code='".getSiteCode()."' ";
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
	}
}

function paymentQuery($arr)
{
	$qry = array();
	//if(!empty($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(isset($arr['bool_cash']))  { $qry[] = 'bool_cash=\''.$arr['bool_cash'].'\''; }
	if(isset($arr['bool_card']))  { $qry[] = 'bool_card=\''.$arr['bool_card'].'\''; }
	if(isset($arr['bool_vaccount']))  { $qry[] = 'bool_vaccount=\''.$arr['bool_vaccount'].'\''; }
	if(isset($arr['bool_accounttrans']))  { $qry[] = 'bool_accounttrans=\''.$arr['bool_accounttrans'].'\''; }
	if(isset($arr['payment_mincardprice']))  { $qry[] = 'payment_mincardprice=\''.$arr['payment_mincardprice'].'\''; }
	if(isset($arr['payment_minmobileprice']))  { $qry[] = 'payment_minmobileprice=\''.$arr['payment_minmobileprice'].'\''; }
	if(isset($arr['payment_maxmobileprice']))  { $qry[] = 'payment_maxmobileprice=\''.$arr['payment_maxmobileprice'].'\''; }
	if(isset($arr['bool_escrow']))  { $qry[] = 'bool_escrow=\''.$arr['bool_escrow'].'\''; }
	if(!empty($arr['escrow_minprice']))  { $qry[] = 'escrow_minprice=\''.$arr['escrow_minprice'].'\''; }
	if(isset($arr['bool_overlap_coupon']))  { $qry[] = 'bool_overlap_coupon=\''.$arr['bool_overlap_coupon'].'\''; }
	return implode(',',$qry);
}

function accountQuery($arr)
{
	$qry = array();
	if(!empty($arr['bank_name']))  { $qry[] = 'bank_name=\''.$arr['bank_name'].'\''; }
	if(!empty($arr['account_no']))  { $qry[] = 'account_no=\''.$arr['account_no'].'\''; }
	if(!empty($arr['account_user']))  { $qry[] = 'account_user=\''.$arr['account_user'].'\''; }
	return implode(',',$qry);
}

?>