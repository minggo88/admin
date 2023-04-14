<?php
function loopMobileA($val='')
{
	$arr = array();
	$arr[] = '<option value="">선택</option>';
	$arr[] = '<option value="010"'.($val=='010'?'selected':'').'>010</option>';
	$arr[] = '<option value="011"'.($val=='011'?'selected':'').'>011</option>';
	$arr[] = '<option value="016"'.($val=='016'?'selected':'').'>016</option>';
	$arr[] = '<option value="017"'.($val=='017'?'selected':'').'>017</option>';
	$arr[] = '<option value="018"'.($val=='018'?'selected':'').'>018</option>';
	$arr[] = '<option value="019"'.($val=='019'?'selected':'').'>019</option>';
	$arr[] = '<option value="0130"'.($val=='0130'?'selected':'').'>0130</option>';
	echo implode("\n",$arr);
}

?>