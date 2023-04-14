// 우측 클릭 방지, 선택 방지, 드래그 방지
$('body').on('contextmenu selectstart dragstart', function() { return false; });
document.onmousedown = disableclick;

function disableclick(event) {
    if (event.button == 2) {
        return false;
    }
}

String.prototype.trim = function() {
	var str = this;
	return this.replace(/^(\s|\u00A0)+|(\s|\u00A0)+$/g, '');
}

String.prototype.toNumber = Number.prototype.toNumber = function() {
	return this ? (this+'').replace(/[^0-9.\-]/, '')*1 : 0;
}

String.prototype.format = function(args1, args2, args3, args4, args5) {
	var arguments = new Array();
	if(args1) arguments[0] = args1;
	if(args2) arguments[1] = args2;
	if(args3) arguments[2] = args3;
	if(args4) arguments[3] = args4;
	if(args5) arguments[4] = args5;

    var formatted = this;
    for (var arg in arguments) {
        formatted = formatted.replace("{" + arg + "}", arguments[arg]);
    }
    return formatted;
}

function setCookie(name, value, expiredays){
	var todayDate = new Date();
	todayDate.setDate( todayDate.getDate() + expiredays );
	document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
}

function getCookie( name ){
	var nameOfCookie = name + "=";
	var x = 0;
	while(x <= document.cookie.length) {
		var y = (x+nameOfCookie.length);
		if(document.cookie.substring( x, y ) == nameOfCookie ){
		if((endOfCookie=document.cookie.indexOf( ";", y )) == -1)
			endOfCookie = document.cookie.length;
			var r = unescape( document.cookie.substring( y, endOfCookie ) );
			return (r=="undefined") ? '' : r;
		}
		x = document.cookie.indexOf( " ", x ) + 1;
		if(x == 0) break;
	}
	return "";
}

function htmlencode(str) {
	return str.replace(/[&<>"']/g, function($0) {
		return "&" + {"&":"amp", "<":"lt", ">":"gt", '"':"quot", "'":"#39"}[$0] + ";";
	});
}
/**
 * get url parameter value
 * @param String sParam Parameter Name
 */
function getURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i=0; i < sURLVariables.length; i++){
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam){
            return sParameterName[1] ? sParameterName[1] : '';
        }
	}
	return '';
}

