<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
class ConfigPg extends BASIC
{
	function ConfigPg(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_config_pg';
		$config['query_func'] = 'pgQuery';
		$config['write_mode'] = 'ajax';//ajax or link
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
		$config['upload_limit'] = TRUE;

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
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = "where code='".getSiteCode()."' ";
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function edit()
	{
		$arr = array(
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

}

function pgQuery($arr)
{
	$qry = array();
	if(isset($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(isset($arr['kind_svc']))  { $qry[] = 'kind_svc=\''.$arr['kind_svc'].'\''; }
	if(isset($arr['bool_pg']))  { $qry[] = 'bool_pg=\''.$arr['bool_pg'].'\''; }
	if(!empty($arr['pg_id']))  { $qry[] = 'pg_id=\''.$arr['pg_id'].'\''; }
	if(!empty($arr['pg_pw']))  { $qry[] = 'pg_pw=\''.$arr['pg_pw'].'\''; }
	if(!empty($arr['pg_key']))  { $qry[] = 'pg_key=\''.$arr['pg_key'].'\''; }
	if(isset($arr['bool_card']))  { $qry[] = 'bool_card=\''.$arr['bool_card'].'\''; }
	if(isset($arr['bool_vaccount']))  { $qry[] = 'bool_vaccount=\''.$arr['bool_vaccount'].'\''; }
	if(isset($arr['bool_accounttrans']))  { $qry[] = 'bool_accounttrans=\''.$arr['bool_accounttrans'].'\''; }
	if(isset($arr['bool_no_interest']))  { $qry[] = 'bool_no_interest=\''.$arr['bool_no_interest'].'\''; }
	if(!empty($arr['pg_quota']))  { $qry[] = 'pg_quota=\''.$arr['pg_quota'].'\''; }
	if(!empty($arr['no_interest_quota']))  { $qry[] = 'no_interest_quota=\''.$arr['no_interest_quota'].'\''; }
	if(isset($arr['bool_escrow']))  { $qry[] = 'bool_escrow=\''.$arr['bool_escrow'].'\''; }
	if(!empty($arr['escrow_id']))  { $qry[] = 'escrow_id=\''.$arr['escrow_id'].'\''; }
	if(!empty($arr['escrow_key']))  { $qry[] = 'escrow_key=\''.$arr['escrow_key'].'\''; }
	return implode(',',$qry);
}

?>