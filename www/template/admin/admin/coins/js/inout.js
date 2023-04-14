$(function() {
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
        , "ajax": {
            type: "post",
            url: "?",
            data: {
                'pg_mode':'list',
                'symbol': getURLParameter('symbol'),
                'txn_type': getURLParameter('txn_type'),
                's_div':getURLParameter('s_div'),
                's_day':getURLParameter('s_day'),
                's_month':getURLParameter('s_month'),
                's_year':getURLParameter('s_year')
            }
        }
        , "columns": [
            { data: 'no', "responsivePriority": 1, "className":"dt-body-center" },
            { data: 'regdate', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], render: function(data) { return data?substr(data, 0, 16):'';} },
            { data: 'txndate', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], render: function(data) { return data?substr(data, 0, 16):'';} },
            { data: 'userid', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": false },
            { data: 'sender', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": false },
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
                        symbol = "";
                        var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
                    } else {
                        symbol = "";
                        var num = $.fn.dataTable.render.number(',', '.', 4, symbol).display(data);
                    }
                    return num;
                }
            },
            { data: 'fee', "className":"dt-body-right", "orderable": false,
                render: function (data, type, row, meta) {
                    var symbol = "";
                    if(row.symbol=='KRW') {
                        symbol = "";
                        var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
                    } else {
                        symbol = "";
                        var num = $.fn.dataTable.render.number(',', '.', 2, symbol).display(data);
                    }
                    return num;
                }
            },
            { data: 'key_relative', "className":"dt-body-center", "orderable": false  },
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
            {extend: 'pdfHtml5', title: 'CoinList', fontSize: '10px', orientation: 'landscape', pageSize: 'A4' },

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
        s_year = $('#s_year').val();
        s_month = $('#s_month').val();
        s_day = $('#s_day').val();
        location.href="?pg_mode=list&symbol="+getURLParameter('symbol')+"&txn_type="+ getURLParameter('txn_type')+"&s_div="+s_div+"&s_year="+s_year+"&s_month="+s_month+"&s_day="+s_day;
        //datatable.ajax.reload(null, false);
    });
});
