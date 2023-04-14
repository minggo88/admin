<?php
/*--------------------------------------------
Date : 2012-05-23
Author : Danny Hwang
comment :
--------------------------------------------*/

class BBS extends BASIC
{
	function __construct(&$tpl)
	{
		global $dbcon;

		//게시판 설정 내용을 가지고 온다.
		$query = array();
		$query['table_name'] = 'js_bbs_info';
		$query['tool'] = 'row';
		$query['fields'] = 'title,bool_category,bbs_category,kind_menu,bbs_type,skin,
			right_access,right_view,right_write,right_del,right_comment,
			loop_scale,page_scale,
			bool_file,
			bool_thumb,thumb_width,thumb_height,
			string_len,
			bool_newmark,term_newmark,
			bool_hotmark,term_hotmark,
			bool_limit_hit,term_cookie,
			bool_notice,bool_info_layer,bool_no_badword,
			color_rollover,color_even,color_odd,
			bool_view_list,bool_comment,bool_secret,bool_kisu,
			bool_header,header,
			bool_footer,footer,
			bool_anti_spam,bool_editor,bool_main';
		$query['where'] = 'where bbscode=\''.$_REQUEST['bbscode'].'\'';
		$info_bbs = $dbcon->query($query,__FILE__,__LINE__);

		$this->info_bbs = $info_bbs;

		$config = array();
		$config['table_name'] = 'js_bbs_main';
		$config['query_func'] = 'bbsQuery';
		$config['write_mode'] = 'ajax';//ajax or link

		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['editor_dir'] = '/data/editor';

		$config['no_tag'] = array('name','passwd','subject_kr','subject_en','subject_cn');
		$config['no_space'] = array('passwd');
		// $config['staple_article'] = array('author'=>'blank','passwd'=>'blank','subject_kr'=>'blank','subject_en'=>'blank','subject_cn'=>'blank','contents_kr'=>'blank','contents_en'=>'blank','contents_cn'=>'blank');
		$config['staple_article'] = array('author'=>'blank','subject_kr'=>'blank','contents_kr'=>'blank');

		$config['bool_file'] = $this->info_bbs['bool_file'];
		$config['file_target'] = array('file');
		$config['file_size'] = 100;
		$config['file_storage_type'] = 'aws_s3'; // 미정의(기본값)시 웹서버 $config['file_dir']에 파일로 저장
		$config['upload_limit'] = TRUE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_width'] = 0;
		$config['thumb_height'] = 0;
		$config['thumb_size'] = array();

		$config['bool_editor'] = $this->info_bbs['bool_editor'];
		$config['editor_target'] = array('contents_kr','contents_en','contents_cn');
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = TRUE;
		$config['editor_thumb_resize'] = TRUE;
		$config['editor_thumb_width'] = $this->info_bbs['thumb_width'];
		$config['editor_thumb_height'] = $this->info_bbs['thumb_height'];

		$config['bool_navi_page'] = TRUE;

		if($tpl->skin == 'admin') {
			$config['bool_navi_bind'] = FALSE;
			$config['kind_menu'] = $this->info_bbs['kind_menu'];
		} else {
			$config['bool_navi_bind'] = TRUE;
			$config['kind_menu'] = $this->info_bbs['kind_menu'];
		}

		$config['bool_navi_justify'] = 'center'; // '', 'center, end
		$config['loop_scale'] = empty($this->info_bbs['loop_scale']) ? 10 : $this->info_bbs['loop_scale'];
		$config['page_scale'] = empty($this->info_bbs['page_scale']) ? 10 : $this->info_bbs['page_scale'];
		$config['navi_url'] = '?';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';// ajax or link
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
		$this->tpl->assign('info_bbs',$this->info_bbs);
		
		$this->lang_c = $_GET['language'];
		if(!$this->lang_c || $this->lang_c=='ko') $this->lang_c='kr';

	}

