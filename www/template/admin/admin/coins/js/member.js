$(function() {
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
        , "searching": false
        , 'pageLength': 10 , "lengthMenu": [ [10, 25, 50, 75, 100], [10, 25, 50, 75, 100] ]
        , "order": [[ 1, 'desc' ]]
        , "ajax": {
            type: "post",
            url: "?",
            data:  function ( d ) {
                d.pg_mode = 'list';
                d.symbol = $('[name=symbol]').val();
                d.userid = $('[name=userid]').val();
                d.name = $('[name=name]').val();
                d.nickname = $('[name=nickname]').val();
            }
        }
        , "columns": [
            { data: 'no', "responsivePriority": 1, "className":"dt-body-center", "orderable": false },
            { data: 'regdate', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], render: function(data) { return substr(data, 0, 16);} },
            { data: 'userid', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": true },
            { data: 'nickname', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": true },
            { data: 'user_name', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['asc','desc'], "orderable": true },
            {
                data: 'symbol', "className": "dt-body-center", "orderSequence": ['desc', 'asc'], "orderable": true, render: function (data, type, row) { 
                    return data == 'USD' || data == 'KRW' ?
                    '<a href="/wallet/admin/ledgerAdmin.php?pg_mode=transaction&symbol=' + data + '&search=' + row.userid + '">' + row.symbol_name + '</a>' :
                    '<a href="/coins/admin/tradehistoryAdmin.php?pg_mode=list&symbol=' + data + '&search=' + row.userid + '">' + row.symbol_name+'('+row.symbol+')' + '</a>';
                }
            },
            // { data: 'address', "className":"dt-body-left", "orderSequence": ['desc','asc'], "orderable": true },
            { data: 'confirmed', "className":"dt-body-right", "orderSequence": ['desc','asc'], "orderable": true,
                render: function (data, type, row, meta) {
                    var symbol = "";
                    var num = 0;
                    if(row.symbol=='USD') {
                        symbol = "";
                        num = real_number_format(data);
                        // var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
                    } else {
                        symbol = "";
                        num = real_number_format(data);
                        // var num = $.fn.dataTable.render.number(',', '.', 4, symbol).display(data);
                    }
                    return num;
                }
            },
            { data: 'locked', "className":"dt-body-center", "orderable": true, render: function(data) { return data=='Y' ? '잠겨있음' : '열려있음';} },
            { data: 'autolocked', "className":"dt-body-center", "orderable": true, render: function(data) { return data=='Y' ? '잠겨있음' : '열려있음';}   },
            { data: 'deposit_check_time', "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": true },
            { data: 'userno', "responsivePriority": 1, "className":"dt-body-center", "orderable": false, render: function(data, type, row, meta) { 
                return row.locked == 'Y' || row.autolocked == 'Y' ? '<a name="btn-unlock" href="?pg_mode=unlock&userno=' + row.userno + '&symbol=' + row.symbol + '" class="btn btn-xs btn-primary">해제</a>' : '<a name="btn-lock" href="?pg_mode=lock&userno=' + row.userno + '&symbol=' + row.symbol + '" class="btn btn-xs btn-danger">잠금</a>';
            } }
        ]
        , "dom": '<html5buttons>Bfrtip'
        , "buttons": [
            {extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'MemberAccountList'},
            {extend: 'pdfHtml5', title: 'MemberAccountList', fontSize: '10px', orientation: 'landscape', pageSize: 'LEGAL', pageMargins: [ 40, 60, 40, 60 ] },

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
    });
    datatable.on('click', '[name=btn-unlock]', function(){
        var url = url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('지갑을 잠금해제하시겠습니까? 자동잠금도 같이 해제됩니다. ')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '잠금해제하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    }).on('click', '[name=btn-lock]', function(){
        var url = url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('지갑을 잠그시겠습니까? 지갑이 잠기면 출금을 할 수 없습니다.')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '지갑을 잠그지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    });

    $('#btn-search').click(function(){
        s_symbol = $('[name=symbol]').val();
        s_userid = $('[name=userid]').val();
        s_name = $('[name=name]').val();
        s_nickname = $('[name=nickname]').val();
        datatable.ajax.reload(null, !!'reset page');
    });
});
