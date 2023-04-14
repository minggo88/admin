<?php
require dirname(__file__) . '/../JsonRPC.php';
require dirname(__file__) . '/../simple_html_dom.php';
require dirname(__file__) . '/../vendor/autoload.php';

use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

/**
 * Tether 토큰(USDT) 용 전자지갑 클래스 - ERC20 Token 과 연동하는 클래스입니다.
 */
class USDTCoind
{
	private $jsonrpc = null;
	private $contractAddress = null;
	private $apiurl = null;
	private $ethersacnurl = null;
	private $etherscankey = null;
	private $abi = null;
	private $opcode = null;
	private $web3 = null; // ethereum web3js
	private $decimals = 6; // coin 소숫점 자릿수. 1 XSCC = 1.0E18 ;

	public function __construct($runtype = 'dev')
	{
		switch ($runtype) {
			case 'loc':
			case 'dev':
				$_host = '54.180.139.236';
				$_port = '6545';
				$_user = '';
				$_pw = '';
				$_protocol = 'http';
				$_access_time = 5;
				$this->contractAddress = ''; // 0x4010e46C6c2E2A9BDE98ffd5073d675eF4c659Fb
				$this->apiurl = 'https://api-ropsten.etherscan.io';
				$this->ethersacnurl = 'https://ropsten.etherscan.io';
				$this->etherscankey = 'D3M38Y1HTW9KHFCIRNJNIXBVVBSHWD1E8T';
				$this->abi = file_exists(dirname(__file__) . '/abi-dev') ? file_get_contents(dirname(__file__) . '/abi-dev') : '';
				$this->opcode = file_exists(dirname(__file__) . '/bytecode-dev') ? file_get_contents(dirname(__file__) . '/bytecode-dev') : '';
				// faucet : https://faucet.ropsten.be/
				break;
			case 'live':
				$_host = '13.124.107.186';
				$_port = '6545';
				$_user = '';
				$_pw = '';
				$_protocol = 'http';
				$_access_time = 5;
				$this->contractAddress = ''; // 2018.7.10 새로 추가
				$this->apiurl = 'https://api.etherscan.io';
				$this->ethersacnurl = 'https://etherscan.io';
				$this->etherscankey = 'D3M38Y1HTW9KHFCIRNJNIXBVVBSHWD1E8T';
				$this->abi = file_get_contents(dirname(__file__) . '/abi-live');
				$this->opcode = file_get_contents(dirname(__file__) . '/bytecode-live');
				break;
		}
		$this->jsonrpc = new JsonRPC($_user, $_pw, $_host, $_port, $_protocol, null);
		$this->web3 = new Web3(new HttpProvider(new HttpRequestManager($_protocol . '://' . $_host . ':' . $_port, $_access_time, false)));
	}

	public function __destruct()
	{
	}

	public function getError()
	{
		return $this->jsonrpc->error;
	}

	/**
	 * Address의 잔액 구하기.
	 * @param Address 주소.
	 * @return Object 잔액 정보. account: 지갑 Account아이디, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
	 */
	public function getBalanaceAddress($address, $account)
	{
		$pending = 0;
		// $address = '0x2a50887cf8610e19153522551dcd1f27d3f7123f'; // testing address 101 XSCC

		// token을 사용하기때문에 token balance를 가져와야 함.
		$url = "https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress={$this->contractAddress}&address={$address}&tag=latest&apikey={$this->etherscankey}";
		$json = $this->jsonrpc->remote_get($url);
		$json = json_decode($json);
		if ($json) {
			$balance = $json->result;
			$balance = $balance / pow(10, $this->decimals);
		}
		// web parsing
		/*/
		$url = $this->ethersacnurl . "/token/{$this->contractAddress}?a={$address}";
		$html = $this->jsonrpc->remote_get($url);
		foreach($DOM->find('div') as $element1){
			if($element1->id =='ContentPlaceHolder1_divFilteredHolderBalance'){
				$element1->plaintext;
				break;
			}
		}
		//$dom = str_get_html($html);
		//$balance_html = $dom->find('#ContentPlaceHolder1_divFilteredHolderBalance', 0)->plaintext;
		preg_match('/([0-9.,]{0,35}) XSCC/', $balance_html, $matches);
		$scc = is_array($matches) && count($matches) > 1 ? str_replace(',', '', $matches[1]) * 1 : 0;
		*/
		$_result = (object) array("account" => $account, 'confirmed' => $balance, 'unconfirmed' => $pending);
		return $_result;
	}

