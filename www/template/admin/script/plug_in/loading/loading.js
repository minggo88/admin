var c_d = true;
var c_t = true;
if (c_d && c_t) {
    (function($) {
        /**
         * loading plugin
         * css spiner : https://codepen.io/collection/HtAne/
         * 
         * @author benant<benant@paran.com>
         * 
         */
        $.fn.loading = function() {
            var callback;
            var options;
            var runmode;
            for (var i = 0; i < arguments.length; i++) {
                switch (typeof arguments[i]) {
                    case 'function':
                        callback = arguments[i];
                        break;
                    case 'object':
                        options = arguments[i];
                        break;
                    case 'string':
                        runmode = arguments[i];
                        break;
                }
            }
            var settings = $.extend({
                'zIndex': 2147483647,
                'show': true,
                'image': "css",
                'blur': "1",
                'opacity': .9
            }, options || {});

            var $_load_box = $('<div class="loader"></div>').css({
                'margin': 'auto',
                'width': '100px',
                'height': '100px',
                'overflow': 'hidden',
                'background': 'url(' + settings.image + ') no-repeat center center',
                'opacity': settings.opacity,
                'z-index': settings.zIndex
            });
            if(settings.image=='css') {
                $_load_box = $('<div class="box_loader"><div class="cs-loader"><div class="cs-loader-inner"><label>	●</label><label>	●</label><label>	●</label><label>	●</label><label>	●</label><label>	●</label></div></div></div>');
            }
            return this.each(function() {
                if (runmode == 'hide') {
                    var self = this;
                    setTimeout(function() {
                        $('#' + $(self).attr('loader')).fadeOut(function() {
                            $(this).remove()
                        });
                        $(self).removeClass('blurLoading');
                    }, navigator.userAgent.indexOf('msie')>=0 ? 2000 : 500)
                } else {
                    var offset = $(this).offset();
                    var id = new Date().getTime() + '_' + Math.round(Math.random() * 100);
                    var $_load_box_clone = $_load_box.clone().attr('id', id);
                    if(settings.image=='css') {
                        $_load_box_clone.css({
                            'position': 'absolute',
                            'width': $(this).outerWidth(),
                            'height': $(this).outerHeight(),
                            'left': offset.left,
                            'top': offset.top
                        });
                    } else {
                        $_load_box_clone.css({
                            'position': 'absolute',
                            'width': $(this).outerWidth(),
                            'height': $(this).outerHeight(),
                            'left': offset.left,
                            'top': offset.top
                        });
                    }
                    $(this).attr('loader', id).addClass('blurLoading');
                    $('body').append($_load_box_clone);
                    if (typeof callback == 'function') callback($_load_box_clone)
                }
            })
        }
    })(jQuery)
}