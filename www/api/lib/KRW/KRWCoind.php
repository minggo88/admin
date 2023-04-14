<?php
require dirname(__file__).'/../JsonRPC.php';
/**
 * 은행 연동 클래스
 * 지금은 계좌 생성용으로만 사용. 나중에 은행 연동시 가상계좌번호 등을 연동하여 사용할때를 대비해 클래스만 만들어 둡니다.
 */
class KRWCoind
{
    private $jsonrpc = null;

    public $txn_fee = 0; // 일반적인 transaction fee. send 하기 전에 참고하여 보내는 금액을 계산해서 넘겨야 합니다. https://www.blockchain.com/ko/explorer?currency=BCH

    public function __construct()
    {
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
     * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
     */
    public function getBalanaceAddress($address)
    {
        return (object) array('total' => 0, 'confirmed' => 0, 'unconfirmed' => 0);
    }

    /**
     * 지갑 잔액 구하기.
     * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
     * @todo 전체 지갑 잔액 구하기는 방법 찾기.
     */
    public function getBalanaceTotal()
    {
        return (object) array('total' => 0, 'confirmed' => 0);
    }

    /**
     * 새 주소 생성.
     * @return Address 새 주소.
     */
    public function genNewAddress($account, $pwd='')
    {
        return ''.date('Y').'-R'.sprintf("%05d", time()%10000).'';
    }

    /**
     * 전체 주소 구하기.
     * @return Array address 배열.
     *     "result": ["0xe104fc2fa6e84b37ab2e88a49f99f49aa424f6b5","0xde7b2e63af783a4105c8a4b6c57a9e033486df1b","0xe0b5542123141c7e3ab5dd350a0404099f2eb407"]
     * @todo 전체 주소를 한번에 다 리턴하기 때문에 주소가 많은경우 부하가 걸릴 수 있음. 사용하지 말고 DB에 address를 저장하고 저장된 address를 사용하도록 하세요.
     */
    public function getListAddress()
    {
    }

    /**
     * 거래내역 상세정보 구하기.
     * @param String 트랜젝션 아이디.
     * @return Object 트랜젝션 정보.
     * @todo 리턴 값에 상세 정보 정리하기.
     * @todo. hex 값으로 전달받는 거래내역에서 거래 상세 정보를 추출하는 방법을 찾아야 함.
     *       https://rinkeby.etherscan.io/tx/0xcb57b9a2e1b965d83cec358102ead26f1756bd2f37ee71906dbe9a578c24446b
     *       Etherscan 을 이용하여 개발용 계정의 트랜젝션을 구하는 것도 생각해보자.
     */
    public function getTransaction($txid)
    {
    }

    /**
     * address의 트랜젝션 목록 구하기.
     * @param Address 주소.
     * @return Array 트랜젝션 목록.
     * @todo 트렌젝션 목록이 페이징 없이 전부 표시되기때문에 거래가 많은 경우 시간이 오래 걸릴 수 있음.
     *       최근 데이터 20개만 사용하거나 DB에 저장해서 사용할 필요 있음.
     *       https://rinkeby.etherscan.io/address/0xe104fc2fa6e84b37ab2e88a49f99f49aa424f6b5
     *       Etherscan 을 이용하여 개발용 계정의 트랜젝션을 구하는 것도 생각해보자.
     */
    public function getListTransactionAddress($address)
    {
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
    public function send($from_address, $from_account, $to_address, $amount, $fee, $msg='')
    {
        // var_dump($from_address, $from_account, $to_address, $amount, $fee, $msg);
    }

    /**
     * 주소 검사.
     * BTC 주소가 맞는지 검사합니다. 동일한 방식을 사용하는 BTC, BCH, BTG 모두 true로 리턴됩니다.
     * @param address BTC 주소.
     * @return boolean 검사 결과. true: BTC, BCH, BTG 주소, false: 다른 주소.
     */
    public function validateAddress($address)
    {
    }

}
