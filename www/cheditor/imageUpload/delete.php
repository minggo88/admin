<?php
var_dump($_POST); exit;
include_once '../../lib/common.php';
include_once './s3.php';
$S3 = new S3;

$r = false;
$filepath = 'https://kmcse.s3.ap-northeast-2.amazonaws.com/tmp/'.date('Ym').'/' .$_POST['filesrc'];
$r = $S3->delete_file_to_s3($filepath);
$filepath = sys_get_temp_dir().'/'.$_POST['filesrc'];
if (file_exists($filepath)) {
    unlink($filepath);
}

echo $r ? true : false;
?>