	/**
	 * Address의 잔액 구하기.
	 * @param Address 주소.
	 * @return Object 잔액 정보. account: 지갑 Account아이디, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
	 */
	public function getEthBalanaceAddress($address)
	{
		// $address = '0x2a50887cf8610e19153522551dcd1f27d3f7123f'; // testing address 101 XSCC
		// token을 사용하기때문에 token balance를 가져와야 함.
		// https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=0x57d90b64a1a57749b0f932f1a3395792e12e7055&address=0xe04f27eb70e025b78871a2ad7eabe85e61212761&tag=latest&apikey=YourApiKeyToken
		// $address = '0xdda3f0407701925c413c719bf1fdde505e77960f';
		// $url = "https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress={$this->contractAddress}&address={$address}&tag=latest&apikey={$this->etherscankey}";
		// $_result = $this->jsonrpc->remote_get($url);
		// var_dump($url, $_result); exit;
		// api는 작동하지만 실제 scc 값은 0으로 나옮

		// web parsing
		$url = $this->apiurl . "/api?module=account&action=balance&address={$address}&tag=latest&apikey=" . $this->etherscankey;
		$json = $this->jsonrpc->remote_get($url);
		$json = json_decode($json);
		if ($json) {
			$balance = $json->result;
			$balance = $balance / pow(10, $this->decimals);
		} else {
			// web3
			$decimals = $this->decimals;
			$r = $this->web3->eth->getBalance($address, function ($err, $balance) use ($decimals) {
			});
			if ($r->err) {
				$this->jsonrpc->error = $r->$err->getMessage();
				return false;
			}
			$balance = $this->web3->utils->toBN($r->data);
			$balance = $balance->value;
			$d = pow(10, $this->decimals);
			$balance = $balance / $d;
		}
		return (object) array("address" => $address, 'confirmed' => $balance);
	}

	/**
	 * 토큰 전체 잔액 구하기. - 발행량.
	 * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
	 */
	public function getBalanaceTotal()
	{
		// web parsing
		$_result = (object) array('total' => 0, 'confirmed' => 0);
		$url = $this->ethersacnurl . "/token/{$this->contractAddress}";
		$html = $this->jsonrpc->remote_get($url);
		$dom = str_get_html($html);
		$balance_html = $dom->find('#ContentPlaceHolder1_divSummary', 0)->plaintext;
		// Total Supply:  10,000,000,000  XSCC
		$r = preg_match('/Total Supply:(\s{0,})([0-9.,]{0,35})(\s{0,})XSCC/', $balance_html, $matches);
		if ($r) {
			$_result->total = str_replace(',', '', $matches[2]);
			$_result->confirmed = $_result->total;
		}
		return $_result;
	}

	/**
	 * 새 주소 생성.
	 * @param String account id. not use this.
	 * @param String address secret key.
	 * @return Address 새 주소.
	 */
	public function genNewAddress($account, $pwd='')
	{
		return $this->jsonrpc->personal_newAccount(array($pwd));
	}

	/**
	 * 전체 주소 구하기.
	 * @return Array address 배열.
	 *     "result": ["0xe104fc2fa6e84b37ab2e88a49f99f49aa424f6b5","0xde7b2e63af783a4105c8a4b6c57a9e033486df1b","0xe0b5542123141c7e3ab5dd350a0404099f2eb407"]
	 * @todo 전체 주소를 한번에 다 리턴하기 때문에 주소가 많은경우 부하가 걸릴 수 있음. 사용하지 말고 DB에 address를 저장하고 저장된 address를 사용하도록 하세요.
	 */
	public function getListAddress()
	{
		return $this->jsonrpc->eth_accounts();
	}

