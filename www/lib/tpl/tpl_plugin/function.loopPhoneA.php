<?php
function loopPhoneA()
{
	$arr = array();
	$arr[] = '<option value="">선택</option>';
	$arr[] = '<option value="000">전국번호</option>';
	$arr[] = '<option value="070">070</option>';
	$arr[] = '<option value="02">02</option>';
	$arr[] = '<option value="031">031</option>';
	$arr[] = '<option value="032">032</option>';
	$arr[] = '<option value="033">033</option>';
	$arr[] = '<option value="041">041</option>';
	$arr[] = '<option value="042">042</option>';
	$arr[] = '<option value="043">043</option>';
	$arr[] = '<option value="051">051</option>';
	$arr[] = '<option value="052">052</option>';
	$arr[] = '<option value="053">053</option>';
	$arr[] = '<option value="054">054</option>';
	$arr[] = '<option value="055">055</option>';
	$arr[] = '<option value="061">061</option>';
	$arr[] = '<option value="062">062</option>';
	$arr[] = '<option value="063">063</option>';
	$arr[] = '<option value="064">064</option>';
	$arr[] = '<option value="080">080</option>';
	$arr[] = '<option value="0502">0502</option>';
	$arr[] = '<option value="0507">0507</option>';
	$arr[] = '<option value="0504">0504</option>';
	$arr[] = '<option value="0503">0503</option>';
	$arr[] = '<option value="0506">0506</option>';
	$arr[] = '<option value="0505">0505</option>';
	$arr[] = '<option value="0303">0303</option>';
	echo implode("\n",$arr);
}

?>