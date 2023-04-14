<?php
/*--------------------------------------------
Date : 2012-08-18
T1uthor : Danny Hwang
comment :
--------------------------------------------*/

class ShopMain extends BASIC
{
	function __construct(&$tpl)
	{
		$config = array();
		$this->BASIC($config,$tpl);
	}

	function hitGoodsList()
	{
		$this->lists('hit');
	}

	function recomGoodsList()
	{
		$this->lists('recom');
	}

	/*
	*** mode ***
	hit
	recom
	*/
	function lists($mode='hit')
	{
		$query = array();
		$query['table_name'] = 'js_shop_goods_category as T1, js_shop_goods as T2, js_shop_category as T3';
		$query['tool'] = 'select';
		$query['fields'] = "T1.c_code,
			T1.goods_code,
			T1.category_code,
			T1.selling_price,
			T2.goods_name,
			T2.bool_price,
			T2.replace_price_word,
			T2.bool_emoney,
			T2.bool_option,
			T2.bool_sold_out,
			T2.goods_summary,
			T2.goods_detail,
			T2.img_goods_a,
			T2.s_word,
			T2.satisfaction,
			T2.bool_icon_new,
			T2.bool_icon_recomm,
			T2.bool_icon_special,
			T2.bool_icon_event,
			T2.bool_icon_regv,
			T2.bool_icon_best,
			T2.bool_icon_sale,
			T3.category_path,
			T3.depth";
		if($mode == 'hit') {
			$loop_id = 'loop_hit';
			$query['where'] = 'where T1.goods_code=T2.goods_code && T1.category_code=T3.category_code && T1.bool_hit=1';
		}
		else {
			$loop_id = 'loop_recom';
			$query['where'] = 'where T1.goods_code=T2.goods_code && T1.category_code=T3.category_code && T1.bool_recom=1';
		}
		$this->bList($query,$loop_id);
	}

	function mainItem()
	{
		$this->mainItemList();
		$this->mainItemGrp();
	}

