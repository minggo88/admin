$(function () {

    // reset form
    const reset_news_form = function () {
        $('[name="box-news"] [name="news-subject"]').val('');
        $('[name="box-news"] [name="news-media"]').val('');
        $('[name="box-news"] [name="news-logo_img"]').val('');
        $('[name="box-news"] [name="news-contents"]').val('');
        $('[name="box-news"] [name="btn-save"]').attr('data-idx', '');
        $('[name="box-news"] [name="btn-cancel"]').hide();
    }

    // 파일 업로드
    $('[name="news-logo_img"]').on('change', function () { 
        upload($(this), 'aws', function (file_url) { 
            if (file_url) {
                $('[name="file_src"]').val(file_url);
            }
        })
    })

    // 글 쓰기
    $('[name="box-news"] [name="btn-save"]').on('click', function () { 
        const idx = $(this).attr('data-idx')||'';
        const link_idx = $(this).attr('data-link_idx')||'';
        const bbscode = $(this).attr('data-bbscode')||'';
        const subject = $.trim($('[name="box-news"] [name="news-subject"]').val())||'';
        const media = $.trim($('[name="box-news"] [name="news-media"]').val())||'';
        const file_src = $.trim($('[name="box-news"] [name="file_src"]').val())||'';
        const contents = $.trim($('[name="box-news"] [name="news-contents"]').val())||'';
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
            data: { 'token': getCookie('token'), 'idx': idx, 'link_idx': link_idx, 'bbscode': bbscode, 'subject': subject, 'media': media, 'file_src': file_src, 'contents': contents },
            cache: false,
            success: function (r) {
                if (r && r.payload) {
                    // alert('저장했습니다.');
                    get_news();
                    reset_news_form();
                } else {
                    const msg = r.error && r.error.message ? r.error.message : '';
                    alert('저장하지 못했습니다. ' + msg);
                    return false;
                }
            }
        });
    })

    // 게시글 가져오기
    const get_news = function () {
        const link_idx = $('[name="box-news"] [name="box-news-info"]').attr('data-link_idx') || '';
        const bbscode = $('[name="box-news"] [name="box-news-info"]').attr('data-bbscode') || '';
        $.post(API_URL+'/getBBSList/', { 'token': getCookie('token'), 'link_idx': link_idx, 'bbscode': bbscode, 'limit':100 }, function (r) { 
            if (r && r.payload) {
                // console.log(r.payload);
                const data = r.payload;
                let html = [];
                if (data && data.length > 0) {
                    const tpl = $('[name="box-news"] [name="box-news-tpl"]').html();
                    for (i in data) {
                        const row = data[i];
                        html.push(tpl
                            .replace(/\{idx\}/g, row.idx)
                            .replace(/\{nickname\}/g, row.author)
                            .replace(/\{subject\}/g, row.subject)
                            .replace(/\{news\}/g, nl2br(row.contents))
                            .replace(/\{website\}/g, nl2br(row.website))
                            .replace(/\{media\}/g, nl2br(row.media))
                            .replace(/\{hide_media\}/g, row.media)
                            .replace(/\{file_src\}/g, row.file_src)
                            .replace(/\{hide_file_src\}/g, row.file_src ? '' : 'hide')
                            .replace(/\{userno\}/g, row.userno)
                            .replace(/\{my_news\}/g, row.my_news)
                            .replace(/\{hide_ctrl\}/g, row.my_news==1 ? '' : 'hide')
                            .replace(/\{like_count\}/g, row.like_cnt||0)
                            .replace(/\{date\}/g, date('m-d H:i', row.regtime))
                        )
                    }
                } else {
                    html = '<div class="text-center">등록된 뉴스 글이 없습니다.</div>';
                }
                $('[name="box-news"] [name="box-news-list"]').html(html);
            }
        })
    }

    // 토론탭 클릭시 가져오기
    $('#information').on('click', get_news);
    // 삭제 버튼 클릭 = 댓글 삭제
    $('[name="box-news"] [name="box-news-list"]').on('click', '[name="btn-delete-news"]', function () {
        const idx = $(this).attr('data-idx');
        if (!idx) {
            alert(__('글번호가 없어 삭제하지 못했습니다.')); return false;
        }
        if (confirm(__('삭제 하시겠습니까?'))) {
            $.post(API_URL + '/deleteBbs/', { 'idx': idx, 'token': getCookie('token') }, function (r) {
                if (r && r.success) {
                    $('#news-' + idx).remove();
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
    $('[name="box-news"] [name="box-news-list"]').on('click', '[name="btn-edit-news"]', function () {
        const idx = $(this).attr('data-idx');
        if (!idx) {return false;}
        const $parent = $(this).closest('[name=tpl]');
        const subject = $parent.find('[name=subject]').text();
        const media = $parent.find('[name=media]').text();
        const news = $parent.find('[name=news]').text();
        $('[name="box-news"] [name="news-subject"]').val(subject);
        $('[name="box-news"] [name="news-media"]').val(media);
        $('[name="box-news"] [name="news-contents"]').val(news);
        $('[name="box-news"] [name="btn-save"]').attr('data-idx', idx);
        $('[name="box-news"] [name="btn-cancel"]').show();
        $('[name="box-news"] [name="btn-save"]').focus();
        return false;
    });
    // 취소 버튼 클릭
    $('[name="box-news"] [name="btn-cancel"]').on('click', function () {
        reset_news_form();
    });
    

});