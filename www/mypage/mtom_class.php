<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class Mtom extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_mtom';
		$config['query_func'] = 'mtomQuery';
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
		$config['bool_editor'] = TRUE;
		$config['editor_target'] = array('contents');
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = FALSE;
		$config['editor_thumb_width'] = 150;
		$config['editor_thumb_height'] = 150;
		/************************************/
		$config['bool_navi_page'] = TRUE;
		$config['loop_scale'] = 10;
		$config['page_scale'] = 5;
		$config['navi_url'] = 'mtom.php';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		if($this->tpl->skin == 'admin') {
			$query['where'] = 'where 1 '.$this->srchQry().' order by regdate desc';
		}
		else {
			$query['where'] = 'where userid=\''.$_SESSION['USER_ID'].'\' order by regdate desc';
		}
		//var_dump($query);
		$this->config['navi_qry'] = $this->srchUrl('',FALSE);
		$this->bList($query,'loop_mtom');
		$this->tpl->assign('srch_url',$this->srchUrl());

	}

	function _lists($row)
	{
		$row['subject'] = cutStr(htmlspecialchars(strip_tags($row['subject'])),30,'...');
		$row['bool_rplcontents'] = empty($row['rplcontents']) ? TRUE : FALSE;
		return $row;
	}

	function view()
	{
		$query = array();
		$query['where'] = 'where idx=\''.$_GET['idx'].'\'';
		$this->bView($query,'_view');
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _view($row)
	{
		$row['regdate'] = date('Y/m/d H:i:s',$row['regdate']);
		$row['contents'] = nl2br($row['contents']);
		$row['bool_rplcontents'] = empty($row['rplcontents']) ? FALSE : TRUE;
		$row['rpldate'] = empty($row['rplcontents']) ? '답변미등록' : date('Y/m/d H:i:s',$row['rpldate']);
		return $row;
	}

	function newForm()
	{
		$arr = array();
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		$query = array();
		$query['where'] = 'where  idx=\''.$_GET['idx'].'\'';
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function write()
	{
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
		$query = array();
		$query['where'] = 'where idx=\''.$_POST['idx'].'\'';
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

	function del()
	{
		$query = array();
		$query['where'] = 'where idx=\''.$_GET['idx'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function delMulti()
	{
		foreach ($_GET['idxs'] as $key => $val) {
			$query = array();
			$query['where'] = 'where idx=\''.$val.'\'';
			if(!$this->bDel($query)) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function srchQry()
	{
		$arr = array();
		if(!empty($_GET['s_val'])) {
			if(!empty($_GET['author'])) $arr[] = 'author like \'%'.$_GET['s_val'].'%\' ';
			if(!empty($_GET['subject'])) $arr[] = 'subject like \'%'.$_GET['s_val'].'%\' ';
			if(!empty($_GET['contents'])) $arr[] = 'contents like \'%'.$_GET['s_val'].'%\'';
		}
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		//$arr[] = empty($_GET['start']) ? 'start=0' : 'start='.$_GET['start'];
		if(!empty($idx)) { $arr[] = 'idx='.$idx; }
		if(!empty($_GET['s_val'])) {
			if(!empty($_GET['author'])) { $arr[] = 'author='.$_GET['author']; }
			if(!empty($_GET['subject'])) { $arr[] = 'subject='.$_GET['subject']; }
			if(!empty($_GET['contents'])) { $arr[] = 'contents='.$_GET['contents']; }
			if(!empty($_GET['s_val'])) { $arr[] = 's_val='.$_GET['s_val']; }
		}
		$ret = '&'.implode('&',$arr);
		return $ret;
	}
}

function mtomQuery($arr)
{
	$qry = array();
	if(isset($arr['idx']))  { $qry[] = 'idx=\''.$arr['idx'].'\''; }
	if(!empty($arr['userid']))  { $qry[] = 'userid=\''.$arr['userid'].'\''; }
	if(!empty($arr['author']))  { $qry[] = 'author=\''.$arr['author'].'\''; }
	if(!empty($arr['subject']))  { $qry[] = 'subject=\''.$arr['subject'].'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if(isset($arr['ipaddr']))  { $qry[] = 'ipaddr=\''.$arr['ipaddr'].'\''; }
	if(isset($arr['hit']))  { $qry[] = 'hit='.$arr['hit']; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	if(!empty($arr['rplcontents']))  { $qry[] = 'rplcontents=\''.$arr['rplcontents'].'\''; }
	if($arr['pg_mode'] == 'edit')  { $qry[] = 'rpldate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

?>