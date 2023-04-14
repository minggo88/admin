<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/auth_form.html 000002497 */ ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to <?php echo $TPL_VAR["config_basic"]["license_company"]?> Admin</title>
<link href="/template/admin/admin/css/bootstrap.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/font-awesome/css/font-awesome.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/animate.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/style.css?t=1666836874" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen animated fadeInDown">
<div>
<div>
<h1 class="logo-name">
<img src="/template/admin/smc/img/main/kmcse-stock-logo.png" style="object-fit: contain;">
</h1>
</div>
<div>
<h2>Welcome to Admin</h2>
</div>
<h3><?php echo $TPL_VAR["config_basic"]["license_company"]?></h3>
<p>Login in. To see it in action.</p>
<form method="post" class="m-t" name="authform" id="authform" action="/admin/auth.php">
<input type="hidden" name="pg_mode" value="auth">
<input type="hidden" name="ret_url" value="<?php echo $TPL_VAR["ret_url"]?>">
<input type="hidden" name="level" value="x">
<div class="form-group">
<input type="text" name="adminid" class="form-control" placeholder="Userid">
</div>
<div class="form-group">
<input type="password" name="adminpw" class="form-control" placeholder="Password">
</div>
<?php if($TPL_VAR["otpuse"]> 0){?>
<div class="form-group">
<input type="password" name="otppw" class="form-control" placeholder="OTP키를 숫자만 입력해주세요.">
</div>
<?php }?>
<input type="submit" class="btn btn-primary block full-width m-b" value="Login" />
</form>
<p class="m-t"> <small><?php echo $TPL_VAR["config_basic"]["license_company"]?> &copy; 2021</small> </p>
</div>
</div>
<!-- Mainly scripts -->
<script src="/template/admin/admin/js/jquery-3.1.1.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/bootstrap.min.js?t=1666836874"></script>
<script>
$(document).ready(function(){
$('#authform').submit(function() {
if(this.adminid.value == '') {
alert('UserID를 입력하여 주세요.!');
this.adminid.focus();
return false;
}
if(this.adminpw.value == '') {
alert('Password를 입력하여 주세요.!');
this.adminpw.focus();
return false;
}
});
$('adminid').focus();
});
</script>
</body>
</html>