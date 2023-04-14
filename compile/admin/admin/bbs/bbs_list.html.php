<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/bbs/bbs_list.html 000007004 */ 
$TPL_loop_category_key_1=empty($TPL_VAR["loop_category_key"])||!is_array($TPL_VAR["loop_category_key"])?0:count($TPL_VAR["loop_category_key"]);
$TPL_loop_bbs_notice_1=empty($TPL_VAR["loop_bbs_notice"])||!is_array($TPL_VAR["loop_bbs_notice"])?0:count($TPL_VAR["loop_bbs_notice"]);
$TPL_loop_bbs_main_1=empty($TPL_VAR["loop_bbs_main"])||!is_array($TPL_VAR["loop_bbs_main"])?0:count($TPL_VAR["loop_bbs_main"]);
$TPL_loop_bbs_1=empty($TPL_VAR["loop_bbs"])||!is_array($TPL_VAR["loop_bbs"])?0:count($TPL_VAR["loop_bbs"]);?>
<style type="text/css">
#bbs_top_left {width:380px; margin:10px 0 10px 0; text-align:left;}
#bbs_top_right {width:380px; margin:10px 0 10px 0; text-align:right}
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
<div style="float:right">
<select name="language">
<option value="kr" <?php if($_GET["language"]=='kr'){?>selected=""<?php }?> >한국어</option>
<option value="en" <?php if($_GET["language"]=='en'){?>selected=""<?php }?>>영어</option>
<option value="cn" <?php if($_GET["language"]=='cn'){?>selected=""<?php }?>>중국어</option>
</select>
<select name="category">
<option value="" <?php if(!$_GET["category"]){?>selected=""<?php }?> >전체</option>
<?php if($TPL_loop_category_key_1){foreach($TPL_VAR["loop_category_key"] as $TPL_V1){?>
<option value="<?php echo $TPL_V1?>" <?php if($_GET["category"]==$TPL_V1){?>selected=""<?php }?> ><?php echo $TPL_V1?></option>
<?php }}?>
</select>
</div>
</div>
<div class="ibox-content">
<div class="area_both">
<div id="bbs_top_left" class="area_child_left">
등록글수 : <span class="emphasis"><?php echo $TPL_VAR["total"]?></span>
</div>
</div>
<form id="list_form">
<table class="list_table table table-striped table-bordered table-hover ">
<colgroup>
<col width="20">
<col width="60">
<col width="120">
<col>
<col width="100">
<col width="60">
<col width="60">
<col width="110">
</colgroup>
<thead>
<tr>
<th><input type="checkbox" id="all_check" /></th>
<th class="text-center">번호</th>
<?php if($TPL_VAR["info_bbs"]["bool_category"]){?><th class="text-center">카테고리</th><?php }?>
<th>제목</th>
<th class="text-center">이름</th>
<th class="text-center">댓글</th>
<th class="text-center">조회</th>
<th class="text-center">날짜</th>
</tr>
</thead>
<tbody>
<?php if($TPL_loop_bbs_notice_1){foreach($TPL_VAR["loop_bbs_notice"] as $TPL_V1){?>
<tr style="background:#e3e3e3;">
<td><input type="checkbox" name="idxs[]" value="<?php echo $TPL_V1["idx"]?>" id="dp_<?php echo $TPL_V1["depth"]?>" class="th_<?php echo $TPL_V1["thread"]?>" /></td>
<td class="text-center">공지</td>
<?php if($TPL_VAR["info_bbs"]["bool_category"]){?><td class="text-center"><?php echo $TPL_V1["category"]?></td><?php }?>
<td style="padding-left:<?php echo $TPL_V1["depth"]* 10?>px;text-align:left"><?php if($TPL_V1["depth"]> 1){?>└[re] <?php }?><a href="?pg_mode=view&idx=<?php echo $TPL_V1["idx"]?>&list_cnt=<?php echo $TPL_V1["list_cnt"]?><?php echo $TPL_VAR["srch_url"]?>"><?php echo $TPL_V1["subject"]?></a>
<?php if($TPL_V1["bool_icon_new"]){?><img src="/template/admin/admin/bbs/images/icon_new.gif" alt="icon_new" /><?php }?>
<?php if($TPL_V1["bool_icon_hot"]){?><img src="/template/admin/admin/bbs/images/icon_hot.gif" alt="icon_hot" /><?php }?>
<?php if($TPL_V1["bool_icon_secret"]){?><img src="/template/admin/admin/bbs/images/icon_secret.gif" alt="icon_secret" /><?php }?>
<?php if($TPL_V1["bool_icon_file"]){?><img src="/template/admin/admin/bbs/images/icon_file.gif" alt="icon_file" /><?php }?>
</td>
<td class="text-center"><?php echo $TPL_V1["author"]?></td>
<td class="text-center"><?php echo $TPL_V1["cnt_comment"]?></td>
<td class="text-center"><?php echo $TPL_V1["hit"]?></td>
<td class="text-center"><?php echo date('Y-m-d',$TPL_V1["regdate"])?></td>
</tr>
<?php }}?>
<?php if($TPL_loop_bbs_main_1){foreach($TPL_VAR["loop_bbs_main"] as $TPL_V1){?>
<tr>
<td><input type="checkbox" name="idxs[]" value="<?php echo $TPL_V1["idx"]?>" id="dp_<?php echo $TPL_V1["depth"]?>_<?php echo $TPL_V1["idx"]?>" class="th_<?php echo $TPL_V1["bbscode"]?>_<?php echo $TPL_V1["thread"]?>" /></td>
<td class="text-center"><?php echo $TPL_V1["no"]?></td>
<?php if($TPL_VAR["info_bbs"]["bool_category"]){?><td class="text-center"><?php echo $TPL_V1["category"]?></td><?php }?>
<td style="padding-left:<?php echo $TPL_V1["depth"]* 10?>px;text-align:left"><?php if($TPL_V1["depth"]> 1){?>└[re] <?php }?><a href="?pg_mode=view&idx=<?php echo $TPL_V1["idx"]?>&list_cnt=<?php echo $TPL_V1["list_cnt"]?><?php echo $TPL_VAR["srch_url"]?>"><?php echo $TPL_V1["subject_kr"]?></a>
<?php if($TPL_V1["bool_icon_new"]){?><img src="/template/admin/admin/bbs/images/icon_new.gif" alt="icon_new" /><?php }?>
<?php if($TPL_V1["bool_icon_hot"]){?><img src="/template/admin/admin/bbs/images/icon_hot.gif" alt="icon_hot" /><?php }?>
<?php if($TPL_V1["bool_icon_secret"]){?><img src="/template/admin/admin/bbs/images/icon_secret.gif" alt="icon_secret" /><?php }?>
<?php if($TPL_V1["bool_icon_file"]){?><img src="/template/admin/admin/bbs/images/icon_file.gif" alt="icon_file" /><?php }?>
</td>
<td class="text-center"><?php echo $TPL_V1["author"]?></td>
<td class="text-center"><?php echo $TPL_V1["cnt_comment"]?></td>
<td class="text-center"><?php echo $TPL_V1["hit"]?></td>
<td class="text-center"><?php echo date('Y-m-d',$TPL_V1["regdate"])?></td>
</tr>
<?php }}else{?>
<tr>
<td colspan="7" class="ctext">등록된 글이 없습니다.</td>
</tr>
<?php }?>
</tbody>
</table>
</form>
<div class="col-md-12">
<div class="col-md-6 text-left no-padding">
<form id="controlform" class="">
<input type="radio" name="pg_mode" value="del_multi" checked="checked" />선택삭제
<input type="radio" name="pg_mode" value="move_multi"/>선택이동
<select id="movebbscode" name="bbscode" class="scroll" >
<option>::: Target :::</option>
<?php if($TPL_loop_bbs_1){foreach($TPL_VAR["loop_bbs"] as $TPL_V1){?>
<option value="<?php echo $TPL_V1["bbscode"]?>"><?php echo $TPL_V1["title"]?></option>
<?php }}?>
</select>
<span class="btn"><input type="button" value="확인"  onclick="multiControl()" class="btn btn-primary"/></span>
</form>
</div>
<div class="col-md-6 text-right no-padding"><a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=form_new<?php echo $TPL_VAR["srch_url"]?>"  class="btn btn-primary" >쓰기</a></div>
</div>
<div id="navipage"><?php echo $TPL_VAR["navi_page"]?></div>
</div>
</div>
</div>
</div>
</div>