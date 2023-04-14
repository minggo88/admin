<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/auction/auction_goodstop.html 000002810 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>경매상품 목록</h2>
<ol class="breadcrumb">
<li>Home</li>
<li>경매 관리</li>
<li class="active"><strong>겅매상품 목록</strong></li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>경매 상품 검색</h5>
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
<label class="col-sm-2 control-label">상품번호</label>
<div class="col-sm-4"><input type="text" name="goods_idx" value="<?php echo $_GET["goods_idx"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">상품명</label>
<div class="col-sm-4"><input type="text" name="title" value="<?php echo $_GET["title"]?>" class="form-control"></div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">패키지 상품 표시 여부</label>
<div class="col-sm-4">
<select name="pack_info"  class="form-control">
<option value="">모두</option>
<option value="Y">패키지 상품</option>
<option value="N">단일 상품</option>
</select>
</div>
</div>
<!-- <div class="form-group">
<label class="col-sm-2 control-label">소유주이름</label>
<div class="col-sm-4"><input type="text" name="goods_owner" value="<?php echo $_GET["goods_owner"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">옥션 기간</label>
<div class="col-sm-4">
<div id="reportrange" class="form-control">
<i class="fa fa-calendar"></i>
<span></span> <b class="caret"></b>
</div>
</div>
</div> -->
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