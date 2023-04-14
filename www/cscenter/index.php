<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_user.php';

function getNavi()
{
	$ret= array();
	return $ret;
}


if(empty($_REQUEST['code'])) {
	$_REQUEST['code'] = "main";
}

$interface = new ControlUserInteface();
$interface->tpl = &$tpl;
$interface->setBasicInterface('user','a3');
$interface->addNavi(getNavi());
$interface->addCss('/template/'.getSiteCode().'/'.$tpl->skin.'/cscenter/css/cscenter.css');

if(empty($_GET['code'])) {
	$interface->layout['js_tpl_main'] = 'cscenter/main.html';
}
else {
	//회사소개
	if($_GET['code'] == 'company') {
		$interface->layout['js_tpl_main'] = 'cscenter/company.html';
	}
	//이용안내
	//else if($_GET['code'] == 'information') {
	//	$interface->layout['js_tpl_main'] = 'cscenter/information.html';
	//}
	//이용약관
	else if($_GET['code'] == 'agreement') {
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['tool'] = 'select_one';
		$query['fields'] = 'clause_agreement';
		$clause_agreement = $dbcon->query($query,__FILE__,__LINE__);
		$tpl->assign('clause_agreement',$clause_agreement);
		$interface->layout['js_tpl_main'] = 'cscenter/agreement.html';
	}
	//개인정보취급방침
	else if($_GET['code'] == 'private') {
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['tool'] = 'select_one';
		$query['fields'] = 'clause_private';
		$clause_private = $dbcon->query($query,__FILE__,__LINE__);
		$tpl->assign('clause_private',$clause_private);
		$lang = $lang ? $lang : 'kr';
		$interface->layout['js_tpl_main'] = 'cscenter/private_'.$lang.'.html';
	}
	//운영규칙
	else if($_GET['code'] == 'information') {
		$query = array();
		$query['table_name'] = 'js_config_basic';
		$query['tool'] = 'select';
		$query['fields'] = 'clause_information01,clause_information02,clause_information03';
		$result = $dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$tpl->assign('loop_info',$loop);
		$interface->layout['js_tpl_main'] = 'cscenter/information.html';
	}
	//이메일 무단수집거부
	else if($_GET['code'] == 'refusal') {
		$interface->layout['js_tpl_main'] = 'cscenter/refusal.html';
	}
	//약도
	else if($_GET['code'] == 'map') {
		$interface->layout['js_tpl_left'] = 'left_menu.html';
		$interface->layout['js_tpl_main'] = 'cscenter/map.html';
	}
	//사이트맵
	else if($_GET['code'] == 'sitemap') {
		$interface->layout['js_tpl_main'] = 'cscenter/sitemap.html';
	}
	//현금영수증 신청
	else if($_GET['code'] == 'bill') {
		$interface->layout['js_tpl_main'] = 'cscenter/bill.html';
	}
	//계좌 안내
	else if($_GET['code'] == 'account') {
		$interface->layout['js_tpl_main'] = 'cscenter/account.html';
	}
	//배송안내
	else if($_GET['code'] == 'delivery') {
		$interface->layout['js_tpl_main'] = 'cscenter/delivery.html';
	}
	else if($_GET['code'] == 'pipe') {
		$interface->layout['js_tpl_main'] = 'cscenter/pipe.html';
	}
	//cs센터 메인
	else { //$_GET['code'] == 'main'
		$interface->layout['js_tpl_main'] = 'cscenter/main.html';
	}
}

$print = 'layout';
$interface->display($print);
$dbcon->close();
?>