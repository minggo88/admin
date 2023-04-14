<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/mypage/mtom_view.html 000002410 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>1:1 문의 상담관리</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a href="/cscenter/admin/faqAdmin.php">CS 관리</a>
</li>
<li class="active">
<strong>1:1 문의 관리</strong>
</li>
</ol>
</div>
<div class="col-lg-2"></div>
</div>
<div class="wrapper search-wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox ">
<div class="ibox-title">
<h5> 1:1 문의 상담관리 </h5>
</div>
<div class="ibox-content">
<div class="table-responsive">
<table class="form_table table table-striped table-bordered table-hover">
<colgroup>
<col width="15%"></col>
<col width="35%"></col>
<col width="15%"></col>
<col width="35%"></col>
</colgroup>
<tbody>
<tr>
<th>작성자</th>
<td><?php echo $TPL_VAR["author"]?>(<?php echo $TPL_VAR["userid"]?>)</td>
<th>작성시간</th>
<td><?php echo $TPL_VAR["regdate"]?></td>
</tr>
<tr>
<th>상담제목</th>
<td colspan="3"><?php echo $TPL_VAR["subject"]?></td>
</tr>
<tr>
<th>상담내용</th>
<td colspan="3"><?php echo $TPL_VAR["contents"]?></td>
</tr>
</tbody>
</table>
<div class="col-md-12 text-right">
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url"]?>" class="btn btn-primary btn-md" >목록</a>
<input type="button" value="삭제" onclick="mtomDel()"  class="btn btn-danger btn-md" />
</div>
<h3>답변내용</h3>
<form method="post" name="rplform" id="rplform" action="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="form-horizontal">
<input type="hidden" name="pg_mode" value="edit" />
<input type="hidden" name="idx"  value="<?php echo $TPL_VAR["idx"]?>" />
<table class="form_table table table-striped table-bordered table-hover">
<colgroup>
<col width="15%"></col>
<col width="85%"></col>
</colgroup>
<tbody>
<tr>
<th>답변시간</th>
<td><?php echo $TPL_VAR["rpldate"]?></td>
</tr>
<tr>
<td colspan="2"><textarea name="rplcontents" id="rplcontents" style="width:100%;height:300px"><?php echo $TPL_VAR["rplcontents"]?></textarea></td>
</tr>
</tbody>
</table>
<div class="col-md-12 text-right">
<input type="submit" value="답변확인"  class="btn btn-primary btn-md" />
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>