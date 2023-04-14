<?php
function menuBanner()
{
	global $dbcon;
	global $tpl;

	$query = array();
	$query['table_name'] = 'js_banner_info';
	$query['tool'] = 'select';
	$query['fields'] = 'bannercode,title';
	$query['where'] = 'order by regdate asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$loop[] = '<li class="lilist" id="banner'.$row['bannercode'].'"><a href="/admin/banner.php?pg_mode=list&bannercode='.$row['bannercode'].'">'.$row['title'].' 관리</a></li>'."\n";
	}
	echo implode('',$loop);
}
?>