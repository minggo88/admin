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

    $('.dataTables-auctionHistory').addClass('nowrap').DataTable({
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
            data: {
                'pg_mode': 'list',
                'start_date': getParam('start_date'),
                'end_date': getParam('end_date'),
                'auction_idx': getParam('auction_idx'),
                'title': getParam('title'),
                'auction_title': getParam('auction_title')
            }
        },
        "columns": [
            { data: 'start_date', className: 'text-center',render: function(data, p2, row) { return (data + '').substr(0, 16); } },
            { data: 'current_price', className: 'text-right' },
            { data: 'userid', className: 'text-left' },
            { data: 'goods_type', className: 'text-center' },
            { data: 'main_pic', className: 'text-center', render: function(data) { return data ? "<img src=" + data + " style='width:50px;height:50px;'>" :''; } },
            { data: 'auction_title', className: 'text-left' },
            { data: 'userid', className: 'text-center' },
            { data: 'end_date', className: 'text-center', "responsivePriority": 1, render: function(data, p2, row) {
                return '<a href="auctionHistoryAdmin.php?pg_mode=historyApplyLists&auction_idx='+row.auction_idx+'" class="btn btn-sm btn-default btn_goldkey" data-toggle="tooltip" data-placement="top">입찰기록</a>';
            } }
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