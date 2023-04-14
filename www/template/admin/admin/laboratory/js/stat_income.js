var isloaded = false;

$(function() {
    if (isloaded) return; //중복 호출방지
    var userid = '';
    var username = '';
    var mobile = '';
    var s_branch = '';

    $('#btn-search').on('click', function() {
        let type=$('select[name=type]').val();
        let s_symbol = $('#s_symbol').val();
        let start_date = $('[name="start_date"]').val();
        let end_date = $('[name="end_date"]').val();
        let s_year=$('select[name=s_year]').val();
        let s_month=$('select[name=s_month]').val();
        let s_day=$('select[name=s_day]').val();
        // day가 있는데 month, year가 없는지 확인
        // if( !s_month && s_day ) {
        //     alert('월을 선택해주세요.'); return false;
        // }
        // if( !s_year && s_day ) {
        //     alert('년을 선택해주세요.'); return false;
        // }
        // if( !s_year && s_month ) {
        //     alert('년을 선택해주세요.'); return false;
        // }
        // 전체 검색시 년도별 표시방식인지 확인
        // if(type=='daily' && !s_year && !s_month && !s_day ) {
        //     alert('전체 검색에는 년도별 혹은 월별 표시방식을 사용해주세요.'); return false;
        // }
        datatable.ajax.reload(null, 1);
    });

    
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



    var columns = [];
    let $cols = $('[name="db_list"] thead th');
    $cols.each(function(i){
        let colname= $(this).attr('data-data');
        let orderSequence = $(this).attr('data-orderSequence');
        orderSequence = orderSequence ? orderSequence.split(',') : ['asc', 'desc'];
        columns.push({
            'data': colname, 'responsivePriority': $(this).attr('data-responsivePriority')=='0' ? false : true, 'className': $(this).attr('data-className'), 'orderable': $(this).attr('data-orderable'), "orderSequence": orderSequence,
            'render': function(data, type, row, meta) {
                return colname=='date' ? data : real_number_format(data);
            }
        });
    });
    var datatable = $('.dataTables-income').addClass('nowrap').DataTable({
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
        processing: true, // 로딩 이미지를 보여줍니다.
        serverSide: false, // 한번에 다 받아옵니다. 날짜별로 패이징 하기 어렵네요.
        searching: false,
        order: [],
        ajax: {
            type: "post",
            url: "?",
            data: function(d) {
                d.pg_mode='stat_income';
                d.s_symbol=$('select[name=s_symbol]').val();
                d.type=$('select[name=type]').val();
                d.start_date = $('[name="start_date"]').val();
                d.end_date = $('[name="end_date"]').val();
            }
        },
        columns: columns,
        dom: '<html5buttons>Bfrtip',
        buttons: [
            { extend: 'copy', title: '거래소 수익'},
            {extend: 'csv', title: '거래소 수익'},
            {extend: 'excel', title: '거래소 수익'},
            {extend: 'pdfHtml5', title: '거래소 수익', fontSize: '10px', pageSize: 'A4',
                customize : function(doc){
                    var colCount = new Array();
                    $('.dataTables-income').find('tbody tr:first-child td').each(function(){
                        if($(this).attr('colspan')){
                            for(var i=1;i<=$(this).attr('colspan');$i++){
                                colCount.push('*');
                            }
                        }else{ colCount.push('*'); }
                    });
                    doc.content[1].table.widths = colCount;
                }
            }, // orientation: 'landscape',
            {extend: 'print',
                title: '거래소 수익',
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

    }).on( 'xhr.dt', function ( e, settings, json, xhr ) {
        sum = {'sum_total':0};
        let d = json.data;
        for( n in d ) {
            let r = d[n];
            for( colname in r ) {
                if(colname!='date') {
                    let data = r[colname];
                    sum['sum_'+colname] = sum['sum_'+colname] ? sum['sum_'+colname] + data*1 : data*1;
                }
            }
        }
        for(i in sum) {
            $('[name="'+i+'"]').text(real_number_format(sum[i]));
        }
    });


    isloaded = true;
});