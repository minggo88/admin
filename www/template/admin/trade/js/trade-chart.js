$(document).ready(function() {
    $("#mtom_list_inner>table>tbody>tr").hover(
        function () { $(this).css('background-color','#FFF2F0'); },
        function () { $(this).css('background-color','#FFF'); }
    );
    $("svg").css("position","relative");

    $( '.chart_m' ).hover(
        function () {
            $('.react-stockcharts-tooltip-hover').show();
        }, function () {
            $('.react-stockcharts-tooltip-hover').hide();
        }
    );

    $("#chartModal").keyup(function(e){if(e.keyCode == 13) $("#chartModal").modal("hide"); });

    if($("#leftDiv").html() != undefined) {         // Chart.html에서만 사용
        var height = window.innerHeight;

        if($("#trade").css("display") != "none") {
            var prevHeight = 0;
            if(height < 800) {
                prevHeight = parseFloat($("#tradeTb").find(".ibox-content").css("min-height").replace("px",""));
                $("#tradeTb").find(".ibox-content").css("min-height", prevHeight-5);
            } else {
                prevHeight = parseFloat($(".minHeightPanel").css("min-height").replace("px",""));
                $(".minHeightPanel").css("min-height", prevHeight + 16);
            }
        } else {
            $("#tradeTb").find(".ibox-content").css("min-height", "auto");
        }

        $(".rc-slider-dot").each(function(e) {
            var index = e;
            if(index > 10) {
                index = index - 11;
            }
            $(".rc-slider-dot").eq(e).html( "<span class='rc-slider-dot-text' style='margin-left:-"+ index +"px;'>" + index * 10 + "%" + "</span>" );
        });
    }

    var heightCalc = window.innerHeight / 2 - 200;

    $("#chartModalDialog").css("margin-top",heightCalc);

    window.setTimeout(function() {
        var errorCnt = $("input[name=dataErrorCnt]").val();
        if(errorCnt !== null && errorCnt !== undefined && errorCnt !== "") {
            if(parseInt(errorCnt) !== 0) {
                fn_show_toast("error", 'Server Connection Error!<br>You need to try it again.');
                $("input[name=dataErrorCnt]").val("0");
            }
        }
    }, 2000);

});

$(window).resize(function() {
    $("svg").css("position","relative");
});

function checkBuyBitcoin() {
    if($('#bitcoin_buy_amount').val().indexOf('.')>-1) {
        var _t = $.trim($('#bitcoin_buy_amount').val()).split('.');
        _t[0] = _t[0]*1;
        $('#bitcoin_buy_amount').val( _t.join('.') ) ;
    } else {
        $('#bitcoin_buy_amount').val($.trim($('#bitcoin_buy_amount').val())*1);
    }
//	$('#bitcoin_buy_amount').val($.trim($('#bitcoin_buy_amount').val()));
    if($('#bitcoin_buy_amount').val() != '' && !$.isNumeric($('#bitcoin_buy_amount').val())) {
//		alert('비트코인 매수 수량은 숫자만 입력하세요');
        $('#bitcoin_buy_amount').val($('#bitcoin_buy_amount').val().replace(/[^0-9.]/g,''));
//		$('#bitcoin_buy_amount').focus();
        return false;
    }
    $('#bitcoin_buy_amount').val($('#bitcoin_buy_amount').val().replace(/[^0-9.]/g,''));
    calBuyInfo();
}

function checkBuyPrice() {
    $('#bitcoin_buy_price').val($.trim($('#bitcoin_buy_price').val())*1);
    if($('#bitcoin_buy_price').val() != '' && !$.isNumeric( $('#bitcoin_buy_price').val())) {
//		alert('주문가는 숫자만 입력하세요');
        $('#bitcoin_buy_price').val($('#bitcoin_buy_price').val().replace(/[^0-9.]/g,''));
//		$('#bitcoin_buy_price').focus();
        return false;
    }
    $('#bitcoin_buy_price').val($('#bitcoin_buy_price').val().replace(/[^0-9.]/g,''));
    calBuyInfo();
}

function calBuyInfo() {

    var buyFee = new Number(($('#bitcoin_buy_amount').val() * $('#bitcoin_buy_fee').val()) / 100).toFixed(8);
    var buyKrw = Math.floor(new Number($('#bitcoin_buy_amount').val() * $('#bitcoin_buy_price').val()).toFixed(1) * 1); // 소숫점 절삭.
    var resultBuyAmount = $('#bitcoin_buy_amount').val() - buyFee;
    var cnt_decimal_point = (resultBuyAmount+'').indexOf('.')>-1 ? (resultBuyAmount+'').substr((resultBuyAmount+'').indexOf('.')+1).length : 0;
//	$('#buy_fee').text(buyFee);
    buyFee = '0';
    $('#buy_fee').text(number_format(buyFee));
    $('#krw_buy_amount').text(number_format(buyKrw));
    $('#bitcoin_buy_result').text(number_format(resultBuyAmount, cnt_decimal_point));
}

