$(function() {

    // 새 데이터 쓰기
    $('#editform').on('submit', function(){
        var fm = this, $fm = $(fm);
        setTimeout(function () {
            pg_mode = $fm.find('[name=pg_mode]').val()
            idx = $fm.find('[name=idx]').val()
            // 이름 확인
            if($.trim($fm.find('[name=title]').val())=='') {
                alert('이름을 입력해주세요.'); return false;
            }
            // 이미지 확인
            if(pg_mode=='wirte' && $.trim($fm.find('[name=image_url]').val())=='') {
                alert('인증(마크) 이미지를 선택해주세요.'); return false;
            }
            // 저장.
            if (confirm('저장하시겠습니까?')) {
                $.post('?', $fm.serialize(), function(r){
                    if(r && r.bool) {
                        alert('저장되었습니다.');
                        window.location.href='?';
                    } else {
                        if (r.msg == 'err_sess') {
                            alert('로그인 해주세요.'); 
                            window.location.reload();
                            return false;
                        }
                        var msg = r.msg ? r.msg : '저장하지 못했습니다.';
                        alert(msg);
                    }
                }, 'json');
            }
        }, 1);
        return false;
    });


    // 아이콘 업로드
    $('[name="icon_file"]').on('change', function () { 
        upload($(this), 'aws', function (file_url, msg) { 
            let $box_image_url = $('#box_image_url');
            if (file_url) {
                $box_image_url.empty();
                $('<img src="'+file_url+'" class="icon_image" style="height:150px">').appendTo($box_image_url);
                $('<input type="hidden" name="image_url" value="'+file_url+'">').appendTo($box_image_url);
            } else {
                alert('이미지를 서버에 등록하지 못했습니다.'+(msg||''));
            }
        })
    })

});