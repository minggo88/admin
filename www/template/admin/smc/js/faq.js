function searchCancel() {
	location.replace('/faq');
}

function searchCheck() {
	if(!$('#s_val').val()) {
		alert('Enter Search word');
		return;
	}
	else {
        var s_val = encodeURIComponent($('#s_val').val());
        var faqcode = $('#faqcode').val();
        var category = $('#category').val();
		location.href = '/faq/list/'+faqcode+'/'+category+'/'+s_val;
	}
}

function viewFaq(faqcode) {
	faqcode = faqcode || '';
	var s_val = encodeURIComponent($('#s_val').val());
    location.href = '/faq/list/'+faqcode;
}

function showAll() {
	var hidden = $('.faq>.faqBody>.article.hide').length;
	var article = $('ul.faqBody li.article');
	if(hidden > 0){
		article.removeClass('hide').addClass('show');
		article.find('.a').slideDown(100);
	} else {
		article.removeClass('show').addClass('hide');
		article.find('.a').slideUp(100);
	}
}

$(function() {
	// Frequently Asked Question
	var article = $('.faq>.faqBody>.article');
	article.addClass('hide');
	article.find('.a').hide();
	article.eq(0).removeClass('hide');
	article.eq(0).find('.a').show();
	$('.faq>.faqBody>.article>.q>a').click(function(){
		var myArticle = $(this).parents('.article:first');
		if(myArticle.hasClass('hide')){
			article.addClass('hide').removeClass('show');
			article.find('.a').slideUp(100);
			myArticle.removeClass('hide').addClass('show');
			myArticle.find('.a').slideDown(100);
		} else {
			myArticle.removeClass('show').addClass('hide');
			myArticle.find('.a').slideUp(100);
		}
		return false;
	});
});