	/**
	 * 거래내역 상세정보 구하기.
	 * 이더리움은 거래상세정보를 영수증 요청으로 확인 할 수 있다. 자세한건 아래 문서 확인하세요.
	 * https://web3js.readthedocs.io/en/1.0/web3-eth.html?#gettransactionreceipt
	 *
	 * @param String 트랜젝션 아이디.
	 * @return Object 트랜젝션 정보.
	 */
	public function getTransaction($txid, $address)
	{
		$_result = (object) array(
			'txnid' => $txid,
			'status' => '',
			'amount' => '',
			'symbol' => '',
			'from_address' => '',
			'to_address' => '',
			'time' => '',
			'cnt_confirm' => '',
			'fee' => ''
		);
		// web3 - 작동 않됨.
		// $r = $this->web3->eth->getTransactionReceipt($txid, function ($err, $transaction) {});
		// $r = $r->data ? $r->data : false;
		// ethersacn api - 이더스캔에서 가져오기.
		// $url ='https://api.etherscan.io/api?module=proxy&action=eth_getTransactionReceipt&txhash=0x1e2910a262b1008d0616a0beb24c1a491d78771baa54a33e66065e03b1f46bc1&apikey=YourApiKeyToken';
		$url = $this->apiurl . "/api?module=proxy&action=eth_getTransactionReceipt&txhash={$txid}&apikey={$this->etherscankey}";
		$r = json_decode($this->jsonrpc->remote_get($url));
		$r = $r->result ? $r->result : false;
		// https://api-ropsten.etherscan.io/api?module=proxy&action=eth_getTransactionReceipt&txhash=0x1d507860ea496110e51800afc12d25676fd78dbd1cd2dade7ef2455fc89a597a&apikey=D3M38Y1HTW9KHFCIRNJNIXBVVBSHWD1E8T
		// var_dump($r); //exit;
		if ($r) {
			if ($r->status == '0x1') {
				$_result->status = 'S';
			}
			$_result->from_address = $r->from;
			$_result->contract_address = $r->to; // to 정보가 틀림.
			// $_result->to_address = $r->to; // to 정보가 틀림.
			$_result->fee = $this->web3->utils->toBn($r->gasUsed)->value;
		} else {
			$_result->status = 'P';
		}
		return $_result;
	}

	/**
	 * address의 트랜젝션 목록 구하기.
	 * https://ropsten.etherscan.io/apis#accounts
	 * http://api-ropsten.etherscan.io/api?module=account&action=tokentx&contractaddress={$this->contractAddress}&contractaddress={$this->contractAddress}&address=0x2039f68b978565629364af8a02e0439f1373c214&page=1&offset=2&sort=asc&apikey=YourApiKeyToken
	 * "Get a list of "ERC20 - Token Transfer Events" by Address" 부분 참고
	 *
	 * @param Address $address 주소.
	 * @param Account $account 계정(미사용)
	 * @param Number $count 갯수
	 * @param Number $from 시작페이지 기본 1
	 * @return Array 트랜젝션 목록.
	 */
	public function getListTransactionAddress($address, $account = '', $count = 100, $from = 0)
	{
		$t = array();
		try {
			// api
			$url = $this->apiurl . "/api?module=account&action=tokentx&contractaddress={$this->contractAddress}&contractaddress={$this->contractAddress}&address={$address}&page={$from}&offset={$count}&sort=desc&apikey={$this->etherscankey}";
			$json = trim($this->jsonrpc->remote_get($url));
			// var_dump($json); exit;
			// {"status":"1","message":"OK","result":[{"blockNumber":"7404908","timeStamp":"1582699258","hash":"0x4598ef5a489035eea4cb7c7e587fe442d17e6e8828a027153637345fa6c4978e","nonce":"29","blockHash":"0x79fc7673971b21e523e98cbc7569557e9cd7a02bfcce773a767887dde7547e8f","from":"0x2039f68b978565629364af8a02e0439f1373c214","contractAddress":"0x5444044eac4898d73c57b7dbfd0e663eb2df4785","to":"0x7e348164fe95669ed711f30bc8e0ae70b1567008","value":"10000000000000000000000000","tokenName":"TRVCoin","tokenSymbol":"TRV","tokenDecimal":"18","transactionIndex":"2","gas":"76801","gasPrice":"10000000000","gasUsed":"51201","cumulativeGasUsed":"2393633","input":"deprecated","confirmations":"157"}]
			// 거래내역 없음. 또는 $count 만큼 가져 왔다면 종료.
			if (strpos($json, '{') == 0) { // json 형식 아니면 종료.
				$json = json_decode($json);
				if ($json->status == '1' && count($json->result) > 0) {
					// 거래내역 가져오기
					foreach ($json->result as $tr) {
						if (count($t) >= $count) {
							break;
						}
						$r = array();
						$r['txnid'] = $tr->hash; // txnid
						$r['time'] = $tr->timeStamp; // txn time. timestamp 값 사용
						$r['direction'] = strtolower($tr->from) == strtolower($address) ? 'out' : 'in'; // direction
						$r['from_address'] = $tr->from; // from address
						$r['to_address'] = $tr->to; // to address
						$r['confirmations'] = $tr->confirmations; //'10'; // txn 리스트에 나온다면 승인 완료된 것임. 승인 완료되고 잠시후 목록에 나타남.
						$r['address'] = $address; // 조회 주소
						$r['amount'] = $tr->value/pow(10, $this->decimals); // 전송금액 토큰 기준
						$r['fee'] = $this->wei2eth($tr->gasUsed * $tr->gasPrice); // 전송수수료 eth 기준
						$t[] = $r;
					}
				}
			}
		} catch (Exception $e) {
		}
		return $t;
	}

