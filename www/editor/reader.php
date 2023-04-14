<?
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/
include_once '../lib/common.php';

$img_path = $_SERVER['DOCUMENT_ROOT'].'/data/editorThumb/';
$filter = "*.*";
$data = array();

if(is_dir($img_path))
{
    foreach (glob($img_path . $filter) as $filename) {
		$arr = array();
        if (is_dir($filename))
        {
            $arr['name'] = basename($filename);
            $arr['type'] = "d";
            $arr['size'] = "0";
        } else {
            $arr['name'] = basename($filename);
            $arr['type'] = "f";
            $arr['size'] = filesize($filename);
        }

        $data[] = $arr;
    }
 
	echo $json->encode($data);
}

?>