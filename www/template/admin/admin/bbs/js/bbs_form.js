
$(function() {
	$('#bbsform').submit(function() {
		//myeditor.outputBodyHTML();
		var chk_option = [
			{ 'target':'author', 'name':'이름', 'type':'blank', 'msg':'작성자 이름을 입력하세요.!' },
			{ 'target':'passwd', 'name':'비밀번호', 'type':'blank', 'msg':'비밀번호를 입력하세요.!' },
			{ 'target':'subject_kr', 'name':'제목', 'type':'blank', 'msg':'글 제목(한국어)을 입력하세요.!' },
			{ 'target':'subject_en', 'name':'제목', 'type':'blank', 'msg':'글 제목(영어)을 입력하세요.!' },
			{ 'target':'subject_cn', 'name':'제목', 'type':'blank', 'msg':'글 제목(중국어)을 입력하세요.!' }
		];

		if(!jsForm(this,chk_option)) {
			return false;
		}

		if(!confirm('저장하시겠습니까?')) {
			return false;
		}
		
		$(this).ajaxSubmit({
			success: function (data, statusText) {
				console.log(data);
				if(data['bool']) {
					alert('저장되었습니다.!');
					location.href='<!--{_SERVER.SCRIPT_NAME}-->?pg_mode=list<!--{srch_url}-->';
				}
				else {
					if(data['msg'] == 'err_access') {
						alert('비정상적인 접근입니다.');
					}
					else if(data['msg'] == 'err_sess') {
						location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
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

$(function() {
	$('#btn_cancel').click(function() {
		history.go(-1);
	});

	$('select#bbscode').val('<!--{_GET.bbscode}-->');
});