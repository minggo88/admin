<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class ConfigAdmin extends BASIC
{
	var $ext;

	function __construct(&$tpl)
	{

		$config = array();

		$config['table_name'] = 'js_admin';
		$config['query_func'] = 'adminQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/attach';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array('adminid','admin_name','adminpw','remark_admin');
		$config['no_space'] = array('adminid','adminpw');
		$config['staple_article'] = array('adminid'=>'blank','admin_name'=>'blank');
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
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'admin_mobile';
		$query['where'] = 'where adminid=\'admin\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign($row);
		$query = array();
		$query['where'] = 'where kind_admin=\'sub\'';
		$this->bList($query,'loop');
	}

	function write()
	{
		$_POST['kind_admin'] = 'sub';
		$query = array();
		if($this->bWrite($query,$_POST)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function edit()
	{
		$query = array();
		$query['where'] = 'where idx=\''.$_POST['idx'].'\'';
		if($this->bEdit($query,$_POST)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		$arr_bool = array('right_basic','right_goods','right_order','right_member','right_community','right_marketing','right_data','right_design','right_wallet','right_cs','right_statistics','right_auction','right_point','right_video');
		foreach ($arr_bool as $key=>$val) {
			if(empty($arr[$val])) {
				$arr[$val] = 0;
			}
		}
		return $arr;
	}

	function del()
	{
		$query = array();
		$query['where'] = 'where adminid=\''.$_GET['adminid'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function getFormValue()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['where'] = 'where adminid=\''.$_GET['adminid'].'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		unset($row['adminpw']);
		echo $this->json->encode($row);
	}

	function editPasswd()
	{
		if(!empty($_POST['bool_passwd'])) {
			if(empty($_POST['old_passwd'])) {
				jsonMsg(0,'err_old_pw');
			}
			if(empty($_POST['new_passwd']) || empty($_POST['renew_passwd'])) {
				jsonMsg(0,'err_new_pw');
			}
			$_POST['adminpw'] = $_POST['new_passwd'];
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'count';
			$query['where'] = 'where adminid=\'admin\' && adminpw=\''.md5($_POST['old_passwd']).'\'';
			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			if($cnt == 0) {
				jsonMsg(0,'err_pw');
			}
		}

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'update';
		$query['fields'] = adminQuery($_POST);
		$query['where'] = 'where adminid=\'admin\'';

		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			if(!empty($_POST['bool_passwd'])) {
				unset($_SESSION['ADMIN_ID']);
				unset($_SESSION['ADMIN_KIND']);
			}
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function saveOtp() {
		$otpuse = $_POST['otpuse']=='1' ? '1' : '0';
		$this->dbcon->query("update js_admin set otpuse='".$otpuse."' where adminid='".$this->dbcon->escape($_SESSION['ADMIN_ID'])."' ");
		responseSuccess();
	}

	function getAccessLog() {
		$p = $_POST['page']*1>0 ? $_POST['page']*1 : 1;
		$c = $_POST['cnt']*1>0 ? $_POST['cnt']*1 : 10;
		$where = '';
		if(trim($_POST['regdate'])) {
			$where .= " AND regdate LIKE '".$this->dbcon->escape($_POST['regdate'])."%' ";
		}
		if(trim($_POST['adminid'])) {
			$where .= " AND adminid LIKE '".$this->dbcon->escape($_POST['adminid'])."%' ";
		}
		$total_cnt = $this->dbcon->query_unique_value("SELECT count(*) FROM js_admin_log WHERE 1 {$where} ");
		$r = (object) array('total_cnt'=>$total_cnt, 'data'=>array() );
		$r->data = $this->dbcon->query_all_object("SELECT regdate, adminid, ip FROM js_admin_log WHERE 1 {$where}  ORDER BY regdate DESC LIMIT ".( ($p-1)*$c ).", {$c} ");
		$r->sql = "SELECT count(*) FROM js_admin_log WHERE 1 {$where} ";
		exit(json_encode($r));
	}

}

function adminQuery($arr)
{
	$qry = array();
	if(!empty($arr['idx']))  { $qry[] = 'idx=\''.$arr['idx'].'\''; }
	if(!empty($arr['adminid']))  { $qry[] = 'adminid=\''.$arr['adminid'].'\''; }
	if(!empty($arr['admin_name']))  { $qry[] = 'admin_name=\''.$arr['admin_name'].'\''; }
	if(!empty($arr['adminpw']))  { $qry[] = 'adminpw=\''.md5($arr['adminpw']).'\''; }
	if(!empty($arr['admin_mobile']))  { $qry[] = 'admin_mobile=\''.$arr['admin_mobile'].'\''; }
	if(isset($arr['right_basic']))  { $qry[] = 'right_basic='.$arr['right_basic']; }
	if(isset($arr['right_schedule']))  { $qry[] = 'right_schedule='.$arr['right_schedule']; }
	if(isset($arr['right_contents']))  { $qry[] = 'right_contents='.$arr['right_contents']; }
	if(isset($arr['right_goods']))  { $qry[] = 'right_goods='.$arr['right_goods']; }
	if(isset($arr['right_plan']))  { $qry[] = 'right_plan='.$arr['right_plan']; }
	if(isset($arr['right_order']))  { $qry[] = 'right_order='.$arr['right_order']; }
	if(isset($arr['right_member']))  { $qry[] = 'right_member='.$arr['right_member']; }
	if(isset($arr['right_community']))  { $qry[] = 'right_community='.$arr['right_community']; }
	if(isset($arr['right_marketing']))  { $qry[] = 'right_marketing='.$arr['right_marketing']; }
	if(isset($arr['right_data']))  { $qry[] = 'right_data='.$arr['right_data']; }
	if(isset($arr['right_design']))  { $qry[] = 'right_design='.$arr['right_design']; }
	if(isset($arr['right_wallet']))  { $qry[] = 'right_wallet='.$arr['right_wallet']; }
	if(isset($arr['right_cs']))  { $qry[] = 'right_cs='.$arr['right_cs']; }
	if(isset($arr['right_video_m']))  { $qry[] = 'right_video_m='.$arr['right_video_m']; }
	if(isset($arr['right_statistics']))  { $qry[] = 'right_statistics='.$arr['right_statistics']; }
	if(isset($arr['right_auction']))  { $qry[] = 'right_auction='.$arr['right_auction']; }
	if(!empty($arr['kind_admin']))  { $qry[] = 'kind_admin=\''.$arr['kind_admin'].'\''; }
	if(!empty($arr['remark_admin']))  { $qry[] = 'remark_admin=\''.$arr['remark_admin'].'\''; }
	if(isset($arr['right_point']))  { $qry[] = 'right_point='.$arr['right_point']; }
	if(isset($arr['right_video']))  { $qry[] = 'right_video='.$arr['right_video']; }
	if(isset($arr['right_shareholder']))  { $qry[] = 'right_shareholder='.$arr['right_shareholder']; }
	$qry = implode(',',$qry);
	return $qry;
}

?>