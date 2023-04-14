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



var s_symbol, s_year, s_month, s_day;

const _get_symbol = getURLParameter('symbol');
if (_get_symbol) {
    $('#s_symbol').val(_get_symbol);
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
      , "order": [[ 0, 'desc' ]]
      , "ajax": {
          type: "post",
          url: "?",
          data:  function ( d ) {
              d.pg_mode = 'list';
              d.symbol = $('#s_symbol').val();
              d.start_date = $('[name=start_date]').val();
              d.end_date = $('[name=end_date]').val();
              d.exchange = getURLParameter('exchange')||'KRW'
          }
      }
      , "columns": [
          // { data: 'no', "responsivePriority": 1 },
          { data: 'orderid', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": true },
          { data: 'userid', "responsivePriority": 1, "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": false }, //, render: function(data) { return substr(data, 0, 19);}
          { data: 'time_order', "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": true },
          { data: 'trading_type_str', "className":"dt-body-center", "orderable": false },
          { data: 'status_str', "className":"dt-body-center", "orderable": false },
          { data: 'price', "className":"dt-body-right", "orderSequence": ['desc','asc'], "orderable": false },
          { data: 'volume', "className":"dt-body-right", "orderSequence": ['desc','asc'], "orderable": false  }, // , render: $.fn.dataTable.render.number( ",", ".", 4, "" )
          { data: 'volume_remain', "className":"dt-body-right", "orderSequence": ['desc','asc'], "orderable": false  }, // , render: $.fn.dataTable.render.number( ",", ".", 4, "" )
          { data: 'amount', "className":"dt-body-right", "orderable": false  }, // , render: $.fn.dataTable.render.number( ",", ".", 0, "" )
          { data: 'time_traded',  "className":"dt-body-center", "orderSequence": ['desc','asc'], "orderable": true  } //, render: function(data) { return substr(data, 0, 19);}
      ]
      // , "dom": '<"html5buttons"B>lTfgitp'
      // , "buttons": [
      //     {extend: 'copy'},
      //     {extend: 'csv'},
      //     {extend: 'excel', title: 'Customers'},
      //     {extend: 'pdf', title: 'Customers'},

      //     {extend: 'print',
      //         customize: function (win){
      //             $(win.document.body).addClass('white-bg');
      //             $(win.document.body).css('font-size', '10px');

      //             $(win.document.body).find('table')
      //                     .addClass('compact')
      //                     .css('font-size', 'inherit');
      //         }
      //     }
      // ]
      , "dom": '<html5buttons>Bfrtip'
      , "buttons": [
          {extend: 'copy'},
          {extend: 'csv'},
          {extend: 'excel', title: 'OrderHistories'},
          {extend: 'pdfHtml5', title: 'OrderHistories', fontSize: '10px', orientation: 'landscape', pageSize: 'A4', pageMargins: [ 40, 60, 40, 60 ] },
          
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

  $('#btn-search').click(function(){
      s_symbol = $('#s_symbol').val();
      start_date = $('[name=start_date]').val();
      end_date = $('[name=end_date]').val();
      datatable.ajax.reload(null, !!'reset page');
  });


});