	//group 리스트
	function mainItemGrp()
	{
		$query = array();
		$query['table_name'] = 'js_shop_main_grp';
		$query['tool'] = 'select';
		$query['fields'] = 'grp_code';
		$query['where'] = 'where 1';
		$result_grp = $this->dbcon->query($query,__FILE__,__LINE__);
		while ($row_grp = mysqli_fetch_assoc($result_grp)) {
			$query = array();
			$query['table_name'] = 'js_shop_main';
			$query['tool'] = 'select';
			$query['fields'] = 'main_code, name_div, img_title, img_div, ranking, type_display, shop_skin';
			$query['where'] = 'where bool_display=1 && bool_grp=1 && grp_code=\''.$row_grp['grp_code'].'\' order by ranking asc';
			$result = $this->dbcon->query($query,__FILE__,__LINE__);
			$loop = array();
			for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
				$loop[] = $row;
				$query = array();
				$query['table_name'] = 'js_shop_main_goods as T1, js_shop_goods_category as T2, js_shop_goods as T3';
				$query['tool'] = 'select';
				$query['fields'] = 'T1.idx,
					T1.main_code,
					T1.c_code,
					T1.ranking,
					(select category_path from js_shop_category where category_code=T2.category_code) as category_path,
					(select depth from js_shop_category where category_code=T2.category_code) as depth,
					T2.category_code,
					T2.goods_code,
					T2.selling_price,
					T3.goods_name,
					T3.goods_name,
					T3.bool_sold_out,
					T3.img_goods_a,
					T3.bool_icon_new,
					T3.bool_icon_recomm,
					T3.bool_icon_special,
					T3.bool_icon_event,
					T3.bool_icon_regv,
					T3.bool_icon_best,
					T3.bool_icon_sale';
				$query['where'] = 'where T1.c_code=T2.c_code && T2.goods_code=T3.goods_code && T1.main_code=\''.$row['main_code'].'\' limit 4';
				$result2 = $this->dbcon->query($query,__FILE__,__LINE__);
				$loop2 = &$loop[$i]['loop_main_goods'];
				while ($row2 = mysqli_fetch_assoc($result2)) {
					$loop2[] = $row2;
				}
			}
			$this->tpl->assign('loop_main_grp_'.$row_grp['grp_code'],$loop);
		}
	}

	//group 리스트
	function mainItemList()
	{
		$query = array();
		$query['table_name'] = 'js_shop_main';
		$query['tool'] = 'select';
		$query['fields'] = 'main_code, name_div, img_title, img_div, ranking, type_display, shop_skin';
		$query['where'] = 'where bool_display=1 && bool_grp=0 order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		while ($row = mysqli_fetch_assoc($result)) {
			$this->tpl->assign('info_'.$row['main_code'],$row);
			$query = array();
			$query['table_name'] = 'js_shop_main_goods AS T1, js_shop_goods_category AS T2, js_shop_goods AS T3';
			$query['tool'] = 'select';
			$query['fields'] = 'T1.idx,
				T1.main_code,
				T1.c_code,
				T1.ranking,
				(select category_path from js_shop_category where category_code=T2.category_code) as category_path,
				(select depth from js_shop_category where category_code=T2.category_code) as depth,
				T2.category_code,
				T2.goods_code,
				T2.selling_price,
				T3.goods_name,
				T3.goods_name,
				T3.bool_sold_out,
				T3.img_goods_a,
				T3.bool_icon_new,
				T3.bool_icon_recomm,
				T3.bool_icon_special,
				T3.bool_icon_event,
				T3.bool_icon_regv,
				T3.bool_icon_best,
				T3.bool_icon_sale';
			$query['where'] = 'where T1.c_code=T2.c_code && T2.goods_code=T3.goods_code && T1.main_code=\''.$row['main_code'].'\'';
			$result2 = $this->dbcon->query($query,__FILE__,__LINE__);
			$loop = array();
			while ($row2 = mysqli_fetch_assoc($result2)) {
				$loop[] = $row2;
			}
			$this->tpl->assign('loop_main_item_'.$row['main_code'],$loop);
		}
	}

	function leftReview()
	{
		$query = array();
		$query['table_name'] = 'js_bbs_main';
		$query['tool'] = 'select';
		$query['fields'] = 'idx,subject, contents, regdate';
		$query['where'] = 'where bbscode=\'EW658\' order by idx desc limit 3';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$row['subject'] = strip_tags($row['subject']);
			$row['contents'] = strip_tags($row['contents']);
			$loop[] = $row;
		}
		$this->tpl->assign('loop_left_review',$loop);;
	}

	function popup()
	{
		$query = array();
		$query['table_name'] = 'js_popup';
		$query['tool'] = 'select';
		$query['where'] = 'where bool_popup=1 && end_date > UNIX_TIMESTAMP()';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop_popup = array();
		while ($row = mysqli_fetch_assoc($result)) {
			if(empty($_COOKIE['popupCookiedrag_popup_'.$row['idx']])) {
				$loop_popup[] = $row;
			}
		}
		$this->tpl->assign('loop_popup',$loop_popup);
	}

	function mainImg()
	{
		$query = array();
		$query['table_name'] = 'js_main_img';
		$query['tool'] = 'select';
		$query['where'] = 'where bool_banner > 0 order by ranking asc';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_main_img',$loop);
	}

	function mainProduct()
	{
		$query = array();
		$query['table_name'] = 'js_shop_goods';
		$query['tool'] = 'select';
		$query['where'] = 'where bool_icon_new > 0 order by regdate desc limit 10';
		$result = $this->dbcon->query($query,__FILE__,__LINE__);
		$loop = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$loop[] = $row;
		}
		$this->tpl->assign('loop_main_product',$loop);
	}

	//카렌다와 연관된 메소스
	function loopYear()
	{
		$this_year = date('Y');
		$start_year = $this_year;
		$end_year = $this_year+2;
		$loop = array();
		for ($i = $start_year ; $i <= $end_year ; $i++) {
			$loop[] = $i;
		}
		$this->tpl->assign('loop_year',$loop);
	}

	function calendar()
	{
		//달력구현
		//오늘은 몇월이고 말일 몇일까지 인지 알아낸다.
		$cur_year = date('Y',time());
		$cur_month = date('n',time());
		$cur_day = date('j',time());
		$today = mktime(0,0,0,$cur_month,$cur_day,$cur_year);

		if(empty($_GET['year']) && empty($_GET['month'])) {
			$cur_date = time();
		}
		else {
			$cur_date = mktime(0,0,0,sprintf('%02d',$_GET['month']),1,$_GET['year']);
		}

		$this_year = date('Y',$cur_date); //해당 년
		$this_month = date('n',$cur_date); //해당 월
		$last_day = date('t',$cur_date); //해당월 말일

		//몇주로 구성되는가?
		$first_day_week = date('w',mktime(0,0,0,$this_month,01,$this_year));
		$last_day_week = date('w',mktime(0,0,0,$this_month,$last_day,$this_year));
		$gap_week = ceil(($first_day_week+$last_day)/7);

		$day_month = 1;
		$loop = array();


		for ($i = 0 ; $i < $gap_week ; $i++) {
			$loop[] = array('week'=>$i);
			$loop2 = &$loop[$i]['loop_day'];
			for ($j = 0; $j < 7 ; $j++) {
				$temp = array();
				if($i == 0 && $j < $first_day_week) {
					$temp['day_month'] = '';
					$temp['css_class'] = 'no_date';
				}
				else if($i == ($gap_week-1) && $j > $last_day_week) {
					$temp['day_month'] = '';
					$temp['css_class'] = 'no_date';
				}
				else {
					$temp['day_month'] = $day_month;
					$loop_date = mktime(0,0,0,$this_month,$day_month,$this_year);
					$day_month = $day_month + 1;
				}
				$loop2[] = $temp;
			}
		}
		$this->tpl->assign('loop_week',$loop);
	}


}

?>