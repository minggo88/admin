<?php
/*--------------------------------------------
Date : 
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/

class ClassName extends BASIC
{
	function ClassName(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_class_name';
		$config['query_func'] = 'classNameQuery';
		$config['write_mode'] = 'ajax';//ajax or link
		/************************************/
		$config['file_dir'] = '/data/bbs';
		//$config['file_dir'] = '/data/shop';
		//$config['file_dir'] = '/data/attach';
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
		$config['thumb_size'] = array('shop_a'=>'150x150','shop_b'=>'250x250','shop_c'=>'350x350');
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
		/************************************/
		$config['bool_nest'] = FALSE;
		$config['nest_method'] = '';
		$config['nest_loop_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function editForm()
	{
		if(empty($_GET['code'])) {
			errMsg(Lang::admin_6);
		}
		$query = array();
		$query['where'] = 'where code=\''.$_GET['code'].'\'';
		$arr = $this->bEditForm($query);
		/** unix_timestamp를 Y-m-d형태로 변환**
		$arr['regdate'] = $this->dateModify($arr['regdate']);
		*/
		$this->tpl->assign($arr);
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function edit()
	{
		$query = array();
		$query['where'] = 'where code=\''.$_POST['code'].'\'';
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

	function srchQry()
	{
		$arr = array();
		/*
		if(!empty($_GET[''])) $arr[] = ' like \'%'.$_GET[''].'%\' ';
		*/
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($start=TRUE)
	{
		$arr = array();
		/*
		if($start) {
			if (empty($_GET['start'])) { $arr[] = 'start=0'; }
			else { $arr[] = 'start='.$_GET['start']; }
		}
		if(!empty($_GET[''])) $arr[] = '='.$_GET[''];
		*/
		$ret = sizeof($arr) > 0 ? '&'.implode('&',$arr) : '';
		return $ret;
	}

}

function classNameQuery($arr)
{
	$qry = array();
	if(isset($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(isset($arr['bool_c1']))  { $qry[] = 'bool_c1=\''.$arr['bool_c1'].'\''; }
	if(!empty($arr['name_c1']))  { $qry[] = 'name_c1=\''.$arr['name_c1'].'\''; }
	if(isset($arr['bool_c2']))  { $qry[] = 'bool_c2=\''.$arr['bool_c2'].'\''; }
	if(!empty($arr['name_c2']))  { $qry[] = 'name_c2=\''.$arr['name_c2'].'\''; }
	if(isset($arr['bool_c3']))  { $qry[] = 'bool_c3=\''.$arr['bool_c3'].'\''; }
	if(!empty($arr['name_c3']))  { $qry[] = 'name_c3=\''.$arr['name_c3'].'\''; }
	if(isset($arr['bool_c4']))  { $qry[] = 'bool_c4=\''.$arr['bool_c4'].'\''; }
	if(!empty($arr['name_c4']))  { $qry[] = 'name_c4=\''.$arr['name_c4'].'\''; }
	if(isset($arr['bool_c5']))  { $qry[] = 'bool_c5=\''.$arr['bool_c5'].'\''; }
	if(!empty($arr['name_c5']))  { $qry[] = 'name_c5=\''.$arr['name_c5'].'\''; }
	if(isset($arr['bool_c6']))  { $qry[] = 'bool_c6=\''.$arr['bool_c6'].'\''; }
	if(!empty($arr['name_c6']))  { $qry[] = 'name_c6=\''.$arr['name_c6'].'\''; }
	if(isset($arr['bool_c7']))  { $qry[] = 'bool_c7=\''.$arr['bool_c7'].'\''; }
	if(!empty($arr['name_c7']))  { $qry[] = 'name_c7=\''.$arr['name_c7'].'\''; }
	if(isset($arr['bool_c8']))  { $qry[] = 'bool_c8=\''.$arr['bool_c8'].'\''; }
	if(!empty($arr['name_c8']))  { $qry[] = 'name_c8=\''.$arr['name_c8'].'\''; }
	if(isset($arr['bool_c9']))  { $qry[] = 'bool_c9=\''.$arr['bool_c9'].'\''; }
	if(!empty($arr['name_c9']))  { $qry[] = 'name_c9=\''.$arr['name_c9'].'\''; }
	if(isset($arr['bool_c10']))  { $qry[] = 'bool_c10=\''.$arr['bool_c10'].'\''; }
	if(!empty($arr['name_c10']))  { $qry[] = 'name_c10=\''.$arr['name_c10'].'\''; }
	return implode(',',$qry);
}

?>