function sendBuyBitcoin() {

    if($('#bitcoin_buy_price').val() == '' || $('#bitcoin_buy_price').val() == '0' ) {
        alert('Please enter your order price.');
        $('#bitcoin_buy_price').focus();
        return false;
    }

    if($('#bitcoin_buy_amount').val() == '' || $('#bitcoin_buy_amount').val() == '0' ) {
        alert('Please input quantity.');
        $('#bitcoin_buy_amount').focus();
        return false;
    }

    if(  $('#bitcoin_buy_price').val()*1 < $('#min_price_amount').val()*1) {
        alert("Please enter larger than the minimum order price.");
        $('#bitcoin_buy_amount').val(0);
        $('#bitcoin_buy_amount').focus();
        return false;
    }

    // if($('#bitcoin_buy_amount').val()*1 < $('#min_bitcoin_amount').val()*1) {
    //     alert("최소주문량("+$('#min_bitcoin_amount').val()+"BTC)보다 작습니다.");
    //     $('#bitcoin_buy_amount').focus();
    //     return false;
    // }

    // var buyKrw = new Number($('#bitcoin_buy_amount').val() * $('#bitcoin_buy_price').val()).toFixed(0);
    // if(parseFloat(buyKrw) > $('#useable_krw_amount').val()) {
    //     alert("충분한 계좌잔액을 갖고 계시지 않거나 거래제한을 초과하셨습니다. \n 사용가능액 : " + number_format($('#useable_krw_amount').val().replace(/[^0-9.]/g,'')) );
    //     return false;
    // }

    // var answer = confirm(""+number_format($('#bitcoin_buy_amount').val(), ($('#bitcoin_buy_amount').val()+'').indexOf('.')>-1 ? ($('#bitcoin_buy_amount').val()+'').substr(($('#bitcoin_buy_amount').val()+'').indexOf('.')+1).length : 0 )+"비트코인을 "+number_format($('#bitcoin_buy_price').val().replace(/[^0-9.]/g,''))+" 원의 가격으로 매수 하시겠습니까? \n\nKRW 지출액: "+number_format($('#krw_buy_amount').text().replace(/[^0-9.]/g,''))+" 원 \n");

    if(answer) {
        // $.post("/order/order.php?type=B", $( "#buy_bitcoin_form" ).serialize(), function(data){
        $.post("/api/v1.0/buy", $( "#buy_bitcoin_form" ).serialize(), function(data){
            if(typeof data != typeof undefined && data.result) {
                alert('매수요청을 했습니다.');
                location.reload();
            } else {
                var msg = '매수요청처리를 하지 못했습니다.';
                if(typeof data!= typeof undefined && data.desc!='') {
                    msg = data.desc;
                }
                alert(msg);
            }
        }, 'json');
    }
}

function checkSellBitcoin() {
    if($('#bitcoin_sell_amount').val().indexOf('.')>-1) {
        var _t = $.trim($('#bitcoin_sell_amount').val()).split('.');
        _t[0] = _t[0]*1;
        $('#bitcoin_sell_amount').val( _t.join('.') ) ;
    } else {
        $('#bitcoin_sell_amount').val($.trim($('#bitcoin_sell_amount').val())*1);
    }
    if($('#bitcoin_sell_amount').val() != '' && !$.isNumeric($('#bitcoin_sell_amount').val())) {
//		alert('비트코인 매도 수량은 숫자만 입력하세요');
        $('#bitcoin_sell_amount').val($('#bitcoin_sell_amount').val().replace(/[^0-9.]/g,''));
//		$('#bitcoin_sell_amount').focus();
        return false;
    }
    $('#bitcoin_sell_amount').val( $('#bitcoin_sell_amount').val().replace(/[^0-9.]/g,'') );
    calSellInfo();
}

function checkSellPrice() {
    $('#bitcoin_sell_price').val($.trim($('#bitcoin_sell_price').val())*1);
    if($('#bitcoin_sell_price').val() != '' && !$.isNumeric( $('#bitcoin_sell_price').val())) {
//		alert('주문가는 숫자만 입력하세요');
        $('#bitcoin_sell_price').val($('#bitcoin_sell_price').val().replace(/[^0-9]/g,''));
//		$('#bitcoin_sell_price').focus();
        return false;
    }
    $('#bitcoin_sell_price').val($('#bitcoin_sell_price').val().replace(/[^0-9]/g,''));
    calSellInfo();
}

