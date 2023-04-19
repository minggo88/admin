<?php
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
class DB
{



	var $db_host;
	var $db_name;
	var $db_user;
	var $db_pass;
	var $db_charset;
	var $debug_mode;

	function DB($db_host,$db_name,$db_user,$db_pass,$db_charset='',$debug_mode='1')
	{
		$this->db_host	= $db_host;
		$this->db_name	= $db_name;
		$this->db_user	= $db_user;
		$this->db_pass	= $db_pass;
		$this->db_charset	= $db_charset;
		$this->debug_mode = $debug_mode;

		// var_dump($this->db_host,$this->db_user,$this->db_pass); exit;

		$this->connect	= mysqli_connect($this->db_host,$this->db_user,$this->db_pass);
        echo $this->connect;
		if(! $this->connect) {
			echo "Mysql Connection Error: Failed connecting to database server\r\n\r\n" . mysqli_connect_error() . "";
			exit;
		}

		if(!empty($this->db_charset)) { mysqli_query($this->connect,'SET NAMES '.$this->db_charset); }
		mysqli_select_db($this->connect,$this->db_name);

		// DB 쿼리 모드에서 STRICT를 제거한다.
		$this->query("SET sql_mode = 'ALLOW_INVALID_DATES'");
		// MySQL 8 에서 날짜 형식 오류 제외하기
		// SELECT VERSION();
		// mysqli_query($this->connect,"SET @@SESSION.sql_mode = CONCAT_WS(',', @@SESSION.sql_mode, 'ALLOW_INVALID_DATES')");

	}

	function query($arr,$err_file='',$err_line='')
	{
		$run_cache = false;
		if(is_array($arr)) {
			if(empty($arr['fields'])) {
				$arr['fields'] = '*';
			}
			if(empty($arr['where'])) {
				$arr['where'] = '';
			}
			switch($arr['tool']) {
				case 'select':
				case 'select_one';
				case 'select_affect':
					$run_cache = true;
					$qry = 'select '.$arr['fields'].' from '.$arr['table_name'].' '.$arr['where'];
					break;
				case 'row':
					$run_cache = true;
					$qry = 'select '.$arr['fields'].' from '.$arr['table_name'].' '.$arr['where'].' limit 1';
					break;
				case 'count';
					$run_cache = true;
					$qry = 'select count(*) from '.$arr['table_name'].' '.$arr['where'];
					break;
				case 'insert':
				case 'insert_idx':
					$qry = 'insert into '.$arr['table_name'].' set '.$arr['fields'];
					break;
				case 'update':
					$qry = 'update '.$arr['table_name'].' set '.$arr['fields'].' '.$arr['where'];
					break;
				case 'delete':
					$qry = 'delete from '.$arr['table_name'].' '.$arr['where'];
					break;
				case 'drop':
					$qry = 'drop table '.$arr['table_name'];
					break;
				case 'query':
				case 'query_one':
					$qry = $arr['query'];
					break;
				default:
					$qry = '';
					break;
			}
		}
		else {
			$qry = $arr;
			if(stripos(trim($qry),'select ')===0) {
				$run_cache = true;
			}
		}

		$run_cache = false; // 라이브 캐시 오류 발생해서 캐시 안쓰도록 수정함. 캐시 수정후 적용하기.
		$cache = $run_cache ? $this->get_cache($qry) : false;
		if($cache) {
			$ret = $cache;
		} else {

			try {
				$t = microtime(true);
				// if($this->debug_mode) {
				// 	$result = mysqli_query($this->connect,$qry) or die($this->dbErr($qry,$err_file,$err_line));
				// }
				// else {
					$result = mysqli_query($this->connect,$qry);
				// }
				$et = microtime(true) - $t;
				
				// 쿼리 로그 저장.실행전 저장함. 오류 발생시 확인을 위함.
				if( 
					preg_match("/(insert |delete |update |replace |alter |create )/i", trim($qry)) &&
					strpos( $qry, 'SET NAMES ' )===false &&
					strpos( $qry, 'SET deposit_check_time=UNIX_TIMESTAMP()' )===false &&
					strpos( $qry, 'INSERT INTO js_member_device SET' )===false &&
					strpos( $qry, '_chart' )===false
				) {
					if(!$result) {
						$qry .= ', '.$this->dbErr($qry,$err_file,$err_line);
					}
					mysqli_query($this->connect, "insert into js_query_log values ( REPLACE(SUBSTR(NOW(),3,8), '-',''), UNIX_TIMESTAMP(), '".$this->escape($_SERVER['PHP_SELF'])."', '".$this->escape($qry)."', '".$this->escape($et)."' ) "); // write query log to db
				}

			} catch (Exception $e) {
				$qry.= ', Error : '.$e->errorMessage();
				mysqli_query($this->connect, "insert into js_query_log values ( REPLACE(SUBSTR(NOW(),3,8), '-',''), UNIX_TIMESTAMP(), '".$this->escape($_SERVER['PHP_SELF'])."', '".$this->escape($qry)."', '".$this->escape($et)."' ) "); // write query log to db
			}

			if(is_array($arr)) {
				if($arr['tool'] == 'select_affect') {
					$ret = array();
					$ret['result'] = $result;
					$ret['cnt'] = mysqli_affected_rows();
				}
				else if($arr['tool'] == 'row') {
					$ret = mysqli_fetch_assoc($result);
				}
				else if($arr['tool'] == 'select_one' || $arr['tool'] == 'count' || $arr['tool'] == 'query_one' ) {
					list($ret) = mysqli_fetch_row($result);
				}
				else if($arr['tool'] == 'insert_idx') {
					$ret = array();
					$ret['result'] = $result;
					$ret['idx'] = mysqli_insert_id($this->connect);
				}
				else {
					$ret = $result;
					// var_dump( mysqli_num_rows($result) );
				}
			}
			else {
				$ret = $result;
			}
			if($run_cache) {
				$this->set_cache($qry, $ret);
			}
		}
		return $ret;
	}

