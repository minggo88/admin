<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/common_admin.php';

class AdminAuth
{
	function auth()
	{


		#아이디 존재여부 체크
		$qry['table_name'] = 'js_admin';
		$qry['tool'] = 'count';
		$qry['where'] = 'where adminid=\''.$_POST['adminid'].'\'';
		$cnt = $this->dbcon->query($qry,__FILE__,__LINE__);
        echo "run mod ", __API_RUNMODE__;
        if(!empty($cnt)) {
			$qry['tool'] = 'row';
			$qry['fields'] = 'adminid,adminpw,kind_admin,otpkey,otpuse,admin_name';
			$row = $this->dbcon->query($qry,__FILE__,__LINE__);
            echo "run mod ", __API_RUNMODE__;
			if(__API_RUNMODE__ == 'loc') {$row['otpuse']=0;}

			// otp
			if($row['otpuse']=='1') {
				include dirname(__file__) . "/../lib/GoogleAuthenticator.php";
				$ga = new PHPGangsta_GoogleAuthenticator();
				$c = $ga->getCode($row['otpkey']);
				$otp_check = $c===$_POST['otppw'];
				if(!$otp_check) {
					errMsg('OTP Key를 다시입력해주세요.');
				}
			}

			// var_dump($row['otpuse']=='1',  $c, $_POST['otppw'], $otp_check); exit;
			if(!strcmp($row['adminpw'],md5($_POST['adminpw']))) {
				$_SESSION['ADMIN_ID'] = $row['adminid'];
				$_SESSION['ADMIN_KIND'] = $row['kind_admin'];
				$_SESSION['ADMIN_NAME'] = $row['admin_name'];
				if(!empty($_SESSION['ADMIN_ID'])) {
					// write log
					$this->dbcon->query("INSERT INTO js_admin_log set regdate=NOW(), adminid='".$this->dbcon->escape($row['adminid'])."', pagename='LOGIN', ip='".$this->dbcon->escape($_SERVER['REMOTE_ADDR'])."', url_path='".$this->dbcon->escape($_SERVER['REQUEST_URI'])."', url_data='' ");

					$ret_url = empty($_POST['ret_url']) ? '/admin/index.php' : base64_decode($_POST['ret_url']);
					alertGo('',$ret_url);
				}
				else {
					errMsg(Lang::admin_3);
				}
			}
			else {
				errMsg(Lang::admin_2);
			}
		}
		else {
			errMsg(Lang::admin_1);
		}
	}

	function out()
	{
		unset($_SESSION['ADMIN_ID']);
		unset($_SESSION['ADMIN_KIND']);
		if(empty($_SESSION['ADMIN_ID']) && empty($_SESSION['ADMIN_KIND'])) {
			alertGo('','/admin/index.php');
		}
		else {
			errMsg(Lang::admin_4);
		}
	}
}

$js = new AdminAuth();
$js->dbcon = &$dbcon;

if($_POST['pg_mode'] == 'auth') {
	ajaxCheck();
	$js->auth();
}
else if($_GET['pg_mode'] == 'out') {
	$js->out();
}
else {

	$otpuse = $dbcon->query_unique_value("select otpuse from js_admin where otpuse=1 limit 1 ");


    if(__API_RUNMODE__ == 'loc') {$otpuse=0;}
	$tpl->assign('otpuse', $otpuse);

	$tpl->define('layout','auth_form.html');

	if(empty($_GET['ret_url'])) {
		$ret_url = base64_encode('/admin/index.php');
	}
	else {
		$ret_url =$_GET['ret_url'];
	}

	$tpl->assign('ret_url',$ret_url);
	$tpl->print_('layout');
}
$dbcon->close();
?>