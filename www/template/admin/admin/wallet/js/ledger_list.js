$(document).ready(function() {
    $('[name=ledger_list]').addClass( 'nowrap' ).DataTable( {
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
        ,"processing": true
        ,"serverSide": true
        ,"ajax": {
            'url':'?',
            'data':{'pg_mode':'wallet_list'},
            'type':'post',
            'dataSrc': function(json){
                for( i in json.data ) {
                    json.data[i]['balance_bc'] = json.data[i].symbol=='GWS' ? '<a name="btn_search_bc_balance" data-walletno="'+json.data[i]['walletno']+'">잔액조회</a>' : '';
                    json.data[i]['action'] = '<a href="?pg_mode=transaction&searchval='+json.data[i]['address']+'"><i class="fa fa-check text-navy"></i> 상세보기</a>';
                    json.data[i]['address'] = '<a href="?pg_mode=transaction&searchval='+json.data[i]['address']+'">'+json.data[i]['address']+'</a>';
                }
                return json.data;
            }
        }
        ,"order":[[0, 'desc']]
        ,"columns": [
            { "data": "walletno", "responsivePriority": 1, "orderSequence": ['desc','asc'], "orderData": 0, "target":0, "type":"num" },
            { "data": "symbol", "orderable": false },
            { "data": "name", "responsivePriority": 1, "orderable": false },
            { "data": "address", "orderable": false },
            { "data": "balance", "responsivePriority": 1, "className":"dt-body-right", "orderSequence": ['desc','asc'], render: $.fn.dataTable.render.number( ",", ".", 8, "" )  },
            { "data": "balance_delta", "className":"dt-body-right", "orderable": false, render: $.fn.dataTable.render.number( ",", ".", 8, "" )  },
            { "data": "balance_bc", "className":"dt-body-right", "orderable": false },
            { "data": "regdate", "orderSequence": ['desc','asc']  },
            { "data": "locked", "className":"dt-body-center", "orderable": false },
            { "data": "autolocked", "className":"dt-body-center", "orderable": false }
            // ,{ "data": "action", "responsivePriority": 1, "orderable": false }
        ]
        , "dom": '<html5buttons>Bfrtip'
        , "buttons": [
            {extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'WalletList'},
            {extend: 'pdfHtml5', title: 'WalletList', fontSize: '10px', orientation: 'landscape', pageSize: 'A4', pageMargins: [ 40, 60, 40, 60 ] },
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
        , "error": function (xhr) {console.log (xhr.responseText);}
        , select: true
    });
    $('[name=ledger_list]').on('click', '[name=btn_search_bc_balance]', function(){
        var _self = this, $parent = $(this).parent(), walletno = $(this).attr('data-walletno');
        $(_self).parent().empty().append('조회중입니다.');
        $.post('?', {'pg_mode':'get_bc_balance', 'walletno':walletno}, function(r){
            if(r) {
                $parent.empty().append(''+r.balance+'');
            }
        },'json');
    });
});