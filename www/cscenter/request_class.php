<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class  Request extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_request';
		$config['query_func'] = 'requestQuery';
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
		$config['upload_limit'] = TRUE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_width'] = 0;
		$config['thumb_height'] = 0;
		$config['thumb_size'] = array();
		/************************************/
		$config['bool_editor'] = TRUE;
		$config['editor_target'] = array('contents');
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = TRUE;
		$config['editor_thumb_width'] = 150;
		$config['editor_thumb_height'] = 150;
		/************************************/
		$config['bool_navi_page'] = TRUE;
		// $config['bool_navi_justify'] = 'center'; // '', 'center, end
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
		$query['where'] = 'where 1 '.$this->srchQry().' order by regdate desc';
		$this->config['navi_qry'] = $this->srchUrl('',FALSE);
		$this->bList($query,'loop_request');
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _lists($row)
	{
		$row['subject'] = cutStr(htmlspecialchars(strip_tags($row['subject'])),30,'...');
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
		return $row;
	}

	function write()
	{
		global $config_basic;
		$query = array();
		if($this->bWrite($query,$_POST,'_write')) {
			if($config_basic['bool_ssl'] > 0) {
				alertGo('접수되었습니다.\n\n빠른 시간내에 답변 드리겠습니다.!','//'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
			}
			else {
				jsonMsg(1);
			}
		}
		else {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_cs1);
			}
			else {
				jsonMsg(1);
			}
		}
	}

	function _write($arr)
	{
		$arr['phone'] = $arr['phone_a'].'-'.$arr['phone_b'].'-'.$arr['phone_c'];
		$arr['mobile'] = $arr['mobile_a'].'-'.$arr['mobile_b'].'-'.$arr['mobile_c'];
		$arr['email'] = $arr['email_a'].'@'.$arr['email_b'];
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

		$arr[] = empty($_GET['start']) ? 'start=0' : 'start='.$_GET['start'];
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

function requestQuery($arr)
{
	$qry = array();
	if(isset($arr['idx']))  { $qry[] = 'idx=\''.$arr['idx'].'\''; }
	if(!empty($arr['userid']))  { $qry[] = 'userid=\''.$arr['userid'].'\''; }
	if(!empty($arr['author']))  { $qry[] = 'author=\''.$arr['author'].'\''; }
	if(!empty($arr['subject']))  { $qry[] = 'subject=\''.$arr['subject'].'\''; }
	if(!empty($arr['comname']))  { $qry[] = 'comname=\''.$arr['comname'].'\''; }
	if(!empty($arr['position']))  { $qry[] = 'position=\''.$arr['position'].'\''; }
	if(isset($arr['email']))  { $qry[] = 'email=\''.$arr['email'].'\''; }
	if(isset($arr['phone']))  { $qry[] = 'phone=\''.$arr['phone'].'\''; }
	if(isset($arr['mobile']))  { $qry[] = 'mobile=\''.$arr['mobile'].'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if($arr['pg_mode'] == 'write')  { $qry[] = 'ipaddr=\''.$_SERVER["REMOTE_ADDR"].'\''; }
	if(isset($arr['hit']))  { $qry[] = 'hit='.$arr['hit']; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	return implode(',',$qry);
}

?>