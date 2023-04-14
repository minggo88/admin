$(function() {

    var render_switch = function(type, value, userno) {
        return '<input type="checkbox" '+(value=='1' ? 'checked' : '')+' data-toggle="toggle" data-on="승인" data-off="비승인" data-size="small" data-type="'+type+'" data-value="'+value+'" data-userno="'+userno+'">';
    };

    var after_draw = function(){
        $("a.thumbnail").each(function(){
            if($(this).attr('data-event-added')!='1') {
                $(this).magnificPopup({type:'image'}).attr('data-event-added','1');
            }
        });
        $('input[data-toggle=toggle]').each(function(){
            if($(this).attr('data-event-added')!='1') {
                $(this).bootstrapToggle().change(function(){
                    console.log('checked', $(this).prop('checked'));
                    // e.preventDefault(); // The flicker is a codepen thing
                    var $self = $(this),
                        confirm_value = $self.prop('checked') ? '1' : '0',
                        confirm_type = $self.attr('data-type'),
                        userno = $self.attr('data-userno');
                        console.log({'pg_mode':'confirm', 'type':confirm_type, 'value':confirm_value, 'userno':userno});
                    $.post('?', {'pg_mode':'confirm', 'type':confirm_type, 'value':confirm_value, 'userno':userno}, function(r){
                        console.log('result:', r);
                        if(r && r.bool) {
                            $self.toggleClass('toggle-on').attr('data-value',confirm_value);
                            // if(confirm_type=='idimage'){datatable.ajax.reload(null, false);}
                        } else {
                            if(r.msg=='err_sess') {// 로그아웃 된 경우.
                                alert('다시 로그인 해주세요.');
                                window.location.reload();
                            } else {
                                alert(r.msg);
                            }
                        }
                    }, 'json');
                }).attr('data-event-added','1');
            }
        })
    }

    var datatable = $('.dataTables-customers').addClass( 'nowrap' ).DataTable({
    "search": {
        "search": getURLParameter('search')||'',
    },
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
        , 'order': [ 3, 'desc' ]
        , 'ajax': {type: "post", url: "?", data: {'pg_mode':'list'}}
        , columns: [
            { data: 'userno', "responsivePriority": 1, "orderSequence": ['desc','asc'], "orderable": true },
            { data: 'userid', "responsivePriority": 1, "orderable": true },
            { data: 'name', "orderable": true },
            { data: 'regdate', "orderSequence": ['desc','asc'], 'render': function(data) { return date('m-d H:i', data);} },
            { data: 'mobile', "responsivePriority": 1, "orderable": false },
            { data: 'bool_confirm_mobile', "responsivePriority": 1, "orderable": false, 'render': function(data, p2, row) { return render_switch('mobile', data, row.userno);} },
            { data: 'email', "responsivePriority": 1, "orderable": true },
            { data: 'bool_confirm_email', "responsivePriority": 1, "orderable": false, 'render': function(data, p2, row) { return render_switch('email', data, row.userno);} },
            { data: 'image_identify_url', "orderable": false, 'render': function(data) { return data=='' ? '' : '<a href="'+data+'" target="_blank" class="thumbnail"><img src="'+data+'" width="50px" /></a>';} },
            { data: 'image_mix_url', "orderable": false, 'render': function(data) { return data=='' ? '' : '<a href="'+data+'" target="_blank" class="thumbnail"><img src="'+data+'" width="50px" /></a>';} },
            { data: 'bool_confirm_idimage', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return render_switch('idimage', data, row.userno)+'<br class="nl"/>'+row.confirm_idimage_date.substr(0,16) ;} },
            { data: 'bool_confirm_idimage', "orderable": false, 'className': 'dt-body-center', 'render': function(data, p2, row) { return '<button class="btn btn-sm btn-warning btn_reject" style="margin-right: 5px;" data-toggle="tooltip" data-placement="top" title="반려시 회원에게 사유를 적어주세" data-userno="'+row.userno+'">반려</button><br class="nl"/>'+row.reject_idimage_date.substr(0,16);} },
            { data: 'bank_name', "orderable": false },
            { data: 'bank_account', "orderable": false },
            { data: 'bank_owner', "orderable": true },
            // { data: 'userno', "responsivePriority": 1, "className":"dt-body-center", "orderable": false, 'render': function(data) { return '<a name="btn-edit" href="?pg_mode=edit&symbol='+data+'" class="btn btn-xs btn-default">수정</a> <a name="btn-delete" href="?pg_mode=delete&symbol='+data+'" class="btn btn-xs btn-danger">삭제</a>'; } }
        ]
        , "dom": '<html5buttons>Bfrtip'
        , "buttons": [
            {extend: 'copy'},
            {extend: 'csv'},
            {extend: 'excel', title: 'memberConfirm'},
            {extend: 'pdf', title: 'memberConfirm'},

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
        , "error": function (xhr) {console.log (xhr.responseText);}
        , select: true
    }).on('draw', after_draw).on('responsive-display', after_draw);

    // Activate an inline edit on click of a table cell
    $('.dataTables-customers').on( 'click', '[name=btn-delete]', function (e) {
        var url = $(this).attr('href'), url = url.split('?'), param = url[1], url = url[0]+'?';
        if(confirm('삭제하시겠습니까?')) {
            setTimeout(function(){
                $.post(url, param, function(r){
                    if(r && r.bool){
                        datatable.ajax.reload(null, false);
                    } else {
                        var msg = r && r.msg && r.msg!='' ? r.msg : '삭제하지 못했습니다.';
                        if(msg=='err_sess') {// 로그아웃 된 경우.
                            alert('다시 로그인 해주세요.');
                            window.location.reload();
                        } else {
                            alert(msg);
                        }
                    }
                }, 'json');
            }, 1);
        }
        return false;
    } );

    var reject = function() {
        var reject_reason = $('#reject-reason').val(),
            userno = $('#btn_do_reject').attr('data-userno');
        $.post('?', {'pg_mode':'reject', 'type':'idimage', 'userno':userno, 'reject_reason':reject_reason}, function(r){
            if(r && r.bool) {
                // $self.toggleClass('toggle-on').attr('data-value',confirm_value);
                $('#rejectModal').modal('hide');
                datatable.ajax.reload(null, false);
            } else {
                if(r.msg=='err_sess') {// 로그아웃 된 경우.
                    alert('다시 로그인 해주세요.');
                    window.location.reload();
                } else {
                    alert(r.msg);
                }
            }
        }, 'json');
    }

    // 반려
    $('body').on('click', '.btn_reject', function(e){
        e.preventDefault(); // The flicker is a codepen thing
        var $self = $(this),
            userno = $self.attr('data-userno'),
            modal_box = '<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">\
            <div class="modal-dialog" role="document">\
              <div class="modal-content">\
                <div class="modal-header">\
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                  <h4 class="modal-title" id="rejectModalLabel">신분증 인증</h4>\
                </div>\
                <div class="modal-body">\
                    <div class="form-group">\
                      <label for="reject-reason" class="control-label">반려 메시지를 작성해주세요.</label>\
                      <textarea class="form-control" id="reject-reason" style="height:100px;"></textarea>\
                    </div>\
                    <div id="preview_msg">* 기본 전송 메시지: 신분증 인증이 반려되었습니다. 올바른 사진을 등록하셨는지 확인해 주세요.</div>\
                </div>\
                <div class="modal-footer">\
                  <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>\
                  <button type="button" class="btn btn-primary" id="btn_do_reject" data-userno="">반려</button>\
                </div>\
              </div>\
            </div>\
          </div>';
        if($('#rejectModal').length<1) {
            $('body').append(modal_box);
            $('#btn_do_reject').on('click', reject);
        }
        $('#btn_do_reject').attr('data-userno', userno);
        $('#rejectModal').modal().on('shown.bs.modal', function(e){
            $('#reject-reason').val('').focus();
        }).on('hidden.bs.modal', function(e){
            $('#reject-reason').val('');
            $('#btn_do_reject').attr('data-userno','');
        });
    });

});
