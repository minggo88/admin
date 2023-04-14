<?php
if ( isset($_POST['rcptto']) && !empty($_POST['rcptto']) &&
 isset($_POST['subject']) && !empty($_POST['subject']) &&
 isset($_POST['content']) && !empty($_POST['content']) ) {

 $header  = "MIME-Version: 1.0rn";
 $header .= "Content-type: text/html; charset=euc-kr";
 $header .= "From: " . $_POST['from'] . "rn";
 $header .= "Reply-To: " . $_POST['from'] . "rn";
 $header .= "X-Mailer: PHP/" . phpversion() . "rn";

 header("Content-Type: text/html; charset=euc-kr");

 if ( mail($_POST['rcptto'], $_POST['subject'], $_POST['content'], $header) === FALSE ) {
  echo "메일 발송 실패!";
  exit;
 } else {
  echo "<p>메일 발송 성공!</p>";
  echo "<p>수신자: " . $_POST['rcptto'] . "<br />제목: " . $_POST['subject'] . "</p>";
  exit;
 }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>메일 보내기 예제</title>
<style type="text/css">
<!--
* { font-family:굴림; font-size:12px; }
h1 { font-size:18px; text-align:center; }
.title { width:20%; text-align:center; font-weight:bold; }
.box { width:100%; }
-->
</style>
<script type="text/javascript">
<!--
 function check_valid() {
  var form = document.mailform;

  if ( form.from.value == "" ) {
   alert("보내는 사람의 이메일 주소를 입력해 주십시오.");
   form.from.focus();
   return false;
  }


  if ( form.rcptto.value == "" ) {
   alert("받는 사람의 이메일 주소를 입력해 주십시오.");
   form.rcptto.focus();
   return false;
  }

  if ( form.subject.value == "" ) {
   alert("제목을 입력해 주십시오.");
   form.subject.focus();
   return false;
  }

  if ( form.content.value == "" ) {
   alert("내용을 입력해 주십시오.");
   form.content.focus();
   return false;
  }

  return true;
 }
//-->
</script>
</head>
<body>

<form name="mailform" method="post" onsubmit="javascript:return check_valid()">

<h1>메일 보내기 예제</h1>

<table width="60%" border="1" cellpadding="3" cellspacing="0" align="center">
 <tr>
  <td class="title">보내는사람</td>
  <td><input type="text" name="from" class="box"></td>
 </tr>
 <tr>
  <td class="title">받는사람</td>
  <td><input type="text" name="rcptto" class="box"></td>
 </tr>
 <tr>
  <td class="title">제목</td>
  <td><input type="text" name="subject" class="box"></td>
 </tr>
 <tr>
  <td class="title">내용</td>
  <td><textarea name="content" rows="30" class="box"></textarea></td>
 </tr>
</table>

<p align="center">
 <input type="submit" value="메일발송">
</p>

</form>

</body>
</html>