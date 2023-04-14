<?php
include_once '../../lib/common.php';
include_once './s3.php';
$S3 = new S3;
$save_result = $S3->save_file_to_s3($_FILES['file']);
$s3_url = '';
if($save_result[0]) {
	$s3_url = $save_result[0]['url'];
}
// var_dump($s3_url, $save_result);exit('1');

$tempfile = $_FILES['file']['tmp_name'];
$filename = $_FILES['file']['name'];
//$sessid   = $_POST['sessid'];

// $pos = strrpos($filename, '.');
// $ext = substr($filename, $pos, strlen($filename));
// $random_code = randCode();
// $random_name = strtolower($random_code.$ext);

// $savefile = EDITOR_DIR.'/'.$random_name;
// move_uploaded_file($tempfile, $savefile);

$filesize = filesize($tempfile);

$arr = array();

$rdata = sprintf('{"fileUrl": "%s", "filePath": "%s", "fileName": "%s", "fileSize": "%d" }',
	$s3_url,
	$tempfile,
	$filename,
	$filesize );

echo $rdata;

?>