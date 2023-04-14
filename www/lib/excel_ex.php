<?php

/**

* MS-Excel stream handler

* Excel export example

* @author      Ignatius Teo            <ignatius@act28.com>

* @copyright   (C)2004 act28.com       <http://act28.com>

* @date        21 Oct 2004

*/

require_once "excel.php";

//$export_file = "xlsfile://example.xls";//저장하고싶은 파일이름, 앞에 꼭 xlsfile:// 를 붙여야한다.
$export_file = $_SERVER['DOCUMENT_ROOT']."/data/attach/example.xls";//저장하고싶은 파일이름, 앞에 꼭 xlsfile:// 를 붙여야한다.

$fp = fopen($export_file, "wb");

if (!is_resource($fp))

{

    die("Cannot open $export_file");

}

// 엑셀에 들어갈 내용을 배열로 만든다.

$assoc = array(

    array("Sales Person" => "엑셀된다 만만세", "Q1" => "$3255", "Q2" => "$3167", "Q3" => 3245, "Q4" => 3943),

    array("Sales Person" => "Jim Brown", "Q1" => "$2580", "Q2" => "$2677", "Q3" => 3225, "Q4" => 3410),

    array("Sales Person" => "John Hancock", "Q1" => "$9367", "Q2" => "$9875", "Q3" => 9544, "Q4" => 10255),

);

fwrite($fp, serialize($assoc));

fclose($fp);

//엑셀파일로 다운로드

$export_file = "example.xls";//다운로드할 파일 이름

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");

header ("Cache-Control: no-cache, must-revalidate");

header ("Pragma: no-cache");

header ("Content-type: application/x-msexcel");

header ("Content-Disposition: attachment; filename=\"" . basename($export_file) . "\"" );

header ("Content-Description: PHP/INTERBASE Generated Data" );

readfile($export_file);

exit;

?>