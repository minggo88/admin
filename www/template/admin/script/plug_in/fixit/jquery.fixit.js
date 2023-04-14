/**
 * jQuery Fixit Plug-in
 *  @requires jQuery v1.4 +
 * 
 * 특정 영역이 스크롤 다운 해서 위로 넘어가는 것을 막고 항상 top:0 위치에 걸쳐서 보이도록 하는 플러그인입니다. 스크롤 업 해서 다시 아래로 내려 오면 원래 위치로 돌아갑니다.
 * jQuery for Designers에 있는 Fixed Floating Elements의 소스를 이용해 plug-in으로 만든 것입니다.
 * IE6에서는 작동하지 않습니다.
 * 
 * 아래 CSS 클래스를 선언해야 합니다.
 * .fixit{position: fixed;top: 0;}
 * 
 * 자바스크립트 사용방법은 아래와 같습니다.
 * $('#fg_header_bottom').fixit();
 * 
 * [참고 자료] 
 * http://jqueryfordesigners.com/fixed-floating-elements/
 * http://static.jqueryfordesigners.com/demo/fixedfloat.html
 * 
 *  Licensed under GPL licenses:
 *  http://www.gnu.org/licenses/gpl.html
 * 
 *  Version: 1.0
 *  Dated : 2012-02-03
 */
(function($) {
	jQuery.fn.fixit = function(options){
		var defaults = { 
			'add_class_name': ''
		};  
		var options = $.extend(defaults, options);

		var _fixit = function($self){
			
			var msie6 = $.browser == 'msie' && $.browser.version < 7;

			if (!msie6) {
				// what the y position of the scroll is
				var y = $(window).scrollTop();

				// whether that's below the form
				if (y >= $self.data('top')) {
					// if so, ad the fixed class
					$self.addClass('fixit').addClass(options.add_class_name);
				} else {
					// otherwise remove it
					$self.removeClass('fixit').addClass(options.add_class_name);
				}
			}
		}

		return this.each(function(){
			var $self = $(this);
			var top = $self.offset().top - parseFloat($self.css('margin-top').replace(/auto/, 0));
			$self.data('top', top);
			$(window).scroll(function (event) {
				_fixit($self);
			});
		});
	};
})(jQuery);
