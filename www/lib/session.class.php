<?php

/**
 * session - db
 */
// CREATE TABLE `sessions` (
//   `id` varchar(32) CHARACTER SET latin1 NOT NULL,
//   `access` int(10) unsigned DEFAULT NULL,
//   `data` text CHARACTER SET latin1,
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8

class SessionDb {

	/**
	 * DB 접속 정보
	 */
	private $db_info = array();

	private $_db_link = null;

	public function __construct($start=true) {

		switch(__API_RUNMODE__) {
			case 'loc' :
				$this->db_info = array(
					'host' => 'loc.master',
					'username' => 'kkikda',
					'password' => 'k$d^39@34',
					'charset' => 'utf8',
					'database' => 'kkikda2'
				);
			break;
			case 'dev' :
				$this->db_info = array(
					'host' => 'localhost',
					'username' => 'kkikda',
					'password' => 'k$d^39@34',
					'charset' => 'utf8',
					'database' => 'kkikda2'
				);
			break;
			case 'live' :
				$this->db_info = array(
					'host' => 'kkikda.catyypkt8dey.ap-northeast-2.rds.amazonaws.com',
					'username' => 'kkikda',
					'password' => 'KKe8IuK28Due82A',
					'charset' => 'utf8',
					'database' => 'kkikda'
				);
			break;
		}

		if($start) {
			$this->_db_connect();
		}
		session_name('token');
		// Set handler to overide SESSION
		session_set_save_handler(
			array($this, "_session_open"),
			array($this, "_session_close"),
			array($this, "_session_read"),
			array($this, "_session_write"),
			array($this, "_session_destroy"),
			array($this, "_session_gc")
		);
		// ini_set("session.save_path", dirname(__file__) . "/../session");
		// ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']); // api 서버 연동위해
		ini_set("session.cookie_lifetime", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.cache_expire", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.gc_maxlifetime", "3600"); //기본 세션타임 1시간으로 설정 합니다.
		session_cache_limiter('private');
		if($start) {
			session_start();
		}
	}

	public function __destruct() {
		session_write_close();
	}


	/**
	 * Session Open
	 */
	public function _session_open(){
		if(is_object($this->_db_link)){
			return true;
		}
		return false;
	}

	/**
	* Session Close
	*/
	public function _session_close(){
		$this->_db_close();
		return true;
	}

	/**
	 * Session Read
	 */
	public function _session_read($id){
		$row = $this->query_fetch_object('SELECT data from js_wallet_sessions WHERE id = "'.$this->escape($id).'"');
		return isset($row->data) ? $row->data : '';
	}

	/**
	* Session Write
	*/
	public function _session_write($id, $data){
		$access = time();
		$r = $this->query('REPLACE INTO js_wallet_sessions VALUES ("'.$this->escape($id).'", "'.$this->escape($access).'", "'.$this->escape($data).'")');
		return $r;
	}

	/**
	* Session Destroy
	*/
	public function _session_destroy($id){
		$r = $this->query('DELETE from js_wallet_sessions WHERE id = "'.$this->escape($id).'"');
		return $r;
	}

	/**
	 * Session Garbage Collection
	 */
	public function _session_gc($max){
		// Calculate what is to be deemed old
		$old = time() - $max;
		$r = $this->query("DELETE from js_wallet_sessions WHERE access < $old");
		return $r;
	}


	/**
	 * DB 연결
	 */
	private function _db_connect()
	{
		if (!is_object($this->_db_link)) {
			$this->_db_link = mysqli_connect($this->db_info['host'], $this->db_info['username'], $this->db_info['password'], $this->db_info['database']);
			if ($this->_db_link->connect_errno) {
				$this->error("001", "Mysql Connection Error: Failed connecting to database server\r\n\r\n" . mysqli_error($this->_db_link) . "");
			}
			if (!empty($this->db_info['charset'])) {
				$this->query('SET NAMES "' . $this->escape($this->db_info['charset']) . '"');
				mysqli_set_charset($this->_db_link, $this->db_info['charset']);
			}
            // MySQL 8 에서 날짜 형식 오류 제외하기
            // SELECT VERSION();
            mysqli_query($this->_db_link,"SET @@SESSION.sql_mode = CONCAT_WS(',', @@SESSION.sql_mode, 'ALLOW_INVALID_DATES')");
		}
	}

	/**
	 * DB 연결 종료
	 */
	private function _db_close()
	{
		if ($this->_db_link) {mysqli_close($this->_db_link);}
	}

	/**
	 * db query function
	 */
	public function query($query)
	{
		$result = mysqli_query($this->_db_link, $query);
		if (!$result) {
			$this->error("005", "Mysql Query Error: Failed executing database query<br />\r\nDate/Time: " . date('Y-m-d H:i:s') . "<br />\r\nQuery: $query<br />\r\nMySQL Error: " . mysqli_error($this->_db_link) . "");
		}
		if (preg_match("/^\\s*(insert|delete|update|replace|alter) /i", $query)) {
			$this->_recently_query['rows_affected'] = mysqli_affected_rows($this->_db_link);
			if (preg_match("/^\\s*(insert|replace) /i", $query)) {
				$this->_recently_query['last_insert_id'] = mysqli_insert_id($this->_db_link);
			}
		} else {
			$this->_recently_query['num_fields'] = @mysqli_num_fields($result);
			$this->_recently_query['num_rows'] = @mysqli_num_rows($result);
		}
		return $result;
	}

	/**
	 * 커리 결과를 object로 받기.
	 */
	public function _fetch_object($result)
	{
		return mysqli_fetch_object($result);
	}

	/**
	 * 쿼리를 실행하고 결과를 Object로 리턴. 단일 row 의 결과를 받을때 사용합니다.
	 */
	public function query_fetch_object($query)
	{
		$result = $this->query($query);
		$return = $this->_fetch_object($result);
		$this->_db_free_result($result);
		return $return;
	}

	/**
	 * 이스케이프 문자열로 변경. 쿼리에 사용되는 값에 적용하여 SQL Injection에 대비합니다.
	 */
	public function escape($v)
	{
		return mysqli_real_escape_string($this->_db_link, $v);
	}

	/**
	 * DB 쿼리 결과 초기화
	 */
	public function _db_free_result($result)
	{
		if ($result) {mysqli_free_result($result);}
	}

	function error($code, $msg='') {
		//file_put_contents(dirname(__file__).'/session.class.log.php', '['.$code."] $msg\n", FILE_APPEND);
	}

}
