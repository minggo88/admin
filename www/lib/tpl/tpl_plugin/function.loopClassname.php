<?php
function loopClassname()
{
	global $dbcon;
	$arr = array();
	$query = array();
	$query['table_name'] = 'js_config_classname';
	$query['tool'] = 'select';
	$query['fields'] = 'class_name,class_title';
	$query['where'] = 'order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$arr = array();
	$arr[] = '<option value="" selected="selected">:::반선택:::</option>';
	for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
		$arr[] = '<option value="'.$row['class_name'].'">'.$row['class_title'].'</option>';
	}
	echo implode("\n",$arr);
}

?>