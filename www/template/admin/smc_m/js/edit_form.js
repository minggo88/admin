//deneme('edit_form.html');

window.name ="Parent_window";
function fnPopup(){
	window.open('', 'popupChk', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
	document.form_chk.action = "https://nice.checkplus.co.kr/CheckPlusSafeModel/checkplus.cb";
	document.form_chk.target = "popupChk";
	document.form_chk.submit();
}
function fnSendPhone(){
	if($('#mobile_a').val()=='' || $('#mobile_b').val()=='' || $('#mobile_c').val()==''){
        fn_show_toast("warning", __('member51'));
		return false;
	}
	var new_phone_number = $('#mobile_a').val() +'-'+ $('#mobile_b').val() +'-'+ $('#mobile_c').val();
	_new_phone_number = new_phone_number.replace(/[^0-9]/g, '');
	var confirm_mobile = $('#confirm_mobile').val().replace(/[^0-9]/g, '');
	if(confirm_mobile == _new_phone_number) {
        fn_show_toast("warning", __('member45'));
	} else {
		$.post('/member/memberEdit.php', {'pg_mode':'send_confirm_number', 'phone_number':new_phone_number}, function(r){
			$('#mobile_a, #mobile_b, #mobile_c').attr('readonly', true);
			if( typeof r!=typeof undefined && r.bool ) {
                fn_show_toast("warning", __('member46'));
				$('#box_write_confirm_number').show();
				window.new_phone_number = new_phone_number;
			}
		},'json');
	}
	return false;
}
function fnConfirmPhone(){
	var new_phone_number = ($('#mobile_a').val() + $('#mobile_b').val() + $('#mobile_c').val()).replace(/[^0-9]/g, '');
	if( window.new_phone_number.replace(/[^0-9]/g, '') != new_phone_number ) {
        fn_show_toast("warning", __('member47'));
		return false;
	}
	var confirm_number = $('#confirm_sms').val();
	$.post('/member/memberEdit.php', {'pg_mode':'confirm_number', 'confirm_number':confirm_number}, function(r){
		if( typeof r!=typeof undefined && r.bool ) {
            fn_show_toast("success", __('member48'));
			$('#confirm_mobile').val(window.new_phone_number);
			$('#box_write_confirm_number').hide();
			$('#btn_send_confirm_number').hide();
			$('#btn_confirmed_mobile').show();
			$('#mobile_a, #mobile_b, #mobile_c').attr('readonly', false);
		}
	},'json');
	return false;
}
function getCookie(c_name) {
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++)
    {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x === c_name) {
            return unescape(y);
        }
    }
}
$(function() {
	$('#frmEdit').submit(function() {

		/*if(!$("input[name=bool_realname]").val()) {
			if($("#imageIdentify").val() === "") {
				fn_show_toast("warning", __('96'));
				return false;
			}

			if($("#imageMix").val() === "") {
				fn_show_toast("warning", __('member32'));
				return false;
			}
        }*/

		var chk_option = [
//			{ 'target':'zipcode', 'name':'우편번호', 'type':'digit', 'msg':'우편번호는 필수 입력사항입니다.' },
//			{ 'target':'address_a', 'name':'주소', 'type':'blank', 'msg':'주소는 필수 입력사항입니다.' },
//			{ 'target':'address_b', 'name':'상세주소', 'type':'blank', 'msg':'상세주소는 필수 입력사항입니다.' },
//			{ 'target':'phone_a', 'name':'전화번호', 'type':'select', 'msg':'지역번호를 선택하여 주세요.!' },
//			{ 'target':'phone_b', 'name':'전화번호', 'type':'digit', 'msg':'전화국번을 입력하여 주세요.!' },
//			{ 'target':'phone_c', 'name':'전화번호', 'type':'digit', 'msg':'전화번호를 입력하여 주세요.!' },
//			{ 'target':'mobile_a', 'name':'휴대전화번호', 'type':'select', 'msg':'휴대전화번호를 선택하여 주세요.!' },
//			{ 'target':'mobile_b', 'name':'휴대전화번호', 'type':'digit', 'msg':'휴대전화번호를 입력하여 주세요.!' },
//			{ 'target':'mobile_c', 'name':'휴대전화번호', 'type':'digit', 'msg':'휴대전화번호를 입력하여 주세요.!' }
//			{ 'target':'email_a', 'name':'이메일', 'type':'blank', 'msg':'이메일을 입력하여 주세요.!' },
//			{ 'target':'email_b', 'name':'이메일', 'type':'blank', 'msg':'이메일 도메인을 입력하여 주세요.!' }
            { 'target':'name', 'name':'name', 'type':'blank', 'msg': __('member49') },
//            { 'target':'mobile', 'name':'mobile', 'type':'blank', 'msg':'Please enter your mobile phone number.' },
//            { 'target':'birthday', 'name':'birthday', 'type':'blank', 'msg':'Please enter your birthday.' }
		];
		if(!jsForm(this, chk_option)) {
			return false;
		}
		/*if($("input:radio[name=gender]:checked").val() === undefined) {
            fn_show_toast("warning", 'Please check your gender.');
			return false;
		}*/

		if(this.securimagecode.value === '') {
			alert(__('member50'));
			this.securimagecode.focus();
			return false;
		}
		if(this.bool_passwd.checked) {
			if(this.userpw.value === '') {
                fn_show_toast("warning", __('member13'));
				this.bool_passwd.focus();
				return false;
			}
			if(this.userpw.value.length < 8) {
                fn_show_toast("warning", __('member17'));
				this.bool_passwd.focus();
				return false;
			}
		}
//		if( $('#btn_send_confirm_number').is(':visible') ) {
//			alert('새로운 핸드폰 번호의 인증을 진행해주세요.');
//			return false;
//		}

        var cookieVal = getCookie('token');

        $("input[name=token]").val(cookieVal);

		if(!confirm(__('member52'))) {
			return false;
		}
        this.mobile.value = this.mobile.value.trim().replace(/[^0-9]/g, '');

        /*var data = new FormData($("#frmEdit")[0]);

        var api_host = window.location.host;

        if(api_host.indexOf("www.") !== -1) {
            api_host = api_host.replace('www.','api.');
        } else {
            api_host = 'api.'+api_host;
        }

        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "//"+api_host+"/v1.0/upload/",
            async: false,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function(data) {
                if(typeof data != typeof undefined && data.payload) {
                    var result = data.payload;
                    var identifyVal = $("#imageIdentify").val().split("\\")[2];
                    var mixVal = $("#imageMix").val().split("\\")[2];

                    if(typeof(result) == 'object') {
                        for(var i=0; i<result.length; i++) {
                            if(result[i].name == identifyVal) {
                                $("input[name=image_identify_url_new]").val(result[i].url);
                            }
                            if(result[i].name == mixVal) {
                                $("input[name=image_mix_url_new]").val(result[i].url);
                            }
                        }
                    }
                }
            },
            error: function(e) {
                console.log("ERROR : " + e);
            }
		});*/

		$(this).ajaxSubmit({
			success: function (data, statusText) {
				if(data['bool']) {
                    fn_show_toast("success", __('member30'));
					$('#userpw').val('');$('[name="bool_passwd"]').attr('checked',false); // 비번 초기화
                    RefreshCaptcha();$('#securimagecode').val(''); // 캡챠 초기화
				}
				else {
					if(data['msg'] == 'err_access') {
                        fn_show_toast("error", __('basic9'));
					}
					else if(data['msg'] == 'err_sess') {
						location.replace('/member/memberAuth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
					}
                    else if(data['msg'] !== '') {
                        fn_show_toast("error", data['msg']);
                    }
					else {
                        fn_show_toast("error", __('basic10'));
					}
				}
			},
			dataType:'json',
			resetForm: false
		});
		return false;
	});

	$('select[name=phone_a]').val('<!--{phone_a}-->');
	$('select[name=mobile_a]').val('<!--{mobile_a}-->');

//	$('#email_domain').change(function() {
//		var select_idx = this.selectedIndex;
//		var domain = $(this).val();
//		if(select_idx > 0) {
//			if(domain == 'etc') {
//				$('#email_b').val('');
//			}
//			else {
//				$('#email_b').val(domain);
//			}
//		}
//	});
	// 핸드폰 번호가 바뀌면 인증하라고 한다.
//	var check_confirm_mobile = function(){
//		var confirm_mobile = $('#confirm_mobile').val().replace(/[^0-9]/g, '');
//		var new_phone_number = ($('#mobile_a').val() + $('#mobile_b').val() + $('#mobile_c').val()).replace(/[^0-9]/g, '');
//		if(confirm_mobile != new_phone_number) {
//			$('#btn_send_confirm_number').show();
//			$('#btn_confirmed_mobile').hide();
//			$('#bool_confirm_mobile').val(0);
//		} else {
//			$('#btn_send_confirm_number').hide();
//			$('#btn_confirmed_mobile').show();
//			$('#bool_confirm_mobile').val(1);
//		}
//	};
//	$('#mobile_a, #mobile_b, #mobile_c').keyup(check_confirm_mobile).change(check_confirm_mobile);
//	$('#btn_send_confirm_number').click(fnSendPhone);
//	$('#btn_confirm_mobile').click(fnConfirmPhone);
});

function return_num(obj) {
    $(function() {
        var value = $(obj).val();
        var convertVal = value.trim().replace(/[^0-9-]/g, '');
        $(obj).val(convertVal);
    });
}

function readURL(input, index) {
    if (input.files && input.files[0] !== '') {
    	var fileName = input.files[0].name;
    	var fileExt = getExtensionOfFilename(fileName);

    	if(fileExt !== 'png' && fileExt !== 'jpg' && fileExt !== 'jpeg') {
            fn_show_toast("warning", __('member33'));
            return false;
		} else {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#imagePreview'+index).css('background-image', 'url('+e.target.result +')');
				$('#imagePreview'+index).hide();
				$('#imagePreview'+index).fadeIn(650);
			};
			reader.readAsDataURL(input.files[0]);
            return true;
        }
    }
}
$("#imageIdentify").change(function() {
    var bool = readURL(this,1);
    if(bool) {
    	$(".imageEmpty").eq(0).hide();
    }
});

$("#imageMix").change(function() {
    var bool = readURL(this,2);
    if(bool) {
        $(".imageEmpty").eq(1).hide();
    }
});

/*
document.addEventListener("DOMContentLoaded", function() {
    document.body.scrollTop; //force css repaint to ensure cssom is ready

    var timeout; //global timout variable that holds reference to timer

    var captcha = new $.Captcha({
        onFailure: function() {
            $(".captcha-chat .correct").hide();
            $(".captcha-chat .wrong").show({
                duration: 30,
                done: function() {
                    var that = this;
                    clearTimeout(timeout);
                    $(this).removeClass("shake");
                    $(this).css("animation");
                    //Browser Reflow(repaint?): hacky way to ensure removal of css properties after removeclass
                    $(this).addClass("shake");
                    var time = parseFloat($(this).css("animation-duration")) * 1000;
                    timeout = setTimeout(function() {
                        $(that).removeClass("shake");
                    }, time);
                }
            });
            $("input[name=chapchaFlag]").val("N");
        },

        onSuccess: function() {
            $(".captcha-chat .wrong").hide();
            $(".captcha-chat .correct").show();
            $("input[name=chapchaFlag]").val("Y");
        }
    });

    captcha.generate();
});*/
