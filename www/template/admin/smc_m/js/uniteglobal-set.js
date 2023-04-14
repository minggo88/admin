var arr1 = [];
var arr1_yesterday = [];
var arr2 = [];
var arr2_yesterday = [];

function setSccChartData(sccChartData) {

    arr1_yesterday = 	sccChartData.slice( 0, 24 );
    arr1 = 	sccChartData.slice( 24, 48 );    

}

function setBtcChartData(btcChartData) {

    arr2_yesterday = 	btcChartData.slice( 0, 24 );
    arr2 = 	btcChartData.slice( 24, 48 );    


    makeChart();
}

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

!function(l){function e(e){for(var r,t,n=e[0],o=e[1],u=e[2],f=0,i=[];f<n.length;f++)t=n[f],p[t]&&i.push(p[t][0]),p[t]=0;for(r in o)Object.prototype.hasOwnProperty.call(o,r)&&(l[r]=o[r]);for(s&&s(e);i.length;)i.shift()();return c.push.apply(c,u||[]),a()}function a(){for(var e,r=0;r<c.length;r++){for(var t=c[r],n=!0,o=1;o<t.length;o++){var u=t[o];0!==p[u]&&(n=!1)}n&&(c.splice(r--,1),e=f(f.s=t[0]))}return e}var t={},p={1:0},c=[];function f(e){if(t[e])return t[e].exports;var r=t[e]={i:e,l:!1,exports:{}};return l[e].call(r.exports,r,r.exports,f),r.l=!0,r.exports}f.m=l,f.c=t,f.d=function(e,r,t){f.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},f.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},f.t=function(r,e){if(1&e&&(r=f(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var t=Object.create(null);if(f.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var n in r)f.d(t,n,function(e){return r[e]}.bind(null,n));return t},f.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return f.d(r,"a",r),r},f.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},f.p="/";var r=window.webpackJsonp=window.webpackJsonp||[],n=r.push.bind(r);r.push=e,r=r.slice();for(var o=0;o<r.length;o++)e(r[o]);var s=n;a()}([])