	function check_table_exists($table_name) {
		return !! $this->query_one("SHOW TABLES LIKE '{$this->escape($table_name)}'");
	}

	function isTable($table_name){
		return $this->check_table_exists($table_name);
	}

	function transaction($val=4)
	{
		if($val == 1) {
			$qry = 'set autocommit=0';
		}
		else if($val = 2) {
			$qry = 'commit';
		}
		else if($val = 3) {
			$qry = 'rollback';
		}
		else {
			$qry = 'set autocommit=1';
		}
		$ret = $this->query($qry);
		if($val == 2) {
			$this->query('set autocommit=1');
		}
		return $ret;
	}

	function dbErr($qry,$err_file='',$err_line='')
	{
		$arr = array();
		$arr[] = 'Query ErrorNo : '.mysqli_errno($this->connect);
		$arr[] = 'Query Error Message : '.mysqli_error($this->connect);
		if(!empty($qry)) { $arr[] = 'Query String : '.$qry; }
		$arr[] = 'Source Error File : '.basename($err_file);
		$arr[] = 'Source Error Line : '.$err_line;
		$arr[] = 'Error Source File : '.basename($_SERVER["PHP_SELF"]);
		$msg = implode('<br />',$arr);
		return $msg;
	}

	function close()
	{
		@mysqli_close($this->connect);
		unset($this->connect);
	}


	function query_unique_value($query,$err_file='',$err_line='') {
		if (is_array($query) ) {
			$q['table_name'] = $query['table_name'];
			$q['tool'] = 'select_one';
			$q['fields'] = $query['fields'];
			$q['where'] = $query['where'] . ' limit 1 ';
			$result= $this->query($q, $err_file, $err_line);
		} else {
			if(stripos($query, 'limit')===false) {$query .= ' limit 1 ';}
			$result = $this->query($query, $err_file, $err_line);
			$result = mysqli_fetch_array($result);
		}
		return $result[0];
	}
	function query_one($query,$err_file='',$err_line='') {
		return $this->query_unique_value($query,$err_file,$err_line);
	}

	function query_unique_object($query,$err_file='',$err_line='') {
		if (is_array($query) ) {
			$q['table_name'] = $query['table_name'];
			$q['tool'] = 'row'; // --- limit 1은 row 속성일때 자동으로 붙음.
			if(isset($query['fields'])) $q['fields'] = $query['fields'];
			$result= (object) $this->query($q, $err_file, $err_line);
		} else {
			if(stripos($query, ' limit ')===false) {$query .= ' limit 1 ';}
			$result = $this->query($query, $err_file, $err_line);
			$result = mysqli_fetch_object($result);
		}
		return $result;
	}
	function query_fetch_object($query,$err_file='',$err_line='') {
		return $this->query_unique_object($query,$err_file,$err_line);
	}

