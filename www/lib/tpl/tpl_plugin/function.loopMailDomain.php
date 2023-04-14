<?php
function loopMailDomain()
{
	$arr = array();
	$arr[] = '<option value="" selected="selected">선택</option>';
	$arr[] = '<option value="chollian.net">chollian.net</option>';
	$arr[] = '<option value="dreamwiz.com">dreamwiz.com</option>';
	$arr[] = '<option value="empal.com">empal.com</option>';
	$arr[] = '<option value="freechal.com">freechal.com</option>';
	$arr[] = '<option value="gmail.com">gmail.com</option>';
	$arr[] = '<option value="hanafos.com">hanafos.com</option>';
	$arr[] = '<option value="hitel.net">hitel.net</option>';
	$arr[] = '<option value="hotmail.com">hotmail.com</option>';
	$arr[] = '<option value="korea.com">korea.com</option>';
	$arr[] = '<option value="lycos.co.kr">lycos.co.kr</option>';
	$arr[] = '<option value="msn.com">msn.com</option>';
	$arr[] = '<option value="nate.com">nate.com</option>';
	$arr[] = '<option value="naver.com">naver.com</option>';
	$arr[] = '<option value="netian.com">netian.com</option>';
	$arr[] = '<option value="paran.com">paran.com</option>';
	$arr[] = '<option value="yahoo.co.kr">yahoo.co.kr</option>';
	$arr[] = '<option value="yahoo.com">yahoo.com</option>';
	$arr[] = '<option value="hanmail.net">hanmail.net</option>';
	$arr[] = '<option value="etc">직접입력</option>';
	echo implode("\n",$arr);
}

?>