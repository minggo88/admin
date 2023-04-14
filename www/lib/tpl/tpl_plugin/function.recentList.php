<?php

function recentList($bbscode,$type='list',$limit=5)
{
	global $dbcon,$tpl;

	$query = array();
	//일대일
	if($bbscode == 'faq') {
		$query['table_name'] = 'js_faq';
		$query['tool'] = 'select';
		$query['where'] = 'where 1 order by idx desc limit '.$limit.'';		
	}
	//일대일
	else if($bbscode == 'mtom') {
		$query['table_name'] = 'js_mtom';
		$query['tool'] = 'select';
		$query['where'] = 'where 1 order by regdate desc limit '.$limit.'';		
	}
	//사용후기
	else if($bbscode == 'review') {
		$query['table_name'] = 'js_shop_review';
		$query['tool'] = 'select';
		$query['where'] = 'where 1 order by regdate desc limit '.$limit.'';	
	}
	//제품문의
	else if($bbscode == 'request') {
		$query['table_name'] = 'js_request';
		$query['tool'] = 'select';
		$query['where'] = 'where 1 order by regdate desc limit '.$limit.'';
	}
	else {
		$query['table_name'] = 'js_bbs_main';
		$query['tool'] = 'select';
		$query['where'] = 'where 1 && bbscode=\''.$bbscode.'\' && division = \'b\' order by pos desc limit '.$limit.'';
	}

	$result = $dbcon->query($query,__FILE__,__LINE__);
	if($type == 'gallery') { $tpl->define('contents','bbs/bbs_list_recent.html?gallery'); }
	else if($type == 'faq') { $tpl->define('contents','cscenter/faq_main.html'); }
	else { $tpl->define('contents','bbs/bbs_list_recent.html?list'); }
	$loop = array();
	while ($row = mysqli_fetch_assoc($result)) {

		if($bbscode == 'mtom') {
			$row['url_view'] = '/mypage/admin/mtomAdmin.php?pg_mode=view&start=0&idx='.$row['idx'];
		}
		else if($bbscode == 'review') {
			$row['url_view'] = '/shop/admin/goodsreviewAdmin.php?pg_mode=view&start=0&idx='.$row['idx'];
		}
		else if($bbscode == 'request') {
			$row['url_view'] = '/shop/admin/goodsquestionAdmin.php?pg_mode=view&start=0&idx='.$row['idx'];
		}
		else {
			$row['url_view'] = '/bbs/admin/bbsAdmin.php?pg_mode=view&list_cnt=0&bbscode='.$bbscode.'&start=0&idx='.$row['idx'];
		}

		if($type== 'gallery') {
			preg_match_all('/<img[^>]*(\d{15}\.\w{3,})[^>]*>/i',$row['contents'],$match);
			$arr_img = $match[1];
			if(!empty($arr_img)) {
				$row['use_img'] =	TRUE;
				$row['img'] = '/data/thumbnail/'.$arr_img[0];
			}
			else {
				$row['use_img'] = FALSE;
			}
		}
		else {
			$row['use_img'] = FALSE;
		}
		$loop[] = $row;
	}

	$tpl->assign('loop',$loop);
	echo $tpl->fetch('contents');
}
?>