	function query_unique_array($query,$err_file='',$err_line='') {
		if (is_array($query) ) {
			$q['table_name'] = $query['table_name'];
			$q['tool'] = 'row'; // --- limit 1은 row 속성일때 자동으로 붙음.
			if(isset($query['fields'])) $q['fields'] = $query['fields'];
			$result= $this->query($q, $err_file, $err_line);
		} else {
			if(stripos($query, ' limit ')===false) {$query .= ' limit 1 ';}
			$result = $this->query($query, $err_file, $err_line);
			$result = mysqli_fetch_assoc($result);
		}
		return $result;
	}
	function query_fetch_array($query,$err_file='',$err_line='') {
		return $this->query_unique_array($query,$err_file,$err_line);
	}

	function query_all_array($query,$err_file='',$err_line='') {
		$_return = array();
		if (is_array($query) ) {
			$query['tool'] = 'select';
			$result= $this->query($query, $err_file, $err_line);
		} else {
			$result = $this->query($query, $err_file, $err_line);
		}
		while( $row = mysqli_fetch_assoc($result) ) {
			$_return[] = $row;
		}
		return $_return;
	}
	function query_list_array($query,$err_file='',$err_line='') {
		return $this->query_all_array($query,$err_file,$err_line);
	}
	/**
	 * 쿼리 결과를 특정 컬럼 값을 키로 사용하는 array값들의 배열을 리턴합니다. 
	 */
	function query_list_array_column($query, $column_name, $err_file='',$err_line='') {
		$r = array();
		$t = $this->query_all_array($query,$err_file,$err_line);
		foreach($t as $row) {
			$r[$row[$column_name]] = (array) $row;
		}
		return $r;
	}

	function query_all_object($query,$err_file='',$err_line='') {
		$_return = array();
		if (is_array($query) ) {
			$query['tool'] = 'select';
			$result= $this->query($query, $err_file, $err_line);
		} else {
			$result = $this->query($query, $err_file, $err_line);
		}
		while( $row = mysqli_fetch_object($result) ) {
			$_return[] = $row;
		}
		return $_return;
	}
	function query_list_object($query,$err_file='',$err_line='') {
		return $this->query_all_object($query,$err_file,$err_line);
	}
	/**
	 * 쿼리 결과를 특정 컬럼 값을 키로 사용하는 object값들의 배열을 리턴합니다. 
	 */
	function query_list_object_column($query, $column_name, $err_file='',$err_line='') {
		$r = array();
		$t = $this->query_all_array($query,$err_file,$err_line);
		foreach($t as $row) {
			$r[$row[$column_name]] = (object) $row;
		}
		return $r;
	}

	/**
	 * 쿼리를 실행하고 결과를 배열 속 Object로 리턴. 여러 row의 결과를 배열로 받을때 사용합니다.
	 */
	public function query_list_one($query, $column_cnt=0)
	{
		$return = array();
		$result = $this->query($query);
		while ($row = mysqli_fetch_array($result)) {
			if (!empty($row)) {
				$return[] = $row[$column_cnt];
			}
		}
		mysqli_free_result($result);
		return $return;
	}


	function getColumnNamebyTable($table_name,$err_file='',$err_line='')
	{
		$ret_value = $this->query(array('tool'=>'columnname','fields'=>'*','table_name'=>$table_name));
		return $ret_value;
	}

	function write_log($p_title='', $p_log='') {
		$micro = microtime();
        $micro = substr($micro, 2, 6);
        $time = date("YmdHis").$micro;
		$query = "insert into tmp_log valuse('{$this->escape($time)}','{$this->escape($p_title)}','{$this->escape($p_log)}')";
		@$this->query($query);
	}

	function escape($s) {
		if(is_array($s)) {
			foreach($s as $k => $v) {
				$s[$k] = mysqli_real_escape_string( $this->connect, $v);
			}
		} else {
			$s = mysqli_real_escape_string( $this->connect, $s);
		}
		return $s;
	}

