$(function() {

    const tpl = '<tr><td>{no}</td><td>{종목이름}</td><td>{회원닉네임}</td><td>{회원아이디}</td><td>{회원이름}</td><td class="text-right">{발행수량}</td><td class="text-right">{발행가격}</td><td class="text-right">{발행금액}</td><td class="text-right">{보유날짜}</td><td class="text-right">{보유기간}</td><td class="text-right">{등록날짜}</td></tr>'
    const get_list = function () {
        $.post('?', { 'pg_mode': 'get_old_member_airdrop', 'symbol': getURLParameter('symbol') }, function (r) {
            if (r && r.success) {
                if (r.payload.length > 0) {
                    let html = [];
                    let no = r.payload.length;
                    for (i in r.payload) {
                        let row = r.payload[i];
                        html.push(tpl
                            .replace(/\{no\}/, no--)
                            .replace(/\{종목이름\}/, row.name||'')
                            .replace(/\{회원닉네임\}/, row.nickname||'')
                            .replace(/\{회원아이디\}/, row.old_userid||'')
                            .replace(/\{회원이름\}/, row.old_username||'')
                            .replace(/\{발행수량\}/, real_number_format(row.volumn))
                            .replace(/\{발행가격\}/, real_number_format(row.price))
                            .replace(/\{발행금액\}/, real_number_format(row.amount))
                            .replace(/\{보유날짜\}/, row.lockup_date)
                            .replace(/\{보유기간\}/, real_number_format(row.lockup_day))
                            .replace(/\{등록날짜\}/, row.regdate)
                        )
                    }
                    $('#box_list').html(html.join(''));
                } else {
                    $('#box_list').html('<tr><td colspan="10" class="text-center">데이터가 없습니다.</td><tr>');
                }
            }
        }, 'json');   
    }
    get_list();

    $('[name="btn-write_old_airdrop_data"]').on('click', function () {
        $('[name="form_write"]').submit();
        return false; 
    });
    $('[name="form_write"]').on('submit', function () { 
        let form = this, $fm = $(this), file = $.trim($fm.find('[name=old_airdrop_data]').val());
        if(file=='') {
            alert('이전 스톡옵션 데이터 파일을 선택해주세요.'); return false;
        }
        if ( file.toLowerCase().indexOf("xlsx")<0 && file.toLowerCase().indexOf("xls")<0 )
        {
            alert('엑셀 파일(xlsx, xls)만 지원합니다.');
            return false;
        }
        let data = new FormData(form);

        if (confirm('이전 데이터가 삭제되고 새로 입력 됩니다.\n새로운 스톡옵션 데이터로 교채하시겠습니까?')) {
            $.ajax({
                'type': "POST",
                'enctype': 'multipart/form-data', 
                'url': '?',
                'data': data,
                'processData': false,
                'contentType': false, 
                'dataType':'JSON',
                'success': function (r) {
                    if (r && !r.bool && r.msg === 'err_sess') {
                        alert('다시 로그인 해주세요');
                        window.location.reload();
                        return false;
                    }
                    if (r && r.success) {
                        $('#box_list').html('');
                        alert("등록했습니다.");
                        get_list();
                    }
                }, 
                'error': function (r) {
                    alert('파일을 등록하지 못했습니다. \n페이지를 새로고침 후 다시 등록해주세요.');
                }
            })
        }
        return false;
    });

});
