$(function () {
	$('#searchform').submit(function() {
		if($(':checkbox:checked',this).length == 0) {
			alert('검색조건을 선택하여주세요.!');
			return false;
		}
		if(this.s_val.value == '') {
			alert('검색어를 입력하여 주세요.!');
			return false;
		}
    });
    
	$('select[name=category]').change(function() {
		var select_val = $(this).val();
		location.href = '/bbs/list/<!--{srch_url}-->&category='+select_val;
	});
	$('select[name=category]').val('<!--{_GET.category}-->');

});