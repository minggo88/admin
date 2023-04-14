/**
 * 
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
        // minDate: '2018-04-01',
        // maxDate: '2021-12-31',
        // dateLimit: { days: 90 },
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

    let search_profit = function(){
        let $target = $('[name=table_profit]');
        let $loading = $('[name=loading]', $target);
        let $empty = $('[name=empty]', $target);
        $loading.show();
        $empty.hide();
        $target.find('[name=data]').remove();
        $.post('//api.'+(window.location.host.replace('www.',''))+'/v1.0/getMyProfit/', {'token':getCookie('token'), 'sdate':$('input[name=start_date]').val(),'edate':$('input[name=end_date]').val()}, function(r){
            if(r && r.success) {
                // set total value
                if(r.payload && r.payload.total) {
                    let t = r.payload.total;
                    for(i in t) {
                        $('[name="total.'+i+'"]').text(real_number_format(t[i]));
                    }
                    $('.npn').removeClass('positive').removeClass('negative').addClass( t.investment_income>0 ?  'positive':(t.investment_income<0 ?  'negative':''));
                }
                // set coin value
                if(r.payload && r.payload.detail) {
                    let $tpl = $('<div></div>').append($('[name=tpl]', $target).clone().attr('name','data').show());
                    let c = r.payload.detail;
                    if(c) {
                        let html = [];
                        for(s in c) {
                            let _tpl = $tpl.html();
                            let t = c[s];
                            console.log(t);
                            html.push( _tpl
                                .replace(/\{coin.symbol\}/g,t.symbol.toLowerCase())
                                .replace(/\{coin.SYMBOL\}/g,t.symbol)
                                .replace(/\{coin.name\}/g,__(t.name))
                                .replace(/\{coin.icon_url\}/g,t.icon_url)
                                .replace(/\{coin.basic_balance\}/g,real_number_format(t.basic_balance))
                                .replace(/\{coin.basic_evaluation_amount\}/g,real_number_format(t.basic_evaluation_amount))
                                .replace(/\{coin.final_balance\}/g,real_number_format(t.final_balance))
                                .replace(/\{coin.final_evaluation_amount\}/g,real_number_format(t.final_evaluation_amount))
                                .replace(/\{coin.deposit_evaluation_amount\}/g,real_number_format(t.deposit_evaluation_amount))
                                .replace(/\{coin.withdraw_evaluation_amount\}/g,real_number_format(t.withdraw_evaluation_amount))
                                .replace(/\{hide_trade_btn\}/g, t.symbol=='KRW' ? 'hide' : '' )
                                .replace(/\{hide_deposit_btn\}/g, t.symbol=='KRW' ? '' : 'hide' )
                                .replace(/\{hide_withdrawal_btn\}/g, t.symbol=='KRW' ? '' : 'hide' )
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
    search_profit();

    // $('#srchform').submit(function() {
    //     if($('*').is('select[name=loop_scale]')) {
    //         var loop_scale = $('select[name=loop_scale]').val();
    //         $('input[name=loop_scale]').val(loop_scale);
    //     }
    // });
    $('[name="btn-search"]').on('click', function(){
        search_profit();
    });

});