function calSellInfo() {

    var bitcoin_sell_amount = $('#bitcoin_sell_amount').val() * 1;
    var sellKrw = Math.floor(new Number(bitcoin_sell_amount * $('#bitcoin_sell_price').val()).toFixed(1) * 1); // 소숫점 절삭.
    var sellFee = new Number(sellKrw * $('#bitcoin_sell_fee').val() / 100).toFixed(8);
    var resultSellAmount = sellKrw - sellFee;
    var cnt_decimal_point = (bitcoin_sell_amount+'').indexOf('.')>-1 ? (bitcoin_sell_amount+'').substr((bitcoin_sell_amount+'').indexOf('.')+1).length : 0;
    $('#sell_fee').text(number_format(sellFee));
    $('#krw_sell_amount').text(number_format(resultSellAmount));
    $('#bitcoin_sell_result').text(number_format(bitcoin_sell_amount, cnt_decimal_point));
}

function sendSellBitcoin() {
    if($('#bitcoin_sell_amount').val() == '' ) {
        alert('비트코인 매도 수량을 입력하세요');
        $('#bitcoin_sell_amount').focus();
        return false;
    }

    if($('#bitcoin_sell_price').val() == '' ) {
        alert('매도주문가를 입력하세요');
        $('#bitcoin_sell_price').focus();
        return false;
    }

    if($('#bitcoin_sell_amount').val()*1 < $('#min_bitcoin_amount').val()*1) {
        alert("최소주문량("+$('#min_bitcoin_amount').val()+"BTC)보다 작습니다.");
        $('#bitcoin_sell_amount').focus();
        return false;
    }

    if(  $('#bitcoin_sell_price').val()*1 < $('#min_price_amount').val()*1) {
        alert("최소주문가("+$('#min_price_amount').val()+"원)보다 작습니다.");
        $('#bitcoin_sell_price').val(0);
        $('#bitcoin_sell_price').focus();
        return false;
    }

    if($('#wallet_btc_amount').val()*1 <= 0) {
        alert('충분한 비트코인을 갖고 계시지 않거나 거래 제한을 초과하셨습니다.');
        return false;
    }

    if($('#bitcoin_sell_amount').val()*1 > $('#useable_btc_amount').val()*1 ) {
        var useable_btc_amount = $('#useable_btc_amount').val().replace(/[^0-9.]/g,'') * 1;
        var str_useable_btc_amount = number_format(useable_btc_amount, (useable_btc_amount+'').indexOf('.')>-1 ? (useable_btc_amount+'').substr((useable_btc_amount+'').indexOf('.')+1).length : 0 );
        alert("충분한 비트코인을 갖고 계시지 않거나 거래 제한을 초과하셨습니다.\n 판매가능 코인 : " + str_useable_btc_amount);
        return false;
    }

    var answer = confirm(""+number_format($('#bitcoin_sell_amount').val(), ($('#bitcoin_sell_amount').val()+'').indexOf('.')>-1 ? ($('#bitcoin_sell_amount').val()+'').substr(($('#bitcoin_sell_amount').val()+'').indexOf('.')+1).length : 0 )+"비트코인을 "+number_format($('#bitcoin_sell_price').val().replace(/[^0-9.]/g,''))+" 원의 가격으로 매도 하시겠습니까? \n\nKRW 수령액: "+number_format($('#krw_sell_amount').text().replace(/[^0-9.]/g,''))+" 원 \n수수료: "+number_format($('#sell_fee').text().replace(/[^0-9.]/g,''))+" 원 \n");
    if(answer) {
        $.post("/order/order.php?type=A", $( "#sell_bitcoin_form" ).serialize(), function(data){
            if(typeof data != typeof undefined && data.result) {
                alert('매도요청을 했습니다.');
                location.reload();
            } else {
                var msg = '매도요청처리를 하지 못했습니다.';
                if(typeof data!= typeof undefined && data.desc!='') {
                    msg = data.desc;
                }
                alert(msg);
            }
        }, 'json');
    }
}

function sendCancleBitcoin(type, idx, btc, krw) {

    var question = "주문을 취소하시겠습니까?\n";
    if(type=='T') {
        question = "전체 주문을 취소하시겠습니까?";
    } else {
        question += "BTC: "+btc+", KRW: "+krw;
    }

    var answer = confirm(question);

    if(answer) {
        $.post("/order/order_cancle.php?type="+type, 'idx='+idx, function(data){
            if(typeof data != typeof undefined && data.result) {
                alert('취소 했습니다.');
                location.reload();
            } else {
                var msg = '취소 하지 못했습니다.';
                if(typeof data != typeof undefined) {

                    if(data.msg=='err_sess') {
                        msg += " 로그인을 다시 해주세요.";
                    }
                    if(data.desc) {
                        msg = data.desc;
                    }
                }
                alert(msg);
            }
        }, 'json');
    }
}

