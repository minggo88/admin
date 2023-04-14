<?php
/*--------------------------------------------
Date : 2012-08-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class MainImg extends BASIC
{
	function MainImg(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_main_img';
		$config['query_func'] = 'mainimgQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
		$config['staple_article'] = array();
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array('img_banner');
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
		$config['page_scale'] = 5;
		$config['navi_url'] = $_SERVER['SCRIPT_NAME'];
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		$query['where'] = 'where 1 order by ranking';
		$this->bList($query,'loop_main_img');
	}

	function _lists($row)
	{
		return $row;
	}

	function newForm()
	{
		$arr = array();
		$arr['bool_banner'] = 1;
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		if(empty($_GET['idx'])) {
			errMsg(Lang::admin_7);
		}
		$query = array();
		$query['where'] = 'where  idx=\''.$_GET['idx'].'\'';
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function write()
	{
		//$this->imgSizeLimit();
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
		//이미지 사이즈 제한 스크립트
		if(empty($_POST['idx'])) {
			jsonMsg(0);
		}
		//$this->imgSizeLimit();
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

	function imgSizeLimit()
	{
		if(!empty($_FILES['img_banner']['size'])) {
			if($info['size_x'] != 1920 || $info['size_y'] != 400 ) {
				jsonMsg(0,'err_size');
			}
		}
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

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		$ret = '&'.implode('&',$arr);
		return $ret;
	}
}

function mainimgQuery($arr)
{
	$qry = array();
	if(isset($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(isset($arr['bool_banner']))  { $qry[] = 'bool_banner=\''.$arr['bool_banner'].'\''; }
	if(!empty($arr['img_banner']))  { $qry[] = 'img_banner=\''.$arr['img_banner'].'\''; }
	if(!empty($arr['ranking']))  { $qry[] = 'ranking=\''.$arr['ranking'].'\''; }
	return implode(',',$qry);
}

?>