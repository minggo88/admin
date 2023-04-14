<?php
/**
 * 빗썸 차트 데이터 샘플용 데이터로 입력하는 스크립트
 * 개발서버만 사용. 라이브는 사용 안함.
 */
include('../lib/TradeApi.php');

$symbols = array('BTC', 'ETH', 'LTC', 'BCH', 'QTUM', 'EOS', 'XRP');

$terms = array('1h','1m','3m','5m','10m','15m','30m');

foreach ($symbols as $symbol) {
		
	foreach ($terms as $term) {
		
		$term_url = '';
		switch($term) {
			case '1m': $term_url = '01M'; break;
			case '3m': $term_url = '03M'; break;
			case '5m': $term_url = '05M'; break;
			case '10m': $term_url = '10M'; break;
			case '15m': $term_url = '15M'; break;
			case '30m': $term_url = '30M'; break;
			case '1h': $term_url = '01H'; break;
			case '12h': $term_url = '12H'; break;
			case '1d': $term_url = '24H'; break;
		}
		
		$url = 'https://www.bithumb.com/resources/chart/'.$symbol.'_xcoinTrade_'.$term_url.'.json?symbol='.$symbol.'&resolution=0.5&from=1530372600&strTime=1533050608889';
		$data = $tradeapi->remote_get($url);
		// json_decode 하면 숫자가 지수로 변형되는 문제 있어서 그냥 문자열로 처리함.
		$data = substr($data, 1, strlen($data)-2);
		$data = explode('],[', $data);
		// echo $url.','.count($data)."\n"; //exit;
		
		foreach ($data as $row) {
			$strrow = $row;
			$row = str_replace(array('[',']'), '', $row);
			$row = explode(',', trim($row));
			// var_dump($symbol, $row);//exit;
			if(! $row[0] || ! is_numeric($row[0]) ) {continue;}
			// 날짜, 시, 종, 고, 저, 거래량
			$date = date('Y-m-d H:i:s', $row[0]/1000);
			$open = $row[1]/1000;
			$open = strpos($open, '.')!==false ? substr($open, 0, strpos($open, '.')+3) : $open;
			$close = $row[2]/1000;
			$close = strpos($close, '.')!==false ? substr($close, 0, strpos($close, '.')+3) : $close;
			$high = $row[3]/1000;
			$high = strpos($high, '.')!==false ? substr($high, 0, strpos($high, '.')+3) : $high;
			$low = $row[4]/1000;
			$low = strpos($low, '.')!==false ? substr($low, 0, strpos($low, '.')+3) : $low;
			$volume = $row[5]=='' ? '0' : $row[5];
			if(stripos($volume, 'e')!==false) {
				$volume = substr($volume, 0, stripos($volume, 'e'));
				$volume = substr($volume, 0, strpos($volume, '.')+5);
				// var_dump($volume); exit;
			} else {
				$volume = substr($volume, 0, strpos($volume, '.')+5);
			}
			
			// $sql = " insert ignore into js_trade_ethusd_chart set term='{$term}', date='{$date}', open='{$open}', high='{$high}', low='{$low}', close='{$close}', volume='{$volume}' ";
			$sql = " insert into js_trade_".strtolower($symbol)."usd_chart set term='{$term}', date='{$date}', open='{$open}', high='{$high}', low='{$low}', close='{$close}', volume='{$volume}'  ON DUPLICATE KEY UPDATE open='{$open}', high='{$high}', low='{$low}', close='{$close}', volume='{$volume}' ";
			// var_dump($sql);exit;
			$tradeapi->query($sql);
		}	
		
		sleep(1);
	
	}
	
	sleep(1);

}