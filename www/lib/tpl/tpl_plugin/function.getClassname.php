<?php

function getClassname($code)
{
	global $dbcon;
	$query = array();
	$query['table_name'] = 'js_config_classname';
	$query['tool'] = 'select_one';
	$query['fields'] = 'class_title';
	$query['where'] = 'where class_name=\''.$code.'\'';
	$class_title = $dbcon->query($query,__FILE__,__LINE__);
	echo $class_title;
}

?>