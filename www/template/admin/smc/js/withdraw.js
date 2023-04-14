$(function () {
	$('#withdrawform').submit(function () {
		var chk_option = [
			{ 'target': 'contents', 'name': __('member85'), 'type': 'blank', 'msg': __('member89') }
		];
		if (!jsForm(this, chk_option)) {
			return false;
		}
		if (!confirm(__('member88'))) {
			return false;
		}
		$(this).ajaxSubmit({
			success: function (data, statusText) {
				if (data['bool']) {
					fn_show_toast("success", __('member86'));
					location.replace('/index.php');
				}
				else {
					if (data['msg'] == 'err_access') {
						fn_show_toast("error", change_lang('member19'));
					}
					else if (data['msg'] == 'err_sess') {
						location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
					}
					else if (data['msg'] == 'err_userid') {
						location.replace('/index.php');
					}
					else {
						fn_show_toast("error", change_lang('member19') + ' ' + data['msg']);
					}
				}
			},
			dataType: 'json',
			resetForm: false
		});
		return false;
	});
});
