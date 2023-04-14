
// add trim method to String object
if (!String.prototype.trim) {
	String.prototype.trim = function() {
		return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
	};
}

jQuery(function($) {

    // get symbol
    /*var symbol = location.href.replace(/.*trade\//, '').toUpperCase(), // current page symbol
        exchange = 'USD',
        quote_unit = 0.001 //quote unit
        ;
    if (!symbol) return;
    $('[datatype=symbol]').text(symbol.toUpperCase());
    $('input[datatype=symbol]').val(symbol.toUpperCase());
    $('[datatype=exchange]').text(exchange.toUpperCase());
    $('input[datatype=exchange]').val(exchange.toUpperCase());*/

    // get symbol information
    /*var price = {};
    $.getJSON('/api/v1.0/getSpotPrice?symbol=' + symbol, function (r) {
        if (r && r.success) {
            for (p in r.payload) {
                var _item = r.payload[p];
                price[_item.symbol] = _item;
            }
            var _curr_price = price[symbol] ? price[symbol] : {
                'price_close':'-',
                'price_open':'-',
                'price_high':'-',
                'price_low':'-',
                'volume':'-',
                'price_change':'-',
                'price_change_rate':'-'
            };
            $('[datatype=price_close_24]').text(_curr_price.price_close);
            $('[datatype=price_close_24]').val(_curr_price.price_close);
            $('[datatype=price_open_24]').text(_curr_price.price_open);
            $('[datatype=price_high_24]').text(_curr_price.price_high);
            $('[datatype=price_low_24]').text(_curr_price.price_low);
            $('[datatype=volume_24]').text(_curr_price.volume);
            $('[datatype=price_change_24]').text(_curr_price.price_change);
            $('[datatype=price_change_rate_24]').text(_curr_price.price_change_rate);
            quote_unit = get_quote_unit(_curr_price.price_close);
            $('[datatype=quote_unit]').text(quote_unit);
        }
    });*/

    // get currency info
    /*var currency = {}, curr_currency = {};
    $.getJSON('/api/v1.0/getCurrency?symbol=' + symbol, function (r) {
        if (r && r.success) {
            for (p in r.payload) {
                var _item = r.payload[p];
                currency[_item.symbol] = _item;
            }
            curr_currency = currency[symbol] ? currency[symbol] : {
                'symbol':'',
                'name':'',
                'fee_in':0,
                'tax_in_ratio':0,
                'fee_out':0,
                'tax_out_ratio':0,
                'fee_buy_ratio':0,
                'tax_buy_ratio':0,
                'fee_sell_ratio':0,
                'tax_sell_ratio':0,
                'tax_income_ratio':0,
                'trade_min_volume':0,
                'trade_max_volume':0,
                'display_decimals':2,
                'regdate':''
            };
            $('[datatype=currency_name]').text(curr_currency.name);
            // $('[datatype=trade_min_volume]').text(curr_currency.trade_min_volume);
            $('[datatype=trade_min_volume]').val(curr_currency.trade_min_volume);
        }
    });*/

    // 호가 가져오기
    /*var quote_list = [], quote_total = { 'volume_sell': 0, 'volume_buy':0};
    $.getJSON('/api/v1.0/getQuoteList?symbol=' + symbol, function (r) {
        if (r && r.success) {
            quote_list = r.payload;
            var _buy = [], _sell = [];
            for (l in quote_list) {
                var item = quote_list[l];
                if (item.trading_type == 'sell') {
                    _sell.push(item);
                }
                if (item.trading_type == 'buy') {
                    _buy.push(item);
                }
            }
            _sell.sort(function (a, b) {
                return a.price - b.price;
            });
            var orderbookdepth = 0;
            for(i in _sell) {
                var item = _sell[i];
                orderbookdepth += (item.volume*1);
                _sell[i].orderbookdepth = orderbookdepth;
            }
            _sell = _sell.reverse();
            quote_total.volume_sell = orderbookdepth;
            
            
            _buy.sort(function (a, b) {
                return b.price - a.price;
            });
            orderbookdepth = 0;
            for(i in _buy) {
                var item = _buy[i];
                orderbookdepth += (item.volume*1);
                _buy[i].orderbookdepth = orderbookdepth;
            }
            quote_total.volume_buy = orderbookdepth;
            quote_list = _sell.concat(_buy);
        }
        // order book depth

        // html 
        var _html = [], _tr = '<tr><td></td><td class="text-right text-{color}" class="border-book-depth-wrap" style="position:relative;padding-left:2em"><div class="border-book-depth bg-{color}" style="background-color:{color};border-left:1px solid black;position: absolute;right: 0;height: 100%;top: 0;opacity: .3;width: {orderbookdepthpercent}%;"> </div></td><td class="cs_{index_} text-center font-bold text-{color}">{price}</td><td class="text-right cs_{index_} bgs_{index_} text-{color}" style="padding-right:5%">{volume}</td></tr>';
        for (l in quote_list) {
            var item = quote_list[l], _color = item.trading_type == 'buy' ? 'green' : 'red', _orderbookdepthpercent = item.trading_type == 'buy' ? (item.orderbookdepth/quote_total.volume_buy*100).toFixed() : (item.orderbookdepth/quote_total.volume_sell*100).toFixed();
            _html.push(_tr.replace(/{volume}/g, (item.volume * 1).toFixed(4)).replace(/{color}/g, _color).replace(/{orderbookdepthpercent}/g, _orderbookdepthpercent).replace(/{price}/g, (item.price * 1).toFixed(4)).replace(/{orderbookdepth}/g, (item.orderbookdepth * 1).toFixed(4)));
        }
        if(_html.length<1) {
            _html.push('<tr><td colspan="4" class="text-center">There is no order.</td></tr>');
        }
        // 호가 설정.
        $("table[datatype=table_quote_price] tbody").empty().html(_html.join(''));

    });*/


    // 거래내역 가져오기
    /*var trading_list = [];
    $.getJSON('/api/v1.0/getTradingList?symbol=' + symbol, function (r) {
        if (r && r.success) {
            trading_list = r.payload;
        }

        // html 
        var _html = [], _tr = '<tr><td class="text-center"><span class="tnormal">{date_str}</span> <span class="tbold">{time_str}</span></td><td class="text-center"><span class="tbold">{symbol}</span></td><td class="text-right">{price}</td><td class="text-right">{volume}</td></tr>', _pp = '';

        for (l in trading_list) {
            var item = trading_list[l], _pp = item.price,  _color = _pp == item.price ? 'black' : (_pp > item.price ? 'green' : 'red'), _date_str = item.time_traded ? item.time_traded.substr(0,10):'', _time_str = item.time_traded ? item.time_traded.substr(11,5):'';
            _html.push(_tr
                .replace(/{volume}/g, (item.volume * 1).toFixed(4))
                .replace(/{color}/g, _color)
                .replace(/{symbol}/g, item.symbol)
                .replace(/{price}/g, (item.price * 1).toFixed(4))
                .replace(/{date_str}/g, _date_str)
                .replace(/{time_str}/g, _time_str));
        }
        if(_html.length<1) {
            _html.push('<tr><td colspan="4" class="pcenter text-center">There is no trade history.</td></tr>');
        }
        // 호가 설정.
        $("table[datatype=table_trading_list] tbody").empty().html(_html.join(''));

    });*/

    // calculate quote price unit
    /*var get_quote_unit = function (price, countrycode) {
        price = price ? price : '0';
        countrycode = countrycode ? countrycode : 'us';
        switch (countrycode) {
            case 'us':
                if (price >= 1) {
                    return 0.01;
                } else {
                    return 0.001;
                }
                break;
            case 'sg':
                if (price >= 10) {
                    return 0.02;
                } else if (price < 10 && price > 1) {
                    return 0.01;
                } else if (price <= 1) {
                    return 0.005;
                }
                break;
            case 'jp':
                if (price > 50000000) {
                    return 100000;
                } else if (price <= 50000000 && price > 30000000) {
                    return 50000;
                } else if (price <= 30000000 && price > 5000000) {
                    return 10000;
                } else if (price <= 5000000 && price > 3000000) {
                    return 5000;
                } else if (price <= 3000000 && price > 500000) {
                    return 1000;
                } else if (price <= 500000 && price > 300000) {
                    return 500;
                } else if (price <= 300000 && price > 50000) {
                    return 100;
                } else if (price <= 50000 && price > 30000) {
                    return 50;
                } else if (price <= 30000 && price > 5000) {
                    return 10;
                } else if (price <= 5000 && price > 3000) {
                    return 5;
                } else if (price <= 3000) {
                    return 1;
                }
                break;
            case 'kr':
                if (price >= 500000) {
                    return 1000;
                } else if (price < 500000 && price >= 100000) {
                    return 500;
                } else if (price < 100000 && price >= 50000) {
                    return 100;
                } else if (price < 50000 && price >= 10000) {
                    return 50;
                } else if (price < 10000 && price >= 5000) {
                    return 10;
                } else if (price < 5000 && price >= 1000) {
                    return 5;
                } else if (price < 1000) {
                    return 1;
                }
                break;
        }

    }*/

    // check out range price - If true, stop trading.
    /*var check_out_range = function (symbol, type, price) {
        var _c_price = price[symbol].price_close,
            _d = Math.floor((price - _c_price) / _c_price * 100);
        return _d > 7 ? true : false;
    }*/


    // 타 마켓 가격 가져오기
    // 작업필요함.

    // 회원 지갑 정보 
    /*var wallet = {}, curr_wallet={};
    $.getJSON('/api/v1.0/getBalance?symbol=all', function (r) {
        if (r && r.success) {
            for (i in r.payload) {
                var _w = r.payload[i];
                if (typeof _w != typeof undefined) {
                    wallet[_w.symbol.toLowerCase()] = _w;
                }
                if (symbol == _w.symbol) {
                    curr_wallet = _w;
                }
            }
            $('[datatype=usd_confirmed]').text(addComma(wallet.usd.confirmed*1));
            console.log('curr_wallet:', curr_wallet);
        }
    });*/



    //구매 
    /*$('#btn_buy').on('click', function(event){
        event.preventDefault();event.stopPropagation();event.stopImmediatePropagation();
        var _buy_price = $('#bitcoin_buy_price').val() * 1,
            _buy_quantity = $('#bitcoin_buy_amount').val() * 1,
            _min_quantity = curr_currency.trade_min_volume * 1,
            _buy_amount = _buy_price * _buy_quantity
        ;

        if( _buy_price == 0 ) {
            alert('Please enter your order price.');
            $('#bitcoin_buy_price').focus();
            return false;
        }
    
        if( _buy_quantity == 0 ) {
            alert('Please input quantity.');
            $('#bitcoin_buy_amount').focus();
            return false;
        }
    
        if( _buy_quantity < _min_quantity ) {
            alert("Please enter larger than the minimum order quantity.");
            $('#bitcoin_buy_amount').focus();
            return false;
        }

        // check usd balance
        if(wallet[exchange].confirmed < _buy_amount) {
            alert("There is not enough balance. Please check the quantity.");
            $('#bitcoin_buy_amount').focus();
            return false;
        }
    
        $.post("/api/v1.0/buy", $( "#buy_bitcoin_form" ).serialize(), function(data){
            if(typeof data != typeof undefined && data.result) {
                alert('Buy request completed.');
                // 데이터 갱신. - 현재가, 호가, 거래내역, 내 주문내역 ...
            } else {
                var msg = 'Could not complete buy request.';
                if(typeof data!= typeof undefined && data.desc!='') {
                    msg = data.desc;
                }
                alert(msg);
            }
        }, 'json');
    });*/

    var menuRight = document.getElementById('cbp-spmenu-s2'),
        body = document.body;

    var openYn = false;

    $("#hideNav").hide();

    if (typeof showRight != typeof undefined) {
        showRight.onclick = function() {
            $(this).toggleClass('active');
            $(menuRight).toggleClass('cbp-spmenu-open');
            openYn = true;
            $("#hideNav").show();
        };
    }

    $("#closeMenu").on('click', function(event) {
        event.preventDefault();
        if (openYn) {
			$(menuRight).toggleClass('cbp-spmenu-open');
            $("#hideNav").hide();
        }
    });

    $("#hideNav").on('click', function(event) {
        event.preventDefault();
        if (openYn) {
			$(menuRight).toggleClass('cbp-spmenu-open');
            $("#hideNav").hide();
        }
    });

    $(".nav_menu").find(".page-scroll").on('click', function(event) {
        event.preventDefault();
        if (openYn) {
			$(menuRight).toggleClass('cbp-spmenu-open');
            $("#hideNav").hide();
            location.href = 'http://' + location.host + $(this).attr("href");
        }
    });

    $(".nav_menu").find(".subMenu_m_coin").on('click', function(event) {
        event.preventDefault();
        if (openYn) {
			$(menuRight).toggleClass('cbp-spmenu-open');
            $("#hideNav").hide();
            location.href = 'http://' + location.host + $(this).attr("href");
        }
    });

    $(".downApp").on('click', function(event) {
        event.preventDefault();
        var ua = navigator.userAgent;

        if (ua.match(/iPhone|iPad|iPod/i)) {
            location.href = "/";
        } else {
            location.href = "/";
        }

    });

    $('.asc_twitter').click(function(e) {
        e.preventDefault();
    });

    $('.asc_facebook').click(function(e) {
        e.preventDefault();
    });


});