	public function getListReceiveAddress($address, $account, $count = 20)
	{
		$cnt = 0;
		$from = 0;
		$t = array();
		while ($cnt < $count) {
			$r = $this->getListTransactionAddress($address, $account, 100, $from);
			$from += 100;
			if ($r) {
				$receive = array();
				if(count($r)<1){break;}
				for ($i = 0; $i < count($r); $i++) {
					$row = (array) $r[$i];
					if ($row['direction'] == 'in') {
						$receive[] = $row;
					}
				}
				$t = array_merge($t, $receive);
				$cnt = count($t);
			} else {
				break;
			}
		}
		return $t;
	}

	/**
	 * 코인 보내기.
	 * 수수료는 0.001 BTC
	 * @todo toWei 함수를 구현해서 보내는양을 ether를 wei로 바꿔야 함.
	 *
	 * @param address sender address.
	 * @param address receiver address.
	 * @param number amount of coin.
	 * @return mixed send result. 만약 실패하면 false. 성공하면 txid.
	 */
	public function send($from_address, $from_account, $to_address, $amount, $fee, $msg = '', $pwd = '')
	{
		$contract = new Contract($this->web3->provider, $this->abi);

		// check balance - 잔액부족
		$r = $this->getBalanaceAddress($from_address, $from_account);
		$remain = $r->confirmed ? $r->confirmed : 0;
		if ($amount > $remain) {
			$this->jsonrpc->error = 'There is not enough balance to send.';
			return false;
		}

		// 이더리움 개스 확인.(전송 수수료)
		$r = $this->getEthBalanaceAddress($from_address);
		$balance = $r->confirmed ? $r->confirmed : 0;
		if ($balance < 0.001) {
			$this->jsonrpc->error = 'There is not enough fee to send. Please charge more than 0.001 ETH.';
			return false;
		}

		// unlock account
		$r = $this->web3->personal->unlockAccount($from_address, $pwd);
		if ($r->data) {
			// send erc20 token
			$amount = $this->numtostr($amount * pow(10, $this->decimals));
			// $gas = $this->bcdechex($this->eth2wei('0.0000000001'));
			$gas = 2100000; //$this->bcdechex('4700000');//5208   21000, '0x200b20':2100000
			$r = $contract->at($this->contractAddress)->send('transfer', $to_address, $amount, ['from' => $from_address, 'gas' => $gas]);
			if (!$r->err) {
				$_result = $r->data;
				// var_dump('=====success ===============',$_result);
			} else {
				$this->jsonrpc->error = $r->err->getMessage();
				$_result = false;
				// var_dump('=====error ===============',$r->err->getMessage());
			}
			// var_dump($r); exit;
		} else {
			$this->jsonrpc->error = $r->err->getMessage();
			$_result = false;
		}
		return $_result;
	}

