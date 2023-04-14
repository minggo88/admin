<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
class certification_marks extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();

		$config['table_name'] = 'js_auction_certification_marks';
		$config['query_func'] = 'querySet';
		$config['write_mode'] = 'ajax';//ajax or link

		$config['bool_navi_page'] = TRUE;
		$config['loop_scale'] = 10;
		$config['page_scale'] = 5;
		$config['navi_url'] = '?';
		$config['navi_pg_mode'] = 'list';
		$config['navi_qry'] = '';
		$config['navi_mode'] = 'link';
		$config['navi_load_id'] = '';

		$this->BASIC($config,$tpl);
	}

	//옥션 관리 목록 total
	function lists_cnt()
	{
		$query = "SELECT count(idx) FROM ".$this->config['table_name']." WHERE 1 ".$this->srchQry();
		return $this->dbcon->query_unique_value($query,__FILE__,__LINE__);
	}

	//옥션 관리 목록
	function lists()
	{
		//$this->tpl->assign('API_RUNMODE', __API_RUNMODE__);
		//회사 계정 수정 필요
		// $com_user_id = "ara_manager";

		$query = "SELECT * FROM js_auction_certification_marks WHERE 1 ".$this->srchQry()." ORDER BY idx DESC ";
		$arr = $this->dbcon->query_all_array($query,__FILE__, __LINE__);
		return $arr;
	}


	function srchQry()
	{
		$arr = array();
		if(!empty($_POST['idx'])) $arr[] = "idx = '{$this->dbcon->escape($_POST['idx'])}' " ;
		if(!empty($_POST['title'])) $arr[] = "title = '{$this->dbcon->escape($_POST['title'])}' " ;
		if(!empty($_POST['image_url'])) $arr[] = "image_url = '{$this->dbcon->escape($_POST['image_url'])}' " ;
		$ret = (sizeof($arr) > 0) ? ' && ('.implode(' || ',$arr).') ' : '';
		return $ret;
	}

	function srchUrl($idx='',$start=TRUE)
	{
		$arr = array();
		//$arr[] = empty($_GET['start']) ? 'start=0' : 'start='.$_GET['start'];
		if(!empty($idx)) { $arr[] = 'idx='.$idx; }
		if(!empty($_GET['s_val'])) {
			if(!empty($_GET['author'])) { $arr[] = 'author='.$_GET['author']; }
			if(!empty($_GET['subject'])) { $arr[] = 'subject='.$_GET['subject']; }
			if(!empty($_GET['contents'])) { $arr[] = 'contents='.$_GET['contents']; }
			if(!empty($_GET['s_val'])) { $arr[] = 's_val='.$_GET['s_val']; }
		}
		$ret = '&'.implode('&',$arr);
		return $ret;
	}


	/**
	 * 경매 상품 폼에서 기존 상품정보를 탬플릿에서 사용할 수 있게 설정합니다.
	 *
	 * @return void
	 * 
	 */
	function editForm()
	{
		// get goods info.
		$row = $this->getRow($_GET['idx']);
		// set data to template
		$this->tpl->assign($row);
	}
	/**
	 * PK로 데이터 한 ROW를 추출합니다.
	 *
	 * @param Number $idx 고유번호
	 * 
	 * @return array idx 에 해당하는 1개의 Row(컬럼 전체) 
	 * 
	 */
	function getRow($idx)
	{
		$query = "SELECT * FROM js_auction_certification_marks WHERE idx='{$this->dbcon->escape($idx)}'";
		return $this->dbcon->query_fetch_array($query,__FILE__,__LINE__);
	}

	/**
	 * 새 데이터 저장
	 * 
	 * 새로운 데이터를 추가하고 json으로 결과를 표시합니다.
	 * @return void
	 * 
	 */
	function write() {
		$same_title = $this->dbcon->query_one("SELECT COUNT(*) FROM {$this->config['table_name']} WHERE title = '{$this->dbcon->escape($_POST['title'])}' ");
		if($same_title) {
			jsonMsg(0,'이름이 같은 정보가 있습니다.');
		}
		if($_POST['image_url'] && strpos($_POST['image_url'], '/tmp/')!==false) {
			require __DIR__.'/../cheditor/imageUpload/s3.php';
			$S3 = new S3;
			$new_url = $S3->copy_tmpfile_to_s3($_POST['image_url']) ;
			$S3->delete_file_to_s3($_POST['image_url']) ;
			$_POST['image_url'] = $new_url;
		}
		$query = array();
		if($this->bWrite($query, $_POST)) {
			jsonMsg(1);
		} else {
			jsonMsg(0,'저장하지 못했습니다.');
		}
	}
	function edit() {
		$prev_data = (object) $this->getRow($_POST['idx']);
		if(! $prev_data) {
			jsonMsg(0,'데이터를 찾지 못했습니다. 패이지를 새로고침 후 다시 저장해주세요.');
		}
		$_POST['title'] = trim($_POST['title']);
		if($prev_data->title != $_POST['title']) {
			$same_title = $this->dbcon->query_one("SELECT COUNT(*) FROM {$this->config['table_name']} WHERE title = '{$this->dbcon->escape($_POST['title'])}' ");
			if($same_title) {
				jsonMsg(0,'이름이 같은 정보가 있습니다.');
			}
		}
		if($_POST['image_url'] && strpos($_POST['image_url'], '/tmp/')!==false) {
			require __DIR__.'/../cheditor/imageUpload/s3.php';
			$S3 = new S3;
			$new_url = $S3->copy_tmpfile_to_s3($_POST['image_url']) ;
			$S3->delete_file_to_s3($_POST['image_url']) ;
			$_POST['image_url'] = $new_url;

			if($prev_data->image_url && $_POST['image_url']) $r = $S3->delete_file_to_s3($prev_data->image_url);
		}
		$query = array();
		$query['where'] = 'where idx='.$_POST['idx'];
		if($this->bEdit($query, $_POST)) {
			jsonMsg(1);
		} else {
			jsonMsg(0,'저장하지 못했습니다.');
		}
	}
	function delete() {
		$query = array();
		$row = $this->dbcon->query_fetch_object("SELECT * FROM {$this->config['table_name']} WHERE idx='{$this->dbcon->escape($_POST['idx'])}'");
		if($row->image_url) {
			require __DIR__.'/../cheditor/imageUpload/s3.php';
			$S3 = new S3;
			$S3->delete_file_to_s3($row->image_url) ;
		}
		$r = $this->dbcon->query("delete from {$this->config['table_name']} where idx='{$this->dbcon->escape($_POST['idx'])}'");
		if($r) {
			jsonMsg(1);
		} else {
			jsonMsg(0,'삭제하지 못했습니다.');
		}
	}

}

function querySet($arr)
{
	$qry = array();
	if(isset($arr['title']))  { $qry[] = 'title=\''.$arr['title'].'\''; }
	if(isset($arr['image_url']))  { $qry[] = 'image_url=\''.$arr['image_url'].'\''; }
	if($arr['pg_mode'] == 'write') { $qry[] = 'regdate=NOW()'; }
	$qry = implode(',',$qry);
	return $qry;
}
