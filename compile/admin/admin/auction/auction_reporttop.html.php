<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/auction/auction_reporttop.html 000003112 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>신고 내역</h2>
<ol class="breadcrumb">
<li>Home</li>
<li>경매 관리</li>
<li class="active"><strong>신고 내역</strong></li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>검색 </h5>
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
<label class="col-sm-2 control-label">신고 종류</label>
<div class="col-sm-4">
<select name="report_type" class="form-control">
<option value="" <?php if($_GET["report_type"]==''){?>selected=""<?php }?>>전체</option>
<option value="C" <?php if($_GET["report_type"]=='C'){?>selected=""<?php }?>>저작권이있는 아트웍의 무단 사용</option>
<option value="S" <?php if($_GET["report_type"]=='S'){?>selected=""<?php }?>>노골적이고 민감한 콘텐츠</option>
<option value="R" <?php if($_GET["report_type"]=='R'){?>selected=""<?php }?>>성 차별 주의자 또는 인종 차별적 표현</option>
<option value="A" <?php if($_GET["report_type"]=='A'){?>selected=""<?php }?>>스팸</option>
<option value="E" <?php if($_GET["report_type"]=='E'){?>selected=""<?php }?>>기타</option>
</select>
</div>
<!-- <label class="col-sm-2 control-label">옥션 기간</label>
<div class="col-sm-4">
<div id="reportrange" class="form-control">
<i class="fa fa-calendar"></i>
<span></span> <b class="caret"></b>
</div>
</div> -->
</div>
<div class="form-group">
<label class="col-sm-2 control-label">상품 번호</label>
<div class="col-sm-4"><input type="text" name="goods_idx" value="<?php echo $_GET["goods_idx"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">상품 이름</label>
<div class="col-sm-4"><input type="text" name="goods_name" value="<?php echo $_GET["goods_name"]?>" class="form-control"></div>
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