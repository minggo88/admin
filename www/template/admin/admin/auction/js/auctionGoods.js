// var elem = document.querySelector('.js-switch');
// var init = new Switchery(elem);

$(function() {
    if ($('[name="goods_upload"]').attr('disabled') == true )  $(this).attr('disabled', false);
    if ($('[name="goods_add_uplaod"]').attr('disabled') == true )  $(this).attr('disabled', false);

    $('[name="goods_upload"]').on('click', function() {
        let goods_file_data = $('[name="goods_file_data"]').val()
        if (!goods_file_data) {
            alert('파일을 선택하세요.');
            return false;
        }
        fileName = goods_file_data.slice(goods_file_data.indexOf(".") + 1).toLowerCase();
        if(fileName != "xlsx"){
            alert("엑셀 파일은 (xlsx) 형식만 등록 가능합니다.");
            return false;
        }
        $('[name="upload_type"]').val("goods_upload")
        $(this).attr('disabled', true);
        $('[name=excel_form]').submit();
    })
    $('[name="goods_add_uplaod"]').on('click', function() {
        let add_goods_file_data = $('[name="add_goods_file_data"]').val()
        if (!add_goods_file_data) {
            alert('파일을 선택하세요.');
        }
        fileName = add_goods_file_data.slice(add_goods_file_data.indexOf(".") + 1).toLowerCase();
        if(fileName != "xlsx"){
            alert("엑셀 파일은 (xlsx) 형식만 등록 가능합니다.");
            return false;
        }
        $('[name="upload_type"]').val("goods_add_uplaod")
        $(this).attr('disabled', true);
        $('[name=excel_form]').submit();
    })

    // 숨김/노출 스위치
    // var render_switch = function(type, value, idx) {
    //     return '<input type="checkbox" ' + (value == 'Y' ? 'checked' : '') + ' data-toggle="toggle" data-on="노출" data-off="숨김" data-size="small" data-type="' + type + '" data-value="' + value + '" data-idx="' + idx + '">';
    // };
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
        // if ($('*').is('select[name=loop_scale]')) {
        //     var loop_scale = $('select[name=loop_scale]').val();
        //     $('input[name=loop_scale]').val(loop_scale);
        // }

        datatable.ajax.reload(null, false);
        return false;
    });

    var datatable = $('.dataTables-auctionGoods').addClass('nowrap').DataTable({
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
        "responsive": true,
        "processing": true,
        'pageLength': 25,
        'searching': false,
        "order": [
            [0, 'desc']
        ],
        "ordering": true,
        "serverSide": true,
        "ajax": {
            type: "post",
            url: "?",
            data: function(d) {
                d.pg_mode = 'list';
                // d.start_date = $('[name=start_date]').val();
                // d.end_date = $('[name=end_date]').val();
                // d.auction_idx = $('[name=auction_idx]').val();
                d.goods_idx = $('[name=goods_idx]').val();
                d.title = $('[name=title]').val();
                d.pack_info = $('select[name=pack_info]').val();
                // d.auction_title = $('[name=auction_title]').val();
                // d.goods_owner = $('[name=goods_owner]').val();
            }
        },
        "columns": [
            { data: 'idx', "className":"text-center" }, // , "responsivePriority": 1, "className":"dt-body-left", "orderSequence": ['desc','asc'], "orderable": true },
            { data: 'pack_info', "className":"text-center", render: function(data) {return data == "Y" ? "패키지상품" : "단일 상품" }  },
            { data: 'main_pic', "className":"text-center", render: function(data) {return data ? "<img src=" + data + " style='width:50px;height:50px;'>":'';} },
            { data: 'goods_type', "className":"text-center" },
            { data: 'goods_grade', "className":"text-center", render: function(data, p2, row) {
                    let sel = [];
                    let seled = ['','',''];

                    if (row.pack_info != "Y" && row.pack_info != "N") {
                        if (data == 'S') {
                            seled[0] = "selected";
                        } else if (data == 'A') {
                            seled[1] = "selected";
                        } else if (data == 'B') {
                            seled[2] = "selected";
                        }
                        sel.push("<select data-switch='switch' data-type='goods_grade' data-idx='"+row.idx+"'>");
                        sel.push("<option value='S' " + seled[0] + ">S</option>");
                        sel.push("<option value='A' " + seled[1] + ">A</option>");
                        sel.push("<option value='B' " + seled[2] + ">B</option>");
                        sel.push("</select>")
                        return sel.join('');
                    } else {
                        return "";
                    }
                }
            },
            { data: 'title', "className":"text-left" },
            { data: 'price', "className":"text-right", render: function(data, p2, row) {return data ? data+" "+row.price_symbol:'';}  },
            { data: 'name', "className":"text-center" },
            { data: 'auction_title', "className":"text-left" },
            { data: 'max_auction_price', "className":"text-right", render: function(data, p2, row) {return data ? data+" "+row.price_symbol:'';} },
            { data: 'max_auction_bider', "className":"text-left" },
            { data: 'start_date', "className":"text-center",render: function(data, p2, row) { return (data+'').substr(0,16); } },
            { data: 'end_date', "className":"text-center",render: function(data, p2, row) { return (data+'').substr(0,16); } },
            {
                data: 'active', "responsivePriority": 1, "className": "text-center",
                render: function (data, p2, row) {
                    let btns = [];
                    btns.push(render_switch('active', data, row.idx));
                    btns.push(render_button('btn-edit-goods', row.idx, '수정', 'btn-default', 'btn-sm'));
                    btns.push(render_button('btn-delete-goods', row.idx, '삭제', 'btn-danger', 'btn-sm'));
                    if (!row.trade_currency_symbol) {
                        if (row.pack_info == "Y" || row.pack_info == "N") { // pack 상품 한개만 거래소등록
                            btns.push(render_button('btn-add-tradegoods', row.idx, '거래소등록', 'btn-success', 'btn-sm', '/coins/admin/coinAdmin.php?pg_mode=write&auction_goods_idx='+row.idx));
                        }
                    }
                    return btns.join('');
                }
            },
        ],
        "dom": '<html5buttons>Bfrtip',
        "buttons": [
            { extend: 'copy' },
            { extend: 'csv' },
            { extend: 'excel', title: 'AuctionGoodsList' },
            { extend: 'pdf', title: 'AuctionGoodsList' },
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

    
    $('.dataTables-auctionGoods')
    // 삭제 버튼
    .on('click', '[name="btn-delete-goods"]', function (evt) {
        let goods_idx = $(this).attr('data-idx');
        if(confirm('상품을 삭제하면 상품정보와 모든 경매 및 입찰 정보가 사라집니다.\n자료를 남겨야 한다면 숨김처리해 주세요.\n패키지 상품일 경우 하위있는 상품들도 삭제 됩니다.\n\n삭제 하시겠습니까?')) {
            $.post('?', 'pg_mode=delete-goods&goods_idx='+goods_idx, function(r){
                if(r && r.bool) {
                    alert('삭제 처리했습니다.');
                    datatable.ajax.reload(null, false);
                } else {
                    if (r.msg == 'err_sess') {
                        alert('다시 로그인해주세요.');
                        return false;
                    }
                    let msg = r.msg ? '\n'+r.msg : '';
                    alert('삭제 처리하지 못했습니다. '+msg);
                    datatable.ajax.reload();
                }
            }, 'json')
        }
        return false;
    })
    // 수정 버튼
    .on('click', '[name="btn-edit-goods"]', function(evt){
        let goods_idx = $(this).attr('data-idx');
        window.location.href = '?pg_mode=edit&idx='+goods_idx;
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