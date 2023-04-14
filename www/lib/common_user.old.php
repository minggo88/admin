<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/common.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/interface_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/tpl/Template_.class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/main_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/bitcoin/BtcService.php';

$tpl = new Template_;
$tpl->template_dir = ROOT_DIR.'/template';
$tpl->compile_dir = ROOT_DIR.'/../compile';
$tpl->prefilter = 'adjustPath';
//$tpl->postfilter = 'arrangeSpace';
//$tpl->compile_check = true; 

if(empty($_SESSION['USER_LEVEL'])) {
	$query = array();
	$query['table_name'] = 'js_member_level';
	$query['tool'] = 'select_one';
	$query['fields'] = 'level_code';
	$query['where'] = 'order by ranking desc limit 1';
	$_SESSION['USER_LEVEL'] = $dbcon->query($query,__FILE__,__LINE__);
}

if(empty($_SESSION['USER_CART'])) {
	$_SESSION['USER_CART'] = getCode();
}

$query = array();
$query['table_name'] = 'js_config_basic';
$query['tool'] = 'row';
$query['fields'] = '
	license_company,
	license_ceo,
	license_no,
	license_sale,
	license_address,
	license_uptae,
	license_jongmok,
	license_opendate,
	shop_name,
	shop_ename,
	shop_url,
	shop_admin_email,
	shop_phone,
	shop_fax,
	shop_private_clerk,
	shop_clerk_email,
	shop_address,
	site_title,
	site_keyword,
	site_description,
	bool_exchange,
	exchange_period,
	exchange_order,
	bool_back,
	back_period,
	img_logo,
	img_sub_logo,
	img_footer_logo,
	img_map,
	bool_ssl,
	ssl_port';
$config_basic = $dbcon->query($query,__FILE__,__LINE__);
if($config_basic['bool_ssl']>0) {
	if(empty($config_basic['ssl_port'])) {
		$config_basic['ssl_port'] = '';
	}
	else {
		$config_basic['ssl_port'] = 'https://'.$_SERVER["SERVER_NAME"].':'.$config_basic['ssl_port'];
		// redirect http to https
		if(!isset($_SERVER["HTTPS"])) {  
			// 이메일 인증 주소 잘못 받은 경우도 있어서 이메일 인증주소는 따로 처리함.
			if($_GET['pg_mode']=='confirm_email') {
				// http://www.smcc.io/member/memberJoin.php?pg_mode=confirm_email&userid=benant@nate.com
				header('Location: '.$config_basic['ssl_port'].'/member/memberJoin.php?pg_mode=confirm_email&userid='.$_GET['userid']); exit;
			} else {
				header('Location: '.$config_basic['ssl_port']);
			}
		}
	}
}
else {
		$config_basic['ssl_port'] = '';
}
$tpl->assign('config_basic',$config_basic);


$query = array();
$query['table_name'] = 'js_shop_category';
$query['tool'] = 'select';
$query['fields'] = 'category_code, category_path, depth, category_name';
$query['where'] = 'where bool_use=1 && depth=1 order by ranking asc';
$result = $dbcon->query($query,__FILE__,__LINE__);
$loop = array();
for($i = 0 ; $row = mysqli_fetch_assoc($result) ; $i++) {
	$loop[$i] = $row;
	//2단계 레벨 카테고리
	$query['where'] = 'where bool_use=1 && depth=2 && category_path like \''.$row['category_code'].'____________\' order by ranking asc';
	$result2 = $dbcon->query($query,__FILE__,__LINE__);
	$loop2 = &$loop[$i]['loop_category2'];
	for($j = 0 ; $row2 = mysqli_fetch_assoc($result2) ; $j++ ) {
		$loop2[$j] = $row2;
		//3단계 레벨 카테고리
		$query['where'] = 'where bool_use=1 && depth=3 && category_path like \''.$row['category_code'].$row2['category_code'].'________\' order by ranking asc';
		$result3 = $dbcon->query($query,__FILE__,__LINE__);
		$loop3 = &$loop2[$j]['loop_category3'];
		for($k = 0 ; $row3 = mysqli_fetch_assoc($result3) ; $k++) {
			$loop3[$k] = $row3;
			//4단계 레벨 카테고리
			$query['where'] = 'where bool_use=1 && depth=4 && category_path like \''.$row['category_code'].$row2['category_code'].$row3['category_code'].'____\' order by ranking asc';
			$result4 = $dbcon->query($query,__FILE__,__LINE__);
			$loop4 = &$loop3[$k]['loop_category4'];
			while($row4 = mysqli_fetch_assoc($result4)) {
				$loop4[] = $row4;
			}
		}
	}
}

