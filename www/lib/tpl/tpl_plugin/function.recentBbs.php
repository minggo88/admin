<?php
function recentBbs(&$tpl,&$dbcon,$bbscode='',$limit=7)
{
	$query = array();
	$query['table_name'] = 'js_bbs_info';
	$query['tool'] = 'row';
	$query['fields'] = 'bbs_type,skin,bool_comment, bool_newmark,term_newmark,bool_hotmark,term_hotmark';
	$query['where'] = 'where bbscode=\''.$bbscode.'\'';
	$info = $dbcon->query($query,__FILE__,__LINE__);
	if($tpl->skin =='admin') {
		$tpl->define('contents','bbs/bbs_main.html?'.$info['bbs_type']);
	}
	else {
		$tpl->define('contents','bbs/'.$info['skin'].'/bbs_main.html');
	}

	$query = array();
	$query['table_name'] = 'js_bbs_main';
	$query['tool'] = 'select';
	$query['fields'] = 'idx, category, bbscode, subject_kr, subject_en, subject_cn, contents_kr, contents_en, contents_cn, bool_secret,hit, regdate';
	$query['where'] = 'where bbscode=\''.$bbscode.'\' && division=\'b\' && depth=1 order by pos desc limit '.$limit;
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$row['bbscode'] = $bbscode;
		$row['bool_thumb'] = false;
		if($info['bbs_type'] == 'gallery' || $info['bbs_type'] == 'webzine') {
			$arr_img = BASIC::editorGetImg($row['contents_kr']);
			if(!empty($arr_img)) {
				$row['bool_thumb'] =	true;
				$row['img'] = $arr_img[0];
			}
		}
		//New마크
		$row['bool_icon_new'] = false;
		$base_date = 60*60*$info['term_newmark'];//하루이내 등록된 글은 new마크를 표시한다.
		if($info['bool_newmark'] > 0) {
			//현재시간을 가지고 온다.
			$cur_date = time();
			if($row['regdate'] > ($cur_date - $base_date)) {
				$row['bool_icon_new'] = true;
			}
		}
		//Hot마크
		$row['bool_icon_hot'] = false;
		if($info['bool_hotmark'] > 0) {
			if($hit > $info['term_hotmark']) {
				$row['bool_icon_hot'] = true;
			}
		}
		//코멘트를 보여준다.
		$row['bool_comment'] = false;
		if($info['bool_comment'] > 0) {
			$query = array();
			$query['table_name'] = 'js_bbs_comment';
			$query['tool'] = 'count';
			$query['where'] = 'where link_idx='.$row['idx'];
			$cnt = $dbcon->query($query,__FILE__,__LINE__);
			if($cnt > 0) {
				$row['bool_comment'] = true;
				$row['cnt_comment'] = $cnt;
			}
		}
		$loop[] = $row;
	}

	$tpl->assign('loop_bbs_recent',$loop);
	$tpl->print_('contents');
}
?>