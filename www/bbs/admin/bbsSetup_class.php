<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class BbsSetup extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_bbs_info';
		$config['query_func'] = 'setQuery';
		$config['write_mode'] = 'ajax';

		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';

		$config['no_tag'] = array('title');
		$config['no_space'] = array();
		$config['staple_article'] = array();

		$config['bool_file'] = FALSE;
		$config['bool_thumb'] = FALSE;
		$config['bool_navi_page'] = FALSE;

		$config['bool_editor'] = TRUE;
		$config['editor_target'] = array('header','footer');
		$config['limit_img_width'] = 500;

		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		$query['table_name'] = 'js_bbs_info AS a';
		$query['tool'] = 'select';
		$query['fields'] = 'bbscode,
			kind_menu,
			title,
			skin,
			(select level_name from js_member_level where level_code=a.right_access) as right_access,
			(select level_name from js_member_level where level_code=a.right_view) as right_view,
			(select level_name from js_member_level where level_code=a.right_write) as right_write,
			(select level_name from js_member_level where level_code=a.right_del) as right_del,
			(select level_name from js_member_level where level_code=a.right_comment) as right_comment,
			(select count(*) from js_bbs_main where bbscode=a.bbscode) as cnt_bbs,
			(select count(*) from js_bbs_comment where bbscode=a.bbscode) as cnt_comment';
		$query['where'] = 'order by ranking asc';
		$this->bList($query,'loop_bbs');
	}

	/**************************************
	새글쓰기, 수정하기 폼에 관련된 메소드
	**************************************/
	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['where'] = 'where bbscode=\''.$_GET['bbscode'].'\'';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign($row);
		$this->getSkin();
		$this->listLevel();
		$this->rightLevel();
	}

	function newForm()
	{
		$arr = array();
		//basic_setup
		$arr['kind_menu'] = 'comm';
		$arr['bbs_type'] = 'list';
		$arr['bool_main'] = '1';
		$arr['bool_file'] = '0';
		$arr['bool_comment'] = '1';
		$arr['bool_anti_spam'] = '1';
		$arr['bool_notice'] = '0';
		$arr['bool_info_layer'] = '0';
		$arr['bool_no_badword'] = '1';
		$arr['bool_view_list'] = '1';
		$arr['bool_secret'] = '1';
		//design_setup
		$arr['color_rollover'] = '#FFFFFF';
		$arr['color_even'] = '#FFFFFF';
		$arr['color_odd'] = '#FFFFFF';
		$arr['bool_header'] = '0';
		$arr['bool_footer'] = '0';
		//extra_setup
		$arr['thumb_width'] = '150';
		$arr['thumb_height'] = '150';
		$arr['loop_scale'] = '10';
		$arr['page_scale'] = '10';
		$arr['bool_newmark'] = '1';
		$arr['term_newmark'] = '24';
		$arr['bool_hotmark'] = '1';
		$arr['term_hotmark'] = '100';
		$arr['bool_limit_hit'] = '1';
		$arr['term_cookie'] = '24';
		$arr['bool_editor'] = '1';
		$arr['level_code'] = $this->getLowLevel();
		$this->tpl->assign($arr);
		$this->getSkin();
		$this->listLevel();
		$this->rightLevel();
	}

	//스킨정보를 가지고 오는 메소드
	function getSkin()
	{
		$arr_type = array('list','gallery','webzine');
		$loop = array();
		for ($i = 0; $i < sizeof($arr_type) ; $i++) {
			$loop[] = array('skin_type'=>$arr_type[$i]);
			$loop2 = &$loop[$i]['loop_skin'];
			$dp = opendir(ROOT_DIR.'/template/user/bbs');
			while($dir_list = readdir($dp)) {
				if($dir_list != '.' && $dir_list != '..') {
					$regexp = '/^(skin_'.$arr_type[$i].'_\w+)$/';
					if(preg_match($regexp,$dir_list,$matches)) {
						$loop2[] = $matches[1];
					}
				}
			}
			closedir($dp);
		}
		$this->tpl->assign('loop_skin_type',$loop);
	}

	//select 박스에 레벨을 loop시켜주는 메소드
	function listLevel()
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select';
		$query['where'] = 'order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_level',$loop);
	}

	//단계별 허용가능한 레벨을 만들어주는 메소드
	function rightLevel()
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select';
		$query['where'] = 'order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		$arr = array();

		for ($i = 1; $row = mysqli_fetch_assoc($result) ; $i++) {
			$arr[] = $row['level_name'];
			$temp = array();
			$temp['level_code'] = $row['level_code'];
			$temp['level_rank'] = $i;
			$temp['level_name'] = implode(',',$arr);
			$loop[] = $temp;
		}
		$this->tpl->assign('loop_right',$loop);
	}

	/**************************************
	게시판 생성에 관련된 메소드
	**************************************/
	function write()
	{
		$_POST['bbscode'] = $this->getBbsCode();
		$_POST['ranking'] = $this->getRanking();
		$level_code = $this->getLowLevel();
		$arr_right = array('right_access','right_view','right_write','right_del');
		foreach ($arr_right as $key => $val) {
			if(empty($_POST[$val])) {
				$_POST[$val] = $level_code;
			}
		}
		if(empty($_POST['title'])) { $_POST['title'] = '게시판 '.$_POST['bbscode']; }
		$query = array();
		if($this->bWrite($query,$_POST,'none')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}


	function makeCategory()
	{
		if(empty($_POST['bbs_category'])) {

		}
	}

	//게시판코드 리턴
	function getBbsCode()
	{
		$arr = array();
		for($i=0;$i<2 ;$i++) { $arr[] = chr(mt_rand(65,90)); }
		$code = implode('',$arr).mt_rand(100,999);
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where bbscode=\''.$code.'\'';
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt >0) { return $this->getBbsCode(); }
		else { return $code; }
	}

	//순서를 정해서 리턴한다.
	function getRanking()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'max(ranking)';
		$max_ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		return $max_ranking + 1;
	}

	//가장 하위 레벨의 회원레벨코드를 자기고 온다.
	//가장 하위레벨은 비회원(방문자)코드 임
	function getLowLevel()
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select_one';
		$query['fields'] = 'level_code';
		$query['where'] = ' order by ranking desc limit 1';
		$level_code = $this->dbcon->query($query,__FILE__,__LINE__);
		return $level_code;
	}

	function edit()
	{
		//print_r($_POST);
		$arr_checkbox = array(
			'bool_file','bool_thumb','bool_newmark','bool_hotmark','bool_limit_hit',
			'bool_notice','bool_info_layer','bool_no_badword','bool_view_list',
			'bool_comment','bool_secret','bool_header','bool_footer','bool_anti_spam','bool_editor');
		foreach($arr_checkbox as $key => $val) { if(empty($_POST[$val])) { $_POST[$val] = 0; }}
		$query = array();
		$query['where'] = 'where bbscode=\''.$_POST['bbscode'].'\'';
		if($this->bEdit($query,$_POST,'none')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0,'err_edit');
		}
	}

	function editMenu()
	{
		if(empty($_POST['bbscode'])) {
			jsonMsg(0);
		}

		foreach ($_POST['bbscode'] as $key => $val) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'kind_menu=\''.$_POST[$val.'_kind_menu'].'\'';
			$query['where'] = 'where bbscode=\''.$val.'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	function del()
	{
		$query = array();
		$query['where'] = 'where bbscode=\''.$_GET['bbscode'].'\'';
		if($this->bDel($query)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0,'err_del');
		}
	}



	function editRank()
	{
		$ret = array();
		$arr = explode(',',$_GET['rank']);
		for ($i = 0; $i < sizeof($arr) ; $i++) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'ranking='.($i+1);
			$query['where'] = 'where bbscode=\''.$arr[$i].'\'';
			$ret[] = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		if($this->logicalSum($ret)) { jsonMsg(1); }
		else { jsonMsg(0); }
	}
}


