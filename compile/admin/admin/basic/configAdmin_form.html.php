<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/basic/configAdmin_form.html 000011860 */ 
$TPL_loop_1=empty($TPL_VAR["loop"])||!is_array($TPL_VAR["loop"])?0:count($TPL_VAR["loop"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="col-lg-10">
<h2>관리자설정</h2>
<ol class="breadcrumb">
<li>
<a href="index.html">Home</a>
</li>
<li>
<a href="/admin/configAdmin.php">기본관리</a>
</li>
<li class="active">
<strong>관리자설정</strong>
</li>
</ol>
</div>
<div class="col-lg-2">
</div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
<div class="tabs-container">
<ul class="nav nav-tabs">
<li class="active"><a data-toggle="tab" href="#tab-1">비밀번호설정</a></li>
<li class=""><a data-toggle="tab" href="#tab-2">관리자설정</a></li>
<?php if($_SESSION["ADMIN_ID"]=='admin'){?>
<li class=""><a data-toggle="tab" href="#tab-3">OTP설정</a></li>
<li class=""><a data-toggle="tab" href="#tab-4">로그인 기록</a></li>
<?php }?>
</ul>
<div class="tab-content">
<div id="tab-1" class="tab-pane active">
<div class="panel-body">
<form method="post" name="jsform1" id="jsform1" action="<?php echo $_SERVER["SCRIPT_NAME"]?>" class="form-horizontal">
<input type="hidden" name="pg_mode" value="edit_passwd" />
<input type="hidden" name="tabs_idx" value="0" />
<div class="form-group"><label class="col-sm-2 control-label">최고 관리자 아이디</label>
<div class="col-sm-10"><p class="form-control-static">admin</p></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">관리자 휴대폰</label>
<div class="col-sm-10"><input type="text" name="admin_mobile" value="<?php echo $TPL_VAR["admin_mobile"]?>" class="form-control"> <span class="help-block m-b-none">SMS, MMS는 관리자 휴대폰으로 전송 됩니다.</span>
</div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group"><label class="col-sm-2 control-label">비밀번호 변경</label>
<div class="col-sm-10">
<div><label> <input type="checkbox" name="bool_passwd" id="bool_passwd" value="1" class="i-checks"> 관리자 비밀번호를 변경 합니다.</label></div>
</div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group"><label class="col-sm-2 control-label">기존 비밀번호</label>
<div class="col-sm-10"><input type="password" name="old_passwd" class="form-control" /></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">신규 비밀번호</label>
<div class="col-sm-10"><input type="password" name="new_passwd" class="form-control" /></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">신규 비밀번호 학인</label>
<div class="col-sm-10"><input type="password" name="renew_passwd" class="form-control" /></div>
</div>
<div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button class="btn btn-primary" type="submit">Save changes</button>
</div>
</div>
</form>
</div>
</div>
<div id="tab-2" class="tab-pane">
<div class="panel-body">
<h4>관리자 권한 설정</h4>
<form method="post" name="jsform2" id="jsform2" action="?" class="form-horizontal">
<input type="hidden" name="pg_mode" id="pg_mode" value="write" />
<input type="hidden" name="tabs_idx" value="1" />
<input type="hidden" name="idx" />
<div class="form-group"><label class="col-sm-2 control-label">관리자명</label>
<div class="col-sm-10"><input type="text" name="admin_name" class="form-control"></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">아이디</label>
<div class="col-sm-10"><input type="text" name="adminid" class="form-control"></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">비밀번호</label>
<div class="col-sm-10"><input type="password" name="adminpw" class="form-control" /></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">휴대전화</label>
<div class="col-sm-10"><input type="text" name="admin_mobile" class="form-control"></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">권한설정</label>
<div class="col-sm-10">
<div class="col-sm-2"><label> <input type="checkbox" name="right_basic" id="right_basic" value="1" > <i></i> 기본관리 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_member" id="right_member" value="1" > <i></i> 일반회원 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_point" id="right_point" value="1" > <i></i> 인증리스트 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_goods" id="right_goods" value="1" > <i></i> 상품상장관리 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_order" id="right_order" value="1" > <i></i> 거래관리 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_wallet" id="right_wallet" value="1" > <i></i> 지갑관리 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_community" id="right_community" value="1" > <i></i> 커뮤니티관리 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_cs" id="right_cs" value="1" > <i></i> CS관리 </label></div>
<div class="col-sm-2"><label> <input type="checkbox" name="right_statistics" id="right_statistics" value="1" > <i></i> 통계 </label></div>
</div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">메모</label>
<div class="col-sm-10"><input type="text" name="remark_admin" class="form-control"></div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button class="btn btn-primary" type="submit">Save changes</button>
</div>
</div>
</form>
<h4>관리자 목록</h4>
<table class="list_table table table-bordered table-hover ">
<colgroup>
<col width="70"></col>
<col width="*"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
<col width="90"></col>
</colgroup>
<thead>
<tr>
<th colspan="2" rowspan="2" class="text-center">아이디</th>
<th colspan="9" class="text-center">이용권한</th>
<th rowspan="2" class="text-center">수정</th>
</tr>
<tr>
<th class="text-center">기본관리</th>
<th class="text-center">일반회원</th>
<th class="text-center">인증리스트</th>
<th class="text-center">상품상장관리</th>
<th class="text-center">거래관리</th>
<th class="text-center">지갑관리</th>
<th class="text-center">커뮤니티관리</th>
<th class="text-center">CS관리</th>
<th class="text-center">통계</th>
</tr>
</thead>
<tbody>
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
<tr>
<td rowspan="2" class="text-center">
<?php echo $TPL_V1["no"]?>
</td>
<td rowspan="2">
<?php echo $TPL_V1["adminid"]?><br />
<?php echo $TPL_V1["admin_name"]?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_basic"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_member"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_point"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_goods"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_order"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_wallet"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_community"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_cs"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td class="text-center">
<?php if($TPL_V1["right_statistics"]){?><strong><span class="text-danger">Y</span></strong>
<?php }else{?><span class="text-muted">N</span>
<?php }?>
</td>
<td rowspan="2" class="text-center">
<button class="btn btn-xs btn-primary" type="button" onClick="javascript:setEditForm('<?php echo $TPL_V1["adminid"]?>');"><strong>수정</strong></button>
<button class="btn btn-xs btn-danger" type="button" onClick="javascript:adminDel('<?php echo $TPL_V1["adminid"]?>');"><strong>삭제</strong></button>
</td>
</tr>
<tr>
<td colspan="9" class="pleft">
<?php echo $TPL_V1["remark_admin"]?>
</td>
</tr>
<?php }}else{?>
<tr>
<td colspan="12" class="pcenter">등록된 관리자가 없습니다.</td>
</tr>
<?php }?>
</tbody>
</table>
</div>
</div>
<?php if($_SESSION["ADMIN_ID"]=='admin'){?>
<div id="tab-3" class="tab-pane">
<div class="panel-body">
<h4>OTP 설정</h4>
<p>2차 인증 로그인을 사용하시려면 <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=ko" target="_blank">Google OTP 앱</a>을 핸드폰에 설치하시고 아래의 QR코드를 스캔해주세요.</p>
<form method="post" name="jsform3" id="jsform3" action="?" class="form-horizontal">
<input type="hidden" name="pg_mode" id="pg_mode" value="save_otp" />
<input type="hidden" name="tabs_idx" value="3" />
<div class="form-group"><label class="col-sm-2 control-label">QR코드</label>
<div class="col-sm-10">
<img src="<?php echo $TPL_VAR["qrCodeUrl"]?>">
</div>
</div>
<div class="form-group"><label class="col-sm-2 control-label">OPT사용</label>
<div class="col-sm-10">
<input name="otpuse" type="checkbox" class="js-switch" <?php if($TPL_VAR["otpuse"]){?>checked
<?php }?>value="1"/>
<script>
</script>
</div>
</div>
<div class="hr-line-dashed"></div>
<div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button name="btn-save-otp" class="btn btn-primary" type="submit">Save changes</button>
</div>
</div>
</form>
</div>
</div>
<div id="tab-4" class="tab-pane" name="box_log">
<div class="panel-body">
<h4>관리자 로그인 기록</h4>
<form name="form-search" class="form-horizontal">
<div class="row form-group">
<label class="col-sm-2 control-label">날짜</label>
<div class="col-sm-4"><input type="date" name="reg_date" class="form-control search_item"></div>
<label class="col-sm-2 control-label">아이디</label>
<div class="col-sm-4"><input type="text" name="adminid" class="form-control search_item"></div>
</div>
<div class="row form-group">
<div class="col-sm-12 text-center ">
<button class="btn btn-sm btn-default m-t-n-xs" type="reset" name="reset"><strong>초기화</strong></button>
<button class="btn btn-sm btn-primary m-t-n-xs" type="submit" name="search"><strong>검색</strong></button>
</div>
</div>
</form>
<div class="hr-line-dashed clear"></div>
<table class="list_table table table-bordered table-hover ">
<colgroup>
<col width="200"></col>
<col width="*"></col>
<col width="200"></col>
</colgroup>
<thead>
<tr>
<th class="text-center">날짜</th>
<th class="text-center">아이디</th>
<th class="text-center">IP</th>
</tr>
</thead>
<tbody id="box_log_list">
<tr>
<td colspan="3" class="text-center">조회중입니다.</td>
</tr>
</tbody>
</table>
<style>
.box_paging {
width: 100%;
text-align: center
}
.box_paging .btn {
float: none
}
</style>
<div name="box_log_paging" class="btn-group box_paging">
<button class="btn btn-white">1</button>
</div>
</div>
</div>
<?php }?>
</div>
</div>
</div>