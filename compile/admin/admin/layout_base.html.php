<?php /* Template_ 2.2.6 2022/10/27 11:14:34 /home/ubuntu/www/admin/www/template/admin/admin/layout_base.html 000006929 */ 
$TPL_loop_css_1=empty($TPL_VAR["loop_css"])||!is_array($TPL_VAR["loop_css"])?0:count($TPL_VAR["loop_css"]);?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Administrator Console</title>
<link href="/template/admin/admin/css/bootstrap.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/font-awesome/css/font-awesome.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/webfont/cryptocoins.css?t=1666836874" rel="stylesheet">
<meta property="og:title" content="<?php echo $TPL_VAR["config_basic"]["shop_ename"]?>">
<meta property="og:description" content="<?php echo $TPL_VAR["config_basic"]["shop_ename"]?>">
<meta property="og:image" content="/kakao-kmcse-logo.png">
<link href="/template/admin/admin/css/plugins/iCheck/custom.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/datapicker/datepicker3.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/ionRangeSlider/ion.rangeSlider.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/daterangepicker/daterangepicker-bs3.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/select2/select2.min.css?t=1666836874" rel="stylesheet">
<!-- Toastr style -->
<link href="/template/admin/admin/css/plugins/toastr/toastr.min.css?t=1666836874" rel="stylesheet">
<!-- Gritter -->
<link href="/template/admin/admin/js/plugins/gritter/jquery.gritter.css?t=1666836874" rel="stylesheet">
<!-- c3 Charts -->
<link href="/template/admin/admin/css/plugins/c3/c3.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/colorpicker/bootstrap-colorpicker.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css?t=1666836874" rel="stylesheet">
<!-- jquery-ui -->
<link href="/template/admin/admin/css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css?t=1666836874" rel="stylesheet">
<!-- jqgrid -->
<link href="/template/admin/admin/css/plugins/jqGrid/ui.jqgrid.css?t=1666836874" rel="stylesheet">
<!-- datatables -->
<!-- <link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"> -->
<!-- <link href="//cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet"> -->
<?php if($_SERVER["SCRIPT_NAME"]=='/admin/index.php'||$_SERVER["SCRIPT_NAME"]=='/'||$_SERVER["SCRIPT_NAME"]=='/index.php'){?>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/extToss.php'){?>
<link href="/template/admin/admin/assets/styles/toss.css?t=" rel="stylesheet">
<link href="/template/admin/admin/assets/styles/rotation.css?t=" rel="stylesheet">
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/schedule/admin/schedule.php'||$_SERVER["SCRIPT_NAME"]=='/schedule/admin/calendar.php'){?>
<link href="/template/admin/admin/css/plugins/fullcalendar/fullcalendar.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/fullcalendar/fullcalendar.print.css?t=1666836874" rel='stylesheet' media='print'>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/laboratory/admin/labAdmin.php'&&($_GET["pg_mode"]=='kpi_joinnsave'||$_GET["pg_mode"]=='tpl_view'||$_GET["pg_mode"]=='stat_published'||$_GET["pg_mode"]=='new_stat_published'||$_GET["pg_mode"]=='analyze_published'||$_GET["pg_mode"]=='new_analyze_published')){?>
<?php if($_GET["pg_mode"]=='kpi_joinnsave'||$_GET["pg_mode"]=='tpl_view'||$_GET["pg_mode"]=='stat_published'||$_GET["pg_mode"]=='new_stat_published'||$_GET["pg_mode"]=='analyze_published'||$_GET["pg_mode"]=='new_analyze_published'){?>
<link href="/template/admin/admin/css/plugins/chosen/bootstrap-chosen.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/cropper/cropper.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/switchery/switchery.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/jasny/jasny-bootstrap.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/nouslider/jquery.nouislider.css?t=1666836874" rel="stylesheet">
<?php }?>
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/graphs.php'&&($_GET["pg_mode"]=='graph_morris')){?>
<!-- morris -->
<link href="/template/admin/admin/css/plugins/morris/morris-0.4.3.min.css?t=1666836874" rel="stylesheet">
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/graphs.php'&&($_GET["pg_mode"]=='graph_chartist')){?>
<link href="/template/admin/admin/css/plugins/chartist/chartist.min.css?t=1666836874" rel="stylesheet">
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/metrics.php'&&($_GET["pg_mode"]=='metrics')){?>
<link href="/template/admin/admin/css/plugins/chartist/chartist.min.css?t=1666836874" rel="stylesheet">
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/bbs/admin/bbsAdmin.php'&&($_GET["pg_mode"]=='list')){?>
<!-- FooTable -->
<link href="/template/admin/admin/css/plugins/footable/footable.core.css?t=1666836874" rel="stylesheet">
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/bbs/admin/bbsAdmin.php'&&($_GET["pg_mode"]=='form_new')){?>
<!-- <link href="/template/admin/admin/css/animate.css?t=1666836874" rel="stylesheet"> -->
<link href="/template/admin/admin/css/plugins/dropzone/basic.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/dropzone/dropzone.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/jasny/jasny-bootstrap.min.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/plugins/codemirror/codemirror.css?t=1666836874" rel="stylesheet">
<!-- <link href="/template/admin/admin/css/style.css?t=1666836874" rel="stylesheet"> -->
<?php }?>
<!-- loop_css -->
<?php if($TPL_loop_css_1){foreach($TPL_VAR["loop_css"] as $TPL_V1){?>
<link href="<?php echo $TPL_V1?>" rel="stylesheet">
<?php }}?>
<!-- loop_css -->
<link href="/template/admin/admin/css/animate.css?t=1666836874" rel="stylesheet">
<link href="/template/admin/admin/css/style.css?t=1666836874" rel="stylesheet">
</head>
<?php if($_SERVER["SCRIPT_NAME"]=='/admin/test.php'){?>
<body class="gray-bg">
<div class="middle-box text-center loginscreen animated fadeInDown">
<?php }elseif($_SERVER["SCRIPT_NAME"]=='/admin/analysesAdmin.php'){?>
<body onload="aijinet.analyses.Application.main();">
<div id="wrapper">
<?php }else{?>
<body>
<div id="wrapper">
<?php }?>
<?php $this->print_("js_tpl_contents",$TPL_SCP,1);?>
</div>
</body>
</html>