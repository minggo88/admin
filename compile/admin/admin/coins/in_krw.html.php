<?php /* Template_ 2.2.6 2022/12/09 17:06:26 /home/ubuntu/www/admin/www/template/admin/admin/coins/in_krw.html 000003062 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>입금 내역</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>거래소 관리</a>
</li>
<li class="active">
<strong>입금 내역</strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12 m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>입금 내역</h5>
</div>
<div class="ibox-content">
<div class="row">
<div class="form-horizontal" id="srchform">
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="s_div">기준</label>
<select id="s_div" name="s_div" class="form-control">
<option value="regdate"  <?php if($_GET["s_div"]=='regdate'){?>selected<?php }?>>등록일</option>
<option value="txndate"  <?php if($_GET["s_div"]=='txndate'){?>selected<?php }?>>처리일</option>
</select>
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
<label class="col-form-label" for="name">이름</label>
<input type="text" name="name" value="<?php echo $_GET["name"]?>" class="form-control">
</div>
<div class="col-sm-12 col-md-2 m-t-sm">
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
<p style="margin-top:10px"><strong>화폐 : </strong> <?php echo strtoupper($_GET["symbol"])?></p>
<div class="table-responsive">
<table class="table table-striped table-bordered table-hover dataTables-customers">
<thead>
<tr>
<th class="text-center">No</th>
<th class="text-center">등록날짜</th>
<th class="text-center">처리날짜</th>
<th class="text-center">회원 아이디</th>
<th class="text-center"><?php if(strtoupper($_GET["symbol"])=='USD'){?>입금자명<?php }else{?>입금자 주소<?php }?></th>
<th class="text-center">거래종류</th>
<th class="text-center">금액</th>
<th class="text-center">수수료</th>
<th class="text-center">상태</th>
<th class="text-center">기능</th>
</tr>
</thead>
<tfoot>
<tr>
<th class="text-center">No</th>
<th class="text-center">등록날짜</th>
<th class="text-center">처리날짜</th>
<th class="text-center">회원 아이디</th>
<th class="text-center"><?php if(strtoupper($_GET["symbol"])=='USD'){?>입금자명<?php }else{?>입금자 주소<?php }?></th>
<th class="text-center">거래종류</th>
<th class="text-center">금액</th>
<th class="text-center">수수료</th>
<th class="text-center">상태</th>
<th class="text-center">기능</th>
</tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>
</div>
</div>