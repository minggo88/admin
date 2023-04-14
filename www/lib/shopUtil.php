<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment : 
--------------------------------------------*/

/*********************************************
상단 select 카테고리를 리턴한다.
*********************************************/
function shopSelectCategory($category_code='')
{
	global $dbcon;
	global $tpl;

	$category_code = empty($category_code) ? $_GET['category_code'] : $category_code;

	//상품리스트페이지
	//신상품,인기상품, 추천상품
	/*
	$query = array();
	$query['table_name'] = 'js_shop_sub_config';
	$query['tool'] = 'select_one';
	$query['fields'] = 'set_show_scope';
	$query['where'] = 'where category_code=\''.$category_code.'\'';
	$set_show_scope = $dbcon->query($query,__FILE__,__LINE__);

	$query = array();
	$query['table_name'] = 'js_shop_category';
	$query['tool'] = 'select_one';
	$query['fields'] = 'category_path';
	$query['where'] = 'where category_code=\''.$category_code.'\'';
	$category_path = $dbcon->query($query,__FILE__,__LINE__);

	$depth = getCategoryDepth($category_path);

	if($set_show_scope == 'first' && $depth > 1) { $show_item = FALSE; }
	else { $show_item = TRUE; }

	$tpl->assign('show_item',$show_item);
	*/

	/*
	preg_match('/^(\d{4})(\d{4})(\d{4})(\d{4})$/',$category_path,$matches);

	$query = array();
	$query['table_name'] = 'js_shop_category';
	$query['tool'] = 'select';
	$query['fields'] = 'category_code,category_path,category_name,depth';
	$query['where'] = 'where bool_use=1 && depth=1 order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop = array();
	while ($row = mysqli_fetch_assoc($result)) { 
		$row['category_name'] = cutStr($row['category_name'],12);
		$loop[] = $row;
	}

	$tpl->assign('loop_1',$loop);
	$tpl->assign('category_1',1);
	$tpl->assign('select_1_value',$matches[1]);

	$arr_show = array(2=>'category_2',3=>'category_3',4=>'category_4');
	foreach($arr_show as $key=>$val) {
		if((int)$matches[$key-1] > 0) {
			$query['tool'] = 'select_affect';
			if($key == 2) { $reg_exp = '\'^('.$matches[$key-1].')([[:digit:]]{4}){3}$\''; }
			else if($key == 3) { $reg_exp = '\'^([[:digit:]]{4}){1}('.$matches[$key-1].')([[:digit:]]{4}){2}$\''; }
			else { $reg_exp = '\'^([[:digit:]]{4}){2}('.$matches[$key-1].')([[:digit:]]{4})$\''; }
			$query['where'] = 'where bool_use=1 && depth='.$key.' && category_path REGEXP '.$reg_exp.' order by ranking asc';
			$result = $dbcon->query($query,__FILE__,__LINE__);
			if($result['cnt'] > 0) {
				$tpl->assign($val,1);
				$tpl->assign('parent_category_'.$key,$matches[$key-1]);
				$tpl->assign('select_'.$key.'_value',$matches[$key]);
				$loop = array();
				while ($row = mysqli_fetch_assoc($result['result'])) {
					$row['category_name'] = cutStr($row['category_name'],9);
					$loop[] = $row;
				}
				$tpl->assign('loop_'.$key,$loop);
			}
		}
	}
	*/
}

