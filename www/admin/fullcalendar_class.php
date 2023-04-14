<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class FullCalendar extends BASIC 
{
	function FullCalendar(&$tpl)
	{

		$config = array();

		$config['table_name'] = 'js_fullcalendar';
		$config['query_func'] = 'fullcalendarQuery';
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

	function lists()
	{
		$query = array();
		$query['table_name'] = 'js_fullcalendar';
		$query['tool'] = 'select';
		$query['fields'] = 'idx,start_date,end_date,title,url,contents,bool_allday';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_event',$loop);
	}

	function write()
	{
		if(empty($_POST['start_month']) || empty($_POST['start_day']) || empty($_POST['start_year'])) {
			jsonMsg(0);
		}

		$bool_allday = 1;

		if(!empty($_POST['start_month']) && !empty($_POST['start_day']) && !empty($_POST['start_year'])) {
			if(!empty($_POST['start_hour']) || !empty($_POST['start_min'])) {
				$bool_allday = 0;
			}
			$_POST['start_hour'] = empty($_POST['start_hour']) ? 0 :$_POST['start_hour'];
			$_POST['start_min'] = empty($_POST['start_min']) ? 0 :$_POST['start_min'];
			$_POST['start_date'] = mktime($_POST['start_hour'],$_POST['start_min'],0,$_POST['start_month'],$_POST['start_day'],$_POST['start_year']);
		}
		else {
			$_POST['start_date'] = 0;
		}

		if(!empty($_POST['end_month']) && !empty($_POST['end_day']) && !empty($_POST['end_year'])) {
			if(!empty($_POST['end_hour']) || !empty($_POST['end_min'])) {
				$bool_allday = 1;
			}			
			$_POST['end_hour'] = empty($_POST['end_hour']) ? 0 :$_POST['end_hour'];
			$_POST['end_min'] = empty($_POST['end_min']) ? 0 :$_POST['end_min'];
			$_POST['end_date'] = mktime($_POST['end_hour'],$_POST['end_min'],0,$_POST['end_month'],$_POST['end_day'],$_POST['end_year']);
		}
		else {
			$_POST['end_date'] = 0;
		}

		$_POST['bool_allday'] = $bool_allday;
		$query = array();
		if($this->bWrite($query,$_POST)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function dragEdit()
	{
		$_GET['start_date'] = $this->tstamptotime($_GET['start_date']);
		$_GET['end_date'] = $this->tstamptotime($_GET['end_date']);
		$query = array();
		$query['where'] = 'where idx=\''.$_GET['idx'].'\'';
		if($this->bEdit($query,$_GET)) {
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

	function tstamptotime($tstamp) {
		sscanf($tstamp,"%u-%u-%uT%u:%u:%uZ",$year,$month,$day,$hour,$min,$sec);
		$newtstamp=mktime($hour,$min,$sec,$month,$day,$year);
		return $newtstamp;
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
}

function fullcalendarQuery($arr)
{
	$qry = array();
	if(!empty($arr['start_date']))  { $qry[] = 'start_date=\''.$arr['start_date'].'\''; }
	if(!empty($arr['end_date']))  { $qry[] = 'end_date=\''.$arr['end_date'].'\''; }
	if(!empty($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if(!empty($arr['bool_allday']))  { $qry[] = 'bool_allday=\''.$arr['bool_allday'].'\''; }
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	$qry = implode(',',$qry);
	return $qry;
}

?>