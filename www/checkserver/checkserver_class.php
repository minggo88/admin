<?php
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin
--------------------------------------------*/

class Checkserver extends BASIC
{
	function Checkserver(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_contents';
		$config['query_func'] = 'setQuery';
		$config['write_mode'] = 'ajax';//ajax or link
		/************************************/
		$config['file_dir'] = '/data/editor';
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

		$config['bool_editor_thumb'] = TRUE;
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
		//$query['fields'] = '';
		$query['where'] = 'where 1 '.$this->srchQry().' order by ranking asc';
		$this->config['navi_qry'] = $this->srchUrl();
		$this->bList($query,'loop_cts','_lists');
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _lists($row)
	{
		/** unix_timestamp를 Y-m-d형태로 변환**
		$row['regdate'] = $this->dateModify($row['regdate']);
		*/
		return $row;
	}

	function nestList($arr)
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';
		//$query['fields'] = '';
		$query['where'] = 'where cts_code=\''.$arr['cts_code'].'\'';
		$loop = array();
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		return $loop;
	}

	function view()
	{
		if(empty($_GET['cts_code'])) {
			errMsg(Lang::admin_6);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		//$query['fields'] = '';
		$query['where'] = 'where cts_code=\''.$_GET['cts_code'].'\'';
		$this->bView($query,'_view');
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _view($row)
	{
		return $row;
	}

	function newForm()
	{
		$arr = array();
		$this->tpl->assign($arr);
	}


	function editForm()
	{
		if(empty($_GET['cts_code'])) {
			errMsg(Lang::admin_6);
		}
		$query = array();
		$query['where'] = 'where cts_code=\''.$_GET['cts_code'].'\'';
		$arr = $this->bEditForm($query);
		/** unix_timestamp를 Y-m-d형태로 변환**
		$arr['regdate'] = $this->dateModify($arr['regdate']);
		*/
		$this->tpl->assign($arr);
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function write()
	{
		if(empty($_POST['cts_code'])) {
			$_POST['cts_code'] = $this->getCode();
		}
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
		if(empty($_POST['cts_code'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['where'] = 'where cts_code=\''.$_POST['cts_code'].'\'';
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
		$code = implode('',$arr).mt_rand(10000,99999);
		$query = array();
		$query['tool'] = 'count';
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where cts_code=\''.$code.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt >0) {
			return $this->getCode();
		}
		else {
			return $code;
		}	
	}

	function del($code='')
	{
		if(empty($code)) {
			$code = $_GET['cts_code'];
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where cts_code=\''.$code.'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function delMulti()
	{
		$ret = array();
		foreach ($_GET['codes'] as $key => $val) {
			$result = $this->del($val);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
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
			$query['where'] = 'where cts_code=\''.$arr[$i].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	//dragsort로 넘어오는 경우	
	/*
	function saveRanking()
	{
		$arr = explode(",",$_GET['drag_code']);
		for ($i = 0,$j=1; $i < sizeof($arr) ; $i++,$j++) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'ranking='.$j;
			$query['where'] = 'where code=\''.$arr[$i].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}
	*/

	function srchQry()
	{
		$arr = array();
		//if(!empty($_GET[''])) $arr[] = ' like \'%'.$_GET[''].'%\' ';

		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		//if($start) { $arr[] = 'start='.$_GET['start']; }
		//if(!empty($_GET[''])) $arr[] = '='.$_GET[''];

		$ret = sizeof($arr) > 0 ? '&'.implode('&',$arr) : '';
		return $ret;
	}

}

function setQuery($arr)
{
	$qry = array();
	if(!empty($arr['cts_code']))  { $qry[] = 'cts_code=\''.$arr['cts_code'].'\''; }
	if(!empty($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if(!empty($arr['ranking']))  { $qry[] = 'ranking=\''.$arr['ranking'].'\''; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

?>