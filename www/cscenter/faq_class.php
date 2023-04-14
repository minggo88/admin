<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class Faq extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_faq';
		$config['query_func'] = 'faqQuery';
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
		$config['bool_editor'] = FALSE;
		$config['editor_target'] = array();
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = FALSE;
		$config['editor_thumb_width'] = 150;
		$config['editor_thumb_height'] = 150;
		/************************************/
		$config['bool_navi_page'] = TRUE;
		$config['bool_navi_justify'] = 'center'; // '', 'center, end

		if($tpl->skin == 'admin') {
			$config['bool_navi_bind'] = FALSE;
			$config['kind_menu'] = 'faq';
		} else {
			$config['bool_navi_bind'] = TRUE;
			$config['kind_menu'] = 'faq';
		}

		$config['loop_scale'] = 10;
		$config['page_scale'] = 5;
		$config['navi_url'] = 'faq.php';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
		
		$this->lang_c = $_GET['language'];
		if(!$this->lang_c || $this->lang_c=='ko') $this->lang_c='kr';
	}

	function lists()
	{
		if(empty($_GET['faqcode'])) {
			$faqcode = '';
		} else {
			$faqcode = ' && faqcode=\''.$_GET['faqcode'].'\'';
		}
		// $lang_c = $_GET['language'];
		// if(!$lang_c || $lang_c=='ko') $lang_c='kr';
		$query = array();
		$query['fields'] = "idx, faqcode, IF(subject_{$this->lang_c}='', `subject_kr`, subject_{$this->lang_c}) `subject`, IF(contents_{$this->lang_c}='', contents_kr, contents_{$this->lang_c}) `contents`";
		$query['where'] = 'WHERE 1 '.$faqcode.$this->srchQry();
		$query['where'].= 'ORDER BY idx DESC';
		$this->config['navi_qry'] = $this->srchUrl();
		$this->bList($query,'loop_faq');
		$this->tpl->assign('srch_url',$this->srchUrl());
	}

	function _lists($row)
	{
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'select_one';
		$query['fields'] = "IF(title_{$this->lang_c}='', `title_kr`, title_{$this->lang_c}) `title`";
		$query['where'] = 'where faqcode=\''.$row['faqcode'].'\'';
		$row['title'] = $this->dbcon->query($query,__FILE__,__LINE__);
        $row['subject'] = htmlspecialchars(strip_tags($row['subject']));
        $row['contents'] = $row['contents'];

		return $row;
	}

	function listCode()
	{
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'select';
		$query['fields'] = "faqcode, IF(title_{$this->lang_c}='', `title_kr`, title_{$this->lang_c}) `title`, ranking";
		$query['where'] = 'order by ranking asc';

		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_code',$loop);
	}

	function newForm()
	{
		$arr = array();
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		$query = array();
		$query['where'] = 'where  idx=\''.$_GET['idx'].'\'';
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
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
		$query = array();
		$query['where'] = 'where idx=\''.$_POST['idx'].'\'';
		if($this->bEdit($query,$_POST,'_write')) { jsonMsg(1); }
		else { jsonMsg(0); }
	}

	function _write($arr)
	{
		return $arr;
	}

	function writeCode()
	{
		$code = $this->getFaqCode();
		//ranking 최대값 가지고 오기
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'select_one';
		$query['fields'] = 'MAX(ranking)';
		$max_ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		$ranking = $max_ranking + 1;

		$title = urldecode($_GET['title']);
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'insert';
		$query['fields'] = 'faqcode=\''.$code.'\',title=\''.$title.'\',ranking=\''.$ranking.'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		$query['tool'] = 'select';
		$query['fields'] = '';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[$row['faqcode']] = $row['title'];
		}
		echo $this->json->encode($arr);
	}

	function getFaqCode()
	{
		$arr = array();
		for($i=0;$i<2 ;$i++) { $arr[] = chr(mt_rand(65,90)); }
		$code = implode('',$arr).mt_rand(10,99);
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'count';
		$query['where'] = 'where faqcode=\''.$code.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt >0) {
			return $this->getFaqCode();
		}
		else {
			return $code;
		}
	}

	function editCode()
	{
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'update';
		$query['fields'] = 'title=\''.$_GET['title'].'\'';
		$query['where'] = 'where faqcode=\''.$_GET['faqcode'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[$row['faqcode']] = $row['title'];
		}
		echo $this->json->encode($arr);
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

	function delMulti()
	{
		foreach ($_GET['idxs'] as $key => $val) {
			$query = array();
			$query['where'] = 'where idx=\''.$val.'\'';
			if(!$this->bDel($query)) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function delCode()
	{

		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';
		$query['where'] = 'where faqcode=\''.$_GET['faqcode'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		while ($row = mysqli_fetch_assoc($result)) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['where'] = 'where idx='.$row['idx'];
			$result_del = $this->bDel($query);
			if(!$result_del) {
				jsonMsg(0);
			}
		}
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'delete';
		$query['where'] = 'where faqcode=\''.$_GET['faqcode'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		$query = array();
		$query['table_name'] = 'js_faq_info';
		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr[$row['faqcode']] = $row['title'];
		}
		echo $this->json->encode($arr);
	}

	function srchQry()
	{
		$ret = '';
		if(!empty($_GET['s_val'])) {
			$_GET['s_val'] = urldecode($_GET['s_val']);
			$ret = " && (subject_{$this->lang_c} like '%{$_GET['s_val']}%' || contents_{$this->lang_c} like '%{$_GET['s_val']}%')";
		}
		return $ret;
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();

		//$arr[] = empty($_GET['start']) ? 'start=0' : 'start='.$_GET['start'];
		if(!empty($idx)) { $arr[] = 'idx='.$idx; }
		if(!empty($_GET['gnb_code'])) { $arr[] = 'gnb_code='.$_GET['gnb_code']; }
		if(!empty($_GET['cate_code'])) { $arr[] = 'cate_code='.$_GET['cate_code']; }
		if(!empty($_GET['s_val'])) { $arr[] = 's_val='.$_GET['s_val']; }
		$ret = '&'.implode('&',$arr);
		return $ret;
	}
}

function faqQuery($arr)
{
	$qry = array();
	if(!empty($arr['faqcode']))  { $qry[] = 'faqcode=\''.$arr['faqcode'].'\''; }
	if(!empty($arr['subject']))  { $qry[] = 'subject=\''.$arr['subject'].'\''; }
	if(!empty($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if(!empty($arr['subject_kr']))  { $qry[] = 'subject_kr=\''.$arr['subject_kr'].'\''; }
	if(!empty($arr['contents_kr']))  { $qry[] = 'contents_kr=\''.$arr['contents_kr'].'\''; }
	if(!empty($arr['subject_cn']))  { $qry[] = 'subject_cn=\''.$arr['subject_cn'].'\''; }
	if(!empty($arr['contents_cn']))  { $qry[] = 'contents_cn=\''.$arr['contents_cn'].'\''; }
	if(!empty($arr['subject_en']))  { $qry[] = 'subject_en=\''.$arr['subject_en'].'\''; }
	if(!empty($arr['contents_en']))  { $qry[] = 'contents_en=\''.$arr['contents_en'].'\''; }
	return implode(',',$qry);
}

?>