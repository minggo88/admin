$(function() {

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
                    'pg_mode':'getemailhistory'
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
