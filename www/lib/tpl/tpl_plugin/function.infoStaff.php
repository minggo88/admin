<?php
/*
** mode ***
comm
cs
main
admin
*/
function infoStaff()
{
	global $dbcon;
	global $tpl;

	$query = array();
	$query['table_name'] = 'js_staff';
	$query['tool'] = 'row';
	$query['fields'] = '';
	$query['where'] = '';
	$dbcon->query($query,__FILE__,__LINE__);





}
?>