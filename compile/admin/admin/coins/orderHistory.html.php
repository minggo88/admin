<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/coins/orderHistory.html 000002868 */  $this->include_("loopCurrency");?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>주문내역확인</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>거래소 관리</a>
</li>
<li class="active">
<strong>주문내역확인</strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12 m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>주문내역확인</h5>
</div>
<div class="ibox-content">
<div class="row">
<div class="form-horizontal" id="srchform">
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">종목</label>
<select id="s_symbol" name="s_symbol" class="form-control">
<?php echo loopCurrency( 1)?>
</select>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">기준</label>
<select id="s_div" name="s_div" class="form-control">
<option value="regdate" selected>주문날짜</option>
<!--<option value="txndate" >처리일</option>-->
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
<div class="col-sm-12 col-md-2 m-t-sm">
<button class="btn btn-md btn-primary m-t-md" id="btn-search">검색</button>
</div>
</div>
</div>
<p style="margin-top:10px"></p>
<div class="table-responsive">
<table class="table table-striped table-bordered table-hover dataTables-customers">
<thead>
<tr>
<!-- <th>No</th> -->
<th class="text-center">주문번호</th>
<th class="text-center">회원아이디</th>
<th class="text-center">주문날자</th>
<th class="text-center">구분</th>
<th class="text-center">상태</th>
<th class="text-center">주문가격</th>
<th class="text-center">주문수량</th>
<th class="text-center">남은수량</th>
<th class="text-center">금액</th>
<th class="text-center">최근거래날짜</th>
</tr>
</thead>
<tfoot>
<tr>
<!-- <th>No</th> -->
<th class="text-center">주문번호</th>
<th class="text-center">회원아이디</th>
<th class="text-center">날자</th>
<th class="text-center">구분</th>
<th class="text-center">상태</th>
<th class="text-center">주문가격</th>
<th class="text-center">주문수량</th>
<th class="text-center">남은수량</th>
<th class="text-center">금액</th>
<th class="text-center">최근거래날짜</th>
</tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>
</div>
</div>