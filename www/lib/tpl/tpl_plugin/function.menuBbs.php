<?php
/*
** mode ***
comm
cs
main
admin
*/
function menuBbs($mode='comm')
{
	global $dbcon;
	global $tpl;

	$query = array();
	$query['table_name'] = 'js_bbs_info';
	$query['tool'] = 'select';
	$query['fields'] = 'bbscode,kind_menu,title';

	if ($mode == 'comm') {
		$query['where'] = 'where kind_menu=\'comm\' order by ranking asc';
	}
	else if ($mode == 'cs') {
	
		$query['where'] = 'where kind_menu=\'cs\' order by ranking asc';
	}
	else if ($mode == 'eta') {
		$query['where'] = 'where kind_menu=\'eta\' order by ranking asc';
	}
	else if ($mode == 'shop') {
		$query['where'] = 'where kind_menu=\'shop\' order by ranking asc';
	}
	else if ($mode == 'main') {
		$query['where'] = 'where bool_main=1 order by ranking asc';
	}
	else {
		$query['where'] = 'order by ranking asc';
	}

	$result = $dbcon->query($query,__FILE__,__LINE__);
	while ($row = mysqli_fetch_assoc($result)) {
		if($mode == 'admin') {
			echo '<li class="lilist" id="bbs'.$row['bbscode'].'"><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode='.$row['bbscode'].'">'.$row['title'].'</a></li>';
		}
		else {
			echo '<li class="arrow"><a href="/bbs/bbs.php?pg_mode=list&mode='.$row['kind_menu'].'&bbscode='.$row['bbscode'].'">'.$row['title'].'</a></li>';
		}
	}
}
?>