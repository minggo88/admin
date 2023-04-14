<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/header.html 000001122 */ ?>
<div class="row border-bottom">
<nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
<div class="navbar-header">
<a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
<!-- <form role="search" class="navbar-form-custom" action="search_results.html">
<div class="form-group">
<input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
</div>
</form> -->
</div>
<ul class="nav navbar-top-links navbar-right">
<li>
<span class="m-r-sm text-muted welcome-message">Welcome to <?php echo $TPL_VAR["config_basic"]["license_company"]?> Admin!</span>
</li>
<li>
<?php if($_SESSION["ADMIN_ID"]){?>
<a href="/admin/auth.php?pg_mode=out">
<i class="fa fa-sign-out"></i> Log out
</a>
<?php }else{?>
<a href="/admin/">
<i class="fa fa-sign-in"></i> Log in
</a>
<?php }?>
</li>
<li>
<a class="right-sidebar-toggle">
<i class="fa fa-tasks"></i>
</a>
</li>
</ul>
</nav>
</div>