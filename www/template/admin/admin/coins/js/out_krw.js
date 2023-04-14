$(function() {
    
    var start_get_date = '';
    var end_get_date = '';

    start_get_date = $('input[name=start_date]').val();
    end_get_date = $('input[name=end_date]').val();

    if(start_get_date) {
        $('#reportrange span').html(start_get_date + ' - ' + end_get_date);
    } else {
        // start_get_date = moment().format('YYYY-MM-DD');
        // $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    };

    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: start_get_date,
        endDate: moment(),
        // minDate: '2018-04-01',
        // maxDate: '2020-12-31',
        // dateLimit: { days: 60 },
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
        console.log(start.toISOString(), end.toISOString(), label);

        var start_date = '';
        var end_date = '';
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');

        $('input[name=start_date]').val(start_date);
        $('input[name=end_date]').val(end_date);

        $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

    });



    var s_div, s_year, s_month, s_day;

    //var txn_type = $('#txn_type').val();
    var datatable = $('.dataTables-customers').addClass( 'nowrap' ).DataTable({
        "search": {
            "search": getURLParameter('search')||'',
        },
        "language": {
            "emptyTable": "데이터가 없음.",
            "lengthMenu": "페이지당 _MENU_ 개씩 보기",
            "info": "현재 _START_ - _END_ / _TOTAL_건",
            "infoEmpty": "",
            "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
            "search": "검색: ",
            "zeroRecords": "일치하는 데이터가 없음",
            "loadingRecords": "로딩중...",
            "processing": '<img src="/template/admin/script/plug_in/loading/loading.gif"> 잠시만 기다려 주세요.',
            "paginate": {
                "next": "다음",
                "previous": "이전"
            }
        },
        "responsive": true
        , "processing": true
        , "serverSide": true
        , 'pageLength': 10 , "lengthMenu": [ [10, 25, 50, 75, 100], [10, 25, 50, 75, 100] ]
        , "order": [[ 1, 'desc' ]]
        , "searching" : false
        , "ajax": {
            type: "post",
            url: "?",
            data: {
                'pg_mode':'list',
                'symbol': getURLParameter('symbol'),
                'name' : $('[name=name]').val(),
                'start_date': $('[name="start_date"]').val(),
                'end_date': $('[name="end_date"]').val(),
                'txn_type': getURLParameter('txn_type'),
                's_div':$('[name="s_div"]').val()
            }
        }
        , "columns": [
            { data: 'no', "responsivePriority": 1, "className":"dt-body-center" },
            { data: 'regdate', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], render: function(data) { return data?substr(data, 0, 16):'';} },
            { data: 'txndate', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], render: function(data) { return data?substr(data, 0, 16):'';} },
            { data: 'userid', "responsivePriority": 1, "className": "dt-body-center", "orderSequence": ['asc', 'desc'], "orderable": false, render: function (data) {
                return '<a href="/coins/admin/memberAdmin.php?search=' + data + '">' + data + '</a>';
            } },
            { data: 'name', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": false },
            { data: 'receiver', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": false },
            { data: 'txn_type', "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": true,
            render: function (data, type, row, meta) {
                var txnType = "";
                if(row.txn_type=='Withdraw') {
                    txnType = '<span class="text-danger">'+row.txn_type+'</span>';
                } else if(row.txn_type=='Deposit') {
                    txnType = '<span class="text-success">'+row.txn_type+'</span>';
                } else {
                    txnType = '<span class="text-danger">'+row.txn_type+'</span>';
                }
                return txnType;
            }
            },
            { data: 'amount', "className":"dt-body-right", "orderSequence": ['desc','asc'], "orderable": true,
                render: function (data, type, row, meta) {
                    var symbol = "";
                    if(row.symbol=='KRW') {
                        symbol = "₩ ";
                        var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
                    } else {
                        symbol = row.symbol+" ";
                        var num = $.fn.dataTable.render.number(',', '.', 4, symbol).display(data);
                    }
                    return num;
                }
            },
            { data: 'fee', "className":"dt-body-right", "orderable": false,
                render: function (data, type, row, meta) {
                    var symbol = "";
                    if(row.symbol=='KRW') {
                        symbol = "₩ ";
                        var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
                    } else {
                        symbol = row.symbol+" ";
                        var num = $.fn.dataTable.render.number(',', '.', 2, symbol).display(data);
                    }
                    return num;
                }
            },
            { data: 'status', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": true },
            { data: 'txnid', "responsivePriority": 1, "className":"dt-body-center", "orderable": false,
                render: function(data, type, row, meta) {
                    var s = '';
                    switch(row.txn_type.toLowerCase()+'-'+row.status.toLowerCase()) {
                        case 'withdraw-waiting' :
                            s = ' <a name="btn-withdraw" href="?pg_mode=withdraw&txnid='+row.txnid+'" class="btn btn-xs btn-danger">출금 처리</a>';
                            // if(row.balance < row.amount + row.fee) {
                            //     s = ' 잔액('+row.balance+') 부족';
                            // }
                            if(row.locked == 'Y' || row.locked == 'Y') {
                                s = ' 지갑 잠김';
                            }
                            s+= ' <a name="btn-cancel" href="?pg_mode=cancel&txnid='+row.txnid+'" class="btn btn-xs btn-warning">취소 처리</a> ';
                            break;
                        case 'deposit-waiting' :
                            s = ' <a name="btn-deposit" href="?pg_mode=deposit&txnid='+row.txnid+'" class="btn btn-xs btn-primary">입금 처리</a>';
                            s+= ' <a name="btn-cancel" href="?pg_mode=cancel&txnid='+row.txnid+'" class="btn btn-xs btn-warning">취소 처리</a>';
                            break;
                    }
                    return s;
                }
            }
        ]
        , "dom": '<html5buttons>Bfrtip'
        , "buttons": [
            {extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'InoutList'},
            {extend: 'pdf', title: 'InoutList'},

            {extend: 'print',
                customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');

                    $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                }
            }
        ]
        , "error": function (xhr) {
            console.log(xhr.responseText);
        }
    })
    .on('click', '[name=btn-withdraw]', function(){
        var url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('출금정보을 완료하고 해당금액을 차감하시겠습니까?')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '처리하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    })
    .on('click', '[name=btn-deposit]', function(){
        var url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('입금정보를 완료하고 해당금액을 지급하시겠습니까?')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '처리하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    })
    .on('click', '[name=btn-cancel]', function(){
        var url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('취소 하시겠습니까?')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '처리하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    });

    $('#btn-search').click(function(){
        s_div = $('#s_div').val();
        name = $('[name=name]').val();
        start_date = $('[name=start_date]').val();
        end_date = $('[name=end_date]').val();
        location.href="?pg_mode=list&symbol="+getURLParameter('symbol')+"&name="+name+"&txn_type="+ getURLParameter('txn_type')+"&s_div="+s_div+"&start_date="+start_date+"&end_date="+end_date;
        //datatable.ajax.reload(null, false);
    });
});