	/**************************************
	pg_mode = list
	**************************************/
	function lists($loop_id='loop_bbs_main',$sub_method='_lists')
	{
		$query = array();
		$query['fields'] = "idx,
			userid,
			category,
			author,
			subject_kr,
			subject_en,
			subject_cn,
			IF(subject_{$this->lang_c}='', `subject_kr`, subject_{$this->lang_c}) `subject`,
			email,
			contents_kr,
			contents_en,
			contents_cn,
			IF(contents_{$this->lang_c}='', contents_kr, contents_{$this->lang_c}) `contents`,
			depth,
			bool_secret,
			hit,
			file,
			thread,
			recommand,
			regdate";
		if($loop_id =='loop_bbs_main') {
			$query['where'] = 'where bbscode=\''.$_GET['bbscode'].'\' && division = \'b\' '.$this->srchQry().' order by pos desc';
		}
		else {
			if ($this->info_bbs['bool_kisu'] > 0) {
				$query['where'] = 'where bbscode=\''.$_GET['bbscode'].'\' && division = \'a\' && category=\''.$_SESSION['USER_KISU'].'\' '.$this->srchQry().' order by pos desc';
			}
			else {
				$query['where'] = 'where bbscode=\''.$_GET['bbscode'].'\' && division = \'a\''.$this->srchQry().' order by pos desc';
			}
		}
		$this->config['navi_qry'] = $this->srchUrl('',FALSE);
		$this->bList($query,$loop_id,$sub_method);
		$this->tpl->assign('srch_url',$this->srchUrl());
		if($this->info_bbs['bool_category']) {
			$this->loopCategory();
		}
	}

	function _lists($row)
	{
		//관리자페이지에서 목록에 나오는 항목
		if($this->tpl->skin == 'admin') {
			$row['title'] = $this->info_bbs['title'];
			$arr_type = array('list'=>'목록형','gallery'=>'갤러리','webzine'=>'혼합형');
			//보기페이지 목록에서 보여주는 거...
			$row['bbs_type'] = $arr_type[$this->info_bbs['bbs_type']];
		}
		$row['icon_arrow'] =  ($row['depth']>1) ? 'inline' : 'none';
		//썸네일 이미지 보여주기
		$row = $this->displayThumb($row);
		//댓글 등록 수 표시 bool_comment
		$row = $this->displayComment($row);
		//NEW 아이콘 표시
		$row['bool_icon_new'] = $this->displayNewIcon($row['regdate']);
		//HOT 아이콘 표시
		$row['bool_icon_hot'] = $this->displayHotIcon($row['hit']);
		//비밀글 표시
		$row['bool_icon_secret'] = ($this->info_bbs['bool_secret'] && $row['bool_secret']) ? TRUE : FALSE ;
		//첨부화일 아이콘표시
		$row['bool_icon_file'] = $this->displayAttachIcon($row['file']);

		$row['author'] = htmlspecialchars(strip_tags($row['author']));
		//리스트 웹진 스타일을 위한 본문 처리
		if(empty($_GET['pg_mode'])) {
			$_GET['pg_mode'] = 'list';
		}
		if($_GET['pg_mode'] == 'list') {
			$row['contents_kr'] = htmlspecialchars(str_replace("&nbsp;"," ",strip_tags($row['contents_kr'])));
			$row['contents_en'] = htmlspecialchars(str_replace("&nbsp;"," ",strip_tags($row['contents_en'])));
			$row['contents_cn'] = htmlspecialchars(str_replace("&nbsp;"," ",strip_tags($row['contents_cn'])));
		}
		return $row;
	}

	//리스트 썸네일 보여주는 함수
	function displayThumb($row)
	{
		//var_dump($this->info_bbs['bbs_type']); exit;
		$row['bool_thumb'] = false;
		if($this->info_bbs['bbs_type'] != 'list') {
			$arr_img = $this->editorGetImg($row['contents_kr']);
			// var_dump($arr_img); exit;
			if(!empty($arr_img)) {
				$row['bool_thumb'] =	true;
				$row['img'] = $arr_img[0];
			}
		}
		return $row;
	}

	//첨부화일 아이콘표시
	function displayComment($row)
	{
		$row['bool_comment'] = false;
		if($this->info_bbs['bool_comment'] > 0) {
			$query = array();
			$query['table_name'] = 'js_bbs_comment';
			$query['tool'] = 'count';
			$query['where'] = 'where link_idx='.$row['idx'];
			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			if($cnt > 0) {
				$row['bool_comment'] = true;
				$row['cnt_comment'] = $cnt;
			}
		}
		return $row;
	}

	//hot아이콘 표시 여부 결정
	function displayHotIcon($hit)
	{
		$ret = false;
		if($this->info_bbs['bool_hotmark'] > 0) {
			if($hit > $this->info_bbs['term_hotmark']) {
				$ret = true;
			}
		}
		return $ret;
	}

