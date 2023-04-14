<?php /* Template_ 2.2.6 2022/10/28 17:49:34 /home/ubuntu/www/admin/www/template/admin/admin/auction/auction_goods_excel_upload.html 000003064 */ 
$TPL_check_list_1=empty($TPL_VAR["check_list"])||!is_array($TPL_VAR["check_list"])?0:count($TPL_VAR["check_list"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>경매상품 목록</h2>
<ol class="breadcrumb">
<li>Home</li>
<li>경매 관리</li>
<li class="active"><strong>대량 상품 등록</strong></li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<form method="post" name="excel_form" class="form-horizontal" enctype="multipart/form-data" action="<?php echo $_SERVER["SCRIPT_NAME"]?>">
<input type="hidden" name="pg_mode" value="file_upload" />
<input type="hidden" name="upload_type" value="" />
<div class="ibox-content">
<div class="form-group">
<label class="col-sm-2 control-label">상품 업로드</label>
<div class="col-sm-4">
<input type="file" name="goods_file_data" id="goods_file_data">
</div>
<label class="col-sm-2 control-label">상품 추가 업로드</label>
<div class="col-sm-4">
<input type="file" name="add_goods_file_data" id="add_goods_file_data">
</div>
</div>
<div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button class="btn" type="button" name="button" onclick="location.href='/auction/admin/kkikda2_goods_upload.xlsx';">상품등록 샘플 파일 다운로드</button>
<button class="btn btn-primary" type="button" name="goods_upload">파일 업로드</button>
</div>
<div class="col-sm-4 col-sm-offset-2">
<button class="btn" type="button" name="button" onclick="location.href='/auction/admin/kkikda2_goods_add.xlsx';">상품추가 샘플 파일 다운로드</button>
<button class="btn btn-primary" type="button" name="goods_add_uplaod">파일 업로드</button>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
<div id="main_contents"></div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox ">
<div class="ibox-title">
<h5 id="page_title"> 상품 등록 결과</h5>
</div>
<div class="ibox-content">
<div id="box_list" class="">
<div class="table-responsive">
<table class="table table-striped table-bordered table-hover dataTables-customers">
<colgroup>
<col width="100">
<col width="120">
<col width="100">
<col width="100">
<col width="100">
</colgroup>
<thead>
<tr class="nodrop nodrag">
<td>엑셀 행번호</td>
<td>결과</td>
<td>상품번호</td>
<td>재고번호</td>
<td>차이름</td>
</tr>
</thead>
<?php if($TPL_check_list_1){foreach($TPL_VAR["check_list"] as $TPL_V1){?>
<tr class="tr_list">
<td><?php echo $TPL_V1["row"]+ 1?></td>
<td><?php echo $TPL_V1["message"]?></td>
<td><?php echo $TPL_V1["idx"]?></td>
<td><?php echo $TPL_V1["stock_number"]?></td>
<td><?php echo $TPL_V1["title"]?></td>
</tr>
<?php }}?>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
</div>