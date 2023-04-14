<?php
/**
 * Coin 관련 Model 객체
 * @author Kenny Han
 */
class Coins extends BASIC
{

	public $dbcon = null;
	public $tpl = null;

	function __construct($tpl, $dbcon=null)
	{
		$this->dbcon = $dbcon;
		$this->tpl = $tpl;

		$config = array();
		$config['table_name'] = 'js_trade_'.strtolower($_REQUEST['symbol']).strtolower($_REQUEST['exchange']).'_txn';
		$config['query_func'] = 'tradeHistoryTxnQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array('txnid','time_traded','volume','price','orderid_buy','orderid_sell','fee','tax_transaction','tax_income','price_updown');
		$config['no_space'] = array('txnid','volume','price','orderid_buy','orderid_sell','fee','tax_transaction','tax_income','price_updown');
		$config['staple_article'] = array('txnid'=>'blank');
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array();
		$config['file_size'] = 2;
		$config['upload_limit'] = TRUE;
		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
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
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->config = $config;
		$this->BASIC($config,$tpl);
	}

	function view()
	{
		if(empty($_GET['txnid'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['fields'] = "*";
		$query['where'] = 'where txnid=\''.$this->dbcon->escape($_GET['txnid']).'\'';
		$this->bView($query);
	}

	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = "*";
		$query['where'] = 'where txnid=\''.$this->dbcon->escape($_GET['txnid']).'\'';
		$this->assign_default_value($this->dbcon->query($query));
	}

	function write()
	{
		$sql = "select txnid from ".$this->config['table_name']." where txnid='".$this->dbcon->escape($_POST['txnid'])."'";
		$txnid = $this->dbcon->query_unique_value($sql);
		$query = array();
		if($txnid) {
			$query['tool'] = 'update';
			$query['where'] = 'where txnid=\''.$this->dbcon->escape($_POST['txnid']).'\'';
			$r = $this->bEdit($query,$_POST);
		} else {
			$query['tool'] = 'insert';
			$r = $this->bWrite($query,$_POST);
		}
		if($r) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function del()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'delete';
		$query['where'] = 'where txnid=\''.$_POST['txnid'].'\'';
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}


	function withdraw()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		// txn 정보 확인.
		$query = "SELECT * FROM js_exchange_wallet_txn where txnid='{$this->dbcon->escape($_POST['txnid'])}' ";
		$txn_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);
		if(!$txn_info['txnid']) {
			jsonMsg(0, Lang::main_coin3);
		}
		if($txn_info['status']!='O') {
			jsonMsg(0, Lang::main_coin4);
		}

		// 화폐 정보 확인.
		$query = "SELECT * FROM js_trade_currency where symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$currency_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);

		// 지갑 확인(항상 있어요. 하지만 수동으로 지우거나 다른오류가 있을수 있으니 확인합니다.)
		$query = "SELECT * FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$wallet = $this->dbcon->query_unique_array($query);
		if(!$wallet) {
			jsonMsg(0, Lang::main_coin5);
		}
		// 잔액 확인 : 잔액 >= amount + fee
		if($wallet['confirmed'] < $txn_info['amount'] + $txn_info['fee']) {
			jsonMsg(0, Lang::main_coin6.$wallet['confirmed'].', '.$txn_info['amount'].', '.$txn_info['fee']);
		}

		// 지갑 잔액 차감
		$amount = $txn_info['amount'] + $txn_info['fee'];
		$query = "UPDATE js_exchange_wallet SET confirmed = confirmed - '{$this->dbcon->escape($amount)}' WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}'  ";
		$this->dbcon->query($query);

		// txn 상태 변경( O -> D )
		$query = "UPDATE js_exchange_wallet_txn SET status = 'D' WHERE txnid='{$this->dbcon->escape($txn_info['txnid'])}' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	function deposit()
	{
		if(empty($_POST['txnid'])) {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_coin1);
			}
			else {
				jsonMsg(0,Lang::main_coin1);
			}
		}

