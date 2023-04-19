<?php
/**
 * UTF-8 :한글
 * Firstgleam Shopping Mall Solution (http://www.firstgleam.com, http://www.fgshop.co.kr)
 * FGShop Shopping Mall Solution
 * Author : Danny Hwang
 * comment : Basic Class
 */

class BASIC
{
	var $config;
	var $tpl;
	var $quotes_gpc;
	var $quotes_runtime;
	var $dbcon;
	var $json;
	var $total;

    public function __construct(){ /* nothing */ }
	function BASIC(&$config,&$tpl)
	{
		$this->config = &$config;
		$this->tpl = &$tpl;
		$this->quotes_gpc = get_magic_quotes_gpc( );
		$this->quotes_runtime = get_magic_quotes_runtime( );
		if(empty($this->config['query_func'])) {
			$this->config['query_func'] = 'querySet';
		}
		if(empty($this->config['bool_file'])) {
			$this->config['bool_file'] = FALSE;
		}
		if(empty($this->config['bool_thumb'])) {
			$this->config['bool_thumb'] = FALSE;
		}
		if(empty($this->config['bool_navi_page'])) {
			$this->config['bool_navi_page'] = FALSE;
		}
		if(empty($this->config['bool_navi_bind'])) {
			$this->config['bool_navi_bind'] = FALSE;
		}
		if(empty($this->config['bool_editor'])) {
			$this->config['bool_editor'] = FALSE;
		}
	}

	function bSetConfig()
	{
		$this->quotes_gpc = get_magic_quotes_gpc( );
		$this->quotes_runtime = get_magic_quotes_runtime( );
		if(empty($this->config['query_func'])) {
			$this->config['query_func'] = 'querySet';
		}
		if(empty($this->config['bool_file'])) {
			$this->config['bool_file'] = FALSE;
		}
		if(empty($this->config['bool_thumb'])) {
			$this->config['bool_thumb'] = FALSE;
		}
		if(empty($this->config['bool_navi_page'])) {
			$this->config['bool_navi_page'] = FALSE;
		}
		if(empty($this->config['bool_navi_bind'])) {
			$this->config['bool_navi_bind'] = FALSE;
		}
		if(empty($this->config['bool_editor'])) {
			$this->config['bool_editor'] = FALSE;
		}
	}

