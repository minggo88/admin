<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/laboratory/stat_income.html 000005311 */  $this->include_("loopCurrency");
$TPL_trade_currency_1=empty($TPL_VAR["trade_currency"])||!is_array($TPL_VAR["trade_currency"])?0:count($TPL_VAR["trade_currency"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>거래소 수익</h2>
<ol class="breadcrumb">
<li>
<a href="/">Home</a>
</li>
<li>
통계
</li>
<li class="active">
<strong>거래소 수익</strong>
</li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>Search <small>상세조건별 검색 </small></h5>
</div>
<div class="ibox-content">
<div method="get" class="form-horizontal" name="srchform" id="srchform" action="<?php echo $_SERVER["SCRIPT_NAME"]?>">
<input type="hidden"  name="loop_scale" value="" />
<input type="hidden"  name="pg_mode" value="<?php echo $_GET["pg_mode"]?>" />
<input type="hidden"  name="start_date" value="<?php echo $_GET["start_date"]?>" />
<input type="hidden"  name="end_date" value="<?php echo $_GET["end_date"]?>" />
<div class="row">
<div class="form-horizontal" id="srchform">
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">종목</label>
<select id="s_symbol" name="s_symbol" class="form-control">
<?php echo loopCurrency( 1)?>
</select>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="type">표시방식</label>
<select name="type" class="form-control">
<option value="daily" <?php if($_GET["type"]=='daily'){?>selected<?php }?>>일별</option>
<option value="monthly" <?php if($_GET["type"]=='monthly'){?>selected<?php }?>>월별</option>
<option value="yearly" <?php if($_GET["type"]=='yearly'){?>selected<?php }?>>년도별</option>
</select>
</div>
<div class="col-sm-12 col-md-3 m-t-sm">
<label class="col-form-label" for="s_day">날짜</label>
<div id="reportrange" class="form-control">
<i class="fa fa-calendar"></i>
<span></span> <b class="caret"></b>
</div>
<input type="hidden"  name="start_date" value="<?php echo $_GET["start_date"]?>" />
<input type="hidden"  name="end_date" value="<?php echo $_GET["end_date"]?>" />
</div>
<!-- <div class="col-sm-4 col-md-2 m-t-sm">
<label class="col-form-label" for="s_year">년</label>
<select id="s_year" name="s_year" class="form-control">
<option value="" <?php if($_GET["s_year"]==''){?>selected<?php }?>>전체</option>
<?php for($y=2021; $y<=date('Y'); $y++) {
echo '<option value="'.$y.'" '.($_GET['s_year']==$y ? 'selected':'').'>'.$y.'</option>';
}?>
</select>
</div>
<div class="col-sm-4 col-md-2 m-t-sm">
<label class="col-form-label" for="s_month">월</label>
<select id="s_month" name="s_month" class="form-control">
<option value="" <?php if($_GET["s_month"]==''){?>selected<?php }?>>전체</option>
<?php for($m=1; $m<=12; $m++) {
$m2 = $m<10 ? '0'.$m : $m;
echo '<option value="'.$m2.'" '.($_GET['s_month']==$m2 ? 'selected':'').'>'.$m.'</option>';
}?>
</select>
</div>
<div class="col-sm-4 col-md-2 m-t-sm">
<label class="col-form-label" for="s_day">일</label>
<select id="s_day" name="s_day" class="form-control">
<option value="" <?php if($_GET["s_day"]==''){?>selected<?php }?>>전체</option>
<?php for($d=1; $d<=31; $d++) {
$d2 = $d<10 ? '0'.$d : $d;
echo '<option value="'.$d2.'" '.($_GET['s_day']==$d2 ? 'selected':'').'>'.$d.'</option>';
}?>
</select>
</div> -->
<div class="col-sm-12 col-md-2 m-t-sm">
<button class="btn btn-md btn-primary m-t-md" id="btn-search">검색</button>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-lg-12 m-t-md m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>거래소 수익 현황 </h5>
<div class="ibox-tools">
<a class="collapse-link">
<i class="fa fa-chevron-up"></i>
</a>
</div>
</div>
<div class="ibox-content">
<div class="table-responsive">
<table name="db_list" class="display dataTables-income" style="width:100%">
<thead>
<tr>
<th class="text-center" data-data="date" data-responsivePriority="0" data-className="text-center" data-orderable="1" data-orderSequence= "asc,desc">날짜</th>
<th class="text-center" data-data="total" data-responsivePriority="0" data-className="text-right" data-orderable="1" data-orderSequence= "desc,asc">전체수익(원)</th>
<?php if($TPL_trade_currency_1){foreach($TPL_VAR["trade_currency"] as $TPL_V1){?>
<?php if($TPL_V1["symbol"]==$_GET["s_symbol"]){?>
<th class="text-center" data-data="<?php echo $TPL_V1["symbol"]?>" data-responsivePriority="0" data-className="text-right" data-orderable="1" data-orderSequence= "desc,asc"><?php echo $TPL_V1["name"]?></th>
<?php }?>
<?php }}?>
</tr>
</thead>
<!-- <tbody>
<tr><td class="text-center" colspan="8">조회중입니다.</td></tr>
</tbody> -->
<tfoot>
<tr>
<th class="text-center">합계</th>
<th class="text-right" name="sum_total"></th>
<?php if($TPL_trade_currency_1){foreach($TPL_VAR["trade_currency"] as $TPL_V1){?>
<?php if($TPL_V1["symbol"]==$_GET["s_symbol"]){?>
<th class="text-center" name="sum_<?php echo $TPL_V1["symbol"]?>"></th>
<?php }?>
<?php }}?>
</tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>
</div>
</div>