	/**
	 * jqgrid 플러그인에서 사용하는 정렬과 검색 쿼리 생성용 메소드입니다.
	 * 쿼리 생성이라 SQL 인젝션 때문에 DB 클래스에 넣었습니다.
	 * @return Ojbect where 문의 일부와  order 문의 일부가 포함된 오브젝트를 리턴합니다. {'where':' searchField = "searchString" ', 'order' : ' sidx sord '}
	 */
	function get_sql_string_for_jqgrid() {
		$where = '';
		$searchField = $_GET['searchField'] ? $_GET['searchField'] : ''; // search field
		$searchString = $_GET['searchString'] ? $_GET['searchString'] : ''; // search string
		$searchOper = $_GET['searchOper'] ? $_GET['searchOper'] : ''; // search operator
		if($searchField && $searchOper) {
			if($searchOper=='in'||$searchOper=='ni') {
				$t = explode(',',$searchString);
				for($i=0; $i<count($t); $i++) {
					$t[$i] = $this->escape($t[$i]);
				}
				$searchString = "('".implode("','", $t)."')";
			}
			switch($searchOper) {
				case 'ni' : // is not in
					$where = " {$this->escape($searchField)} NOT IN {$searchString} "; break;
				case 'in' : // is in
					$where = " {$this->escape($searchField)} IN {$searchString} "; break; // 쿼리 확인하기
				case 'nu' : // is null
					$where = " {$this->escape($searchField)} IS NULL "; break;
				case 'nn' : // is not null
					$where = " {$this->escape($searchField)} IS NOT NULL "; break;
				case 'cn' : // contains
					$where = " {$this->escape($searchField)} LIKE '%{$this->escape($searchString)}%' "; break;
				case 'nc' : // does not contain
					$where = " {$this->escape($searchField)} NOT LIKE '%{$this->escape($searchString)}%' "; break;
				case 'ew' : // ends with
					$where = " {$this->escape($searchField)} LIKE '%{$this->escape($searchString)}' "; break;
				case 'en' : // does not end with
					$where = " {$this->escape($searchField)} NOT LIKE '%{$this->escape($searchString)}' "; break;
				case 'bw' : // begins with
					$where = " {$this->escape($searchField)} LIKE '{$this->escape($searchString)}%' "; break;
				case 'bn' : // does not begin with
					$where = " {$this->escape($searchField)} NOT LIKE '{$this->escape($searchString)}%' "; break;
				case 'gt' : // greater
					$where = " {$this->escape($searchField)} > '{$this->escape($searchString)}' "; break;
				case 'ge' : // greater or equal
					$where = " {$this->escape($searchField)} >= '{$this->escape($searchString)}' "; break;
				case 'lt' : // less
					$where = " {$this->escape($searchField)} < '{$this->escape($searchString)}' "; break;
				case 'le' : // less or equal
					$where = " {$this->escape($searchField)} <= '{$this->escape($searchString)}' "; break;
				case 'ne' : // not equal
					$where = " {$this->escape($searchField)} <> '{$this->escape($searchString)}' "; break;
				case 'eq' : // equal
				default :
					$where = " {$this->escape($searchField)} = '{$this->escape($searchString)}' ";
			}
			$where = " AND ".$where;
		}
		$order = '';
		$sidx = $_GET['sidx'] ? $_GET['sidx'] : ''; // sort idx
		if($sidx) {
			$sord = strtoupper($_GET['sord'])=='DESC' ? 'DESC' : 'ASC'; // sort order
			$order = " {$sidx} {$sord} ";
		}
		return (object) array('where'=>$where, 'order'=>$order);
	}

	/**
	 * 캐시 아이디 생성.
	 */
	function gen_cache_id($id) {
		return md5(serialize($id));
	}

	/**
	 * 메모리 캐시 추출( 중복 쿼리 방지용 )
	 */
	function get_cache($id)
	{
		$r = $this->cache[$this->gen_cache_id($id)];
		$r = $r ? unserialize(gzuncompress($r)) : array('time'=>0);
		if( $r['time'] >= time()) {
			$r = $r['contents'];
		} else {
			$r = '';
		}
		return $r;
	}

	/**
	 * 메모리 캐시 저장( 중복 쿼리 방지용 )
	 */
	function set_cache($id, $contents, $sec=2)
	{
		$this->cache[$this->gen_cache_id($id)] = gzcompress(serialize(array('time'=>time()+$sec, 'contents'=>$contents)));
		return $contents;
	}
	
	/**
	 * 쿼리 결과 NULL 은 빈 문자열로 바꿉니다.
	 * @param String|NULL|Array|Object $a 변경전 값.
	 * @return Mixed $a 변경된 값.
	 */
	function null_to_emtpy ($a) {
		if(is_array($a) || is_object($a)) {
			if(is_object($a)) {
				$a_type= 'object';
				$a = (array) $a;
			}
			foreach($a as $i => $v) {
				$a[$i] = $this->null_to_emtpy($v);
			}
			if($a_type=='object') {
				$a = (object) $a;
			}
		} else {
			$a = $a || $a===0 ? $a : '';
		}
		return $a;
	}
}
?>