function real_number_format(n, d){
	if(typeof n==typeof undefined || n=='' || is_null(n) || is_nan(n) ){n='0';}
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

remove_array_by_value = function(array, value) {
    var what, a = arguments, L = a.length, ax;
    while (L && array.length) {
        what = a[--L];
        while ((ax = array.indexOf(what)) !== -1) {
            array.splice(ax, 1);
        }
    }
    return array;
};

function get_keycode(evt) {
    return evt.which ? evt.which : window.event.keyCode;
}

function get_str_by_keycode(keycode) {
    let char = '';
    if (window.event.which == null)
        char = String.fromCharCode(event.keyCode); // old IE
    else
        char = String.fromCharCode(window.event.which); // All others
    return char;
}

/**
 * INPUT 객체에 keydown 이벤트 발생시 숫자만 입력할 수 있도록 하는 필터링함수.
 * 숫자와 커서이동에 필요한 화살표, 탭, Del, Backspace 키등만 허용되고 모두 필터링.
 * @param {window.event}} evt
 * @example $('#login form input[type=password]').on('keydown', input_filter_number)
 */
function input_filter_number(evt) {
    let keyCode = evt.which ? evt.which : event.keyCode,
        val = String.fromCharCode(keyCode);
        // console.log(keyCode, val);
    if (val.match(/[^0-9]/g) && keyCode != 8 && keyCode != 9 && keyCode != 46 && keyCode != 35 && keyCode != 36 && keyCode != 37 && keyCode != 38 && keyCode != 39 && keyCode != 40 && keyCode != 96 && keyCode != 97 && keyCode != 98 && keyCode != 99 && keyCode != 100 && keyCode != 101 && keyCode != 102 && keyCode != 103 && keyCode != 104 && keyCode != 105 && keyCode != 48 && keyCode != 49 && keyCode != 50 && keyCode != 51 && keyCode != 52 && keyCode != 53 && keyCode != 54 && keyCode != 55 && keyCode != 56 && keyCode != 57 && keyCode != 190 && keyCode != 110) {
        return false;
    }
}

$(document).on("keyup", ".onlynum", function(ev) { $(this).val($(this).val().replace(/[^0-9.,\-]/g, "")); }).on('keydown', ".onlynum", input_filter_number);
$(document).on("blur change", ".realnumber", function(ev) { $(this).val(real_number_format($(this).val())); })
$(document).on("keyup", ".onlyeng", function(ev) { $(this).val($(this).val().replace(/[^\!-z]/g, "")); });

$('input[type=number]').on('change', function(){
    let min = $(this).attr('data-min'), max = $(this).attr('data-max'), val = $(this).val().toNumber();
    if(min && val<min) {val = min};
    if(max && val<min) {val = max};
    $(this).val(val);
});

// i18n.js ----------------------------------------------------------------------------
;(function() {
    const support_lang = ['en', 'cn', 'kr'],
        default_lang = 'kr';
    var lang_data = {},
        lang = navigator.language || navigator.userLanguage,
        cookielang = getCookie('lang');
    lang = lang.substr(0, 2);
    // lang = in_array(lang, support_lang) ? lang : default_lang; // 브라우저 언어 설정값을 기준으로 첫번째 언어를 선택하도록 할때
    lang = default_lang; // 브라우저 언어 설정에 상관없이 처음 언어 지정할때 사용.
    lang = cookielang && cookielang !== lang && in_array(cookielang, support_lang) ? cookielang : lang;

    if (cookielang !== lang) {
        setCookie('lang', lang, 365);
    }
    if (window.lang !== lang) {
        window.lang = lang;
    }

    const get_lang_data = function(callback) {
        let cache_time = Math.ceil(((new Date().getTime()) / 1000) / (60 * 60 * 1));
        let data_file = '/lib/language/langcache/i18n_exc_' + lang + '.cache.js?v=' + cache_time;
        httpRequest = new XMLHttpRequest();
        if (httpRequest) {
            httpRequest.onreadystatechange = function() {
                if (httpRequest.readyState === XMLHttpRequest.DONE) {
                    if (httpRequest.status === 200) {
                        lang_data = JSON.parse(httpRequest.responseText);
                    } else {
                        console.error('번역 데이터 가져오지 못함.');
                    }
                    if (callback) { callback(); }
                }
            };
            httpRequest.open('GET', data_file);
            httpRequest.send();
        }
    }

    const get_key_string = function(key) {
        key = key+'';
        return key.match(/[^0-9a-zA-Z_]/) ? md5(key) :  key.replace(/\W/,'').replace(/\s{1,}/,'_');
    }

    get_lang_data(); // i18n 미사용
    /**
     * get translate string
     * @param {string} key
     */
    window.__ = function(s) {
        key = get_key_string(s);
        // console.log(s, key, lang_data[key], lang_data);
        return lang_data && lang_data[key] ? lang_data[key] : s;
    };
    /**
     * echo translate string
     * @param {string} key
     */
    window._e = function(key) {
        document.write(__(key));
    };
    /**
     * change language
     * @param {string} l language code
     * @param {function} callback callback function
     */
    window._c = function(l, callback) {
        if (!in_array(l, support_lang)) { l = default_lang; }
        if (l != lang) {
            lang = l;
            // setCookie('lang', l, 365);
            Model.lang = l;
            get_lang_data(callback);
            // window.location.reload();
        }
    }
})();

// app
;(jQuery(function($){

    const API_URL = window.location.href.indexOf('loc.')>-1 ? 'http://api.trade.loc.kmcse.com/v1.0' : 'https://api.trade.kmcse.com/v1.0';
    const TOKEN = getCookie('token');
    let user_info = {};
    let user_wallet = [];
    let exc_price = {'GWS':[], 'BDC':[]};
    let exchange_info = {'GWS_buyalbe':0,'BDC_buyalbe':0, 'price':100};

    /**
     * API Request 객체
     */
    var items = [],
        callbacks = [],
        timeoutkeys = [],
        request_warkable = true;
    const request_api = function() {
        setTimeout(request_api, 300); //key_request =
        // console.log('----------request_api --------------------');
        if (request_warkable && items && items.length > 0) {
            // console.log('---------------> request_api working');
            request_warkable = false;
            var _items = items,
                _callbacks = callbacks;
            items = [];
            callbacks = [];
            let form = { 'item': _items };
            form.token = TOKEN;
            jQuery.post(API_URL + '/request/?', form, function(r) {
                // console.log('---------------> response:', r);
                request_warkable = true;
                for (var i in r.payload) {
                    const tr = r.payload[i];
                    const response = JSON.parse(tr.data);
                    if (response.error && response.error.code === '001') {
                        // console.log('---------------> 다시 로그인 하기');
                        // alert(__('다시 로그인 해주세요.'));
                        // fn_logout();
                        // return false;
                    }
                    if (_callbacks[i]) { _callbacks[i](response, i); }
                }
            }, 'json');
        }
        return false;
    };
    request_api();

    const add_request_item = function(method_name, params, callback, repeat_time, old_path_name, duplicate) {
        var curr_path_name = window.location.pathname,
            old_path_name = old_path_name ? old_path_name : curr_path_name;
        if (repeat_time > 0 && old_path_name !== curr_path_name) { // 이전에 얘약걸어 둔 작업이 페이지가 다르면 종료합니다.(path_name으로 확인해 전체 경로를 비교합니다.)
            return false;
        }
        if (TOKEN) { params.token = TOKEN; }
        const item = { "method": method_name, "params": params };
        if (!duplicate) {
            for (var i in items) {
                if (JSON.stringify(items[i]) == JSON.stringify(item)) {
                    return; // 중복시 추가 종료.
                }
            }
        }
        items.push(item);
        let indexno = items.length - 1;
        callbacks[indexno] = callback;
        if (repeat_time > 0 && old_path_name === curr_path_name) {
            let newtimeoutkey = setTimeout(function() {
                add_request_item(method_name, params, callback, repeat_time, curr_path_name);
            }, repeat_time);
            timeoutkeys[indexno] = newtimeoutkey;
        }
    };

    // rander : Model -> HTML
    const rander = function(property, value, force) {
        for (i in value) {
            const vn = value[i];
            if (typeof vn == typeof {}) {
                rander(property + '.' + i, vn, force);
            } else {
                // data-bind
                $('[data-bind="' + property + '.' + i + '"]').each(function() {
                    var tagname = this.tagName,
                        tagname = tagname.toUpperCase(),
                        format = $(this).attr('data-format'),
                        decimals = $(this).attr('data-decimals'),
                        blinking = $(this).attr('data-blinking');
                    // 데이터 출력 형식 변경
                    // if(format=='table') {
                    //     console.log('table rander property:',property);
                    // }
                    switch (format) {
                        case 'table':
                            const $tpl = $(this).find('[name=tpl]').clone(), $empty =$(this).find('[name=empty]'), $search =$(this).find('[name=search]');
                            $search.hide().addClass('hide');
                            $empty.hide().addClass('hide');
                            if(!vn || vn.length < 1) {
                                $empty.show().removeClass('hide');
                            } else {
                                let html = [];
                                for( i in vn) {
                                    let row = vn[i];
                                    for(key in row) {
                                        let v = row[key];
                                        $tpl.find('[name='+key+']').each(function(){
                                            let $self = $(this), data_format = $self.attr('data-format');
                                            if(data_format=='comma') {
                                                v = real_number_format(v);
                                            }
                                            $self.text(v);
                                        })
                                    }
                                    html[i] = $tpl.html();
                                }
                                $(this).children().not('[name=tpl],[name=search],[name=empty]').remove();
                                $(this).append(html.join(''));
                                vt = null;
                            }
                            break;
                        case 'comma':
                            // console.log(i, vt, vn, decimals);
                            vt = real_number_format(vn, decimals);
                            break;
                        case 'number':
                            vt = (vn + '').replace(/[^0-9.]/g, '') * 1;
                            break;
                        default:
                            vt = vn;
                    }
                    // 값 지정
                    // console.log('property:',property,'i:',i,'tagname:',tagname);
                    switch (tagname) {
                        case 'INPUT':
                            let type = ($(this).attr('type') + '').toUpperCase();
                            switch(type) {
                                case 'CHECKBOX':
                                    // $(this).prop('checked', vn==$(this).val()); // 안바뀌는 경우 있어서 click으로 변경.
                                    let same_value = vn==$(this).val(); // 값이 같은가?
                                    // 값이 같은데 체크 안되있으면 클릭해서 체크함.
                                    // 값이 다른데 체크 되있으면 클릭해서 언체크함.
                                    // console.log('same_value:', same_value);
                                    // console.log('checked:', $(this).is(':checked'));
                                    if(same_value && !$(this).is(':checked') || !same_value && $(this).is(':checked')) {
                                        $(this).trigger('click');
                                    }
                                    break;
                                case 'RADIO': // 라디오, 채크박스는 값이 같으면 checked 속성을 넣습니다.
                                    // $(this).prop('checked', vt==$(this).val()); // 안바뀌는 경우 있어서 click으로 변경.
                                    if(vt==$(this).val()) { $(this).trigger('click');}
                                    break;
                                case 'NUMBER': // <input type="hidden" 에 숫자 값은 콤마 없이 넣고 hidden이 아니면 콤마를 추가합니다.
                                case 'HIDDEN': // <input type="hidden" 에 숫자 값은 콤마 없이 넣고 hidden이 아니면 콤마를 추가합니다.
                                    $(this).val(vt);
                                    break;
                                default:
                                    vt = (vt && vt.toNumber() == vt && (typeof(vt)).toLowerCase() == 'number' && !(vt + '').match(/[^0-9.]/)) ? real_number_format(vt) : vt;
                                    $(this).val(vt);
                            }
                            break;
                        case 'TEXTAREA':
                        case 'SELECT':
                            $(this).val(vt);
                            break;
                        case 'IMG':
                        case 'IFRAME':
                            $(this).attr('src', vt);
                            break;
                        default:
                            if ('userid' != i) { // userid는 콤마 미입력
                                vt = (vt && vt.toNumber() == vt && (typeof(vt)).toLowerCase() == 'number' && !(vt + '').match(/[^0-9.]/)) ? real_number_format(vt) : vt;
                            }
                            // if(format=='table') {
                            //     console.log('table rander vt:',vt);
                            // }
                            $(this).html(vt);
                            break;
                    }
                    if(blinking) {
                        $(this).blinking();
                    }
                });

                // display-bind
                // <div data-display="market.use_userpw=Y"></div>
                $('[data-display^="'+property+'.'+i+'"]').each(function() { //
                    let data = $(this).attr('data-display');
                    data = data.split('=');
                    if(vn == data[1]) {
                        $(this).removeClass('hide').attr('style','');
                    } else {
                        $(this).addClass('hide').attr('style','');
                    }
                });
            }
        }
    }
    const set_table_data = function(target, data) {
        const $target = $(target), $tpl = $target.find('[name=tpl]').clone(), $empty =$target.find('[name=empty]'), $search =$target.find('[name=search]');
        $search.hide().addClass('hide');
        $empty.hide().addClass('hide');
        // console.log('target:', $target);
        // console.log('data:', data);
        // console.log('$tpl:', $tpl);
        if(!data || data.length < 1) {
            $empty.show().removeClass('hide');
            $target.children().not('[name=tpl],[name=search],[name=empty]').remove();
        } else {
            let html = [];
            for( i in data) {
                let _row = data[i], $t = $tpl.clone();
                for(let k in _row) {
                    let $row = $t.find('[data-bind="row.'+k+'"]'), format = $row.attr('data-format'), css_name = $row.attr('data-css'), attr_name = $row.attr('data-attr') ;
                    // console.log('k:','[data-bind="row.'+k+'"]', 'format:',format,'attr_name:',attr_name);
                    switch(format) {
                        case 'attr': $row.attr('data-'+k, _row[k]); break;
                        case 'comma': $row.text(real_number_format(_row[k])); break;
                        case 'number': $row.text((_row[k]+'').replace(/[^0-9.-]/g,'')); break;
                        case 'css': $row.css(css_name, _row[k]); break;
                        case 'attr': $row.attr(attr_name, _row[k]); console.log('k:','[data-bind="row.'+k+'"]', 'format:',format,'attr_name:',attr_name); break;
                        case 'class': $row.addClass(_row[k]); break;
                        default: $row.text(_row[k]);
                    }
                }
                // console.log('$tpl:', $('div').append($tpl.attr('name','').removeClass('hide')).html());

                html[i] = $('<div></div>').append($t.attr('name','').removeClass('hide')).html();
            }
            $target.children().not('[name=tpl],[name=search],[name=empty]').remove();
            $target.append(html.join(''));
        }
    }
    window.set_table_data = set_table_data;
    window.reset_table_data = function(target) {
        const $target = $(target), $tpl = $target.find('[name=tpl]').clone(), $empty =$target.find('[name=empty]'), $search =$target.find('[name=search]');
        $search.show().removeClass('hide');
        $empty.hide().addClass('hide');
        $target.children().not('[name=tpl],[name=search],[name=empty]').remove();
    };
    // 회원정보
    const getUserInfo = function(callback) {
        add_request_item('getMyInfo', {'token':TOKEN}, function(r) {
            // console.log(r);
            if(r && r.success) {
                user_info = r.payload;
                rander('user_info', user_info);
            }
            if(callback) {callback();}
        });
    }
    // 지갑정보
    const getUserWallet = function(callback, msec) {
        add_request_item('getBalance', {'token':TOKEN}, function(r) {
            // console.log(r);
            if(r && r.success) {
                user_wallet = r.payload;
                for(i in user_wallet) {
                    let row = user_wallet[i];
                    user_info[(row.symbol)+'_balance'] = row.confirmed *1;
                    // console.log((row.symbol)+'_balance : ',row.confirmed *1)
                }
                // rander('user_wallet', user_wallet);
                rander('user_info', user_info);
            }
            if(callback) {callback();}
        }, msec);
    }
    // 모든 거래 조회
    const getTradingData = function(callback, msec) {
        add_request_item('getTradingList', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'page':1, 'rows':100}, function(r) {
            let price = '';
            if(r && r.success) {
                for(i in r.payload) {
                    let row = r.payload[i];
                    r.payload[i].amount = row.amount*1;
                    r.payload[i].price = row.price*1;
                    if(price=='') {
                        price = r.payload[i].price;
                        exchange_info.GWS_buyalbe = Math.floor(user_info.BDC_balance*1 * price);
                        exchange_info.BDC_buyalbe = Math.floor(user_info.GWS_balance*1 / price);
                        exchange_info.price = price;
                    }
                    // r.payload[i].volume = row.volume*1;
                    // r.payload[i].volume_remain = row.volume_remain*1;
                    // r.payload[i].tot_cnt = row.tot_cnt*1;
                    // r.payload[i].my_exchange_symbol = row.trading_type=='sell' ? row.exchange : row.symbol;
                    // r.payload[i].my_exchange_cnt = row.trading_type=='sell' ? row.amount : row.volume;
                    // r.payload[i].my_sell_symbol = row.trading_type=='sell' ? row.symbol : row.exchange;
                    // r.payload[i].my_sell_cnt = row.trading_type=='sell' ? row.volume : row.amount;
                    // r.payload[i].color = row.my_sell_symbol=='BDC' ? 'text-blue' : 'text-red';
                    // r.payload[i].date_traded = row.time_traded ? date('m.d H:i', row.time_traded) : '';
                    // r.payload[i].date_order = row.time_order ? date('m.d H:i', row.time_order) : '';
                }
                // set_table_data('[data-bind="trading_list"]', r.payload);
                rander('exchange_info', exchange_info);
            }
            if(callback) {callback();}
        }, msec);
    }
    // 초기 회원정보 조회
    getUserInfo(function(){
        getUserWallet(null, 5000);
        getMyOrderData(null, 5000);
        getCloseOrderData(null, 5000);
        getTradingData(null, 5000);
    });

    // 교환시세조회
    const getQuoteData = function(callback, msec) {
        add_request_item('getQuoteList', {'symbol':'BDC', 'exchange':'GWS'}, function(r) {
            let sell_list=[], buy_list=[], max_sell_volume=0, max_buy_volume=0, max_buy_amount=0 ;
            if(r && r.success) {
                // console.log(r.payload);
                for(i in r.payload) {
                    let row = r.payload[i];
                    row.price = row.price*1;
                    row.volume = row.volume*1;
                    if(row.trading_type == 'sell') {
                        max_sell_volume = max_sell_volume < row.volume ? row.volume : max_sell_volume;
                        sell_list.push(row);
                    } else {
                        max_buy_volume = max_buy_volume < row.volume ? row.volume : max_buy_volume;
                        row.amount = Math.round(row.price * row.volume);
                        max_buy_amount = max_buy_amount < row.amount ? row.amount : max_buy_amount;
                        buy_list.push(row);
                    }
                }
                sell_list.map(function(row){
                    row.volume_percent = Math.round(row.volume/max_sell_volume * 100) + '%';
                })
                buy_list.map(function(row){
                    row.volume_percent = Math.round(row.volume/max_buy_volume * 100) + '%';
                    row.amount_percent = Math.round(row.amount/max_buy_amount * 100) + '%';
                })
                // console.log(sell_list, buy_list);
                set_table_data('[data-bind="sell_list"]', sell_list);
                set_table_data('[data-bind="buy_list"]', buy_list);
            }
            if(callback) {callback();}
        }, msec);
    }
    getQuoteData(null, 5000);
    // 호가테이블에서 교환비 클릭시 매매 폼에 교환비 값으로 적용하기
    $('[data-bind="sell_list"],[data-bind="buy_list"]').on('click', '[name="price"]', function(){
        let price = $(this).text().toNumber();
        $('#price_sell_bdc,#price_sell_gws').val(price).trigger('change');
    })



    // 나의 거래 완료 조회
    // const getMyCloseOrderData = function(callback, msec) {
    //     add_request_item('getCloseOrderList', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'page':1, 'rows':1000}, function(r) {
    //         if(r && r.success) {
    //             // console.log(r.payload);
    //             for(i in r.payload) {
    //                 let row = r.payload[i];
    //                 r.payload[i].amount = row.amount*1;
    //                 r.payload[i].price = row.price*1;
    //                 r.payload[i].volume = row.volume*1;
    //                 r.payload[i].volume_remain = row.volume_remain*1;
    //                 r.payload[i].tot_cnt = row.tot_cnt*1;
    //                 r.payload[i].my_exchange_symbol = row.trading_type=='sell' ? row.exchange : row.symbol;
    //                 r.payload[i].my_exchange_cnt = row.trading_type=='sell' ? row.amount : row.volume;
    //                 r.payload[i].my_sell_symbol = row.trading_type=='sell' ? row.symbol : row.exchange;
    //                 r.payload[i].my_sell_cnt = row.trading_type=='sell' ? row.volume : row.amount;
    //                 r.payload[i].color = row.my_sell_symbol=='BDC' ? 'text-blue' : 'text-red';
    //                 r.payload[i].date_traded = row.time_traded ? date('m.d H:i', row.time_traded) : '';
    //                 r.payload[i].date_order = row.time_order ? date('m.d H:i', row.time_order) : '';
    //             }
    //             set_table_data('[data-bind="close_order_list"]', r.payload);
    //         }
    //         if(callback) {callback();}
    //     }, msec);
    // }
    let order_data_type = 'total';
    const getCloseOrderData = function(callback, msec) {
        if(order_data_type === 'total') {
            getTotalCloseOrderData(callback);
        } else {
            getMyCloseOrderData(callback);
        }
        if(msec) setTimeout(function(){ getCloseOrderData(callback, msec); }, msec);
    }
    // 전체 거래 내역 조회
    const getTotalCloseOrderData = function(callback, msec) {
        add_request_item('getTradingList', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'page':1, 'rows':100}, function(r) {
            console.log(r);
            let price = '';
            if(r && r.success) {
                // console.log(r.payload);
                for(i in r.payload) {
                    let row = r.payload[i];
                    r.payload[i].price = row.price*1;
                    if(price=='') {
                        price = r.payload[i].price;
                        exchange_info.GWS_buyalbe = Math.floor(user_info.BDC_balance*1 * price);
                        exchange_info.BDC_buyalbe = Math.floor(user_info.GWS_balance*1 / price);
                        exchange_info.price = price;
                    }
                    r.payload[i].volume = row.volume*1;
                    r.payload[i].amount = row.amount ? row.amount*1 : row.price*row.volume;
                    r.payload[i].volume_remain = row.volume_remain?row.volume_remain*1:0;
                    r.payload[i].tot_cnt = row.tot_cnt*1;
                    r.payload[i].my_exchange_symbol = row.trading_type=='sell' ? row.exchange : row.symbol;
                    r.payload[i].my_exchange_cnt = row.trading_type=='sell' ? Math.round(row.amount) : row.volume;
                    r.payload[i].my_sell_symbol = row.trading_type=='sell' ? row.symbol : row.exchange;
                    r.payload[i].my_sell_cnt = row.trading_type=='sell' ? row.volume : Math.round(row.amount);
                    r.payload[i].color = row.my_sell_symbol=='BDC' ? 'text-blue' : 'text-red';
                    r.payload[i].date_traded = row.time_traded ? date('m.d H:i', row.time_traded) : '';
                    r.payload[i].date_order = row.time_order ? date('m.d H:i', row.time_order) : '';
                }
                rander('exchange_info', exchange_info);
            }
            set_table_data('[data-bind="close_order_list"]', r.payload);
            if(callback) {callback();}
        }, msec);
    }
    // 나의 거래 내역 조회
    const getMyCloseOrderData = function(callback, msec) {
        // console.log('getMyCloseOrderData start');
        add_request_item('getMyTradingList', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'page':1, 'rows':100}, function(r) {
            // console.log(r.payload);
            if(r && r.success) {
                for(i in r.payload) {
                    let row = r.payload[i];
                    r.payload[i].amount = row.amount*1;
                    r.payload[i].price = row.price*1;
                    r.payload[i].volume = row.volume*1;
                    r.payload[i].volume_remain = row.volume_remain*1;
                    r.payload[i].tot_cnt = row.tot_cnt*1;
                    r.payload[i].my_exchange_symbol = row.trading_type=='sell' ? row.exchange : row.symbol;
                    r.payload[i].my_exchange_cnt = row.trading_type=='sell' ? Math.round(row.amount) : row.volume;
                    r.payload[i].my_sell_symbol = row.trading_type=='sell' ? row.symbol : row.exchange;
                    r.payload[i].my_sell_cnt = row.trading_type=='sell' ? row.volume : Math.round(row.amount);
                    r.payload[i].color = row.my_sell_symbol=='BDC' ? 'text-blue' : 'text-red';
                    r.payload[i].date_traded = row.time_traded ? date('m.d H:i', row.time_traded) : '';
                    r.payload[i].date_order = row.time_order ? date('m.d H:i', row.time_order) : '';
                }
            }
            set_table_data('[data-bind="close_order_list"]', r.payload);
            if(callback) {callback();}
            // console.log('getMyCloseOrderData end');
        }, msec);
    }
    // 나의 주문 조회
    const getMyOrderData = function(callback, msec) {
        add_request_item('getOpenOrderList', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'page':1, 'rows':100}, function(r) {
            if(r && r.success) {
                for(i in r.payload) {
                    let row = r.payload[i];
                    r.payload[i].amount = row.amount*1;
                    r.payload[i].price = row.price*1;
                    r.payload[i].volume = row.volume*1;
                    r.payload[i].volume_remain = row.volume_remain*1;
                    r.payload[i].volume_remain_str = '('+real_number_format(row.volume_remain)+')';
                    r.payload[i].cancel_diplay = row.volume_remain>0 ? 'inline-block' : 'none';
                    r.payload[i].tot_cnt = row.tot_cnt*1;
                    r.payload[i].my_exchange_symbol = row.trading_type=='sell' ? row.exchange : row.symbol;
                    r.payload[i].my_exchange_cnt = row.trading_type=='sell' ? row.amount : row.volume;
                    r.payload[i].my_sell_symbol = row.trading_type=='sell' ? row.symbol : row.exchange;
                    r.payload[i].my_sell_cnt = row.trading_type=='sell' ? row.volume : row.amount;
                    r.payload[i].my_sell_cnt_remain = row.trading_type=='sell' ? '('+real_number_format(row.volume_remain)+')' : '('+real_number_format(row.amount*1 - Math.round(row.volume_remain / row.price))+')';
                    r.payload[i].color = row.my_sell_symbol=='BDC' ? 'text-blue' : 'text-red';
                    r.payload[i].date_traded = row.time_traded ? date('m.d H:i', row.time_traded) : '';
                    r.payload[i].date_order = row.time_order ? date('m.d H:i', row.time_order) : '';
                }
            }
            set_table_data('[data-bind="my_order_list"]', r.payload);
            if(callback) {callback();}
        }, msec);
    }
    // 주문 취소
    $('[data-bind="my_order_list"]').on('click', '[name="btn-order-cancel"]', function(){
        let orderid = $(this).parent().find('[name=orderid]').text();
        if(orderid && confirm(__('주문을 취소하시겠습니까?'))) {
            $.post(API_URL+'/cancel/', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'orderid':orderid}, function(r){
                if(r && r.success) {
                    // alert('취소되었습니다.');
                    getUserWallet();
                    getMyOrderData();
                    getQuoteData();
                } else {
                    alert(__('취소하지 못했습니다.'));
                }
            }, 'json')
        }
    });
    // 교환비는 소숫점 못쓰도록 차단.
    $('#price_sell_bdc,#price_sell_gws,#amount_sell_gws').on('keydown', function(evt){
        let keycode = get_keycode(evt);
        if(keycode==190 || keycode==110 || keycode==78)  { // 쩜(.) 키코드들
            return false;
        }
    }).on('change', function(){
        let val = $(this).val();
        val = val.split('.');
        val = val[0];
        $(this).val(val);
    })
    // BDC 매도량, BDC 교환비 입력시 GWS 매수량 표시
    $('#volume_sell_bdc,#price_sell_bdc').on('change keyup', function(){
        let volume = $('#volume_sell_bdc').val().toNumber(), price = $('#price_sell_bdc').val().toNumber(), amount = volume * price;
        $('#amount_sell_bdc').val(amount).trigger('change');
    })
    // BDC 매도
    $('[name="btn-sell-bdc"]').on('click', function(){
        let volume = $('#volume_sell_bdc').val().toNumber(), price = $('#price_sell_bdc').val().toNumber(), amount = volume * price;
        if(!volume) {
            alert(__('BDC 매도량을 입력해주세요.'));
            $('#volume_sell_bdc').focus();
            return false;
        }
        if(!price) {
            alert(__('교환비를 입력해주세요.'));
            $('#price_sell_bdc').focus();
            return false;
        }
        if(confirm('BDC를 매도하시겠습니까?')) {
            $.post(API_URL+'/sell/', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'volume':volume, 'price':price}, function(r){
                if(r && r.success) {
                    getUserWallet();
                    getMyOrderData();
                    getQuoteData();
                    if(r.payload.remain_volume<=0) {
                        // getMyCloseOrderData();
                        getCloseOrderData();
                    }
                    let msg = r.payload.remain_volume <= 0 ? __('BDC를 매도했습니다.') : __('BDC 매도를 등록했습니다.');
                    alert(msg);
                } else {
                    let msg = r.error && r.error.message ? r.error.message : '';
                    alert(__('BDC를 매도하지 못했습니다.') + msg);
                }
            },'json');
        }
    });
    $('#form-sell').on('submit', function(){
        $('[name="btn-sell-bdc"]').trigger('click');
        return false;
    });
    // GWS 매도량, GWS 교환비 입력시 BDC 매수량 표시
    $('#amount_sell_gws,#price_sell_gws').on('change keyup', function(){
        let amount = $('#amount_sell_gws').val().toNumber(), price = $('#price_sell_gws').val().toNumber(), volume = price>0 ? (amount / price).toFixed(4) : 0;
        $('#volume_sell_gws').val(volume).trigger('change');
    });
    // GWS 매도 = BDC 매수
    $('[name="btn-sell-gws"]').on('click', function(){
        let amount = $('#amount_sell_gws').val().toNumber(), price = $('#price_sell_gws').val().toNumber(), volume = price>0 ? (amount / price).toFixed(4) : 0;
        if(!amount) {
            alert(__('GWS 매도량을 입력해주세요.'));
            $('#amount_sell_gws').focus();
            return false;
        }
        if(!price) {
            alert(__('교환비를 입력해주세요.'));
            $('#price_sell_gws').focus();
            return false;
        }
        if(confirm(__('GWS를 매도하시겠습니까?'))) {
            $.post(API_URL+'/buy/', {'symbol':'BDC', 'exchange':'GWS', 'token':TOKEN, 'volume':volume, 'amount':amount, 'price':price}, function(r){
                if(r && r.success) {
                    getUserWallet();
                    getMyOrderData();
                    getQuoteData();
                    if(r.remain_volume<=0) {
                        // getMyCloseOrderData();
                        getCloseOrderData();
                    }
                    let msg = r.remain_volume <= 0 ? __('GWS를 매도했습니다.') : __('GWS 매도를 등록했습니다.');
                    alert(msg);
                } else {
                    let msg = r.error && r.error.message ? r.error.message : '';
                    alert(__('GWS를 매도하지 못했습니다.') + msg);
                }
            },'json');
        }
    });
    $('#form-buy').on('submit', function(){
        $('[name="btn-sell-gws"]').trigger('click');
        return false;
    });
    $('[name=btn-total-txns],[name=btn-my-txns]').on('click', function(){
        _type = $(this).attr('name').indexOf('-total-')>-1 ? 'total' : 'my';
        order_data_type = _type;
        // console.log(type);
        // if(type==='my') {
        //     getMyCloseOrderData();
        // } else {
        //     getTotalCloseOrderData();
        // }
        getCloseOrderData();
        $(this).addClass('active').siblings().removeClass('active');
        return false;
    });



}))
