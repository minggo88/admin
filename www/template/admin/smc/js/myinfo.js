$(function() {
	$('.btn_krwwithdraw_cancel').click(function(){
		var _url = $(this).attr('href');
		$.post(_url, {}, function(r){
			if(r=='success') {
				alert('출금 신청이 취소되었습니다.');
				location.reload();
			} else {
				alert(r);
			}
		});
		return false;
	});
	
	$("table>tbody>tr").hover(
		function () { $(this).css('background-color','#FFF2F0'); }, 
		function () { $(this).css('background-color','#FFF'); }
	);
});

$(function() {
	$(".tooltip").tooltipster({
		   animation: 'fade',
		   delay: 200,
		   theme: 'tooltipster-shadow',
		   touchDevices: true,
		   trigger: 'click'
		});
});

function change_symbol(symbol) {
	location.href = "/histories/"+symbol;
}