jQuery(function ($) {
// get symbol
    var symbol = location.href.replace(/.*trade\//, '').toUpperCase(), // current page symbol
        quote_unit = 0.001 //quote unit
    ; // /trade/jin
    if (!symbol) return;
    $('[datatype=symbol]').text(symbol.toUpperCase());
});

// 호가 테이블에서 Price 클릭 시 Buy/Sell 데이터 반영
function handlePrice(type, price) {
    /*if('buy' === type) {
        $('#bitcoin_sell_price').val(parseFloat(price));
        $("#middleDiv").find(".tabs-container:first").find(".nav").find("li").each(function(e) {
            $(this).removeClass("active");
            if($(this).find("a").attr("href") === '#tab-2') {
                $(this).addClass("active");
            }
        });
        $("#middleDiv").find(".tabs-container:first").find(".tab-content").find("div").removeClass("active");
        $("#middleDiv").find(".tabs-container:first").find(".tab-content").find("#tab-2").addClass("active");
        $('#bitcoin_sell_amount').focus();
        /!*$('#bitcoin_sell_amount').val(parseFloat(volume));
        $('#krw_sell_amount').val(parseFloat(price)*parseFloat(volume));*!/
    } else {
        $('#bitcoin_buy_price').val(parseFloat(price));
        $("#middleDiv").find(".tabs-container:first").find(".nav").find("li").each(function(e) {
            $(this).removeClass("active");
            if($(this).find("a").attr("href") === '#tab-1') {
                $(this).addClass("active");
            }
        });
        $("#middleDiv").find(".tabs-container:first").find(".tab-content").find("div").removeClass("active");
        $("#middleDiv").find(".tabs-container:first").find(".tab-content").find("#tab-1").addClass("active");
        $('#bitcoin_buy_amount').focus();
        /!*$('#bitcoin_buy_amount').val(parseFloat(volume));
        $('#krw_buy_amount').val(parseFloat(price)*parseFloat(volume));*!/
    }*/

    // Buy or Sell 해당 Section에 값만 변경하도록 로직 변경 180816
    $("#middleDiv").find(".tabs-container:first").find(".nav").find("li").each(function(e) {
        var currentPrice = $("#price_current").text();
        var priceLen = returnDigit(currentPrice);
        if($(this).find("a").attr("href") === '#tab-1') {
            if($(this).hasClass("active") === true) {
                $('#bitcoin_buy_price').val(returnComma(price.toFixed(priceLen)));
                $('#bitcoin_buy_amount').focus();
            }
        } else {
            if($(this).hasClass("active") === true) {
                $('#bitcoin_sell_price').val(returnComma(price.toFixed(priceLen)));
                $('#bitcoin_sell_amount').focus();
            }
        }
    });
}

function returnDigit(value) {
    var convertVal = 0;
    value = value.toString();
    if(value.indexOf('.') !== -1) {
        var down = value.substring(value.indexOf('.'), value.length);
        convertVal = down.length-1;
    } else {
        convertVal = 0;
    }
    return convertVal;
}

function returnComma(value) {
    var convertVal = "";
    value = value.toString();
    if(value.indexOf('.') !== -1) {
        var upper = value.substring(0, value.indexOf('.'));
        var down = value.substring(value.indexOf('.'), value.length);
        upper = upper.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        convertVal = upper.concat(down);
    } else {
        convertVal = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    return convertVal;
}

function validateAddress(address, unit) {
    var result = true;
    if (!WAValidator.validate(address, unit)) {
        result = false;
    }
    return result;
}

function showModal() {
    $("#chartModal").modal("show");
    /*$("#middleDiv").find(".tabs-container").eq(1).find(".nav").find("li").eq(0).removeClass("active");
    $("#middleDiv").find(".tabs-container").eq(1).find(".nav").find("li").eq(1).addClass("active");
    $("#tab-3").removeClass("active");
    $("#tab-4").addClass("active");*/
    $("#tradeOpen").click();
}

function fn_show_toast(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: false,
        showMethod: 'slideDown',
        positionClass: "toast-top-center",
        timeOut: 4000
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

if (!Object.is) {
    Object.is = function(x, y) {
        // SameValue algorithm
        if (x === y) { // Steps 1-5, 7-10
            // Steps 6.b-6.e: +0 != -0
            return x !== 0 || 1 / x === 1 / y;
        } else {
            // Step 6.a: NaN == NaN
            return x !== x && y !== y;
        }
    };
}