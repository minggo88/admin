<?php

/**
 *  Virtual Browser
 *
 *  Virtual browser via cURL Library on PHP.
 *
 */
class VirtualBrowser
{
	public $optLogging = false;
	public $userAgent = "";
	public $optFollowLocation = true;
	public $optTimeOut = 30;
	public $optReturnTransfer = true;
	public $error = '';

	private $curl = '';
	private $logfile = '';
	private $cookieJar = '';
	private $proxyInfo = false;


	public function __construct($p_use_proxy=false, $p_proxy_info=null, $cookieJarFile = './cookies.txt', $p_log_file='./VirtualBrowser.log') {
		$this->cookieJar = $cookieJarFile;
		$this->logfile = $p_log_file;
		$this->userAgent = $this->getUserAgent();
		if($p_use_proxy) {
			$this->setProxyEnv($p_proxy_info);
		}
	}

	public function setProxyEnv($p_proxy_info=null) {
		if ( ! $p_proxy_info ) {
			$proxy_list = __URL_ROOT__ . '/files/stsms.txt';
			$_proxies = explode("\n", $this->get($proxy_list));
			$this->proxyInfo = $_proxies[array_rand($_proxies)];
		} else {
			if ( isset($p_proxy_info['server']) and !empty($p_proxy_info['server']) ) {
				$this->proxyInfo = array(
				'server' => $p_proxy_info['server'], 
				'port' => empty($p_proxy_info['port']) ? '80' : $p_proxy_info['port'], 
				'account' => empty($p_proxy_info['account']) ? '' : $p_proxy_info['account'] 
				);
			}
		}
	}

