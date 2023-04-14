<?php /* Template_ 2.2.6 2022/12/09 17:06:26 /home/ubuntu/www/admin/www/template/admin/admin/member/customers-balance.html 000002399 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>일반회원</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a href="/member/admin/memberAdmin.php?pg_mode=customers">회원관리</a>
</li>
<li class="active">
<strong>일반회원</strong>
</li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>Customers search form elements </h5>
</div>
<div class="ibox-content">
<form method="get" class="form-horizontal" name="srchform" id="srchform" action="<?php echo $_SERVER["SCRIPT_NAME"]?>">
<input type="hidden"  name="loop_scale" value="" />
<input type="hidden"  name="pg_mode" value="<?php echo $_GET["pg_mode"]?>" />
<input type="hidden"  name="start_date" value="<?php echo $_GET["start_date"]?>" />
<input type="hidden"  name="end_date" value="<?php echo $_GET["end_date"]?>" />
<div class="form-group">
<label class="col-sm-2 control-label">아이디</label>
<div class="col-sm-4"><input type="text" name="userid" value="<?php echo $_GET["userid"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">이름</label>
<div class="col-sm-4"><input type="text" name="name" value="<?php echo $_GET["name"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">SYMBOL이름</label>
<div class="col-sm-4"><input type="text" name="symbol_name" value="<?php echo $_GET["symbol_name"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">등급</label>
<div class="col-sm-4"><input type="text" name="goods_grade" value="<?php echo $_GET["goods_grade"]?>" class="form-control"></div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button class="btn btn-primary" type="submit">검색</button>
<button class="btn btn-white" type="button" onclick="javascript:location.href=('<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=customers');">초기화</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
<div id="main_contents"><?php $this->print_("js_tpl_main_sub",$TPL_SCP,1);?></div>