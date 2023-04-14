$(function() {
    var start_get_date = '';
    var end_get_date = '';

    start_get_date = $('input[name=start_date]').val();
    end_get_date = $('input[name=end_date]').val();

    if (start_get_date) {
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
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
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

    $('#srchform').submit(function() {
        if ($('*').is('select[name=loop_scale]')) {
            var loop_scale = $('select[name=loop_scale]').val();
            $('input[name=loop_scale]').val(loop_scale);
        }
    });
    
    var datatable = $('.dataTables-list').addClass('nowrap').DataTable({
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
            data: function (d) {
                d.pg_mode = 'list';
                // d.start_date = $('[name=start_date]').val();
                // d.end_date = $('[name=end_date]').val();
                // d.auction_idx = $('[name=auction_idx]').val();
                d.goods_idx = $('[name=goods_idx]').val();
                d.title = $('[name=title]').val();
                // d.auction_title = $('[name=auction_title]').val();
                // d.goods_owner = $('[name=goods_owner]').val();
            }
        },
        "columns": [
            { data: 'idx', className: 'text-center' },
            { data: 'image_url', className: 'text-center', render: function (data) { return data ? "<img src=" + data + " style='width:100px;height:100px;'>" : ''; } },
            { data: 'title', className: 'text-left' },
            {
                data: 'idx', className: 'text-center', render: function (data, p2, row) {
                    return '<a href="?pg_mode=edit&idx=' + row.idx + '" name="btn-edit" class="btn btn-sm btn-default" data-toggle="tooltip" data-placement="top">수정</a> <button name="btn-delete" data-idx="' + row.idx + '" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top">삭제</button>';
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
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],
        "error": function (xhr) {
            console.log(xhr.responseText);
        }
    }).on('click', '[name="btn-delete"]', function () { 
        const idx = $(this).attr('data-idx');
        if (idx && confirm("인증(마크)를 삭제 하시겠습니까?")) {
            $.post('?', { 'pg_mode': 'delete', 'idx': idx }, function (r) {
                if (r && r.bool) {
                    datatable.ajax.reload(null, false);
                } else {
                    // if(r.)
                    alert('삭제하지 못했습니다.');
                }
            }, 'json');
        }
        return false;
    })
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