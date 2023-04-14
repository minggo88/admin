//신규 메인 5개 미니차트
function setChartData(priceArr,volArr,symbol,rank) {
	// console.log('kmcsetrade-set.js setChartData start');
	// console.log(priceArr,volArr,symbol,rank);

    var priceArr_max = priceArr.reduce( function (previous, current) {
        return previous > current ? previous:current;
    });

    var priceArr_min = priceArr.reduce( function (previous, current) {
        return previous > current ? current:previous;
    });



    //차트 최소 여유값 구하기. 자바스크립트 부동소수점 문제 함수사용

    $('.mini_chart').eq(rank).sparkline(priceArr,
       {
        type: 'line',
        width: '94%',
        height: 70,
        lineWidth: 1,
        lineColor: '#545454',
        fillColor: '#3c3c3c',
        chartRangeMin: priceArr_max,
        chartRangeMax: priceArr_min,
        spotRadius: 0,
      }
    );

    //로고
    $('.coin_icon').eq(rank).children('span').addClass('icon-24-'+ symbol.toLowerCase() );
    //코인 이름 change_lang번역 함수는 템플릿/js/lang_js.js 안에 있음
    $('.coin_title').eq(rank).html(change_lang(symbol));
    //통화
    $('.coin_currency').eq(rank).html(symbol + "/KRW");
    //현재가
    $('.long_price').eq(rank).html(returnComma(priceArr[24]));
    //원
    var currency = change_lang("currency_mark");
    $('.currency').eq(rank).html(currency);
    $('.currency2').eq(rank).html(currency);

    //24시간 변동가 체크
    var check = check_price(priceArr[24],priceArr[0]);

    //변동가격
    $('.short_price').eq(rank).html(returnComma(check.price));

    //빨강 파랑 색상 적용
    $('.mini_chart_container2').eq(rank).find('.color_class').addClass(check.color);
    $('.mini_chart_container2').eq(rank).find('.color_bg').addClass(check.color_bg);

    //화살표 적용
    $('.color_arrow').eq(rank).addClass(check.arrow);

    //퍼센트 적용
    $('.short_percent').eq(rank).html(check.per);

    //총 거래량 적용
    $('.total_vol').eq(rank).html( returnComma((priceArr[24] * volArr[24]).toFixed(0)) );



}

//신규 메인 5개 미니차트 끝

//24시간 변동가
function check_price(arr24, arr0){
    tmp = arr24 - arr0;
    if(tmp < 0){
        color = "bull";
        arrow = "fa-caret-down"; //fa-arrow-down
        color_bg = "bull_bg";
        add = "";
    }else{
        color = "bear";
        arrow = "fa-caret-up";
        color_bg = "bear_bg";
        add = "+";
    }
    //퍼센트
    per = add + ( 100 - (arr0/arr24 *100) ).toFixed(2) + "%";

    return {
        price: tmp,
        color: color,
        arrow: arrow,
        color_bg : color_bg,
        per : per,
    };
}


