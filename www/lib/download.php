<?php
/*--------------------------------------------
Date : 2011-12-16
Author : Danny Hwang
comment : 
--------------------------------------------*/
/*--------------------------------------------

--------------------------------------------*/
$file_name = $_GET['file_name'];
$file_path = $_GET['file_path'];
$file = $_SERVER['DOCUMENT_ROOT'].'/data/'.$file_path.'/'.$file_name;


if(!empty($_GET['file_name_src'])) {
	$file_name_src = base64_decode($_GET['file_name_src']);
	$file_name_src = iconv("UTF-8", "EUC-KR", $file_name_src);
	$dn_file = trim($file_name_src);
}
else {
	$dn_file = trim($file_name);
}

if(is_file($file)) {
	$file_size = filesize($file);
	if(eregi("(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0|MSIE 7.0)", $_SERVER['HTTP_USER_AGENT'])){
		//header("Content-Type: application/octet-stream"); 
		header("Content-Type: doesn/matter");
		//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		Header("Content-Length: ".$file_size);
		header("Content-Disposition: attachment; filename=".$dn_file);
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	else {
		//Header("Content-type: application/download"); 
		Header("Content-type: file/unknown");
		Header("Content-Disposition: attachment; filename=".$dn_file);
		Header("Content-Transfer-Encoding: binary");
		Header("Content-Length: ".$file_size);
		Header("Content-Description: PHP4 Generated Data");
		header("Pragma: no-cache");
		header("Expires: 0");
	}

	//echo file_get_contents($file);
	//flush();

	$fp = fopen($file, "r");
	if (!fpassthru($fp)) { fclose($fp); }
}

?>