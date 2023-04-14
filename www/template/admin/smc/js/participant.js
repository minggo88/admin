$(function() {
	$('#participantform').submit(function() {
		// var chk_option = [
		// 	{ 'target':'name', 'name':'Title', 'type':'blank', 'msg':'성함을 입력해 주세요.' },
		// 	{ 'target':'mobile', 'name':'Mobile', 'type':'digit', 'msg':'휴대폰 번호를 입력해 주세요.' },
		// 	{ 'target':'email', 'name':'E-mail', 'type':'blank', 'msg':'이메일을 입력해 주세요.' }
		// ];
		// if(!jsForm(this,chk_option)) {
		// 	return false;
		// }
		if(!confirm('신청하시겠습니까?')) {
			return false;
		}
		$(this).ajaxSubmit({
			success: function (data, statusText) {
				if(data['bool']) {
					alert('신청 접수 되었습니다.');
					location.replace('/conference');
				}
				else {
					if(data['msg'] == 'err_access') {
            alert('관리자에게 문의 하세요.');
					}
					else if(data['msg'] == 'err_sess') {
            alert('관리자에게 문의 하세요.');
            location.replace('/conference');
					}
					else {
            alert('관리자에게 문의 하세요.');
					}
				}
			},
			dataType:'json',
			resetForm: false
		});
		return false;
	});
});

function del($idx) {
	if(!confirm("삭제 하시겠습니까?")) {
		return false;
	}
	$.get('/conference/del/'+$idx,function(data) {
		if(data['bool']) {
			alert("삭제 되었습니다.");
			location.href = '/conference';
		}
		else { alert("다시 시도 하세요."); }
	},'json');
}