<?php
/*
** mode ***
comm
cs
main
admin
*/
function footerMenu()
{
	global $dbcon;
	global $tpl;

	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'row';
	$query['fields'] = 'cate_code';
	$query['where'] = 'where bool_footer=1 && depth=1';
	$row = $dbcon->query($query,__FILE__,__LINE__);
	$cate_code = $row['cate_code'];

	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select';
	$query['fields'] = 'cate_code, parent_code, contents_name, link_name, kinds_contents, contents_code, link_url';
	$query['where'] = 'where parent_code=\''.$cate_code.'\' order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$arr = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$arr[] = '<a href="/'.$row['link_name'].'">'.$row['contents_name'].'</a>';
		//$arr[] = '<a href="'.$row['link_url'].'&gnb_code='.$cate_code.'&cate_code='.$row['cate_code'].'">'.$row['contents_name'].'</a>';
	}
	echo implode('<span class="space">|</span>',$arr);
}
?>