	//new아이콘 표시 여부 결정
	function displayNewIcon($regdate)
	{
		$ret = false;
		$base_date = 60*60*$this->info_bbs['term_newmark'];//하루이내 등록된 글은 new마크를 표시한다.
		if($this->info_bbs['bool_newmark'] > 0) {
			//현재시간을 가지고 온다.
			$cur_date = time();
			if($regdate > ($cur_date - $base_date)) {
				$ret = true;
			}
		}
		return $ret;
	}

	//첨부화일 아이콘표시
	function displayAttachIcon($file)
	{
		$ret = false;
		if($this->info_bbs['bool_file'] && !empty($file)) {
			if(file_exists(ROOT_DIR.$this->config['file_dir'].'/'.$file)) {
				$ret = true;
			}
		}
		return $ret;
	}

	//목록 상단 공지사항
	function listNotice()
	{
		$this->lists('loop_bbs_notice','_listNotice');
	}

	function _listNotice($row)
	{
		$row = $this->_lists($row);
		return $row;
	}

	//메인페이지에서 최근 글 표현할때 사용
	function listMain()
	{
		$query = array();
		$query['fields'] = 'idx,
			subject_kr,
			subject_en,
			subject_cn,
			contents_kr,
			contents_en,
			contents_cn,
			bool_secret,
			regdate';
		$query['where'] = 'where 1 && bbscode=\''.$_GET['bbscode'].'\' && division = \'b\' order by pos desc';
		$this->config['navi_qry'] = $this->srchUrl('',FALSE);
		$this->bList($query,'loop_bbs_recent','none');
	}

	/**************************************
	pg_mode = view
	**************************************/
	function view()
	{
		$query = array();
		$query['where'] = 'where bbscode=\''.$_GET['bbscode'].'\' && idx='.$_GET['idx'];
		$this->bView($query);
		$this->tpl->assign('srch_url',$this->srchUrl());
		$this->tpl->assign('ret_url',base64_encode($_SERVER['SCRIPT_NAME'].'?pg_mode=list'.$this->srchUrl()));
		$this->setInfoUpDown($_GET['idx']);
		$this->addHit();
	}

	function _view($row)
	{
		//본문내용 처리
		$row = $this->_lists($row);
		$row['bool_file'] = $row['bool_icon_file'];
		$row = $this->gen_file_src_html($row);
		return $row;
	}


	/**
	 * file_src_html 라는 이름으로 file_src 값을 화면에 보여줄때 사용하는 HTML을 생성해 게시판 정보 배열에 추가합니다.
	 * 
	 * 기본값은 DB에 저장된 file_src값(업로드파일이름)을 사용합니다.
	 * 하지만, file_src에 이미지 확장자가 있으면 이미지로 판단해 <img src=""> 로 html 코드를 넣습니다.
	 *
	 * @param Array $row 개시판 정보 
	 * 
	 * @return Array file_src_html이 추가된 개시판 정보 
	 * 
	 */
	function gen_file_src_html($row) {

		$row['file_src_html'] = $row['file_src'];
		
		// 첨부파일이 이미지면 바로 볼수 있도록 준비
		if(strpos($row['file_src'],'.png')!==false || strpos($row['file_src'],'.jpg')!==false || strpos($row['file_src'],'.jpeg')!==false || strpos($row['file_src'],'.gif')!==false) {
			switch($this->config['file_storage_type']) {
				case 'aws_s3': case 's3':
					$row['file_src_html'] = '<img src="'.$row['file'].'">';
					break;
				default:
					$row['file_src_html'] = '<img src="'.$this->config['file_dir'].'/'.$row['file'].'">';

			}
		}

		return $row;
	}

