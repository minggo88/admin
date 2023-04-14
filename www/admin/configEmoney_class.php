<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
class ConfigEmoney extends BASIC
{
	function ConfigEmoney(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_config_emoney';
		$config['query_func'] = 'configEmoneyQuery';
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

function configEmoneyQuery($arr)
{
	$qry = array();
	//if(!empty($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(isset($arr['bool_emoney']))  { $qry[] = 'bool_emoney=\''.$arr['bool_emoney'].'\''; }
	if(isset($arr['emoney_ratio']))  { $qry[] = 'emoney_ratio=\''.$arr['emoney_ratio'].'\''; }
	if(isset($arr['bool_emoney_card']))  { $qry[] = 'bool_emoney_card=\''.$arr['bool_emoney_card'].'\''; }
	if(isset($arr['bool_emoney_account']))  { $qry[] = 'bool_emoney_account=\''.$arr['bool_emoney_account'].'\''; }
	if(isset($arr['bool_emoney_cash']))  { $qry[] = 'bool_emoney_cash=\''.$arr['bool_emoney_cash'].'\''; }
	if(isset($arr['bool_emoney_mobile']))  { $qry[] = 'bool_emoney_mobile=\''.$arr['bool_emoney_mobile'].'\''; }
	if(isset($arr['bool_emoney_with_coupon']))  { $qry[] = 'bool_emoney_with_coupon=\''.$arr['bool_emoney_with_coupon'].'\''; }
	if(!empty($arr['use_min_emoney']))  { $qry[] = 'use_min_emoney=\''.$arr['use_min_emoney'].'\''; }
	if(!empty($arr['use_max_emoney']))  { $qry[] = 'use_max_emoney=\''.$arr['use_max_emoney'].'\''; }
	if(isset($arr['bool_join_emoney']))  { $qry[] = 'bool_join_emoney=\''.$arr['bool_join_emoney'].'\''; }
	if(!empty($arr['join_give_emoney']))  { $qry[] = 'join_give_emoney=\''.$arr['join_give_emoney'].'\''; }
	if(isset($arr['bool_review_emoney']))  { $qry[] = 'bool_review_emoney=\''.$arr['bool_review_emoney'].'\''; }
	if(!empty($arr['method_review_emoney']))  { $qry[] = 'method_review_emoney=\''.$arr['method_review_emoney'].'\''; }
	if(isset($arr['review_give_emoney']))  { $qry[] = 'review_give_emoney=\''.$arr['review_give_emoney'].'\''; }
	return implode(',',$qry);
}

?>