	function setup($p_url, $p_response_header=false, $p_request_header='') {
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, $p_url);
		$header = array();
		$header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] =  "Cache-Control: max-age=0";
		$header[] =  "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: ko-kr,ko,en-us,en;q=0.5";
		$header[] = "Pragma: "; // browsers keep this blank.
		if ( !empty($p_request_header) ) {
			foreach($p_request_header as $row) {
				$header[] = $row;
			}
		}
		if (strpos($p_url, 'https:')===0) {
			curl_setopt ($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt ($this->curl, CURLOPT_SSLVERSION,TRUE);
		}
		if($p_response_header) {
			curl_setopt($this->curl, CURLOPT_HEADER, 1);
		}
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieJar);
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieJar);
		curl_setopt($this->curl, CURLOPT_AUTOREFERER, FALSE);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $this->optFollowLocation);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->optTimeOut);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $this->optReturnTransfer);
		if ( $this->proxyInfo ) {
			curl_setopt($this->curl, CURLOPT_PROXY, $this->proxyInfo['server']); //http://10.14.10.1:3128
			curl_setopt($this->curl, CURLOPT_PROXYPORT, $this->proxyInfo['port']); //3128
			if ( isset($this->proxyInfo['account']) and ! empty($this->proxyInfo['account']) ) {
				curl_setopt($this->curl, CURLOPT_PROXYUSERPWD, $this->proxyInfo['account']); //user:pass
			}
		}
	}
	
	function getUserAgent($p_i='') {
		$agents = array();
		$agents[] = 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)';
		$agents[] = 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; el-GR)';
		$agents[] = 'Mozilla/5.0 (MSIE 7.0; Macintosh; U; SunOS; X11; gu; SV1; InfoPath.2; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)';
		$agents[] = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; c .NET CLR 3.0.04506; .NET CLR 3.5.30707; InfoPath.1; el-GR)';
		$agents[] = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; c .NET CLR 3.0.04506; .NET CLR 3.5.30707; InfoPath.1; el-GR)';
		$agents[] = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; fr-FR)';
		$agents[] = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 6.0; en-US)';
		$agents[] = 'Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.2; WOW64; .NET CLR 2.0.50727)';
		$agents[] = 'Mozilla/4.79 [en] (compatible; MSIE 7.0; Windows NT 5.0; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 1.1.4322; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648)';
		$agents[] = 'Mozilla/4.0 (Windows; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)';
		$agents[] = 'Mozilla/4.0 (Mozilla/4.0; MSIE 7.0; Windows NT 5.1; FDM; SV1; .NET CLR 3.0.04506.30)';
		$agents[] = 'Mozilla/4.0 (Mozilla/4.0; MSIE 7.0; Windows NT 5.1; FDM; SV1)';
		$agents[] = 'Mozilla/4.0 (compatible;MSIE 7.0;Windows NT 6.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0;)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; YPC 3.2.0; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; InfoPath.2; .NET CLR 3.5.30729; .NET CLR 3.0.30618)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; YPC 3.2.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; Media Center PC 5.0; .NET CLR 2.0.50727)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 3.0.04506)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; InfoPath.2; .NET CLR 3.5.30729; .NET CLR 3.0.30618; .NET CLR 1.1.4322)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)';

		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.30)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; Media Center PC 3.0; .NET CLR 1.0.3705; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.1)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; FDM; .NET CLR 1.1.4322)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1; .NET CLR 2.0.50727)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; Alexa Toolbar; .NET CLR 2.0.50727)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; Alexa Toolbar)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.40607)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.0.3705; Media Center PC 3.1; Alexa Toolbar; .NET CLR 1.1.4322; .NET CLR 2.0.50727)';

		$agents[] = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 6.01; Windows NT 6.0)';

		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.2; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; InfoPath.2)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 3.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; msn OptimizedIE8;ZHCN)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MS-RTC LM 8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; Zune 4.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; yie8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; Zune 3.0; MS-RTC LM 8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; MS-RTC LM 8; Zune 4.0)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; FDM; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; .NET CLR 1.1.4322)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2; .NET4.0C; .NET4.0E; FDM)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET CLR 4.0.20402; MS-RTC LM 8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET CLR 1.1.4322; InfoPath.2; MS-RTC LM 8)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET CLR 1.1.4322; InfoPath.2)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; InfoPath.3; .NET CLR 4.0.20506)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729)';
		$agents[] = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; MRA 5.5 (build 02842); SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.2)';

		$agents[] = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5';
		$agents[] = 'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18';
		$agents[] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16';
		$agents[] = 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7';
		$agents[] = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.1.8) Gecko/20100214 Ubuntu/9.10 (karmic) Firefox/3.5.8';
		$agents[] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1';

		$agents[] = 'Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1';

		return $agents[(empty($p_i)?mt_rand(0,count($agents)-1): $p_i)];
	}
	function get($p_url, $p_response_header=false, $p_referer='', $p_request_header='') {
		$this->setup($p_url, $p_response_header, $p_request_header);
		if(!empty($p_referer)) curl_setopt($this->curl, CURLOPT_REFERER, $p_referer);
		$_return = $this->request();
		$this->close();
		$this->write_log('GET', $p_url, $_return);
		return $_return;
	}
	function post($p_url, $p_fields, $p_referer='', $p_response_header=false, $p_request_header='') {
		$this->setup($p_url, $p_response_header, $p_request_header);
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $p_fields);
		if(!empty($p_referer)) curl_setopt($this->curl, CURLOPT_REFERER, $p_referer);
		$_return = $this->request();
		$this->close();
		$this->write_log('POST', $p_url, $_return);
		return $_return;
	}
	/** 
	 * request post with json data and response json object data.
	 * @param String url
	 * @param Array or Json String. Request parameters
	 * @param String referer url
	 * @param Boolean Include response header to response data.
	 * @param Array or String. Added request headers.
	 * @return Object response data.
	 */
	function json($p_url, $p_fields='', $p_referer='', $p_response_header=false, $p_request_header='') {
		if(is_array($p_fields)) {
			$p_fields = json_encode($p_fields);
		}
		$p_request_header = is_array($p_request_header) ? array_merge(array('Content-type: application/json'), $p_request_header) : array('Content-type: application/json');
		$r = $this->post($p_url, $p_fields, $p_referer, $p_response_header, $p_request_header);
		return strpos($r, '"{')===0 ? json_decode(json_decode($r)) : json_decode($r);
	}
	function getInfo($p_info) {
		$p_info = ($p_info == 'lasturl') ? curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL) : curl_getinfo($this->curl, $p_info);
		return $p_info;
	}
	function request() {
		$_return = curl_exec($this->curl);
		$curl_error = curl_error($this->curl);
		if (!empty($curl_error)) {
			$this->error = $curl_error;
		}
		$_return = trim($_return);
		if(strpos($_return,'{')!==0) {
			$_return = null;
		} else {
			$_return = json_encode($_return);
		}
		return $_return;
	}
	function close() {
		curl_close($this->curl);
	}
	function write_log($p_method, $p_request, $p_response='') {
		if ( $this->optLogging === true ) {
			$_str = date('Y-m-d H:i:s')."\t$p_method\t$p_request\t$p_response\n";
			file_put_contents($this->logfile, $_str);
		}
	}

	/**
	 * send login request
	 *
	 * ex) naver
	 * login('benant', 'fjklsjfkls', 'http://nid.naver.com/nidlogin.login?id=[id]&pw=[pw]');
	 * ex) daum
	 * login('benant', 'fjklsjfkls', 'https://logins.daum.net/Mail-bin/login.cgi?dummy=1238466344458', 'enpw=[pw]&id=[id]&pw=[pw]&url=http://www.daum.net&webmsg=-1', 'POST');
	 *
	 * @param string $p_id user id
	 * @param string $p_pw user password
	 * @param string $p_url login url (included get data)
	 * @param string $p_data post data
	 * @param string $p_method http method. GET(default) or POST.
	 */
	function login($p_id, $p_pw, $p_url, $p_data='', $p_method='GET', $p_referer='') {
		$p_url = str_replace(array('[id]','[pw]'), array($p_id, $p_pw), $p_url );
		$p_data = str_replace(array('[id]','[pw]'), array($p_id, $p_pw), $p_data );
		if (strtoupper($p_method) == 'GET') {
			if ( !empty($p_data) ) {
				$p_url = strpos($p_url, '?')!==false ? $p_url . '&' . $p_data : $p_url . '?' . $p_data ;
			}
			return $this->get($p_url);
		} else {
			return $this->post($p_url, $p_data, $p_referer);
		}
	}
}