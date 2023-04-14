<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Captcha</title>
<style type="text/css">
<!--
body {
	background: #e4e4e8;
}
img {
	border:0px;
}
input {
	margin: 0px auto;
	width: 98%;
}
.captcha {
	width: 200px;
	margin: 10px auto 0px;
	border: 1px solid #999;
	background: #d0d0d4;
}
.titr{
	text-align: center;
	font-size: 36px;
	margin-top: 150px;
}
.showCode{
	width: 200px;
	margin: 0px auto;
}
-->
</style>
<script language="javascript" type="text/javascript">
function RefreshCaptcha()
{
	var captchaImage = document.getElementById('captcha');
	captchaImage.src = 'securimage/securimage_show.php?sid=' + Math.random();
	return false
}
</script>
</head>
<body>
<div class="titr">
	PHP Fusion 7 Captcha
</div>
<div class="captcha"> 
    <img src="securimage/securimage_show.php?sid=<?php echo time() ?>" alt="Validation Code:" name="captcha" width="145" height="45" align="left" id="captcha"/> <a onclick="RefreshCaptcha();" href="#"> <img align="top" alt="" src="securimage/images/refresh.gif"/> </a> <a href="securimage/securimage_play.php"> <img align="top" style="margin-bottom: 1px;" alt="" src="securimage/images/audio_icon.gif"/> </a> <br />
  <div style="clear:both;"></div>
 </div>
<div class="showCode"><a href="getCode.php" target="_blank">Show code</a></div>
</body>
</html>

<!-- 
Created by Mohammad Dayyan
My wblog : http://www.mds-soft.persianblog.ir/
1387/10/5
-->
