<?php
/*
** mode ***
comm
cs
main
admin
*/
function siteMap()
{
	global $dbcon;
	global $tpl;

	$tpl->define('contents','cscenter/'.$info['skin'].'/sitemap_category.html');

	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select';
	$query['fields'] = 'cate_code, contents_name, link_name, kinds_contents, contents_code, depth, link_code';
	$query['where'] = 'where depth=1 order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop_sitemap = array();
	for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
		$query = array();
		$query['table_name'] = 'js_contents_category';
		$query['tool'] = 'row';
		$query['fields'] = 'cate_code, contents_name, link_name, kinds_contents, contents_code, link_url';
		$query['where'] = 'where cate_code=\''.$row['link_code'].'\'';
		$link_sub = $dbcon->query($query,__FILE__,__LINE__);
		$row['link_url'] = menuLink($link_sub['kinds_contents'],$row['cate_code'],$link_sub['cate_code'],$link_sub['contents_code'],$link_sub['link_url']);
		$loop_sitemap[] = $row;

		$query = array();
		$query['table_name'] = 'js_contents_category';
		$query['tool'] = 'select';
		$query['fields'] = 'cate_code, parent_code, contents_name, link_name, kinds_contents, contents_code, link_url';
		$query['where'] = 'where parent_code=\''.$row['cate_code'].'\' order by ranking asc';
		$result2 = $dbcon->query($query,__FILE__,__LINE__);
		$loop_sub = &$loop_sitemap[$i]['loop_sub'];
		while ($row2 = mysqli_fetch_assoc($result2)) {
			$row2['link_url'] = menuLink($row2['kinds_contents'],$row['cate_code'],$row2['cate_code'],$row2['contents_code'],$row2['link_url']);
			$loop_sub[] = $row2;
		}
	}
	$tpl->assign('loop_sitemap',$loop_sitemap);
	$tpl->print_('contents');

}
?>