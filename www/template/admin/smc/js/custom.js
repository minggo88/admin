
const API_URL = '//api.' + window.location.host.replace('www.', '') + '/v1.0';

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


function getOSInfoStr() {
    var ua = navigator.userAgent;

    if(ua.indexOf("Windows") !== -1) return "N";
    else if(ua.indexOf("Linux") !== -1 && ua.indexOf("Android") === -1) return "N";
    else if(ua.indexOf("Macintosh") !== -1) return "N";
    else if(ua.match(/Android/i)) return "Y";
    else if(ua.match(/BlackBerry/i)) return "Y";
    else if(ua.match(/iPhone|iPad|iPod/i)) return "Y";
    else if(ua.match(/IEMobile/i)) return "Y";
    else return "N";
}


String.prototype.replaceAll = function(org, dest) {
    return this.split(org).join(dest);
}

function deneme(link) {

    $( document ).ready(function(){
        var cookieVal = getCookie('sitecode');
        var flink = '/template/'.concat(cookieVal).concat('/smc/smartforms/Templates/').concat(link);
        var contents;   //내용 html

        $.ajax({
            url: flink,
            context: document.body,
            async: false,
            success: function (response) {
                contents = response;
            }
        });

        //용량이 큰 약관내용은 별도 txt 로 치환
        if(link === "join_clause.html"){

            //이용약관
            $.ajax({
                url: '/lib/language/agree/agree_'+lang+'.txt',
                context: document.body,
                async: false,
                success: function (response) {
                    contents = contents.replaceAll("<!--{terms}-->", response);
                }
            });

            //개인정보수집 동의
            $.ajax({
                url: '/lib/language/agree/privacy_'+lang+'.txt',
                context: document.body,
                async: false,
                success: function (response) {
                    contents = contents.replaceAll("<!--{privacy}-->", response);
                }
            });

        }

        //html 번역내용 치환하기
        //1. html 안에 <!--{ }--> 내용들 찾기
        var str;
        pattern = /(<!--{)(.*?)(}-->)/gi;
        while((matchArray = pattern.exec(contents)) != null) {

            //찾은 내용은 matchArray[0] matchArray[2]
            contents = contents.replaceAll(matchArray[0], change_lang(matchArray[2]) );
        }

        $("#divId").html(contents);

    });
}

function CoinPriceChange(item, i){
    var color = "";
    if( item.price_diff !== undefined ) {

        item.pricePer = item.price_diff_per !== undefined ? item.price_diff_per : '-';
        item.pricePer = (item.price_diff_per !== undefined ? (item.price_diff > 0 ? '+' : '') : '').concat(item.pricePer).concat("%");

        if(parseFloat(item.price_diff) > 0) {
            color = "text-green";
        } else if (parseFloat(item.price_diff) === 0) {
            color = "";
        } else {
            color = "text-red";
        }
    }

    $("#main-coin-price-table").find(".main-coin-price").eq(i).text(item.priceVal);
    $("#main-coin-price-table").find(".main-coin-price").eq(i).addClass(color);

    $("#main-coin-price-table").find(".main-coin-price-per").eq(i).text(item.pricePer);
    $("#main-coin-price-table").find(".main-coin-price-per").eq(i).addClass(color);


};

