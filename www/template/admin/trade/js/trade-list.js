/**
 * 팝니다/삽니다 리스트 방식의 매매 화면에서 사용하는 trade 스크립트입니다.
 */
$(function () {
    var start_get_date = '';
    var end_get_date = '';

    start_get_date = $('input[name=start_date]').val();
    end_get_date = $('input[name=end_date]').val();

    if(start_get_date) {
        $('#reportrange span').html(start_get_date + ' - ' + end_get_date);
    } else {
        start_get_date = moment().format('YYYY-MM-DD');
        $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    };

    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: start_get_date,
        endDate: moment(),
        minDate: '2018-04-01',
        maxDate: '2021-12-31',
        dateLimit: { days: 90 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
            'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
            'Last 60 Days': [moment().subtract(60, 'days'), moment().subtract(1, 'days')],
            'Last 90 Days': [moment().subtract(90, 'days'), moment().subtract(1, 'days')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        opens: 'right',
        drops: 'down',
        buttonClasses: ['btn', 'btn-sm'],
        applyClass: 'btn-primary',
        cancelClass: 'btn-default',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
            monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            firstDay: 1
        }
    }, function(start, end, label) {
        // console.log(start.toISOString(), end.toISOString(), label);

        var start_date = '';
        var end_date = '';
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');

        $('input[name=start_date]').val(start_date);
        $('input[name=end_date]').val(end_date);

        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

    });

    let symbol = $('input[name=symbol]').val();
    let user_no = $('input[name=user_no]').val();

    // 팝니다.
    $('body').on('click', '.sellstock', function(e){
        
        e.preventDefault(); // The flicker is a codepen thing

        if (!user_no) { 
            alert('로그인을 해 주세요.'); 
            location.href = "/login";
            return false;
        }
        
        let sell_price = $('[name=sell_price]').val();
        if (sell_price<=0) { alert('매도가격을 입력해주세요.'); $('[name=sell_price]').focus(); return false;}
        let sell_volume = $('[name=sell_volume]').val();
        if (sell_volume<=0) { alert('매도수량을 입력해주세요.'); $('[name=sell_volume]').focus(); return false;}
        
        let d_sell_price = sell_price.replace(/[^0-9.]/g, '');
        let d_sell_volume = sell_volume.replace(/[^0-9.]/g, '');
        
        const data = { 'token': getCookie('token'), 'symbol': symbol, 'exchange': 'KRW', 'price': d_sell_price, 'volume': d_sell_volume };
        // console.log(data); //return;
        $.post(API_URL+'/sell/', data, function(r){
            if (r && r.success) {
                if (r.payload && r.payload.price) {
                    alert(real_number_format(r.payload.price) + '원에 '+real_number_format(r.payload.volume)+'주를 판매했습니다.');
                } else {
                    alert('판매 주문이 추가 되었습니다.');
                }
                $('#sellStock').modal('hide');
                window.location.reload();
            } else {
                let msg = r.error && r.error.message ? r.error.message : '';
                alert('판매 주문이 처리되지 않았습니다. '+msg);
            }
        }, 'json');

    });

    // 차트가 보인적이 있는지 확인하기 - 차트가 안보일때 그리면 width가 너무 작게 그려져서 다시 그려야 해서 보인적이 있을때 그렸는지 확인합니다.
    let stock_chart_visble = false;
    if ($('#box_chart').is(':visible')) {
        stock_chart_visble = true;
    }

    // 차트 보이기/숨기기 버튼 추가
    $('body').on('click', '#btnShowChart', function (e) {
        $('#box_chart').toggle();
        // if($('#box_chart').is(':visible')) window.stock_line_chart();
        if (!stock_chart_visble) {
            window.stock_line_chart();
            stock_chart_visble = true;
        }
    });
    // 화면이 커지면 차트 항상 보이도록 수정
    $(window).on('resize', function () { 
        const w = $(document).outerWidth();
        $('#btnShowChart').hide();
        if (w > 983 && !$('#box_chart').is(':visible')) {
            $('#box_chart').show();
        }
        if (!stock_chart_visble) {
            window.stock_line_chart();
            stock_chart_visble = true;
        }
    });

    // 삽니다.
    $('body').on('click', '.buystock', function(e){
        
        e.preventDefault(); // The flicker is a codepen thing

        if (!user_no) { 
            alert('로그인을 해 주세요.'); 
            location.href = "/login";
            return false;
        }
        
        let buy_price = $('[name=buy_price]').val();
        if (!buy_price) { alert('매수가격을 입력해주세요.'); $('[name=buy_price]').focus(); return false;}
        let buy_volume = $('[name=buy_volume]').val();
        if (!buy_volume) { alert('매수수량을 입력해주세요.'); $('[name=buy_volume]').focus(); return false;}
        
        let d_buy_price = buy_price.replace(/[^0-9.]/g, '');
        let d_buy_volume = buy_volume.replace(/[^0-9.]/g, '');
        let d_buy_amount = real_number_format(d_buy_price * d_buy_volume, 4).replace(/[^0-9.]/g,'');

        const data = { 'token': getCookie('token'), 'symbol': symbol, 'exchange': 'KRW', 'price': d_buy_price, 'volume': d_buy_volume, 'amount': d_buy_amount };
        // console.log(data); //return;
        $.post(API_URL+'/buy/', data, function(r){
            if(r && r.success) {
                if (r.payload && r.payload.price) {
                    alert(real_number_format(r.payload.price) + '원에 '+real_number_format(r.payload.volume)+'주를 구매했습니다.');
                } else {
                    alert('구매 주문이 추가 되었습니다.');
                }
                $('#buyStock').modal('hide');
                window.location.reload();
            } else {
                let msg = r.error && r.error.message ? r.error.message : '';
                alert('구매 주문이 처리되지 않았습니다. '+msg);
            }
        }, 'json');

    });


    let company_Info = function() {
        let $target = $('[name=company_info]');
        let $loading = $('[name=loading]', $target);
        let $empty = $('[name=empty]', $target);
        $loading.show();
        $empty.hide();
        $target.find('[name=data]').remove();
        $.post('//api.'+(window.location.host.replace('www.',''))+'/v1.0/getSpotPrice/', {'token':getCookie('token'), 'symbol':symbol}, function(r){
            if(r && r.success) {
                // set total value
                // set coin value
                if(r.payload) {
                    let $tpl = $('<div></div>').append($('[name=tpl]', $target).clone().attr('name','data').show());
                    let c = r.payload;
                    if(c) {
                        let html = [];
                        for(s in c) {
                            let _tpl = $tpl.html();
                            let t = c[s];
                            let logo = t.icon_url ? t.icon_url : "/img/favicon.png";
                            let gap = t.price_open - t.price_close;
                            html.push( _tpl
                                .replace(/\{spot.symbol\}/g,t.symbol.toLowerCase())
                                .replace(/\{spot.icon_url\}/g,logo)
                                .replace(/\{spot.SYMBOL\}/g,t.symbol)
                                .replace(/\{spot.name\}/g,t.name)
                                .replace(/\{spot.user_no\}/g,user_no)
                                .replace(/\{spot.price_close\}/g,real_number_format(t.price_close))
                                .replace(/\{spot.gap\}/g,real_number_format(gap))
                                .replace(/\{spot.market_cap\}/g,real_number_format(t.market_cap))
                            );
                        }
                        $target.append(html.join(''));
                    } else {
                        $empty.show();
                    }
                    $loading.hide();
                }
            }
        },'json');
    };
    company_Info();

    let list_direct_sell = function (page) {
        if (!symbol) return;
        let $target = $('[name=direct_sell]');
        let $loading = $('[name=loading]', $target);
        let $empty = $('[name=empty]', $target);
        page = page > 1 ? page : 1;
        $loading.show();
        $empty.hide();
        $target.find('[name=data]').remove();
        $.post('//api.'+(window.location.host.replace('www.',''))+'/v1.0/getOrderList/', {'token':getCookie('token'), 'symbol':symbol, 'trading_type':'sell','status':'unclose','rows':30, 'page':page}, function(r){
            if(r && r.success) {
                // set total value
                if(r.payload) {
                    let t = r.payload.length;
                    for(i in t) {
                        $('[name="total.'+i+'"]').text(real_number_format(t[i]));
                    }
                    $('.npn').removeClass('positive').removeClass('negative').addClass( t.investment_income>0 ?  'positive':(t.investment_income<0 ?  'negative':''));
                }
                // set coin value
                if(r.payload) {
                    let $tpl = $('<div></div>').append($('[name=tpl]', $target).clone().attr('name','data').show());
                    let c = r.payload;
                    if(c && c.length>0) {
                        let html = [];
                        for(s in c) {
                            let _tpl = $tpl.html();
                            let t = c[s];
                            html.push( _tpl
                                .replace(/\{sell.symbol\}/g,t.symbol.toLowerCase())
                                .replace(/\{sell.SYMBOL\}/g,t.symbol)
                                .replace(/\{sell.time_order\}/g,moment().format('YYYY-MM-DD hh:mm:ss',t.time_order))
                                .replace(/\{sell.orderid\}/g,t.orderid)
                                .replace(/\{sell.userno\}/g,t.userno)
                                .replace(/\{sell.amount\}/g,real_number_format(t.amount))
                                .replace(/\{sell.exchange\}/g,t.exchange)
                                .replace(/\{sell.price\}/g,real_number_format(t.price))
                                .replace(/\{sell.tot_cnt\}/g,real_number_format(t.tot_cnt))
                                .replace(/\{sell.trading_type\}/g,t.trading_type)
                                .replace(/\{sell.volume\}/g,real_number_format(t.volume))
                                .replace(/\{sell.volume_remain\}/g,real_number_format(t.volume_remain))
                                .replace(/\{sell.my_order\}/g, t.my_order )
                                .replace(/\{sell.hide_trade_btn\}/g, t.my_order=='Y' ? 'hide' : '' )
                                .replace(/\{sell.hide_cancel_btn\}/g, t.my_order=='Y' ? '' : 'hide' )
                            );
                        }
                        $target.append(html.join(''));
                    } else {
                        $empty.show();
                    }
                    $loading.hide();
                }
            }
        },'json');
    };
    list_direct_sell();

    // $('#srchform').submit(function() {
    //     if($('*').is('select[name=loop_scale]')) {
    //         var loop_scale = $('select[name=loop_scale]').val();
    //         $('input[name=loop_scale]').val(loop_scale);
    //     }
    // });
    $('[name="btn-sell-search"]').on('click', function(){
        list_direct_sell();
    });

    // list_direct_sell 에서는  buy_direct 콜
    // - validate parameters -
    // $orderid // 주문번호
    // $symbol // 코인
    // $exchange // 구매 화폐
    // $price // 가격
    // $volume // 구매량
    // $amount // 구매금액
    // $userno // 판매자

    let buy_direct = function() {
        let seller_no = $('#btn_do_direct_buy').attr('data-userno');
        let orderid = $('#btn_do_direct_buy').attr('data-orderid');
        if (!orderid) { alert('주문번호를 확인해주세요.'); return false;}
        let direct_buy_price = $('#direct_buy_price').val();
        if (!direct_buy_price) { alert('구매가격을 입력해주세요.'); $('#direct_buy_price').focus(); return false;}
        let direct_buy_volume = $('#direct_buy_volume').val();
        if (!direct_buy_volume) { alert('구매수량을 입력해주세요.'); $('#direct_buy_volume').focus(); return false; }
        const max_buy_volume = $('#direct_buy_volume').attr('placeholder').replace(/[^0-9.]/g, '');
        if (direct_buy_volume * 1 > max_buy_volume * 1) {
            alert('구매가능수량('+max_buy_volume+') 이하로 입력해주세요.'); return false;
        }
        
        let d_buy_price = direct_buy_price.replace(/[^0-9.]/g, '');
        let d_buy_volume = direct_buy_volume.replace(/[^0-9.]/g, '');
        let d_buy_amount = real_number_format(d_buy_price * d_buy_volume, 4).replace(/[^0-9.]/g,'');

        const data = {'token':getCookie('token'), 'orderid':orderid, 'symbol':symbol, 'price':d_buy_price, 'volume':d_buy_volume, 'amount':d_buy_amount,  'seller_no':seller_no } // , 'exchange':'KRW'
        $.post(API_URL + '/buy_direct/', data, function (r) {
            if (r && r.success) {
                alert(real_number_format(r.payload.price) + '원에 '+real_number_format(r.payload.volume)+'주를 구매했습니다.');
                $('#direct_buy_Modal').modal('hide');
                list_direct_sell();
                window.location.reload();
            } else {
                let msg = r.error && r.error.message ? r.error.message : '';
                alert('구매하지 못했습니다. '+msg);
            }
        }, 'json');

    }

    // 바로체결(구매)
    $('body').on('click', '.btn_direct_buy', function(e){
        
        e.preventDefault(); // The flicker is a codepen thing
        let $self = $(this),
            orderid = $self.attr('data-orderid'),
            seller_no = $self.attr('data-userno'),
            buy_price = $self.attr('data-price'),
            buy_volume = $self.attr('data-volume'),
            html_box_trade_price_range = $('<div></div>').append($('[name="box_trade_price_range"]:eq(0)').clone()).html();
            buy_modal_box = '<div class="modal fade" id="direct_buy_Modal" tabindex="-1" role="dialog" aria-labelledby="direct_buy_ModalLabel">\
            <div class="modal-dialog" role="document">\
            <div class="modal-content">\
                <div class="modal-header">\
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                    <h4 class="modal-title" id="direct_buy_ModalLabel">바로체결 : 구매하기</h4>\
                </div>\
                <div class="modal-body">\
                    <input type="hidden" name="orderid" id="orderid" value="'+orderid+'" />\
                    <div class="direct_buy_price">\
                        <div class="entry">가격</div>\
                        <div class="data"><input type="text" name="direct_buy_price" id="direct_buy_price" value="'+buy_price+'" disabled="" />'+html_box_trade_price_range+'</div>\
                    </div>\
                    <div class="direct_buy_volume">\
                    <div class="entry">구매수량</div>\
                    <div class="data"><input type="text" name="direct_buy_volume" id="direct_buy_volume" placeholder="'+buy_volume+'" value="'+buy_volume+'" /></div>\
                    </div>\
                    <div class="direct_buy_amount">\
                        <div class="entry">구매금액</div>\
                        <div class="data"><input type="text" name="direct_buy_amount" id="direct_buy_amount" placeholder="" value="'+real_number_format(buy_price.replace(/[^0-9.]/g,'')*buy_volume.replace(/[^0-9.]/g,''))+'" disabled="" /></div>\
                    </div>\
                </div>\
                <div class="modal-footer">\
                    <button type="button" class="btn btn-primary" id="btn_do_direct_buy" data-userno=""  data-price="">구매하기</button>\
                </div>\
            </div>\
            </div>\
        </div>';

        if ($('#direct_buy_Modal').length ) {
            $('#direct_buy_Modal').remove();
        }
        $('body').append(buy_modal_box);
        $('#btn_do_direct_buy').on('click', buy_direct);
        $('#btn_do_direct_buy').attr({'data-orderid':orderid,'data-userno':seller_no,'data-price':buy_price,'data-volume':buy_volume});
        $('#direct_buy_Modal').modal().on('shown.bs.modal', function(e){
            // $('#reject-reason').val($('[name=reject_msg]:last').text()).focus();
            // $('[name=reject_msg]:last').click();
        }).on('hidden.bs.modal', function(e){
            $('#orderid').val('');
            $('#direct_buy_price').val('');
            $('#direct_buy_volume').val('');
            $('#direct_buy_amount').val('');
            $('#btn_do_direct_buy').attr({'data-orderid':'','data-userno':'','data-price':'','data-volume':''});
        });
    });

    $('body').on('keyup', '#direct_buy_volume', function (e) {
        const buy_price = $('#direct_buy_price').val().replace(/[^0-9.]/g, '');
        let buy_volume = $(this).val().replace(/[^0-9.]/g, '');
        const max_buy_volume = $(this).attr('placeholder').replace(/[^0-9.]/g, '');
        buy_volume = buy_volume*1 < max_buy_volume*1 ? buy_volume : max_buy_volume;
        if (buy_volume * 1 > max_buy_volumee * 1) {
            alert('구매가능수량('+max_buy_volume+') 이하로 입력해주세요.'); return false;
        }
        // $(this).val( real_number_format(buy_volume) );
        const buy_amount = buy_price * buy_volume;
        $('#direct_buy_amount').val(real_number_format(buy_amount, 4));
    });



    let list_direct_buy = function (page) {
        if (!symbol) return;
        let $target = $('[name=direct_buy]');
        let $loading = $('[name=loading]', $target);
        let $empty = $('[name=empty]', $target);
        page = page > 1 ? page : 1;
        $loading.show();
        $empty.hide();
        $target.find('[name=data]').remove();
        $.post('//api.'+(window.location.host.replace('www.',''))+'/v1.0/getOrderList/', {'token':getCookie('token'), 'symbol':symbol, 'trading_type':'buy','status':'unclose','rows':30, 'page':page}, function(r){
            if(r && r.success) {
                // set total value
                if(r.payload) {
                    let t = r.payload.length;
                    for(i in t) {
                        $('[name="total.'+i+'"]').text(real_number_format(t[i]));
                    }
                    $('.npn').removeClass('positive').removeClass('negative').addClass( t.investment_income>0 ?  'positive':(t.investment_income<0 ?  'negative':''));
                }
                // console.log(r.payload);
                // set coin value
                if(r.payload) {
                    let $tpl = $('<div></div>').append($('[name=tpl]', $target).clone().attr('name','data').show());
                    let c = r.payload;
                    if(c && c.length>0) {
                        let html = [];
                        for(s in c) {
                            let _tpl = $tpl.html();
                            let t = c[s];
                            html.push( _tpl
                                .replace(/\{buy.symbol\}/g,t.symbol.toLowerCase())
                                .replace(/\{buy.SYMBOL\}/g,t.symbol)
                                .replace(/\{buy.time_order\}/g,moment().format('YYYY-MM-DD hh:mm:ss',t.time_order))
                                .replace(/\{buy.orderid\}/g,t.orderid)
                                .replace(/\{buy.userno\}/g,t.userno)
                                .replace(/\{buy.amount\}/g,real_number_format(t.amount))
                                .replace(/\{buy.exchange\}/g,t.exchange)
                                .replace(/\{buy.price\}/g,real_number_format(t.price))
                                .replace(/\{buy.tot_cnt\}/g,real_number_format(t.tot_cnt))
                                .replace(/\{buy.trading_type\}/g,t.trading_type)
                                .replace(/\{buy.volume\}/g,real_number_format(t.volume))
                                .replace(/\{buy.volume_remain\}/g,real_number_format(t.volume_remain))
                                .replace(/\{buy.my_order\}/g, t.my_order )
                                .replace(/\{buy.hide_trade_btn\}/g, t.my_order=='Y' ? 'hide' : '' )
                                .replace(/\{buy.hide_cancel_btn\}/g, t.my_order=='Y' ? '' : 'hide' )
                            );
                        }
                        $target.append(html.join(''));
                    } else {
                        $empty.show();
                    }
                    $loading.hide();
                }
            }
        },'json');
    };
    list_direct_buy();

    // $('#srchform').submit(function() {
    //     if($('*').is('select[name=loop_scale]')) {
    //         var loop_scale = $('select[name=loop_scale]').val();
    //         $('input[name=loop_scale]').val(loop_scale);
    //     }
    // });
    $('[name="btn-buy-search"]').on('click', function(){
        list_direct_buy();
    });


    // list_direct_buy 에서는  sell_direct 콜
    // - validate parameters -
    // $orderid // 주문번호
    // $symbol // 코인
    // $exchange // 구매 화폐
    // $price // 가격
    // $volume // 구매량
    // $amount // 구매금액
    // $userno // 구매자

    let sell_direct = function() {
        let buyer_no = $('#btn_do_direct_sell').attr('data-userno');
        let orderid = $('#btn_do_direct_sell').attr('data-orderid');
        if (!orderid) { alert('주문번호를 확인해주세요.'); return false;}
        let direct_sell_price = $('#direct_sell_price').val();
        if (!direct_sell_price) { alert('판매가격을 입력해주세요.'); $('#direct_sell_price').focus(); return false;}
        let direct_sell_volume = $('#direct_sell_volume').val();
        if (direct_sell_volume<=0) { alert('판매수량을 입력해주세요.'); $('#direct_sell_volume').focus(); return false;}
        const max_sell_volume = $('#btn_do_direct_sell').attr('data-volume').replace(/[^0-9.]/g, '');
        if (direct_sell_volume * 1 > max_sell_volume * 1) {
            alert('판매가능수량('+max_sell_volume+') 이하로 입력해주세요.'); return false;
        }
        
        let d_sell_price = direct_sell_price.replace(/[^0-9.]/g, '');
        let d_sell_volume = direct_sell_volume.replace(/[^0-9.]/g, '');
        let d_sell_amount = real_number_format(d_sell_price * d_sell_volume, 4).replace(/[^0-9.]/g,'');

        const data = { 'token': getCookie('token'), 'orderid': orderid, 'symbol': symbol, 'price': d_sell_price, 'volume': d_sell_volume, 'amount': d_sell_amount, 'buyer_no': buyer_no }; // , 'exchange': 'KRW'
        // console.log(data); //return;
        $.post(API_URL+'/sell_direct/', data, function(r){
            if (r && r.success) {
                alert(real_number_format(r.payload.price) + '원에 '+real_number_format(r.payload.volume)+'주를 판매했습니다.');
                $('#direct_sell_Modal').modal('hide');
                list_direct_buy();
                window.location.reload();
            } else {
                let msg = r.error && r.error.message ? r.error.message : '';
                alert('판매하지 못했습니다. '+msg);
            }
        }, 'json');

    }

    // 취소
    $('body').on('click', '.btn_cancel', function (e) {
        const orderid = $(this).attr('data-orderid'),
            order_type = $(this).attr('data-order-type'),
            $tr = $(this).closest('tr');
        if (orderid) {
            $.post(API_URL + '/cancel/', { 'token': getCookie('token'), 'orderid':orderid, 'symbol': symbol}, function(r){
                if (r && r.success) {
                    alert('주문을 취소했습니다.');
                    $tr.remove();
                    if (order_type == 'buy') { list_direct_buy(); }
                    else {list_direct_sell();}
                    // window.location.reload();
                } else {
                    let msg = r.error && r.error.message ? r.error.message : '';
                    alert('취소하지 못했습니다. '+msg);
                }
            }, 'json');
        }
    });

    // 바로체결(판매)
    $('body').on('click', '.btn_direct_sell', function(e){
        
        e.preventDefault(); // The flicker is a codepen thing
        let $self = $(this),
            orderid = $self.attr('data-orderid'),
            buyer_no = $self.attr('data-userno'),
            sell_price = $self.attr('data-price'),
            sell_volume = $self.attr('data-volume'),
            html_box_trade_price_range = $('<div></div>').append($('[name="box_trade_price_range"]:eq(0)').clone()).html();
            sell_modal_box = '<div class="modal fade" id="direct_sell_Modal" tabindex="-1" role="dialog" aria-labelledby="direct_sell_ModalLabel">\
            <div class="modal-dialog" role="document">\
            <div class="modal-content">\
                <div class="modal-header">\
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                    <h4 class="modal-title" id="direct_sell_ModalLabel">바로체결 : 판매하기</h4>\
                </div>\
                <div class="modal-body">\
                    <input type="hidden" name="orderid" id="orderid" value="'+orderid+'" />\
                    <div class="direct_sell_price">\
                        <div class="entry">가격</div>\
                        <div class="data"><input type="text" name="direct_sell_price" id="direct_sell_price" value="'+sell_price+'" disabled="" />'+html_box_trade_price_range+'</div>\
                    </div>\
                    <div class="direct_sell_volume">\
                    <div class="entry">판매수량</div>\
                    <div class="data"><input type="text" name="direct_sell_volume" id="direct_sell_volume" placeholder="0" value="'+sell_volume+'" /></div>\
                    </div>\
                    <div class="direct_sell_amount">\
                        <div class="entry">판매금액</div>\
                        <div class="data"><input type="text" name="direct_sell_amount" id="direct_sell_amount" placeholder="0" value="'+real_number_format(sell_price.replace(/[^0-9.]/g,'')*sell_volume.replace(/[^0-9.]/g,''))+'" disabled="" /></div>\
                    </div>\
                </div>\
                <div class="modal-footer">\
                    <button type="button" class="btn btn-danger" id="btn_do_direct_sell" data-userno=""  data-price="">판매하기</button>\
                </div>\
            </div>\
            </div>\
        </div>';
        // 

        if ($('#direct_sell_Modal').length ) {
            $('#direct_sell_Modal').remove();
        }
        $('body').append(sell_modal_box);
        $('#btn_do_direct_sell').on('click', sell_direct);
        $('#btn_do_direct_sell').attr({'data-orderid':orderid,'data-userno':buyer_no,'data-price':sell_price,'data-volume':sell_volume});
        $('#direct_sell_Modal').modal().on('shown.bs.modal', function(e){
            // $('#reject-reason').val($('[name=reject_msg]:last').text()).focus();
            // $('[name=reject_msg]:last').click();
        }).on('hidden.bs.modal', function(e){
            $('#orderid').val('');
            $('#direct_sell_price').val('');
            $('#direct_sell_volume').val('');
            $('#direct_sell_amount').val('');
            $('#btn_do_direct_sell').attr({'data-orderid':'','data-userno':'','data-price':'','data-volume':''});
        });
        
        $('#direct_sell_volume').on('keyup blur', function (e) {
            // console.log('direct_sell_volume keyup, blur')
            const sell_price = $('#direct_sell_price').val().replace(/[^0-9.]/g, '');
            if (sell_price<=0) { alert('가격을 입력해주세요.'); $('#direct_sell_price').focus(); return false;}
            let sell_volume = $(this).val().replace(/[^0-9.]/g, '');
            if (sell_volume <= 0) {
                // alert('판매수량을 입력해주세요.'); $('#direct_sell_volume').focus(); return false;
                sell_volume = 0;
            }
            const max_sell_volume = $('#btn_do_direct_sell').attr('data-volume').replace(/[^0-9.]/g, '');
            sell_volume = sell_volume * 1 < max_sell_volume * 1 ? sell_volume : max_sell_volume;
            if (sell_volume * 1 > max_sell_volume * 1) {
                alert('판매가능수량('+max_sell_volume+') 이하로 입력해주세요.'); return false;
            }
            const sell_amount = sell_price * sell_volume;
            $('#direct_sell_amount').val(real_number_format(sell_amount, 4));
        });
    });

    // $('body').on('keyup', '#direct_sell_volume', function (e) {
    //     const sell_price = $('#direct_sell_price').val().replace(/[^0-9.]/g, '');
    //     let sell_volume = $(this).val().replace(/[^0-9.]/g, '');
    //     const max_sell_volume = $(this).attr('placeholder').replace(/[^0-9.]/g, '');
    //     sell_volume = sell_volume * 1 < max_sell_volume * 1 ? sell_volume : max_sell_volume;
    //     if (sell_volume * 1 > max_sell_volume * 1) {
    //         alert('판매가능수량 이하로 입력해주세요.'+sell_volume+','+max_sell_volume); return false;
    //     }
    //     // $(this).val( real_number_format(sell_volume) );
    //     const sell_amount = sell_price * sell_volume;
    //     $('#direct_sell_amount').val(real_number_format(sell_amount, 4));
    // });



});