/*********************************************
카테고리 목록을 리턴한다.
왼쪽 카테고리 목록등에 사용한다.
*********************************************/
function categoryLists($mode='user')
{
	global $dbcon;
	/*
	$bool_use = ($mode == 'user') ? ' && bool_use=1' : '';
	$query = array();
	$query['table_name'] = 'js_shop_category';
	$query['tool'] = 'select';
	$query['where'] = 'where 1 '.$bool_use.' && depth=1 order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop = array();
	for($i = 0 ; $row = mysqli_fetch_assoc($result) ; $i++) {
		$loop[$i] = $row;
		$loop_two = &$loop[$i]['loop_two'];
		$query['where'] = 'where 1 '.$bool_use.' && category_path REGEXP \'^('.substr($row['category_path'],0,4).')([[:digit:]]{4}){3}$\' && depth=2 order by ranking asc';
		$result_two = $dbcon->query($query,__FILE__,__LINE__);
		for($j = 0 ; $row_two = mysqli_fetch_assoc($result_two) ; $j++ ) {
			$loop_two[$j] = $row_two;
			$loop_three = &$loop_two[$j]['loop_three'];
			$query['where'] = 'where 1 '.$bool_use.' && category_path REGEXP \'^('.substr($row_two['category_path'],0,8).')([[:digit:]]{4}){2}$\' && depth=3 order by ranking asc';
			$result_three = $dbcon->query($query,__FILE__,__LINE__);
			for($k = 0 ; $row_three = mysqli_fetch_assoc($result_three) ; $k++) {
				$loop_three[$k] = $row_three;
				$loop_four = &$loop_three[$k]['loop_four'];
				$query['where'] = 'where 1 '.$bool_use.' && category_path REGEXP \'^('.substr($row_three['category_path'],0,12).')([[:digit:]]{4})$\' && depth=4 order by ranking asc';
				$result_four = $dbcon->query($query,__FILE__,__LINE__);
				while($row_four = mysqli_fetch_assoc($result_four)) {
					$loop_four[] = $row_four;
				}
			}
		}
	}
	*/
	return $loop;
}

/*********************************************
카테고리 경로를 category_name으로 > 로 구분하여 리턴한다.
*********************************************/
function getCategoryPath($code,$bool_link=0,$bool_add_arrow=0)
{
	global $dbcon;
	$len = strlen($code);
	//if(preg_match('/^\d{4}$/',$code)) {
	if($len == 4) {
		$query = array();
		$query['table_name'] = 'js_shop_category';
		$query['tool'] = 'select_one';
		$query['fields'] = 'category_path';
		$query['where'] = 'where category_code='.$code;
		$category_path = $dbcon->query($query,__FILE__,__LINE__);
	}
	else {
		$category_path = $code;
	}
	preg_match('/^(\d{4})(\d{4})(\d{4})(\d{4})$/',$category_path,$matches);
	$arr = array();
	for ($i = 1; $i < 5 ; $i++) {
		if((int)$matches[$i] > 0) {
			$query['table_name'] = 'js_shop_category';
			$query['tool'] = 'select_one';
			$query['fields'] = 'category_name';
			$query['where'] = 'where category_code=\''.$matches[$i].'\'';
			if(empty($bool_link)) {
				$arr[] = $dbcon->query($query,__FILE__,__LINE__);
			}
			else {
				$arr[] = '<span class="select_category depth_0'.$i.'"> <a href="/shop/shop.php?category_code='.$matches[$i].'&depth='.$i.'">'.$dbcon->query($query,__FILE__,__LINE__).'</a> </span>';
			}
		}
		else {
			break;
		}
	}
	$ret =  empty($bool_add_arrow) ? implode('',$arr) : implode(' > ',$arr) ;
	return $ret;
}

