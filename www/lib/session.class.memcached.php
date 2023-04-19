<?php

/**
 * session - memcached
 */

class SessionMemCached {

	/**
	 * DB 접속 정보
	 */
	private $memcache_info = array();

	private $cache_obj = null;

	/**
	 * session time
	 */
	private $_session_time = 3600; // 1 hr.

	public function __construct($start=true) {

		switch(__API_RUNMODE__) {
			case 'loc' :
				$this->memcache_info = array(
					'host' => '127.0.0.1',
					'port' => '11211',
				);
			break;
            case 'dev' :
				$this->memcache_info = array(
					'host' => 'kkikdacache-dev.a12ygy.cfg.apn2.cache.amazonaws.com',
					'port' => '11211',
				);
			break;
			case 'live' :
				$this->memcache_info = array(
					'host' => 'kkikdacache.a12ygy.cfg.apn2.cache.amazonaws.com',
					'port' => '11211',
				);
			break;
		}
		if(isset($this->memcache_info['host']) && $this->memcache_info['host'] && !$this->cache_obj) {
			$this->cache_obj = new Memcached();
			$this->cache_obj->addServer($this->memcache_info['host'], $this->memcache_info['port']);
		}

		session_name('token');
		// Set handler to overide SESSION
		// var_dump(is_object($this->cache_obj)); exit;
		if(is_object($this->cache_obj)) {
			session_set_save_handler(
				array($this, "_session_open"),
				array($this, "_session_close"),
				array($this, "_session_read"),
				array($this, "_session_write"),
				array($this, "_session_destroy"),
				array($this, "_session_gc")
			);
		} else {
			$dir = __DIR__."/../../_session";
			if(!file_exists($dir)) { mkdir($dir, 0777, true);}
			ini_set("session.save_path", $dir);
			// ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']); // api 서버 연동위해
		}

		$domain = $_SERVER['HTTP_HOST'];
		if(strpos($domain, 'api.')!==false) $domain = str_replace('api.', '', $domain); // 서비스 도메인과 맞추기위해 api. 을 삭제합니다.
		if(strpos($domain, 'www.')!==false) $domain = str_replace('www.', '', $domain); // 서비스 도메인과 맞추기위해 api. 을 삭제합니다.
		if(strpos($domain, 'trade.')!==false) $domain = str_replace('trade.', '', $domain); // 서비스 도메인과 맞추기위해 api. 을 삭제합니다.
		if(strpos($domain, 'auction.')!==false) $domain = str_replace('auction.', '', $domain); // 서비스 도메인과 맞추기위해 api. 을 삭제합니다.
		$domain = '.'.$domain;
		ini_set('session.cookie_domain', $domain); // api 서버와 동일한 도메인을 사용해야합니다. 기본값은 서비스 도메인(www.cexstock.com)입니다.
		// session_set_cookie_params(0, '/', $domain, false, false);
		ini_set("session.cookie_lifetime", $this->_session_time); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.cache_expire", $this->_session_time); //기본 세션타임 1시간으로 설정 합니다.
		ini_set("session.gc_maxlifetime", $this->_session_time); //기본 세션타임 1시간으로 설정 합니다.
		session_cache_limiter('private');
		if($start) {
			// $this->token = $_REQUEST['token'];
			session_id($_COOKIE['token']); // session_start 전에 전달 받은 값을 session_id로 사용합니다.
			session_start(); // je4rogd4eq5qq15t1smbia4qk9
			// var_dump($domain, $_SESSION, $_COOKIE); exit;
		}
	}

	public function __destruct() {
		session_write_close();
	}


	/**
	 * Session Open
	 */
	public function _session_open(){
		if($this->cache_obj) {
			return true;
		} else {
			return false;
		}
	}

	/**
	* Session Close
	*/
	public function _session_close(){
		$this->cache_obj->quit();
		return true;
	}

	/**
	 * session id 생성
	 * memcached를 공통으로 사용할 경우 중복키가 발생할 수 있어서 키에 호스트이름과 세션 구분자를 넣어 중복을 피합니다.
	 */
	private function _gen_session_id($id){
		return sha1(ini_get('session.cookie_domain').'/session/'.$id);
	}

	/**
	 * Session Read
	 */
	public function _session_read($id){
		$r = $this->cache_obj->get($this->_gen_session_id($id));
		$r = $r ? unserialize(gzuncompress($r)) : '';
		if(!$r) {
				$this->_session_destroy($id);
		}
		return $r;
	}

	/**
	* Session Write
	*/
	public function _session_write($id, $data){
		$v = gzcompress(serialize($data));
		$r = $this->cache_obj->set($this->_gen_session_id($id), $v, time() + $this->_session_time);
		return $r;
	}

	/**
	* Session Destroy
	*/
	public function _session_destroy($id){
		$r = $this->cache_obj->delete($this->_gen_session_id($id));
		return $r;
	}

	/**
	 * Session Garbage Collection
	 */
	public function _session_gc($max){
		return true;
	}

}
