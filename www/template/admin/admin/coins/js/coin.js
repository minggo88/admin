$(function() {

    // 숨김 처리 / 노출 처리 스위치 액션
    var after_draw = function() {
        $('input[data-toggle=toggle]').each(function() {
            if ($(this).attr('data-event-added') != '1') {
                $(this).bootstrapToggle().change(function() {
                    // e.preventDefault(); // The flicker is a codepen thing
                    var $self = $(this),
                        confirm_value = $self.prop('checked') ? 'Y' : 'N',
                        confirm_type = $self.attr('data-type'),
                        idx = $self.attr('data-idx');
                    // console.log({ 'pg_mode': 'confirm', 'type': confirm_type, 'value': confirm_value, 'idx': idx });
                    $.post('?', { 'pg_mode': 'confirm', 'type': confirm_type, 'value': confirm_value, 'idx': idx }, function(r) {
                        console.log('result:', r);
                        if (r && r.bool) {
                            $self.toggleClass('toggle-on').attr('data-value', confirm_value);
                            datatable.ajax.reload(null, false);
                        } else {
                            if (r.msg == 'err_sess') { // 로그아웃 된 경우.
                                alert('다시 로그인 해주세요.');
                                window.location.reload();
                            } else {
                                alert(r.msg);
                            }
                        }
                    }, 'json');
                }).attr('data-event-added', '1');
            }
        })

        $('select[data-switch=switch]').on('change', function () {
            // console.log($(this).val())
            confirm_value = $(this).val()
            confirm_type =  $(this).attr('data-type')
            idx =  $(this).attr('data-idx')
            $.post('?', { 'pg_mode': 'confirm', 'type': confirm_type, 'value': confirm_value, 'idx': idx }, function(r) {
                console.log('result:', r);
                if (r && r.bool) {
                    datatable.ajax.reload(null, false);
                } else {
                    if (r.msg == 'err_sess') { // 로그아웃 된 경우.
                        alert('다시 로그인 해주세요.');
                        window.location.reload();
                    } else {
                        alert(r.msg);
                    }
                }
            }, 'json');
        })
    }

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
        , 'order': [ 4, 'desc' ]
        , 'ajax': {type: "post", url: "?", data: {'pg_mode':'list'}}
        , columns: [
            { data: 'no', "responsivePriority": 1, "orderSequence": ['desc','asc'], "orderable": false },
            // { data: 'symbol', "responsivePriority": 1, "className":"dt-body-center", "orderable": false, render: function(data) { return '<a name="btn-edit" href="?pg_mode=edit&symbol='+data+'" class="btn btn-xs btn-default">수정</a> <a name="btn-delete" href="?pg_mode=delete&symbol='+data+'" class="btn btn-xs btn-danger">삭제</a>';} },
            { data: 'icon_url', "responsivePriority": 1, "orderable": false,
                render: function(data) {
                    var rtn_img = ""; 
                    if(data==='NULL') {
                        rtn_img = '-';
                    } else {
                        rtn_img = '<img src="'+data+'" class="icon_url" />';
                    }
                    return rtn_img;
                } 
            },
            { data: 'name', "responsivePriority": 1, "orderable": true, render: function(data, type, row, meta) { return '<a name="btn-edit" href="//'+(window.location.host.replace('admin.',''))+'/exchange.html?symbol='+(row.symbol)+'" target="_blank">'+data+'</a><br><a name="btn-edit" href="?pg_mode=edit&symbol='+row.symbol+'" class="btn btn-xs btn-default">수정</a> <a name="btn-delete" href="?pg_mode=delete&symbol='+row.symbol+'" class="btn btn-xs btn-danger">삭제</a>';} },
            { data: 'active', "responsivePriority": 1, "orderable": true, render: function (data, p2, row) { 
                    return render_switch('active', data, row.symbol, '사용', '중단');
            } },
            { data: 'display_grade', "className":"text-center", render: function(data, p2, row) {
                    return data;
                }
            },
            { data: 'regdate', "orderSequence": ['desc','asc'], render: function(data) { return substr(data, 0, 16);} },
            // { data: 'fee_in', "orderable": false },
            // { data: 'tax_in_ratio', "orderable": false },
            { data: 'fee_out', "orderable": true },
            // { data: 'tax_out_ratio', "orderable": false },
            // { data: 'fee_buy_ratio', "orderable": false },
            // { data: 'tax_buy_ratio', "orderable": false },
            { data: 'fee_sell_ratio', "orderable": true },
            // { data: 'tax_sell_ratio', "orderable": false },
            // { data: 'tax_income_ratio', "orderable": false },
            { data: 'trade_min_volume', "orderable": true },
            // { data: 'trade_max_volume', "orderable": false },
            { data: 'out_min_volume', "orderable": false },
            // { data: 'out_max_volume', "orderable": false },
            // { data: 'display_decimals', "orderable": true },
            // { data: 'creatable', "orderable": false, render: function (data, p2, row) { 
            //     return render_switch('creatable', data, row.symbol, '가능', '불가');
            // } },
            // { data: 'crypto_currency', "orderable": false, render: function (data, p2, row) { 
            //     return render_switch('crypto_currency', data, row.symbol, '암호화폐', '비 암호화폐');
            // } },
            // { data: 'backup_address', "orderable": false },
            // { data: 'menu', "orderable": true, render: function (data, p2, row) { 
            //     return render_switch('menu', data, row.symbol, '매뉴', '중단');
            // } },
            // { data: 'sortno', "orderable": true },
            // { data: 'color', "orderable": false },
            // { data: 'check_deposit', "orderable": false, render: function (data, p2, row) { 
            //     return render_switch('check_deposit', data, row.symbol, '작동', '중지');
            // } },
            // { data: 'transaction_outlink', "orderable": false },
            // { data: 'circulating_supply', "orderable": false,
            //     render: function (data, type, row, meta) {
            //         var symbol = "";
            //         var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
            //         return num;
            //     }
            // },
            // { data: 'max_supply', "orderSequence": ['desc','asc'], "orderable": true,
            //     render: function (data, type, row, meta) {
            //         var symbol = "";
            //         var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
            //         return num;
            //     }
            // },
            // { data: 'price', "className":"dt-body-right", "orderSequence": ['desc','asc'], "orderable": true,
            //     render: function (data, type, row, meta) {
            //         var symbol = "";
            //         var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
            //         return num;
            //     }
            // }
        ]
        , "dom": '<html5buttons>Bfrtip'
        , "buttons": [
            {extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'CoinList'},
            {extend: 'pdfHtml5', title: 'CoinList', fontSize: '10px', orientation: 'landscape', pageSize: 'A1' },
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
    }).on('draw', after_draw).on('responsive-display', after_draw);


    // Activate an inline edit on click of a table cell
    $('.dataTables-customers').on( 'click', '[name=btn-delete]', function (e) {
        var url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('삭제하시겠습니까?')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '삭제하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    } );

});