function makeBanner(list) {
	if(list !== null && list !== undefined) {
		if(list.length > 0) {
			for(var index=0; index<list.length; index++) {
				var item = list[index][0];
				item.priceVal = '-';

                if(index === 0) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(0).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(1).text(item.priceVal);
                } else if (index === 1) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(2).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(3).text(item.priceVal);

                    CoinPriceChange(item, 0);

                    //main mini chart
                    $("#apcPrice").html("<b>"+item.priceVal+"</b>");

                    if(item.price_diff < 0 ){ color = "main-coin-red";plus="▼";head="";}
                    else if(item.price_diff > 0 ){ color = "main-coin-blue";plus="▲";head="+";}
                    else{color = "";plus="";head="";}

                    var new_price_diff = String(item.price_diff).replace('-', '');

                    $(".main-chart-container").find(".main-coin-history").eq(0).html(head+item.price_diff_per+"% "+ plus + new_price_diff );
                    $(".main-chart-container").find(".main-coin-history").eq(0).addClass(color);

                } else if (index === 2) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(4).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(5).text(item.priceVal);
                } else if (index === 3) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(6).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(7).text(item.priceVal);

                    CoinPriceChange(item, 1);

                    //main mini chart
                    $("#btcPrice").html("<b>"+item.priceVal+"</b>");

                    if(item.price_diff < 0 ){ color = "main-coin-red";plus="▼";head="";}
                    else if(item.price_diff > 0 ){ color = "main-coin-blue";plus="▲";head="+";}
                    else{color = "";plus="";head="";}

                    var new_price_diff = String(item.price_diff).replace('-', '');

                    $(".main-chart-container").find(".main-coin-history").eq(1).html(head+item.price_diff_per+"% "+ plus + new_price_diff);
                    $(".main-chart-container").find(".main-coin-history").eq(1).addClass(color);

                } else if (index === 4) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(8).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(9).text(item.priceVal);

                    CoinPriceChange(item, 2);

                } else if (index === 5) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(10).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(11).text(item.priceVal);

                    CoinPriceChange(item, 3);

                } else if (index === 6) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');

                    $("#BannerText").find(".list-item").eq(12).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(13).text(item.priceVal);
                } else if (index === 7) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(14).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(15).text(item.priceVal);

                    CoinPriceChange(item, 4);

                } else if (index === 8) {
                    item.priceVal = item.price_close !== undefined ? item.price_close : '-';
                    item.priceVal = item.priceVal.concat(item.price_diff !== undefined ? (item.price_diff >= 0 ? '↑' : '↓') : '');
                    $("#BannerText").find(".list-item").eq(16).text(item.symbol);
                    $("#BannerText").find(".list-item").eq(17).text(item.priceVal);
                }

			}
        }
	}
}


function sendEmail() {
    $("input[name=emailRegYn]").val("0");
    if ($("input[name=email]").val() === "") {
        fn_show_toast("warning", __('member12'));
        return false;
    }
    var email = $('[name=email]').val();
    if (email) { // 가입한 회원 확인함.
        $("#sendBtn").attr("disabled", true);
        $.ajax({
            type: "POST",
            url: "/join",
            async: false,
            data: { pg_mode: 'send_confirm_email', email: email },
            dataType: 'json',
            success: function (data) {
                if (data['bool']) {
                    $("input[name=emailRegYn]").val("1");
                    $("#sendBtn").attr("disabled", true);

                    $("#emailVerify").fadeIn();
                    //fn_show_toast("success", __('member30'));
                }
                else {
                    if (data['msg'] == 'err_duplicate') {
                        fn_show_toast("error", __('member22'));
                    } else if (data['msg'] == 'err_sms') {
                        fn_show_toast("error", __('basic10'));
                    } else {
                        fn_show_toast("error", __('basic10'));
                    }
                    $("#sendBtn").attr("disabled", false);
                }
            },
            error: function (e) {
                $("#sendBtn").attr("disabled", false);
                console.log("ERROR : " + e);
            }
        });
        if(!email) {
            return false;
        }
    }
}

