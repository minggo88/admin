<?php

class Trading extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'account';
		$config['query_func'] = 'account';
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
		$config['navi_url'] = '123123.php';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function getCurrencyCode () {
		$sql = "select symbol from js_trade_currency where active='Y' and menu='Y' and tradable='Y' order by sortno ";
		$r = $this->dbcon->query_all_object($sql);
		$t = array();
		foreach($r as $row) {
			$t[] = $row->symbol;
		}
		return $t;
	}

	function getTradableCurrencyCode () {
		$sql = "select symbol from js_trade_currency where active='Y' and tradable='Y'  ";
		$r = $this->dbcon->query_all_object($sql);
		$t = array();
		foreach($r as $row) {
			$t[] = $row->symbol;
		}
		return $t;
	}

	function getInvestment () {

		// get Return on Investment
		/* ** 계산식
			총 투자금액 : ∑자산 별 (기초평가금액 + ∑입금평가금액 - ∑출금평가금액)
			총 투자손익 : ∑자산 별 (기말평가금액 - 기초평가금액 + ∑출금평가금액 - ∑입금평가금액)
			총 투자수익률 : 총 투자수익률 (총투자손익 / 총투자금액) * 100

			기초평가금액 : 기초 KRW + 기초 가상자산 평가금액 (해당일 종가, 00시 기준)
			기말평가금액 : 기말 KRW + 기말 가상자산 평가금액 (해당일 종가, 00시 기준)

			출금가능원화 : -
			주문가능원화 : -

			입금평가금액 : 기간 내 입금 KRW + 입금 가상자산 평가금액 (해당시간 시세기준)
			출금평가금액 : 기간 내 출금 KRW + 출금 가상자산 평가금액 (해당시간 시세기준)
		*/ 
		
		$row['TIA'] = 3241234567; // 총 투자금액 Total Investment Amount
		$row['TII'] = 3241234567 / 4.3; // 총 투자손익 Total Investment Income
		$row['TROI'] = -43.00; // 총 투자수익률 Total Return on Investment
		if($row['TROI'] < 0) {
			$row['npn'] = 'negative';
		} else {
			$row['npn'] = 'positive';
		}
		$row['BEA'] = 5456789; // 기초평가금액 Basic evaluation amount
		$row['FEA'] = 6758432; // 기말평가금액 Final evaluation amount
		$row['WKRW'] = 1241234567; // 출금가능원화 Withdrawable KRW
		$row['OKRW'] = 1241234567; // 주문가능원화 Orderable KRW
		$row['DEA'] = 1241234567;// 입금평가금액 Deposit evaluation amount
		$row['WEA'] = 1241234567;// 출금평가금액 Withdrawal evaluation amount

		$this->tpl->assign($row);
	}

	function loopGetanalysis($mode='tpl')
	{
		// test
		$_POST['userid'] = $_SESSION['USER_ID'];
		$_POST['name'] = $_SESSION['NAME'];
		$_POST['email'] = $_SESSION['USER_EMAIL'];
		$_POST['mobile'] = $_SESSION['USER_MOBILE'];


		
		$_GET['sort_target'] = array('m.regdate');
		$_GET['sort_method'] = array('desc');
		if($_REQUEST['order']) {
			$i=0;
			foreach($_REQUEST['order'] as $order) {
				$_GET['sort_target'][$i] = $_REQUEST['columns'][ $order['column'] ]['data'];
				$_GET['sort_method'][$i] = $order['dir'];
				$i++;
			}
		}
		// $_GET['searchval'] = $_REQUEST['search']['value'] ? $_REQUEST['search']['value'] : false;
		// $_GET['searchval'] = $_REQUEST['searchval'] ? $_REQUEST['searchval'] : $_GET['searchval'];
		$_GET['start'] = $_REQUEST['start'] ? $_REQUEST['start']*1 : 0;
		$page = $_REQUEST['draw'] ? $_REQUEST['draw']*1 : 1;
		$js->config['loop_scale'] = 10;
		$js->config['bool_navi_page'] = strtoupper($_REQUEST['length'])=='ALL' ? false : true;


		if (empty($_GET['sort_method'])) {
			$sort_method = 'desc';
		}
		else {
			$sort_method = $GLOBALS['_GET_ESCAPE']['sort_method'];
		}

		// 검색
		if($_POST['start_date']) { $_GET['start_date'] = $_POST['start_date']; $GLOBALS['_GET_ESCAPE']['start_date'] = $GLOBALS['_POST_ESCAPE']['start_date']; }
		if($_POST['end_date']) { $_GET['end_date'] = $_POST['end_date']; $GLOBALS['_GET_ESCAPE']['end_date'] = $GLOBALS['_POST_ESCAPE']['end_date']; }
		
		// 자산목록
		$qry = $this->srchQry_period(); // 기간
		$query = array();
		$query['table_name'] = 'js_exchange_wallet as ew 
			left join js_trade_currency as tc on ew.symbol = tc.symbol 
			left join js_member as m on m.userno = ew.userno ';
		$query['tool'] = 'select';
		$query['fields'] = 'tc.symbol,tc.name,ew.confirmed';
		$query['where'] = 'where tc.active=\'Y\' and tc.menu=\'Y\' and m.userid = \''.$_SESSION['USER_ID'].'\' '.$qry.' order by tc.sortno asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$row['icon_symbol'] = strtolower($row['symbol']);

			$row['sb'] = 1.34056788; // 기초잔고 start balance
			$row['sb_krw'] = 1234567; // 기초잔고 KRW로 계산  
			$row['eb'] = 0.45671237; // 기말잔고 end balance
			$row['eb_krw'] = 654321; // 기말잔고 KRW로 계산  
			$row['ddp'] = 12567890;// 기간중입금 Deposit during period
			$row['ddp_krw'] = 12567890;// 기간중입금 RW로 계산  
			$row['wdp'] = 5367890;// 기간중출금 Withdrawal during period
			$row['wdp_krw'] = 12567890;// 기간중출금 RW로 계산  

			$loop[] = $row;
		}

		if($mode == 'json') {
			$ret = array();
			$ret['data'] = $loop;
			//$ret['total'] = $sum;
			echo json_encode($ret);
		}
		else {//mode : tpl
			$this->tpl->assign('loop_getanalysis',$loop);
		}
		// exit(json_encode(array('data'=>$loop,'draw'=>$page,'recordsFiltered'=>$total,'recordsTotal'=>$total)));
	}

	function srchQry_period ()
	{
		$arr = array();
		$arr[] = 'm.userid = \''.$_SESSION['USER_ID'].'\'';

		if(isset($_GET['start_date'])) { $arr[] = ' m.regdate>=\''.strtotime($_GET['start_date'].' 00:00:00').'\''; }
		if(isset($_GET['end_date'])) { $arr[] = ' m.regdate<=\''.strtotime($_GET['end_date'].' 23:59:59').'\''; }
		$ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';

		return $ret;
	}

	function srchQry_member()
	{
		$arr = array();
		$arr[] = 'm.userid = \''.$_SESSION['USER_ID'].'\'';

		if(isset($_GET['start_date'])) { $arr[] = ' m.regdate>=\''.strtotime($_GET['start_date'].' 00:00:00').'\''; }
		if(isset($_GET['end_date'])) { $arr[] = ' m.regdate<=\''.strtotime($_GET['end_date'].' 23:59:59').'\''; }
		$ret = sizeof($arr) > 0 ? '&& ('.implode(' && ',$arr).')' : '';

		return $ret;
	}

	public function get_trade_item_info($symbol) {
		return $this->dbcon->query_unique_array("SELECT * FROM js_trade_currency WHERE symbol='{$this->dbcon->escape($symbol)}' ");
	}

	public function get_base_price($symbol) {
		$symbol = strtolower($symbol);
		$prev_avg_price = $this->dbcon->query_one("SELECT ROUND(SUM(volume * price) / SUM(volume)) prev_avg_price FROM `js_trade_{$symbol}krw_txn` FORCE INDEX(time_traded) WHERE time_traded LIKE CONCAT((SELECT DATE(MAX(time_traded)) FROM `js_trade_{$symbol}krw_txn` FORCE INDEX(time_traded)  WHERE time_traded < DATE(NOW())),'%')");
		if($prev_avg_price <= 0) {
			$prev_avg_price = $this->dbcon->query_one("SELECT price_close FROM `js_trade_price` WHERE symbol='{$this->dbcon->escape($symbol)}'");
		}
		return $prev_avg_price;
	}
	public function get_trade_price_info($symbol) {
		$base_price = $this->get_base_price($symbol);
		$trade_max_price = floor($base_price * (1 + 0.3));
		$trade_min_price = ceil($base_price * (1 - 0.3));
		$trade_min_price = $trade_min_price<1 ? 1 : $trade_min_price;
		return array('base_price'=>$base_price, 'trade_max_price'=> $trade_max_price, 'trade_min_price'=> $trade_min_price );
	}

	public function get_user_wallet($userno, $symbol) {
		return $this->dbcon->query_unique_array("SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($userno)}' AND symbol='{$this->dbcon->escape($symbol)}' ");
	}

}
?>
