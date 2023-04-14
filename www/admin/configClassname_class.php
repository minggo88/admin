<?php
/*--------------------------------------------
Date : 
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/

class ConfigClassname extends BASIC
{
	function ConfigClassname(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_config_classname';
		$config['query_func'] = 'configClassnameQuery';
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
		/************************************/
		$config['bool_nest'] = FALSE;
		$config['nest_method'] = '';
		$config['nest_loop_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where 1  order by ranking asc';
		$this->bList($query,'loop','_lists');
	}

	function _lists($row)
	{
		/** unix_timestamp를 Y-m-d형태로 변환**
		$row['regdate'] = $this->dateModify($row['regdate']);
		*/
		return $row;
	}

	function write()
	{
		if(empty($_POST['class_name'])) {
			$_POST['class_name'] = $this->getCode();
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'MAX(ranking) AS max_ranking';
		$max_ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		$_POST['ranking'] = $max_ranking + 1;
		$query = array();
		if($this->bWrite($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function edit()
	{
		if(empty($_POST['class_name'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['where'] = 'where class_name=\''.$_POST['class_name'].'\'';
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		/** calendar를 통해서 넘어온 값을 처리하는 부분**
		$arr['regdate'] = $this->dateModify($arr['regdate']);
		*/
		return $arr;
	}

	function getCode()
	{
		$arr = array();
		for($i=0;$i<2 ;$i++) {
			$arr[] = chr(mt_rand(66,90));
		}
		$code = implode('',$arr);
		$query = array();
		$query['tool'] = 'count';
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where class_name=\''.$code.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt >0) {
			return $this->getCode();
		}
		else {
			return $code;
		}	
	}

	function del()
	{
		if(empty($_GET['class_name'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where class_name=\''.$_GET['class_name'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	//tablednd로 넘어는 순서저장
	function saveRanking()
	{
		$arr = array();
		foreach ($_GET['drag_table'] as $key => $val) {
			if(!empty($val)) {
				$arr[] = $val;
			}
		}
		for ($i = 0,$j=1; $i < sizeof($arr) ; $i++,$j++) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'ranking='.$j;
			$query['where'] = 'where class_name=\''.$arr[$i].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}
}

function configClassnameQuery($arr)
{
	$qry = array();
	if(!empty($arr['class_name']))  { $qry[] = 'class_name=\''.$arr['class_name'].'\''; }
	if(!empty($arr['class_title']))  { $qry[] = 'class_title=\''.$arr['class_title'].'\''; }
	if(!empty($arr['ranking']))  { $qry[] = 'ranking=\''.$arr['ranking'].'\''; }
	$qry = implode(',',$qry);
	return $qry;
}


?>