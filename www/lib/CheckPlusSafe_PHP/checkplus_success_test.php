<?php
    
	file_put_contents(dirname(__file__).'/../../data/realname/'.date('Ymd').'.success.log', print_r($_POST, true));
	//**************************************************************************************************************
    //NICE�ſ������� Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
    
    //���񽺸� :  üũ�÷��� - �Ƚɺ������� ����
    //�������� :  üũ�÷��� - ��� ������
    
    //������ ���� �����ص帮�� ������������ ���� ���� �� �������� ������ �ֽñ� �ٶ��ϴ�. 
    //**************************************************************************************************************
    ini_set("session.save_path",$_SERVER['DOCUMENT_ROOT']."/../_session");
	session_start(); 
	$_SESSION["USER_REALNAME"] = iconv('euc-kr', 'utf-8', "0");
	
	var_dump($_SESSION); exit;
	
    $sitecode = "G5799";					// NICE�κ��� �ο����� ����Ʈ �ڵ�
    $sitepasswd = "4XB4Y7E32TK3";				// NICE�κ��� �ο����� ����Ʈ �н�����
    
    $cb_encode_path = "\������\CPClient.exe";		// NICE�κ��� ���� ��ȣȭ ���α׷��� ��ġ (������+����)
	$cb_encode_path = dirname(__file__)."/64bit/CPClient";		// NICE�κ��� ���� ��ȣȭ ���α׷��� ��ġ (������+����)
		
    $enc_data = $_POST["EncodeData"];		// ��ȣȭ�� ��� ����Ÿ
    $sReserved1 = $_POST['param_r1'];		
		$sReserved2 = $_POST['param_r2'];
		$sReserved3 = $_POST['param_r3'];

		//////////////////////////////////////////////// ���ڿ� ����///////////////////////////////////////////////
	if(preg_match('~[^0-9a-zA-Z+/=]~', $enc_data, $match)) {echo "�Է� �� Ȯ���� �ʿ��մϴ�"; exit;}
	if(base64_encode(base64_decode($enc_data))!==$enc_data) {echo " �Է� �� Ȯ���� �ʿ��մϴ�"; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved1, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved2, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved3, $match)) {echo "���ڿ� ���� : ".$match[0]; exit;}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		
    if ($enc_data != "") {

        $plaindata = `$cb_encode_path DEC $sitecode $sitepasswd $enc_data`;		// ��ȣȭ�� ��� �������� ��ȣȭ
        //echo "[plaindata]  " . $plaindata . "<br>";

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
			$userid = str_replace('REQ_','',$requestnumber);
			
            $responsenumber = GetValue($plaindata , "RES_SEQ");
            $authtype = GetValue($plaindata , "AUTH_TYPE");
// var_dump($authtype);
            $name = iconv('euc-kr','utf-8', GetValue($plaindata , "NAME"));
// var_dump($name);
            $birthdate = GetValue($plaindata , "BIRTHDATE");
// var_dump($birthdate);
            $gender = GetValue($plaindata , "GENDER");
// var_dump($gender);
            $nationalinfo = GetValue($plaindata , "NATIONALINFO");	//��/�ܱ�������(����� �Ŵ��� ����)
// var_dump($nationalinfo);
            $dupinfo =@ GetValue($plaindata , "DI");
// var_dump($dupinfo);
            $conninfo =@ GetValue($plaindata , "CI");
// var_dump($conninfo);
			// �޴��� ��ȣ : MOBILE_NO => GetValue($plaindata , "MOBILE_NO");
			// ����� ���� : MOBILE_CO => GetValue($plaindata , "MOBILE_CO");
			// checkplus_success ���������� ����� null �� ���, ���� ���Ǵ� ��������ڿ��� �Ͻñ� �ٶ��ϴ�.
// var_dump('-------', $_SESSION["REQ_SEQ"],$requestnumber, strcmp($_SESSION["REQ_SEQ"], $requestnumber), '-------');
            if(strcmp($_SESSION["REQ_SEQ"], $requestnumber) != 0)
            {
/*
            	echo "���ǰ��� �ٸ��ϴ�. �ùٸ� ��η� �����Ͻñ� �ٶ��ϴ�.<br>";
                $requestnumber = "";
                $responsenumber = "";
                $authtype = "";
                $name = "";
            		$birthdate = "";
            		$gender = "";
            		$nationalinfo = "";
            		$dupinfo = "";
            		$conninfo = "";
*/
            }
        }
    }
	
	include dirname(__file__).'/../basic_config.php';
	include dirname(__file__).'/../db_class.php';
	$dbcon = new DB($db_host,$db_name,$db_user,$db_pass,$db_charset);
	if( !empty($userid) ) {
		$query = "insert into js_realname set userid='".$userid."', ciphertime='$ciphertime',requestnumber='$requestnumber',responsenumber='$responsenumber',authtype='$authtype',name='$name',birthdate='$birthdate',gender='$gender',nationalinfo='$nationalinfo',dupinfo='$dupinfo',conninfo='$conninfo',sReserved1='$sReserved1',sReserved2='$sReserved2',sReserved3='$sReserved3' on duplicate key update ciphertime='$ciphertime',requestnumber='$requestnumber',responsenumber='$responsenumber',authtype='$authtype',name='$name',birthdate='$birthdate',gender='$gender',nationalinfo='$nationalinfo',dupinfo='$dupinfo',conninfo='$conninfo',sReserved1='$sReserved1',sReserved2='$sReserved2',sReserved3='$sReserved3'";
		// var_dump($query);
		@ $dbcon->query($query);
		$query = "update js_member set level_code='JB37' where userid='".$userid."' ";
		@ $dbcon->query($query);
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
    <title>NICE�ſ������� - CheckPlus �������� �׽�Ʈ</title>
</head>
<body>
<script type="text/javascript">
window.opener.location.href='/member/memberEdit.php';
window.close();
</script>
<?/*
    <center>
    <p><p><p><p>
    ���������� �Ϸ� �Ǿ����ϴ�.<br>
    <table border=1>
        <tr>
            <td>��ȣȭ�� �ð�</td>
            <td><?= $ciphertime ?> (YYMMDDHHMMSS)</td>
        </tr>
        <tr>
            <td>��û ��ȣ</td>
            <td><?= $requestnumber ?></td>
        </tr>            
        <tr>
            <td>���������� ��ȣ</td>
            <td><?= $responsenumber ?></td>
        </tr>            
        <tr>
            <td>��������</td>
            <td><?= $authtype ?></td>
        </tr>
                <tr>
            <td>����</td>
            <td><?= $name ?></td>
        </tr>
                <tr>
            <td>�������</td>
            <td><?= $birthdate ?></td>
        </tr>
                <tr>
            <td>����</td>
            <td><?= $gender ?></td>
        </tr>
                <tr>
            <td>��/�ܱ�������</td>
            <td><?= $nationalinfo ?></td>
        </tr>
                <tr>
            <td>DI(64 byte)</td>
            <td><?= $dupinfo ?></td>
        </tr>
                <tr>
            <td>CI(88 byte)</td>
            <td><?= $conninfo ?></td>
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
