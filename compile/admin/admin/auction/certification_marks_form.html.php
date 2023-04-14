<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/auction/certification_marks_form.html 000002117 */ ?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>인증(마크) 등록</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a>경매 관리</a>
</li>
<li class="active">
<strong>인증(마크) 등록</strong>
</li>
</ol>
</div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12 m-b-md">
<div class="ibox float-e-margins">
<div class="ibox-title">
<h5>인증(마크) 추가 </h5>
<div class="text-right"><a href="?" class="btn btn-info">목록</a></div>
</div>
<div class="ibox-content">
<form method="post" class="form-horizontal" name="editform" id="editform" action="">
<input type="hidden"  name="pg_mode" value="<?php if($TPL_VAR["idx"]){?>edit<?php }else{?>write<?php }?>" />
<div class="form-group">
<label class="col-sm-2 control-label">인증(마크) 번호</label>
<div class="col-sm-4">
<input type="text" name="idx" value="<?php echo $TPL_VAR["idx"]?>" class="form-control" readonly>
</div>
<label class="col-sm-2 control-label">이름</label>
<div class="col-sm-4">
<input type="text" name="title" value="<?php echo $TPL_VAR["title"]?>" class="form-control">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">인증(마크) 이미지</label>
<div class="col-sm-4">
<input type="file" name="icon_file" value="" class="form-control">
<ul>
<li>지원되는 파일 형식 : JPG, PNG, GIF</li>
<li>최대 크기 : 50 MB.</li>
</ul>
</div>
<div class="col-sm-6" style="overflow-x:auto;overflow-y:none">
<div style="white-space: nowrap" id="box_image_url">
<?php if($TPL_VAR["image_url"]){?>
<img src="<?php echo $TPL_VAR["image_url"]?>" height="150px" id="icon_image">
<?php }?>
</div>
</div>
</div>
<hr>
<div class="form-group text-center">
<input type="submit" class="btn btn-default" value="저장"/>
<input type="reset" class="btn btn-warning" value="초기화"/>
</div>
</form>
</div>
</div>
</div>
</div>
</div>