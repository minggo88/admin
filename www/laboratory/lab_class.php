<?php
/*--------------------------------------------
Date :
Author : FirstGleam - http://www.firstgleam.com
comment :
History :
--------------------------------------------*/

class Lab extends BASIC
{
	function __construct(&$tpl)
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
		if(empty($_GET['loop_scale'])) {
			$config['loop_scale'] = 20;
		} else {
			$config['loop_scale'] = $_GET['loop_scale'];
		}
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
		
		$this->exchange = 'KRW';
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
    /*
    function loopTotal($mode='tpl')
    {}*/

    function loopJoinnsave($mode='tpl')
    {
		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_created_at'])) {
			$qry = ' && DATE(regdate) BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'count';
        $query['where'] = 'where regdate IS NOT NULL';
        $member_cnt = $this->dbcon->query($query,__FILE__,__LINE__);
        $this->tpl->assign('member_cnt',$member_cnt);

        $query = array();
        $query['table_name'] = 'js_member';
        $query['tool'] = 'count';
        $query['where'] = 'where regdate IS NOT NULL '.$qry.'';
        $member_search_cnt = $this->dbcon->query($query,__FILE__,__LINE__);
        $this->tpl->assign('member_search_cnt',$member_search_cnt);

		$members = '
			SELECT FROM_UNIXTIME(regdate, \'%Y-%c-%d\') AS date, COUNT(userno) AS member_cnt
			FROM js_member
			WHERE regdate IS NOT NULL GROUP BY FROM_UNIXTIME(regdate, \'%Y-%c-%d\')';

		// $chargings = '
		// 	SELECT FROM_UNIXTIME(regdate, \'%Y-%c-%d\') AS date, SUM(scc) AS scc
		// 	FROM js_buysmartcoin
		// 	WHERE regdate IS NOT NULL GROUP BY FROM_UNIXTIME(regdate, \'%Y-%c-%d\')';

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_created_at'])) {
			$qry = 'D.date BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = 'D.date BETWEEN \''.$_REQUEST['start_created_at'].'\' AND \''.$_REQUEST['end_created_at'].'\' ';
		}

		$query = array();
		// $query['table_name'] = 'js_dates AS D left join ('.$members.') AS M
		// 	on D.date = M.date left join ('.$chargings.') AS C
		// 	on D.date = C.date';
		$query['table_name'] = 'js_dates AS D left join ('.$members.') AS M on D.date = M.date';
		$query['tool'] = 'select';
		// $query['fields'] = 'D.date,
		// 	M.member_cnt,
		// 	C.scc AS charging';
		$query['fields'] = 'D.date,
			M.member_cnt';
		$query['where'] = 'where '.$qry.' ORDER BY D.date ASC';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);

		//var_dump($query); exit;

		$total_charging = 0;
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			//$row['avg'] = round($row['count'] / $bidding_cnt * 100, 2);
			$total_charging = $total_charging + $row['charging'];
			$loop[] = $row;
		}

		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_joinnsave',$loop);
			$this->tpl->assign('total_charging',$total_charging);
        }
    }

    function loopHouravg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && published_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL';
        $bidding_hour_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_hour_cnt',$bidding_hour_cnt);

        $query = array();
        $query['table_name'] = 'biddings';
        $query['tool'] = 'count';
		$query['where'] = 'where published_at IS NOT NULL '.$qry.'';
		$bidding_hour_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_hour_search_cnt',$bidding_hour_search_cnt);

        $query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'select';
		$query['fields'] = 'HOUR(published_at) as hour, count(id) as count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.' GROUP BY HOUR(published_at)';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_hour_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_hour_avg',$loop);
        }
	}

	// Ver 4.1
    function loopNewhouravg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && CSP.created_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = ' && DATE(CSP.created_at)  BETWEEN \''.$_REQUEST['start_published_at'].'\' AND \''.$_REQUEST['end_published_at'].'\'';

		}

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'where CSP.created_at IS NOT NULL';
        $bidding_hour_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_hour_cnt',$bidding_hour_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
        $query['tool'] = 'count';
		$query['where'] = 'where CSP.created_at IS NOT NULL '.$qry.'';
		$bidding_hour_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_hour_search_cnt',$bidding_hour_search_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'select';
		$query['fields'] = 'HOUR(CSP.created_at) as hour, count(CSP.id) as count';
        $query['where'] = 'where CSP.created_at IS NOT NULL '.$qry.' GROUP BY HOUR(CSP.created_at)';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_hour_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_hour_avg',$loop);
        }
    }

    function loopWeekavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && published_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL';
        $bidding_week_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_week_cnt',$bidding_week_cnt);

        $query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.'';
        $bidding_week_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_week_search_cnt',$bidding_week_search_cnt);

        $query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'select';
        $query['fields'] = '
            CASE WEEKDAY(published_at)
            WHEN 0 THEN \'월요일\'
            WHEN 1 THEN \'화요일\'
            WHEN 2 THEN \'수요일\'
            WHEN 3 THEN \'목요일\'
            WHEN 4 THEN \'금요일\'
            WHEN 5 THEN \'토요일\'
            WHEN 6 THEN \'일요일\'
            ELSE NULL
            END AS day, count(id) as count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.' GROUP BY WEEKDAY(published_at)';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_week_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_week_avg',$loop);
        }
	}

	// Ver 4.1
    function loopNewweekavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && CSP.created_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = ' && DATE(CSP.created_at)  BETWEEN \''.$_REQUEST['start_published_at'].'\' AND \''.$_REQUEST['end_published_at'].'\'';
		}

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL';
        $bidding_week_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_week_cnt',$bidding_week_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL '.$qry.'';
        $bidding_week_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_week_search_cnt',$bidding_week_search_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
		LEFT JOIN customer_study_publishes AS CSP
			ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'select';
        $query['fields'] = '
            CASE WEEKDAY(CSP.created_at)
            WHEN 0 THEN \'월요일\'
            WHEN 1 THEN \'화요일\'
            WHEN 2 THEN \'수요일\'
            WHEN 3 THEN \'목요일\'
            WHEN 4 THEN \'금요일\'
            WHEN 5 THEN \'토요일\'
            WHEN 6 THEN \'일요일\'
            ELSE NULL
            END AS day, count(CSP.id) as count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL '.$qry.' GROUP BY WEEKDAY(CSP.created_at)';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_week_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_week_avg',$loop);
        }
	}

    function loopLocationavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && published_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);

        $query = array();
		$query['table_name'] = 'biddings AS B left join locations AS L
			on B.location_id = L.id';
		$query['tool'] = 'select';
        $query['fields'] = 'L.name, count(B.id) as count';
        $query['where'] = 'where B.published_at IS NOT NULL '.$qry.' && L.pid=1 GROUP BY B.location_id';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		//var_dump($query);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_location_avg',$loop);
        }
    }

	// Ver 4.1
    function loopNewlocationavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && CSP.created_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = ' && DATE(CSP.created_at)  BETWEEN \''.$_REQUEST['start_published_at'].'\' AND \''.$_REQUEST['end_published_at'].'\'';
		}

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id
			LEFT JOIN locations AS L
				ON CS.location_id = L.id';
		$query['tool'] = 'select';
        $query['fields'] = 'L.name, count(CS.id) as count';
        $query['where'] = 'where CSP.created_at IS NOT NULL '.$qry.' && L.pid=1 GROUP BY CS.location_id';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_location_avg',$loop);
        }
	}

    function loopAgeavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && published_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);

        $query = array();
		$query['table_name'] = 'v_biddings';
		$query['tool'] = 'select';
		$query['fields'] = 'compute_template_age(birthdate) as age, count(id) as count';
		$query['where'] = 'where published_at IS NOT NULL '.$qry.' GROUP BY compute_template_age(birthdate)';

		/*
		 * compute_template_age 내장 함수를 접근할수 없을경우
			select age, count(id) as count
			from
			(
				select distinct B.id, T.age
				from v_biddings AS B
					inner join v_bidding_inquiries AS BI
						on B.id = BI.bidding_id
					inner join study_inquiry_templates as SIT
						on BI.id = SIT.study_inquiry_id
					inner join templates as T
						on SIT.template_id = T.id
				where B.published_at is not null
			) as T
			group by age;
		*/

        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_age_avg',$loop);
        }
	}

	// Ver 4.1
    function loopNewageavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && CSP.created_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = ' && DATE(CSP.created_at)  BETWEEN \''.$_REQUEST['start_published_at'].'\' AND \''.$_REQUEST['end_published_at'].'\'';
		}

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id
			LEFT JOIN studies AS S
				ON CS.id = S.id';
		$query['tool'] = 'select';
		$query['fields'] = 'compute_template_age(S.birthdate) as age, count(S.id) as count';
		$query['where'] = 'where CSP.created_at IS NOT NULL '.$qry.' GROUP BY compute_template_age(S.birthdate)';

		/*
		 * compute_template_age 내장 함수를 접근할수 없을경우
			select age, count(id) as count
			from
			(
				select distinct B.id, T.age
				from v_biddings AS B
					inner join v_bidding_inquiries AS BI
						on B.id = BI.bidding_id
					inner join study_inquiry_templates as SIT
						on BI.id = SIT.study_inquiry_id
					inner join templates as T
						on SIT.template_id = T.id
				where B.published_at is not null
			) as T
			group by age;
		*/

        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_age_avg',$loop);
        }
    }

    function loopJobavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && published_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);


		$query = array();
		$query['table_name'] = 'biddings AS B left join jobs AS J
			on B.job_id = J.id';
		$query['tool'] = 'select';
		$query['fields'] = 'J.name, count(B.id) as count';
		$query['where'] = 'where B.published_at IS NOT NULL '.$qry.' GROUP BY B.job_id';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_job_avg',$loop);
        }
    }

	// Ver 4.1
    function loopNewjobavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && CSP.created_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = ' && DATE(CSP.created_at)  BETWEEN \''.$_REQUEST['start_published_at'].'\' AND \''.$_REQUEST['end_published_at'].'\'';
		}

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
		$query['where'] = 'WHERE CSP.created_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);


		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id
			INNER JOIN studies AS S
				ON CS.id = S.id';
		$query['tool'] = 'select';
		$query['fields'] = 'S.job_grade, count(CS.id) as count';
		$query['where'] = 'where CSP.created_at IS NOT NULL '.$qry.' GROUP BY S.job_grade';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_job_avg',$loop);
        }
    }

    function loopSexavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && published_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = $this->srchQry();
		}

		$query = array();
		$query['table_name'] = 'v_biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'v_biddings';
		$query['tool'] = 'count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);

		$query = array();
		$query['table_name'] = 'v_biddings';
		$query['tool'] = 'select';
        $query['fields'] = 'sex, count(id) as count';
        $query['where'] = 'where published_at IS NOT NULL '.$qry.' GROUP BY sex';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_sex_avg',$loop);
        }
	}

	// Ver 4.1
    function loopNewsexavg($mode='tpl')
    {
		global $dbcon_mrpv4;

		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");

		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));

		$time = time() - ( 86400 * $last_day);
		$last_date = date( "Y-m-d", $time );

		if(empty($_REQUEST['start_published_at'])) {
			$qry = ' && CSP.created_at BETWEEN \''.$last_date.'\' AND \''.$now_date.'\' ';
		} else {
			$qry = ' && DATE(CSP.created_at)  BETWEEN \''.$_REQUEST['start_published_at'].'\' AND \''.$_REQUEST['end_published_at'].'\'';
		}

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
        $query['where'] = 'WHERE CSP.created_at IS NOT NULL';
        $bidding_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_cnt',$bidding_cnt);

        $query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id';
		$query['tool'] = 'count';
		$query['where'] = 'WHERE CSP.created_at IS NOT NULL '.$qry.'';
        $bidding_search_cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
        $this->tpl->assign('bidding_search_cnt',$bidding_search_cnt);

		$query = array();
		$query['table_name'] = 'customer_studies AS CS
			LEFT JOIN customer_study_publishes AS CSP
				ON CS.id = CSP.customer_study_id
			INNER JOIN studies AS S
				ON CS.id = S.id';
		$query['tool'] = 'select';
        $query['fields'] = 'S.sex, count(S.id) as count';
        $query['where'] = 'where CSP.created_at IS NOT NULL '.$qry.' GROUP BY S.sex';
        $result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
            $row['avg'] = round($row['count'] / $bidding_search_cnt * 100, 2);
			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_sex_avg',$loop);
        }
	}

    function loopSellableSections($mode='tpl')
    {
		global $dbcon_mrpv4;

		$fontcolor = array('#4a4a49','#4a4a47','#58574b','#8d8958','#faf293','#f9f461','#f8f2ac','#f7f3be','#fbed05','#fbf205');

		$query = array();
		$query['table_name'] = 'categories';
		$query['tool'] = 'select';
		$query['fields'] = 'id,name';
		$query['where'] = 'where code NOT LIKE \'temp%\' order by id asc';
		$result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop_categories = array();
		for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
			$loop_categories[] = $row;

			$query = array();
			$query['table_name'] = 'insurers AS I left join companies AS C
				on I.id = C.id';
			$query['tool'] = 'select';
			$query['fields'] = 'C.id,C.name';
			$query['where'] = 'where 1 order by C.name ASC';
			$result2 = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
			$loop_insurers = &$loop_categories[$i]['loop_insurers'];
			for($j = 0 ; $row2 = mysqli_fetch_assoc($result2) ; $j++) {

				//3단계 레벨 카테고리
				$query = array();
				$query['table_name'] = 'v_planner_sellable_sections AS PSS left join v_planner_balances AS PB
					on PSS.planner_id = PB.planner_id left join v_enumerations AS E
					on E.group_code = \'COIN\' && E.code=\'DEFICIT_STANDARD\' left join categories AS C
					on C.id = PSS.category_id';
				$query['tool'] = 'count';
				$query['where'] = 'where PB.coin >= E.value && PSS.insurer_id = \''.$row2['id'].'\' && PSS.category_id = \''.$row['id'].'\'';
				$cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);

				$row2['count'] = $cnt;
				$row2['fontcolor'] = $fontcolor[$cnt % 10];

				$loop_insurers[$j] = $row2;

			}
		}
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop_categories;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_sellable_section',$loop_categories);
        }
	}

    function loopInquiredSections($mode='tpl')
    {
		global $dbcon_mrpv4;

		$fontcolor = array('#4a4a49','#4a4a47','#58574b','#8d8958','#faf293','#f9f461','#f8f2ac','#f7f3be','#fbed05','#fbf205');

		$query = array();
		$query['table_name'] = 'categories';
		$query['tool'] = 'select';
		$query['fields'] = 'id,name';
		$query['where'] = 'where code NOT LIKE \'temp%\' order by id asc';
		$result = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
		$loop_categories = array();
		for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
			$loop_categories[] = $row;

			$query = array();
			$query['table_name'] = 'insurers AS I left join companies AS C
				on I.id = C.id';
			$query['tool'] = 'select';
			$query['fields'] = 'C.id,C.name';
			$query['where'] = 'where 1 order by C.name ASC';
			$result2 = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
			$loop_insurers = &$loop_categories[$i]['loop_insurers'];
			for($j = 0 ; $row2 = mysqli_fetch_assoc($result2) ; $j++) {

				//3단계 레벨 카테고리
				/*
				// SECTIONS
				$sections = DB::table("study_inquiry_templates AS SIT")
					->join("v_template_sections AS TS", "SIT.template_id", "=", "TS.template_id")
					->groupBy("TS.category_id", "TS.insurer_id")
					->select
					(
						"TS.category_id",
						"TS.insurer_id",
						DB::raw("COUNT(SIT.id) AS count")
					)
					->get();


				// SYMMETRIES
				$symmetries = DB::table("v_planner_sellable_sections AS PSS")
					->select("PSS.insurer_id", "PSS.category_id")
					->distinct()
					->get();

				*/

				$query = array();
				$query['table_name'] = 'study_inquiry_templates AS SIT left join v_template_sections AS TS
					on SIT.template_id = TS.template_id left join categories AS C
					on C.id = TS.category_id';
				$query['tool'] = 'count';
				$query['where'] = 'where TS.insurer_id = \''.$row2['id'].'\' && TS.category_id = \''.$row['id'].'\'';
				$cnt = $dbcon_mrpv4->query($query,__FILE__,__LINE__);

				$row2['count'] = $cnt;
				if($cnt < 100) { $row2['fontcolor'] = '#4a4a49'; }
				elseif($cnt >= 100 && $cnt < 500 ) { $row2['fontcolor'] = '#58574b'; }
				elseif($cnt >= 500 && $cnt < 1000) { $row2['fontcolor'] = '#f8f2ac'; }
				elseif($cnt >= 1000 && $cnt < 10000) { $row2['fontcolor'] = '#fbf205'; }

				// 적립금이 없는 설계사
				$query = array();
				$query['table_name'] = 'v_planner_sellable_sections';
				$query['tool'] = 'count';
				$query['where'] = 'where insurer_id = \''.$row2['id'].'\' && category_id = \''.$row['id'].'\'';
				$planner_count = $dbcon_mrpv4->query($query,__FILE__,__LINE__);
				if($cnt > 0) $row2['planner'] = 'O';
				else $row2['planner'] = 'X';

				$loop_insurers[$j] = $row2;

			}
		}
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop_categories;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_inquired_section',$loop_categories);
        }
	}

	function srchQry()
	{
		$arr = array();

        if(!empty($_REQUEST['start_published_at'])) {
			$arr[] = 'FROM_UNIXTIME(regdate, \'%Y-%c-%d\') >= \''.$_REQUEST['start_published_at'].'\'';
		}
		if(!empty($_REQUEST['end_published_at'])) {
            $arr[] = 'FROM_UNIXTIME(regdate, \'%Y-%c-%d\') < DATE(\''.$_REQUEST['end_published_at'].'\' + INTERVAL 1 DAY)';
		}

        if(!empty($_REQUEST['start_created_at']) && !empty($_REQUEST['end_created_at'])) {
            $arr[] = 'FROM_UNIXTIME(regdate, \'%Y-%c-%d\') BETWEEN \''.$_REQUEST['start_created_at'].'\' AND \''.$_REQUEST['end_created_at'].'\'';
		}

        if(!empty($_REQUEST['start_date']) && !empty($_REQUEST['end_date'])) {
            $arr[] = 'FROM_UNIXTIME(regdate, \'%Y-%c-%d\') BETWEEN \''.$_REQUEST['start_date'].'\' AND \''.$_REQUEST['end_date'].'\'';
		}

		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' && ',$arr).') ' : '';
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

		// Trade Status
		/*
		function tradeState()
		{
			// 거래소 USD 현황
			// 거래소 USD 금일입금
			$query = array();
			$query['table_name'] = 'js_trade_wallet_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(amount)';
			$query['where'] = 'WHERE symbol=\'USD\' AND txn_type=\'R\' AND status=\'D\' AND regdate like concat(curdate(),\'%\') ';
			$today_total_trade_krw_deposit = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('today_total_trade_krw_deposit',$today_total_trade_krw_deposit);

			// 거래소 USD 총 입금액
			$query = array();
			$query['table_name'] = 'js_trade_wallet_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(amount)';
			$query['where'] = 'WHERE symbol=\'USD\' AND txn_type=\'R\' AND status=\'D\' ';
			$total_trade_krw_deposit = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('total_trade_krw_deposit',$total_trade_krw_deposit);


			// 거래소 USD 금일출금
			$query = array();
			$query['table_name'] = 'js_trade_wallet_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(amount)';
			$query['where'] = 'WHERE symbol=\'USD\' AND txn_type=\'W\' AND status=\'D\' AND txndate like concat(curdate(),\'%\') ';
			$today_total_trade_krw_withdraw = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('today_total_trade_krw_withdraw',$today_total_trade_krw_withdraw);

			// 거래소 USD 총 출금액
			$query = array();
			$query['table_name'] = 'js_trade_wallet_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(amount)';
			$query['where'] = 'WHERE symbol=\'USD\' AND txn_type=\'W\' AND status=\'D\' ';
			$total_trade_krw_withdraw = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('total_trade_krw_withdraw',$total_trade_krw_withdraw);

			$total_trade_income = $total_trade_krw_deposit - $total_trade_krw_withdraw;
			$this->tpl->assign('total_trade_income',$total_trade_income);

			// 거래소 코인 현황
			// [Todo] 상장된 코인을 가져와 loop돌린다.

			// GWS 지갑의 전체 잔액 cnt_total_trade_gws
			// SELECT SUM(confirmed) FROM js_trade_wallet WHERE symbol='GWS';
			$query = array();
			$query['table_name'] = 'js_trade_wallet';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(confirmed)';
			$query['where'] = 'WHERE symbol=\'GWS\'';
			$cnt_total_trade_gws = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_gws',$cnt_total_trade_gws);

			// GWS 미체결 매도 총량 cnt_total_trade_unsell
			$query = array();
			$query['table_name'] = 'js_trade_gwskrw_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt_total_trade_unsell = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_unsell',$cnt_total_trade_unsell);

			// GWS 회사분 총량;
			$query['table_name'] = 'js_trade_wallet';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(confirmed)';
			$query['where'] = 'WHERE userno = 3257 and symbol=\'GWS\'';
			$cnt_total_trade_company_gws = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_company_gws',$cnt_total_trade_company_gws);

			// GWS 회사분 미체결 매도 총량 cnt_total_trade_company_unsell
			$query = array();
			$query['table_name'] = 'js_trade_gwskrw_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE userno = 3257 and status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt_total_trade_company_unsell = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_company_unsell',$cnt_total_trade_company_unsell);

			// GWS 회원분 총량;
			$query['table_name'] = 'js_trade_wallet';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(confirmed)';
			$query['where'] = 'WHERE userno <> 3257 and symbol=\'GWS\'';
			$cnt_total_trade_member_gws = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_member_gws',$cnt_total_trade_member_gws);

			// GWS 회원분 미체결 매도 총량 cnt_total_trade_member_unsell
			$query = array();
			$query['table_name'] = 'js_trade_gwskrw_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE userno <> 3257 and status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt_total_trade_member_unsell = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_member_unsell',$cnt_total_trade_member_unsell);

			// GWS 매도 총량 cnt_total_trade_sell
			// SELECT SUM(volume) FROM js_trade_gwskrw_txn;
			$query = array();
			$query['table_name'] = 'js_trade_gwskrw_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE 1';
			$cnt_total_trade_sell = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_sell',$cnt_total_trade_sell);

			// 오늘 매도 총량
			$query = array();
			$query['table_name'] = 'js_trade_gwskrw_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE date_format(time_traded,\'%Y-%m-%d\')=date_format(now(),\'%Y-%m-%d\')';
			$cnt_today_trade_sell = $this->dbcon->query($query,__FILE__,__LINE__);
			// var_dump($query); exit;
			$this->tpl->assign('cnt_today_trade_sell',$cnt_today_trade_sell);

			// GWS 매수 총량 cnt_total_trade_buy
			// SELECT SUM(volume) FROM js_trade_gwskrw_txn;
			$query['table_name'] = 'js_trade_gwskrw_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE 1';
			$cnt_total_trade_buy = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_buy',$cnt_total_trade_buy);

			// 오늘  매수 총량
			$query = array();
			$query['table_name'] = 'js_trade_gwskrw_txn';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE date_format(time_traded,\'%Y-%m-%d\')=date_format(now(),\'%Y-%m-%d\')';
			$cnt_today_trade_buy = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_today_trade_buy',$cnt_today_trade_buy);


			// GWS 매도 대기량 cnt_total_trade_sell_wait
			// SELECT SUM(volume) FROM js_trade_gwskrw_quote WHERE trading_type='S';
			$query['table_name'] = 'js_trade_gwskrw_quote';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE trading_type=\'S\'';
			$cnt_total_trade_sell_wait = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_sell_wait',$cnt_total_trade_sell_wait);

			// GWS 매수 대기량 cnt_total_trade_buy_wait
			// SELECT SUM(volume) FROM js_trade_gwskrw_quote WHERE trading_type='B';
			$query['table_name'] = 'js_trade_gwskrw_quote';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE trading_type=\'B\'';
			$cnt_total_trade_buy_wait = $this->dbcon->query($query,__FILE__,__LINE__);
			$this->tpl->assign('cnt_total_trade_buy_wait',$cnt_total_trade_buy_wait);
		}*/

		function querySelectTradeCurrency() {
			$query = array();
			$query['table_name'] = 'js_trade_currency';
			$query['tool'] = 'select';
			$query['fields'] = 'symbol, name, color';
			$query['where'] = 'where symbol<>exchange AND active="Y" ORDER BY sortno ASC';
			return $this->dbcon->query($query,__FILE__,__LINE__);
		}

		function loopTradeState()
		{

			// 거래소 코인 현황
			// [Todo] 상장된 코인을 가져와 loop돌린다.
			// $query = array();
			// $query['table_name'] = 'js_trade_currency';
			// $query['tool'] = 'select';
			// $query['fields'] = 'symbol, name, color';
			// $query['where'] = 'where symbol<>\'USD\' AND active=\'Y\' ORDER BY sortno ASC';
			// $result = $this->dbcon->query($query,__FILE__,__LINE__);
			$result = $this->querySelectTradeCurrency();
			$loop = array();
			while ($row = mysqli_fetch_assoc($result)) {
				$row['cnt_total_trade'] = $this->cnt_total_trade($row['symbol']); // 지갑잔액
				$row['cnt_total_trade_unsell'] = $this->cnt_total_trade_unsell($row['symbol']); // 미체결 매도 총량
				$row['cnt_total_exchange'] = $this->cnt_total_exchange($row['symbol']); // 미체결 매도 총량
				$row['cnt_total_trade_company'] = $this->cnt_total_trade_company($row['symbol']); // GWS 회사분 총량;
				$row['cnt_total_trade_company_unsell'] = $this->cnt_total_trade_company_unsell($row['symbol']); // GWS 회사분 미체결 매도 총량
				$row['cnt_total_trade_member'] = $this->cnt_total_trade_member($row['symbol']); // GWS 회원분 총량;
				$row['cnt_total_trade_member_unsell'] = $this->cnt_total_trade_member_unsell($row['symbol']); // GWS 회원분 미체결 매도 총량
				$row['cnt_total_trade_sell_wait'] = $this->cnt_total_trade_sell_wait($row['symbol']); // GWS 매도 대기량
				$row['cnt_total_trade_buy_wait'] = $this->cnt_total_trade_buy_wait($row['symbol']); // GWS 매수 대기량
				$loop[] = $row;
			}
			$this->tpl->assign('loop_tradestate',$loop);

		}

		public function cnt_total_trade($symbol) {
			// $symbol 지갑의 전체 잔액 cnt_total_trade
			// SELECT SUM(confirmed) FROM js_trade_wallet WHERE symbol='GWS';
			$query = array();
			$query['table_name'] = 'js_exchange_wallet';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(confirmed)';
			$query['where'] = 'WHERE symbol=\''.$symbol.'\'';
			$cnt_total_trade = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade;
		}

		public function cnt_total_trade_unsell($symbol) {
			// GWS 미체결 매도 총량 cnt_total_trade_unsell
			$query = array();
			$query['table_name'] = 'js_trade_'.strtolower($symbol).strtolower($this->exchange).'_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt_total_trade_unsell = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_unsell;
		}

		public function cnt_total_exchange($symbol) {
			// 교환소 매도 잔량
			if($symbol<>'BDC'){return 0;}
			$query = array();
			$query['table_name'] = 'js_trade_'.strtolower($symbol).strtolower($this->exchange).'_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt;
		}

		public function cnt_total_trade_company($symbol) {
			// GWS 회사분 총량;
			$query['table_name'] = 'js_exchange_wallet';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(confirmed)';
			$query['where'] = 'WHERE userno = 1 and symbol=\''.$symbol.'\'';
			$cnt_total_trade_company = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_company;
		}

		public function cnt_total_trade_company_unsell($symbol) {
			// GWS 회사분 미체결 매도 총량 cnt_total_trade_company_unsell
			$query = array();
			$query['table_name'] = 'js_trade_'.strtolower($symbol).strtolower($this->exchange).'_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE userno = 1 and status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt_total_trade_company_unsell = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_company_unsell;
		}

		public function cnt_total_trade_member($symbol) {
			// GWS 회원분 총량;
			$query['table_name'] = 'js_exchange_wallet';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(confirmed)';
			$query['where'] = 'WHERE userno <> 1 and symbol=\''.$symbol.'\'';
			$cnt_total_trade_member = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_member;
		}

		public function cnt_total_trade_member_unsell($symbol) {
			// GWS 회원분 미체결 매도 총량 cnt_total_trade_member_unsell
			$query = array();
			$query['table_name'] = 'js_trade_'.strtolower($symbol).strtolower($this->exchange).'_order';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume_remain)';
			$query['where'] = 'WHERE userno <> 1 and status IN (\'O\',\'T\') AND trading_type = \'S\'';
			$cnt_total_trade_member_unsell = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_member_unsell;
		}

		public function cnt_total_trade_sell_wait($symbol) {
			// GWS 매도 대기량 cnt_total_trade_sell_wait
			$query['table_name'] = 'js_trade_'.strtolower($symbol).strtolower($this->exchange).'_quote';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE trading_type=\'S\'';
			$cnt_total_trade_sell_wait = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_sell_wait;
		}

		public function cnt_total_trade_buy_wait($symbol) {
			// GWS 매수 대기량 cnt_total_trade_buy_wait
			$query['table_name'] = 'js_trade_'.strtolower($symbol).strtolower($this->exchange).'_quote';
			$query['tool'] = 'select_one';
			$query['fields'] = 'sum(volume)';
			$query['where'] = 'WHERE trading_type=\'B\'';
			$cnt_total_trade_buy_wait = $this->dbcon->query($query,__FILE__,__LINE__);
			return $cnt_total_trade_buy_wait;
		}

	private $var_not_in_member ="'walletmanager','itsddan@gmail.com','ara_manager'";

	// 금일 회원 현황 데이터 생성 (10분 캐시)
	function genDataTotalNowState() {
		$file = __dir__.'/gentime.txt';
		$gen_time = file_get_contents($file);
		$cnt = $this->dbcon->query_one("select count(*) from `js_state_today_member_balance`");
		if(!$gen_time || !$cnt || time() - $gen_time >= 60*10) {
			$this->dbcon->query("TRUNCATE `js_state_today_member_balance`");
			$sql = 'INSERT INTO js_state_today_member_balance
			SELECT
				userno, userid, name, amount_krw_in, amount_krw_out,
				amount_gws_balance, amount_gws_trade, amount_gws_balance + amount_gws_trade AS amount_gws_total, cnt_gws_trade, amount_gws_sell_regist, amount_gws_sell_success,
				amount_htc_balance, amount_htc_trade, amount_htc_balance + amount_htc_trade AS amount_htc_total, cnt_htc_trade, amount_htc_sell_regist, amount_htc_sell_success,
				amount_bdc_balance, amount_bdc_trade, amount_bdc_balance + amount_bdc_trade AS amount_bdc_total, cnt_bdc_trade, amount_bdc_sell_regist, amount_bdc_sell_success,
				amount_htp_balance,
				cnt_gws_trade + cnt_htc_trade + cnt_bdc_trade AS cnt_total_trade, mobile
			FROM (
				select jm.userno, jm.userid, jm.name, jm.mobile,
				(SELECT TRIM(IFNULL(SUM(amount), 0))+0 FROM js_exchange_wallet_txn WHERE userno = jm.userno and symbol="USD" and txn_type="R") AS amount_krw_in, -- USD 입금액
				(SELECT TRIM(IFNULL(SUM(amount), 0))+0 FROM js_exchange_wallet_txn WHERE userno = jm.userno and symbol="USD" and txn_type="W") AS amount_krw_out, -- USD 출금액
				(SELECT TRIM(IFNULL(SUM(confirmed), 0))+0 FROM js_exchange_wallet WHERE userno = jm.userno and symbol="GWS") AS amount_gws_balance, -- GWS 잔액
				-- (SELECT TRIM(IFNULL(SUM(volume_remain), 0))+0 FROM js_trade_gwskrw_order FORCE INDEX(userno) WHERE userno = jm.userno and status in ("O","T")) AS amount_gws_trade, -- GWS 매도중
				0 AS amount_gws_trade, -- GWS 매도중
				-- (SELECT TRIM(IFNULL(COUNT(*), 0))+0 FROM js_trade_gwskrw_order FORCE INDEX(userno) WHERE userno = jm.userno and status in ("T", "D")) AS cnt_gws_trade, -- GWS 매매수
				0 AS cnt_gws_trade, -- GWS 매매수
				-- (SELECT TRIM(IFNULL(SUM(volume),0))+0 FROM js_trade_gwskrw_order FORCE INDEX(userno) WHERE userno=jm.userno AND trading_type="S" AND status IN("O","T")) amount_gws_sell_regist, -- GWS 현재 매도등록량
				0 amount_gws_sell_regist,
				-- (SELECT TRIM(IFNULL(SUM(volume-volume_remain),0))+0 FROM js_trade_gwskrw_order FORCE INDEX(userno) WHERE userno=jm.userno AND trading_type="S" AND status IN("T","D")) amount_gws_sell_success, -- GWS 전체 매도량
				0 amount_gws_sell_success,
				-- (SELECT TRIM(IFNULL(SUM(confirmed), 0))+0 FROM js_exchange_wallet WHERE userno = jm.userno and symbol="HTC") AS amount_htc_balance, -- HTC 잔액
				0 amount_htc_balance, 
				-- (SELECT TRIM(IFNULL(SUM(volume_remain), 0))+0 FROM js_trade_htckrw_order FORCE INDEX(userno) WHERE userno = jm.userno and status in ("O","T")) AS amount_htc_trade, -- HTC 매도중
				0 amount_htc_trade,
				-- (SELECT TRIM(IFNULL(COUNT(*), 0))+0 FROM js_trade_htckrw_order FORCE INDEX(userno) WHERE userno = jm.userno and status in ("T", "D")) AS cnt_htc_trade, -- GWS 매매수
				0 cnt_htc_trade,
				-- (SELECT TRIM(IFNULL(SUM(volume),0))+0 FROM js_trade_htckrw_order FORCE INDEX(userno) WHERE userno=jm.userno AND trading_type="S" AND status IN("O","T")) amount_htc_sell_regist, -- HTC 현재 매도등록량
				0 amount_htc_sell_regist,
				-- (SELECT TRIM(IFNULL(SUM(volume-volume_remain),0))+0 FROM js_trade_htckrw_order FORCE INDEX(userno) WHERE userno=jm.userno AND trading_type="S" AND status IN("T","D")) amount_htc_sell_success, -- HTC 전체 매도량
				0 amount_htc_sell_success, 
				(SELECT TRIM(IFNULL(SUM(confirmed), 0))+0 FROM js_exchange_wallet WHERE userno = jm.userno and symbol="BDC") AS amount_bdc_balance, -- BDC 잔액
				0 amount_bdc_balance,
				-- (SELECT TRIM(IFNULL(SUM(volume_remain), 0))+0 FROM js_trade_bdckrw_order FORCE INDEX(userno) WHERE userno = jm.userno and status in ("O","T")) AS amount_bdc_trade,  -- BDC 매도중
				0 amount_bdc_trade,
				-- (SELECT TRIM(IFNULL(COUNT(*), 0))+0 FROM js_trade_bdckrw_order FORCE INDEX(userno) WHERE userno = jm.userno and status in ("T", "D")) AS cnt_bdc_trade, -- GWS 매매수
				0 cnt_bdc_trade,
				-- (SELECT TRIM(IFNULL(SUM(volume),0))+0 FROM js_trade_bdckrw_order FORCE INDEX(userno) WHERE userno=jm.userno AND trading_type="S" AND status IN("O","T")) amount_bdc_sell_regist, -- BDC 현재 매도등록량
				0 amount_bdc_sell_regist,
				-- (SELECT TRIM(IFNULL(SUM(volume-volume_remain),0))+0 FROM js_trade_bdckrw_order FORCE INDEX(userno) WHERE userno=jm.userno AND trading_type="S" AND status IN("T","D")) amount_bdc_sell_success, -- BDC 전체 매도량
				0 amount_bdc_sell_success,
				-- (SELECT TRIM(IFNULL(SUM(confirmed), 0))+0 FROM js_exchange_wallet WHERE userno = jm.userno and symbol="HTP") AS amount_htp_balance -- HTP 잔액
				0 amount_htp_balance
				FROM js_member AS jm
				WHERE userid NOT LIKE "%_w_%"
			) t';
			$this->dbcon->query($sql);
			$gen_time = time();
			file_put_contents($file, $gen_time);
		}
		return $gen_time;
	}

	//금일 회원들의 현황
	function loopNowState(){
		$userid 	= $_POST['userid'];
		$username 	= $_POST['username'];
		$mobile 	= $_POST['mobile'];
		$branch 	= $_POST['branch'];
		$start 		= $_POST['start'];
		$loop_scale = $_POST['loop_scale'] ? $_POST['loop_scale'] : $this->config['loop_scale'];

		if(!empty($userid)){
			$srch_userid =" AND userid like '%".$this->dbcon->escape($userid)."%'";
		}
		if(!empty($username)){
			$srch_username =" AND `name` like '%".$this->dbcon->escape($username)."%'";
		}
		if(!empty($mobile)){
			$srch_mobile =" AND mobile like '%".$this->dbcon->escape($mobile)."%'";
		}
		// $query = 'select *, (select ifnull(mobile,"") from js_member where userno=b.userno limit 1) mobile
		// from js_state_today_member_balance b
		// WHERE userno NOT IN (1,2,3) '.$srch_userid.$srch_username.$srch_mobile;
		// if($_GET['sort_target'] ) {
		// 	$query .= ' ORDER BY ';
		// 	for($i=0;$i<count($_GET['sort_target']);$i++) {
		// 		if($i>0) {
		// 			$query .= ', ';
		// 		}
		// 		$query .= ''.$this->dbcon->escape($_GET['sort_target'][$i]).'*1 '.$this->dbcon->escape($_GET['sort_method'][$i]);
		// 	}
		// } else {
		// 	$query .= ' ORDER BY userid ASC';
		// }
		// $query .= " LIMIT {$start}, {$loop_scale} ";
		// $list = $this->dbcon->query_all_object($query);


		$query = array();
		$query['table_name'] = ' js_state_today_member_balance b ';
		$query['tool'] = 'select';
		$query['fields'] = ' * ';
		$query['where'] = " WHERE userno NOT IN (1,2,3) {$srch_userid} {$srch_username} {$srch_mobile} ";
		if($_GET['sort_target'] ) {
			$query['order'] = ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['order'] .= ', ';
				}
				if($_GET['sort_target'][$i]=='name') {
					$query['order'] .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
				} else {
					$query['order'] .= ''.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
				}
			}
		} else {
			$query['order'] = ' ORDER BY userid ASC';
		}
		// var_dump($query); exit;
		$list = $this->bList($query,'loop','_lists',true);
		return $list;
	}

	//회원들의 현황 총합
	function loopTotalNowState(){

		// $query = 'select
		// (SELECT IFNULL(SUM(amount), 0) FROM js_exchange_wallet_txn WHERE userno not in (1,2,3) and symbol="USD" and txn_type="R") AS total_krw_in, -- USD 입금액
		// (SELECT IFNULL(SUM(amount), 0) FROM js_exchange_wallet_txn WHERE userno not in (1,2,3) and symbol="USD" and txn_type="W") AS total_krw_out, -- USD 출금액
		// (SELECT IFNULL(SUM(confirmed), 0) FROM js_exchange_wallet WHERE userno not in (1,2,3) and symbol="GWS") AS total_gws_balance, -- GWS 잔액
		// (SELECT IFNULL(SUM(volume_remain), 0) FROM js_trade_gwskrw_order WHERE userno not in (1,2,3) and status in ("O","T") AND trading_type="S") AS total_gws_trade, -- GWS 매도중
		// (SELECT IFNULL(COUNT(*), 0) FROM js_trade_gwskrw_order WHERE userno not in (1,2,3) and status in ("T", "D")) AS total_gws_trade_cnt, -- GWS 매매수

		// (SELECT IFNULL(SUM(confirmed), 0) FROM js_exchange_wallet WHERE symbol="HTC") AS total_htc_balance, -- HTC 잔액
		// (SELECT IFNULL(SUM(volume_remain), 0) FROM js_trade_htckrw_order WHERE userno not in (1,2,3) and status in ("O","T") AND trading_type="S") AS total_htc_trade, -- HTC 매도중
		// (SELECT IFNULL(COUNT(*), 0) FROM js_trade_htckrw_order WHERE userno not in (1,2,3) and status in ("T", "D")) AS total_htc_trade_cnt, -- HTC 매매수

		// (SELECT IFNULL(SUM(confirmed), 0) FROM js_exchange_wallet WHERE userno not in (1,2,3) and symbol="BDC") AS total_bdc_balance, -- BDC 잔액
		// (SELECT IFNULL(SUM(volume_remain), 0) FROM js_trade_bdckrw_order WHERE userno not in (1,2,3) and status in ("O","T") AND trading_type="S") AS total_bdc_trade,  -- BDC 매도중
		// (SELECT IFNULL(COUNT(*), 0) FROM js_trade_bdckrw_order WHERE userno not in (1,2,3) and status in ("T", "D")) AS total_bdc_trade_cnt, -- BDC 매매수
		// ""';
		$query = 'select
		(SELECT IFNULL(SUM(amount_krw_in), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_krw_in, -- USD 입금액
		(SELECT IFNULL(SUM(amount_krw_out), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_krw_out, -- USD 출금액

		(SELECT IFNULL(SUM(amount_gws_balance), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_gws_balance, -- GWS 잔액
		(SELECT IFNULL(SUM(amount_gws_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_gws_trade, -- GWS 매도중
		(SELECT IFNULL(SUM(amount_gws_total), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_gws_total, -- GWS 보유량
		(SELECT IFNULL(SUM(cnt_gws_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_gws_trade_cnt, -- GWS 매매수

		(SELECT IFNULL(SUM(amount_htc_balance), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_htc_balance, -- htc 잔액
		(SELECT IFNULL(SUM(amount_htc_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_htc_trade, -- htc 매도중
		(SELECT IFNULL(SUM(amount_htc_total), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_htc_total, -- htc 보유량
		(SELECT IFNULL(SUM(cnt_htc_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_htc_trade_cnt, -- htc 매매수

		(SELECT IFNULL(SUM(amount_bdc_balance), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_bdc_balance, -- bdc 잔액
		(SELECT IFNULL(SUM(amount_bdc_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_bdc_trade, -- bdc 매도중
		(SELECT IFNULL(SUM(amount_bdc_total), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_bdc_total, -- bdc 보유량
		(SELECT IFNULL(SUM(cnt_bdc_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_bdc_trade_cnt, -- bdc 매매수

		(SELECT IFNULL(SUM(cnt_total_trade), 0) FROM js_state_today_member_balance WHERE userno not in (1,2,3) ) AS total_cnt_trade, -- 전체 매매수
		""';
		// 	exit($query);

		$result = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);

		$this->tpl->assign('total_krw_in',$result['total_krw_in']);
		$this->tpl->assign('total_krw_out',$result['total_krw_out']);
		$this->tpl->assign('total_gws_trade_cnt',$result['total_gws_trade_cnt']);
		$this->tpl->assign('total_htc_trade_cnt',$result['total_htc_trade_cnt']);
		$this->tpl->assign('total_bdc_trade_cnt',$result['total_bdc_trade_cnt']);
		// $this->tpl->assign('total_cnt_trade',$result['total_gws_trade_cnt'] + $result['total_htc_trade_cnt'] + $result['total_bdc_trade_cnt']);
		$this->tpl->assign('total_cnt_trade',$result['total_cnt_trade']);
		// $this->tpl->assign('total_gws_total',$result['total_gws_balance']+$result['total_gws_trade']);
		$this->tpl->assign('total_gws_total',$result['total_gws_total']);
		$this->tpl->assign('total_gws_balance',$result['total_gws_balance']);
		$this->tpl->assign('total_gws_trade',$result['total_gws_trade']);
		// $this->tpl->assign('total_htc_total',$result['total_htc_balance']+$result['total_htc_trade']);
		$this->tpl->assign('total_htc_total',$result['total_htc_total']);
		$this->tpl->assign('total_htc_balance',$result['total_htc_balance']);
		$this->tpl->assign('total_htc_trade',$result['total_htc_trade']);
		// $this->tpl->assign('total_bdc_total',$result['total_bdc_balance']+$result['total_bdc_trade']);
		$this->tpl->assign('total_bdc_total',$result['total_bdc_total']);
		$this->tpl->assign('total_bdc_balance',$result['total_bdc_balance']);
		$this->tpl->assign('total_bdc_trade',$result['total_bdc_trade']);


	}

	function setTradeCurrency() {
		$this->tpl->assign('trade_currency', $this->getTradeCurrency());
	}
	function getTradeCurrency() {
		$result = $this->querySelectTradeCurrency();
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		return $loop;
	}

	function genSearchDateFormat($date, $type) {
		switch($type) {
			case 'daily': $sdate = date('Y-m-d', strtotime($date)); break;
			case 'monthly': $sdate = date('Y-m-d', strtotime($date)); break;
		}
	}

	/**
	 * 수익률 통계 페이지 데이터 생성
	 */
	function loopStatIncome () {

		$currency = $this->getTradeCurrency();

		$type = $_POST['type'] ? $_POST['type'] : 'daily';

		// $s_year = $_POST['s_year'] ? $_POST['s_year'] : '';
		// $s_month = $_POST['s_month'] ? $_POST['s_month'] : '';
		// $s_day = $_POST['s_day'] ? $_POST['s_day'] : '';
		// $sdate = ($s_year ? $s_year : '2021')."-".($s_month ? $s_month : '01')."-".($s_day ? $s_day : '01');
		// $edate = ($s_year ? $s_year : date('Y'))."-".($s_month ? $s_month : '12')."-".($s_day ? $s_day : '31');

		$sdate = $_POST['start_date'];
		$edate = $_POST['end_date'];

		// 기간별로 데이터를 수집합니다.
		$data = array();
		while($sdate<=$edate) {
			switch($type) {
				case 'daily':
					$sd = $sdate;
					$ed = $sdate;
					break;
				case 'monthly':
					$sd = $sdate;
					$ed = date('Y-m-31', strtotime($sdate));
					$ed = $ed<$edate ? $ed : $edate;
					break;
				case 'yearly':
					$sd = $sdate;
					$ed = date('Y-12-31', strtotime($sdate));
					$ed = $ed<$edate ? $ed : $edate;
					break;
			}
			$sql = "SELECT 0 total";
			foreach($currency as $c) {
				$c = (object) $c;
				$c->SYMBOL = $c->symbol;
				$c->symbol = strtolower($c->symbol);
				$exchange = strtolower($this->exchange);
				$sql.= ", (SELECT IFNULL(ROUND(SUM(fee)),0) income FROM `js_trade_{$c->symbol}{$exchange}_txn` WHERE '{$sd} 00:00:00'<=time_traded AND time_traded<='{$ed} 23:59:59') + (SELECT IFNULL(ROUND(SUM(fee)),0) income FROM `js_exchange_wallet_txn` WHERE symbol='{$c->SYMBOL}' AND txn_type NOT IN ('B') AND '{$sd} 00:00:00'<=txndate AND txndate<='{$ed} 23:59:59') AS {$c->SYMBOL}";
			}
			$r = $this->dbcon->query_fetch_array($sql);
			foreach($r as $key => $val) {
				if($key!='total') {
					$r['total'] += $val;
				}
			}
			switch($type) {
				case 'daily':
					$r['date'] = $sd;
					break;
				case 'monthly':
					$r['date'] = date('Y-m', strtotime($sd));
					break;
				case 'yearly':
					$r['date'] = date('Y', strtotime($sdate));
					break;
			}

			$data[] = $r;

			// 날짜 증가
			switch($type) {
				case 'daily':
					$sdate = date('Y-m-d', strtotime('+1 day', strtotime($sdate)));
					break;
				case 'monthly':
					$sdate = date('Y-m-01', strtotime('+1 month', strtotime($sdate)));
					break;
				case 'yearly':
					$sdate = date('Y-01-01', strtotime('+1 year', strtotime($sdate)));
					break;
			}

		}
		return $data;
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