$_SESSION['LOOP_CATEGORY'] = $loop;
$tpl->assign('loop_category',$loop);


if($_SERVER["HTTP_HOST"]=="m.smcc.io") {
		$user_skin = 'user';
} else {
		$user_skin = 'user';
}

$bool_mobile = 0;

$arr_browser = array ("iPhone","iPad","iPod","IEMobile","Mobile","lgtelecom","PPC");
for($indexi = 0 ; $indexi < count($arr_browser) ; $indexi++) {
	if(strpos($_SERVER['HTTP_USER_AGENT'],$arr_browser[$indexi]) == true) {

		if(empty($_SESSION['mdv']) || $_REQUEST['mdv']=='mobile' ) {
			$_SESSION['mdv'] = 'mobile';
		}

		if($_REQUEST['mdv']=='pc' || $_SESSION['mdv'] == 'pc') {
			$_SESSION['mdv'] = 'pc';
			$bool_mobile = 0;
			$user_skin = 'user';
 		} else {
			$_SESSION['mdv'] = 'mobile';
			$bool_mobile = 1;
			$user_skin = 'user';
		}
		
		//exit;
	}
}

define('BOOL_MOBILE',$bool_mobile);


$tpl->skin = $user_skin;

function menuLink($kinds_contents,$gnb_code,$cate_code,$contents_code='',$link_url='')
{
	$arr_link = array(
		"bbs"=>"/bbs/bbs.php?gnb_code=", //게시판
		"cts"=>"/contents/contents.php?gnb_code=", //컨텐츠
		"faq"=>'/cscenter/faq.php?gnb_code=',//FAQ
		"history"=>'/history/history.php?gnb_code=', //연혁
		"request"=>'/curriculum/ent.php?gnb_code=', //신청
		"manual"=>'' //
	);

	if($kinds_contents == 'bbs') {
		$ret = $arr_link[$kinds_contents].$gnb_code.'&cate_code='.$cate_code.'&bbscode='.$contents_code;
	}
	else if ($kinds_contents == 'cts') {
		$ret = $arr_link[$kinds_contents].$gnb_code.'&cate_code='.$cate_code.'&cts_code='.$contents_code;
	}
	else if ($kinds_contents == 'manual') {
		$ret = $link_url.'&gnb_code='.$gnb_code.'&cate_code='.$cate_code;
	}
	else {
		$ret = $arr_link[$kinds_contents].$gnb_code.'&cate_code='.$cate_code;
	}

	return $ret;
}

if(empty($_GET['gnb_code']) && isset($_GET['bbscode'])) {
	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'row';//select,select_one,select_affect,,count,insert,insert_idx,update,delete,drop
	$query['fields'] = 'cate_code, parent_code';
	$query['where'] = 'where contents_code=\''.$_GET['bbscode'].'\'';
	$row = $dbcon->query($query,__FILE__,__LINE__);
	$_GET['cate_code'] = $row['cate_code'];
	$_GET['gnb_code'] = $row['parent_code'];
}

$query = array();
$query['table_name'] = 'js_contents_category';
$query['tool'] = 'select';
$query['fields'] = 'cate_code, contents_name, link_name, kinds_contents, contents_code, depth, link_code';
$query['where'] = 'where depth=1 && bool_display=1 order by ranking asc';
$result = $dbcon->query($query,__FILE__,__LINE__);
$loop_gnb = array();
for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'row';
	$query['fields'] = 'cate_code, contents_name, link_name, kinds_contents, contents_code, link_url';
	$query['where'] = 'where cate_code=\''.$row['link_code'].'\'';
	$link_sub = $dbcon->query($query,__FILE__,__LINE__);
	$row['link_url'] = menuLink($link_sub['kinds_contents'],$row['cate_code'],$link_sub['cate_code'],$link_sub['contents_code'],$link_sub['link_url']);
	$loop_gnb[] = $row;

	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select';
	$query['fields'] = 'cate_code, parent_code, contents_name, link_name, kinds_contents, contents_code, link_url';
	$query['where'] = 'where parent_code=\''.$row['cate_code'].'\' order by ranking asc';
	$result2 = $dbcon->query($query,__FILE__,__LINE__);
	$loop_sub = &$loop_gnb[$i]['loop_sub'];
	while ($row2 = mysqli_fetch_assoc($result2)) {
		$row2['link_url'] = menuLink($row2['kinds_contents'],$row['cate_code'],$row2['cate_code'],$row2['contents_code'],$row2['link_url']);
		$loop_sub[] = $row2;
	}
}
$tpl->assign('loop_gnb',$loop_gnb);

