<?php
/*--------------------------------------------
Date : 2018-04-27
Author : Danny Hwang, Kenny Han
comment : SmartCoin Index
--------------------------------------------*/
include_once '../lib/common_trade.php';
include_once './trading_class.php';
include_once '../member/member_class.php';

function getNavi()
{
	$ret = array();
	return $ret;
}

$js = new Trading($tpl);
// 조회용 DB 사용.
$dbcon = connect_db_slave();
$js->dbcon = &$dbcon;
$js->json = &$json;

$js_member = new Member($tpl);
$js_member->dbcon = &$dbcon;
$js_member->json = &$json;

if($_POST['pg_mode'] == 'getanalysis') {
    // ajaxCheckUser();
	// $js->loopGetanalysis();
} else {

    //checkUser();
    //checkUserTrade();

    $interface = new ControlUserInteface();
    $interface->tpl = &$tpl;
    $interface->setBasicInterface('user','a3');
    $interface->addNavi(getNavi());
    // $interface->addCss('/template/'.getSiteCode().'/'.$user_skin.'/account/css/account.css');
    $interface->addCss('https://cdn.jsdelivr.net/npm/cryptocoins-icons@2.7.0/webfont/cryptocoins.css');

    // $interface->layout['js_tpl_left'] = 'menu.html';

    // get currency
    $currency_codes = $js->getTradableCurrencyCode();
    $currency_codes = is_array($currency_codes) ? $currency_codes : array();
    $tpl->assign('currency_codes',$currency_codes);

    if(preg_match('/^(wallet|deposit|exchange|withdrawal)$/',$_GET['pg_mode'])) {
        $interface->tpl->assign('active_trade_menu',false);
        checkUser();

        if(preg_match('/^(wallet)$/',$_GET['pg_mode'])) {
            $interface->addScript('/template/'.getSiteCode().'/trade/js/'.$_GET['pg_mode'].'.js');

            $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade-set.js?v='.__REACT_VERSION__);
            $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.2.chunk.js?v='.__REACT_VERSION__);
            $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.main.chunk.js?v='.__REACT_VERSION__);

        } else if(preg_match('/^(deposit|withdrawal)$/',$_GET['pg_mode'])) {
            $row = $js_member->editForm();
            $access_level = 4;
            // var_dump($row['permission_lv']);exit;
            if($_GET['pg_mode'] == 'deposit') {$access_level = 3;}
            if($row['permission_lv'] >= $access_level) {
                $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade-set.js?v='.__REACT_VERSION__);
                $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.2.chunk.js?v='.__REACT_VERSION__);
                $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.main.chunk.js?v='.__REACT_VERSION__);
            } else {
                // 인증 페이지로 이동
                replaceGo('/certification','');//___('main','withdrawal0')
                // $interface->addScript('/template/'.getSiteCode().'/script/plug_in/form/jquery.form.js');
                // $interface->addScript('/template/'.getSiteCode().'/script/Javascript-Load-Image/load-image.all.min.js');
                // $interface->addScript('/template/'.getSiteCode().'/smc/js/certification.js');
            }
        } else {
            $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade-set.js?v='.__REACT_VERSION__);
            $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.2.chunk.js?v='.__REACT_VERSION__);
            $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.main.chunk.js?v='.__REACT_VERSION__);
        }
        $interface->layout['js_tpl_main'] = 'trading/'.$_GET['pg_mode'].'.html';
    } else if(in_array(strtoupper($_GET['pg_mode']),$currency_codes)) {
        $symbol = $_GET['pg_mode'];
        $exchange = $_GET['exchange'] ? $_GET['exchange'] : '';
        $item_info = $js->get_trade_item_info($symbol);
        $item_info = array_merge($item_info, $js->get_trade_price_info($symbol, $exchange)); // 매매 용 기준가격, min 가격, max 가격 추가.
        // var_dump($item_info); exit;
        $interface->tpl->assign($item_info); 
        $interface->tpl->assign($item_info); 
        $interface->tpl->assign('manager_userid', $item_info['manager_userid']); 
        
        $login_user_wallet = $js->get_user_wallet($_SESSION['USER_NO'], $symbol);
        $interface->tpl->assign('login_user_wallet', $login_user_wallet);
        $login_user_wallet_krw = $js->get_user_wallet($_SESSION['USER_NO'], 'KRW');
        $interface->tpl->assign('login_user_wallet_krw', $login_user_wallet_krw);

        $interface->tpl->assign('active_trade_menu',true);
        // $interface->addScript('/template/'.getSiteCode().'/trade/js/trade.js');
        
        // $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade-set.js?v='.__REACT_VERSION__);
        // $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.2.chunk.js?v='.__REACT_VERSION__);
        // $interface->addScript('/template/'.getSiteCode().'/smc/js/kmcsetrade.main.chunk.js?v='.__REACT_VERSION__);

        // $interface->layout['js_tpl_main'] = 'trading/chart.html';

        // $interface->addScript('/template/'.getSiteCode().'/trade/js/jquery-3.1.1.min.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/plugins/flot/jquery.flot.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/plugins/flot/jquery.flot.tooltip.min.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/plugins/flot/jquery.flot.resize.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/plugins/flot/jquery.flot.pie.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/plugins/flot/jquery.flot.time.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/chart-moving.js');

        $interface->addScript('/template/'.getSiteCode().'/trade/js/trade-list.js'); // analysis.js -> trade-list.js 파일명변경
        $interface->addScript('/template/'.getSiteCode().'/trade/js/finance_chart.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/comment.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/news.js');
        $interface->addScript('/template/'.getSiteCode().'/trade/js/notice.js');
        // $interface->addScript('/template/'.getSiteCode().'/trade/js/chart01.js');
        // $interface->addScript('/template/'.getSiteCode().'/trade/js/chart02.js');
        // $interface->addScript('/template/'.getSiteCode().'/trade/js/chart03.js');

        $interface->addCss('/template/'.getSiteCode().'/trade/css/scc.css');
        


        $interface->layout['js_tpl_main'] = 'trading/company_info.html';
        $interface->layout['js_tpl_main_sub'] = 'trading/company_trade.html';

    } else if($_GET['pg_mode'] == 'total') {
        // checkUser();

        $interface->addScript('/template/'.getSiteCode().'/trade/js/total.js');
        $interface->addCss('/template/'.getSiteCode().'/trade/css/scc.css');

        $interface->layout['js_tpl_main'] = 'trading/trade_all.html';

    } else if($_GET['pg_mode'] == 'order') {
        checkUser();
        $interface->addScript('/template/'.getSiteCode().'/smc/js/analysis-react.js');
        $interface->layout['js_tpl_main'] = 'trading/trade_order.html';
    } else if($_GET['pg_mode'] == 'analysis') {

        $interface->tpl->assign('active_trade_menu',false);
        checkUser();
        
		if(trim($_GET['start_date'])=='') { $_GET['start_date'] = date('Y-m-01'); } // 이달 1일부터 시작. 30일전부터 시작하려면 ... time() - 60*60*24*30
		if(trim($_GET['end_date'])=='') { $_GET['end_date'] = date('Y-m-d'); }
	
        $interface->addScript('/template/'.getSiteCode().'/trade/js/analysis.js');
        $interface->addCss('/template/'.getSiteCode().'/trade/css/scc.css');
        
        $interface->layout['js_tpl_main'] = 'trading/analysis.html';
        $interface->layout['js_tpl_main_sub'] = 'trading/trade_analysis.html';

    } else if($_GET['pg_mode'] == 'cash') {
        checkUser();
        $interface->addScript('/template/'.getSiteCode().'/smc/js/analysis-react.js');
        $interface->layout['js_tpl_main'] = 'trading/trade_cash.html';
    } else if($_GET['pg_mode'] == 'coin') {
        checkUser();
        $interface->addScript('/template/'.getSiteCode().'/smc/js/analysis-react.js');
        $interface->layout['js_tpl_main'] = 'trading/trade_coin.html';
    } else if($_GET['pg_mode'] == 'history') {
        checkUser();
        $interface->addScript('/template/'.getSiteCode().'/smc/js/analysis-react.js');
        $interface->layout['js_tpl_main'] = 'trading/trade_history.html';
    } else  {
        $interface->tpl->assign('active_trade_menu',true);
        $interface->layout['js_tpl_main'] = 'trading/404.html';
    }

    $print = 'layout';
    $interface->display($print);
}

$dbcon->close();
