<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/bbs/bbs_view.html 000004089 */ ?>
<style type="text/css">
div#loop_comment {margin:10px 0px; border-top:1px solid #999; text-align:right;}
table.comment_table {width:100%;}
table.comment_reply_table {width:90%;}
.comment_info_row th,
.comment_info_row td {height:18px; padding: 5px;border-bottom:1px dashed #999;}
.comment_contents_row td {padding: 5px;border-bottom:1px solid #999;}
div.comment_form {background:#f6f6f6; margin: 0px 0px 0px 0px; padding:5px 10px; border:0px solid #ccc; clear:both;}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2><?php echo $TPL_VAR["info_bbs"]["title"]?></h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>커뮤니티관리</a>
</li>
<li class="active">
<strong><?php echo $TPL_VAR["info_bbs"]["title"]?></strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content  animated fadeInRight">
<div class="row">
<div class="col-lg-12">
<div class="ibox ">
<div class="ibox-title">
<h5><?php echo $TPL_VAR["info_bbs"]["title"]?></h5>
</div>
<div class="ibox-content m-b-lg">
<table class="form_table table table-striped table-bordered table-hover">
<colgroup>
<col width="15%"></col>
<col width="35%"></col>
<col width="50%"></col>
</colgroup>
<tbody>
<tr>
<th>글제목</th>
<td colspan="2">
<h3>(한국어) <?php echo $TPL_VAR["subject_kr"]?></h3>
<h3>(영어) <?php echo $TPL_VAR["subject_en"]?></h3>
<h3>(중국어) <?php echo $TPL_VAR["subject_cn"]?></h3>
</td>
</tr>
<tr>
<th>글쓴이</th>
<td><?php echo $TPL_VAR["author"]?></td>
<td>날짜 : <?php echo date('Y-m-d H:m:i',$TPL_VAR["regdate"])?> &nbsp; 조회 : <?php echo $TPL_VAR["hit"]?> &nbsp; 추천 : <?php echo $TPL_VAR["recommand"]?></td>
</tr>
<tr>
<th>첨부화일</th>
<td colspan="2">
<a href="<?php echo $TPL_VAR["file"]?>"><?php echo str_replace('<img ','<img style="max-width:300px;" ',$TPL_VAR["file_src_html"])?></a>
</td>
</tr>
<tr>
<th>한국어 내용</th>
<td colspan="2" style="padding:10px 20px;margin:0px;">
<?php echo $TPL_VAR["contents_kr"]?>
</td>
</tr>
<tr>
<th>영어 내용</th>
<td colspan="2" style="padding:10px 20px;margin:0px;">
<?php echo $TPL_VAR["contents_en"]?>
</td>
</tr>
<tr>
<th>중국어 내용</th>
<td colspan="2" style="padding:10px 20px;margin:0px;">
<?php echo $TPL_VAR["contents_cn"]?>
</td>
</tr>
</tbody>
</table>
<div class="col-md-12 no-padding m-b-md">
<div class="col-md-6 no-padding text-let">
<a href="<?php if(!$TPL_VAR["up_info"]["bool_no_list"]){?><?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=view&idx=<?php echo $TPL_VAR["up_info"]["idx"]?>&list_cnt=<?php echo $TPL_VAR["up_info"]["list_cnt"]?>&start=<?php echo $TPL_VAR["up_info"]["start"]?><?php echo $TPL_VAR["up_info"]["srch_url"]?><?php }?>" id="btn_bbs_up" class="btn btn-default" >이전</a>
<a href="<?php if(!$TPL_VAR["down_info"]["bool_no_list"]){?><?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=view&idx=<?php echo $TPL_VAR["down_info"]["idx"]?>&list_cnt=<?php echo $TPL_VAR["down_info"]["list_cnt"]?>&start=<?php echo $TPL_VAR["down_info"]["start"]?><?php echo $TPL_VAR["down_info"]["srch_url"]?><?php }?>" id="btn_bbs_down" class="btn btn-default" >다음</a>
</div>
<div class="col-md-6 no-padding text-right">
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url"]?>" class="btn btn-default">목록</a>
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=form_new<?php echo $TPL_VAR["srch_url"]?>" class="btn btn-primary">새글</a>
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=form_edit&idx=<?php echo $_GET["idx"]?>&pos=<?php echo $TPL_VAR["pos"]?>&thread=<?php echo $TPL_VAR["thread"]?>&depth=<?php echo $TPL_VAR["depth"]?><?php echo $TPL_VAR["srch_url"]?>" class="btn btn-warning">수정</a>
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=del&bbscode=<?php echo $_GET["bbscode"]?>&idx=<?php echo $_GET["idx"]?>&ret_url=<?php echo $TPL_VAR["ret_url"]?>" class="btn btn-danger">삭제</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>