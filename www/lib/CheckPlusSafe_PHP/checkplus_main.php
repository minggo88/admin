<?php
    //**************************************************************************************************************
    //NICE�ſ������� Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
    
    //���񽺸� :  üũ�÷��� - �Ƚɺ������� ����
    //�������� :  üũ�÷��� - ���� ȣ�� ������
    
    //������ ���� �����ص帮�� ������������ ���� ���� �� �������� ������ �ֽñ� �ٶ��ϴ�. 
    //**************************************************************************************************************
    
    session_start();
    
    $sitecode = "G5799";				// NICE�κ��� �ο����� ����Ʈ �ڵ�
    $sitepasswd = "4XB4Y7E32TK3";			// NICE�κ��� �ο����� ����Ʈ �н�����
    
    $cb_encode_path = "\������\CPClient.exe";		// NICE�κ��� ���� ��ȣȭ ���α׷��� ��ġ (������+����)
    $cb_encode_path = dirname(__file__)."/64bit/CPClient";		// NICE�κ��� ���� ��ȣȭ ���α׷��� ��ġ (������+����)
    
    $authtype = "";      	// ������ �⺻ ����ȭ��, X: ����������, M: �ڵ���, C: ī��
    	
		$popgubun 	= "N";		//Y : ��ҹ�ư ���� / N : ��ҹ�ư ����
		$customize 	= "";			//������ �⺻ �������� / Mobile : �����������
    
    $reqseq = "REQ_0123456789";     // ��û ��ȣ, �̴� ����/�����Ŀ� ���� ������ �ǵ����ְ� �ǹǷ�
                                    // ��ü���� �����ϰ� �����Ͽ� ���ų�, �Ʒ��� ���� �����Ѵ�.
    $reqseq = `$cb_encode_path SEQ $sitecode`;
    
    // CheckPlus(��������) ó�� ��, ��� ����Ÿ�� ���� �ޱ����� ���������� ���� http���� �Է��մϴ�.
    $returnurl = "http://www.smcc.io/lib/CheckPlusSafe_PHP/checkplus_success.php";	// ������ �̵��� URL
    $errorurl = "http://www.smcc.io/lib/CheckPlusSafe_PHP/checkplus_fail.php";		// ���н� �̵��� URL
	
    // reqseq���� ������������ �� ��� ������ ���Ͽ� ���ǿ� ��Ƶд�.
    
    $_SESSION["REQ_SEQ"] = $reqseq;

    // �Էµ� plain ����Ÿ�� �����.
    $plaindata =  "7:REQ_SEQ" . strlen($reqseq) . ":" . $reqseq .
			    			  "8:SITECODE" . strlen($sitecode) . ":" . $sitecode .
			    			  "9:AUTH_TYPE" . strlen($authtype) . ":". $authtype .
			    			  "7:RTN_URL" . strlen($returnurl) . ":" . $returnurl .
			    			  "7:ERR_URL" . strlen($errorurl) . ":" . $errorurl .
			    			  "11:POPUP_GUBUN" . strlen($popgubun) . ":" . $popgubun .
			    			  "9:CUSTOMIZE" . strlen($customize) . ":" . $customize ;
    
    $enc_data = `$cb_encode_path ENC $sitecode $sitepasswd $plaindata`;

    if( $enc_data == -1 )
    {
        $returnMsg = "��/��ȣȭ �ý��� �����Դϴ�.";
        $enc_data = "";
    }
    else if( $enc_data== -2 )
    {
        $returnMsg = "��ȣȭ ó�� �����Դϴ�.";
        $enc_data = "";
    }
    else if( $enc_data== -3 )
    {
        $returnMsg = "��ȣȭ ������ ���� �Դϴ�.";
        $enc_data = "";
    }
    else if( $enc_data== -9 )
    {
        $returnMsg = "�Է°� ���� �Դϴ�.";
        $enc_data = "";
    }
?>


<html>
<head>
	<title>NICE�ſ������� - CheckPlus �Ƚɺ������� �׽�Ʈ</title>
	
	<script language='javascript'>
	window.name ="Parent_window";
	
	function fnPopup(){
		window.open('', 'popupChk', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
		document.form_chk.action = "https://nice.checkplus.co.kr/CheckPlusSafeModel/checkplus.cb";
		document.form_chk.target = "popupChk";
		document.form_chk.submit();
	}
	</script>
</head>
<body>
	<?= $returnMsg ?><br><br>
	��ü���� ��ȣȭ ����Ÿ : [<?= $enc_data ?>]<br><br>

	<!-- �������� ���� �˾��� ȣ���ϱ� ���ؼ��� ������ ���� form�� �ʿ��մϴ�. -->
	<form name="form_chk" method="post">
		<input type="hidden" name="m" value="checkplusSerivce">						<!-- �ʼ� ����Ÿ��, �����Ͻø� �ȵ˴ϴ�. -->
		<input type="hidden" name="EncodeData" value="<?= $enc_data ?>">		<!-- ������ ��ü������ ��ȣȭ �� ����Ÿ�Դϴ�. -->
	    
	    <!-- ��ü���� ����ޱ� ���ϴ� ����Ÿ�� �����ϱ� ���� ����� �� ������, ������� ����� �ش� ���� �״�� �۽��մϴ�.
	    	 �ش� �Ķ���ʹ� �߰��Ͻ� �� �����ϴ�. -->
		<input type="hidden" name="param_r1" value="">
		<input type="hidden" name="param_r2" value="">
		<input type="hidden" name="param_r3" value="">
	    
		<a href="javascript:fnPopup();"> CheckPlus �Ƚɺ������� Click</a>
	</form>
</body>
</html>
