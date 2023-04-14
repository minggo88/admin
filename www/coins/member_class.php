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
		$config['table_name'] = 'js_exchange_wallet';
		$config['query_func'] = 'tradeWalletQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array('userno','symbol','address','regdate','locked','autolocked','confirmed','unconfirmed','account','regdate','deposit_check_time');
		$config['no_space'] = array('userno','symbol','address','locked','autolocked','confirmed','unconfirmed','account');
		$config['staple_article'] = array('symbol'=>'blank','userno'=>'blank');
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
	public function assign_default_value($val='') {
		$default_value = array();
		if($val) {
			$default_value = array_merge($default_value, $val);
		}
		$this->tpl->assign('currency_info', $default_value);
	}

	/**
	 * 거래소 종목의 회원별 보유량 표시용 데이터 추출 
	 */
	public function lists() {
		$query = array();
		$query['tool'] = 'select';
		$query['fields'] = " t1.*, IF(t2.userno IS NULL, 'Y', 'N') withdrawn, IFNULL(t2.userid, concat(mw.userid,'(탈퇴)')) userid, IFNULL(t2.nickname, concat(mw.nickname,'(탈퇴)')) nickname, IFNULL(t2.name, concat(mw.name,'(탈퇴)')) user_name, t2.mobile, t3.name symbol_name ";
		$query['table_name'] = $this->config['table_name'].' t1 left join js_member t2 on t1.userno=t2.userno left join js_trade_currency t3 on t1.symbol=t3.symbol ';
		$query['where'] = " WHERE 1 "; // AND t1.symbol<>'KRW'
		if($_GET['searchval']) {
			// $query['where'] .= " AND (symbol LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR address LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR userid LIKE '%".str_replace('010', '10', $this->dbcon->escape($_GET['searchval']))."%') ";
			$query['where'] .= " AND (t2.nickname LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR t2.name LIKE '%".$this->dbcon->escape($_GET['searchval'])."%' OR t2.userid LIKE '%".$this->dbcon->escape($_GET['searchval'])."%') ";
		}
		if( $_REQUEST['symbol'] ) $query['where'] .= " AND t1.symbol LIKE '".$this->dbcon->escape($_REQUEST['symbol'])."' ";
		if( $_REQUEST['userid'] ) $query['where'] .= " AND t2.userid LIKE '%".$this->dbcon->escape($_REQUEST['userid'])."%' ";
		if( $_REQUEST['name'] ) $query['where'] .= " AND t2.name LIKE '%".$this->dbcon->escape($_REQUEST['name'])."%' ";
		if( $_REQUEST['nickname'] ) $query['where'] .= " AND t2.nickname LIKE '%".$this->dbcon->escape($_REQUEST['nickname'])."%' ";
		if($_GET['sort_target'] ) {
			$query['where'] .= ' ORDER BY ';
			for($i=0;$i<count($_GET['sort_target']);$i++) {
				if($i>0) {
					$query['where'] .= ', ';
				}
				if($_GET['sort_target'][$i]=='userid') {
					$query['where'] .= 't2.'.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
				} else {
					$query['where'] .= 't1.'.$_GET['sort_target'][$i].' '.$_GET['sort_method'][$i];
				}
			}
		} else {
			$query['where'] .= ' ORDER BY t1.regdate DESC';
		}
		// var_dump($query); exit;
		$list = $this->bList($query,'loop','_lists',true);
		return $list;
	}
	/**
	 * 트랜젝션 리스트 데이터를 JSON 형식으로 출력합니다.
	 * @param number 페이징용으로 사용 할 이전페이지 마지막 줄의 txnid. 첫페이지는 빈값을 사용합니다.
	 * @param number 추출 개수입니다. 기본값 20개입니다.
	 */
	public function _lists($row) {
		$row['deposit_check_time'] = $row['deposit_check_time']>0 ? date('Y-m-d H:i:s', $row['deposit_check_time']) : '';
		return $row;
	}

	public function lock() {
		$query = "update js_exchange_wallet set locked='Y' where userno='{$this->dbcon->escape($_POST['userno'])}' and symbol='{$this->dbcon->escape($_POST['symbol'])}' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}
	public function unlock() {
		$query = "update js_exchange_wallet set locked='N', autolocked='N' where userno='{$this->dbcon->escape($_POST['userno'])}' and symbol='{$this->dbcon->escape($_POST['symbol'])}' ";
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			jsonMsg(1);
		} else {
			jsonMsg(0);
		}
	}
}

function tradeWalletQuery($arr)
{
	$qry = array();
	if(!empty($arr['userno']))  { $qry[] = 'userno=\''.$arr['userno'].'\''; }
	if(!empty($arr['symbol']))  { $qry[] = 'symbol=\''.$arr['symbol'].'\''; }
	if(!empty($arr['locked']))  { $qry[] = 'locked=\''.$arr['locked'].'\''; }
	if(!empty($arr['autolocked']))  { $qry[] = 'autolocked=\''.$arr['autolocked'].'\''; }
	if(!empty($arr['confirmed']))  { $qry[] = 'confirmed=\''.$arr['confirmed'].'\''; }
	if(!empty($arr['unconfirmed']))  { $qry[] = 'unconfirmed=\''.$arr['unconfirmed'].'\''; }
	if(!empty($arr['account']))  { $qry[] = 'account=\''.$arr['account'].'\''; }
	if(!empty($arr['address']))  { $qry[] = 'address=\''.$arr['address'].'\''; }
	if(!empty($arr['deposit_check_time']))  { $qry[] = 'deposit_check_time=\''.$arr['deposit_check_time'].'\''; }
	if($_POST['pg_mode'] == 'write') {
		$qry[] = 'regdate=NOW()';
	} else {
		if(!empty($arr['regdate']))  { $qry[] = 'regdate=\''.$arr['regdate'].'\''; }
	}
	return implode(',',$qry);
}
