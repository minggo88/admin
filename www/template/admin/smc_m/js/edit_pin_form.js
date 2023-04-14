$(function() {
	$('#frmEdit').submit(function() {
		var chk_option = [
			{ 'target':'pin', 'name':__('member61'), 'type':'blank', 'msg':__('member54') },
			{ 'target':'pin_b', 'name':__('member61'), 'type':'blank', 'msg':__('member55') }
		];
		if(!jsForm(this, chk_option)) {
			return false;
		}
		if(this.pin.value.length < 4 || this.pin.value.length > 6 || this.pin.value.search(/\D/)>-1) {
            fn_show_toast("warning", __('member53'));
			this.pin.focus();
			return false;
		}
		if(this.pin.value != this.pin_b.value) {
            fn_show_toast("warning", __('member60'));
			this.pin_b.focus();
			return false;
		}
		if(!confirm(__('member56'))) {
			return false;
		}

		let form = this;
		$(this).ajaxSubmit({
			success: function (data, statusText) {
				if(data['bool']) {
                    fn_show_toast("success", __('member57'));
					form.reset();
				}
				else {
					if(data['msg'] == 'err_access') {
                        fn_show_toast("error", change_lang('member19'));
					}
					else if(data['msg'] == 'err_sess') {
						location.replace('/login/<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
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

//	$('select[name=phone_a]').val('<!--{phone_a}-->');

});

function return_num(obj, index) {
    $(function() {
        var value = $(obj).val();
        var re = /[^0-9]/g;
        if(value.match(re)) {
			if(index === 1) {
                $("#validateLabel").show();
                $("#validateLabel").text(__('member53'));
			} else {
                $("#validateLabel_b").show();
                $("#validateLabel_b").text(__('member53'));
			}
        } else {
            if(index === 1) {
                $("#validateLabel").hide();
            } else {
                $("#validateLabel_b").hide();
            }
		}
        var convertVal = value.trim().replace(/[^0-9]/g, '');
        $(obj).val(convertVal);
    });
}