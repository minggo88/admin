<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
class ConfigDelivery extends BASIC
{
	function ConfigDelivery(&$tpl)
	{

		$config = array();

		$config['table_name'] = 'js_config_delivery';
		$config['query_func'] = 'setQuery';
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

	function viewForm()
	{
		$this->insertRecord();
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = "where code='".getSiteCode()."' ";
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);

		//배송지역 루푸 돌리기
		$query = array();
		$query['table_name'] = 'js_config_delivery_region';
		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_region',$loop);
		$query = array();
		$query['table_name'] = 'js_config_delivery_company';
		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_company',$loop);
	}

	function edit()
	{
		$this->insertRecord();
		$query = array();
		$query['where'] = "where code='".getSiteCode()."' ";
		
		if($_POST['pg_mode'] == 'edit') {
			if(empty($_POST['bool_homedelivery'])) {
				$_POST['bool_homedelivery'] = 0;
			}
			if(empty($_POST['bool_quick'])) {
				$_POST['bool_quick'] = 0;
			}
			if(empty($_POST['bool_selfdelivery'])) {
				$_POST['bool_selfdelivery'] = 0;
			}
			$this->editRegion();
			$result = $this->bEdit($query,$_POST);
		}
		else if($_POST['pg_mode'] == 'edit_company') {
			$result = $this->editCompany();
		}
		else {
			$result = $this->bEdit($query,$_POST);
		}

		if($result) {
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

	function editRegion()
	{
		$query = array();
		$query['table_name'] = 'js_config_delivery_region';
		$query['tool'] = 'delete';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			if(!empty($_POST['delivery_region'])) {
				for ($i = 0; $i < sizeof($_POST['delivery_region']) ; $i++) {
					$arr = array();
					if(!empty($_POST['delivery_region'][$i])) {
						$arr['delivery_region'] = $_POST['delivery_region'][$i];
					}

					if(!empty($_POST['delivery_region_fee'][$i])) {
						$arr['delivery_region_fee'] = $_POST['delivery_region_fee'][$i];
					}

					if(!empty($arr)) {
						$query['tool'] = 'insert';
						$query['fields'] = regionQuery($arr);
						$this->dbcon->query($query,__FILE__,__LINE__);
					}
				}
			}
			return true;
		}
		else { return false; }
	}

	function editCompany()
	{
		$query = array();
		$query['table_name'] = 'js_config_delivery_company';
		$query['tool'] = 'delete';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			for ($i = 0; $i < sizeof($_POST['company_name']) ; $i++) {
				$arr = array();
				if(!empty($_POST['company_name'][$i])) {
					$arr['company_name'] = $_POST['company_name'][$i];
				}
				if(!empty($_POST['company_phone'][$i])) {
					$arr['company_phone'] = $_POST['company_phone'][$i];
				}
				if(!empty($_POST['company_url'][$i])) {
					$arr['company_url'] = $_POST['company_url'][$i];
				}

				if(!empty($arr)) {
					$query['tool'] = 'insert';
					$query['fields'] = companyQuery($arr);
					$this->dbcon->query($query,__FILE__,__LINE__);
				}
			}
			return true;
		}
		else { return false; }
	}

	function insertRecord()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = "where code='".getSiteCode()."' ";
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			$query['tool'] = 'insert';
			$query['fields'] = " code='".getSiteCode()."' ";
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
	}
}

function setQuery($arr)
{
	$qry = array();
	if(!empty($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(isset($arr['bool_homedelivery']))  { $qry[] = 'bool_homedelivery='.$arr['bool_homedelivery']; }
	if(isset($arr['bool_quick']))  { $qry[] = 'bool_quick='.$arr['bool_quick']; }
	if(isset($arr['bool_selfdelivery']))  { $qry[] = 'bool_selfdelivery='.$arr['bool_selfdelivery']; }
	if(!empty($arr['kind_fee']))  { $qry[] = 'kind_fee=\''.$arr['kind_fee'].'\''; }
	if(!empty($arr['homedelivery_pre_fee']))  { $qry[] = 'homedelivery_pre_fee='.$arr['homedelivery_pre_fee']; }
	if(!empty($arr['homedelivery_post_fee']))  { $qry[] = 'homedelivery_post_fee='.$arr['homedelivery_post_fee']; }
	if(!empty($arr['delivery_free_amount']))  { $qry[] = 'delivery_free_amount='.$arr['delivery_free_amount']; }
	if(isset($arr['bool_box_weight']))  { $qry[] = 'bool_box_weight='.$arr['bool_box_weight']; }
	if(!empty($arr['clause_delivery']))  { $qry[] = 'clause_delivery=\''.$arr['clause_delivery'].'\''; }
	return implode(',',$qry);
}

function regionQuery($arr)
{
	$qry = array();
	if(!empty($arr['delivery_region']))  { $qry[] = 'delivery_region=\''.$arr['delivery_region'].'\''; }
	if(!empty($arr['delivery_region_fee']))  { $qry[] = 'delivery_region_fee='.$arr['delivery_region_fee']; }
	return implode(',',$qry);
}

function companyQuery($arr)
{
	$qry = array();
	if(!empty($arr['company_name']))  { $qry[] = 'company_name=\''.$arr['company_name'].'\''; }
	if(!empty($arr['company_phone']))  { $qry[] = 'company_phone=\''.$arr['company_phone'].'\''; }
	if(!empty($arr['company_url']))  { $qry[] = 'company_url=\''.$arr['company_url'].'\''; }
	return implode(',',$qry);
}

?>