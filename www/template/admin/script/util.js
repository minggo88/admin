/*--------------------------------------------
Date : 2011-07-12
Author : Danny Hwang
Comment : 자바스크립트 기본 라이브러리
--------------------------------------------*/

// JavaScript Document

/*     GNB_네비게이션     */
/*$(document).ready(function(){

	var sub_nav_boxWideh = $('.sub_nav_box').width() * 2;
	$('.sub_nav_box').css('width',sub_nav_boxWideh + "px");

});*/


/*
*/
var globalMenuOnOff = 'off';

function globalMenu(menu,mode)
{
	if(mode == 'open')
	{
		globalMenuOnOff = 'on';

		$('.sub_nav_box').hide();
		/*$('#sub_'+menu).show();*/
		$('#sub_'+menu).slideDown('fast');

		for(var i=0;i<$('.menuImg').length;i++)
		{
			$('.menuImg').eq(i).attr('src',$('.menuImg').eq(i).attr('src').replace('_on.png','.png'));
		}

		$('#'+menu).find('.menuImg').attr('src',$('#'+menu).find('.menuImg').attr('src').replace('.png','_on.png'));

		$('#sub_'+menu).bind('mouseleave',function(){
			setTimeout("globalMenu('"+menu+"','close');",500);
		});
	}
	else if(mode == 'close' && globalMenuOnOff == 'off')
	{
		$('#sub_'+menu).hide();
		$('#'+menu).find('.menuImg').attr('src',$('#'+menu).find('.menuImg').attr('src').replace('_on.png','.png'));
	}
}

$(document).ready(function(){
	$('.m_nav .globalMenu').hover(
		function(){
			globalMenu($(this).attr('id'),'open');
		},
		function(){
			globalMenuOnOff = 'off';
			setTimeout("globalMenu('"+$(this).attr('id')+"','close');",500);
		}
	);

	$('.sub_nav_box').hover(
		function(){
			globalMenuOnOff = 'on';
		},
		function(){
			globalMenuOnOff = 'off';
		}
	);

	$('.sub_nav_box .sub_nav li').hover(
		function(){
			$(this).addClass('on');
		},
		function(){
			$(this).removeClass('on');
		}
	);

	/*$('.sub_nav_box .sub_nav li img').hover(
		function(){
			$(this).attr('src',this.src.replace('.gif','_on.gif'));
		},
		function(){
			$(this).attr('src',this.src.replace('_on.gif','.gif'));
		}
	);*/


});

/*     GNB_네비게이션     */


//바이트검사
function Byte(input)
{
    var i, j=0;
    for(i=0;i<input.length;i++) {
        val=escape(input.charAt(i)).length;
        if(val==  6) j++;
        j++;
    }
    return j;
}

//위치변경
function winsize(w,h,l,t)
{
    if(window.opener) resizeTo(w,h);
}

// 해상도에 맞는 크기 사용
function screenSize()
{
    self.moveTo(0,0);
    self.resizeTo(screen.availWidth,screen.availHeight);
}

//콤마찍기 -  소수점에도 콤마가 들어가는 오류 수정.
function addComma(x) {
  var parts = x.toString().split(".");
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  return parts.join(".");
}

//콤마제거
function delComma(num) {
	num = ''+ num;
	return (num.replace(/\,/g,""));
}


//문자열에서 숫자만 가져가기
function getNum(str)
{
    var val = str;
    var temp = "";
    var num = "";

    for(i=0; i<val.length; i++) {
        temp = val.charAt(i);
        if(temp >= "0" && temp <= "9") num += temp;
    }
    return num;
}

//새창띄우기
function openWin(url,target,w,h,s,t,l)
{
    if(s) { s = 'yes'; }
    else { s = 'no'; }
    var its = window.open(url,target,'width='+w+',height='+h+',top='+t+',left='+l+',scrollbars='+s);
    its.focus();
}