function corfirmEmailCode() {
    if ($("input[name=confirm_number]").val() === "") {
        fn_show_toast("warning", __('member73'));
        return false;
    }
    var email = $('[name=email]').val();
    var confirm_number = $('[name=confirm_number]').val();
    if(confirm_number) { // 인증코드확인
        $.ajax({
            type: "POST",
            url: "/join",
            async: false,
            data: { pg_mode: 'check_confirm_number', email: email, confirm_number: confirm_number },
            dataType: 'json',
            success: function (data) {
                if (data['bool']) {
                    $("#confirmEmail").hide();
                    $("#emailVerify").hide();

                    $("#confirmedEmail").fadeIn();
                    $('[name=bool_email]').val('1'); // 인증 완료 처리
                    //fn_show_toast("success", __('member30'));
                }
                else {
                    if (data['msg'] == 'err_duplicate') {
                        fn_show_toast("error", __('member22'));
                    } else if (data['msg'] == 'err_confirm_number') {
                        fn_show_toast("error", __('ledger14'));
                    } else {
                        fn_show_toast("error", __('basic10'));
                    }
                }
            },
            error: function (e) {
                console.log("ERROR : " + e);
            }
        });
        if(!email) {
            return false;
        }
    }
}
/*function makeChart(index, arr) {
    $(function() {
		$(".trend-chart").eq(index).sparkline(arr, {
			type: 'line',
			width: '100%',
			height: '40',
			lineWidth: 2,
			lineColor: '#1ab394',
			spotRadius: 3,
			fillColor: "#ffffff"
		});
    });
}*/

/**
 * 토스트 메시지
 * https://codeseven.github.io/toastr/demo.html 에 상세 옵션 있습니다.
 * @param {*} type. success, info, warning, error
 * @param {*} message
 */
function fn_show_toast(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: false,
        showMethod: 'slideDown',
        positionClass: "toast-top-center", //
        timeOut: 4000 // 자동숨김시간. 0으로 설정하면 계속 보입니다.
    };
    if(type === "success") {
        toastr.success(window.location.host, message);
    } else if(type === "info") {
        toastr.info(window.location.host, message);
    } else if(type === "warning") {
        toastr.warning(window.location.host, message);
    } else if(type === "error") {
        toastr.error(window.location.host, message);
    }
}


function kakaoCallback(r) {
    console.log(r);
    if (r && r.id) { // 로그인 성공
        const email = r.kakao_account && r.kakao_account.email ? r.kakao_account.email : '';
        const nickname = r.properties && r.properties.nickname ? r.properties.nickname : '';
        const userpw = md5('kakao'+r.id); // pin은 사용자가 정보수정에서 직접입력해야함. 여기서는 간단한 가입만 시켜줌
        const pin = ''; // pin은 사용자가 정보수정에서 직접입력해야함. 여기서는 간단한 가입만 시켜줌
        const data = { 'social_id': r.id, 'social_name': 'kakao', 'mobile': '', 'name': nickname, 'nickname': nickname, 'email': email, 'userpw': userpw, 'pin': pin };
        console.log(data);
        // 가입시키기 이후 로그인 하기
        $.post(API_URL + '/socialJoin/', data, function (r) { 
            // console.log(r);
            if (r && r.payload && r.payload.token) {
                // 로그인 r.payload.token
                setCookie('token', r.payload.token, 365, '.'+window.location.host);
                window.location.href = '/editpin';
            } else {
                if (r && r.error && r.error.code == '041') { // 이미 가입되어 있음.
                    $.post(API_URL + '/socialLogin/', data, function (r) {
                        // console.log('socialLogin r:', r);
                        setCookie('token', r.payload.token, 365, '.'+window.location.host);
                        window.location.href = '/';
                    });
                }
            }
        })
    }
}


function fbCallback(response) {
    // console.log(response);
    if(response.status === 'connected') {
        fn_show_toast('info',__('member34'));
        if(confirm("You want logout?")) {
            FB.logout(function(response) {
                // console.log(response);
            });
        }
        return false;
    } else {
        FB.login(function(response) {
            if(response.status === 'connected') {
                FB.api('/me?fields=id,name,email,first_name,last_name', function(response) {
                    fn_show_toast('info',__('member35').replace('{email}', response.email));
                });
            }
        }, { scope: 'email', auth_type: 'rerequest' });
    }
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {
        fbCallback(response);
    });
}

