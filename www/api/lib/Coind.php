<?php
if (!defined('__LOADED_COIND__')) {
    class Coind
    {

        public $coin = 'BTC'; // 사용할 가상화폐단위. BTC, ETH, LTC, ...
        public $coind = null; // 사용할 가상화폐daemon 핼퍼. BTC, ETH, LTC, ...
        public $runtype = __API_RUNMODE__; // 실행종류. dev: 개발서버, live: 실서버

        public function __construct($symbol = '')
        {
            if ($symbol) {
                $this->coin = strtoupper($symbol);
            }
            if ($this->coin) {
                include dirname(__file__) . '/' . $this->coin . '/' . $this->coin . 'Coind.php';
                $_class = $this->coin . 'Coind';
                $this->coind = new $_class($this->runtype);
                // if(method_exists($this->coind, 'set_ini')) {
                //     $this->coind->set_ini($this->runtype);
                // }
            }
        }

        public function __destruct()
        {

        }

        public function getError()
        {
            return $this->coind->getError();
        }

        /**
         * Address의 잔액 구하기.
         * @param Address Wallet Address.
         * @param account Wallet Account ID.
         * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
         */
        public function getBalanaceAddress($address, $account, $passwd='')
        {
            return $this->coind->getBalanaceAddress($address, $account, $passwd);
        }

        /**
         * 지갑 잔액 구하기.
         * @return Object 잔액 정보. total: 전체잔액, confirmed: confirmed 잔액, unconfirmed: 미승인 잔액
         */
        public function getBalanaceTotal()
        {
            return $this->coind->getBalanaceTotal();
        }

        /**
         * 새 주소 생성. - ok
         * @return address 새 주소.
         */
        public function genNewAddress($userno, $pwd)
        {
            return $this->coind->genNewAddress($userno, $pwd);
        }

        /**
         * 전체 주소 구하기.
         * @return Array address 배열.
         * @todo 전체 주소를 한번에 다 리턴하기 때문에 주소가 많은경우 부하가 걸릴 수 있음. 사용하지 말고 DB에 address를 저장하고 저장된 address를 사용하도록 하세요.
         */
        public function getListAddress()
        {
            return $this->coind->getListAddress();
        }

        /**
         * 거래내역 상세정보 구하기.
         * @param String 트랜젝션 아이디.
         * @param String 받는 사람 주소. address를 넣는 이유는 하나의 transaction에서 받는 사람이 여럿일때 어떤 주소가 얼마나 받았는지 알아야 하는 경우가 있어서 받는 사람의 address를 넣어 줍니다.
         * @return Object 트랜젝션 정보.
         * @todo 리턴 값에 상세 정보 정리하기.
         * @todo. hex 값으로 전달받는 거래내역에서 거래 상세 정보를 추출하는 방법을 찾아야 함.
         */
        public function getTransaction($txnid, $address, $account='')
        {
            return $this->coind->getTransaction($txnid, $address, $account);
        }

        /**
         * address의 트랜젝션 목록 구하기.
         * @param Address 주소.
         * @return Array 트랜젝션 목록.
         * @todo 트렌젝션 목록이 페이징 없이 전부 표시되기때문에 거래가 많은 경우 시간이 오래 걸릴 수 있음.
         *       최근 데이터 20개만 사용하거나 DB에 저장해서 사용할 필요 있음.
         */
        public function getListTransactionAddress($address, $account, $count=100, $from=0, $fromid='')
        {
            return $this->coind->getListTransactionAddress($address, $account, $count, $from, $fromid, $gubun);
        }

        /**
         * 수신 트랜젝션 목록 구하기.
         * @param Address 주소.
         * @return Array 트랜젝션 목록.
         * @todo 트렌젝션 목록이 페이징 없이 전부 표시되기때문에 거래가 많은 경우 시간이 오래 걸릴 수 있음.
         *       최근 데이터 20개만 사용하거나 DB에 저장해서 사용할 필요 있음.
         */
        public function getListReceiveAddress($address, $account, $count=100, $from=0, $fromid='')
        {
            return $this->coind->getListReceiveAddress($address, $account, $count, $from, $fromid, $gubun);
        }

        /**
         * 코인 보내기.
         * 수수료는 고정
         * BTC 0.001 BTC
         *
         * @param address sender address. ethereum 계열, electrum은 address를 사용합니다.
         * @param account sender account. bitcoin 계열은 account를 사용합니다.
         * @param address receiver address.
         * @param number amount of coin.
         * @param number amount of fee.
         * @param string message.
         * @param string password. 비밀번호가 필요한 경우 사용됩니다. 사용자가 입력한 비밀번호를 사용합니다.
         * @return mixed send result. 만약 실패하면 false. 성공하면 txid.
         */
        public function send($from_address, $from_account, $to_address, $amount, $fee, $msg='', $passwd='')
        {
            return $this->coind->send($from_address, $from_account, $to_address, $amount, $fee, $msg, $passwd);
        }

        /**
         * 주소 검사.
         * 가상화폐별로 주소가 맞는지 검사합니다. 동일한 방식을 사용하는 가상화폐 주소는 모두 true로 리턴됩니다.
         * @param address 주소.
         * @return boolean 검사 결과. true: 올바른 주소, false: 다른 주소.
         */
        public function validateAddress($address)
        {
            return $this->coind->validateAddress($address);
        }

    }
    define('__LOADED_COIND__', true);
}