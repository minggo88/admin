<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/auction/auction_historytop.html 000002503 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>입찰 내역</h2>
<ol class="breadcrumb">
<li>Home</li>
<li>경매 관리</li>
<li class="active"><strong>입찰 내역</strong></li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>Aucion History search form elements </h5>
<div class="ibox-tools">
<a class="collapse-link">
<i class="fa fa-chevron-up"></i>
</a>
<a class="close-link">
<i class="fa fa-times"></i>
</a>
</div>
</div>
<div class="ibox-content">
<form method="get" class="form-horizontal" name="srchform" id="srchform" action="<?php echo $_SERVER["SCRIPT_NAME"]?>">
<input type="hidden"  name="loop_scale" value="" />
<input type="hidden"  name="pg_mode" value="<?php echo $_GET["pg_mode"]?>" />
<input type="hidden"  name="start_date" value="<?php echo $_GET["start_date"]?>" />
<input type="hidden"  name="end_date" value="<?php echo $_GET["end_date"]?>" />
<div class="form-group">
<label class="col-sm-2 control-label">옥션 번호</label>
<div class="col-sm-4"><input type="text" name="auction_idx" value="<?php echo $_GET["auction_idx"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">제품 이름</label>
<div class="col-sm-4"><input type="text" name="title" value="<?php echo $_GET["title"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">옥션 제목</label>
<div class="col-sm-4"><input type="text" name="auction_title" value="<?php echo $_GET["auction_title"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">옥션 기간</label>
<div class="col-sm-4">
<div id="reportrange" class="form-control">
<i class="fa fa-calendar"></i>
<span></span> <b class="caret"></b>
</div>
</div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button class="btn btn-primary" type="submit">검색</button>
<button class="btn btn-white" type="button" onclick="javascript:location.href=('<?php echo $_SERVER["SCRIPT_NAME"]?>');">초기화</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
<div id="main_contents"><?php $this->print_("js_tpl_main_sub",$TPL_SCP,1);?></div>