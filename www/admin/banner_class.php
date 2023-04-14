<?php
/*--------------------------------------------
Date : 2012-08-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class Banner extends BASIC
{
	function Banner(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_banner';
		$config['query_func'] = 'bannerQuery';
		$config['write_mode'] = 'ajax';
		/************************************/
		$config['file_dir'] = '/data/design';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';
		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
		$config['staple_article'] = array();
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array('img_banner');
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
		$config['page_scale'] = 5;
		$config['navi_url'] = $_SERVER['SCRIPT_NAME'];
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		if(empty($_GET['bannercode'])) {
			errMsg(Lang::admin_5);
		}
		$this->getBannerInfo($_GET['bannercode']);
		$query = array();
		$query['where'] = 'where bannercode=\''.$_GET['bannercode'].'\'';
		$this->bList($query,'loop_banner');
	}

	function _lists($row)
	{
		return $row;
	}

	function infoList()
	{
		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['tool'] = 'select';
		$query['fields'] = 'bannercode,
			title,
			size_x,
			size_y,
			remark,
			bool_slide,
			regdate';
		$this->bList($query,'loop_banner_info','none');
	}

	function infoNewForm()
	{
		$arr = array();
		$arr['bool_slide'] = 0;
		$this->tpl->assign($arr);
	}

	function newForm()
	{
		if(empty($_GET['bannercode'])) {
			errMsg(Lang::admin_5);
		}
		$this->getBannerInfo($_GET['bannercode']);
		$arr = array();
		$arr['bool_banner'] = 1;
		$this->tpl->assign($arr);
	}

	function infoEditForm()
	{
		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['where'] = 'where  bannercode=\''.$_GET['bannercode'].'\'';
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		if(empty($_GET['bannercode'])) {
			errMsg(Lang::admin_5);
		}
		if(empty($_GET['idx'])) {
			errMsg(Lang::admin_7);
		}
		$this->getBannerInfo($_GET['bannercode']);
		$query = array();
		$query['where'] = 'where  idx=\''.$_GET['idx'].'\'';
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function getBannerInfo($bannercode,$bool_return = 0)
	{
		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['tool'] = 'row';
		$query['fields'] = 'bannercode, title, size_x, size_y, remark, bool_slide';
		$query['where'] = 'where bannercode=\''.$bannercode.'\'';
		$banner_info = $this->dbcon->query($query,__FILE__,__LINE__);
		if($bool_return > 0) {
			return $banner_info;
		}
		else {
			$this->tpl->assign('banner_info',$banner_info);
		}
	}

	function writeInfo()
	{
		$_POST['bannercode'] = $this->getBannerCode();
		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['tool'] = 'insert';
		$query['fields'] = bannerInfoQuery($_POST);
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function write()
	{
		$this->imgSizeLimit($_POST['bannercode']);
		if(empty($_POST['bannercode'])) {
			jsonMsg(0);
		}
		$query = array();
		if($this->bWrite($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function editInfo()
	{
		$this->config['query_func'] = 'bannerInfoQuery';
		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['where'] = 'where bannercode=\''.$_POST['bannercode'].'\'';
		if($this->bEdit($query,$_POST,'_write')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function edit()
	{
		//이미지 사이즈 제한 스크립트
		if(empty($_POST['bannercode']) || empty($_POST['idx'])) {
			jsonMsg(0);
		}
		$this->imgSizeLimit($_POST['bannercode']);
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
		return $arr;
	}

	function imgSizeLimit($bannercode)
	{
		if(!empty($_FILES['img_banner']['size'])) {
			$info = $this->getBannerInfo($bannercode,1);
			list($width, $height, $type, $attr) = getimagesize($_FILES['img_banner']['tmp_name']);
			if($info['size_x'] != $width || $info['size_y'] != $height ) {
				jsonMsg(0,'err_size');
			}
		}
	}

	function getBannerCode()
	{
		$arr = array();
		for($i=0;$i<2 ;$i++) { $arr[] = chr(mt_rand(65,90)); }
		$code = implode('',$arr).mt_rand(10,99);
		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['tool'] = 'count';
		$query['where'] = 'where bannercode=\''.$code.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt >0) {
			return $this->getBannerCode();
		}
		else {
			return $code;
		}
	}

	function del()
	{
		$query = array();
		$query['where'] = 'where idx=\''.$_GET['idx'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function delInfo()
	{
		//배너가 존재하고있는지 여부를 확인
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where bannercode=\''.$_GET['bannercode'].'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt > 0) {
			jsonMsg(0,'err_exist_banner');
		}

		$query = array();
		$query['table_name'] = 'js_banner_info';
		$query['tool'] = 'delete';
		$query['where'] = 'where bannercode=\''.$_GET['bannercode'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		
		$arr[] = empty($_GET['start']) ? 'start=0' : 'start='.$_GET['start'];
		if(!empty($idx)) { $arr[] = 'idx='.$idx; }
		if(!empty($_GET['s_val'])) { $arr[] = 's_val='.$_GET['s_val']; }
		$ret = '&'.implode('&',$arr);
		return $ret;
	}
}

function bannerInfoQuery($arr)
{
	$qry = array();
	if(!empty($arr['bannercode']))  { $qry[] = 'bannercode=\''.$arr['bannercode'].'\''; }
	if(!empty($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(isset($arr['size_x']))  { $qry[] = 'size_x=\''.$arr['size_x'].'\''; }
	if(isset($arr['size_y']))  { $qry[] = 'size_y=\''.$arr['size_y'].'\''; }
	if(!empty($arr['remark']))  { $qry[] = 'remark=\''.$arr['remark'].'\''; }
	if(isset($arr['bool_slide']))  { $qry[] = 'bool_slide=\''.$arr['bool_slide'].'\''; }
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	return implode(',',$qry);
}

function bannerQuery($arr)
{
	$qry = array();
	if(!empty($arr['bannercode']))  { $qry[] = 'bannercode=\''.$arr['bannercode'].'\''; }
	if(isset($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(isset($arr['bool_banner']))  { $qry[] = 'bool_banner=\''.$arr['bool_banner'].'\''; }
	if(!empty($arr['img_banner']))  { $qry[] = 'img_banner=\''.$arr['img_banner'].'\''; }
	if(isset($arr['banner_url']))  { $qry[] = 'banner_url=\''.$arr['banner_url'].'\''; }
	return implode(',',$qry);
}

?>