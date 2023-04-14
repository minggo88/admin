
const API_URL = '//api.' + window.location.host.replace('www.', '') + '/v1.0';

$(function () {

    // reset form
    const reset_form = function () {
        $('[name="box-comments"] [name="comment-contents"]').val('');
        $('[name="box-comments"] [name="btn-save"]').attr('data-idx', '');
        $('[name="box-comments"] [name="btn-cancel"]').hide();
    }

    // 댓글 쓰기
    $('[name="box-comments"] [name="btn-save"]').on('click', function () { 
        const idx = $(this).attr('data-idx')||'';
        const link_idx = $(this).attr('data-link_idx')||'';
        const bbscode = $(this).attr('data-bbscode')||'';
        const contents = $.trim($('[name="box-comments"] [name="comment-contents"]').val())||'';
        if (!link_idx) {
            // alert('오류가 발생하여 댓글을 작성 하실 수 없습니다.');
            return false;
        }
        if (!contents) {
            alert('의견을 적어주세요.'); return false;
        }
        // 
        $.ajax({
            type: "post",
            dataType: "json",  //xml,html,jeon,jsonp,script,text
            url: API_URL+'/putComment/',
            data: { 'token': getCookie('token'), 'idx': idx, 'link_idx': link_idx, 'bbscode': bbscode, 'contents': contents },
            cache: false,
            success: function (r) {
                if (r && r.payload) {
                    // alert('저장했습니다.');
                    get_comment();
                    reset_form();
                } else {
                    const msg = r.error && r.error.message ? r.error.message : '';
                    alert('저장하지 못했습니다. ' + msg);
                    return false;
                }
            }
        });
    })

    // 댓글 가져오기
    const get_comment = function () {
        const link_idx = $('[name="box-comments"] [name="btn-save"]').attr('data-link_idx') || '';
        const bbscode = $('[name="box-comments"] [name="btn-save"]').attr('data-bbscode') || '';
        $.post(API_URL+'/getCommentList/', { 'token': getCookie('token'), 'link_idx': link_idx, 'bbscode': bbscode, 'limit':100 }, function (r) { 
            if (r && r.payload) {
                console.log(r.payload);
                const data = r.payload;
                let html = [];
                if (data && data.length > 0) {
                    const tpl = $('[name="box-comments"] [name="box-comment-tpl"]').html();
                    for (i in data) {
                        const row = data[i];
                        html.push(tpl
                            .replace(/\{idx\}/g, row.idx)
                            .replace(/\{nickname\}/g, row.author)
                            .replace(/\{comment\}/g, nl2br(row.contents))
                            .replace(/\{userno\}/g, row.userno)
                            .replace(/\{my_comment\}/g, row.my_comment)
                            .replace(/\{hide_ctrl\}/g, row.my_comment==1 ? '' : 'hide')
                            .replace(/\{like_count\}/g, row.like_cnt||0)
                            .replace(/\{date\}/g, date('m-d H:i', row.regtime))
                        )
                    }
                }
                $('[name="box-comments"] [name="box-comments-list"]').html(html);
            }
        })
    }
    // 토론탭 클릭시 가져오기
    $('#talk').on('click', get_comment);
    // 삭제 버튼 클릭 = 댓글 삭제
    $('[name="box-comments"] [name="box-comments-list"]').on('click', '[name="btn-delete-comment"]', function () {
        const idx = $(this).attr('data-idx');
        if (!idx) {
            alert(__('글번호가 없어 삭제하지 못했습니다.')); return false;
        }
        if (confirm(__('삭제 하시겠습니까?'))) {
            $.post(API_URL + '/deleteComment/', { 'idx': idx, 'token': getCookie('token') }, function (r) {
                if (r && r.success) {
                    $('#comment-' + idx).remove();
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
    $('[name="box-comments"] [name="box-comments-list"]').on('click', '[name="btn-edit-comment"]', function () {
        const idx = $(this).attr('data-idx');
        if (!idx) {return false;}
        const $parent = $(this).closest('[name=tpl]');
        const comment = $parent.find('[name=comment]').text();
        $('[name="box-comments"] [name="comment-contents"]').val(comment);
        $('[name="box-comments"] [name="btn-save"]').attr('data-idx', idx);
        $('[name="box-comments"] [name="btn-cancel"]').show();
        $('[name="box-comments"] [name="btn-save"]').focus();
        return false;
    });
    // 취소 버튼 클릭
    $('[name="box-comments"] [name="btn-cancel"]').on('click', function () {
        reset_form();
    });
    

});