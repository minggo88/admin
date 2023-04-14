<?php
function loopCurrency($tradable=null)
{
	global $dbcon;
	$arr = array();
	$query = array();
	$query['table_name'] = 'js_trade_currency';
	$query['tool'] = 'select';
	$query['fields'] = 'symbol,name';
	$query['where'] = 'where active = \'Y\' ';
	if($tradable) {
		$query['where'] .= 'and tradable = "Y" ';
	}
	$query['where'] .= 'order by sortno asc';
	$result = $dbcon->query($query,__FILE__,__LINE__);
	$arr = array();
	for ($i = 0; $row = mysqli_fetch_assoc($result) ; $i++) {
		if( $row['symbol'] == $_GET['symbol']) {
			$arr[] = '<option value="'.$row['symbol'].'" selected=\"selected\">'.$row['name'].'</option>';
		}
		else {
			$arr[] = '<option value="'.$row['symbol'].'">'.$row['name'].'</option>';
		}
		// $arr[] = '<option value="'.$row['symbol'].'" '.$row['selected'].'>'.$row['name'].'</option>';
	}
	echo $GET_['symbol'];
	echo implode("\n",$arr);

}

?>