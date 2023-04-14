<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/

class Comment extends BASIC
{
	function __construct(&$tpl)
	{

		$config = array();

		$config['table_name'] = 'js_bbs_comment';
		$config['query_func'] = 'commentQry';
		$config['write_mode'] = 'ajax';//ajax or link
		/************************************/
		$config['file_dir'] = '/data/bbs';
		$config['thumb_dir'] = '/data/thumbnail';
		$config['temp_dir'] = '/data/editorTemp';
		$config['staple_article'] = array('contents');
		/************************************/
		$config['no_tag'] = array();
		$config['no_space'] = array();
		$config['staple_article'] = array();
		/************************************/
		$config['bool_file'] = TRUE;
		$config['file_target'] = array();
		$config['file_size'] = 2;
		$config['upload_limit'] = TRUE;

		$config['bool_thumb'] = FALSE;
		$config['thumb_target'] = array();
		$config['thumb_width'] = 0;
		$config['thumb_height'] = 0;
		$config['thumb_size'] = array();
		/************************************/
		$config['bool_editor'] = FALSE;
		$config['editor_target'] = array();
		$config['limit_img_width'] = 500;

		$config['bool_editor_thumb'] = TRUE;
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
		$query['where'] = 'where link_idx='.$_GET['idx'].' order by thread asc,pos desc';
		$this->bList($query,'loop_comment');
	}

	function _lists($row)
	{
		$row['contents'] = nl2br(htmlspecialchars(strip_tags($row['contents'])));
		$row['regdate'] = date('y-m-d H:i',$row['regdate']);
		$row['url_del'] = '/bbs/comment.php?pg_mode=del&idx='.$row['idx'];
		return $row;
	}

	function view()
	{
		$query = array();
		$query['where'] = 'where idx=\''.$_GET['idx'].'\'';
		$this->bView($query,'_view');
	}

	function _view($row)
	{
		$row = $this->_lists($row);
		return $row;
	}

	function newForm()
	{
		$arr = array();
		$arr['bbscode'] = $_GET['bbscode'];
		$arr['link_idx'] = $_GET['idx'];
		$arr['pg_mode'] = 'write';
		$this->tpl->assign($arr);
	}

	function editForm()
	{
		$query = array();
		$query['where'] = 'where idx='.$_GET['idx'];
		$row = $this->bEditForm($query);
		$this->tpl->assign($row);
	}

	function replyForm()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'pos,thread';
		$query['where'] = 'where idx='.$_GET['idx'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		$this->tpl->assign($row);
	}

	function write()
	{
		$query = array();
		if($this->bWrite($query,$_POST)) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _write($arr)
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select_one';
		$query['fields'] = 'max(thread)';
		$arr['thread'] = $this->dbcon->query($query,__FILE__,__LINE__) + 1;
		$query['fields'] = 'max(pos)';
		$arr['pos'] = $this->dbcon->query($query,__FILE__,__LINE__) + 1;
		$arr['depth'] = 1;
		return $arr;
	}

	function edit()
	{
		$query = array();
		$query['where'] = 'where idx='.$_POST['idx'];
		if($this->bEdit($query,$_POST,'none')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function reply()
	{
		$query = array();
		if($this->bWrite($query,$_POST,'_reply')) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function _reply($arr)
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$arr['pos'] = $arr['pos'] - 0.0001;
		$arr['depth'] = 2;
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'update';
		$query['fields'] = 'pos=pos-0.0001';
		$query['where'] = 'where pos<='.$arr['pos'].' && thread='.$arr['thread'];
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$result) {
			jsonMsg(0);
		}
		return $arr;
	}

	function del()
	{
		if(empty($idx)) {
			$idx = $_REQUEST['idx'];
		}
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'depth,thread,contents';
		$query['where'] = 'where idx='.$idx;
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		if($row['depth'] > 1) {
			$query = array();
			$query['where'] = 'where idx=\''.$idx.'\'';
			return $this->bDel($query);
		}
		else {
			$query = array();
			$query['table_name'] = $this->config['table_name'];
			$query['tool'] = 'select';
			$query['where'] = 'where thread='.$row['thread'];
			return $this->bDel($query);
		}
	}

	function checkPasswd()
	{
		//현재글이 비밀글인지 확인한다.
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'row';
		$query['fields'] = 'passwd';
		$query['where'] = 'where idx='.$_REQUEST['idx'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		if($_REQUEST['passwd'] == md5($row['passwd'])) {
			return true;
		}
		else {
			return false;
		}
	}

	function checkRightComment()
	{
		$query = array();
		$query['table_name'] = 'js_member_level';
		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$arr= array();
		while ($row = mysqli_fetch_assoc($result)) { $arr[$row['level_code']] = $row['ranking']; }
		$target_level = $arr[$this->config['right_comment']];
		$user_level = $arr[$_SESSION['USER_LEVEL']];
		return ($target_level < $user_level) ? FALSE : TRUE;
	}
}

function commentQry($arr)
{
	$qry = array();
	if(!empty($arr['link_idx']))  { $qry[] = 'link_idx='.$arr['link_idx']; }
	if(!empty($_SESSION['USER_ID']))  { $qry[] = 'userid=\''.$_SESSION['USER_ID'].'\''; }
	if(!empty($arr['bbscode']))  { $qry[] = 'bbscode=\''.$arr['bbscode'].'\''; }
	if(!empty($arr['author']))  { $qry[] = 'author=\''.$arr['author'].'\''; }
	if(isset($arr['contents']))  { $qry[] = 'contents=\''.$arr['contents'].'\''; }
	if(!empty($arr['passwd']))  { $qry[] = 'passwd=\''.$arr['passwd'].'\''; }
	$qry[] = 'ipaddr=\''.$_SERVER["REMOTE_ADDR"].'\'';
	if(!empty($arr['thread']))  { $qry[] = 'thread='.$arr['thread']; }
	if(!empty($arr['pos']))  { $qry[] = 'pos='.$arr['pos']; }
	if(!empty($arr['depth']))  { $qry[] = 'depth='.$arr['depth']; }
	$qry[] = 'regdate=UNIX_TIMESTAMP()';
	$qry = implode(',',$qry);
	return $qry;
}

?>