	/**
	 * 주소 검사.
	 * BTC 주소가 맞는지 검사합니다. 동일한 방식을 사용하는 BTC, BCH, BTG 모두 true로 리턴됩니다.
	 * @param address BTC 주소.
	 * @return boolean 검사 결과. true: BTC, BCH, BTG 주소, false: 다른 주소.
	 */
	public function validateAddress($address)
	{
		$_result = $this->jsonrpc->eth_getBalance(array($address, 'latest'));
		if (!$_result) {
			// electrum 지갑 속의 address가 아닌 BTC, BCH, BTG 주소인지 확인.
			// if(strpos( $this->jsonrpc->error, ' unknown address type: 0')!==false) {
			//     $_result = true;
			// } else {
			$_result = false;
			// }
		} else {
			$_result = true;
		}
		return $_result;
	}

	/*
     * The following functions are for conversion
     * and for handling big numbers
     */
	public function wei2eth($wei)
	{
		return bcdiv($wei, 1000000000000000000, 18);
	}

	/*
     * The following functions are for conversion
     * and for handling big numbers
     */
	public function eth2wei($eth)
	{
		return bcmul($eth, 1000000000000000000, 0);
	}

	public function bchexdec($hex)
	{
		if (strlen($hex) == 1) {
			return hexdec($hex);
		} else {
			$remain = substr($hex, 0, -1);
			$last = substr($hex, -1);
			return bcadd(bcmul(16, $this->bchexdec($remain)), hexdec($last));
		}
		// https://github.com/kvhnuke/etherwallet/blob/mercury/json_relay_php/api.php
		// $dec = 0;
		// $len = strlen($hex);
		// for ($i = 1; $i <= $len; $i++) {
		//     $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
		// }
		// return $dec;
	}

	function bcdechex($dec)
	{
		$hex = '';
		do {
			$last = bcmod($dec, 16);
			$hex = dechex($last) . $hex;
			$dec = bcdiv(bcsub($dec, $last), 16);
		} while ($dec > 0);
		return '0x' . $hex;
	}

	// 아래는 https://github.com/sc0Vu/web3.php/blob/4df0835f2bf0d187fee799737017b16d3c8b72c8/src/Utils.php 에서

	/**
	 * UNITS
	 * from ethjs-unit
	 *
	 * @const array
	 */
	const UNITS = [
		'noether'   => '0',
		'wei'       => '1',
		'kwei'      => '1000',
		'Kwei'      => '1000',
		'babbage'   => '1000',
		'femtoether' => '1000',
		'mwei'      => '1000000',
		'Mwei'      => '1000000',
		'lovelace'  => '1000000',
		'picoether' => '1000000',
		'gwei'      => '1000000000',
		'Gwei'      => '1000000000',
		'shannon'   => '1000000000',
		'nanoether' => '1000000000',
		'nano'      => '1000000000',
		'szabo'     => '1000000000000',
		'microether' => '1000000000000',
		'micro'     => '1000000000000',
		'finney'    => '1000000000000000',
		'milliether' => '1000000000000000',
		'milli'     => '1000000000000000',
		'ether'     => '1000000000000000000',
		'kether'    => '1000000000000000000000',
		'grand'     => '1000000000000000000000',
		'mether'    => '1000000000000000000000000',
		'gether'    => '1000000000000000000000000000',
		'tether'    => '1000000000000000000000000000000',
	];


	/**
	 * convert number to string
	 * 10 * 0.000000000000000111 -> '10.000000000000000111'
	 * 0.000000000000000111 -> '0.000000000000000111'
	 * 111000000000000 * pow(10,18) -> "111000000000000000000000000000000"
	 * 111000000000000 -> "111000000000000"
	 */
	function numtostr($n)
	{
		$decimals = 0;
		$sign = '+';
		$s = strval($n);
		// 10승 확인.
		if (strpos($s, 'E') !== false) {
			$t = explode('E', $s);
			$number = $t[0];
			$decimals = substr($t[1], 1);
			$sign = substr($t[1], 0, 1);
			// 소숫점 확인
			if (strpos($number, '.') !== false) {
				$t = explode('.', $number);
				$number = $t[0] . $t[1];
				if ($sign == '+') {
					$decimals -= strlen($t[1]);
				} else {
					$decimals -= strlen($t[0]);
				}
			}
		} else {
			$number = $s;
		}
		if ($sign == '+') {
			$s = $number . str_repeat('0', $decimals);
		} else {
			$s = '0.' . str_repeat('0', $decimals) . $number;
		}
		return $s;
	}
}