//삭제확인
function confirmGo(msg,url)
{
	if(confirm(msg)) {
		location.href=url;
	}
	else {
		return;
	}
}

function alertHref(msg,url)
{
	alert(msg);
	location.href=url;
}

function alertReplace(msg,url)
{
	alert(msg);
	location.replace(url);
}

function frameGo(url,target)
{
	eval('window.'+target+'.location.replace('+url+')');
}

function cal_byte(str){
	var tmpStr;
	var temp=0;
	var onechar;
	var tcount;
	tcount = 0;

	tmpStr = new String(str);
	temp = tmpStr.length;

	for (k=0;k<temp;k++) {
		onechar = tmpStr.charAt(k);
		if (escape(onechar) =='%0D') { } else if (escape(onechar).length > 4) { tcount += 2; } else { tcount++; }
	}

	document.getElementById("smsByte").innerText = tcount + " Bytes";
}


function bookMark(url,str){
   window.external.AddFavorite(url,str)
}

function onlyNumber( Ev ) {
    if (window.event) { var code = window.event.keyCode;	}
    else { var code = Ev.which; }

    if ((code > 34 && code < 41) || (code > 47 && code < 58) || (code > 95 && code < 106) || code == 8 || code == 9 || code == 13 || code == 46) {
        window.event.returnValue = true;
        return;
    }
    if (window.event) { window.event.returnValue = false; }
    else { Ev.preventDefault(); }
}

// input 박스 내용삭제
function clickclear(thisfield, defaulttext)
{
    if (thisfield.value == defaulttext) {
        thisfield.value = "";
    }
}

// input 박스 내용복원
function clickrecall(thisfield, defaulttext)
{
    if (thisfield.value == "") {
        thisfield.value = defaulttext;
    }
}

// 파일 확장자 추출
function getExtensionOfFilename(filename) {
	let _fileLen = filename.length;
	let _lastDot = filename.lastIndexOf('.');

	return filename.substring(_lastDot+1, _fileLen).toLowerCase();
}

/**
 * 위로 한줄씩 올라가면서 밑에서 다른 내용이 나오는 스크립트.
 * 사용방법. beautiful_notice('#footer_notice');
 * @param selector target_selector jQuery에서 사용하는 selector
 * @returns {undefined}
 */
function beautiful_notice(target_selector) {
	var _rolling_time = 5000;
	var _event_time = 1200;
	var $this = $(target_selector);
	var $target_li = $this.children();
	var $this_height = $this.height();
	var $this_width = $this.width();
	var current = 0;
	var total = $target_li.length - 1;
	var set_interval;
	$this.parent().height($this.parent().height()).css({'position':'relative','overflow':'hidden'});
	$target_li.css({'top': $this_height,'position':'absolute'});
	$target_li.eq(0).addClass('on').css({'top': 0}).fadeIn(_event_time);
	function click_left() {
		$target_li.not(':eq(' + current + ')').css('top', $this_height);
		if (current == total) {
			current = 0;
			$target_li.eq(current).animate({top: 0}, _event_time);
			$target_li.eq(total).animate({top: -$this_height}, _event_time, function() {
				$target_li.not(':eq(' + current + ')').css('top', $this_height);
			});
		} else {
			$target_li.eq(current).animate({top: -$this_height}, _event_time);
			$target_li.eq(current + 1).animate({top: 0}, _event_time, function() {
				$target_li.not(':eq(' + current + ')').css('top', $this_height);
			});
			current++;
		}
	}
	function interval_on() {
		set_interval = setInterval(function() {
			click_left();
		}, _rolling_time);
	}
	interval_on();
	function keyword_off() {
		clearInterval(set_interval);
	}
	$this.on('mouseenter', function() {
		keyword_off();
	});
	$this.on('mouseleave', function() {
		interval_on();
	});
}

/**
 * validateEmail
 *
 * @param {String} email
 * @returns {Boolean} true: validate, false: un format email.
 */
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

