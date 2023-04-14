<?php
require dirname(__file__).'/../JsonRPC.php';
require dirname(__file__).'/../simple_html_dom.php';
require dirname(__file__).'/../vendor/autoload.php';

use Web3\Web3;
// use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

/**
 * 이더리음 연동 클래스 - geth 사용.
 */
class ETHCoind
{
    private $jsonrpc = null;
    // private $contractAddress = null;
    private $txnapiurl = null;
    private $txnweburl = null;
    private $etherscankey = null;
    private $abi = null;
    private $opcode = null;
    private $web3 = null; // ethereum web3js
    private $decimals = 18; // coin 소숫점 자릿수. 1 SCC = 1.0E18 ;

    public $txn_fee = 0.001; // 일반적인 transaction fee. send 하기 전에 참고하여 보내는 금액을 계산해서 넘겨야 합니다. https://etherscan.io/


    public function __construct($runtype='dev')
    {
        switch($runtype) {
            case 'loc':
            case 'dev':
                $_host = '54.180.139.236'; // rinkeby testnet 사용. 테스트코인받기:  https://faucet.rinkeby.io/, 이더스켄: https://rinkeby.etherscan.io/
                $_port = '6545';
                $_user = '';
                $_pw = '';
                $_protocol = 'http';
                $_access_time = 5;
                // $this->contractAddress = '0x5ad2679916fb618c26ee518fa70d7c86b18c2800';
                $this->txnapiurl = 'https://api-rinkeby.etherscan.io';
                $this->txnweburl = 'https://rinkeby.etherscan.io';
                // $this->txnapiurl = 'https://api-ropsten.etherscan.io';
                // $this->txnweburl = 'https://ropsten.etherscan.io';
                $this->etherscankey = 'Y2YU4P2R1WPFFQEV32K814GNVJSRBGYQCR';
            break;
            case 'live':
                $_host = '13.124.107.186'; // ethereum 13.209.3.155 172.31.16.139
                $_port = '6545';
                $_user = '';
                $_pw = '';
                $_protocol = 'http';
                $_access_time = 60;
                // $this->contractAddress = '0x8895F77Cc7Fb341E950D6aB8b12B5a96C7a7896c'; // 2018.7.10 새로 추가
                $this->txnapiurl = 'https://api.etherscan.io';
                $this->txnweburl = 'https://etherscan.io';
                $this->etherscankey = 'VCK6SW9PN2NG5JMZ4A8Q59R37F6KPKDAMF';
                $this->abi = file_get_contents(dirname(__file__).'/abi-live');
                $this->opcode = file_get_contents(dirname(__file__).'/bytecode-live');
                // curl --data-binary '{"jsonrpc":"2.0","method":"eth_getBalance","params":["0xc94770007dda54cF92009BFF0dE90c06F603a09f","latest"],"id":1}' -H 'content-type:application/json;' http://10.10.2.245:6545
            break;
        }
        $this->jsonrpc = new JsonRPC($_user, $_pw, $_host, $_port, $_protocol, null);
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($_protocol.'://'.$_host.':'.$_port, $_access_time, false)));
    }

    public function __destruct()
    {

    }

    public function getError()
    {
        return $this->jsonrpc->error;
    }

    public function setError($str)
    {
        $this->jsonrpc->error = $str;
    }

    // /**
    //  * Address의 잔액 구하기.
    //  * @param Address 주소.
    //  * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
    //  */
    public function getBalanaceAddress($address)
    {
		$pending = 0;
		// $address = '0x2a50887cf8610e19153522551dcd1f27d3f7123f'; // testing address 101 XSCC

		// token을 사용하기때문에 token balance를 가져와야 함.
		$url = $this->txnapiurl."/api?module=account&action=balance&address={$address}&tag=latest&apikey={$this->etherscankey}";
		$json = $this->jsonrpc->remote_get($url);
		$json = json_decode($json);
		if ($json) {
			$balance = $json->result;
			$balance = $balance / pow(10, $this->decimals);
		}
		// web parsing
		$_result = (object) array("account" => $account, 'confirmed' => $balance, 'unconfirmed' => $pending, "address" => $address);
		return $_result;
    }

    /**
     * 지갑 잔액 구하기.
     * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
     * @todo 전체 지갑 잔액 구하기는 방법 찾기.
     */
    public function getBalanaceTotal()
    {
        $_result = $this->jsonrpc->eth_getBalance(array());
        if ($_result) {
            $_result = json_decode($_result);
            $_result->total = $_result->confirmed;
        } else {
            $_result = (object) array('total' => 0, 'confirmed' => 0);
        }
        return $_result;
    }

    /**
     * 새 주소 생성.
     * @return Address 새 주소.
     */
    public function genNewAddress($account, $pwd='')
    {
        //$pwd = '';//$pwd ? $pwd : ''; 패스워드 설정 않하도록 수정함.
        $r = $this->web3->personal->newAccount($pwd);
        // var_dump($r->err, $r->err->getMessage()); //exit;
        if($r->err) {
            $this->setError($r->err->getMessage());
            return false;
        }
        return $r->data;
    }

    // /**
    //  * 전체 주소 구하기.
    //  * @return Array address 배열.
    //  *     "result": ["0xe104fc2fa6e84b37ab2e88a49f99f49aa424f6b5","0xde7b2e63af783a4105c8a4b6c57a9e033486df1b","0xe0b5542123141c7e3ab5dd350a0404099f2eb407"]
    //  * @todo 전체 주소를 한번에 다 리턴하기 때문에 주소가 많은경우 부하가 걸릴 수 있음. 사용하지 말고 DB에 address를 저장하고 저장된 address를 사용하도록 하세요.
    //  */
    // public function getListAddress()
    // {
    //     return $this->jsonrpc->eth_accounts(); // ok
    // }

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
        // web3
        $r = $this->web3->eth->getTransactionReceipt($txid, function ($err, $transaction) {});
        // var_dump($r->data); //exit;
        // The receipt is not available for pending transactions and returns null.
        if($r->data) {
            $_result = (object) array(
                'txnid'=>$txid,
                // 'txndate' => date('Y-m-d H:i:s', $r->time), // getTransactionReceipt 에는 거래 시간값이 없음. https://github.com/ethereum/wiki/wiki/JSON-RPC#eth_gettransactionreceipt
                'status'=>'',
                'amount'=>'',
                // 'symbol'=>'',
                'from_address'=>'',
                'to_address'=>'',
                'time'=>'',
                'cnt_confirm'=>'',
                'tax' => 0,
                'fee'=>''
            );

            if($r->data->status=='0x1') {
                $_result->status = 'S';
            }
            $_result->from_address = $r->data->from;
            $_result->to_address = $r->data->to;
            $_result->fee = $this->web3->utils->toBn($r->data->gasUsed)->value;
        } else {
            $_result->status = 'P';
        }
        return $_result;
    }

    /**
     * address의 트랜젝션 목록 구하기.
     * @param Address 주소.
     * @return Array 트랜젝션 목록.
     * @todo 트렌젝션 목록이 페이징 없이 전부 표시되기때문에 거래가 많은 경우 시간이 오래 걸릴 수 있음.
     *       최근 데이터 20개만 사용하거나 DB에 저장해서 사용할 필요 있음.
     *       https://rinkeby.etherscan.io/address/0xe104fc2fa6e84b37ab2e88a49f99f49aa424f6b5
     *       https://ropsten.etherscan.io/token/generic-tokentxns2?contractAddress=0x987df0df68755578ec5e3907ad0a5eeb523d88cc&mode=&a=0x08f094d3bc043df21727919aad5a3a0d182fe32d&p=8
     *       Etherscan 을 이용하여 개발용 계정의 트랜젝션을 구하는 것도 생각해보자.
     */
    public function getListTransactionAddress($address, $account, $count=20, $from=0)
    {
        $t = array();
        try {
            // api 방식으로 변경 (웹사이트 차단시킴)
            $page = $from>0 ? floor($from/$count)+1 : 1;
            $url = $this->txnapiurl."/api?module=account&action=txlist&address={$address}&page={$page}&offset={$count}&sort=desc&apikey={$this->etherscankey}";
            // http://api.etherscan.io/api?module=account&action=txlist&address=0x2039f68B978565629364aF8A02e0439F1373C214&page=1&offset=100&sort=desc&apikey=D3M38Y1HTW9KHFCIRNJNIXBVVBSHWD1E8T
            // {"status":"1","message":"OK","result":[{"blockNumber":"8759496","timeStamp":"1571328706","hash":"0xec7d0c91c3dc8de3a50fe5fb1626aaf57f7d92ba1d4342e79388589d693f42ed","nonce":"356","blockHash":"0xa1d3128cf3696798d2bc87e06fc6cdee329129f3111fc2946a3ebec948bec6fb","transactionIndex":"199","from":"0x2039f68b978565629364af8a02e0439f1373c214","to":"0x8bf7326c3fff3a3ba9fcc618641bb8f3cd2eb7f9","value":"0","gas":"77497","gasPrice":"7000000000","isError":"0","txreceipt_status":"1","input":"0xa9059cbb0000000000000000000000001e3b750e08b3d715fd665bb633ae6533107bfd5900000000000000000000000000000000000000000000e8ef1e96ae3897800000","contractAddress":"","cumulativeGasUsed":"8767809","gasUsed":"51665","confirmations":"40974"},{"blockNumber":"8749525","timeStamp":"1571192029","hash":"0xd4867ec09af9d445e91c8e2a4217ac07e9f5fc676430bb8a40124919469e8888","nonce":"355","blockHash":"0xbf62b8d9a6749b7f7fe42ba7386b6a4b60ea50cf188ae1c7f6bb73396971367b","transactionIndex":"14","from":"0x2039f68b978565629364af8a02e0439f1373c214","to":"0xec748a5171b4abc61a4583e21e8934444e897097","value":"20000000000000000","gas":"21000","gasPrice":"5000000000","isError":"0","txreceipt_status":"1","input":"0x","contractAddress":"","cumulativeGasUsed":"803744","gasUsed":"21000","confirmations":"50945"}]}
            $json = $this->jsonrpc->remote_get($url);
            $result = json_decode($json);
            // if($result->status!='1') {
            //     throw new Exception("API 결과가 오류상태({$result->status})입니다.", 1);
            // }
            $result = $result->result;
            foreach($result as $row) {
                $r = array();
                // txnid
                $r['txnid'] = $row->hash;
                $r['time'] = $row->timeStamp;
                $r['txn_date'] = $r['time']!='' ? date('Y-m-d H:i:s', $r['time']) : '';
                $r['account'] = $account;
                $r['amount'] = $this->wei2eth($row->value);
                $r['fee'] =  $this->wei2eth($row->gasPrice * $row->gasUsed);
                $r['confirmations'] = $row->confirmations;
                $r['status'] = $row->confirmations>=1 ? 'S' : 'P'; // S: Success, P: Pendding
                $r['direction'] = '';
                if(strtolower(trim($row->to))==strtolower($address) && strtolower(trim($row->from))==strtolower($address)) {
                    $r['direction'] = 'self';
                    continue; // continue for. self 이동  txn은 사용안합니다.
                }
                if(strtolower(trim($row->to))==strtolower($address) && strtolower(trim($row->from))!=strtolower($address)) {
                    $r['direction'] = 'in';
                }
                if(strtolower(trim($row->to))!=strtolower($address) && strtolower(trim($row->from))==strtolower($address)) {
                    $r['direction'] = 'out';
                }
                $r['from_address'] = trim($row->from);
                $r['to_address'] = trim($row->to);
                if($r['direction']=='out') {
                    $r['address'] = $r['to_address'];
                } else {
                    $r['address'] = $r['from_address'];
                }
                $t[] = $r;
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage() . ' (오류코드:' . $e->getCode() . ')');
        }
        return $t;
    }

    // /**
    //  * address의 트랜젝션 목록 구하기.
    //  * @param Address 주소.
    //  * @return Array 트랜젝션 목록.
    //  * @todo 트렌젝션 목록이 페이징 없이 전부 표시되기때문에 거래가 많은 경우 시간이 오래 걸릴 수 있음.
    //  *       최근 데이터 20개만 사용하거나 DB에 저장해서 사용할 필요 있음.
    //  *       https://rinkeby.etherscan.io/address/0xe104fc2fa6e84b37ab2e88a49f99f49aa424f6b5
    //  *       Etherscan 을 이용하여 개발용 계정의 트랜젝션을 구하는 것도 생각해보자.
    //  */
    // public function getListTransactionAddress($address)
    // {
    //     $_result = $this->jsonrpc->getaddresshistory(array('account'=>$account));
    //     if($_result && is_string($_result)) {
    //         $_result = json_decode($_result);
    //     }
    //     $t = array();
    //     foreach ($_result as $row) {
    //         $t[] = array(
    //             'txnid'=> $row['txid']
    //         );
    //     }
    //     return $t;
    //     // $_result = $this->jsonrpc->getaddresshistory(array('address' => $address));
    //     // if ($_result) {
    //     //     $_result = json_decode($_result);
    //     // }
    //     // return $_result;
    // }

    public function getListReceiveAddress($address, $account='', $count=100)
    {
        $t = array();
        try {
            $from  = 0;
            $offset = 50;
            while(count($t)<$count) {

                // api 방식으로 변경 (웹사이트 차단시킴)
                $page = $from>0 ? floor($from/$count)+1 : 1;
                $url = $this->txnapiurl."/api?module=account&action=txlist&address={$address}&page={$page}&offset={$offset}&sort=desc&apikey={$this->etherscankey}";
                // http://api.etherscan.io/api?module=account&action=txlist&address=0x2039f68B978565629364aF8A02e0439F1373C214&page=1&offset=100&sort=desc&apikey=D3M38Y1HTW9KHFCIRNJNIXBVVBSHWD1E8T
                // {"status":"1","message":"OK","result":[{"blockNumber":"8759496","timeStamp":"1571328706","hash":"0xec7d0c91c3dc8de3a50fe5fb1626aaf57f7d92ba1d4342e79388589d693f42ed","nonce":"356","blockHash":"0xa1d3128cf3696798d2bc87e06fc6cdee329129f3111fc2946a3ebec948bec6fb","transactionIndex":"199","from":"0x2039f68b978565629364af8a02e0439f1373c214","to":"0x8bf7326c3fff3a3ba9fcc618641bb8f3cd2eb7f9","value":"0","gas":"77497","gasPrice":"7000000000","isError":"0","txreceipt_status":"1","input":"0xa9059cbb0000000000000000000000001e3b750e08b3d715fd665bb633ae6533107bfd5900000000000000000000000000000000000000000000e8ef1e96ae3897800000","contractAddress":"","cumulativeGasUsed":"8767809","gasUsed":"51665","confirmations":"40974"},{"blockNumber":"8749525","timeStamp":"1571192029","hash":"0xd4867ec09af9d445e91c8e2a4217ac07e9f5fc676430bb8a40124919469e8888","nonce":"355","blockHash":"0xbf62b8d9a6749b7f7fe42ba7386b6a4b60ea50cf188ae1c7f6bb73396971367b","transactionIndex":"14","from":"0x2039f68b978565629364af8a02e0439f1373c214","to":"0xec748a5171b4abc61a4583e21e8934444e897097","value":"20000000000000000","gas":"21000","gasPrice":"5000000000","isError":"0","txreceipt_status":"1","input":"0x","contractAddress":"","cumulativeGasUsed":"803744","gasUsed":"21000","confirmations":"50945"}]}
                $json = $this->jsonrpc->remote_get($url);
                $result = json_decode($json);
                // if($result->status!='1') {
                //     throw new Exception("API 결과가 오류상태({$result->status})입니다.", 1);
                // }
                $result = $result->result;
                if(count($result)<1) {
                    break; // break while
                }
                foreach($result as $row) {
                    $r = array();
                    // txnid
                    $r['txnid'] = $row->hash;
                    $r['time'] = $row->timeStamp;
                    $r['txn_date'] = $r['time']!='' ? date('Y-m-d H:i:s', $r['time']) : '';
                    $r['account'] = $account;
                    $r['amount'] = $this->wei2eth($row->value);
                    $r['fee'] =  $this->wei2eth($row->gasPrice * $row->gasUsed);
                    $r['confirmations'] = $row->confirmations;
                    $r['status'] = $row->confirmations>=1 ? 'S' : 'P'; // S: Success, P: Pendding
                    $r['direction'] = '';
                    if(strtolower(trim($row->to))==strtolower($address) && strtolower(trim($row->from))==strtolower($address)) {
                        $r['direction'] = 'self';
                        continue; // continue for. self 이동  txn은 사용안합니다.
                    }
                    if(strtolower(trim($row->to))==strtolower($address) && strtolower(trim($row->from))!=strtolower($address)) {
                        $r['direction'] = 'in';
                    }
                    if(strtolower(trim($row->to))!=strtolower($address) && strtolower(trim($row->from))==strtolower($address)) {
                        $r['direction'] = 'out';
                    }
                    $r['from_address'] = trim($row->from);
                    $r['to_address'] = trim($row->to);
                    if($r['direction']=='out') {
                        $r['address'] = $r['to_address'];
                    } else {
                        $r['address'] = $r['from_address'];
                    }
                    // 받은 히스토리만 추출
                    if($r['direction']=='in') {
                        $t[] = $r;
                    }
                }
                $from += $offset;
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage() . ' (오류코드:' . $e->getCode() . ')');
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
     * @param number amount of Ether.
     * @return mixed send result. 만약 실패하면 false. 성공하면 txid.
     */
    // public function send($from_address, $to_address, $amount)
    public function send($from_address, $from_account, $to_address, $amount, $fee, $msg='', $passwd='')
    {
        // dev에서 마이닝 안해서 잔액이 0으로 나오는 현상 있어 발송 태스트를 못함. 예전에 성공한 소스로 그대로 유지함.
        // dev에 geth 마이닝 시작했음(10-23 11:54) 내일 다시 확인해서 send 태스트 진행하기.
        // jsonrpc도 정상작동함. - web3 클래스로 사용하려 했는데 web3도 personal 메소드를 쓰려면 결국 geth를 실행할때 옵션에 추가되어 있어야 한다. 즉, web3 클래스나 jsonrpc 클래스나 클라이언트 라이브러리는 뭘쓰던 동일함.
        $_result = $this->jsonrpc->personal_unlockAccount($from_address,$passwd,30); // parameter를 array로 감싸면 애러발생함. "invalid argument 0: json: cannot unmarshal non-string into Go value of type common.Address"
        if(! $_result) {
            return false;
        }
        $_result = $this->jsonrpc->eth_sendTransaction(array(
            "from" => $from_address,
            "to" => $to_address,
            "value" => $this->bcdechex($this->eth2wei($amount))
        ));
        // "result": "0x7553966746464058a8eb7fd668675dfb5b061cf60746af965fac43b049b71981"  0.1
        // "result": "0x8214a8eb98f98e13004f046c6a55c37d14786a6b20e337cae110730b5f5f87fe"  0.01
        // "result": "0xcb57b9a2e1b965d83cec358102ead26f1756bd2f37ee71906dbe9a578c24446b"  0.01
        if (!$_result) {
            // var_dump($this->getError());
            return false;
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
        return preg_replace('/\.$/','',preg_replace('/0+$/','',bcdiv($wei, 1000000000000000000, 18)));
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

    function bcdechex($dec) {
        $hex = '';
        do {
            $last = bcmod($dec, 16);
            $hex = dechex($last).$hex;
            $dec = bcdiv(bcsub($dec, $last), 16);
        } while($dec>0);
        return '0x'.$hex;
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
        'femtoether'=> '1000',
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
        'microether'=> '1000000000000',
        'micro'     => '1000000000000',
        'finney'    => '1000000000000000',
        'milliether'=> '1000000000000000',
        'milli'     => '1000000000000000',
        'ether'     => '1000000000000000000',
        'kether'    => '1000000000000000000000',
        'grand'     => '1000000000000000000000',
        'mether'    => '1000000000000000000000000',
        'gether'    => '1000000000000000000000000000',
        'tether'    => '1000000000000000000000000000000',
    ];

}
