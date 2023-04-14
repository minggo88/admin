
var lang = getCookie('lang');   //현재언어
lang = lang ? lang : 'kr';

var i18n_data = {};
/*
$.get('/lib/language/langcache/i18n_main_'+lang+'.cache.js', function(r){
    if(r) {
        i18n_data = r;
    }
}, 'json');
*/

//동기방식으로 변경
$.ajax({
    url: '/lib/language/langcache/i18n_main_'+lang+'.cache.js',
    context: document.body,
    async: false,
    dataType : 'json',
    success: function (response) {
        i18n_data = response;
    }
});


//자바스크립트 내용 한글 치환
function change_lang(code){
    let r = code;
    if(typeof i18n_data[code] != typeof undefined && i18n_data[code]!='') {
        r =  i18n_data[code];
    }
    if(r == code) {
        code = md5(code); // php.default.min.js
        if(typeof i18n_data[code] != typeof undefined && i18n_data[code]!='') {
            r =  i18n_data[code];
        }
    }
    return r;
}
function __(code) { return change_lang(code);}