/**
 * Return an object with the selection range or cursor position (if both have the same value)
 * @param {DOMElement} el A dom element of a textarea or input text.
 * @return {Object} reference Object with 2 properties (start and end) with the identifier of the location of the cursor and selected text.
 **/
function getInputSelection(el) {
    var start = 0, end = 0, normalizedValue, range, textInputRange, len, endRange;

    if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
        start = el.selectionStart;
        end = el.selectionEnd;
    } else {
        range = document.selection.createRange();

        if (range && range.parentElement() == el) {
            len = el.value.length;
            normalizedValue = el.value.replace(/\r\n/g, "\n");

            // Create a working TextRange that lives only in the input
            textInputRange = el.createTextRange();
            textInputRange.moveToBookmark(range.getBookmark());

            // Check if the start and end of the selection are at the very end
            // of the input, since moveStart/moveEnd doesn't return what we want
            // in those cases
            endRange = el.createTextRange();
            endRange.collapse(false);

            if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                start = end = len;
            } else {
                start = -textInputRange.moveStart("character", -len);
                start += normalizedValue.slice(0, start).split("\n").length - 1;

                if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                    end = len;
                } else {
                    end = -textInputRange.moveEnd("character", -len);
                    end += normalizedValue.slice(0, end).split("\n").length - 1;
                }
            }
        }
    }

    return {
        start: start,
        end: end
    };
}

// Credits: http://blog.vishalon.net/index.php/javascript-getting-and-setting-caret-position-in-textarea/
function setCaretPosition(ctrl, pos) {
  // Modern browsers
  if (ctrl.setSelectionRange) {
    ctrl.focus();
    ctrl.setSelectionRange(pos, pos);

  // IE8 and below
  } else if (ctrl.createTextRange) {
    var range = ctrl.createTextRange();
    range.collapse(true);
    range.moveEnd('character', pos);
    range.moveStart('character', pos);
    range.select();
  }
}

/**
 * get url parameter value
 * @param String key Parameter Name
 */
function getURLParameter(key) {
	let url = new URL(window.location.href);
	return url.searchParams.get(key)??'';
}

/**
 * get url parameter value
 *
 * @param String key 파라메터 이름
 * @param String val 파라메터 값
 * 
 * @return String URL
 * 
 */
