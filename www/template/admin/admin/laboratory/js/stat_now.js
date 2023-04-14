var isloaded = false;

$(function() {
    if (isloaded) return; //중복 호출방지
    var userid = '';
    var username = '';
    var mobile = '';
    var s_branch = '';

    function draw_datatables_stat_now() {
		var datatable = $('[name=db_list]');
		datatable.addClass('nowrap');
		datatable.DataTable({
            "language": {
                "emptyTable": "데이터가 없음.",
                "lengthMenu": '페이지당 <select><option value="500" selected>500</option><option value="10000000">All</option></select> 보기',
                "info": "현재 _START_ - _END_ / _TOTAL_건",
                "infoEmpty": "",
                "infoFiltered": "( _MAX_건의 데이터에서 필터링됨 )",
                "search": "검색: ",
                "zeroRecords": "일치하는 데이터가 없습니다.",
                "loadingRecords": "로딩중...",
                "processing": '<img src="/template/admin/script/plug_in/loading/loading.gif"> 잠시만 기다려 주세요.',
                "paginate": {
                    "next": "다음",
                    "previous": "이전"
                }
            },
            // "searching": false,
            // "deferRender": true,
            // "fixedColumns": true,
            "destroy": true,
            "responsive": true,
            "processing": true,
            "serverSide": true,
            "pageLength": 500,
            "order": [[6, 'desc']],
            "ajax": {
                type: "post",
                url: "?",
                data: {
                    'pg_mode': 'loopStateNowData',
                    'userid': userid,
                    'username': username,
                    'mobile': mobile,
                    'branch': s_branch
                }
            },
            "columns": [
                { data: 'userid', "responsivePriority": 1, "className": "text-left", "orderable": true, "width": "" }, //사용자Id "orderSequence": ['asc','desc'],
				{ data: 'name', "className": "text-center", "orderable": false, "width": "" }, //이름

				{ data: 'amount_krw_in', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" }, // krw 입금
				{ data: 'amount_krw_out', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" }, // krw 출금

                { data: 'amount_htp_balance', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" }, // htp 잔액
                { data: 'amount_htc_balance', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" },
				{ data: 'amount_gws_balance', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" },
				{ data: 'amount_bdc_balance', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" },

                { data: 'amount_htc_sell_regist', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" }, // gws 매도중
				{ data: 'amount_gws_sell_regist', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" },
				{ data: 'amount_bdc_sell_regist', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" },

                { data: 'amount_htc_sell_success', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" }, // gws 매도총량
				{ data: 'amount_gws_sell_success', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" },
				{ data: 'amount_bdc_sell_success', "className": "text-right", "orderSequence": ['desc', 'asc'], "orderable": true, "width": "" }

            ],
            dom: '<html5buttons>Bfrtip',
            "columnDefs": [
                { "targets": 0, "render": function(data, type, row, meta) { return data.replace(/mobile/, '').replace(/^82/, '0'); } },
                { "targets": 2, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 3, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 4, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 5, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 6, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 7, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 8, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 9, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 10, "render": function(data, type, row, meta) { return number_format(data); } },
                { "targets": 11, "render": function(data, type, row, meta) { return number_format(data); } }
            ],
            buttons: [
                { extend: 'copy' },
                { extend: 'csv', title: '현재회원별현황_'+date('YmdHis') },
                { extend: 'excel', title: '현재회원별현황_'+date('YmdHis') },
                { extend: 'pdf', title: '현재회원별현황'+date('YmdHis') },
                { extend: 'print', customize: function(win) {
					$(win.document.body).addClass('white-bg');
					$(win.document.body).css('font-size', '10px');
					$(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
				}}
            ],
            "error": function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    draw_datatables_stat_now();

    $('#btn-search').on('click', function() {
        userid = $('#userid').val().replace(/^0(.)/,'$1'); // 0으로 시작하는 경우 0 제거
        username = $('#username').val();
        mobile = $('#mobile').val().replace(/^0(.)/,'$1');
        s_branch = $('#s_branch').val();
        draw_datatables_stat_now();
    });

    isloaded = true;
});