function loginGoogle(authResult) {
    if($("input[name=googleYn]").val() === "Y") {
        fn_show_toast('info',__('member34'));
        if(confirm("You want logout?")) {
            window.open("https://accounts.google.com/logout");
            $("input[name=googleYn]").val("N");
        }
    } else {
        if (authResult['access_token']) {
            var googleAuthToken = authResult['access_token'];
            gapi.auth.setToken(authResult); // 반환된 토큰을 저장합니다.
            getEmail();
            // 사용자가 승인되었으므로 로그인 버튼 숨김. 예:
            //document.getElementById('signinButton').setAttribute('style', 'display: none');
        } else if (authResult['error']) {
            // 오류가 발생했습니다.
            // 가능한 오류 코드:
            //   "access_denied" - 사용자가 앱에 대한 액세스 거부
            //   "immediate_failed" - 사용자가 자동으로 로그인할 수 없음
            console.log('오류 발생: ' + authResult['error']);
        }
    }
}

function loginTwitter() {
    window.open('/member/twitter_request_token.php', 'twitterpopup', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
    /*$.ajax({
        url: 'https://api.twitter.com/oauth/access_token',
        method: 'POST',
        success: function (response) {
            console.log(response);
        }
    });*/
}

function getEmail() {
    gapi.client.load('oauth2', 'v2', function() {
        var request = gapi.client.oauth2.userinfo.get();
        request.execute(getEmailCallback);
    });
}

function getEmailCallback(obj) {
    /*for (let field in obj) {
        alert(obj[field]);
    }*/
    //for문으로 값이 뭐가 나오는지 일일이 확인 (id, email, family_name, given_name, name, locale)
    if($("input[name=googleYn]").val() === "N") {
        fn_show_toast('info', __('member36').replace('{email}', obj['email']));
        $("input[name=googleYn]").val("Y");
    }
}

function click(type) {
    if(type === 'google') {
        $("#signinButton").show();
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/client:plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    }
}

/*
function changeMobileCnt() {
	$("#mobileCnt").text("2");
}*/

var menuRight = document.getElementById( 'cbp-spmenu-s2' ),
    body = document.body;

var openYn = false;

$("#hideNav").hide();

if (typeof showRight != typeof undefined) {
    showRight.onclick = function () {
        classie.toggle(this, 'active');
        classie.toggle(menuRight, 'cbp-spmenu-open');
        openYn = true;
        $("#hideNav").show();
        //disableOther( 'showRight' );
    };
}

$("#closeMenu").on('click', function(event) {
    event.preventDefault();
    if(openYn) {
        classie.toggle( menuRight, 'cbp-spmenu-open' );
        $("#hideNav").hide();
    }
});

$("#hideNav").on('click', function(event) {
    event.preventDefault();
    if(openYn) {
        classie.toggle(menuRight, 'cbp-spmenu-open');
        $("#hideNav").hide();
    }
});

$(".nav_menu").find(".page-scroll").on('click', function(event) {
    event.preventDefault();
    if(openYn) {
        classie.toggle(menuRight, 'cbp-spmenu-open');
        $("#hideNav").hide();
        location.href = 'http://' + location.host + $(this).attr("href");
    }
});

$(".nav_menu").find(".subMenu_m_coin").on('click', function(event) {
    event.preventDefault();
    if(openYn) {
        classie.toggle(menuRight, 'cbp-spmenu-open');
        $("#hideNav").hide();
        location.href = 'http://' + location.host + $(this).attr("href");
    }
});

$(".downApp").on('click', function(event) {
    event.preventDefault();
    var ua = navigator.userAgent;

    if(ua.match(/iPhone|iPad|iPod/i)) {
        location.href = "/";
    } else {
        location.href = "/";
    }

});

function chartOpen() {
    $( '.main-chart' ).hover(
        function () {
            $('.react-stockcharts-tooltip-hover').show();
        }, function () {
            $('.react-stockcharts-tooltip-hover').hide();
        }
    );
}

// add trim method to String object
if (!String.prototype.trim) {
    String.prototype.trim = function () {
        return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
    };
}

$('.asc_twitter').click(function (e) {
    e.preventDefault();
});

$('.asc_facebook').click(function (e) {
    e.preventDefault();
});

$(function() {

    $(document).ready(function() {

        $("#main_banner1").hover(function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner1-Over-'+lang+'.png')
        }, function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner1-'+lang+'.png')
        });
        $("#main_banner2").hover(function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner2-Over-'+lang+'.png')
        }, function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner2-'+lang+'.png')
        });
        $("#main_banner3").hover(function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner3-Over-'+lang+'.png')
        }, function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner3-'+lang+'.png')
        });
        $("#main_banner4").hover(function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner4-Over-'+lang+'.png')
        }, function(){
            $(this).attr("src",'/template/admin/smc/img/main/banner4-'+lang+'.png')
        });
    });


    $(document).ready(function() {
        if( getOSInfoStr() === "Y") {
            $(".pd-box2").css("font-size","12px");
        }
        $("#gws_area").hover(function(){
            $(this).find(".gws_image").css("background", "url(/template/admin/trade/img/gws_active.gif)");
        }, function(){
            $(this).find(".gws_image").css("background", "url(/template/admin/trade/img/gws.gif)");
        });
        $("#htc_area").hover(function(){
            $(this).find(".htc_image").css("background", "url(/template/admin/trade/img/htc_active.gif)");
        }, function(){
            $(this).find(".htc_image").css("background", "url(/template/admin/trade/img/htc.gif)");
        });
        $("#bdc_area").hover(function(){
            $(this).find(".bdc_image").css("background", "url(/template/admin/trade/img/bdc_active.gif)");
        }, function(){
            $(this).find(".bdc_image").css("background", "url(/template/admin/trade/img/bdc.gif)");
        });
    });

    $('#userpw').keypress(function(e){
        if(e.which === 13){
            // 이벤트 무시
        }
    });

	// Login Form
    $('#authform').submit(function () {
        var chk_option = [];
        
        // 중국어는 핸드폰번호로 아이디 처리함.
        // if(getCookie('lang')=='cn') {
        //     var mobile_country_code = $.trim($('[name=mobile_country_code]:visible').val()),
        //         phone_number = $.trim($('[name=phone_number]').val());
        //     $('[name=userid]').val(phone_number=='' ? '' : mobile_country_code+phone_number);
        //     chk_option.push({ 'target':'userid', 'name':__('63'), 'type':'blank', 'msg':__('ledger9') });
        // } else {
            chk_option.push({ 'target':'userid', 'name':__('member62'), 'type':'blank', 'msg':__('94') });
        // }
        chk_option.push({ 'target':'userpw', 'name':__('bbs12'), 'type':'blank', 'msg':__('95') });

		if(!jsForm(this,chk_option)) {
			return false;
		}
		$(this).ajaxSubmit({
            success: function (data, statusText) {
                console.log('success login data:', data);
				if(data['bool']) {
					//parent.location.reload();
					location.replace(data['msg']);
				}
				else {
					if(data['msg'] == 'err_access') {
                        fn_show_toast("warning", change_lang('bbs9'));
					}
					else if(data['msg'] == 'err_id') {
                        fn_show_toast("warning", change_lang('member75'));
					}
					else if(data['msg'] == 'err_pw') {
                        fn_show_toast("warning", change_lang('member75'));
					}
					else if(data['msg'] != '') {
                        fn_show_toast("warning", data['msg']);
					}
					else {
                        fn_show_toast("warning", change_lang('member19'));
					}
				}
			},
			dataType:'json',
			resetForm: false
		});
		return false;
    });
    
    $('[name="btn-kakao-login"]').on('click', function () {
        kakaoLogin(kakaoCallback);
    })

});
