<?php
/*--------------------------------------------
Date : 
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/

class Contents extends BASIC
{
	function Contents(&$tpl)
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
		
		$this->lang_c = $_GET['language'];
		if(!$this->lang_c || $this->lang_c=='ko') $this->lang_c='kr';
	}

	function lists()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		//$query['fields'] = '';
		$query['fields'] = "cts_code, IF(title_{$this->lang_c}='', `title_kr`, title_{$this->lang_c}) `title`, IF(contents_{$this->lang_c}='', contents_kr, contents_{$this->lang_c}) `contents`, ranking, regdate regtime, FROM_UNIXTIME(regdate) regdate";
		$query['where'] = 'where 1 '.$this->srchQry().' order by regdate desc';
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
		$row['regtime'] = $row['regdate'];
		$row['regdate'] = date('Y-m-d H:i:s', $row['regdate']);
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
		} else {
			if($this->checkDuplicateCode($_POST['cts_code'])) {
				jsonMsg(0, '중복된 코드입니다. 다른 코드를 입력해주세요.');
			}
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
			jsonMsg(0, 'cts_code 값이 필요합니다.');
		}
		if(empty($_POST['cts_code_old'])) {
			jsonMsg(0, 'cts_code_old 값이 필요합니다.');
		}
		if($_POST['cts_code']!=$_POST['cts_code_old']) {
			$cnt = $this->dbcon->query_one("SELECT COUNT(*) FROM js_contents where cts_code='{$this->dbcon->escape($_POST['cts_code'])}' COLLATE utf8_bin");// 대소문자를 바꿀 수 있도록 대소문자 구분하여 검색합니다.
			if($cnt) {
				jsonMsg(0, '이미 사용중인 cts_code입니다.');
			}
		}
		$query = array();
		$query['where'] = "WHERE cts_code='{$this->dbcon->escape($_POST['cts_code_old'])}'"; // 대소문자를 바꿀 수 있도록 대소문자 구분하여 검색합니다.
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		// calendar를 통해서 넘어온 값을 처리하는 부분
		// $arr['regdate'] = $this->dateModify($arr['regdate']);
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
	function checkDuplicateCode($code)
	{
		$query = array();
		$query['tool'] = 'count';
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = "where cts_code='{$this->dbcon->escape($code)}'";
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt) {return true;}
		else {return false;}	
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
	if(!empty($arr['title_kr']))  { $qry[] = 'title_kr=\''.$arr['title_kr'].'\''; }
	if(!empty($arr['contents_kr']))  { $qry[] = 'contents_kr=\''.$arr['contents_kr'].'\''; }
	if(!empty($arr['title_en']))  { $qry[] = 'title_en=\''.$arr['title_en'].'\''; }
	if(!empty($arr['contents_en']))  { $qry[] = 'contents_en=\''.$arr['contents_en'].'\''; }
	if(!empty($arr['title_cn']))  { $qry[] = 'title_cn=\''.$arr['title_cn'].'\''; }
	if(!empty($arr['contents_cn']))  { $qry[] = 'contents_cn=\''.$arr['contents_cn'].'\''; }
	if(!empty($arr['title_es']))  { $qry[] = 'title_es=\''.$arr['title_es'].'\''; }
	if(!empty($arr['contents_es']))  { $qry[] = 'contents_es=\''.$arr['contents_es'].'\''; }
	if(!empty($arr['title_ja']))  { $qry[] = 'title_ja=\''.$arr['title_ja'].'\''; }
	if(!empty($arr['contents_ja']))  { $qry[] = 'contents_ja=\''.$arr['contents_ja'].'\''; }
	if(!empty($arr['ranking']))  { $qry[] = 'ranking=\''.$arr['ranking'].'\''; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

?>