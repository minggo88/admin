/*
 * jQuery Slideshow Plugin v1.3
 * Author: Matt Oakes
 * URL: http://www.matto1990.com/jquery/slideshow/
 *
 * Modifications by Meinhard Benn (http://benn.org/):
 *  - fadetime setting
 */

jQuery.fn.slideshow = function(options) {
	var settings = {
		fadetime: 'slow',
		timeout: '2000',
		type: 'sequence',
		pauselink: null,
		playcallback: null,
		pausecallback: null
	};
	if (options) {
		jQuery.extend(settings, options);
	}
	
	var	pauseState = 0,
		current = 1,
		last = 0,
		timer = '';
	
	var change = function () {
		if ( pauseState == 0 ) {
			for (var i = 0; i < slides.length; i++) {
				jQuery(slides[i]).css('display', 'none');
			}
			jQuery(slides[last]).css('display', 'block').css('zIndex', '0');
			jQuery(slides[current]).css('zIndex', '1').fadeIn(settings.fadetime);
			
			if ( settings.type == 'sequence' ) {
				if ( ( current + 1 ) < slides.length ) {
					current = current + 1;
					last = current - 1;
				}
				else {
					current = 0;
					last = slides.length - 1;
				}
			}
			else if ( settings.type == 'random' ) {
				last = current;
				while (	current == last ) {
					current = Math.floor ( Math.random ( ) * ( slides.length ) );
				}
			}
			else {
				alert('type must either be \'sequence\' or \'random\'');
			}
			timer = setTimeout(change, settings.timeout);
		}
	};
	
	var pause = function() {
		if ( pauseState == 0 ) {
			pauseState = 1;
			clearTimeout(timer);
			if ( settings.playcallback != null ) {
				settings.pausecallback(jQuery('#' + settings.pauselink));
			}
		}
		else {
			pauseState = 0;
			change();
			if ( settings.playcallback != null ) {
				settings.playcallback(jQuery('#' + settings.pauselink));
			}
		}
		return false;
	};
	
	this.css('position', 'relative');
	var slides = this.find('img').get();
	jQuery.each(slides, function(i){
		jQuery(slides[i]).css('zIndex', slides.length - i).css('position', 'absolute').css('top', '0').css('left', '0');
	});
	if ( settings.type == 'sequence' ) {
		timer = setTimeout(change, settings.timeout);
	}
	else if ( settings.type == 'random' ) {
		do {
			current = Math.floor ( Math.random ( ) * ( slides.length ) );
		} while ( current == 0 );
		timer = setTimeout(change, settings.timeout);
	}
	else {
		alert('type must either be \'sequence\' or \'random\'');
	}
	
	if ( settings.pauselink != null ) {
		jQuery('#' + settings.pauselink).click(pause);
	}
	
	return this;
};