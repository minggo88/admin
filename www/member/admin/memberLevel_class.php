<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

class MemberLevel extends BASIC
{
	function MemberLevel(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_member_level';
		$config['query_func'] = 'levelQuery';
		$config['write_mode'] = 'ajax';

		/************************************/
		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';

		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
		$config['staple_article'] = array('level_name'=>'blank');

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
		$this->checkBasicLevel();


		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';
		$query['fields'] = "level_code,
			level_name,
			bool_basic,
			(select count(*) from js_member where level_code=js_member_level.level_code) AS cnt_member,
			kind_level,
			ranking";
		$query['where'] = ' order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_level',$loop);
	}

	function editForm()
	{
		$query = array();
		$query['where'] = 'where level_code=\''.$_GET['level_code'].'\'';
		$arr = $this->bEditForm($query);
		$this->tpl->assign($arr);
	}

	function write()
	{
		//필수 레벨외에 8개까지 생성가능함
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';//select,select_one,select_affect,row,,insert,insert_idx,update,delete,drop
		$query['where'] = 'where bool_basic < 1';
		$cnt_basic = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt_basic  >= 8) {
			jsonMsg(0,'err_max');
		}

		if(empty($_POST['level_code'])) {
			$_POST['level_code'] = $this->getLevelCode();
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'max(ranking)';
		$query['where'] = 'where bool_basic=0';
		$max_ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		if(empty($max_ranking)) {
			$max_ranking = 1;
		}
		$_POST['ranking'] = $max_ranking+1;
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
		$query['where'] = 'where level_code=\''.$_POST['level_code'].'\'';
		if($this->bEdit($query,$_POST)) {
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

	function del()
	{
		//필수요소인지 확인
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'bool_basic';
		$query['where'] = 'where level_code=\''.$_GET['level_code'].'\'';
		$bool_basic = $this->dbcon->query($query,__FILE__,__LINE__);
		if($bool_basic > 0) {
			jsonMsg(0,'err_basic');
		}
		//기본회원레벨로 전환한다.
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'level_code';
		$query['where'] = 'where ranking <100 && bool_basic=1 order by ranking desc';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'update';
		$query['fields'] = 'level_code=\''.$row['level_code'].'\'';
		$query['where'] = 'where level_code=\''.$_GET['level_code'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);

		if(!$result) {
			jsonMsg(0);
		}
		$query = array();
		$query['where'] = 'where level_code=\''.$_GET['level_code'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function saveOrder()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';
		$query['fields'] = 'level_code,ranking';
		$query['where'] = 'where bool_basic=1';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr_basic = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$arr_basic[$row['level_code']] = $row['ranking'];
		}
		$arr_code = array_slice($_GET['level_table'], 1);
		for ($i = 0,$j=1; $i < sizeof($arr_code) ; $i++,$j++) {
			if(empty($arr_basic[$arr_code[$i]])) {
				$ranking = $j;	
			}
			else {
				$ranking = $arr_basic[$arr_code[$i]];
			}
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'ranking='.$ranking;
			$query['where'] = 'where level_code=\''.$arr_code[$i].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function changeLevel()
	{
		if(empty($_POST['target_level']) || empty($_POST['target_level'])) {
			jsonMsg(0);
		}

		$query = array();
		$query['table_name'] = 'js_member';
		$query['tool'] = 'update';
		$query['fields'] = 'level_code=\''.$_POST['level_code'].'\'';
		$query['where'] = 'where level_code=\''.$_POST['target_level'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	//기본 레벨이 있는지 확인하고 없으면 데이터를 추가한다.
	function checkBasicLevel()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt == 0) {
			$arr = array(
				array('ranking'=>1,'level_name'=>'관리자'),
				array('ranking'=>10,'level_name'=>'회원'),
				array('ranking'=>100,'level_name'=>'비회원')				
			);
			foreach ($arr as $key => $val) {
				$temp = array();
				$temp['level_code'] = $this->getLevelCode();
				$temp['level_name'] = $val['level_name'];
				$temp['bool_basic'] = 1;
				$temp['ranking'] = $val['ranking'];
				$query['tool'] = 'insert';
				$query['fields'] = levelQuery($temp);
				$this->dbcon->query($query,__FILE__,__LINE__);
			}
		}
	}

	function getLevelCode()
	{
		$arr = array();
		for($i=0;$i<2 ;$i++) {
			$arr[] = chr(mt_rand(65,90));
		}
		$code = implode('',$arr).mt_rand(10,99);
		$query = array();
		$query['tool'] = 'count';
		$query['table_name'] = $this->config['table_name'];
		$query['where'] = 'where level_code=\''.$code.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt >0) {
			return $this->getLevelCode();
		}
		else {
			return $code;
		}
	}
}

function levelQuery($arr)
{
	$qry = array();
	if(!empty($arr['level_code']))  { $qry[] = 'level_code=\''.$arr['level_code'].'\''; }
	if(isset($arr['level_name']))  { $qry[] = 'level_name=\''.$arr['level_name'].'\''; }
	if(!empty($arr['bool_basic']))  { $qry[] = 'bool_basic='.$arr['bool_basic']; }
	if(!empty($arr['ranking']))  { $qry[] = 'ranking='.$arr['ranking']; }
	return implode(',',$qry);
}

?>