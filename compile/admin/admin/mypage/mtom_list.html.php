<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/mypage/mtom_list.html 000003466 */ 
$TPL_loop_mtom_1=empty($TPL_VAR["loop_mtom"])||!is_array($TPL_VAR["loop_mtom"])?0:count($TPL_VAR["loop_mtom"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>1:1 문의 관리</h2>
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
<h5> 1:1 문의 목록 </h5>
</div>
<div class="ibox-content">
<div class="table-responsive">
<div class="text-left" style="margin-bottom: 20px;">
<a href="javascript:;" onclick="checkDel()" class="btn btn-danger btn-xs">선택삭제</a>
</div>
<div class=" text-right" style="margin-bottom: 20px;"></div>
<form id="list_form">
<table class="table table-striped table-bordered table-hover dataTables-mtom">
<colgroup>
<col width="30"></col>
<col width="60"></col>
<col></col>
<col width="120"></col>
<col width="150"></col>
<col width="120"></col>
</colgroup>
<thead>
<tr>
<th><input type="checkbox" id="all_check" /></th>
<th>번호</th>
<th>제목</th>
<th>이름</th>
<th>날짜</th>
<th>답변여부</th>
</tr>
</thead>
<tbody>
<?php if($TPL_loop_mtom_1){foreach($TPL_VAR["loop_mtom"] as $TPL_V1){?>
<tr>
<td><input type="checkbox" name="idxs[]" value="<?php echo $TPL_V1["idx"]?>" /></td>
<td class="text-center"><?php echo $TPL_V1["no"]?></td>
<td style="text-align:left;padding-left:10px"><a href="<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=view&idx=<?php echo $TPL_V1["idx"]?><?php echo $TPL_VAR["srch_url"]?>"><?php echo $TPL_V1["subject"]?></a></td>
<td><?php echo $TPL_V1["author"]?></td>
<td class="text-center"><?php echo date('Y/m/d H:i:s',$TPL_V1["regdate"])?></td>
<td class="text-center"><?php if($TPL_V1["bool_rplcontents"]){?><span style="color: #555">[답변대기]</span><?php }else{?><span style="color:#FF6600">[답변완료]</span><?php }?></td>
</tr>
<?php }}else{?>
<tr>
<td colspan="6" class="ctext">등록된 글이 없습니다.</td>
</tr>
<?php }?>
</tbody>
</table>
</form>
<div class="text-left" style="margin-bottom: 20px;">
<a href="javascript:;" onclick="checkDel()" class="btn btn-danger btn-xs">선택삭제</a>
</div>
<div class=" text-right" style="margin-bottom: 20px;"></div>
<form name="searchform" id="searchform" method="get" action="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="form-horizontal">
<div class="search_area pcenter">
<input type="checkbox" name="author" id="author" value="1" >&nbsp;<label for="author">이름</label>
<input type="checkbox" name="subject" id="subject" value="1" checked="checked">&nbsp;<label for="subject">제목</label>
<input type="checkbox" name="contents" id="contents" value="1" checked="checked">&nbsp;<label for="contents">내용</label>
<input type="text" name="s_val" value="<?php echo $_GET["s_val"]?>" class="frm_input" style="width:100px">&nbsp;
<input type="submit" value="검 색" class="btn btn-primary btn-xs" />&nbsp;
<a href="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="btn btn-danger btn-xs"> 취소</a>
</div>
</form>
<div id="navipage"><?php echo $TPL_VAR["navi_page"]?></div>
</div>
</div>
</div>
</div>
</div>
</div>