		// txn 정보 확인.
		$query = "SELECT * FROM js_exchange_wallet_txn where txnid='{$this->dbcon->escape($_POST['txnid'])}' ";
		$txn_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);
		if(!$txn_info['txnid']) {
			jsonMsg(0, Lang::main_coin3);
		}
		if($txn_info['status']!='O') {
			jsonMsg(0, Lang::main_coin4);
		}

		// 화폐 정보 확인.
		$query = "SELECT * FROM js_trade_currency where symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$currency_info = $this->dbcon->query_unique_array($query,__FILE__,__LINE__);

		// 지갑 확인(항상 있어요. 하지만 수동으로 지우거나 다른오류가 있을수 있으니 확인합니다.)
		$query = "SELECT count(*) cnt FROM js_exchange_wallet WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}' ";
		$wallet = $this->dbcon->query_unique_value($query);
		if(!$wallet) {
			jsonMsg(0, Lang::main_coin5);
		}

		// 입급시 추가 금액계산
		$currency_info['tax_in_ratio'] = $currency_info['tax_in_ratio'] ? $currency_info['tax_in_ratio'] : 0;
		$currency_info['display_decimals'] = $currency_info['display_decimals'] ? $currency_info['display_decimals'] : 0;
		$currency_info['fee_in'] = $currency_info['fee_in'] ? $currency_info['fee_in'] : 0;
		$amount = $txn_info['amount'] - round($txn_info['amount'] * $currency_info['tax_in_ratio'], $currency_info['display_decimals']) - $currency_info['fee_in'];

		// 지갑 잔액 추가
		$query = "UPDATE js_exchange_wallet SET confirmed = confirmed + '{$this->dbcon->escape($amount)}' WHERE userno='{$this->dbcon->escape($txn_info['userno'])}' AND symbol='{$this->dbcon->escape($txn_info['symbol'])}'  ";
		$this->dbcon->query($query);

		// txn 상태 변경( O -> D )
		$query = "UPDATE js_exchange_wallet_txn SET status = 'D' WHERE txnid='{$this->dbcon->escape($txn_info['txnid'])}' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}

	public function assign_default_value($val='') {
		$default_value = array();
		if($val) {
			$default_value = array_merge($default_value, $val);
		}
		$this->tpl->assign('currency_info', $default_value);
	}

	/**
	 * 트랜젝션 리스트 데이터를 추출합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function lists() {
		$query = array();
		$query['table_name'] = $this->config['table_name'].' t1 INNER JOIN js_trade_'.$this->dbcon->escape(strtolower($_REQUEST['symbol'])).$this->dbcon->escape(strtolower($_REQUEST['exchange'])).'_order AS o_buy ON t1.orderid_buy = o_buy.orderid
			LEFT JOIN js_trade_'.$this->dbcon->escape(strtolower($_REQUEST['symbol'])).$this->dbcon->escape(strtolower($_REQUEST['exchange'])).'_order AS o_sell ON t1.orderid_sell = o_sell.orderid
			LEFT JOIN js_member AS m_buy ON o_buy.userno = m_buy.userno
			LEFT JOIN js_member AS m_sell ON o_sell.userno = m_sell.userno';
		$query['tool'] = 'select';
		// 'txnid','time_traded','volume','price','orderid_buy','orderid_sell','fee','tax_transaction','tax_income','price_updown'
		$query['fields'] = " t1.txnid, t1.time_traded, t1.volume, t1.price, t1.orderid_buy, t1.orderid_sell, t1.fee, t1.tax_transaction, t1.tax_income, t1.price_updown,
			m_buy.userid AS buy_userid, m_sell.userid AS sell_userid, m_buy.name as buy_name, m_sell.name as sell_name ";
		$query['where'] = " WHERE 1 ";

		if($_GET['searchval']) {
			$search_keyword = $this->dbcon->escape($_GET['searchval']);
			$query['where'] .= " AND (t1.txnid = '".$this->dbcon->escape($_GET['searchval'])."'
				OR m_buy.userid LIKE '%".$search_keyword."%' OR m_sell.userid LIKE '%".$search_keyword."%' )";
		}

		//날짜 검색
		// if($_POST['s_year'] && $_POST['s_month']){
		// 	if($_POST['s_day']){
		// 		$s_yearmonthday = $this->dbcon->escape($_POST['s_year']).$this->dbcon->escape($_POST['s_month']).$this->dbcon->escape($_POST['s_day']);
		// 		$query['where'] .= " AND DATE_FORMAT(t1.time_traded, '%Y%m%d')='{$s_yearmonthday}'";

		// 	}else{
		// 		$s_yearmonth = $this->dbcon->escape($_POST['s_year']).$this->dbcon->escape($_POST['s_month']);
		// 		$query['where'] .= " AND DATE_FORMAT(t1.time_traded, '%Y%m')='{$s_yearmonth}'";
		// 	}
		// }
		//날짜 기간 검색
		if($_POST['start_date']){
			$t = $_POST['start_date'];
			$query['where'] .= " AND '{$this->dbcon->escape($t)}' <= t1.time_traded ";
		}
		if($_POST['end_date']){
			$t = $_POST['end_date'];
			$query['where'] .= " AND t1.time_traded <= '{$this->dbcon->escape($t)} 23:59:59' ";
		}

        if($_POST['name']) {
            $t = $_POST['name'];
            $query['where'] .= " AND (m_buy.name like '%{$t}%' or m_sell.name like '%{$t}%')";
        }

		// var_dump($query['where']); exit;

		//order by절
		if($_GET['sort_target'] ) {
			$query['where'] .= ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['where'] .= ', ';
				}
				if($_GET['sort_target'][$i] =="buy_userid"){$sel_field ='m_buy.userid';}
				else if($_GET['sort_target'][$i] =="sell_userid"){$sel_field  ='m_sell.userid';}
				else{$sel_field  ='t1.'.$_GET['sort_target'][$i];}

				$query['where'] .= $sel_field .' '.$_GET['sort_method'][$i];
			}
		} else {
			$query['where'] .= ' ORDER BY t1.txnid DESC';
		}
		// $_GET['start'] = preg_replace('/[^0-9]/g', '', $_GET['start']);
		// $_GET['start'] = $_GET['start'] ? $_GET['start'] : 0;
		// $sql .=" LIMIT {$_GET['start']}, {$this->dbcon->escape($rows)} ";
		//var_dump($query); exit;
		$list = $this->bList($query,'loop','',true);
		return $list;
	}
	/**
	 * 트랜젝션 리스트 데이터를 JSON 형식으로 출력합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	/*
	public function _lists($row) {
		$buy_userno = $this->get_userno_form_orderid($_REQUEST['symbol'], $_REQUEST['exchange'], $row['orderid_buy']);
		$member = $this->get_member_by_userno($buy_userno);
		$row['buy_userid'] = $member->userid;
		$sell_userno = $this->get_userno_form_orderid($_REQUEST['symbol'], $_REQUEST['exchange'], $row['orderid_sell']);
		$member = $this->get_member_by_userno($sell_userno);
		$row['sell_userid'] = $member->userid;
		$row['volume'] = $row['volume'] * 1;
		$row['price'] = $row['price'] * 1;
		$row['fee'] = $row['fee'] * 1;
		return $row;
	}*/

	public function get_userno_form_orderid($symbol, $exchange, $orderid) {
		$sql = "SELECT userno from js_trade_{$this->dbcon->escape(strtolower($symbol))}{$this->dbcon->escape(strtolower($exchange))}_order where orderid='{$this->dbcon->escape($orderid)}' limit 1 ";
		return $this->dbcon->query_unique_value($sql);
	}
	public function get_member_by_userno($userno) {
		$sql = "SELECT * from js_member where userno='{$this->dbcon->escape($userno)}' limit 1 ";
		return $this->dbcon->query_unique_object($sql);
	}
}

