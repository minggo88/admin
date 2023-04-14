<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/common.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/interface_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/tpl/Template_.class.php';

$tpl = new Template_;
$tpl->template_dir = ROOT_DIR.'/template/'.getSiteCode();
$tpl->compile_dir = ROOT_DIR.'/../compile/'.getSiteCode();
$tpl->prefilter = 'adjustPath';
$tpl->postfilter = 'arrangeSpace';
//$tpl->compile_check = false; 
$user_skin = 'staff';
$tpl->skin = $user_skin;

$config_basic = array();
$config_basic['bool_ssl'] =0;

/*
$query = array();
$query['table_name'] = 'js_curriculum';
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
		}
	}
}
$tpl->assign('loop_category',$loop);
*/

$query = array();
$query['table_name'] = 'js_course';
$query['tool'] = 'select';//select,select_one,select_affect,row,count,insert,insert_idx,update,delete,drop
$query['fields'] = 'course_code,course_name';


if(empty($_GET['category_code'])) {
	$query['where'] = 'where bool_finish=0';
}
else {
	$query['where'] = 'where bool_finish=0 && category_code=\''.$_GET['category_code'].'\'' ;
}
$result = $dbcon->query($query,__FILE__,__LINE__);
$loop = array();
while ($row = mysqli_fetch_assoc($result)) {
	$loop[] = $row;
}
$tpl->assign('loop_course',$loop);

?>