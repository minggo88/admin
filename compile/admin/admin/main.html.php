<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/main.html 000005140 */ 
$TPL_main_notice_공지_1=empty($TPL_VAR["main_notice_공지"])||!is_array($TPL_VAR["main_notice_공지"])?0:count($TPL_VAR["main_notice_공지"]);
$TPL_main_notice_보도자료_1=empty($TPL_VAR["main_notice_보도자료"])||!is_array($TPL_VAR["main_notice_보도자료"])?0:count($TPL_VAR["main_notice_보도자료"]);
$TPL_main_notice_이벤트_1=empty($TPL_VAR["main_notice_이벤트"])||!is_array($TPL_VAR["main_notice_이벤트"])?0:count($TPL_VAR["main_notice_이벤트"]);?>
<div class="row wrapper border-bottom white-bg page-heading">
<div class="">
<h1>
<?php echo $TPL_VAR["config_basic"]["license_company"]?><br />
Management
</h1>
</div>
</div>
<div class="wrapper wrapper-content animated fadeIn">
<div class="panel-body">
<h4>서비스 설정</h4>
<!-- <p>2차 인증 로그인을 사용하시려면 <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&amp;hl=ko" target="_blank">Google OTP 앱</a>을 핸드폰에 설치하시고 아래의 QR코드를 스캔해주세요.</p> -->
<form method="post" name="jsform3" id="jsform3" action="?" class="form-horizontal">
<input type="hidden" name="pg_mode" id="pg_mode" value="save_otp">
<input type="hidden" name="tabs_idx" value="3">
<div class="form-group">
<label class="col-xs-4 col-sm-3 col-md-2 control-label">거래 On/Off</label>
<div class="col-xs-8 col-sm-9 col-md-10">
<input name="otpuse" type="checkbox" class="js-switch switch-trade" value="1" data-switchery="true" style="display: none;" <?php if($TPL_VAR["config_basic"]["bool_trade"]=='1'){?>checked<?php }?> >
<span class="status-trade m-l"><?php if($TPL_VAR["config_basic"]["bool_trade"]=='1'){?>거래 가능<?php }else{?>거래 불가<?php }?></span>
<script>
var changeCheckbox = document.querySelector('.js-switch.switch-trade') , changeField = document.querySelector('.status-trade'), actSwitchTradePost = true;
changeCheckbox.onchange = function() {
changeField.innerHTML = changeCheckbox.checked ? '거래 가능' : '거래 불가'; // 클릭시 이미 상태는 변경된 후라서 상태값을 먼저 바꿉니다.
if(!actSwitchTradePost) return ; //js에서 클릭처리시 post를 보내지 말아야 하는경우가 있어서 확인합니다.
actSwitchTradePost = false;
if(confirm('거래 상태를 변경하시겠습니까?')) {
$.post('.', {'pg_mode':'switch_bool_trade', 'bool_trade':changeCheckbox.checked ? '1' : '0'}, function(r){
if(!r) {
$('.js-switch.switch-trade').click();
}
actSwitchTradePost = true;
})
} else {
$('.js-switch.switch-trade').click();
setTimeout(function(){actSwitchTradePost = true;}, 500);
}
};
</script>
</div>
</div>
</form>
<h4>매인 페이지</h4>
<form method="post" name="jsform_main" id="jsform_main" action="?" class="form-horizontal">
<div class="form-group">
<label class="col-xs-4 col-sm-3 col-md-2 control-label">주요공지배너</label>
<div class="col-xs-8 col-sm-9 col-md-10">
<div class="col-xs-12 col-sm-4">
<h5><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode=NOTICE&category=공지">공지사항</a></h5>
<ul>
<?php if($TPL_main_notice_공지_1){foreach($TPL_VAR["main_notice_공지"] as $TPL_V1){?>
<li><a href="/bbs/admin/bbsAdmin.php?pg_mode=view&bbscode=NOTICE&idx=<?php echo $TPL_V1["idx"]?>"><?php echo $TPL_V1["subject_kr"]?></a></li>
<?php }}?>
</ul>
</div>
<div class="col-xs-12 col-sm-4">
<h5><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode=NOTICE&category=보도자료">보도자료</a></h5>
<ul>
<?php if($TPL_main_notice_보도자료_1){foreach($TPL_VAR["main_notice_보도자료"] as $TPL_V1){?>
<li><a href="/bbs/admin/bbsAdmin.php?pg_mode=view&bbscode=NOTICE&idx=<?php echo $TPL_V1["idx"]?>"><?php echo $TPL_V1["subject_kr"]?></a></li>
<?php }}?>
</ul>
</div>
<div class="col-xs-12 col-sm-4">
<h5><a href="/bbs/admin/bbsAdmin.php?pg_mode=list&bbscode=NOTICE&category=이벤트">이벤트</a></h5>
<ul>
<?php if($TPL_main_notice_이벤트_1){foreach($TPL_VAR["main_notice_이벤트"] as $TPL_V1){?>
<li><a href="/bbs/admin/bbsAdmin.php?pg_mode=view&bbscode=NOTICE&idx=<?php echo $TPL_V1["idx"]?>"><?php echo $TPL_V1["subject_kr"]?></a></li>
<?php }}?>
</ul>
</div>
</div>
</div>
<div class="form-group">
<label class="col-xs-4 col-sm-3 col-md-2 control-label">앱소개</label>
<div class="col-xs-8 col-sm-9 col-md-10"></div>
</div>
<div class="form-group">
<label class="col-xs-4 col-sm-3 col-md-2 control-label">인증 시스템 소개</label>
<div class="col-xs-8 col-sm-9 col-md-10"></div>
</div>
<div class="form-group">
<label class="col-xs-4 col-sm-3 col-md-2 control-label">보안 및 안심거래 게시</label>
<div class="col-xs-8 col-sm-9 col-md-10"></div>
</div>
<div class="form-group">
<label class="col-xs-4 col-sm-3 col-md-2 control-label">파트너 게시</label>
<div class="col-xs-8 col-sm-9 col-md-10"></div>
</div>
<div class="hr-line-dashed"></div>
<!-- <div class="form-group">
<div class="col-sm-4 col-sm-offset-2">
<button name="btn-save-otp" class="btn btn-primary" type="submit">저장</button>
</div>
</div> -->
</form>
</div>
</div>