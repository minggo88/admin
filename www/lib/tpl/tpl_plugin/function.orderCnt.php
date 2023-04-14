<?php
function orderCnt($process='total')
{
	global $dbcon;



	if($process == 'aa') {
		$query = "select count(*) from js_shop_order_info where process='aa'";
	}
	else if($process == 'ab') {
		$query = "select count(*) from js_shop_order_info where process='ab'";
	}
	else if($process == 'ac') {
		$query = "select count(*) from js_shop_order_info where process='ac'";
	}
	else if($process == 'ad') {
		$query = "select count(*) from js_shop_order_info where process='ad'";
	}
	else if($process == 'ae') {
		$query = "select count(*) from js_shop_order_info where process='ae'";
	}
	else if($process == 'ax') {
		$query = "select count(*) from js_shop_order_info where process='ax'";
	}
	else if($process == 'ba') {
		$query = "select count(*) from js_shop_order_info where process='ba'";
	}
	else if($process == 'bb') {
		$query = "select count(*) from js_shop_order_info where process='bb'";
	}
	else if($process == 'be') {
		$query = "select count(*) from js_shop_order_info where process='be'";
	}
	else if($process == 'bx') {
		$query = "select count(*) from js_shop_order_info where process='bx'";
	}
	else if($process == 'ea') {
		$query = "select count(*) from js_shop_order_info where process='ea'";
	}
	else if($process == 'eb') {
		$query = "select count(*) from js_shop_order_info where process='eb'";
	}
	else if($process == 'ec') {
		$query = "select count(*) from js_shop_order_info where process='ec'";
	}
	else if($process == 'ee') {
		$query = "select count(*) from js_shop_order_info where process='ee'";
	}
	else if($process == 'ex') {
		$query = "select count(*) from js_shop_order_info where process='ex'";
	}
	else {
		$query = "select count(*) from js_shop_order_info";
	}
		
	$result = $dbcon->query($query,__FILE__,__LINE__);
	list($ret) = mysqli_fetch_row($result);
	return $ret;
}

?>