/*********************************************
카테고리 목록을 리턴한다.
왼쪽 카테고리 목록등에 사용한다.
*********************************************/
function curriculum_categoryLists($mode='user')
{
	global $dbcon;
	
	$bool_use = ($mode == 'user') ? ' && bool_use=1' : '';
	$query = array();
	//$query['table_name'] = 'js_shop_category';
	$query['table_name'] = 'js_curriculum';
	$query['tool'] = 'select';
	$query['where'] = 'where 1 '.$bool_use.' && depth=1 order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$loop = array();
	for($i = 0 ; $row = mysqli_fetch_assoc($result) ; $i++) {
		$loop[$i] = $row;
		$loop_two = &$loop[$i]['loop_two'];
		$query['where'] = 'where 1 '.$bool_use.' && category_path REGEXP \'^('.substr($row['category_path'],0,4).')([[:digit:]]{4}){3}$\' && depth=2 order by ranking asc';
		$result_two = $dbcon->query($query,__FILE__,__LINE__);
		for($j = 0 ; $row_two = mysqli_fetch_assoc($result_two) ; $j++ ) {
			$loop_two[$j] = $row_two;
			$loop_three = &$loop_two[$j]['loop_three'];
			$query['where'] = 'where 1 '.$bool_use.' && category_path REGEXP \'^('.substr($row_two['category_path'],0,8).')([[:digit:]]{4}){2}$\' && depth=3 order by ranking asc';
			$result_three = $dbcon->query($query,__FILE__,__LINE__);
			for($k = 0 ; $row_three = mysqli_fetch_assoc($result_three) ; $k++) {
				$loop_three[$k] = $row_three;
				$loop_four = &$loop_three[$k]['loop_four'];
				$query['where'] = 'where 1 '.$bool_use.' && category_path REGEXP \'^('.substr($row_three['category_path'],0,12).')([[:digit:]]{4})$\' && depth=4 order by ranking asc';
				$result_four = $dbcon->query($query,__FILE__,__LINE__);
				while($row_four = mysqli_fetch_assoc($result_four)) {
					$loop_four[] = $row_four;
				}
			}
		}
	}
	return $loop;
}

/*********************************************
컨텐츠코드값으로 컨텐츠 카테고리 값을 리턴한다.
*********************************************/
function getCateCode($contents_code)
{
	global $dbcon;
	$query = array();
	$query['table_name'] = 'js_contents_category';
	$query['tool'] = 'select_one';
	$query['fields'] = 'cate_code';
	$query['where'] = 'where contents_code=\''.$contents_code.'\'';
	return $dbcon->query($query,__FILE__,__LINE__);
}

/*********************************************
카테고리 경로를 category_name으로 > 로 구분하여 리턴한다.
*********************************************/
function curriculum_getCategoryPath($code,$bool_link=0,$bool_add_arrow=0)
{
	global $dbcon;
	$len = strlen($code);
	//if(preg_match('/^\d{4}$/',$code)) {
	if($len == 4) {
		$query = array();
		$query['table_name'] = 'js_curriculum';
		$query['tool'] = 'select_one';
		$query['fields'] = 'category_path';
		$query['where'] = 'where category_code='.$code;
		$category_path = $dbcon->query($query,__FILE__,__LINE__);
	}
	else {
		$category_path = $code;
	}
	preg_match('/^(\d{4})(\d{4})(\d{4})(\d{4})$/',$category_path,$matches);
	$arr = array();
	for ($i = 1; $i < 5 ; $i++) {
		if((int)$matches[$i] > 0) {
			$query['table_name'] = 'js_curriculum';
			$query['tool'] = 'select_one';
			$query['fields'] = 'category_name';
			$query['where'] = 'where category_code=\''.$matches[$i].'\'';
			if(empty($bool_link)) {
				$arr[] = $dbcon->query($query,__FILE__,__LINE__);
			}
			else {
				$arr[] = '<span class="select_category depth_0'.$i.'"> <a href="/shop/shop.php?category_code='.$matches[$i].'&depth='.$i.'">'.$dbcon->query($query,__FILE__,__LINE__).'</a> </span>';
			}
		}
		else {
			break;
		}
	}
	$ret =  empty($bool_add_arrow) ? implode('',$arr) : implode(' > ',$arr) ;
	return $ret;
}

/*********************************************
카테고리 path를 리턴한다.
*********************************************/
function getCategoryPathCode($code)
{
	global $dbcon;
	$query = array();
	$query['table_name'] = 'js_shop_category';
	$query['tool'] = 'select_one';
	$query['fields'] = 'category_path';
	$query['where'] = 'where category_code=\''.$code.'\'';
	return $dbcon->query($query,__FILE__,__LINE__);
}

