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

    $('#srchform').submit(function() {
        if($('*').is('select[name=loop_scale]')) {
            var loop_scale = $('select[name=loop_scale]').val();
            $('input[name=loop_scale]').val(loop_scale);
        }
    });

    var columns = [];
    columns.push({data: 'no', "responsivePriority": 0, className: 'text-center'});
    columns.push({data: 'userid', "responsivePriority": 0, className: 'text-center'});
    columns.push({data: 'name', "responsivePriority": 0, className: 'text-center'});
    columns.push({data: 'mobile', "responsivePriority": 1, className: 'text-center'});
    columns.push({data: 'regdate', "responsivePriority": 1, className: 'text-center'});
    columns.push({data: 'joindate', "responsivePriority": 1, className: 'text-center'});
    /*
    columns.push({data: 'gws', "responsivePriority": 1, className: 'text-right',
        render: function (data, type, row, meta) {
            var symbol = "";
            var num = $.fn.dataTable.render.number(',', '.', 4, symbol).display(data);
            return num;
        }
    });
    columns.push({data: 'htc', "responsivePriority": 1, className: 'text-right',
        render: function (data, type, row, meta) {
            var symbol = "";
            var num = $.fn.dataTable.render.number(',', '.', 4, symbol).display(data);
            return num;
        }
    });
    columns.push({data: 'bdc', "responsivePriority": 1, className: 'text-right',
        render: function (data, type, row, meta) {
            var symbol = "";
            var num = $.fn.dataTable.render.number(',', '.', 4, symbol).display(data);
            return num;
        }
    });
    */
    columns.push({data: 'krw', "responsivePriority": 1, className: 'text-right',
        render: function (data, type, row, meta) {
            var symbol = "";
            var num = $.fn.dataTable.render.number(',', '.', 0, symbol).display(data);
            return num;
        }
    });
    //for(r in currency_list) {
     //   var row = currency_list[r];
       // columns.push({data: 'GWS', "responsivePriority": 1, className: 'text-right', "orderable": false, render: function (data, type, full, meta) {
        //    return data ? data : '0';
       // }});
   // }
    columns.push({data: 'contents', "responsivePriority": 0, className: 'text-left'});
    columns.push({data: 'passwd_default', "responsivePriority": 0, className: 'text-center',
        render: function(data, type, row, meta) { 
            return '<a href="/member/admin/memberWithdraw.php?pg_mode=view&userid=' + row.userid + '" class="btn btn-xs btn-success">상세보기</a> <a name="btn-passwd_default" href="?pg_mode=passwd_default&userno='+row.userno+'" class="btn btn-xs btn-primary">탈퇴취소</a> <a name="btn-delete" href="?pg_mode=del&idx='+row.no+'" class="btn btn-xs btn-danger">영구삭제</a>';}
    });
    // columns.push({data: 'etc', "responsivePriority": 1, className: 'text-center', render: function (data, type, full, meta) {
    //     return '';//<a href="/member/admin/memberAdmin.php?pg_mode=view&userid=' + full["email"] + '" class="btn btn-xs btn-primary">상세보기</a>';
    // }});

    var datatable = $('.dataTables-withdraw').addClass('nowrap').DataTable({
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
        pageLength: 10,
        responsive: true,
        processing: true,
        serverSide: true,
        searching: false,
        order: [],
        ajax: {
            type: "post",
            url: "?",
            data: {
                'pg_mode': 'getwithdraw',
                'userid': $('input[name=userid]').val(),
                'name': $('input[name=name]').val(),
                'mobile': $('input[name=mobile]').val(),
                'start_date': start_get_date,
                'end_date': end_get_date
            }
        },
        columns: columns,
        dom: '<html5buttons>Bfrtip',
        buttons: [
            { extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'Customers'},
            {extend: 'pdf', title: 'Customers'},

            {extend: 'print',
                customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');

                    $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                }
            }
        ],
        error: function (xhr) {
            alert(xhr.responseText);
        }

    }).on('click', '[name=btn-passwd_default]', function(){
        var url = url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('비밀번호를 초기화 하시겠습니까?')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        //datatable.ajax.reload(null, false);
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '초기화 하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;

    }).on('click', '[name=btn-delete]', function(){
        var url = url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('탈퇴회원을 영구 삭제하시겠습니까? 삭제하면 모든 데이터가 삭제되며 복구 할 수 없습니다.')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '탈퇴회원을 삭제하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }, 1);
        }
        return false;
    });

});