if(!empty($_GET['gnb_code'])) {

	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select_one';
	$query['fields'] = 'contents_name';
	$query['where'] = 'where cate_code=\''.$_GET['gnb_code'].'\'';
	$left_title = $dbcon->query($query,__FILE__,__LINE__);
	$tpl->assign('left_title',$left_title);

	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select';
	$query['fields'] = 'cate_code, parent_code, contents_name, link_name, kinds_contents, contents_code, link_url,depth, ranking';
	$query['where'] = 'where parent_code=\''.$_GET['gnb_code'].'\' order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop_lnb = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$row['link_url'] = menuLink($row['kinds_contents'],$_GET['gnb_code'],$row['cate_code'],$row['contents_code'],$row['link_url']);
		$loop_lnb[] = $row;
	}
	$tpl->assign('loop_lnb',$loop_lnb);
}

//컨텐츠 타이틀
if(!empty($_GET['cate_code'])) {
	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select_one';
	$query['fields'] = 'contents_name';
	$query['where'] = 'where cate_code=\''.$_GET['cate_code'].'\'';
	$contents_name = $dbcon->query($query,__FILE__,__LINE__);
	$tpl->assign('contents_name',$contents_name);
}

//서브 타이틀 카피문구
if(!empty($_GET['gnb_code'])) {
	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'row';
	$query['fields'] = 'bg_img,title_copy';
	$query['where'] = 'where cate_code=\''.$_GET['gnb_code'].'\'';
	$row = $dbcon->query($query,__FILE__,__LINE__);
	$tpl->assign('bg_img',$row['bg_img']);
	$tpl->assign('title_copy',$row['title_copy']);
}

// 회원의 보유 btc, krw 정보를 셋팅함. $btcService 는 BtcService.php 에서 생성 됨.
$btcService->setMyWallet($_SESSION["USER_ID"]);
$btc_amount = $btcService->_WALLET_BTC_AMOUNT;
$krw_amount = $btcService->_WALLET_KRW_AMOUNT;
$tpl->assign("WALLET_BTC_AMOUNT", $btc_amount);
$tpl->assign("WALLET_KRW_AMOUNT", $krw_amount);
$tpl->assign("WALLET_BTC_ADDRESS", $btcService->_WALLET_BTC_ADDRESS);
// 회원의 보유 btc, krw 정보를 셋팅함. $btcService 는 BtcService.php 에서 생성 됨.


//		$loop_trade_buy = $btcService->btcViewDao->selectTradeBuy();
//		$interface->tpl->assign("loop_trade_buy", $loop_trade_buy);
//		$loop_trade_sell = $btcService->btcViewDao->selectTradeSell();
//		$interface->tpl->assign("loop_trade_sell", $loop_trade_sell);

		$_cnt_list = 7;
		$loop_trade_buy = $btcService->btcViewDao->selectTradeBuy($_cnt_list);
		$_cnt = count($loop_trade_buy);
		if($_cnt!=$_cnt_list) {
			$_tmp = array();
			for($i=0;$i<$_cnt_list;$i++) {
				if(empty($loop_trade_buy[$i])) {
					$_tmp[] = '';
				} else {
					$_tmp[] = $loop_trade_buy[$i];
				}
			}
			$loop_trade_buy = $_tmp;
		}
		$tpl->assign("loop_trade_buy", $loop_trade_buy);
		$loop_trade_sell = $btcService->btcViewDao->selectTradeSell($_cnt_list);
		$_cnt = count($loop_trade_sell);
		if($_cnt!=$_cnt_list) {
			$_tmp = array();
			for($i=0;$i<($_cnt_list - $_cnt);$i++) {
				$_tmp[] = '';
			}
			foreach($loop_trade_sell as $row) {
				$_tmp[] = $row;
			}
			$loop_trade_sell = $_tmp;
		}
		$tpl->assign("loop_trade_sell", $loop_trade_sell);


analysisVisitant();
?>