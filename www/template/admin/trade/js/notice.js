$(function () {

    // reset form
    const reset_notice_form = function () {
        $('[name="box-notices"] [name="notice-subject"]').val('');
        $('[name="box-notices"] [name="notice-media"]').val('');
        $('[name="box-notices"] [name="notice-logo_img"]').val('');
        $('[name="box-notices"] [name="notice-contents"]').val('');
        $('[name="box-notices"] [name="btn-save"]').attr('data-idx', '');
        $('[name="box-notices"] [name="btn-cancel"]').hide();
    }

    // 글 쓰기
    $('[name="box-notices"] [name="btn-save"]').on('click', function () { 
        const idx = $(this).attr('data-idx')||'';
        const link_idx = $(this).attr('data-link_idx')||'';
        const bbscode = $(this).attr('data-bbscode')||'';
        const subject = $.trim($('[name="box-notices"] [name="notice-subject"]').val())||'';
        const media = $.trim($('[name="box-notices"] [name="notice-media"]').val())||'';
        const file = $.trim($('[name="box-notices"] [name="notice-logo_img"]').val())||'';
        const contents = $.trim($('[name="box-notices"] [name="notice-contents"]').val())||'';
        if (!link_idx) {
            // alert('오류가 발생하여 댓글을 작성 하실 수 없습니다.');
            return false;
        }
        if (!contents) {
            alert('내용을 적어주세요.'); return false;
        }
        // 
        $.ajax({
            type: "post",
            dataType: "json",  //xml,html,jeon,jsonp,script,text
            url: API_URL+'/putBbs/',
            data: { 'token': getCookie('token'), 'idx': idx, 'link_idx': link_idx, 'bbscode': bbscode, 'subject': subject, 'media': media, 'file': file, 'contents': contents },
            cache: false,
            success: function (r) {
                if (r && r.payload) {
                    // alert('저장했습니다.');
                    get_notice();
                    reset_notice_form();
                } else {
                    const msg = r.error && r.error.message ? r.error.message : '';
                    alert('저장하지 못했습니다. ' + msg);
                    return false;
                }
            }
        });
    })

    // 게시글 가져오기
    const get_notice = function () {
        const link_idx = $('[name="box-notices"] [name="box-notice-info"]').attr('data-link_idx') || '';
        const bbscode = $('[name="box-notices"] [name="box-notice-info"]').attr('data-bbscode') || '';
        $.post(API_URL+'/getBBSList/', { 'token': getCookie('token'), 'link_idx': link_idx, 'bbscode': bbscode, 'limit':100 }, function (r) { 
            if (r && r.payload) {
                // console.log(r.payload);
                const data = r.payload;
                let html = [];
                if (data && data.length > 0) {
                    const tpl = $('[name="box-notices"] [name="box-notice-tpl"]').html();
                    for (i in data) {
                        const row = data[i];
                        html.push(tpl
                            .replace(/\{idx\}/g, row.idx)
                            .replace(/\{nickname\}/g, row.author)
                            .replace(/\{subject\}/g, row.subject)
                            .replace(/\{notice\}/g, nl2br(row.contents))
                            .replace(/\{website\}/g, nl2br(row.website))
                            .replace(/\{media\}/g, nl2br(row.media))
                            .replace(/\{media_logo\}/g, nl2br(row.file))
                            .replace(/\{userno\}/g, row.userno)
                            .replace(/\{my_notice\}/g, row.my_notice)
                            .replace(/\{hide_ctrl\}/g, row.my_notice==1 ? '' : 'hide')
                            .replace(/\{like_count\}/g, row.like_cnt||0)
                            .replace(/\{date\}/g, date('m-d H:i', row.regtime))
                        )
                    }
                } else {
                    html = '<div class="text-center">등록된 공시 글이 없습니다.</div>';
                }
                $('[name="box-notices"] [name="box-notices-list"]').html(html);
            }
        })
    }

    // 토론탭 클릭시 가져오기
    $('#information').on('click', get_notice);
    // 삭제 버튼 클릭 = 댓글 삭제
    $('[name="box-notices"] [name="box-notices-list"]').on('click', '[name="btn-delete-notice"]', function () {
        const idx = $(this).attr('data-idx');
        if (!idx) {
            alert(__('글번호가 없어 삭제하지 못했습니다.')); return false;
        }
        if (confirm(__('삭제 하시겠습니까?'))) {
            $.post(API_URL + '/deleteBbs/', { 'idx': idx, 'token': getCookie('token') }, function (r) {
                if (r && r.success) {
                    $('#notice-' + idx).remove();
                    alert('삭제되었습니다.');
                } else {
                    const msg = r.error && r.error.message ? r.error.message : '';
                    alert('삭제하지 못했습니다. ' + msg);
                    return false;
                }
            });
        }
        return false;
    });
    // 수정 버튼 클릭
    $('[name="box-notices"] [name="box-notices-list"]').on('click', '[name="btn-edit-notice"]', function () {
        const idx = $(this).attr('data-idx');
        if (!idx) {return false;}
        const $parent = $(this).closest('[name=tpl]');
        const subject = $parent.find('[name=subject]').text();
        const media = $parent.find('[name=media]').text();
        const notice = $parent.find('[name=notice]').text();
        $('[name="box-notices"] [name="notice-subject"]').val(subject);
        $('[name="box-notices"] [name="notice-media"]').val(media);
        $('[name="box-notices"] [name="notice-contents"]').val(notice);
        $('[name="box-notices"] [name="btn-save"]').attr('data-idx', idx);
        $('[name="box-notices"] [name="btn-cancel"]').show();
        $('[name="box-notices"] [name="btn-save"]').focus();
        return false;
    });
    // 취소 버튼 클릭
    $('[name="box-notices"] [name="btn-cancel"]').on('click', function () {
        reset_notice_form();
    });
    

});