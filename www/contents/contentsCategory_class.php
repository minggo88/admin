<?php
/*--------------------------------------------
Date : 
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/

class ContentsCategory extends BASIC
{
	function ContentsCategory(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_contents_category';
		$config['query_func'] = 'contentsCategoryQuery';
		$config['write_mode'] = 'ajax';//ajax or link
		/************************************/
		//$config['file_dir'] = '/data/bbs';
		//$config['file_dir'] = '/data/shop';
		$config['file_dir'] = '/data/attach';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
		$config['staple_article'] = array();
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array('bg_img');
		$config['file_size'] = 2;
		$config['upload_limit'] = TRUE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_width'] = 300;
		$config['thumb_height'] = 300;
		$config['thumb_size'] = array('thumbnail'=>'300x300');
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
		$config['bool_nest'] = TRUE;
		$config['nest_method'] = 'nestList';
		$config['nest_loop_id'] = 'loop_lnb';

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		//$query['fields'] = '';
		$query['where'] = 'where depth=1 '.$this->srchQry().' order by ranking asc';
		$this->config['navi_qry'] = $this->srchUrl();
		$this->bList($query,'loop_gnb','_lists');
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
		$query['where'] = 'where depth=2 && parent_code=\''.$arr['cate_code'].'\' order by ranking asc';
		$loop = array();
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		while ($row = mysqli_fetch_assoc($result)) {
			if($row['cate_code'] == $arr['link_code']) {
				$row['bool_link'] = 1;
			}
			else {
				$row['bool_link'] = 0;
			}

			$loop[] = $row;
		}
		return $loop;
	}

	function view()
	{
		if(empty($_GET['cate_code'])) {
			errMsg(Lang::admin_6);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		//$query['fields'] = '';
		$query['where'] = 'where cate_code=\''.$_GET['cate_code'].'\'';
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
		$arr['depth'] = $_GET['depth'];
		$arr['bool_display'] = 1;
		$arr['bool_footer'] = 0;

		if(!empty($_GET['parent_code'])) {
			$arr['parent_code'] = $_GET['parent_code'];
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'select_one';
			$query['fields'] = 'contents_name';
			$query['where'] = 'where cate_code=\''.$_GET['parent_code'].'\'';
			$arr['parent_menu'] = $this->dbcon->query($query,__FILE__,__LINE__);
			$arr['kinds_contents'] = 'bbs';
		}
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		if(empty($_GET['cate_code'])) {
			errMsg(Lang::admin_6);
		}
		$query = array();
		$query['where'] = 'where cate_code=\''.$_GET['cate_code'].'\'';
		$arr = $this->bEditForm($query);
		if (empty($arr['parent_code'])) {
			$arr['depth'] = 1;
			$this->getSubMenu();
		}
		else{
			$arr['depth'] = 2;
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'select_one';
			$query['fields'] = 'contents_name';
			$query['where'] = 'where cate_code=\''.$arr['parent_code'].'\'';
			$arr['parent_menu'] = $this->dbcon->query($query,__FILE__,__LINE__);
		}

		/** unix_timestamp를 Y-m-d형태로 변환**
		$arr['regdate'] = $this->dateModify($arr['regdate']);
		*/
		$this->tpl->assign($arr);
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function getSubMenu()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';//select,select_one,select_affect,row,count,insert,insert_idx,update,delete,drop
		$query['fields'] = 'cate_code,contents_name';
		$query['where'] = 'where parent_code=\''.$_GET['cate_code'].'\' order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_menu',$loop);
	}

	function write()
	{
		if(empty($_POST['cate_code'])) {
			$_POST['cate_code'] = $this->getCode();
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'MAX(ranking)';
		if ($_POST['depth'] > 1) {
			$query['where'] = 'where parent_code=\''.$_POST['parent_code'].'\'';
		}
		else {
			$query['where'] = 'where depth=1';
		}
		$ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		$_POST['ranking'] = $ranking + 1;
		if (!empty($_POST['bool_footer'])) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'bool_footer=0';
			$this->dbcon->query($query,__FILE__,__LINE__);
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
		if(empty($_POST['cate_code'])) {
			jsonMsg(0);
		}
		if (!empty($_POST['bool_footer'])) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'bool_footer=0';
			$this->dbcon->query($query,__FILE__,__LINE__);
		}
		$query = array();
		$query['where'] = 'where cate_code=\''.$_POST['cate_code'].'\'';
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
		$query['where'] = 'where cate_code=\''.$code.'\'';
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
			$code = $_GET['cate_code'];
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		if($_GET['depth'] > 1) {
			$query['where'] = 'where cate_code=\''.$code.'\'';
		}
		else {
			$query['where'] = 'where cate_code=\''.$code.'\' || parent_code=\''.$code.'\'';
		}

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
			$query['where'] = 'where cate_code=\''.$arr[$i].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	//dragsort로 넘어오는 경우
	function saveSubRanking()
	{
		$arr = explode(",",$_GET['drag_code']);
		for ($i = 0,$j=1; $i < sizeof($arr) ; $i++,$j++) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'ranking='.$j;
			$query['where'] = 'where cate_code=\''.$arr[$i].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function getBbsCode()
	{
		$query = array();
		$query['table_name'] = 'js_bbs_info';
		$query['tool'] = 'select';
		$query['fields'] = 'bbscode,title';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		echo $this->json->encode($arr);
	}

	function getCtsCode()
	{
		$query = array();
		$query['table_name'] = 'js_contents';
		$query['tool'] = 'select';
		$query['fields'] = 'cts_code,title';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		echo $this->json->encode($arr);
	}
 	
	function getCurriculumCode()
	{
		$query = array();
		$query['table_name'] = 'js_curriculum';
		$query['tool'] = 'select';
		$query['fields'] = 'category_code, category_name';
		$query['where'] = 'where depth=1 order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[] = $row;
		}
		echo $this->json->encode($arr);
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

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		/*
		if($start) { $arr[] = 'start='.$_GET['start']; }
		if(!empty($_GET[''])) $arr[] = '='.$_GET[''];
		*/

		$ret = sizeof($arr) > 0 ? '&'.implode('&',$arr) : '';
		return $ret;
	}
}

