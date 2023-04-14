$(function() {


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
            minDate: '2017-07-01',
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

        $('.dataTables-email').DataTable({
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
            ServerSide: true,
            pageLength: 10,
            responsive: true,
            order: [[ 0, 'desc' ], [ 1, 'asc' ]],
            ajax: {
                type: "post",
                url: "/member/admin/memberAdmin.php",
                data: {
                    'pg_mode':'getemailhistory',
                    'start_date': start_get_date,
                    'end_date': end_get_date
                }
            },
            columns: [
                { data: 'no' },
                { data: 'mail_to' },
                { data: 'mail_to_name' },
                { data: 'mail_subject' },
                { data: 'mail_result' },
                { data: 'regdate' },
                { data: 'etc' } //
            ],
            columnDefs: [
                {
                    targets: [0],
                    render: function(data) {
                        return data;
                    },
                    className: 'text-center'
                },
                {
                    targets: [1],
                    render: function(data) {
                        return data;
                    },
                    className: 'text-left'
                },
                {
                    targets: [2],
                    render: function(data) {
                        return data;
                    },
                    className: 'text-center'
                },
                {
                    targets: [3],
                    render: function(data) {
                        return data;
                    },
                    className: 'text-left'
                },
                {
                    targets: [4],
                    render: function(data) {
                        return data;
                    },
                    className: 'text-center'
                },
                {
                    targets: [5],
                    render: function(data) {
                        return data;
                    },
                    className: 'text-center'
                },
                {
                    targets: [6],
                    render: function ( data, type, full, meta ) {
                        return '<a href="/member/admin/memberAdmin.php?pg_mode=del_emailhistory&idx='+full["idx"]+'" class="btn btn-xs btn-danger">삭제</a>';
                    },
                    className: 'text-center'
                }
            ],
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'E-mail History'},
                {extend: 'pdf', title: 'E-mail History'},

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

        });
    });
