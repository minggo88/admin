<?php
/*--------------------------------------------
Date : 
Author : Danny Hwang
comment : 
History : 
--------------------------------------------*/

class Holiday extends BASIC
{
	function Holiday(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_holiday';
		$config['query_func'] = 'holidayQuery';
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
		if(empty($_POST['idx'])) {
			jsonMsg(0);
		}
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
		$arr['hdate'] = strtotime($arr['hdate']);
		return $arr;
	}

	function del()
	{
		if(empty($_GET['idx'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where idx=\''.$_GET['idx'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function calendar()
	{
		//달력구현
		//오늘은 몇월이고 말일 몇일까지 인지 알아낸다.
		$cur_year = date('Y',time());
		$cur_month = date('n',time());
		$cur_day = date('j',time());
		$today = mktime(0,0,0,$cur_month,$cur_day,$cur_year);

		if(empty($_GET['year']) && empty($_GET['month'])) {
			$cur_date = time();
		}
		else {
			if(empty($_GET['year'])) {
				$year = $cur_year;
			}
			else {
				$year = $_GET['year'];
			}
			if(empty($_GET['month'])) {
				$month = $cur_month;
			}
			else {
				$month = $_GET['month'];
			}
			$cur_date = mktime(0,0,0,sprintf('%02d',$month),1,$year);
		}

		$this_year = date('Y',$cur_date); //해당 년
		$this_month = date('n',$cur_date); //해당 월
		$last_day = date('t',$cur_date); //해당월 말일

		$this->tpl->assign('year',$this_year);
		$this->tpl->assign('month',$this_month);

		//몇주로 구성되는가?
		$first_day_week = date('w',mktime(0,0,0,$this_month,01,$this_year));
		$last_day_week = date('w',mktime(0,0,0,$this_month,$last_day,$this_year));
		$gap_week = ceil(($first_day_week+$last_day)/7);

		$day_month = 1;
		$loop = array();
		for ($i = 0 ; $i < $gap_week ; $i++) {
			$loop[] = array('week'=>$i);
			$loop2 = &$loop[$i]['loop_day'];
			for ($j = 0; $j < 7 ; $j++) {
				$temp = array();
				if($i == 0 && $j < $first_day_week) {
					$temp['day_month'] = '';
					$temp['css_class'] = 'no_date';
					$temp['bool_button'] = 0;
				}
				else if($i == ($gap_week-1) && $j > $last_day_week) {
					$temp['day_month'] = '';
					$temp['css_class'] = 'no_date';
					$temp['bool_button'] = 0;
				}
				else {
					if($j == 0 || $j== 6) {
						$temp['day_title'] = '휴일';
						$temp['bool_button'] = 0;
					}
					else {
						$temp['bool_button'] = 1;
					}
					$temp['day_month'] = $day_month;
					$loop_date2 = $this_year.'-'.$this_month.'-'.$day_month;
					$loop_date = mktime(0,0,0,$this_month,$day_month,$this_year);
					$day_month = $day_month +1;


					$query = array();
					$query['table_name'] = $this->config['table_name'];
					$query['tool'] = 'count';
					$query['where'] = 'where FROM_UNIXTIME(hdate,\'%Y-%c-%e\')=\''.$loop_date2.'\'';
					$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
					if($cnt > 0) {
						$temp['bool_holiday'] = 1;
						$query['tool'] = 'row';
						$query['fields'] = 'idx,year,hdate,day_title';
						$row = $this->dbcon->query($query,__FILE__,__LINE__);
						$temp['idx'] = $row['idx'];
						$temp['year'] = $row['year'];
						$temp['hdate'] = $row['hdate'];
						$temp['day_title'] = $row['day_title'];
					}
					else {
						$temp['bool_holiday'] = 0;
					}
				}
				$loop2[] = $temp;
			}
		}
		$this->tpl->assign('loop_week',$loop);
	}

	function loopYear()
	{
		$this_year = date('Y');
		$start_year = $this_year;
		$end_year = $this_year+5;
		$loop = array();
		for ($i = $start_year ; $i <= $end_year ; $i++) {
			$loop[] = $i;
		}
		$this->tpl->assign('loop_year',$loop);
	}


	function srchQry()
	{
		$arr = array();
		//if(!empty($_GET['var'])) { $arr[] = 'var like \'%'.$_GET['var'].'%\' '; }
		//if(!empty($_GET['var'])) { $arr[] = 'var=\''.$_GET['var'].'\''; }
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($start=TRUE)
	{
		$arr = array();
		/*
		if($start) {
			if(empty($_GET['start'])) { $arr[] = 'start=0'; }
			else { $arr[] = 'start='.$_GET['start']; }
		}
		*/
		if(!empty($_GET['year'])) { $arr[] = 'year='.$_GET['year']; }
		if(!empty($_GET['month'])) { $arr[] = 'month='.$_GET['month']; }
		//if(!empty($_GET['var'])) { $arr[] = 'var='.$_GET['var']; }
		$ret = sizeof($arr) > 0 ? '&'.implode('&',$arr) : '';
		return $ret;
	}

}

function holidayQuery($arr)
{
	$qry = array();
	if(!empty($arr['idx']))  { $qry[] = 'idx=\''.$arr['idx'].'\''; }
	if(!empty($arr['year']))  { $qry[] = 'year=\''.$arr['year'].'\''; }
	if(!empty($arr['hdate']))  { $qry[] = 'hdate=\''.$arr['hdate'].'\''; }
	if(!empty($arr['day_title']))  { $qry[] = 'day_title=\''.$arr['day_title'].'\''; }
	return implode(',',$qry);
}

?>