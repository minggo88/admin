/**
 * @author Lee Jun Sung
 */
 
(function($) {
	$.fn.dragPopup = function(settings) {
		var config = {
			popup_id: 'drag',
			popup_title: '',
			popup_width: 300,
			popup_height: 0,
			bool_today_close:true
		};
		settings = $.extend(config, settings);

		var popup_id = settings['popup_id'];
		var popup_title = settings['popup_title'];
		var popup_width = settings['popup_width'];
		var popup_height = settings['popup_height'];
		var bool_today_close = settings['bool_today_close'];
		
		$(this)
			.wrap('<div id="'+popup_id+'" class="drag_popup"></div>');
		$(this)
			.css('display','block')
			.wrap('<div class="drag_popup_contents"></div>');

		$('#'+popup_id).prepend('<div class="drag_popup_handle"><div class="drag_popup_title">'+popup_title+'</div><div class="drag_popup_control"><span id="btn_close_'+popup_id+'">×</span></div></div>');
		if(bool_today_close) {
			$('#'+popup_id).append('<div class="drag_popup_footer"><span id="btn_close_today_'+popup_id+'" style="cursor:pointer;">Close</span></div>');
		}

		//popup 사이즈를 결정한다.
		if(popup_width > 0) {
			$('#'+popup_id).css('width',popup_width+'px');
		}

		if(popup_height > 0) {
			$('#'+popup_id).css('height',popup_height+'px');

			if(bool_today_close) {
				var handle_height = $('#'+popup_id+' div.drag_popup_handle').height();
				var footer_height = $('#'+popup_id+' div.drag_popup_footer').height();
				var contents_height = popup_height - (handle_height + footer_height+2);
			}
			else {
				var handle_height = $('#'+popup_id+' div.drag_popup_handle').height();
				var contents_height = popup_height - (handle_height+1);
			}
			$('#'+popup_id+' div.drag_popup_contents').css('height',contents_height);
			//alert($('#'+popup_id+' div.drag_popup_contents').height());
		}

		/*
		$('#'+popup_id).drag(function( ev, dd ){

			if($('*').is('div#divfixed')) {
				var popup_top = dd.offsetY;
				//var popup_left = dd.offsetX;
				var popup_left = dd.offsetX - ($(window).width() - $('div#divfixed').width())/2;
			}
			else {
				var popup_top = dd.offsetY;
				var popup_left = dd.offsetX;
			}
			$(this).css({
				top: popup_top,
				left: popup_left // - (브라우져전체사이즈/2 - 레이아웃 사이즈/2)
			});
		},{ handle:".drag_popup_title" });
		*/

		$('#btn_close_'+popup_id).click(function(data) {
			$('#'+popup_id).hide();
		});

		if(bool_today_close) {
			$('#btn_close_today_'+popup_id).click(function(data) {
				var cookie_name = 'popupCookie'+popup_id;
				$.cookie(cookie_name,"Y", { expires: 1 });
				$('#'+popup_id).hide();
			});
		}
		return this;
	};
})(jQuery);


function showPopup(id,pos) {
	//kind_pos : center, manual
	pos = pos || { kind_pos:'center', pos_x:0, pos_y:0 };
	var pos_x, pos_y;

	if(pos['kind_pos'] == 'center') {
		if($('*').is('div#divfixed')) {
			pos_x = $(window).scrollTop() + ($(window).height() - $('#'+id).height())/2;
			pos_y = $(window).scrollLeft() + ($(window).width() - $('div#divfixed').width())/2;
		}
		else {
			pos_x = $(window).scrollTop() + ($(window).height() - $('#'+id).height())/2;
			pos_y = $(window).scrollLeft() + ($(window).width() - $('#'+id).width())/2;
		}
	}
	else {
		pos_x = pos['pos_x'];
		pos_y = pos['pos_y'];
	}

	$('#'+id).css({
		top: pos_x,
		left: pos_y
	});
	$('#'+id).show();
}

/*
showPopup('아이디',{kind_pos:'manual',pos_x:100,pos_y:100})
<div><a href="javascript:;" onclick="showPopup('drag',{kind_pos:'manual',pos_x:100,pos_y:100});return false;">팝업띄우기</a></div>
*/