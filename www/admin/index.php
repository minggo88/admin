<?php
/*--------------------------------------------
Date : 2012-08-27
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common_admin.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

class AdminMain
{
	function AdminMain(&$tpl)
	{
		$this->tpl = &$tpl;
	}


	function loopRevenue($mode='tpl') 
    {
		$now_date =  date("Y-m-d");
		$now_year = date("Y");
		$now_month = date("m");
		
		$last_day = date("t", mktime(0, 0, 1, $now_month, 1, $now_year));
		
		$time = time() - ( 86400 * 30 * 6 * $last_day); 
		$last_date = date( "Y-m-d", $time );

		$halfyear_month = '4';

		if(empty($_REQUEST['start_created_at'])) {
			$qry = '1 ';
		} else {
			$qry = 'D.date BETWEEN \''.$_REQUEST['start_created_at'].'\' AND \''.$_REQUEST['end_created_at'].'\' ';
		}

		//  BITCOIN
		$bitcoins = '
			SELECT FROM_UNIXTIME(regdate, \'%m-%d\') AS DATE, sum(amount) AS bitcoin, regdate
			FROM js_wallet_buycoin
			WHERE method=1
			GROUP BY FROM_UNIXTIME(regdate, \'%m-%d\')
			';

		//  ETHEREUM
		$ethereums = '
			SELECT FROM_UNIXTIME(regdate, \'%m-%d\') AS DATE, sum(amount) AS ethereum, regdate
			FROM js_wallet_buycoin
			WHERE method=2
			GROUP BY FROM_UNIXTIME(regdate, \'%m-%d\')
			';
		
		//  LIGHTCOIN
		$lightcoins = '
			SELECT FROM_UNIXTIME(regdate, \'%m-%d\') AS DATE, sum(amount) AS lightcoin, regdate
			FROM js_wallet_buycoin
			WHERE method=3
			GROUP BY FROM_UNIXTIME(regdate, \'%m-%d\')
			';
		
		//  USDOLLAR
		$dollars = '
			SELECT FROM_UNIXTIME(regdate, \'%m-%d\') AS DATE, sum(amount) AS dollar, regdate
			FROM js_wallet_buycoin
			WHERE method=4
			GROUP BY FROM_UNIXTIME(regdate, \'%m-%d\')
		';
		
		$query = array();
		$query['table_name'] = 'js_wallet_buycoin AS S
		 	LEFT JOIN ('.$bitcoins.') AS B
				ON FROM_UNIXTIME(S.regdate, \'%m-%d\') = FROM_UNIXTIME(B.regdate, \'%m-%d\')
			LEFT JOIN ('.$ethereums.') AS E
				ON FROM_UNIXTIME(S.regdate, \'%m-%d\') = FROM_UNIXTIME(E.regdate, \'%m-%d\')
			LEFT JOIN ('.$lightcoins.') AS L
				ON FROM_UNIXTIME(S.regdate, \'%m-%d\') = FROM_UNIXTIME(L.regdate, \'%m-%d\')
			LEFT JOIN ('.$dollars.') AS U
				ON FROM_UNIXTIME(S.regdate, \'%m-%d\') = FROM_UNIXTIME(U.regdate, \'%m-%d\')';
		$query['tool'] = 'select';
		$query['fields'] = '
			FROM_UNIXTIME(S.regdate, \'%m-%d\') AS date,
			B.DATE AS bitcoin_date,
			B.bitcoin,
			E.DATE AS ethereum_date,
			E.ethereum,
			L.DATE AS lightcoin_date,
			L.lightcoin,
			U.DATE AS dollar_date,
			U.dollar';
		$query['where'] = 'where '.$qry.' GROUP BY FROM_UNIXTIME(S.regdate, \'%m-%d\')';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);

		$total_bitcoin = 0;
		$total_ethereum = 0;
		$total_lightcoin = 0;
		$total_dollar = 0;
		$total_krw = 0;

		$total_rate_bitcoin = 0;
		$total_rate_ethereum = 0;
		$total_rate_lightcoin = 0;
		$total_rate_dollar = 0;

		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			
			$row['bitcoin'] = round($row['bitcoin'],8);
			$row['ethereum'] = round($row['ethereum'],8);
			$row['lightcoin'] = round($row['lightcoin'],8);
			$row['dollar'] = round($row['dollar'] / 1000,2);

			$row['total_krw'] = round(($row['bitcoin']*7300000) + ($row['ethereum'] * 400000) + ($row['lightcoin'] * 100000) + ($row['dollar'] * 1000 * 1000),0);

			$total_bitcoin = $total_bitcoin + $row['bitcoin'];
			$total_ethereum = $total_ethereum + $row['ethereum'];
			$total_lightcoin = $total_lightcoin + $row['lightcoin'];
			$total_dollar = $total_dollar + $row['dollar'] * 1000;

			// KRW Convert
			$total_krw = ($total_bitcoin * 7300000) + ($total_ethereum * 400000) + ($total_lightcoin * 100000) + ($total_dollar * 1000);

			// rate
			$total_rate_bitcoin = round($total_bitcoin * 7300000 / $total_krw * 100,0);
			$total_rate_ethereum = round($total_ethereum * 400000 / $total_krw * 100,0);
			$total_rate_lightcoin = round($total_lightcoin * 100000 / $total_krw * 100,0);
			$total_rate_dollar = round($total_dollar * 1000 / $total_krw * 100,0);

			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl

			$this->tpl->assign('loop_revenue',$loop);
			$this->tpl->assign('total_bitcoin',$total_bitcoin);
			$this->tpl->assign('total_ethereum',$total_ethereum);
			$this->tpl->assign('total_lightcoin',$total_lightcoin);
			$this->tpl->assign('total_dollar',$total_dollar);
			$this->tpl->assign('total_krw',$total_krw);

			$this->tpl->assign('total_rate_bitcoin',$total_rate_bitcoin);
			$this->tpl->assign('total_rate_ethereum',$total_rate_ethereum);
			$this->tpl->assign('total_rate_lightcoin',$total_rate_lightcoin);
			$this->tpl->assign('total_rate_dollar',$total_rate_dollar);


        }
	}

	function sendNotice() 
	{
		global $dbcon;
		$msg = strip_tags($_POST['msg']);
		$sql = "insert into js_wallet_message set ";
		$regtime = str_replace('.','',sprintf('%01.6f', array_sum(explode(' ',microtime())))); // 16?먮━
		$sql.= " reg_time='{$regtime}', ";
		$sql.= " receiver_walletno='', ";
		$sql.= " receiver_name='', ";
		$sql.= " sender_walletno='0', ";
		$sql.= " sender_name='Administrator', ";
		$sql.= " message='{$msg}', ";
		$sql.= " read_time='', ";
		$sql.= " relation_data='', ";
		$sql.= " message_type='N' ";
		$r = $dbcon->query($sql,__FILE__,__LINE__);
		if($r) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function loopCoins($mode='tpl')
	{
		global $dbcon;

		$query = "
			SELECT D.year, D.month,
				# coins
				C.chargings AS coin_chargings,
				C.inquiries AS coin_inquiries,
				C.assigns AS coin_assigns,
				C.comments AS coin_comments,
				C.choices AS coin_choices
			
			FROM 
			(
				SELECT YEAR(`date`) AS `year`, MONTH(`date`) AS `month`
				FROM dates
				WHERE `date` BETWEEN '2017-07-01' AND CURDATE()
				GROUP BY YEAR(`DATE`), MONTH(`DATE`)
			) AS D 
			
			# coins
			LEFT OUTER JOIN 
			(
				SELECT YEAR(CH.created_at) AS `year`, MONTH(CH.created_at) AS `month`,
					SUM(IF(C.source = 'coin_chargings', CH.value, 0)) AS chargings,
					SUM(IF(C.source = 'bidding_inquiries', CH.value, 0)) AS inquiries,
					SUM(IF(C.source = 'bidding_candidates', CH.value, 0)) AS assigns,
					SUM(IF(C.source = 'feeing_bbs_comments', CH.value, 0)) AS comments,
					SUM(IF(C.source = 'feeing_bbs_comment_choices', CH.value, 0)) AS choices
				FROM coin_histories AS CH
					INNER JOIN coins AS C
						ON CH.coin_id = C.id
					LEFT OUTER JOIN coin_chargings AS CC
						ON CH.source_id = CC.id AND C.source = 'coin_chargings'
				WHERE CC.id IS NULL OR CC.merchant_uid NOT LIKE 'mrpv3_%'
				GROUP BY YEAR(CH.created_at), MONTH(CH.created_at)
			) AS C 
				ON D.year = C.year AND D.month = C.month
			ORDER BY D.month ASC";
		$result = $dbcon->query($query,__FILE__,__LINE__);
		$sub_total_coin = 0;
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$row['sub_total_coin'] = round($row['coin_inquiries'] + $row['coin_assigns'] + $row['coin_comments'] + $row['coin_choices'],0);
			$row['coin_chargings'] = round($row['coin_chargings'],0);
			$loop[] = $row;
		}
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_coins',$loop);
		}
	}


	function loopSummaries($mode='tpl')
	{
		global $dbcon;

		$query = "
			SELECT D.year, D.month,
				# members
				M.customer_count,
				M.planner_count,
			
				# coins
				C.chargings AS coin_chargings,
				C.inquiries AS coin_inquiries,
				C.assigns AS coin_assigns,
				C.comments AS coin_comments,
				C.choices AS coin_choices,
			
				# biddings
				BP.count AS bidding_publish_count,
				BI.count AS bidding_inquiry_count,
				BW.count AS bidding_win_count,
			
				# feeing_comments
				FC.count AS comment_write_count,
				FCC.count AS comment_choice_count
			
			FROM 
			(
				SELECT YEAR(`date`) AS `year`, MONTH(`date`) AS `month`
				FROM dates
				WHERE `date` BETWEEN '2017-07-01' AND CURDATE()
				GROUP BY YEAR(`DATE`), MONTH(`DATE`)
			) AS D 
			
			#====================
			# JOIN STATEMENTS
			#====================
			# member joins
			LEFT OUTER JOIN 
			(
				SELECT YEAR(created_at) AS `year`, MONTH(created_at) AS `month`,
					COUNT(C.id) AS customer_count,
					COUNT(P.id) AS planner_count
				FROM members AS M
					LEFT OUTER JOIN customers AS C
						ON M.id = C.id
					LEFT OUTER JOIN planners AS P
						ON M.id = P.id
				GROUP BY YEAR(created_at), MONTH(created_at) 
			) AS M
				ON D.year = M.year AND D.month = M.month
			
			# coins
			LEFT OUTER JOIN 
			(
				SELECT YEAR(CH.created_at) AS `year`, MONTH(CH.created_at) AS `month`,
					SUM(IF(C.source = 'coin_chargings', CH.value, 0)) AS chargings,
					SUM(IF(C.source = 'bidding_inquiries', CH.value, 0)) AS inquiries,
					SUM(IF(C.source = 'bidding_candidates', CH.value, 0)) AS assigns,
					SUM(IF(C.source = 'feeing_bbs_comments', CH.value, 0)) AS comments,
					SUM(IF(C.source = 'feeing_bbs_comment_choices', CH.value, 0)) AS choices
				FROM coin_histories AS CH
					INNER JOIN coins AS C
						ON CH.coin_id = C.id
					LEFT OUTER JOIN coin_chargings AS CC
						ON CH.source_id = CC.id AND C.source = 'coin_chargings'
				WHERE CC.id IS NULL OR CC.merchant_uid NOT LIKE 'mrpv3_%'
				GROUP BY YEAR(CH.created_at), MONTH(CH.created_at)
			) AS C 
				ON D.year = C.year AND D.month = C.month
				
			#--------------------
			# BIDDING
			#--------------------
			# publish
			LEFT OUTER JOIN
			(
				SELECT YEAR(published_at) AS `year`, MONTH(published_at) AS `month`,
					COUNT(id) AS count
				FROM v_biddings
				WHERE published_at IS NOT NULL
				GROUP BY YEAR(published_at), MONTH(published_at)
			) AS BP
				ON D.year = BP.year AND D.month = BP.month
			
			# inquiries
			LEFT OUTER JOIN
			(
				SELECT YEAR(created_at) AS `year`, MONTH(created_at) AS `month`,
					COUNT(id) AS count
				FROM v_bidding_inquiries
				GROUP BY YEAR(created_at), MONTH(created_at)
			) AS BI
				ON D.year = BI.year AND D.month = BI.month
				
			# wins
			LEFT OUTER JOIN 
			(
				SELECT YEAR(NA.created_at) AS `year`, MONTH(NA.created_at) AS `month`,
					COUNT(NA.id) AS count
				FROM safety_call_number_assigns AS NA
					INNER JOIN safety_calls AS SC
						ON NA.safety_call_id = SC.id
				WHERE SC.source = 'bidding_candidates'
				GROUP BY YEAR(NA.created_at), MONTH(NA.created_at)
			) AS BW
				ON D.year = BW.year AND D.month = BW.month
			
			#--------------------
			# FEEING COMMENTS
			#--------------------
			# comments
			LEFT OUTER JOIN
			(
				SELECT YEAR(created_at) AS `year`, MONTH(created_at) AS `month`, 
					COUNT(id) AS count
				FROM feeing_bbs_comments
				GROUP BY YEAR(created_at), MONTH(created_at)
			) AS FC
				ON D.year = FC.year AND D.month = FC.month
		
			# chocies
			LEFT OUTER JOIN
			(
				SELECT YEAR(created_at) AS `year`, MONTH(created_at) AS `month`, 
					COUNT(id) AS count
				FROM feeing_bbs_comment_choices
				GROUP BY YEAR(created_at), MONTH(created_at)
			) AS FCC
				ON D.year = FCC.year AND D.month = FCC.month
			ORDER BY D.month ASC";
		$result = $dbcon->query($query,__FILE__,__LINE__);
		
		$total_coin_chargings = 0;
		$total_coin_inquiries = 0;
		$total_coin_assigns = 0;
		$total_coin_comments = 0;
		$total_coin_choices = 0;
		
		$sub_total_coin = 0;
		$total_coin = 0;
		
		// RATE
		$total_rate_inquiries = 0;
		$total_rate_assigns = 0;
		$total_rate_comments = 0;
		$total_rate_choices = 0;
		
		$total_bidding_inquiry_count = 0;
		$total_bidding_publish_count = 0;
		$total_bidding_win_count = 0;
		$total_matching_ctr = 0;
		$total_win_ctr = 0;

		$total_customer_count = 0;
		$total_planner_count = 0;

		$total_comment_write_count = 0;
		$total_comment_choice_count = 0;

		$total_inquiry_cpa = 0;
		$total_assign_cpa = 0;
		$total_win_cpa = 0;

		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$row['sub_total_coin'] = $row['coin_inquiries'] + $row['coin_assigns'] + $row['coin_comments'] + $row['coin_choices'];
			$total_coin_chargings = $total_coin_chargings + $row['coin_chargings'];
			$total_coin_inquiries = $total_coin_inquiries + $row['coin_inquiries'];
			$total_coin_assigns = $total_coin_assigns + $row['coin_assigns'];
			$total_coin_comments = $total_coin_comments + $row['coin_comments'];
			$total_coin_choices = $total_coin_choices + $row['coin_choices'];
			$total_coin = $total_coin + $row['sub_total_coin'];

			$total_bidding_inquiry_count = $total_bidding_inquiry_count + $row['bidding_inquiry_count'];
			$total_bidding_publish_count = $total_bidding_publish_count + $row['bidding_publish_count'];
			$total_bidding_win_count = $total_bidding_win_count + $row['bidding_win_count'];

			$row['matching_ctr'] = @round($row['bidding_publish_count'] / $row['bidding_inquiry_count'] * 100,0);
			$row['win_ctr'] = @round($row['bidding_win_count'] / $row['bidding_inquiry_count'] * 100,0);

			// CPA
			$row['inquiry_cpa'] = @round($row['coin_inquiries'] / $row['bidding_inquiry_count'], 0);
			$row['assign_cpa'] = @round($row['coin_inquiries'] / $row['bidding_publish_count'], 0);
			$row['win_cpa'] = @round($row['coin_inquiries'] / $row['bidding_win_count'], 0);
			
			$total_customer_count = $total_customer_count + $row['customer_count'];
			$total_planner_count = $total_planner_count + $row['planner_count'];
			
			$total_comment_write_count = $total_comment_write_count + $row['comment_write_count'];
			$total_comment_choice_count = $total_comment_choice_count + $row['comment_choice_count'];

			$loop[] = $row;
        }
		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			echo json_encode($ret);
		}
		else {//mode : tpl

			$total_matching_ctr = round($total_bidding_publish_count / $total_bidding_inquiry_count * 100,0);
			$total_win_ctr = round($total_bidding_win_count / $total_bidding_inquiry_count * 100,0);

			// RATE
			$total_rate_inquiries = round($total_coin_inquiries / $total_coin_chargings * 100, 2);
			$total_rate_assigns = round($total_coin_assigns / $total_coin_chargings * 100, 2);
			$total_rate_comments = round($total_coin_comments / $total_coin_chargings * 100, 2);
			$total_rate_choices = round($total_coin_choices / $total_coin_chargings * 100, 2);

			// CPA
			$total_inquiry_cpa = round($total_coin_inquiries / $total_bidding_inquiry_count, 0);
			$total_assign_cpa = round($total_coin_inquiries / $total_bidding_publish_count, 0);
			$total_win_cpa = round($total_coin_inquiries / $total_bidding_win_count, 0);
			
			$this->tpl->assign('loop_summaries',$loop);
			$this->tpl->assign('total_coin_chargings',$total_coin_chargings);
			$this->tpl->assign('total_coin_inquiries',$total_coin_inquiries);
			$this->tpl->assign('total_coin_assigns',$total_coin_assigns);
			$this->tpl->assign('total_coin_comments',$total_coin_comments);
			$this->tpl->assign('total_coin_choices',$total_coin_choices);
			$this->tpl->assign('total_coin',$total_coin);

			// RATE
			$this->tpl->assign('total_rate_inquiries',$total_rate_inquiries);
			$this->tpl->assign('total_rate_assigns',$total_rate_assigns);
			$this->tpl->assign('total_rate_comments',$total_rate_comments);
			$this->tpl->assign('total_rate_choices',$total_rate_choices);

			$this->tpl->assign('total_bidding_inquiry_count',$total_bidding_inquiry_count);
			$this->tpl->assign('total_bidding_publish_count',$total_bidding_publish_count);
			$this->tpl->assign('total_bidding_win_count',$total_bidding_win_count);
			$this->tpl->assign('total_matching_ctr',$total_matching_ctr);
			$this->tpl->assign('total_win_ctr',$total_win_ctr);

			$this->tpl->assign('total_customer_count',$total_customer_count);
			$this->tpl->assign('total_planner_count',$total_planner_count);
			
			$this->tpl->assign('total_comment_write_count',$total_comment_write_count);
			$this->tpl->assign('total_comment_choice_count',$total_comment_choice_count);

			// CPA
			$this->tpl->assign('total_inquiry_cpa',$total_inquiry_cpa);
			$this->tpl->assign('total_assign_cpa',$total_assign_cpa);
			$this->tpl->assign('total_win_cpa',$total_win_cpa);

        }
	}

	/**
	 * 매인페이지에 표시할 공지사항 글을 카테고리별로 템플릿 변수에 추가해둡니다.
	 * 템플릿에서 사용할때는 <!--{main_notice_카테고리코드}--> 처럼 사용합니다. 
	 * 
	 * 사용예: <!--{main_notice_보도자료}-->
	 * 
	 * @param mixed $bbscode 게시판 코드. 공지사항은 NOTICE 입니다.
	 * @param int $cnt 표시할 글 수. 기본 5개.
	 * 
	 * @return [type]
	 * 
	 */
	function setMainNotice($bbscode, $cnt = 5) {
		$category = $this->dbcon->query_one("SELECT bbs_category FROM js_bbs_info WHERE bbscode='{$this->dbcon->escape($bbscode)}' ");// 카태고리 추출
		$category = $category ? explode(',', $category) : array();
		foreach($category as $c) {
			// 공지글 전부
			$query = "SELECT * FROM js_bbs_main WHERE bbscode='{$this->dbcon->escape($bbscode)}' "; //  a : 공지글 , b: 일반글
			$r = $this->dbcon->query_list_array($query." AND division='a' AND category='{$this->dbcon->escape($c)}' ORDER BY idx DESC LIMIT $cnt ");// 카태고리별 공지글 5개 추출
			// 공지글 없으면 최근 글 5개
			if(count($r) < $cnt) {
				$cnt = $cnt - count($r);
				$query = "SELECT * FROM js_bbs_main WHERE bbscode='{$this->dbcon->escape($bbscode)}' "; // AND division='a'  a : 공지글 , b: 일반글
				$r2 = $this->dbcon->query_list_array($query." AND category='{$this->dbcon->escape($c)}'  ORDER BY idx DESC LIMIT $cnt "); // 카태고리별 공지글 
				$r2 ? $r = array_merge( $r, $r2)  : $r;
			}
			$this->tpl->assign('main_notice_'.$c, $r);
		}
	}
	

	//?뚯썝?꾪솴
	function memberState()
	{
		//금일 회원 가입중 이메일인증단계회원
		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'count';
		$query['where'] = "where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d') && bool_confirm_email = '1' ";
		$cnt_confirm_email = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_confirm_email',$cnt_confirm_email);

		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'count';
		$query['where'] = "where level_code='JB37' && from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d') ";
		$cnt_confirm_nicecheck = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_confirm_nicecheck',$cnt_confirm_nicecheck);

		//금일 회원 가입중 휴대폰인증단계회원
		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'count';
		$query['where'] = "where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d') && bool_confirm_mobile = '1' ";
		$cnt_confirm_mobile = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_confirm_mobile',$cnt_confirm_mobile);


		//금일 회원 가입수
		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'count';
		$query['where'] = "where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d')";
		$cnt_join_today = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_join_today',$cnt_join_today);
		
		//총회원수
		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'count';
		//$query['where'] = "where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d')";
		$cnt_member = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_member',$cnt_member);

		//회원포인트 수
		$query = array();
		$query['table_name'] = 'js_member_emoney';
		$query['tool'] = 'count';
		$query['where'] = "where kind_emoney IN ('admin')";
		$cnt_member_point = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_member_point',$cnt_member_point);

		//회원포인트 인증건수
		$query = array();
		$query['table_name'] = 'js_member_emoney';
		$query['tool'] = 'count';
		$query['where'] = "where kind_emoney IN ('admin')";
		$cnt_member_point_auth = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_member_point_auth',$cnt_member_point_auth);

		//회원포인트 
		$query = array();
		$query['table_name'] = 'js_member_emoney';
		$query['tool'] = 'select';
		$query['fields'] = 'sum(emoney)';
		$query['where'] = "where kind_emoney IN ('admin')";
		$sum_member_point_emoney = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('sum_member_point_emoney',$sum_member_point_emoney);

		//금일 구매신청수
		$query = array();
		// $query['table_name'] = 'js_wallet_buycoin';
		// $query['tool'] = 'count';
		// $query['where'] = "where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d')";
		// $cnt_buysmartcoin_today = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_buysmartcoin_today',$cnt_buysmartcoin_today??0);
		
		//총구매신청수
		$query = array();
		// $query['table_name'] = 'js_wallet_buycoin';
		// $query['tool'] = 'count';
		// //$query['where'] = "where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d')";
		// $cnt_buysmartcoin = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign('cnt_buysmartcoin',$cnt_buysmartcoin??0);

	}

	//거래소현황
	function getExchangeState()
	{

		// $query = array();
		//KRW 포인트
		// $query[] = "(select count(*) from js_btc_wallet_krw) AS cnt_krw_point"; //요청건수
		// $query[] = "(select count(*) from js_btc_wallet_krw) AS cnt_krw_point_auth"; //인증건수
		// $query[] = "(select count(*) from js_btc_wallet_krw_txn where status IN ('A5', 'B5', 'C5', 'D5')) AS cnt_krw_point_txn"; //실행건수
		// $query[] = "(select sum(amount) from js_btc_wallet_krw_txn where status IN ('A5', 'B5', 'C5', 'D5')) AS sum_krw_point_amount"; //금액
		// $query[] = "(select sum(amount) from js_btc_wallet_krw) AS sum_krw_point_fee"; //금액

		// //KRW 입금
		// $query[] = "(select count(*) from js_btc_wallet_krw_txn where type='A') AS cnt_krw_A"; //요청건수
		// $query[] = "(select count(*) from js_btc_wallet_krw) AS cnt_krw_auth"; //인증건수
		// $query[] = "(select count(*) from js_btc_wallet_krw_txn where type='A' && status IN ('A5', 'B5', 'C5', 'D5')) AS cnt_krw_txx"; //실행건수
		// $query[] = "(select sum(amount) from js_btc_wallet_krw_txn where type='A' && status IN ('A5', 'B5', 'C5', 'D5')) AS sum_krw_A_amount"; //금액
		// $query[] = "(select sum(amount) from js_btc_wallet_krw) AS sum_krw_fee"; //금액

		// //KRW 출금
		// $query[] = "(select count(*) from js_btc_wallet_krw_txn where type='B') AS cnt_krw_B"; //요청건수
		// $query[] = "(select sum(amount) from js_btc_wallet_krw_txn where type='B') AS sum_krw_B_amount";//출금금액

		// //BTC 입금
		// $query[] = "(select count(*) from js_btc_wallet_btc_txn where type='C') AS cnt_btc_C"; //요청건수
		// $query[] = "(select sum(amount) from js_btc_wallet_btc_txn where type='C') AS sum_btc_C_amount";//입금금액

		// //BTC 출금
		// $query[] = "(select count(*) from js_btc_wallet_btc_txn where type='D') AS cnt_btc_D"; //요청건수
		// $query[] = "(select count(*) from js_btc_wallet_btc_txn where type='D') AS cnt_btc_D_auth"; //인증건수
		// $query[] = "(select sum(amount) from js_btc_wallet_btc_txn where type='D') AS sum_btc_D_amount";
	
		// //BTC 매수
		// $query[] = "(select count(*) from js_btc_trade where type='A') AS cnt_btc_trade_A"; //요청건수
		// $query[] = "(select count(*) from js_btc_trade where type='A') AS cnt_btc_trade_A_auth"; //인증건수
		// $query[] = "(select count(*) from js_btc_trade where type='A') AS cnt_btc_trade_A_confirm"; //실행건수
		// $query[] = "(select sum(price) from js_btc_trade where type='A') AS sum_btc_trade_A_amount";//매수금액
		// $query[] = "(select sum(site_trading_fee) from js_btc_trade where type='A') AS sum_btc_trade_A_fee";//수수료

		// //BTC 매도
		// $query[] = "(select count(*) from js_btc_trade where type='B') AS cnt_btc_trade_B"; //요청건수
		// $query[] = "(select count(*) from js_btc_trade where type='B') AS cnt_btc_trade_B_auth"; //인증건수
		// $query[] = "(select count(*) from js_btc_trade where type='B') AS cnt_btc_trade_B_confirm"; //실행건수
		// $query[] = "(select sum(price) from js_btc_trade where type='B') AS sum_btc_trade_B_amount"; //매도금액
		// $query[] = "(select sum(site_trading_fee) from js_btc_trade where type='B') AS sum_btc_trade_B_fee";//수수료

		// $query = 'select '.implode(',',$query);
		// $result = $this->dbcon->query($query,__FILE__,__LINE__);
		// $row = mysqli_fetch_assoc($result);
		// $this->tpl->assign($row);
	}


	//게시판 등록 현황
	function bbsState()
	{
		$query = array();
		$query['table_name'] = 'js_bbs_info';
		$query['tool'] = 'select';
		$query['fields'] = "bbscode,
			title,
			(select count(*) from  js_bbs_main where bbscode=js_bbs_info.bbscode) AS cnt_bbs_main,
			(select count(*) from  js_bbs_comment where bbscode=js_bbs_info.bbscode) AS cnt_bbs_comment,
			(select count(*) from  js_bbs_main where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d') && bbscode=js_bbs_info.bbscode) AS cnt_today_bbs,
			(select count(*) from  js_bbs_comment where from_unixtime(regdate,'%Y-%m-%d')=date_format(now(),'%Y-%m-%d') && bbscode=js_bbs_info.bbscode) AS cnt_today_comment";
		//$query['where'] = '';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_bbs',$loop);
	}

	function switch_bool_trade () {
		$bool_trade = $_POST['bool_trade'] == '1' ? '1' : '0';
		return $this->dbcon->query("UPDATE js_config_basic SET bool_trade='{$bool_trade}' WHERE code='KKIKDA'  ");// $query['where'] = " WHERE code='".getSiteCode()."' ";
	}

}

