<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/layout_admin.html 000052261 */ 
$TPL_loop_script_1=empty($TPL_VAR["loop_script"])||!is_array($TPL_VAR["loop_script"])?0:count($TPL_VAR["loop_script"]);
$TPL_loop_event_1=empty($TPL_VAR["loop_event"])||!is_array($TPL_VAR["loop_event"])?0:count($TPL_VAR["loop_event"]);?>
<?php $this->print_("js_tpl_left",$TPL_SCP,1);?>
<div id="page-wrapper" class="white-bg">
<?php $this->print_("js_tpl_header",$TPL_SCP,1);?>
<?php $this->print_("js_tpl_main",$TPL_SCP,1);?>
<?php $this->print_("js_tpl_footer",$TPL_SCP,1);?>
</div>
<!-- Mainly scripts -->
<script src="/template/admin/admin/js/jquery-3.1.1.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/bootstrap.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/metisMenu/jquery.metisMenu.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/slimscroll/jquery.slimscroll.min.js?t=1666836874"></script>
<!-- Peity -->
<script src="/template/admin/admin/js/plugins/peity/jquery.peity.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/demo/peity-demo.js?t=1666836874"></script>
<!-- Custom and plugin javascript -->
<script src="/template/admin/admin/js/smcc.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/pace/pace.min.js?t=1666836874"></script>
<!-- jQuery UI -->
<script src="/template/admin/admin/js/plugins/jquery-ui/jquery-ui.min.js?t=1666836874"></script>
<!-- datatables -->
<!-- <script src="/template/admin/admin/js/plugins/dataTables/datatables.min.js?t=1666836874"></script> -->
<!-- Chosen -->
<script src="/template/admin/admin/js/plugins/chosen/chosen.jquery.js?t=1666836874"></script>
<!-- Input Mask-->
<script src="/template/admin/admin/js/plugins/jasny/jasny-bootstrap.min.js?t=1666836874"></script>
<!-- Data picker -->
<script src="/template/admin/admin/js/plugins/datapicker/bootstrap-datepicker.js?t=1666836874"></script>
<!-- Date range use moment.js same as full calendar plugin -->
<script src="/template/admin/admin/js/plugins/fullcalendar/moment.min.js?t=1666836874"></script>
<!-- Date range picker -->
<script src="/template/admin/admin/js/plugins/daterangepicker/daterangepicker.js?t=1666836874"></script>
<!-- Select2 -->
<script src="/template/admin/admin/js/plugins/select2/select2.full.min.js?t=1666836874"></script>
<!-- iCheck -->
<script src="/template/admin/admin/js/plugins/iCheck/icheck.min.js?t=1666836874"></script>
<script>
jQuery(function () {
$('.i-checks').iCheck({
checkboxClass: 'icheckbox_square-green',
radioClass: 'iradio_square-green',
});
});
</script>
<!-- dataTables -->
<!-- <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></s-cript>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<!-- <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script> -> -->
<?php if($TPL_loop_script_1){foreach($TPL_VAR["loop_script"] as $TPL_V1){?>
<script src="<?php echo $TPL_V1?>" type="text/javascript" charset="utf-8"></script>
<?php }}?>
<?php if($_SERVER["SCRIPT_NAME"]=='/admin/index.php'||$_SERVER["SCRIPT_NAME"]=='/'||$_SERVER["SCRIPT_NAME"]=='/index.php'){?>
<!-- Flot -->
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.tooltip.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.spline.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.resize.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.pie.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.symbol.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/jquery.flot.time.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/flot/curvedLines.js?t=1666836874"></script>
<!-- Jvectormap -->
<script src="/template/admin/admin/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js?t=1666836874"></script>
<!-- Sparkline -->
<script src="/template/admin/admin/js/plugins/sparkline/jquery.sparkline.min.js?t=1666836874"></script>
<!-- Sparkline demo data  -->
<script src="/template/admin/admin/js/demo/sparkline-demo.js?t=1666836874"></script>
<!-- ChartJS-->
<script src="/template/admin/admin/js/plugins/chartJs/Chart.min.js?t=1666836874"></script>
<!-- d3 and c3 charts -->
<script src="/template/admin/admin/js/plugins/d3/d3.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/c3/c3.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/dashboard-chart.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/contents/admin/contentsAdmin.php'){?>
<!-- jqGrid -->
<script src="/template/admin/admin/js/plugins/jqGrid/i18n/grid.locale-en.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/jqGrid/jquery.jqGrid.min.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/analysesAdmin.php'){?>
<script src="/template/admin/admin/analyses/include/analyses.min.js?t="></script>
<!-- Clock picker -->
<script src="/template/admin/admin/js/plugins/clockpicker/clockpicker.js?t=1666836874"></script>
<!-- Date range picker -->
<!-- Custom and plugin javascript -->
<!-- Tinycon -->
<script src="/template/admin/admin/js/plugins/tinycon/tinycon.min.js?t=1666836874"></script>
<script>
jQuery(function(){
$('#data_1 .input-group.date').datepicker({
todayBtn: "linked",
keyboardNavigation: false,
forceParse: false,
calendarWeeks: true,
autoclose: true,
format: "yyyy-mm-dd"
});
$('#toggleSpinners').on('click', function(){
$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
});
})
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/extToss.php'){?>
<script src="/template/admin/admin/exttoss/include/bundle.min.js?t="></script>
<!-- Clock picker -->
<script src="/template/admin/admin/js/plugins/clockpicker/clockpicker.js?t=1666836874"></script>
<!-- Date range picker -->
<!-- Custom and plugin javascript -->
<!-- Tinycon -->
<script src="/template/admin/admin/js/plugins/tinycon/tinycon.min.js?t=1666836874"></script>
<script>
jQuery(function(){
$('#data_1 .input-group.date').datepicker({
todayBtn: "linked",
keyboardNavigation: false,
forceParse: false,
calendarWeeks: true,
autoclose: true,
format: "yyyy-mm-dd"
});
$('#toggleSpinners').on('click', function(){
$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
});
})
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/studyReact.php'){?>
<!-- React -->
<script src="/template/admin/admin/react/js/bundle.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/schedule/admin/schedule.php'||$_SERVER["SCRIPT_NAME"]=='/schedule/admin/commentAdmin.php'){?>
<script>
jQuery(function($){
$('#jsform').submit(function() {
if(!confirm('프로젝트를 저장 하시겠습니까?')) {
return false;
}
$(this).ajaxSubmit({
success: function (data, statusText) {
if(data['bool']) {
<?php if($_GET["pg_mode"]=='form_new'){?>
alert('프로젝트가 생성 되었습니다.!');
location.replace('<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list');
<?php }else{?>
if(confirm('계속 수정하시겠습니까?')) {
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
location.href = '?pg_mode=list';
}
<?php }?>
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
//location.replace('/member/memberAuth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},
'dataType':'json',
'resetForm': false
});
return false;
});
});
jQuery(function(){
$('#data_1 .input-group.date').datepicker({
todayBtn: "linked",
keyboardNavigation: false,
forceParse: false,
calendarWeeks: true,
autoclose: true,
format: "yyyy-mm-dd"
});
$('#data_2 .input-group.date').datepicker({
todayBtn: "linked",
keyboardNavigation: false,
forceParse: false,
calendarWeeks: true,
autoclose: true,
format: "yyyy-mm-dd"
});
$('#data_p1 .input-group.date').datepicker({
todayBtn: "linked",
keyboardNavigation: false,
forceParse: false,
calendarWeeks: true,
autoclose: true,
format: "yyyy-mm-dd"
});
$('#data_p2 .input-group.date').datepicker({
todayBtn: "linked",
keyboardNavigation: false,
forceParse: false,
calendarWeeks: true,
autoclose: true,
format: "yyyy-mm-dd"
});
$('input[name=bool_ing]').val([<?php if($_GET["pg_mode"]=='form_new'){?>'1'<?php }else{?>'<?php echo $TPL_VAR["bool_ing"]?>'<?php }?>]);
$('input[name=bool_repeat]').val([<?php if($_GET["pg_mode"]=='form_new'){?>'0'<?php }else{?>'<?php echo $TPL_VAR["bool_repeat"]?>'<?php }?>]);
//$('select[name=]').val('<!--{}-->');//select
$('select[name=adminid]').val("<?php echo $_GET["adminid"]?>");
$('select[name=participant_id]').val("<?php echo $_GET["participant_id"]?>");
});
jQuery(function(){
$('#loading-example-btn').click(function () {
btn = $(this);
simpleLoad(btn, true)
// Ajax example
// $.ajax().always(function () {
//     simpleLoad($(this), false)
// });
simpleLoad(btn, false)
});
});
// Comment
jQuery(function(){
$('#commentForm').submit(function() {
var chk_option = [
{ 'target':'contents', 'name':'댓글', 'type':'blank', 'msg':'댓글 내용을 입력하세요.!' }
];
if(!jsForm(this,chk_option)) {
return false;
}
if(!confirm('등록 하시겠습니까?')) {
return false;
}
$(this).ajaxSubmit({
success: function (data, statusText) {
if(data['bool']) {
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else {
alert('재시도 해주세요.!');
}
}
},
dataType:'json',
resetForm: true
});
return false;
});
});
jQuery(function(){
$('.btn_comment_del').click(function() {
if(!confirm('삭제하시겠습니까?')) {
return false;
}
var str = $(this).attr('id');
var arr = str.split('_');
$.get('/schedule/admin/commentAdmin.php?pg_mode=del&idx='+arr[2],function(data) {
if(data['bool']) {
alert('삭제되었습니다.');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
});
});
jQuery(function(){
$('.btn_comment_reply').click(function() {
$('.comment_reply_form').empty();
var str = $(this).attr('id');
var arr = str.split('_');
$('#comment_reply_form_'+arr[2]).load('/schedule/admin/commentAdmin.php?pg_mode=form_reply&link_idx=<?php echo $_GET["idx"]?>&idx='+arr[2]+'&ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
});
});
jQuery(function(){
$('.btn_comment_edit').click(function() {
var str = $(this).attr('id');
var arr = str.split('_');
$('#comment_list_'+arr[2]+' #comment_contents'+arr[2]).load('/schedule/admin/commentAdmin.php?pg_mode=form_edit&idx='+arr[2]+'&ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
});
});
function simpleLoad(btn, state) {
if (state) {
btn.children().addClass('fa-spin');
btn.contents().last().replaceWith(" Loading");
} else {
setTimeout(function () {
btn.children().removeClass('fa-spin');
btn.contents().last().replaceWith(" Refresh");
}, 2000);
}
}
</script>
<?php if($_GET["pg_mode"]=="view"){?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="/template/admin/admin/schedule/js/gantt.js?t="></script>
<?php }?>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/schedule/admin/calendar.php'){?>
<!-- Custom and plugin javascript -->
<script>
jQuery(function(){
$('#frmcalendar').submit(function() {
var chk_option = [
{ 'target':'start_year', 'name':'시작년도', 'type':'select', 'msg':'시작년도를 선택하여 주세요.!' },
{ 'target':'start_month', 'name':'시작월', 'type':'select', 'msg':'시작월을 선택하여 주세요.!' },
{ 'target':'start_day', 'name':'시작일', 'type':'select', 'msg':'시작일을 선택하여 주세요.!' },
{ 'target':'title', 'name':'일정', 'type':'blank', 'msg':'일정을 입력하여 주세요.!' }
];
if(!jsForm(this,chk_option)) {
return false;
}
if(!confirm('저장하시겠습니까?')) {
return false;
}
$(this).ajaxSubmit({
success: function (data, statusText) {
if(data['bool']) {
alert('저장되었습니다.!');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},
dataType:'json',
resetForm: false
});
return false;
});
});
jQuery(function(){
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
var event_id;
var event_start;
var event_end;
var calendar = $('#calendar').fullCalendar({
header: {
left: 'prev,next today',
center: 'title',
right: 'month,agendaWeek,agendaDay'
},
selectable: false,
selectHelper: true,
select: function(start, end, allDay) {
$('select[name=start_year]').val($.fullCalendar.formatDate(start,'yyyy'));
$('select[name=start_month]').val($.fullCalendar.formatDate(start,'MM')).change();
$('select[name=start_day]').val($.fullCalendar.formatDate(start,'d'));
$('select[name=start_hour]').val($.fullCalendar.formatDate(start,'HH'));
$('select[name=start_min]').val($.fullCalendar.formatDate(start,'mm'));
$('select[name=end_year]').val($.fullCalendar.formatDate(end,'yyyy'));
$('select[name=end_month]').val($.fullCalendar.formatDate(end,'MM')).change();
$('select[name=end_day]').val($.fullCalendar.formatDate(end,'d'));
$('select[name=end_hour]').val($.fullCalendar.formatDate(end,'HH'));
$('select[name=end_min]').val($.fullCalendar.formatDate(end,'mm'));
$('#btn_del').hide();
if(confirm('선택하신 일자 또는 시간에 일정을 추가하시겠습니까?')) {
showPopup('drag_popup_fullcalendar',{kind_pos:'center'});
}
},
eventClick: function(calEvent, jsEvent, view) {
event_id = calEvent.id;
event_start = calEvent.start;
event_end = calEvent.end;
$('select[name=start_year]').val($.fullCalendar.formatDate(calEvent.start,'yyyy'));
$('select[name=start_month]').val($.fullCalendar.formatDate(calEvent.start,'MM')).change();
$('select[name=start_day]').val($.fullCalendar.formatDate(calEvent.start,'d'));
$('select[name=start_hour]').val($.fullCalendar.formatDate(calEvent.start,'HH'));
$('select[name=start_min]').val($.fullCalendar.formatDate(calEvent.start,'mm'));
$('select[name=end_year]').val($.fullCalendar.formatDate(calEvent.end,'yyyy'));
$('select[name=end_month]').val($.fullCalendar.formatDate(calEvent.end,'MM')).change();
$('select[name=end_day]').val($.fullCalendar.formatDate(calEvent.end,'d'));
$('select[name=end_hour]').val($.fullCalendar.formatDate(calEvent.end,'HH'));
$('select[name=end_min]').val($.fullCalendar.formatDate(calEvent.end,'mm'));
$('input[name=title]').val(calEvent.title);
$('input[name=url]').val(calEvent.url);
$('#btn_del').show();
showPopup('drag_popup_fullcalendar',{kind_pos:'center'});
},
editable: false,
eventDragStop: function( event, jsEvent, ui, view ) {
return;
},
eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
if (!confirm("일정을 변경하시겠습니까?")) {
revertFunc();
}
else {
$.get('<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=edit_drag&idx='+event.id
+'&start_date='+$.fullCalendar.formatDate(event.start,'u')
+'&end_date='+$.fullCalendar.formatDate(event.end,'u'), function(data) {
if(data['bool']) {
alert('일정이 변경 되었습니다.!');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
}
},
eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
if (!confirm("일정을 변경하시겠습니까?")) {
revertFunc();
}
else {
}
},
events: [
<?php if($TPL_loop_event_1){$TPL_I1=-1;foreach($TPL_VAR["loop_event"] as $TPL_V1){$TPL_I1++;?>
{
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
<?php if($TPL_V1["bool_allday"]> 0){?>
start: new Date(<?php echo date('Y',$TPL_V1["sdate"])?>,<?php echo date('n',$TPL_V1["sdate"])- 1?>,<?php echo date('j',$TPL_V1["sdate"])?>),
<?php }else{?>
start: new Date(<?php echo date('Y',$TPL_V1["sdate"])?>,<?php echo date('n',$TPL_V1["sdate"])- 1?>,<?php echo date('j',$TPL_V1["sdate"])?>,<?php echo date('G',$TPL_V1["sdate"])?>,<?php echo date('i',$TPL_V1["sdate"])?>),
<?php }?>
<?php if(!empty($TPL_V1["end_date"])){?>
end: new Date(<?php echo date('Y',$TPL_V1["edate"])?>,<?php echo date('n',$TPL_V1["edate"])- 1?>,<?php echo date('j',$TPL_V1["edate"])?>,<?php echo date('G',$TPL_V1["edate"])?>,<?php echo date('i',$TPL_V1["edate"])?>),
<?php }else{?>
end: new Date(<?php echo date('Y',$TPL_V1["edate"])?>,<?php echo date('n',$TPL_V1["edate"])- 1?>,<?php echo date('j',$TPL_V1["edate"])?>),
<?php }?>
allDay: <?php if($TPL_V1["bool_allday"]> 0){?>true<?php }else{?>false<?php }?>
}<?php if($TPL_loop_event_1>$TPL_I1+ 1){?>,<?php }?>
<?php }}?>
]
});
// adding a every monday and wednesday events:
$('#calendar').fullCalendar( 'addEventSource',
function(start, end, callback) {
// When requested, dynamically generate virtual
// events for every monday and wednesday.
var events = [];
for (loop = start.getTime();
loop <= end.getTime();
loop = loop + (24 * 60 * 60 * 1000)) {
var test_date = new Date(loop);
<?php if($TPL_loop_event_1){foreach($TPL_VAR["loop_event"] as $TPL_V1){?>
<?php if($TPL_V1["bool_repeat"]== 1){?>
var edate_ = parseInt('<?php echo $TPL_V1["edate"]?>');
var startdate_ = parseInt('<?php echo $TPL_V1["startdate"]?>');
var enddate_ = parseInt('<?php echo $TPL_V1["enddate"]?>');
edate_ = edate_ * 1000;
startdate_ = startdate_ * 1000;
enddate_ = enddate_ * 1000;
if (edate_ < loop && enddate_ >= loop)
{
<?php if($TPL_V1["w1"]== 1){?>
// 월요일
if (test_date.getDay()==1) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
<?php if($TPL_V1["w2"]== 1){?>
// 화요일
if (test_date.getDay()==2) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
<?php if($TPL_V1["w3"]== 1){?>
// 수요일
if (test_date.getDay()==3) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
<?php if($TPL_V1["w4"]== 1){?>
// 목요일
if (test_date.getDay()==4) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
<?php if($TPL_V1["w5"]== 1){?>
// 금요일
if (test_date.getDay()==5) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
<?php if($TPL_V1["w6"]== 1){?>
// 토요일
if (test_date.getDay()==6) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
<?php if($TPL_V1["w7"]== 1){?>
// 일요일
if (test_date.getDay()==7) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: test_date
});
}
<?php }?>
}
<?php }else{?> // bool_repeat 1
var edate_ = parseInt('<?php echo $TPL_V1["edate"]?>');
var startdate_ = parseInt('<?php echo $TPL_V1["startdate"]?>');
var enddate_ = parseInt('<?php echo $TPL_V1["enddate"]?>');
edate_ = edate_ * 1000;
startdate_ = startdate_ * 1000;
enddate_ = enddate_ * 1000;
if (startdate_ == loop)
{
<?php if($TPL_V1["w1"]== 1){?>
// 월요일
if (test_date.getDay()==1) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
<?php if($TPL_V1["w2"]== 1){?>
// 화요일
if (test_date.getDay()==2) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
<?php if($TPL_V1["w3"]== 1){?>
// 수요일
if (test_date.getDay()==3) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
<?php if($TPL_V1["w4"]== 1){?>
// 목요일
if (test_date.getDay()==4) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
<?php if($TPL_V1["w5"]== 1){?>
// 금요일
if (test_date.getDay()==5) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
<?php if($TPL_V1["w6"]== 1){?>
// 토요일
if (test_date.getDay()==6) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
<?php if($TPL_V1["w7"]== 1){?>
// 일요일
if (test_date.getDay()==7) {
events.push({
id: <?php echo $TPL_V1["idx"]?>,
cate_code: 'BP53448',
<?php if($TPL_V1["bool_ing"]=='0'){?>
title: '[작업대기] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='1'){?>
title: '[작업중] <?php echo $TPL_V1["title"]?>',
<?php }elseif($TPL_V1["bool_ing"]=='2'){?>
title: '[완료] <?php echo $TPL_V1["title"]?>',
<?php }?>
url: '<?php echo $TPL_V1["url"]?>',
color: '#1ab394',
start: new Date(<?php echo date('Y',$TPL_V1["startdate"])?>,<?php echo date('n',$TPL_V1["startdate"])- 1?>,<?php echo date('j',$TPL_V1["startdate"])?>)
});
}
<?php }?>
}
<?php }?> // bool_repeat 0
<?php }}?> // loop
} // for loop
// return events generated
callback( events );
}
);
});
Date.getLastDay = function(Y,M) {
return new Date(Y,M,0).getDate();
}
jQuery(function(){
//년도 셀렉트 박스 구성
var cdate = new Date();
var cyear = cdate.getFullYear();
$('select#start_year, select#end_year').append('<option value="">::년도::</option>');
for (var i=cyear; i<cyear+5; i++) {
$('select#start_year, select#end_year').append('<option value="'+i+'">'+i+'</option>');
}
$('select#start_year, select#end_year').val(cyear);
//월에 따른 마지막 날짜를 구해서 셀렉트 박스를 만든다.
$('select[name=start_month]').change(function() {
var select_month_idx = $(this)[0].selectedIndex;
var select_year = $('select#start_year option:selected').val();
var select_month = $('option:selected',this).val();
if(select_month_idx == 0) {
//alert('월을 선택하여 주세요.!');
return false;
}
$('select#start_day').empty();
var last_day = Date.getLastDay(select_year,select_month);
$('select#start_day').append('<option value="" selected="selected">::일::</option>');
for (var i=1; i<=last_day; i++) {
$('select#start_day').append('<option value="'+i+'">'+i+'일</option>');
}
});
$('select[name=start_month]').val("<?php echo date('m')?>").change();
$('select[name=end_month]').change(function() {
var select_month_idx = $(this)[0].selectedIndex;
var select_year = $('select#end_year option:selected').val();
var select_month = $('option:selected',this).val();
if(select_month_idx == 0) {
//alert('월을 선택하여 주세요.!');
return false;
}
$('select#end_day').empty();
var last_day = Date.getLastDay(select_year,select_month);
$('select#end_day').append('<option value="" selected="selected">::일::</option>');
for (var i=1; i<=last_day; i++) {
$('select#end_day').append('<option value="'+i+'">'+i+'일</option>');
}
});
$('select[name=end_month]').val("<?php echo date('m')?>").change();
});
jQuery(function(){
$('#popup_fullcalendar').dragPopup({
popup_id: 'drag_popup_fullcalendar',
popup_title: ' 일정관리',
popup_width: 450,
popup_height: 150,
bool_today_close:false
});
});
function addSchedule() {
showPopup('drag_popup_fullcalendar',{kind_pos:'center'});
}
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/presaleAdmin.php'){?>
<!-- jqGrid -->
<script src="/template/admin/admin/js/plugins/jqGrid/i18n/grid.locale-en.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/jqGrid/jquery.jqGrid.min.js?t=1666836874"></script>
<!-- Custom and plugin javascript -->
<script src="/template/admin/admin/basic/js/presale.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/coins/admin/tradehistoryAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/coins/admin/exchangeHistoryAdmin.php'){?>
<!-- Custom and plugin javascript -->
<script src="/template/admin/admin/coins/js/tradehistory.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/coins/admin/orderHistoryAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/auction/admin/auctionHistoryAdmin.php'){?>
<!-- Custom and plugin javascript -->
<script src="/template/admin/admin/coins/js/orderHistory.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/member/admin/memberConfirm.php'){?>
<!-- Custom and plugin javascript -->
<script src="/template/admin/admin/member/js/member_confirm.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/member/admin/memberAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/member/admin/memberWithdraw.php'||$_SERVER["SCRIPT_NAME"]=='/member/admin/memberLevel.php'){?>
<!-- Custom and plugin javascript -->
<?php if($_SERVER["SCRIPT_NAME"]=='/member/admin/memberWithdraw.php'){?>
<script src="/template/admin/admin/member/js/withdraw.js?t=1666836874"></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/member/admin/memberAdmin.php'){?>
<script src="/template/admin/admin/member/js/customers.js?t=1666836874"></script>
<?php }?>
<script>
jQuery(function(){
$('select[name=sort_target]').val('<?php if(!empty($_GET["sort_target"])){?><?php echo $_GET["sort_target"]?><?php }else{?>date<?php }?>');//select
$('select[name=sort_method]').val('<?php if(!empty($_GET["sort_method"])){?><?php echo $_GET["sort_method"]?><?php }else{?>desc<?php }?>');//select
$('input[name=start_date]').val('<?php echo $_GET["start_date"]?>');
$('input[name=end_date]').val('<?php echo $_GET["end_date"]?>');
$('select[name=loop_scale]').val('<?php echo $_GET["loop_scale"]?>');
$('#loop_scale').change(function() {
var loop_scale = $(this).val();
location.href = '<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url_loop"]?>&loop_scale='+loop_scale;
});
});
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/billing/admin/billingAdmin.php'){?>
<!-- d3 and c3 charts -->
<script src="/template/admin/admin/js/plugins/d3/d3.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/c3/c3.min.js?t=1666836874"></script>
<script src="/template/admin/admin/billing/js/billing.js?t="></script>
<script>
jQuery(function(){
$('select[name=sort_target]').val('<?php if(!empty($_GET["sort_target"])){?><?php echo $_GET["sort_target"]?><?php }else{?>date<?php }?>');//select
$('select[name=sort_method]').val('<?php if(!empty($_GET["sort_method"])){?><?php echo $_GET["sort_method"]?><?php }else{?>desc<?php }?>');//select
$('input[name=start_date]').val('<?php echo $_GET["start_date"]?>');
$('input[name=end_date]').val('<?php echo $_GET["end_date"]?>');
$('select[name=loop_scale]').val('<?php echo $_GET["loop_scale"]?>');
$('#loop_scale').change(function() {
var loop_scale = $(this).val();
location.href = '<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url_loop"]?>&loop_scale='+loop_scale;
});
});
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/plan/admin/planAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/plan/admin/newplanAdmin.php'){?>
<!-- Custom and plugin javascript -->
<?php if($_SERVER["SCRIPT_NAME"]=='/plan/admin/planAdmin.php'){?>
<script src="/template/admin/admin/plan/js/plan.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/plan/admin/newplanAdmin.php'){?>
<script src="/template/admin/admin/plan/js/newplan.js?t="></script>
<?php }?>
<script>
jQuery(function(){
$('select[name=sort_target]').val('<?php if(!empty($_GET["sort_target"])){?><?php echo $_GET["sort_target"]?><?php }else{?>date<?php }?>');//select
$('select[name=sort_method]').val('<?php if(!empty($_GET["sort_method"])){?><?php echo $_GET["sort_method"]?><?php }else{?>desc<?php }?>');//select
$('input[name=start_date]').val('<?php echo $_GET["start_date"]?>');
$('input[name=end_date]').val('<?php echo $_GET["end_date"]?>');
$('select[name=loop_scale]').val('<?php echo $_GET["loop_scale"]?>');
$('#loop_scale').change(function() {
var loop_scale = $(this).val();
location.href = '<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url_loop"]?>&loop_scale='+loop_scale;
});
});
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/newKpisummaries.php'||$_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiSummaries.php'||$_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiMembers.php'||$_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiComments.php'||$_SERVER["SCRIPT_NAME"]=='/kpireports/admin/newKpiplans.php'||$_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiPlans.php'){?>
<!-- Custom and plugin javascript -->
<?php if($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/newKpisummaries.php'){?>
<script src="/template/admin/admin/kpireports/js/newkpi_summaries.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiSummaries.php'){?>
<script src="/template/admin/admin/kpireports/js/kpi_summaries.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiMembers.php'){?>
<script src="/template/admin/admin/kpireports/js/kpi_members.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiComments.php'){?>
<script src="/template/admin/admin/kpireports/js/kpi_comments.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/newKpiplans.php'){?>
<script src="/template/admin/admin/kpireports/js/newkpi_plans.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kpireports/admin/kpiPlans.php'){?>
<script src="/template/admin/admin/kpireports/js/kpi_plans.js?t="></script>
<?php }?>
<script>
jQuery(function(){
$('select[name=sort_target]').val('<?php if(!empty($_GET["sort_target"])){?><?php echo $_GET["sort_target"]?><?php }else{?>date<?php }?>');//select
$('select[name=sort_method]').val('<?php if(!empty($_GET["sort_method"])){?><?php echo $_GET["sort_method"]?><?php }else{?>desc<?php }?>');//select
$('input[name=start_date]').val('<?php echo $_GET["start_date"]?>');
$('input[name=end_date]').val('<?php echo $_GET["end_date"]?>');
$('select[name=loop_scale]').val('<?php echo $_GET["loop_scale"]?>');
$('#loop_scale').change(function() {
var loop_scale = $(this).val();
location.href = '<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url_loop"]?>&loop_scale='+loop_scale;
});
});
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/partner/admin/partnerAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/partner/admin/funnelAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/partner/admin/newfunnelAdmin.php'){?>
<!-- Custom and plugin javascript -->
<?php if($_SERVER["SCRIPT_NAME"]=='/partner/admin/partnerAdmin.php'){?>
<script src="/template/admin/admin/partner/js/partner.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/partner/admin/funnelAdmin.php'){?>
<script src="/template/admin/admin/partner/js/funnel.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/partner/admin/newfunnelAdmin.php'){?>
<script src="/template/admin/admin/partner/js/newfunnel.js?t="></script>
<?php }?>
<script>
jQuery(function(){
$('select[name=sort_target]').val('<?php if(!empty($_GET["sort_target"])){?><?php echo $_GET["sort_target"]?><?php }else{?>date<?php }?>');//select
$('select[name=sort_method]').val('<?php if(!empty($_GET["sort_method"])){?><?php echo $_GET["sort_method"]?><?php }else{?>desc<?php }?>');//select
$('input[name=start_date]').val('<?php echo $_GET["start_date"]?>');
$('input[name=end_date]').val('<?php echo $_GET["end_date"]?>');
$('select[name=loop_scale]').val('<?php echo $_GET["loop_scale"]?>');
$('#loop_scale').change(function() {
var loop_scale = $(this).val();
location.href = '<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url_loop"]?>&loop_scale='+loop_scale;
});
});
</script>
<script>
function adminAjaxSubmit(obj) {
obj.ajaxSubmit({
success: function (data, statusText) {
if(data['bool']) {
alert('저장되었습니다.!');
location.replace('<?php echo $_SERVER["SCRIPT_NAME"]?>?');
}
else {
if(data['msg'] == 'err_exist') {
alert('이미 사용중인 아이디 입니다.');
}
else if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_pw') {
alert('기존비밀번호가 맞지 않습니다.');
}
else if(data['msg'] == 'err_old_pw') {
alert('기존비밀번호를 입력하여 주세요.');
}
else if(data['msg'] == 'err_new_pw') {
alert('신규비밀번호를 입력하여 주세요.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},
dataType:'json',
resetForm: false
});
}
jQuery(function(){
$('#jsform1').submit(function() {
if(this.bool_passwd.checked==true) {
var chk_option = [
{ 'target':'name', 'name':'제휴사명', 'type':'blank', 'msg':'제휴사명을 입력하여 주세요.!' },
{ 'target':'phone', 'name':'회사전화', 'type':'blank', 'msg':'회사전화를 입력하여 주세요.!' },
{ 'target':'damdang', 'name':'담당자명', 'type':'blank', 'msg':'담당자명을 입력하여 주세요.!' },
{ 'target':'email', 'name':'담당자 이메일', 'type':'blank', 'msg':'담당자 이메일을 입력하여 주세요.!' },
{ 'target':'mobile', 'name':'담당자 휴대폰', 'type':'blank', 'msg':'담당자 휴대폰을 입력하여 주세요.!' },
{ 'target':'old_passwd', 'name':'기존비밀번호', 'type':'blank', 'msg':'기존비밀번호를 입력하여 주세요.!' },
{ 'target':'new_passwd', 'name':'신규비밀번호', 'type':'blank', 'msg':'신규비밀번호를 입력하여 주세요.!' },
{ 'target':'renew_passwd', 'name':'신규비밀번호', 'type':'blank', 'msg':'신규비밀번호를 다시 한번 입력하여 주세요.!' },
{ 'target':'new_passwd', 'target2':'renew_passwd', 'name':'비밀번호', 'type':'eq_check', 'msg':'비밀번호를 동일하게 입력하여 주세요.!' }
];
}
else {
var chk_option = [
{ 'target':'name', 'name':'제휴사명', 'type':'blank', 'msg':'제휴사명을 입력하여 주세요.!' },
{ 'target':'phone', 'name':'회사전화', 'type':'blank', 'msg':'회사전화를 입력하여 주세요.!' },
{ 'target':'damdang', 'name':'담당자명', 'type':'blank', 'msg':'담당자명을 입력하여 주세요.!' },
{ 'target':'email', 'name':'담당자 이메일', 'type':'blank', 'msg':'담당자 이메일을 입력하여 주세요.!' },
{ 'target':'mobile', 'name':'담당자 휴대폰', 'type':'blank', 'msg':'담당자 휴대폰을 입력하여 주세요.!' }
];
}
if(!jsForm(this,chk_option)) {
return false;
}
if(!confirm('저장하시겠습니까?')) {
return false;
}
adminAjaxSubmit($(this));
return false;
});
});
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/api/admin/apiAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/api/admin/apiHistory.php'||$_SERVER["SCRIPT_NAME"]=='/api/admin/apiFunnel.php'||$_SERVER["SCRIPT_NAME"]=='/advertisement/admin/adAdmin.php'||$_SERVER["SCRIPT_NAME"]=='/advertisement/admin/adBanner.php'||$_SERVER["SCRIPT_NAME"]=='/advertisement/admin/bannerHistory.php'){?>
<!-- d3 and c3 charts -->
<script src="/template/admin/admin/js/plugins/d3/d3.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/c3/c3.min.js?t=1666836874"></script>
<?php if($_SERVER["SCRIPT_NAME"]=='/api/admin/apiAdmin.php'){?>
<script src="/template/admin/admin/api/js/api.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/api/admin/apiHistory.php'){?>
<script src="/template/admin/admin/api/js/apihistory.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/api/admin/apiFunnel.php'){?>
<script src="/template/admin/admin/api/js/apifunnel.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/advertisement/admin/adAdmin.php'){?>
<script src="/template/admin/admin/advertisement/js/advertiser.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/advertisement/admin/adBanner.php'){?>
<script src="/template/admin/admin/advertisement/js/adbanner.js?t="></script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/advertisement/admin/bannerHistory.php'){?>
<script src="/template/admin/admin/advertisement/js/bannerhistory.js?t="></script>
<?php }?>
<script>
jQuery(function(){
$('select[name=sort_target]').val('<?php if(!empty($_GET["sort_target"])){?><?php echo $_GET["sort_target"]?><?php }else{?>date<?php }?>');//select
$('select[name=sort_method]').val('<?php if(!empty($_GET["sort_method"])){?><?php echo $_GET["sort_method"]?><?php }else{?>desc<?php }?>');//select
$('input[name=start_date]').val('<?php echo $_GET["start_date"]?>');
$('input[name=end_date]').val('<?php echo $_GET["end_date"]?>');
$('select[name=loop_scale]').val('<?php echo $_GET["loop_scale"]?>');
$('#loop_scale').change(function() {
var loop_scale = $(this).val();
location.href = '<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=list<?php echo $TPL_VAR["srch_url_loop"]?>&loop_scale='+loop_scale;
});
});
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/laboratory/admin/labAdmin.php'){?>
<!-- ChartJS-->
<script src="/template/admin/admin/js/plugins/chartJs/Chart.min.js?t=1666836874"></script>
<!-- d3 and c3 charts -->
<script src="/template/admin/admin/js/plugins/d3/d3.min.js?t=1666836874"></script>
<script src="/template/admin/admin/js/plugins/c3/c3.min.js?t=1666836874"></script>
<?php if($_GET["pg_mode"]=='kpi_joinnsave'){?>
<script src="/template/admin/admin/laboratory/js/joinnsave_chart.js?t=1666836874"></script>
<?php }elseif($_GET["pg_mode"]=='tpl_view'){?>
<script src="/template/admin/admin/laboratory/js/tpl_chart.js?t=1666836874"></script>
<?php }elseif($_GET["pg_mode"]=='stat_published'){?>
<script src="/template/admin/admin/laboratory/js/stat_chart.js?t=1666836874"></script>
<?php }elseif($_GET["pg_mode"]=='new_stat_published'){?>
<script src="/template/admin/admin/laboratory/js/new_stat_chart.js?t=1666836874"></script>
<?php }elseif($_GET["pg_mode"]=='analyze_published'){?>
<script src="/template/admin/admin/laboratory/js/analyze_chart.js?t=1666836874"></script>
<?php }elseif($_GET["pg_mode"]=='new_analyze_published'){?>
<script src="/template/admin/admin/laboratory/js/new_analyze_chart.js?t=1666836874"></script>
<?php }?>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/cscenter/admin/requestAdmin.php'){?>
<!-- Custom and plugin javascript -->
<script>
jQuery(function () {
$('#searchform').submit(function() {
var checked_size = $(':checkbox:checked',this).size();
var s_val = $('input[name=s_val]').val();
if(checked_size == 0) {
alert('검색조건을 선택하여 주세요');
return false;
}
if(s_val == '') {
alert('검색어를 입력하여 주세요');
return false;
}
});
$('#all_check').click(function() {
if(this.checked) {
$("table.list_table tbody input:checkbox").attr('checked','checked');
}
else {
$("table.list_table tbody input:checkbox").removeAttr('checked');
}
});
$("#list_form>table>tbody>tr").hover(
function () { $(this).css('background-color','#FFF2F0'); },
function () { $(this).css('background-color','#FFF'); }
);
});
function checkDel() {
var num_checked = $('#list_form :checkbox:checked').length;
if(num_checked == 0) {
alert('선택된 항목이 없습니다.!');
return false;
}
$.get('<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=del_multi&'+$('#list_form').serialize(),function(data) {
if(data['bool']) {
alert('삭제되었습니다.');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
}
</script>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/kspo/admin/spoAdmin.php'){?>
<!-- Custom and plugin javascript -->
<?php if($_SERVER["SCRIPT_NAME"]=='/kspo/admin/spoAdmin.php'&&($_GET["pg_mode"]=='view')){?>
<script>
jQuery(function () {
$('#rplform').submit(function() {
myeditor.outputBodyHTML();
var chk_option = [
];
if(!jsForm(this,chk_option)) {
return false;
}
if(!confirm('저장하시겠습니까?')) {
return false;
}
$(this).ajaxSubmit({
success: function (data, statusText) {
if(data['bool']) {
alert('저장되었습니다.!');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},
dataType:'json',
resetForm: false
});
return false;
});
});
function spoDel() {
if(!confirm('삭제하시겠습니까?')) {
return false;
}
$.get('?pg_mode=del&idx=<?php echo $_GET["idx"]?>',function (data) {
if(data['bool']) {
location.replace('?pg_mode=list<?php echo $TPL_VAR["srch_url"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
}
</script>
<?php }else{?>
<script>
jQuery(function () {
$('#searchform').submit(function() {
var checked_size = $(':checkbox:checked',this).size();
var s_val = $('input[name=s_val]').val();
if(checked_size == 0) {
alert('검색조건을 선택하여 주세요');
return false;
}
if(s_val == '') {
alert('검색어를 입력하여 주세요');
return false;
}
});
$('#all_check').click(function() {
if(this.checked) {
$("#list_form table tbody :checkbox").attr('checked','checked');
}
else {
$("#list_form table tbody :checkbox").removeAttr('checked');
}
});
$("#list_form>table>tbody>tr").hover(
function () { $(this).css('background-color','#FFF2F0'); },
function () { $(this).css('background-color','#FFF'); }
);
});
function checkDel() {
var num_checked = $('#list_form :checkbox:checked').length;
if(num_checked == 0) {
alert('선택된 항목이 없습니다.!');
return false;
}
$.get('<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=del_multi&'+$('#list_form').serialize(),function(data) {
if(data['bool']) {
alert('삭제되었습니다.');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
}
</script>
<?php }?>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/mypage/admin/mtomAdmin.php'){?>
<!-- Custom and plugin javascript -->
<?php if($_SERVER["SCRIPT_NAME"]=='/mypage/admin/mtomAdmin.php'&&($_GET["pg_mode"]=='view')){?>
<script>
var myeditor = new cheditor("rplcontents");
jQuery(function () {
$('#rplform').submit(function() {
myeditor.outputBodyHTML();
var chk_option = [
];
if(!jsForm(this,chk_option)) {
return false;
}
if(!confirm('저장하시겠습니까?')) {
return false;
}
$(this).ajaxSubmit({
success: function (data, statusText) {
if(data['bool']) {
alert('저장되었습니다.!');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},
dataType:'json',
resetForm: false
});
return false;
});
});
function mtomDel() {
if(!confirm('삭제하시겠습니까?')) {
return false;
}
$.get('?pg_mode=del&idx=<?php echo $_GET["idx"]?>',function (data) {
if(data['bool']) {
location.replace('?pg_mode=list<?php echo $TPL_VAR["srch_url"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
}
myeditor.config.editorHeight = '300px';
myeditor.config.editorWidth = '100%';
myeditor.inputForm = 'rplcontents';
myeditor.config.imgMaxWidth = 800;
myeditor.config.imgSetAttrWidth = true;
myeditor.config.useSource = true;
myeditor.config.usePreview = true;
myeditor.run();
</script>
<?php }else{?>
<script>
jQuery(function () {
$('#searchform').submit(function() {
var checked_size = $(':checkbox:checked',this).size();
var s_val = $('input[name=s_val]').val();
if(checked_size == 0) {
alert('검색조건을 선택하여 주세요');
return false;
}
if(s_val == '') {
alert('검색어를 입력하여 주세요');
return false;
}
});
$('#all_check').click(function() {
if(this.checked) {
$("#list_form table tbody :checkbox").attr('checked','checked');
}
else {
$("#list_form table tbody :checkbox").removeAttr('checked');
}
});
$("#list_form>table>tbody>tr").hover(
function () { $(this).css('background-color','#FFF2F0'); },
function () { $(this).css('background-color','#FFF'); }
);
});
function checkDel() {
var num_checked = $('#list_form :checkbox:checked').length;
if(num_checked == 0) {
alert('선택된 항목이 없습니다.!');
return false;
}
$.get('<?php echo $_SERVER["SCRIPT_NAME"]?>?pg_mode=del_multi&'+$('#list_form').serialize(),function(data) {
if(data['bool']) {
alert('삭제되었습니다.');
location.replace('<?php echo $_SERVER["REQUEST_URI"]?>');
}
else {
if(data['msg'] == 'err_access') {
alert('비정상적인 접근입니다.');
}
else if(data['msg'] == 'err_sess') {
location.replace('/admin/auth.php?ret_url=<?php echo base64_encode($_SERVER["REQUEST_URI"])?>');
}
else {
alert('재시도 해주세요.!');
}
}
},'json');
}
</script>
<?php }?>
<?php }?>