/*********************************************
해당 카테고리 코드의 depth를 리턴한다.
*********************************************/
function getCategoryDepth($code)
{
	global $dbcon;

	if(preg_match('/^\d{4}$/',$code)) {
		$query = array();
		$query['table_name'] = 'js_shop_category';
		$query['tool'] = 'select_one';
		$query['fields'] = 'category_path';
		$query['where'] = 'where category_code='.$code;
		$category_path = $dbcon->query($query,__FILE__,__LINE__);
	}
	else { $category_path = $code; }

	if(preg_match('/^([1-9]\d{3})[0]{12}$/',$category_path)) {
		$ret = 1;
	}
	else if(preg_match('/^([1-9]\d{3}){2}[0]{8}$/',$category_path)) {
		$ret = 2;
	}
	else if(preg_match('/^([1-9]\d{3}){3}[0]{4}$/',$category_path)) {
		$ret = 3;
	}
	else {
		$ret = 4;
	}
	return $ret;
}

/*********************************************
카테고리 경로에서 상위 카테고리 path를 가지고 온다.
*********************************************/
function getParentCategory($category_path)
{
	$depth = getCategoryDepth($category_path);
	$depth = $depth -1;
	preg_match('/^(([1-9]\d{3}){'.$depth.'})((\d{4})*?)$/',$category_path,$matches);
	return $matches[1];
}

/*********************************************
0를 제외한 카테고리경로를 리턴한다.
*********************************************/
function getCategory($category_path)
{
	preg_match('/^(([1-9]\d{3})+?)((0{4})*?)$/',$category_path,$matches);
	return $matches[1];
}

/*********************************************
카테고리 정규식을 리턴하는 함수
cagegory_code 와 depth를 넘겨준다.
*********************************************/
function getRegExp($category_code,$depth=4)
{
	if($depth == 1) {
		$reg_exp = '\'^('.$category_code.')([[:digit:]]{4}){3}$\'';
	}
	else if($depth == 2) {
		$reg_exp = '\'^([[:digit:]]{4})('.$category_code.')([[:digit:]]{4}){2}$\'';
	}
	else if($depth == 3) {
		$reg_exp = '\'^([[:digit:]]{4}){2}('.$category_code.')([[:digit:]]{4})$\'';
	}
	else {
		$reg_exp = '\'^([[:digit:]]{4}){3}('.$category_code.')$\'';
	}
	return $reg_exp;
}


/*********************************************
4단 셀렉트에서 ajax로 값을 가지고 올때 사용
카테고리 정규식을 리턴하는 함수
cagegory_code 와 depth를 넘겨준다.
*********************************************/
function selectRegExp($category_code,$depth=4)
{
	if($depth == 2) {
		$reg_exp = '\'^('.$category_code.')([[:digit:]]{4}){3}$\'';
	}
	else if($depth == 3) {
		$reg_exp = '\'^([[:digit:]]{4})('.$category_code.')([[:digit:]]{4}){2}$\'';
	}
	else if($depth == 4) {
		$reg_exp = '\'^([[:digit:]]{4}){2}('.$category_code.')([[:digit:]]{4})$\'';
	}
	else {
		$reg_exp = '';
	}
	return $reg_exp;
}

function getSubCategory($category_code,$depth=1)
{
	global $dbcon;
	$query = array();
	$query['table_name'] = 'js_shop_category';
	$query['tool'] = 'select';
	$query['fields'] = 'category_code';
	if($depth == 1) {
		$reg_exp = '\'^('.$category_code.')([[:digit:]]{4}){3}$\'';
	}
	else if($depth == 2) {
		$reg_exp = '\'^([[:digit:]]{4})('.$category_code.')([[:digit:]]{4}){2}$\'';
	}
	else if($depth == 3) {
		$reg_exp = '\'^([[:digit:]]{4}){2}('.$category_code.')([[:digit:]]{4})$\'';
	}
	else {
		$reg_exp = '\'^([[:digit:]]{4}){3}('.$category_code.')$\'';
	}
	$query['where'] = 'where category_path REGEXP '.$reg_exp.' order by ranking asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$arr = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$arr[] = 'category_code='.$row['category_code'];
	}
	return empty($arr) ? '' : ' && ('.implode(' || ',$arr).')';
}

?>