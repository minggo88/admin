$(function() {
    // var start_get_date = '';
    // var end_get_date = '';

    // start_get_date = $('input[name=start_date]').val();
    // end_get_date = $('input[name=end_date]').val();

    // if (start_get_date) {
    //     $('#reportrange span').html(start_get_date + ' - ' + end_get_date);
    // } else {
    //     start_get_date = moment().format('YYYY-MM-DD');
    //     $('#reportrange span').html(moment().subtract(30, 'days').format('YYYY-MM-DD') + ' - ' + moment().format('YYYY-MM-DD'));
    // };

    // $('#reportrange').daterangepicker({
    //     format: 'YYYY-MM-DD',
    //     startDate: start_get_date,
    //     endDate: moment(),
    //     minDate: '2018-04-01',
    //     maxDate: '2020-12-31',
    //     dateLimit: { days: 60 },
    //     showDropdowns: true,
    //     showWeekNumbers: true,
    //     timePicker: false,
    //     timePickerIncrement: 1,
    //     timePicker12Hour: true,
    //     ranges: {
    //         'Today': [moment(), moment()],
    //         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    //         'Last 7 Days': [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
    //         'Last 30 Days': [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
    //         'This Month': [moment().startOf('month'), moment().endOf('month')],
    //         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    //     },
    //     opens: 'right',
    //     drops: 'down',
    //     buttonClasses: ['btn', 'btn-sm'],
    //     applyClass: 'btn-primary',
    //     cancelClass: 'btn-default',
    //     separator: ' to ',
    //     locale: {
    //         applyLabel: 'Submit',
    //         cancelLabel: 'Cancel',
    //         fromLabel: 'From',
    //         toLabel: 'To',
    //         customRangeLabel: 'Custom',
    //         daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    //         monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //         firstDay: 1
    //     }
    // }, function(start, end, label) {
    //     console.log(start.toISOString(), end.toISOString(), label);

    //     var start_date = '';
    //     var end_date = '';
    //     start_date = start.format('YYYY-MM-DD');
    //     end_date = end.format('YYYY-MM-DD');

    //     $('input[name=start_date]').val(start_date);
    //     $('input[name=end_date]').val(end_date);

    //     $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
    // });

    var render_switch = function(type, value, idx) {
        return '<input type="checkbox" ' + (value == 'Y' ? 'checked' : '') + ' data-toggle="toggle" data-on="노출" data-off="숨김" data-size="small" data-type="' + type + '" data-value="' + value + '" data-idx="' + idx + '">';
    };

    $('#srchform').submit(function() {
        // $('.dataTables-auctionHistory').DataTable();
        datatable.ajax.reload(null, false);
        return false;
    });

    var datatable = $('.dataTables-auctionReport').addClass('nowrap').DataTable({
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
        'pageLength': 25,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        'searching': false,
        "order": [
            [0, 'desc']
        ],
        "ordering": true,
        "ajax": {
            type: "post",
            url: "?",
            data: function(d) {
                d.pg_mode = 'list';
                d.goods_idx = $('[name=goods_idx]').val();
                d.goods_name = $('[name=goods_name]').val();
                d.report_type = $('[name=report_type]').val();
            }
        },
        "columns": [
            { data: 'reg_date', className: 'text-center',render: function(data, p2, row) { return (data + '').substr(0, 16); } },
            { data: 'goods_idx', className: 'text-center'},
            { data: 'main_pic', className: 'text-center'
                , render: function(data) { return data ? "<img src=" + data + " style='width:100px;height:100px;'>" :''; }
            },
            { data: 'goods_name', className: 'text-left' },
            { data: 'report_type_str', className: 'text-center' },
            { data: 'report_desc', className: 'text-left', render: function(data, p2, row) { return nl2br(data); } },
            { data: 'report_user_name', className: 'text-left', render: function(data, p2, row) { return row.report_user_name+'<br>('+row.report_userid+')'; } },
            { data: 'report_idx', className: 'text-center', "responsivePriority": 1
                , render: function(data, p2, row) {
                    let btns = [];
                    btns.push('<a href="#" data-report_idx="'+data+'" class="btn btn-sm btn-warning " name="btn-delete-report">처리 완료</a>');
                    // btns.push(render_switch('active', row.goods_active, row.idx));
                    if(row.goods_active=='Y') {
                        btns.push('<a href="#" data-report_idx="'+data+'" class="btn btn-sm btn-info " name="btn-hide-goods">상품 숨김</a>');
                    } else {
                        btns.push('<a href="#" data-report_idx="'+data+'" class="btn btn-sm btn-default " name="btn-show-goods">상품 노출</a>');
                    }
                    btns.push('<a href="#" data-report_idx="'+data+'" class="btn btn-sm btn-danger " name="btn-delete-goods">상품 삭제</a>');
                    return btns.join('<br>');
                }
            }
        ],
        "dom": '<html5buttons>Bfrtip',
        "buttons": [
            { extend: 'copy' },
            { extend: 'csv' },
            { extend: 'excel', title: 'AuctionApplyHistories' },
            { extend: 'pdf', title: 'AuctionApplyHistories' },
            {
                extend: 'print',
                customize: function(win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],
        "error": function(xhr) {
            console.log(xhr.responseText);
        }
    });


    // 완료 버튼
    $('.dataTables-auctionReport').on('click', '[name="btn-delete-report"]', function(evt){
        let report_idx = $(this).attr('data-report_idx');
        if(confirm('완료 상태가 되면 목록에서 사라집니다.\n\n완료 상태로 변경하시겠습니까? ')) {
            $.post('?', 'pg_mode=delete&report_idx='+report_idx, function(r){
                if(r && r.result) {
                    alert('완료 처리했습니다.');
                    datatable.ajax.reload(null, false);
                } else {
                    let msg = r.message ? r.message : '';
                    alert('완료 처리하지 못했습니다. '+msg);
                    datatable.ajax.reload();
                }
            }, 'json')
        }
        return false;
    });
    // 노출 버튼
    $('.dataTables-auctionReport').on('click', '[name="btn-show-goods"]', function(evt){
        let report_idx = $(this).attr('data-report_idx');
        if(confirm('마켓 목록에 상품을 노출하시겠습니까?')) {
            $.post('?', 'pg_mode=show-goods&report_idx='+report_idx, function(r){
                if(r && r.result) {
                    alert('노출 처리했습니다.');
                    datatable.ajax.reload(null, false);
                } else {
                    let msg = r.message ? r.message : '';
                    alert('노출 처리하지 못했습니다. '+msg);
                    datatable.ajax.reload();
                }
            }, 'json')
        }
        return false;
    });
    // 숨김 버튼
    $('.dataTables-auctionReport').on('click', '[name="btn-hide-goods"]', function(evt){
        let report_idx = $(this).attr('data-report_idx');
        if(confirm('숨김 처리하면 마켓 목록에서 상품이 표시되지 않습니다.\n\n상품을 숨김 처리하시겠습니까?')) {
            $.post('?', 'pg_mode=hide-goods&report_idx='+report_idx, function(r){
                if(r && r.result) {
                    alert('숨김 처리했습니다.');
                    datatable.ajax.reload(null, false);
                } else {
                    let msg = r.message ? r.message : '';
                    alert('숨김 처리하지 못했습니다. '+msg);
                    datatable.ajax.reload();
                }
            }, 'json')
        }
        return false;
    });
    // 삭제 버튼
    $('.dataTables-auctionReport').on('click', '[name="btn-delete-goods"]', function(evt){
        let report_idx = $(this).attr('data-report_idx');
        if(confirm('상품을 삭제하면 상품정보와 모든 경매 및 입찰 정보가 사라집니다.\n자료를 남겨야 한다면 숨김처리해 주세요.\n\n삭제 하시겠습니까?')) {
            $.post('?', 'pg_mode=delete-goods&report_idx='+report_idx, function(r){
                if(r && r.result) {
                    alert('삭제 처리했습니다.');
                    datatable.ajax.reload(null, false);
                } else {
                    let msg = r.message ? r.message : '';
                    alert('삭제 처리하지 못했습니다. '+msg);
                    datatable.ajax.reload();
                }
            }, 'json')
        }
        return false;
    });


});

// url 에서 parameter 추출
function getParam(sname) {
    var params = location.search.substr(location.search.indexOf("?") + 1);
    var sval = "";
    params = params.split("&");
    for (var i = 0; i < params.length; i++) {
        temp = params[i].split("=");
        if ([temp[0]] == sname) { sval = temp[1]; }
    }
    return sval;
}