<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

class Popup extends BASIC
{
	function Popup(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_popup';
		$config['query_func'] = 'popupQuery';
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
		$config['bool_editor'] = TRUE;
		$config['editor_target'] = array('contents');
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

	function lists()
	{
		$query = array();
		$query['where'] = 'order by bool_popup desc,ranking asc';
		$this->bList($query,'loop','_lists');
	}

	function _lists($row)
	{
		$row['start_date'] = date('Y-m-d',$row['start_date']);
		$row['end_date'] = date('Y-m-d',$row['end_date']);
		return $row;
	}

	function rankList()
	{
		$query = array();
		$query['where'] = 'where bool_popup=1 order by ranking asc';
		$this->bList($query,'loop_rank','none');
	}

	function newForm()
	{
		$arr = array();
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		$query = array();
		$query['where'] = 'where idx='.$_GET['idx'];
		$arr = $this->bEditForm($query);
		$arr['start_date'] = $this->dateModify($arr['start_date']);
		$arr['end_date'] = $this->dateModify($arr['end_date']);
		$this->tpl->assign($arr);
	}

	function write()
	{
		$_POST['width'] = empty($_POST['width']) ? 100 : $_POST['width'];
		$_POST['height'] = empty($_POST['height']) ? 100 : $_POST['height'];
		$_POST['pos_x'] = empty($_POST['pos_x']) ? 0 : $_POST['pos_x'];
		$_POST['pos_y'] = empty($_POST['pos_y']) ? 0 : $_POST['pos_y'];
		$this->checkBool(2);
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
		$this->checkBool(3);
		$query = array();
		$query['where'] = 'where idx='.$_POST['idx'];
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		if(empty($arr['bool_popup'])) {
			$arr['bool_popup'] = 0;
		}
		$arr['start_date'] = $this->dateModify($arr['start_date']);
		$arr['end_date'] = $this->dateModify($arr['end_date']);
		return $arr;
	}

	function editRank()
	{
		$ret = array();
		$arr = explode(',',$_GET['rank']);
		for ($i = 0; $i < sizeof($arr) ; $i++) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'ranking='.($i+1);
			$query['where'] = 'where idx=\''.$arr[$i].'\'';
			$ret[] = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		if($this->logicalSum($ret)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function del()
	{
		$query = array();
		$query['where'] = 'where idx='.$_GET['idx'];
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function checkBool($max)
	{
		if(!empty($_POST['bool_popup'])) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'count';
			$query['where'] = 'where bool_popup=1';
			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			if($cnt > $max) {
				jsonMsg(0,'err_cnt');
			}
		}
	}
}

function popupQuery($arr)
{
	$qry = array();
	if(!empty($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(!empty($arr['start_date']))  { $qry[] = 'start_date=\''.$arr['start_date'].'\''; }
	if(!empty($arr['end_date']))  { $qry[] = 'end_date=\''.$arr['end_date'].'\''; }
	if(isset($arr['bool_popup']))  { $qry[] = 'bool_popup=\''.$arr['bool_popup'].'\''; }
	if(!empty($arr['size_mode']))  { $qry[] = 'size_mode=\''.$arr['size_mode'].'\''; }
	if(isset($arr['width']))  { $qry[] = 'width=\''.$arr['width'].'\''; }
	if(isset($arr['height']))  { $qry[] = 'height=\''.$arr['height'].'\''; }
	if(!empty($arr['pos_mode']))  { $qry[] = 'pos_mode=\''.$arr['pos_mode'].'\''; }
	if(isset($arr['pos_x']))  { $qry[] = 'pos_x=\''.$arr['pos_x'].'\''; }
	if(isset($arr['pos_y']))  { $qry[] = 'pos_y=\''.$arr['pos_y'].'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if(isset($arr['ranking']))  { $qry[] = 'ranking=\''.$arr['ranking'].'\''; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

?>