function setQuery($arr)
{
	$qry = array();
	if(isset($arr['bbscode']))  { $qry[] = 'bbscode=\''.$arr['bbscode'].'\''; }
	if(isset($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(isset($arr['bool_category']))  { $qry[] = 'bool_category=\''.$arr['bool_category'].'\''; }
	if(isset($arr['bbs_category']))  { $qry[] = 'bbs_category=\''.$arr['bbs_category'].'\''; }
	if(isset($arr['kind_menu']))  { $qry[] = 'kind_menu=\''.$arr['kind_menu'].'\''; }
	if(isset($arr['bbs_type']))  { $qry[] = 'bbs_type=\''.$arr['bbs_type'].'\''; }
	if(isset($arr['skin']))  { $qry[] = 'skin=\''.$arr['skin'].'\''; }
	if(isset($arr['right_access']))  { $qry[] = 'right_access=\''.$arr['right_access'].'\''; }
	if(isset($arr['right_view']))  { $qry[] = 'right_view=\''.$arr['right_view'].'\''; }
	if(isset($arr['right_write']))  { $qry[] = 'right_write=\''.$arr['right_write'].'\''; }
	if(isset($arr['right_del']))  { $qry[] = 'right_del=\''.$arr['right_del'].'\''; }
	if(isset($arr['right_comment']))  { $qry[] = 'right_comment=\''.$arr['right_comment'].'\''; }
	if(isset($arr['loop_scale']))  { $qry[] = 'loop_scale=\''.$arr['loop_scale'].'\''; }
	if(isset($arr['page_scale']))  { $qry[] = 'page_scale=\''.$arr['page_scale'].'\''; }
	if(isset($arr['bool_file']))  { $qry[] = 'bool_file=\''.$arr['bool_file'].'\''; }
	if(isset($arr['bool_thumb']))  { $qry[] = 'bool_thumb=\''.$arr['bool_thumb'].'\''; }
	if(isset($arr['thumb_width']))  { $qry[] = 'thumb_width=\''.$arr['thumb_width'].'\''; }
	if(isset($arr['thumb_height']))  { $qry[] = 'thumb_height=\''.$arr['thumb_height'].'\''; }
	if(isset($arr['string_len']))  { $qry[] = 'string_len=\''.$arr['string_len'].'\''; }
	if(isset($arr['bool_newmark']))  { $qry[] = 'bool_newmark=\''.$arr['bool_newmark'].'\''; }
	if(isset($arr['term_newmark']))  { $qry[] = 'term_newmark=\''.$arr['term_newmark'].'\''; }
	if(isset($arr['bool_hotmark']))  { $qry[] = 'bool_hotmark=\''.$arr['bool_hotmark'].'\''; }
	if(isset($arr['term_hotmark']))  { $qry[] = 'term_hotmark=\''.$arr['term_hotmark'].'\''; }
	if(isset($arr['bool_limit_hit']))  { $qry[] = 'bool_limit_hit=\''.$arr['bool_limit_hit'].'\''; }
	if(isset($arr['term_cookie']))  { $qry[] = 'term_cookie=\''.$arr['term_cookie'].'\''; }
	if(isset($arr['bool_notice']))  { $qry[] = 'bool_notice=\''.$arr['bool_notice'].'\''; }
	if(isset($arr['bool_info_layer']))  { $qry[] = 'bool_info_layer=\''.$arr['bool_info_layer'].'\''; }
	if(isset($arr['bool_no_badword']))  { $qry[] = 'bool_no_badword=\''.$arr['bool_no_badword'].'\''; }
	if(isset($arr['color_rollover']))  { $qry[] = 'color_rollover=\''.$arr['color_rollover'].'\''; }
	if(isset($arr['color_even']))  { $qry[] = 'color_even=\''.$arr['color_even'].'\''; }
	if(isset($arr['color_odd']))  { $qry[] = 'color_odd=\''.$arr['color_odd'].'\''; }
	if(isset($arr['bool_view_list']))  { $qry[] = 'bool_view_list=\''.$arr['bool_view_list'].'\''; }
	if(isset($arr['bool_comment']))  { $qry[] = 'bool_comment=\''.$arr['bool_comment'].'\''; }
	if(isset($arr['bool_secret']))  { $qry[] = 'bool_secret=\''.$arr['bool_secret'].'\''; }
	if(isset($arr['bool_header']))  { $qry[] = 'bool_header=\''.$arr['bool_header'].'\''; }
	if(isset($arr['header']))  { $qry[] = 'header=\''.$arr['header'].'\''; }
	if(isset($arr['bool_footer']))  { $qry[] = 'bool_footer=\''.$arr['bool_footer'].'\''; }
	if(isset($arr['footer']))  { $qry[] = 'footer=\''.$arr['footer'].'\''; }
	if(isset($arr['bool_anti_spam']))  { $qry[] = 'bool_anti_spam=\''.$arr['bool_anti_spam'].'\''; }
	if(isset($arr['bool_editor']))  { $qry[] = 'bool_editor=\''.$arr['bool_editor'].'\''; }
	if(isset($arr['bool_main']))  { $qry[] = 'bool_main=\''.$arr['bool_main'].'\''; }
	if(isset($arr['bool_kisu']))  { $qry[] = 'bool_kisu=\''.$arr['bool_kisu'].'\''; }
	if($arr['pg_mode']=='write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	$qry = implode(',',$qry);
	return $qry;
}

?>