function contentsCategoryQuery($arr)
{
	$qry = array();
	if(!empty($arr['cate_code']))  { $qry[] = 'cate_code=\''.$arr['cate_code'].'\''; }
	if(!empty($arr['parent_code']))  { $qry[] = 'parent_code=\''.$arr['parent_code'].'\''; }
	if(!empty($arr['contents_name']))  { $qry[] = 'contents_name=\''.$arr['contents_name'].'\''; }
	if(!empty($arr['kinds_contents']))  { $qry[] = 'kinds_contents=\''.$arr['kinds_contents'].'\''; }
	if(!empty($arr['contents_code']))  { $qry[] = 'contents_code=\''.$arr['contents_code'].'\''; }
	if(!empty($arr['depth']))  { $qry[] = 'depth=\''.$arr['depth'].'\''; }
	if(!empty($arr['bool_display']))  { $qry[] = 'bool_display=\''.$arr['bool_display'].'\''; }
	if(!empty($arr['bool_footer']))  { $qry[] = 'bool_footer=\''.$arr['bool_footer'].'\''; }
	if(!empty($arr['link_code']))  { $qry[] = 'link_code=\''.$arr['link_code'].'\''; }
	if(!empty($arr['link_url']))  { $qry[] = 'link_url=\''.$arr['link_url'].'\''; }
	if(!empty($arr['bg_img']))  { $qry[] = 'bg_img=\''.$arr['bg_img'].'\''; }
	if(!empty($arr['title_copy']))  { $qry[] = 'title_copy=\''.$arr['title_copy'].'\''; }
	if(!empty($arr['ranking']))  { $qry[] = 'ranking=\''.$arr['ranking'].'\''; }
	//if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

?>