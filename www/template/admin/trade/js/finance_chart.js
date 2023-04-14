$(function () {

    var start_get_created_at = '';
    var end_get_created_at = '';

    start_get_created_at = $('input[name=start_created_at]').val();
    end_get_created_at = $('input[name=end_created_at]').val();

    if (start_get_created_at) {
        $('#reportrange span').html(start_get_created_at + ' - ' + end_get_created_at);
    } else {
        $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    };

    let symbol = $('input[name=symbol]').val();

    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        minDate: '2018-04-01',
        maxDate: '2020-12-31',
        dateLimit: { days: 60 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
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
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            firstDay: 1
        }
    }, function (start, end, label) {
        // console.log(start.toISOString(), end.toISOString(), label);

        var start_created_at = '';
        var end_created_at = '';
        start_created_at = start.format('YYYY-MM-DD');
        end_created_at = end.format('YYYY-MM-DD');

        $('input[name=start_created_at]').val(start_created_at);
        $('input[name=end_created_at]').val(end_created_at);

        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

    });

    $('#srchform').submit(function () {
        if ($('*').is('select[name=loop_scale]')) {
            var loop_scale = $('select[name=loop_scale]').val();
            $('input[name=loop_scale]').val(loop_scale);
        }
    });

    let bar_chart = function(){
        
        let $target1 = $('[name=barChart1]');
        let $loading1 = $('[name=loading1]', $target1);
        let $empty1 = $('[name=empty1]', $target1);
        $loading1.show();
        $empty1.hide();


        let $target2 = $('[name=barChart2]');
        let $loading2 = $('[name=loading2]', $target2);
        let $empty2 = $('[name=empty2]', $target2);
        $loading2.show();
        $empty2.hide();


        let $target3 = $('[name=barChart3]');
        let $loading3 = $('[name=loading3]', $target3);
        let $empty3 = $('[name=empty3]', $target3);
        $loading3.show();
        $empty3.hide();

        $.ajax({
            type: "post",
            dataType: "json",  //xml,html,jeon,jsonp,script,text
            url: '//api.'+(window.location.host.replace('www.',''))+'/v1.0/getTradeItemFinenceInfo/',
            data: {'token':getCookie('token'),'symbol':symbol},
            cache: false,
            success: function (data) {
                
                if (data && data.payload) {
                    data = data.payload;
                    
                    if(data) {

                        var dates = [];
                        var customers = [];
                        var chargings = [];
                        $(data.data).each(function () {
                            dates.push(this.date);
                            customers.push(this.member_cnt);
                            chargings.push(this.charging);
                        });
            
                        // barChart - 손익
                        var barData1 = {
                            // labels: dates,
                            labels: data.years, //["2018", "2019", "2020", "2021", "2022"],
                            datasets: [
                                {
                                    label: "매출",
                                    backgroundColor: "rgba(255,174,94,1)",
                                    borderColor: "rgba(255,174,94,1)",
                                    pointBackgroundColor: "rgba(255,174,94,1)",
                                    pointBorderColor: "#fff",
                                    data: data.sales, //[40, 110, 360, 450, 600]
                                },
                                {
                                    label: "영업이익",
                                    backgroundColor: "rgba(0,186,181,1)",
                                    borderColor: "rgba(0,186,181)",
                                    pointBackgroundColor: "rgba(0,186,181)",
                                    pointBorderColor: "#fff",
                                    data: data.opt_profit, //[60, 90, 110, 170, 150]
                                },
                                {
                                    label: "순이익",
                                    backgroundColor: "rgba(234,29,80,1)",
                                    borderColor: "rgba(234,29,80,1)",
                                    pointBackgroundColor: "rgba(234,29,80,1)",
                                    pointBorderColor: "#fff",
                                    data: data.net_profit, //[10, 20, 40, 50, 40]
                                }
                            ]
                        };
                        var barOptions1 = {
                            responsive: true
                        };
                        var ctx1 = document.getElementById("barChart_01").getContext("2d");
                        new Chart(ctx1, { type: 'bar', data: barData1, options: barOptions1 });
            
                        // barChart - 재무상태
                        var barData2 = {
                            // labels: dates,
                            labels: data.years, //["2018", "2019", "2020", "2021", "2022"],
                            datasets: [
                                {
                                    label: "자산",
                                    backgroundColor: "rgba(255,174,94,1)",
                                    borderColor: "rgba(255,174,94,1)",
                                    pointBackgroundColor: "rgba(255,174,94,1)",
                                    pointBorderColor: "#fff",
                                    data: data.assets, //[40, 110, 360, 450, 600]
                                },
                                {
                                    label: "부채",
                                    backgroundColor: "rgba(0,186,181,1)",
                                    borderColor: "rgba(0,186,181)",
                                    pointBackgroundColor: "rgba(0,186,181)",
                                    pointBorderColor: "#fff",
                                    data: data.debt, //[60, 390, 560, 670, 850]
                                },
                                {
                                    label: "자본",
                                    backgroundColor: "rgba(234,29,80,1)",
                                    borderColor: "rgba(234,29,80,1)",
                                    pointBackgroundColor: "rgba(234,29,80,1)",
                                    pointBorderColor: "#fff",
                                    data: data.equity, //[100, 500, 920, 1120, 1450]
                                }
                            ]
                        };
                        var barOptions2 = {
                            responsive: true
                        };
                        var ctx2 = document.getElementById("barChart_02").getContext("2d");
                        new Chart(ctx2, { type: 'bar', data: barData2, options: barOptions2 });
            
                        // barChart - 주요지표
                        var barData3 = {
                            // labels: dates,
                            labels: data.years, //["2018", "2019", "2020", "2021", "2022"],
                            datasets: [
                                // {
                                //     label: "영업이익",
                                //     backgroundColor: "rgba(255,174,94,1)",
                                //     borderColor: "rgba(255,174,94,1)",
                                //     pointBackgroundColor: "rgba(255,174,94,1)",
                                //     pointBorderColor: "#fff",
                                //     data: data.opt_profit, //[40, 50, 60, 70, 80]
                                // },
                                {
                                    label: "부채비율",
                                    backgroundColor: "rgba(0,186,181,1)",
                                    borderColor: "rgba(0,186,181)",
                                    pointBackgroundColor: "rgba(0,186,181)",
                                    pointBorderColor: "#fff",
                                    data: data.debt_ratio, //[60, 70, 80, 90, 100]
                                },
                                {
                                    label: "ROE",
                                    backgroundColor: "rgba(234,29,80,1)",
                                    borderColor: "rgba(234,29,80,1)",
                                    pointBackgroundColor: "rgba(234,29,80,1)",
                                    pointBorderColor: "#fff",
                                    data: data.roe, //[45, 60, 90, 99, 100]
                                }
                            ]
                        };
                        var barOptions3 = {
                            responsive: true
                        };
                        var ctx3 = document.getElementById("barChart_03").getContext("2d");
                        new Chart(ctx3, {type: 'bar', data: barData3, options:barOptions3});
                        
                    } else {
                        $empty1.show();
                        $empty2.show();
                        $empty3.show();
                    }
                    $loading1.hide();
                    $loading2.hide();
                    $loading3.hide();
                }
            } 
        });
    } 
    bar_chart();

});