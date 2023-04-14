
var api_host = window.location.host;

if (api_host.indexOf("www.") !== -1) {
    api_host = api_host.replace('www.', 'api.');
} else {
    api_host = 'api.' + api_host;
}

var cookieVal = getCookie('token');

var permission = "00000";
var level = 0;
var displayType = $("input[name=displayType]").val();
var bool_confirm_bank = 0;

function getPermission(mode) {
    // permission
    // 0: islogin
    // 1: islogin
    // 2: bool_confirm_mobile
    // 3: bool_confirm_idimage
    // 4: bank_info
    var _p = permission.split('');
    switch(mode) {
        // case 'email': // 미사용.
        //     return _p[1]=='1' ? true: false;
        // break;
        case 'mobile':
            return _p[2]=='1' ? true: false;
        break;
        case 'idimage':
            return _p[3]=='1' ? true: false;
        break;
        case 'bank':
            return _p[4]=='1' ? true: false;
        break;
    }
}

$(document).ready(function() {

    var disable_input = function(){
        $('select').each(function(){
            if($(this).is(':visible')){
                $(this).removeAttr('disabled');
            } else {
                $(this).attr('disabled', true);
            }
        });
    };

    $( window ).resize(function() {
        disable_input();
    });
    disable_input();

    var country_code = "KR";
    var mobile = "";

    $.ajax({
        type: "POST",
        url: "//" + api_host + "/v1.0/getMyInfo/",
        async: false,
        data: {token: cookieVal},
        success: function (data) {
            if (!data.success) {
                fn_show_toast("error", data.error.message);
            }
            if (typeof data != typeof undefined && data.payload) {
                // console.log('data.payload:', data.payload);
                var result = data.payload;
                permission = result.permission;
                country_code = result.mobile_country_code;
                mobile = result.mobile;
                bool_confirm_bank = result.bool_confirm_bank=='0' ? false : true;
            }
        },
        error: function (e) {
            console.log("ERROR : " + e);
        }
    });

    if(country_code == "") {
        country_code = "KR";
    }

    if(permission != null && permission.lastIndexOf("1") != -1) {
		level = permission.indexOf("0")>-1 ? permission.indexOf("0") - 2 : 3; // 11101처럼 중간에 0이 있을수 있어서 0이 처음 있는곳을 찾습니다. 앞에 2개는 이 페이지에서 않쓰기 때문에 제외합니다.
		level = permission.lastIndexOf("1") - 1; // 마지막 1을 찾을때 씁니다. old 버전??
    }
    // if("W" == displayType) {
    //     if(level > 1) {
    //         level = 1;
    //         $("#withdrawal_msg").hide();
    //     }
    // }

    if(level < 0) {
        level = 0;
    }

    // 고액출금자 처리
    if( !bool_confirm_bank) { // getURLParameter('utype') == "bigdaddy" &&
        level = level>2 ? 2 : level;
	}

    $("select[name=mobile_country_code]:visible option").each(function(e) {
        if($(this).val() == country_code) {
            $(this).attr("selected", true);
        }
    });
    var target = $("select[name=mobile_country_code]:visible option:selected").text();
    var result_num = target.substring(target.indexOf("+"), target.length);
    $("input[name=mobile_country_code_origin]").val(result_num);

    let country_calling_code = $('select[name=mobile_country_code]:visible option[value='+country_code+']').text().replace(/[^0-9]/g,'');
    let phone_number = mobile.replace(new RegExp('^'+country_calling_code), '0'); // 국제전화번호 삭제, 지역번호0 추가
    $("input[name=phone_number]").val(phone_number);

    $("input[name=init_idx]").val(level);
    $("input[name=current_idx]").val(level);
    $("input[name=max_idx]").val(level);

    if(level >= 2) {
        // $("#reviewDiv").hide();
    }

	var idx = $("input[name=current_idx]").val();

    $('[name="box_step"]').removeClass('step1 step2 step3 step4').addClass('step'+(idx*1+1));

    if(idx != 0) {
        $("#smart-form-t-"+idx).parent().attr("aria-selected","true");
        $("#smart-form-t-"+idx).parent().addClass("current");

        $("#smart-form-p-"+idx).addClass("current");
        $("#smart-form-p-"+idx).attr("aria-hidden","false");
		$("#smart-form-p-"+idx).show();

        for(var i=0; i<=2; i++) {
            if(i < idx) {
                $("#smart-form-t-"+i).parent().attr("aria-selected","false");
                $("#smart-form-t-"+i).parent().removeClass("current");
                $("#smart-form-t-"+i).parent().addClass("done");
                $("#smart-form-t-"+i).attr("style","cursor: pointer !important");

                $("#smart-form-p-"+i).removeClass("current");
                $("#smart-form-p-"+i).attr("aria-hidden","true");
                $("#smart-form-p-"+i).hide();
            }
		}

		// $('[name="box_step"]').removeClass('step1 step2 step3 step4').addClass('step'+(++idx));

        $("#previousLi").attr("aria-disabled", "false");
        $("#previousLi").attr("aria-hidden", "false");
        $("#previousLi").removeClass("disabled");

        if(idx == 3) {
            $("#nextLi").attr("aria-disabled", "true");
            $("#nextLi").attr("aria-hidden", "true");
            // $("#nextLi").addClass("disabled");
        } else if (idx == 1 && $("input[name=image_identify_url_old]").val() == "" && $("input[name=image_mix_url_old]").val() == "") {
            $("#nextLi").attr("aria-disabled", "true");
            $("#nextLi").attr("aria-hidden", "true");
            $("#nextLi").addClass("disabled");
            $("#saveLi").attr("aria-disabled", "false");
            $("#saveLi").attr("aria-hidden", "false");
            $("#saveLi").removeClass("disabled");
        } else if (idx == 1 && $("input[name=image_identify_url_old]").val() != "" && $("input[name=image_mix_url_old]").val() != "") {
            $("#nextLi").attr("aria-disabled", "false");
            $("#nextLi").attr("aria-hidden", "false");
            $("#nextLi").removeClass("disabled");
            $("#saveLi").attr("aria-disabled", "true");
            $("#saveLi").attr("aria-hidden", "true");
            $("#saveLi").addClass("disabled");
        }

        $("#smart-form-t-"+idx).attr("style","cursor: pointer !important");
        $("#smart-form-t-"+idx).attr("href","#").click(function() {
            back();
        });

		// 마지막에서 다음 단계버튼 삭제
		if(idx>=3) {
			$('#nextLi').hide();
		} else {
            $('#nextLi').show();
        }
    }

    var prevParam = window.location.href.replace(/.*certification\//, '');

    // 특정 스텝으로 이동
    switch(prevParam) {
        case "changeMobile" :$("input[name=current_idx]").val(0);direct(0);break;
        case "changePhoto" :$("input[name=current_idx]").val(0);direct(1);break;
        case "changeBank" :$("input[name=current_idx]").val(0);direct(2);break;
    }

    // 강제로 표시 스텝 정의시
    var _param_step = getURLParameter('step');
    console.log(_param_step, level, bool_confirm_bank);
    if( _param_step && level>3 && getURLParameter('utype') == "bigdaddy" && !bool_confirm_bank) {
        _param_step = 'bank';
    }
    if(_param_step) {
        switch(_param_step) {
            case '1': case 'mobile':
                _param_step=0;
            break;
            case '2': case 'photo':
                _param_step=1;
            break;
            case '3': case 'bank':
                _param_step=2;
            break;
            case '4': case 'complite':
                _param_step=3;
            break;
        }
        $("input[name=current_idx]").val(_param_step);
        direct(_param_step);
    }

    $('[name=btn_pre]').on('click', function(){
        pre();
        return false;
    });
    $('[name=btn_next]').on('click', function(){
        next();
        return false;
    });
    $('[name=btn_certify]').on('click', function(){
        save_certify('R');
        return false;
    });

});

function next() {
    var idx = $("input[name=current_idx]").val();
    var nextIdx = parseInt(idx) + 1;
    var flag = saveProcess(idx);

    if(flag) {
        $("#smart-form-t-"+idx).parent().attr("aria-selected","false");
        $("#smart-form-t-"+idx).parent().removeClass("current");
        $("#smart-form-t-"+idx).parent().addClass("done");
        $("#smart-form-t-"+idx).attr("style","cursor: pointer !important");

        $("#smart-form-p-"+idx).removeClass("current");
        $("#smart-form-p-"+idx).attr("aria-hidden","true");
        $("#smart-form-p-"+idx).hide();

        $("#smart-form-t-"+nextIdx).parent().attr("aria-selected","true");
        $("#smart-form-t-"+nextIdx).parent().addClass("current");

        $("#smart-form-p-"+nextIdx).addClass("current");
        $("#smart-form-p-"+nextIdx).attr("aria-hidden","false");
		$("#smart-form-p-"+nextIdx).show();

		$('[name="box_step"]').removeClass('step1 step2 step3 step4').addClass('step'+(nextIdx+1));

        if(idx == 0) {
            $("#previousLi").attr("aria-disabled", "false");
            $("#previousLi").attr("aria-hidden", "false");
            $("#previousLi").removeClass("disabled");
        }
        if(idx == 2) {
            $("#nextLi").attr("aria-disabled", "true");
            $("#nextLi").attr("aria-hidden", "true");
			// $("#nextLi").addClass("disabled");
			// $("#endLi").removeClass("disabled");
        }
        $("input[name=current_idx]").val(nextIdx);

        var max_idx = $("input[name=max_idx]").val();

        var check_idx = nextIdx > max_idx ? nextIdx : max_idx;

        $("input[name=max_idx]").val(check_idx);

        $("#smart-form-t-"+idx).attr("href","#").click(function() {
            direct(idx);
        });
        $("#smart-form-t-"+check_idx).attr("style","cursor: pointer !important");
        $("#smart-form-t-"+check_idx).attr("href","#").click(function() {
            back();
		});

		set_step_position (nextIdx);
		$( window ).scrollTop( 0 );

		// 마지막에서 다음 단계버튼 삭제
		if(nextIdx>=3) {
			$('#nextLi').hide();
		} else {
            $('#nextLi').show();
        }
    }
}

function set_step_position (idx) {

    // 상단 탭 스타일 변경.
    if(level >= idx) {
        $('div.steps ul[role=tablist] > li').each(function(i){
            $(this).removeClass('current');
            if(i<idx) {
                $(this).addClass('done');
            }
            if(i==idx) {
                $(this).removeClass('done').addClass('current');
            }
        });
    }

}

function pre() {
    var idx = $("input[name=current_idx]").val();
    var preIdx = parseInt(idx) - 1;
    /*$("#smart-form-t-"+preIdx).parent().attr("area-selected","false");
    $("#smart-form-t-"+preIdx).parent().removeClass("done");
    $("#smart-form-t-"+preIdx).parent().addClass("current");*/

    $("#smart-form-p-"+preIdx).addClass("current");
    $("#smart-form-p-"+preIdx).attr("aria-hidden","false");
	$("#smart-form-p-"+preIdx).show();

	$('[name="box_step"]').removeClass('step1 step2 step3 step4').addClass('step'+(preIdx+1));

    /*$("#smart-form-t-"+idx).parent().attr("area-selected","false");
    $("#smart-form-t-"+idx).parent().removeClass("current");*/

    $("#smart-form-p-"+idx).removeClass("current");
    $("#smart-form-p-"+idx).attr("aria-hidden","true");
    $("#smart-form-p-"+idx).hide();
    if(preIdx == 0) {
        $("#previousLi").attr("aria-disabled", "true");
        $("#previousLi").attr("aria-hidden", "true");
        $("#previousLi").addClass("disabled");
        $("#nextLi").attr("aria-disabled", "false");
        $("#nextLi").attr("aria-hidden", "false");
        $("#nextLi").removeClass("disabled");
    }
    if(preIdx == 2) {
        $("#nextLi").attr("aria-disabled", "false");
        $("#nextLi").attr("aria-hidden", "false");
        // $("#nextLi").removeClass("disabled");
    }

    $("#saveLi").attr("aria-disabled", "true");
    $("#saveLi").attr("aria-hidden", "true");
    $("#saveLi").addClass("disabled");

    $("input[name=current_idx]").val(preIdx);
}

function direct(idx) {

    var max_idx = $("input[name=max_idx]").val();
    var current_idx = $("input[name=current_idx]").val();

	$('[name="box_step"]').removeClass('step1 step2 step3 step4').addClass('step'+(idx+1));

    if(max_idx > idx) {
        for(var i=0; i<=3; i++) {
            $("#smart-form-p-"+i).removeClass("current");
            $("#smart-form-p-"+i).attr("aria-hidden","true");
            $("#smart-form-p-"+i).hide();
        }

        if(idx == 0) {
            $("#previousLi").attr("aria-disabled", "true");
            $("#previousLi").attr("aria-hidden", "true");
            $("#previousLi").addClass("disabled");
        } else {
            $("#previousLi").attr("aria-disabled", "false");
            $("#previousLi").attr("aria-hidden", "false");
            $("#previousLi").removeClass("disabled");
        }

        if(idx == 3) {
            $("#nextLi").attr("aria-disabled", "true");
            $("#nextLi").attr("aria-hidden", "true");
            // $("#nextLi").addClass("disabled");
        } else {
            $("#nextLi").attr("aria-disabled", "false");
            $("#nextLi").attr("aria-hidden", "false");
            $("#nextLi").removeClass("disabled");
        }

        $("#saveLi").attr("aria-disabled", "true");
        $("#saveLi").attr("aria-hidden", "true");
        $("#saveLi").addClass("disabled");

        $("#smart-form-p-"+idx).addClass("current");
        $("#smart-form-p-"+idx).attr("aria-hidden","false");
        $("#smart-form-p-"+idx).show();

        $("input[name=current_idx]").val(idx);
    } else if(max_idx == current_idx) {
        if(current_idx == 3) {
            $("#nextLi").attr("aria-disabled", "true");
            $("#nextLi").attr("aria-hidden", "true");
            // $("#nextLi").addClass("disabled");
            $("#saveLi").attr("aria-disabled", "true");
            $("#saveLi").attr("aria-hidden", "true");
            // $("#saveLi").addClass("disabled");
        } else if (current_idx == 1 && $("input[name=image_identify_url_old]").val() == "" && $("input[name=image_mix_url_old]").val() == "") {
            $("#nextLi").attr("aria-disabled", "true");
            $("#nextLi").attr("aria-hidden", "true");
            $("#nextLi").addClass("disabled");
            $("#saveLi").attr("aria-disabled", "false");
            $("#saveLi").attr("aria-hidden", "false");
            $("#saveLi").removeClass("disabled");
        } else {
            $("#nextLi").attr("aria-disabled", "false");
            $("#nextLi").attr("aria-hidden", "false");
            $("#nextLi").removeClass("disabled");
            $("#saveLi").attr("aria-disabled", "true");
            $("#saveLi").attr("aria-hidden", "true");
            $("#saveLi").addClass("disabled");
        }
    }
	// alert(idx);
	// 마지막에서 다음 단계버튼 삭제
	if(idx>=3) {
		$('#nextLi').hide();
	} else {
        $('#nextLi').show();
    }
}

function back() {

    var max_idx = $("input[name=max_idx]").val();
    $("input[name=current_idx]").val(max_idx);
    var current_idx = $("input[name=current_idx]").val();

    for(var i=0; i<=2; i++) {
        if(i < max_idx) {
            $("#smart-form-p-"+i).removeClass("current");
            $("#smart-form-p-"+i).attr("aria-hidden","true");
            $("#smart-form-p-"+i).hide();
        }
    }

    $("#smart-form-p-"+max_idx).addClass("current");
    $("#smart-form-p-"+max_idx).attr("aria-hidden","false");
	$("#smart-form-p-"+max_idx).show();

	$('[name="box_step"]').removeClass('step1 step2 step3 step4').addClass('step'+(++max_idx));

    if(max_idx != 0) {
        $("#previousLi").attr("aria-disabled", "false");
        $("#previousLi").attr("aria-hidden", "false");
        $("#previousLi").removeClass("disabled");
    } else {
        $("#previousLi").attr("aria-disabled", "true");
        $("#previousLi").attr("aria-hidden", "true");
        $("#previousLi").addClass("disabled");
    }

    if(max_idx != 3) {
        $("#nextLi").attr("aria-disabled", "false");
        $("#nextLi").attr("aria-hidden", "false");
        $("#nextLi").removeClass("disabled");
    } else {
        $("#nextLi").attr("aria-disabled", "true");
        $("#nextLi").attr("aria-hidden", "true");
        $("#nextLi").addClass("disabled");
    }

    if (current_idx == 1 && $("input[name=image_identify_url_old]").val() == "" && $("input[name=image_mix_url_old]").val() == "") {
        $("#nextLi").attr("aria-disabled", "true");
        $("#nextLi").attr("aria-hidden", "true");
        $("#nextLi").addClass("disabled");
        $("#saveLi").attr("aria-disabled", "false");
        $("#saveLi").attr("aria-hidden", "false");
        $("#saveLi").removeClass("disabled");
    } else if (current_idx == 1 && $("input[name=image_identify_url_old]").val() != "" && $("input[name=image_mix_url_old]").val() != "") {
        $("#nextLi").attr("aria-disabled", "false");
        $("#nextLi").attr("aria-hidden", "false");
        $("#nextLi").removeClass("disabled");
        $("#saveLi").attr("aria-disabled", "true");
        $("#saveLi").attr("aria-hidden", "true");
        $("#saveLi").addClass("disabled");
    }

    $("#backLi").attr("aria-disabled", "true");
    $("#backLi").attr("aria-hidden", "true");
    $("#backLi").addClass("disabled");

}

function sendNumber() {
    $("input[name=phoneRegYn]").val("0");
    if ($("input[name=phone_number]").val() === "") {
        fn_show_toast("warning", __('member26'));
        return false;
    }
    var userid = $('[name=userid]').val();
    var target = $("select[name=mobile_country_code]:visible option:selected").text();
    var target_val = $("select[name=mobile_country_code]:visible option:selected").val();
    var result_num = target.substring(target.indexOf("+"), target.length);
    var new_val = result_num + $("input[name=phone_number]").val().replace(result_num, "");
    // 전화번호 문자 확인
    if(new_val.match(/[^0-9\-\+]/)) {
        fn_show_toast("warning", __('member51'));return false;
    }
    var mobile_old = $('[name="mobile"]').val();
    var send_confirm_number = true;
    if(mobile_old) { // 이전 전화번호가 있으면 바꿀건지 물어 보기
        same_phone_number = mobile_old.indexOf( new_val.substr(new_val.length<10? 0: new_val.length-10, 10))>-1;
        if(!same_phone_number) { // 가입한 회원으로 로그인 한건지 인지시키기 위해 다른 전화번호를 넣으면 한번 확인함.
            send_confirm_number = confirm(__('인증받은 전화번호와 다른 번호입니다.')+' '+__('휴대폰 인증을 진행하시겠습니까?'));
        }
    }
    if(send_confirm_number) {
        $.ajax({
            type: "POST",
            url: "/edit",
            async: false,
            data: { pg_mode: 'send_confirm_number', phone_number: new_val, phone_country_code: target_val },
            dataType: 'json',
            success: function (data) {
                if (data['bool']) {
                    $("input[name=phoneRegYn]").val("1");
                    $("#sendBtn").attr("disabled", true);

                    if(interval != null) {
                        clearInterval(interval);
                    }

                    var e = (function e() {
                        var nowTime = parseInt((new Date()).getTime()/1000);
                        var nextTime = nowTime + (3 * 60);
                        var checkTime = nowTime + (3 * 60);
                        var now = parseInt((new Date()).getTime()/1000);

                        if(checkTime < now) {
                            return null;
                        } else {
                            var a = new Date(nextTime * 1000);
                            var year = a.getFullYear();
                            var month = (a.getMonth() * 1 + 1);
                            var date = a.getDate();
                            var hour = a.getHours();
                            var min = a.getMinutes();
                            var sec = a.getSeconds();

                            var futureFormattedDate = month + "/" + date + "/" + year + ' ' + hour + ':' + min + ':' + sec;

                            return futureFormattedDate;
                        }
                    });

                    var check = e();

                    if(check != null) {
                        $('.countdown').downCount({
                            date: check,
                            offset: 9
                        }, function () {
                            $("#sendBtn").attr("disabled", false);
                        });
                    }

                    $("#phoneVerify").fadeIn();
                    //fn_show_toast("success", __('member30'));
                }
                else {
                    if (data['msg'] == 'err_duplicate') {
                        fn_show_toast("error", __('member27'));
                    } else if (data['msg'] == 'err_sms') {
                        fn_show_toast("error", __('member28'));
                    } else {
                        fn_show_toast("error", __('basic10'));
                    }
                }
            },
            error: function (e) {
                console.log("ERROR : " + e);
            }
        });
    }
}

function changeCountry() {
    var result_num = $("input[name=mobile_country_code_origin]").val();
    $("input[name=phone_number]").val($("input[name=phone_number]").val().replace(result_num, ""));
}

function saveProcess(idx) {
    var returnFlag = false;
    switch(idx) {
        case "0" :
            returnFlag = save_phone();
            return returnFlag;
        case "1" :
            if($("input[name=image_identify_url_old]").val() == "" || $("input[name=image_mix_url_old]").val() == "") { // 아직 사진을 둘다 안올린경우
                returnFlag = save_certify();
            // } else if(level < 2) { // 사진 아진 미인증 일때
            } else if(! getPermission('idimage')) { // 이전에 인증을 했었으나 반려되서 사진 아진 미인증 일때
                if($.trim($("#imageIdentify").val()) != "" || $.trim($("#imageMix").val()) != "") { // 새로 등록한 수정사진이 있는경우. 저장하시겠습니까? 표시.
                    returnFlag = save_certify("R");
                    returnFlag = true; // 저장후 바로 다음페이지로 이동
                } else if(level >= 2) { // 이전에 인증을 했었다면 다음단계로 넘어갈수 있어야 한다.
                    returnFlag = true;
                } else { // 아직 사진 인증 못했고 했던적도 없는데 새로 등록한 이미지도 없는 경우
                    returnFlag = true; // 승인이 안나도 다음단계로 이동은 할수 있도록 수정 요청 받음.
                    // fn_show_toast("warning", __('97')); //신분증 사진을 심사중입니다.
                }
            } else {// 승인 완료시 다음페이지로 이동. - 은행정보 확인해야 함.
                // returnFlag = true;
            }
            return returnFlag;
        case "2" :
            var _need_save = false, returnFlag = true, _waite_confirm_bank = false,
                _bank_name_old = $.trim($("input[name=bank_name_old]").val()), _bank_name = $.trim($("input[name=bank_name]").val()),
                _bank_account_old = $.trim($("input[name=bank_account_old]").val()), _bank_account = $.trim($("input[name=bank_account]").val()),
                _bank_owner_old = $.trim($("input[name=bank_owner_old]").val()), _bank_owner = $.trim($("input[name=bank_owner]").val()),
                _image_bank_url_old = $.trim($("input[name=image_bank_url_old]").val()), _imageBank = $.trim($("#imageBank").val())
                ;
            // 은행명
            if(!_need_save && _bank_name_old == "") { _need_save = true; }
            if(!_need_save && _bank_name_old != "" && _bank_name_old!=_bank_name) { _need_save = true; }
            if(!_need_save && _bank_account_old == "") { _need_save = true; }
            if(!_need_save && _bank_account_old != "" && _bank_account_old!=_bank_account) { _need_save = true; }
            if(!_need_save && _bank_owner_old == "") { _need_save = true; }
            if(!_need_save && _bank_owner_old != "" && _bank_owner_old!=_bank_owner) { _need_save = true; }
			if(!_need_save && _imageBank!='') { _need_save = true; }
			// alert (_need_save);
			// alert (bool_confirm_bank);
            // if(!_need_save &&  getURLParameter('utype') == "bigdaddy" && !bool_confirm_bank) {  // 은행통장 이미지 미승인인 빅대대는 //  // 빅대디 확인 안함 모두 인증 필요함.
            if(!bool_confirm_bank) {  // 빅대디 확인 안함 모두 인증 필요함.
                _waite_confirm_bank = true; // 고액출금자는 은행 인증 않되면 다음 단계로 이동은 못해야 함.
                if(_image_bank_url_old=='') {
                    _need_save = true; // 이미지 선택하세요는 저장하는 로직에서 채크함.
                } else {
                    if(_imageBank!='') { // 이미지 수정시 저장해야 함.
                        _need_save = true;
                    }
                }
            }
            if(_need_save) {
                returnFlag = save_account();
                if(! returnFlag) { return false; } // 저장 오류시 종료.
			}
			// alert(_waite_confirm_bank);
            if(_waite_confirm_bank) {
                // fn_show_toast("warning", __('97_2')); // 심사중입니다로 변경
                return false;
            }
            return returnFlag;
        default :
            break;
    }
}

function readURL(input, index) {
    if (input.files) {
        var $imagePreview = $('#imagePreview'+index);
        if (input.files.length > 0) {
            var fileName = input.files[0].name;
            var fileExt = getExtensionOfFilename(fileName);
            if(fileExt !== 'png' && fileExt !== 'jpg' && fileExt !== 'jpeg') {
                fn_show_toast("warning", __('member33'));
                return false;
            } else {
                var currentFile = input.files[0]
                var options = {
                    maxWidth: $imagePreview.width(),
                    canvas: true,
                    pixelRatio: window.devicePixelRatio,
                    downsamplingRatio: 1,
                    orientation: true
                }
                if (!loadImage(currentFile, function(img, data){
                    if (img.src || img instanceof HTMLCanvasElement) {
                        $imagePreview.css('background-image', 'url(' + img.toDataURL(currentFile.type + 'REMOVEME') + ')').hide().fadeIn(650);
                    }
                }, options)) {
                    // $imagePreview.children().replaceWith($('<span>Your browser does not support the URL or FileReader API.</span>'))
                }
                return true;
            }
        } else { // 선택된 파일이 없는경우 기본 이미지로 표시. (파일 선택을 취소하면 발생)
            $imagePreview.css('background-image', 'none').find('.imageEmpty').show().hide().fadeIn(650);
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

$("#imageBank").change(function() {
    var bool = readURL(this,3);
    if(bool) {
        $(".imageEmpty").eq(2).hide();
    }
});
$('[name=delete_img]').click(function(){
    var target_preview = $(this).attr('data-target-preview'),
        target_file = $(this).attr('data-target-file'),
        image_url = $(this).attr('data-image-url');
    $('#'+target_preview).css('background-image', 'none').find('.imageEmpty').show().hide().fadeIn(650);
    $('#'+target_file).val('');
    if(image_url){
        $.post('/edit', {pg_mode: 'delete_img', imgurl: image_url }, function(){});
    }
    return false;
});

// 전화번호
function save_phone() {
    var returnFlag = false;
    if ($("input[name=phone_number]").val() == "") {
        fn_show_toast("warning", __('member26'));
        returnFlag = false;
        return false;
    }
    var idx = $("input[name=init_idx]").val();
    if(idx > 0) {
        if($("input[name=phoneRegYn]").val() == '0') {
            returnFlag = true;
        } else {
            if ($("input[name=confirm_number]").val() == "") {
                fn_show_toast("warning", __('member29'));
                returnFlag = false;
                return false;
            }
            if (confirm(__('bbs5'))) {
                $.ajax({
                    type: "POST",
                    url: "/edit",
                    async: false,
                    data: {pg_mode: 'confirm_number', confirm_number: $("input[name=confirm_number]").val()},
                    dataType: 'json',
                    success: function (data) {
                        if (data['bool']) {
                            fn_show_toast("success", __('member30'));
                            returnFlag = true;
                        }
                        else {
                            if (data['msg'] !== '') {
                                fn_show_toast("error", data['msg']);
                                returnFlag = false;
                            }
                            else {
                                fn_show_toast("error", __('basic10'));
                                returnFlag = false;
                            }
                        }
                    },
                    error: function (e) {
                        console.log("ERROR : " + e);
                    }
                });
            }
        }
    } else {
        if($("input[name=phoneRegYn]").val() == '0') {
            fn_show_toast("warning", __('member31'));
            returnFlag = false;
            return false;
        }
        if ($("input[name=confirm_number]").val() == "") {
            fn_show_toast("warning", __('member29'));
            returnFlag = false;
            return false;
        }
        if (confirm(__('bbs5'))) {
            $.ajax({
                type: "POST",
                url: "/edit",
                async: false,
                data: {pg_mode: 'confirm_number', confirm_number: $("input[name=confirm_number]").val()},
                dataType: 'json',
                success: function (data) {
                    if (data['bool']) {
                        fn_show_toast("success", __('member30'));
                        returnFlag = true;
                    }
                    else {
                        if (data['msg'] !== '') {
                            fn_show_toast("error", data['msg']);
                            returnFlag = false;
                        }
                        else {
                            fn_show_toast("error", __('basic10'));
                            returnFlag = false;
                        }
                    }
                },
                error: function (e) {
                    console.log("ERROR : " + e);
                }
            });
        }
    }
    return returnFlag;
}

function save_certify(type) {
    var returnFlag = false;
    if ($("input[name=image_identify_url_old]").val() == "" && $("#imageIdentify").val() === "") {
        fn_show_toast("warning", __('96'));
        returnFlag = false;
        return false;
    }

    if ($("input[name=image_mix_url_old]").val() == "" && $("#imageMix").val() === "") {
        fn_show_toast("warning", __('member32'));
        returnFlag = false;
        return false;
    }
    if (confirm(__('bbs5'))) {

        $("input[name=token]").val(cookieVal);

        var data = new FormData($('#smart-form')[0]);

        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "//" + api_host + "/v1.0/upload/",
            async: false,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                if (!data.success) {
                    fn_show_toast("error", data.error.message);
                }
                if (typeof data != typeof undefined && data.payload) {
                    var result = data.payload;
                    var identifyVal = $("#imageIdentify").val().split("\\")[2];
                    var mixVal = $("#imageMix").val().split("\\")[2];

                    if (typeof(result) == 'object') {
                        for (var i = 0; i < result.length; i++) {
                            if (result[i].name == identifyVal) {
                                $("input[name=image_identify_url_new]").val(result[i].url);
                            }
                            if (result[i].name == mixVal) {
                                $("input[name=image_mix_url_new]").val(result[i].url);
                            }
                        }
                    }
                    returnFlag = submitData('certify');
                    if(returnFlag) {
                        let image_mix_url = $("input[name=image_mix_url_new]").val();
                        let image_identify_url = $("input[name=image_identify_url_new]").val();
                        $("input[name=image_mix_url_new]").val('');
                        $("input[name=image_mix_url_old]").val(image_mix_url);
                        $("input[name=image_identify_url_new]").val('');
                        $("input[name=image_identify_url_old]").val(image_identify_url);
                        $("#imageIdentify").val('');
                        $("#imageMix").val('');
                    }
					// location.reload(); // 신분증 사진 저장후 페이지 새로고침 하지 않고 다음으로 바로 넘어가기.

                }
            },
            error: function (e) {
                console.log("ERROR : " + e);
                returnFlag = false;
            }
        });
    }
    return returnFlag;
}

// 은행이미지
function save_account() {
	var returnFlag = false;

    // 고액출금자는 통장앞면이미지 업로드 필요함.(필수) 일반 사용자는 필수 아님.
    if ( getURLParameter('utype') == "bigdaddy" && !bool_confirm_bank && $("input[name=image_bank_url_old]").val() == "" && $("#imageBank").val() === "") {
        fn_show_toast("warning", __('137'));
        return false;
    }

    if (confirm(__('bbs5'))) {

        // 이미지 업로드
        if($("#imageBank").val()!='') {
            var result_image_upload = false;
            // 전송용 폼데이터 생성
            var data = new FormData();
            data.append("token", getCookie('token'));
            data.append("file_data[]", $("#imageBank")[0].files[0]);
            $.ajax({
                type: "POST",
                enctype: "multipart/form-data",
                url: "//" + api_host + "/v1.0/upload/",
                async: false,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success: function (data) {
                    if (!data.success) {
                        fn_show_toast("error", data.error.message);
                    }
                    if (typeof data != typeof undefined && data.payload) {
                        var result = data.payload;
                        var imageBank = $("#imageBank").val().split("\\")[2];
                        if (typeof(result) == 'object') {
                            for (var i = 0; i < result.length; i++) {
                                if (result[i].name == imageBank) {
                                    $("input[name=image_bank_url_new]").val(result[i].url);
                                    result_image_upload = true;
                                }
                            }
                        }
                    }
                },
                error: function (e) {
                    console.log("ERROR : " + e);
                }
            });
            if(! result_image_upload){
                return false;
            }
        }

        $.ajax({
            type: "POST",
            url: "/edit",
            async: false,
            data: {
                pg_mode: 'save_bank_info',
                bank_name: $("input[name=bank_name]").val(),
                bank_account: $("input[name=bank_account]").val(),
                bank_owner: $("input[name=bank_owner]").val(),
                image_bank_url: $("input[name=image_bank_url_new]").val()
            },
            dataType: 'json',
            success: function (data) {
                if (data['bool']) {
					fn_show_toast("success", __('member30'));
					// setTimeout(function(){window.location.reload();}, 1600); // 은행 정보 저장후 페이지 새로고침 안함. 왜했지??
                    if ( getURLParameter('utype') == "bigdaddy" && !bool_confirm_bank) {
                        returnFlag = false;
                    } else {
                        returnFlag = true;
                    }
                }
                else {
                    if (data['msg'] !== '') {
                        fn_show_toast("error", data['msg']);
                        returnFlag = false;
                    }
                    else {
                        fn_show_toast("error", __('basic10'));
                        returnFlag = false;
                    }
				}
            },
            error: function (e) {
                console.log("ERROR : " + e);
            }
        });
    }
    return returnFlag;
}

function save_photo() {
    if (confirm(__('bbs5'))) {
        if ($("#imageIdentify").val() === "") {
            fn_show_toast("warning", __('96'));
            return false;
        }

        if ($("#imageMix").val() === "") {
            fn_show_toast("warning", __('member32'));
            return false;
        }

        if ($("#imageBank").val() === "") {
            fn_show_toast("warning", __('member32'));
            return false;
        }

        var cookieVal = getCookie('token');

        $("input[name=token]").val(cookieVal);

        var data = new FormData($('#smart-form')[0]);

        var api_host = window.location.host;

        if (api_host.indexOf("www.") !== -1) {
            api_host = api_host.replace('www.', 'api.');
        } else {
            api_host = 'api.' + api_host;
        }

        $.ajax({
            type: "POST",
            enctype: "multipart/form-data",
            url: "//" + api_host + "/v1.0/upload/",
            async: false,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                if (!data.success) {
                    fn_show_toast("error", data.error.message);
                }
                if (typeof data != typeof undefined && data.payload) {
                    var result = data.payload;
                    var identifyVal = $("#imageIdentify").val().split("\\")[2];
                    var mixVal = $("#imageMix").val().split("\\")[2];
                    var bankVal = $("#imageBank").val().split("\\")[2];

                    if (typeof(result) == 'object') {
                        for (var i = 0; i < result.length; i++) {
                            if (result[i].name == identifyVal) {
                                $("input[name=image_identify_url_new]").val(result[i].url);
                            }
                            if (result[i].name == mixVal) {
                                $("input[name=image_mix_url_new]").val(result[i].url);
                            }
                            if (result[i].name == bankVal) {
                                $("input[name=image_bank_url_new]").val(result[i].url);
                            }
                        }
                    }
                    submitData('withdrawal');
                }
            },
            error: function (e) {
                console.log("ERROR : " + e);
            }
        });
    }
}

// 신분증
function submitData(type) {
    $.ajax({
        type: "POST",
        url: "/edit",
        async: false,
        data: {pg_mode: 'edit_photo',
                userid: $("input[name=userid]").val(),
                bool_passwd: 0,
                image_identify_url_new: $("input[name=image_identify_url_new]").val(),
                image_identify_url_old: $("input[name=image_identify_url_old]").val(),
                image_mix_url_new: $("input[name=image_mix_url_new]").val(),
                image_mix_url_old: $("input[name=image_mix_url_old]").val() },
        dataType: 'json',
        success: function (data) {
            if (data['bool']) {
                if(type == 'withdrawal') {
                    fn_show_toast("success", __('member30'));
                    location.href = location.pathname;
                } else {
                    fn_show_toast("success", __('member30'));
                    return true;
                }
            }
            else {
                if (data['msg'] !== '') {
                    fn_show_toast("error", data['msg']);
                }
                else {
                    fn_show_toast("error", __('basic10'));
                }
            }
        },
        error: function (e) {
            console.log("ERROR : " + e);
        }
    });
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