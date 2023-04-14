$(function() {
	$('#spoform').submit(function() {
		var chk_option = [
			{ 'target':'subject', 'name':'Title', 'type':'blank', 'msg':change_lang('bbs4') }
		];
		if(!jsForm(this,chk_option)) {
			return false;
		}
		if(!confirm(change_lang('bbs5'))) {
			return false;
		}
		$(this).ajaxSubmit({
			success: function (data, statusText) {
				if(data['bool']) {
                    fn_show_toast("success", change_lang('bbs6'));
					location.replace('/spo');
				}
				else {
					if(data['msg'] == 'err_access') {
                        fn_show_toast("error", change_lang('basic9'));
					}
					else if(data['msg'] == 'err_sess') {
						location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
					}
					else {
                        fn_show_toast("error", change_lang('member19'));
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
	if(!confirm(change_lang('bbs7'))) {
		return false;
	}
	$.get('/spo/del/'+$idx,function(data) {
		if(data['bool']) {
			alert(change_lang('bbs8'));
			location.href = '/spo';
		}
		else { alert(change_lang('member19')); }
	},'json');
}