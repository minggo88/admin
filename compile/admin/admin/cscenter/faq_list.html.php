<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/cscenter/faq_list.html 000003346 */ 
$TPL_loop_faq_1=empty($TPL_VAR["loop_faq"])||!is_array($TPL_VAR["loop_faq"])?0:count($TPL_VAR["loop_faq"]);?>
<script src="/template/admin/admin/js/jquery-3.1.1.min.js"></script>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>F.A.Q.s 관리</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a href="/cscenter/admin/faqAdmin.php">CS 관리</a>
</li>
<li class="active">
<strong>F.A.Q.s 관리</strong>
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
<h5> FAQ 목록 </h5>
<div style="float:right">
<select name="language">
<option value="kr" <?php if($_GET["language"]=='kr'){?>selected=""<?php }?> >한국어</option>
<option value="en" <?php if($_GET["language"]=='en'){?>selected=""<?php }?>>영어</option>
<option value="cn" <?php if($_GET["language"]=='cn'){?>selected=""<?php }?>>중국어</option>
</select>
</div>
</div>
<div class="ibox-content">
<div class="table-responsive">
<form id="list_form">
<input type="hidden" name="pg_mode" value="del_multi" />
<table class="table table-striped table-bordered table-hover list_table">
<colgroup>
<col width="40"></col>
<col width="50"></col>
<!-- <col width="150"></col> -->
<col width="*"></col>
<col width="100"></col>
</colgroup>
<thead>
<tr>
<th class="text-center"><input type="checkbox" id="all_check" /></th>
<th class="text-center">번호</th>
<!-- <th class="text-center">항목</th> -->
<th class="text-center">제목</th>
<th class="text-center">관리</th>
</tr>
</thead>
<tbody>
<?php if($TPL_loop_faq_1){foreach($TPL_VAR["loop_faq"] as $TPL_V1){?>
<tr>
<td class="text-center"><input type="checkbox" name="idxs[]"
value="<?php echo $TPL_V1["idx"]?>" /></td>
<td class="text-center">
<?php echo $TPL_V1["no"]?>
</td>
<td style="text-align: left;">
<?php echo $TPL_V1["subject"]?>
</td>
<td class="text-center">
<span class="button small black btn-info"><a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=form_edit&idx=<?php echo $TPL_V1["idx"]?><?php echo $TPL_VAR["srch_url"]?>" class="btn btn-info btn-xs">수정</a></span>
</td>
</tr>
<?php }}else{?>
<tr>
<td colspan="5" class="ctext">등록된 글이 없습니다.</td>
</tr>
<?php }?>
</tbody>
</table>
</form>
<div class="col-md-12 no-padding">
<div class="col-md-6 text-left"><a href="javascript:;" onclick="checkDel()" class="btn btn-danger">선택삭제</a></div>
<div class="col-md-6 text-right"><a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=form_new" class="btn btn-primary">새로작성</a></div>
</div>
<form name="searchform" id="searchform" method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="form-horizontal">
<div class="search_area pcenter">
<input type="text" name="s_val" value="<?php echo $_GET["s_val"]?>" class="frm_input" style="width:150px" />&nbsp;
<input type="submit" value="검색" class="btn btn-primary" />&nbsp;
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="btn btn-danger">취소</a>
</div>
</form>
<div id="navipage">
<?php echo $TPL_VAR["navi_page"]?>
</div>
</div>
</div>
</div>
</div>
</div>
</div>