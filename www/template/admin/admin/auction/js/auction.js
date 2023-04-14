$(function() {
    // 숨김/노출 스위치
    var render_switch = function(type, value, idx) {
        return '<input type="checkbox" ' + (value == 'Y' ? 'checked' : '') + ' data-toggle="toggle" data-on="노출" data-off="제외" data-size="small" data-type="' + type + '" data-value="' + value + '" data-idx="' + idx + '">';
    };
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
                    console.log({ 'pg_mode': 'confirm', 'type': confirm_type, 'value': confirm_value, 'idx': idx });
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
    }
    
    /*var start_get_date = '';
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
    });*/
    var datatable = $('.dataTables-auctionList').addClass('nowrap').DataTable({
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
                'pg_mode': 'list'
            }
        },
        "columns": [
            { data: 'auction_idx', "className":"text-center" }, // 경매번호 , "responsivePriority": 1, "orderSequence": ['desc','asc'], "orderable": true },
            { data: 'finish_str', "className":"text-center" },  // 상태
            { data: 'goods_type', "className":"text-center" },  // 종류
            { data: 'auction_title', "className":"text-left" }, // 경매이름
            { data: 'userid', "className":"text-left" },    // 판매자
            { data: 'main_pic', "className":"text-center",render: function(data) {return data ? "<img src=" + data + " style='width:50px;height:50px;'>" :'';} }, // 상품이미지
            { data: 'title', "className":"text-left" }, // 상품이름
            { data: 'start_price', "className":"text-right", render: function(data, p2, row) {return data ? data+" "+row.price_symbol:'';} },  // 시작가격
            { data: 'max_auction_price', "className":"text-right", render: function(data, p2, row) {return data ? data+" "+row.price_symbol:'';} },    // 최고입찰가격
            { data: 'max_auction_bider', "className":"text-center" },    // 최고입찰회원
            { data: 'event_bn', "className":"text-center", render: function(data, p2, row) {return render_switch('event_bn', data, row.auction_idx) ;} }, // 이벤트 노출
            { data: 'start_date', "className":"text-center",render: function(data, p2, row) { return (data + '').substr(0, 16); } },    // 시작날짜
            { data: 'end_date', "className":"text-center",render: function(data, p2, row) { return (data + '').substr(0, 16); } },  // 종료날짜
            { data: 'auction_idx', "className":"text-center",render: function(data, p2, row) {return '<a href="auctionHistoryAdmin.php?auction_idx=' + row.auction_idx + '" class="btn btn-sm btn-default btn_goldkey" data-toggle="tooltip" data-placement="top">입찰내역</a>';} } // 입찰기록
        ],
        "dom": '<html5buttons>Bfrtip',
        "buttons": [
            { extend: 'copy' },
            { extend: 'csv' },
            { extend: 'excel', title: 'AuctionLists' },
            { extend: 'pdf', title: 'AuctionLists' },

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
    }).on('draw', after_draw).on('responsive-display', after_draw);

    //$('[name=btn-edit]').on('click', function() {
    $('body').on('click', '.btn-edit', function(e) {
        let json_data = $(this).attr('data-json');
        if (!json_data) return false;
        json_data = JSON.parse(urldecode(json_data));

        $('input[name=idx]').val(json_data.idx);
        $('input[name=auction_idx]').val(json_data.auction_idx);
        $('input[name=auction_title]').val(json_data.auction_title);
        $('input[name=title]').val(json_data.title);
        $('input[name=old_userid]').val(json_data.userid);
        $('input[name=userid]').val(json_data.userid);
        $('#content').val(json_data.content);
        $('input[name=sell_price]').val(json_data.sell_price);
        $('#start_date').val(json_data.start_date ? new Date(json_data.start_date).toISOString().slice(0, 19) : '');
        $('#end_date').val(json_data.end_date ? new Date(json_data.end_date).toISOString().slice(0, 19) : '');
        $("select[name=auction_type] ").val(json_data.goods_type);

        $('.btn-preview').addClass('hide');
        if (json_data.main_pic) {
            $("[name=btn-preview-main_pic] ").attr('href', json_data.main_pic).removeClass('hide');
            $("[name=btn-delete-main_pic] ").attr('data-img', 'main_pic').attr('data-idx', json_data.idx).removeClass('hide');
        }
        if (json_data.sub1_pic) {
            $("[name=btn-preview-sub1_pic] ").attr('href', json_data.sub1_pic).removeClass('hide');
            $("[name=btn-delete-sub1_pic] ").attr('data-img', 'sub1_pic').attr('data-idx', json_data.idx).removeClass('hide');
        }
        if (json_data.sub2_pic) {
            $("[name=btn-preview-sub2_pic] ").attr('href', json_data.sub2_pic).removeClass('hide');
            $("[name=btn-delete-sub2_pic] ").attr('data-img', 'sub2_pic').attr('data-idx', json_data.idx).removeClass('hide');
        }
        if (json_data.sub3_pic) {
            $("[name=btn-preview-sub3_pic] ").attr('href', json_data.sub3_pic).removeClass('hide');
            $("[name=btn-delete-sub3_pic] ").attr('data-img', 'sub3_pic').attr('data-idx', json_data.idx).removeClass('hide');
        }
        if (json_data.sub4_pic) {
            $("[name=btn-preview-sub4_pic] ").attr('href', json_data.sub4_pic).removeClass('hide');
            $("[name=btn-delete-sub4_pic] ").attr('data-img', 'sub4_pic').attr('data-idx', json_data.idx).removeClass('hide');
        }
        if (json_data.animation) {
            $("[name=btn-preview-animation] ").attr('href', json_data.animation).removeClass('hide');
            $("[name=btn-delete-animation] ").attr('data-img', 'animation').attr('data-idx', json_data.idx).removeClass('hide');
        }

        $('#box_edit').removeClass('hide');
        $('#box_list').addClass('hide');
        $('#page_title').text('상품 수정');
    })
});