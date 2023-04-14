<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

include_once $_SERVER["DOCUMENT_ROOT"].'/lib/basic_config.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/db_class.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/lib/PHPExcel.php';
date_default_timezone_set('Asia/Seoul');

$dbcon = new DB($db_host,$db_name,$db_user,$db_pass,$db_charset);

$objPHPExcel = new PHPExcel();
$objPHPExcelSheetFirst = $objPHPExcel->setActiveSheetIndex(0);

$arr = array(
	'userid'=>'아이디',
	'name'=>'이름',
	'nickname'=>'닉네임',
	'sid_a'=>'주민번호',
	'sid_b'=>'주민번호',
	'phone'=>'전화번호',
	'mobile'=>'휴대전화',
	'email'=>'이메일',
	'zipcode'=>'우편번호',
	'address_a'=>'주소',
	'address_b'=>'상세주소',
	'bool_email'=>'메일수신여부',
	'bool_sms'=>'문자수신여부',
	'bool_lunar'=>'음력',
	'birthday'=>'생일',
	'level_code'=>'레벨코드',
	'regdate'=>'가입일');

$arr_val = array_values($arr);
$arr_key = array_keys($arr);

for ($i = 0,$alpha = 'A'; $i < sizeof($arr_val) ; $i++,$alpha++) {
	$objPHPExcelSheetFirst->getColumnDimension($alpha)->setAutoSize(true);
}
for ($i = 0,$alpha = 'A'; $i < sizeof($arr_val) ; $i++,$alpha++) {
	$rn = $i+1;
	$objPHPExcelSheetFirst->setCellValue($alpha.'1',$arr_val[$i]);
}
for ($i = 0,$alpha = 'A'; $i < sizeof($arr_key) ; $i++,$alpha++) {
	$rn = $i+1;
	$objPHPExcelSheetFirst->setCellValue($alpha.'2',$arr_key[$i]);
}

$query = array();
$query['table_name'] = 'js_member';
$query['tool'] = 'select';
$query['where'] = 'order by regdate desc';
$result = $dbcon->query($query,__FILE__,__LINE__);

for ($i = 3; $row = mysqli_fetch_assoc($result) ; $i++) {
	$objPHPExcelSheetFirst->setCellValue("A{$i}", $row['userid'])
		->setCellValue("B{$i}", $row['name'])
		->setCellValue("C{$i}", $row['nickname'])
		->setCellValue("D{$i}", $row['sid_a'])
		->setCellValue("E{$i}", $row['sid_b'])
		->setCellValue("F{$i}", $row['phone'])
		->setCellValue("G{$i}", $row['mobile'])
		->setCellValue("H{$i}", $row['email'])
		->setCellValue("I{$i}", $row['zipcode'])
		->setCellValue("J{$i}", $row['address_a'])
		->setCellValue("K{$i}", $row['address_b'])
		->setCellValue("L{$i}", $row['bool_email'])
		->setCellValue("M{$i}", $row['bool_sms'])
		->setCellValue("N{$i}", $row['bool_lunar'])
		->setCellValue("O{$i}", $row['birthday'])
		->setCellValue("P{$i}", $row['level_code'])
		->setCellValue("Q{$i}", $row['regdate']);
}

$dbcon->close();

$objPHPExcel->getActiveSheet()->setTitle('회원목록');

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="member.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

exit;
?>