//콤마 찎기
function returnComma(value){
    if(value == null || value == undefined){
        return;
    }
    let convertVal = "";
    value = value.toString();
    if(value.indexOf('.') !== -1) {
        value = parseFloat(value).toFixed(3);

        let upper = value.substring(0, value.indexOf('.'));
        let down = value.substring(value.indexOf('.'), value.length);
        upper = upper.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        convertVal = upper.concat(down);
    } else {
        convertVal = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return convertVal;
}


//기존 2개짜리 차트 시작

var arr1 = [];
var arr1_yesterday = [];
var arr2 = [];
var arr2_yesterday = [];

function setSccChartData(sccChartData) {

    arr1_yesterday = 	sccChartData.slice( 0, 24 );
    arr1 = 	sccChartData.slice( 24, 48 );

}

function setBtcChartData(btcChartData) {
	console.log('kmcsetrade-set.js setBtcChartData start');

    arr2_yesterday = 	btcChartData.slice( 0, 24 );
    arr2 = 	btcChartData.slice( 24, 48 );


    makeChart();
}

//자바스크립트 연산자 부동소수점 문제...
var nums = function (op, x, y) {

    var n = {
            '*': Number(x) * Number(y),
            '-': Number(x) - Number(y),
            '+': Number(x) + Number(y),
            '/': Number(x) / Number(y)
        }[op];

    return Math.round(n * 1000000000)/1000000000;
};

function makeChart() {
	console.log('kmcsetrade-set.js makeChart start');

    if(arr1.length !== 0 && arr2.length !== 0) {
        $(function() {

            var arr1_max = arr1.concat(arr1_yesterday).reduce( function (previous, current) {
                return previous > current ? previous:current;
            });

            var arr1_min = arr1.concat(arr1_yesterday).reduce( function (previous, current) {
                return previous > current ? current:previous;
            });

            var arr2_max = arr2.concat(arr2_yesterday).reduce( function (previous, current) {
                return previous > current ? previous:current;
            });

            var arr2_min = arr2.concat(arr2_yesterday).reduce( function (previous, current) {
                return previous > current ? current:previous;
            });

            //차트 최소 여유값 구하기. 자바스크립트 부동소수점 문제 함수사용
            var tmp = nums('-',arr1_max , arr1_min);
            tmp = nums('*',tmp , 0.1);
            arr1_min = nums('-', arr1_min, tmp );
            arr1_max = nums('+', arr1_max, tmp );

            var tmp = nums('-',arr2_max , arr2_min);
            tmp = nums('*',tmp , 0.1);
            arr2_min = nums('-', arr2_min, tmp );
            arr2_max = nums('+', arr2_max, tmp );


            $('.trend-chart').eq(0).sparkline(arr1_yesterday,
               {
                type: 'line',
                width: '100%',
                height: '180',
                lineWidth: 1,
                lineColor: '#FFB601',
                fillColor: '#FFB601',
                chartRangeMin: arr1_min,
                chartRangeMax: arr1_max,
                spotRadius: 0
              }
            );

            $(".trend-chart").eq(0).sparkline(arr1,
                {
                 composite: true,
                 type: 'line',
                 width: '100%',
                 height: '180',
                 lineWidth: 1.5,
                 lineColor: '#000',
                 spotRadius: 0,
                 fillColor: false,
                 chartRangeMin: arr1_min,
                 chartRangeMax: arr1_max
               }
             );


             $(".trend-chart").eq(1).sparkline(arr2_yesterday,
                {
                type: 'line',
                width: '100%',
                height: '180',
                lineWidth: 1,
                lineColor: '#FFB601',
                fillColor: '#FFB601',
                chartRangeMin: arr2_min,
                chartRangeMax: arr2_max,
                spotRadius: 0
                }
            );

            $(".trend-chart").eq(1).sparkline(arr2,
                {
                composite: true,
                type: 'line',
                width: '100%',
                height: '180',
                lineWidth: 1.5,
                lineColor: '#000',
                spotRadius: 0,
                fillColor: false,
                chartRangeMin: arr2_min,
                chartRangeMax: arr2_max,
                }
            );

        });
    }
}

//기존 2개짜리 차트 끝

!function(l){function e(e){for(var r,t,n=e[0],o=e[1],u=e[2],f=0,i=[];f<n.length;f++)t=n[f],p[t]&&i.push(p[t][0]),p[t]=0;for(r in o)Object.prototype.hasOwnProperty.call(o,r)&&(l[r]=o[r]);for(s&&s(e);i.length;)i.shift()();return c.push.apply(c,u||[]),a()}function a(){for(var e,r=0;r<c.length;r++){for(var t=c[r],n=!0,o=1;o<t.length;o++){var u=t[o];0!==p[u]&&(n=!1)}n&&(c.splice(r--,1),e=f(f.s=t[0]))}return e}var t={},p={1:0},c=[];function f(e){if(t[e])return t[e].exports;var r=t[e]={i:e,l:!1,exports:{}};return l[e].call(r.exports,r,r.exports,f),r.l=!0,r.exports}f.m=l,f.c=t,f.d=function(e,r,t){f.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},f.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},f.t=function(r,e){if(1&e&&(r=f(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var t=Object.create(null);if(f.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var n in r)f.d(t,n,function(e){return r[e]}.bind(null,n));return t},f.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return f.d(r,"a",r),r},f.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},f.p="/";var r=window.webpackJsonp=window.webpackJsonp||[],n=r.push.bind(r);r.push=e,r=r.slice();for(var o=0;o<r.length;o++)e(r[o]);var s=n;a()}([])