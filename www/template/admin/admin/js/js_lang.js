
var lang = getCookie('lang');   //현재언어
lang = lang ? lang : 'kr';
lang = lang == 'ko' ? 'kr' : lang;

var i18n_data = {};
$.get('/lib/language/langcache/i18n_admin_'+lang+'.cache.js', function(r){
    if(r) {
        i18n_data = r;
    }
}, 'json');


//자바스크립트 내용 한글 치환
function change_lang(code){
    return typeof i18n_data[code] != typeof undefined && i18n_data[code]!='' ? i18n_data[code] : code;
}
function __(code) { return change_lang(code);}