<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
include_once '../lib/basic_config.php';

$connect	= mysqli_connect($db_host,$db_user,$db_pass);
mysqli_select_db($db_name,$connect);

$path= $_SERVER["DOCUMENT_ROOT"]."/data/backup/";
$filename= "mysqli_dump_".date("Ymd").".sql";
$result = @mysqli_query("show variables",$connect);
while($row = mysqli_fetch_row($result)) {
	if($row[0] == "basedir") {
		$bindir = $row[1]."bin/";
	}
}
mysqli_close($connect);
passthru($bindir."mysqldump --user=".$db_user." --password=".$db_pass." ".$db_name." > ".$path.$filename);
passthru("gzip ".$path.$filename); 
$filename.=".gz";
if (is_file($path.$filename) && file_exists($path.$filename))  {
	if(eregi("(MSIE 5.5|MSIE 6.0)", $HTTP_USER_AGENT)) { 
		header("Content-Type: application/octet-stream");
		header("Content-Length: ".filesize($path.$filename));
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-Transfer-Encoding: binary");
		header("Pragma: no-cache");
		header("Expires: 0"); 
	}
	else {
		header("Content-type: file/unknown");
		header("Content-Length: ".filesize($path.$filename));
		header("Content-Disposition: attachment; filename=".$filename);
		header("Content-Description: PHP3 Generated Data");
		header("Pragma: no-cache");
		header("Expires: 0");
	}
}
$fp = fopen($path.$filename, "rb"); 
if (!fpassthru($fp)) {
	fclose($fp);
}
unlink($path.$filename);

?>