	function addHit()
	{
		$bool_hit = true;
		if($this->info_bbs['bool_limit_hit']) {
			$cookie_name = 'HIT'.$_GET['bbscode'].$_GET['idx'];
			if(empty($_COOKIE[$cookie_name])) {
				$life_time = $this->info_bbs['term_cookie'] * 3600;
				@setcookie($cookie_name,'ok',time()+$life_time,'/');
			}
			else {
				$bool_hit = false;
			}
		}
		if($this->tpl->skin == 'admin') {
			$bool_hit = false;
		}

		if($bool_hit) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'update';
			$query['fields'] = 'hit=hit+1';
			$query['where'] = 'where idx='.$_GET['idx'];
			$this->dbcon->query($query,__FILE__,__LINE__);
		}
	}

	function setInfoUpDown($idx)
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'pos';
		$query['where'] = 'where idx='.$idx;
		$pos = $this->dbcon->query($query,__FILE__,__LINE__);
		$qry_upside = 'where bbscode=\''.$_GET['bbscode'].'\' && pos >'.$pos.$this->srchQry().' order by pos asc limit 1';
		$qry_downside = 'where bbscode=\''.$_GET['bbscode'].'\' && pos <'.$pos.$this->srchQry().' order by pos desc limit 1';

		$up_info = $this->infoUpDown($qry_upside,'up');
		$this->tpl->assign('up_info',$up_info);

		$down_info = $this->infoUpDown($qry_downside,'down');
		$this->tpl->assign('down_info',$down_info);
	}

	function infoUpDown($qry,$gubun)
	{

		if(empty($_GET['list_cnt'])) {
			$_GET['list_cnt'] = 0;
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = $qry;
		$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
		if($cnt > 0) {
			$query['tool'] = 'select';
			$query['fields'] = 'idx,bool_secret';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			$row = mysqli_fetch_assoc($result);
			if($gubun == 'up') {
				$start =  ($_GET['list_cnt']-1 < 0 && $_GET['start'] > 0) ? $_GET['start'] - $this->config['loop_scale']: $_GET['start'];
				$list_cnt = ($_GET['list_cnt']-1 < 0) ? $this->config['loop_scale']-1: $_GET['list_cnt']-1;
			}
			else {
				$start = ($_GET['list_cnt']+1 == $this->config['loop_scale']) ? $_GET['start'] + $this->config['loop_scale']: $_GET['start'];
				$list_cnt = ($_GET['list_cnt']+1 == $this->config['loop_scale']) ? 0 : $_GET['list_cnt'] +1;
			}
			$ret = array();
			$ret['bool_secret'] = empty($row['bool_secret']) ? false : true;
			$ret['bool_no_list'] = false;
			$ret['idx'] = $row['idx'];
			$ret['start'] = $start;
			$ret['list_cnt'] = $list_cnt;
			$ret['srch_url'] = $this->srchUrl('',false);
		}
		else {
			$ret = array();
			$ret['bool_no_list'] = true;
		}
		return $ret;
	}

	//뷰페이지 하단에 글 목록에서 보여주는 것
	function listView()
	{
		$this->lists('loop_bbs_main','_listView');
	}

	function _listView($row)
	{
		$row = $this->_lists($row);
		if($row['idx'] == $_GET['idx']) {
			$row['no'] = '→';
		}
		return $row;
	}

	/**************************************
	pg_mode = form_new
	**************************************/
	function newForm()
	{
		$arr = array();
		$arr['pg_mode'] = 'write';
		if (!empty($_SESSION['USER_ID'])) {
			$arr['author'] = $_SESSION['USER_NAME'];
		}
		else if (!empty($_SESSION['ADMIN_ID'])) {
			$arr['author'] = $_SESSION['ADMIN_NAME'];
		}
		else {
			$arr['author'] = '';
		}
		$this->tpl->assign($arr);
		$this->tpl->assign('srch_url',$this->srchUrl());
				if($this->info_bbs['bool_category']) {
			$this->loopCategory();
		}
	}

	/**************************************
	pg_mode = form_edit, form_reply
	**************************************/
	function editForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['where'] = 'where idx='.$_GET['idx'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		if($_GET['pg_mode'] == 'form_reply') {
			unset($row['author']);
			$row['pg_mode'] = 'reply';
			if (!empty($_SESSION['USER_ID'])) {
				$row['author'] = $_SESSION['USER_NAME'];
			}
			else {
				$row['author'] = '';
			}
		}
		else {
			$row['pg_mode'] = 'edit';
		}
		
		$row = $this->gen_file_src_html($row);
		
		$this->tpl->assign($row);
		$this->tpl->assign('srch_url',$this->srchUrl());
		if($this->info_bbs['bool_category']) {
			$this->loopCategory();
		}
	}

	/**************************************
	pg_mode = write
	**************************************/
	function write()
	{
		global $config_basic;
		if($this->tpl->skin == 'admin') {
			$_POST['passwd'] = md5('admin');
		}
		$query = array();
		if($this->bWrite($query,$_POST,'_write')) {
			if($config_basic['bool_ssl'] > 0) {
				replaceGo('http://'.$_SERVER['SERVER_NAME'].base64_decode($_POST['ret_url']));
			}
			else {
				jsonMsg(1);
			}
		}
		else {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_bbs2);
			}
			else {
				jsonMsg(0,'err_write');
			}
		}
	}

	function _write($arr)
	{
		if($this->info_bbs['bool_no_badword']) {
			$arr['subject_kr'] = $this->badword($arr['subject_kr']);
			$arr['subject_en'] = $this->badword($arr['subject_en']);
			$arr['subject_cn'] = $this->badword($arr['subject_cn']);
			$arr['author'] = $this->badword($arr['author']);
			$arr['contents_kr'] = $this->badword($arr['contents_kr']);
			$arr['contents_en'] = $this->badword($arr['contents_en']);
			$arr['contents_cn'] = $this->badword($arr['contents_cn']);
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$arr['userid'] = empty($_SESSION['USER_ID']) ? 'guest' : $_SESSION['USER_ID'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'max(thread)';
		$max_thread = $this->dbcon->query($query,__FILE__,__LINE__) + 1;
		$arr['thread'] = $max_thread + 1;
		$query['fields'] = 'max(pos)';
		$max_pos = $this->dbcon->query($query,__FILE__,__LINE__) + 1;
		$arr['pos'] = $max_pos + 1;
		$arr['depth'] = 1;
		return $arr;
	}

	/**************************************
	pg_mode = edit
	**************************************/
	function edit()
	{
		global $config_basic;
		$query = array();
		$query['where'] = 'where idx='.$_POST['idx'];
		if($this->bEdit($query,$_POST,'_edit')) {
			if($config_basic['bool_ssl'] > 0) {
				replaceGo('http://'.$_SERVER['SERVER_NAME'].base64_decode($_POST['ret_url']));
			}
			else {
				jsonMsg(1);
			}
		}
		else {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_bbs3);
			}
			else {
				jsonMsg(0, 'err_edit');
			}
		}
	}

	function _edit($arr)
	{
		if($this->info_bbs['bool_no_badword']) {
			$arr['subject_kr'] = $this->badword($arr['subject_kr']);
			$arr['subject_en'] = $this->badword($arr['subject_en']);
			$arr['subject_cn'] = $this->badword($arr['subject_cn']);
			$arr['author'] = $this->badword($arr['author']);
			$arr['contents_kr'] = $this->badword($arr['contents_kr']);
			$arr['contents_en'] = $this->badword($arr['contents_en']);
			$arr['contents_cn'] = $this->badword($arr['contents_cn']);
		}
		return $arr;
	}

	/**************************************
	pg_mode = reply
	**************************************/
	function reply()
	{
		global $config_basic;
		$query = array();
		if($this->bWrite($query,$_POST,'_reply')) {
			if($config_basic['bool_ssl'] > 0) {
				replaceGo('http://'.$_SERVER['SERVER_NAME'].base64_decode($_POST['ret_url']));
			}
			else {
				jsonMsg(1);
			}
		}
		else {
			if($config_basic['bool_ssl'] > 0) {
				errMsg(Lang::main_bbs2);
			}
			else {
				jsonMsg(0,'err_write');
			}
		}
	}

	function _reply($arr)
	{
		if($this->info_bbs['bool_no_badword']) {

			$arr['subject_kr'] = $this->badword($arr['subject_kr']);
			$arr['subject_en'] = $this->badword($arr['subject_en']);
			$arr['subject_cn'] = $this->badword($arr['subject_cn']);
			$arr['author'] = $this->badword($arr['author']);
			$arr['contents_kr'] = $this->badword($arr['contents_kr']);
			$arr['contents_en'] = $this->badword($arr['contents_en']);
			$arr['contents_cn'] = $this->badword($arr['contents_cn']);

		}
		$arr['pos'] = $arr['pos'] - 0.0001;
		$arr['depth'] = $arr['depth'] + 1;
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'update';
		$query['fields'] = 'pos=pos-0.0001';
		$query['where'] = 'where pos<='.$arr['pos'].' && thread='.$arr['thread'];
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0,'err_write');
		}
		return $arr;
	}

	/**************************************
	pg_mode = del
	**************************************/
	#글삭제함수
	function del($idx='',$bool_return=0)
	{
		if(empty($idx)) {
			$idx = $_REQUEST['idx'];
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'depth, thread';
		$query['where'] = 'where idx='.$idx;
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		if($row['depth'] > 1) {
			$ret = $this->idxDel($idx);
		}
		else {
			$ret = $this->threadDel($row['thread']);
		}
		if($bool_return > 0) {
			return $ret;
		}
		else {
			if($ret) {
				jsonMsg(1);
			}
			else {
				jsonMsg(0,"err_del");
			}
		}
	}

	function idxDel($idx)
	{
		$query = array();
		$query['where'] = 'where idx=\''.$idx.'\'';
		if($this->bDel($query)) {
			$query = array();
			$query['table_name'] = 'js_bbs_comment';
			$query['tool'] = 'count';
			$query['where'] = 'where link_idx='.$idx;
			$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
			if($cnt > 0) {
				return $this->bDel($query);
			}
			else {
				return TRUE;
			}
		}
		else {
			return FALSE;
		}
	}

	function threadDel($thread)
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';
		$query['where'] = 'where thread='.$thread;
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		while($row = mysqli_fetch_array($result)) {
			$query = array();
			$query['where'] = 'where idx=\''.$row['idx'].'\'';
			if($this->bDel($query)) {
				$query = array();
				$query['table_name'] = 'js_bbs_comment';
				$query['tool'] = 'count';
				$query['where'] = 'where link_idx='.$row['idx'];
				$cnt = $this->dbcon->query($query,__FILE__,__LINE__);
				if($cnt > 0) {
					if(!$this->bDel($query)) {
						return false;
					}
				}
			}
			else {
				return FALSE;
			}
		}
		return true;
	}

	function delMulti()
	{
		foreach($_POST['idxs'] as $key=>$val) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'count';
			$query['where'] = 'where idx='.$val;
			$is_idx = $this->dbcon->query($query,__FILE__,__LINE__);
			if($is_idx) {
				if(!$this->del($val,1)) {
					jsonMsg(0);
				}
			}
		}
		jsonMsg(1);
	}

	function moveMulti()
	{
		foreach($_POST['idxs'] as $key=>$val) {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'row';
			$query['where'] = 'where idx='.$val;
			$row = $this->dbcon->query($query,__FILE__,__LINE__);

			$query['tool'] = 'update';
			$query['fields'] = 'bbscode=\''.$_POST['bbscode'].'\'';
			$query['where'] = 'where thread='.$row['thread'].' && bbscode=\''.$row['bbscode'].'\'';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			if(!$result) {
				jsonMsg(0);
			}
		}
		jsonMsg(1);
	}

	//해당 서비스 이용권한 체크하는 메소드
	function userRightCheck($mode='access')
	{
		//해당모드의 접근허용 레벨을 가지고 온다
		//사용자의 레벨과 비교하여 접근 허용여부를 결정한다.
		$arr_mode = array(
			'access'=>'right_access',//접근권한
			'view'=>'right_view',//보기권한
			'write'=>'right_write',//쓰기권한
			'del'=>'right_del',//삭제권한
			'comment'=>'right_comment');//댓글쓸권한

		$query = array();
		$query['table_name'] = 'js_bbs_info';
		$query['tool'] = 'select_one';
		$query['fields'] = '(select ranking from js_member_level where level_code='.$arr_mode[$mode].') as ranking';
		$query['where'] = 'where bbscode=\''.$_REQUEST['bbscode'].'\'';
		$permission_ranking = $this->dbcon->query($query,__FILE__,__LINE__);

		if (empty($_SESSION['USER_LEVEL'])) {
			$query = array();
			$query['table_name'] = 'js_member_level';
			$query['tool'] = 'select_one';
			$query['fields'] = 'ranking';
			$query['where'] = 'order by ranking desc limit 1';
			$user_ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		else {
			$query = array();
			$query['table_name'] = 'js_member_level';
			$query['tool'] = 'select_one';
			$query['fields'] = 'ranking';
			$query['where'] = 'where level_code=\''.$_SESSION['USER_LEVEL'].'\'';
			$user_ranking = $this->dbcon->query($query,__FILE__,__LINE__);
		}

		$ret = 1;

		if($permission_ranking >= $user_ranking) {
			if ($this->info_bbs['bool_kisu'] > 0) {
				if(empty($_SESSION['USER_KISU'])) {
					$ret = 0;
				}
				else {
					if(!empty($_GET['category'])) {
						if ($_GET['category'] != $_SESSION['USER_KISU']) {
							$ret = 0;
						}
					}
				}
			}
		}
		else {
			$ret = 0;
		}

		if($_SESSION['USER_LEVEL'] == 'WR89') {
			$ret = 1;
		}
		return $ret;
	}

	//권한별로 버튼을 보여줄지 여부를 결정하는 메소드
	//write, del
	function displayButton($mode)
	{
		$this->tpl->assign('bool_btn_'.$mode,$this->userRightCheck($mode));
	}

	//댓글 폼 여부를 보여주는 함수
	function displayCommentForm()
	{
		$this->tpl->assign('bool_comment_form',$this->userRightCheck('comment'));
	}

	//욕글 필터링하는 메소드
	function badword($input)
	{
		$query = array();
		$query['table_name'] = 'js_badword';
		$query['tool'] = 'select';
		$query['fields'] = 'badword';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr = array();
		while($row = mysqli_fetch_assoc($result)) {
			$badword = trim($row['badword']);
			$len = ceil(strlen($badword)/2);
			$x = '';
			for($i=0;$i< $len;$i++) { $x .= '♡'; }
			$arr[$row['badword']] = $x;
		}
		$input = strtr($input,$arr);
		return $input;
	}

	//스팸방지 메소드
	function antiSpam()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'count';
		$query['where'] = 'where ipaddr=\''.$_SERVER["REMOTE_ADDR"].'\'';
		if($this->dbcon->query($query,__FILE__,__LINE__)) {
			$query['tool'] = 'select_one';
			$query['fields'] = 'regdate';
			$query['where'] = 'where ipaddr=\''.$_SERVER["REMOTE_ADDR"].'\' order by regdate desc limit 1';
			$regdate = $this->dbcon->query($query,__FILE__,__LINE__);
			$gap = time() - $regdate;
			if($gap < 2) {
				jsonMsg(0,'err_spam');
			}
		}
	}

	//비밀번호 체크하는 함수
	function checkPasswd()
	{
		//현재글이 비밀글인지 확인한다.
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'userid,bool_secret,passwd';
		$query['where'] = 'where idx='.$_REQUEST['idx'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		if (empty($_SESSION['USER_ID'])) {
			if($_REQUEST['passwd'] == md5($row['passwd'])) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			if ($_SESSION['USER_ID'] == $row['userid']) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	function loopCategory()
	{
		if (!empty($this->info_bbs['bbs_category'])) {
			$arr_category = explode(',',$this->info_bbs['bbs_category']);
			$loop = array();
			$loop_key = array();
			$loop_val = array();
			foreach ($arr_category as $key => $val) {
				$arr = explode('@',$val);
				$temp = array();
				$temp['select_val'] = $arr[0];
				$temp['select_text'] = $arr[1]??$arr[0];
				$loop[] = $temp;
				$loop_key[] = $temp['select_val'];
				$loop_val[] = $temp['select_text'];
			}
			// var_dump($loop_key); exit;
			$this->tpl->assign('loop_select',$loop);
			$this->tpl->assign('loop_category_key',$loop_key);
			$this->tpl->assign('loop_category_val',$loop_val);
		}
	}

	function srchQry()
	{
		$arr = array();
		if(!empty($_GET['s_mode'])) {
			if(!empty($_GET['author'])) { $arr[] = 'author like \'%'.$_GET['s_val'].'%\' '; }
			if(!empty($_GET['subject'])) { $arr[] = 'subject_kr like \'%'.$_GET['s_val'].'%\' or subject_en like \'%'.$_GET['s_val'].'%\' or subject_cn like \'%'.$_GET['s_val'].'%\' '; }
			if(!empty($_GET['contents'])) { $arr[] = 'contents_kr like \'%'.$_GET['s_val'].'%\' or contents_en like \'%'.$_GET['s_val'].'%\' or contents_cn like \'%'.$_GET['s_val'].'%\''; }
		}
		if(!empty($_GET['category'])) {
			$arr[] = 'category=\''.$_GET['category'].'\'';
		}
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		$arr[] = 'bbscode='.$_GET['bbscode'];
		if($start) {
			$arr[] = 'start='.$_GET['start'];
		}
		if(!empty($idx)) {
			$arr[] = 'idx='.$idx;
		}
		if(!empty($_GET['s_mode'])) {
			$arr[] = 's_mode='.$_GET['s_mode'];
			if(empty($_GET['author']) && empty($_GET['subject_kr']) && empty($_GET['contents_kr'])) { $_GET['subject'] = $_GET['contents'] = 1; }
			if(!empty($_GET['author'])) { $arr[] = 'author='.$_GET['author']; }
			if(!empty($_GET['subject_kr'])) { $arr[] = 'subject_kr='.$_GET['subject_kr']; }
			if(!empty($_GET['subject_en'])) { $arr[] = 'subject_en='.$_GET['subject_en']; }
			if(!empty($_GET['subject_cn'])) { $arr[] = 'subject_cn='.$_GET['subject_cn']; }
			if(!empty($_GET['contents_kr'])) { $arr[] = 'contents_kr='.$_GET['contents_kr']; }
			if(!empty($_GET['contents_en'])) { $arr[] = 'contents_en='.$_GET['contents_en']; }
			if(!empty($_GET['contents_cn'])) { $arr[] = 'contents_cn='.$_GET['contents_cn']; }
			if(!empty($_GET['s_val'])) { $arr[] = 's_val='.$_GET['s_val']; }
		}
		// if(!empty($_GET['gnb_code'])) { $arr[] = 'gnb_code='.$_GET['gnb_code']; }
		// if(!empty($_GET['cate_code'])) { $arr[] = 'cate_code='.$_GET['cate_code']; }
		if(!empty($_GET['category'])) { $arr[] = 'category='.$_GET['category']; }
		$ret = '&'.implode('&',$arr);
		return $ret;
	}
}

function bbsQuery($arr)
{
	$qry = array();
	if(isset($arr['bbscode']))  { $qry[] = 'bbscode=\''.$arr['bbscode'].'\''; }
	if(isset($arr['category']))  { $qry[] = 'category=\''.$arr['category'].'\''; }
	if(isset($arr['userid']))  { $qry[] = 'userid=\''.$arr['userid'].'\''; }
	if(isset($arr['division']))  { $qry[] = 'division=\''.$arr['division'].'\''; }
	if(isset($arr['author']))  { $qry[] = 'author=\''.$arr['author'].'\''; }
	if(isset($arr['subject_kr']))  { $qry[] = 'subject_kr=\''.$arr['subject_kr'].'\''; }
	if(isset($arr['subject_en']))  { $qry[] = 'subject_en=\''.$arr['subject_en'].'\''; }
	if(isset($arr['subject_cn']))  { $qry[] = 'subject_cn=\''.$arr['subject_cn'].'\''; }
	if(isset($arr['email']))  { $qry[] = 'email=\''.$arr['email'].'\''; }
	if(isset($arr['contents_kr']))  { $qry[] = 'contents_kr=\''.$arr['contents_kr'].'\''; }
	if(isset($arr['contents_en']))  { $qry[] = 'contents_en=\''.$arr['contents_en'].'\''; }
	if(isset($arr['contents_cn']))  { $qry[] = 'contents_cn=\''.$arr['contents_cn'].'\''; }
	if(isset($arr['thread']))  { $qry[] = 'thread=\''.$arr['thread'].'\''; }
	if(isset($arr['pos']))  { $qry[] = 'pos=\''.$arr['pos'].'\''; }
	if(isset($arr['depth']))  { $qry[] = 'depth=\''.$arr['depth'].'\''; }
	if(isset($arr['bool_secret']))  { $qry[] = 'bool_secret=\''.$arr['bool_secret'].'\''; }
	if(isset($arr['website']))  { $qry[] = 'website=\''.$arr['website'].'\''; }
	if(isset($arr['file']))  { $qry[] = 'file=\''.$arr['file'].'\''; }
	if(isset($arr['file_src']))  { $qry[] = 'file_src=\''.$arr['file_src'].'\''; }
	if(isset($arr['passwd']))  { $qry[] = 'passwd=\''.$arr['passwd'].'\''; }
	$qry[] = 'ipaddr=\''.$_SERVER["REMOTE_ADDR"].'\'';
	if(isset($arr['recommand']))  { $qry[] = 'recommand=\''.$arr['recommand'].'\''; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=UNIX_TIMESTAMP()'; }
	$qry = implode(',',$qry);
	return $qry;
}

?>