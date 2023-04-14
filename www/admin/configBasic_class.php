<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

class ConfigBasic extends BASIC
{
	function ConfigBasic(&$tpl)
	{
		$config = array();
		$config['table_name'] = 'js_config_basic';
		$config['query_func'] = 'configBasicQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		/*
		$config['no_tag'] = array('license_company','license_company','license_ceo','license_address','license_uptae','license_jongmok',
		'shop_name','shop_ename','shop_url','shop_admin_email','shop_private_clerk','shop_clerk_email','shop_address');
		*/
		$config['no_space'] = array('license_no','license_sale','license_opendate','shop_phone','shop_fax','shop_clerk_email');
		$config['staple_article'] = array();
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array('img_logo','img_sub_logo','img_footer_logo','img_map');
		$config['file_size'] = 2;
		$config['upload_limit'] = TRUE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_size'] = array();
		/*
		$config['thumb_size'] = array('aaa'=>'200x300');
		*/
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

	function viewForm()
	{
		$this->insertRecord();
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['where'] = "where code='".getSiteCode()."' ";
		$row = $this->bEditForm($query);
		$this->tpl->assign($row);
	}

	function edit()
	{
		$this->insertRecord();
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['where'] = "where code='".getSiteCode()."' ";

		if($this->bEdit($query,$_POST,'_write')) {
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

	function insertRecord()
	{
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['tool'] = 'count';
		$query['where'] = "where code='".getSiteCode()."' ";
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if(empty($cnt)) {
			$query['tool'] = 'insert';
			$query['fields'] = "where code='".getSiteCode()."' ";
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
	}
}

function configBasicQuery($arr)
{
	$qry = array();
	if(!empty($arr['code']))  { $qry[] = 'code=\''.$arr['code'].'\''; }
	if(!empty($arr['license_company']))  { $qry[] = 'license_company=\''.$arr['license_company'].'\''; }
	if(!empty($arr['license_ceo']))  { $qry[] = 'license_ceo=\''.$arr['license_ceo'].'\''; }
	if(!empty($arr['license_no']))  { $qry[] = 'license_no=\''.$arr['license_no'].'\''; }
	if(!empty($arr['license_sale']))  { $qry[] = 'license_sale=\''.$arr['license_sale'].'\''; }
	if(!empty($arr['license_address']))  { $qry[] = 'license_address=\''.$arr['license_address'].'\''; }
	if(!empty($arr['license_uptae']))  { $qry[] = 'license_uptae=\''.$arr['license_uptae'].'\''; }
	if(!empty($arr['license_jongmok']))  { $qry[] = 'license_jongmok=\''.$arr['license_jongmok'].'\''; }
	if(!empty($arr['license_opendate']))  { $qry[] = 'license_opendate=\''.$arr['license_opendate'].'\''; }
	if(!empty($arr['shop_name']))  { $qry[] = 'shop_name=\''.$arr['shop_name'].'\''; }
	if(!empty($arr['shop_ename']))  { $qry[] = 'shop_ename=\''.$arr['shop_ename'].'\''; }
	if(!empty($arr['shop_url']))  { $qry[] = 'shop_url=\''.$arr['shop_url'].'\''; }
	if(!empty($arr['shop_admin_email']))  { $qry[] = 'shop_admin_email=\''.$arr['shop_admin_email'].'\''; }
	if(!empty($arr['shop_phone']))  { $qry[] = 'shop_phone=\''.$arr['shop_phone'].'\''; }
	if(!empty($arr['shop_fax']))  { $qry[] = 'shop_fax=\''.$arr['shop_fax'].'\''; }
	if(!empty($arr['shop_private_clerk']))  { $qry[] = 'shop_private_clerk=\''.$arr['shop_private_clerk'].'\''; }
	if(!empty($arr['shop_clerk_email']))  { $qry[] = 'shop_clerk_email=\''.$arr['shop_clerk_email'].'\''; }
	if(!empty($arr['shop_address']))  { $qry[] = 'shop_address=\''.$arr['shop_address'].'\''; }
	if(!empty($arr['clause_information']))  { $qry[] = 'clause_information=\''.$arr['clause_information'].'\''; }
	if(!empty($arr['clause_information01']))  { $qry[] = 'clause_information01=\''.$arr['clause_information01'].'\''; }
	if(!empty($arr['clause_information02']))  { $qry[] = 'clause_information02=\''.$arr['clause_information02'].'\''; }
	if(!empty($arr['clause_information03']))  { $qry[] = 'clause_information03=\''.$arr['clause_information03'].'\''; }
	if(!empty($arr['clause_agreement']))  { $qry[] = 'clause_agreement=\''.$arr['clause_agreement'].'\''; }
	if(!empty($arr['clause_private']))  { $qry[] = 'clause_private=\''.$arr['clause_private'].'\''; }
	if(isset($arr['bool_exchange']))  { $qry[] = 'bool_exchange=\''.$arr['bool_exchange'].'\''; }
	if(!empty($arr['exchange_period']))  { $qry[] = 'exchange_period=\''.$arr['exchange_period'].'\''; }
	if(!empty($arr['exchange_order']))  { $qry[] = 'exchange_order=\''.$arr['exchange_order'].'\''; }
	if(isset($arr['bool_back']))  { $qry[] = 'bool_back=\''.$arr['bool_back'].'\''; }
	if(!empty($arr['back_period']))  { $qry[] = 'back_period=\''.$arr['back_period'].'\''; }
	if(!empty($arr['clause_exchange']))  { $qry[] = 'clause_exchange=\''.$arr['clause_exchange'].'\''; }
	if(!empty($arr['site_title']))  { $qry[] = 'site_title=\''.$arr['site_title'].'\''; }
	if(!empty($arr['site_keyword']))  { $qry[] = 'site_keyword=\''.$arr['site_keyword'].'\''; }
	if(!empty($arr['site_description']))  { $qry[] = 'site_description=\''.$arr['site_description'].'\''; }
	if(!empty($arr['img_logo']))  { $qry[] = 'img_logo=\''.$arr['img_logo'].'\''; }
	if(!empty($arr['img_sub_logo']))  { $qry[] = 'img_sub_logo=\''.$arr['img_sub_logo'].'\''; }
	if(!empty($arr['img_footer_logo']))  { $qry[] = 'img_footer_logo=\''.$arr['img_footer_logo'].'\''; }	
	if(!empty($arr['img_map']))  { $qry[] = 'img_map=\''.$arr['img_map'].'\''; }
	if(isset($arr['bool_ssl']))  { $qry[] = 'bool_ssl=\''.$arr['bool_ssl'].'\''; }
	if(!empty($arr['ssl_port']))  { $qry[] = 'ssl_port=\''.$arr['ssl_port'].'\''; }
	return implode(',',$qry);
}

?>