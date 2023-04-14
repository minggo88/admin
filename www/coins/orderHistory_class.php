<?php
/**
 * 거래소 주문 내역 클래스
 * @author Kenny Han
 */
class orderHistory extends BASIC
{

	public $dbcon = null;
	public $tpl = null;

	function __construct($tpl, $dbcon=null)
	{
		$this->dbcon = $dbcon;
		$this->tpl = $tpl;

		$config = array();
		$config['table_name'] = 'js_trade_'.strtolower($_REQUEST['symbol']).strtolower($_REQUEST['exchange']).'_order';
		$config['query_func'] = 'orderHistoryQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array('orderid','userno','address','time_order','trading_type','price','volume','volume_remain','amount','status','time_traded');
		$config['no_space'] = array('orderid','userno','address','trading_type','price','volume','volume_remain','amount','status');
		$config['staple_article'] = array('orderid'=>'blank');
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
		if(empty($_GET['orderid'])) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['fields'] = "*";
		$query['where'] = 'where orderid=\''.$this->dbcon->escape($_GET['orderid']).'\'';
		$this->bView($query);
	}

	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = "*";
		$query['where'] = 'where orderid=\''.$this->dbcon->escape($_GET['orderid']).'\'';
		$this->assign_default_value($this->dbcon->query($query));
	}

	function write()
	{
		// 이곳에서는 주문내역을 추가/수정/삭제 할수 없음.
		jsonMsg(0);
		// $sql = "select orderid from ".$this->config['table_name']." where orderid='".$this->dbcon->escape($_POST['orderid'])."'";
		// $orderid = $this->dbcon->query_unique_value($sql);
		// $query = array();
		// if($orderid) {
		// 	$query['tool'] = 'update';
		// 	$query['where'] = 'where orderid=\''.$this->dbcon->escape($_POST['orderid']).'\'';
		// 	$r = $this->bEdit($query,$_POST);
		// } else {
		// 	$query['tool'] = 'insert';
		// 	$r = $this->bWrite($query,$_POST);
		// }
		// if($r) {
		// 	jsonMsg(1);
		// } else {
		//	jsonMsg(0);
		// }
	}

	function del()
	{
		// 이곳에서는 주문내역을 추가/수정/삭제 할수 없음.
		jsonMsg(0);
		// if(empty($_POST['orderid'])) {
		// 	if($config_basic['bool_ssl'] > 0) {
		// 		errMsg('주문번호를 입력해주세요.');
		// 	}
		// 	else {
		// 		jsonMsg(0,'주문번호를 입력해주세요.');
		// 	}
		// }
		// $query = array();
		// $query['table_name'] = $this->config['table_name'];
		// $query['tool'] = 'delete';
		// $query['where'] = 'where orderid=\''.$_POST['orderid'].'\'';
		// if($this->dbcon->query($query,__FILE__,__LINE__)) {
		// 	jsonMsg(1);
		// } else {
		//	jsonMsg(0);
		// }
	}

	public function assign_default_value($val='') {
		$default_value = array();
		if($val) {
			$default_value = array_merge($default_value, $val);
		}
		$this->tpl->assign('currency_info', $default_value);
	}

	/**
	 * 주문 리스트 데이터를 추출합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 orderid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function lists() {
		$query = array();
		$query['table_name'] = $this->config['table_name'].' t1' ;//.' t1 left join js_member t2 on t1.userno=t2.userno ';
		$query['tool'] = 'select';
		$query['fields'] = " * ";
		$query['where'] = " WHERE 1 ";
		// datatabe search
		if($_GET['searchval']) {
			$search_keyword = $this->dbcon->escape($_GET['searchval']);

			$query['where'] .= " AND userno IN (SELECT userno FROM js_member WHERE userid LIKE '%".$search_keyword."%')";
		}
		//날짜 기간 검색
		if($_POST['start_date']){
			$t = $_POST['start_date'];
			$query['where'] .= " AND '{$this->dbcon->escape($t)}' <= t1.time_order ";
		}
		if($_POST['end_date']){
			$t = $_POST['end_date'];
			$query['where'] .= " AND t1.time_order <= '{$this->dbcon->escape($t)} 23:59:59' ";
		}
		//order by절
		if($_GET['sort_target'] ) {
			$query['where'] .= ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['where'] .= ', ';
				}
				$table = 't1';
				$t2_col = array('userid');
				if(in_array($_GET['sort_target'][$i], $t2_col)) {
					$table = 't2';
				}
				$query['where'] .= $table.'.'.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
			}
		} else {
			$query['where'] .= ' ORDER BY t1.orderid DESC';
		}
		// var_dump($query);exit;
		$list = $this->bList($query,'loop','_lists',true);
		return $list;
	}
	/**
	 * 주문 리스트 데이터를 JSON 형식으로 출력합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 orderid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function _lists($row) {
		$member = $this->get_member_by_userno($row['userno']);
		$row['userid'] = $member->userid;
		$row['trading_type_str'] = $row['trading_type']=='S' ? '매도' : '매수';
		$row['status_str'] = $row['status']=='O' ? '대기중' : ( $row['status']=='C' ? '완료' : ( $row['status']=='T' ? '매매중' : ( $row['status']=='D' ? '취소' : '' ) ) );
		$row['volume'] = $row['volume'] * 1;
		$row['volume_remain'] = $row['volume_remain'] * 1;
		$row['price'] = $row['price'] * 1;
		$row['fee'] = $row['fee'] * 1;
		return $row;
	}

	public function get_member_by_userno($userno) {
		$sql = "SELECT * from js_member where userno='{$this->dbcon->escape($userno)}' limit 1 ";
		return $this->dbcon->query_unique_object($sql);
	}
}

function orderHistoryQuery($arr)
{
	$qry = array();
	if(!empty($arr['orderid']))  { $qry[] = 'orderid=\''.$arr['orderid'].'\''; }
	if(!empty($arr['userno']))  { $qry[] = 'userno=\''.$arr['userno'].'\''; }
	if(!empty($arr['address']))  { $qry[] = 'address=\''.$arr['address'].'\''; }
	if(!empty($arr['time_order']))  { $qry[] = 'time_order=\''.$arr['time_order'].'\''; }
	if(!empty($arr['trading_type']))  { $qry[] = 'trading_type=\''.$arr['trading_type'].'\''; }
	if(!empty($arr['price']))  { $qry[] = 'price=\''.$arr['price'].'\''; }
	if(!empty($arr['volume']))  { $qry[] = 'volume=\''.$arr['volume'].'\''; }
	if(!empty($arr['volume_remain']))  { $qry[] = 'volume_remain=\''.$arr['volume_remain'].'\''; }
	if(!empty($arr['amount']))  { $qry[] = 'amount=\''.$arr['amount'].'\''; }
	if(!empty($arr['status']))  { $qry[] = 'status=\''.$arr['status'].'\''; }
	if(!empty($arr['time_traded']))  { $qry[] = 'time_traded=\''.$arr['time_traded'].'\''; }
	return implode(',',$qry);
}
