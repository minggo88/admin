<?php

include_once '../lib/common_user.php';
include_once './myinfo_class.php';
include dirname(__file__) . "/../api/lib/TradeApi.php";

function getNavi() {
	$ret = array();
	return $ret;
}

$js = new Myinfo($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

// 관리자가 특정 회원의 내역을 볼때 사용.
if($_SESSION['ADMIN_ID'] && trim($_GET['userid'])!='') {
	$_SESSION["USER_ID"] = trim($_GET['userid']);
	$_SESSION['USER_REALNAME'] = true;
// echo '<!-- '; var_dump($_SESSION); echo ' -->';
}

checkUser();
// checkUserTrade(); // 실명 인증확인은 미사용으로 주석처리합니다.
$tradeapi->checkLogin();
$userno = $tradeapi->get_login_userno();

// validate parameters
$symbol = checkSymbol(strtoupper(setDefault($_REQUEST['symbol'], 'ALL')));

if($symbol=='ALL') {
    $symbol = '';
}

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
$interface->setBasicInterface('user', 'a3');
$interface->addNavi(getNavi());
$interface->setPlugIn('tooltipster');
$interface->addCss('/template/'.getSiteCode().'/' . $user_skin . '/account/css/account.css');

$balanceList = $tradeapi->get_wallet($userno, $symbol);
// var_dump($balanceList);// exit;

$cache_id = 'getCurrency-'.$symbol;
$c = $tradeapi->get_cache($cache_id, 60);
if($c=='') {
    $c = $tradeapi->set_cache($cache_id, $tradeapi->get_currency($symbol));
}
$w_have_balance = array();
$w_have_wallet = array();
if($c !== null && count($c) != 0) {
    for($i=0; $i<count($c); $i++) {
        $symbol = $c[$i]->symbol;
        $displayDecimal = $c[$i]->display_decimals*1;
        $cnt = 0;
        $have_wallet = 0;
        if($balanceList !== null && count($balanceList) != 0) {
            for($j=0; $j<count($balanceList); $j++) {
                if($symbol == $balanceList[$j]->symbol) {
                    $cnt++;
                    $have_wallet = 1;
                    $balanceTxt = strval(number_format($balanceList[$j]->confirmed*1,$displayDecimal));
                    $c[$i]->balanceTxt = $balanceTxt;
                }
            }
        }
        $c[$i]->ea_unit = $c[$i]->symbol=='KRW' ? 'KRW' : '주';

        if($cnt == 0) {
            $c[$i]->balance = number_format(0,$displayDecimal);
        }
        if($c[$i]->name == "대한민국, 원"){
            $c[$i]->name = Lang::main_KRW;
        }
        if($c[$i]->balance>0 || $symbol=='KRW' ) {
            $w_have_balance[] = $c[$i];
        }

        if($have_wallet || $symbol=='KRW' ) {
            $w_have_wallet[] = $c[$i];
        }
    }
}

// $interface->tpl->assign("balanceList", $c); // 전체 지갑 잔액 표시
// $interface->tpl->assign("balanceList", $w_have_balance); // KRW와 잔액있는 지갑만 표시
$interface->tpl->assign("balanceList", $w_have_wallet); // 지갑 있는것만 표시

$symbol_transaction = "KRW";

$paramSymbol = str_replace('histories','',$_SERVER['REQUEST_URI']);
$paramSymbol = str_replace('/','',$paramSymbol);
$p_q = strpos($paramSymbol, '?');
if($p_q!==false) {
    $paramSymbol = substr($paramSymbol, 0, $p_q);
}
// var_dump($_SERVER['REQUEST_URI'], $paramSymbol); exit;


if($paramSymbol != "") {
    $symbol_transaction = $paramSymbol;
}

$wallet_tmp = $tradeapi->get_wallet($userno, $symbol_transaction);

/*$address_list = [];

if($c !== null && count($c) != 0) {
    for($i=0; $i<count($c); $i++) {
        $wallet_tmp = $tradeapi->get_wallet($userno, $c[$i]->symbol);
        $address_list[$i]['symbol'] = $c[$i]->symbol;
        $address_list[$i]['address'] = $wallet_tmp[0]->address;
    }
}*/

$page = $_GET['page']>1 ? $_GET['page'] : 1;
$txnid = $_GET['txnid']>1 ? $_GET['txnid'] : 0;
$rows = 10;
$loop_total = 0;

if($symbol_transaction=='KRW') {
    $txns = $tradeapi->get_wallet_txn_list($symbol_transaction, $userno, $page, $rows, $txnid);
    if($txns !== null && count($txns) != 0) {
        $loop_total = $txns[0]->tot_cnt; // 전체 row 수
        for($i=0; $i<count($txns); $i++) {
            $txns[$i]->direction = $txns[$i]->direction=='in' ? __('입금') : __('출금');
            $txns[$i]->date_str = date("m-d H:i", $txns[$i]->regtime);
            $txns[$i]->status_txt = $txns[$i]->status=='O' ? __('대기중') : ($txns[$i]->status=='P' ? __('처리중') : ($txns[$i]->status=='C' ? __('취소') : __('완료')));
            $txns[$i]->from_address_txt = iconv_strlen($txns[$i]->from_address,'UTF-8') > 10 ? iconv_substr($txns[$i]->from_address, 0, 10,'UTF-8').'...' : $txns[$i]->from_address;
            $txns[$i]->to_address_txt = iconv_strlen($txns[$i]->to_address,'UTF-8') > 10 ? iconv_substr($txns[$i]->to_address, 0, 10,'UTF-8').'...' : $txns[$i]->to_address;
            $txns[$i]->amount = real_number_format($txns[$i]->amount);
            $txns[$i]->fee = real_number_format($txns[$i]->fee);
        }
    }
} else {
    $txns = $tradeapi->get_order_list($userno, 'all', $symbol_transaction, 'KRW', $page, $rows, $txnid); // 변수명이 txnid 이지만  orderid 값을 사용합니다.
    if($txns !== null && count($txns) != 0) {
        $loop_total = $txns[0]->tot_cnt; // 전체 row 수
        for($i=0; $i<count($txns); $i++) {
            $txns[$i]->txnid = $txns[$i]->orderid;
            $txns[$i]->direction = $txns[$i]->trading_type=='buy' ? __('구매') : __('판매');
            $txns[$i]->date_str = date("m-d H:i", $txns[$i]->time_order);
            $txns[$i]->status_txt = $txns[$i]->status=='open' ? __('대기중') : ($txns[$i]->status=='trading' ? __('처리중') : ($txns[$i]->status=='cancel' ? __('취소') : __('완료')));
            $txns[$i]->amount = real_number_format($txns[$i]->amount);
            $txns[$i]->volume = real_number_format($txns[$i]->volume);
            $txns[$i]->volume_remain = real_number_format($txns[$i]->volume_remain);
            $txns[$i]->price = real_number_format($txns[$i]->price);
            // $txns[$i]->from_address_txt = iconv_strlen($txns[$i]->from_address,'UTF-8') > 10 ? iconv_substr($txns[$i]->from_address, 0, 10,'UTF-8').'...' : $txns[$i]->from_address;
            // $txns[$i]->to_address_txt = iconv_strlen($txns[$i]->to_address,'UTF-8') > 10 ? iconv_substr($txns[$i]->to_address, 0, 10,'UTF-8').'...' : $txns[$i]->to_address;
        }
    }
}

$interface->tpl->assign("transactionList", $txns);

// $interface->tpl->assign("WALLET_BTC_AMOUNT", $btcService->_WALLET_BTC_AMOUNT);
// $interface->tpl->assign("WALLET_KRW_AMOUNT", $btcService->_WALLET_KRW_AMOUNT);
// $interface->tpl->assign("WALLET_BTC_ADDRESS", $btcService->_WALLET_BTC_ADDRESS);

// $interface->tpl->assign("BTC_TRADING_RATE", $btcService->_BTC_TRADING_RATE);
// $interface->tpl->assign("SITE_TRADING_RATE", $btcService->_SITE_TRADING_RATE);
// $interface->tpl->assign("WITHDRAWAL_FEE", $btcService->_WITHDRAWAL_FEE);

$interface->tpl->assign("USER_NAME", $_SESSION["USER_NAME"]);
// $interface->tpl->assign("USER_MOBILE", $btcService->_USER_INFO["mobile"]);

// $loop_user_trade = $btcService->btcViewDao->selectUserTradeAll($_SESSION["USER_ID"]);
// $interface->tpl->assign("loop_user_trade", $loop_user_trade);

// $loop_user_complete_trade = $btcService->btcViewDao->selectUserCompleteTrade($_SESSION["USER_ID"]);
// $interface->tpl->assign("loop_user_complete_trade", $loop_user_complete_trade);

// $pageType = isset($_GET['type']) ? $_GET['type'] : "";


// $_page = intval($_GET['page']);
// $_page = empty($_page) ? 1 : $_page;
// $_cnt_rows = 20;

// if ($pageType == 'trade_history') {

	// $loop_total = $btcService->btcViewDao->selectUserCompleteTrade2($_SESSION["USER_ID"], true);
	// $loop_user_trade = $btcService->btcViewDao->selectUserCompleteTrade2($_SESSION["USER_ID"], false, $_page, $_cnt_rows);
	// $interface->tpl->assign("loop_user_trade", $loop_user_trade);
	//$interface->tpl->assign("navi_page", genPaging($loop_total, $_page, '&type=trade_history&gnb_code=BV55632&cate_code=TP12873 ', $_cnt_rows));
	// $interface->layout['js_tpl_main'] = 'account/myinfo_trade_history.html';

// } else if ($pageType == 'btc_history') {

	// $loop_total = $btcService->btcViewDao->selectUserCompleteTrade3($_SESSION["USER_ID"], true);
	// $loop_user_trade = $btcService->btcViewDao->selectUserCompleteTrade3($_SESSION["USER_ID"], false, $_page, $_cnt_rows);
	// $interface->tpl->assign("loop_user_trade", $loop_user_trade);
	//$interface->tpl->assign("navi_page", genPaging($loop_total, $_page, '&type=btc_history&gnb_code=BV55632&cate_code=TP12873', $_cnt_rows));
	// $interface->layout['js_tpl_main'] = 'account/myinfo_btc_history.html';

// } else {

	// if ($_GET['cate_code'] == 'TP12873') { // 나의계정 > 개인거래내역 > 종합내역
		// $loop_total = $btcService->btcViewDao->selectUserHistoryAll($_SESSION["USER_ID"], true);
		// $loop_user_trade = $btcService->btcViewDao->selectUserHistoryAll($_SESSION["USER_ID"], false, $_page, $_cnt_rows);
	// }
	// if ($_GET['cate_code'] == 'JG40489' || empty($_GET['cate_code'])) { // 매수매도 > 체결내역
	// 	$loop_total = $btcService->btcViewDao->selectUserCompleteTrade2($_SESSION["USER_ID"], true);
	// 	$loop_user_trade = $btcService->btcViewDao->selectUserCompleteTrade2($_SESSION["USER_ID"], false, $_page, $_cnt_rows);
	// }
	// $interface->tpl->assign("loop_user_trade", $loop_user_trade);
	$interface->tpl->assign("navi_page", genPaging($loop_total, $page, '', $rows, 10, 'page', 'center'));
	$interface->layout['js_tpl_main'] = 'account/myinfo.html';
// }

$print = 'layout';
$interface->display($print);

$dbcon->close();
?>