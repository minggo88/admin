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
                          datatable.ajax.reload(null, false);
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
      , 'order': [ 0, 'desc' ]
      , 'ajax': {type: "post", url: "?", data: {'pg_mode':'list'}}
      , columns: [
          { data: 'userno', "responsivePriority": 1, "orderSequence": ['desc','asc'], "orderable": true },
          { data: 'userid', "responsivePriority": 1, "orderable": true },
          { data: 'name', "responsivePriority": 1, "orderable": true },
          { data: 'regdate', "orderSequence": ['desc','asc'], 'render': function(data) { return date('m-d H:i', data);} },
        //   { data: 'bool_confirm_join', "responsivePriority": 1, "orderable": false, 'render': function(data, p2, row) { return render_switch('admin', data, row.userno);} },
        //   { data: 'bool_confirm_join', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return data=='1' ? '' : '<button class="btn btn-sm btn-success btn_join_confirm" style="margin-right: 5px;" data-toggle="tooltip" data-placement="top" title="승인시 회원의 이전 기업페이를 입력해주세요" data-userno="'+row.userno+'" data-prev_stocks="'+urlencode(row.prev_stocks)+'" data-prev_airdrop="'+urlencode(row.prev_airdrop)+'">승인</button><br class="nl"/>';} },
          { data: 'mobile', "responsivePriority": 1, "orderable": false },
          { data: 'bool_confirm_mobile', "responsivePriority": 1, "orderable": false, 'render': function(data, p2, row) { return render_switch('mobile', data, row.userno);} },
          { data: 'image_identify_url', "orderable": true, 'render': function(data) { return data=='' ? '' : '<a href="'+data+'" target="_blank" class="thumbnail"><img src="'+data+'" width="50px" /></a>';} },
          { data: 'image_mix_url', "orderable": true, 'render': function(data) { return data=='' ? '' : '<a href="'+data+'" target="_blank" class="thumbnail"><img src="'+data+'" width="50px" /></a>';} },
          { data: 'bool_confirm_idimage', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return render_switch('idimage', data, row.userno)+'<br class="nl"/>'+row.confirm_idimage_date.substr(0,16) ;} },
          { data: 'bool_confirm_idimage', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return '<button class="btn btn-sm btn-warning btn_reject" style="margin-right: 5px;" data-toggle="tooltip" data-placement="top" title="반려시 회원에게 사유를 적어주세요" data-userno="'+row.userno+'">반려</button><br class="nl"/>'+row.reject_idimage_date.substr(0,16);} },
          { data: 'bank_name', "orderable": false },
          { data: 'bank_account', "orderable": false },
          { data: 'bank_owner', "orderable": true },
          { data: 'image_bank_url', "orderable": true, 'render': function(data) { return data=='' ? '' : '<a href="'+data+'" target="_blank" class="thumbnail"><img src="'+data+'" width="50px" /></a>';} },
          { data: 'bool_confirm_bank', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return render_switch('bank', data, row.userno)+'<br class="nl"/>'+row.confirm_bank_date.substr(0,16) ;} },
          { data: 'recomid', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return data+'<br>'+row.recomname+''; } },
          { data: 'bool_confirm_bank', "orderable": true, 'className': 'dt-body-center', 'render': function(data, p2, row) { return '<button class="btn btn-sm btn-warning btn_bank_reject" style="margin-right: 5px;" data-toggle="tooltip" data-placement="top" title="반려시 회원에게 사유를 적어주세요" data-userno="'+row.userno+'">반려</button><br class="nl"/>'+row.reject_bank_date.substr(0,16);} }
          // { data: 'userno', "responsivePriority": 1, "className":"dt-body-center", "orderable": false, 'render': function(data) { return '<a name="btn-edit" href="?pg_mode=edit&symbol='+data+'" class="btn btn-xs btn-default">수정</a> <a name="btn-delete" href="?pg_mode=delete&symbol='+data+'" class="btn btn-xs btn-danger">삭제</a>'; } }
      ]
      , "dom": '<html5buttons>Bfrtip'
      , "buttons": [
          {extend: 'copy'},
          {extend: 'csv'},
          {extend: 'excel', title: 'memberConfirm'},
          {extend: 'pdfHtml5', title: 'memberConfirm', fontSize: '10px', orientation: 'landscape', pageSize: 'LEGAL', pageMargins: [ 40, 60, 40, 60 ] },
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
          modal_box = '<div class="modal fade test" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">\
          <div class="modal-dialog" role="document">\
            <div class="modal-content">\
              <div class="modal-header">\
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                <h4 class="modal-title" id="rejectModalLabel">신분증 인증</h4>\
              </div>\
              <div class="modal-body">\
                  <div class="form-group">\
                    <label for="reject-reason" class="control-label">반려 메시지를 작성해주세요.</label>\
                    <textarea class="form-control" id="reject-reason" style="height:50px;"></textarea>\
                  </div>\
                  <label name="reject_msg"><input type="radio" name="radio_reject_msg"> 신분증에 노출되는 주민번호 뒷자리7자리만 가려주세요. 주민번호가 모두 노출될 경우 반려됩니다.</label>\
                  <label name="reject_msg"><input type="radio" name="radio_reject_msg"> 신분증 인증이 반려되었습니다. 올바른 사진을 등록하셨는지 확인해 주세요.</label>\
              </div>\
              <div class="modal-footer">\
                <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>\
                <button type="button" class="btn btn-primary" id="btn_do_reject" data-userno="">반려</button>\
              </div>\
            </div>\
          </div>\
        </div>';

        console.log($('#direct_buy_Modal').length);
      if($('#rejectModal').length<1) {
          $('body').append(modal_box);
          $('#btn_do_reject').on('click', reject);
          $('#rejectModal').on('click', '[name=reject_msg]', function(e){
              var msg = $(this).text();
              $('#reject-reason').val(msg);
          });
      }
      $('#btn_do_reject').attr('data-userno', userno);
      $('#rejectModal').modal().on('shown.bs.modal', function(e){
          $('#reject-reason').val($('[name=reject_msg]:last').text()).focus();
          $('[name=reject_msg]:last').click();
      }).on('hidden.bs.modal', function(e){
          $('#reject-reason').val('');
          $('#btn_do_reject').attr('data-userno','');
      });
  });

  var bankreject = function() {
      var bankreject_reason = $('#bankreject-reason').val(),
          userno = $('#btn_do_bankreject').attr('data-userno');
      $.post('?', {'pg_mode':'reject', 'type':'bank', 'userno':userno, 'reject_reason':bankreject_reason}, function(r){
          if(r && r.bool) {
              // $self.toggleClass('toggle-on').attr('data-value',confirm_value);
              $('#bankrejectModal').modal('hide');
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

  // 통장사본 반려
  $('body').on('click', '.btn_bank_reject', function(e){
      e.preventDefault(); // The flicker is a codepen thing
      var $self = $(this),
          userno = $self.attr('data-userno'),
          modal_box = '<div class="modal fade" id="bankrejectModal" tabindex="-1" role="dialog" aria-labelledby="bankrejectModalLabel">\
          <div class="modal-dialog" role="document">\
            <div class="modal-content">\
              <div class="modal-header">\
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                <h4 class="modal-title" id="bankrejectModalLabel">은행계좌 인증</h4>\
              </div>\
              <div class="modal-body">\
                  <div class="form-group">\
                    <label for="bankreject-reason" class="control-label">반려 메시지를 작성해주세요.</label>\
                    <textarea class="form-control" id="bankreject-reason" style="height:50px;"></textarea>\
                  </div>\
                  <label name="bankreject_msg"><input type="radio" name="radio_bankreject_msg"> 가입시 등록한 본인의 은행계좌를 등록해 주세요.</label>\
                  <label name="bankreject_msg"><input type="radio" name="radio_bankreject_msg"> 은행계좌 인증이 반려되었습니다. 올바른 통장사진을 등록하셨는지 확인해 주세요.</label>\
              </div>\
              <div class="modal-footer">\
                <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>\
                <button type="button" class="btn btn-primary" id="btn_do_bankreject" data-userno="">반려</button>\
              </div>\
            </div>\
          </div>\
        </div>';
      if($('#bankrejectModal').length<1) {
          $('body').append(modal_box);
          $('#btn_do_bankreject').on('click', bankreject);
          $('#bankrejectModal').on('click', '[name=bankreject_msg]', function(e){
              var msg = $(this).text();
              $('#bankreject-reason').val(msg);
          });
      }
      $('#btn_do_bankreject').attr('data-userno', userno);
      $('#bankrejectModal').modal().on('shown.bs.modal', function(e){
          $('#bankreject-reason').val($('[name=bankreject_msg]:last').text()).focus();
          $('[name=bankreject_msg]:last').click();
      }).on('hidden.bs.modal', function(e){
          $('#bankreject-reason').val('');
          $('#btn_do_bankreject').attr('data-userno','');
      });
  });
    
    var confirmJoin = function() {
        var prev_krw_amount = $('#prev_krw_amount').val(),
            userno = $('#btn_do_confirm_join').attr('data-userno');
        $.post('?', {'pg_mode':'confirm', 'type':'join', 'userno':userno, 'prev_krw_amount':prev_krw_amount}, function(r){
            if(r && r.bool) {
                // $self.toggleClass('toggle-on').attr('data-value',confirm_value);
                $('#confirmJoinModal').modal('hide');
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

    // 가입 승인
    $('body').on('click', '.btn_join_confirm', function(e){
        e.preventDefault(); // The flicker is a codepen thing
        var $self = $(this),
            prev_stocks = $self.attr('data-prev_stocks'),
            prev_stocks = prev_stocks ? JSON.parse(urldecode(prev_stocks)) : [];
            console.log('prev_stocks:', prev_stocks)
            prev_airdrop = $self.attr('data-prev_airdrop'),
            prev_airdrop = prev_airdrop ? JSON.parse(urldecode(prev_airdrop)) : [];
            console.log('prev_airdrop:', prev_airdrop)
            userno = $self.attr('data-userno'),
            modal_box = '<div class="modal fade" id="confirmJoinModal" tabindex="-1" role="dialog" aria-labelledby="confirmJoinModalLabel">\
          <div class="modal-dialog" role="document">\
            <div class="modal-content">\
            <div class="modal-header">\
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
              <h4 class="modal-title" id="confirmJoinModalLabel">가입 인증</h4>\
            </div>\
              <div class="modal-body">\
                  <div class="form-group">\
                    <label class="control-label">이전 주식 잔고</label>\
                    <div class="fixedHeaderTable" style="height: 200px;"><table class="table table-bordered">\
                    <thead><tr><th>닉네임</th><th>종목명</th><th>주식수</th></tr></thead><tbody>';
            if (prev_stocks && prev_stocks.length > 0) {
                for (i in prev_stocks) {
                    const row = prev_stocks[i];
                    modal_box += '<tr><td>'+row.nickname+'</td><td>'+row.stock_name+'</td><td class="text-right">'+real_number_format(row.balance)+'</td></tr>';
                }
            } else {
                modal_box += '<tr><td colspan="3" class="text-center">이전 주식의 잔고가 없습니다.</td></tr>';
            }
            modal_box+= '</tbody></table></div>\
                  </div>\
                  <div class="form-group">\
                    <label class="control-label">이전 스톡옵션</label>\
                    <div class="fixedHeaderTable" style="height: 200px;"><table class="table table-bordered">\
                    <thead><tr><th>닉네임</th><th>종목명</th><th>주식수</th><th>발행날짜</th></tr></thead><tbody>';
            if (prev_airdrop && prev_airdrop.length > 0) {
                for (i in prev_airdrop) {
                    const row = prev_airdrop[i];
                    modal_box += '<tr><td>'+row.nickname+'</td><td>'+row.stock_name+'</td><td class="text-right">'+real_number_format(row.volumn)+'</td><td>'+(row.regdate||'').substr(0,10)+'</td></tr>';
                }
            } else {
                modal_box += '<tr><td colspan="4" class="text-center">이전 스톡옵션의 정보가 없습니다.</td></tr>';
            }
            modal_box+= '</tbody></table></div>\
                  </div>\
                  <div class="form-group">\
                    <label for="prev_krw_amount" class="control-label hide">기업페이</label>\
                    <div class="input-group"><div class="input-group-addon">기업페이</div><input type="text" class="form-control text-right" id="prev_krw_amount" name="prev_krw_amount"><div class="input-group-addon">원</div></div>\
                  </div>\
              </div>\
              <div class="modal-footer">\
                <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>\
                <button type="button" class="btn btn-success" id="btn_do_confirm_join" data-userno="">승인</button>\
              </div>\
            </div>\
          </div>\
        </div>';
        if($('#confirmJoinModal').length>0) {
            $('#confirmJoinModal').remove();
        }
        $('body').append(modal_box);
        $('#btn_do_confirm_join').on('click', confirmJoin);
        // $('#confirmJoinModal').on('click', '[name=prev_krw_amount]', function(e){
        //     var msg = $(this).text();
        //     $('#prev_krw_amount').val(msg);
        // });
        $('#confirmJoinModal').modal().on('shown.bs.modal', function(e){
            $('#prev_krw_amount').val($('[name=prev_krw_amount]:last').text()).focus();
            $('[name=prev_krw_amount]:last').click();
        }).on('hidden.bs.modal', function(e){
            $('#prev_krw_amount').val('');
            $('#btn_do_confirm_join').attr('data-userno', '');
            datatable.ajax.reload(null, false);
        });
        $('#btn_do_confirm_join').attr('data-userno', userno);
    });
    
});