	function bList($qry,$loop_id='loop',$_func='_lists',$bool_return=FALSE)
	{
		$query = array();
		$start = 0;
		if(!empty($_GET['start'])) {
			$start = $GLOBALS['_GET_ESCAPE']['start'] ? $GLOBALS['_GET_ESCAPE']['start'] : $this->dbcon->escape($_GET['start']);
		}
		$query['table_name'] = empty($qry['table_name']) ? $this->config['table_name'] : $qry['table_name'];
		$query['table_name2'] = empty($qry['table_name2']) ? $this->config['table_name2'] : $qry['table_name2'];
		$query['table_name3'] = empty($qry['table_name3']) ? $this->config['table_name3'] : $qry['table_name3'];
		$query['fields'] = empty($qry['fields']) ? '*' : $qry['fields'];
		$query['fields2'] = empty($qry['fields2']) ? '*' : $qry['fields2'];
                           $query['fields3'] = empty($qry['fields3']) ? '*' : $qry['fields3'];
		$query['where'] = empty($qry['where']) ? '' : $qry['where'];
		$query['where2'] = empty($qry['where2']) ? '' : $qry['where2'];
		$query['where3'] = empty($qry['where3']) ? '' : $qry['where3'];
		$query['order'] = empty($qry['order']) ? '' : $qry['order'];
		$query['tool'] = 'count';
		$this->total = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!isset($this->config['bool_assign_total'])) {
			$this->config['bool_assign_total'] = TRUE;
		}
		if ($this->config['bool_assign_total']) {
			$this->tpl->assign('total',$this->total);
		}
		if($this->config['bool_navi_page']) {
			if(empty($this->config['limit_start'])) {
				$loop_limit = ' limit '.$start.','.$this->dbcon->escape($this->config['loop_scale']);
			}
			else {
				$loop_limit = ' limit '.$this->dbcon->escape($this->config['limit_start']).','.$this->dbcon->escape($this->config['loop_scale']);
			}
		}
		else { $loop_limit = ''; }
		$query['tool'] = empty($qry['tool']) ? 'select' : $qry['tool'];
		$query['where'] = $query['where'].$query['order'].$loop_limit;
// var_dump($query);
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		$list_cnt = 0;
		for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
		//while($row = mysql_fetch_assoc($result)) {
			if(!$this->quotes_runtime) {
				$row = $this->handleSlash($row);
			}
			$row['list_cnt'] = $list_cnt;
			$row['no'] = $this->total - $_GET['start'] - $row['list_cnt'];
			if(!empty($_func) && method_exists($this,$_func) && $_func != 'none') {
				$row = $this->$_func($row);
			}
			$loop[] = $row;
			if(!empty($this->config['bool_nest'])) {
				if(method_exists($this,$this->config['nest_method']) && !empty($this->config['nest_loop_id'])) {
					$loop2 = &$loop[$i][$this->config['nest_loop_id']];
					$nest_method = $this->config['nest_method'];
					$loop2 = $this->$nest_method($row);
				}
			}
			$list_cnt++;
		}
		if(empty($bool_return)) {
			$this->tpl->assign($loop_id,$loop);
			if(!empty($this->config['bool_navi_page'])) {
				if(empty($this->config['navi_pg_mode'])) {
					if(empty($this->config['bool_navi_bind'])) {
						$navi_pg_mode = '&pg_mode=list';
					} else {
						$navi_pg_mode = '/list';
					}
				}
				else {
					if(empty($this->config['bool_navi_bind'])) {
						$navi_pg_mode = '&pg_mode='.$this->config['navi_pg_mode'];
					} else {
						$navi_pg_mode = '/list';
					}
				}
				if(empty($this->config['navi_qry'])) {
					$this->config['navi_qry'] = '';
				}
				if(empty($this->config['bool_navi_bind'])) {
					$navi_page = $this->naviPage($_SERVER['PHP_SELF'],$this->total,$navi_pg_mode.$this->config['navi_qry'],$this->config['bool_navi_justify']);
				} else {
					// var_dump('/'.$this->config['kind_menu'].$navi_pg_mode.'/'.$this->total);
					$navi_page = $this->naviPagebind($this->config['kind_menu'],$navi_pg_mode,$this->total,$this->config['navi_qry'],$this->config['bool_navi_justify']);
				}
				$this->tpl->assign('navi_page',$navi_page);
			}
		}
		else {
			return $loop;
		}
	}

	/*
	사용자 정의 함수 추가
	userView();
	*/
	function bView($qry,$_func='_view',$bool_return =FALSE)
	{
		$query = array();
		$query['table_name'] = empty($qry['table_name']) ? $this->config['table_name'] : $qry['table_name'];
		$query['tool'] = empty($qry['tool']) ? 'row' : $qry['tool'];
		$query['fields'] = empty($qry['fields']) ? '*' : $qry['fields'];
		$query['where'] = empty($qry['where']) ? '' : $qry['where'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$this->quotes_runtime) {
			$row = $this->handleSlash($row);
		}
		if(!empty($_func) &&  method_exists($this,$_func) && $_func != 'none') {
			$row = $this->$_func($row);
		}
		if(empty($bool_return)) {
			$this->tpl->assign($row);
		}
		else {
			return $row;
		}
	}

	function bNewForm($row)
	{
		if(!$this->quotes_runtime) {
			$row = $this->handleSlash($row);
		}
		return $row;
	}

	function bEditForm($qry)
	{
		$query = array();
		$query['table_name'] = empty($qry['table_name']) ? $this->config['table_name'] : $qry['table_name'];
		$query['tool'] = empty($qry['tool']) ? 'row' : $qry['tool'];
		$query['where'] = empty($qry['where']) ? '' : $qry['where'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!$this->quotes_runtime) {
			$row = $this->handleSlash($row);
		}
		return $row;
	}

	function bWrite($qry,&$arr,$_func='_write')
	{
		$arr = $this->_bWrite($arr);
		if(!empty($this->config['bool_editor'])) {
			foreach($this->config['editor_target'] as $key =>$val) {
				if(!empty($arr[$val])) {
					$arr[$val] = $this->editorWrite($arr[$val]);
				}
			}
		}
		if(!empty($_FILES)) { $arr = $this->upload($arr); }
		if(!empty($_func) && method_exists($this,$_func) && $_func != 'none') {
			$arr = $this->$_func($arr);
		}
		$query = array();
		$query['table_name'] = empty($qry['table_name']) ? $this->config['table_name'] : $qry['table_name'];
		$query['tool'] = empty($qry['tool']) ? 'insert' : $qry['tool'];
		$query['fields'] = $this->config['query_func']($arr);
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		return $result;
	}

	function bEdit($qry,&$arr,$_func='_write')
	{
		$arr = $this->_bWrite($arr);
		$query = array();
		$query['table_name'] = empty($qry['table_name']) ? $this->config['table_name'] : $qry['table_name'];
		$query['tool'] = 'row';
		$query['where'] = $qry['where'];
		$row = $this->dbcon->query($query,__FILE__,__LINE__);

		if(!empty($this->config['bool_editor']) && !empty($this->config['editor_target'])) {
			if(is_array($this->config['editor_target'])) {
				foreach ($this->config['editor_target'] as $key => $val) {
					if(!empty($arr[$val])) {
						$arr[$val] = $this->editorEdit($row[$val],$arr[$val]);
					}
				}
			}
			else {
				$editor_target = $this->config['editor_target'];
				if(!empty($arr[$editor_target])) {
					$arr[$this->config['editor_target']] = $this->editorEdit($row[$editor_target],$arr[$editor_target]);
				}
			}
		}

		if(!empty($_FILES)) {
			//기존화일을 삭제한다.
			foreach(array_keys($_FILES) as $key=>$val) {
				if($_FILES[$val]['size'] > 0 && !empty($row[$val])) {

					switch($this->config['file_storage_type']) {

						case 'aws-s3' :
						case 's3' :
							
							$save_file = $row[$val];

							require __DIR__.'/../cheditor/imageUpload/s3.php';
							// var_dump($url, $tmp_url, $file); 
							$S3 = new S3();
							$S3->delete_file_to_s3($save_file) ;

							break;

						default: 
						
							$save_file = ROOT_DIR.$this->config['file_dir'].'/'.$row[$val];

							if(!$this->removeFile($save_file)) {
								if($this->config['write_mode'] == 'ajax') {
									jsonMsg(0,'err_del_file');
								}
								else {
									errMsg(Lang::main_basic1);
								}
							}
							//썸네일이미지가 있으면 삭제한다.
							if(!empty($this->config['bool_thumb']) && !empty($this->config['thumb_target'])) {
								if(is_array($this->config['thumb_target'])) {
									//썸네일 대상이 많은 경우
									if(in_array($val,$this->config['thumb_target'])) {
										$this->removeThumb($row[$val]);
									}
								}
								//썸네일 대상이 하나인 경우
								else {
									if($this->config['thumb_target'] == $val) {
										$this->removeThumb($row[$val]);
									}
								}
							}

					}
				}
			}
			$arr = $this->upload($arr);
		}
		if(!empty($_func) && method_exists($this,$_func) && $_func != 'none') {
			$arr = $this->$_func($arr);
		}
		$query['tool'] = empty($qry['tool']) ? 'update' : $qry['tool'];
		$query['fields'] = $this->config['query_func']($arr);
		$query['where'] = $qry['where'];
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		return $result;
	}

	function removeThumb($file_name)
	{
		if(!empty($this->config['thumb_size'])) {
			//썸네일이 여러 사이즈 일경우
			if(is_array($this->config['thumb_size'])) {
				$thumb_dir = array_keys($this->config['thumb_size']);
				foreach ($thumb_dir as $key => $val) {
					$thumb_file = ROOT_DIR.'/'.$val.'/'.$file_name;
					if(!$this->removeFile($thumb_file)) {
						if($this->config['write_mode'] == 'ajax') {
							jsonMsg(0,'err_del_thumb');
						}
						else {
							errMsg(Lang::main_basic2);
						}
					}
				}
			}
			//한가지 사이즈 일경우
			else {
				$thumb_file = ROOT_DIR.$this->config['thumb_dir'].'/'.$file_name;
				if(!$this->removeFile($thumb_file)) {
					if($this->config['write_mode'] == 'ajax') {
						jsonMsg(0,'err_del_thumb');
					}
					else {
						errMsg(Lang::main_basic2);
					}
				}
			}
		}
	}

	function _bWrite($arr)
	{
		if(!empty($this->config['staple_article'])) {
			$this->stapleArticle($arr);
		}

		if(!$this->quotes_gpc) {
			$arr = $this->handleSlash($arr,'add');
		}

		if(!empty($this->config['no_tag'])) {
			if(is_array($this->config['no_tag'])) {
				foreach($this->config['no_tag'] as $key =>$val) {
					if(!empty($arr[$val])) {
						$arr[$val] = $this->removeTag($arr[$val]);
					}
				}
			}
			else {
				if(!empty($arr[$this->config['no_tag']])) {
					$arr[$this->config['no_tag']] = $this->removeTag($arr[$this->config['no_tag']]);
				}
			}
		}

		if(!empty($this->config['no_space'])) {
			if(is_array($this->config['no_space'])) {
				foreach($this->config['no_space'] as $key =>$val) {
					if(!empty($arr[$val])) {
						$arr[$val] = $this->removeSpace($arr[$val]);
					}
				}
			}
			else {
				if(!empty($arr[$this->config['no_space']])) {
					$arr[$this->config['no_space']] = $this->removeSpace($arr[$this->config['no_space']]);
				}
			}
		}
		return $arr;
	}

	function bDel($qry)
	{
		$query = array();
		$query['table_name'] = empty($qry['table_name']) ? $this->config['table_name'] : $qry['table_name'];
		$query['where'] = $qry['where'];
		$query['tool'] = 'row';
		$row = $this->dbcon->query($query,__FILE__,__LINE__);
		if(!empty($this->config['bool_editor']) && !empty($this->config['editor_target'])) {
			foreach ($this->config['editor_target'] as $key => $val) {
				if(!empty($row[$val])) {
					$this->editorDel($row[$val]);
				}
			}
		}
		if(!empty($this->config['bool_file']) && !empty($this->config['file_target'])) {
			//파일대상이 배열일때
			if(is_array($this->config['file_target'])) {
				foreach($this->config['file_target'] as $key=>$val) {
					if(!empty($row[$val])) {
						if(!$this->removeFile(ROOT_DIR.$this->config['file_dir'].'/'.$row[$val])) {
							if($this->config['write_mode'] == 'ajax') {
								jsonMsg(0,'err_del_file');
							}
							else {
								errMsg(Lang::main_basic1);
							}
						}
						$use_thumb = !empty($this->config['bool_thumb']) ? TRUE : FALSE;
						if($use_thumb) {
							if(!empty($this->config['thumb_size'])) {
								if(is_array($this->config['thumb_size'])) {
									foreach ($this->config['thumb_size'] as $_key => $_val) {
										$this->removeFile(ROOT_DIR.'/data/'.$_key.'/'.$row[$val]);
									}
								}
								else {
									$this->removeFile(ROOT_DIR.$this->config['thumb_dir'].'/'.$row[$val]);
								}
							}
						}
					}
				}
			}
			//배열이 아닐때
			else {
				if(!$this->removeFile(ROOT_DIR.$this->config['file_dir'].'/'.$row[$this->config['file_target']])) {
					if($this->config['write_mode'] == 'ajax') {
						jsonMsg(0,'err_del_file');
					}
					else {
						errMsg(Lang::main_basic1);
					}
				}

				$use_thumb = !empty($this->config['bool_thumb']) ? TRUE : FALSE;
				if($use_thumb) {
					if(!empty($this->config['thumb_size'])) {
						if(is_array($this->config['thumb_size'])) {
							foreach ($this->config['thumb_size'] as $_key => $_val) {
								$this->removeFile(ROOT_DIR.'/data/'.$_key.'/'.$row[$this->config['file_target']]);
							}
						}
						else {
							$this->removeFile(ROOT_DIR.$this->config['thumb_dir'].'/'.$row[$this->config['file_target']]);
						}
					}
				}
			}
		}
		$query['tool'] = 'delete';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		return $result;
	}

	function upload($arr)
	{
		foreach ($_FILES as $key => $val) {
			if (empty($this->config['bool_thumb'])) {
				$bool_thumb = FALSE;
			}
			else {
				if(empty($this->config['thumb_target'])) {
					$bool_thumb = FALSE;
				}
				else {
					$bool_thumb = in_array($key,$this->config['thumb_target']) ? TRUE : FALSE;
				}
			}
			if($_FILES[$key]['size'] > 0 && in_array($key,$this->config['file_target'])) {
				$ret = $this->_upload($_FILES[$key],$bool_thumb);
				if(!$ret) {
					if($this->config['write_mode'] == 'ajax') {
						jsonMsg(0,'err_upload');
					}
					else {
						errMsg(Lang::main_basic3);
					}
				}
				else {
					$arr[$key] = $ret['save'];
					$arr[$key.'_src'] = $ret['src'];
				}
				// var_dump($ret, $key, $arr[$key]); exit;
			}
		}
		return $arr;
	}

	function _upload($file,$bool_thumb=FALSE)
	{
		if(!empty($this->config['upload_limit'])) {
			if(!$this->uploadLimit($file['type'])) {
				if($this->config['write_mode'] == 'ajax') {
					jsonMsg(0,'err_upload_limit');
				}
				else {
					errMsg(Lang::main_basic5);
				}
			}
		}
		if($file['size'] > $this->config['file_size']*1024*1024) {
			if($this->config['write_mode'] == 'ajax') {
				jsonMsg(0,'err_upload_size');
			}
			else {
				errMsg(Lang::main_basic6);
			}
		}
		$file_name = randCode();
		$file_ext = $this->getExtension($file['name']);
		$save_file = $file_name.'.'.$file_ext;
		$ret = array();
		$ret['src'] = $file['name'];
		$ret['save'] = $save_file;

		switch($this->config['file_storage_type']) {
			case 'aws_s3' :
			case 's3' :
				require __DIR__.'/../cheditor/imageUpload/s3.php';
				// var_dump($url, $tmp_url, $file); 
				$S3 = new S3();
				$tmp_url = $S3->save_file_to_s3($file) ;
				// var_dump($tmp_url);  exit;
				// array(1) {
				// 	[0]=>
				// 	array(2) {
				// 	  ["name"]=>
				// 	  string(15) "test-benner.png"
				// 	  ["url"]=>
				// 	  string(125) "https://kmcse.s3.ap-northeast-2.amazonaws.com/tmp/202208/2645e4d0877eed4fbcdb085dd31c3d17dac765212cfacff49b592b2598af9a94.png"
				// 	}
				// }
				if($tmp_url) {
					$tmp_url = $tmp_url[0]['url'];
					$url = $S3->copy_tmpfile_to_s3($tmp_url);
					$S3->delete_file_to_s3($tmp_url);
					// var_dump($tmp_url, $url);
					$ret['save'] = $url;
					return $ret;
				} else {
					@unlink($file['tmp_name']);
					return FALSE;
				}

				break;

			default:

				if(move_uploaded_file($file['tmp_name'],ROOT_DIR.$this->config['file_dir'].'/'.$save_file)) {
					@unlink($file['tmp_name']);
					if($bool_thumb && preg_match('/^image\/(x-)?\w+$/i',$file['type'])) {
						if(!empty($this->config['thumb_size'])) {
							if(is_array($this->config['thumb_size'])) {
								foreach ($this->config['thumb_size'] as $key => $val) {
									$val = strtolower($val);
									$arr_thumb_size = explode('x',$val);
									$thumb_width = (empty($arr_thumb_size[0])) ? 150 : $arr_thumb_size[0];
									$thumb_height = (empty($arr_thumb_size[1])) ? 150 : $arr_thumb_size[1];
									$thumb_file = $save_file;
									$bool_resize = isset($this->config['bool_resize']) ? $this->config['bool_resize'] : TRUE;
									if(!$this->thumbnail2(ROOT_DIR.$this->config['file_dir'].'/'.$save_file,ROOT_DIR.'/data/'.$key.'/'.$thumb_file,$thumb_width,$thumb_height,$bool_resize)) {
										if($this->config['write_mode'] == 'ajax') {
											jsonMsg(0,'err_make_thumb');
										}
										else {
											errMsg(Lang::main_basic4);
										}
									}
								}
							}
							else {
								$font_size = strtolower($this->config['thumb_size']);
								$arr_thumb_size = explode('x',$font_size);
								$thumb_width = (empty($arr_thumb_size[0])) ? 150 : $arr_thumb_size[0];
								$thumb_height = (empty($arr_thumb_size[1])) ? 150 : $arr_thumb_size[1];
								$thumb_file = $save_file;
								$bool_resize = isset($this->config['bool_resize']) ? $this->config['bool_resize'] : TRUE;
								if(!$this->thumbnail2(ROOT_DIR.$this->config['file_dir'].'/'.$save_file,ROOT_DIR.$this->config['thumb_dir'].'/'.$thumb_file,$thumb_width,$thumb_height,$bool_resize)) {
									if($this->config['write_mode'] == 'ajax') {
										jsonMsg(0,'err_make_thumb');
									}
									else {
										errMsg(Lang::main_basic4);
									}
								}
							}
						}
					}
		
					return $ret;
				}
				else {
					@unlink($file['tmp_name']);
					return FALSE;
				}
		}
		
	}

	/*
	$_FILES["file"]["name"] - the name of the uploaded file
	$_FILES["file"]["type"] - the type of the uploaded file
	$_FILES["file"]["size"] - the size in bytes of the uploaded file
	$_FILES["file"]["tmp_name"] - the name of the temporary copy of the file stored on the server
	$_FILES["file"]["error"] - the error code resulting from the file upload
	*/

	function fileUpload($file,$save_file)
	{
		if($file['error'] > 0) {
			jsonMsg(0);
		}
		if($file['size'] > 20000) {
			jsonMsg(0,'err_size');
		}
		//폴더에 동일한 파일이 존재하는지 확인한후 동일한 화일이 존재할 경우 삭제한다.
		if(file_exists($save_file) ){
			unlink($save_file);
		}
		if(move_uploaded_file($file['tmp_name'],$save_file)) {
			@unlink($file['tmp_name']);
			return TRUE;
		}
		else {
			jsonMsg(0,'err_upload');
		}
	}

	/*
	필수항목체크 프로세스 수정
	array(
		'필드명'=>'체크타입'
	);
	*/
	function stapleArticle($arr)
	{
		if(!empty($this->config['staple_article'])) {
			foreach($this->config['staple_article'] as $key=>$val) {
				if(empty($arr[$key])) {
					if($this->config['write_mode'] == 'ajax') {
						jsonMsg(0,'err_empty_'.$key);
					}
					else {
						errMsg(strtr(Lang::main_basic7, '{key}', $key));
					}
				}
				else {
					if($val != 'blank') {
						if(!$this->checkValid($arr[$key],$val)) {
							if($this->config['write_mode'] == 'ajax') {
								jsonMsg(0,'err_type_'.$key);
							}
							else {
								errMsg(strtr(Lang::main_basic7, '{key}', $key));
							}
						}
					}
				}
			}
		}
		return TRUE;
	}

	function checkValid ($data,$type) {
		if( $type == 'digit') {
			$filter = '/^\d+$/';
		}
		else if ($type == 'hangul') {
			$filter = '/^[가-힣]+$/';
		}
		else if ($type == 'charall') {
			$filter = '/^[0-9a-zA-Z가-힣]+$/';
		}
		else if ($type == 'capital') {
			$filter = '/^[A-Z][0-9a-zA-Z]+$/';
		}
		else if ($type == 'alpha') {
			$filter = '/^[a-zA-Z]+$/';
		}
		else if ($type == 'alnum') {
			$filter = '/^[0-9a-zA-Z]+$/';
		}
		else if ($type == 'passwd') {
			$filter = '/^[a-zA-Z][0-9a-zA-Z]{3,}$/';
		}
		else if ($type == 'userid') {
			$filter = '/^[a-zA-Z][0-9a-zA-Z]{3,}$/';
		}
		else if ($type == 'email') {
			$filter = '/^([\w.])+\@(([\w])+\.)[a-zA-Z0-9]{2,}/';
		}
		else if ($type == 'filename') {
			$filter = '/^([\w])+.[a-zA-Z0-9]{2,}/';
		}
		else {
			$filter = '/^[\s\S]*$/';
		}
		return preg_match($filter,$data);
	}

	function naviPage($url,$total,$qry="",$justify="center")
	{
		/*
		<ul class="pagination justify-content-center">
			<li class="page-item disabled">
				<a class="page-link" href="#" tabindex="-1">Previous</a>
			</li>
			<li class="page-item"><a class="page-link" href="#">1</a></li>
			<li class="page-item"><a class="page-link" href="#">2</a></li>
			<li class="page-item"><a class="page-link" href="#">3</a></li>
			<li class="page-item">
				<a class="page-link" href="#">Next</a>
			</li>
		</ul>
		*/
		if(!$justify && $justify!=='') { $justify = 'center'; }
		if($justify == 'center') {
			$justify = 'justify-content-center';
		} else if($justify == 'end') {
			$justify = 'justify-content-end';
		} else {
			$justify = '';
		}

		$ret = array();
		$ret[] = '<ul class="pagination '.$justify.'">'."\n";
		$start = empty($_GET['start']) ? 0 : $_GET['start'];
		$page = floor($start/($this->config['loop_scale']*$this->config['page_scale']));
		$arr = array();

		if(empty($this->config['navi_mode'])) {
			$arr['pre'] = $arr['end'] = '';
		}
		else {
			if($this->config['navi_mode'] == 'ajax') {
				$arr['pre'] = 'listLoad(\''.$this->config['navi_load_id'].'\',\'';
				$arr['end'] = '\')';
			}
			else {
				$arr['pre'] = 'location.href=\'';
				$arr['end'] = '\'';
			}
		}

		if ($total > $this->config['loop_scale']) {
			if($this->config['page_scale'] > $this->config['loop_scale']) {
				//처음
				if($start + 1 > $this->config['loop_scale']*$this->config['page_scale']) {
					$p_start = ($page-1)*$this->config['loop_scale']*$this->config['page_scale'];
					$href = $arr['pre'].$url.'?start='.$p_start.$qry.$arr['end'];
					$ret[] = '<li class="page-item disabled"><a href="javascript:;" onclick="'.$href.'" class="page-link"><<</a></li>'."\n";
				}
				else {
					$ret[] = '<li class="page-item disabled"><a href="javascript:;" class="page-link"><<</a></li>'."\n";
				}
			}
			//이전페이지
			if ($start+1>$this->config['loop_scale']) {
				$p_p = $start- $this->config['loop_scale'];
				$href = $arr['pre'].$url.'?start='.$p_p.$qry.$arr['end'];
				$ret[] = '<li class="page-item disabled"><a href="javascript:;" onclick="'.$href.'" class="page-link"><</a></li>'."\n";
			}
			else {
				$ret[] = '<li class="page-item disabled"><a href="javascript:;" class="page-link"><</a></li>'."\n";
			}
			//페이징넘버
			for ($vj=0;$vj<$this->config['page_scale'];$vj++) {
				$ln = ($page * $this->config['page_scale'] + $vj)*$this->config['loop_scale'] ;
				$vk = $page * $this->config['page_scale'] + $vj+1 ;
				if($ln < $total) {
					if($ln != $start) {
						$href = $arr['pre'].$url.'?start='.$ln.$qry.$arr['end'];
						$ret[] = '<li class="page-item"><a class="page-link" href="javascript:;" onclick="'.$href.'">'.$vk.'</a></li>'."\n";
					}
					else {
						$ret[] = '<li class="page-item active"><a class="page-link current" href="#"><strong>'.$vk.'</strong></a></li>'."\n";
					}
				}
			}
			//다음페이지
			if($start+$this->config['loop_scale'] < $total) {
				$n_p = $start+ $this->config['loop_scale'];
				$href = $arr['pre'].$url.'?start='.$n_p.$qry.$arr['end'];
				$ret[] = '<li class="page-item"><a class="page-link" href="javascript:;" onclick="'.$href.'">></a></li>'."\n";
			}
			else {
				$ret[] = '<li class="page-item"><a class="page-link" href="javascript:;">></a></li>'."\n";
			}
			if($this->config['page_scale'] > $this->config['loop_scale']) {
				//마지막페이지
				if($total > (($page+1)*$this->config['loop_scale']*$this->config['page_scale'])) {
					$n_start = ($page+1)*$this->config['loop_scale']*$this->config['page_scale'];
					$href = $arr['pre'].$url.'?start='.$n_start.$qry.$arr['end'];
					$ret[] ='<li class="page-item"><a class="page-link" href="javascript:;" onclick="'.$href.'">>></a></li>'."\n";
				}
				else {
					$ret[] ='<li class="page-item"><a class="page-link" href="javascript:;">>></a></li>'."\n";
				}
			}
		}
		else {
			//$ret[] = '<li class="page-item disabled"><a href="javascript:;" class="page-link" tabindex="-1"><<</a></li>'."\n";
			$ret[] = '<li class="page-item disabled"><a href="javascript:;" class="page-link" tabindex="-1"><</a></li>'."\n";
			$ret[] = '<li class="page-item"><a class="page-link" href="#"><strong>1</strong></a></li>'."\n";
			$ret[] = '<li class="page-item"><a class="page-link" href="javascript:;">></a></li>'."\n";
			//$ret[] ='<li class="page-item"><a class="page-link" href="javascript:;">>></a></li>'."\n";
		}
		$ret[] = '</ul>';
		$ret = implode('',$ret);
		return $ret;
	}

	// naviPage($url,$total,$qry="",$justify="center")
	function naviPagebind($kind_menu,$navi_pg_mode,$total,$qry="",$justify="center")
	{
		// $navi_page = $this->naviPagebind($this->config['kind_menu'],$navi_pg_mode,$this->total,$this->config['navi_qry'],$this->config['bool_navi_justify']);

		/*
		<ul class="pagination justify-content-center">
			<li class="page-item disabled">
				<a class="page-link" href="#" tabindex="-1">Previous</a>
			</li>
			<li class="page-item"><a class="page-link" href="#">1</a></li>
			<li class="page-item"><a class="page-link" href="#">2</a></li>
			<li class="page-item"><a class="page-link" href="#">3</a></li>
			<li class="page-item">
				<a class="page-link" href="#">Next</a>
			</li>
		</ul>
		*/

		$kind_menu =  '//'.$_SERVER['HTTP_HOST'].'/'.$kind_menu;

		if(!$justify && $justify!=='') { $justify = 'center'; }
		if($justify == 'center') {
			$justify = 'justify-content-center';
		} else if($justify == 'end') {
			$justify = 'justify-content-end';
		} else {
			$justify = '';
		}

		$ret = array();
		$ret[] = '<ul class="pagination '.$justify.'">'."\n";
		$start = empty($_GET['start']) ? 0 : $_GET['start'];
		$page = floor($start/($this->config['loop_scale']*$this->config['page_scale']));

		$arr = array();
		if(empty($this->config['navi_mode'])) {
			$arr['pre'] = $arr['end'] = '';
		}
		else {
			if($this->config['navi_mode'] == 'ajax') {
				$arr['pre'] = 'listLoad(\''.$this->config['navi_load_id'].'\',\'';
				$arr['end'] = '\')';
			}
			else {
				$arr['pre'] = 'location.href=\'';
				$arr['end'] = '\'';
			}
		}

		if ($total > $this->config['loop_scale']) {
			if($this->config['page_scale'] > $this->config['loop_scale']) {
				//처음
				if($start + 1 > $this->config['loop_scale']*$this->config['page_scale']) {
					$p_start = ($page-1)*$this->config['loop_scale']*$this->config['page_scale'];
					$href = $arr['pre'].$kind_menu.$navi_pg_mode.$arr['end'];
					$ret[] = '<li class="page-item prev disabled"><a href="javascript:;" onclick="'.$href.'" class="page-link"><<</a></li>'."\n";
				}
				else {
					$ret[] = '<li class="page-item prev disabled"><a href="javascript:;" class="page-link"><<</a></li>'."\n";
				}
			}
			//이전페이지
			if ($start+1>$this->config['loop_scale']) {
				$p_p = $start- $this->config['loop_scale'];
				$href = $arr['pre'].$kind_menu.$navi_pg_mode.'/'.$p_p.$arr['end'];
				$ret[] = '<li class="page-item prev disabled"><a href="javascript:;" onclick="'.$href.'" class="page-link"><</a></li>'."\n";
			}
			else {
				$ret[] = '<li class="page-item prev disabled"><a href="javascript:;" class="page-link"><</a></li>'."\n";
			}
			//페이징넘버
			for ($vj=0;$vj<$this->config['page_scale'];$vj++) {
				$ln = ($page * $this->config['page_scale'] + $vj)*$this->config['loop_scale'] ;
				$vk = $page * $this->config['page_scale'] + $vj+1 ;
				// var_dump($total, $ln, $page); exit;
				if($ln < $total) {
					if($ln != $start) {
						$href = $arr['pre'].$kind_menu.$navi_pg_mode.'/'.$ln.$arr['end'];
						$ret[] = '<li class="page-item"><a class="page-link" href="javascript:;" onclick="'.$href.'">'.$vk.'</a></li>'."\n";
					}
					else {
						$ret[] = '<li class="page-item active"><a class="page-link current" href="#"><strong>'.$vk.'</strong></a></li>'."\n";
					}
				}
			}
			//다음페이지
			if($start+$this->config['loop_scale'] < $total) {
				$n_p = $start+ $this->config['loop_scale'];
				$href = $arr['pre'].$kind_menu.$navi_pg_mode.'/'.$n_p.$arr['end'];
				$ret[] = '<li class="page-item next"><a class="page-link" href="javascript:;" onclick="'.$href.'">></a></li>'."\n";
			}
			else {
				$ret[] = '<li class="page-item next"><a class="page-link" href="javascript:;">></a></li>'."\n";
			}
			if($this->config['page_scale'] > $this->config['loop_scale']) {
				//마지막페이지
				if($total > (($page+1)*$this->config['loop_scale']*$this->config['page_scale'])) {
					$n_start = ($page+1)*$this->config['loop_scale']*$this->config['page_scale'];
					$href = $arr['pre'].$kind_menu.$navi_pg_mode.'/'.$n_start.$arr['end'];
					$ret[] ='<li class="page-item"><a class="page-link" href="javascript:;" onclick="'.$href.'">>></a></li>'."\n";
				}
				else {
					$ret[] ='<li class="page-item"><a class="page-link" href="javascript:;">>></a></li>'."\n";
				}
			}
		}
		else {
			//$ret[] = '<li class="page-item disabled"><a href="javascript:;" class="page-link" tabindex="-1"><<</a></li>'."\n";
			$ret[] = '<li class="page-item prev disabled"><a href="javascript:;" class="page-link" tabindex="-1"><</a></li>'."\n";
			$ret[] = '<li class="page-item"><a class="page-link on" href="#"><strong class="on">1</strong></a></li>'."\n";
			$ret[] = '<li class="page-item next"><a class="page-link" href="javascript:;">></a></li>'."\n";
			//$ret[] ='<li class="page-item"><a class="page-link" href="javascript:;">>></a></li>'."\n";
		}
		$href = '';
		$ret[] = '</ul>';
		$ret = implode('',$ret);
		return $ret;
	}

	/******************************************************
	에디터 관련 메소드
	******************************************************/
	function editorWrite($input)
	{
		//$input = htmlspecialchars_decode($input);

		foreach($this->editorGetImg($input) as $key=>$val) {
			$this->editorMoveImg($val);
			if(!empty($this->config['bool_editor_thumb'])) {
				if($key == 0 ) {
					$this->editorThumb($val);
				}
			}
		}

		$input = str_replace($this->config['temp_dir'],$this->config['editor_dir'],$input);
		return $input;
	}

	function editorEdit($old_input,$new_input)
	{
		//$new_input = htmlspecialchars_decode($new_input);
		$new_img = $this->editorGetImg($new_input);
		$old_img = $this->editorGetImg($old_input);
		$diff_img = array_diff($new_img,$old_img);
		if(!empty($new_img)) {
			foreach($diff_img as $key=>$val) {
				$this->editorMoveImg($val);
			}
		}
		if(!empty($old_img)) {
			$diff_img = array_diff($old_img,$new_img);
			foreach($diff_img as $key=>$val) {
				$img = ROOT_DIR.$this->config['editor_dir'].'/'.$val;
				if(file_exists($img)) {
					@unlink($img);
				}
			}
		}
		if(!empty($this->config['bool_editor_thumb'])) {
			if(!empty($new_img[0])) {
				$this->editorThumb($new_img[0]);
			}
		}
		return str_replace($this->config['temp_dir'],$this->config['editor_dir'],$new_input);
	}

	function editorDel($input)
	{
		foreach($this->editorGetImg($input) as $key=>$val) {
			$img = ROOT_DIR.$this->config['editor_dir'].'/'.$val;
			if(file_exists($img)) {
				@unlink($img);
			}
			if(empty($this->config['bool_editor_thumb'])) {
				$img = ROOT_DIR.$this->config['thumb_dir'].'/'.$val;
				if(file_exists($img)) {
					@unlink($img);
				}
			}
		}
	}

	function editorMoveImg($val)
	{
		$this->fileMove(ROOT_DIR.$this->config['temp_dir'].'/'.$val,ROOT_DIR.$this->config['editor_dir'].'/'.$val);
	}

	function editorThumb($val)
	{
		$src_img = ROOT_DIR.$this->config['editor_dir'].'/'.$val;
		$dst_img = ROOT_DIR.$this->config['thumb_dir'].'/'.$val;
		if(!file_exists($dst_img)) {
			$bool_resize = isset($this->config['editor_thumb_resize']) ? $this->config['editor_thumb_resize'] : FALSE;
			$this->thumbnail2($src_img,$dst_img,$this->config['editor_thumb_width'],$this->config['editor_thumb_height'],$bool_resize);
		}
	}

	function editorGetImg($input)
	{
		preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $input, $match);
		return $match[0];
	}

	/*****************************************************************
	유틸리티
	*****************************************************************/
	function handleSlash($input,$way='strip')
	{
		if(!empty($input)) {
			if(gettype($input) == 'array') {
				foreach ($input as $key=>$val) {
					if(gettype($val) != 'array') {
						if(!empty($input[$key])) {
							if(is_string($input[$key])) {
								$input[$key] = ($way == 'add') ?  addslashes($input[$key]) : stripslashes($input[$key]);
							}
						}
					}
				}
			}
			else {
				if(is_string($input)) {
					$input = ($way == 'add') ? addslashes($input) : stripslashes($input);
				}
			}
		}
		else {
			$input = FALSE;
		}
		return $input;
	}

	function removeSpace($input)
	{
		$input = trim($input);
		$input = str_replace(" ","",$input);
		return $input;
	}

	function removeTag($input)
	{
		$input = htmlspecialchars($input);
		$input = nl2br($input);
		return $input;
	}

	function removeScript($input)
	{
		$pattern = "@<\s*?script[^>]*?>.*?</script\s*?>@is";
		$replace = '';
		return preg_replace($pattern,$replace,$input);
	}

	function fileCopy($src_file,$dst_file)
	{
		if(@file_exists($src_file)) {
			if(copy($src_file,$dst_file)) {
				$ret = TRUE;
			}
			else {
				$ret = FALSE;
			}
		}
		else {
			$ret = FALSE;
		}
		return $ret;
	}

	function fileMove($src_file,$dst_file)
	{
		$ret = FALSE;
		if(@file_exists($src_file)) {
			if(!file_exists($dst_file)) {
				if(copy($src_file,$dst_file)) {
					@unlink($src_file);
					$ret = TRUE;
				}
			}
		}
		return $ret;
	}

	function removeFile($input)
	{
		if(file_exists($input)) {
			if(@unlink($input)) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return TRUE;
		}
	}

	function makeDir($dir_name)
	{
		$ret = TRUE;
		if(!is_dir($dir_name)) {
			@umask(000);
			if(!@mkdir($dir_name,0777)) {
				$ret = FALSE;
			}
		}
		//if(!is_dir($dir_name)) { $ret = FALSE; }
		return $ret;
	}

	function removeDir($dir_name)
	{
		$ret = TRUE;
		if(is_dir($dir_name)) {
			$dh = @opendir($dir_name);
			while($file_name = readdir($dh)) {
				if ($file_name != "." && $file_name != "..") {
					unlink($dir_name."/".$file_name);
				}
			}
			closedir($dh);
			if(!@rmdir($dir_name)) {
				$ret = FALSE;
			}
		}
		else {
			$ret = FALSE;
		}
		return $ret;
	}

	function uploadLimit($input)
	{
		if(preg_match('/^image\/\w+$/i',$input)) {
			if(preg_match('/^image\/\w*?(gif|jpg|png|jpeg)$/i',$input)) {
				$ret = TRUE;
			}
			else {
				$ret = FALSE;
			}
		}
		else {
			if(preg_match('/^\w+\/(php|cgi|php3|pl|html|htm)$/i',$input)) {
				$ret = FALSE;
			}
			else {
				$ret = TRUE;
			}
		}
		return $ret;
	}

	function getExtension($input)
	{
		$arr = explode(".",$input);
		$ext = strtolower(end($arr));
		return $ext;
	}

	function getFileType($input)
	{
		$ext = $this->getExtension($input);
		if(preg_match('/^(gif|jpg|png|jpeg)$/i',$ext)) {
			$ret = 'img';
		}
		else {
			$ret = 'etc';
		}
		return $ret;
	}

	function download($file_name,$file_name_src='')
	{
		$file = ROOT_DIR.$this->config['file_dir'].'/'.$file_name;
		if(empty($file_name_src)) {
			$dn_file = trim($file_name);
		}
		else {
			$dn_file = trim($file_name_src);
		}
		if(is_file($file)) {
			$file_size = filesize($file);
			if(preg_match("/"."(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0|MSIE 7.0)"."/", $_SERVER['HTTP_USER_AGENT'])){
				//header("Content-Type: application/octet-stream");
				header("Content-Type: doesn/matter");
	            //header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				Header("Content-Length: ".$file_size);
				header("Content-Disposition: attachment; filename=".$dn_file);
				header("Content-Transfer-Encoding: binary");
				header("Pragma: no-cache");
				header("Expires: 0");
			}
			else {
				Header("Content-type: file/unknown");
				Header("Content-Disposition: attachment; filename=".$dn_file);
				Header("Content-Transfer-Encoding: binary");
				Header("Content-Length: ".$file_size);
				Header("Content-Description: PHP4 Generated Data");
				header("Pragma: no-cache");
				header("Expires: 0");
			}
			$fp = fopen($file, "r");
			if (!fpassthru($fp)) { fclose($fp); }
		}
		else{
			if($this->config['write_mode'] == 'ajax') {
				jsonMsg(0,'err_no_file');
			}
			else {
				errMsg(Lang::main_basic8);
			}
		}
	}

	//max_width,max_height 는 필수 입력 요소입니다.
	function thumbnail($src_img,$dst_img,$max_width=150,$max_height=150,$bool_resize=FALSE,$quality=100)
	{
		$is_read = is_readable($src_img);
		$is_exist = file_exists($src_img);
		$is_gd = extension_loaded('gd');
		if($is_read && $is_exist && $is_gd) {
			$img_info = getimagesize($src_img);
			$arr_type = array(1,2,3);
			if(!in_array($img_info[2],$arr_type)) {
				return FALSE;
			}
			else {
				switch($img_info[2]) {
					case 1:
						$src_img = imagecreatefromgif($src_img);
						break;
					case 2:
						$src_img = imagecreatefromjpeg($src_img);
						break;
					case 3:
						$src_img = imagecreatefrompng($src_img);
						break;
				}
				$src_width = $img_info[0];
				$src_height = $img_info[1];

				if($bool_resize) {
					$dst_x = $dst_y = 0;
					$dst_width = $max_width;
					$dst_height = $max_width;
					$resize_width = $resize_height = TRUE;
					if($src_width > $src_height) {
						$src_y = 0;
						$src_x = round(($src_width-$src_height)/2);
						$src_width = $src_height;
					}
					else {
						$src_x = 0;
						$src_y = round(($src_height-$src_width)/2);
						$src_height = $src_width;
					}
				}
				else {
					$src_x = $src_y = 0;
					$dst_x = $dst_y = 0;
					$ratio = $src_height / $src_width;
					$dst_width = $max_width;
					$dst_height = $max_height= round($dst_width * $ratio);
				}

				$new_img = imagecreatetruecolor($max_width,$max_height);
				$white = imagecolorallocate($new_img, 255, 255, 255);
				ImageFill($new_img, 0, 0, $white);
				imagecopyresampled($new_img, $src_img,$dst_x,$dst_y,$src_x,$src_y, $dst_width, $dst_height, $src_width,$src_height);
				switch($img_info[2]) {
					case 1:
						imagegif($new_img,$dst_img);
						break;
					case 2:
						imagejpeg($new_img,$dst_img,$quality);
						break;
					case 3:
						imagepng($new_img,$dst_img);
						break;
				}
				imagedestroy($new_img);
				imagedestroy($src_img);
				return TRUE;
			}
		}
		else {
			return FALSE;
		}
	}

	/*
		$thumbnail = imagecreatetruecolor($width, $height);
		width			- max_width
		height		- max_height

		imagecopyresampled
		thumbnail - new_img
		image			- src_img
		thumb_x	- dst_x
		thumb_y	- dst_y
		0	- src_x
		0	- src_y
		thumb_width	- dst_width
		thumb_height	- dst_height
		image_width	- src_width
		image_height	- src_height
	*/

	//width,height 는 필수 입력 요소입니다.
	function thumbnail2($image,$dst_img,$width=150,$height=150,$bool_resize=FALSE,$quality=100)
	{
		$is_read = is_readable($image);
		$is_exist = file_exists($image);
		$is_gd = extension_loaded('gd');
		if($is_read && $is_exist && $is_gd) {
			$img_info = getimagesize($image);
			$arr_type = array(1,2,3);

			if(!in_array($img_info[2],$arr_type)) {
				return FALSE;
			}
			else {
				switch($img_info[2]) {
					case 1:
						$image = imagecreatefromgif($image);
						break;
					case 2:
						$image = imagecreatefromjpeg($image);
						break;
					case 3:
						$image = imagecreatefrompng($image);
						break;
				}

				// AntiAlias
				if (function_exists('imageantialias'))
					imageantialias($image, TRUE);

				$image_width = $img_info[0];
				$image_height = $img_info[1];


				if($bool_resize) {
					$thumb_x = $thumb_y = 0;

					// side - width
					if($image_width > $image_width) {

						$ratio = $image_width / $width;
						$thumb_width = $width;
						$thumb_height = floor($image_height / $ratio);
						$thumb_y = round(($height - $thumb_height) / 2);

					// side - height
					}
					else {
						$ratio = $image_height / $height;
						$thumb_width = floor($image_width / $ratio);
						$thumb_height = $height;
						$thumb_x = round(($width - $thumb_width) / 2);
					}
				}
				else {
					// width 또는 height 크기가 지정되지 않았을 경우,
					// 지정된 섬네일 크기 비율에 맞게 다른 면의 크기를 맞춤
					$thumb_x = $thumb_y = 0;
					if ( ! $width)
					{
						$thumb_width = $width = intval($image_width / ($image_height / $height));
						$thumb_height = $height;
					}
					elseif ( ! $height)
					{
						$thumb_width = $width;
						$thumb_height = $height = intval($image_height / ($image_width / $width));
					}
				}

				$thumbnail = imagecreatetruecolor($width,$height);
				$white = imagecolorallocate($thumbnail, 255, 255, 255);
				ImageFill($thumbnail, 0, 0, $white);
				imagecopyresampled($thumbnail, $image,$thumb_x,$thumb_y,0,0, $thumb_width, $thumb_height, $image_width,$image_height);

				/*
					$thumbnail = imagecreatetruecolor($width, $height);
					width			- max_width
					height		- max_height

					@imagecopyresampled($thumbnail, $image, $thumb_x, $thumb_y, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);

					thumbnail - new_img
					image			- src_img
					thumb_x	- dst_x
					thumb_y	- dst_y
					0	- src_x
					0	- src_y
					thumb_width	- dst_width
					thumb_height	- dst_height
					image_width	- src_width
					image_height	- src_height
				*/

				switch($img_info[2]) {
					case 1:
						imagegif($thumbnail,$dst_img);
						break;
					case 2:
						imagejpeg($thumbnail,$dst_img,$quality);
						break;
					case 3:
						imagepng($thumbnail,$dst_img);
						break;
				}
				imagedestroy($thumbnail);
				imagedestroy($image);
				return TRUE;
			}
		}
		else {
			return FALSE;
		}
	}

	function logicalSum($arr)
	{
		$ret = TRUE;
		foreach($arr as $key=>$val) {
			$ret = $ret && $val;
		}
		return $ret;
	}

	function dateModify($val,$separator='-')
	{
		$arr = explode($separator,$val);
		if(sizeof($arr) >= 3) {
			$ret = mktime ( 0,0,0,$arr[1],$arr[2],$arr[0]);
		}
		else {
			$ret = date('Y-m-d',$val);
		}
		return $ret;
	}
}

?>