function tradeHistoryTxnQuery($arr)
{
	$qry = array();
	// 'txnid','time_traded','volume','price','orderid_buy','orderid_sell','fee','tax_transaction','tax_income','price_updown'
	if(!empty($arr['txnid']))  { $qry[] = 'txnid=\''.$arr['txnid'].'\''; }
	if(!empty($arr['time_traded']))  { $qry[] = 'time_traded=\''.$arr['time_traded'].'\''; }
	if(!empty($arr['volume']))  { $qry[] = 'volume=\''.$arr['volume'].'\''; }
	if(!empty($arr['price']))  { $qry[] = 'price=\''.$arr['price'].'\''; }
	if(!empty($arr['orderid_buy']))  { $qry[] = 'orderid_buy=\''.$arr['orderid_buy'].'\''; }
	if(!empty($arr['orderid_sell']))  { $qry[] = 'orderid_sell=\''.$arr['orderid_sell'].'\''; }
	if(!empty($arr['fee']))  { $qry[] = 'fee=\''.$arr['fee'].'\''; }
	if(!empty($arr['tax_transaction']))  { $qry[] = 'tax_transaction=\''.$arr['tax_transaction'].'\''; }
	if(!empty($arr['tax_income']))  { $qry[] = 'tax_income=\''.$arr['tax_income'].'\''; }
	if(!empty($arr['price_updown']))  { $qry[] = 'price_updown=\''.$arr['price_updown'].'\''; }
	return implode(',',$qry);
}
