
function searchCancel() {
    location.href = '<!--{_SERVER.SCRIPT_NAME}-->?bbscode=<!--{_GET.bbscode}-->';
}

function searchCheck() {
    if (!$('#searchform :checkbox:checked').size()) {
        alert('검색조건을 선택하여 주세요');
        return false;
    }
    if (!$('#s_val').val()) {
        alert('검색어를 입력하여 주세요');
        return false;
    }
    $('#searchform').submit();
}

function multiControl() {
    var chk_size = $('#list_form tbody :checkbox:checked').length;
    if (chk_size == 0) {
        alert('선택된 항목이 없습니다.!');
        return false;
    }
    var pg_mode = $('#controlform input[name=pg_mode]:checked').val();
    var idxs = $('#list_form').serialize();
    var control = $('#controlform').serialize();

    if (pg_mode == 'move_multi') {
        if ($('select#movebbscode')[0].selectedIndex == 0) {
            alert('이동할 게시판을 선택해주세요');
            return false;
        }
        var target_bbs = $('select#movebbscode option:selected').val();

        if (target_bbs == '<!--{_GET.bbscode}-->') {
            alert('다른 게시판을 선택해주세요.!');
            return false;
        }
        if (!confirm('선택한 게시물을 이동하시겠습니까?')) {
            return false;
        }
    }
    else {
        if (!confirm('선택한 게시물을 삭제하시겠습니까?')) {
            return false;
        }
    }

    $.post('<!--{_SERVER.SCRIPT_NAME}-->', control + '&' + idxs, function (data) {
        if (data['bool']) {
            location.replace('<!--{_SERVER.REQUEST_URI}-->');
        }
        else {
            if (data['msg'] == 'err_access') {
                alert('비정상적인 접근입니다.');
            }
            else if (data['msg'] == 'err_sess') {
                location.replace('/admin/auth.php?ret_url=<!--{=base64_encode(_SERVER.REQUEST_URI)}-->');
            }
            else {
                alert('재시도 해주세요!');
            }
        }
    }, 'json');
}

jQuery(function ($) {
    $('#gobbscode').change(function () {
        var idx = $(this)[0].selectedIndex;
        if (idx == 0) {
            location.href = '<!--{_SERVER.REQUEST_URI}-->';
        }
        else {
            location.href = '<!--{_SERVER.SCRIPT_NAME}-->?bbscode=' + $('option:selected', this).val();
        }
    });

    $("#list_form>table>tbody>tr").hover(
        function () { $(this).css('background-color', '#FFF2F0'); },
        function () { $(this).css('background-color', '#FFF'); }
    ).css('cursor', 'pointer');

    $('#all_check').click(function () {
        if (this.checked) {
            $("#list_form table tbody :checkbox").attr('checked', 'checked');
        }
        else {
            $("#list_form table tbody :checkbox").removeAttr('checked');
        }
    });

    $('#list_form input[name^=idxs]').click(function () {
        var class_name = $(this).attr('class');
        var arr = explode('_', $(this).attr('id'));
        var depth = parseInt(arr[1]);
        if (depth == 1) {
            if (this.checked) {
                $("." + class_name).attr('checked', 'checked');
            }
            else {
                $("." + class_name).removeAttr('checked');
            }
        }
    });

    $('#controlform :radio').click(function () {
        if ($(this).val() == 'del_multi') {
            $('#movebbscode').hide();
        }
        else {
            $('#movebbscode').show();
        }
    });

    $('[name="language"]').on('change', function () {
        const lang = $(this).val();
        window.location.href = setURLParameter('language', lang); 
    });
    $('[name="category"]').on('change', function () {
        const category = $(this).val();
        window.location.href = setURLParameter('category', category); 
    });

});

