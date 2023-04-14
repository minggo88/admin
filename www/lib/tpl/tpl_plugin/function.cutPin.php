<?php

function cutPin($text, $length, $suffix='...',$div='utf-8')
{
	if($div == 'euc-kr') {
		if (strlen($text) <= $length) { return $text; }
		$cpos = $length - 1; 
		$count_2B = 0; 
		$lastchar = $text[$cpos]; 
		while (ord($lastchar)>127 && $cpos>=0) { 
			$count_2B++; 
			$cpos--; 
			$lastchar = $text[$cpos]; 
		} 
		$starfix = $suffix * ($lastchar - 2);
		return substr($text,0,(($count_2B % 2)?$length-1:$length)).$starfix; 
	}
	else {
		if(!$text || !$length) { return $text; }
		if(function_exists('iconv')) {
			$unicode_str = iconv("UTF-8","UCS-2",$text);
			if(strlen($unicode_str) < $length*2) { return $text; }
			$output_str = substr($unicode_str, 0, $length*2);
			return iconv("UCS-2","UTF-8",$output_str).$suffix;
		}
		$arr = array();
		$starfix = $suffix * ($unicode_str - 2);
		return preg_match('/.{'.$length.'}/su', $text, $arr) ? $arr[0].$starfix : $text; 
	}
}

?>