<?php

	file_put_contents(dirname(__file__).'/../../data/realname/'.date('Ymd').'.fail.log', print_r($_POST, true)."\n");
	//**************************************************************************************************************
    //NICE�ſ������� Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
    
    //���񽺸� :  üũ�÷��� - �Ƚɺ������� ����
    //�������� :  üũ�÷��� - ��� ������
    
    //������ ���� �����ص帮�� ������������ ���� ���� �� �������� ������ �ֽñ� �ٶ��ϴ�. 
    //**************************************************************************************************************
    
    ini_set("session.save_path",$_SERVER['DOCUMENT_ROOT']."/../_session");
	session_start(); 
	
    $sitecode = "G5799";					// NICE�κ��� �ο����� ����Ʈ �ڵ�
    $sitepasswd = "4XB4Y7E32TK3";				// NICE�κ��� �ο����� ����Ʈ �н�����
    
    //$cb_encode_path = "\������\CPClient.exe";		// NICE�κ��� ���� ��ȣȭ ���α׷��� ��ġ (������+����)
	$cb_encode_path = dirname(__file__)."/64bit/CPClient";		// NICE�κ��� ���� ��ȣȭ ���α׷��� ��ġ (������+����)
		
    $enc_data = $_POST["EncodeData"];		// ��ȣȭ�� ��� ����Ÿ

    $sReserved1 = $_POST['param_r1'];		
		$sReserved2 = $_POST['param_r2'];
		$sReserved3 = $_POST['param_r3'];

		//////////////////////////////////////////////// ���ڿ� ����///////////////////////////////////////////////
	if(preg_match('~[^0-9a-zA-Z+/=]~', $enc_data, $match)) {echo "�Է� �� Ȯ���� �ʿ��մϴ�"; exit;}
    if(base64_encode(base64_decode($enc_data))!==$enc_data) { echo " �Է� �� Ȯ���� �ʿ��մϴ�"; exit;}	
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved1, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved2, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved3, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		
    if ($enc_data != "") {

        $plaindata = `$cb_encode_path DEC $sitecode $sitepasswd $enc_data`;		// ��ȣȭ�� ��� �������� ��ȣȭ
        //echo "[plaindata] " . $plaindata . "<br>";

        if ($plaindata == -1){
            $returnMsg  = "��/��ȣȭ �ý��� ����";
        }else if ($plaindata == -4){
            $returnMsg  = "��ȣȭ ó�� ����";
        }else if ($plaindata == -5){
            $returnMsg  = "HASH�� ����ġ - ��ȣȭ �����ʹ� ���ϵ�";
        }else if ($plaindata == -6){
            $returnMsg  = "��ȣȭ ������ ����";
        }else if ($plaindata == -9){
            $returnMsg  = "�Է°� ����";
        }else if ($plaindata == -12){
            $returnMsg  = "����Ʈ ��й�ȣ ����";
        }else{
            // ��ȣȭ�� �������� ��� �����͸� �Ľ��մϴ�.
            $ciphertime = `$cb_encode_path CTS $sitecode $sitepasswd $enc_data`;	// ��ȣȭ�� ��� ������ ���� (��ȣȭ�� �ð�ȹ��)
            
            $requestnumber = GetValue($plaindata , "REQ_SEQ");
            $errcode = GetValue($plaindata , "ERR_CODE");
            $authtype = GetValue($plaindata , "AUTH_TYPE");
        }
    }
?>

<?
    function GetValue($str , $name)
    {
        $pos1 = 0;  //length�� ���� ��ġ
        $pos2 = 0;  //:�� ��ġ

        while( $pos1 <= strlen($str) )
        {
            $pos2 = strpos( $str , ":" , $pos1);
            $len = substr($str , $pos1 , $pos2 - $pos1);
            $key = substr($str , $pos2 + 1 , $len);
            $pos1 = $pos2 + $len + 1;
            if( $key == $name )
            {
                $pos2 = strpos( $str , ":" , $pos1);
                $len = substr($str , $pos1 , $pos2 - $pos1);
                $value = substr($str , $pos2 + 1 , $len);
                return $value;
            }
            else
            {
                // �ٸ��� ��ŵ�Ѵ�.
                $pos2 = strpos( $str , ":" , $pos1);
                $len = substr($str , $pos1 , $pos2 - $pos1);
                $pos1 = $pos2 + $len + 1;
            }            
        }
    }
?>
<html>
<head>
    <title>NICE�ſ������� - CheckPlus ��������</title>
</head>
<body>
<script type="text/javascript">
alert('���������� �����Ͽ����ϴ�.\n�ٽ� �õ��� �ּ���.');
window.close();
</script>

<?/*
<html>
<head>
    <title>NICE�ſ������� - CheckPlus �Ƚɺ������� �׽�Ʈ</title>
</head>
<body>
    <center>
    <p><p><p><p>
    ���������� �����Ͽ����ϴ�.<br>
    <table width=500 border=1>
        <tr>
            <td>��ȣȭ�� �ð�</td>
            <td><?= $ciphertime ?> (YYMMDDHHMMSS)</td>
        </tr>
        <tr>
            <td>��û ��ȣ</td>
            <td><?= $requestnumber ?></td>
        </tr>            
        <tr>
            <td>�������� ���� �ڵ�</td>
            <td><?= $errcode ?></td>
        </tr>            
        <tr>
            <td>��������</td>
            <td><?= $authtype ?></td>
        </tr>
        <tr>
          <td>RESERVED1</td>
          <td><?= $sReserved1 ?></td>
	      </tr>
	      <tr>
	          <td>RESERVED2</td>
	          <td><?= $sReserved2 ?></td>
	      </tr>
	      <tr>
	          <td>RESERVED3</td>
	          <td><?= $sReserved3 ?></td>
	      </tr>
    </table>
    </center>
*/?>

</body>
</html>