<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/cscenter/faq_form.html 000003211 */ 
$TPL_loop_code_1=empty($TPL_VAR["loop_code"])||!is_array($TPL_VAR["loop_code"])?0:count($TPL_VAR["loop_code"]);?>
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
<div class="wrapper search-wrapper-content animated fadeInRight mb-50">
<div class="row">
<div class="col-lg-12">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5> FAQ 등록/수정 </h5>
</div>
<div class="ibox-content">
<div class="">
<form method="post" name="faqform" id="faqform" action="?" enctype="multipart/form-data" class="form-horizontal">
<input type="hidden" name="pg_mode" value="<?php if($_GET["pg_mode"]=='form_new'){?>write<?php }else{?>edit<?php }?>" />
<?php if($_GET["pg_mode"]=='form_edit'){?>
<input type="hidden" name="idx" value="<?php echo $TPL_VAR["idx"]?>" />
<?php }?>
<table  class="table table-striped table-bordered table-hover form_table">
<colgroup>
<col width="15%" />
<col width="85%" />
</colgroup>
<tbody>
<tr>
<th class="pbold ">항목</th>
<td class="pleft"><select name="faqcode" id="faqcode">
<option value="">:::FAQ항목선택:::</option>
<?php if($TPL_loop_code_1){foreach($TPL_VAR["loop_code"] as $TPL_V1){?>
<option value="<?php echo $TPL_V1["faqcode"]?>"><?php echo $TPL_V1["title"]?></option>
<?php }}?>
</select></td>
</tr>
<tr>
<th>제목</th>
<td>
<label class="control-label">한글</label>
<input type="text" name="subject_kr" value="<?php echo $TPL_VAR["subject_kr"]?>" class=" form-control"  style="margin-bottom: 10px;"/>
<label class="control-label">영문</label>
<input type="text" name="subject_en" value="<?php echo $TPL_VAR["subject_en"]?>" class="form-control" style="margin-bottom: 10px;"/>
<label class="control-label">중문</label>
<input type="text" name="subject_cn" value="<?php echo $TPL_VAR["subject_cn"]?>" class=" form-control" />
</td>
</tr>
</tbody>
</table>
<div class=""><br /><br />
<label class="control-label">내용(한국어)</label>
<textarea name="contents_kr" id="contents_kr" style="width:100%;height:300px;margin:0px auto;"><?php echo $TPL_VAR["contents_kr"]?></textarea>
</div>
<div class=""><br /><br />
<label class="control-label">내용(영문)</label>
<textarea name="contents_en" id="contents" style="width:100%;height:300px;margin:0px auto;"><?php echo $TPL_VAR["contents_en"]?></textarea>
</div>
<div class=""><br /><br />
<label class="control-label">내용(중문)</label>
<textarea name="contents_cn" id="contents_cn" style="width:100%;height:300px;margin:0px auto;"><?php echo $TPL_VAR["contents_cn"]?></textarea><br /><br />
</div>
<div class="form-group">
<div class="col-md-12 mt-50 mb-50">
<input type="submit" value="확인" class="btn btn-primary" />&nbsp;
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="btn btn-info">목록</a>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
</div>