function setURLParameter(key, val) {
	let url = new URL(window.location.href);
	url.searchParams.set(key, val);
	return url.href;
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
function setCookie(name, value, expiredays, domain){
	var todayDate = new Date();
	todayDate.setDate( todayDate.getDate() + expiredays );
	domain = domain ? 'domain='+domain : '';
	document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" + domain
}

// php date 함수와 같은 방식으로 상용합니다.. https://php.net/manual/en/function.date.php
function date(n,t){var e,r,u=["Sun","Mon","Tues","Wednes","Thurs","Fri","Satur","January","February","March","April","May","June","July","August","September","October","November","December"],o=/\\?(.?)/gi,i=function(n,t){return r[n]?r[n]():t},c=function(n,t){for(n=String(n);n.length<t;)n="0"+n;return n};r={d:function(){return c(r.j(),2)},D:function(){return r.l().slice(0,3)},j:function(){return e.getDate()},l:function(){return u[r.w()]+"day"},N:function(){return r.w()||7},S:function(){var n=r.j(),t=n%10;return t<=3&&1===parseInt(n%100/10,10)&&(t=0),["st","nd","rd"][t-1]||"th"},w:function(){return e.getDay()},z:function(){var n=new Date(r.Y(),r.n()-1,r.j()),t=new Date(r.Y(),0,1);return Math.round((n-t)/864e5)},W:function(){var n=new Date(r.Y(),r.n()-1,r.j()-r.N()+3),t=new Date(n.getFullYear(),0,4);return c(1+Math.round((n-t)/864e5/7),2)},F:function(){return u[6+r.n()]},m:function(){return c(r.n(),2)},M:function(){return r.F().slice(0,3)},n:function(){return e.getMonth()+1},t:function(){return new Date(r.Y(),r.n(),0).getDate()},L:function(){var n=r.Y();return n%4==0&n%100!=0|n%400==0},o:function(){var n=r.n(),t=r.W();return r.Y()+(12===n&&t<9?1:1===n&&t>9?-1:0)},Y:function(){return e.getFullYear()},y:function(){return r.Y().toString().slice(-2)},a:function(){return e.getHours()>11?"pm":"am"},A:function(){return r.a().toUpperCase()},B:function(){var n=3600*e.getUTCHours(),t=60*e.getUTCMinutes(),r=e.getUTCSeconds();return c(Math.floor((n+t+r+3600)/86.4)%1e3,3)},g:function(){return r.G()%12||12},G:function(){return e.getHours()},h:function(){return c(r.g(),2)},H:function(){return c(r.G(),2)},i:function(){return c(e.getMinutes(),2)},s:function(){return c(e.getSeconds(),2)},u:function(){return c(1e3*e.getMilliseconds(),6)},e:function(){throw new Error("Not supported (see source code of date() for timezone on how to add support)")},I:function(){return new Date(r.Y(),0)-Date.UTC(r.Y(),0)!=new Date(r.Y(),6)-Date.UTC(r.Y(),6)?1:0},O:function(){var n=e.getTimezoneOffset(),t=Math.abs(n);return(n>0?"-":"+")+c(100*Math.floor(t/60)+t%60,4)},P:function(){var n=r.O();return n.substr(0,3)+":"+n.substr(3,2)},T:function(){return"UTC"},Z:function(){return 60*-e.getTimezoneOffset()},c:function(){return"Y-m-d\\TH:i:sP".replace(o,i)},r:function(){return"D, d M Y H:i:s O".replace(o,i)},U:function(){return e/1e3|0}};return function(n,t){return e=void 0===t?new Date:t instanceof Date?new Date(t):new Date(1e3*t),n.replace(o,i)}(n,t)}


function real_number_format(n, d){
	if(typeof n==typeof undefined || n=='' || is_null(n) || n=='NaN' ){n='0';}
	var sign = n<0 ? '-':'';
	if(d) { n = number_format(n, d); }
	n = n+'';
	n = n.replace(/[^0-9.]/g,'');
	var r = n.split('.');
	r[0] = r[0].length==1 ? r[0] : r[0].replace(/^0+/g,'');// 숫자얖 0 제거
	if(1000 <= n) { r[0] = number_format(r[0]); }// 콤마추가
	r[1] = r[1] ? r[1].replace(/0{1,}$/g, '') : '';
	if(r[1] && r[1].length>0) {
		r = r.join('.');
	} else {
		r = r[0];
	}
	return sign + r;
}


function upload($input_file, storage_type, callback) {
	callback = callback ? callback : function () { };
	storage_type = storage_type ? storage_type : 'aws_s3';
	if ($.trim($input_file.val()) != '') {
		$form = $('<form><input type="hidden" name="token" value="' + getCookie('token') + '"><input type="hidden" name="storage_type" value="' + storage_type + '"></form>');
		$form.attr('enctype', 'multipart/form-data');
		$input_file.after($input_file.clone(true)).attr('name', 'file_data');
		$form.append($input_file);
		var formData = new FormData($form[0]);
		// formData.append("token", getCookie('token'));
		// console.log('//' + (window.location.host.replace('admin.', 'api.')) + '/v1.0/upload/?'); return false;
		$.ajax({
			'url': '//'+(window.location.host.replace('admin.','api.'))+'/v1.0/upload/?',
			'async': false,
			'processData': false,
			'contentType': false,
			'data': formData,
			'type': 'POST',
			'success': function(r) {
				if (r && r.success && r.payload) {
					let file = r.payload[0];
					if (file.url) {
						callback(file.url, '');
					} else {
						callback(false, '');
						// alert(__('{item_name} 파일을 저장하지 못했습니다.').replace('{item_name}', $item_name));
					}
				} else {
					let msg = r.error && r.error.message ? r.error.message : '';
					callback(false, msg);
					// alert(__('{item_name} 파일을 저장하지 못했습니다.').replace('{item_name}', $item_name) + ' ' + msg)
				}
			},
			'fail': function () {
				callback(false, '');
				// alert(__('{item_name} 파일을 업로드하지 못했습니다.').replace('{item_name}', $item_name));
			}
		});
	}
}


/**
 * 숨김/노출 스위치 HTML 생성
 *
 * @param String type data-type 속성 값
 * @param String value data-value 속성 값. Y 값이면 checked 속성 추가됩니다.
 * @param String idx data-idx 속성 값
 * @param String str_on On 상태에서 표시할 글자. 기본 '노출'
 * @param String str_off Off 상태에서 표시할 글자. 기본 '숨김'
 * 
 * @return String 
 * 
 */
function render_switch(type, value, idx, str_on, str_off) {
	str_on = str_on ? str_on : '노출';
	str_off = str_off ? str_off : '숨김';
	type = type ? type : '';
	value = value ? value : '';
	idx = idx ? idx : '';
	return '<input type="checkbox" ' + (value == 'Y' ? 'checked' : '') + ' data-toggle="toggle" data-on="'+str_on+'" data-off="'+str_off+'" data-size="small" data-type="' + type + '" data-value="' + value + '" data-idx="' + idx + '"> ';
	
};

/**
 * 버큰 HTML 생성
 *
 * @param mixed btn_name 버튼 태그의 name 속성 값. 
 * @param mixed btn_idx 버튼 태그의 data-idx 값.
 * @param mixed btn_str 버튼 태그의 표시글
 * @param mixed btn_type 버튼의 스타일 종류. btn-default: 기본 버튼용 색(흰색 또는 회색), btn-warning: 경고 버튼용 색(주황색), btn-danger: 경고 버튼용 색(빨강), btn-success: 파랑, btn-primary: 초록, btn-link : 태두리 없이 글자만 보이는 버튼. http://office_5f.smart-coin.co.kr/1.inspinia_v2.7.1/Static_Full_Version/buttons.html 
 * @param mixed btn_size 버튼 사이즈. btn-lg, 기본(미지정), btn-sm, btn-xs. http://office_5f.smart-coin.co.kr/1.inspinia_v2.7.1/Static_Full_Version/buttons.html 
 * 
 * @return [type]
 * @에제 render_button('btn-delete-goods', '1234', '상품 삭제', 'btn-danger', 'btn-sm') '<a href="#" data-idx="1234" class="btn btn-sm btn-danger" name="btn-delete-goods">상품 삭제</a>'
 * 
 */
function render_button(btn_name, btn_idx, btn_str, btn_type, btn_size, link_url) {
	btn_name = btn_name ? btn_name : '';
	if (!btn_name) { console.error('[render_button] btn_name 값이 필요합니다.'); }
	btn_idx = btn_idx ? btn_idx : '';
	if (!btn_idx) { console.error('[render_button] btn_idx 값이 필요합니다.'); }
	btn_str = btn_str ? btn_str : '버튼';
	if (!btn_str) { console.error('[render_button] btn_str 값이 필요합니다.'); }
	btn_type = btn_type ? btn_type : 'btn-default';
	btn_size = btn_size ? btn_size : 'btn-sm';
	link_url = link_url ? ' onClick="window.location.href=\''+link_url+'\'"' : '';
	return '<button data-idx="'+btn_idx+'" class="btn '+btn_size+' '+btn_type+'" name="'+btn_name+'" '+link_url+'>'+btn_str+'</button> ';
};