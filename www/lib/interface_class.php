<?php
/*--------------------------------------------
Date : 2010-11-18
Author : Danny Hwang
comment :
--------------------------------------------*/
class ControlUserInteface
{
	function __construct()
	{
		$this->css = array();
		$this->script = array();
		$this->layout = array();
		$this->navi = array();
		$this->cNum = 1;
		$this->pNum = 1;
		$this->sNum = 1;
	}

	function setBasicInterface($mode='user',$type='a4',$bool_mobile=0)
	{
		$this->mode = $mode;
		$user_skin = $GLOBALS['user_skin'];


		if($mode == 'user') {
			// $this->addScript("/template/".getSiteCode()."/script/jquery.js");
			$this->addScript("/template/".getSiteCode()."/script/php.default.min.js");
			$this->addScript("/template/".getSiteCode()."/script/util.js");
			$this->addScript('/template/'.getSiteCode().'/smc/js/js_lang.js');
			$this->addCss('/template/'.getSiteCode().'/css/button.css');
			$this->layout['layout'] = 'layout_base.html';

			$this->setPlugIn('select');
			$this->setPlugIn('fixit');
			$this->setPlugIn('ddlevelsmenu');
			$this->setPlugIn('jcarousel');

			if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') == true) {
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/common_ie6.css');
			}
			else {
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/common.css');
			}

			$this->navi['Home'] = '/';

			//head 템플릿 선언
			if(BOOL_MOBILE) {
				//모바일 css
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_header_m.css');
			} else {
				//현재 css
				if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') == true) {
					$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_header_ie6.css');
				}
				else {
					$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_header.css');
				}
			}
			$this->layout['js_tpl_header'] = 'js_header.html';
			//foot 템플릿 선언
			if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') == true) {
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_footer_ie6.css');
			}
			else {
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_footer.css');
			}
			$this->layout['js_tpl_footer'] = 'js_footer.html';

			if($type == 'a3') {
				if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') == true) {
					$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/layout_a3_ie6_only.css');
				} else {
					$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/layout_a3.css');
				}
				$this->layout['js_tpl_contents'] = 'layout_a3.html';

			}
			else if($type == 'a4') {
				if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0') == true) {
					$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/layout_a4_ie6_only.css');
				} else {
					$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/layout_a4.css');
				}
				$this->layout['js_tpl_contents'] = 'layout_a4.html';
				//left 템플릿 선언
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_left.css');
				$this->layout['js_tpl_left'] = 'js_left.html';
			}
			else if($type == 'main') {
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/layout_a4.css');
				$this->layout['js_tpl_contents'] = 'layout_main.html';
				$this->addCss('/template/'.getSiteCode().'/'.$user_skin.'/css/js_left.css');
				$this->layout['js_tpl_left'] = 'js_left_main.html';
			}
			else { //iframe
				$this->layout['layout'] = 'layout_base_iframe.html';
			}
		} elseif($mode == 'new') {
			$this->layout['layout'] = 'layout_base_new.html';

			$this->navi['Home'] = '/';
			$this->layout['js_tpl_header'] = 'js_header.html';
			$this->layout['js_tpl_footer'] = 'js_footer.html';

			if($type == 'a3') {
				$this->layout['js_tpl_contents'] = 'layout_a3.html';
			}
			else if($type == 'a4') {
				$this->layout['js_tpl_contents'] = 'layout_a4.html';
				$this->layout['js_tpl_left'] = 'js_left.html';
			}
			else if($type == 'main') {
				$this->layout['js_tpl_contents'] = 'layout_main.html';
				$this->layout['js_tpl_left'] = 'js_left_main.html';
			}
			else { //iframe
				$this->layout['layout'] = 'layout_base_iframe.html';
			}
		} elseif($mode == 'mobile') {

			// $this->addScript("/template/".getSiteCode()."/script/jquery.js");
			$this->addScript("/template/".getSiteCode()."/script/php.default.min.js");
			$this->addScript("/template/".getSiteCode()."/script/util.js");
			$this->addCss('/template/'.getSiteCode().'/css/button.css');

			$this->layout['layout'] = 'layout_mobile.html';
			$this->layout['js_tpl_contents'] = 'layout_a1.html';
		}
		else { //admin

			// $this->addScript("/template/".getSiteCode()."/script/jquery.js");
			$this->addScript("/template/".getSiteCode()."/script/php.default.min.js");
			$this->addScript("/template/".getSiteCode()."/script/util.js");
			$this->addScript('/template/'.getSiteCode().'/admin/js/js_lang.js');
			$this->addCss('/template/'.getSiteCode().'/css/button.css');
			$this->layout['layout'] = 'layout_base.html';

			if($type == 'iframe') {
				$this->layout['js_tpl_contents'] = 'layout_base_iframe.html';
			}
			else if($type == 'a1') {
				$this->layout['js_tpl_contents'] = 'layout_admin.html';
			}
			else {
				$this->navi['Home'] = '/admin/index.php';
				$this->layout['js_tpl_contents'] = 'layout_admin.html';
				$this->layout['js_tpl_header'] = 'header.html';
				$this->layout['js_tpl_footer'] = 'footer.html';
			}
		}
	}

	function setPlugIn($package)
	{
		if($package == 'jcarousel') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jcarousel/jcarousellite.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/mousewheel/jquery.mousewheel.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/easing/jquery.easing.js');
		}
		else if($package == 'datatables-1.10.19' || $package == 'datatables') {
			$this->addCss('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');
			$this->addCss('//cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css');
			$this->addCss('//cdn.datatables.net/responsive/2.2.2/css/responsive.dataTables.min.css');
			
			$this->addScript('//cdn.datatables.net/1.10.19/js/jquery.dataTables.js');
			$this->addScript('//cdn.datatables.net/responsive/2.2.2/js/dataTables.responsive.min.js');
			$this->addScript('//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js');
			$this->addScript('/template/'.getSiteCode().'/admin/js/pdfmake.min.js');
			$this->addScript('/template/'.getSiteCode().'/admin/js/vfs_fonts.js');
			$this->addScript('//cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js');
			$this->addScript('//cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js');
			$this->addScript('//cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js');
			$this->addScript('//cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js');
		}
		else if($package == 'magnific-popup') {
			$this->addScript('/template/'.getSiteCode().'/admin/js/plugins/magnific-popup/jquery.magnific-popup.min.js');
			$this->addCss('/template/'.getSiteCode().'/admin/js/plugins/magnific-popup/magnific-popup.min.css');
		}
		else if($package == 'bootstrap-toggle') {
			$this->addScript('/template/'.getSiteCode().'/admin/js/plugins/bootstrap-toggle/bootstrap-toggle.min.js');
			$this->addCss('/template/'.getSiteCode().'/admin/css/plugins/bootstrap-toggle/bootstrap-toggle.min.css');
		}
		else if($package == 'switchery') {
			$this->addScript('/template/'.getSiteCode().'/admin/js/plugins/switchery/switchery.js');
			$this->addCss('/template/'.getSiteCode().'/admin/css/plugins/switchery/switchery.css');
		}
		else if($package == 'typehead') {
			$this->addScript('/template/'.getSiteCode().'/admin/js/plugins/typehead/bootstrap3-typeahead.min.js');
		}
		else if($package == 'dragsort') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/dragsort/jquery.dragsort.js');
		}
		else if($package == 'tablednd') {
			$this->addScript("/template/".getSiteCode()."/script/plug_in/tablednd/jquery.tablednd_0_5.js");
		}
		else if($package == 'loading') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/loading/loading.min.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/loading/loading.min.js");
		}
		else if($package == 'form') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/form/jquery.form.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/form/jsform.js');
		}
		else if($package == 'drag') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/drag/jquery.drag.js');
		}
		else if($package == 'drop') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/drop/jquery.drop.js');
		}
		else if($package == 'lightbox') {
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/lightbox/slimbox2.css');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/lightbox/slimbox2.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/lightbox/autoload.js');
		}
		else if($package == 'ddlevelsmenu') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/ddlevelsmenu/ddlevelsmenu-base.css");
			//$this->addCss("/template/".getSiteCode()."/script/plug_in/ddlevelsmenu/ddlevelsmenu-sidebar.css");
			$this->addCss("/template/".getSiteCode()."/script/plug_in/ddlevelsmenu/ddlevelsmenu-topbar.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/ddlevelsmenu/ddlevelsmenu.js");
		}
		else if($package == 'tooltip') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tooltip/tooltip.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/tooltip/jquery.bgiframe.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/tooltip/jquery.delegate.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/tooltip/jquery.tooltip.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/tooltip/chili-1.7.pack.js");
		}
		else if($package == 'tooltipster') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tooltipster/css/tooltipster.css");
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tooltipster/css/themes/tooltipster-light.css");
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tooltipster/css/themes/tooltipster-noir.css");
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tooltipster/css/themes/tooltipster-punk.css");
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tooltipster/css/themes/tooltipster-shadow.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/tooltipster/js/jquery.tooltipster.js");
		}
		else if($package == 'calendar') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/calendar/calendar.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/calendar/jquery.calendar.js");
		}
		else if($package == 'tree') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/tree/tree.css");
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/tree/jquery.tree.js');
		}
		else if($package == 'treeview') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/treeview/jquery.treeview.css");
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/cookie/jquery.cookie.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/treeview/jquery.treeview.js');
		}
		else if($package == 'cleditor') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/cleditor/cleditor.css");
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.min.js');
			//$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.xhtml.min.js');
			//$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.icon.min.js');
		}
		else if($package == 'cleditorUpload') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/cleditor/cleditor.css");
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.min.js');
			//$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.xhtml.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.extimage.js');
			//$this->addScript('/template/'.getSiteCode().'/script/plug_in/cleditor/jquery.cleditor.icon.min.js');
		}
		else if($package == 'cookie') {
			$this->addScript("/template/".getSiteCode()."/script/plug_in/cookie/jquery.cookie.js");
		}
		else if($package == 'atools') {
			$this->addScript("/template/".getSiteCode()."/script/plug_in/atools/jquery.atools.min.js");
		}
		else if($package == 'popup') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/popup/popup.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/cookie/jquery.cookie.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/drag/jquery.drag.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/popup/jquery.popup.js");
		}
		else if($package == 'paginate') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/paginate/paginate.css");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/paginate/jquery.paginate.js");
		}
		else if($package == 'ui_tabs') {
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.tabs.js');
		}
		else if($package == 'jqplot') {
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/jqplot/jquery.jqplot.css');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/excanvas.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jquery.jqplot.js');
			/*
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.dragable.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.mekkoRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.categoryAxisRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.pointLabels.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.highlighter.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.cursor.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.dateAxisRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.pieRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.mekkoAxisRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.logAxisRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.trendline.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.canvasAxisTickRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.canvasTextRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.canvasAxisLabelRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.barRenderer.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/jqplot/jqplot.ohlcRenderer.js');
			*/
		}
		else if($package == 'flot') {
			$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/excanvas.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.colorhelpers.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.crosshair.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.fillbetween.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.image.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.navigate.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.pie.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.resize.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.selection.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.stack.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.symbol.min.js");
			//$this->addScript("/template/".getSiteCode()."/script/plug_in/flot/jquery.flot.threshold.min.js");
		}
		else if($package == 'fullcalendar') {
			$this->addCss("/template/".getSiteCode()."/script/plug_in/fullcalendar/fullcalendar.css");
			//$this->addCss("/template/".getSiteCode()."/script/plug_in/fullcalendar/fullcalendar.print.css");
			$this->addScript("/template/".getSiteCode()."/script/jquery-ui.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/fullcalendar/fullcalendar.min.js");
			$this->addScript("/template/".getSiteCode()."/script/plug_in/fullcalendar/gcal.js");
		}
		else if(strtolower($package) == 'fixit') { // fixit
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/fixit/jquery.fixit.js');
		}
		else if($package == 'select') { // fixit
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/select/jquery.select.js');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/select/select.css');
		}
		else if($package == 'slideshow') { // fixit
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/slideshow/jquery.slideshow.js');
		}
		else if($package == 'hoverpulse') { // hoverpulse
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/hoverpulse/jquery.hoverpulse.js');
		}
		else if($package == 'kendoui_pro') {
			//$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/js/jquery.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/js/kendo.all.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/js/cultures/kendo.culture.ko-KR.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/examples/content/shared/js/console.js');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/examples/content/shared/styles/examples-offline.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/styles/kendo.common.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/styles/kendo.rtl.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/styles/kendo.default.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/styles/kendo.dataviz.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendoui_pro/styles/kendo.dataviz.default.min.css');
		}
		else if($package == 'kendo') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/kendo.all.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/cultures/kendo.culture.ko-KR.min.js');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.common.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.default.min.css');
		}
		else if($package == 'kendo_web') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/kendo.web.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/cultures/kendo.culture.ko-KR.min.js');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.common.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.default.min.css');
		}
		else if($package == 'kendo_mobile') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/kendo.mobile.min.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/cultures/kendo.culture.ko-KR.min.js');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.common.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.default.min.css');
		}
		else if($package == 'kendo_dataviz') {
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kendo/js/kendo.dataviz.min.js');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.common.min.css');
			$this->addCss('/template/'.getSiteCode().'/script/plug_in/kendo/styles/kendo.default.min.css');
		}
		else if($package == 'cheditor') {
			$this->addScript("/cheditor/cheditor.js");
		}
		else if($package == 'kakao_login') {
			$this->addScript('https://developers.kakao.com/sdk/js/kakao.js');
			$this->addScript('/template/'.getSiteCode().'/script/plug_in/kakao/login.js');
			// <!-- load kakao social login library -->
			// <script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
		}
		else {
		}

		//jQuery UI
		/***********
		ui_datepicker
		ui_accordion
		ui_button
		ui_dialog
		ui_draggable
		ui_droppable
		ui_progressbar
		ui_resizable
		ui_slider
		ui_sortable
		ui_tabs
		***********/
		if(preg_match('/^ui\w*$/',$package)) {
			$this->addCss('/template/'.getSiteCode().'/script/ui_themes/smoothness/jquery.ui.all.css');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.core.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.widget.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.mouse.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.position.js');
		}
		if($package == 'ui_accordion') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.accordion.js'); }
		if($package == 'ui_autocomplete') {
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.position.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.autocomplete.js');
		}
		if($package == 'ui_button') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.button.js'); }
		if($package == 'ui_datepicker') {
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.datepicker.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/i18n/jquery.ui.datepicker-ko.js');//korean pack
		}
		if($package == 'ui_dialog') {
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.draggable.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.resizable.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.button.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.dialog.js');
		}
		if($package == 'ui_draggable') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.draggable.js'); }
		if($package == 'ui_droppable') {
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.draggable.js');
			$this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.droppable.js');
		}
		if($package == 'ui_progressbar') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.progressbar.js'); }
		if($package == 'ui_resizable') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.resizable.js'); }
		if($package == 'ui_selectable') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.selectable.js'); }
		if($package == 'ui_slider') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.slider.js'); }
		if($package == 'ui_sortable') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.sortable.js'); }
		if($package == 'ui_tabs') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.ui.tabs.js'); }

		//jQuery effects
		if(preg_match('/^effects\w*$/',$package)) { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.core.js'); }
		if($package == 'effects_blind' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.blind.js'); }
		if($package == 'effects_bounce' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.bounce.js'); }
		if($package == 'effects_clip' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.clip.js'); }
		if($package == 'effects_drop' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.drop.js'); }
		if($package == 'effects_explode' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.explode.js'); }
		if($package == 'effects_fold' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.fold.js'); }
		if($package == 'effects_highlight' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.highlight.js'); }
		if($package == 'effects_pulsate' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.pulsate.js'); }
		if($package == 'effects_scale' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.scale.js'); }
		if($package == 'effects_shake' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.shake.js'); }
		if($package == 'effects_slide' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.slide.js'); }
		if($package == 'effects_transfer' || $package == 'effects_all') { $this->addScript('/template/'.getSiteCode().'/script/ui/jquery.effects.transfer.js'); }

	}

	function addScript($script)
	{
		if(!in_array($script,$this->script)) {
			$script = $this->addFileMDatetoURL($script);
			$this->script[] = $script;
		}
	}

	function addCss($css)
	{
		if(!in_array($css,$this->css)) {
			$css = $this->addFileMDatetoURL($css);
			$this->css[] = $css;
		}
	}

	function addFileMDatetoURL($url) {
		$t = '';
		$url = trim($url);
		if(! preg_match('/^http/', $url)) {
			list($file, $param) = explode('?',$url);
			if(file_exists(__dir__.'/../'.$file)) {
				$t = filemtime(realpath(__dir__.'/../'.$file));
				$url = $param ? $file.'?'.$param.'&t='.$t : $file.'?t='.$t;
			}
		}
		return $url;
	}

	function addNavi($arr)
	{

		global $dbcon;

		$arr=array();
		if(!empty($_GET['cate_code'])) {

			$query = array();
			$query['table_name'] = 'js_contents_category';
			$query['tool'] = 'row';
			$query['fields'] = 'cate_code, parent_code,contents_name, kinds_contents, contents_code, depth, link_code';
			$query['where'] = 'where cate_code=\''.$_GET['cate_code'].'\'';
			$row = $dbcon->query($query,__FILE__,__LINE__);

			$query['tool'] = 'select_one';
			$query['fields'] = 'contents_name';
			$query['where'] = 'where cate_code=\''.$row['parent_code'].'\'';
			$contents_name =$dbcon->query($query,__FILE__,__LINE__);

			$arr[$contents_name] = 1;
			$arr[$row['contents_name']] = 1;
		}


		foreach ($arr as $key => $val) {
			$this->navi[$key] = $val;
		}
	}

	function setDefault()
	{
		$this->css = array();
		$this->script = array();
		$this->layout = array();
	}

	function display($print='layout')
	{
		if(!empty($this->css)) {
			$this->tpl->assign('loop_css',$this->css);
		}
		if(!empty($this->script)) {
			$this->tpl->assign('loop_script',$this->script);
		}
		if(!empty($this->layout)) {
			$this->tpl->define($this->layout);
		}
		if(!empty($this->navi)) {
			$link = array();
			foreach( $this->navi as $key=>$val ) {
				if($val == 1) { $link[] = $key; }
				else { $link[] = '<a href="'.$val .'">'.$key.'</a>'; }
			}
			$this->tpl->assign('link',implode(' > ',$link));
		}
		$this->tpl->print_($print);
	}
}

?>