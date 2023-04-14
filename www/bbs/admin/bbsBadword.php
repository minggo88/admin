<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../../lib/common_admin.php';

function getNavi()
{
	$ret = array(
		'커뮤니티'=>'/bbs/admin/bbsSetup.php',
		'<span class="link">욕글 관리</span>'=>'/bbs/admin/bbsBadword.php'
	);
	return $ret;
}

class BadWord extends BASIC
{
	function BadWord(&$tpl)
	{
		$config = array();
		$config['table_name'] = 'js_badword';
		$this->BASIC($config,$tpl);
	}

	function lists()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'select';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		while($row = mysqli_fetch_assoc($result)) {
			echo '<input type="checkbox" name="idxs[]" value="'.$row['idx'].'" />'.$row['badword'];
		}
	}

	#수정함수
	function write()
	{
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'insert';
		$query['fields'] = 'badword=\''.$_POST['badword'].'\'';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		if($result) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}

	function del()
	{
		$ret = TRUE;
		$query = array();
		$query['table_name'] = $this->config['table_name'];
		$query['tool'] = 'delete';
		foreach($_POST['idxs'] as $key=>$val) {
			$query['where'] = 'where idx=\''.$val.'\'';
			$ret = $this->dbcon->query($query,__FILE__,__LINE__);
		}
		if($ret) {
			jsonMsg(1);
		}
		else {
			jsonMsg(0);
		}
	}
}

$js = new BadWord($tpl);
$js->dbcon = &$dbcon;
$js->json = &$json;

if($_POST['pg_mode'] == 'write') {
	ajaxCheckAdmin();
	$js->write();
}
else if($_POST['pg_mode'] == 'del') {
	ajaxCheckAdmin();
	$js->del();
}
else if($_GET['pg_mode'] == 'list') {
	loadCheckAdmin();
	$js->lists();
}
else {
	checkAdmin();

	$interface = new ControlUserInteface();
	$interface->tpl = &$tpl;
	$interface->setBasicInterface('admin');
	$interface->addNavi(getNavi());
	$tpl->assign('cur_page','comm03');
	$interface->layout['js_tpl_left'] = 'menu.html?community';
	$interface->addCss('/template/admin/bbs/css/bbsSetup.css');
	$interface->setPlugIn('form');
	$interface->layout['js_tpl_main'] = 'bbs/badword.html';

	$print = 'layout';

	$interface->display();

}
$dbcon->close();
?>