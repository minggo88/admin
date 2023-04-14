<?php /* Template_ 2.2.6 2022/12/09 17:06:26 /home/ubuntu/www/admin/www/template/admin/admin/bbs/bbs_form.html 000004945 */ ?>
<script type="text/javascript">
<!--
// var myeditor = new cheditor("myeditor");
//-->
</script>
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
<form method="post" name="bbsform" id="bbsform" action="?" enctype="multipart/form-data" class="form-horizontal">
<input type="hidden" name="pg_mode" value="<?php echo $TPL_VAR["pg_mode"]?>"/>
<?php if($_GET["pg_mode"]=='form_edit'){?>
<input type="hidden" name="idx" value="<?php echo $_GET["idx"]?>" />
<?php }?>
<?php if($_GET["pg_mode"]=='form_reply'){?>
<input type="hidden" name="passwd" value="<?php echo $TPL_VAR["passwd"]?>" />
<input type="hidden" name="pos" value="<?php echo $TPL_VAR["pos"]?>" />
<input type="hidden" name="thread" value="<?php echo $TPL_VAR["thread"]?>" />
<input type="hidden" name="depth" value="<?php echo $TPL_VAR["depth"]?>" />
<input type="hidden" name="bool_secret" value="<?php echo $TPL_VAR["bool_secret"]?>" />
<?php }?>
<table class="form_table table table-striped table-bordered table-hover">
<colgroup>
<col width="15%"></col>
<col width="35%"></col>
<col width="15%"></col>
<col width="35%"></col>
</colgroup>
<tbody>
<tr>
<th>게시판</th>
<td <?php if(!$TPL_VAR["info_bbs"]["bool_category"]){?> colspan="3"<?php }?>>
<input type="hidden" name="bbscode" value="<?php echo $_GET["bbscode"]?>" />
<?php echo $TPL_VAR["info_bbs"]["title"]?>
</td>
<?php if($TPL_VAR["info_bbs"]["bool_category"]){?>
<th>카테고리</th>
<td >
<input type="text" name="category" value="<?php echo $TPL_VAR["category"]?>" class="form-control" style="width:200px" data-provide="typeahead" data-showHintOnFocus="all" placeholder="<?php echo implode(',',$TPL_VAR["loop_category_key"])?>" autocomplete="off" data-source='["<?php echo implode('","',$TPL_VAR["loop_category_key"])?>"]' />
</td>
<?php }?>
</tr>
<?php if($_GET["pg_mode"]!='form_reply'){?>
<tr>
<th>유형</th>
<td>
<input type="radio" name="division" id="division_b" value="b" checked="checked"><label for="division_b">일반</label>
<input type="radio" name="division" id="division_a" value="a"><label for="division_a">공지 (고정글) </label>
</td>
<th>이름</th>
<td ><input type="text" name="author" value="<?php echo $TPL_VAR["author"]?>"class="form-control" style="width:200px"/></td>
</tr>
<?php }?>
<tr>
<th>비밀글</th>
<td>
<input type="radio" name="bool_secret" id="bool_secret_y" value="1"><label for="bool_secret_y">예</label>
<input type="radio" name="bool_secret" id="bool_secret_n" value="0"  checked="checked"><label for="bool_secret_n">아니요</label>
</td>
<th>비밀번호</th>
<td><input type="password" name="passwd" value="" style="width:100px" class="form-control" autocomplete="off"/></td>
</tr>
<?php if($TPL_VAR["info_bbs"]["bool_file"]){?>
<tr>
<th>첨부화일</th>
<td colspan="3">
<input type="file" name="file" id="file"  class="form-control" size="40">
<a href="<?php echo $TPL_VAR["file"]?>"><?php echo str_replace('<img ','<img style="max-width:300px;" ',$TPL_VAR["file_src_html"])?></a>
</td>
</tr>
<?php }?>
<tr>
<th>제목(한국어)</th>
<td colspan="3"><input type="text" name="subject_kr" value="<?php echo $TPL_VAR["subject_kr"]?>" class="form-control" autocomplete="off"/></td>
</tr>
<tr>
<th>제목(영어)</th>
<td colspan="3"><input type="text" name="subject_en" value="<?php echo $TPL_VAR["subject_en"]?>" class="form-control" autocomplete="off"/></td>
</tr>
<tr>
<th>제목(중국어)</th>
<td colspan="3"><input type="text" name="subject_cn" value="<?php echo $TPL_VAR["subject_cn"]?>" class="form-control" autocomplete="off"/></td>
</tr>
</tbody>
</table>
<div style="margin:10px 0px 10px 0px;">
내용(한국어)
<textarea id="contents_kr" name="contents_kr" style="width:100%;height:300px"><?php echo $TPL_VAR["contents_kr"]?></textarea><br /><br />
내용(영어)
<textarea id="contents_en" name="contents_en" style="width:100%;height:300px"><?php echo $TPL_VAR["contents_en"]?></textarea><br /><br />
내용(중국어)
<textarea id="contents_cn" name="contents_cn" style="width:100%;height:300px"><?php echo $TPL_VAR["contents_cn"]?></textarea><br />
</div>
<div class="col-md-12">
<input type="submit" value=" 확 인 " class="button_ok btn btn-primary" />&nbsp;
<a href="javascript:;" id="btn_cancel" class=" btn btn-danger">취 소</a>
</div>
</form>
</div>
</div>
</div>
</div>
</div>