checkAdmin();
$js = new AdminMain($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;


if($_POST['pg_mode'] == 'revenue') {
	ajaxCheckAdmin();
	$js->loopRevenue('json');
} 
elseif($_POST['pg_mode'] == 'coins') {
	ajaxCheckAdmin();
	$js->loopCoins('json');
}
elseif($_POST['pg_mode'] == 'send_notice') {
	ajaxCheckAdmin();
	$js->sendNotice();
	exit('9');
}
elseif($_POST['pg_mode'] == 'switch_bool_trade') {
	ajaxCheckAdmin();
	$r = $js->switch_bool_trade();
	exit($r ? '1' : '');
}
else {

	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$interface->setPlugIn('kendo_dataviz');

	$interface->setPlugIn('switchery');
	$interface->addScript('/template/'.getSiteCode().'/admin/basic/js/configAdmin.js');

	$interface->layout['js_tpl_left'] = 'menu.html?main';
	$interface->layout['js_tpl_main'] = 'main.html';
	
	$js->getExchangeState();
	// $js->loopRevenue();
	// $js->bitcoinState();
	$js->memberState();
	// $js->partnershipState();
	// $js->questionState();
	// $js->reviewState();
	// $js->bbsState(); 
	$js->setMainNotice('NOTICE'); // 공지사항의 카테고리별 메인 페이지 글 추출
	$print = 'layout';
	$interface->display($print);

}
$dbcon->close();
