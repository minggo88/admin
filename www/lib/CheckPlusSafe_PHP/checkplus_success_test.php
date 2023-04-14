<?php
    
	file_put_contents(dirname(__file__).'/../../data/realname/'.date('Ymd').'.success.log', print_r($_POST, true));
	//**************************************************************************************************************
    //NICE신용평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
    
    //서비스명 :  체크플러스 - 안심본인인증 서비스
    //페이지명 :  체크플러스 - 결과 페이지
    
    //보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 
    //**************************************************************************************************************
    ini_set("session.save_path",$_SERVER['DOCUMENT_ROOT']."/../_session");
	session_start(); 
	$_SESSION["USER_REALNAME"] = iconv('euc-kr', 'utf-8', "0");
	
	var_dump($_SESSION); exit;
	
    $sitecode = "G5799";					// NICE로부터 부여받은 사이트 코드
    $sitepasswd = "4XB4Y7E32TK3";				// NICE로부터 부여받은 사이트 패스워드
    
    $cb_encode_path = "\절대경로\CPClient.exe";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
	$cb_encode_path = dirname(__file__)."/64bit/CPClient";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
		
    $enc_data = $_POST["EncodeData"];		// 암호화된 결과 데이타
    $sReserved1 = $_POST['param_r1'];		
		$sReserved2 = $_POST['param_r2'];
		$sReserved3 = $_POST['param_r3'];

		//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
	if(preg_match('~[^0-9a-zA-Z+/=]~', $enc_data, $match)) {echo "입력 값 확인이 필요합니다"; exit;}
	if(base64_encode(base64_decode($enc_data))!==$enc_data) {echo " 입력 값 확인이 필요합니다"; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved1, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved2, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
    if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $sReserved3, $match)) {echo "문자열 점검 : ".$match[0]; exit;}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		
    if ($enc_data != "") {

        $plaindata = `$cb_encode_path DEC $sitecode $sitepasswd $enc_data`;		// 암호화된 결과 데이터의 복호화
        //echo "[plaindata]  " . $plaindata . "<br>";

        if ($plaindata == -1){
            $returnMsg  = "암/복호화 시스템 오류";
        }else if ($plaindata == -4){
            $returnMsg  = "복호화 처리 오류";
        }else if ($plaindata == -5){
            $returnMsg  = "HASH값 불일치 - 복호화 데이터는 리턴됨";
        }else if ($plaindata == -6){
            $returnMsg  = "복호화 데이터 오류";
        }else if ($plaindata == -9){
            $returnMsg  = "입력값 오류";
        }else if ($plaindata == -12){
            $returnMsg  = "사이트 비밀번호 오류";
        }else{
            // 복호화가 정상적일 경우 데이터를 파싱합니다.
            $ciphertime = `$cb_encode_path CTS $sitecode $sitepasswd $enc_data`;	// 암호화된 결과 데이터 검증 (복호화한 시간획득)
        
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
            $nationalinfo = GetValue($plaindata , "NATIONALINFO");	//내/외국인정보(사용자 매뉴얼 참조)
// var_dump($nationalinfo);
            $dupinfo =@ GetValue($plaindata , "DI");
// var_dump($dupinfo);
            $conninfo =@ GetValue($plaindata , "CI");
// var_dump($conninfo);
			// 휴대폰 번호 : MOBILE_NO => GetValue($plaindata , "MOBILE_NO");
			// 이통사 정보 : MOBILE_CO => GetValue($plaindata , "MOBILE_CO");
			// checkplus_success 페이지에서 결과값 null 일 경우, 관련 문의는 관리담당자에게 하시기 바랍니다.
// var_dump('-------', $_SESSION["REQ_SEQ"],$requestnumber, strcmp($_SESSION["REQ_SEQ"], $requestnumber), '-------');
            if(strcmp($_SESSION["REQ_SEQ"], $requestnumber) != 0)
            {
/*
            	echo "세션값이 다릅니다. 올바른 경로로 접근하시기 바랍니다.<br>";
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
        $pos1 = 0;  //length의 시작 위치
        $pos2 = 0;  //:의 위치

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
                // 다르면 스킵한다.
                $pos2 = strpos( $str , ":" , $pos1);
                $len = substr($str , $pos1 , $pos2 - $pos1);
                $pos1 = $pos2 + $len + 1;
            }            
        }
    }
?>

<html>
<head>
    <title>NICE신용평가정보 - CheckPlus 본인인증 테스트</title>
</head>
<body>
<script type="text/javascript">
window.opener.location.href='/member/memberEdit.php';
window.close();
</script>
<?/*
    <center>
    <p><p><p><p>
    본인인증이 완료 되었습니다.<br>
    <table border=1>
        <tr>
            <td>복호화한 시간</td>
            <td><?= $ciphertime ?> (YYMMDDHHMMSS)</td>
        </tr>
        <tr>
            <td>요청 번호</td>
            <td><?= $requestnumber ?></td>
        </tr>            
        <tr>
            <td>나신평응답 번호</td>
            <td><?= $responsenumber ?></td>
        </tr>            
        <tr>
            <td>인증수단</td>
            <td><?= $authtype ?></td>
        </tr>
                <tr>
            <td>성명</td>
            <td><?= $name ?></td>
        </tr>
                <tr>
            <td>생년월일</td>
            <td><?= $birthdate ?></td>
        </tr>
                <tr>
            <td>성별</td>
            <td><?= $gender ?></td>
        </tr>
                <tr>
            <td>내/외국인정보</td>
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
