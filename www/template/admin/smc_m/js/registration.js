if(getCookie('lang')=='cn') {
	deneme('join_form_cn.html');
} else {
	deneme('join_form.html');
}

var overlap_check = false;

$(document).ready(function(){

	$('#divId').on('submit', '#joinform', function() {
		var $div = $('<div></div>');
		this.firstname.value = $.trim($div.html(this.firstname.value).text()); // &nbsp; 같은 html entity 제거
		this.lastname.value = $.trim($div.html(this.lastname.value).text());

		var chk_option = [
			{ 'target':'username', 'name':__('member64-1'), 'type':'blank', 'msg':change_lang('member10-1') } // 성+명
			// { 'target':'firstname', 'name':__('member64'), 'type':'blank', 'msg':change_lang('member10') }
			// ,{ 'target':'lastname', 'name':__('member63'), 'type':'blank', 'msg':change_lang('member11') }
		];
		if(getCookie('lang')=='cn') {
			chk_option.push({ 'target':'phone_number', 'name':__('63'), 'type':'blank', 'msg':change_lang('member26') });
		} else {
			chk_option.push({ 'target':'userid', 'name':__('member62'), 'type':'blank', 'msg':change_lang('member12') });
		}
		// chk_option.push({ 'target':'username', 'name':'User Name', 'type':'blank', 'msg':'Enter your user name!' });
		chk_option.push({ 'target':'userpw', 'name':__('bbs12'), 'type':'passwd', 'msg':change_lang('member13') });
		// chk_option.push({ 'target':'userpw_a', 'name':__('bbs12'), 'type':'passwd', 'msg':change_lang('member14') });
		chk_option.push({ 'target':'userpw', 'target2':'userpw_a', 'name':__('bbs12'), 'type':'eq_check', 'msg':change_lang('member15') });

		if(!jsForm(this, chk_option)) {
			return false;
		}

		if($.trim(this.firstname.value) == '') {
			fn_show_toast("warning", __('member10'));
			this.firstname.focus();
			return false;
		}
		if($.trim(this.lastname.value) == '') {
			fn_show_toast("warning", __('member11'));
			this.lastname.focus();
			return false;
		}

		if( getCookie('lang')=='cn') {
			var mobile_country_code = $.trim($("[name=mobile_country_code]:visible").val()),
				mobile_country_number = $("[name=mobile_country_code]:visible option:selected").text().replace(/[^0-9+]/g,''),
				phone_number = $.trim($("[name=phone_number]").val()),
				userid = phone_number!='' ? mobile_country_code + phone_number : '',
				mobile = phone_number!='' ? mobile_country_number + phone_number : ''
			;
			$("input[name=userid]").val(userid);
			$("input[name=mobile]").val(mobile); // 비밀번호 찾기에 필요함. 인증 상태는 아님.
		} else {
			$("input[name=email]").val($.trim($("input[name=userid]").val()));
		}

		if(getCookie('lang')!='cn' && ! validateEmail(this.userid.value) ) {
			$("input[name=userid]").get(0).focus();
			fn_show_toast("warning", __('member12'));
			return false;
		}

		if(!overlap_check) {
			$("input[name=userid]").get(0).focus();
			fn_show_toast("warning", __('member74'));
			return false;
		}
		if(this.userpw.value == '') {
			fn_show_toast("warning", __('member13'));
			this.userpw.focus();
			return false;
		}
		if(this.userpw.value.length < 8) {
			fn_show_toast("warning", __('member17'));
			this.userpw.focus();
			return false;
		}

		if(!confirm(change_lang('member18'))) {
			return false;
		}

		$("input[name=name]").val(this.firstname.value + " " + this.lastname.value);
		$(this).ajaxSubmit({
			success: function (data, statusText) {
				if(data['bool']) {
					location.href = '/certification';
				}
				else {
					if(data['msg'] == 'err_access') {
						fn_show_toast("error", __('member19'));
					}
					else {
						fn_show_toast("error", data['msg'] ? data['msg'] : __('member19'));
					}
				}
			},
			dataType:'json',
			resetForm: false
		});
		return false;
	});

	$('#divId').on('blur', 'input[name=userid], [name=phone_number], [name=mobile_country_code]', function () {
		if( getCookie('lang')=='cn') {
			var mobile_country_code = $.trim($("[name=mobile_country_code]:visible").val()),
				mobile_country_number = $("[name=mobile_country_code]:visible option:selected").text().replace(/[^0-9+]/g,''),
				phone_number = $.trim($("[name=phone_number]").val()),
				userid = phone_number!='' ? mobile_country_code + phone_number : '',
				mobile = phone_number!='' ? mobile_country_number + phone_number : ''
			;
			$("input[name=userid]").val(userid);
			$("input[name=mobile]").val(mobile); // 비밀번호 찾기에 필요함. 인증 상태는 아님.
		}
		overlapCheck();
	});

});

function overlapCheck() {
	var userid = $('input[name=userid]').val();
	if(userid == '') {
		overlap_check = false;
		if(getCookie('lang')=='cn') {
			$('#overlap_msg').html('ID为您的手机号码. 请输入您的手机号');
		} else {
			$('#overlap_msg').html(change_lang('member12'));
		}
		$('#overlap_msg').removeClass('valid').addClass('invalid');
	} else if( getCookie('lang')!='cn' && ! validateEmail(userid) ) {
		overlap_check = false;
		$('#overlap_msg').html(change_lang('member20')).removeClass('valid').addClass('invalid');
	} else  {
		$.get('/member/memberJoin.php?pg_mode=overlap_check&userid='+userid, function(data) {
			if(data['bool']) {
				overlap_check = true;
				$('#overlap_msg').html(change_lang('member21_s')).removeClass('invalid').addClass('valid');
			}
			else {
				overlap_check = false;
				$('#overlap_msg').html(change_lang('member22_s')).removeClass('valid').addClass('invalid');
			}
		},'json');
	}
}

function changeCountry() {
    var result_num = $("[name=mobile_country_code]").val();
    $("input[name=phone_number]").val($("input[name=phone_number]").val().replace(result_num, ""));
}