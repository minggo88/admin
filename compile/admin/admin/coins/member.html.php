<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/coins/member.html 000003831 */  $this->include_("loopCurrency");?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>회원별 상품보유수량</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>거래소 관리</a>
</li>
<li class="active">
<strong>회원별 상품보유수량</strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12 m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>회원별 상품보유수량</h5>
</div>
<div class="ibox-content">
<div class="row">
<div class="form-horizontal" id="srchform">
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">종목</label>
<select id="symbol" name="symbol" class="form-control">
<option value=""></option>
<?php echo loopCurrency( 1)?>
</select>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">아이디</label>
<input type="text"  name="userid" value="<?php echo $_GET["userid"]?>" class="form-control"/>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">이름</label>
<input type="text"  name="name" value="<?php echo $_GET["name"]?>" class="form-control"/>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">닉네임</label>
<input type="text"  name="nickname" value="<?php echo $_GET["nickname"]?>" class="form-control"/>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<button class="btn btn-md btn-primary m-t-md" id="btn-search">검색</button>
</div>
</div>
</div>
<p style="margin-top:10px"></p>
<!-- <form method="get" class="form-horizontal" name="srchform" id="srchform" action="<?php echo $_SERVER["SCRIPT_NAME"]?>">
<input type="hidden"  name="loop_scale" value="" />
<input type="hidden"  name="pg_mode" value="<?php echo $_GET["pg_mode"]?>" />
<input type="hidden"  name="start_date" value="<?php echo $_GET["start_date"]?>" />
<input type="hidden"  name="end_date" value="<?php echo $_GET["end_date"]?>" />
<div class="form-group">
<label class="col-sm-2 control-label">Mobile</label>
<div class="col-sm-4"><input type="text" name="mobile" value="<?php echo $_GET["mobile"]?>" class="form-control"></div>
<label class="col-sm-2 control-label">회원가입일</label>
<div class="col-sm-4">
<div id="reportrange" class="form-control">
<i class="fa fa-calendar"></i>
<span></span> <b class="caret"></b>
</div>
</div>
</div>
</form> -->
<div class="table-responsive">
<table class="table table-striped table-bordered table-hover dataTables-customers">
<thead>
<tr>
<th class="text-center">번호</th>
<th class="text-center">가입날짜</th>
<th class="text-center">아이디</th>
<th class="text-center">닉네임</th>
<th class="text-center">이름</th>
<th class="text-center">종목</th>
<!-- <th class="text-center">지갑주소</th> -->
<th class="text-center">잔액</th>
<th class="text-center">잠김여부</th>
<th class="text-center">자동잠김여부</th>
<th class="text-center">마지막 입금 확인날짜</th>
<th class="text-center"></th>
</tr>
</thead>
<tfoot>
<tr>
<th class="text-center">번호</th>
<th class="text-center">가입날짜</th>
<th class="text-center">아이디</th>
<th class="text-center">닉네임</th>
<th class="text-center">이름</th>
<th class="text-center">종목</th>
<!-- <th class="text-center">지갑주소</th> -->
<th class="text-center">잔액</th>
<th class="text-center">잠김여부</th>
<th class="text-center">자동잠김여부</th>
<th class="text-center">마지막 입금 확인날짜</th>
<th class="text-center"></th>
</tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>
</div>
</div>