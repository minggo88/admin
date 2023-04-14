<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class ConfigStudentcode extends BASIC 
{
	var $ext;

	function ConfigStudentcode(&$tpl)
	{

		$config = array();

		$config['table_name'] = 'js_config_studentcode';
		$config['query_func'] = 'studentcodeQuery';
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

	function editForm()
	{
		$query = array();
		$query['where'] = "where code='".getSiteCode()."' ";
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function edit()
	{
		$query = array();
		$query['where'] = "where code='".getSiteCode()."' ";
		if($this->bEdit($query,$_POST)) {
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

function studentcodeQuery($arr)
{
	$qry = array();
	if(!empty($arr['code_prefix']))  { $qry[] = 'code_prefix=\''.$arr['code_prefix'].'\''; }
	$qry = implode(',',$qry);
	return $qry;
}

?>